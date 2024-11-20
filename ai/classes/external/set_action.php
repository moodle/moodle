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

use core\context\system;
use core_ai\manager;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;

/**
 * External API to set provider action enabled.
 *
 * @package    core_ai
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_action extends external_api {
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'plugin' => new external_value(
                PARAM_TEXT,
                'The name of the plugin',
                VALUE_REQUIRED,
            ),
            'state' => new external_value(
                PARAM_INT,
                'The target state',
                VALUE_REQUIRED,
            ),
        ]);
    }

    /**
     * Set the providers action state.
     *
     * @param string $plugin The name of the plugin.
     * @param int $state The target state.
     * @return array
     */
    public static function execute(
        string $plugin,
        int $state,
    ): array {
        // Parameter validation.
        [
            'plugin' => $plugin,
            'state' => $state,
        ] = self::validate_parameters(self::execute_parameters(), [
            'plugin' => $plugin,
            'state' => $state,
        ]);

        $context = system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        [$plugin, $action] = explode('-', $plugin);
        $actionname = get_string("action_$action", 'core_ai');

        if (!empty($state)) {
            \core\notification::add(
                get_string('plugin_enabled', 'core_admin', $actionname),
                \core\notification::SUCCESS
            );
        } else {
            \core\notification::add(
                get_string('plugin_disabled', 'core_admin', $actionname),
                \core\notification::SUCCESS
            );
        }

        manager::set_action_state($plugin, $action, $state);

        return [];
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_function_parameters
     */
    public static function execute_returns(): external_function_parameters {
        return new external_function_parameters([]);
    }
}
