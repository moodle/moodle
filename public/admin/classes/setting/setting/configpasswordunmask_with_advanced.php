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
 * Password field, allows unmasking of password, with an advanced checkbox that controls an additional $name.'_adv' setting.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2018 Paul Holden (pholden@greenhead.ac.uk)
 */
namespace core_admin\setting\setting;

class configpasswordunmask_with_advanced extends \core_admin\setting\setting\configpasswordunmask {

    /**
     * Constructor
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param array $defaultsetting ('value'=>string, 'adv'=>bool)
     */
    public function __construct($name, $visiblename, $description, $defaultsetting) {
        parent::__construct($name, $visiblename, $description, $defaultsetting['value']);
        $this->set_advanced_flag_options(\core_admin\setting\setting\flag::ENABLED, !empty($defaultsetting['adv']));
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(configpasswordunmask_with_advanced::class, \admin_setting_configpasswordunmask_with_advanced::class);
