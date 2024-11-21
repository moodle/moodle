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

namespace local_intelliboard\bb_collaborate;

use Exception;
use local_intelliboard\event\local_intelliboard_bb_collaborate_api_request_finished;
use local_intelliboard\services\bb_collaborate_service;

class bb_collaborate_adapter {
    /**
     * API URL of BB collaborate
     *
     * @var string
     */
    private $url;

    /**
     * Consumer key
     *
     * @var string
     */
    private $consumer_key;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $jwt_token;

    /**
     * @var string
     */
    private $access_token;

    /**
     * HTTP client
     *
     * @var \curl
     */
    private $httpclient;

    /**
     * BB collaborate service
     *
     * @var bb_collaborate_service
     */
    private $service;

    /**
     * BB collaborate repository
     *
     * @var bb_collaborate_repository
     */
    private $repository;


    /**
     * bb_collaborate_adapter constructor.
     * @param bb_collaborate_service $service
     * @param bb_collaborate_repository $repository
     * @throws \dml_exception
     */
    public function __construct(
        bb_collaborate_service $service, bb_collaborate_repository $repository
    ) {
        global $CFG;

        require_once($CFG->dirroot . '/lib/filelib.php');

        $this->url = trim(
            get_config('local_intelliboard', 'bb_col_api_endpoint'), '/'
        );
        $this->consumer_key = trim(
            get_config('local_intelliboard', 'bb_col_consumer_key')
        );
        $this->secret = trim(
            get_config('local_intelliboard', 'bb_col_secret')
        );
        $this->httpclient = new \curl([
            'debug' => get_config('local_intelliboard', 'bb_col_debug')
        ]);
        $this->jwt_token = $service->generate_jwt_token();
        $this->service = $service;
        $this->repository = $repository;

        try {
            $this->access_token = $this->get_access_token();
        } catch (Exception $e) {
            $this->access_token = '';
        }

    }

    /**
     * Get instances of session
     *
     * @param $sessionuid
     * @return array
     * @throws Exception
     */
    public function get_session_instances($sessionuid) {
        if (!$sessionuid) {
            return [];
        }

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->access_token
        ];
        $url = sprintf('%s/sessions/%s/instances', $this->url, $sessionuid);

        $response = json_decode(
            $this->make_request('get', $url, $headers), true
        );

        if(isset($response['results'])) {
            return $response['results'];
        }

        throw new Exception('Request error. ' . json_encode($response));
    }

    /**
     * Get instances of session
     *
     * @param string $sessionuid
     * @param string $instanceid
     * @return array
     * @throws \Exception
     */
    public function get_session_attendees($sessionuid, $instanceid) {
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->access_token
        ];
        $url = sprintf(
            '%s/sessions/%s/instances/%s/attendees',
            $this->url,
            $sessionuid,
            $instanceid
        );

        $response = json_decode(
            $this->make_request('get', $url, $headers), true
        );

        if(isset($response['results'])) {
            return $response['results'];
        }

        return [];
    }

    /**
     * Get recordings of session
     *
     * @param string $sessionuid
     * @param array $options
     * @return array
     * @throws Exception
     */
    public function get_session_recordings($sessionuid, $options = []) {
        if (!$sessionuid) {
            return [];
        }

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->access_token
        ];
        $url = sprintf(
            '%s/recordings?sessionId=%s',
            $this->url,
            $sessionuid
        );

        $response = json_decode(
            $this->make_request('get', $url, $headers, [], $options), true
        );

        if(isset($response['results'])) {
            return $response['results'];
        }

        return [];
    }

    /**
     * Get URL of recording
     *
     * @param string $recordingid
     * @param array $options
     * @return string
     * @throws Exception
     */
    public function get_recording_url($recordingid, $options = []) {
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->access_token
        ];
        $url = sprintf(
            '%s/recordings/%s/url',
            $this->url,
            $recordingid
        );

        $response = json_decode(
            $this->make_request('get', $url, $headers, [], $options), true
        );

        if(isset($response['url'])) {
            return $response['url'];
        }

        return '';
    }

    /**
     * Get access token
     *
     * @return string
     * @throws Exception
     * @throws \coding_exception
     */
    protected function get_access_token() {
        $token = $this->repository->cached_access_token();

        if(!$token) {
            $token = $this->ask_for_token();
            $this->service->remember_access_token($token);
        }

        return $token;
    }

    /**
     * Get connection status
     *
     * @return bool
     * @throws Exception
     */
    public function checkConnection() {
        if(get_config('local_intelliboard', 'bb_col_api_endpoint') &&
            get_config('local_intelliboard', 'bb_col_consumer_key') &&
            get_config('local_intelliboard', 'bb_col_secret')){

            try {
                $token = $this->get_access_token();
            } catch (Exception $e) {
                $token = '';
            }
            return $token != '';
        }else{
            return false;
        }
    }

    /**
     * Get access token from BB collaborate server
     *
     * @return string Access token
     * @throws Exception
     */
    private function ask_for_token() {
        $headers = ['Content-Type: application/x-www-form-urlencoded'];
        $url = $this->get_access_token_url();
        $params = [
            'grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion=' . $this->jwt_token
        ];
        $response = json_decode(
            $this->make_request('post', $url, $headers, $params), true
        );

        if(isset($response['access_token'])) {
            return $response['access_token'];
        }

        throw new Exception('Request error. ' . json_encode($response));
    }

    /**
     * Get access token url
     *
     * @return string Access token
     * @throws Exception
     */
    private function get_access_token_url() {
        return $this->url . '/token';
    }

    /**
     * Make request to BB collaborate server
     *
     * @param string $method HTTP method (post, get)
     * @param string $url Request url
     * @param array $headers
     * @param array $params
     * @param array $options
     * @return string Response
     * @throws Exception
     */
    private function make_request($method, $url, $headers = [], $params = [], $options = []) {
        global $USER;

        $this->httpclient->resetHeader();
        $this->httpclient->setHeader($headers);

        if(strtolower($method) === 'post') {
            $response = $this->httpclient->post($url, implode('&', $params), $options);
        } elseif(strtolower($method) === 'get') {
            $response = $this->httpclient->get($url, $params, $options);
        } else {
            throw new Exception('Method not allowed');
        }

        $tokenurl = $this->get_access_token_url();
        if($this->httpclient->get_info()['http_code'] === 401 && $url != $tokenurl) {
            $token = $this->ask_for_token();
            $this->access_token = $token;
            $this->service->remember_access_token($token);
            $this->update_auth_header($headers);

            $response = $this->make_request($method, $url, $headers, $params);
        }

        if (get_config('local_intelliboard', 'bb_col_debug')) {
            $event = local_intelliboard_bb_collaborate_api_request_finished::create([
                'objectid' => null,
                'userid' => $USER->id,
                'relateduserid' => $USER->id,
                'context' => \context_system::instance(),
                'other' => [
                    'response' => $response,
                    'request' => [
                        'method' => $method,
                        'url' => $url,
                        'headers' => $headers,
                        'params' => $params,
                        'options' => $options
                    ]
                ]
            ]);
            $event->trigger();
        }

        return $response;
    }

    /**
     * Update auth header
     *
     * @param $headers
     */
    private function update_auth_header(&$headers) {
        foreach($headers as &$header) {
            $tmp = explode(':', $header);

            if($tmp[0] === 'Authorization') {
                $header = 'Authorization: Bearer ' . $this->access_token;
            }
        }
    }
}
