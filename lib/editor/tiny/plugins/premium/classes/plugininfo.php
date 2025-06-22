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
use editor_tiny\plugin_with_configuration_for_external;

/**
 * Tiny Premium plugin.
 *
 * @package     tiny_premium
 * @copyright   2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugininfo extends plugin implements plugin_with_configuration, plugin_with_configuration_for_external {

    #[\Override]
    public static function is_enabled(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): bool {
        return has_capability('tiny/premium:use', $context) && (get_config('tiny_premium', 'apikey') != false);
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
        $allowedplugins = [];
        $serviceurls = [];
        $serverplugins = manager::get_server_side_plugins();

        foreach (manager::get_enabled_plugins() as $plugin) {
            if (has_capability("tiny/premium:use{$plugin}", $context)) {
                $allowedplugins[] = $plugin;
            }
            $serviceurl = get_config(manager::get_formatted_plugin_name($plugin), 'service_url');
            if ($serviceurl && isset($serverplugins[$plugin])) {
                $serviceurls[$serverplugins[$plugin]] = $serviceurl;
            }
        }

        return [
            'premiumplugins' => implode(',', $allowedplugins),
            'serviceurls' => $serviceurls,
        ];
    }

    #[\Override]
    public static function get_plugin_configuration_for_external(context $context): array {
        $settings = self::get_plugin_configuration_for_context($context, [], []);
        return [
            'premiumplugins' => $settings['premiumplugins'],
            'serviceurls' => $settings['serviceurls'],
        ];
    }
}
