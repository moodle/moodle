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

namespace core;

use core_grades_external;
use core_external\external_api;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Grades functions unit tests
 *
 * Unit tests for the grade API at /lib/classes/grades_external.php
 *
 * @package core
 * @category test
 * @copyright 2012 Andrew Davis
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grades_external_test extends \externallib_advanced_testcase {

    /**
     * Load initial test information
     *
     * @param  string $assignmentname   Assignment name
     * @param  int $student1rawgrade    Student 1 grade
     * @param  int $student2rawgrade    Student 2 grade
     * @return array                    Array of vars with test information
     */
    protected function load_test_data($assignmentname, $student1rawgrade, $student2rawgrade) {
        global $DB;

        // Adds a course, a teacher, 2 students, an assignment and grades for the students.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        $student1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, $studentrole->id);

        $student2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, $studentrole->id);

        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);

        $parent = $this->getDataGenerator()->create_user();
        $this->setUser($parent);
        $student1context = \context_user::instance($student1->id);
        // Creates a new role, gives it the capability and gives $USER that role.
        $parentroleid = $this->assignUserCapability('moodle/grade:viewall', $student1context->id);
        // Enrol the user in the course using the new role.
        $this->getDataGenerator()->enrol_user($parent->id, $course->id, $parentroleid);

        $assignment = $this->getDataGenerator()->create_module('assign', array('name' => $assignmentname, 'course' => $course->id));
        $modcontext = get_coursemodule_from_instance('assign', $assignment->id, $course->id);
        $assignment->cmidnumber = $modcontext->id;

        $student1grade = array('userid' => $student1->id, 'rawgrade' => $student1rawgrade);
        $student2grade = array('userid' => $student2->id, 'rawgrade' => $student2rawgrade);
        $studentgrades = array($student1->id => $student1grade, $student2->id => $student2grade);
        assign_grade_item_update($assignment, $studentgrades);

        // Insert a custom grade scale to be used by an outcome.
        $gradescale = new \grade_scale();
        $gradescale->name        = 'unittestscale3';
        $gradescale->courseid    = $course->id;
        $gradescale->userid      = 0;
        $gradescale->scale       = 'Distinction, Very Good, Good, Pass, Fail';
        $gradescale->description = 'This scale is used to mark standard assignments.';
        $gradescale->insert();

        // Insert an outcome.
        $data = new \stdClass();
        $data->courseid = $course->id;
        $data->fullname = 'Team work';
        $data->shortname = 'Team work';
        $data->scaleid = $gradescale->id;
        $outcome = new \grade_outcome($data, false);
        $outcome->insert();

        $outcomegradeitem = new \grade_item();
        $outcomegradeitem->itemname = $outcome->shortname;
        $outcomegradeitem->itemtype = 'mod';
        $outcomegradeitem->itemmodule = 'assign';
        $outcomegradeitem->iteminstance = $assignment->id;
        $outcomegradeitem->outcomeid = $outcome->id;
        $outcomegradeitem->cmid = 0;
        $outcomegradeitem->courseid = $course->id;
        $outcomegradeitem->aggregationcoef = 0;
        $outcomegradeitem->itemnumber = 1; // The activity's original grade item will be 0.
        $outcomegradeitem->gradetype = GRADE_TYPE_SCALE;
        $outcomegradeitem->scaleid = $outcome->scaleid;
        // This next two values for testing that returns parameters are correcly formatted.
        $outcomegradeitem->set_locked(true);
        $outcomegradeitem->hidden = '';
        $outcomegradeitem->insert();

        $assignmentgradeitem = \grade_item::fetch(
            array(
                'itemtype' => 'mod',
                'itemmodule' => 'assign',
                'iteminstance' => $assignment->id,
                'itemnumber' => 0,
                'courseid' => $course->id
            )
        );
        $outcomegradeitem->set_parent($assignmentgradeitem->categoryid);
        $outcomegradeitem->move_after_sortorder($assignmentgradeitem->sortorder);

        return array($course, $assignment, $student1, $student2, $teacher, $parent);
    }

    /**
     * Test update_grades()
     */
    public function test_update_grades(): void {
        global $DB;

        $this->resetAfterTest(true);

        $assignmentname = 'The assignment';
        $student1rawgrade = 10;
        $student2rawgrade = 20;
        list($course, $assignment, $student1, $student2, $teacher, $parent) =
            $this->load_test_data($assignmentname, $student1rawgrade, $student2rawgrade);
        $assigmentcm = get_coursemodule_from_id('assign', $assignment->cmid, 0, false, MUST_EXIST);

        $this->setUser($teacher);

        // Teacher updating grade item information.
        $changedmax = 93;
        $result = core_grades_external::update_grades(
            'test',
            $course->id,
            'mod_assign',
            $assigmentcm->id,
            0,
            array(),
            array('grademax' => $changedmax)
        );
        $result = external_api::clean_returnvalue(core_grades_external::update_grades_returns(), $result);
        $this->assertTrue($result == GRADE_UPDATE_OK);
        $grades = grade_get_grades($course->id, 'mod', 'assign', $assignment->id);
        $this->assertTrue($grades->items[0]->grademax == $changedmax);

        // Teacher updating 1 student grade.
        $student1grade = 23;
        $result = core_grades_external::update_grades(
            'test',
            $course->id,
            'mod_assign',
            $assigmentcm->id,
            0,
            array(array('studentid' => $student1->id, 'grade' => $student1grade))
        );
        $result = external_api::clean_returnvalue(core_grades_external::update_grades_returns(), $result);
        $this->assertTrue($result == GRADE_UPDATE_OK);
        $grades = grade_get_grades($course->id, 'mod', 'assign', $assignment->id, array($student1->id));
        $this->assertTrue($grades->items[0]->grades[$student1->id]->grade == $student1grade);

        // Teacher updating multiple student grades.
        $student1grade = 11;
        $student2grade = 13;
        $result = core_grades_external::update_grades(
            'test',
            $course->id,
            'mod_assign',
            $assigmentcm->id,
            0,
            array(
                array('studentid' => $student1->id, 'grade' => $student1grade),
                array('studentid' => $student2->id, 'grade' => $student2grade)
            )
        );
        $result = external_api::clean_returnvalue(core_grades_external::update_grades_returns(), $result);
        $this->assertTrue($result == GRADE_UPDATE_OK);
        $grades = grade_get_grades($course->id, 'mod', 'assign', $assignment->id, array($student1->id, $student2->id));
        $this->assertTrue($grades->items[0]->grades[$student1->id]->grade == $student1grade);
        $this->assertTrue($grades->items[0]->grades[$student2->id]->grade == $student2grade);

        // Student attempting to update their own grade (should fail).
        $this->setUser($student1);
        try {
            $student1grade = 17;
            $result = core_grades_external::update_grades(
                'test',
                $course->id,
                'mod_assign',
                $assigmentcm->id,
                0,
                array( array('studentid' => $student1->id, 'grade' => $student1grade))
            );
            $this->fail('moodle_exception expected');
        } catch (\moodle_exception $ex) {
            $this->assertTrue(true);
        }

        // Parent attempting to update their child's grade (should fail).
        $this->setUser($parent);
        try {
            $student1grade = 13;
            $result = core_grades_external::update_grades(
                'test',
                $course->id,
                'mod_assign',
                $assigmentcm->id,
                0,
                array( array('studentid' => $student1->id, 'grade' => $student1grade))
            );
            $this->fail('moodle_exception expected');
        } catch (\moodle_exception $ex) {
            $this->assertTrue(true);
        }

        // Student trying to hide a grade item (should fail).
        $this->setUser($student1);
        try {
            $result = core_grades_external::update_grades(
                'test',
                $course->id,
                'mod_assign',
                $assigmentcm->id,
                0,
                array(),
                array('hidden' => 1)
            );
            $this->fail('moodle_exception expected');
        } catch (\moodle_exception $ex) {
            $this->assertTrue(true);
        }

        // Give the student role 'moodle/grade:hide' and they should now be able to hide the grade item.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $coursecontext = \context_course::instance($course->id);
        assign_capability('moodle/grade:hide', CAP_ALLOW, $studentrole->id, $coursecontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        // Check the activity isn't already hidden.
        $grades = grade_get_grades($course->id, 'mod', 'assign', $assignment->id);
        $this->assertTrue($grades->items[0]->hidden == 0);

        $result = core_grades_external::update_grades(
            'test',
            $course->id,
            'mod_assign',
            $assigmentcm->id,
            0,
            array(),
            array('hidden' => 1)
        );
        $result = external_api::clean_returnvalue(core_grades_external::update_grades_returns(), $result);
        $this->assertTrue($result == GRADE_UPDATE_OK);
        $grades = grade_get_grades($course->id, 'mod', 'assign', $assignment->id);
        $this->assertTrue($grades->items[0]->hidden == 1);
    }
}
