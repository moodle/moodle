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
 * Selection of one of the recognised countries using the list returned by {@see get_list_of_countries()}.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class country_select extends \core_admin\setting\setting\configselect {
    /** @var bool Whether to include all countries */
    protected $includeall;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param mixed $defaultsetting
     * @param bool $includeall
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $includeall = false) {
        $this->includeall = $includeall;
        parent::__construct($name, $visiblename, $description, $defaultsetting, null);
    }

    #[\Override]
    public function load_choices() {
        global $CFG;
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array_merge(
            ['0' => get_string('choosedots')],
            get_string_manager()->get_list_of_countries($this->includeall)
        );
        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(country_select::class, \admin_settings_country_select::class);
