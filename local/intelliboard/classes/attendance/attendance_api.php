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

namespace local_intelliboard\attendance;

class attendance_api {
    const TOKEN_LIFE_TIME = 300;

    /** @var string API Key */
    private $api_key;

    /** @var string API secret */
    private $api_secret;

    /** @var string API base path */
    private $api_base_path;

    public function __construct() {
        $this->api_base_path = trim(get_config(
            'local_intelliboard', 'attendanceapibase'
        ));
        $this->api_key = trim(get_config(
            'local_intelliboard', 'attendanceapikey'
        ));
        $this->api_secret = trim(get_config(
            'local_intelliboard', 'attendanceapisecret'
        ));

        if(!$this->api_base_path or !$this->api_key or !$this->api_secret) {
            throw new \Exception(
                'API base path, API key and API secret is required'
            );
        }
    }

    /**
     * Create session
     *
     * @param stdClass $session Session object
     * @return json
     */
    public function create_session($session) {
        $requesturl = rtrim($this->api_base_path) . '/session/create';
        $token = $this->generate_token();

        return $this->make_request(
            'POST', $requesturl, $token, $session
        );
    }

    /**
     * Insert session attendance
     *
     * @param array $data Request data
     * @return json
     */
    public function insert_attendance($data) {
        $requesturl = rtrim($this->api_base_path) . '/attendance/create';
        $token = $this->generate_token();

        return $this->make_request(
            'POST', $requesturl, $token, $data
        );
    }

    /**
     * Generate JWT token for request to Attendance API
     *
     * @return string JWT token
     */
    private function generate_token() {
        $date_utc = new \DateTime("now", new \DateTimeZone("UTC"));
        $exp = $date_utc->getTimestamp() + self::TOKEN_LIFE_TIME;

        // Create token header as a JSON string
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

        // Create token payload as a JSON string
        $payload = json_encode([
            "api_key" => $this->api_key,
            "exp" => $exp,
        ]);

        // Encode Header to Base64Url String
        $base64UrlHeader = str_replace(
            ['+', '/', '='], ['-', '_', ''], base64_encode($header)
        );

        // Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(
            ['+', '/', '='], ['-', '_', ''], base64_encode($payload)
        );

        // Create Signature Hash
        $signature = hash_hmac(
            'sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->api_secret, true
        );

        // Encode Signature to Base64Url String
        $base64UrlSignature = str_replace(
            ['+', '/', '='], ['-', '_', ''], base64_encode($signature)
        );

        // JWT
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * Make request to Attendance API
     *
     * @param string $method
     * @param string $url
     * @param string $token
     * @param array $params
     * @return string
     */
    private function make_request($method, $url, $token, $params = []) {
        global $CFG;

        require_once($CFG->dirroot . '/lib/filelib.php');

        $curl = new \curl();

        if(strtolower($method) === 'post') {
            $response = $curl->post(
                $url, $params, ['CURLOPT_HTTPHEADER' => ['Authorization: ' . $token]]
            );
        } elseif(strtolower($method) === 'get') {
            $response = $curl->get(
                $url, $params, ['CURLOPT_HTTPHEADER' => ['Authorization: ' . $token]]
            );
        } else {
            throw new \Exception('Invalid method');
        }

        if($curl->get_info()['http_code'] !== 200) {
            throw new \Exception($response);
        }
        
        return $response;
    }
}