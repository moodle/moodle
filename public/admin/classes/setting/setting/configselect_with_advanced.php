<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\setting;

/**
 * Dropdown menu with an advanced checkbox, that controls a additional $name.'_adv' setting.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class configselect_with_advanced extends \core_admin\setting\setting\configselect {
    /**
     * Calls parent::__construct with specific arguments
     *
     * @param string $name The name of the setting
     * @param string $visiblename The visible name of the setting
     * @param string $description The description of the setting
     * @param array $defaultsetting The default setting, with keys 'value' and 'adv'
     * @param array $choices The choices for the dropdown menu
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices) {
        parent::__construct($name, $visiblename, $description, $defaultsetting['value'], $choices);
        $this->set_advanced_flag_options(\core_admin\setting\setting\flag::ENABLED, !empty($defaultsetting['adv']));
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(configselect_with_advanced::class, \admin_setting_configselect_with_advanced::class);
