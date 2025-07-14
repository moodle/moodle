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
use core_external\external_single_structure;
use core_external\external_value;

/**
 * External service to get unsent messages from the session.
 *
 * @package   core_message
 * @category  external
 * @copyright 2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_unsent_message extends external_api {
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Get unsent messages from the session.
     */
    public static function execute(): array {
        global $SESSION, $USER;

        $usercontext = \context_user::instance($USER->id);
        self::validate_context($usercontext);

        $message = isset($SESSION->core_message_set_unsent_message) ? $SESSION->core_message_set_unsent_message : [];
        // Unset this as we only want to return this once.
        unset($SESSION->core_message_set_unsent_message);

        return $message;
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_value
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'message' => new external_value(PARAM_TEXT, 'The message string', VALUE_OPTIONAL, ''),
            'conversationid' => new external_value(PARAM_INT, 'The conversation id', VALUE_OPTIONAL, 0),
            'otheruserid' => new external_value(PARAM_INT, 'The other user id', VALUE_OPTIONAL, 0),
        ]);
    }
}
