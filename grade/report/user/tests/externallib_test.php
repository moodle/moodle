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
 * User grade report functions unit tests
 *
 * @package    gradereport_user
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/grade/report/user/externallib.php');

/**
 * User grade report functions unit tests
 *
 * @package    gradereport_user
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradereport_user_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Loads some data to be used by the different tests
     * @param  int $s1grade Student 1 grade
     * @param  int $s2grade Student 2 grade
     * @return array          Course and users instances
     */
    private function load_data($s1grade, $s2grade) {
        global $DB;

        $course = $this->getDataGenerator()->create_course();

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $student1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, $studentrole->id);

        $student2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, $studentrole->id);

        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);

        $assignment = $this->getDataGenerator()->create_module('assign', array('name' => "Test assign", 'course' => $course->id));
        $modcontext = get_coursemodule_from_instance('assign', $assignment->id, $course->id);
        $assignment->cmidnumber = $modcontext->id;

        $student1grade = array('userid' => $student1->id, 'rawgrade' => $s1grade);
        $student2grade = array('userid' => $student2->id, 'rawgrade' => $s2grade);
        $studentgrades = array($student1->id => $student1grade, $student2->id => $student2grade);
        assign_grade_item_update($assignment, $studentgrades);

        return array($course, $teacher, $student1, $student2);
    }

    /**
     * Test get_grades_table function case teacher
     */
    public function test_get_grades_table_teacher() {

        $this->resetAfterTest(true);

        $s1grade = 80;
        $s2grade = 60;

        list($course, $teacher, $student1, $student2) = $this->load_data($s1grade, $s2grade);

        // A teacher must see all student grades.
        $this->setUser($teacher);

        $studentgrades = gradereport_user_external::get_grades_table($course->id);
        $studentgrades = external_api::clean_returnvalue(gradereport_user_external::get_grades_table_returns(), $studentgrades);

        // No warnings returned.
        $this->assertTrue(count($studentgrades['warnings']) == 0);

        // Check that two grades are returned (each for student).
        $this->assertTrue(count($studentgrades['tables']) == 2);

        // Read returned grades.
        $studentreturnedgrades = array();
        $studentreturnedgrades[$studentgrades['tables'][0]['userid']] =
            (int) $studentgrades['tables'][0]['tabledata'][1]['grade']['content'];

        $studentreturnedgrades[$studentgrades['tables'][1]['userid']] =
            (int) $studentgrades['tables'][1]['tabledata'][1]['grade']['content'];

        $this->assertEquals($s1grade, $studentreturnedgrades[$student1->id]);
        $this->assertEquals($s2grade, $studentreturnedgrades[$student2->id]);
    }

    /**
     * Test get_grades_table function case student
     */
    public function test_get_grades_table_student() {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        $s1grade = 80;
        $s2grade = 60;

        list($course, $teacher, $student1, $student2) = $this->load_data($s1grade, $s2grade);

        // A user can see his own grades.
        $this->setUser($student1);
        $studentgrade = gradereport_user_external::get_grades_table($course->id, $student1->id);
        $studentgrade = external_api::clean_returnvalue(gradereport_user_external::get_grades_table_returns(), $studentgrade);

        // No warnings returned.
        $this->assertTrue(count($studentgrade['warnings']) == 0);

        $this->assertTrue(count($studentgrade['tables']) == 1);
        $student1returnedgrade = (int) $studentgrade['tables'][0]['tabledata'][1]['grade']['content'];
        $this->assertEquals($s1grade, $student1returnedgrade);

    }

    /**
     * Test get_grades_table function case incorrect permissions
     */
    public function test_get_grades_table_permissions() {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        $s1grade = 80;
        $s2grade = 60;

        list($course, $teacher, $student1, $student2) = $this->load_data($s1grade, $s2grade);

        $this->setUser($student2);

        try {
            $studentgrade = gradereport_user_external::get_grades_table($course->id, $student1->id);
            $this->fail('Exception expected due to not perissions to view other user grades.');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissiontoviewgrades', $e->errorcode);
        }

    }

}
