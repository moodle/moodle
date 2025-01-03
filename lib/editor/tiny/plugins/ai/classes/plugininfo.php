<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Tiny Tiny AI Button plugin for Moodle.
 *
 * @package     tiny_ai
 * @copyright   2024, ISB Bayern
 * @author      Dr. Peter Mayer
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_ai;

use context;
use editor_tiny\plugin;
use editor_tiny\plugin_with_buttons;
use editor_tiny\plugin_with_menuitems;
use editor_tiny\plugin_with_configuration;
use local_ai_manager\ai_manager_utils;
use local_ai_manager\local\config_manager;
use local_ai_manager\local\tenant;
use local_ai_manager\local\userinfo;

/**
 * Tiny plugin info class.
 *
 * @package     tiny_ai
 * @copyright   2024 ISB Bayern
 * @author      Philipp Memmel
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugininfo extends plugin implements plugin_with_configuration, plugin_with_buttons, plugin_with_menuitems {

    #[\Override]
    public static function is_enabled(
        context $context,
        array $options,
        array $fpoptions,
        ?\editor_tiny\editor $editor = null
    ): bool {
        global $USER;
        $tenant = \core\di::get(tenant::class);
        $configmanager = \core\di::get(config_manager::class);
        if (!has_capability('tiny/ai:view', $context) || !$tenant->is_tenant_allowed()) {
            return false;
        }
        if (!$configmanager->is_tenant_enabled()) {
            return ai_manager_utils::get_ai_config($USER)['role'] !== userinfo::get_role_as_string(userinfo::ROLE_BASIC);
        }
        return true;
    }


    #[\Override]
    public static function get_available_buttons(): array {
        return [
            'tiny_ai/plugin',
        ];
    }

    #[\Override]
    public static function get_available_menuitems(): array {
        return [
            'tiny_ai/plugin',
        ];
    }

    #[\Override]
    public static function get_plugin_configuration_for_context(
        context $context,
        array $options,
        array $fpoptions,
        ?\editor_tiny\editor $editor = null
    ): array {
        global $USER;
        return [
            'userId' => intval($USER->id),
        ];
    }
}
