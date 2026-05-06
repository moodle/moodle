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
 * Dropdown menu with an advanced checkbox, that controls a additional $name.'_adv' setting.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configselect_with_advanced extends admin_setting_configselect {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices) {
        parent::__construct($name, $visiblename, $description, $defaultsetting['value'], $choices);
        $this->set_advanced_flag_options(admin_setting_flag::ENABLED, !empty($defaultsetting['adv']));
    }

}
