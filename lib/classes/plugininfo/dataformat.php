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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 * @package    core
 */
namespace core\plugininfo;

use moodle_url, part_of_admin_tree, admin_settingpage, admin_externalpage, core_plugin_manager;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for dataformats
 *
 * @package    core
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @copyright  2016 Brendan Heywood (brendan@catalyst-au.net)
 */
class dataformat extends base {

    /**
     * Display name
     */
    public function init_display_name() {
        if (!get_string_manager()->string_exists('dataformat', $this->component)) {
            $this->displayname = '[dataformat,' . $this->component . ']';
        } else {
            $this->displayname = get_string('dataformat', $this->component);
        }
    }

    /**
     * Gathers and returns the information about all plugins of the given type
     *
     * @param string $type the name of the plugintype, eg. mod, auth or workshopform
     * @param string $typerootdir full path to the location of the plugin dir
     * @param string $typeclass the name of the actually called class
     * @param core_plugin_manager $pluginman the plugin manager calling this method
     * @return array of plugintype classes, indexed by the plugin name
     */
    public static function get_plugins($type, $typerootdir, $typeclass, $pluginman) {
        global $CFG;
        $formats = parent::get_plugins($type, $typerootdir, $typeclass, $pluginman);

        if (!empty($CFG->dataformat_plugins_sortorder)) {
            $order = explode(',', $CFG->dataformat_plugins_sortorder);
            $order = array_merge(array_intersect($order, array_keys($formats)),
                        array_diff(array_keys($formats), $order));
        } else {
            $order = array_keys($formats);
        }
        $sortedformats = array();
        foreach ($order as $formatname) {
            $sortedformats[$formatname] = $formats[$formatname];
        }
        return $sortedformats;
    }

    /**
     * Finds all enabled plugins, the result may include missing plugins.
     * @return array|null of enabled plugins $pluginname=>$pluginname, null means unknown
     */
    public static function get_enabled_plugins() {
        $enabled = array();
        $plugins = core_plugin_manager::instance()->get_installed_plugins('dataformat');

        if (!$plugins) {
            return array();
        }

        $enabled = array();
        foreach ($plugins as $plugin => $version) {
            $disabled = get_config('dataformat_' . $plugin, 'disabled');
            if (empty($disabled)) {
                $enabled[$plugin] = $plugin;
            }
        }
        return $enabled;
    }

    /**
     * Returns the node name used in admin settings menu for this plugin settings (if applicable)
     *
     * @return null|string node name or null if plugin does not create settings node (default)
     */
    public function get_settings_section_name() {
        return 'dataformatsetting' . $this->name;
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
    public function load_settings(part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
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
        $settings = new admin_settingpage($section, $this->displayname, 'moodle/site:config', $this->is_enabled() === false);
        include($fullpath); // This may also set $settings to null.

        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    /**
     * dataformats can be uninstalled
     *
     * @return bool
     */
    public function is_uninstall_allowed() {
        return true;
    }

    /**
     * Return URL used for management of plugins of this type.
     * @return moodle_url
     */
    public static function get_manage_url() {
        return new moodle_url('/admin/settings.php?section=managedataformats');
    }

}

