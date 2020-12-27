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
 * Core container for calendar events.
 *
 * The purpose of this class is simply to wire together the various
 * implementations of calendar event components to produce a solution
 * to the problems Moodle core wants to solve.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event;

defined('MOODLE_INTERNAL') || die();

use core_calendar\action_factory;
use core_calendar\local\event\data_access\event_vault;
use core_calendar\local\event\entities\action_event;
use core_calendar\local\event\entities\action_event_interface;
use core_calendar\local\event\entities\event_interface;
use core_calendar\local\event\factories\event_factory;
use core_calendar\local\event\mappers\event_mapper;
use core_calendar\local\event\strategies\raw_event_retrieval_strategy;

/**
 * Core container.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class container {
    /**
     * @var event_factory $eventfactory Event factory.
     */
    protected static $eventfactory;

    /**
     * @var event_mapper $eventmapper Event mapper.
     */
    protected static $eventmapper;

    /**
     * @var action_factory $actionfactory Action factory.
     */
    protected static $actionfactory;

    /**
     * @var event_vault $eventvault Event vault.
     */
    protected static $eventvault;

    /**
     * @var raw_event_retrieval_strategy $eventretrievalstrategy Event retrieval strategy.
     */
    protected static $eventretrievalstrategy;

    /**
     * @var \stdClass[] An array of cached courses to use with the event factory.
     */
    protected static $coursecache = array();

    /**
     * @var \stdClass[] An array of cached modules to use with the event factory.
     */
    protected static $modulecache = array();

    /**
     * @var int The requesting user. All capability checks are done against this user.
     */
    protected static $requestinguserid;

    /**
     * Initialises the dependency graph if it hasn't yet been.
     */
    private static function init() {
        if (empty(self::$eventfactory)) {
            self::$actionfactory = new action_factory();
            self::$eventmapper = new event_mapper(
                // The event mapper we return from here needs to know how to
                // make events, so it needs an event factory. However we can't
                // give it the same one as we store and return in the container
                // as that one uses all our plumbing to control event visibility.
                //
                // So we make a new even factory that doesn't do anyting other than
                // return the instance.
                new event_factory(
                    // Never apply actions, simply return.
                    function(event_interface $event) {
                        return $event;
                    },
                    // Never hide an event.
                    function() {
                        return true;
                    },
                    // Never bail out early when instantiating an event.
                    function() {
                        return false;
                    },
                    self::$coursecache,
                    self::$modulecache
                )
            );

            self::$eventfactory = new event_factory(
                [self::class, 'apply_component_provide_event_action'],
                [self::class, 'apply_component_is_event_visible'],
                function ($dbrow) {
                    $requestinguserid = self::get_requesting_user();

                    if (!empty($dbrow->categoryid)) {
                        // This is a category event. Check that the category is visible to this user.
                        $category = \core_course_category::get($dbrow->categoryid, IGNORE_MISSING, true, $requestinguserid);

                        if (empty($category) || !$category->is_uservisible($requestinguserid)) {
                            return true;
                        }
                    }

                    // For non-module events we assume that all checks were done in core_calendar_is_event_visible callback.
                    // For module events we also check that the course module and course itself are visible to the user.
                    if (empty($dbrow->modulename)) {
                        return false;
                    }

                    $instances = get_fast_modinfo($dbrow->courseid, $requestinguserid)->instances;

                    // If modinfo doesn't know about the module, we should ignore it.
                    if (!isset($instances[$dbrow->modulename]) || !isset($instances[$dbrow->modulename][$dbrow->instance])) {
                        return true;
                    }

                    $cm = $instances[$dbrow->modulename][$dbrow->instance];

                    // If the module is not visible to the current user, we should ignore it.
                    // We have to check enrolment here as well because the uservisible check
                    // looks for the "view" capability however some activities (such as Lesson)
                    // have that capability set on the "Authenticated User" role rather than
                    // on "Student" role, which means uservisible returns true even when the user
                    // is no longer enrolled in the course.
                    // So, with the following we are checking -
                    // 1) Only process modules if $cm->uservisible is true.
                    // 2) Only process modules for courses a user has the capability to view OR they are enrolled in.
                    // 3) Only process modules for courses that are visible OR if the course is not visible, the user
                    //    has the capability to view hidden courses.
                    if (!$cm->uservisible) {
                        return true;
                    }

                    $coursecontext = \context_course::instance($dbrow->courseid);
                    if (!$cm->get_course()->visible &&
                            !has_capability('moodle/course:viewhiddencourses', $coursecontext, $requestinguserid)) {
                        return true;
                    }

                    if (!has_capability('moodle/course:view', $coursecontext, $requestinguserid) &&
                            !is_enrolled($coursecontext, $requestinguserid)) {
                        return true;
                    }

                    // Ok, now check if we are looking at a completion event.
                    if ($dbrow->eventtype === \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED) {
                        // Need to have completion enabled before displaying these events.
                        $course = new \stdClass();
                        $course->id = $dbrow->courseid;
                        $completion = new \completion_info($course);

                        return (bool) !$completion->is_enabled($cm);
                    }

                    return false;
                },
                self::$coursecache,
                self::$modulecache
            );
        }

        if (empty(self::$eventvault)) {
            self::$eventretrievalstrategy = new raw_event_retrieval_strategy();
            self::$eventvault = new event_vault(self::$eventfactory, self::$eventretrievalstrategy);
        }
    }

    /**
     * Reset all static caches, called between tests.
     */
    public static function reset_caches() {
        self::$requestinguserid = null;
        self::$eventfactory = null;
        self::$eventmapper = null;
        self::$eventvault = null;
        self::$actionfactory = null;
        self::$eventretrievalstrategy = null;
        self::$coursecache = [];
        self::$modulecache = [];
    }

    /**
     * Gets the event factory.
     *
     * @return event_factory
     */
    public static function get_event_factory() {
        self::init();
        return self::$eventfactory;
    }

    /**
     * Gets the event mapper.
     *
     * @return event_mapper
     */
    public static function get_event_mapper() {
        self::init();
        return self::$eventmapper;
    }

    /**
     * Return an event vault.
     *
     * @return event_vault
     */
    public static function get_event_vault() {
        self::init();
        return self::$eventvault;
    }

    /**
     * Sets the requesting user so that all capability checks are done against this user.
     * Setting the requesting user (hence calling this function) is optional and if you do not so,
     * $USER will be used as the requesting user. However, if you wish to set the requesting user yourself,
     * you should call this function before any other function of the container class is called.
     *
     * @param int $userid The user id.
     * @throws \coding_exception
     */
    public static function set_requesting_user($userid) {
        self::$requestinguserid = $userid;
    }

    /**
     * Returns the requesting user id.
     * It usually is the current user unless it has been set explicitly using set_requesting_user.
     *
     * @return int
     */
    public static function get_requesting_user() {
        global $USER;

        return empty(self::$requestinguserid) ? $USER->id : self::$requestinguserid;
    }

    /**
     * Calls callback 'core_calendar_provide_event_action' from the component responsible for the event
     *
     * If no callback is present or callback returns null, there is no action on the event
     * and it will not be displayed on the dashboard.
     *
     * @param event_interface $event
     * @return action_event|event_interface
     */
    public static function apply_component_provide_event_action(event_interface $event) {
        // Callbacks will get supplied a "legacy" version
        // of the event class.
        $mapper = self::$eventmapper;
        $action = null;
        if ($event->get_component()) {
            $requestinguserid = self::get_requesting_user();
            $legacyevent = $mapper->from_event_to_legacy_event($event);
            // We know for a fact that the the requesting user might be different from the logged in user,
            // but the event mapper is not aware of that.
            if (empty($event->user) && !empty($legacyevent->userid)) {
                $legacyevent->userid = $requestinguserid;
            }

            // Any other event will not be displayed on the dashboard.
            $action = component_callback(
                $event->get_component(),
                'core_calendar_provide_event_action',
                [
                    $legacyevent,
                    self::$actionfactory,
                    $requestinguserid
                ]
            );
        }

        // If we get an action back, return an action event, otherwise
        // continue piping through the original event.
        //
        // If a module does not implement the callback, component_callback
        // returns null.
        return $action ? new action_event($event, $action) : $event;
    }

    /**
     * Calls callback 'core_calendar_is_event_visible' from the component responsible for the event
     *
     * The visibility callback is optional, if not present it is assumed as visible.
     * If it is an actionable event but the get_item_count() returns 0 the visibility
     * is set to false.
     *
     * @param event_interface $event
     * @return bool
     */
    public static function apply_component_is_event_visible(event_interface $event) {
        $mapper = self::$eventmapper;
        $eventvisible = null;
        if ($event->get_component()) {
            $requestinguserid = self::get_requesting_user();
            $legacyevent = $mapper->from_event_to_legacy_event($event);
            // We know for a fact that the the requesting user might be different from the logged in user,
            // but the event mapper is not aware of that.
            if (empty($event->user) && !empty($legacyevent->userid)) {
                $legacyevent->userid = $requestinguserid;
            }

            $eventvisible = component_callback(
                $event->get_component(),
                'core_calendar_is_event_visible',
                [
                    $legacyevent,
                    $requestinguserid
                ]
            );
        }

        // Do not display the event if there is nothing to action.
        if ($event instanceof action_event_interface && $event->get_action()->get_item_count() === 0) {
            return false;
        }

        // Module does not implement the callback, event should be visible.
        if (is_null($eventvisible)) {
            return true;
        }

        return $eventvisible ? true : false;
    }
}
