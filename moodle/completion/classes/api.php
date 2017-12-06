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
 * Contains class containing completion API.
 *
 * @package    core_completion
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_completion;

defined('MOODLE_INTERNAL') || die();

/**
 * Class containing completion API.
 *
 * @package    core_completion
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * @var string The completion expected on event.
     */
    const COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED = 'expectcompletionon';

    /**
     * Creates, updates or deletes an event for the expected completion date.
     *
     * @param int $cmid The course module id
     * @param string $modulename The name of the module (eg. assign, quiz)
     * @param \stdClass|int $instanceorid The instance object or ID.
     * @param int|null $completionexpectedtime The time completion is expected, null if not set
     * @return bool
     */
    public static function update_completion_date_event($cmid, $modulename, $instanceorid, $completionexpectedtime) {
        global $CFG, $DB;

        // Required for calendar constant CALENDAR_EVENT_TYPE_ACTION.
        require_once($CFG->dirroot . '/calendar/lib.php');

        $instance = null;
        if (is_object($instanceorid)) {
            $instance = $instanceorid;
        } else {
            $instance = $DB->get_record($modulename, array('id' => $instanceorid), '*', MUST_EXIST);
        }
        $course = get_course($instance->course);

        $completion = new \completion_info($course);

        // Can not create/update an event if completion is disabled.
        if (!$completion->is_enabled() && $completionexpectedtime !== null) {
            return true;
        }

        // Create the \stdClass we will be using for our language strings.
        $lang = new \stdClass();
        $lang->modulename = get_string('pluginname', $modulename);
        $lang->instancename = $instance->name;

        // Create the calendar event.
        $event = new \stdClass();
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = self::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED;
        if ($event->id = $DB->get_field('event', 'id', array('modulename' => $modulename,
                'instance' => $instance->id, 'eventtype' => $event->eventtype))) {
            if ($completionexpectedtime !== null) {
                // Calendar event exists so update it.
                $event->name = get_string('completionexpectedfor', 'completion', $lang);
                $event->description = format_module_intro($modulename, $instance, $cmid);
                $event->timestart = $completionexpectedtime;
                $event->timesort = $completionexpectedtime;
                $event->visible = instance_is_visible($modulename, $instance);
                $event->timeduration = 0;

                $calendarevent = \calendar_event::load($event->id);
                $calendarevent->update($event);
            } else {
                // Calendar event is no longer needed.
                $calendarevent = \calendar_event::load($event->id);
                $calendarevent->delete();
            }
        } else {
            // Event doesn't exist so create one.
            if ($completionexpectedtime !== null) {
                $event->name = get_string('completionexpectedfor', 'completion', $lang);
                $event->description = format_module_intro($modulename, $instance, $cmid);
                $event->courseid = $instance->course;
                $event->groupid = 0;
                $event->userid = 0;
                $event->modulename = $modulename;
                $event->instance = $instance->id;
                $event->timestart = $completionexpectedtime;
                $event->timesort = $completionexpectedtime;
                $event->visible = instance_is_visible($modulename, $instance);
                $event->timeduration = 0;

                \calendar_event::create($event);
            }
        }

        return true;
    }
}
