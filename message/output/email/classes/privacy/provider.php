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
 * Privacy class for requesting user data.
 *
 * @package    message_email
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_email\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use \core_privacy\local\request\approved_userlist;

/**
 * Privacy class for requesting user data.
 *
 * @package    message_email
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\core_userlist_provider,
        \core_privacy\local\request\user_preference_provider,
        \core_privacy\local\request\plugin\provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $messageemailmessages = [
            'useridto' => 'privacy:metadata:message_email_messages:useridto',
            'conversationid' => 'privacy:metadata:message_email_messages:conversationid',
            'messageid' => 'privacy:metadata:message_email_messages:messageid',
        ];
        // Note - this data gets deleted once the scheduled task runs.
        $collection->add_database_table('message_email_messages',
            $messageemailmessages, 'privacy:metadata:message_email_messages');

        $collection->link_external_location('smtp', [
                'recipient' => 'privacy:metadata:recipient',
                'userfrom' => 'privacy:metadata:userfrom',
                'subject' => 'privacy:metadata:subject',
                'fullmessage' => 'privacy:metadata:fullmessage',
                'fullmessagehtml' => 'privacy:metadata:fullmessagehtml',
                'attachment' => 'privacy:metadata:attachment',
                'attachname' => 'privacy:metadata:attachname',
                'replyto' => 'privacy:metadata:replyto',
                'replytoname' => 'privacy:metadata:replytoname'
        ], 'privacy:metadata:externalpurpose');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int         $userid     The user to search.
     * @return  contextlist $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new contextlist();
        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
    }

    /**
     * Delete all use data which matches the specified deletion_criteria.
     *
     * @param   context $context A user context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
    }

    /**
     * Export all user preferences for the plugin
     *
     * @param int $userid
     */
    public static function export_user_preferences(int $userid) {
        $preference = get_user_preferences('message_processor_email_email', null, $userid);
        if (!empty($preference)) {
            writer::export_user_preference(
                'message_email',
                'email',
                $preference,
                get_string('privacy:preference:email', 'message_email')
            );
        }
    }
}
