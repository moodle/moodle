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
trait eb_create_service {

    /**
     * functionality to create new external service
     * @param  string $webservicename
     * @param  int $userid
     * @return boolean
     */
    public static function eb_create_service($webservicename, $userid) {
        $settingshandler = new \eb_settings_handler();
        $response = $settingshandler->eb_create_externle_service($webservicename, $userid);
        return $response;
    }

    /**
     * Paramters defined for create service function.
     */
    public static function eb_create_service_parameters() {
        return new external_function_parameters(
            array(
                'web_service_name' => new external_value(PARAM_TEXT, get_string('web_service_name', 'local_edwiserbridge')),
                'user_id'          => new external_value(PARAM_TEXT, get_string('web_service_auth_user', 'local_edwiserbridge'))
            )
        );
    }

    /**
     * paramters which will be returned from create service function.
     */
    public static function eb_create_service_returns() {
        return new external_single_structure(
            array(
                'token'     => new external_value(PARAM_TEXT, get_string('web_service_token', 'local_edwiserbridge')),
                'site_url'  => new external_value(PARAM_TEXT, get_string('moodle_url', 'local_edwiserbridge')),
                'service_id'  => new external_value(PARAM_INT, get_string('web_service_id', 'local_edwiserbridge')),
                'status'  => new external_value(PARAM_INT, get_string('web_service_creation_status', 'local_edwiserbridge')),
                'msg'  => new external_value(PARAM_TEXT, get_string('web_service_creation_msg', 'local_edwiserbridge'))
            )
        );
    }
}
