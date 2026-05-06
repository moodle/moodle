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
 * Special select for settings that are altered in setup.php and can not be altered on the fly
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class special_selectsetup extends \core_admin\setting\setting\configselect {
    /**
     * Reads the setting directly from the database
     *
     * @return mixed
     */
    public function get_setting() {
    // read directly from db!
        return get_config(NULL, $this->name);
    }

    /**
     * Save the setting passed in $data
     *
     * @param string $data The setting to save
     * @return string empty or error message
     */
    public function write_setting($data) {
        global $CFG;
        // do not change active CFG setting!
        $current = $CFG->{$this->name};
        $result = parent::write_setting($data);
        $CFG->{$this->name} = $current;
        return $result;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(special_selectsetup::class, \admin_setting_special_selectsetup::class);
