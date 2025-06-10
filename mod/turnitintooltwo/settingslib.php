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
 * LDAP enrolment plugin admin setting classes
 *
 * @package    mod_turnitintooltwo
 * @author     John McGettrick
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class admin_setting_configtext_int_only extends admin_setting_configtext {

    /**
     * Config text constructor
     *
     * @param string $name unique ascii name, 'mysetting' for settings in config, 'myplugin/mysetting' for config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param int $size default field size
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $size=null) {
        $this->paramtype = PARAM_INT;
        $this->size = (!is_null($size)) ? $size : 30;

        admin_setting::__construct($name, $visiblename, $description, $defaultsetting);
    }

    public function write_setting($data) {
        $data = trim($data);
        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    public function validate($data) {
        global $PAGE;

        // Don't force the plugin to be fully set up when installing.
        if ($PAGE->pagelayout === 'maintenance' && strlen($data) === 0) {
            return true;
        }
        return parent::validate($data);
    }
}

class admin_setting_config_tii_secret_key extends admin_setting_configpasswordunmask {

    /**
     * Validate data before storage
     * @param string data
     * @return mixed true if ok string if error found
     */
    public function validate($data) {
        global $PAGE;

        // Don't force the plugin to be fully set up when installing.
        if ($PAGE->pagelayout === 'maintenance' && strlen($data) === 0) {
            return true;
        }

        $cleaned = clean_param($data, $this->paramtype);
        if ("$data" === "$cleaned" && strlen($data) > 0) { // Implicit conversion to string is needed to do exact comparison.
            return true;
        } else {
            return get_string('validateerror', 'admin');
        }
    }
}
