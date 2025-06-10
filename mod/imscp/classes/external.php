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
 * IMSCP external API
 *
 * @package    mod_imscp
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

use core_course\external\helper_for_get_mods_by_courses;

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * IMSCP external functions
 *
 * @package    mod_imscp
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_imscp_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function view_imscp_parameters() {
        return new external_function_parameters(
            array(
                'imscpid' => new external_value(PARAM_INT, 'imscp instance id')
            )
        );
    }

    /**
     * Simulate the imscp/view.php web interface page: trigger events, completion, etc...
     *
     * @param int $imscpid the imscp instance id
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function view_imscp($imscpid) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/imscp/lib.php");

        $params = self::validate_parameters(self::view_imscp_parameters(),
                                            array(
                                                'imscpid' => $imscpid
                                            ));
        $warnings = array();

        // Request and permission validation.
        $imscp = $DB->get_record('imscp', array('id' => $params['imscpid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($imscp, 'imscp');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/imscp:view', $context);

        // Call the imscp/lib API.
        imscp_view($imscp, $course, $cm, $context);

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
    public static function view_imscp_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for get_imscps_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_imscps_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'course id'), 'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of IMSCP packages in a provided list of courses,
     * if no list is provided all IMSCP packages that the user can view will be returned.
     *
     * @param array $courseids the course ids
     * @return array of IMSCP packages details and possible warnings
     * @since Moodle 3.0
     */
    public static function get_imscps_by_courses($courseids = array()) {
        global $CFG;

        $returnedimscps = array();
        $warnings = array();

        $params = self::validate_parameters(self::get_imscps_by_courses_parameters(), array('courseids' => $courseids));

        $courses = array();
        if (empty($params['courseids'])) {
            $courses = enrol_get_my_courses();
            $params['courseids'] = array_keys($courses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $courses);

            // Get the imscps in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $imscps = get_all_instances_in_courses("imscp", $courses);
            foreach ($imscps as $imscp) {
                $imscpdetails = helper_for_get_mods_by_courses::standard_coursemodule_element_values(
                        $imscp, 'mod_imscp', 'moodle/course:manageactivities', 'mod/imscp:view');

                if (has_capability('moodle/course:manageactivities', context_module::instance($imscp->coursemodule))) {
                    $imscpdetails['revision']      = $imscp->revision;
                    $imscpdetails['keepold']       = $imscp->keepold;
                    $imscpdetails['structure']     = $imscp->structure;
                    $imscpdetails['timemodified']  = $imscp->timemodified;
                }
                $returnedimscps[] = $imscpdetails;
            }
        }
        $result = array();
        $result['imscps'] = $returnedimscps;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_imscps_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.0
     */
    public static function get_imscps_by_courses_returns() {
        return new external_single_structure(
            array(
                'imscps' => new external_multiple_structure(
                    new external_single_structure(array_merge(
                        helper_for_get_mods_by_courses::standard_coursemodule_elements_returns(true),
                        [
                            'revision' => new external_value(PARAM_INT, 'Revision', VALUE_OPTIONAL),
                            'keepold' => new external_value(PARAM_INT, 'Number of old IMSCP to keep', VALUE_OPTIONAL),
                            'structure' => new external_value(PARAM_RAW, 'IMSCP structure', VALUE_OPTIONAL),
                            'timemodified' => new external_value(PARAM_RAW, 'Time of last modification', VALUE_OPTIONAL),
                        ]
                    ), 'IMS content packages')
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

}
