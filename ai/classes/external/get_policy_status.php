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

namespace core_ai\external;

use core_ai\manager;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;

/**
 * External API to get a users AI policy acceptance.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_policy_status extends external_api {

    /**
     * Get policy parameters.
     *
     * @since  Moodle 4.5
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'userid' => new external_value(
                PARAM_INT,
                'The user ID',
                VALUE_REQUIRED,
            ),
            'contextid' => new external_value(
                PARAM_INT,
                'The context ID',
                VALUE_REQUIRED,
            ),
        ]);
    }

    /**
     * Set a users AI policy acceptance.
     *
     * @since  Moodle 4.5
     * @param int $userid The user ID.
     * @param int $contextid The context ID.
     * @return array The generated content.
     */
    public static function execute(
        int $userid,
        int $contextid,
    ): array {
        global $USER;
        // Parameter validation.
        [
            'userid' => $userid,
            'contextid' => $contextid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'userid' => $userid,
            'contextid' => $contextid,
        ]);

        // Context validation and permission check.
        // Get the context from the passed in ID.
        $context = \context::instance_by_id($contextid);

        // Check the user has permission to use the AI service.
        self::validate_context($context);
        require_capability('moodle/ai:fetchpolicy', $context);

        // If the context level is that of a user, check the user is the same as the one passed in.
        if ($context->contextlevel == CONTEXT_USER && ((int) $USER->id !== $userid)) {
            throw new \moodle_exception('invaliduser', 'error');
        }

        return [
            'status' => manager::get_user_policy($userid),
        ];
    }

    /**
     * Generate content return value.
     *
     * @since  Moodle 4.5
     * @return external_function_parameters
     */
    public static function execute_returns(): external_function_parameters {
        return new external_function_parameters([
            'status' => new external_value(
                PARAM_BOOL,
                'True if the policy was accepted, false otherwise.',
                VALUE_REQUIRED,
            ),
        ]);
    }
}
