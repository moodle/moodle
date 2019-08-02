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
use core_calendar\local\event\exceptions\invalid_callback_exception;
use core_calendar\local\event\proxies\cm_info_proxy;
use core_calendar\local\event\proxies\coursecat_proxy;
use core_calendar\local\event\proxies\std_proxy;
use core_calendar\local\event\value_objects\event_description;
use core_calendar\local\event\value_objects\event_times;
use core_calendar\local\event\entities\event_interface;

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
     * @var array Module cache reference for use with get_module_cached.
     */
    protected $modulecachereference;

    /**
     * @var callable Bail out check for create_instance.
     */
    protected $bailoutcheck;

    /**
     * Applies component actions to the event.
     *
     * @param event_interface $event The event to be updated.
     * @return event_interface The potentially modified event.
     */
    protected abstract function apply_component_action(event_interface $event);

    /**
     * Exposes the event (or not).
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
     * @param callable $bailoutcheck              Function to test if we can return null early.
     * @param array    $coursecachereference      Cache to use with get_course_cached.
     * @param array    $modulecachereference      Cache to use with get_module_cached.
     */
    public function __construct(
        callable $actioncallbackapplier,
        callable $visibilitycallbackapplier,
        callable $bailoutcheck,
        array &$coursecachereference,
        array &$modulecachereference
    ) {
        $this->actioncallbackapplier = $actioncallbackapplier;
        $this->visibilitycallbackapplier = $visibilitycallbackapplier;
        $this->bailoutcheck = $bailoutcheck;
        $this->coursecachereference = &$coursecachereference;
        $this->modulecachereference = &$modulecachereference;
    }

    public function create_instance(\stdClass $dbrow) {
        if ($dbrow->modulename && $dbrow->instance && $dbrow->courseid == 0) {
            // Some events (for example user overrides) may contain module instance but not course id. Find course id.
            $cm = calendar_get_module_cached($this->modulecachereference, $dbrow->modulename, $dbrow->instance);
            $dbrow->courseid = $cm->course;
        }

        $bailcheck = $this->bailoutcheck;
        $bail = $bailcheck($dbrow);

        if (!is_bool($bail)) {
            throw new invalid_callback_exception(
                'Bail check must return true or false'
            );
        }

        if ($bail) {
            return null;
        }

        $category = null;
        $course = null;
        $group = null;
        $user = null;
        $module = null;
        $subscription = null;

        if ($dbrow->modulename && $dbrow->instance) {
            $module = new cm_info_proxy($dbrow->modulename, $dbrow->instance, $dbrow->courseid);
        }

        if ($dbrow->categoryid) {
            $category = new coursecat_proxy($dbrow->categoryid);
        }

        $course = new std_proxy($dbrow->courseid, function($id) {
            return calendar_get_course_cached($this->coursecachereference, $id);
        });

        if ($dbrow->groupid) {
            $group = new std_proxy($dbrow->groupid, function($id) {
                return calendar_get_group_cached($id);
            });
        }

        if ($dbrow->userid) {
            $user = new std_proxy($dbrow->userid, function($id) {
                global $DB;
                return $DB->get_record('user', ['id' => $id]);
            });
        }

        if ($dbrow->subscriptionid) {
            $subscription = new std_proxy($dbrow->subscriptionid, function($id) {
                return calendar_get_subscription($id);
            });
        }

        if (!empty($dbrow->repeatid)) {
            $repeatcollection = new repeat_event_collection($dbrow, $this);
        } else {
            $repeatcollection = null;
        }

        $event = new event(
            $dbrow->id,
            $dbrow->name,
            new event_description($dbrow->description, $dbrow->format),
            $category,
            $course,
            $group,
            $user,
            $repeatcollection,
            $module,
            $dbrow->eventtype,
            new event_times(
                (new \DateTimeImmutable())->setTimestamp($dbrow->timestart),
                (new \DateTimeImmutable())->setTimestamp($dbrow->timestart + $dbrow->timeduration),
                (new \DateTimeImmutable())->setTimestamp($dbrow->timesort ? $dbrow->timesort : $dbrow->timestart),
                (new \DateTimeImmutable())->setTimestamp($dbrow->timemodified)
            ),
            !empty($dbrow->visible),
            $subscription,
            $dbrow->location
        );

        $isactionevent = !empty($dbrow->type) && $dbrow->type == CALENDAR_EVENT_TYPE_ACTION;

        return $isactionevent ? $this->expose_event($this->apply_component_action($event)) : $event;
    }
}
