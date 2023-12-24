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
 * Represent the url for each method and the encoding of the parameters and response.
 *
 * @package    core_badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

namespace core_badges;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filelib.php');

use context_system;
use core_badges\external\assertion_exporter;
use core_badges\external\collection_exporter;
use core_badges\external\issuer_exporter;
use core_badges\external\badgeclass_exporter;
use curl;

/**
 * Represent a single method for the remote api.
 *
 * @package    core_badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backpack_api_mapping {

    /** @var string The action of this method. */
    public $action;

    /** @var string The base url of this backpack. */
    private $url;

    /** @var array List of parameters for this method. */
    public $params;

    /** @var string Name of a class to export parameters for this method. */
    public $requestexporter;

    /** @var string Name of a class to export response for this method. */
    public $responseexporter;

    /** @var boolean This method returns an array of responses. */
    public $multiple;

    /** @var string get or post methods. */
    public $method;

    /** @var boolean json decode the response. */
    public $json;

    /** @var boolean Authentication is required for this request. */
    public $authrequired;

    /** @var boolean Differentiate the function that can be called on a user backpack or a site backpack. */
    private $isuserbackpack;

    /** @var string Error string from authentication request. */
    private static $authenticationerror = '';

    /** @var mixed List of parameters for this method. */
    protected $postparams;

    /** @var int OpenBadges version 1 or 2. */
    protected $backpackapiversion;

    /**
     * Create a mapping.
     *
     * @param string $action The action of this method.
     * @param string $url The base url of this backpack.
     * @param mixed $postparams List of parameters for this method.
     * @param string $requestexporter Name of a class to export parameters for this method.
     * @param string $responseexporter Name of a class to export response for this method.
     * @param boolean $multiple This method returns an array of responses.
     * @param string $method get or post methods.
     * @param boolean $json json decode the response.
     * @param boolean $authrequired Authentication is required for this request.
     * @param boolean $isuserbackpack user backpack or a site backpack.
     * @param integer $backpackapiversion OpenBadges version 1 or 2.
     */
    public function __construct($action, $url, $postparams, $requestexporter, $responseexporter,
                                $multiple, $method, $json, $authrequired, $isuserbackpack, $backpackapiversion) {
        $this->action = $action;
        $this->url = $url;
        $this->postparams = $postparams;
        $this->requestexporter = $requestexporter;
        $this->responseexporter = $responseexporter;
        $this->multiple = $multiple;
        $this->method = $method;
        $this->json = $json;
        $this->authrequired = $authrequired;
        $this->isuserbackpack = $isuserbackpack;
        $this->backpackapiversion = $backpackapiversion;
    }

    /**
     * Get the unique key for the token.
     *
     * @param string $type The type of token.
     * @return string
     */
    private function get_token_key($type) {
        $prefix = 'badges_';
        if ($this->isuserbackpack) {
            $prefix .= 'user_backpack_';
        } else {
            $prefix .= 'site_backpack_';
        }
        $prefix .= $type . '_token';
        return $prefix;
    }

    /**
     * Remember the error message in a static variable.
     *
     * @param string $msg The message.
     */
    public static function set_authentication_error($msg) {
        self::$authenticationerror = $msg;
    }

    /**
     * Get the last authentication error in this request.
     *
     * @return string
     */
    public static function get_authentication_error() {
        return self::$authenticationerror;
    }

    /**
     * Does the action match this mapping?
     *
     * @param string $action The action.
     * @return boolean
     */
    public function is_match($action) {
        return $this->action == $action;
    }

    /**
     * Parse the method url and insert parameters.
     *
     * @param string $apiurl The raw apiurl.
     * @param string $param1 The first parameter.
     * @param string $param2 The second parameter.
     * @return string
     */
    private function get_url($apiurl, $param1, $param2) {
        $urlscheme = parse_url($apiurl, PHP_URL_SCHEME);
        $urlhost = parse_url($apiurl, PHP_URL_HOST);

        $url = $this->url;
        $url = str_replace('[SCHEME]', $urlscheme, $url);
        $url = str_replace('[HOST]', $urlhost, $url);
        $url = str_replace('[URL]', $apiurl, $url);
        $url = str_replace('[PARAM1]', $param1 ?? '', $url);
        $url = str_replace('[PARAM2]', $param2 ?? '', $url);

        return $url;
    }

    /**
     * Parse the post parameters and insert replacements.
     *
     * @param string $email The api username.
     * @param string $password The api password.
     * @param string $param The parameter.
     * @return mixed
     */
    private function get_post_params($email, $password, $param) {
        global $PAGE;

        if ($this->method == 'get') {
            return '';
        }

        $request = $this->postparams;
        if ($request === '[PARAM]') {
            $value = $param;
            foreach ($value as $key => $keyvalue) {
                if (gettype($value[$key]) == 'array') {
                    $newkey = 'related_' . $key;
                    $value[$newkey] = $value[$key];
                    unset($value[$key]);
                }
            }
        } else if (is_array($request)) {
            foreach ($request as $key => $value) {
                if ($value == '[EMAIL]') {
                    $value = $email;
                    $request[$key] = $value;
                } else if ($value == '[PASSWORD]') {
                    $value = $password;
                    $request[$key] = $value;
                } else if ($value == '[PARAM]') {
                    $request[$key] = is_array($param) ? $param[0] : $param;
                }
            }
        }
        $context = context_system::instance();
        $exporter = $this->requestexporter;
        $output = $PAGE->get_renderer('core', 'badges');
        if (!empty($exporter)) {
            $exporterinstance = new $exporter($value, ['context' => $context]);
            $request = $exporterinstance->export($output);
        }
        if ($this->json) {
            return json_encode($request);
        }
        return $request;
    }

    /**
     * Read the response from a V1 user request and save the userID.
     *
     * @param string $response The request response.
     * @param integer $backpackid The backpack id.
     * @return mixed
     */
    private function convert_email_response($response, $backpackid) {
        global $SESSION;

        if (isset($response->status) && $response->status == 'okay') {

            // Remember the tokens.
            $useridkey = $this->get_token_key(BADGE_USER_ID_TOKEN);
            $backpackidkey = $this->get_token_key(BADGE_BACKPACK_ID_TOKEN);

            $SESSION->$useridkey = $response->userId;
            $SESSION->$backpackidkey = $backpackid;
            return $response->userId;
        }
        if (!empty($response->error)) {
            self::set_authentication_error($response->error);
        }
        return false;
    }

    /**
     * Get the user id from a previous user request.
     *
     * @return integer
     */
    private function get_auth_user_id() {
        global $USER;

        if ($this->isuserbackpack) {
            return $USER->id;
        } else {
            // The access tokens for the system backpack are shared.
            return -1;
        }
    }

    /**
     * Parse the response from an openbadges 2 login.
     *
     * @param string $response The request response data.
     * @param integer $backpackid The id of the backpack.
     * @return mixed
     */
    private function oauth_token_response($response, $backpackid) {
        global $SESSION;

        if (isset($response->access_token) && isset($response->refresh_token)) {
            // Remember the tokens.
            $accesskey = $this->get_token_key(BADGE_ACCESS_TOKEN);
            $refreshkey = $this->get_token_key(BADGE_REFRESH_TOKEN);
            $expireskey = $this->get_token_key(BADGE_EXPIRES_TOKEN);
            $useridkey = $this->get_token_key(BADGE_USER_ID_TOKEN);
            $backpackidkey = $this->get_token_key(BADGE_BACKPACK_ID_TOKEN);
            if (isset($response->expires_in)) {
                $timeout = $response->expires_in;
            } else {
                $timeout = 15 * 60; // 15 minute timeout if none set.
            }
            $expires = $timeout + time();

            $SESSION->$expireskey = $expires;
            $SESSION->$useridkey = $this->get_auth_user_id();
            $SESSION->$accesskey = $response->access_token;
            $SESSION->$refreshkey = $response->refresh_token;
            $SESSION->$backpackidkey = $backpackid;
            return -1;
        } else if (isset($response->error_description)) {
            self::set_authentication_error($response->error_description);
        }
        return $response;
    }

    /**
     * Standard options used for all curl requests.
     *
     * @return array
     */
    private function get_curl_options() {
        return array(
            'FRESH_CONNECT'     => true,
            'RETURNTRANSFER'    => true,
            'FOLLOWLOCATION'    => true,
            'FORBID_REUSE'      => true,
            'HEADER'            => 0,
            'CONNECTTIMEOUT'    => 3,
            'CONNECTTIMEOUT'    => 3,
            // Follow redirects with the same type of request when sent 301, or 302 redirects.
            'CURLOPT_POSTREDIR' => 3,
        );
    }

    /**
     * Make an api request and parse the response.
     *
     * @param string $apiurl Raw request url.
     * @param string $urlparam1 Parameter for the request.
     * @param string $urlparam2 Parameter for the request.
     * @param string $email User email for authentication.
     * @param string $password for authentication.
     * @param mixed $postparam Raw data for the post body.
     * @param string $backpackid the id of the backpack to use.
     * @return mixed
     */
    public function request($apiurl, $urlparam1, $urlparam2, $email, $password, $postparam, $backpackid) {
        global $SESSION, $PAGE;

        $curl = new curl();

        $url = $this->get_url($apiurl, $urlparam1, $urlparam2);

        if ($this->authrequired) {
            $accesskey = $this->get_token_key(BADGE_ACCESS_TOKEN);
            if (isset($SESSION->$accesskey)) {
                $token = $SESSION->$accesskey;
                $curl->setHeader('Authorization: Bearer ' . $token);
            }
        }
        if ($this->json) {
            $curl->setHeader(array('Content-type: application/json'));
        }
        $curl->setHeader(array('Accept: application/json', 'Expect:'));
        $options = $this->get_curl_options();

        $post = $this->get_post_params($email, $password, $postparam);

        if ($this->method == 'get') {
            $response = $curl->get($url, $post, $options);
        } else if ($this->method == 'post') {
            $response = $curl->post($url, $post, $options);
        } else if ($this->method == 'put') {
            $response = $curl->put($url, $post, $options);
        }
        $response = json_decode($response);
        if (isset($response->result)) {
            $response = $response->result;
        }
        $context = context_system::instance();
        $exporter = $this->responseexporter;
        if (class_exists($exporter)) {
            $output = $PAGE->get_renderer('core', 'badges');
            if (!$this->multiple) {
                if (count($response)) {
                    $response = $response[0];
                }
                if (empty($response)) {
                    return null;
                }
                $apidata = $exporter::map_external_data($response, $this->backpackapiversion);
                $exporterinstance = new $exporter($apidata, ['context' => $context]);
                $data = $exporterinstance->export($output);
                return $data;
            } else {
                $multiple = [];
                if (empty($response)) {
                    return $multiple;
                }
                foreach ($response as $data) {
                    $apidata = $exporter::map_external_data($data, $this->backpackapiversion);
                    $exporterinstance = new $exporter($apidata, ['context' => $context]);
                    $multiple[] = $exporterinstance->export($output);
                }
                return $multiple;
            }
        } else if (method_exists($this, $exporter)) {
            return $this->$exporter($response, $backpackid);
        }
        return $response;
    }
}
