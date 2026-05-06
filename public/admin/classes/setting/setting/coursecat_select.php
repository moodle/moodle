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
 * Course category selection
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class coursecat_select extends \core_admin\setting\setting\configselect_autocomplete {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct($name, $visiblename, $description, $defaultsetting = 1) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, $choices = null);
    }

    /**
     * Load the available choices for the select box
     *
     * @return bool
     */
    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = \core_course_category::make_categories_list('', 0, ' / ');
        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(coursecat_select::class, \admin_settings_coursecat_select::class);
