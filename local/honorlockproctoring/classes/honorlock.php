<?php
// This file is part of the honorlockproctoring module for Moodle - http://moodle.org/
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

namespace local_honorlockproctoring;

/**
 * Honorlock proctoring module.
 *
 * @package    local_honorlockproctoring
 * @copyright  2023 Honorlock (https://honorlock.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class honorlock {

    /**
     * The Honorlock API class instance.
     *
     * @var honorlockapi
     */
    private $honorlockapi;

    /**
     * The class constructor.
     */
    public function __construct() {
        $this->honorlockapi = new honorlockapi();
    }

    /**
     * Extension check.
     *
     * @return object
     */
    public function extension_check(): object {
        $result = $this->honorlockapi->send_request("get", "/api/en/v1/extension/check");

        return $result->data;
    }

    /**
     * Create a session.
     *
     * @param array $sessiondetails
     * @return object|null
     */
    public function create_session(array $sessiondetails): ?object {
        $result = $this->honorlockapi->send_request("post", "/api/en/v1/exams/sessions/create", $sessiondetails);

        return $result->data;
    }

    /**
     * Get exam instructions.
     *
     * @param string $examid
     * @return object|null
     */
    public function get_exam_instructions(string $examid): ?object {
        $result = $this->honorlockapi->send_request("get", "/api/en/v1/exams/".$examid."/instructions");

        return $result->data;
    }

    /**
     * Begin session.
     *
     * @param string $userid
     * @param string $examid
     * @param string $attemptid
     * @return object|null
     */
    public function begin_session(string $userid, string $examid, string $attemptid): ?object {
        $payload = [
            'external_exam_id' => $examid,
            'exam_taker_id' => $userid,
            'exam_taker_attempt_id' => $attemptid,
        ];

        $result = $this->honorlockapi->send_request("post", "/api/en/v1/session/start", $payload);

        if ($result->message === "Session has already started") {
            $payload['continue'] = 1;
            $result = $this->honorlockapi->send_request("post", "/api/en/v1/session/start", $payload);
        }

        return $result->data;
    }

    /**
     * End session.
     *
     * @param string $userid
     * @param string $examid
     * @param string $attemptid
     * @return object|null
     */
    public function end_session(string $userid, string $examid, string $attemptid): ?object {
        $payload = [
            'external_exam_id' => $examid,
            'exam_taker_id' => $userid,
            'exam_taker_attempt_id' => $attemptid,
        ];

        $result = $this->honorlockapi->send_request("post", "/api/en/v1/session/complete", $payload);

        return $result->data;
    }
}
