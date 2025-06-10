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
 * @package   mod_customcert
 * @copyright 2013 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_customcert\plugininfo;

use core\plugininfo\base;

/**
 * Subplugin info class.
 *
 * @package   mod_customcert
 * @copyright 2013 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class customcertelement extends base {

    /**
     * Do not allow users to uninstall these plugins as it could cause customcerts to break.
     *
     * @return bool
     */
    public function is_uninstall_allowed() {
        return false;
    }

    /**
     * Loads plugin settings to the settings tree.
     *
     * @param \part_of_admin_tree $adminroot
     * @param string $parentnodename
     * @param bool $hassiteconfig whether the current user has moodle/site:config capability
     */
    public function load_settings(\part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE;
        $ADMIN = $adminroot;
        $plugininfo = $this;

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig || !file_exists($this->full_path('settings.php'))) {
            return;
        }

        $section = $this->get_settings_section_name();
        $settings = new \admin_settingpage($section, $this->displayname, 'moodle/site:config', false);

        include($this->full_path('settings.php'));
        $ADMIN->add($parentnodename, $settings);
    }

    /**
     * Get the settings section name.
     *
     * @return null|string the settings section name.
     */
    public function get_settings_section_name() {
        if (file_exists($this->full_path('settings.php'))) {
            return 'customcertelement_' . $this->name;
        } else {
            return null;
        }
    }
}
