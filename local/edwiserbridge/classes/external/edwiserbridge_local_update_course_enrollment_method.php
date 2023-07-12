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
 * Provides edwiserbridge_local\external\course_progress_data trait.
 *
 * @package     edwiserbridge_local
 * @category    external
 * @copyright   2018 Wisdmlabs
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserbridge\external;
defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use core_completion\progress;

require_once($CFG->dirroot.'/local/edwiserbridge/classes/class-settings-handler.php');

/**
 * Trait implementing the external function edwiserbridge_local_course_progress_data
 */
trait edwiserbridge_local_update_course_enrollment_method {

    /**
     * Get list of active course enrolment methods for current user.
     *
     * @param int $courseid
     * @return array of course enrolment methods
     * @throws moodle_exception
     */
    public static function edwiserbridge_local_update_course_enrollment_method( $courseid ) {
        global $DB,$CFG;

        $params = self::validate_parameters(
            self::edwiserbridge_local_update_course_enrollment_method_parameters(),
            array(
                'courseid'   => $courseid,
            )
        );

        // include manual enrollment file .
        require_once($CFG->dirroot.'/enrol/manual/locallib.php');

        $response = array();
        foreach ($params['courseid'] as $singlecourseid) {
            // Add enrolment instance.
            $enrolinstance = new \enrol_manual_plugin();
            // $course = $DB->get_record('course', ['id' => $cm->course]);
            $course = $DB->get_record('course', ['id' => $singlecourseid]);
            $status = $enrolinstance->add_instance($course);

            $instance = enrol_get_instances($course->id, false);
            //get manual enrolment instance id.
            //other plugin instances are also available
            foreach ($instance as $instances) { //CHANGE YET TO COMMIT
                if($instances->enrol == 'manual'){
                    $instanceid = $instances->id;
                }
            }
            $enrolinstance->update_status($instance[$instanceid], ENROL_INSTANCE_ENABLED);
            
            $response[] = array(
                'courseid' => $singlecourseid,
                'status' => 1
            ); 
        }

        return $response;
    }



    /**
     * Returns description of edwiserbridge_local_get_course_enrollment_method() parameters
     *
     * @return external_function_parameters
     */
    public static function edwiserbridge_local_update_course_enrollment_method_parameters() {
        return new external_function_parameters(
            array(
                'courseid'   => new external_multiple_structure( 
                    new external_value(
                        PARAM_TEXT,
                        get_string('web_service_wp_url', 'local_edwiserbridge')
                    ),
                    'List of course id.'
                )
            )
        );
    }


    /**
     * Returns description of edwiserbridge_local_get_course_enrollment_method() result value
     *
     * @return external_description
     */
    public static function edwiserbridge_local_update_course_enrollment_method_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'courseid' => new external_value(PARAM_INT, 'id of course'),
                    'status' => new external_value(PARAM_INT, 'Returns 1 if manual enrolment is enabled and 0 if disabled.'),
                )
            )
        );
    }
}
