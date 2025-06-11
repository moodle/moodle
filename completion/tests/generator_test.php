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

namespace core_completion;

/**
 * PHPUnit data generator testcase
 *
 * @package     core_completion
 * @category    test
 * @copyright   2023 Amaia Anabitarte <amaia@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_completion_generator
 */
final class generator_test extends \advanced_testcase {

    /**
     * Test create_default_completion.
     *
     * @dataProvider create_default_completion_provider
     *
     * @param int|null|string $course The course to add the default activities conditions to.
     * @param int|null|string $module The module to add the default activities conditions to.
     * @param bool $exception Whether an exception is expected or not.
     * @param int $count The number of default activity completions to be created.
     * @param int $completion The value for completion setting.
     *
     * @covers ::create_default_completion
     */
    public function test_create_default_completion($course, $module, bool $exception, int $count, int $completion = 0): void {
        global $DB;

        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator()->get_plugin_generator('core_completion');

        $record = [
            'course' => $course,
            'module' => $module,
            'completion' => $completion,
        ];
        $result = (object) array_merge([
            'completion' => 0,
            'completionview' => 0,
            'completionusegrade' => 0,
            'completionpassgrade' => 0,
            'completionexpected' => 0,
            'customrules' => '',
        ], $record);

        if ($exception) {
            $this->expectException('moodle_exception');
        }
        $defaultcompletion = $generator->create_default_completion($record);

        if (!$exception) {
            foreach ($result as $key => $value) {
                $this->assertEquals($defaultcompletion->{$key}, $value);
            }
        }
        $this->assertEquals(
            $count,
            $DB->count_records('course_completion_defaults', ['course' => $course, 'module' => $module])
        );
    }

    /**
     * Data provider for test_create_default_completion().
     * @return array[]
     */
    public static function create_default_completion_provider(): array {
        global $SITE;

        return [
            'Null course' => [
                'course' => null,
                'module' => null,
                'exception' => true,
                'count' => 0,
            ],
            'Empty course' => [
                'course' => '',
                'module' => null,
                'exception' => true,
                'count' => 0,
            ],
            'Invalid course' => [
                'course' => 0,
                'module' => null,
                'exception' => true,
                'count' => 0,
            ],
            'Null module' => [
                'course' => $SITE->id,
                'module' => null,
                'exception' => true,
                'count' => 0,
            ],
            'Empty module' => [
                'course' => $SITE->id,
                'module' => null,
                'exception' => true,
                'count' => 0,
            ],
            'Invalid module' => [
                'course' => $SITE->id,
                'module' => 0,
                'exception' => true,
                'count' => 0,
            ],
            'Default activity completion: NONE' => [
                'course' => $SITE->id,
                'module' => 1,
                'exception' => false,
                'count' => 1,
            ],
            'Default activity completion: AUTOMATIC' => [
                'course' => $SITE->id,
                'module' => 1,
                'exception' => false,
                'count' => 1,
                'completion' => 2,
            ],
        ];
    }
}
