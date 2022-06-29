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
 * This file contains helper classes and functions for testing.
 *
 * @package core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/calendar/lib.php');

use core_calendar\local\event\entities\action_event;
use core_calendar\local\event\entities\event;
use core_calendar\local\event\entities\repeat_event_collection;
use core_calendar\local\event\proxies\std_proxy;
use core_calendar\local\event\proxies\coursecat_proxy;
use core_calendar\local\event\proxies\cm_info_proxy;
use core_calendar\local\event\value_objects\action;
use core_calendar\local\event\value_objects\event_description;
use core_calendar\local\event\value_objects\event_times;
use core_calendar\local\event\factories\event_factory_interface;

/**
 * Create a calendar event with the given properties.
 *
 * @param array $properties The properties to set on the event
 * @return \calendar_event
 */
function create_event($properties) {
    $record = new \stdClass();
    $record->name = 'event name';
    $record->eventtype = 'site';
    $record->repeat = 0;
    $record->repeats = 0;
    $record->timestart = time();
    $record->timeduration = 0;
    $record->timesort = 0;
    $record->type = CALENDAR_EVENT_TYPE_STANDARD;
    $record->courseid = 0;
    $record->categoryid = 0;

    foreach ($properties as $name => $value) {
        $record->$name = $value;
    }

    $event = new \calendar_event($record);
    return $event->create($record);
}

/**
 * Helper function to create a x number of events for each event type.
 *
 * @param int $quantity The quantity of events to be created.
 * @return array List of created events.
 */
function create_standard_events(int $quantity): array {
    $types = ['site', 'category', 'course', 'group', 'user'];

    $events = [];
    foreach ($types as $eventtype) {
        // Create five events of each event type.
        for ($i = 0; $i < $quantity; $i++) {
            $events[] = create_event(['eventtype' => $eventtype]);
        }
    }

    return $events;
}

/**
 * Helper function to create an action event.
 *
 * @param array $data The event data.
 * @return bool|calendar_event
 */
function create_action_event(array $data) {
    global $CFG;

    require_once($CFG->dirroot . '/calendar/lib.php');

    if (!isset($data['modulename']) || !isset($data['instance'])) {
        throw new coding_exception('Module and instance should be specified when creating an action event.');
    }

    $isuseroverride = isset($data->priority) && $data->priority == CALENDAR_EVENT_USER_OVERRIDE_PRIORITY;
    if ($isuseroverride) {
        if (!in_array($data['modulename'], ['assign', 'lesson', 'quiz'])) {
            throw new coding_exception('Only assign, lesson and quiz modules supports overrides');
        }
    }

    $event = array_merge($data, [
        'eventtype' => isset($data['eventtype']) ? $data['eventtype'] : 'open',
        'courseid' => isset($data['courseid']) ? $data['courseid'] : 0,
        'instance' => $data['instance'],
        'modulename' => $data['modulename'],
        'type' => CALENDAR_EVENT_TYPE_ACTION,
    ]);

    return create_event($event);
}

/**
 * Helper function to create an user override calendar event.
 *
 * @param string $modulename The modulename.
 * @param int $instanceid The instance id.
 * @param int $userid The user id.
 * @return calendar_event|false
 */
function create_user_override_event(string $modulename, int $instanceid, int $userid) {
    if (!isset($userid)) {
        throw new coding_exception('Must specify userid when creating a user override.');
    }

    return create_action_event([
        'modulename' => $modulename,
        'instance' => $instanceid,
        'userid' => $userid,
        'priority' => CALENDAR_EVENT_USER_OVERRIDE_PRIORITY,
    ]);
}

/**
 * Helper function to create an group override calendar event.
 *
 * @param string $modulename The modulename.
 * @param int $instanceid The instance id.
 * @param int $courseid The course id.
 * @param int $groupid The group id.
 * @return calendar_event|false
 */
function create_group_override_event(string $modulename, int $instanceid, int $courseid, int $groupid) {
    if (!isset($groupid)) {
        throw new coding_exception('Must specify groupid when creating a group override.');
    }

    return create_action_event([
        'groupid' => $groupid,
        'courseid' => $courseid,
        'modulename' => $modulename,
        'instance' => $instanceid,
    ]);
}

/**
 * A test factory that will create action events.
 *
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class action_event_test_factory implements event_factory_interface {

    /**
     * @var callable $callback.
     */
    private $callback;

    /**
     * A test factory that will create action events. The factory accepts a callback
     * that will be used to determine if the event should be returned or not.
     *
     * The callback will be given the event and should return true if the event
     * should be returned and false otherwise.
     *
     * @param callable $callback The callback.
     */
    public function __construct($callback = null) {
        $this->callback = $callback;
    }

    public function create_instance(\stdClass $record) {
        $module = null;
        $subscription = null;

        if ($record->instance && $record->modulename) {
            $module = new cm_info_proxy($record->instance, $record->modulename, $record->courseid);
        }

        if ($record->subscriptionid) {
            $subscription = new std_proxy($record->subscriptionid, function($id) {
                return (object)['id' => $id];
            });
        }

        $event = new event(
            $record->id,
            $record->name,
            new event_description($record->description, $record->format),
            new coursecat_proxy($record->categoryid),
            new std_proxy($record->courseid, function($id) {
                $course = new \stdClass();
                $course->id = $id;
                return $course;
            }),
            new std_proxy($record->groupid, function($id) {
                $group = new \stdClass();
                $group->id = $id;
                return $group;
            }),
            new std_proxy($record->userid, function($id) {
                $user = new \stdClass();
                $user->id = $id;
                return $user;
            }),
            !empty($record->repeatid) ? new repeat_event_collection($record, $this) : null,
            $module,
            $record->eventtype,
            new event_times(
                (new \DateTimeImmutable())->setTimestamp($record->timestart),
                (new \DateTimeImmutable())->setTimestamp($record->timestart + $record->timeduration),
                (new \DateTimeImmutable())->setTimestamp($record->timesort ? $record->timesort : $record->timestart),
                (new \DateTimeImmutable())->setTimestamp($record->timemodified),
                (new \DateTimeImmutable())->setTimestamp($record->timesort ? usergetmidnight($record->timesort) : 0)
            ),
            !empty($record->visible),
            $subscription,
            $record->location,
            !empty($record->component) ? $record->component : null
        );

        $action = new action(
            'Test action',
            new \moodle_url('/'),
            1,
            true
        );

        $actionevent = new action_event($event, $action);

        if ($callback = $this->callback) {
            return $callback($actionevent) ? $actionevent : false;
        } else {
            return $actionevent;
        }
    }
}
