<?php

/**
 * Unit tests for mod_turnitintooltwo classes/digitalreceipt/receipt_message
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/turnitintooltwo/classes/digitalreceipt/receipt_message.php');

class mod_turnitintooltwo_receipt_message_testcase extends advanced_testcase {

    public function test_send_message() {
        global $DB;

        $this->resetAfterTest();            // Reset the DB when finished
        $this->preventResetByRollback();    // Messaging doesn't support rollback

        $sink = $this->redirectMessages();  // Collect the emails

        $userOne = $this->getDataGenerator()->create_user();

        $receipt_message = new receipt_message();

        $receipt_message->send_message($userOne, "Test message for email", 123);

        $this->assertEquals(1, $sink->count()); // One email sent

        $messages = $sink->get_messages();

        // Correct user was sent an email
        $this->assertEquals($userOne->id, $messages[0]->useridto);
        $this->assertEquals("This is your Turnitin Digital Receipt", $messages[0]->subject);
        $this->assertEquals("Test message for email", $messages[0]->fullmessage);
        $this->assertEquals("Test message for email", $messages[0]->fullmessagehtml);
        $this->assertEquals(1, $messages[0]->fullmessageformat); // HTML format
    }

    public function test_build_message_single_part() {

        $receipt_message = new receipt_message();

        $date = date('c');

        $message = [];
        $message['firstname']        = 'test_user_firstname';
        $message['lastname']         = 'test_user_lastname';
        $message['submission_title'] = 'test submission title';
        $message['assignment_name']  = 'test assignment name';
        $message['course_fullname']  = 'test course name';
        $message['submission_date']  = $date;
        $message['submission_id']    = '12345';

        $response = $receipt_message->build_message($message);

        $message_text = format_string("Dear %s %s,<br /><br />You have successfully submitted the file <strong>%s</strong> to the assignment <strong>%s</strong> in the class <strong>%s</strong> on <strong>%s</strong>. Your submission id is <strong>%s</strong>. Your full digital receipt can be viewed and printed from the assignment inbox or from the print/download button in the document viewer.<br /><br />Thank you for using Turnitin,<br /><br />The Turnitin Team");

        $this->assertEquals(sprintf($message_text, $message['firstname'], $message['lastname'], $message['submission_title'], $message['assignment_name'], $message['course_fullname'], $date, $message['submission_id']) , $response);
    }

    public function test_build_message_multi_part() {
        $receipt_message = new receipt_message();

        $date = date('c');

        $message = [];
        $message['firstname']        = 'test_user_firstname';
        $message['lastname']         = 'test_user_lastname';
        $message['submission_title'] = 'test submission title';
        $message['assignment_name']  = 'test assignment name';
        $message['assignment_part']  = 'assignment_part';
        $message['course_fullname']  = 'test course name';
        $message['submission_date']  = $date;
        $message['submission_id']    = '12345';

        $response = $receipt_message->build_message($message);

        $messagetext = format_string("Dear %s %s,<br /><br />You have successfully submitted the file <strong>%s</strong> to the assignment <strong>%s: %s</strong> in the class <strong>%s</strong> on <strong>%s</strong>. Your submission id is <strong>%s</strong>. Your full digital receipt can be viewed and printed from the assignment inbox or from the print/download button in the document viewer.<br /><br />Thank you for using Turnitin,<br /><br />The Turnitin Team");

        $this->assertEquals(sprintf($messagetext, $message['firstname'], $message['lastname'],
            $message['submission_title'], $message['assignment_name'], $message['assignment_part'],
            $message['course_fullname'], $date, $message['submission_id']), $response);
    }
}
