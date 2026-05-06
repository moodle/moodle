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

use core_plugin_manager;

/**
 * Defines classes used for plugin info.
 *
 * @package    core
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 */
class dataformat extends base {
    #[\Override]
    public static function plugintype_supports_disabling(): bool {
        return true;
    }

    #[\Override]
    public function init_display_name() {
        if (!get_string_manager()->string_exists('dataformat', $this->component)) {
            $this->displayname = '[dataformat,' . $this->component . ']';
        } else {
            $this->displayname = get_string('dataformat', $this->component);
        }
    }

    /**
     * Given a list of dataformat types, return them sorted according to site configuration (if set)
     *
     * @param string[] $formats List of formats, ['csv', 'pdf', etc]
     * @return string[] List of formats according to configured sort, ['csv', 'odf', etc]
     */
    private static function get_plugins_sortorder(array $formats): array {
        global $CFG;

        if (!empty($CFG->dataformat_plugins_sortorder)) {
            $order = explode(',', $CFG->dataformat_plugins_sortorder);
            $order = array_merge(array_intersect($order, $formats), array_diff($formats, $order));
        } else {
            $order = $formats;
        }

        return $order;
    }

    #[\Override]
    public static function get_plugins($type, $typerootdir, $typeclass, $pluginman) {
        $formats = parent::get_plugins($type, $typerootdir, $typeclass, $pluginman);

        $order = static::get_plugins_sortorder(array_keys($formats));
        $sortedformats = [];
        foreach ($order as $formatname) {
            $sortedformats[$formatname] = $formats[$formatname];
        }
        return $sortedformats;
    }

    #[\Override]
    public static function get_enabled_plugins() {
        $plugins = core_plugin_manager::instance()->get_installed_plugins('dataformat');
        if (!$plugins) {
            return [];
        }

        $order = static::get_plugins_sortorder(array_keys($plugins));
        $enabled = [];
        foreach ($order as $formatname) {
            $disabled = get_config('dataformat_' . $formatname, 'disabled');
            if (empty($disabled)) {
                $enabled[$formatname] = $formatname;
            }
        }
        return $enabled;
    }

    #[\Override]
    public static function enable_plugin(string $pluginname, int $enabled): bool {
        $haschanged = false;

        $plugin = 'dataformat_' . $pluginname;
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

    #[\Override]
    public function get_settings_section_name() {
        return 'dataformatsetting' . $this->name;
    }

    #[\Override]
    public function load_settings(
        \core_admin\setting\tree\part_of_admin_tree $adminroot,
        $parentnodename,
        $hassiteconfig,
    ) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
        /** @var \core_admin\setting\tree\root $ADMIN */
        $ADMIN = $adminroot; // May be used in settings.php.
        $plugininfo = $this; // Also can be used inside settings.php.
        $dataformat = $this;     // Also can be used inside settings.php.

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig) {
            return;
        }
        if (file_exists($this->full_path('settings.php'))) {
            $fullpath = $this->full_path('settings.php');
        } else if (file_exists($this->full_path('dataformatsettings.php'))) {
            $fullpath = $this->full_path('dataformatsettings.php');
        } else {
            return;
        }

        $section = $this->get_settings_section_name();
        $settings = new \core_admin\setting\settingpage\settingpage(
            $section,
            $this->displayname,
            'moodle/site:config',
            $this->is_enabled() === false,
        );
        include($fullpath); // This may also set $settings to null.

        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    #[\Override]
    public function is_uninstall_allowed() {
        // Data formats can be uninstalled.
        return true;
    }

    #[\Override]
    public static function get_manage_url() {
        return new \core\url('/admin/settings.php?section=managedataformats');
    }
}
