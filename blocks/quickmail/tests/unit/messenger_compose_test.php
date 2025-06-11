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

class block_quickmail_messenger_compose_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        submits_compose_message_form,
        sends_emails,
        sends_messages,
        assigns_mentors;

    public function test_messenger_sends_composed_email_now() {
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

        // Send an email from the teacher to the students now (not as queued adhoc tasks).
        messenger::compose($userteacher, $course, $composeformdata, null, false);

        $this->assertEquals(4, $this->email_sink_email_count($sink));
        $this->assertEquals('Hello world', $this->email_in_sink_attr($sink, 1, 'subject'));
        $this->assertTrue($this->email_in_sink_body_contains($sink, 1, 'This is one fine body.'));
        $this->assertEquals(get_config('moodle', 'noreplyaddress'), $this->email_in_sink_attr($sink, 1, 'from'));
        $this->close_email_sink($sink);
    }

    public function test_messenger_sends_composed_email_including_mentors_now() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $sink = $this->open_email_sink();

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Assign a mentor to the first student.
        $mentoruser = $this->create_mentor_for_user($userstudents[0]);

        // Specify recipients.
        $recipients['included']['user'] = $this->get_user_ids_from_user_array($userstudents);

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($recipients, 'email', [
            'subject' => 'Hello world',
            'body' => 'This is one fine body.',
            'mentor_copy' => 1,
        ]);

        // Send an email from the teacher to the students now (not as queued adhoc tasks).
        messenger::compose($userteacher, $course, $composeformdata, null, false);

        // Should have been sent to 4 users + 1 mentor.
        $this->assertEquals(5, $this->email_sink_email_count($sink));
        $this->assertEquals('Hello world', $this->email_in_sink_attr($sink, 1, 'subject'));
        $this->assertTrue($this->email_in_sink_body_contains($sink, 1, 'This is one fine body.'));
        $this->assertEquals(get_config('moodle', 'noreplyaddress'), $this->email_in_sink_attr($sink, 1, 'from'));
        $this->close_email_sink($sink);
    }

    public function test_messenger_does_not_send_compose_message_with_invalid_params() {
        $this->expectException(validation_exception::class);

        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $sink = $this->open_email_sink();

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($userstudents, 'email', [
            'subject' => '',
            'body' => 'This is one fine body.',
        ]);

        // Send an email from the teacher to the students now (not as queued adhoc tasks).
        messenger::compose($userteacher, $course, $composeformdata, null, false);

        $this->assertEquals(0, $this->email_sink_email_count($sink));

        $this->close_email_sink($sink);
    }

    public function test_messenger_sends_composed_message_now() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $sink = $this->open_message_sink();

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Specify recipients.
        $recipients['included']['user'] = $this->get_user_ids_from_user_array($userstudents);

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($recipients, 'message', []);

        // Send a moodle message from the teacher to the students now (not as queued adhoc tasks).
        messenger::compose($userteacher, $course, $composeformdata, null, false);

        $this->assertEquals(4, $this->message_sink_message_count($sink));
        $this->close_message_sink($sink);
    }

    public function test_skips_invalid_user_ids_when_sending() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $sink = $this->open_email_sink();

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Specify recipients, some invalid.
        $recipients['included']['user'] = ['12', '24', '36', '48', $userstudents[0]->id];

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($recipients, 'email', [
            'subject' => 'Hello world',
            'body' => 'This is one fine body.',
        ]);

        // Send an email from the teacher to the students as queued adhoc tasks).
        messenger::compose($userteacher, $course, $composeformdata, null, false);

        $this->assertEquals(1, $this->email_sink_email_count($sink));

        $this->close_email_sink($sink);
    }

    public function test_messenger_does_not_send_scheduled_composed_email_now() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $sink = $this->open_email_sink();

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Specify recipients.
        $recipients['included']['user'] = $this->get_user_ids_from_user_array($userstudents);

        $now = time();
        $nextweek = $now + (7 * 24 * 60 * 60);

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($recipients, 'email', [
            'to_send_at' => $nextweek
        ]);

        // Schedule an email from the teacher to the students (as queued adhoc tasks).
        messenger::compose($userteacher, $course, $composeformdata);

        \phpunit_util::run_all_adhoc_tasks();

        $this->assertEquals(0, $this->email_sink_email_count($sink));
        $this->close_email_sink($sink);
    }

    public function test_messenger_sends_to_additional_emails() {
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
            'additional_emails' => 'additional@one.com,additional@two.com,additional@three.com'
        ]);

        // Send an email from the teacher to the students now (not as queued adhoc tasks).
        messenger::compose($userteacher, $course, $composeformdata, null, false);

        $this->assertEquals(7, $this->email_sink_email_count($sink));
        $this->assertEquals('Hello world', $this->email_in_sink_attr($sink, 7, 'subject'));
        $this->assertTrue($this->email_in_sink_body_contains($sink, 7, 'This is one fine body.'));
        $this->assertEquals(get_config('moodle', 'noreplyaddress'), $this->email_in_sink_attr($sink, 7, 'from'));
        $this->assertEquals('additional@three.com', $this->email_in_sink_attr($sink, 7, 'to'));

        $this->close_email_sink($sink);
    }

    public function test_messenger_sends_a_receipt_if_asked() {
        // Segun Babalola, 2020-10-30.
        // Various minor fixes to get tests passing.

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
            'receipt' => '1'
        ]);

        // Send an email from the teacher to the students now (not as queued adhoc tasks).
        messenger::compose($userteacher, $course, $composeformdata, null, false);

        $this->assertEquals(5, $this->email_sink_email_count($sink));
        $this->assertEquals(block_quickmail_string::get('send_receipt_subject_addendage') . ': Hello world',
            $this->email_in_sink_attr($sink, 5, 'subject'));
        $this->assertTrue($this->email_in_sink_body_contains(
            $sink, 5, 'This is one fine body.'));
        $this->assertEquals(get_config('moodle', 'noreplyaddress'), $this->email_in_sink_attr($sink, 5, 'from'));
        $this->assertEquals($userteacher->email, $this->email_in_sink_attr($sink, 5, 'to'));

        $this->close_email_sink($sink);
    }

    public function test_messenger_sends_with_signature_appended() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $sink = $this->open_email_sink();

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        // Create a signature for the teacher.
        $signature = signature::create_new([
            'user_id' => $userteacher->id,
            'title' => 'mine',
            'signature' => '<p>This is my signature! Signed, The Teacher!</p>',
        ]);

        // Specify recipients.
        $recipients['included']['user'] = $this->get_user_ids_from_user_array($userstudents);

        // Get a compose form submission.
        $composeformdata = $this->get_compose_message_form_submission($recipients, 'email', [
            'subject' => 'Hello world',
            'body' => 'This is one fine body.',
            'signature_id' => $signature->get('id')
        ]);

        // Send an email from the teacher to the students now (not as queued adhoc tasks).
        messenger::compose($userteacher, $course, $composeformdata, null, false);

        $this->assertTrue($this->email_in_sink_body_contains($sink, 1, 'This is one fine body.'));
        $this->assertTrue($this->email_in_sink_body_contains($sink, 1, 'This is my signature! Signed, The Teacher!'));

        $this->close_email_sink($sink);
    }

}
