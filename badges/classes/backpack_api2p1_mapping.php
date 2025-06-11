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
 * The code is based on badges/classes/backpack_api_mapping.php by Yuliya Bozhko <yuliya.bozhko@totaralms.com>.
 *
 * @copyright  2020 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */

namespace core_badges;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filelib.php');

use context_system;
use curl;

/**
 * Represent a single method for the remote api and this class using for Open Badge API v2.1 methods.
 *
 * @package   core_badges
 * @copyright  2020 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backpack_api2p1_mapping {

    /** @var string The action of this method. */
    public $action;

    /** @var string The base url of this backpack. */
    private $url;

    /** @var array List of parameters for this method. */
    public $params;

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
     * @param boolean $multiple This method returns an array of responses.
     * @param string $method get or post methods.
     * @param boolean $json json decode the response.
     * @param boolean $authrequired Authentication is required for this request.
     * @param boolean $isuserbackpack user backpack or a site backpack.
     * @param integer $backpackapiversion OpenBadges version 1 or 2.
     */
    public function __construct($action, $url, $postparams,
                                $multiple, $method, $json, $authrequired, $isuserbackpack, $backpackapiversion) {
        $this->action = $action;
        $this->url = $url;
        $this->postparams = $postparams;
        $this->multiple = $multiple;
        $this->method = $method;
        $this->json = $json;
        $this->authrequired = $authrequired;
        $this->isuserbackpack = $isuserbackpack;
        $this->backpackapiversion = $backpackapiversion;
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
     * @return string
     */
    private function get_url($apiurl) {
        $urlscheme = parse_url($apiurl, PHP_URL_SCHEME);
        $urlhost = parse_url($apiurl, PHP_URL_HOST);

        $url = $this->url;
        $url = str_replace('[SCHEME]', $urlscheme, $url);
        $url = str_replace('[HOST]', $urlhost, $url);
        $url = str_replace('[URL]', $apiurl, $url);

        return $url;
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
     * @param string $tokenkey to verify authorization.
     * @param array $post request method.
     * @return bool|mixed
     */
    public function request($apiurl, $tokenkey, $post = []) {
        $curl = new curl();
        $url = $this->get_url($apiurl);
        if ($tokenkey) {
            $curl->setHeader('Authorization: Bearer ' . $tokenkey);
        }

        if ($this->json) {
            $curl->setHeader(array('Content-type: application/json'));
            if ($this->method == 'post') {
                $post = json_encode($post);
            }
        }

        $curl->setHeader(array('Accept: application/json', 'Expect:'));
        $options = $this->get_curl_options();
        if ($this->method == 'get') {
            $response = $curl->get($url, $post, $options);
        } else if ($this->method == 'post') {
            $response = $curl->post($url, $post, $options);
        }
        $response = json_decode($response);
        if (isset($response->result)) {
            $response = $response->result;
        }

        return $response;
    }
}