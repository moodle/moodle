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
 * Provides local_edwiserbridge\external\course_progress_data trait.
 *
 * @package     local_edwiserbridge
 * @category    external
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */

namespace local_edwiserbridge\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use core_completion\progress;

// require_once($CFG->libdir.'/externallib.php');

/**
 * Trait implementing the external function local_edwiserbridge_course_progress_data
 */
trait edwiserbridge_local_get_mandatory_settings {

    /**
     * Request to test connection
     *
     * @param  string $wpurl   wpurl.
     * @param  string $wptoken wptoken.
     *
     * @return array
     */
    public static function edwiserbridge_local_get_mandatory_settings() {
        global $CFG, $DB;

        $settings_array = array();
        // get all settings and form array.
        $protocols = $CFG->webserviceprotocols;

        // Get rest_protocol settings.
        if ( in_array( 'rest', explode(',', $protocols) ) ) {
            $settings_array['rest_protocol'] = 1;
        }
        else{
            $settings_array['rest_protocol'] = 0;
        }
        
        // Get web_service settings.
        $settings_array['web_service'] = $CFG->enablewebservices;

        // Get password policy settings.
        $settings_array['password_policy'] = $CFG->passwordpolicy;
    
        // Get allow_extended_char settings.
        $settings_array['allow_extended_char'] = $CFG->extendedusernamechars;

        $studentroleid = $DB->get_record('role', array('shortname' => 'student'))->id;

        $settings_array['student_role_id'] = $studentroleid;

        // Get lang_code settings.
        $settings_array['lang_code'] = $CFG->lang;

        return $settings_array;

    }

    /**
     * Request to test connection parameter.
     */
    public static function edwiserbridge_local_get_mandatory_settings_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * paramters which will be returned from test connection function.
     */
    public static function edwiserbridge_local_get_mandatory_settings_returns() {
        return new external_single_structure(
            array(
                'rest_protocol'             => new external_value(PARAM_TEXT, get_string('web_service_rest_protocol', 'local_edwiserbridge')),
                'web_service'               => new external_value(PARAM_RAW, get_string('web_service_web_service', 'local_edwiserbridge')),
                'allow_extended_char'       => new external_value(PARAM_RAW, get_string('web_service_extended_char', 'local_edwiserbridge')),
                'password_policy'           => new external_value(PARAM_RAW, get_string('web_service_password_policy', 'local_edwiserbridge')),
                'lang_code'                 => new external_value(PARAM_RAW, get_string('web_service_lang_code', 'local_edwiserbridge')),
                'student_role_id'           => new external_value(PARAM_RAW, get_string('web_service_student_role_id', 'local_edwiserbridge')),
            )
        );
    }
}
