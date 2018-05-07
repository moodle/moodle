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
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_message\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
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
    \core_privacy\local\request\user_preference_provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items) : collection {
        $items->add_database_table(
            'message',
            [
                'useridfrom' => 'privacy:metadata:messages:useridfrom',
                'useridto' => 'privacy:metadata:messages:useridto',
                'subject' => 'privacy:metadata:messages:subject',
                'fullmessage' => 'privacy:metadata:messages:fullmessage',
                'fullmessageformat' => 'privacy:metadata:messages:fullmessageformat',
                'fullmessagehtml' => 'privacy:metadata:messages:fullmessagehtml',
                'smallmessage' => 'privacy:metadata:messages:smallmessage',
                'notification' => 'privacy:metadata:messages:notification',
                'contexturl' => 'privacy:metadata:messages:contexturl',
                'contexturlname' => 'privacy:metadata:messages:contexturlname',
                'component' => 'privacy:metadata:messages:component',
                'eventtype' => 'privacy:metadata:messages:eventtype',
                'timecreated' => 'privacy:metadata:messages:timecreated',
                'timeuserfromdeleted' => 'privacy:metadata:messages:timeuserfromdeleted',
                'timeusertodeleted' => 'privacy:metadata:messages:timeusertodeleted'
            ],
            'privacy:metadata:messages'
        );

        $items->add_database_table(
            'message_read',
            [
                'useridfrom' => 'privacy:metadata:messages:useridfrom',
                'useridto' => 'privacy:metadata:messages:useridto',
                'subject' => 'privacy:metadata:messages:subject',
                'fullmessage' => 'privacy:metadata:messages:fullmessage',
                'fullmessageformat' => 'privacy:metadata:messages:fullmessageformat',
                'fullmessagehtml' => 'privacy:metadata:messages:fullmessagehtml',
                'smallmessage' => 'privacy:metadata:messages:smallmessage',
                'notification' => 'privacy:metadata:messages:notification',
                'contexturl' => 'privacy:metadata:messages:contexturl',
                'contexturlname' => 'privacy:metadata:messages:contexturlname',
                'component' => 'privacy:metadata:messages:component',
                'eventtype' => 'privacy:metadata:messages:eventtype',
                'timecreated' => 'privacy:metadata:messages:timecreated',
                'timeread' => 'privacy:metadata:messages:timeread',
                'timeuserfromdeleted' => 'privacy:metadata:messages:timeuserfromdeleted',
                'timeusertodeleted' => 'privacy:metadata:messages:timeusertodeleted'
            ],
            'privacy:metadata:messages'
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
        // Messages are in the system context.
        $contextlist = new contextlist();
        $contextlist->add_system_context();

        return $contextlist;
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

        // Remove non-system contexts. If it ends up empty then early return.
        $contexts = array_filter($contextlist->get_contexts(), function($context) {
            return $context->contextlevel == CONTEXT_SYSTEM;
        });

        if (empty($contexts)) {
            return;
        }

        $userid = $contextlist->get_user()->id;

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
        global $DB;

        if (!$context instanceof \context_system) {
            return;
        }

        $DB->delete_records('message');
        $DB->delete_records('message_read');
        $DB->delete_records('message_contacts');
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        // Remove non-system contexts. If it ends up empty then early return.
        $contexts = array_filter($contextlist->get_contexts(), function($context) {
            return $context->contextlevel == CONTEXT_SYSTEM;
        });

        if (empty($contexts)) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        $DB->delete_records_select('message', 'useridfrom = ? AND notification = 0', [$userid]);
        $DB->delete_records_select('message_read', 'useridfrom = ? AND notification = 0', [$userid]);
        $DB->delete_records_select('message', '(useridfrom = ? OR useridto = ?) AND notification = 1', [$userid, $userid]);
        $DB->delete_records_select('message_read', '(useridfrom = ? OR useridto = ?) AND notification = 1', [$userid, $userid]);
        $DB->delete_records_select('message_contacts', 'userid = ? OR contactid = ?', [$userid, $userid]);
    }

    /**
     * Export the messaging contact data.
     *
     * @param int $userid
     */
    protected static function export_user_data_contacts(int $userid) {
        global $DB;

        $context = \context_system::instance();

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

        $context = \context_system::instance();

        $users = self::get_userids_in_conversation_with($userid);
        if (!empty($users)) {
            // Ok, let's get the other users details. Note - the user may no longer exist.
            $userids = array_keys($users);
            list($useridsql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
            $userfields = \user_picture::fields('u');
            $userssql = "SELECT $userfields
                           FROM {user} u
                          WHERE u.id $useridsql
                            AND u.deleted = 0";
            $otherusers = $DB->get_records_sql($userssql, $userparams + ['userid' => $userid]);
            foreach ($users as $user) {
                $otheruserfullname = get_string('unknownuser', 'core_message');

                // It's possible the other user may have been deleted.
                if (isset($otherusers[$user->id])) {
                    $otheruserfullname = fullname($otherusers[$user->id]);
                }

                $sql = "SELECT id, useridfrom, useridto, subject, fullmessage, fullmessageformat,
                               fullmessagehtml, smallmessage, notification, contexturl,
                               contexturlname, timecreated, timeuserfromdeleted, timeusertodeleted,
                               component, eventtype, 0 as timeread
                          FROM {message} m
                         WHERE notification = 0
                           AND (useridfrom = ? AND useridto = ?) OR (useridfrom = ? AND useridto = ?)
                     UNION ALL
                        SELECT id, useridfrom, useridto, subject, fullmessage, fullmessageformat,
                               fullmessagehtml, smallmessage, notification, contexturl,
                               contexturlname, timecreated, timeuserfromdeleted, timeusertodeleted,
                               component, eventtype, timeread
                          FROM {message_read} mr
                         WHERE notification = 0
                           AND (useridfrom = ? AND useridto = ?) OR (useridfrom = ? AND useridto = ?)
                      ORDER BY timecreated ASC";
                $params = [$userid, $user->id, $user->id, $userid, $userid, $user->id, $user->id, $userid];
                $messages = $DB->get_recordset_sql($sql, $params);
                $messagedata = [];
                foreach ($messages as $message) {
                    $timeread = !empty($message->timeread) ? transform::datetime($message->timeread) : '-';
                    $issender = $userid == $message->useridfrom;

                    $timedeletedfield = 'timeusertodeleted';
                    if ($issender) {
                        $timedeletedfield = 'timeuserfromdeleted';
                    }

                    $data = [
                        'sender' => transform::yesno($issender),
                        'message' => message_format_message_text($message),
                        'timecreated' => transform::datetime($message->timecreated),
                        'timeread' => $timeread
                    ];

                    if (!empty($message->$timedeletedfield)) {
                        $data['timedeleted'] = transform::datetime($message->$timedeletedfield);
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

        $context = \context_system::instance();

        $notificationdata = [];
        $sql = "SELECT id, useridfrom, useridto, subject, fullmessage, fullmessageformat,
                       fullmessagehtml, smallmessage, notification, contexturl,
                       contexturlname, timecreated, timeuserfromdeleted, timeusertodeleted,
                       component, eventtype, 0 as timeread
                  FROM {message} m
                 WHERE notification = 1 AND useridfrom = ? OR useridto = ?
             UNION ALL
                SELECT id, useridfrom, useridto, subject, fullmessage, fullmessageformat,
                       fullmessagehtml, smallmessage, notification, contexturl,
                       contexturlname, timecreated, timeuserfromdeleted, timeusertodeleted,
                       component, eventtype, timeread
                  FROM {message_read} mr
                 WHERE notification = 1 AND useridfrom = ? OR useridto = ?
              ORDER BY timecreated DESC";
        $notifications = $DB->get_recordset_sql($sql, [$userid, $userid, $userid, $userid]);
        foreach ($notifications as $notification) {
            $timeread = !empty($notification->timeread) ? transform::datetime($notification->timeread) : '-';

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

    /**
     * Returns a list of users the given user is in a conversation with.
     *
     * @param int $userid
     * @return array
     * @throws \dml_exception
     */
    private static function get_userids_in_conversation_with(int $userid) {
        global $DB;

        $sql = "SELECT DISTINCT(useridfrom) as id
                      FROM {message} m
                     WHERE useridto = ?
                       AND notification = 0
                     UNION
                    SELECT DISTINCT(useridfrom) as id
                      FROM {message_read} m
                     WHERE useridto = ?
                       AND notification = 0";
        $users = $DB->get_records_sql($sql, [$userid, $userid]);

        $sql = "SELECT DISTINCT(useridto) as id
                      FROM {message} m
                     WHERE useridfrom = ?
                       AND notification = 0
                     UNION
                    SELECT DISTINCT(useridto) as id
                      FROM {message_read} m
                     WHERE useridfrom = ?
                       AND notification = 0";
        return $users + $DB->get_records_sql($sql, [$userid, $userid]);
    }
}
