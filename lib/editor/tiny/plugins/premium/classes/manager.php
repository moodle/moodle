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

namespace tiny_premium;

/**
 * Tiny Premium manager.
 *
 * @package    tiny_premium
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /**
     * Get all Tiny Premium plugins currently supported.
     *
     * The plugin identifiers are taken from Tiny Cloud (https://www.tiny.cloud/docs/tinymce/6/plugins/#premium-plugins).
     *
     * @return array The array of plugins.
     */
    public static function get_plugins(): array {
        return [
            'advtable',
            'typography',
            'casechange',
            'checklist',
            'editimage',
            'export',
            'footnotes',
            'formatpainter',
            'linkchecker',
            'pageembed',
            'permanentpen',
            'powerpaste',
            'tinymcespellchecker',
            'autocorrect',
            'tableofcontents',
        ];
    }

    /**
     * Get enabled Tiny Premium plugins.
     *
     * @return array The array of enabled plugins.
     */
    public static function get_enabled_plugins(): array {
        $plugins = self::get_plugins();
        $enabledplugins = [];
        foreach ($plugins as $plugin) {
            if (self::is_plugin_enabled($plugin)) {
                $enabledplugins[] = $plugin;
            }
        }
        return $enabledplugins;
    }

    /**
     * Check if a Tiny Premium plugin is enabled in config.
     *
     * @param string $plugin The plugin to check.
     * @return bool Return true if enabled.
     */
    public static function is_plugin_enabled(string $plugin): bool {
        $config = get_config('tiny_premium_' . $plugin, 'enabled');
        return ($config == 1);
    }

    /**
     * Set a new value for a Tiny Premium plugin config.
     *
     * @param array $data The data to set.
     * @param string $plugin The plugin to use.
     */
    public static function set_plugin_config(array $data, string $plugin): void {
        // Check this is a valid premium plugin.
        if (!in_array($plugin, self::get_plugins())) {
            return;
        }

        $plugin = 'tiny_premium_' . $plugin;

        foreach ($data as $key => $newvalue) {
            // Get the old value for the log.
            $oldvalue = get_config($plugin, $key) ?? null;
            add_to_config_log($key, $oldvalue, $newvalue, $plugin);

            // If we are disabling the plugin, remove it, otherwise, set the new value.
            if ($key === 'enabled' && $newvalue == 0) {
                unset_config($key, $plugin);
            } else {
                set_config($key, $newvalue, $plugin);
            }
        }
    }
}
