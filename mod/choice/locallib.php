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
 * Internal library of functions for choice module.
 *
 * All the choice specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package   mod_choice
 * @copyright 2016 Stephen Bourget
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * This creates new calendar events given as timeopen and timeclose by $choice.
 *
 * @param stdClass $choice
 * @return void
 */
function choice_set_events($choice) {
    global $DB, $CFG;

    require_once($CFG->dirroot.'/calendar/lib.php');

    // Get CMID if not sent as part of $choice.
    if (!isset($choice->coursemodule)) {
        $cm = get_coursemodule_from_instance('choice', $choice->id, $choice->course);
        $choice->coursemodule = $cm->id;
    }

    // Choice start calendar events.
    $event = new stdClass();
    $event->eventtype = CHOICE_EVENT_TYPE_OPEN;
    // The CHOICE_EVENT_TYPE_OPEN event should only be an action event if no close time is specified.
    $event->type = empty($choice->timeclose) ? CALENDAR_EVENT_TYPE_ACTION : CALENDAR_EVENT_TYPE_STANDARD;
    if ($event->id = $DB->get_field('event', 'id',
            array('modulename' => 'choice', 'instance' => $choice->id, 'eventtype' => $event->eventtype))) {
        if ((!empty($choice->timeopen)) && ($choice->timeopen > 0)) {
            // Calendar event exists so update it.
            $event->name         = get_string('calendarstart', 'choice', $choice->name);
            $event->description  = format_module_intro('choice', $choice, $choice->coursemodule);
            $event->timestart    = $choice->timeopen;
            $event->timesort     = $choice->timeopen;
            $event->visible      = instance_is_visible('choice', $choice);
            $event->timeduration = 0;
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->update($event);
        } else {
            // Calendar event is on longer needed.
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->delete();
        }
    } else {
        // Event doesn't exist so create one.
        if ((!empty($choice->timeopen)) && ($choice->timeopen > 0)) {
            $event->name         = get_string('calendarstart', 'choice', $choice->name);
            $event->description  = format_module_intro('choice', $choice, $choice->coursemodule);
            $event->courseid     = $choice->course;
            $event->groupid      = 0;
            $event->userid       = 0;
            $event->modulename   = 'choice';
            $event->instance     = $choice->id;
            $event->timestart    = $choice->timeopen;
            $event->timesort     = $choice->timeopen;
            $event->visible      = instance_is_visible('choice', $choice);
            $event->timeduration = 0;
            calendar_event::create($event);
        }
    }

    // Choice end calendar events.
    $event = new stdClass();
    $event->type = CALENDAR_EVENT_TYPE_ACTION;
    $event->eventtype = CHOICE_EVENT_TYPE_CLOSE;
    if ($event->id = $DB->get_field('event', 'id',
            array('modulename' => 'choice', 'instance' => $choice->id, 'eventtype' => $event->eventtype))) {
        if ((!empty($choice->timeclose)) && ($choice->timeclose > 0)) {
            // Calendar event exists so update it.
            $event->name         = get_string('calendarend', 'choice', $choice->name);
            $event->description  = format_module_intro('choice', $choice, $choice->coursemodule);
            $event->timestart    = $choice->timeclose;
            $event->timesort     = $choice->timeclose;
            $event->visible      = instance_is_visible('choice', $choice);
            $event->timeduration = 0;
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->update($event);
        } else {
            // Calendar event is on longer needed.
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->delete();
        }
    } else {
        // Event doesn't exist so create one.
        if ((!empty($choice->timeclose)) && ($choice->timeclose > 0)) {
            $event->name         = get_string('calendarend', 'choice', $choice->name);
            $event->description  = format_module_intro('choice', $choice, $choice->coursemodule);
            $event->courseid     = $choice->course;
            $event->groupid      = 0;
            $event->userid       = 0;
            $event->modulename   = 'choice';
            $event->instance     = $choice->id;
            $event->timestart    = $choice->timeclose;
            $event->timesort     = $choice->timeclose;
            $event->visible      = instance_is_visible('choice', $choice);
            $event->timeduration = 0;
            calendar_event::create($event);
        }
    }
}
