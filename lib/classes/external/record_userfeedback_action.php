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

namespace core\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;

/**
 * The external API to record users action on the feedback notification.
 *
 * @package    core
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class record_userfeedback_action extends external_api {
    /**
     * Returns description of parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'action' => new external_value(PARAM_ALPHA, 'The action taken by user'),
            'contextid' => new external_value(PARAM_INT, 'The context id of the page the user is in'),
        ]);
    }

    /**
     * Record users action to the feedback CTA
     *
     * @param string $action The action the user took
     * @param int $contextid The context id
     * @throws \invalid_parameter_exception
     */
    public static function execute(string $action, int $contextid) {
        external_api::validate_parameters(self::execute_parameters(), [
            'action' => $action,
            'contextid' => $contextid,
        ]);

        $context = \context::instance_by_id($contextid);
        self::validate_context($context);

        switch ($action) {
            case 'give':
                set_user_preference('core_userfeedback_give', time());
                $event = \core\event\userfeedback_give::create(['context' => $context]);
                $event->trigger();
                break;
            case 'remind':
                set_user_preference('core_userfeedback_remind', time());
                $event = \core\event\userfeedback_remind::create(['context' => $context]);
                $event->trigger();
                break;
            default:
                throw new \invalid_parameter_exception('Invalid value for action parameter (value: ' . $action . '),' .
                        'allowed values are: give,remind');
        }
    }

    /**
     * Returns description of method result value
     *
     * @return null
     */
    public static function execute_returns() {
        return null;
    }
}
