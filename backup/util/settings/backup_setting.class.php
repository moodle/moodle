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
 * @package    moodlecore
 * @subpackage backup-settings
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This abstract class defines one backup_setting
 *
 * TODO: Finish phpdocs
 */
abstract class backup_setting extends base_setting implements checksumable {

    // Some constants defining levels of setting
    const ROOT_LEVEL     = 1;
    const COURSE_LEVEL   = 5;
    const SECTION_LEVEL  = 9;
    const ACTIVITY_LEVEL = 13;

    protected $level;  // level of the setting

    public function get_level() {
        return $this->level;
    }

    public function add_dependency(backup_setting $dependentsetting, $type=setting_dependency::DISABLED_VALUE, $options=array()) {
        // Check the dependency level is >= current level
        if ($dependentsetting->get_level() < $this->level) {
            throw new backup_setting_exception('cannot_add_upper_level_dependency');
        }
        parent::add_dependency($dependentsetting, $type, $options);
    }

// checksumable interface methods

    public function calculate_checksum() {
        // Checksum is a simple md5 hash of name, value, level
        // Not following dependencies at all. Each setting will
        // calculate its own checksum
        return md5($this->name . '-' . $this->value . '-' . $this->level);
    }

    public function is_checksum_correct($checksum) {
        return $this->calculate_checksum() === $checksum;
    }

    public function get_dependencies() {
        return $this->dependencies;
    }

    public function get_ui_name() {
        return $this->uisetting->get_name();
    }

    public function get_ui_type() {
        return $this->uisetting->get_type();
    }
}

/*
 * Exception class used by all the @backup_setting stuff
 */
class backup_setting_exception extends base_setting_exception {
}
