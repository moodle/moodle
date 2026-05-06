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
 * Select with an advanced checkbox that controls an additional $name.'_locked' config setting.
 *
 * @copyright 2017 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class configselect_with_lock extends \core_admin\setting\setting\configselect {
    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config,
     *     or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting ('value'=>string, 'locked'=>bool)
     * @param array $choices array of $value=>$label for each selection
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices) {
        parent::__construct($name, $visiblename, $description, $defaultsetting['value'], $choices);
        $this->set_locked_flag_options(\core_admin\setting\setting\flag::ENABLED, !empty($defaultsetting['locked']));
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(configselect_with_lock::class, \admin_setting_configselect_with_lock::class);
