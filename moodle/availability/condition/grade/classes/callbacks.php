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
 * Observer handling events.
 *
 * @package availability_grade
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_grade;

defined('MOODLE_INTERNAL') || die();

/**
 * Callbacks handling grade changes (to clear cache).
 *
 * This ought to use the hooks system, but it doesn't exist - calls are
 * hard-coded. (The new event system is not suitable for this type of use.)
 *
 * @package availability_grade
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class callbacks {
    /**
     * A user grade has been updated in gradebook.
     *
     * @param int $userid User ID
     */
    public static function grade_changed($userid) {
        \cache::make('availability_grade', 'scores')->delete($userid);
    }

    /**
     * A grade item has been updated in gradebook.
     *
     * @param int $courseid Course id
     */
    public static function grade_item_changed($courseid) {
        \cache::make('availability_grade', 'items')->delete($courseid);
    }
}
