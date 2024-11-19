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

namespace core_message\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;

/**
 * External service to store unsent messages in the session.
 *
 * @package   core_message
 * @category  external
 * @copyright 2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_unsent_message extends external_api {
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'message' => new external_value(PARAM_TEXT, 'The message string', VALUE_REQUIRED, ''),
            'conversationid' => new external_value(PARAM_INT, 'The conversation id', VALUE_REQUIRED, 0),
            'otheruserid' => new external_value(PARAM_INT, 'The other user id', VALUE_REQUIRED, 0),
        ]);
    }

    /**
     * Store the unsent message, along with conversation params, in the session for later retrieval.
     *
     * @param string $message The message string.
     * @param int $conversationid The conversation id.
     * @param int $otheruserid The other user id.
     */
    public static function execute(string $message, int $conversationid, int $otheruserid): void {
        global $SESSION, $USER;

        self::validate_parameters(self::execute_parameters(), [
            'message' => $message,
            'conversationid' => $conversationid,
            'otheruserid' => $otheruserid,
        ]);

        $usercontext = \context_user::instance($USER->id);
        self::validate_context($usercontext);

        $SESSION->core_message_set_unsent_message = [
            'message' => $message,
            'conversationid' => $conversationid,
            'otheruserid' => $otheruserid,
        ];
    }

    /**
     * Describes the data returned from the external function.
     */
    public static function execute_returns(): void {
    }
}
