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
 * Tests the send email task.
 *
 * @package message_email
 * @category test
 * @copyright 2018 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/message/tests/messagelib_test.php');

/**
 * Class for testing the send email task.
 *
 * @package message_email
 * @category test
 * @copyright 2019 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_message_send_email_task_testcase extends advanced_testcase {

    /**
     * Test sending email task.
     */
    public function test_sending_email_task() {
        global $DB, $SITE;

        $this->preventResetByRollback(); // Messaging is not compatible with transactions.

        $this->resetAfterTest();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        $user1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $user2 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create two groups in the course.
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        groups_add_member($group1->id, $user1->id);
        groups_add_member($group2->id, $user1->id);

        groups_add_member($group1->id, $user2->id);
        groups_add_member($group2->id, $user2->id);

        $conversation1 = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id],
            'Group 1', \core_message\api::MESSAGE_CONVERSATION_ENABLED,
            'core_group',
            'groups',
            $group1->id,
            context_course::instance($course->id)->id
        );

        $conversation2 = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id],
            'Group 2',
            \core_message\api::MESSAGE_CONVERSATION_ENABLED,
            'core_group',
            'groups',
            $group2->id,
            context_course::instance($course->id)->id
        );

        // Go through each conversation.
        if ($conversations = $DB->get_records('message_conversations')) {
            foreach ($conversations as $conversation) {
                $conversationid = $conversation->id;

                // Let's send 5 messages.
                for ($i = 1; $i <= 5; $i++) {
                    $message = new \core\message\message();
                    $message->courseid = 1;
                    $message->component = 'moodle';
                    $message->name = 'instantmessage';
                    $message->userfrom = $user1;
                    $message->convid = $conversationid;
                    $message->subject = 'message subject';
                    $message->fullmessage = 'message body';
                    $message->fullmessageformat = FORMAT_MARKDOWN;
                    $message->fullmessagehtml = '<p>message body</p>';
                    $message->smallmessage = 'small message';
                    $message->notification = '0';

                    message_send($message);
                }
            }
        }

        $this->assertEquals(10, $DB->count_records('message_email_messages'));

        // Only 1 email is sent as the messages are included in it at a digest.
        $sink = $this->redirectEmails();
        $task = new \message_email\task\send_email_task();
        $task->execute();
        $this->assertEquals(1, $sink->count());

        // Confirm it contains the correct data.
        $emails = $sink->get_messages();
        $email = reset($emails);
        $sitename = format_string($SITE->fullname);
        $this->assertSame(get_string('messagedigestemailsubject', 'message_email', $sitename), $email->subject);
        $this->assertSame($user2->email, $email->to);
        $this->assertNotEmpty($email->header);
        $emailbody = quoted_printable_decode($email->body);
        $this->assertContains('Group 1', $emailbody);
        $this->assertContains('Group 2', $emailbody);
        // 5 unread messages per conversation, this will be listed twice.
        $this->assertRegExp("/<span\b[^>]*>5<\/span> <span\b[^>]*>Unread message\w+/", $emailbody);

        // Confirm table was emptied after task was run.
        $this->assertEquals(0, $DB->count_records('message_email_messages'));

        // Confirm running it again does not send another.
        $sink = $this->redirectEmails();
        $task = new \message_email\task\send_email_task();
        $task->execute();
        $this->assertEquals(0, $sink->count());
    }
}
