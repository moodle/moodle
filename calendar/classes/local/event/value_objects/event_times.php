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
 * Event times class.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\value_objects;

defined('MOODLE_INTERNAL') || die();

/**
 * Class representing event times.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_times implements times_interface {
    /**
     * @var \DateTimeImmutable $start Event start time.
     */
    protected $start;

    /**
     * @var \DateTimeImmutable $end Event end time.
     */
    protected $end;

    /**
     * @var \DateTimeImmutable $sort Time used to sort events.
     */
    protected $sort;

    /**
     * @var \DateTimeImmutable $modified Time event was last modified.
     */
    protected $modified;

    /**
     * Constructor.
     *
     * @param \DateTimeImmutable $start    Event start time.
     * @param \DateTimeImmutable $end      Event end time.
     * @param \DateTimeImmutable $sort     Date used to sort events.
     * @param \DateTimeImmutable $modified Time event was last updated.
     */
    public function __construct(
        \DateTimeImmutable $start,
        \DateTimeImmutable $end,
        \DateTimeImmutable $sort,
        \DateTimeImmutable $modified
    ) {
        $this->start = $start;
        $this->end = $end;
        $this->sort = $sort;
        $this->modified = $modified;
    }

    public function get_start_time() {
        return $this->start;
    }

    public function get_end_time() {
        return $this->end;
    }

    public function get_duration() {
        return $this->end->diff($this->start);
    }

    public function get_modified_time() {
        return $this->modified;
    }

    public function get_sort_time() {
        return $this->sort;
    }
}
