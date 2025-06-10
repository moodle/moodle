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
 * API for bot framework.
 *
 * @package local_o365
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2018 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\rest;

defined('MOODLE_INTERNAL') || die();

/**
 * API for bot framework.
 */
class botframework {
    /**
     * @var string|null
     */
    private $token;
    /**
     * @var \local_o365\httpclient
     */
    private $httpclient;

    /**
     * botframework constructor.
     */
    public function __construct() {
        $this->httpclient = new \local_o365\httpclient();
        $this->token = null;
        $this->get_token();
    }

    /**
     * Authenticate with bot framework to get token.
     */
    public function get_token() {
        $tokenendpoint = 'https://login.microsoftonline.com/botframework.com/oauth2/v2.0/token';
        $params = [
            'grant_type' => 'client_credentials',
            'client_id' => get_config('local_o365', 'bot_app_id'),
            'client_secret' => get_config('local_o365', 'bot_app_password'),
            'scope' => 'https://api.botframework.com/.default',
        ];
        $paramstring = '';
        foreach ($params as $key => $param) {
            $paramstring .= urlencode($key) . '=' . urlencode($param) . '&';
        }
        $paramstring = substr($paramstring, 0, strlen($paramstring) - 1);
        $header = [
            'Host: login.microsoftonline.com',
            'Content-Type: application/x-www-form-urlencoded',
        ];

        $this->httpclient->resetHeader();
        $this->httpclient->setHeader($header);
        $rawresult = $this->httpclient->post($tokenendpoint, $paramstring);

        $result = json_decode($rawresult);
        if (property_exists($result, 'access_token')) {
            $this->token = $result->access_token;
        }
    }

    /**
     * Determine if a token exists.
     *
     * @return bool
     */
    public function has_token() {
        return !is_null($this->token);
    }

    /**
     * Send a notification to notification endpoint.
     *
     * @param int $teamid object ID of the team
     * @param int $userid object ID of the recipient user
     * @param string $message content of the message
     * @param array $listitems
     * @param string|null $endpoint endpoint URL
     */
    public function send_notification($teamid, $userid, $message, $listitems, $endpoint = null) {
        if (is_null($endpoint)) {
            $endpoint = get_config('local_o365', 'bot_webhook_endpoint');
        }

        $params = [
            'team' => $teamid,
            'user' => $userid,
            'message' => $message,
            'listItems' => $listitems
        ];
        $params = json_encode($params);

        $header = [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json',
        ];

        $this->httpclient->resetHeader();
        $this->httpclient->setHeader($header);
        $this->httpclient->post($endpoint, $params);

        $result = $this->httpclient->info['http_code'];

        // Todo handle result.
    }
}
