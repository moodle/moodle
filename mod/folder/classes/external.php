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
 * folder external API
 *
 * @package    mod_folder
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

use core_course\external\helper_for_get_mods_by_courses;

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * folder external functions
 *
 * @package    mod_folder
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_folder_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function view_folder_parameters() {
        return new external_function_parameters(
            array(
                'folderid' => new external_value(PARAM_INT, 'folder instance id')
            )
        );
    }

    /**
     * Simulate the folder/view.php web interface page: trigger events, completion, etc...
     *
     * @param int $folderid the folder instance id
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function view_folder($folderid) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/folder/lib.php");

        $params = self::validate_parameters(self::view_folder_parameters(),
                                            array(
                                                'folderid' => $folderid
                                            ));
        $warnings = array();

        // Request and permission validation.
        $folder = $DB->get_record('folder', array('id' => $params['folderid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($folder, 'folder');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/folder:view', $context);

        // Call the page/lib API.
        folder_view($folder, $course, $cm, $context);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function view_folder_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for get_folders_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_folders_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Course id'), 'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of folders in a provided list of courses.
     * If no list is provided all folders that the user can view will be returned.
     *
     * @param array $courseids course ids
     * @return array of warnings and folders
     * @since Moodle 3.3
     */
    public static function get_folders_by_courses($courseids = array()) {

        $warnings = array();
        $returnedfolders = array();

        $params = array(
            'courseids' => $courseids,
        );
        $params = self::validate_parameters(self::get_folders_by_courses_parameters(), $params);

        $mycourses = array();
        if (empty($params['courseids'])) {
            $mycourses = enrol_get_my_courses();
            $params['courseids'] = array_keys($mycourses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $mycourses);

            // Get the folders in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $folders = get_all_instances_in_courses("folder", $courses);
            foreach ($folders as $folder) {
                helper_for_get_mods_by_courses::format_name_and_intro($folder, 'mod_folder');
                $returnedfolders[] = $folder;
            }
        }

        $result = array(
            'folders' => $returnedfolders,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_folders_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_folders_by_courses_returns() {
        return new external_single_structure(
            array(
                'folders' => new external_multiple_structure(
                    new external_single_structure(array_merge(
                        helper_for_get_mods_by_courses::standard_coursemodule_elements_returns(),
                        [
                            'revision' => new external_value(PARAM_INT, 'Incremented when after each file changes, to avoid cache'),
                            'timemodified' => new external_value(PARAM_INT, 'Last time the folder was modified'),
                            'display' => new external_value(PARAM_INT, 'Display type of folder contents on a separate page or inline'),
                            'showexpanded' => new external_value(PARAM_INT, '1 = expanded, 0 = collapsed for sub-folders'),
                            'showdownloadfolder' => new external_value(PARAM_INT, 'Whether to show the download folder button'),
                            'forcedownload' => new external_value(PARAM_INT, 'Whether file download is forced'),
                        ]
                    ))
                ),
                'warnings' => new external_warnings(),
            )
        );
    }
}
