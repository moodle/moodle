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
 * @package    search_simpledb
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace search_simpledb\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Provider for the search_simpledb plugin.
 *
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\core_userlist_provider,
        \core_privacy\local\request\plugin\provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table(
            'search_simpledb_index',
            [
                'docid' => 'privacy:metadata:index:docid',
                'itemid' => 'privacy:metadata:index:itemid',
                'title' => 'privacy:metadata:index:title',
                'content' => 'privacy:metadata:index:content',
                'contextid' => 'privacy:metadata:index:contextid',
                'areaid' => 'privacy:metadata:index:areaid',
                'type' => 'privacy:metadata:index:type',
                'courseid' => 'privacy:metadata:index:courseid',
                'owneruserid' => 'privacy:metadata:index:owneruserid',
                'modified' => 'privacy:metadata:index:modified',
                'userid' => 'privacy:metadata:index:userid',
                'description1' => 'privacy:metadata:index:description1',
                'description2' => 'privacy:metadata:index:description2',
            ],
            'privacy:metadata:index'
        );
        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int         $userid     The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();

        $params = ['userid' => $userid, 'owneruserid' => $userid];
        $sql = "SELECT DISTINCT contextid
                  FROM {search_simpledb_index}
                 WHERE (userid = :userid OR owneruserid = :owneruserid)";
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

        $params = [
            'contextid' => $context->id,
        ];

        $sql = "SELECT ssi.userid
                  FROM {search_simpledb_index} ssi
                 WHERE ssi.contextid = :contextid";

        $userlist->add_from_sql('userid', $sql, $params);

        $sql = "SELECT ssi.owneruserid AS userid
                  FROM {search_simpledb_index} ssi
                 WHERE ssi.contextid = :contextid";

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        // Plugin search_simpledb uses the default document object (core_search\document) which uses FORMAT_PLAIN.
        $textformat = FORMAT_PLAIN;

        $userid = $contextlist->get_user()->id;

        $ctxfields = \context_helper::get_preload_record_columns_sql('ctx');
        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $sql = "SELECT ssi.*, $ctxfields FROM {search_simpledb_index} ssi
                  JOIN {context} ctx ON ctx.id = ssi.contextid
                 WHERE ssi.contextid $contextsql AND (ssi.userid = :userid OR ssi.owneruserid = :owneruserid)";
        $params = ['userid' => $userid, 'owneruserid' => $userid] + $contextparams;

        $records = $DB->get_recordset_sql($sql, $params);
        foreach ($records as $record) {

            \context_helper::preload_from_record($record);
            $context = \context::instance_by_id($record->contextid);
            $document = (object)[
                'title' => format_string($record->title, true, ['context' => $context]),
                'content' => format_text($record->content, $textformat, ['context' => $context]),
                'description1' => format_text($record->description1, $textformat, ['context' => $context]),
                'description2' => format_text($record->description2, $textformat, ['context' => $context]),
                'context' => $context->get_context_name(true, true),
                'modified' => transform::datetime($record->modified),

            ];

            $path = [get_string('search', 'search'), $record->docid];
            writer::with_context($context)->export_data($path, $document);
        }
        $records->close();
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param   context                 $context   The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        $DB->delete_records('search_simpledb_index', ['contextid' => $context->id]);

        if ($context->contextlevel == CONTEXT_USER) {
            $select = "userid = :userid OR owneruserid = :owneruserid";
            $params = ['userid' => $context->instanceid, 'owneruserid' => $context->instanceid];
            $DB->delete_records_select('search_simpledb_index', $select, $params);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $userid = $contextlist->get_user()->id;

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $select = "contextid $contextsql AND (userid = :userid OR owneruserid = :owneruserid)";
        $params = ['userid' => $userid, 'owneruserid' => $userid] + $contextparams;
        $DB->delete_records_select('search_simpledb_index', $select, $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();

        list($usersql, $userparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        list($ownersql, $ownerparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);

        $select = "contextid = :contextid AND (userid {$usersql} OR owneruserid {$ownersql})";
        $params = ['contextid' => $context->id] + $userparams + $ownerparams;

        $DB->delete_records_select('search_simpledb_index', $select, $params);
    }
}
