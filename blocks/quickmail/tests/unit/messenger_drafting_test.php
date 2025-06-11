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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/traits/unit_testcase_traits.php');

use block_quickmail\messenger\messenger;
use block_quickmail\persistents\message;
use block_quickmail\persistents\signature;
use block_quickmail\exceptions\validation_exception;

class block_quickmail_messenger_drafting_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        submits_compose_message_form,
        sends_emails,
        sends_messages,
        assigns_mentors;

    public function test_messenger_saves_draft_email() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $sink = $this->open_email_sink();

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Specify recipients.
        $recipients['included']['user'] = $this->get_user_ids_from_user_array($userstudents);

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($recipients, 'email', [
            'subject' => 'Hello world',
            'body' => 'This is one fine body.',
        ]);

        // Save this email message as a draft.
        $message = messenger::save_compose_draft($userteacher, $course, $composeformdata);

        $messagerecipients = $message->get_message_recipients();

        $this->assertEquals(0, $this->email_sink_email_count($sink));
        $this->assertCount(4, $messagerecipients);
        $this->assertInstanceOf(message::class, $message);
        $this->assertEquals(1, $message->get('is_draft'));

        $this->close_email_sink($sink);
    }

    public function test_cannot_duplicate_a_draft_that_not_created_by_the_given_user() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Specify recipients.
        $recipients['included']['user'] = $this->get_user_ids_from_user_array($userstudents);

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($recipients, 'email', [
            'subject' => 'Hello world',
            'body' => 'This is one fine body.',
        ]);

        // Save this email message as a draft.
        $draftmessage = messenger::save_compose_draft($userteacher, $course, $composeformdata);

        $this->expectException(validation_exception::class);

        // Now attempt to duplicate this draft which belongs to the teacher.
        $duplicateddraft = messenger::duplicate_draft($draftmessage->get('id'), $userstudents[0]);

        $this->assertNotInstanceOf(message::class, $duplicateddraft);
    }

    public function test_duplicates_drafts() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Specify recipients.
        $recipients['included']['user'] = $this->get_user_ids_from_user_array($userstudents);

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($recipients, 'email', [
            'subject' => 'Hello world',
            'body' => 'This is one fine body.',
        ]);

        // Save this email message as a draft.
        $draftmessage = messenger::save_compose_draft($userteacher, $course, $composeformdata);

        // Now attempt to duplicate this draft.
        $duplicateddraft = messenger::duplicate_draft($draftmessage->get('id'), $userteacher);
        $this->assertInstanceOf(message::class, $duplicateddraft);
        $this->assertEquals($draftmessage->get('course_id'), $duplicateddraft->get('course_id'));
        $this->assertEquals($draftmessage->get('user_id'), $duplicateddraft->get('user_id'));
        $this->assertEquals($draftmessage->get('message_type'), $duplicateddraft->get('message_type'));
        $this->assertEquals($draftmessage->get('alternate_email_id'), $duplicateddraft->get('alternate_email_id'));
        $this->assertEquals($draftmessage->get('signature_id'), $duplicateddraft->get('signature_id'));
        $this->assertEquals($draftmessage->get('subject'), $duplicateddraft->get('subject'));
        $this->assertEquals($draftmessage->get('body'), $duplicateddraft->get('body'));
        $this->assertEquals($draftmessage->get('editor_format'), $duplicateddraft->get('editor_format'));
        $this->assertEquals(1, $duplicateddraft->get('is_draft'));
        $this->assertEquals($draftmessage->get('send_receipt'), $duplicateddraft->get('send_receipt'));
        $this->assertEquals($draftmessage->get('no_reply'), $duplicateddraft->get('no_reply'));

        $draftmessagerecipients = $draftmessage->get_message_recipients();
        $duplicateddraftrecipients = $duplicateddraft->get_message_recipients();
        $this->assertEquals(count($draftmessagerecipients), count($duplicateddraftrecipients));

        $draftmessageadditionalemails = $draftmessage->get_additional_emails();
        $duplicateddraftadditionalemails = $duplicateddraft->get_additional_emails();
        $this->assertEquals(count($draftmessageadditionalemails), count($duplicateddraftadditionalemails));
    }

}
