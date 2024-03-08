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
 * @package    tool_dataprivacy
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_dataprivacy\privacy;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use context;
use context_user;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\transform;
use \core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use dml_exception;
use stdClass;
use tool_dataprivacy\api;
use tool_dataprivacy\local\helper as tool_helper;

/**
 * Privacy class for requesting user data.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // This tool stores user data.
        \core_privacy\local\metadata\provider,

        // This plugin is capable of determining which users have data within it.
        \core_privacy\local\request\core_userlist_provider,

        // This tool may provide access to and deletion of user data.
        \core_privacy\local\request\plugin\provider,

        // This plugin has some sitewide user preferences to export.
        \core_privacy\local\request\user_preference_provider {
    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'tool_dataprivacy_request',
            [
                'comments' => 'privacy:metadata:request:comments',
                'userid' => 'privacy:metadata:request:userid',
                'requestedby' => 'privacy:metadata:request:requestedby',
                'dpocomment' => 'privacy:metadata:request:dpocomment',
                'timecreated' => 'privacy:metadata:request:timecreated'
            ],
            'privacy:metadata:request'
        );

        // Regarding this block, we are unable to export or purge this data, as
        // it would damage the privacy data across the whole site.
        $collection->add_database_table(
            'tool_dataprivacy_purposerole',
            [
                'usermodified' => 'privacy:metadata:purpose:usermodified',
            ],
            'privacy:metadata:purpose'
        );

        $collection->add_user_preference(tool_helper::PREF_REQUEST_FILTERS,
            'privacy:metadata:preference:tool_dataprivacy_request-filters');
        $collection->add_user_preference(tool_helper::PREF_REQUEST_PERPAGE,
            'privacy:metadata:preference:tool_dataprivacy_request-perpage');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $sql = "SELECT id
                  FROM {context}
                 WHERE instanceid = :userid
                       AND contextlevel = :contextlevel";

        $contextlist = new contextlist();
        $contextlist->set_component('tool_dataprivacy');
        $contextlist->add_from_sql($sql, ['userid' => $userid, 'contextlevel' => CONTEXT_USER]);
        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     *
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!is_a($context, \context_user::class)) {
            return;
        }

        $params = [
            'contextlevel' => CONTEXT_USER,
            'contextid' => $context->id,
        ];

        $sql = "SELECT instanceid AS userid
                  FROM {context}
                 WHERE id = :contextid
                       AND contextlevel = :contextlevel";

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     * @throws coding_exception
     * @throws dml_exception
     * @throws \moodle_exception
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();
        $datarequests = api::get_data_requests($user->id);
        $context = context_user::instance($user->id);
        $contextdatatowrite = [];
        foreach ($datarequests as $request) {
            $record = $request->to_record();
            $data = new stdClass();

            // The user ID that made the request/the request is made for.
            if ($record->requestedby != $record->userid) {
                if ($user->id != $record->requestedby) {
                    // This request is done by this user for another user.
                    $data->userid = fullname($user);
                } else if ($user->id != $record->userid) {
                    // This request was done by another user on behalf of this user.
                    $data->requestedby = fullname($user);
                }
            }

            // Request type.
            $data->type = tool_helper::get_shortened_request_type_string($record->type);
            // Status.
            $data->status = tool_helper::get_request_status_string($record->status);
            // Creation method.
            $data->creationmethod = tool_helper::get_request_creation_method_string($record->creationmethod);
            // Comments.
            $data->comments = $record->comments;
            // The DPO's comment about this request.
            $data->dpocomment = $record->dpocomment;
            // The date and time this request was lodged.
            $data->timecreated = transform::datetime($record->timecreated);
            $contextdatatowrite[] = $data;
        }

        // User context / Privacy and policies / Data requests.
        $subcontext = [
            get_string('privacyandpolicies', 'admin'),
            get_string('datarequests', 'tool_dataprivacy'),
        ];
        writer::with_context($context)->export_data($subcontext, (object)$contextdatatowrite);

        // Write generic module intro files.
        helper::export_context_files($context, $user);
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context) {
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist    $userlist The approved context and user information to delete information for.
     *
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
    }

    /**
     * Export all user preferences for the plugin.
     *
     * @param   int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $preffilter = get_user_preferences(tool_helper::PREF_REQUEST_FILTERS, null, $userid);
        if ($preffilter !== null) {
            $filters = json_decode($preffilter);
            $descriptions = [];
            foreach ($filters as $filter) {
                list($category, $value) = explode(':', $filter);
                $option = new stdClass();
                switch($category) {
                    case tool_helper::FILTER_TYPE:
                        $option->category = get_string('requesttype', 'tool_dataprivacy');
                        $option->name = tool_helper::get_shortened_request_type_string($value);
                        break;
                    case tool_helper::FILTER_STATUS:
                        $option->category = get_string('requeststatus', 'tool_dataprivacy');
                        $option->name = tool_helper::get_request_status_string($value);
                        break;
                    case tool_helper::FILTER_CREATION:
                        $option->category = get_string('requestcreation', 'tool_dataprivacy');
                        $option->name = tool_helper::get_request_creation_method_string($value);
                        break;
                }
                $descriptions[] = get_string('filteroption', 'tool_dataprivacy', $option);
            }
            // Export the filter preference as comma-separated values and text descriptions.
            $values = implode(', ', $filters);
            $descriptionstext = implode(', ', $descriptions);
            writer::export_user_preference('tool_dataprivacy', tool_helper::PREF_REQUEST_FILTERS, $values, $descriptionstext);
        }

        $prefperpage = get_user_preferences(tool_helper::PREF_REQUEST_PERPAGE, null, $userid);
        if ($prefperpage !== null) {
            writer::export_user_preference('tool_dataprivacy', tool_helper::PREF_REQUEST_PERPAGE, $prefperpage,
                get_string('privacy:metadata:preference:tool_dataprivacy_request-perpage', 'tool_dataprivacy'));
        }
    }
}
