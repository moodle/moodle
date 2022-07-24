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
 * Internal library of functions for module jitsi
 *
 * All the jitsi specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_jitsi
 * @copyright  2019 Sergio Comerón Sánchez-Paniagua <sergiocomeron@icloud.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Update the calendar entries for this jitsi instance.
 *
 * @param stdClass $jitsi An jitsi object
 * @param cmid $cmid
 */
function jitsi_update_calendar(stdClass $jitsi, $cmid) {
    global $DB, $CFG;

    require_once($CFG->dirroot.'/calendar/lib.php');

    $event = new stdClass();
    $event->eventtype = 'open';
    $event->type = CALENDAR_EVENT_TYPE_STANDARD;

    if ($event->id = $DB->get_field('event', 'id',
            array('modulename' => 'jitsi', 'instance' => $jitsi->id,
            'eventtype' => $event->eventtype))) {
        if ((!empty($jitsi->timeopen)) && ($jitsi->timeopen > 0)) {
            $event->name = get_string('calendarstart', 'jitsi', $jitsi->name);
            $event->timestart = $jitsi->timeopen;
            $event->timesort = $jitsi->timeopen;
            $event->visible = instance_is_visible('jitsi', $jitsi);
            $event->timeduration = 0;

            $calendarevent = calendar_event::load($event->id);
            $calendarevent->update($event);
        } else {
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->delete();
        }

    } else {
        if ((!empty($jitsi->timeopen)) && ($jitsi->timeopen > 0)) {
            $event->name = get_string('calendarstart', 'jitsi', $jitsi->name);
            $event->courseid = $jitsi->course;
            $event->groupid = 0;
            $event->userid = 0;
            $event->modulename = 'jitsi';
            $event->instance = $jitsi->id;
            $event->timestart = $jitsi->timeopen;
            $event->timesort = $jitsi->timeopen;
            $event->visible = instance_is_visible('jitsi', $jitsi);
            $event->timeduration = 0;
            calendar_event::create($event);
        }
    }
    return true;
}
