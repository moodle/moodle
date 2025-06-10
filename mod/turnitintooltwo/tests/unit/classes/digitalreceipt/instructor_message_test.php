<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/turnitintooltwo/classes/digitalreceipt/instructor_message.php');

/**
 * Tests for classes/digitalreceipt/instructor_message
 *
 * @package turnitintooltwo
 */
class mod_turnitintooltwo_instructor_message_testcase extends advanced_testcase {

    /**
     * Test data being passed in will generate the correct output text.
     */
    public function test_build_instructor_message() {
        $instructormessage = new instructor_message();

        $data = [
            'submission_title' => 'Foo',
            'assignment_name' => 'Bar',
            'course_fullname' => 'Foobar',
            'submission_date' => '01-09-1994',
            'submission_id' => '1234567'
        ];

        $this->assertEquals(
            format_string('A submission entitled <strong>Foo</strong> has been made to assignment <strong>Bar</strong> in the class <strong>Foobar</strong>.<br /><br />Submission ID: <strong>1234567</strong><br />Submission Date: <strong>01-09-1994</strong><br />'),
            $instructormessage->build_instructor_message($data)
        );
    }

    /**
     * Test data being passed in will generate the correct output text, with assignment part.
     */
    public function test_build_instructor_message_with_assignment_part() {
        $instructor_message = new instructor_message();

        $data = [
            'submission_title' => 'Foo',
            'assignment_name' => 'Bar',
            'course_fullname' => 'Foobar',
            'submission_date' => '01-09-1994',
            'submission_id' => '1234567',
            'assignment_part' => 'Part 2'
        ];

        $this->assertEquals(
            format_string('A submission entitled <strong>Foo</strong> has been made to assignment <strong>Bar: Part 2</strong> in the class <strong>Foobar</strong>.<br /><br />Submission ID: <strong>1234567</strong><br />Submission Date: <strong>01-09-1994</strong><br />'),
            $instructor_message->build_instructor_message($data)
        );
    }

    /**
     * Test that multiple messages get sent for instructors.
     */
    public function test_send_instructor_message() {
        $this->resetAfterTest();
        $this->preventResetByRollback();

        $sink = $this->redirectMessages();

        $instructormessage = new instructor_message();

        // Generate two new users to send messages to.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $instructors = [
            $user1,
            $user2
        ];

        // Send message to both instructors.
        $instructormessage->send_instructor_message($instructors, 'Instructor Message', 123);

        $messages = $sink->get_messages();

        $this->assertEquals(2, count($messages));
        $this->assertEquals('Instructor Message', $messages[0]->fullmessage);
        $this->assertEquals('Instructor Message', $messages[1]->fullmessage);

        $this->assertEquals($user1->id, $messages[0]->useridto);
        $this->assertEquals($user2->id, $messages[1]->useridto);
    }
}
