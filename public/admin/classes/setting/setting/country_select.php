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
 * Selection of one of the recognised countries using the list
 * returned by {@link get_list_of_countries()}.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class country_select extends \admin_setting_configselect {
    protected $includeall;
    public function __construct($name, $visiblename, $description, $defaultsetting, $includeall=false) {
        $this->includeall = $includeall;
        parent::__construct($name, $visiblename, $description, $defaultsetting, null);
    }

    /**
     * Lazy-load the available choices for the select box
     */
    public function load_choices() {
        global $CFG;
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array_merge(
                array('0' => get_string('choosedots')),
                get_string_manager()->get_list_of_countries($this->includeall));
        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(country_select::class, \admin_settings_country_select::class);
