<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace core_backup;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->libdir . '/completionlib.php');

/**
 * Test for restore_stepslib.
 *
 * @package core_backup
 * @copyright 2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversMethod(\restore_gradebook_structure_step::class, 'gradebook_calculation_freeze')]
final class restore_gradebook_structure_step_test extends \advanced_testcase {

    /**
     * Provide tests for rewrite_step_backup_file_for_legacy_freeze based upon fixtures.
     *
     * @return array
     */
    public static function rewrite_step_backup_file_for_legacy_freeze_provider(): array {
        $fixturesdir = realpath(__DIR__ . '/fixtures/rewrite_step_backup_file_for_legacy_freeze/');
        $tests = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($fixturesdir),
            \RecursiveIteratorIterator::LEAVES_ONLY,
        );

        foreach ($iterator as $sourcefile) {
            $pattern = '/\.test$/';
            if (!preg_match($pattern, $sourcefile)) {
                continue;
            }

            $expectfile = preg_replace($pattern, '.expectation', $sourcefile);
            $test = array($sourcefile, $expectfile);
            $tests[basename($sourcefile)] = $test;
        }

        return $tests;
    }

    /**
     * @param   string  $source     The source file to test
     * @param   string  $expected   The expected result of the transformation
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('rewrite_step_backup_file_for_legacy_freeze_provider')]
    public function test_rewrite_step_backup_file_for_legacy_freeze($source, $expected): void {
        $restore = $this->getMockBuilder('\restore_gradebook_structure_step')
            ->onlyMethods([])
            ->disableOriginalConstructor()
            ->getMock()
            ;

        // Copy the file somewhere as the rewrite_step_backup_file_for_legacy_freeze will write the file.
        $dir = make_request_directory(true);
        $filepath = $dir . DIRECTORY_SEPARATOR . 'file.xml';
        copy($source, $filepath);

        $rc = new \ReflectionClass('\restore_gradebook_structure_step');
        $rcm = $rc->getMethod('rewrite_step_backup_file_for_legacy_freeze');
        $rcm->invoke($restore, $filepath);

        // Check the result.
        $this->assertFileEquals($expected, $filepath);
    }

    /**
     * Provide version boundary cases for the MDL-88407 penalty calculation freeze.
     *
     * @return array
     */
    public static function penalty_calculation_freeze_version_provider(): array {
        $fixversion = \restore_gradebook_structure_step::PENALTY_CALCULATION_BUG_VERSION;

        return [
            'Before affected range' => [2025031799.00, false],
            'Start of affected range' => [2025031800.00, true],
            'Within affected range' => [2025090100.00, true],
            'End of affected range' => [$fixversion, false],
            'After affected range' => [$fixversion + 0.01, false],
        ];
    }

    /**
     * Tests that gradebook_calculation_freeze() applies the MDL-88407 penalty freeze for
     * the correct backup version ranges and not for versions that already contain the fix.
     *
     * @param float $version The moodle_version stored in the backup.
     * @param bool $expectfreeze Whether a gradebook freeze should be applied for this version.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('penalty_calculation_freeze_version_provider')]
    public function test_gradebook_calculation_freeze_penalty_version_boundary(
        float $version,
        bool $expectfreeze,
    ): void {
        global $CFG;
        $this->resetAfterTest();

        require_once($CFG->libdir . '/gradelib.php');

        // A course with a penalised grade, so there is something for the freeze to apply to.
        $course = $this->getDataGenerator()->create_course();
        $gradeitem = new \grade_item();
        $gradeitem->itemname = 'test item';
        $gradeitem->itemtype = 'manual';
        $gradeitem->courseid = $course->id;
        $gradeitem->insert();
        $grade = $gradeitem->get_grade($this->getDataGenerator()->create_user()->id, true);
        $grade->rawgrade = 50;
        $grade->deductedmark = 10;
        $grade->update();

        // A restore task reporting the version under test, wired into a real step instance
        // via its normal constructor.
        $task = $this->createMock(\restore_task::class);
        $task->method('get_courseid')->willReturn($course->id);
        $task->method('get_info')->willReturn((object) ['moodle_version' => $version]);
        // Replicate restore_plan::backup_version_compare()'s behaviour, since the mocked
        // task otherwise always returns null/false regardless of the version under test.
        $task->method('backup_version_compare')->willReturnCallback(
            static function (int $comparedversion, string $operator) use ($version): bool {
                preg_match('/(\d{' . strlen((string) $comparedversion) . '})/', (string) $version, $matches);
                $backupbuild = (int) $matches[1];
                return version_compare($backupbuild, $comparedversion, $operator);
            }
        );
        $step = new \restore_gradebook_structure_step('gradebook_structure', 'gradebook.xml', $task);

        $rc = new \ReflectionClass($step);
        $rc->getMethod('gradebook_calculation_freeze')->invoke($step);
        $freeze = get_config('core', 'gradebook_calculations_freeze_' . $course->id);
        $this->assertEquals($expectfreeze ? 20260808 : false, $freeze);
    }
}
