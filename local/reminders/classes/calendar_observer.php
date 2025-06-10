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
 * Class to invoke corresponding method implementations.
 *
 * @package    local_reminders
 * @subpackage reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_reminders;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/reminders/lib.php');

/**
 * Calendar event observer class.
 *
 * @package local_reminders
 * @copyright 2012 Isuru Madushanka Weerarathna
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calendar_observer {

    /**
     * Calls when calendar event updated.
     *
     * @param \core\event\calendar_event_updated $event updated event.
     * @return void.
     */
    public static function calendar_event_updated($event) {
        when_calendar_event_updated($event, REMINDERS_CALENDAR_EVENT_UPDATED);
    }

    /**
     * Calls when calendar event removed.
     *
     * @param \core\event\calendar_event_deleted $event deleted event.
     * @return void.
     */
    public static function calendar_event_removed($event) {
        when_calendar_event_updated($event, REMINDERS_CALENDAR_EVENT_REMOVED);
    }

    /**
     * Calls when calendar event added.
     *
     * @param \core\event\calendar_event_created $event added event.
     * @return void.
     */
    public static function calendar_event_added($event) {
        when_calendar_event_updated($event, REMINDERS_CALENDAR_EVENT_ADDED);
    }

}
