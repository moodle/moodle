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
 * An activity to interface with WebEx.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_webexactivity;

use \mod_webexactivity\local\exception;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/xmlize.php');

/**
 * Provides the low level connection to the WebEx server.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class service_connector {
    private $success = null;
    private $error = array();
    private $response = null;

    /**
     * Fetch the response for the provided XML.
     *
     * @param string    $xml The XML to send.
     * @return bool     True on success, false on failure.
     * @throws curl_setup_exception on curl setup failure.
     * @throws connection_exception on connection failure.
     */
    public function retrieve($xml) {
        $this->clear_status();
        $handle = $this->create_curl_handle();

        if (!$handle) {
            throw new exception\curl_setup_exception();
        }

        curl_setopt($handle, CURLOPT_POSTFIELDS, $xml);

        $this->response = curl_exec($handle);

        if ($this->response === false) {
            $error = curl_errno($handle) .':'. curl_error($handle);
            throw new exception\connection_exception($error);
        }
        curl_close($handle);

        $this->update_success();

        return $this->success;
    }

    /**
     * Clear all internal status tracking.
     */
    private function clear_status() {
        $this->success = null;
        $this->error = array();
        $this->response = null;
    }

    /**
     * Setup a new curl handle for use.
     *
     * @return object    The configured curl handle.
     */
    private function create_curl_handle() {
        $url = get_config('webexactivity', 'sitename');
        if ($url === false) {
            return false;
        }
        $url = 'https://'.$url.'.webex.com';

        $handle = curl_init($url.'/WBXService/XMLService');
        curl_setopt($handle, CURLOPT_TIMEOUT, 120);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_USERAGENT, 'Moodle');
        curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: text/xml charset=UTF-8"));
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);

        return $handle;
    }

    /**
     * Update the success fields based on the response.
     */
    private function update_success() {
        if ($this->response === null || $this->response === false) {
            $this->success = false;
            return;
        }

        $response = xmlize($this->response);

        if (!isset($response['serv:message']['#']['serv:header'][0]['#']['serv:response'][0]['#'])) {
            $this->success = false;
            return;
        }

        $response = $response['serv:message']['#']['serv:header'][0]['#']['serv:response'][0]['#'];

        if (!isset($response['serv:result'][0]['#'])) {
            $this->success = false;
            return;
        }

        $success = $response['serv:result'][0]['#'];
        if (strcmp($success, 'SUCCESS') === 0) {
            $this->success = true;
        } else {
            $this->success = false;
            if (isset($response['serv:reason'][0]['#'])) {
                $this->error['message'] = $response['serv:reason'][0]['#'];
            }
            if (isset($response['serv:exceptionID'][0]['#'])) {
                $this->error['exception'] = $response['serv:exceptionID'][0]['#'];
            }
        }
    }

    /**
     * Get the errors from the last connection.
     *
     * @return array|bool    The errors array.
     */
    public function get_errors() {
        return $this->error;
    }

    /**
     * Get the success flag from the last connection.
     *
     * @return bool    The last success status (true is success).
     */
    public function get_success() {
        return $this->success;
    }

    /**
     * Get the response from the last connection.
     *
     * @return string|bool    The response in XML form.
     */
    public function get_response() {
        return $this->response;
    }

    /**
     * Get the response from the last connection.
     *
     * @return string|bool    The response in XML form.
     */
    public function get_response_array() {
        return xmlize($this->response);
    }
}
