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
use core_calendar\local\event\factories\action_event_factory;
use core_calendar\local\event\factories\event_factory;
use core_calendar\local\event\mappers\event_mapper;
use core_calendar\local\interfaces\event_interface;

/**
 * Core container.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_container {
    /**
     * @var event_factory $eventfactory Event factory.
     */
    protected static $eventfactory;

    /**
     * @var action_event_factory $actioneventfactory Action event factory.
     */
    protected static $actioneventfactory;

    /**
     * @var event_mapper_interface $eventmapper Event mapper.
     */
    protected static $eventmapper;

    /**
     * @var action_factory $actionfactory Action factory.
     */
    protected static $actionfactory;

    /**
     * Initialises the dependency graph if it hasn't yet been.
     */
    private static function init() {
        if (empty(self::$eventfactory)) {
            self::$actionfactory = new action_factory();
            self::$actioneventfactory = new action_event_factory();
            self::$eventmapper = new event_mapper(
                new event_factory(
                    function(event_interface $event) {
                        return $event;
                    }
                )
            );
            self::$eventfactory = new event_factory(
                function(event_interface $event) {
                    $mapper = self::$eventmapper;
                    $action = component_callback(
                        'mod_' . $event->get_course_module()->get('modname'),
                        'core_calendar_provide_event_action',
                        [
                            $mapper->from_event_to_legacy_event($event),
                            self::$actionfactory
                        ]
                    );

                    return $action ? self::$actioneventfactory->create_instance($event, $action) : $event;
                }
            );
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
     * Gets the event mapper
     *
     * @return event_mapper_interface
     */
    public static function get_event_mapper() {
        self::init();
        return self::$eventmapper;
    }
}
