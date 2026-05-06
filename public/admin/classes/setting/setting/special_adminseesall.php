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
 * Special checkbox for calendar - resets SESSION vars.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class special_adminseesall extends \admin_setting_configcheckbox {
    /**
     * Calls the parent::__construct with default values
     *
     * name =>  calendar_adminseesall
     * visiblename => get_string('adminseesall', 'admin')
     * description => get_string('helpadminseesall', 'admin')
     * defaultsetting => 0
     */
    public function __construct() {
        parent::__construct('calendar_adminseesall', get_string('adminseesall', 'admin'),
            get_string('helpadminseesall', 'admin'), '0');
    }

    /**
     * Stores the setting passed in $data
     *
     * @param mixed gets converted to string for comparison
     * @return string empty string or error message
     */
    public function write_setting($data) {
        global $SESSION;
        return parent::write_setting($data);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(special_adminseesall::class, \admin_setting_special_adminseesall::class);
