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

require_once($CFG->dirroot . '/local/edwiserbridge/classes/class-settings-handler.php');

/**
 * Trait implementing the external function local_edwiserbridge_course_progress_data
 */
trait eb_link_service {


    /**
     * Functionality to link existing services.
     *
     * @param  string $serviceid
     * @param  int $token
     * @return array
     */
    public static function eb_link_service($serviceid, $token) {
        $response           = array();
        $response['status'] = 0;
        $response['msg']    = get_string('eb_link_err', 'local_edwiserbridge');

        $settingshandler = new \eb_settings_handler();
        $result           = $settingshandler->eb_link_exitsing_service($serviceid, $token);
        if ($result) {
            $response['status'] = 1;
            $response['msg'] = get_string('eb_link_success', 'local_edwiserbridge');
            return $response;
        }
        return $response;
    }

    /**
     * paramters defined for link service function.
     */
    public static function eb_link_service_parameters() {
        return new external_function_parameters(
            array(
                'service_id' => new external_value(PARAM_TEXT, get_string('web_service_id', 'local_edwiserbridge')),
                'token'      => new external_value(PARAM_TEXT, get_string('web_service_token', 'local_edwiserbridge'))
            )
        );
    }

    /**
     * paramters which will be returned from link service function.
     */
    public static function eb_link_service_returns() {
        return new external_single_structure(
            array(
                'status'  => new external_value(PARAM_INT, get_string('web_service_creation_status', 'local_edwiserbridge')),
                'msg'  => new external_value(PARAM_TEXT, get_string('web_service_creation_msg', 'local_edwiserbridge'))
            )
        );
    }
}
