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
 * Event collection class.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\entities;

defined('MOODLE_INTERNAL') || die();

use core_calendar\local\event\factories\event_factory_interface;

/**
 * Class representing a collection of repeat events.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repeat_event_collection implements event_collection_interface {
    /**
     * @var int DB_QUERY_LIMIT How many records to pull from the DB at once.
     */
    const DB_QUERY_LIMIT = 100;

    /**
     * @var int $parentid The ID of the event which the events in this collection are repeats of.
     */
    protected $parentid;

    /**
     * @var \stdClass $parentrecord The parent event record from the database.
     */
    protected $parentrecord;

    /**
     * @var event_factory_interface $factory Event factory.
     */
    protected $factory;

    /**
     * @var int $num Total number of events that could be retrieved by this collection.
     */
    protected $num;

    /**
     * Constructor.
     *
     * @param stdClass                $dbrow    The event dbrow that is being repeated.
     * @param event_factory_interface $factory  Event factory.
     */
    public function __construct($dbrow, event_factory_interface $factory) {
        $eventid = $dbrow->id;
        $repeatid = $dbrow->repeatid;

        if (empty($repeatid)) {
            $this->parentrecord = $dbrow;
            $this->parentid = $eventid;
        } else {
            $this->parentid = $repeatid;
        }

        if ($eventid === $repeatid) {
            // This means the record we've been given is the parent
            // record.
            $this->parentrecord = $dbrow;
        }

        $this->factory = $factory;
    }

    public function get_id() {
        return $this->parentid;
    }

    public function get_num() {
        global $DB;
        // Subtract one because the original event has repeatid = its own id.
        return $this->num = max(
            isset($this->num) ? $this->num : ($DB->count_records('event', ['repeatid' => $this->parentid]) - 1),
            0
        );
    }

    public function getIterator() {
        $parentrecord = $this->get_parent_record();
        foreach ($this->load_event_records() as $eventrecords) {
            foreach ($eventrecords as $eventrecord) {
                // In the case of the repeat event having unset information, fallback on the parent.
                yield $this->factory->create_instance((object)array_merge((array)$parentrecord, (array)$eventrecord));
            }
        }
    }

    /**
     * Return the parent DB record.
     *
     * @return \stdClass
     */
    protected function get_parent_record() {
        global $DB;

        if (!isset($this->parentrecord)) {
            $this->parentrecord = $DB->get_record('event', ['id' => $this->parentid]);
        }

        return $this->parentrecord;
    }

    /**
     * Generate more event records.
     *
     * @param int $start Start offset.
     * @return \stdClass[]
     */
    protected function load_event_records($start = 0) {
        global $DB;
        while ($records = $DB->get_records_select(
            'event',
            'id <> :parentid AND repeatid = :repeatid',
            [
                'parentid' => $this->parentid,
                'repeatid' => $this->parentid,
            ],
            'id ASC',
            '*',
            $start,
            self::DB_QUERY_LIMIT
        )) {
            yield $records;
            $start += self::DB_QUERY_LIMIT;
        }
    }
}
