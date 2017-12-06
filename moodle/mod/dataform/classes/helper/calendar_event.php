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
 * The mod_dataform calendar helper.
 *
 * @package    mod_dataform
 * @copyright  2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\helper;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/calendar/lib.php");

/**
 * Dataform helper for Moodle Calendar management
 */
class calendar_event {
    /**
     *
     */
    public static function update_event_timeavailable($data) {
        global $DB;

        if (!empty($data->timeavailable)) {
            $event = new \stdClass;
            $event->name = $data->name;
            $event->description = format_module_intro('dataform', $data, $data->coursemodule);
            $event->timestart = $data->timeavailable;

            if ($event->id = $DB->get_field('event', 'id', array('modulename' => 'dataform', 'instance' => $data->id))) {
                $calendarevent = \calendar_event::load($event->id);
                $calendarevent->update($event);
            } else {
                $event->courseid = $data->course;
                $event->groupid = 0;
                $event->userid = 0;
                $event->modulename = 'dataform';
                $event->instance = $data->id;
                $event->eventtype = 'available';
                $event->timeduration = 0;
                $event->visible = $DB->get_field('course_modules', 'visible', array('module' => $data->module, 'instance' => $data->id));

                \calendar_event::create($event);
            }
        } else {
            $DB->delete_records('event', array('modulename' => 'dataform', 'instance' => $data->id, 'eventtype' => 'available'));
        }
    }

    /**
     *
     */
    public static function update_event_timedue($data) {
        global $DB;

        if (!empty($data->timedue)) {
            $event = new \stdClass;
            $event->name = $data->name;
            $event->description = format_module_intro('dataform', $data, $data->coursemodule);
            $event->timestart = $data->timedue;

            if ($event->id = $DB->get_field('event', 'id', array('modulename' => 'dataform', 'instance' => $data->id))) {
                $calendarevent = \calendar_event::load($event->id);
                $calendarevent->update($event);
            } else {
                $event->courseid = $data->course;
                $event->groupid = 0;
                $event->userid = 0;
                $event->modulename = 'dataform';
                $event->instance = $data->id;
                $event->eventtype = 'due';
                $event->timeduration = 0;
                $event->visible = $DB->get_field('course_modules', 'visible', array('module' => $data->module, 'instance' => $data->id));

                \calendar_event::create($event);
            }
        } else {
            $DB->delete_records('event', array('modulename' => 'dataform', 'instance' => $data->id, 'eventtype' => 'due'));
        }
    }

}
