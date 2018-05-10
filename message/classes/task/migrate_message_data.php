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
 * Adhoc task handling migrating data to the new messaging table schema.
 *
 * @package    core_message
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Class handling migrating data to the new messaging table schema.
 *
 * @package    core_message
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migrate_message_data extends \core\task\adhoc_task {

    /**
     * Run the migration task.
     */
    public function execute() {
        global $DB;

        $userid = $this->get_custom_data()->userid;

        // Get the user's preference.
        $hasbeenmigrated = get_user_preferences('core_message_migrate_data', false, $userid);

        if (!$hasbeenmigrated) {
            // To determine if we should update the preference.
            $updatepreference = true;

            // Get all the users the current user has received a message from.
            $sql = "SELECT DISTINCT(useridfrom)
                      FROM {message} m
                     WHERE useridto = ?
                     UNION
                    SELECT DISTINCT(useridfrom)
                      FROM {message_read} m
                     WHERE useridto = ?";
            $users = $DB->get_records_sql($sql, [$userid, $userid]);

            // Get all the users the current user has messaged.
            $sql = "SELECT DISTINCT(useridto)
                      FROM {message} m
                     WHERE useridfrom = ?
                     UNION
                    SELECT DISTINCT(useridto)
                      FROM {message_read} m
                     WHERE useridfrom = ?";
            $users = $users + $DB->get_records_sql($sql, [$userid, $userid]);
            if (!empty($users)) {
                // Loop through each user and migrate the data.
                foreach ($users as $otheruserid => $user) {
                    $ids = [$userid, $otheruserid];
                    sort($ids);
                    $key = implode('_', $ids);

                    // Set the lock data.
                    $timeout = 5; // In seconds.
                    $locktype = 'core_message_migrate_data';

                    // Get an instance of the currently configured lock factory.
                    $lockfactory = \core\lock\lock_config::get_lock_factory($locktype);

                    // See if we can grab this lock.
                    if ($lock = $lockfactory->get_lock($key, $timeout)) {
                        try {
                            $transaction = $DB->start_delegated_transaction();
                            $this->migrate_data($userid, $otheruserid);
                            $transaction->allow_commit();
                        } catch (\Throwable $e) {
                            $updatepreference = false;
                        }

                        $lock->release();
                    } else {
                        // Couldn't get a lock, move on to next user but make sure we don't update user preference so
                        // we still try again.
                        $updatepreference = false;
                        continue;
                    }
                }
            }

            if ($updatepreference) {
                set_user_preference('core_message_migrate_data', true, $userid);
            } else {
                // Throwing an exception in the task will mean that it isn't removed from the queue and is tried again.
                throw new \moodle_exception('Task failed.');
            }
        }
    }

    /**
     * Helper function to deal with migrating the data.
     *
     * @param int $userid The current user id.
     * @param int $otheruserid The user id of the other user in the conversation.
     * @throws \dml_exception
     */
    private function migrate_data($userid, $otheruserid) {
        global $DB;

        if (!$conversationid = \core_message\api::get_conversation_between_users([$userid, $otheruserid])) {
            $conversationid = \core_message\api::create_conversation_between_users([$userid, $otheruserid]);
        }

        // First, get the rows from the 'message' table.
        $select = "(useridfrom = ? AND useridto = ?) OR (useridfrom = ? AND useridto = ?)";
        $params = [$userid, $otheruserid, $otheruserid, $userid];
        $messages = $DB->get_recordset_select('message', $select, $params, 'id ASC');
        foreach ($messages as $message) {
            if ($message->notification) {
                $this->migrate_notification($message, false);
            } else {
                $this->migrate_message($conversationid, $message);
            }
        }
        $messages->close();

        // Ok, all done, delete the records from the 'message' table.
        $DB->delete_records_select('message', $select, $params);

        // Now, get the rows from the 'message_read' table.
        $messages = $DB->get_recordset_select('message_read', $select, $params, 'id ASC');
        foreach ($messages as $message) {
            if ($message->notification) {
                $this->migrate_notification($message, true);
            } else {
                $this->migrate_message($conversationid, $message);
            }
        }
        $messages->close();

        // Ok, all done, delete the records from the 'message_read' table.
        $DB->delete_records_select('message_read', $select, $params);
    }

    /**
     * Helper function to deal with migrating an individual notification.
     *
     * @param \stdClass $notification
     * @param bool $isread Was the notification read?
     * @throws \dml_exception
     */
    private function migrate_notification($notification, $isread) {
        global $DB;

        $tabledata = new \stdClass();
        $tabledata->useridfrom = $notification->useridfrom;
        $tabledata->useridto = $notification->useridto;
        $tabledata->subject = $notification->subject;
        $tabledata->fullmessage = $notification->fullmessage;
        $tabledata->fullmessageformat = $notification->fullmessageformat;
        $tabledata->fullmessagehtml = $notification->fullmessagehtml;
        $tabledata->smallmessage = $notification->smallmessage;
        $tabledata->component = $notification->component;
        $tabledata->eventtype = $notification->eventtype;
        $tabledata->contexturl = $notification->contexturl;
        $tabledata->contexturlname = $notification->contexturlname;
        $tabledata->timeread = $notification->timeread ?? null;
        $tabledata->timecreated = $notification->timecreated;

        $newid = $DB->insert_record('notifications', $tabledata);

        // Check if there is a record to move to the new 'message_popup_notifications' table.
        if ($mp = $DB->get_record('message_popup', ['messageid' => $notification->id, 'isread' => (int) $isread])) {
            $mpn = new \stdClass();
            $mpn->notificationid = $newid;
            $DB->insert_record('message_popup_notifications', $mpn);

            $DB->delete_records('message_popup', ['id' => $mp->id]);
        }
    }

    /**
     * Helper function to deal with migrating an individual message.
     *
     * @param int $conversationid The conversation between the two users.
     * @param \stdClass $message The message from either the 'message' or 'message_read' table
     * @throws \dml_exception
     */
    private function migrate_message($conversationid, $message) {
        global $DB;

        // Create the object we will be inserting into the database.
        $tabledata = new \stdClass();
        $tabledata->useridfrom = $message->useridfrom;
        $tabledata->conversationid = $conversationid;
        $tabledata->subject = $message->subject;
        $tabledata->fullmessage = $message->fullmessage;
        $tabledata->fullmessageformat = $message->fullmessageformat;
        $tabledata->fullmessagehtml = $message->fullmessagehtml;
        $tabledata->smallmessage = $message->smallmessage;
        $tabledata->timecreated = $message->timecreated;

        $messageid = $DB->insert_record('messages', $tabledata);

        // Check if we need to mark this message as deleted for the user from.
        if ($message->timeuserfromdeleted) {
            $mua = new \stdClass();
            $mua->userid = $message->useridfrom;
            $mua->messageid = $messageid;
            $mua->action = \core_message\api::MESSAGE_ACTION_DELETED;
            $mua->timecreated = $message->timeuserfromdeleted;

            $DB->insert_record('message_user_actions', $mua);
        }

        // Check if we need to mark this message as deleted for the user to.
        if ($message->timeusertodeleted) {
            $mua = new \stdClass();
            $mua->userid = $message->useridto;
            $mua->messageid = $messageid;
            $mua->action = \core_message\api::MESSAGE_ACTION_DELETED;
            $mua->timecreated = $message->timeusertodeleted;

            $DB->insert_record('message_user_actions', $mua);
        }

        // Check if we need to mark this message as read for the user to (it is always read by the user from).
        // Note - we do an isset() check here because this column only exists in the 'message_read' table.
        if (isset($message->timeread)) {
            $mua = new \stdClass();
            $mua->userid = $message->useridto;
            $mua->messageid = $messageid;
            $mua->action = \core_message\api::MESSAGE_ACTION_READ;
            $mua->timecreated = $message->timeread;

            $DB->insert_record('message_user_actions', $mua);
        }
    }

    /**
     * Queues the task.
     *
     * @param int $userid
     */
    public static function queue_task($userid) {
        // Let's set up the adhoc task.
        $task = new \core_message\task\migrate_message_data();
        $task->set_custom_data(
            [
                'userid' => $userid
            ]
        );

        // Queue it.
        \core\task\manager::queue_adhoc_task($task, true);
    }
}
