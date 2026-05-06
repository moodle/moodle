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
 * Multiple checkboxes 2, value stored as string 00101011
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configmulticheckbox2 extends admin_setting_configmulticheckbox {

    /**
     * Returns the setting if set
     *
     * @return mixed null if not set, else an array of set settings
     */
    public function get_setting() {
        $result = $this->config_read($this->name);
        if (is_null($result)) {
            return NULL;
        }
        if (!$this->load_choices()) {
            return NULL;
        }
        $result = str_pad($result, count($this->choices), '0');
        $result = preg_split('//', $result, -1, PREG_SPLIT_NO_EMPTY);
        $setting = array();
        foreach ($this->choices as $key=>$unused) {
            $value = array_shift($result);
            if ($value) {
                $setting[$key] = 1;
            }
        }
        return $setting;
    }

    /**
     * Save setting(s) provided in $data param
     *
     * @param array $data An array of settings to save
     * @return mixed empty string for bad data or bool true=>success, false=>error
     */
    public function write_setting($data) {
        if (!is_array($data)) {
            return ''; // ignore it
        }
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        $result = '';
        foreach ($this->choices as $key=>$unused) {
            if (!empty($data[$key])) {
                $result .= '1';
            } else {
                $result .= '0';
            }
        }
        return $this->config_write($this->name, $result) ? '' : get_string('errorsetting', 'admin');
    }
}
