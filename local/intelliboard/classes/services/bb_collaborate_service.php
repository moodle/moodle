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

use cache;
use local_intelliboard\bb_collaborate\bb_collaborate_repository;
use local_intelliboard\bb_collaborate\session_attendances;
use local_intelliboard\attendance\attendance_api;

class bb_collaborate_service {
    /**
     * Key of cached access token
     */
    const ACCESS_TOKEN_CACHE_KEY = 'bb_col_access_token';

    /**
     * Life time of access token for bb collaborate API (in seconds)
     */
    const ACCESS_TOKEN_LIFE_TIME = 300;

    /**
     * Generate JWT token for requests to BB collaborate API
     *
     * @return string
     * @throws \dml_exception
     */
    public function generate_jwt_token() {
        $date_utc = new \DateTime("now", new \DateTimeZone("UTC"));
        $exp = $date_utc->getTimestamp() + self::ACCESS_TOKEN_LIFE_TIME;
        $consumerkey = get_config('local_intelliboard', 'bb_col_consumer_key');
        $secret = get_config('local_intelliboard', 'bb_col_secret');

        // Create token header as a JSON string
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

        // Create token payload as a JSON string
        $payload = json_encode([
            "iss" => $consumerkey,
            "sub" => $consumerkey,
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
            'sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true
        );

        // Encode Signature to Base64Url String
        $base64UrlSignature = str_replace(
            ['+', '/', '='], ['-', '_', ''], base64_encode($signature)
        );

        // Create JWT
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        return $jwt;
    }

    /**
     * Mark session as tracked
     *
     * @param string $sessionuid
     * @return bool|int
     * @throws \dml_exception
     */
    public function mark_session_tracked($sessionuid) {
        global $DB;

        $row = new \stdClass();
        $row->sessionuid = $sessionuid;
        $row->track_time = time();

        return $DB->insert_record('local_intelliboard_bb_trck_m', $row);
    }

    /**
     * Insert attendees of session
     *
     * @param string $sessionuid
     * @param array $attendees
     * @throws \dml_exception
     * @throws \dml_transaction_exception
     */
    public function insert_session_attendees($sessionuid, $attendees) {
        global $DB;

        try {
            $transaction = $DB->start_delegated_transaction();

            foreach($attendees as $item) {
                if (isset($item['userId']) && isset($item['externalUserId'])) {
                    $row = new \stdClass();
                    $row->sessionuid = $sessionuid;
                    $row->useruid = $item['userId'];
                    $row->external_user_id = $item['externalUserId'];
                    $row->role = $item['role'];
                    $row->display_name = $item['displayName'];
                    $row->first_join_time = 0;
                    $row->last_left_time = 0;
                    $row->duration = 0;
                    $row->rejoins = -1;

                    foreach($item['attendance'] as $join) {
                        $row->duration += $join['duration'];
                        $row->rejoins += 1;

                        if(
                            strtotime($join['joined']) < $row->first_join_time or
                            $row->first_join_time === 0
                        ) {
                            $row->first_join_time = strtotime($join['joined']);
                        }

                        if(
                            strtotime($join['left']) < $row->last_left_time or
                            $row->last_left_time === 0
                        ) {
                            $row->last_left_time = strtotime($join['left']);
                        }
                    }

                    $DB->insert_record('local_intelliboard_bb_partic', $row);
                }
            }

            // Assuming the both inserts work, we get to the following line.
            $transaction->allow_commit();

        } catch(\Exception $e) {
            $transaction->rollback($e);
        }
    }

    /**
     * Cache BB collaborate access token
     *
     * @param $token
     * @return bool
     */
    public function remember_access_token($token) {
        $cache = cache::make(
            'local_intelliboard', 'bb_collaborate_access_token'
        );

        return $cache->set(self::ACCESS_TOKEN_CACHE_KEY, $token);
    }

    /**
     * Mark session as synchronized wit InAttendance
     *
     * @param $sessionid
     * @return bool|int
     * @throws \dml_exception
     */
    public function mark_session_synchronized($sessionid, $externalsessionid) {
        global $DB;

        $row = new \stdClass();
        $row->type = bb_collaborate_repository::ATT_SYNC_TYPE;
        $row->instance = $sessionid;
        $row->data = json_encode([
            'external_session_id' => $externalsessionid
        ]);

        return $DB->insert_record(
            'local_intelliboard_att_sync', $row
        );
    }

    /**
     *
     *
     * @param object $session
     * @param session_attendances $attendances
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function synchronize_attendances($session, $attendances) {
        $items = [];

        $students = get_role_users(
            explode(
                ',', get_config('local_intelliboard', 'filter11')
            ),
            \context_course::instance($session->course),
            false,
            'ra.id, u.id, u.firstname, u.lastname'
        );

        foreach($students as $student) {
            $items[] = [
                'lms_user_id' => $student->id,
                'status' => $attendances->get_status($student->id),
            ];
        }

        $data = [
            'attendances' => json_encode($items),
            'session' => json_decode($session->sync_data)->external_session_id
        ];

        try {
            $attendanceapi = new attendance_api();
            $response = json_decode($attendanceapi->insert_attendance($data));
        } catch(\Exception $e) {
            if(get_config('local_intelliboard', 'bb_col_debug')) {
                var_dump($e);
            }
        }

        if(!isset($response) or !$response or !$response->inserted) {
            throw new \Exception(
                'Attendance not synchronized. Attendance service error'
            );
        }

        return true;
    }
}
