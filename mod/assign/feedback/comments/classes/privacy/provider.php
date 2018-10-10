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
 * @package    assignfeedback_comments
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_comments\privacy;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/assign/locallib.php');

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\contextlist;
use \mod_assign\privacy\assign_plugin_request_data;
use \mod_assign\privacy\useridlist;

/**
 * Privacy class for requesting user data.
 *
 * @package    assignfeedback_comments
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \mod_assign\privacy\assignfeedback_provider,
        \mod_assign\privacy\assignfeedback_user_provider {

    /**
     * Return meta data about this plugin.
     *
     * @param  collection $collection A list of information to add to.
     * @return collection Return the collection after adding to it.
     */
    public static function get_metadata(collection $collection) : collection {
        $data = [
            'assignment' => 'privacy:metadata:assignmentid',
            'grade' => 'privacy:metadata:gradepurpose',
            'commenttext' => 'privacy:metadata:commentpurpose'
        ];
        $collection->add_database_table('assignfeedback_comments', $data, 'privacy:metadata:tablesummary');
        return $collection;
    }

    /**
     * No need to fill in this method as all information can be acquired from the assign_grades table in the mod assign
     * provider.
     *
     * @param  int $userid The user ID.
     * @param  contextlist $contextlist The context list.
     */
    public static function get_context_for_userid_within_feedback(int $userid, contextlist $contextlist) {
        // This uses the assign_grades table.
    }

    /**
     * This also does not need to be filled in as this is already collected in the mod assign provider.
     *
     * @param  useridlist $useridlist A list of user IDs
     */
    public static function get_student_user_ids(useridlist $useridlist) {
        // Not required.
    }

    /**
     * If you have tables that contain userids and you can generate entries in your tables without creating an
     * entry in the assign_grades table then please fill in this method.
     *
     * @param  \core_privacy\local\request\userlist $userlist The userlist object
     */
    public static function get_userids_from_context(\core_privacy\local\request\userlist $userlist) {
        // Not required.
    }

    /**
     * Export all user data for this plugin.
     *
     * @param  assign_plugin_request_data $exportdata Data used to determine which context and user to export and other useful
     * information to help with exporting.
     */
    public static function export_feedback_user_data(assign_plugin_request_data $exportdata) {
        // Get that comment information and jam it into that exporter.
        $assign = $exportdata->get_assign();
        $plugin = $assign->get_plugin_by_type('assignfeedback', 'comments');
        $comments = $plugin->get_feedback_comments($exportdata->get_pluginobject()->id);
        if ($comments && !empty($comments->commenttext)) {
            $data = (object)['commenttext' => format_text($comments->commenttext, $comments->commentformat,
                    ['context' => $exportdata->get_context()])];
            writer::with_context($exportdata->get_context())
                    ->export_data(array_merge($exportdata->get_subcontext(),
                            [get_string('privacy:commentpath', 'assignfeedback_comments')]), $data);
        }
    }

    /**
     * Any call to this method should delete all user data for the context defined in the deletion_criteria.
     *
     * @param  assign_plugin_request_data $requestdata Data useful for deleting user data from this sub-plugin.
     */
    public static function delete_feedback_for_context(assign_plugin_request_data $requestdata) {
        $assign = $requestdata->get_assign();
        $plugin = $assign->get_plugin_by_type('assignfeedback', 'comments');
        $plugin->delete_instance();
    }

    /**
     * Calling this function should delete all user data associated with this grade entry.
     *
     * @param  assign_plugin_request_data $requestdata Data useful for deleting user data.
     */
    public static function delete_feedback_for_grade(assign_plugin_request_data $requestdata) {
        global $DB;
        $DB->delete_records('assignfeedback_comments', ['assignment' => $requestdata->get_assignid(),
                'grade' => $requestdata->get_pluginobject()->id]);
    }

    /**
     * Deletes all feedback for the grade ids / userids provided in a context.
     * assign_plugin_request_data contains:
     * - context
     * - assign object
     * - grade ids (pluginids)
     * - user ids
     * @param  assign_plugin_request_data $deletedata A class that contains the relevant information required for deletion.
     */
    public static function delete_feedback_for_grades(assign_plugin_request_data $deletedata) {
        global $DB;
        if (empty($deletedata->get_gradeids())) {
            return;
        }
        list($sql, $params) = $DB->get_in_or_equal($deletedata->get_gradeids(), SQL_PARAMS_NAMED);
        $params['assignment'] = $deletedata->get_assignid();
        $DB->delete_records_select('assignfeedback_comments', "assignment = :assignment AND grade $sql", $params);
    }
}
