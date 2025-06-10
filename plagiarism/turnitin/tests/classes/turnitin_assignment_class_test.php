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
 * Unit tests for plagiarism/turnitin/classes/modules/turnitin_assign.class.php.
 *
 * @package    plagiarism_turnitin
 * @copyright  2018 Turnitin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/plagiarism/turnitin/lib.php');
require_once($CFG->dirroot . '/plagiarism/turnitin/classes/turnitin_assignment.class.php');
require_once($CFG->dirroot . '/mod/assign/externallib.php');

/**
 * Tests for Turnitin assignment class
 *
 * @package turnitin
 */
class plagiarism_turnitin_assignment_class_testcase extends advanced_testcase {

    /**
     * Set Overwrite mtrace to avoid output during the tests.
     */
    public function setUp(): void {
        global $CFG;

        // Overwrite mtrace.
        $CFG->mtrace_wrapper = 'plagiarism_turnitin_mtrace';
    }

    public function test_get_course_data() {
        global $DB;

        $this->resetAfterTest();

        // Create a PP course.
        $course = new stdClass();
        $course->courseid = 1;
        $course->turnitin_ctl = "Test Course";
        $course->turnitin_cid = 10;

        // Insert the course to the plagiarism turnitin courses table.
        $DB->insert_record('plagiarism_turnitin_courses', $course);

        $response = turnitin_assignment::get_course_data(1, "site");

        $this->assertEquals($course->turnitin_ctl, $response->turnitin_ctl);
        $this->assertEquals($course->turnitin_cid, $response->turnitin_cid);
    }

    public function test_create_tii_course() {
        global $DB;

        $this->resetAfterTest();

        // Create a PP course.
        $course = new stdClass();
        $course->courseid = 1;
        $course->turnitin_ctl = "Test Course";
        $course->turnitin_cid = 10;
        $course->fullname = "This is a test course";
        $course->tii_rel_id = 1234;

        // Insert the course to the plagiarism turnitin courses table.
        $course->id = $DB->insert_record('plagiarism_turnitin_courses', $course);

        // Stub a fake tii comms.
        $faketiicomms = $this->getMockBuilder(turnitin_comms::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mock initialise_api method.
        $faketiicomms->expects($this->any())
            ->method('initialise_api')
            ->with("");

        $mock = $this->getMockBuilder('turnitin_assignment')
            ->setMethods(array('api_create_class', 'api_get_class', 'api_get_class_id'))
            ->setConstructorArgs(array(0, $faketiicomms))
            ->getMock();

        $mock->expects($this->any())
            ->method('api_get_class_id')
            ->willReturn(1);

        $response = $mock->create_tii_course($course, 1);

        $expected = new stdClass();
        $expected->id = $course->tii_rel_id;
        $expected->turnitin_cid = 1;
        $expected->turnitin_ctl = "This is a test course (Moodle PP)";
        $expected->courseid = $course->id;

        // We should expect that the course above was updated.
        $this->assertEquals($expected, $response);

        // If we remove $course->tii_rel_id then we should expect a new course to be added with a new ID.
        unset($course->tii_rel_id);
        $response = $mock->create_tii_course($course, 1);
        $this->assertNotEquals($expected->id, $response->id);
    }

    public function test_edit_tii_course() {
        global $DB;

        $this->resetAfterTest();

        // Create a PP course.
        $course = new stdClass();
        $course->courseid = 1;
        $course->turnitin_ctl = "Test Course";
        $course->turnitin_cid = 1;
        $course->fullname = "This is a test course";
        $course->tii_rel_id = 1234;

        // Insert the course to the plagiarism turnitin courses table.
        $course->id = $DB->insert_record('plagiarism_turnitin_courses', $course);

        // Stub a fake tii comms.
        $faketiicomms = $this->getMockBuilder(turnitin_comms::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mock initialise_api method.
        $faketiicomms->expects($this->any())
            ->method('initialise_api')
            ->with("");

        $mock = $this->getMockBuilder('turnitin_assignment')
            ->setMethods(array('api_update_class', 'api_set_class_id'))
            ->setConstructorArgs(array(0, $faketiicomms))
            ->getMock();

        // Edit a PP course.
        $editcourse = new stdClass();
        $editcourse->id = 1;
        $editcourse->turnitin_cid = 10;
        $editcourse->fullname = "This is an edited test course";
        $editcourse->tii_rel_id = 1234;

        // As the method does not return anything we will have to check the database before assertion.
        $mock->edit_tii_course($editcourse);

        $responsecourse = $DB->get_record("plagiarism_turnitin_courses", array("id" => $course->id));

        $expected = new stdClass();
        $expected->id = $course->id;
        $expected->courseid = $course->courseid;
        $expected->turnitin_ctl = "This is an edited test course (Moodle PP)";
        $expected->turnitin_cid = 10;

        // We should expect that the original course was updated with the values we passed in.
        $this->assertEquals($expected, $responsecourse);
    }

    public function test_truncate_title() {
        $this->resetAfterTest();

        $title = "This is a very long title that we are going to use to test the truncate title method.";
        $limit = 50;

        $response = turnitin_assignment::truncate_title($title, $limit);

        $this->assertEquals('This is a very long title that we a... (Moodle PP)', $response);

        // Try a title that is within our limit.
        $response = turnitin_assignment::truncate_title("This title should not be truncated.", $limit);
        $this->assertEquals('This title should not be truncated. (Moodle PP)', $response);
    }

    public function test_create_tii_assignment() {
        $this->resetAfterTest();

        // Create a PP assignment.
        $assignment = new stdClass();
        $assignment->id = 1;

        // Stub a fake tii comms.
        $faketiicomms = $this->getMockBuilder(turnitin_comms::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mock initialise_api method.
        $faketiicomms->expects($this->any())
            ->method('initialise_api')
            ->with("");

        $mock = $this->getMockBuilder('turnitin_assignment')
            ->setMethods(array('api_create_assignment', 'api_get_assignment', 'api_get_assignment_id'))
            ->setConstructorArgs(array(0, $faketiicomms))
            ->getMock();

        $mock->expects($this->any())
            ->method('api_get_assignment_id')
            ->willReturn(1);

        $response = $mock->create_tii_assignment($assignment);

        // We should expect that the assignment was created.
        $this->assertEquals(1, $response);
    }

    public function test_edit_tii_assignment() {
        $this->resetAfterTest();

        // Create a PP assignment.
        $assignment = new stdClass();
        $assignment->id = 1;
        $assignment->title = "This is a test assignment.";

        // Stub a fake tii comms.
        $faketiicomms = $this->getMockBuilder(turnitin_comms::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mock initialise_api method.
        $faketiicomms->expects($this->any())
            ->method('initialise_api')
            ->with("");

        // Mock handle_exceptions method.
        $faketiicomms->expects($this->any())
            ->method('handle_exceptions')
            ->withAnyParameters();

        $mock = $this->getMockBuilder('turnitin_assignment')
            ->setMethods(array('api_update_assignment', 'api_get_assignment_id', 'api_get_title'))
            ->setConstructorArgs(array(0, $faketiicomms))
            ->getMock();

        $mock->expects($this->any())
            ->method('api_get_assignment_id')
            ->willReturn(1);

        $mock->expects($this->any())
            ->method('api_get_title')
            ->willReturn($assignment->title);

        $response = $mock->edit_tii_assignment($assignment);

        // We should expect that the assignment above was updated.
        $this->assertEquals(true, $response["success"]);
        $this->assertEquals(1, $response["tiiassignmentid"]);

        // Test the exception handling for default workflow.
        $mock->expects($this->any())
            ->method('api_update_assignment')
            ->will($this->throwException(new Exception()));

        $response = $mock->edit_tii_assignment($assignment);
        $this->assertEquals(false, $response["success"]);
        $this->assertEquals(get_string('editassignmenterror', 'plagiarism_turnitin'), $response["error"]);

        // Test the error handling for the cron workflow.
        $error = new stdClass();
        $error->title = $assignment->title;
        $error->assignmentid = 1;

        $response = $mock->edit_tii_assignment($assignment, "cron");
        $this->assertEquals(false, $response["success"]);
        $this->assertEquals(get_string('ppassignmentediterror', 'plagiarism_turnitin', $error), $response["error"]);
        $this->assertEquals(1, $response["tiiassignmentid"]);
    }

    public function test_get_peermark_assignments() {
        global $DB;

        $this->resetAfterTest();

        // Create a PP course.
        $peermark = new stdClass();
        $peermark->parent_tii_assign_id = 1;
        $peermark->title = "This is a test Peermark assignment.";
        $peermark->tiiassignid = 1;
        $peermark->dtstart = 1530000000;
        $peermark->dtdue = 1530000000;
        $peermark->dtpost = 1530000000;
        $peermark->maxmarks = 100;

        // Insert the peermark to the plagiarism turnitin courses table.
        $DB->insert_record('plagiarism_turnitin_peermark', $peermark);

        $assignment = new turnitin_assignment(0, 1);

        // We should have a peermark object.
        $response = $assignment->get_peermark_assignments(1, $peermark->parent_tii_assign_id);
        $this->assertCount(1, $response);

        // We should not have a peermark object as we didn't insert one with ID 2.
        $response = $assignment->get_peermark_assignments(2, $peermark->parent_tii_assign_id);
        $this->assertCount(0, $response);
    }
}
