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
 * The local_ai_manager course module viewed event.
 *
 * @package     local_ai_manager
 * @category    string
 * @copyright   2024 ISB Bayern
 * @author      Dr. Peter Mayer
 * @author      Dr. Peter Mayer
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ai_manager\plugininfo;

use coding_exception;
use cache_exception;
use core\plugininfo\base, core_plugin_manager;
use dml_exception;

/**
 * The local_ai_manager course module viewed event class.
 *
 * @package     local_ai_manager
 * @category    string
 * @copyright   2024 ISB Bayern
 * @author      Dr. Peter Mayer
 * @author      Dr. Peter Mayer
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aitool extends base {

    /**
     * Finds all enabled plugins, the result may include missing plugins.
     * @return array|null of enabled plugins $pluginname=>$pluginname, null means unknown
     */
    public static function get_enabled_plugins() {
        global $DB;

        $plugins = core_plugin_manager::instance()->get_installed_plugins('aitool');
        if (!$plugins) {
            return [];
        }
        $installed = [];
        foreach ($plugins as $plugin => $version) {
            $installed[] = 'aitool_' . $plugin;
        }

        list($installed, $params) = $DB->get_in_or_equal($installed, SQL_PARAMS_NAMED);
        $disabled = $DB->get_records_select('config_plugins', "plugin $installed AND name = 'disabled'", $params, 'plugin ASC');
        foreach ($disabled as $conf) {
            if (empty($conf->value)) {
                continue;
            }
            list($type, $name) = explode('_', $conf->plugin, 2);
            unset($plugins[$name]);
        }

        $enabled = [];
        foreach ($plugins as $plugin => $version) {
            $enabled[$plugin] = $plugin;
        }

        return $enabled;
    }

    /**
     * Enable a Subplugin.
     *
     * @param string $pluginname
     * @param int $enabled
     * @return bool
     * @throws dml_exception
     * @throws coding_exception
     * @throws cache_exception
     */
    public static function enable_plugin(string $pluginname, int $enabled): bool {
        $haschanged = false;

        $plugin = 'aitool_' . $pluginname;
        $oldvalue = get_config($plugin, 'disabled');
        $disabled = !$enabled;
        // Only set value if there is no config setting or if the value is different from the previous one.
        if ($oldvalue === false || ((bool) $oldvalue != $disabled)) {
            set_config('disabled', $disabled, $plugin);
            $haschanged = true;

            add_to_config_log('disabled', $oldvalue, $disabled, $plugin);
            \core_plugin_manager::reset_caches();
        }

        return $haschanged;
    }

    /**
     * Is uninstallation allowed.
     *
     * @return bool
     */
    public function is_uninstall_allowed() {
        return true;
    }

    /**
     * Get the section name for settings.
     *
     * @return string
     */
    public function get_settings_section_name() {
        return $this->type . '_' . $this->name;
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
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
        $ADMIN = $adminroot; // May be used in settings.php.
        $plugininfo = $this; // Also can be used inside settings.php.

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig || !file_exists($this->full_path('settings.php'))) {
            return;
        }

        $section = $this->get_settings_section_name();

        $settings = new \admin_settingpage($section, $this->displayname, 'moodle/site:config', $this->is_enabled() === false);

        if ($adminroot->fulltree) {
            $shortsubtype = substr($this->type, strlen('assign'));
            include($this->full_path('settings.php'));
        }

        $adminroot->add($this->type . 'plugins', $settings);
    }

    #[\Override]
    public function uninstall(\progress_trace $progress) {
        global $DB;
        $deletedinstanceids = $DB->get_fieldset('local_ai_manager_instance', 'id', ['connector' => $this->name]);
        $DB->delete_records('local_ai_manager_instance', ['connector' => $this->name]);

        if (empty($deletedinstanceids)) {
            return true;
        }
        $sqllike = $DB->sql_like('configkey', '?');
        $params = ['purpose_%_tool'];
        $select = $sqllike;
        [$insql, $inparams] = $DB->get_in_or_equal($deletedinstanceids);
        $params = array_merge($params, $inparams);
        $select = $select . ' AND configvalue ' . $insql;

        $DB->delete_records_select('local_ai_manager_config', $select, $params);
        return true;
    }
}
