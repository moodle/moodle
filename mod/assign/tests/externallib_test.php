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

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External mod assign functions unit tests
 *
 * @package mod_assign
 * @category external
 * @copyright 2012 Paul Charsley
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_external_testcase extends externallib_advanced_testcase {

    /**
     * Tests set up
     */
    protected function setUp() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/externallib.php');
    }

    /**
     * Test get_grades
     */
    public function test_get_grades () {
        global $DB, $USER;

        $this->resetAfterTest(true);
        // Create a course and assignment.
        $coursedata['idnumber'] = 'idnumbercourse';
        $coursedata['fullname'] = 'Lightwork Course';
        $coursedata['summary'] = 'Lightwork Course description';
        $coursedata['summaryformat'] = FORMAT_MOODLE;
        $course = self::getDataGenerator()->create_course($coursedata);

        $assigndata['course'] = $course->id;
        $assigndata['name'] = 'lightwork assignment';

        $assign = self::getDataGenerator()->create_module('assign', $assigndata);

        // Create a manual enrolment record.
        $manual_enrol_data['enrol'] = 'manual';
        $manual_enrol_data['status'] = 0;
        $manual_enrol_data['courseid'] = $course->id;
        $enrolid = $DB->insert_record('enrol', $manual_enrol_data);

        // Create a teacher and give them capabilities.
        $context = context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:viewparticipants', $context->id, 3);
        $context = context_module::instance($assign->id);
        $this->assignUserCapability('mod/assign:grade', $context->id, $roleid);

        // Create the teacher's enrolment record.
        $user_enrolment_data['status'] = 0;
        $user_enrolment_data['enrolid'] = $enrolid;
        $user_enrolment_data['userid'] = $USER->id;
        $DB->insert_record('user_enrolments', $user_enrolment_data);

        // Create a student and give them a grade.
        $student = self::getDataGenerator()->create_user();
        $grade = new stdClass();
        $grade->assignment = $assign->id;
        $grade->userid = $student->id;
        $grade->timecreated = time();
        $grade->timemodified = $grade->timecreated;
        $grade->grader = $USER->id;
        $grade->grade = 75;
        $grade->locked = false;
        $grade->mailed = true;
        $DB->insert_record('assign_grades', $grade);

        $assignmentids[] = $assign->id;
        $result = mod_assign_external::get_grades($assignmentids);

        // Check that the correct grade information for the student is returned.
        $this->assertEquals(1, count($result['assignments']));
        $assignment = $result['assignments'][0];
        $this->assertEquals($assign->id, $assignment['assignmentid']);
        $this->assertEquals(1, count($assignment['grades']));
        $grade = $assignment['grades'][0];
        $this->assertEquals($student->id, $grade['userid']);
        $this->assertEquals(75, $grade['grade']);
    }

    /**
     * Test get_assignments
     */
    public function test_get_assignments () {
        global $DB, $USER;

        $this->resetAfterTest(true);

        $category = self::getDataGenerator()->create_category(array(
            'name' => 'Test category'
        ));

        // Create a course.
        $course1 = self::getDataGenerator()->create_course(array(
            'idnumber' => 'idnumbercourse1',
            'fullname' => 'Lightwork Course 1',
            'summary' => 'Lightwork Course 1 description',
            'summaryformat' => FORMAT_MOODLE,
            'category' => $category->id
        ));

        // Create a second course, just for testing.
        $course2 = self::getDataGenerator()->create_course(array(
            'idnumber' => 'idnumbercourse2',
            'fullname' => 'Lightwork Course 2',
            'summary' => 'Lightwork Course 2 description',
            'summaryformat' => FORMAT_MOODLE,
            'category' => $category->id
        ));

        // Create the assignment module.
        $assign1 = self::getDataGenerator()->create_module('assign', array(
            'course' => $course1->id,
            'name' => 'lightwork assignment'
        ));

        // Create manual enrolment record.
        $enrolid = $DB->insert_record('enrol', (object)array(
            'enrol' => 'manual',
            'status' => 0,
            'courseid' => $course1->id
        ));

        // Create the user and give them capabilities.
        $context = context_course::instance($course1->id);
        $roleid = $this->assignUserCapability('moodle/course:view', $context->id);
        $context = context_module::instance($assign1->id);
        $this->assignUserCapability('mod/assign:view', $context->id, $roleid);

        // Create the user enrolment record.
        $DB->insert_record('user_enrolments', (object)array(
            'status' => 0,
            'enrolid' => $enrolid,
            'userid' => $USER->id
        ));

        $result = mod_assign_external::get_assignments();
        // Check the course and assignment are returned.
        $this->assertEquals(1, count($result['courses']));
        $course = $result['courses'][0];
        $this->assertEquals('Lightwork Course 1', $course['fullname']);
        $this->assertEquals(1, count($course['assignments']));
        $assignment = $course['assignments'][0];
        $this->assertEquals($assign1->id, $assignment['id']);
        $this->assertEquals($course1->id, $assignment['course']);
        $this->assertEquals('lightwork assignment', $assignment['name']);

        $result = mod_assign_external::get_assignments(array($course1->id));
        $this->assertEquals(1, count($result['courses']));
        $course = $result['courses'][0];
        $this->assertEquals('Lightwork Course 1', $course['fullname']);
        $this->assertEquals(1, count($course['assignments']));
        $assignment = $course['assignments'][0];
        $this->assertEquals($assign1->id, $assignment['id']);
        $this->assertEquals($course1->id, $assignment['course']);
        $this->assertEquals('lightwork assignment', $assignment['name']);

        $result = mod_assign_external::get_assignments(array($course2->id));
        $this->assertEquals(0, count($result['courses']));
        $this->assertEquals(1, count($result['warnings']));
    }
}
