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
 * Tiles course format, registration manager class.
 *
 * @package format_tiles
 * @copyright 2019 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_tiles;

defined('MOODLE_INTERNAL') || die();

/**
 * Class registration_manager
 * @package format_tiles
 * @copyright 2019 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class registration_manager {

    /**
     * Contact the registration server and seek a key.
     * @copyright 2018 David Watson {@link http://evolutioncode.uk}
     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     * @param [] $serverresponse the registration data.
     * @return bool|mixed
     */
    public static function parse_server_response($serverresponse) {
        try {
            if (isset($serverresponse['status']) && $serverresponse['status']
                && isset($serverresponse['key']) && self::validate_key($serverresponse['key'])) {
                return $serverresponse;
            }
            // In all other cases we failed to register.
            if (isset($serverresponse['errno']) && $serverresponse['errno']) {
                debugging('Connection error' . $serverresponse['errno'], DEBUG_DEVELOPER);
            } else if (!isset($serverresponse['http_code'])
                || ($serverresponse['http_code'] != 200 && $serverresponse['http_code'] != 201)) { // Code 201 is "created".
                debugging(
                    'Unexpected HTTP code ' . $serverresponse['http_code'] . json_encode($serverresponse), DEBUG_DEVELOPER
                );
            } else if (isset($serverresponse['exception'])) {
                debugging('Exception ' . $serverresponse['exception']);
            } else if (!isset($serverresponse['status'])) {
                debugging(
                    'Server JSON response did not contain status field as expected, or status was not true', DEBUG_DEVELOPER
                );
            } else if (!$serverresponse['status']) {
                debugging(
                    'Server JSON response status field was not true: '
                        . $serverresponse['status'] . ' ' . gettype($serverresponse['status']),
                    DEBUG_DEVELOPER
                );
            } else {
                debugging('Unknown curl request error ' . json_encode($serverresponse), DEBUG_DEVELOPER);
            }
            return false;
        } catch (\Exception $ex) {
            debugging('Curl request error ' . $ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }

    /**
     * Is this plugin already registered.
     * @return bool
     */
    public static function is_registered() {
        $dbvalue = get_config('format_tiles', 'registered');
        return is_numeric($dbvalue) && $dbvalue > 1514764800; // Newer than 1/1/2018.
    }

    /**
     * Have we attempted to register in the last hour.
     * @return bool
     * @throws \dml_exception
     */
    public static function has_recent_attempt() {
        $lastattempt = get_config('format_tiles', 'lastregistrationattempt');
        if ($lastattempt && $lastattempt > time() - 60 * 60) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Schedule an attempt to register with the server for later.
     * @param [] $data
     * @throws \coding_exception
     */
    public static function schedule_registration_attempt($data) {
        global $USER;
        if (!self::is_registered()) {
            // Schedule an attempt to register later.
            \core\notification::warning(get_string('registrationdeferred', 'format_tiles'));
            $task = new \format_tiles\task\deferred_register();
            $task->set_custom_data($data);
            $task->set_component('format_tiles');
            $task->set_userid($USER->id);
            \core\task\manager::queue_adhoc_task($task, true);
        }
    }

    /**
     * Contact the registration server using CURL and get response.
     * @param object $data the form data
     * @param int $timeout
     * @return mixed
     * @throws \coding_exception
     */
    public static function make_curl_request($data, $timeout) {
        $curl = new \curl();
        $curl->setopt(
            array(
                'CURLOPT_TIMEOUT' => $timeout,
                'CURLOPT_CONNECTTIMEOUT' => $timeout,
                'CURLOPT_URL' => self::registration_server_url(),
                'CURLOPT_CUSTOMREQUEST' => "POST",
                'CURLOPT_RETURNTRANSFER' => true
            )
        );

        $curloutput = json_decode($curl->post(self::registration_server_url(), json_encode($data)), true);
        $curloutput['http_code'] = $curl->get_info()['http_code'];
        $curloutput['errno'] = $curl->get_errno();
        return $curloutput;
    }

    /**
     * Execute this when we want to make our deferred registration attempt (rescheduled from earlier fail).
     * @param [] $data
     * @return bool
     * @throws \coding_exception
     */
    public static function attempt_deferred_registration($data) {
        if (self::is_registered()) {
            return true;
        }
        $serverresponse = self::make_curl_request($data, 6);
        $result = self::parse_server_response(self::process_data($serverresponse));
        if ($result && $result['status'] && self::validate_key($result['key'])) {
            self::set_registered();
            unset_config('lastregistrationattempt', 'format_tiles');
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the registration server URL.
     * @return string the URL.
     */
    public static function registration_server_url() {
        return "https://api.evolutioncode.uk/registration";
    }

    /**
     * Set the plugin as registered.
     * @return bool if successful.
     * @throws \coding_exception
     */
    public static function set_registered() {
        \core\notification::success(get_string('registeredthanks', 'format_tiles'));
        return set_config('registered', time(), 'format_tiles');
    }

    /**
     * Validate the a key for this plugin.
     * @param string $key the key we want to check.
     * @return bool
     */
    public static function validate_key($key) {
        global $CFG;
        $utcyearmonth = gmdate( 'Yn'); // We use UTC not server's time zone.
        return $key === hash('sha256', $CFG->wwwroot .$utcyearmonth);
    }

    /**
     * Take the data submitted from the form and supplement it / remove submit button.
     * @package format_tiles
     * @param object $data
     * @param bool $forjs
     * @return array
     */
    public static function process_data($data, $forjs = false) {
        $returndata = (array)$data;
        $returndata['ip'] = getremoteaddr();
        unset($returndata['submitbutton']);
        if (!$forjs) {
            $returndata['browserlanguages'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            $returndata['useragent'] = \core_useragent::get_user_agent_string();
        }
        return $returndata;
    }
}
