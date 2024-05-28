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
 * Tests for feedback events.
 *
 * @package    mod_feedback
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace mod_feedback\event;

/**
 * Class mod_feedback_events_testcase
 *
 * Class for tests related to feedback events.
 *
 * @package    mod_feedback
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class events_test extends \advanced_testcase {

    /** @var  stdClass A user who likes to interact with feedback activity. */
    private $eventuser;

    /** @var  stdClass A course used to hold feedback activities for testing. */
    private $eventcourse;

    /** @var  stdClass A feedback activity used for feedback event testing. */
    private $eventfeedback;

    /** @var  stdClass course module object . */
    private $eventcm;

    /** @var  stdClass A feedback item. */
    private $eventfeedbackitem;

    /** @var  stdClass A feedback activity response submitted by user. */
    private $eventfeedbackcompleted;

    /** @var  stdClass value associated with $eventfeedbackitem . */
    private $eventfeedbackvalue;

    public function setUp(): void {
        global $DB;
        parent::setUp();

        $this->setAdminUser();
        $gen = $this->getDataGenerator();
        $this->eventuser = $gen->create_user(); // Create a user.
        $course = $gen->create_course(); // Create a course.
        // Assign manager role, so user can see reports.
        role_assign(1, $this->eventuser->id, \context_course::instance($course->id));

        // Add a feedback activity to the created course.
        $record = new \stdClass();
        $record->course = $course->id;
        $feedback = $gen->create_module('feedback', $record);
        $this->eventfeedback = $DB->get_record('feedback', array('id' => $feedback->id), '*', MUST_EXIST); // Get exact copy.
        $this->eventcm = get_coursemodule_from_instance('feedback', $this->eventfeedback->id, false, MUST_EXIST);

        // Create a feedback item.
        $item = new \stdClass();
        $item->feedback = $this->eventfeedback->id;
        $item->type = 'numeric';
        $item->presentation = '0|0';
        $itemid = $DB->insert_record('feedback_item', $item);
        $this->eventfeedbackitem = $DB->get_record('feedback_item', array('id' => $itemid), '*', MUST_EXIST);

        // Create a response from a user.
        $response = new \stdClass();
        $response->feedback = $this->eventfeedback->id;
        $response->userid = $this->eventuser->id;
        $response->anonymous_response = FEEDBACK_ANONYMOUS_YES;
        $completedid = $DB->insert_record('feedback_completed', $response);
        $this->eventfeedbackcompleted = $DB->get_record('feedback_completed', array('id' => $completedid), '*', MUST_EXIST);

        $value = new \stdClass();
        $value->course_id = $course->id;
        $value->item = $this->eventfeedbackitem->id;
        $value->completed = $this->eventfeedbackcompleted->id;
        $value->value = 25; // User response value.
        $valueid = $DB->insert_record('feedback_value', $value);
        $this->eventfeedbackvalue = $DB->get_record('feedback_value', array('id' => $valueid), '*', MUST_EXIST);
        // Do this in the end to get correct sortorder and cacherev values.
        $this->eventcourse = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);

    }

    /**
     * Tests for event response_deleted.
     */
    public function test_response_deleted_event(): void {
        global $USER, $DB;
        $this->resetAfterTest();

        // Create and delete a module.
        $sink = $this->redirectEvents();
        feedback_delete_completed($this->eventfeedbackcompleted->id);
        $events = $sink->get_events();
        $event = array_pop($events); // Delete feedback event.
        $sink->close();

        // Validate event data.
        $this->assertInstanceOf('\mod_feedback\event\response_deleted', $event);
        $this->assertEquals($this->eventfeedbackcompleted->id, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals($this->eventuser->id, $event->relateduserid);
        $this->assertEquals('feedback_completed', $event->objecttable);
        $this->assertEquals(null, $event->get_url());
        $this->assertEquals($this->eventfeedbackcompleted, $event->get_record_snapshot('feedback_completed', $event->objectid));
        $this->assertEquals($this->eventcourse, $event->get_record_snapshot('course', $event->courseid));
        $this->assertEquals($this->eventfeedback, $event->get_record_snapshot('feedback', $event->other['instanceid']));

        // Test can_view() .
        $this->setUser($this->eventuser);
        $this->assertFalse($event->can_view());
        $this->assertDebuggingCalled();
        $this->setAdminUser();
        $this->assertTrue($event->can_view());
        $this->assertDebuggingCalled();

        // Create a response, with anonymous set to no and test can_view().
        $response = new \stdClass();
        $response->feedback = $this->eventcm->instance;
        $response->userid = $this->eventuser->id;
        $response->anonymous_response = FEEDBACK_ANONYMOUS_NO;
        $completedid = $DB->insert_record('feedback_completed', $response);
        $DB->get_record('feedback_completed', array('id' => $completedid), '*', MUST_EXIST);
        $value = new \stdClass();
        $value->course_id = $this->eventcourse->id;
        $value->item = $this->eventfeedbackitem->id;
        $value->completed = $completedid;
        $value->value = 25; // User response value.
        $DB->insert_record('feedback_valuetmp', $value);

        // Save the feedback.
        $sink = $this->redirectEvents();
        feedback_delete_completed($completedid);
        $events = $sink->get_events();
        $event = array_pop($events); // Response submitted feedback event.
        $sink->close();

        // Test can_view() .
        $this->setUser($this->eventuser);
        $this->assertTrue($event->can_view());
        $this->assertDebuggingCalled();
        $this->setAdminUser();
        $this->assertTrue($event->can_view());
        $this->assertDebuggingCalled();
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for event validations related to feedback response deletion.
     */
    public function test_response_deleted_event_exceptions(): void {

        $this->resetAfterTest();

        $context = \context_module::instance($this->eventcm->id);

        // Test not setting other['anonymous'].
        try {
            \mod_feedback\event\response_submitted::create(array(
                'context'  => $context,
                'objectid' => $this->eventfeedbackcompleted->id,
                'relateduserid' => 2,
            ));
            $this->fail("Event validation should not allow \\mod_feedback\\event\\response_deleted to be triggered without
                    other['anonymous']");
        } catch (\coding_exception $e) {
            $this->assertStringContainsString("The 'anonymous' value must be set in other.", $e->getMessage());
        }
    }

    /**
     * Tests for event response_submitted.
     */
    public function test_response_submitted_event(): void {
        global $USER, $DB;
        $this->resetAfterTest();
        $this->setUser($this->eventuser);

        // Create a temporary response, with anonymous set to yes.
        $response = new \stdClass();
        $response->feedback = $this->eventcm->instance;
        $response->userid = $this->eventuser->id;
        $response->anonymous_response = FEEDBACK_ANONYMOUS_YES;
        $completedid = $DB->insert_record('feedback_completedtmp', $response);
        $completed = $DB->get_record('feedback_completedtmp', array('id' => $completedid), '*', MUST_EXIST);
        $value = new \stdClass();
        $value->course_id = $this->eventcourse->id;
        $value->item = $this->eventfeedbackitem->id;
        $value->completed = $completedid;
        $value->value = 25; // User response value.
        $DB->insert_record('feedback_valuetmp', $value);

        // Save the feedback.
        $sink = $this->redirectEvents();
        $id = feedback_save_tmp_values($completed);
        $events = $sink->get_events();
        $event = array_pop($events); // Response submitted feedback event.
        $sink->close();

        // Validate event data. Feedback is anonymous.
        $this->assertInstanceOf('\mod_feedback\event\response_submitted', $event);
        $this->assertEquals($id, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals($USER->id, $event->relateduserid);
        $this->assertEquals('feedback_completed', $event->objecttable);
        $this->assertEquals(1, $event->anonymous);
        $this->assertEquals(FEEDBACK_ANONYMOUS_YES, $event->other['anonymous']);
        $this->setUser($this->eventuser);
        $this->assertFalse($event->can_view());
        $this->assertDebuggingCalled();
        $this->setAdminUser();
        $this->assertTrue($event->can_view());
        $this->assertDebuggingCalled();

        // Create a temporary response, with anonymous set to no.
        $response = new \stdClass();
        $response->feedback = $this->eventcm->instance;
        $response->userid = $this->eventuser->id;
        $response->anonymous_response = FEEDBACK_ANONYMOUS_NO;
        $completedid = $DB->insert_record('feedback_completedtmp', $response);
        $completed = $DB->get_record('feedback_completedtmp', array('id' => $completedid), '*', MUST_EXIST);
        $value = new \stdClass();
        $value->course_id = $this->eventcourse->id;
        $value->item = $this->eventfeedbackitem->id;
        $value->completed = $completedid;
        $value->value = 25; // User response value.
        $DB->insert_record('feedback_valuetmp', $value);

        // Save the feedback.
        $sink = $this->redirectEvents();
        feedback_save_tmp_values($completed);
        $events = $sink->get_events();
        $event = array_pop($events); // Response submitted feedback event.
        $sink->close();

        // Test can_view().
        $this->assertTrue($event->can_view());
        $this->assertDebuggingCalled();
        $this->setAdminUser();
        $this->assertTrue($event->can_view());
        $this->assertDebuggingCalled();
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for event validations related to feedback response submission.
     */
    public function test_response_submitted_event_exceptions(): void {

        $this->resetAfterTest();

        $context = \context_module::instance($this->eventcm->id);

        // Test not setting instanceid.
        try {
            \mod_feedback\event\response_submitted::create(array(
                'context'  => $context,
                'objectid' => $this->eventfeedbackcompleted->id,
                'relateduserid' => 2,
                'anonymous' => 0,
                'other'    => array('cmid' => $this->eventcm->id, 'anonymous' => 2)
            ));
            $this->fail("Event validation should not allow \\mod_feedback\\event\\response_deleted to be triggered without
                    other['instanceid']");
        } catch (\coding_exception $e) {
            $this->assertStringContainsString("The 'instanceid' value must be set in other.", $e->getMessage());
        }

        // Test not setting cmid.
        try {
            \mod_feedback\event\response_submitted::create(array(
                'context'  => $context,
                'objectid' => $this->eventfeedbackcompleted->id,
                'relateduserid' => 2,
                'anonymous' => 0,
                'other'    => array('instanceid' => $this->eventfeedback->id, 'anonymous' => 2)
            ));
            $this->fail("Event validation should not allow \\mod_feedback\\event\\response_deleted to be triggered without
                    other['cmid']");
        } catch (\coding_exception $e) {
            $this->assertStringContainsString("The 'cmid' value must be set in other.", $e->getMessage());
        }

        // Test not setting anonymous.
        try {
            \mod_feedback\event\response_submitted::create(array(
                 'context'  => $context,
                 'objectid' => $this->eventfeedbackcompleted->id,
                 'relateduserid' => 2,
                 'other'    => array('cmid' => $this->eventcm->id, 'instanceid' => $this->eventfeedback->id)
            ));
            $this->fail("Event validation should not allow \\mod_feedback\\event\\response_deleted to be triggered without
                    other['anonymous']");
        } catch (\coding_exception $e) {
            $this->assertStringContainsString("The 'anonymous' value must be set in other.", $e->getMessage());
        }
    }

    /**
     * Test that event observer is executed on course deletion and the templates are removed.
     */
    public function test_delete_course(): void {
        global $DB;
        $this->resetAfterTest();
        feedback_save_as_template($this->eventfeedback, 'my template', 0);
        $courseid = $this->eventcourse->id;
        $this->assertNotEmpty($DB->get_records('feedback_template', array('course' => $courseid)));
        delete_course($this->eventcourse, false);
        $this->assertEmpty($DB->get_records('feedback_template', array('course' => $courseid)));
    }
}
