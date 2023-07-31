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
 * @package    qbassignsubmission_onlinetex
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qbassignsubmission_onlinetex\privacy;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/qbassign/locallib.php');

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\contextlist;
use \mod_qbassign\privacy\qbassign_plugin_request_data;

/**
 * Privacy class for requesting user data.
 *
 * @package    qbassignsubmission_onlinetex
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \mod_qbassign\privacy\qbassignsubmission_provider,
        \mod_qbassign\privacy\qbassignsubmission_user_provider {

    /**
     * Return meta data about this plugin.
     *
     * @param  collection $collection A list of information to add to.
     * @return collection Return the collection after adding to it.
     */
    public static function get_metadata(collection $collection) : collection {
        $detail = [
                    'qbassignment' => 'privacy:metadata:qbassignmentid',
                    'submission' => 'privacy:metadata:submissionpurpose',
                    'onlinetex' => 'privacy:metadata:textpurpose'
                  ];
        $collection->add_database_table('qbassignsubmission_onlinetex', $detail, 'privacy:metadata:tablepurpose');
        $collection->link_subsystem('core_files', 'privacy:metadata:filepurpose');
        return $collection;
    }

    /**
     * This is covered by mod_qbassign provider and the query on qbassign_submissions.
     *
     * @param  int $userid The user ID that we are finding contexts for.
     * @param  contextlist $contextlist A context list to add sql and params to for contexts.
     */
    public static function get_context_for_userid_within_submission(int $userid, contextlist $contextlist) {
        // This is already fetched from mod_qbassign.
    }

    /**
     * This is also covered by the mod_qbassign provider and it's queries.
     *
     * @param  \mod_qbassign\privacy\useridlist $useridlist An object for obtaining user IDs of students.
     */
    public static function get_student_user_ids(\mod_qbassign\privacy\useridlist $useridlist) {
        // No need.
    }

    /**
     * If you have tables that contain userids and you can generate entries in your tables without creating an
     * entry in the qbassign_submission table then please fill in this method.
     *
     * @param  \core_privacy\local\request\userlist $userlist The userlist object
     */
    public static function get_userids_from_context(\core_privacy\local\request\userlist $userlist) {
        // Not required.
    }

    /**
     * Export all user data for this plugin.
     *
     * @param  qbassign_plugin_request_data $exportdata Data used to determine which context and user to export and other useful
     * information to help with exporting.
     */
    public static function export_submission_user_data(qbassign_plugin_request_data $exportdata) {
        // We currently don't show submissions to teachers when exporting their data.
        if ($exportdata->get_user() != null) {
            return null;
        }
        // Retrieve text for this submission.
        $qbassign = $exportdata->get_qbassign();
        $plugin = $qbassign->get_plugin_by_type('qbassignsubmission', 'onlinetex');
        $submission = $exportdata->get_pluginobject();
        $editortext = $plugin->get_editor_text('onlinetex', $submission->id);
        $context = $exportdata->get_context();
        if (!empty($editortext)) {
            $submissiontext = new \stdClass();
            $currentpath = $exportdata->get_subcontext();
            $currentpath[] = get_string('privacy:path', 'qbassignsubmission_onlinetex');
            $submissiontext->text = writer::with_context($context)->rewrite_pluginfile_urls($currentpath,
                    'qbassignsubmission_onlinetex', 'submissions_onlinetex', $submission->id, $editortext);
            writer::with_context($context)
                    ->export_area_files($currentpath, 'qbassignsubmission_onlinetex', 'submissions_onlinetex', $submission->id)
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
                'qbassignment' => $submission->qbassignment
            ]);
        }
    }

    /**
     * Any call to this method should delete all user data for the context defined in the deletion_criteria.
     *
     * @param  qbassign_plugin_request_data $requestdata Data useful for deleting user data from this sub-plugin.
     */
    public static function delete_submission_for_context(qbassign_plugin_request_data $requestdata) {
        global $DB;

        \core_plagiarism\privacy\provider::delete_plagiarism_for_context($requestdata->get_context());

        // Delete related files.
        $fs = get_file_storage();
        $fs->delete_area_files($requestdata->get_context()->id, 'qbassignsubmission_onlinetex',
                qbassignsubmission_onlinetex_FILEAREA);

        // Delete the records in the table.
        $DB->delete_records('qbassignsubmission_onlinetex', ['qbassignment' => $requestdata->get_qbassignid()]);
    }

    /**
     * A call to this method should delete user data (where practicle) from the userid and context.
     *
     * @param  qbassign_plugin_request_data $deletedata Details about the user and context to focus the deletion.
     */
    public static function delete_submission_for_userid(qbassign_plugin_request_data $deletedata) {
        global $DB;

        \core_plagiarism\privacy\provider::delete_plagiarism_for_user($deletedata->get_user()->id, $deletedata->get_context());

        $submissionid = $deletedata->get_pluginobject()->id;

        // Delete related files.
        $fs = get_file_storage();
        $fs->delete_area_files($deletedata->get_context()->id, 'qbassignsubmission_onlinetex', qbassignsubmission_onlinetex_FILEAREA,
                $submissionid);

        // Delete the records in the table.
        $DB->delete_records('qbassignsubmission_onlinetex', ['qbassignment' => $deletedata->get_qbassignid(),
                'submission' => $submissionid]);
    }

    /**
     * Deletes all submissions for the submission ids / userids provided in a context.
     * qbassign_plugin_request_data contains:
     * - context
     * - qbassign object
     * - submission ids (pluginids)
     * - user ids
     * @param  qbassign_plugin_request_data $deletedata A class that contains the relevant information required for deletion.
     */
    public static function delete_submissions(qbassign_plugin_request_data $deletedata) {
        global $DB;

        \core_plagiarism\privacy\provider::delete_plagiarism_for_users($deletedata->get_userids(), $deletedata->get_context());
        if (empty($deletedata->get_submissionids())) {
            return;
        }

        $fs = get_file_storage();
        list($sql, $params) = $DB->get_in_or_equal($deletedata->get_submissionids(), SQL_PARAMS_NAMED);
        $fs->delete_area_files_select($deletedata->get_context()->id,
                'qbassignsubmission_onlinetex', qbassignsubmission_onlinetex_FILEAREA, $sql, $params);

        $params['qbassignid'] = $deletedata->get_qbassignid();
        $DB->delete_records_select('qbassignsubmission_onlinetex', "qbassignment = :qbassignid AND submission $sql", $params);
    }
}
