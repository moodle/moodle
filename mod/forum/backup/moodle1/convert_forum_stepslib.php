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
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 *
 * @package    mod
 * @subpackage forum
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Convert forum
 */
class moodle1_forum_activity_structure_step extends convert_structure_step {
    /**
     * Function that will return the structure to be processed by this convert_step.
     * Must return one array of @convert_path_element elements
     *
     * NOTE: /MOD/ACTIVITYNAME XML path does not actually exist.  The moodle1_converter
     * class automatically transforms the /MOD path to include the activity name.
     */
    protected function define_structure() {
        return array(
            new convert_path_element('forum', '/MOODLE_BACKUP/COURSE/MODULES/MOD/FORUM'),
            // new convert_path_element('foo', '/MOODLE_BACKUP/COURSE/MODULES/MOD/FORUM/FOO'),  // Example of sub-path
        );
    }

    public function convert_forum($data) {
        print_object($data);
    }

    public function convert_foo($data) {
        print_object($data);
    }
}
