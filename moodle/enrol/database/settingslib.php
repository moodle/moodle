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
 * Database enrolment plugin custom settings.
 *
 * @package    enrol_database
 * @copyright  2013 Darko Miletic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class implements new specialized setting for course categories that are loaded
 * only when required
 * @author Darko Miletic
 *
 */
class enrol_database_admin_setting_category extends admin_setting_configselect {
    public function __construct($name, $visiblename, $description) {
        parent::__construct($name, $visiblename, $description, 1, null);
    }

    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }

        $this->choices = make_categories_options();
        return true;
    }
}
