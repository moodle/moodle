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
 * Privacy Subsystem implementation for tool_mobile.
 *
 * @package    tool_mobile
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_mobile\privacy;
defined('MOODLE_INTERNAL') || die();
use core_privacy\local\request\writer;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;

/**
 * Privacy provider for tool_mobile.
 *
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
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
     * @param   collection     $collection The initialised item collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        // There is a one user preference.
        $collection->add_user_preference('tool_mobile_autologin_request_last',
            'privacy:metadata:preference:tool_mobile_autologin_request_last');
        $collection->add_subsystem_link('core_userkey', [], 'privacy:metadata:core_userkey');

        return $collection;
    }
    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $sql = "SELECT ctx.id
                  FROM {user_private_key} k
                  JOIN {user} u ON k.userid = u.id
                  JOIN {context} ctx ON ctx.instanceid = u.id AND ctx.contextlevel = :contextlevel
                 WHERE k.userid = :userid AND (k.script = 'tool_mobile' OR k.script = 'tool_mobile/qrlogin')";
        $params = ['userid' => $userid, 'contextlevel' => CONTEXT_USER];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!is_a($context, \context_user::class)) {
            return;
        }

        // Add users based on userkey.
        \core_userkey\privacy\provider::get_user_contexts_with_script($userlist, $context, 'tool_mobile');
        \core_userkey\privacy\provider::get_user_contexts_with_script($userlist, $context, 'tool_mobile/qrlogin');
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        // If the user has data, then only the CONTEXT_USER should be present so get the first context.
        $contexts = $contextlist->get_contexts();
        if (count($contexts) == 0) {
            return;
        }
        $context = reset($contexts);
        // Sanity check that context is at the user context level, then get the userid.
        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }
        // Export associated userkeys.
        \core_userkey\privacy\provider::export_userkeys($context, [], 'tool_mobile');
        \core_userkey\privacy\provider::export_userkeys($context, [], 'tool_mobile/qrlogin');
    }
    /**
     * Export all user preferences for the plugin.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $autologinrequestlast = get_user_preferences('tool_mobile_autologin_request_last', null, $userid);
        if ($autologinrequestlast !== null) {
            $time = transform::datetime($autologinrequestlast);
            writer::export_user_preference('tool_mobile',
                'tool_mobile_autologin_request_last',
                $time,
                get_string('privacy:metadata:preference:tool_mobile_autologin_request_last', 'tool_mobile')
            );
        }
    }
    /**
     * Delete all use data which matches the specified deletion_criteria.
     *
     * @param context $context A user context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // Sanity check that context is at the user context level, then get the userid.
        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }
        $userid = $context->instanceid;
        // Delete all the userkeys.
        \core_userkey\privacy\provider::delete_userkeys('tool_mobile', $userid);
        \core_userkey\privacy\provider::delete_userkeys('tool_mobile/qrlogin', $userid);
    }
    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        // If the user has data, then only the user context should be present so get the first context.
        $contexts = $contextlist->get_contexts();
        if (count($contexts) == 0) {
            return;
        }
        $context = reset($contexts);
        // Sanity check that context is at the user context level, then get the userid.
        if ($context->contextlevel !== CONTEXT_USER) {
            return;
        }
        $userid = $context->instanceid;
        // Delete all the userkeys.
        \core_userkey\privacy\provider::delete_userkeys('tool_mobile', $userid);
        \core_userkey\privacy\provider::delete_userkeys('tool_mobile/qrlogin', $userid);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();
        $userids = $userlist->get_userids();
        $userid = reset($userids);

        // Only deleting data for the user ID in that user's user context should be valid.
        if ($context->contextlevel !== CONTEXT_USER || count($userids) != 1 || $userid != $context->instanceid) {
            return;
        }

        // Delete all the userkeys.
        \core_userkey\privacy\provider::delete_userkeys('tool_mobile', $userid);
        \core_userkey\privacy\provider::delete_userkeys('tool_mobile/qrlogin', $userid);
    }
}
