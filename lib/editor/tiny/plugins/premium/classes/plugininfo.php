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

use context;
use editor_tiny\editor;
use editor_tiny\plugin;
use editor_tiny\plugin_with_configuration;
use tiny_premium\manager;

/**
 * Tiny Premium plugin.
 *
 * @package     tiny_premium
 * @copyright   2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugininfo extends plugin implements plugin_with_configuration {

    /**
     * Determine if the plugin should be enabled by checking the capability and if the Tiny Premium API key is set.
     *
     * @param context $context The context that the editor is used within
     * @param array $options The options passed in when requesting the editor
     * @param array $fpoptions The filepicker options passed in when requesting the editor
     * @param editor $editor The editor instance in which the plugin is initialised
     * @return bool
     */
    public static function is_enabled(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): bool {
        return has_capability('tiny/premium:accesspremium', $context) && (get_config('tiny_premium', 'apikey') != false);
    }

    /**
     * Get a list of enabled Tiny Premium plugins set by the admin.
     *
     * @param context $context The context that the editor is used within
     * @param array $options The options passed in when requesting the editor
     * @param array $fpoptions The filepicker options passed in when requesting the editor
     * @param editor|null $editor The editor instance in which the plugin is initialised
     * @return array
     */
    public static function get_plugin_configuration_for_context(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): array {
        return [
            'premiumplugins' => implode(',', manager::get_enabled_plugins()),
        ];
    }
}
