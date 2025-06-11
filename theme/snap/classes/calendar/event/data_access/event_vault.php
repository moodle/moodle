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
 * @package    theme_snap
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com> / 2017 Open LMS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\calendar\event\data_access;
use core_calendar\local\event\entities\event_interface;

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
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com> / 2017 Open LMS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_vault extends \core_calendar\local\event\data_access\event_vault {

    /**
     * @var array | null
     */
    protected $usersfilter;

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
        callable $filter = null,
        ?string $searchvalue = null
    ) {

        $this->usersfilter = $usersfilter;

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

            $offset += $limitnum;
        }

        return $events;
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
            if ($this->usersfilter) {
                $where = '(' . $where . ' OR auf.extensionduedate > :timefrom3)';
            }
            if ($field === 'timestart') {
                $where = '((timestart = :timefrom1 AND e.id > :timefromid) OR timestart > :timefrom2' .
                       ($withduration ? ' OR timestart + timeduration > :timefrom3' : '') . ')';

                if ($this->usersfilter) {
                    $where = '(' . $where . ' OR auf.extensionduedate > :timefrom4'.
                        ($withduration ? ' OR auf.extensionduedate + timeduration > :timefrom5' : '').
                        ')';
                }

            }
            $params['timefromid'] = $lastseenid;
            $params['timefrom1'] = $lastseentime;
            $params['timefrom2'] = $lastseentime;
            $params['timefrom3'] = $lastseentime;
            $params['timefrom4'] = $lastseentime;
            $params['timefrom5'] = $lastseentime;
        } else {
            $where = 'timesort >= :timefrom';
            if ($field === 'timestart') {
                $where = '(timestart >= :timefrom' .
                       ($withduration ? ' OR timestart + timeduration > :timefrom2' : '') . ')';
                if ($this->usersfilter) {
                    $where = '(' . $where . ' OR auf.extensionduedate > :timefrom3'.
                        ($withduration ? ' OR auf.extensionduedate + timeduration > :timefrom4' : '').
                        ')';
                }
            }

            $params['timefrom'] = $timefrom;
            $params['timefrom2'] = $timefrom;
            $params['timefrom3'] = $timefrom;
            $params['timefrom4'] = $timefrom;
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
            $wheresql = '((timesort = :timeto1 AND e.id > :timetoid) OR timesort < :timeto2)';
            if ($this->usersfilter) {
                $wheresql = '(' . $wheresql . ' OR auf.extensionduedate < :timeto3)';
            }
            $where[] = $wheresql;
            if ($field === 'timestart') {
                $wheresql = '((timestart = :timeto1 AND e.id > :timetoid) OR timestart < :timeto2)';
                if ($this->usersfilter) {
                    $wheresql = '(' . $wheresql . ' OR auf.extensionduedate < :timeto3)';
                }
                $where[] = $wheresql;
            }
            $params['timetoid'] = $lastseenid;
            $params['timeto1'] = $timeto;
            $params['timeto2'] = $timeto;
            $params['timeto3'] = $timeto;
        } else {
            $wheresql = ($field === 'timestart' ? 'timestart' : 'timesort') . ' <= :timeto';
            if ($this->usersfilter) {
                $wheresql = '(' . $wheresql . ' OR auf.extensionduedate <= :timeto2)';
            }
            $where[] = $wheresql;
            $params['timeto'] = $timeto;
            $params['timeto2'] = $timeto;
        }

        return ['where' => $where, 'params' => $params];
    }
}
