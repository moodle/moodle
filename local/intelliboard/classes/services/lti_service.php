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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\services;

use local_intelliboard\attendance\OAuthConsumer;
use local_intelliboard\attendance\OAuthRequest;
use local_intelliboard\attendance\OAuthSignatureMethod_HMAC_SHA1;

class lti_service {
    /** @var mixed LTI endpoint */
    private $endpoint;

    /** @var mixed LTI consumer key */
    private $key;

    /** @var mixed LTI shared secret */
    private $secret;

    /**
     * Set endpoint for LTI
     *
     * @param string $endpoint
     */
    public function set_endpoint($endpoint) {
        $this->endpoint = $endpoint;
    }

    /**
     * lti_service constructor.
     * @throws \dml_exception
     */
    public function __construct()
    {
        $this->endpoint = get_config(
            'local_intelliboard', 'attendancetoolurl'
        );
        $this->key = get_config(
            'local_intelliboard', 'attendanceconsumerkey'
        );
        $this->secret = get_config(
            'local_intelliboard', 'attendancesharedsecret'
        );
    }

    /**
     * Get signed parameters for LTI request
     *
     * @param array $customparameters [param_key => param_value]
     * @return array
     */
    private function lti_request_params($customparameters) {
        global $USER;

        $requestparams = [
            'user_id' => $USER->id,
            'lti_message_type' => 'basic-lti-launch-request',
            'lti_version' => 'LTI-1p0',
            'resource_link_id' => 0,
        ];

        $requestparams = array_merge($requestparams, $customparameters);

        return $this->lti_sign_parameters($requestparams);
    }

    /**
     * @param $oldparms
     * @return array|null
     */
    public function lti_sign_parameters($oldparms) {
        $parms = $oldparms;
        $hmacmethod = new OAuthSignatureMethod_HMAC_SHA1();
        $testconsumer = new OAuthConsumer($this->key, $this->secret, null);
        $accreq = OAuthRequest::from_consumer_and_token(
            $testconsumer, '', "POST", $this->endpoint, $parms
        );
        $accreq->sign_request($hmacmethod, $testconsumer, '');
        $newparms = $accreq->get_parameters();

        return $newparms;
    }

    /**
     * Return the launch data required for opening the attendance tool.
     *
     * @param $customparams
     * @return array the endpoint URL and parameters (including the signature)
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function lti_get_launch_data($customparams) {
        if(!empty($this->key) && !empty($this->secret) && !empty($this->endpoint)) {
            $parms = $this->lti_request_params($customparams);

            $endpointurl = new \moodle_url(
                get_config('local_intelliboard', 'attendancetoolurl')
            );
            $endpointparams = $endpointurl->params();

            // Strip querystring params in endpoint url from $parms to avoid duplication.
            if (!empty($endpointparams) && !empty($parms)) {
                foreach (array_keys($endpointparams) as $paramname) {
                    if (isset($parms[$paramname])) {
                        unset($parms[$paramname]);
                    }
                }
            }

        } else {
            echo 'Invalid LTI credentials';exit;
        }

        return array($this->endpoint, $parms);
    }
}