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

use core_calendar\local\interfaces\event_collection_interface;
use core_calendar\local\interfaces\event_factory_interface;
use core_calendar\local\interfaces\event_interface;
use core_calendar\local\event\exceptions\no_repeat_parent_exception;

/**
 * Class representing a collection of repeat events.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repeat_event_collection implements event_collection_interface {
    /**
     * @var DB_QUERY_LIMIT How many records to pull from the DB at once.
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
     * @param int                     $parentid ID of the parent event.
     * @param event_factory_interface $factory  Event factory.
     * @throws no_repeat_parent_exception If the parent record can't be loaded.
     */
    public function __construct($parentid, event_factory_interface $factory) {
        $this->parentid = $parentid;
        $this->factory = $factory;

        if (!$this->get_parent_record()) {
            throw new no_repeat_parent_exception(sprintf('No record found for id %d', $parentid));
        }
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
                yield $this->factory->create_instance(
                    $eventrecord->id,
                    !empty($eventrecord->name) ? $eventrecord->name : $parentrecord->name,
                    !empty($eventrecord->description) ? $eventrecord->description : $parentrecord->description,
                    !empty($eventrecord->format) ? $eventrecord->format : $parentrecord->format,
                    !empty($eventrecord->courseid) ? $eventrecord->courseid : $parentrecord->courseid,
                    !empty($eventrecord->groupid) ? $eventrecord->groupid : $parentrecord->groupid,
                    !empty($eventrecord->userid) ? $eventrecord->userid : $parentrecord->userid,
                    !empty($eventrecord->repeatid) ? $eventrecord->repeatid : $parentrecord->repeatid,
                    !empty($eventrecord->modulename) ? $eventrecord->modulename : $parentrecord->modulename,
                    !empty($eventrecord->instance) ? $eventrecord->instance : $parentrecord->instance,
                    !empty($eventrecord->eventtype) ? $eventrecord->eventtype : $parentrecord->eventtype,
                    !empty($eventrecord->timestart) ? $eventrecord->timestart : $parentrecord->timestart,
                    !empty($eventrecord->timeduration) ? $eventrecord->timeduration : $parentrecord->timeduration,
                    !empty($eventrecord->timemodified) ? $eventrecord->timemodified : $parentrecord->timemodified,
                    !empty($eventrecord->timesort) ? $eventrecord->timesort : $parentrecord->timesort,
                    !empty($eventrecord->visible) ? $eventrecord->visible : $parentrecord->visible,
                    !empty($eventrecord->subscriptionid) ? $eventrecord->subscriptionid : $parentrecord->subscriptionid
                );
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

        if (isset($this->parentrecord)) {
                return $this->parentrecord;
        }

        return $DB->get_record('event', ['id' => $this->parentid]); 
    }

    /**
     * Generate more event records.
     *
     * @param int $start Start offset.
     * @return \stdClass[]
     */
    protected function load_event_records($start = 1) {
        global $DB;
        while ($records = $DB->get_records(
            'event',
            ['repeatid' => $this->parentid],
            '',
            '*',
            $start,
            self::DB_QUERY_LIMIT
        )) {
            yield $records;
            $start += self::DB_QUERY_LIMIT;
        }
    }
}
