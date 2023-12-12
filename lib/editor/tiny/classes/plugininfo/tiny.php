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

namespace editor_tiny\plugininfo;

use moodle_url;

/**
 * Subplugin info class.
 *
 * @package     editor_tiny
 * @copyright   2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tiny extends \core\plugininfo\base {

    public static function plugintype_supports_disabling(): bool {
        return true;
    }

    /**
     * These subplugins can be uninstalled.
     *
     * @return bool
     */
    public function is_uninstall_allowed(): bool {
        return true;
    }

    /**
     * Return URL used for management of plugins of this type.
     *
     * @return moodle_url
     */
    public static function get_manage_url(): moodle_url {
        return new moodle_url('/admin/settings.php', [
            'section' => 'editorsettingstiny',
        ]);
    }

    /**
     * Include the settings.php file from subplugins if provided.
     *
     * This is a copy of very similar implementations from various other subplugin areas.
     *
     * @param \part_of_admin_tree $adminroot
     * @param string $parentnodename
     * @param bool $hassiteconfig whether the current user has moodle/site:config capability
     */
    public function load_settings(\part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig): void {
        // In case settings.php wants to refer to them.
        global $CFG, $USER, $DB, $OUTPUT, $PAGE;

        /** @var \admin_root $ADMIN */
        $ADMIN = $adminroot; // May be used in settings.php.
        $plugininfo = $this; // Also can be used inside settings.php.

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig || !file_exists($this->full_path('settings.php'))) {
            return;
        }

        $section = $this->get_settings_section_name();
        $settings = new \admin_settingpage(
            $section,
            $this->displayname,
            'moodle/site:config',
            $this->is_enabled() === false
        );

        // This may also set $settings to null.
        include($this->full_path('settings.php'));

        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    /**
     * Get the settings section name.
     * This is used to get the setting links in the Tiny sub-plugins table.
     *
     * @return null|string the settings section name.
     */
    public function get_settings_section_name(): ?string {
        if (!file_exists($this->full_path('settings.php'))) {
            return null;
        }

        return "tiny_{$this->name}_settings";
    }

    public static function get_enabled_plugins(): array {
        $pluginmanager = \core_plugin_manager::instance();
        $plugins = $pluginmanager->get_installed_plugins('tiny');

        if (!$plugins) {
            return [];
        }

        // Filter to return only enabled plugins.
        $enabled = [];
        foreach (array_keys($plugins) as $pluginname) {
            $disabled = get_config("tiny_{$pluginname}", 'disabled');
            if (empty($disabled)) {
                $enabled[$pluginname] = $pluginname;
            }
        }
        return $enabled;
    }

    public static function enable_plugin(string $plugin, int $enabled): bool {
        $pluginname = "tiny_{$plugin}";

        $oldvalue = !empty(get_config($pluginname, 'disabled'));
        $disabled = empty($enabled);
        $haschanged = false;

        // Only set value if there is no config setting or if the value is different from the previous one.
        if (!$oldvalue && $disabled) {
            set_config('disabled', $disabled, $pluginname);
            $haschanged = true;
        } else if ($oldvalue && !$disabled) {
            unset_config('disabled', $pluginname);
            $haschanged = true;
        }

        if ($haschanged) {
            add_to_config_log('disabled', $oldvalue, $disabled, $pluginname);
            \core_plugin_manager::reset_caches();
        }

        return $haschanged;
    }
}
