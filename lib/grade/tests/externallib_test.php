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
 * Unit tests for the grade API at /lib/grade/externallib.php
 *
 * @package    core_grade
 * @category   external
 * @copyright  2012 Andrew Davis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.6
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->libdir . '/grade/externallib.php');

class core_grade_external_testcase extends externallib_advanced_testcase {

    protected function load_test_data($assignmentname, $student1rawgrade, $student2rawgrade) {
        global $DB;

        // Adds a course, a teacher, 2 students, an assignment and grades for the students.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));

        $student1 = $this->getDataGenerator()->create_user();        
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, $studentrole->id);

        $student2 = $this->getDataGenerator()->create_user();        
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, $studentrole->id);

        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'));
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);

        $parent = $this->getDataGenerator()->create_user();
        $this->setUser($parent);
        $student1context = context_user::instance($student1->id);
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
        $grade_scale = new grade_scale();
        $grade_scale->name        = 'unittestscale3';
        $grade_scale->courseid    = $course->id;
        $grade_scale->userid      = 0;
        $grade_scale->scale       = 'Distinction, Very Good, Good, Pass, Fail';
        $grade_scale->description = 'This scale is used to mark standard assignments.';
        $grade_scale->insert();

        // Insert an outcome.
        $data = new stdClass();
        $data->courseid = $course->id;
        $data->fullname = 'Team work';
        $data->shortname = 'Team work';
        $data->scaleid = $grade_scale->id;
        $outcome = new grade_outcome($data, false);
        $outcome->insert();

        $outcome_gradeitem = new grade_item();
        $outcome_gradeitem->itemname = $outcome->shortname;
        $outcome_gradeitem->itemtype = 'mod';
        $outcome_gradeitem->itemmodule = 'assign';
        $outcome_gradeitem->iteminstance = $assignment->id;
        $outcome_gradeitem->outcomeid = $outcome->id;
        $outcome_gradeitem->cmid = 0;
        $outcome_gradeitem->courseid = $course->id;
        $outcome_gradeitem->aggregationcoef = 0;
        $outcome_gradeitem->itemnumber = 1; // The activity's original grade item will be 0.
        $outcome_gradeitem->gradetype = GRADE_TYPE_SCALE;
        $outcome_gradeitem->scaleid = $outcome->scaleid;
        $outcome_gradeitem->insert();

        $assignment_gradeitem = grade_item::fetch(
            array(
                'itemtype' => 'mod',
                'itemmodule' => 'assign',
                'iteminstance' => $assignment->id,
                'itemnumber' => 0,
                'courseid' => $course->id
            )
        );
        $outcome_gradeitem->set_parent($assignment_gradeitem->categoryid);
        $outcome_gradeitem->move_after_sortorder($assignment_gradeitem->sortorder);

        return array($course, $assignment, $student1, $student2, $teacher, $parent);
    }

    /**
     * Test get_grades()
     */
    public function test_get_grades() {
        global $CFG;

        $this->resetAfterTest(true);
        $CFG->enableoutcomes = 1;

        $assignmentname = 'The assignment';
        $student1rawgrade = 10;
        $student2rawgrade = 20;
        list($course, $assignment, $student1, $student2, $teacher, $parent) = $this->load_test_data($assignmentname, $student1rawgrade, $student2rawgrade);
        $assigment_cm = get_coursemodule_from_id('assign', $assignment->id, 0, false, MUST_EXIST);

        // Student requesting their own grade for the assignment.
        $this->setUser($student1);
        $grades = core_grade_external::get_grades(
            $course->id,
            'mod_assign',
            $assigment_cm->id,
            array($student1->id)
        );
        $grades = external_api::clean_returnvalue(core_grade_external::get_grades_returns(), $grades);
        $this->assertEquals($student1rawgrade, $this->get_activity_student_grade($grades, $assigment_cm->id, $student1->id));

        // Student requesting all of their grades in a course.
        $grades = core_grade_external::get_grades(
            $course->id,
            null,
            null,
            array($student1->id)
        );
        $grades = external_api::clean_returnvalue(core_grade_external::get_grades_returns(), $grades);
        $this->assertTrue(count($grades['items']) == 2);
        $this->assertEquals($student1rawgrade, $this->get_activity_student_grade($grades, $assigment_cm->id, $student1->id));
        $this->assertEquals($student1rawgrade, $this->get_activity_student_grade($grades, 'course', $student1->id));

        $outcome = $this->get_outcome($grades, $assigment_cm->id);
        $this->assertEquals($outcome['name'], 'Team work');
        $this->assertEquals(0, $this->get_outcome_student_grade($grades, $assigment_cm->id, $student1->id));

        // Student requesting another student's grade for the assignment (should fail).
        try {
            $grades = core_grade_external::get_grades(
                $course->id,
                'mod_assign',
                $assigment_cm->id,
                array($student2->id)
            );
            $this->fail('moodle_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertTrue(true);
        }

        // Parent requesting their child's grade for the assignment.
        $this->setUser($parent);
        $grades = core_grade_external::get_grades(
            $course->id,
            'mod_assign',
            $assigment_cm->id,
            array($student1->id)
        );
        $grades = external_api::clean_returnvalue(core_grade_external::get_grades_returns(), $grades);
        $this->assertEquals($student1rawgrade, $this->get_activity_student_grade($grades, $assigment_cm->id, $student1->id));

        // Parent requesting another student's grade for the assignment(should fail).
        try {
            $grades = core_grade_external::get_grades(
                $course->id,
                'mod_assign',
                $assigment_cm->id,
                array($student2->id)
            );
            $this->fail('moodle_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertTrue(true);
        }

        // Student requesting all other student grades for the assignment (should fail).
        try {
            $grades = core_grade_external::get_grades(
                $course->id,
                'mod_assign',
                $assigment_cm->id,
                array($student1->id, $student2->id)
            );
            $this->fail('moodle_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertTrue(true);
        }

        // Student requesting only grade item information (should fail).
        try {
            $grades = core_grade_external::get_grades(
                $course->id,
                'mod_assign',
                $assigment_cm->id,
                array()
            );
            $this->fail('moodle_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertTrue(true);
        }

        // Teacher requesting student grades for a course.
        $this->setUser($teacher);
        $grades = core_grade_external::get_grades(
            $course->id,
            'mod_assign',
            $assigment_cm->id,
            array($student1->id, $student2->id)
        );
        $grades = external_api::clean_returnvalue(core_grade_external::get_grades_returns(), $grades);
        $this->assertEquals($student1rawgrade, $this->get_activity_student_grade($grades, $assigment_cm->id, $student1->id));
        $this->assertEquals($student2rawgrade, $this->get_activity_student_grade($grades, $assigment_cm->id, $student2->id));

        // Teacher requesting grade item information.
        $grades = core_grade_external::get_grades(
            $course->id,
            'mod_assign',
            $assigment_cm->id
        );
        $grades = external_api::clean_returnvalue(core_grade_external::get_grades_returns(), $grades);
        $activity = $this->get_activity($grades, $assigment_cm->id);
        $this->assertEquals($activity['name'], $assignmentname);
        $this->assertEquals(count($activity['grades']), 0);

        // Teacher requesting all grade items in a course.
        $grades = core_grade_external::get_grades(
            $course->id
        );
        $grades = external_api::clean_returnvalue(core_grade_external::get_grades_returns(), $grades);
        $this->assertTrue(count($grades['items']) == 2);

        $activity = $this->get_activity($grades, $assigment_cm->id);
        $this->assertEquals($activity['name'], $assignmentname);
        $this->assertEquals(count($activity['grades']), 0);

        $outcome = $this->get_outcome($grades, $assigment_cm->id);
        $this->assertEquals($outcome['name'], 'Team work');

        // Hide a grade item then have student request it
        $result = core_grade_external::update_grades(
            'test',
            $course->id,
            'mod_assign',
            $assigment_cm->id,
            0,
            array(),
            array('hidden' => 1)
        );
        $this->assertTrue($result == GRADE_UPDATE_OK);

        // Check it's definitely hidden.
        $grades = grade_get_grades($course->id, 'mod', 'assign', $assignment->id);
        $this->assertEquals($grades->items[0]->hidden, 1);

        // Student should now not be able to see it.
        $this->setUser($student1);
        $grades = core_grade_external::get_grades(
            $course->id,
            'mod_assign',
            $assigment_cm->id,
            array($student1->id)
        );
        $grades = external_api::clean_returnvalue(core_grade_external::get_grades_returns(), $grades);
        $this->assertEquals(null, $this->get_activity($grades, $assigment_cm->id));

        // Teacher should still be able to see the hidden grades.
        $this->setUser($teacher);
        $grades = core_grade_external::get_grades(
            $course->id,
            'mod_assign',
            $assigment_cm->id,
            array($student1->id)
        );
        $grades = external_api::clean_returnvalue(core_grade_external::get_grades_returns(), $grades);
        $this->assertEquals($student1rawgrade, $this->get_activity_student_grade($grades, $assigment_cm->id, $student1->id));
    }

    private function get_activity($grades, $cmid) {
        foreach ($grades['items'] as $item) {
            if ($item['activityid'] == $cmid) {
                return $item;
            }
        }
        return null;
    }

    private function get_activity_student_grade($grades, $cmid, $studentid) {
        $item = $this->get_activity($grades, $cmid);
        foreach ($item['grades'] as $grade) {
            if ($grade['userid'] == $studentid) {
                return $grade['grade'];
            }
        }
        return null;
    }
    
    private function get_outcome($grades, $cmid) {
        foreach($grades['outcomes'] as $outcome) {
            if ($outcome['activityid'] == $cmid) {
                return $outcome;
            }
        }
        return null;
    }
    
    private function get_outcome_student_grade($grades, $cmid, $studentid) {
        $outcome = $this->get_outcome($grades, $cmid);
        foreach ($outcome['grades'] as $grade) {    
            if ($grade['userid'] == $studentid) {
                return $grade['grade'];
            }
        }
        return null;
    }

    /**
     * Test get_grades()
     */
    public function test_update_grades() {
        global $DB;

        $this->resetAfterTest(true);

        $assignmentname = 'The assignment';
        $student1rawgrade = 10;
        $student2rawgrade = 20;
        list($course, $assignment, $student1, $student2, $teacher, $parent) = $this->load_test_data($assignmentname, $student1rawgrade, $student2rawgrade);
        $assigment_cm = get_coursemodule_from_id('assign', $assignment->id, 0, false, MUST_EXIST);

        $this->setUser($teacher);

        // Teacher updating grade item information
        $changedmax = 93;
        $result = core_grade_external::update_grades(
            'test',
            $course->id,
            'mod_assign',
            $assigment_cm->id,
            0,
            array(),
            array('grademax' => $changedmax)
        );
        $this->assertTrue($result == GRADE_UPDATE_OK);
        $grades = grade_get_grades($course->id, 'mod', 'assign', $assignment->id);
        $this->assertTrue($grades->items[0]->grademax == $changedmax);

        // Teacher updating 1 student grade
        $student1grade = 23;
        $result = core_grade_external::update_grades(
            'test',
            $course->id,
            'mod_assign',
            $assigment_cm->id,
            0,
            array(array('studentid' => $student1->id, 'grade' => $student1grade))
        );
        $this->assertTrue($result == GRADE_UPDATE_OK);
        $grades = grade_get_grades($course->id, 'mod', 'assign', $assignment->id, array($student1->id));
        $this->assertTrue($grades->items[0]->grades[$student1->id]->grade == $student1grade);

        // Teacher updating multiple student grades
        $student1grade = 11;
        $student2grade = 13;
        $result = core_grade_external::update_grades(
            'test',
            $course->id,
            'mod_assign',
            $assigment_cm->id,
            0,
            array(
                array('studentid' => $student1->id, 'grade' => $student1grade),
                array('studentid' => $student2->id, 'grade' => $student2grade)
            )
        );
        $this->assertTrue($result == GRADE_UPDATE_OK);
        $grades = grade_get_grades($course->id, 'mod', 'assign', $assignment->id, array($student1->id, $student2->id));
        $this->assertTrue($grades->items[0]->grades[$student1->id]->grade == $student1grade);
        $this->assertTrue($grades->items[0]->grades[$student2->id]->grade == $student2grade);

        // Student attempting to update their own grade (should fail)
        $this->setUser($student1);
        try {
            $student1grade = 17;
            $result = core_grade_external::update_grades(
                'test',
                $course->id,
                'mod_assign',
                $assigment_cm->id,
                0,
                array( array('studentid' => $student1->id, 'grade' => $student1grade))
            );
            $this->fail('moodle_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertTrue(true);
        }

        // Parent attempting to update their child's grade (should fail)
        $this->setUser($parent);
        try {
            $student1grade = 13;
            $result = core_grade_external::update_grades(
                'test',
                $course->id,
                'mod_assign',
                $assigment_cm->id,
                0,
                array( array('studentid' => $student1->id, 'grade' => $student1grade))
            );
            $this->fail('moodle_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertTrue(true);
        }

        // Student trying to hide a grade item (should fail).
        $this->setUser($student1);
        try {
            $result = core_grade_external::update_grades(
                'test',
                $course->id,
                'mod_assign',
                $assigment_cm->id,
                0,
                array(),
                array('hidden' => 1)
            );
            $this->fail('moodle_exception expected');
        } catch (moodle_exception $ex) {
            $this->assertTrue(true);
        }

        // Give the student role 'moodle/grade:hide' and they should now be able to hide the grade item.
        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $coursecontext = context_course::instance($course->id);
        assign_capability('moodle/grade:hide', CAP_ALLOW, $studentrole->id, $coursecontext->id);
        accesslib_clear_all_caches_for_unit_testing();

        // Check the activity isn't already hidden.
        $grades = grade_get_grades($course->id, 'mod', 'assign', $assignment->id);
        $this->assertTrue($grades->items[0]->hidden == 0);

        $result = core_grade_external::update_grades(
            'test',
            $course->id,
            'mod_assign',
            $assigment_cm->id,
            0,
            array(),
            array('hidden' => 1)
        );
        $this->assertTrue($result == GRADE_UPDATE_OK);
        $grades = grade_get_grades($course->id, 'mod', 'assign', $assignment->id);
        $this->assertTrue($grades->items[0]->hidden == 1);
    }

}
