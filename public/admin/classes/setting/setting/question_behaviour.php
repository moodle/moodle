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
 * Admin setting that allows a user to pick a behaviour.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_question_behaviour extends admin_setting_configselect {
    /**
     * @param string $name name of config variable
     * @param string $visiblename display name
     * @param string $description description
     * @param string $default default.
     */
    public function __construct($name, $visiblename, $description, $default) {
        parent::__construct($name, $visiblename, $description, $default, null);
    }

    /**
     * Load list of behaviours as choices
     * @return bool true => success, false => error.
     */
    public function load_choices() {
        global $CFG;
        require_once($CFG->dirroot . '/question/engine/lib.php');
        $this->choices = question_engine::get_behaviour_options('');
        return true;
    }
}
