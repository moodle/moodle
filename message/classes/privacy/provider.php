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
 * Privacy Subsystem implementation for core_message.
 *
 * @package    core_message
 * @category   privacy
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_message\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem implementation for core_message.
 *
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\subsystem\provider,
    \core_privacy\local\request\user_preference_provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items) : collection {
        $items->add_database_table(
            'messages',
            [
                'useridfrom' => 'privacy:metadata:messages:useridfrom',
                'conversationid' => 'privacy:metadata:messages:conversationid',
                'subject' => 'privacy:metadata:messages:subject',
                'fullmessage' => 'privacy:metadata:messages:fullmessage',
                'fullmessageformat' => 'privacy:metadata:messages:fullmessageformat',
                'fullmessagehtml' => 'privacy:metadata:messages:fullmessagehtml',
                'smallmessage' => 'privacy:metadata:messages:smallmessage',
                'timecreated' => 'privacy:metadata:messages:timecreated'
            ],
            'privacy:metadata:messages'
        );

        $items->add_database_table(
            'message_user_actions',
            [
                'userid' => 'privacy:metadata:message_user_actions:userid',
                'messageid' => 'privacy:metadata:message_user_actions:messageid',
                'action' => 'privacy:metadata:message_user_actions:action',
                'timecreated' => 'privacy:metadata:message_user_actions:timecreated'
            ],
            'privacy:metadata:message_user_actions'
        );

        $items->add_database_table(
            'message_conversation_members',
            [
                'conversationid' => 'privacy:metadata:message_conversation_members:conversationid',
                'userid' => 'privacy:metadata:message_conversation_members:userid',
                'timecreated' => 'privacy:metadata:message_conversation_members:timecreated',
            ],
            'privacy:metadata:message_conversation_members'
        );

        $items->add_database_table(
            'message_contacts',
            [
                'userid' => 'privacy:metadata:message_contacts:userid',
                'contactid' => 'privacy:metadata:message_contacts:contactid',
                'blocked' => 'privacy:metadata:message_contacts:blocked',
            ],
            'privacy:metadata:message_contacts'
        );

        $items->add_database_table(
            'notifications',
            [
                'useridfrom' => 'privacy:metadata:notifications:useridfrom',
                'useridto' => 'privacy:metadata:notifications:useridto',
                'subject' => 'privacy:metadata:notifications:subject',
                'fullmessage' => 'privacy:metadata:notifications:fullmessage',
                'fullmessageformat' => 'privacy:metadata:notifications:fullmessageformat',
                'fullmessagehtml' => 'privacy:metadata:notifications:fullmessagehtml',
                'smallmessage' => 'privacy:metadata:notifications:smallmessage',
                'component' => 'privacy:metadata:notifications:component',
                'eventtype' => 'privacy:metadata:notifications:eventtype',
                'contexturl' => 'privacy:metadata:notifications:contexturl',
                'contexturlname' => 'privacy:metadata:notifications:contexturlname',
                'timeread' => 'privacy:metadata:notifications:timeread',
                'timecreated' => 'privacy:metadata:notifications:timecreated',
            ],
            'privacy:metadata:notifications'
        );

        // Note - we are not adding the 'message' and 'message_read' tables
        // as they are legacy tables. This information is moved to these
        // new tables in a separate ad-hoc task. See MDL-61255.

        // Now add that we also have user preferences.
        $items->add_user_preference('core_message_messageprovider_settings',
            'privacy:metadata:preference:core_message_settings');

        return $items;
    }

    /**
     * Store all user preferences for core message.
     *
     * @param  int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $preferences = get_user_preferences(null, null, $userid);
        foreach ($preferences as $name => $value) {
            if ((substr($name, 0, 16) == 'message_provider') || ($name == 'message_blocknoncontacts')) {
                writer::export_user_preference(
                    'core_message',
                    $name,
                    $value,
                    get_string('privacy:request:preference:set', 'core_message', (object) [
                        'name' => $name,
                        'value' => $value,
                    ])
                );
            }
        }
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        global $DB;

        $contextlist = new contextlist();

        // Messages are in the user context.
        // For the sake of performance, there is no need to call add_from_sql for each of the below cases.
        // It is enough to add the user's context as soon as we come to the conclusion that the user has some data.
        // Also, the order of checking is sorted by the probability of occurrence (just by guess).
        // There is no need to check the message_user_actions table, as there needs to be a message in order to be a message action.
        // So, checking messages table would suffice.

        $hasdata = false;
        $hasdata = $hasdata || $DB->record_exists_select('notifications', 'useridfrom = ? OR useridto = ?', [$userid, $userid]);
        $hasdata = $hasdata || $DB->record_exists('message_conversation_members', ['userid' => $userid]);
        $hasdata = $hasdata || $DB->record_exists('messages', ['useridfrom' => $userid]);
        $hasdata = $hasdata || $DB->record_exists_select('message_contacts', 'userid = ? OR contactid = ?', [$userid, $userid]);

        if ($hasdata) {
            $contextlist->add_user_context($userid);
        }

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if (!$context instanceof \context_user) {
            return;
        }

        $userid = $context->instanceid;

        // Messages are in the user context.
        // For the sake of performance, there is no need to call add_from_sql for each of the below cases.
        // It is enough to add the user's context as soon as we come to the conclusion that the user has some data.
        // Also, the order of checking is sorted by the probability of occurrence (just by guess).
        // There is no need to check the message_user_actions table, as there needs to be a message in order to be a message action.
        // So, checking messages table would suffice.

        $hasdata = false;
        $hasdata = $hasdata || $DB->record_exists_select('notifications', 'useridfrom = ? OR useridto = ?', [$userid, $userid]);
        $hasdata = $hasdata || $DB->record_exists('message_conversation_members', ['userid' => $userid]);
        $hasdata = $hasdata || $DB->record_exists('messages', ['useridfrom' => $userid]);
        $hasdata = $hasdata || $DB->record_exists_select('message_contacts', 'userid = ? OR contactid = ?', [$userid, $userid]);

        if ($hasdata) {
            $userlist->add_user($userid);
        }
    }

    /**
     * Export personal data for the given approved_contextlist. User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        // Remove non-user and invalid contexts. If it ends up empty then early return.
        $contexts = array_filter($contextlist->get_contexts(), function($context) use($userid) {
            return $context->contextlevel == CONTEXT_USER && $context->instanceid == $userid;
        });

        if (empty($contexts)) {
            return;
        }

        // Export the contacts.
        self::export_user_data_contacts($userid);

        // Export the notifications.
        self::export_user_data_notifications($userid);

        // Export the messages, with any related actions.
        self::export_user_data_messages($userid);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        if ($context instanceof \context_user) {
            static::delete_user_data($context->instanceid);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        // Remove non-user and invalid contexts. If it ends up empty then early return.
        $contexts = array_filter($contextlist->get_contexts(), function($context) use($userid) {
            return $context->contextlevel == CONTEXT_USER && $context->instanceid == $userid;
        });

        if (empty($contexts)) {
            return;
        }

        static::delete_user_data($userid);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_user) {
            return;
        }

        // Remove invalid users. If it ends up empty then early return.
        $userids = array_filter($userlist->get_userids(), function($userid) use($context) {
            return $context->instanceid == $userid;
        });

        if (empty($userids)) {
            return;
        }

        static::delete_user_data($context->instanceid);
    }

    /**
     * Delete all user data for the specified user.
     *
     * @param int $userid The user id
     */
    protected static function delete_user_data(int $userid) {
        global $DB;

        $DB->delete_records('messages', ['useridfrom' => $userid]);
        $DB->delete_records('message_user_actions', ['userid' => $userid]);
        $DB->delete_records('message_conversation_members', ['userid' => $userid]);
        $DB->delete_records_select('message_contacts', 'userid = ? OR contactid = ?', [$userid, $userid]);
        $DB->delete_records_select('notifications', 'useridfrom = ? OR useridto = ?', [$userid, $userid]);
    }

    /**
     * Export the messaging contact data.
     *
     * @param int $userid
     */
    protected static function export_user_data_contacts(int $userid) {
        global $DB;

        $context = \context_user::instance($userid);

        // Get the user's contacts.
        if ($contacts = $DB->get_records('message_contacts', ['userid' => $userid], 'id ASC')) {
            $contactdata = [];
            foreach ($contacts as $contact) {
                $contactdata[] = (object) [
                    'contact' => transform::user($contact->contactid),
                    'blocked' => transform::yesno($contact->blocked)
                ];
            }
            writer::with_context($context)->export_data([get_string('contacts', 'core_message')], (object) $contactdata);
        }
    }

    /**
     * Export the messaging data.
     *
     * @param int $userid
     */
    protected static function export_user_data_messages(int $userid) {
        global $DB;

        $context = \context_user::instance($userid);

        $sql = "SELECT DISTINCT mcm.conversationid as id
                  FROM {message_conversation_members} mcm
                 WHERE mcm.userid = :userid";
        if ($conversations = $DB->get_records_sql($sql, ['userid' => $userid])) {
            // Ok, let's get the other users in the conversations.
            $conversationids = array_keys($conversations);
            list($conversationidsql, $conversationparams) = $DB->get_in_or_equal($conversationids, SQL_PARAMS_NAMED);
            $userfields = \user_picture::fields('u');
            $userssql = "SELECT mcm.conversationid, $userfields
                           FROM {user} u
                     INNER JOIN {message_conversation_members} mcm
                             ON u.id = mcm.userid
                          WHERE mcm.conversationid $conversationidsql
                            AND mcm.userid != :userid
                            AND u.deleted = 0";
            $otherusers = $DB->get_records_sql($userssql, $conversationparams + ['userid' => $userid]);
            foreach ($conversations as $conversation) {
                $otheruserfullname = get_string('unknownuser', 'core_message');

                // It's possible the other user has requested to be deleted, so might not exist
                // as a conversation member, or they have just been deleted.
                if (isset($otherusers[$conversation->id])) {
                    $otheruserfullname = fullname($otherusers[$conversation->id]);
                }

                // Get all the messages for this conversation from start to finish.
                $sql = "SELECT m.*, muadelete.timecreated as timedeleted, muaread.timecreated as timeread
                          FROM {messages} m
                     LEFT JOIN {message_user_actions} muadelete
                            ON m.id = muadelete.messageid AND muadelete.action = :deleteaction
                     LEFT JOIN {message_user_actions} muaread
                            ON m.id = muaread.messageid AND muaread.action = :readaction
                         WHERE conversationid = :conversationid
                      ORDER BY m.timecreated ASC";
                $messages = $DB->get_recordset_sql($sql, ['deleteaction' => \core_message\api::MESSAGE_ACTION_DELETED,
                    'readaction' => \core_message\api::MESSAGE_ACTION_READ, 'conversationid' => $conversation->id]);
                $messagedata = [];
                foreach ($messages as $message) {
                    $timeread = !is_null($message->timeread) ? transform::datetime($message->timeread) : '-';
                    $issender = $userid == $message->useridfrom;

                    $data = [
                        'sender' => transform::yesno($issender),
                        'message' => message_format_message_text($message),
                        'timecreated' => transform::datetime($message->timecreated),
                        'timeread' => $timeread
                    ];

                    if (!is_null($message->timedeleted)) {
                        $data['timedeleted'] = transform::datetime($message->timedeleted);
                    }

                    $messagedata[] = (object) $data;
                }
                $messages->close();

                writer::with_context($context)->export_data([get_string('messages', 'core_message'), $otheruserfullname],
                    (object) $messagedata);
            }
        }
    }

    /**
     * Export the notification data.
     *
     * @param int $userid
     */
    protected static function export_user_data_notifications(int $userid) {
        global $DB;

        $context = \context_user::instance($userid);

        $notificationdata = [];
        $select = "useridfrom = ? OR useridto = ?";
        $notifications = $DB->get_recordset_select('notifications', $select, [$userid, $userid], 'timecreated ASC');
        foreach ($notifications as $notification) {
            $timeread = !is_null($notification->timeread) ? transform::datetime($notification->timeread) : '-';

            $data = (object) [
                'subject' => $notification->subject,
                'fullmessage' => $notification->fullmessage,
                'smallmessage' => $notification->smallmessage,
                'component' => $notification->component,
                'eventtype' => $notification->eventtype,
                'contexturl' => $notification->contexturl,
                'contexturlname' => $notification->contexturlname,
                'timeread' => $timeread,
                'timecreated' => transform::datetime($notification->timecreated)
            ];

            $notificationdata[] = $data;
        }
        $notifications->close();

        writer::with_context($context)->export_data([get_string('notifications', 'core_message')], (object) $notificationdata);
    }
}
