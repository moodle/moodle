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
 * Special class for register auth selection
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_special_registerauth extends admin_setting_configselect {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct('registerauth', get_string('selfregistration', 'auth'), get_string('selfregistration_help', 'auth'), '', null);
    }

    /**
     * Returns the default option
     *
     * @return string empty or default option
     */
    public function get_defaultsetting() {
        $this->load_choices();
        $defaultsetting = parent::get_defaultsetting();
        if (array_key_exists($defaultsetting, $this->choices)) {
            return $defaultsetting;
        } else {
            return '';
        }
    }

    /**
     * Loads the possible choices for the array
     *
     * @return bool always returns true
     */
    public function load_choices() {
        global $CFG;

        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array();
        $this->choices[''] = get_string('disable');

        $authentication = \core\di::get(\core\authentication::class);
        $authsenabled = $authentication->get_enabled_plugins();

        foreach ($authsenabled as $auth) {
            $authplugin = $authentication->get_plugin($auth);
            if (!$authplugin->can_signup()) {
                continue;
            }
            // Get the auth title (from core or own auth lang files)
            $authtitle = $authplugin->get_title();
            $this->choices[$auth] = $authtitle;
        }
        return true;
    }
}
