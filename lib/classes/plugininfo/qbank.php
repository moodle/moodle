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

/**
 * Defines classes used for plugin info.
 *
 * @package    core
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\plugininfo;

/**
 * Base class for qbank plugins.
 *
 * @package    core
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbank extends base {

    public static function plugintype_supports_disabling(): bool {
        return true;
    }

    public function is_uninstall_allowed(): bool {
        if (in_array($this->name, \core_plugin_manager::standard_plugins_list('qbank'))) {
            return false;
        }
        return true;
    }

    public static function get_manage_url(): \moodle_url {
        return new \moodle_url('/admin/settings.php', ['section' => 'manageqbanks']);
    }

    public function get_settings_section_name() {
        return $this->type . '_' . $this->name;
    }

    public static function get_plugins($type, $typerootdir, $typeclass, $pluginman): array {
        global $CFG;

        $qbank = parent::get_plugins($type, $typerootdir, $typeclass, $pluginman);
        $order = array_keys($qbank);
        $sortedqbanks = [];
        foreach ($order as $qbankname) {
            $sortedqbanks[$qbankname] = $qbank[$qbankname];
        }
        return $sortedqbanks;
    }

    public static function get_enabled_plugins(): ?array {
        global $CFG;
        $pluginmanager = \core_plugin_manager::instance();
        $plugins = $pluginmanager->get_installed_plugins('qbank');

        if (!$plugins) {
            return [];
        }

        $plugins = array_keys($plugins);

        // Filter to return only enabled plugins.
        $enabled = [];
        foreach ($plugins as $plugin) {
            $qbankinfo = $pluginmanager->get_plugin_info('qbank_'.$plugin);
            $qbankavailable = $qbankinfo->get_status();
            if ($qbankavailable === \core_plugin_manager::PLUGIN_STATUS_MISSING) {
                continue;
            }
            $disabled = get_config('qbank_' . $plugin, 'disabled');
            if (empty($disabled)) {
                $enabled[$plugin] = $plugin;
            }
        }
        return $enabled;
    }

    public static function enable_plugin(string $pluginname, int $enabled): bool {
        $haschanged = false;

        $plugin = 'qbank_' . $pluginname;
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
     * Checks if a qbank plugin is ready to be used.
     * It checks the plugin status as well as the plugin is missing or not.
     *
     * @param string $fullpluginname the name of the plugin
     * @return bool
     */
    public static function is_plugin_enabled($fullpluginname): bool {
        $pluginmanager = \core_plugin_manager::instance();
        $qbankinfo = $pluginmanager->get_plugin_info($fullpluginname);
        if (empty($qbankinfo)) {
            return false;
        }
        $qbankavailable = $qbankinfo->get_status();
        if ($qbankavailable === \core_plugin_manager::PLUGIN_STATUS_MISSING ||
                !empty(get_config($fullpluginname, 'disabled'))) {
            return false;
        }
        return true;
    }

    public function load_settings(\part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig): void {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
        /** @var \admin_root $ADMIN */
        $ADMIN = $adminroot; // May be used in settings.php.
        $plugininfo = $this; // Also can be used inside settings.php.

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig) {
            return;
        }

        $section = $this->get_settings_section_name();

        $settings = null;
        if (file_exists($this->full_path('settings.php'))) {
            if ($this->name !== 'columnsortorder') {
                $settings = new \admin_settingpage($section, $this->displayname,
                                    'moodle/site:config', $this->is_enabled() === false);
            }
            include($this->full_path('settings.php')); // This may also set $settings to null.
        }
        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }
}
