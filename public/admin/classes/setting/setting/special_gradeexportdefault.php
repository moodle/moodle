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
 * A setting for the default grade export plugin.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_gradeexportdefault extends admin_setting_configselect {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct('gradeexport_default', get_string('gradeexportdefault', 'admin'),
                get_string('configgradeexportdefault', 'admin'), null, null);
    }

    /**
     * Returns the default option
     *
     * @return string default option
     */
    public function get_defaultsetting() {
        $this->load_choices();
        $defaultsetting = parent::get_defaultsetting();
        if (array_key_exists($defaultsetting, $this->choices)) {
            return $defaultsetting;
        } else {
            return array_key_first($this->choices);
        }
    }

    /**
     * Load the available choices for the configselect
     *
     * @return bool always returns true
     */
    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = [];

        if ($plugins = core_component::get_plugin_list('gradeexport')) {
            foreach ($plugins as $plugin => $unused) {
                $this->choices[$plugin] = get_string('pluginname', 'gradeexport_'.$plugin);
            }
        }
        return true;
    }
}
