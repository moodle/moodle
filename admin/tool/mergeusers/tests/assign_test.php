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
 * Version information
 *
 * @package    tool_mergeusers
 * @author     Andrew Hancox <andrewdchancox@googlemail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_mergeusers\local\user_merger;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->dirroot/mod/assign/tests/generator.php");

/**
 * Class assign_test
 */
final class assign_test extends advanced_testcase {
    use \mod_assign_test_generator;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Test merging two users where one has submitted an assignment and the other
     * has no.
     * @group tool_mergeusers
     * @group tool_mergeusers_assign
     * @group tool_mergeusers_reaggregate
     * @group tool_mergeusers_regrade
     */
    public function test_merge_non_conflicting_assign_grades(): void {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);

        // Give a grade to student 1.
        $data = new stdClass();
        $data->grade = '75.0';
        $assign->testable_apply_grade_to_user($data, $student2->id, 0);

        // Check initial state - student 0 has no grade, student 1 has 75.00.
        $this->assertEquals(false, $assign->testable_is_graded($student1->id));
        $this->assertEquals(true, $assign->testable_is_graded($student2->id));
        $this->assertEquals('75.00', $this->get_user_assign_grade($student2, $assign, $course));
        $this->assertEquals('-', $this->get_user_assign_grade($student1, $assign, $course));

        // Merge student 1 into student 0.
        $mut = new user_merger();
        // This merge already invokes the callback for regrading.
        [$success, $logs, $logid] = $mut->merge($student1->id, $student2->id);

        // Check that logs contain regrading log lines.
        $this->assertTrue($success);
        $found = '';
        foreach ($logs as $logline) {
            $found = strstr($logline, 'Regraded grade item with id');
            if (!empty($found)) {
                break;
            }
        }
        $this->assertNotEmpty($found);
        // Check that there is no reaggregation of course completion.
        $found = '';
        foreach ($logs as $logline) {
            $found = strstr($logline, 'Course completion reaggregation asked for no courses.');
            if (!empty($found)) {
                break;
            }
        }
        $this->assertNotEmpty($found);

        // Student 0 should now have a grade of 75.00.
        $this->assertEquals(true, $assign->testable_is_graded($student1->id));
        $this->assertEquals('75.00', $this->get_user_assign_grade($student1, $assign, $course));

        // Student 1 should now be suspended.
        $userremove = $DB->get_record('user', ['id' => $student2->id]);
        $this->assertEquals(1, $userremove->suspended);
    }

    /**
     * Utility method to get the grade for a user.
     * @param $user
     * @param $assign
     * @param $course
     * @return testable_assign
     */
    private function get_user_assign_grade($user, $assign, $course) {
        $gradebookgrades = \grade_get_grades($course->id, 'mod', 'assign', $assign->get_instance()->id, $user->id);
        $gradebookitem   = array_shift($gradebookgrades->items);
        $grade     = $gradebookitem->grades[$user->id];
        return $grade->str_grade;
    }

    /**
     * Test failing merge due to missing course module.
     *
     * @group tool_mergeusers
     * @group tool_mergeusers_assign
     * @throws dml_exception
     */
    public function test_failed_merged_for_missing_course_module() {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);

        // Give a grade to student 1.
        $data = new stdClass();
        $data->grade = '75.0';
        $assign->testable_apply_grade_to_user($data, $student2->id, 0);

        // Delete course_modules record. This emulates some weird behaviour on the Moodle site.
        $assignmodule = $DB->get_record('modules', ['name' => 'assign']);
        $DB->delete_records(
            'course_modules',
            [
                'module' => $assignmodule->id,
                'instance' => $assign->get_course_module()->instance,
                'course' => $course->id,
            ],
        );

        // Try to merge users.
        $mut = new user_merger();
        [$success, $log, $logid] = $mut->merge($student1->id, $student2->id);

        // Merge have failed. Check it!
        $this->assertFalse($success);
        // Be sure logs contain the detail of the exception.
        $exceptionmessage = get_string(
            'exception:nocoursemodule',
            'tool_mergeusers',
            [
                'module' => 'assign',
                'activityid' => $assign->get_course_module()->instance,
                'courseid' => $course->id,
            ],
        );
        $matchingerror = array_filter(
            $log,
            function ($line) use ($exceptionmessage) {
                return strstr($line, $exceptionmessage);
            },
        );
        $this->assertCount(1, $matchingerror);

        // Users are kept unaltered.
        $this->assertEquals(0, $DB->get_field('user', 'suspended', ['id' => $student1->id]));
        $this->assertEquals(0, $DB->get_field('user', 'suspended', ['id' => $student2->id]));
    }

    /**
     * Test failing merge due to missing module record.
     *
     * @group tool_mergeusers
     * @group tool_mergeusers_assign
     * @throws dml_exception
     */
    public function test_failed_merged_for_missing_module_record() {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);

        // Give a grade to student 1.
        $data = new stdClass();
        $data->grade = '75.0';
        $assign->testable_apply_grade_to_user($data, $student2->id, 0);

        // Delete module's table record. This emulates some weird behaviour on the Moodle site.
        $DB->delete_records('assign', ['id' => $assign->get_course_module()->instance]);

        // Try to merge users.
        $mut = new user_merger();
        [$success, $log, $logid] = $mut->merge($student1->id, $student2->id);

        // Merge have failed. Check it!
        $this->assertFalse($success);
        // Be sure logs contain the detail of the exception.
        $exceptionmessage = get_string(
            'exception:nomoduleinstance',
            'tool_mergeusers',
            [
                'module' => 'assign',
                'activityid' => $assign->get_course_module()->instance,
            ],
        );
        $matchingerror = array_filter(
            $log,
            function ($line) use ($exceptionmessage) {
                return strstr($line, $exceptionmessage);
            },
        );
        $this->assertCount(1, $matchingerror);

        // Users are kept unaltered.
        $this->assertEquals(0, $DB->get_field('user', 'suspended', ['id' => $student1->id]));
        $this->assertEquals(0, $DB->get_field('user', 'suspended', ['id' => $student2->id]));
    }
}
