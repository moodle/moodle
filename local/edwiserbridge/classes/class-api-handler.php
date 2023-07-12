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
 * Edwiser Bridge - WordPress and Moodle integration.
 * This file is responsible for WordPress connection related functionality.
 *
 * @package     local_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Handles API requests and response from WordPress.
 *
 * @package     local_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api_handler {

    /** @var int  Returns instance of the class if already created */
    protected static $instance = null;

    /**
     * Creates insce of the class.
     *
     * @return object self object.
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Create external service with the provided name and the user id
     *
     * @param  string $requesturl   requesturl.
     * @param  int $requestdata requestdata.
     * @return array
     */
    public function connect_to_wp_with_args($requesturl, $requestdata) {
        $requesturl .= '/wp-json/edwiser-bridge/wisdmlabs/';

        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL            => $requesturl,
                CURLOPT_TIMEOUT        => 100
            )
        );

        curl_setopt($curl, CURLOPT_POST, 1);
        global $CFG;
        curl_setopt($curl, CURLOPT_USERAGENT, 'Moodle/' . $CFG->version . ' (' . $CFG->wwwroot . ') Edwiser Bridge Moodle Server');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Skip SSL Verification.

        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestdata);
        $response = curl_exec($curl);
        $statuscode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_error($curl)) {
            $errormsg = curl_error($curl);
            curl_close($curl);
            return array("error" => 1, "msg" => $errormsg);
        } else {
            curl_close($curl);

            if ("200" == $statuscode) {
                return array("error" => 0, "data" => json_decode($response));
            } else {
                $msg = get_string("default_error", "local_edwiserbridge");
                // check if response is html.
                if ($response != strip_tags($response)) {
                   $msg = "Html response received from WordPress. Please make sure the WordPress site is up and running.";
                }
                if (strpos($response, "BitNinja") !== false || strpos($response, "Security check by BitNinja.IO") !== false) {
                    $msg = "Request blocked by BitNinja. Please whitelist the IP address of your Moodle server.";
                }
                if (strpos($response, "Cloudflare Ray ID") !== false) {
                    $msg = "Request blocked by Cloudflare. Please whitelist the IP address of your Moodle server.";
                }
                if (strpos($response, "Mod_Security") !== false) {
                    $msg = "Request blocked by Mod Security. Please whitelist the IP address of your Moodle server.";
                }
                return array("error" => 1, "msg" => $msg);
            }
        }
    }
}
