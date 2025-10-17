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

/**
 * Tests for the \core_course\task\course_delete_modules class.
 *
 * @package    core
 * @subpackage course
 * @copyright  2021 Tomo Tsuyuki <tomotsuyuki@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_course\task\course_delete_modules
 */
namespace core_course;

/**
 * Tests for the \core_course\task\course_delete_modules class.
 *
 * @package    core
 * @subpackage course
 * @copyright  2021 Tomo Tsuyuki <tomotsuyuki@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class course_delete_modules_test extends \advanced_testcase {

    /**
     * Test to have a no message for usual process.
     */
    public function test_delete_module_execution(): void {
        $this->resetAfterTest();

        // Generate test data.
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $assign = $generator->create_module('assign', ['course' => $course]);
        $assigncm = get_coursemodule_from_id('assign', $assign->cmid);

        // The module exists in the course.
        $coursedmodules = get_course_mods($course->id);
        $this->assertCount(1, $coursedmodules);

        // Execute the task.
        $removaltask = new \core_course\task\course_delete_modules();
        $data = [
            'cms' => [$assigncm],
            'userid' => $user->id,
            'realuserid' => $user->id,
        ];
        $removaltask->set_custom_data($data);
        $removaltask->execute();

        // The module has deleted from the course.
        $coursedmodules = get_course_mods($course->id);
        $this->assertCount(0, $coursedmodules);
    }

    /**
     * Test with failed and successful cms
     */
    public function test_delete_module_exception(): void {
        global $DB;
        $this->resetAfterTest();

        // Generate test data.
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $assign = $generator->create_module('assign', ['course' => $course]);
        $assigncm = get_coursemodule_from_id('assign', $assign->cmid);

        // Modify module name to make an exception in the course_delete_modules task.
        $module = $DB->get_record('modules', ['id' => $assigncm->module], 'id, name', MUST_EXIST);
        $module->name = 'TestModuleToDelete';
        $DB->update_record('modules', $module);

        // Generate successful test data.
        $quiz1 = $generator->create_module('quiz', ['course' => $course]);
        $quizcm1 = get_coursemodule_from_id('quiz', $quiz1->cmid);

        $quiz2 = $generator->create_module('quiz', ['course' => $course]);
        $quizcm2 = get_coursemodule_from_id('quiz', $quiz2->cmid);

        // Execute the task.
        $removaltask = new \core_course\task\course_delete_modules();
        $data = [
            'cms' => [$quizcm1, $assigncm, $quizcm2],
            'userid' => $user->id,
            'realuserid' => $user->id,
        ];
        $removaltask->set_custom_data($data);
        try {
            $removaltask->execute();
        } catch (\coding_exception $e) {
            // Assert exception.
            $this->assertInstanceOf(\coding_exception::class, $e);
            $errormsg = str_replace('\\', '/', $e->getMessage()); // Normalise dir separator.
            $this->assertStringContainsString('cannotdeletemodulemissinglib', $errormsg);
            $this->assertStringContainsString('Missing file mod/TestModuleToDelete/lib.php', $errormsg);
            // Get line numbers array which contains the exception name.
            $lines = array_keys(preg_grep("/cannotdeletemodulemissinglib/", file(dirname(__DIR__) . '/lib.php')));
            // Increase 1 to keys to convert to actual line number.
            $lines = array_map(function($key) {
                return ++$key;
            }, $lines);
            $regex = "/(\(" . implode('\))|(\(', $lines) . "\))/";
            // Assert the error message has correct line number.
            $this->assertMatchesRegularExpression($regex, $errormsg);
        }

        // The success modules have been deleted from the course, and only the failed module is in the course.
        $coursedmodules = get_course_mods($course->id);
        $this->assertCount(1, $coursedmodules);
    }
}
