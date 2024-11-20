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
 * Extend admin_setting_configtext to validate form data in tincanlaunch global settings
 *
 * @package    mod_tincanlaunch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configtext_mod_tincanlaunch extends admin_setting_configtext {
    /**
     * Saves the setting(s) provided in $data
     *
     * @param array $data An array of data, if not array returns empty str
     * @return mixed empty string on useless data or success, error string if failed
     */
    public function write_setting($data) {
        if ($this->paramtype === PARAM_INT && $data === '') {
            // Do not complain if '' used instead of 0.
            $data = 0;
        }

        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }

        // Make sure there is always a trailing slash on endpoint URLs.
        if ($this->name == 'tincanlaunchlrsendpoint') {
            $data = rtrim($data, '/') . '/';
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }
}
