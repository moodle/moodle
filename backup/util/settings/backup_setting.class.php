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

    public function add_dependency($obj) {
        if (! $obj instanceof backup_setting) {
            throw new backup_setting_exception('dependency_is_not_backkup_setting');
        }
        // Check the dependency level is >= current level
        if ($obj->get_level() < $this->level) {
            throw new backup_setting_exception('cannot_add_upper_level_dependency');
        }
        parent::add_dependency($obj);
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
}

/*
 * Exception class used by all the @backup_setting stuff
 */
class backup_setting_exception extends base_setting_exception {
}
