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

defined('MOODLE_INTERNAL') || die();

use core_calendar\local\event\entities\action_event_interface;
use core_calendar\local\event\entities\event_interface;
use core_calendar\local\event\exceptions\limit_invalid_parameter_exception;
use core_calendar\local\event\factories\action_factory_interface;
use core_calendar\local\event\factories\event_factory_interface;
use core_calendar\local\event\strategies\raw_event_retrieval_strategy_interface;

/**
 * Event vault class.
 *
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

    /**
     * @var event_factory_interface $factory Factory for creating events.
     */
    protected $factory;

    /**
     * @var raw_event_retrieval_strategy_interface $retrievalstrategy Strategy for getting events from the DB.
     */
    protected $retrievalstrategy;

    /**
     * Create an event vault.
     *
     * @param event_factory_interface $factory An event factory
     * @param raw_event_retrieval_strategy_interface $retrievalstrategy
     */
    public function __construct(
        event_factory_interface $factory,
        raw_event_retrieval_strategy_interface $retrievalstrategy
    ) {
        $this->factory = $factory;
        $this->retrievalstrategy = $retrievalstrategy;
    }

    public function get_event_by_id($id) {
        global $DB;

        if ($record = $DB->get_record('event', ['id' => $id])) {
            return $this->transform_from_database_record($record);
        } else {
            return false;
        }
    }

    public function get_events(
        $timestartfrom = null,
        $timestartto = null,
        $timesortfrom = null,
        $timesortto = null,
        event_interface $timestartafterevent = null,
        event_interface $timesortafterevent = null,
        $limitnum = 20,
        $type = null,
        array $usersfilter = null,
        array $groupsfilter = null,
        array $coursesfilter = null,
        array $categoriesfilter = null,
        $withduration = true,
        $ignorehidden = true,
        callable $filter = null
    ) {

        $fromquery = function($field, $timefrom, $lastseenmethod, $afterevent, $withduration) {
            if (!$timefrom) {
                return false;
            }

            return $this->timefield_pagination_from(
                $field,
                $timefrom,
                $afterevent ? $afterevent->get_times()->{$lastseenmethod}()->getTimestamp() : null,
                $afterevent ? $afterevent->get_id() : null,
                $withduration
            );
        };

        $toquery = function($field, $timeto, $lastseenmethod, $afterevent) {
            if (!$timeto) {
                return false;
            }

            return $this->timefield_pagination_to(
                $field,
                $timeto,
                $afterevent ? $afterevent->get_times()->{$lastseenmethod}()->getTimestamp() : null,
                $afterevent ? $afterevent->get_id() : null
            );
        };

        $timesortfromquery = $fromquery('timesort', $timesortfrom, 'get_sort_time', $timesortafterevent, $withduration);
        $timesorttoquery = $toquery('timesort', $timesortto, 'get_sort_time', $timesortafterevent);
        $timestartfromquery = $fromquery('timestart', $timestartfrom, 'get_start_time', $timestartafterevent, $withduration);
        $timestarttoquery = $toquery('timestart', $timestartto, 'get_start_time', $timestartafterevent);

        if (($timesortto && !$timesorttoquery) || ($timestartto && !$timestarttoquery)) {
            return [];
        }

        $params = array_merge(
            $type ? ['type' => $type] : [],
            $timesortfromquery ? $timesortfromquery['params'] : [],
            $timesorttoquery ? $timesorttoquery['params'] : [],
            $timestartfromquery ? $timestartfromquery['params'] : [],
            $timestarttoquery ? $timestarttoquery['params'] : []
        );

        $where = array_merge(
            $type ? ['type = :type'] : [],
            $timesortfromquery ? $timesortfromquery['where'] : [],
            $timesorttoquery ? $timesorttoquery['where'] : [],
            $timestartfromquery ? $timestartfromquery['where'] : [],
            $timestarttoquery ? $timestarttoquery['where'] : []
        );

        $offset = 0;
        $events = [];

        while ($records = array_values($this->retrievalstrategy->get_raw_events(
            $usersfilter,
            $groupsfilter,
            $coursesfilter,
            $categoriesfilter,
            $where,
            $params,
            "COALESCE(e.timesort, e.timestart) ASC, e.id ASC",
            $offset,
            $limitnum,
            $ignorehidden
        ))) {
            foreach ($records as $record) {
                if ($event = $this->transform_from_database_record($record)) {
                    $filtertest = $filter ? $filter($event) : true;

                    if ($event && $filtertest) {
                        $events[] = $event;
                    }

                    if (count($events) == $limitnum) {
                        // We've got all of the events so break both loops.
                        break 2;
                    }
                }
            }

            if (!$limitnum) {
                break;
            } else {
                $offset += $limitnum;
            }
        }

        return $events;
    }

    public function get_action_events_by_timesort(
        \stdClass $user,
        $timesortfrom = null,
        $timesortto = null,
        event_interface $afterevent = null,
        $limitnum = 20,
        $limittononsuspendedevents = false
    ) {
        $courseids = array_map(function($course) {
            return $course->id;
        }, enrol_get_all_users_courses($user->id, $limittononsuspendedevents));

        $groupids = array_reduce($courseids, function($carry, $courseid) use ($user) {
            $groupings = groups_get_user_groups($courseid, $user->id);
            // Grouping 0 is all groups.
            return array_merge($carry, $groupings[0]);
        }, []);

        return $this->get_events(
            null,
            null,
            $timesortfrom,
            $timesortto,
            null,
            $afterevent,
            $limitnum,
            CALENDAR_EVENT_TYPE_ACTION,
            [$user->id],
            $groupids ? $groupids : null,
            $courseids ? $courseids : null,
            null, // All categories.
            true,
            true,
            function ($event) {
                return $event instanceof action_event_interface;
            }
        );
    }

    public function get_action_events_by_course(
        \stdClass $user,
        \stdClass $course,
        $timesortfrom = null,
        $timesortto = null,
        event_interface $afterevent = null,
        $limitnum = 20
    ) {
        $groupings = groups_get_user_groups($course->id, $user->id);
        return array_values(
            $this->get_events(
                null,
                null,
                $timesortfrom,
                $timesortto,
                null,
                $afterevent,
                $limitnum,
                CALENDAR_EVENT_TYPE_ACTION,
                [$user->id],
                $groupings[0] ? $groupings[0] : null,
                [$course->id],
                [],
                true,
                true,
                function ($event) use ($course) {
                    return $event instanceof action_event_interface && $event->get_course()->get('id') == $course->id;
                }
            )
        );
    }

    /**
     * Generates SQL subquery and parameters for 'from' pagination.
     *
     * @param string    $field
     * @param int       $timefrom
     * @param int|null  $lastseentime
     * @param int|null  $lastseenid
     * @param bool      $withduration
     * @return array
     */
    protected function timefield_pagination_from(
        $field,
        $timefrom,
        $lastseentime = null,
        $lastseenid = null,
        $withduration = true
    ) {
        $where = '';
        $params = [];

        if ($lastseentime && $lastseentime >= $timefrom) {
            $where = '((timesort = :timefrom1 AND e.id > :timefromid) OR timesort > :timefrom2)';
            if ($field === 'timestart') {
                $where = '((timestart = :timefrom1 AND e.id > :timefromid) OR timestart > :timefrom2' .
                       ($withduration ? ' OR timestart + timeduration > :timefrom3' : '') . ')';
            }
            $params['timefromid'] = $lastseenid;
            $params['timefrom1'] = $lastseentime;
            $params['timefrom2'] = $lastseentime;
            $params['timefrom3'] = $lastseentime;
        } else {
            $where = 'timesort >= :timefrom';
            if ($field === 'timestart') {
                $where = '(timestart >= :timefrom' .
                       ($withduration ? ' OR timestart + timeduration > :timefrom2' : '') . ')';
            }

            $params['timefrom'] = $timefrom;
            $params['timefrom2'] = $timefrom;
        }

        return ['where' => [$where], 'params' => $params];
    }

    /**
     * Generates SQL subquery and parameters for 'to' pagination.
     *
     * @param string   $field
     * @param int      $timeto
     * @param int|null $lastseentime
     * @param int|null $lastseenid
     * @return array|bool
     */
    protected function timefield_pagination_to(
        $field,
        $timeto,
        $lastseentime = null,
        $lastseenid = null
    ) {
        $where = [];
        $params = [];

        if ($lastseentime && $lastseentime > $timeto) {
            // The last seen event from this set is after the time sort range which
            // means all events in this range have been seen, so we can just return
            // early here.
            return false;
        } else if ($lastseentime && $lastseentime == $timeto) {
            $where[] = '((timesort = :timeto1 AND e.id > :timetoid) OR timesort < :timeto2)';
            if ($field === 'timestart') {
                $where[] = '((timestart = :timeto1 AND e.id > :timetoid) OR timestart < :timeto2)';
            }
            $params['timetoid'] = $lastseenid;
            $params['timeto1'] = $timeto;
            $params['timeto2'] = $timeto;
        } else {
            $where[] = ($field === 'timestart' ? 'timestart' : 'timesort') . ' <= :timeto';
            $params['timeto'] = $timeto;
        }

        return ['where' => $where, 'params' => $params];
    }

    /**
     * Create an event from a database record.
     *
     * @param \stdClass $record The database record
     * @return event_interface|null
     */
    protected function transform_from_database_record(\stdClass $record) {
        return $this->factory->create_instance($record);
    }

    /**
     * Fetches records from DB.
     *
     * @param int    $userid
     * @param string $whereconditions
     * @param array  $whereparams
     * @param string $ordersql
     * @param int    $offset
     * @param int    $limitnum
     * @return array
     */
    protected function get_from_db(
        $userid,
        $whereconditions,
        $whereparams,
        $ordersql,
        $offset,
        $limitnum
    ) {
        return array_values(
            $this->retrievalstrategy->get_raw_events(
                [$userid],
                null,
                null,
                null,
                $whereconditions,
                $whereparams,
                $ordersql,
                $offset,
                $limitnum
            )
        );
    }
}
