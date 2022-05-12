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

namespace core_message\task;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/message/tests/messagelib_test.php');

/**
 * Class for testing the migrate message data task.
 *
 * @package core_message
 * @category test
 * @copyright 2018 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migrate_message_data_test extends \advanced_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Test migrating legacy messages.
     */
    public function test_migrating_messages() {
        global $DB;

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        // Get the current time minus some, to make sure our data is migrated accurately and just not using the current timestamp.
        $now = time();
        $timedeleted1 = $now - (2 * DAYSECS);
        $timedeleted2 = $now - (2 * DAYSECS) + 1;
        $timeread1 = $now - DAYSECS;
        $timeread2 = $now - DAYSECS + 1;
        $timeread3 = $now - DAYSECS + 2;

        // Send messages from user 1 to user 2.
        $m1 = $this->create_legacy_message_or_notification($user1->id, $user2->id, 1, false, $timeread1);
        $m2 = $this->create_legacy_message_or_notification($user1->id, $user2->id, 2);
        $m3 = $this->create_legacy_message_or_notification($user1->id, $user2->id, 3);

        // Send messages from user 3 to user 1.
        $m4 = $this->create_legacy_message_or_notification($user3->id, $user1->id, 4, false, $timeread2);
        $m5 = $this->create_legacy_message_or_notification($user3->id, $user1->id, 5);
        $m6 = $this->create_legacy_message_or_notification($user3->id, $user1->id, 6);

        // Send messages from user 3 to user 2.
        $m7 = $this->create_legacy_message_or_notification($user3->id, $user2->id, 7, false, $timeread3);
        $m8 = $this->create_legacy_message_or_notification($user3->id, $user2->id, 8);
        $m9 = $this->create_legacy_message_or_notification($user3->id, $user2->id, 9);

        // Let's delete some messages, not using API here as it does not use the legacy tables.
        $messageupdate = new \stdClass();
        $messageupdate->id = $m1;
        $messageupdate->timeusertodeleted = $timedeleted1;
        $DB->update_record('message_read', $messageupdate);

        $messageupdate = new \stdClass();
        $messageupdate->id = $m5;
        $messageupdate->timeuserfromdeleted = $timedeleted2;
        $DB->update_record('message', $messageupdate);

        // Now, let's execute the task for user 1.
        $task = new migrate_message_data();
        $task->set_custom_data(
            [
                'userid' => $user1->id
            ]
        );
        $task->execute();

        // Ok, now we need to confirm all is good.
        // Remember - we are only converting the messages related to user 1.
        $this->assertEquals(2, $DB->count_records('message'));
        $this->assertEquals(1, $DB->count_records('message_read'));
        $this->assertEquals(6, $DB->count_records('messages'));
        $this->assertEquals(0, $DB->count_records('notifications'));
        $this->assertEquals(0, $DB->count_records('message_popup_notifications'));

        // Get the conversations.
        $conversation1 = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);
        $conversation2 = \core_message\api::get_conversation_between_users([$user1->id, $user3->id]);

        // Confirm what we have in the messages table is correct.
        $messages = $DB->get_records('messages', [], 'timecreated ASC');
        $i = 1;
        foreach ($messages as $message) {
            $useridfrom = $user1->id;
            $conversationid = $conversation1;
            if ($i > 3) {
                $useridfrom = $user3->id;
                $conversationid = $conversation2;
            }

            if ($i == 1) {
                $messagereadid1 = $message->id;
                $messagedeletedid1 = $message->id;
            } else if ($i == 4) {
                $messagereadid2 = $message->id;
            } else if ($i == 5) {
                $messagedeletedid2 = $message->id;
            }

            $this->assertEquals($useridfrom, $message->useridfrom);
            $this->assertEquals($conversationid, $message->conversationid);
            $this->assertEquals('Subject ' . $i, $message->subject);
            $this->assertEquals('Full message ' . $i, $message->fullmessage);
            $this->assertEquals(FORMAT_PLAIN, $message->fullmessageformat);
            $this->assertEquals('Full message HTML '. $i, $message->fullmessagehtml);
            $this->assertEquals('Small message ' . $i, $message->smallmessage);
            $this->assertEquals($i, $message->timecreated);
            $i++;
        }

        // Confirm there are 4 actions.
        $this->assertEquals(4, $DB->count_records('message_user_actions'));

        // Confirm the messages that were marked as read have actions associated with them.
        $muas = $DB->get_records('message_user_actions', ['action' => \core_message\api::MESSAGE_ACTION_READ], 'timecreated DESC');
        $this->assertCount(2, $muas);

        // Message user action for message read by user 1 (referring to $m4).
        $mua1 = array_shift($muas);
        // Message user action for message read by user 2 (referring to $m1).
        $mua2 = array_shift($muas);

        $this->assertEquals($user1->id, $mua1->userid);
        $this->assertEquals($messagereadid2, $mua1->messageid);
        $this->assertEquals($timeread2, $mua1->timecreated);

        $this->assertEquals($user2->id, $mua2->userid);
        $this->assertEquals($messagereadid1, $mua2->messageid);
        $this->assertEquals($timeread1, $mua2->timecreated);

        // Confirm the messages that were deleted have actions associated with them.
        $muas = $DB->get_records('message_user_actions', ['action' => \core_message\api::MESSAGE_ACTION_DELETED],
            'timecreated DESC');
        $this->assertCount(2, $muas);

        // Message user action for message deleted by user 3 (referring to $m5).
        $mua1 = array_shift($muas);
        // Message user action for message deleted by user 2 (referring to $m1).
        $mua2 = array_shift($muas);

        $this->assertEquals($user3->id, $mua1->userid);
        $this->assertEquals($messagedeletedid2, $mua1->messageid);
        $this->assertEquals($timedeleted2, $mua1->timecreated);

        $this->assertEquals($user2->id, $mua2->userid);
        $this->assertEquals($messagedeletedid1, $mua2->messageid);
        $this->assertEquals($timedeleted1, $mua2->timecreated);
    }

    /**
     * Test migrating legacy notifications.
     */
    public function test_migrating_notifications() {
        global $DB;

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        // Get the current time minus some, to make sure our data is migrated accurately and just not using the current timestamp.
        $timeread = time() - DAYSECS;

        // Send notifications from user 1 to user 2.
        $this->create_legacy_message_or_notification($user1->id, $user2->id, 1, true, $timeread);
        $this->create_legacy_message_or_notification($user1->id, $user2->id, 2, true);
        $this->create_legacy_message_or_notification($user1->id, $user2->id, 3, true);

        // Send notifications from user 3 to user 1.
        $this->create_legacy_message_or_notification($user3->id, $user1->id, 4, true, $timeread);
        $this->create_legacy_message_or_notification($user3->id, $user1->id, 5, true);
        $this->create_legacy_message_or_notification($user3->id, $user1->id, 6, true);

        // Send notifications from user 3 to user 2.
        $this->create_legacy_message_or_notification($user3->id, $user2->id, 7, true, $timeread);
        $this->create_legacy_message_or_notification($user3->id, $user2->id, 8, true);
        $this->create_legacy_message_or_notification($user3->id, $user2->id, 9, true);

        // Now, let's execute the task for user 1.
        $task = new migrate_message_data();
        $task->set_custom_data(
            [
                'userid' => $user1->id
            ]
        );
        $task->execute();

        // Ok, now we need to confirm all is good.
        // Remember - we are only converting the notifications related to user 1.
        $this->assertEquals(2, $DB->count_records('message'));
        $this->assertEquals(1, $DB->count_records('message_read'));
        $this->assertEquals(3, $DB->count_records('message_popup'));
        $this->assertEquals(6, $DB->count_records('notifications'));
        $this->assertEquals(6, $DB->count_records('message_popup_notifications'));

        // Confirm what we have in the notifications table is correct.
        $notifications = $DB->get_records('notifications', [], 'timecreated ASC');
        $popupnotifications = $DB->get_records('message_popup_notifications', [], 'notificationid ASC', 'notificationid');
        $i = 1;
        foreach ($notifications as $notification) {
            // Assert the correct id is stored in the 'message_popup_notifications' table.
            $this->assertArrayHasKey($notification->id, $popupnotifications);

            $useridfrom = $user1->id;
            $useridto = $user2->id;
            if ($i > 3) {
                $useridfrom = $user3->id;
                $useridto = $user1->id;
            }

            $this->assertEquals($useridfrom, $notification->useridfrom);
            $this->assertEquals($useridto, $notification->useridto);
            $this->assertEquals('Subject ' . $i, $notification->subject);
            $this->assertEquals('Full message ' . $i, $notification->fullmessage);
            $this->assertEquals(FORMAT_PLAIN, $notification->fullmessageformat);
            $this->assertEquals('Full message HTML '. $i, $notification->fullmessagehtml);
            $this->assertEquals('Small message ' . $i, $notification->smallmessage);
            $this->assertEquals('mod_assign', $notification->component);
            $this->assertEquals('assign_notification', $notification->eventtype);
            $this->assertEquals('https://www.google.com', $notification->contexturl);
            $this->assertEquals('google', $notification->contexturlname);
            $this->assertEquals($i, $notification->timecreated);

            if (($i == 1) || ($i == 4)) {
                $this->assertEquals($timeread, $notification->timeread);
            } else {
                $this->assertNull($notification->timeread);
            }

            $i++;
        }
    }

    /**
     * Test migrating a legacy message that contains null as the format.
     */
    public function test_migrating_message_null_format() {
        global $DB;

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->create_legacy_message_or_notification($user1->id, $user2->id, null, false, null, null);

        // Now, let's execute the task for user 1.
        $task = new migrate_message_data();
        $task->set_custom_data(
            [
                'userid' => $user1->id
            ]
        );
        $task->execute();

        $messages = $DB->get_records('messages');
        $this->assertCount(1, $messages);

        $message = reset($messages);
        $this->assertEquals(FORMAT_MOODLE, $message->fullmessageformat);
    }

    /**
     * Test migrating a legacy notification that contains null as the format.
     */
    public function test_migrating_notification_null_format() {
        global $DB;

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->create_legacy_message_or_notification($user1->id, $user2->id, null, true, null, null);

        // Now, let's execute the task for user 1.
        $task = new migrate_message_data();
        $task->set_custom_data(
            [
                'userid' => $user1->id
            ]
        );
        $task->execute();

        $notifications = $DB->get_records('notifications');
        $this->assertCount(1, $notifications);

        $notification = reset($notifications);
        $this->assertEquals(FORMAT_MOODLE, $notification->fullmessageformat);
    }

    /**
     * Test migrating a legacy message that a user sent to themselves then deleted.
     */
    public function test_migrating_message_deleted_message_sent_to_self() {
        global $DB;

        // Create user to test with.
        $user1 = $this->getDataGenerator()->create_user();

        $m1 = $this->create_legacy_message_or_notification($user1->id, $user1->id, null, false, null, null);

        // Let's delete the message for the 'user to' and 'user from' which in this case is the same user.
        $messageupdate = new \stdClass();
        $messageupdate->id = $m1;
        $messageupdate->timeuserfromdeleted = time();
        $messageupdate->timeusertodeleted = time();
        $DB->update_record('message', $messageupdate);

        // Now, let's execute the task for the user.
        $task = new migrate_message_data();
        $task->set_custom_data(
            [
                'userid' => $user1->id
            ]
        );
        $task->execute();

        $this->assertEquals(0, $DB->count_records('message'));
        $this->assertEquals(1, $DB->count_records('message_user_actions'));
    }

    /**
     * Creates a legacy message or notification to be used for testing.
     *
     * @param int $useridfrom The user id from
     * @param int $useridto The user id to
     * @param int $timecreated
     * @param bool $notification
     * @param int|null $timeread The time the message/notification was read, null if it hasn't been.
     * @param string|int|null $format The format of the message.
     * @return int The id of the message (in either the message or message_read table)
     * @throws dml_exception
     */
    private function create_legacy_message_or_notification($useridfrom, $useridto, $timecreated = null,
            $notification = false, $timeread = null, $format = FORMAT_PLAIN) {
        global $DB;

        $tabledata = new \stdClass();

        if (is_null($timecreated)) {
            $timecreated = time();
        }

        if (!is_null($timeread)) {
            $table = 'message_read';
            $tabledata->timeread = $timeread;
        } else {
            $table = 'message';
        }

        if ($notification) {
            $tabledata->eventtype = 'assign_notification';
            $tabledata->component = 'mod_assign';
            $tabledata->notification = 1;
            $tabledata->contexturl = 'https://www.google.com';
            $tabledata->contexturlname = 'google';
        } else {
            $tabledata->eventtype = 'instantmessage';
            $tabledata->component = 'moodle';
            $tabledata->notification = 0;
        }

        $tabledata->useridfrom = $useridfrom;
        $tabledata->useridto = $useridto;
        $tabledata->subject = 'Subject ' . $timecreated;
        $tabledata->fullmessage = 'Full message ' . $timecreated;
        $tabledata->fullmessageformat = $format;
        $tabledata->fullmessagehtml = 'Full message HTML ' . $timecreated;
        $tabledata->smallmessage = 'Small message ' . $timecreated;
        $tabledata->timecreated = $timecreated;

        $id = $DB->insert_record($table, $tabledata);

        // Insert into the legacy 'message_popup' table if it is a notification.
        if ($notification) {
            $mp = new \stdClass();
            $mp->messageid = $id;
            $mp->isread = (!is_null($timeread)) ? 1 : 0;

            $DB->insert_record('message_popup', $mp);
        }

        return $id;
    }
}
