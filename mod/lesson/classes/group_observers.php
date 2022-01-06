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
 * Group observers.
 *
 * @package    mod_lesson
 * @copyright  2015 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lesson;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/lesson/locallib.php');

/**
 * Group observers class.
 *
 * @package    mod_lesson
 * @copyright  2015 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class group_observers {

    /**
     * Flag whether a course reset is in progress or not.
     *
     * @var int The course ID.
     */
    protected static $resetinprogress = false;

    /**
     * A course reset has started.
     *
     * @param \core\event\base $event The event.
     * @return void
     */
    public static function course_reset_started($event) {
        self::$resetinprogress = $event->courseid;
    }

    /**
     * A course reset has ended.
     *
     * @param \core\event\base $event The event.
     * @return void
     */
    public static function course_reset_ended($event) {
        if (!empty(self::$resetinprogress)) {
            if (!empty($event->other['reset_options']['reset_groups_remove'])) {
                lesson_process_group_deleted_in_course($event->courseid);
            }
        }

        self::$resetinprogress = null;
    }

    /**
     * A group was deleted.
     *
     * @param \core\event\base $event The event.
     * @return void
     */
    public static function group_deleted($event) {
        if (!empty(self::$resetinprogress)) {
            // We will take care of that once the course reset ends.
            return;
        }
        lesson_process_group_deleted_in_course($event->courseid, $event->objectid);
    }
}
