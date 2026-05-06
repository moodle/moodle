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
 * Admin setting that allows a user to pick a behaviour.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_behaviour extends \core_admin\setting\setting\configselect {
    /**
     * Constructor for the question behaviour setting.
     *
     * @param string $name name of config variable
     * @param string $visiblename display name
     * @param string $description description
     * @param string $default default.
     */
    public function __construct($name, $visiblename, $description, $default) {
        parent::__construct($name, $visiblename, $description, $default, null);
    }

    #[\Override]
    public function load_choices() {
        global $CFG;
        require_once($CFG->dirroot . '/question/engine/lib.php');
        $this->choices = \question_engine::get_behaviour_options('');
        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(question_behaviour::class, \admin_setting_question_behaviour::class);
