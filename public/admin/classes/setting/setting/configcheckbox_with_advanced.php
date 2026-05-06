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
 * Checkbox with an advanced checkbox that controls an additional $name.'_adv' config setting.
 *
 * @copyright 2009 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configcheckbox_with_advanced extends admin_setting_configcheckbox {

    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting ('value'=>string, 'adv'=>bool)
     * @param string $yes value used when checked
     * @param string $no value used when not checked
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $yes='1', $no='0') {
        parent::__construct($name, $visiblename, $description, $defaultsetting['value'], $yes, $no);
        $this->set_advanced_flag_options(admin_setting_flag::ENABLED, !empty($defaultsetting['adv']));
    }

}
