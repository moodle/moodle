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

namespace mod_bigbluebuttonbn;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use mod_bigbluebuttonbn\local\config;

/**
 * The broker routines
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */
class broker {

    /** @var array List of required params */
    protected $requiredparams = [
        'recording_ready' => [
            'bigbluebuttonbn' => 'The BigBlueButtonBN instance ID must be specified.',
            'signed_parameters' => 'A JWT encoded string must be included as [signed_parameters].'
        ],
        'meeting_events' => [
            'bigbluebuttonbn' => 'The BigBlueButtonBN instance ID must be specified.'
        ],
    ];

    /**
     * Validate the supplied list of parameters, providing feedback about any missing or incorrect values.
     *
     * @param array $params
     * @return null|string
     */
    public function validate_parameters(array $params): ?string {
        if (!isset($params['action']) || empty($params['action']) ) {
            return 'Parameter ['.$params['action'].'] was not included';
        }

        $action = strtolower($params['action']);
        if (!array_key_exists($action, $this->requiredparams)) {
            return "Action {$params['action']} can not be performed.";
        }
        return $this->validate_parameters_message($params, $this->requiredparams[$action]);
    }

    /**
     * Check whether the specified parameter is valid.
     *
     * @param array $params
     * @param array $requiredparams
     * @return null|string
     */
    protected static function validate_parameters_message(array $params, array $requiredparams): ?string {
        foreach ($requiredparams as $param => $message) {
            if (!array_key_exists($param, $params) || $params[$param] == '') {
                return $message;
            }
        }

        // Everything is valid.
        return null;
    }

    /**
     * Helper for responding when recording ready is performed.
     *
     * @param instance $instance
     * @param array $params
     */
    public static function process_recording_ready(instance $instance, array $params): void {
        // Decodes the received JWT string.
        try {
            $decodedparameters = JWT::decode(
                $params['signed_parameters'],
                new Key(config::get('shared_secret'), 'HS256')
            );
        } catch (Exception $e) {
            $error = 'Caught exception: ' . $e->getMessage();
            header('HTTP/1.0 400 Bad Request. ' . $error);
            return;
        }

        // Validations.
        if (!isset($decodedparameters->record_id)) {
            header('HTTP/1.0 400 Bad request. Missing record_id parameter');
            return;
        }

        $recording = recording::get_record(['recordingid' => $decodedparameters->record_id]);
        if (!isset($recording)) {
            header('HTTP/1.0 400 Bad request. Invalid record_id');
            return;
        }

        // Sends the messages.
        try {
            // We make sure messages are sent only once.
            if ($recording->get('status') != recording::RECORDING_STATUS_NOTIFIED) {
                $task = new \mod_bigbluebuttonbn\task\send_recording_ready_notification();
                $task->set_instance_id($instance->get_instance_id());

                \core\task\manager::queue_adhoc_task($task);

                $recording->set('status', recording::RECORDING_STATUS_NOTIFIED);
                $recording->update();
            }
            header('HTTP/1.0 202 Accepted');
        } catch (Exception $e) {
            $error = 'Caught exception: ' . $e->getMessage();
            header('HTTP/1.0 503 Service Unavailable. ' . $error);
        }
    }

    /**
     * Process meeting events for instance with provided HTTP headers.
     *
     * @param instance $instance
     * @return void
     */
    public static function process_meeting_events(instance $instance) {
        try {
            // Get the HTTP headers (getallheaders is a PHP function that may only work with Apache).
            $headers = getallheaders();

            // Pull the Bearer from the headers.
            if (!array_key_exists('Authorization', $headers)) {
                $msg = 'Authorization failed';
                header('HTTP/1.0 400 Bad Request. ' . $msg);
                return;
            }
            $authorization = explode(" ", $headers['Authorization']);

            // Verify the authenticity of the request.
            $token = \Firebase\JWT\JWT::decode(
                $authorization[1],
                new Key(config::get('shared_secret'), 'HS512')
            );

            // Get JSON string from the body.
            $jsonstr = file_get_contents('php://input');

            // Convert JSON string to a JSON object.
            $jsonobj = json_decode($jsonstr);
            $headermsg = meeting::meeting_events($instance, $jsonobj);
            header($headermsg);
        } catch (Exception $e) {
            $msg = 'Caught exception: ' . $e->getMessage();
            header('HTTP/1.0 400 Bad Request. ' . $msg);
        }
    }
}
