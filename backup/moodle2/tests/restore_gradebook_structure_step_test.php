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
class restore_gradebook_structure_step_test extends \advanced_testcase {

    /**
     * Provide tests for rewrite_step_backup_file_for_legacy_freeze based upon fixtures.
     *
     * @return array
     */
    public function rewrite_step_backup_file_for_legacy_freeze_provider() {
        $fixturesdir = realpath(__DIR__ . '/fixtures/rewrite_step_backup_file_for_legacy_freeze/');
        $tests = [];
        $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($fixturesdir),
                \RecursiveIteratorIterator::LEAVES_ONLY);

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
     * @dataProvider rewrite_step_backup_file_for_legacy_freeze_provider
     * @param   string  $source     The source file to test
     * @param   string  $expected   The expected result of the transformation
     */
    public function test_rewrite_step_backup_file_for_legacy_freeze($source, $expected) {
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
}
