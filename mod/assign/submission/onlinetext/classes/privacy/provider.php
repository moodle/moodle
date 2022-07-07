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
 * @package    assignsubmission_onlinetext
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_onlinetext\privacy;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/assign/locallib.php');

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\contextlist;
use \mod_assign\privacy\assign_plugin_request_data;

/**
 * Privacy class for requesting user data.
 *
 * @package    assignsubmission_onlinetext
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \mod_assign\privacy\assignsubmission_provider,
        \mod_assign\privacy\assignsubmission_user_provider {

    /**
     * Return meta data about this plugin.
     *
     * @param  collection $collection A list of information to add to.
     * @return collection Return the collection after adding to it.
     */
    public static function get_metadata(collection $collection) : collection {
        $detail = [
                    'assignment' => 'privacy:metadata:assignmentid',
                    'submission' => 'privacy:metadata:submissionpurpose',
                    'onlinetext' => 'privacy:metadata:textpurpose'
                  ];
        $collection->add_database_table('assignsubmission_onlinetext', $detail, 'privacy:metadata:tablepurpose');
        $collection->link_subsystem('core_files', 'privacy:metadata:filepurpose');
        return $collection;
    }

    /**
     * This is covered by mod_assign provider and the query on assign_submissions.
     *
     * @param  int $userid The user ID that we are finding contexts for.
     * @param  contextlist $contextlist A context list to add sql and params to for contexts.
     */
    public static function get_context_for_userid_within_submission(int $userid, contextlist $contextlist) {
        // This is already fetched from mod_assign.
    }

    /**
     * This is also covered by the mod_assign provider and it's queries.
     *
     * @param  \mod_assign\privacy\useridlist $useridlist An object for obtaining user IDs of students.
     */
    public static function get_student_user_ids(\mod_assign\privacy\useridlist $useridlist) {
        // No need.
    }

    /**
     * If you have tables that contain userids and you can generate entries in your tables without creating an
     * entry in the assign_submission table then please fill in this method.
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
    public static function export_submission_user_data(assign_plugin_request_data $exportdata) {
        // We currently don't show submissions to teachers when exporting their data.
        if ($exportdata->get_user() != null) {
            return null;
        }
        // Retrieve text for this submission.
        $assign = $exportdata->get_assign();
        $plugin = $assign->get_plugin_by_type('assignsubmission', 'onlinetext');
        $submission = $exportdata->get_pluginobject();
        $editortext = $plugin->get_editor_text('onlinetext', $submission->id);
        $context = $exportdata->get_context();
        if (!empty($editortext)) {
            $submissiontext = new \stdClass();
            $currentpath = $exportdata->get_subcontext();
            $currentpath[] = get_string('privacy:path', 'assignsubmission_onlinetext');
            $submissiontext->text = writer::with_context($context)->rewrite_pluginfile_urls($currentpath,
                    'assignsubmission_onlinetext', 'submissions_onlinetext', $submission->id, $editortext);
            writer::with_context($context)
                    ->export_area_files($currentpath, 'assignsubmission_onlinetext', 'submissions_onlinetext', $submission->id)
                    // Add the text to the exporter.
                    ->export_data($currentpath, $submissiontext);

            // Handle plagiarism data.
            $coursecontext = $context->get_course_context();
            $userid = $submission->userid;
            \core_plagiarism\privacy\provider::export_plagiarism_user_data($userid, $context, $currentpath, [
                'cmid' => $context->instanceid,
                'course' => $coursecontext->instanceid,
                'userid' => $userid,
                'content' => $editortext,
                'assignment' => $submission->assignment
            ]);
        }
    }

    /**
     * Any call to this method should delete all user data for the context defined in the deletion_criteria.
     *
     * @param  assign_plugin_request_data $requestdata Data useful for deleting user data from this sub-plugin.
     */
    public static function delete_submission_for_context(assign_plugin_request_data $requestdata) {
        global $DB;

        \core_plagiarism\privacy\provider::delete_plagiarism_for_context($requestdata->get_context());

        // Delete related files.
        $fs = get_file_storage();
        $fs->delete_area_files($requestdata->get_context()->id, 'assignsubmission_onlinetext',
                ASSIGNSUBMISSION_ONLINETEXT_FILEAREA);

        // Delete the records in the table.
        $DB->delete_records('assignsubmission_onlinetext', ['assignment' => $requestdata->get_assignid()]);
    }

    /**
     * A call to this method should delete user data (where practicle) from the userid and context.
     *
     * @param  assign_plugin_request_data $deletedata Details about the user and context to focus the deletion.
     */
    public static function delete_submission_for_userid(assign_plugin_request_data $deletedata) {
        global $DB;

        \core_plagiarism\privacy\provider::delete_plagiarism_for_user($deletedata->get_user()->id, $deletedata->get_context());

        $submissionid = $deletedata->get_pluginobject()->id;

        // Delete related files.
        $fs = get_file_storage();
        $fs->delete_area_files($deletedata->get_context()->id, 'assignsubmission_onlinetext', ASSIGNSUBMISSION_ONLINETEXT_FILEAREA,
                $submissionid);

        // Delete the records in the table.
        $DB->delete_records('assignsubmission_onlinetext', ['assignment' => $deletedata->get_assignid(),
                'submission' => $submissionid]);
    }

    /**
     * Deletes all submissions for the submission ids / userids provided in a context.
     * assign_plugin_request_data contains:
     * - context
     * - assign object
     * - submission ids (pluginids)
     * - user ids
     * @param  assign_plugin_request_data $deletedata A class that contains the relevant information required for deletion.
     */
    public static function delete_submissions(assign_plugin_request_data $deletedata) {
        global $DB;

        \core_plagiarism\privacy\provider::delete_plagiarism_for_users($deletedata->get_userids(), $deletedata->get_context());
        if (empty($deletedata->get_submissionids())) {
            return;
        }

        $fs = get_file_storage();
        list($sql, $params) = $DB->get_in_or_equal($deletedata->get_submissionids(), SQL_PARAMS_NAMED);
        $fs->delete_area_files_select($deletedata->get_context()->id,
                'assignsubmission_onlinetext', ASSIGNSUBMISSION_ONLINETEXT_FILEAREA, $sql, $params);

        $params['assignid'] = $deletedata->get_assignid();
        $DB->delete_records_select('assignsubmission_onlinetext', "assignment = :assignid AND submission $sql", $params);
    }
}
