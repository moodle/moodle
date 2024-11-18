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
 * @subpackage iomadmerge
 * @copyright  Derick Turner
 * @author     Derick Turner
 * @basedon    admin tool merge by:
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author     Mike Holzer
 * @author     Forrest Gaston
 * @author     Juan Pablo Torres Herrera
 * @author     Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @author     John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');

/**
 * Class assign_test
 */
class tool_iomadmerge_assign_testcase extends mod_assign_base_testcase {
    /**
     *
     */
    public function setUp(): void {
        global $CFG;
        require_once("$CFG->dirroot/admin/tool/iomadmerge/lib/iomadmergetool.php");
        parent::setUp();
    }

    /**
     * Test merging two users where one has submitted an assignment and the other
     * has no.
     * @group tool_iomadmerge
     * @group tool_iomadmerge_assign
     */
    public function test_mergenonconflictingassigngrades() {
        global $DB;

        $this->setUser($this->editingteachers[0]);
        $assign = $this->create_instance();

        $this->setUser($this->teachers[0]);

        // Give a grade to student 1.
        $data = new stdClass();
        $data->grade = '75.0';
        $assign->testable_apply_grade_to_user($data, $this->students[1]->id, 0);

        // Check initial state - student 0 has no grade, student 1 has 75.00.
        $this->assertEquals(false, $assign->testable_is_graded($this->students[0]->id));
        $this->assertEquals(true, $assign->testable_is_graded($this->students[1]->id));
        $this->assertEquals('75.00', $this->get_user_assign_grade($this->students[1], $assign, $this->course));
        $this->assertEquals('-', $this->get_user_assign_grade($this->students[0], $assign, $this->course));

        // Merge student 1 into student 0.
        $mut = new IomadMergeTool();
        $mut->merge($this->students[0]->id, $this->students[1]->id);

        // Student 0 should now have a grade of 75.00.
        $this->assertEquals(true, $assign->testable_is_graded($this->students[0]->id));
        $this->assertEquals('75.00', $this->get_user_assign_grade($this->students[0], $assign, $this->course));

        // Student 1 should now be suspended.
        $user_remove = $DB->get_record('user', array('id' => $this->students[1]->id));
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
