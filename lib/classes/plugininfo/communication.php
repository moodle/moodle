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

use admin_settingpage;
use core_communication\processor;
use core_plugin_manager;
use moodle_url;

/**
 * Class for communication provider.
 *
 * @package    core
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class communication extends base {

    public static function get_manage_url(): ?moodle_url {
        if (!\core_communication\api::is_available()) {
            return null;
        }

        return new moodle_url('/admin/settings.php', ['section' => 'managecommunicationproviders']);
    }

    public function get_settings_section_name(): string {
        return $this->type . '_' . $this->name;
    }

    public static function enable_plugin(string $pluginname, int $enabled): bool {
        $haschanged = false;

        $plugin = 'communication_' . $pluginname;
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
            core_plugin_manager::reset_caches();
        }

        return $haschanged;
    }

    public static function get_enabled_plugins(): ?array {
        $pluginmanager = core_plugin_manager::instance();
        $plugins = $pluginmanager->get_installed_plugins('communication');

        if (!$plugins) {
            return [];
        }

        $plugins = array_keys($plugins);

        // Filter to return only enabled plugins.
        $enabled = [];
        foreach ($plugins as $plugin) {
            $disabled = get_config('communication_' . $plugin, 'disabled');
            if (empty($disabled)) {
                $enabled[$plugin] = $plugin;
            }
        }
        return $enabled;
    }

    public function load_settings(\part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
        $ADMIN = $adminroot; // May be used in settings.php.
        $plugininfo = $this;      // Also can be used inside settings.php.

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig) {
            return;
        }

        $section = $this->get_settings_section_name();
        $settings = null;
        if (file_exists($this->full_path('settings.php'))) {
            $settings = new admin_settingpage($section, $this->displayname,
                'moodle/site:config', $this->is_enabled() === false);
            include($this->full_path('settings.php')); // This may also set $settings to null.
        }
        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    public function is_uninstall_allowed(): bool {
        if (in_array($this->name, \core_plugin_manager::standard_plugins_list('communication'))) {
            return false;
        }
        return true;
    }

    /**
     * Checks if a communication plugin is ready to be used.
     * It checks the plugin status as well as the plugin is missing or not.
     *
     * @param string $fullpluginname the name of the plugin
     * @return bool
     */
    public static function is_plugin_enabled($fullpluginname): bool {
        $pluginmanager = \core_plugin_manager::instance();
        $communicationinfo = $pluginmanager->get_plugin_info($fullpluginname);
        if (empty($communicationinfo)) {
            return false;
        }
        $communicationavailable = $communicationinfo->get_status();
        return !($communicationavailable === \core_plugin_manager::PLUGIN_STATUS_MISSING ||
            !empty(get_config($fullpluginname, 'disabled')));
    }

}
