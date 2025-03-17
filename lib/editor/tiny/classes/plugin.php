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

namespace editor_tiny;

use context;

/**
 * Tiny Editor Plugin class.
 *
 * This class must be implemented by any Moodle plugin adding TinyMCE features.
 *
 * It should optionally implement the following interfaces:
 * - plugin_with_buttons: to add buttons to the TinyMCE toolbar
 * - plugin_with_menuitems
 * - plugin_with_configuration: to add configuration to the TinyMCE editor
 *
 * @package editor_tiny
 * @copyright  2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class plugin {
    /**
     * Whether the plugin is enabled and accessible (e.g. capability checks).
     *
     * @param context $context The context that the editor is used within
     * @param array $options The options passed in when requesting the editor
     * @param array $fpoptions The filepicker options passed in when requesting the editor
     * @param editor $editor The editor instance in which the plugin is initialised
     * @return boolean
     */
    public static function is_enabled(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): bool {
        $plugin = $options['pluginname'];
        $capability = "tiny/$plugin:use";
        if (!get_capability_info($capability)) {
            // Debug warning that the capability does not exist.
            debugging(
                'The tiny ' . $plugin . ' plugin does not define the standard capability ' . $capability ,
                DEBUG_DEVELOPER
            );
            return true;
        }

        return has_capability($capability, $context);
    }

    /**
     * Whether the plugin is enabled and accessible for external functions.
     *
     * @param context $context The context that the editor is used within.
     * @param array $options Additional options:
     *    - pluginname: Name of the plugin, without the "tiny_" prefix.
     * @return bool
     */
    public static function is_enabled_for_external(context $context, array $options): bool {
        return static::is_enabled($context, $options, []);
    }

    /**
     * Get the plugin information for the plugin.
     *
     * @param context $context The context that the editor is used within
     * @param array $options The options passed in when requesting the editor
     * @param array $fpoptions The filepicker options passed in when requesting the editor
     * @param editor $editor The editor instance in which the plugin is initialised
     * @return array
     */
    final public static function get_plugin_info(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): array {
        $plugindata = [];

        if (is_a(static::class, plugin_with_buttons::class, true)) {
            $plugindata['buttons'] = static::get_available_buttons();
        }

        if (is_a(static::class, plugin_with_menuitems::class, true)) {
            $plugindata['menuitems'] = static::get_available_menuitems();
        }

        if (is_a(static::class, plugin_with_configuration::class, true)) {
            $plugindata['config'] = static::get_plugin_configuration_for_context($context, $options, $fpoptions, $editor);
        }

        return $plugindata;
    }
}
