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
     * @var array A list of callbacks to use.
     */
    protected static $callbacks = array();

    /**
     * @var \stdClass[] An array of cached courses to use with the event factory.
     */
    protected static $coursecache = array();

    /**
     * @var \stdClass[] An array of cached modules to use with the event factory.
     */
    protected static $modulecache = array();

    /**
     * Initialises the dependency graph if it hasn't yet been.
     */
    private static function init() {
        if (empty(self::$eventfactory)) {
            // When testing the container's components, we need to make sure
            // the callback implementations in modules are not executed, since
            // we cannot control their output from PHPUnit. To do this we have
            // a set of 'testing' callbacks that the factory can use. This way
            // we know exactly how the factory behaves when being tested.
            $getcallback = function($which) {
                return self::$callbacks[PHPUNIT_TEST ? 'testing' : 'production'][$which];
            };

            self::initcallbacks();
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
                $getcallback('action'),
                $getcallback('visibility'),
                function ($dbrow) {
                    // At present we only have a bail-out check for events in course modules.
                    if (empty($dbrow->modulename)) {
                        return false;
                    }

                    $instances = get_fast_modinfo($dbrow->courseid)->instances;

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
                    $modulecontext = \context_module::instance($cm->id);
                    // A user with the 'moodle/course:view' capability is able to see courses
                    // that they are not a participant in.
                    $canseecourse = (has_capability('moodle/course:view', $modulecontext) || is_enrolled($modulecontext));
                    if (!$cm->uservisible || !$canseecourse) {
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
     * Initialises the callbacks.
     *
     * There are two sets here, one is used during PHPUnit runs.
     * See the comment at the start of the init method for more
     * detail.
     */
    private static function initcallbacks() {
        self::$callbacks = array(
            'testing' => array(
                // Always return an action event.
                'action' => function (event_interface $event) {
                    return new action_event(
                        $event,
                        new \core_calendar\local\event\value_objects\action(
                            'test',
                            new \moodle_url('http://example.com'),
                            420,
                            true
                        ));
                },
                // Always be visible.
                'visibility' => function (event_interface $event) {
                    return true;
                }
            ),
            'production' => array(
                // This function has type event_interface -> event_interface.
                // This is enforced by the event_factory.
                'action' => function (event_interface $event) {
                    // Callbacks will get supplied a "legacy" version
                    // of the event class.
                    $mapper = self::$eventmapper;
                    $action = null;
                    if ($event->get_course_module()) {
                        // TODO MDL-58866 Only activity modules currently support this callback.
                        // Any other event will not be displayed on the dashboard.
                        $action = component_callback(
                            'mod_' . $event->get_course_module()->get('modname'),
                            'core_calendar_provide_event_action',
                            [
                                $mapper->from_event_to_legacy_event($event),
                                self::$actionfactory
                            ]
                        );
                    }

                    // If we get an action back, return an action event, otherwise
                    // continue piping through the original event.
                    //
                    // If a module does not implement the callback, component_callback
                    // returns null.
                    return $action ? new action_event($event, $action) : $event;
                },
                // This function has type event_interface -> bool.
                // This is enforced by the event_factory.
                'visibility' => function (event_interface $event) {
                    $mapper = self::$eventmapper;
                    $eventvisible = null;
                    if ($event->get_course_module()) {
                        // TODO MDL-58866 Only activity modules currently support this callback.
                        $eventvisible = component_callback(
                            'mod_' . $event->get_course_module()->get('modname'),
                            'core_calendar_is_event_visible',
                            [
                                $mapper->from_event_to_legacy_event($event)
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
            ),
        );
    }
}
