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
 * Seconds duration setting with an advanced checkbox, that controls a additional
 * $name.'_adv' setting.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2014 The Open University
 */
class admin_setting_configduration_with_advanced extends admin_setting_configduration {
    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config,
     *                     or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised name
     * @param string $description localised long description
     * @param array  $defaultsetting array of int value, and bool whether it is
     *                     is advanced by default.
     * @param int $defaultunit - day, week, etc. (in seconds)
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $defaultunit = 86400) {
        parent::__construct($name, $visiblename, $description, $defaultsetting['value'], $defaultunit);
        $this->set_advanced_flag_options(admin_setting_flag::ENABLED, !empty($defaultsetting['adv']));
    }
}
