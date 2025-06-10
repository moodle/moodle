<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/turnitintooltwo/classes/nonsubmitters/nonsubmitters_message.php');

/**
 * Tests for classes/digitalreceipt/nonsubmitters_message
 *
 * @package turnitintooltwo
 */
class mod_turnitintooltwo_nonsubmitter_message_testcase extends advanced_testcase {

    /**
     * Test that non submitter messages send.
     */
    public function test_send_instructor_message() {
        $this->resetAfterTest();
        $this->preventResetByRollback();

        $sink = $this->redirectMessages();

        $nonsubmitters_message = new nonsubmitters_message();

        // Generate two new users to send messages to.
        $user1 = $this->getDataGenerator()->create_user();

        // Send message to both instructors.
        $nonsubmitters_message->send_message($user1->id, 'Nonsubmitters Subject', 'Nonsubmitters Message', 1);

        $messages = $sink->get_messages();

        $this->assertEquals(1, count($messages));
        $this->assertEquals('Nonsubmitters Subject', $messages[0]->subject);
        $this->assertEquals('Nonsubmitters Message', $messages[0]->fullmessage);
        $this->assertEquals($user1->id, $messages[0]->useridto);
    }
}
