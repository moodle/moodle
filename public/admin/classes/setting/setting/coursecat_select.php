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
 * Course category selection
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursecat_select extends \core_admin\setting\setting\configselect_autocomplete {
    /**
     * Calls parent::__construct with specific arguments
     *
     * @param string $name The name of the setting
     * @param string $visiblename The visible name of the setting
     * @param string $description The description of the setting
     * @param int $defaultsetting The default setting value
     */
    public function __construct($name, $visiblename, $description, $defaultsetting = 1) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, $choices = null);
    }

    #[\Override]
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
