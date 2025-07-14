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


namespace core\plugininfo;

use core\url;

/**
 * Plugin information for the gradepenalty plugin type.
 *
 * @package   core
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradepenalty extends base {
    #[\Override]
    public function is_uninstall_allowed(): bool {
        return true;
    }

    #[\Override]
    public static function get_manage_url(): url {
        return new url('/grade/penalty/manage_penalty_plugins.php');
    }

    #[\Override]
    public static function plugintype_supports_disabling(): bool {
        return true;
    }

    #[\Override]
    public static function get_enabled_plugins(): array {
        // List of enabled plugins, string delimited.
        $plugins = get_config('core_grades', 'gradepenalty_enabled_plugins');

        // Return empty array if no plugins are enabled.
        return $plugins ? array_flip(explode(',', $plugins)) : [];
    }

    #[\Override]
    public static function enable_plugin(string $pluginname, int $enabled): bool {
        // Current enabled plugins.
        $enabledplugins = self::get_enabled_plugins();

        // If we are enabling the plugin.
        if ($enabled) {
            $enabledplugins[$pluginname] = $pluginname;
        } else {
            unset($enabledplugins[$pluginname]);
        }

        // Convert to string.
        $enabledplugins = implode(',', array_keys($enabledplugins));

        // Save the new list of enabled plugins.
        set_config('gradepenalty_enabled_plugins', $enabledplugins, 'core_grades');

        return true;
    }

    #[\Override]
    public function is_enabled(): bool {
        return self::is_plugin_enabled($this->name);
    }

    /**
     * If the provided plugin is enabled.
     *
     * @param string $pluginname The name of the plugin.
     * @return bool if the plugin is enabled.
     */
    public static function is_plugin_enabled(string $pluginname): bool {
        // Check if the plugin contains plugin type, remove it.
        $pluginname = str_replace('gradepenalty_', '', $pluginname);

        return key_exists($pluginname, self::get_enabled_plugins());
    }

    #[\Override]
    public function get_settings_section_name(): string {
        return $this->component;
    }

    #[\Override]
    public function get_settings_url(): ?url {
        $plugins = get_plugin_list_with_function('gradepenalty', 'get_settings_url');
        if (isset($plugins[$this->component])) {
            return component_callback($this->component, 'get_settings_url');
        } else {
            // Use the default settings page.
            return parent::get_settings_url();
        }
    }
}
