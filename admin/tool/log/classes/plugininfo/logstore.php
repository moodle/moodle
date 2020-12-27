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
 * Subplugin info class.
 *
 * @package   tool_log
 * @copyright 2013 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_log\plugininfo;

use core\plugininfo\base, moodle_url, part_of_admin_tree, admin_settingpage;

defined('MOODLE_INTERNAL') || die();

/**
 * Plugin info class for logging store plugins.
 */
class logstore extends base {

    public function is_enabled() {
        $enabled = get_config('tool_log', 'enabled_stores');
        if (!$enabled) {
            return false;
        }

        $enabled = array_flip(explode(',', $enabled));
        return isset($enabled['logstore_' . $this->name]);
    }

    public function get_settings_section_name() {
        return 'logsetting' . $this->name;
    }

    public function load_settings(part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
        $ADMIN = $adminroot; // May be used in settings.php.
        $section = $this->get_settings_section_name();

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig or !file_exists($this->full_path('settings.php'))) {
            return;
        }

        $settings = new admin_settingpage($section, $this->displayname, 'moodle/site:config', $this->is_enabled() === false);
        include($this->full_path('settings.php'));

        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    public static function get_manage_url() {
        return new moodle_url('/admin/settings.php', array('section' => 'managelogging'));
    }

    public function is_uninstall_allowed() {
        return true;
    }

    public function uninstall_cleanup() {
        $enabled = get_config('tool_log', 'enabled_stores');
        if ($enabled) {
            $enabled = array_flip(explode(',', $enabled));
            unset($enabled['logstore_' . $this->name]);
            $enabled = array_flip($enabled);
            set_config('enabled_stores', implode(',', $enabled), 'tool_log');
        }

        parent::uninstall_cleanup();
    }
}
