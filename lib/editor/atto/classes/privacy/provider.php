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
        \core_privacy\local\request\plugin\provider {

    /**
     * Returns information about how editor_atto stores its data.
     *
     * @param   collection     $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) {
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
    public static function get_contexts_for_userid($userid) {
        // This block doesn't know who information is stored against unless it
        // is at the user context.
        $contextlist = new \core_privacy\local\request\contextlist();
        $contextuser = \context_user::instance($userid);

        $sql = "SELECT contextid FROM {editor_atto_autosave} WHERE userid = :userid OR contextid = :contextid";
        $params = [
            'userid' => $userid,
            'contextid' => $contextuser->id,
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $contextparams['userid'] = $contextlist->get_user()->id;

        $sql = "SELECT *
                  FROM {editor_atto_autosave}
                 WHERE
                    (userid = :userid AND contextid {$contextsql})
                    OR
                    (contextid = :usercontext)";

        $usercontext = \context_user::instance($user->id);
        $contextparams['usercontext'] = $usercontext->id;
        $autosaves = $DB->get_recordset_sql($sql, $contextparams);

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
     * @param   context                 $context   The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        $DB->delete_records('editor_atto_autosave', [
                'contextid' => $context->id,
            ]);
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
