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
 * @package    assignfeedback_editpdf
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_editpdf\privacy;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/assign/locallib.php');

use \core_privacy\local\metadata\collection;
use \core_privacy\local\metadata\provider as metadataprovider;
use \mod_assign\privacy\assignfeedback_provider;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\contextlist;
use \mod_assign\privacy\assign_plugin_request_data;
use \mod_assign\privacy\useridlist;

/**
 * Privacy class for requesting user data.
 *
 * @package    assignfeedback_editpdf
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements metadataprovider, assignfeedback_provider {

    /**
     * Return meta data about this plugin.
     *
     * @param  collection $collection A list of information to add to.
     * @return collection Return the collection after adding to it.
     */
    public static function get_metadata(collection $collection) {
        $quickdata = [
            'userid' => 'privacy:metadata:userid',
            'rawtext' => 'privacy:metadata:rawtextpurpose',
            'colour' => 'privacy:metadata:colourpurpose'
        ];
        $collection->add_database_table('assignfeedback_editpdf_quick', $quickdata, 'privacy:metadata:tablepurpose');
        $collection->add_subsystem_link('core_files', [], 'privacy:metadata:filepurpose');
        $collection->add_subsystem_link('core_fileconverted', [], 'privacy:metadata:conversionpurpose');
        return $collection;
    }

    /**
     * No need to fill in this method as all information can be acquired from the assign_grades table in the mod assign
     * provider.
     *
     * @param  int $userid The user ID.
     * @param  contextlist $contextlist The context list.
     */
    public static function get_context_for_userid_within_feedback($userid, contextlist $contextlist) {
        // This uses the assign_grade table.
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
     * Export all user data for this plugin.
     *
     * @param  assign_plugin_request_data $exportdata Data used to determine which context and user to export and other useful
     * information to help with exporting.
     */
    public static function export_feedback_user_data(assign_plugin_request_data $exportdata) {
        $currentpath = $exportdata->get_subcontext();
        $currentpath[] = get_string('privacy:path', 'assignfeedback_editpdf');
        $assign = $exportdata->get_assign();
        $plugin = $assign->get_plugin_by_type('assignfeedback', 'editpdf');
        $fileareas = $plugin->get_file_areas();
        $grade = $exportdata->get_pluginobject();
        foreach ($fileareas as $filearea => $notused) {
            writer::with_context($exportdata->get_context())
                    ->export_area_files($currentpath, 'assignfeedback_editpdf', $filearea, $grade->id);
        }
    }

    /**
     * Any call to this method should delete all user data for the context defined in the deletion_criteria.
     *
     * @param  assign_plugin_request_data $requestdata Data useful for deleting user data from this sub-plugin.
     */
    public static function delete_feedback_for_context(assign_plugin_request_data $requestdata) {

        $assign = $requestdata->get_assign();
        $plugin = $assign->get_plugin_by_type('assignfeedback', 'editpdf');
        $fileareas = $plugin->get_file_areas();
        $fs = get_file_storage();
        foreach ($fileareas as $filearea => $notused) {
            // Delete pdf files.
            $fs->delete_area_files($requestdata->get_context()->id, 'assignfeedback_editpdf', $filearea);
        }
        // Delete entries from the tables.
        $plugin->delete_instance();
    }

    /**
     * Calling this function should delete all user data associated with this grade.
     *
     * @param  assign_plugin_request_data $requestdata Data useful for deleting user data.
     */
    public static function delete_feedback_for_grade(assign_plugin_request_data $requestdata) {
        global $DB;

        $assign = $requestdata->get_assign();
        $plugin = $assign->get_plugin_by_type('assignfeedback', 'editpdf');
        $fileareas = $plugin->get_file_areas();
        $fs = get_file_storage();
        foreach ($fileareas as $filearea => $notused) {
            // Delete pdf files.
            $fs->delete_area_files($requestdata->get_context()->id, 'assignfeedback_editpdf',
                    $filearea, $requestdata->get_pluginobject()->id);
        }

        // Remove table entries.
        $DB->delete_records('assignfeedback_editpdf_annot', ['gradeid' => $requestdata->get_pluginobject()->id]);
        $DB->delete_records('assignfeedback_editpdf_cmnt', ['gradeid' => $requestdata->get_pluginobject()->id]);
        // Submission records in assignfeedback_editpdf_queue will be cleaned up in a scheduled task
    }
}
