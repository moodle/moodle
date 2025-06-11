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
 * @package    tool
 * @subpackage mergeusers
 * @author     Andrew Hancox <andrewdchancox@googlemail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');

/**
 * Class assign_test
 */
class assign_test extends advanced_testcase {
    use \mod_assign_test_generator;

    /**
     *
     */
    public function setUp(): void {
        global $CFG;
        require_once("$CFG->dirroot/admin/tool/mergeusers/lib/mergeusertool.php");
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Test merging two users where one has submitted an assignment and the other
     * has no.
     * @group tool_mergeusers
     * @group tool_mergeusers_assign
     */
    public function test_mergenonconflictingassigngrades() {
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
        $mut = new MergeUserTool();
        $mut->merge($student1->id, $student2->id);

        // Student 0 should now have a grade of 75.00.
        $this->assertEquals(true, $assign->testable_is_graded($student1->id));
        $this->assertEquals('75.00', $this->get_user_assign_grade($student1, $assign, $course));

        // Student 1 should now be suspended.
        $user_remove = $DB->get_record('user', array('id' => $student2->id));
        $this->assertEquals(1, $user_remove->suspended);
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
}
