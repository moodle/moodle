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
 * URL external API
 *
 * @package    mod_url
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

use core_course\external\helper_for_get_mods_by_courses;

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * URL external functions
 *
 * @package    mod_url
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_url_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function view_url_parameters() {
        return new external_function_parameters(
            array(
                'urlid' => new external_value(PARAM_INT, 'url instance id')
            )
        );
    }

    /**
     * Trigger the course module viewed event and update the module completion status.
     *
     * @param int $urlid the url instance id
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function view_url($urlid) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/url/lib.php");

        $params = self::validate_parameters(self::view_url_parameters(),
                                            array(
                                                'urlid' => $urlid
                                            ));
        $warnings = array();

        // Request and permission validation.
        $url = $DB->get_record('url', array('id' => $params['urlid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($url, 'url');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/url:view', $context);

        // Call the url/lib API.
        url_view($url, $course, $cm, $context);

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
    public static function view_url_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for get_urls_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_urls_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Course id'), 'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of urls in a provided list of courses.
     * If no list is provided all urls that the user can view will be returned.
     *
     * @param array $courseids course ids
     * @return array of warnings and urls
     * @since Moodle 3.3
     */
    public static function get_urls_by_courses($courseids = array()) {

        $warnings = array();
        $returnedurls = array();

        $params = array(
            'courseids' => $courseids,
        );
        $params = self::validate_parameters(self::get_urls_by_courses_parameters(), $params);

        $mycourses = array();
        if (empty($params['courseids'])) {
            $mycourses = enrol_get_my_courses();
            $params['courseids'] = array_keys($mycourses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $mycourses);

            // Get the urls in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $urls = get_all_instances_in_courses("url", $courses);
            foreach ($urls as $url) {
                helper_for_get_mods_by_courses::format_name_and_intro($url, 'mod_url');
                $returnedurls[] = $url;
            }
        }

        $result = array(
            'urls' => $returnedurls,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_urls_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_urls_by_courses_returns() {
        return new external_single_structure(
            array(
                'urls' => new external_multiple_structure(
                    new external_single_structure(array_merge(
                        helper_for_get_mods_by_courses::standard_coursemodule_elements_returns(),
                        [
                            'externalurl' => new external_value(PARAM_RAW_TRIMMED, 'External URL'),
                            'display' => new external_value(PARAM_INT, 'How to display the url'),
                            'displayoptions' => new external_value(PARAM_RAW, 'Display options (width, height)'),
                            'parameters' => new external_value(PARAM_RAW, 'Parameters to append to the URL'),
                            'timemodified' => new external_value(PARAM_INT, 'Last time the url was modified'),
                        ]
                    ))
                ),
                'warnings' => new external_warnings(),
            )
        );
    }
}
