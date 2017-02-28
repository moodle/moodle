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
 * Abstract event factory.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\factories;

defined('MOODLE_INTERNAL') || die();

use core_calendar\local\event\entities\event;
use core_calendar\local\event\entities\repeat_event_collection;
use core_calendar\local\event\proxies\std_proxy;
use core_calendar\local\event\value_objects\event_description;
use core_calendar\local\event\value_objects\event_times;
use core_calendar\local\interfaces\action_event_factory_interface;
use core_calendar\local\interfaces\event_factory_interface;
use core_calendar\local\interfaces\event_interface;

/**
 * Abstract factory for creating calendar events.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class event_abstract_factory implements event_factory_interface {
    /**
     * @var callable $actioncallbackapplier Function to apply component action callbacks.
     */
    protected $actioncallbackapplier;

    /**
     * @var callable $visibilitycallbackapplier Function to apply component visibility callbacks.
     */
    protected $visibilitycallbackapplier;

    /**
     * @var array Course cache for use with get_course_cached.
     */
    protected $coursecachereference;

    /**
     * Applies component actions to the event.
     *
     * @param event_interface $event The event to be updated.
     * @return event_interface The potentially modified event.
     */
    protected abstract function apply_component_action(event_interface $event);

    /**
     * Exposes the event (or not)
     *
     * @param event_interface $event The event to potentially expose.
     * @return event_interface|null The exposed event or null.
     */
    protected abstract function expose_event(event_interface $event);

    /**
     * Constructor.
     *
     * @param callable $actioncallbackapplier     Function to apply component action callbacks.
     * @param callable $visibilitycallbackapplier Function to apply component visibility callbacks.
     */
    public function __construct(
        callable $actioncallbackapplier,
        callable $visibilitycallbackapplier,
        array &$coursecachereference
    ) {
        $this->actioncallbackapplier = $actioncallbackapplier;
        $this->visibilitycallbackapplier = $visibilitycallbackapplier;
        $this->coursecachereference = &$coursecachereference;
    }

    public function create_instance(\stdClass $dbrow) {
        $course = null;
        $group = null;
        $user = null;
        $module = null;
        $subscription = null;

        if ($dbrow->courseid == 0) {
            $cm = get_coursemodule_from_instance($dbrow->modulename, $dbrow->instance);
            $dbrow->courseid = get_course($cm->course)->id;
        }

        $course = new std_proxy($dbrow->courseid, function($id) {
            return \core_calendar\api::get_course_cached($this->coursecachereference, $id);
        });

        if ($dbrow->groupid) {
            $group = new std_proxy($dbrow->groupid, function($id) {
                return \core_calendar\api::get_group_cached($id);
            });
        }

        if ($dbrow->userid) {
            $user = new std_proxy($dbrow->userid, function($id) {
                global $DB;
                return $DB->get_record('user', ['id' => $id]);
            });
        }

        if ($dbrow->instance && $dbrow->modulename) {
            $modulename = $dbrow->modulename;
            $module = new std_proxy($dbrow->instance, function($id) use ($modulename) {
                return get_coursemodule_from_instance($modulename, $id);
            },
            (object)[
                'modname' => $modulename,
                'instance' => $dbrow->instance
            ]);
        }

        if ($dbrow->subscriptionid) {
            $subscription = new std_proxy($dbrow->subscriptionid, function($id) {
                return \core_calendar\api::get_subscription($id);
            });
        }

        return $this->expose_event(
            $this->apply_component_action(
                new event(
                    $dbrow->id,
                    $dbrow->name,
                    new event_description($dbrow->description, $dbrow->format),
                    $course,
                    $group,
                    $user,
                    new repeat_event_collection($dbrow->id, $this),
                    $module,
                    $dbrow->eventtype,
                    new event_times(
                        (new \DateTimeImmutable())->setTimestamp($dbrow->timestart),
                        (new \DateTimeImmutable())->setTimestamp($dbrow->timestart + $dbrow->timeduration),
                        (new \DateTimeImmutable())->setTimestamp($dbrow->timesort ? $dbrow->timesort : $dbrow->timestart),
                        (new \DateTimeImmutable())->setTimestamp($dbrow->timemodified)
                    ),
                    !empty($dbrow->visible),
                    $subscription
                )
            )
        );
    }
}
