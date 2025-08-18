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

use moodle_url;
use moodleform;
use stdClass;

/**
 * Tiny Premium manager.
 *
 * @package    tiny_premium
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /** @var string Tiny Premium plugin prefix. */
    const PLUGIN_NAME_PREFIX = 'tiny_premium_';
    /** @var int Indicates usage of self-hosted TinyMCE packages. */
    public const PACKAGE_SELF_HOSTED = 2;
    /** @var int Indicates usage of cloud-hosted TinyMCE packages. */
    public const PACKAGE_CLOUD = 1;

    /**
     * Get all Tiny Premium plugins currently supported.
     *
     * The plugin identifiers are taken from Tiny Cloud (https://www.tiny.cloud/docs/tinymce/6/plugins/#premium-plugins).
     *
     * @return array The array of plugins.
     */
    public static function get_plugins(): array {
        return [
            'a11ychecker',
            'advtable',
            'typography',
            'casechange',
            'checklist',
            'editimage',
            'export',
            'footnotes',
            'formatpainter',
            'linkchecker',
            'math',
            'pageembed',
            'permanentpen',
            'powerpaste',
            'tinymcespellchecker',
            'autocorrect',
            'tableofcontents',
        ];
    }

    /**
     * Get all Tiny Premium plugins that require server-side processing.
     *
     * This method returns an associative array where the keys are the plugin names
     * and the values are the corresponding configuration keys for the server-side
     * processing URLs. These plugins require additional server-side services to
     * function properly.
     *
     * @return string[] An associative array of server-side plugins and their configuration keys.
     */
    public static function get_server_side_plugins(): array {
        return [
            'editimage' => 'editimage_proxy_service_url',
            'linkchecker' => 'linkchecker_service_url',
            'tinymcespellchecker' => 'spellchecker_rpc_url',
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
        $plugin = self::get_formatted_plugin_name($plugin);
        $config = get_config($plugin, 'enabled');
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

        $formattedplugin = self::get_formatted_plugin_name($plugin);
        $unsetconfigs = [];
        foreach ($data as $key => $newvalue) {
            // Get the old value for the log.
            $oldvalue = get_config($formattedplugin, $key) ?? null;
            add_to_config_log($key, $oldvalue, $newvalue, $formattedplugin);

            // If we are disabling the plugin, remove it, otherwise, set the new value.
            if ($key === 'enabled' && $newvalue == 0) {
                $unsetconfigs[] = $key;
            } else {
                set_config($key, $newvalue, $formattedplugin);
            }
        }

        self::unset_plugin_config($unsetconfigs, $plugin);
    }

    /**
     * Get values for a Tiny Premium plugin config.
     *
     * @param string $plugin The plugin to get.
     * @param string|null $name The name of the config to get.
     * @return mixed hash-like object or single value, return false no config found.
     */
    public static function get_plugin_config(
        string $plugin,
        ?string $name = null,
    ): mixed {
        // Check this is a valid premium plugin.
        if (!in_array($plugin, self::get_plugins())) {
            return false;
        }
        $plugin = self::get_formatted_plugin_name($plugin);
        return get_config($plugin, $name);
    }

    /**
     * Unset a Tiny Premium plugin configs.
     *
     * @param array $names List of config names to unset.
     * @param string $plugin The plugin to use.
     */
    public static function unset_plugin_config(
        array $names,
        string $plugin,
    ): void {
        // Check this is a valid premium plugin.
        if (!in_array($plugin, self::get_plugins())) {
            return;
        }

        $plugin = self::get_formatted_plugin_name($plugin);
        foreach ($names as $key) {
            unset_config($key, $plugin);
        }
    }

    /**
     * Get the settings form for a Tiny Premium plugin.
     * This method will check if a custom form class exists for the plugin,
     * otherwise it will use the default settings form.
     *
     * @param string $plugin The plugin to get the settings form for.
     * @param moodle_url|null $return The URL to return to after saving the form.
     * @return moodleform Settings form for the Tiny Premium plugin.
     */
    public static function get_settings_form(
        string $plugin,
        ?moodle_url $return = null,
    ): moodleform {
        $formclassname = '\tiny_premium\form\tiny_premium_' . $plugin . '_settings_form';
        if (!class_exists($formclassname)) {
            $formclassname = '\tiny_premium\form\tiny_premium_settings_form';
        }

        return new $formclassname(null, [
            'plugin' => $plugin,
            'returnurl' => $return,
        ]);
    }

    /**
     * Get the formatted plugin name for config.
     *
     * @param string $plugin The plugin to format.
     * @return string The formatted plugin name.
     */
    public static function get_formatted_plugin_name(
        string $plugin,
    ): string {
        return self::PLUGIN_NAME_PREFIX . $plugin;
    }
}
