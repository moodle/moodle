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

namespace core_adminpresets\local\setting;

/**
 * Reimplemented to store values in course table, not in config or config_plugins.
 *
 * @package          core_adminpresets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adminpresets_admin_setting_sitesettext extends adminpresets_admin_setting_configtext {

    /**
     * Overwritten to store the value in the course table
     *
     * @param bool $name
     * @param mixed $value
     * @return  int|false config_log inserted id or false whenever the new value is the same as old value.
     */
    public function save_value($name = false, $value = null) {
        global $DB;

        // Object values if no arguments.
        if ($value === null) {
            $value = $this->value;
        }
        if (!$name) {
            $name = $this->settingdata->name;
        }

        $sitecourse = $DB->get_record('course', ['id' => 1]);
        $actualvalue = $sitecourse->{$name};

        // If it's the same value skip.
        if ($actualvalue == $value) {
            return false;
        }

        // Plugin name or ''.
        $plugin = $this->settingdata->plugin;
        if ($plugin == 'none' || $plugin == '') {
            $plugin = null;
        }

        // Updating mdl_course.
        $sitecourse->{$name} = $value;
        $DB->update_record('course', $sitecourse);

        return $this->to_log($plugin, $name, $this->value, $actualvalue);
    }
}
