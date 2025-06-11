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
 * Privacy class for requesting user data
 * @package assignfeedback_onenote
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace assignfeedback_onenote\privacy;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/assign/locallib.php');

use core_privacy\local\metadata\collection;
use core_privacy\local\metadata\provider as metadataprovider;
use mod_assign\privacy\assignfeedback_provider;
use mod_assign\privacy\assignfeedback_user_provider;
use core_privacy\local\request\writer;
use core_privacy\local\request\contextlist;
use mod_assign\privacy\assign_plugin_request_data;
use mod_assign\privacy\useridlist;
/**
 * Privacy class for requesting user data.
 *
 * @package assignfeedback_onenote
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */
class provider implements
        metadataprovider,
        assignfeedback_provider,
        assignfeedback_user_provider {
    /**
     * Return meta data about this plugin.
     *
     * @param  collection $collection A list of information to add to.
     * @return collection Return the collection after adding to it.
     */
    public static function get_metadata(collection $collection): collection {
        $detail = [
            'assignment' => 'privacy:metadata:assignmentid',
            'grade' => 'privacy:metadata:gradepurpose',
            'numfiles' => 'privacy:metadata:numfiles',
        ];
        $collection->add_database_table('assignfeedback_onenote', $detail, 'privacy:metadata:tablepurpose');
        return $collection;
    }

    /**
     * This is covered by the mod_assign provider.
     *
     * @param int $userid The user ID to get context IDs for.
     * @param \core_privacy\local\request\contextlist $contextlist Use add_from_sql with this object to add your context IDs.
     */
    public static function get_context_for_userid_within_feedback(int $userid, contextlist $contextlist) {
    }

    /**
     * This is covered by the mod_assign provider.
     *
     * @param  useridlist $useridlist A user ID list object that you can append your user IDs to.
     */
    public static function get_student_user_ids(useridlist $useridlist) {
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
        global $DB;

        $currentpath = $exportdata->get_subcontext();
        $currentpath[] = get_string('privacy:path', 'assignfeedback_onenote');
        $context = $exportdata->get_context();
        $assignmentid = $exportdata->get_assign()->get_instance()->id;
        $gradeid = $exportdata->get_pluginobject()->id;
        $filters = ['assignment' => $assignmentid, 'grade' => $gradeid];
        $records = $DB->get_records('assignfeedback_onenote', $filters);
        foreach ($records as $record) {
            writer::with_context($context)
                ->export_data($currentpath, $record);
            writer::with_context($exportdata->get_context())->export_area_files($currentpath,
                'assignfeedback_onenote', \local_onenote\api\base::ASSIGNFEEDBACK_ONENOTE_FILEAREA, $record->grade);
        }
    }

    /**
     * Any call to this method should delete all user data for the context defined in the deletion_criteria.
     *
     * @param  assign_plugin_request_data $requestdata Data useful for deleting user data from this sub-plugin.
     */
    public static function delete_feedback_for_context(assign_plugin_request_data $requestdata) {
        $assign = $requestdata->get_assign();
        $fs = get_file_storage();
        $fs->delete_area_files($requestdata->get_context()->id, 'assignfeedback_onenote',
            \local_onenote\api\base::ASSIGNFEEDBACK_ONENOTE_FILEAREA);
        $plugin = $assign->get_plugin_by_type('assignfeedback', 'onenote');
        $plugin->delete_instance();
    }

    /**
     * Calling this function should delete all user data associated with this grade entry.
     *
     * @param  assign_plugin_request_data $requestdata Data useful for deleting user data.
     */
    public static function delete_feedback_for_grade(assign_plugin_request_data $requestdata) {
        global $DB;

        $fs = new \file_storage();
        $fs->delete_area_files($requestdata->get_context()->id, 'assignfeedback_onenote',
            \local_onenote\api\base::ASSIGNFEEDBACK_ONENOTE_FILEAREA, $requestdata->get_pluginobject()->id);

        $filters = [
            'assignment' => $requestdata->get_assign()->get_instance()->id,
            'grade' => $requestdata->get_pluginobject()->id,
        ];
        $DB->delete_records('assignfeedback_onenote', $filters);
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

        $fs = new \file_storage();
        $fs->delete_area_files_select(
            $deletedata->get_context()->id,
            'assignfeedback_onenote',
            \local_onenote\api\base::ASSIGNFEEDBACK_ONENOTE_FILEAREA,
            $sql,
            $params
        );

        $params['assignment'] = $deletedata->get_assignid();
        $DB->delete_records_select('assignfeedback_onenote', "assignment = :assignment AND grade $sql", $params);
    }
}
