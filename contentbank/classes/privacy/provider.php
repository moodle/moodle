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
 * Privacy provider implementation for core_contentbank.
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_contentbank\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;
use context_system;
use context_coursecat;
use context_course;

/**
 * Privacy provider implementation for core_contentbank.
 *
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\user_preference_provider {

    /**
     * Returns meta data about this system.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('contentbank_content', [
            'name' => 'privacy:metadata:content:name',
            'contenttype' => 'privacy:metadata:content:contenttype',
            'usercreated' => 'privacy:metadata:content:usercreated',
            'usermodified' => 'privacy:metadata:content:usermodified',
            'timecreated' => 'privacy:metadata:content:timecreated',
            'timemodified' => 'privacy:metadata:content:timemodified',
        ], 'privacy:metadata:contentbankcontent');

        return $collection;
    }

    /**
     * Export all user preferences for the contentbank
     *
     * @param int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $preference = get_user_preferences('core_contentbank_view_list', null, $userid);
        if (isset($preference)) {
            writer::export_user_preference(
                    'core_contentbank',
                    'core_contentbank_view_list',
                    $preference,
                    get_string('privacy:request:preference:set', 'core_contentbank', (object) [
                            'name' => 'core_contentbank_view_list',
                            'value' => $preference,
                    ])
            );
        }
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $sql = "SELECT DISTINCT ctx.id
                  FROM {context} ctx
                  JOIN {contentbank_content} cb
                       ON cb.contextid = ctx.id
                 WHERE cb.usercreated = :userid
                       AND (ctx.contextlevel = :contextlevel1
                           OR ctx.contextlevel = :contextlevel2
                           OR ctx.contextlevel = :contextlevel3)";

        $params = [
            'userid'        => $userid,
            'contextlevel1' => CONTEXT_SYSTEM,
            'contextlevel2' => CONTEXT_COURSECAT,
            'contextlevel3' => CONTEXT_COURSE,
        ];

        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        $allowedcontextlevels = [
            CONTEXT_SYSTEM,
            CONTEXT_COURSECAT,
            CONTEXT_COURSE,
        ];

        if (!in_array($context->contextlevel, $allowedcontextlevels)) {
            return;
        }

        $sql = "SELECT cb.usercreated as userid
                  FROM {contentbank_content} cb
                 WHERE cb.contextid = :contextid";

        $params = [
            'contextid' => $context->id
        ];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        // Remove contexts different from SYSTEM, COURSECAT or COURSE.
        $contextids = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_SYSTEM || $context->contextlevel == CONTEXT_COURSECAT
                || $context->contextlevel == CONTEXT_COURSE) {
                $carry[] = $context->id;
            }
            return $carry;
        }, []);

        if (empty($contextids)) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED);
        // Retrieve the contentbank_content records created for the user.
        $sql = "SELECT cb.id,
                       cb.name,
                       cb.contenttype,
                       cb.usercreated,
                       cb.usermodified,
                       cb.timecreated,
                       cb.timemodified,
                       cb.contextid
                  FROM {contentbank_content} cb
                 WHERE cb.usercreated = :userid
                       AND cb.contextid {$contextsql}
                 ORDER BY cb.contextid";

        $params = ['userid' => $userid] + $contextparams;

        $contents = $DB->get_recordset_sql($sql, $params);
        $data = [];
        $lastcontextid = null;
        $subcontext = [
            get_string('name', 'core_contentbank'),
        ];
        foreach ($contents as $content) {
            // The core_contentbank data export is organised in:
            // {Sytem|Course Category|Course Context Level}/Content/data.json.
            if ($lastcontextid && $lastcontextid != $content->contextid) {
                $context = \context::instance_by_id($lastcontextid);
                writer::with_context($context)->export_data($subcontext, (object)$data);
                $data = [];
            }
            $data[] = (object) [
                'name' => $content->name,
                'contenttype' => $content->contenttype,
                'usercreated' => transform::user($content->usercreated),
                'usermodified' => transform::user($content->usermodified),
                'timecreated' => transform::datetime($content->timecreated),
                'timemodified' => transform::datetime($content->timemodified)
            ];
            $lastcontextid = $content->contextid;

            // The core_contentbank files export is organised in:
            // {Sytem|Course Category|Course Context Level}/Content/_files/public/_itemid/filename.
            $context = \context::instance_by_id($lastcontextid);
            writer::with_context($context)->export_area_files($subcontext, 'contentbank', 'public', $content->id);
        }
        if (!empty($data)) {
            $context = \context::instance_by_id($lastcontextid);
            writer::with_context($context)->export_data($subcontext, (object)$data);
        }
        $contents->close();
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param   context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!$context instanceof context_system && !$context instanceof context_coursecat
                && !$context instanceof context_course) {
            return;
        }

        static::delete_data($context, []);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof context_system && !$context instanceof context_coursecat
                && !$context instanceof context_course) {
            return;
        }

        static::delete_data($context, $userlist->get_userids());
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof context_system && !$context instanceof context_coursecat
            && !$context instanceof context_course) {
                continue;
            }
            static::delete_data($context, [$userid]);
        }
    }

    /**
     * Delete data related to a context and users (if defined).
     *
     * @param context $context A context.
     * @param array $userids The user IDs.
     */
    protected static function delete_data(\context $context, array $userids) {
        global $DB;

        $params = ['contextid' => $context->id];
        $select = 'contextid = :contextid';

        // Delete the Content Bank files.
        if (!empty($userids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
            $params += $inparams;
            $select .= ' AND usercreated '.$insql;
        }
        $fs = get_file_storage();
        $contents = $DB->get_records_select('contentbank_content',
            $select, $params);
        foreach ($contents as $content) {
            $fs->delete_area_files($content->contextid, 'contentbank', 'public', $content->id);
        }

        // Delete all the contents.
        $DB->delete_records_select('contentbank_content', $select, $params);
    }
}
