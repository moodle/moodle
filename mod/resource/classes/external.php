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
 * Resource external API
 *
 * @package    mod_resource
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * Resource external functions
 *
 * @package    mod_resource
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_resource_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function view_resource_parameters() {
        return new external_function_parameters(
            array(
                'resourceid' => new external_value(PARAM_INT, 'resource instance id')
            )
        );
    }

    /**
     * Simulate the resource/view.php web interface page: trigger events, completion, etc...
     *
     * @param int $resourceid the resource instance id
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function view_resource($resourceid) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/resource/lib.php");

        $params = self::validate_parameters(self::view_resource_parameters(),
                                            array(
                                                'resourceid' => $resourceid
                                            ));
        $warnings = array();

        // Request and permission validation.
        $resource = $DB->get_record('resource', array('id' => $params['resourceid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($resource, 'resource');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/resource:view', $context);

        // Call the resource/lib API.
        resource_view($resource, $course, $cm, $context);

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
    public static function view_resource_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for get_resources_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_resources_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Course id'), 'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of files in a provided list of courses.
     * If no list is provided all files that the user can view will be returned.
     *
     * @param array $courseids course ids
     * @return array of warnings and files
     * @since Moodle 3.3
     */
    public static function get_resources_by_courses($courseids = array()) {

        $warnings = array();
        $returnedresources = array();

        $params = array(
            'courseids' => $courseids,
        );
        $params = self::validate_parameters(self::get_resources_by_courses_parameters(), $params);

        $mycourses = array();
        if (empty($params['courseids'])) {
            $mycourses = enrol_get_my_courses();
            $params['courseids'] = array_keys($mycourses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $mycourses);

            // Get the resources in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $resources = get_all_instances_in_courses("resource", $courses);
            foreach ($resources as $resource) {
                $context = context_module::instance($resource->coursemodule);
                // Entry to return.
                $resource->name = external_format_string($resource->name, $context->id);
                $options = array('noclean' => true);
                list($resource->intro, $resource->introformat) =
                    external_format_text($resource->intro, $resource->introformat, $context->id, 'mod_resource', 'intro', null,
                        $options);
                $resource->introfiles = external_util::get_area_files($context->id, 'mod_resource', 'intro', false, false);
                $resource->contentfiles = external_util::get_area_files($context->id, 'mod_resource', 'content');

                $returnedresources[] = $resource;
            }
        }

        $result = array(
            'resources' => $returnedresources,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_resources_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_resources_by_courses_returns() {
        return new external_single_structure(
            array(
                'resources' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Module id'),
                            'coursemodule' => new external_value(PARAM_INT, 'Course module id'),
                            'course' => new external_value(PARAM_INT, 'Course id'),
                            'name' => new external_value(PARAM_RAW, 'Page name'),
                            'intro' => new external_value(PARAM_RAW, 'Summary'),
                            'introformat' => new external_format_value('intro', 'Summary format'),
                            'introfiles' => new external_files('Files in the introduction text'),
                            'contentfiles' => new external_files('Files in the content'),
                            'tobemigrated' => new external_value(PARAM_INT, 'Whether this resource was migrated'),
                            'legacyfiles' => new external_value(PARAM_INT, 'Legacy files flag'),
                            'legacyfileslast' => new external_value(PARAM_INT, 'Legacy files last control flag'),
                            'display' => new external_value(PARAM_INT, 'How to display the resource'),
                            'displayoptions' => new external_value(PARAM_RAW, 'Display options (width, height)'),
                            'filterfiles' => new external_value(PARAM_INT, 'If filters should be applied to the resource content'),
                            'revision' => new external_value(PARAM_INT, 'Incremented when after each file changes, to avoid cache'),
                            'timemodified' => new external_value(PARAM_INT, 'Last time the resource was modified'),
                            'section' => new external_value(PARAM_INT, 'Course section id'),
                            'visible' => new external_value(PARAM_INT, 'Module visibility'),
                            'groupmode' => new external_value(PARAM_INT, 'Group mode'),
                            'groupingid' => new external_value(PARAM_INT, 'Grouping id'),
                        )
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }
}
