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

namespace core_admin\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Web Service to control the order of a plugin.
 *
 * @package   core_admin
 * @category  external
 * @copyright 2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_plugin_order extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'plugin' => new external_value(PARAM_PLUGIN, 'The name of the plugin', VALUE_REQUIRED),
            'direction' => new external_value(PARAM_INT, 'The direction to move', VALUE_REQUIRED),
        ]);
    }

    /**
     * Set the plugin state.
     *
     * @param string $plugin The name of the plugin
     * @param int $direction The direction to move the plugin
     * @return array
     */
    public static function execute(
        string $plugin,
        int $direction,
    ): array {
        [
            'plugin' => $plugin,
            'direction' => $direction,
        ] = self::validate_parameters(self::execute_parameters(), [
            'plugin' => $plugin,
            'direction' => $direction,
        ]);

        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        [$plugintype, $pluginname] = explode('_', \core_component::normalize_componentname($plugin), 2);

        $manager = \core_plugin_manager::resolve_plugininfo_class($plugintype);
        $manager::change_plugin_order($pluginname, $direction);

        return [];
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([]);
    }
}
