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
 * Privacy Subsystem implementation for editor_atto.
 *
 * @package    editor_atto
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace editor_atto\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\helper;
use \core_privacy\local\request\deletion_criteria;
use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\userlist;
use \core_privacy\local\request\approved_userlist;

/**
 * Privacy Subsystem implementation for editor_atto.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // The Atto editor stores user provided data.
        \core_privacy\local\metadata\provider,
        // The Atto editor provides data directly to core.
        \core_privacy\local\request\plugin\provider,
        // The Atto editor is capable of determining which users have data within it.
        \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns information about how editor_atto stores its data.
     *
     * @param   collection     $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        // There isn't much point giving details about the pageid, etc.
        $collection->add_database_table('editor_atto_autosave', [
                'userid' => 'privacy:metadata:database:atto_autosave:userid',
                'drafttext' => 'privacy:metadata:database:atto_autosave:drafttext',
                'timemodified' => 'privacy:metadata:database:atto_autosave:timemodified',
            ], 'privacy:metadata:database:atto_autosave');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int         $userid     The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : \core_privacy\local\request\contextlist {
        // This block doesn't know who information is stored against unless it
        // is at the user context.
        $contextlist = new \core_privacy\local\request\contextlist();

        $sql = "SELECT
                    c.id
                  FROM {editor_atto_autosave} eas
                  JOIN {context} c ON c.id = eas.contextid
                 WHERE contextlevel = :contextuser AND c.instanceid = :userid";
        $contextlist->add_from_sql($sql, ['contextuser' => CONTEXT_USER, 'userid' => $userid]);

        $sql = "SELECT contextid FROM {editor_atto_autosave} WHERE userid = :userid";
        $contextlist->add_from_sql($sql, ['userid' => $userid]);

        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        $params = [
            'contextid' => $context->id
        ];

        $sql = "SELECT userid
                  FROM {editor_atto_autosave}
                 WHERE contextid = :contextid";

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $user = $contextlist->get_user();

        // Firstly export all autosave records from all contexts in the list owned by the given user.

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $contextparams['userid'] = $user->id;

        $sql = "SELECT *
                  FROM {editor_atto_autosave}
                 WHERE userid = :userid AND contextid {$contextsql}";

        $autosaves = $DB->get_recordset_sql($sql, $contextparams);
        self::export_autosaves($user, $autosaves);

        // Additionally export all eventual records in the given user's context regardless the actual owner.
        // We still consider them to be the user's personal data even when edited by someone else.

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $contextparams['userid'] = $user->id;
        $contextparams['contextuser'] = CONTEXT_USER;

        $sql = "SELECT eas.*
                  FROM {editor_atto_autosave} eas
                  JOIN {context} c ON c.id = eas.contextid
                 WHERE c.id {$contextsql} AND c.contextlevel = :contextuser AND c.instanceid = :userid";

        $autosaves = $DB->get_recordset_sql($sql, $contextparams);
        self::export_autosaves($user, $autosaves);
    }

    /**
     * Export all autosave records in the recordset, and close the recordset when finished.
     *
     * @param   \stdClass   $user The user whose data is to be exported
     * @param   \moodle_recordset $autosaves The recordset containing the data to export
     */
    protected static function export_autosaves(\stdClass $user, \moodle_recordset $autosaves) {
        foreach ($autosaves as $autosave) {
            $context = \context::instance_by_id($autosave->contextid);
            $subcontext = [
                get_string('autosaves', 'editor_atto'),
                $autosave->id,
            ];

            $html = writer::with_context($context)
                ->rewrite_pluginfile_urls($subcontext, 'user', 'draft', $autosave->draftid, $autosave->drafttext);

            $data = (object) [
                'drafttext' => format_text($html, FORMAT_HTML, static::get_filter_options()),
                'timemodified' => \core_privacy\local\request\transform::datetime($autosave->timemodified),
            ];

            if ($autosave->userid != $user->id) {
                $data->author = \core_privacy\local\request\transform::user($autosave->userid);
            }

            writer::with_context($context)
                ->export_data($subcontext, $data)
                ->export_area_files($subcontext, 'user', 'draft', $autosave->draftid);
        }
        $autosaves->close();
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param   \context $context   The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        $DB->delete_records('editor_atto_autosave', [
                'contextid' => $context->id,
            ]);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        $userids = $userlist->get_userids();

        list($useridsql, $useridsqlparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params = ['contextid' => $context->id] + $useridsqlparams;

        $DB->delete_records_select('editor_atto_autosave', "contextid = :contextid AND userid {$useridsql}",
            $params);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $contextparams['userid'] = $user->id;

        $sql = "SELECT * FROM {editor_atto_autosave} WHERE contextid {$contextsql}";
        $autosaves = $DB->delete_records_select('editor_atto_autosave', "userid = :userid AND contextid {$contextsql}",
                $contextparams);
    }

    /**
     * Get the filter options.
     *
     * This is shared to allow unit testing too.
     *
     * @return  \stdClass
     */
    public static function get_filter_options() {
        return (object) [
            'overflowdiv' => true,
            'noclean' => true,
        ];
    }
}
