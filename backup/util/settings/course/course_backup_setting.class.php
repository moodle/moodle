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
 * Abstract class containing all the common stuff for course backup settings
 *
 * TODO: Finish phpdocs
 */
abstract class course_backup_setting extends backup_setting {

    public function __construct($name, $vtype, $value = null, $visibility = self::VISIBLE, $status = self::NOT_LOCKED) {
        $this->level = self::COURSE_LEVEL;
        parent::__construct($name, $vtype, $value, $visibility, $status);
    }
}
