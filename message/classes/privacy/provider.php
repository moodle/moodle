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
    // The messaging subsystem contains data.
    \core_privacy\local\metadata\provider,

    // The messaging subsystem provides all the messages at user context - i.e. individual ones.
    \core_privacy\local\request\subsystem\provider,

    // This plugin has some sitewide user preferences to export.
    \core_privacy\local\request\user_preference_provider,

    // This plugin is capable of determining which users have data within it.
    \core_privacy\local\request\core_userlist_provider,

    // The messaging subsystem provides a data service to other components.
    \core_privacy\local\request\subsystem\plugin_provider {

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
                'timecreated' => 'privacy:metadata:messages:timecreated',
                'customdata' => 'privacy:metadata:messages:customdata',
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
            'message_conversation_actions',
            [
                'conversationid' => 'privacy:metadata:message_conversation_actions:conversationid',
                'userid' => 'privacy:metadata:message_conversation_actions:userid',
                'timecreated' => 'privacy:metadata:message_conversation_actions:timecreated',
            ],
            'privacy:metadata:message_conversation_actions'
        );

        $items->add_database_table(
            'message_contacts',
            [
                'userid' => 'privacy:metadata:message_contacts:userid',
                'contactid' => 'privacy:metadata:message_contacts:contactid',
                'timecreated' => 'privacy:metadata:message_contacts:timecreated',
            ],
            'privacy:metadata:message_contacts'
        );

        $items->add_database_table(
            'message_contact_requests',
            [
                'userid' => 'privacy:metadata:message_contact_requests:userid',
                'requesteduserid' => 'privacy:metadata:message_contact_requests:requesteduserid',
                'timecreated' => 'privacy:metadata:message_contact_requests:timecreated',
            ],
            'privacy:metadata:message_contact_requests'
        );

        $items->add_database_table(
            'message_users_blocked',
            [
                'userid' => 'privacy:metadata:message_users_blocked:userid',
                'blockeduserid' => 'privacy:metadata:message_users_blocked:blockeduserid',
                'timecreated' => 'privacy:metadata:message_users_blocked:timecreated',
            ],
            'privacy:metadata:message_users_blocked'
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
                'customdata' => 'privacy:metadata:notifications:customdata',
            ],
            'privacy:metadata:notifications'
        );

        // Note - we are not adding the 'message' and 'message_read' tables
        // as they are legacy tables. This information is moved to these
        // new tables in a separate ad-hoc task. See MDL-61255.

        // Now add that we also have user preferences.
        $items->add_user_preference('core_message_messageprovider_settings',
            'privacy:metadata:preference:core_message_settings');

        // Add favourite conversations.
        $items->link_subsystem('core_favourites', 'privacy:metadata:core_favourites');

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
            if (
                (substr($name, 0, 16) == 'message_provider') ||
                ($name == 'message_blocknoncontacts') ||
                ($name == 'message_entertosend')
            ) {
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
        // There is no need to check the message_conversation_actions table, as there needs to be a conversation in order to
        // be a conversation action.
        // So, checking messages table would suffice.

        $hasdata = false;
        $hasdata = $hasdata || $DB->record_exists_select('notifications', 'useridfrom = ? OR useridto = ?', [$userid, $userid]);
        $sql = "SELECT mc.id
              FROM {message_conversations} mc
              JOIN {message_conversation_members} mcm
                ON (mcm.conversationid = mc.id AND mcm.userid = :userid)
             WHERE mc.contextid IS NULL";
        $hasdata = $hasdata || $DB->record_exists_sql($sql, ['userid' => $userid]);
        $sql = "SELECT mc.id
              FROM {message_conversations} mc
              JOIN {messages} m
                ON (m.conversationid = mc.id AND m.useridfrom = :useridfrom)
             WHERE mc.contextid IS NULL";
        $hasdata = $hasdata || $DB->record_exists_sql($sql, ['useridfrom' => $userid]);
        $hasdata = $hasdata || $DB->record_exists_select('message_contacts', 'userid = ? OR contactid = ?', [$userid, $userid]);
        $hasdata = $hasdata || $DB->record_exists_select('message_users_blocked', 'userid = ? OR blockeduserid = ?',
                [$userid, $userid]);
        $hasdata = $hasdata || $DB->record_exists_select('message_contact_requests', 'userid = ? OR requesteduserid = ?',
                [$userid, $userid]);

        if ($hasdata) {
            $contextlist->add_user_context($userid);
        }

        // Add favourite conversations.
        \core_favourites\privacy\provider::add_contexts_for_userid($contextlist, $userid, 'core_message', 'message_conversations');

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
        // There is no need to check the message_conversation_actions table, as there needs to be a conversation in order to
        // be a conversation action.
        // So, checking messages table would suffice.

        $hasdata = false;
        $hasdata = $hasdata || $DB->record_exists_select('notifications', 'useridfrom = ? OR useridto = ?', [$userid, $userid]);
        $sql = "SELECT mc.id
              FROM {message_conversations} mc
              JOIN {message_conversation_members} mcm
                ON (mcm.conversationid = mc.id AND mcm.userid = :userid)
             WHERE mc.contextid IS NULL";
        $hasdata = $hasdata || $DB->record_exists_sql($sql, ['userid' => $userid]);
        $sql = "SELECT mc.id
              FROM {message_conversations} mc
              JOIN {messages} m
                ON (m.conversationid = mc.id AND m.useridfrom = :useridfrom)
             WHERE mc.contextid IS NULL";
        $hasdata = $hasdata || $DB->record_exists_sql($sql, ['useridfrom' => $userid]);
        $hasdata = $hasdata || $DB->record_exists_select('message_contacts', 'userid = ? OR contactid = ?', [$userid, $userid]);
        $hasdata = $hasdata || $DB->record_exists_select('message_users_blocked', 'userid = ? OR blockeduserid = ?',
                        [$userid, $userid]);
        $hasdata = $hasdata || $DB->record_exists_select('message_contact_requests', 'userid = ? OR requesteduserid = ?',
                        [$userid, $userid]);

        if ($hasdata) {
            $userlist->add_user($userid);
        }

        // Add favourite conversations.
        $component = $userlist->get_component();
        if ($component != 'core_message') {
            $userlist->set_component('core_message');
        }
        \core_favourites\privacy\provider::add_userids_for_context($userlist, 'message_conversations');
        if ($component != 'core_message') {
            $userlist->set_component($component);
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

        // Export the contact requests.
        self::export_user_data_contact_requests($userid);

        // Export the blocked users.
        self::export_user_data_blocked_users($userid);

        // Export the notifications.
        self::export_user_data_notifications($userid);

        // Conversations with empty contextid should be exported here because they are not related to any component/itemid.
        $context = reset($contexts);
        self::export_conversations($userid, '', '', $context);
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
     * Provide a list of contexts which have conversations for the user, in the respective area (component/itemtype combination).
     *
     * This method is to be called by consumers of the messaging subsystem (plugins), in their get_contexts_for_userid() method,
     * to add the contexts for items which may have any conversation, but would normally not be reported as having user data by the
     * plugin responsible for them.
     *
     * @param contextlist $contextlist
     * @param int $userid The id of the user in scope.
     * @param string $component the frankenstyle component name.
     * @param string $itemtype the type of the conversation items.
     * @param int $itemid Optional itemid associated with component.
     */
    public static function add_contexts_for_conversations(contextlist $contextlist, int $userid, string $component,
                                                          string $itemtype, int $itemid = 0) {
        // Search for conversations for this user in this area.
        $sql = "SELECT mc.contextid
                  FROM {message_conversations} mc
                  JOIN {message_conversation_members} mcm
                    ON (mcm.conversationid = mc.id AND mcm.userid = :userid)
                  JOIN {context} ctx
                    ON mc.contextid = ctx.id
                 WHERE mc.component = :component AND mc.itemtype = :itemtype";
        $params = [
            'userid' => $userid,
            'component' => $component,
            'itemtype' => $itemtype,
        ];

        if (!empty($itemid)) {
            $sql .= " AND itemid = :itemid";
            $params['itemid'] = $itemid;
        }

        $contextlist->add_from_sql($sql, $params);

        // Add favourite conversations. We don't need to filter by itemid because for now they are in the system context.
        \core_favourites\privacy\provider::add_contexts_for_userid($contextlist, $userid, 'core_message', 'message_conversations');

    }

    /**
     * Add the list of users who have a conversation in the specified area (component + itemtype + itemid).
     *
     * @param userlist $userlist The userlist to add the users to.
     * @param string $component The component to check.
     * @param string $itemtype The type of the conversation items.
     * @param int $itemid Optional itemid associated with component.
     */
    public static function add_conversations_in_context(userlist $userlist, string $component, string $itemtype, int $itemid = 0) {
        $sql = "SELECT mcm.userid
                  FROM {message_conversation_members} mcm
            INNER JOIN {message_conversations} mc
                    ON mc.id = mcm.conversationid
                 WHERE mc.contextid = :contextid AND mc.component = :component AND mc.itemtype = :itemtype";
        $params = [
            'contextid' => $userlist->get_context()->id,
            'component' => $component,
            'itemtype' => $itemtype
        ];

        if (!empty($itemid)) {
            $sql .= " AND itemid = :itemid";
            $params['itemid'] = $itemid;
        }

        $userlist->add_from_sql('userid', $sql, $params);

        // Add favourite conversations.
        $component = $userlist->get_component();
        if ($component != 'core_message') {
            $userlist->set_component('core_message');
        }
        \core_favourites\privacy\provider::add_userids_for_context($userlist, 'message_conversations');
        if ($component != 'core_message') {
            $userlist->set_component($component);
        }
    }

    /**
     * Store all conversations which match the specified component, itemtype, and itemid.
     *
     * Conversations without context (for now, the private ones) are stored in '<$context> | Messages | <Other user id>'.
     * Conversations with context are stored in '<$context> | Messages | <Conversation item type> | <Conversation name>'.
     *
     * @param   int         $userid The user whose information is to be exported.
     * @param   string      $component The component to fetch data from.
     * @param   string      $itemtype The itemtype that the data was exported in within the component.
     * @param   \context    $context The context to export for.
     * @param   array       $subcontext The sub-context in which to export this data.
     * @param   int         $itemid Optional itemid associated with component.
     */
    public static function export_conversations(int $userid, string $component, string $itemtype, \context $context,
                                                array $subcontext = [], int $itemid = 0) {
        global $DB;

        // Search for conversations for this user in this area.
        $sql = "SELECT DISTINCT mc.*
                  FROM {message_conversations} mc
                  JOIN {message_conversation_members} mcm
                    ON (mcm.conversationid = mc.id AND mcm.userid = :userid)";
        $params = [
            'userid' => $userid
        ];

        // Get the conversations for the defined component and itemtype.
        if (!empty($component) && !empty($itemtype)) {
            $sql .= " WHERE mc.component = :component AND mc.itemtype = :itemtype";
            $params['component'] = $component;
            $params['itemtype'] = $itemtype;
            if (!empty($itemid)) {
                $sql .= " AND mc.itemid = :itemid";
                $params['itemid'] = $itemid;
            }
        } else {
            // Get all the conversations without any component and itemtype, so with null contextid.
            $sql .= " WHERE mc.contextid IS NULL";
        }

        if ($conversations = $DB->get_records_sql($sql, $params)) {
            // Export conversation messages.
            foreach ($conversations as $conversation) {
                self::export_user_data_conversation_messages($userid, $conversation, $context, $subcontext);
            }
        }
    }

    /**
     * Deletes all group memberships for a specified context and component.
     *
     * @param \context  $context    Details about which context to delete group memberships for.
     * @param string    $component  The component to delete. Empty string means no component.
     * @param string    $itemtype   The itemtype of the component to delele. Empty string means no itemtype.
     * @param int       $itemid     Optional itemid associated with component.
     */
    public static function delete_conversations_for_all_users(\context $context, string $component, string $itemtype,
                                                              int $itemid = 0) {
        global $DB;

        if (empty($context)) {
            return;
        }

        $select = "contextid = :contextid AND component = :component AND itemtype = :itemtype";
        $params = [
            'contextid' => $context->id,
            'component' => $component,
            'itemtype' => $itemtype
        ];

        if (!empty($itemid)) {
            $select .= " AND itemid = :itemid";
            $params['itemid'] = $itemid;
        }

        // Get and remove all the conversations and messages for the specified context and area.
        if ($conversationids = $DB->get_records_select('message_conversations', $select, $params, '', 'id')) {
            $conversationids = array_keys($conversationids);
            $messageids = $DB->get_records_list('messages', 'conversationid', $conversationids);
            $messageids = array_keys($messageids);

            // Delete these favourite conversations to all the users.
            foreach ($conversationids as $conversationid) {
                \core_favourites\privacy\provider::delete_favourites_for_all_users(
                    $context,
                    'core_message',
                    'message_conversations',
                    $conversationid);
            }

            // Delete messages and user_actions.
            $DB->delete_records_list('message_user_actions', 'messageid', $messageids);
            $DB->delete_records_list('messages', 'id', $messageids);

            // Delete members and conversations.
            $DB->delete_records_list('message_conversation_members', 'conversationid', $conversationids);
            $DB->delete_records_list('message_conversation_actions', 'conversationid', $conversationids);
            $DB->delete_records_list('message_conversations', 'id', $conversationids);
        }
    }

    /**
     * Deletes all records for a user from a list of approved contexts.
     *
     * When the component and the itemtype are empty and there is only one user context in the list, all the
     * conversations without contextid will be removed. However, if the component and itemtype are defined,
     * only the conversations in these area for the contexts in $contextlist wil be deleted.
     *
     * @param approved_contextlist  $contextlist    Contains the user ID and a list of contexts to be deleted from.
     * @param string    $component  The component to delete. Empty string means no component.
     * @param string    $itemtype   The itemtype of the component to delele. Empty string means no itemtype.
     * @param int       $itemid     Optional itemid associated with component.
     */
    public static function delete_conversations_for_user(approved_contextlist $contextlist, string $component, string $itemtype,
                                                         int $itemid = 0) {
        self::delete_user_data_conversations(
            $contextlist->get_user()->id,
            $contextlist->get_contextids(),
            $component,
            $itemtype,
            $itemid
        );
    }

    /**
     * Deletes all records for multiple users within a single context.
     *
     * @param approved_userlist $userlist  The approved context and user information to delete information for.
     * @param string    $component  The component to delete. Empty string means no component.
     * @param string    $itemtype   The itemtype of the component to delele. Empty string means no itemtype.
     * @param int       $itemid     Optional itemid associated with component.
     */
    public static function delete_conversations_for_users(approved_userlist $userlist, string $component, string $itemtype,
                                                          int $itemid = 0) {
        global $DB;

        $userids = $userlist->get_userids();
        if (empty($userids)) {
            return;
        }

        $context = $userlist->get_context();
        $select = "mc.contextid = :contextid AND mc.component = :component AND mc.itemtype = :itemtype";
        $params = [
            'contextid' => $context->id,
            'component' => $component,
            'itemtype' => $itemtype
        ];
        if (!empty($itemid)) {
            $select .= " AND itemid = :itemid";
            $params['itemid'] = $itemid;
        }

        // Get conversations in this area where the specified users are a member of.
        list($useridsql, $useridparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $sql = "SELECT DISTINCT mcm.conversationid as id
                  FROM {message_conversation_members} mcm
            INNER JOIN {message_conversations} mc
                    ON mc.id = mcm.conversationid
                 WHERE mcm.userid $useridsql AND $select";
        $params += $useridparams;
        $conversationids = array_keys($DB->get_records_sql($sql, $params));
        if (!empty($conversationids)) {
            list($conversationidsql, $conversationidparams) = $DB->get_in_or_equal($conversationids, SQL_PARAMS_NAMED);

            // Get all the messages for these conversations which has some action stored for these users.
            $sql = "SELECT DISTINCT m.id
                      FROM {messages} m
                INNER JOIN {message_conversations} mc
                        ON mc.id = m.conversationid
                INNER JOIN {message_user_actions} mua
                        ON mua.messageid = m.id
                     WHERE mua.userid $useridsql  AND mc.id $conversationidsql";
            $params = $useridparams + $conversationidparams;
            $messageids = array_keys($DB->get_records_sql($sql, $params));
            if (!empty($messageids)) {
                // Delete all the user_actions for the messages on these conversations where the user has any action.
                list($messageidsql, $messageidparams) = $DB->get_in_or_equal($messageids, SQL_PARAMS_NAMED);
                $select = "messageid $messageidsql AND userid $useridsql";
                $DB->delete_records_select('message_user_actions', $select, $messageidparams + $useridparams);
            }

            // Get all the messages for these conversations sent by these users.
            $sql = "SELECT DISTINCT m.id
                      FROM {messages} m
                     WHERE m.useridfrom $useridsql AND m.conversationid $conversationidsql";
            // Reuse the $params var because it contains the useridparams and the conversationids.
            $messageids = array_keys($DB->get_records_sql($sql, $params));
            if (!empty($messageids)) {
                // Delete all the user_actions for the messages sent by any of these users.
                $DB->delete_records_list('message_user_actions', 'messageid', $messageids);

                // Delete all the messages sent by any of these users.
                $DB->delete_records_list('messages', 'id', $messageids);
            }

            // In that case, conversations can't be removed, because they could have more members and messages.
            // So, remove only users from the context conversations where they are member of.
            $sql = "conversationid $conversationidsql AND userid $useridsql";
            // Reuse the $params var because it contains the useridparams and the conversationids.
            $DB->delete_records_select('message_conversation_members', $sql, $params);

            // Delete any conversation actions.
            $DB->delete_records_select('message_conversation_actions', $sql, $params);

            // Delete the favourite conversations.
            $userlist = new \core_privacy\local\request\approved_userlist($context, 'core_message', $userids);
            \core_favourites\privacy\provider::delete_favourites_for_userlist(
                $userlist,
                'message_conversations'
            );
        }
    }

    /**
     * Deletes all records for multiple users within multiple contexts in a component area.
     *
     * @param  int    $userid     The user identifier to delete information for.
     * @param  array  $contextids The context identifiers to delete information for. Empty array means no context (for
     *                            individual conversations).
     * @param  string $component  The component to delete. Empty string means no component (for individual conversations).
     * @param  string $itemtype   The itemtype of the component to delele. Empty string means no itemtype (for individual
     *                            conversations).
     * @param  int    $itemid     Optional itemid associated with component.
     */
    protected static function delete_user_data_conversations(int $userid, array $contextids, string $component,
                                                            string $itemtype, int $itemid = 0) {
        global $DB;

        if (empty($contextids) && empty($component) && empty($itemtype) && empty($itemid)) {
            // Individual conversations haven't context, component neither itemtype.
            $select = "mc.contextid IS NULL";
            $params = [];
        } else {
            list($contextidsql, $contextidparams) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED);
            $select = "mc.contextid $contextidsql AND mc.component = :component AND mc.itemtype = :itemtype";
            $params = [
                'component' => $component,
                'itemtype' => $itemtype
            ];
            $params += $contextidparams;
            if (!empty($itemid)) {
                $select .= " AND itemid = :itemid";
                $params['itemid'] = $itemid;
            }
        }

        // Get conversations in these contexts where the specified userid is a member of.
        $sql = "SELECT DISTINCT mcm.conversationid as id
                  FROM {message_conversation_members} mcm
            INNER JOIN {message_conversations} mc
                    ON mc.id = mcm.conversationid
                 WHERE mcm.userid = :userid AND $select";
        $params['userid'] = $userid;
        $conversationids = array_keys($DB->get_records_sql($sql, $params));
        if (!empty($conversationids)) {
            list($conversationidsql, $conversationidparams) = $DB->get_in_or_equal($conversationids, SQL_PARAMS_NAMED);

            // Get all the messages for these conversations which has some action stored for the userid.
            $sql = "SELECT DISTINCT m.id
                      FROM {messages} m
                INNER JOIN {message_conversations} mc
                        ON mc.id = m.conversationid
                INNER JOIN {message_user_actions} mua
                        ON mua.messageid = m.id
                     WHERE mua.userid = :userid AND mc.id $conversationidsql";
            $params = ['userid' => $userid] + $conversationidparams;
            $messageids = array_keys($DB->get_records_sql($sql, $params));
            if (!empty($messageids)) {
                // Delete all the user_actions for the messages on these conversations where the user has any action.
                list($messageidsql, $messageidparams) = $DB->get_in_or_equal($messageids, SQL_PARAMS_NAMED);
                $select = "messageid $messageidsql AND userid = :userid";
                $DB->delete_records_select('message_user_actions', $select, $messageidparams + ['userid' => $userid]);
            }

            // Get all the messages for these conversations sent by the userid.
            $sql = "SELECT DISTINCT m.id
                      FROM {messages} m
                     WHERE m.useridfrom = :userid AND m.conversationid $conversationidsql";
            // Reuse the $params var because it contains the userid and the conversationids.
            $messageids = array_keys($DB->get_records_sql($sql, $params));
            if (!empty($messageids)) {
                // Delete all the user_actions for the messages sent by the userid.
                $DB->delete_records_list('message_user_actions', 'messageid', $messageids);

                // Delete all the messages sent by the userid.
                $DB->delete_records_list('messages', 'id', $messageids);
            }

            // In that case, conversations can't be removed, because they could have more members and messages.
            // So, remove only userid from the context conversations where he/she is member of.
            $sql = "conversationid $conversationidsql AND userid = :userid";
            // Reuse the $params var because it contains the userid and the conversationids.
            $DB->delete_records_select('message_conversation_members', $sql, $params);

            // Delete any conversation actions.
            $DB->delete_records_select('message_conversation_actions', $sql, $params);

            // Delete the favourite conversations.
            if (empty($contextids) && empty($component) && empty($itemtype) && empty($itemid)) {
                // Favourites for individual conversations are stored into the user context.
                $favouritectxids = [\context_user::instance($userid)->id];
            } else {
                $favouritectxids = $contextids;
            }
            $contextlist = new \core_privacy\local\request\approved_contextlist(
                \core_user::get_user($userid),
                'core_message',
                $favouritectxids
            );
            \core_favourites\privacy\provider::delete_favourites_for_user(
                $contextlist,
                'core_message',
                'message_conversations'
            );
        }
    }

    /**
     * Delete all user data for the specified user.
     *
     * @param int $userid The user id
     */
    protected static function delete_user_data(int $userid) {
        global $DB;

        // Delete individual conversations information for this user.
        self::delete_user_data_conversations($userid, [], '', '');

        // Delete contacts, requests, users blocked and notifications.
        $DB->delete_records_select('message_contacts', 'userid = ? OR contactid = ?', [$userid, $userid]);
        $DB->delete_records_select('message_contact_requests', 'userid = ? OR requesteduserid = ?', [$userid, $userid]);
        $DB->delete_records_select('message_users_blocked', 'userid = ? OR blockeduserid = ?', [$userid, $userid]);
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
        if ($contacts = $DB->get_records_select('message_contacts', 'userid = ? OR contactid = ?', [$userid, $userid], 'id ASC')) {
            $contactdata = [];
            foreach ($contacts as $contact) {
                $contactdata[] = (object) [
                    'contact' => transform::user($contact->contactid)
                ];
            }
            writer::with_context($context)->export_data([get_string('contacts', 'core_message')], (object) $contactdata);
        }
    }

    /**
     * Export the messaging contact requests data.
     *
     * @param int $userid
     */
    protected static function export_user_data_contact_requests(int $userid) {
        global $DB;

        $context = \context_user::instance($userid);

        if ($contactrequests = $DB->get_records_select('message_contact_requests', 'userid = ? OR requesteduserid = ?',
                [$userid, $userid], 'id ASC')) {
            $contactrequestsdata = [];
            foreach ($contactrequests as $contactrequest) {
                if ($userid == $contactrequest->requesteduserid) {
                    $maderequest = false;
                    $contactid = $contactrequest->userid;
                } else {
                    $maderequest = true;
                    $contactid = $contactrequest->requesteduserid;
                }

                $contactrequestsdata[] = (object) [
                    'contactrequest' => transform::user($contactid),
                    'maderequest' => transform::yesno($maderequest)
                ];
            }
            writer::with_context($context)->export_data([get_string('contactrequests', 'core_message')],
                (object) $contactrequestsdata);
        }
    }

    /**
     * Export the messaging blocked users data.
     *
     * @param int $userid
     */
    protected static function export_user_data_blocked_users(int $userid) {
        global $DB;

        $context = \context_user::instance($userid);

        if ($blockedusers = $DB->get_records('message_users_blocked', ['userid' => $userid], 'id ASC')) {
            $blockedusersdata = [];
            foreach ($blockedusers as $blockeduser) {
                $blockedusersdata[] = (object) [
                    'blockeduser' => transform::user($blockeduser->blockeduserid)
                ];
            }
            writer::with_context($context)->export_data([get_string('blockedusers', 'core_message')], (object) $blockedusersdata);
        }
    }

    /**
     * Export conversation messages.
     *
     * @param int $userid The user identifier.
     * @param \stdClass $conversation The conversation to export the messages.
     * @param \context $context The context to export for.
     * @param array $subcontext The sub-context in which to export this data.
     */
    protected static function export_user_data_conversation_messages(int $userid, \stdClass $conversation, \context $context,
                                                                     array $subcontext = []) {
        global $DB;

        // Get all the messages for this conversation from start to finish.
        $sql = "SELECT m.*, muadelete.timecreated as timedeleted, muaread.timecreated as timeread
                  FROM {messages} m
             LEFT JOIN {message_user_actions} muadelete
                    ON m.id = muadelete.messageid AND muadelete.action = :deleteaction AND muadelete.userid = :deleteuserid
             LEFT JOIN {message_user_actions} muaread
                    ON m.id = muaread.messageid AND muaread.action = :readaction AND muaread.userid = :readuserid
                 WHERE conversationid = :conversationid
              ORDER BY m.timecreated ASC";
        $messages = $DB->get_recordset_sql($sql, ['deleteaction' => \core_message\api::MESSAGE_ACTION_DELETED,
            'readaction' => \core_message\api::MESSAGE_ACTION_READ, 'conversationid' => $conversation->id,
            'deleteuserid' => $userid, 'readuserid' => $userid]);
        $messagedata = [];
        foreach ($messages as $message) {
            $timeread = !is_null($message->timeread) ? transform::datetime($message->timeread) : '-';
            $issender = $userid == $message->useridfrom;

            $data = [
                'issender' => transform::yesno($issender),
                'message' => message_format_message_text($message),
                'timecreated' => transform::datetime($message->timecreated),
                'timeread' => $timeread,
                'customdata' => $message->customdata,
            ];
            if ($conversation->type == \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP && !$issender) {
                // Only export sender for group conversations when is not the current user.
                $data['sender'] = transform::user($message->useridfrom);
            }

            if (!is_null($message->timedeleted)) {
                $data['timedeleted'] = transform::datetime($message->timedeleted);
            }

            $messagedata[] = (object) $data;
        }
        $messages->close();

        if (!empty($messagedata)) {
            // Get subcontext.
            if (empty($conversation->contextid)) {
                // Conversations without context are stored in 'Messages | <Other user id>'.
                if ($conversation->type == \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF) {
                    // This is a self-conversation. The other user is the same userid.
                    $otherusertext = $userid;
                } else {
                    $members = $DB->get_records('message_conversation_members', ['conversationid' => $conversation->id]);
                    $members = array_filter($members, function ($member) use ($userid) {
                        return $member->userid != $userid;
                    });
                    if ($otheruser = reset($members)) {
                        $otherusertext = $otheruser->userid;
                    } else {
                        $otherusertext = get_string('unknownuser', 'core_message') . '_' . $conversation->id;
                    }
                }

                $subcontext = array_merge(
                    $subcontext,
                    [get_string('messages', 'core_message'), $otherusertext]
                );

                // Get the context for the favourite conversation.
                $conversationctx = \context_user::instance($userid);
            } else {
                // Conversations with context are stored in 'Messages | <Conversation item type> | <Conversation name>'.
                if (get_string_manager()->string_exists($conversation->itemtype, $conversation->component)) {
                    $itemtypestring = get_string($conversation->itemtype, $conversation->component);
                } else {
                    // If the itemtype doesn't exist in the component string file, the raw itemtype will be returned.
                    $itemtypestring = $conversation->itemtype;
                }

                $conversationname = get_string('privacy:export:conversationprefix', 'core_message') . $conversation->name;
                $subcontext = array_merge(
                    $subcontext,
                    [get_string('messages', 'core_message'), $itemtypestring, $conversationname]
                );

                // Get the context for the favourite conversation.
                $conversationctx = \context::instance_by_id($conversation->contextid);
            }

            // Export the conversation messages.
            writer::with_context($context)->export_data($subcontext, (object) $messagedata);

            // Get user's favourites information for the particular conversation.
            $conversationfavourite = \core_favourites\privacy\provider::get_favourites_info_for_user($userid, $conversationctx,
                'core_message', 'message_conversations', $conversation->id);
            if ($conversationfavourite) {
                // If the conversation has been favorited by the user, include it in the export.
                writer::with_context($context)->export_related_data($subcontext, 'starred', (object) $conversationfavourite);
            }

            // Check if the conversation was muted.
            $params = [
                'userid' => $userid,
                'conversationid' => $conversation->id,
                'action' => \core_message\api::CONVERSATION_ACTION_MUTED
            ];
            if ($mca = $DB->get_record('message_conversation_actions', $params)) {
                $mcatostore = [
                    'muted' => transform::yesno(true),
                    'timecreated' => transform::datetime($mca->timecreated),
                ];
                writer::with_context($context)->export_related_data($subcontext, 'muted', (object) $mcatostore);
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
                'timecreated' => transform::datetime($notification->timecreated),
                'customdata' => $notification->customdata,
            ];

            $notificationdata[] = $data;
        }
        $notifications->close();

        writer::with_context($context)->export_data([get_string('notifications', 'core_message')], (object) $notificationdata);
    }
}
