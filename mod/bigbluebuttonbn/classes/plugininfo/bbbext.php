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
namespace mod_bigbluebuttonbn\plugininfo;

use core\plugininfo\base;
use mod_bigbluebuttonbn\extension;

/**
 * Subplugin extension info class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2022 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class bbbext extends base {
    /**
     * Check if BigBlueButton plugin is enabled
     * @return bool
     */
    private static function is_bbb_enabled(): bool {
        $enabledplugins = \core\plugininfo\mod::get_enabled_plugins();
        return isset($enabledplugins['bigbluebuttonbn']);
    }

    /**
     * Get a list of enabled plugins
     *
     * @return array
     * @throws \dml_exception
     */
    public static function get_enabled_plugins(): array {
        // If the mod_bigbluebuttonbn is not enabled, then all subplugin are disabled.
        if (!self::is_bbb_enabled()) {
            return [];
        }
        // Get all available plugins.
        $plugins = \core_plugin_manager::instance()->get_installed_plugins(extension::BBB_EXTENSION_PLUGIN_NAME);
        if (!$plugins) {
            return [];
        }

        // Check they are enabled using get_config (which is cached and hopefully fast).
        $enabled = [];
        foreach ($plugins as $plugin => $version) {
            $disabled = get_config(extension::BBB_EXTENSION_PLUGIN_NAME . '_' . $plugin, 'disabled');
            if (empty($disabled)) {
                $enabled[$plugin] = $plugin;
            }
        }

        return $enabled;
    }

    /**
     * Enable the plugin
     *
     * @param string $pluginname
     * @param int $enabled
     * @return bool
     * @throws \dml_exception
     */
    public static function enable_plugin(string $pluginname, int $enabled): bool {
        $haschanged = false;
        // If the mod_bigbluebuttonbn is not enabled, then all subplugin are disabled.
        if (!self::is_bbb_enabled()) {
            return false;
        }
        $plugin = extension::BBB_EXTENSION_PLUGIN_NAME. '_' . $pluginname;
        $oldvalue = get_config($plugin, 'disabled');
        $disabled = !$enabled;
        // Only set value if there is no config setting or if the value is different from the previous one.
        if ($oldvalue == false && $disabled) {
            set_config('disabled', $disabled, $plugin);
            $haschanged = true;
        } else if ($oldvalue != false && !$disabled) {
            unset_config('disabled', $plugin);
            $haschanged = true;
        }

        if ($haschanged) {
            add_to_config_log('disabled', $oldvalue, $disabled, $plugin);
            \core_plugin_manager::reset_caches();
        }

        return $haschanged;
    }

    /**
     * Loads plugin settings to the settings tree
     *
     * This function usually includes settings.php file in plugins folder.
     * Alternatively it can create a link to some settings page (instance of admin_externalpage)
     *
     * @param \part_of_admin_tree $adminroot
     * @param string $parentnodename
     * @param bool $hassiteconfig whether the current user has moodle/site:config capability
     */
    public function load_settings(\part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        // If the mod_bigbluebuttonbn is not enabled, then all subplugin are disabled.
        if (!self::is_bbb_enabled()) {
            return;
        }
        $ADMIN = $adminroot;
        $plugininfo = $this;

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig || !file_exists($this->full_path('settings.php'))) {
            return;
        }

        $section = $this->get_settings_section_name();

        $settings = new \admin_settingpage($section, $this->displayname, 'moodle/site:config',
            $this->is_enabled() === false);

        if ($adminroot->fulltree) {
            $shortsubtype = substr($this->type, strlen(extension::BBB_EXTENSION_PLUGIN_NAME));
            include($this->full_path('settings.php'));
        }

        $adminroot->add($this->type . 'plugins', $settings);
    }

    /**
     * Get settings section name
     *
     * @return string
     */
    public function get_settings_section_name() {
        return $this->type . '_' . $this->name;
    }

    /**
     * Should there be a way to uninstall the plugin via the administration UI.
     *
     *
     * @return bool
     */
    public function is_uninstall_allowed() {
        return true;
    }
}
