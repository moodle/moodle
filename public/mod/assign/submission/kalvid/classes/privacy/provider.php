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
 * @package    assignsubmission_kalvid
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_kalvid\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\contextlist;
use \mod_assign\privacy\assign_plugin_request_data;

/**
 * Privacy class for requesting user data.
 *
 * @package    assignsubmission_kalvid
 * @copyright  2025 Kaltura Inc
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
    public static function get_metadata(collection $collection): collection {
        $detail = [
            'assignment' => 'privacy:metadata:assignmentid',
            'submission' => 'privacy:metadata:submissionpurpose',
            'entry_id' => 'privacy:metadata:entryid',
            'source' => 'privacy:metadata:source',
        ];
        $collection->add_database_table('assignsubmission_kalvid', $detail, 'privacy:metadata:kalvid_tablepurpose');
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
        global $DB;
        // We currently don't show submissions to teachers when exporting their data.
        if ($exportdata->get_user() != null) {
            return null;
        }
        $context = $exportdata->get_context();
        $currentpath = $exportdata->get_subcontext();
        $currentpath[] = get_string('privacy:path', 'assignsubmission_kalvid');
        $submission = $exportdata->get_pluginobject();
        $kalvidsubmission = $DB->get_record('assignsubmission_kalvid', ['submission' => $submission->id]);
        if (!empty($kalvidsubmission)) {
            writer::with_context($context)->export_data($currentpath, $kalvidsubmission);
        }
    }

    /**
     * Any call to this method should delete all user data for the context defined in the deletion_criteria.
     *
     * @param  assign_plugin_request_data $requestdata Data useful for deleting user data from this sub-plugin.
     */
    public static function delete_submission_for_context(assign_plugin_request_data $requestdata) {
        global $DB;
        $DB->delete_records('assignsubmission_kalvid', ['assignment' => $requestdata->get_assignid()]);
    }

    /**
     * A call to this method should delete user data (where practicle) from the userid and context.
     *
     * @param  assign_plugin_request_data $deletedata Details about the user and context to focus the deletion.
     */
    public static function delete_submission_for_userid(assign_plugin_request_data $deletedata) {
        global $DB;

        $submissionid = $deletedata->get_pluginobject()->id;

        // Delete the records in the table.
        $DB->delete_records('assignsubmission_kalvid', ['assignment' => $deletedata->get_assignid(),
            'submission' => $submissionid]);
    }

    /**
     * Delete multiple submissions for a given context.
     *
     * @param \mod_assign\privacy\assign_plugin_request_data $requestdata
     */
    public static function delete_submissions(assign_plugin_request_data $requestdata) {
        global $DB;

        $submissionids = $requestdata->get_submissionids();

        if (empty($submissionids)) {
            return;
        }

        list($insql, $params) = $DB->get_in_or_equal($submissionids, SQL_PARAMS_NAMED);

        $params['assignment'] = $requestdata->get_assignid();

        $DB->delete_records_select(
            'assignsubmission_kalvid',
            "assignment = :assignment AND submission $insql",
            $params
        );
    }
}
