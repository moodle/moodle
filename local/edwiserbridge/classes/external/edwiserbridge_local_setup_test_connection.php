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
trait edwiserbridge_local_setup_test_connection {

    /**
     * Request to test connection
     *
     * @param  string $wpurl   wpurl.
     * @param  string $wptoken wptoken.
     *
     * @return array
     */
    public static function edwiserbridge_local_setup_test_connection($wpurl) {

        $params = self::validate_parameters(
            self::edwiserbridge_local_setup_test_connection_parameters(),
            array(
                'wp_url' => $wpurl,
                // "wp_token" => $wptoken
            )
        );

        $status = 0;
        $msg    = get_string('setup_test_conn_error', 'local_edwiserbridge');


        // $requestdata = array(
        //     'action'     => "test_connection",
        //     'secret_key' => $params["wp_token"]
        // );

        // $apihandler = api_handler_instance();
        // $response   = $apihandler->connect_to_wp_with_args($params["wp_url"] . '/wp-json', array());

        $requesturl = $params["wp_url"] . '/wp-json';

        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL            => $requesturl,
                CURLOPT_TIMEOUT        => 100
            )
        );

        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Skip SSL Verification.

        $response = curl_exec($curl);


        json_decode($response);

        if(json_last_error() == JSON_ERROR_NONE){
            $status = 1;
            $msg    = get_string('setup_test_conn_succ', 'local_edwiserbridge');
        }


        return array("status" => $status, "msg" => $msg);
    }

    /**
     * Request to test connection parameter.
     */
    public static function edwiserbridge_local_setup_test_connection_parameters() {
        return new external_function_parameters(
            array(
                'wp_url'   => new external_value(PARAM_RAW, get_string('web_service_wp_url', 'local_edwiserbridge')),
                // 'wp_token' => new external_value(PARAM_TEXT, get_string('web_service_wp_token', 'local_edwiserbridge'))
            )
        );
    }

    /**
     * paramters which will be returned from test connection function.
     */
    public static function edwiserbridge_local_setup_test_connection_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_TEXT, get_string('web_service_test_conn_status', 'local_edwiserbridge')),
                'msg'    => new external_value(PARAM_RAW, get_string('web_service_test_conn_msg', 'local_edwiserbridge'))
            )
        );
    }
}
