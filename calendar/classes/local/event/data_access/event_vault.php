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
 * Event vault class
 *
 * @package    core_calendar
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\data_access;

use core_calendar\local\event\exceptions\limit_invalid_parameter_exception;
use core_calendar\local\event\exceptions\timesort_invalid_parameter_exception;
use core_calendar\local\interfaces\action_event_interface;
use core_calendar\local\interfaces\event_interface;
use core_calendar\local\interfaces\event_factory_interface;
use core_calendar\local\interfaces\event_vault_interface;

/**
 * This class will handle interacting with the database layer to retrieve
 * the records. This is required to house the complex logic required for
 * pagination because it's not a one-to-one mapping between database records
 * and users.
 *
 * This is a repository. It's called a vault to reduce confusion because
 * Moodle has already taken the name repository. Vault is cooler anyway.
 *
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_vault implements event_vault_interface {

    private $factory;

    /**
     * Create an event vault.
     *
     * @param event_factory_interface $factory An event factory
     */
    public function __construct(event_factory_interface $factory) {
        $this->factory = $factory;
    }

    /**
     * Retrieve an event for the given id.
     *
     * @param int $id The event id
     * @return event_interface
     */
    public function get_event_by_id($id) {
        global $DB;

        if ($record = $DB->get_record('event', ['id' => $id])) {
            return $this->transform_from_database_record($record);
        } else {
            return false;
        }
    }

    /**
     * Retrieve an array of events for the given user and time constraints.
     *
     * @param \stdClass            $user         The user for whom the events belong
     * @param int|null             $timesortfrom Events with timesort from this value (inclusive)
     * @param int|null             $timesortto   Events with timesort until this value (inclusive)
     * @param event_interface|null $afterevent   Only return events after this one
     * @param int                  $limitnum     Return at most this number of events
     * @throws timesort_invalid_parameter_exception
     * @throws limit_invalid_parameter_exception
     * @return action_event_interface
     */
    public function get_action_events_by_timesort(
        \stdClass $user,
        $timesortfrom = null,
        $timesortto = null,
        event_interface $afterevent = null,
        $limitnum = 20
    ) {
        global $DB;

        if (is_null($timesortfrom) && is_null($timesortto)) {
            throw new timesort_invalid_parameter_exception("Must provide a timesort to and/or from value");
        }

        if ($limitnum < 1 || $limitnum > 50) {
            throw new limit_invalid_parameter_exception("Limit must be between 1 and 50 (inclusive)");
        }

        $params = ['type' => CALENDAR_EVENT_TYPE_ACTION];
        $where = ['type = :type'];

        if ($timesortfrom) {
            $where[] = 'timesort >= :timesortfrom';
            $params['timesortfrom'] = $timesortfrom;
        }

        if ($timesortto) {
            $where[] = 'timesort <= :timesortto';
            $params['timesortto'] = $timesortto;
        }

        if (!is_null($afterevent)) {
            $where[] = 'id > :id';
            $params['id'] = $afterevent->get_id();
        }

        $sql = sprintf("SELECT * FROM {event} WHERE %s ORDER BY timesort ASC, id ASC",
                       implode(' AND ', $where));

        $offset = 0;
        $events = [];
        // We need to continue to pull records from the database until we reach
        // the requested amount of events because not all records in the database
        // will be visible for the current user.
        while ($records = array_values($DB->get_records_sql($sql, $params, $offset, $limitnum))) {
            foreach ($records as $record) {
                if ($event = $this->transform_from_database_record($record)) {
                    if ($event instanceof action_event_interface) {
                        $events[] = $event;
                    }

                    if (count($events) == $limitnum) {
                        // We've got all of the events so break both loops.
                        break 2;
                    }
                }
            }

            $offset += $limitnum;
        }

        return $events;
    }

    /**
     * Retrieve an array of events for the given user filtered by the course and time constraints.
     *
     * @param \stdClass            $user         The user for whom the events belong
     * @param array                $courses      The courses to filter by
     * @param int|null             $timesortfrom Events with timesort from this value (inclusive)
     * @param int|null             $timesortto   Events with timesort until this value (inclusive)
     * @param event_interface|null $afterevent   Only return events after this one
     * @param int                  $limitnum     Return at most this number of events
     * @return action_event_interface
     */
    public function get_action_events_by_course(
        \stdClass $user,
        \stdClass $course,
        int $timesortfrom = null,
        int $timesortto = null,
        event_interface $afterevent = null,
        int $limitnum = 20
    ) {
        global $DB;

        if ($limitnum < 1 || $limitnum > 50) {
            throw new limit_invalid_parameter_exception("Limit must be between 1 and 50 (inclusive)");
        }

        $params = [
            'type' => CALENDAR_EVENT_TYPE_ACTION,
            'courseid' => $course->id,
        ];
        $where = [
            'type = :type',
            'courseid = :courseid',
        ];

        if ($timesortfrom) {
            $where[] = 'timesort >= :timesortfrom';
            $params['timesortfrom'] = $timesortfrom;
        }

        if ($timesortto) {
            $where[] = 'timesort <= :timesortto';
            $params['timesortto'] = $timesortto;
        }

        if (!is_null($afterevent)) {
            $where[] = 'id > :id';
            $params['id'] = $afterevent->get_id();
        }

        $wheresql = implode(' AND ', $where);
        $sql = sprintf("SELECT * FROM {event} WHERE %s ORDER BY timesort ASC, id ASC", $wheresql);

        $offset = 0;
        $events = [];
        // We need to continue to pull records from the database until we reach
        // the requested amount of events because not all records in the database
        // will be visible for the current user.
        while ($records = array_values($DB->get_records_sql($sql, $params, $offset, $limitnum))) {
            foreach ($records as $record) {
                if ($event = $this->transform_from_database_record($record)) {
                    if ($event instanceof action_event_interface) {
                        $events[] = $event;
                    }

                    if (count($events) == $limitnum) {
                        // We've got all of the events so break both loops.
                        break 2;
                    }
                }
            }

            $offset += $limitnum;
        }

        return $events;
    }

    /**
     * Create an event from a database record.
     *
     * @param \stdClass $record The database record
     * @return event_interface|false
     */
    private function transform_from_database_record(\stdClass $record) {
        return $this->factory->create_instance($record);
    }
}
