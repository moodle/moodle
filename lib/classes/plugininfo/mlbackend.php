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
 * @copyright  2017 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\plugininfo;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for analytics machine learning backend plugins
 *
 * @package    core
 * @copyright  2017 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mlbackend extends base {

    /**
     * Is uninstall allowed or not.
     *
     * @return bool
     */
    public function is_uninstall_allowed() {

        return !\core_analytics\manager::is_mlbackend_used('mlbackend_' . $this->name);
    }

    /**
     * Returns the node name used in admin settings menu for this plugin settings (if applicable).
     *
     * @return null|string node name or null if plugin does not create settings node (default)
     */
    public function get_settings_section_name() {
        return 'mlbackendsettings' . $this->name;
    }

    /**
     * Load the global settings for a particular availability plugin (if there are any)
     *
     * @param  \part_of_admin_tree $adminroot
     * @param  string $parentnodename
     * @param  bool $hassiteconfig
     * @return void
     */
    public function load_settings(\part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
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
            $settings = new \admin_settingpage($section, $this->displayname, 'moodle/site:config', $this->is_enabled() === false);
            include($this->full_path('settings.php')); // This may also set $settings to null.
        }
        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }
}
