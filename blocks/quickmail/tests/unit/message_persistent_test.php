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

use block_quickmail\persistents\message;
use block_quickmail\persistents\message_draft_recipient;
use block_quickmail\persistents\message_recipient;
use block_quickmail\persistents\message_additional_email;

class block_quickmail_message_persistent_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        sets_up_notifications;

    public function test_create_composed_with_recipients_as_draft() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $params = [
            'message_type' => 'message',
            'alternate_email_id' => 4,
            'signature_id' => 6,
            'subject' => 'subject is here',
            'message' => 'the message',
            'receipt' => 0,
            'to_send_at' => 0,
            'no_reply' => 1,
            'mentor_copy' => 1,
        ];

        $message = message::create_type('compose', $userteacher, $course, (object) $params, true);

        $this->assertInstanceOf(message::class, $message);
        $this->assertEquals($course->id, $message->get('course_id'));
        $this->assertEquals($userteacher->id, $message->get('user_id'));
        $this->assertEquals($params['message_type'], $message->get('message_type'));
        $this->assertEquals($params['alternate_email_id'], $message->get('alternate_email_id'));
        $this->assertEquals($params['signature_id'], $message->get('signature_id'));
        $this->assertEquals($params['subject'], $message->get('subject'));
        $this->assertEquals($params['message'], $message->get('body'));
        $this->assertEquals($params['receipt'], $message->get('send_receipt'));
        $this->assertEquals($params['to_send_at'], $message->get('to_send_at'));
        $this->assertEquals($params['no_reply'], $message->get('no_reply'));
        $this->assertEquals($params['mentor_copy'], $message->get('send_to_mentors'));
        $this->assertEquals(1, $message->get('is_draft'));
        $this->assertCount(2, $message->get_substitution_code_classes());
    }

    public function test_getters() {
        $this->resetAfterTest(true);

        $message = message::create_new([
            'course_id' => 1,
            'user_id' => 1,
            'message_type' => 'email',
            'subject' => 'Id dolore irure nostrud dolor eu elit et laborum',
            'body' => 'Id dolore irure nostrud dolor eu elit et laborum sit
                      ullamco laboris cillum consectetur irure quis esse occaecat
                      in amet culpa nulla duis id velit in ut officia.',
        ]);

        // Segun Babalola, 2020-10-30.
        // Actual dates are using config format, so use the same format here to prevent test failure.
        $datetimeformat = get_string('strftimedatetime', 'langconfig');

        $this->assertEquals('Id dolore irure...', $message->get_subject_preview(20));
        $this->assertEquals('Id dolore irure nostrud dolor eu elit et...', $message->get_body_preview(40));

        $this->assertEquals(userdate($message->get('timecreated'), $datetimeformat), $message->get_readable_created_at());
        $this->assertEquals(userdate($message->get('timemodified'), $datetimeformat), $message->get_readable_last_modified_at());
        $this->assertEquals(block_quickmail_string::get('never'), $message->get_readable_sent_at());
        $this->assertEquals(block_quickmail_string::get('never'), $message->get_readable_to_send_at());
    }

    public function test_find_owned_by_user_or_null() {
        $this->resetAfterTest(true);

        $userone = $this->getDataGenerator()->create_user();
        $usertwo = $this->getDataGenerator()->create_user();

        $useronedraft = $this->create_message(true, 1, $userone->id);
        $useronesent = $this->create_message(false, 2, $userone->id);
        $usertwodraft = $this->create_message(true, 3, $usertwo->id);
        $usertwosent = $this->create_message(false, 2, $usertwo->id);

        $mymessage = message::find_owned_by_user_or_null($useronedraft->get('id'), $userone->id);

        $this->assertInstanceOf(message::class, $mymessage);
        $this->assertEquals($useronedraft->get('id'), $mymessage->get('id'));

        $notmymessage = message::find_owned_by_user_or_null($useronesent->get('id'), $usertwo->id);

        $this->assertNull($notmymessage);
    }

    public function test_get_all_message_recipients() {
        $this->resetAfterTest(true);

        $message = $this->create_message();

        $userone = $this->getDataGenerator()->create_user();
        $one = message_recipient::create_new([
            'message_id' => $message->get('id'),
            'user_id' => $userone->id,
        ]);

        $usertwo = $this->getDataGenerator()->create_user();
        $two = message_recipient::create_new([
            'message_id' => $message->get('id'),
            'user_id' => $usertwo->id,
        ]);

        $userthree = $this->getDataGenerator()->create_user();
        $three = message_recipient::create_new([
            'message_id' => $message->get('id'),
            'user_id' => $userthree->id,
        ]);

        $messagerecipients = $message->get_message_recipients();
        $messagerecipientarray = $message->get_message_recipients('all', true);

        $this->assertCount(3, $messagerecipients);
        $this->assertInstanceOf(message_recipient::class, $messagerecipients[0]);
        $this->assertCount(3, $messagerecipientarray);
        $this->assertEquals($usertwo->id, $messagerecipientarray[1]);
    }

    public function test_get_message_recipients_by_status() {
        $this->resetAfterTest(true);

        $message = $this->create_message();

        // Create an unsent-to recip.
        $userone = $this->getDataGenerator()->create_user();
        $one = message_recipient::create_new([
            'message_id' => $message->get('id'),
            'user_id' => $userone->id,
        ]);

        // Create an sent-to recip.
        $usertwo = $this->getDataGenerator()->create_user();
        $two = message_recipient::create_new([
            'message_id' => $message->get('id'),
            'user_id' => $usertwo->id,
            'sent_at' => time()
        ]);

        // Create an unsent-to recip.
        $userthree = $this->getDataGenerator()->create_user();
        $three = message_recipient::create_new([
            'message_id' => $message->get('id'),
            'user_id' => $userthree->id,
        ]);

        $messagerecipients = $message->get_message_recipients('unsent');
        $messagerecipientarray = $message->get_message_recipients('unsent', true);

        $this->assertCount(2, $messagerecipients);
        $this->assertInstanceOf(message_recipient::class, $messagerecipients[0]);
        $this->assertCount(2, $messagerecipientarray);
        $this->assertEquals($userthree->id, $messagerecipientarray[1]);

        $messagerecipients = $message->get_message_recipients('sent');
        $messagerecipientarray = $message->get_message_recipients('sent', true);

        $this->assertCount(1, $messagerecipients);
        $this->assertInstanceOf(message_recipient::class, $messagerecipients[0]);
        $this->assertCount(1, $messagerecipientarray);
        $this->assertEquals($usertwo->id, $messagerecipientarray[0]);
    }

    public function test_get_message_recipient_users() {
        $this->resetAfterTest(true);

        $message = $this->create_message();

        $userone = $this->getDataGenerator()->create_user();
        $one = message_recipient::create_new([
            'message_id' => $message->get('id'),
            'user_id' => $userone->id,
        ]);

        $usertwo = $this->getDataGenerator()->create_user();
        $two = message_recipient::create_new([
            'message_id' => $message->get('id'),
            'user_id' => $usertwo->id,
        ]);

        $userthree = $this->getDataGenerator()->create_user();
        $three = message_recipient::create_new([
            'message_id' => $message->get('id'),
            'user_id' => $userthree->id,
        ]);

        $messagerecipientusers = $message->get_message_recipient_users('all', 'id,email');

        $this->assertIsArray($messagerecipientusers);
        $this->assertCount(3, $messagerecipientusers);
        $this->assertEquals($userone->id, $messagerecipientusers[$userone->id]->id);
        $this->assertEquals($userone->email, $messagerecipientusers[$userone->id]->email);
    }

    public function test_get_additional_emails() {
        $this->resetAfterTest(true);

        $message = $this->create_message();

        $one = message_additional_email::create_new([
            'message_id' => $message->get('id'),
            'email' => 'email@one.com',
        ]);

        $two = message_additional_email::create_new([
            'message_id' => $message->get('id'),
            'email' => 'email@two.com',
        ]);

        $three = message_additional_email::create_new([
            'message_id' => $message->get('id'),
            'email' => 'email@three.com',
        ]);

        $additionalemails = $message->get_additional_emails();
        $additionalemailarray = $message->get_additional_emails(true);

        $this->assertCount(3, $additionalemails);
        $this->assertInstanceOf(message_additional_email::class, $additionalemails[0]);
        $this->assertCount(3, $additionalemailarray);
        $this->assertEquals('email@two.com', $additionalemailarray[1]);
    }

    public function test_sync_recipients() {
        $this->resetAfterTest(true);

        $message = $this->create_message();

        // Create 2 original recipients.
        $userone = $this->getDataGenerator()->create_user();
        $one = message_recipient::create_new([
            'message_id' => $message->get('id'),
            'user_id' => $userone->id,
        ]);

        $usertwo = $this->getDataGenerator()->create_user();
        $two = message_recipient::create_new([
            'message_id' => $message->get('id'),
            'user_id' => $usertwo->id,
        ]);

        $originalrecipientarray = $message->get_message_recipients('all', true);
        $this->assertCount(2, $originalrecipientarray);

        // Create new users to become recipients.
        $userthree = $this->getDataGenerator()->create_user();
        $userfour = $this->getDataGenerator()->create_user();
        $userfive = $this->getDataGenerator()->create_user();

        // Sync the recipients.
        $message->sync_recipients([
            $userthree->id,
            $userfour->id,
            $userfive->id
        ]);

        $newrecipientarray = $message->get_message_recipients('all', true);
        $this->assertCount(3, $newrecipientarray);

        $this->assertCount(0, array_intersect($originalrecipientarray, $newrecipientarray));
    }

    public function test_sync_recipients_caches_count() {
        $this->resetAfterTest(true);

        $message = $this->create_message();

        // Create new users to become recipients.
        $userthree = $this->getDataGenerator()->create_user();
        $userfour = $this->getDataGenerator()->create_user();
        $userfive = $this->getDataGenerator()->create_user();

        // Sync the recipients.
        $message->sync_recipients([
            $userthree->id,
            $userfour->id,
            $userfive->id
        ]);

        $cache = \cache::make('block_quickmail', 'qm_msg_recip_count');
        $cachedcount = $cache->get($message->get('id'));
        $this->assertEquals(3, $cachedcount);

        $value = $message->cached_recipient_count();
        $this->assertEquals(3, $value);
    }

    public function test_sync_additional_emails() {
        $this->resetAfterTest(true);

        $message = $this->create_message();

        // Create 2 original additional emails.
        $one = message_additional_email::create_new([
            'message_id' => $message->get('id'),
            'email' => 'email@one.com',
        ]);

        $two = message_additional_email::create_new([
            'message_id' => $message->get('id'),
            'email' => 'email@two.com',
        ]);

        $originalemailarray = $message->get_additional_emails(true);
        $this->assertCount(2, $originalemailarray);

        $newemailarray = ['email@three.com', 'email@four.com', 'email@five.com'];

        $message->sync_additional_emails($newemailarray);

        $this->assertCount(3, $message->get_additional_emails(true));

        $this->assertCount(0, array_intersect($originalemailarray, $newemailarray));
    }

    public function test_sync_additional_emails_caches_count() {
        $this->resetAfterTest(true);

        $message = $this->create_message();

        $newemailarray = ['email@three.com', 'email@four.com', 'email@five.com'];

        $message->sync_additional_emails($newemailarray);

        $cache = \cache::make('block_quickmail', 'qm_msg_addl_email_count');
        $cachedcount = $cache->get($message->get('id'));
        $this->assertEquals(3, $cachedcount);

        $value = $message->cached_additional_email_count();
        $this->assertEquals(3, $value);
    }

    public function test_sync_compose_draft_recipients() {
        $this->resetAfterTest(true);

        $message = $this->create_message();

        // Create some includes and excludes (with some invalid).
        $includes = [
            'not_good',
            'role_1',
            'role_a',
            'role_3',
            'group_2',
            'group_4',
            'user_11',
            'user_15',
            'something_else'
        ];

        $excludes = [
            'role_1',
            'role_2',
            'group_2',
            'group_45',
            'user_19',
            'user_15',
            'invalid_key'
        ];

        $message->sync_compose_draft_recipients($includes, $excludes);

        $count = message_draft_recipient::get_records(['message_id' => $message->get('id')]);

        $this->assertCount(12, $count);

        $draftrecipients = $message->get_message_draft_recipients();

        $this->assertCount(12, $draftrecipients);

        $first = $draftrecipients[0];

        $this->assertInstanceOf(message_draft_recipient::class, $first);
        $this->assertEquals('include', $first->get('type'));
        $this->assertEquals('role', $first->get('recipient_type'));
        $this->assertEquals('1', $first->get('recipient_id'));
        $this->assertNotNull($first->get('timecreated'));
        $this->assertNotNull($first->get('timemodified'));

        message_draft_recipient::clear_all_for_message($message);

        $count = message_draft_recipient::get_records(['message_id' => $message->get('id')]);

        $this->assertCount(0, $count);

        $message = $this->create_message();

        // Create some includes and excludes for this new message.
        $includes = [
            'role_1',
            'role_3',
            'group_2',
            'group_4',
            'user_11',
            'user_15',
        ];

        $excludes = [
            'role_1',
            'role_2',
            'group_2',
            'group_45',
            'user_19',
            'user_15',
            'invalid_key'
        ];

        $message->sync_compose_draft_recipients($includes, $excludes);

        $count = message_draft_recipient::get_records(['message_id' => $message->get('id')]);

        $this->assertCount(12, $count);

        // Create some different includes and excludes for this same message.
        $includes = [
            'role_2',
            'role_3',
            'group_1',
            'user_17',
            'user_18',
        ];

        $excludes = [
            'role_5',
            'role_not_good',
            'user_19',
            'user_15',
            'invalid_key'
        ];

        $message->sync_compose_draft_recipients($includes, $excludes);

        $count = message_draft_recipient::get_records(['message_id' => $message->get('id')]);

        $this->assertCount(8, $count);
    }

    public function test_message_draft_status() {
        $this->resetAfterTest(true);

        $message = message::create_new([
            'course_id' => 1,
            'user_id' => 1,
            'message_type' => 'email',
            'is_draft' => true
        ]);

        $this->assertTrue($message->is_message_draft());
        $this->assertEquals(block_quickmail_string::get('drafted'), $message->get_status());
    }

    public function test_message_queued_status() {
        $this->resetAfterTest(true);

        $message = message::create_new([
            'course_id' => 1,
            'user_id' => 1,
            'message_type' => 'email',
            'to_send_at' => time()
        ]);

        $this->assertTrue($message->is_queued_message());
        $this->assertEquals(block_quickmail_string::get('queued'), $message->get_status());
    }

    public function test_message_sending_status() {
        $this->resetAfterTest(true);

        $message = message::create_new([
            'course_id' => 1,
            'user_id' => 1,
            'message_type' => 'email',
            'is_sending' => true
        ]);

        $this->assertTrue($message->is_being_sent());
        $this->assertEquals(block_quickmail_string::get('sending'), $message->get_status());
    }

    public function test_message_sent_status() {
        $this->resetAfterTest(true);

        $message = message::create_new([
            'course_id' => 1,
            'user_id' => 1,
            'message_type' => 'email',
            'sent_at' => time()
        ]);

        $this->assertTrue($message->is_sent_message());
        $this->assertEquals(block_quickmail_string::get('sent'), $message->get_status());
    }

    public function test_message_get_to_send_in_future() {
        $this->resetAfterTest(true);

        $now = time();
        $nextweek = $now + (7 * 24 * 60 * 60);

        $messagenow = message::create_new([
            'course_id' => 1,
            'user_id' => 1,
            'message_type' => 'email',
            'to_send_at' => $now
        ]);

        $messagefuture = message::create_new([
            'course_id' => 1,
            'user_id' => 1,
            'message_type' => 'email',
            'to_send_at' => $nextweek
        ]);

        $this->assertFalse($messagenow->get_to_send_in_future());
        $this->assertTrue($messagefuture->get_to_send_in_future());
    }

    public function test_create_composed_with_no_recipients_as_draft() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $params = [
            'message_type' => 'message',
            'alternate_email_id' => 4,
            'signature_id' => 6,
            'subject' => 'subject is here',
            'message' => 'the message',
            'receipt' => 0,
            'to_send_at' => 0,
            'no_reply' => 1,
            'mentor_copy' => 1,
        ];

        $message = message::create_type('compose', $userteacher, $course, (object) $params, true);

        $this->assertInstanceOf(message::class, $message);
        $this->assertEquals($course->id, $message->get('course_id'));
        $this->assertEquals($userteacher->id, $message->get('user_id'));
        $this->assertEquals($params['message_type'], $message->get('message_type'));
        $this->assertEquals($params['alternate_email_id'], $message->get('alternate_email_id'));
        $this->assertEquals($params['signature_id'], $message->get('signature_id'));
        $this->assertEquals($params['subject'], $message->get('subject'));
        $this->assertEquals($params['message'], $message->get('body'));
        $this->assertEquals($params['receipt'], $message->get('send_receipt'));
        $this->assertEquals($params['to_send_at'], $message->get('to_send_at'));
        $this->assertEquals($params['no_reply'], $message->get('no_reply'));
        $this->assertEquals($params['mentor_copy'], $message->get('send_to_mentors'));
        $this->assertEquals(1, $message->get('is_draft'));
    }

    public function test_create_composed_not_as_draft() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $params = [
            'message_type' => 'message',
            'alternate_email_id' => 4,
            'signature_id' => 6,
            'subject' => 'subject is here',
            'message' => 'the message',
            'receipt' => 0,
            'to_send_at' => 0,
            'no_reply' => 1,
            'mentor_copy' => 1,
        ];

        $message = message::create_type('compose', $userteacher, $course, (object) $params, false);

        $this->assertInstanceOf(message::class, $message);
        $this->assertEquals(0, $message->get('is_draft'));
    }

    public function test_update_draft_as_draft() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $creationparams = [
            'message_type' => 'message',
            'alternate_email_id' => 4,
            'signature_id' => 6,
            'subject' => 'subject is here',
            'message' => 'the message',
            'receipt' => 0,
            'to_send_at' => 0,
            'no_reply' => 1,
            'mentor_copy' => 1,
        ];

        $message = message::create_type('compose', $userteacher, $course, (object) $creationparams, true);

        $updateparams = [
            'message_type' => 'email',
            'alternate_email_id' => 5,
            'signature_id' => 7,
            'subject' => 'an updated subject is here',
            'message' => 'the updated message',
            'receipt' => 1,
            'to_send_at' => 1518124011,
            'no_reply' => 0,
            'mentor_copy' => 0,
        ];

        $updatedmessage = $message->update_draft((object) $updateparams);

        $this->assertInstanceOf(message::class, $updatedmessage);
        $this->assertEquals($course->id, $updatedmessage->get('course_id'));
        $this->assertEquals($userteacher->id, $updatedmessage->get('user_id'));
        $this->assertEquals($updateparams['message_type'], $updatedmessage->get('message_type'));
        $this->assertEquals($updateparams['alternate_email_id'], $updatedmessage->get('alternate_email_id'));
        $this->assertEquals($updateparams['signature_id'], $updatedmessage->get('signature_id'));
        $this->assertEquals($updateparams['subject'], $updatedmessage->get('subject'));
        $this->assertEquals($updateparams['message'], $updatedmessage->get('body'));
        $this->assertEquals($updateparams['receipt'], $updatedmessage->get('send_receipt'));
        $this->assertEquals($updateparams['to_send_at'], $updatedmessage->get('to_send_at'));
        $this->assertEquals($updateparams['no_reply'], $updatedmessage->get('no_reply'));
        $this->assertEquals($updateparams['mentor_copy'], $updatedmessage->get('send_to_mentors'));
        $this->assertEquals(1, $updatedmessage->get('is_draft'));

        $secondupdateparams = [
            'message_type' => 'email',
            'alternate_email_id' => 5,
            'signature_id' => 7,
            'subject' => 'an updated subject is here',
            'message' => 'the updated message',
            'receipt' => 1,
            'to_send_at' => 1518124011,
            'no_reply' => 0,
            'mentor_copy' => 0,
        ];

        $secondupdatedmessage = $message->update_draft((object) $secondupdateparams, false);

        $this->assertEquals(0, $secondupdatedmessage->get('is_draft'));
    }

    public function test_filter_messages_by_course_from_array() {
        $this->resetAfterTest(true);

        $messages = [];

        $messages[] = $this->create_message(false, 1, 1);
        $messages[] = $this->create_message(false, 2, 1);
        $messages[] = $this->create_message(false, 3, 1);
        $messages[] = $this->create_message(false, 4, 1);
        $messages[] = $this->create_message(false, 3, 1);
        $messages[] = $this->create_message(false, 2, 1);
        $messages[] = $this->create_message(false, 1, 1);

        $filtered = message::filter_messages_by_course($messages, 1);
        $this->assertCount(2, $filtered);

        $filtered = message::filter_messages_by_course($messages, 4);
        $this->assertCount(1, $filtered);
    }

    public function test_unqueue_message() {
        $this->resetAfterTest(true);

        $queuedmessage = message::create_new([
            'course_id' => 1,
            'user_id' => 1,
            'message_type' => 'email',
            'to_send_at' => time()
        ]);

        $this->assertTrue($queuedmessage->is_queued_message());
        $this->assertEquals(block_quickmail_string::get('queued'), $queuedmessage->get_status());

        $queuedmessage->unqueue();

        $this->assertFalse($queuedmessage->is_queued_message());
        $this->assertEquals(block_quickmail_string::get('drafted'), $queuedmessage->get_status());
    }

    public function test_creates_message_from_reminder_notification() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $params = [
            'name' => 'My Reminder Notification',
            'schedule_unit' => 'week',
            'schedule_amount' => 1,
            'schedule_begin_at' => time(),
            'schedule_end_at' => null,
            'max_per_interval' => 0,
            'message_type' => 'email',
            'subject' => 'This is the subject',
            'body' => 'This is the body',
            'is_enabled' => 1,
            'alternate_email_id' => 0,
            'signature_id' => 0,
            'editor_format' => 1,
            'send_receipt' => 0,
            'send_to_mentors' => 0,
            'no_reply' => 1,
            'conditions' => '',
            'condition_time_amount' => 4,
            'condition_time_unit' => 'day',
        ];

        $remindernotification = $this->create_reminder_notification_for_course_user('course-non-participation',
                                                                                    $course,
                                                                                    $userteacher,
                                                                                    null,
                                                                                    $params);

        $notification = $remindernotification->get_notification();

        $message = message::create_from_notification($notification, []);

        $this->assertInstanceOf(message::class, $message);
        $this->assertEquals($course->id, $message->get('course_id'));
        $this->assertEquals($userteacher->id, $message->get('user_id'));
        $this->assertEquals($params['message_type'], $message->get('message_type'));
        $this->assertEquals($params['alternate_email_id'], $message->get('alternate_email_id'));
        $this->assertEquals($params['signature_id'], $message->get('signature_id'));
        $this->assertEquals($params['subject'], $message->get('subject'));
        $this->assertEquals($params['body'], $message->get('body'));
        $this->assertEquals($params['send_receipt'], $message->get('send_receipt'));
        $this->assertEquals($params['no_reply'], $message->get('no_reply'));
        $this->assertEquals($params['send_to_mentors'], $message->get('send_to_mentors'));
        $this->assertEquals(0, $message->get('is_draft'));
    }

    // Helpers.
    private function create_message($isdraft = false, $courseid = 1, $userid = 1) {
        return message::create_new([
            'course_id' => $courseid,
            'user_id' => $userid,
            'message_type' => 'email',
            'is_draft' => $isdraft
        ]);
    }

    private function create_message_and_recipient() {
        $message = $this->create_message();

        $recipient = message_recipient::create_new([
            'message_id' => $message->get('id'),
            'user_id' => 1,
        ]);

        return [$message, $recipient];
    }

}
