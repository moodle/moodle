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
 * Text field with an advanced checkbox, that controls a additional $name.'_adv' setting.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class configtext_with_advanced extends \core_admin\setting\setting\configtext {
    /**
     * Constructor.
     *
     * @param string $name A unique ascii name for the setting.
     *      Either 'mysetting' for core settings, or 'myplugin/mysetting' for those belonging to a plugin.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting ('value'=>string, '__construct'=>bool)
     * @param mixed $paramtype int means PARAM_XXX type, string is a allowed format in regex
     * @param int|null $size default field size
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $paramtype = PARAM_RAW, $size = null) {
        parent::__construct($name, $visiblename, $description, $defaultsetting['value'], $paramtype, $size);
        $this->set_advanced_flag_options(\core_admin\setting\setting\flag::ENABLED, !empty($defaultsetting['adv']));
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(configtext_with_advanced::class, \admin_setting_configtext_with_advanced::class);
