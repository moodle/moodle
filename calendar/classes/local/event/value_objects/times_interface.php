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
 * Times interface.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\value_objects;

defined('MOODLE_INTERNAL') || die();

/**
 * Interface for various times.
 *
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface times_interface {
    /**
     * Get the start time.
     *
     * @return \DateTimeImmutable
     */
    public function get_start_time();

    /**
     * Get the end time.
     *
     * @return \DateTimeImmutable
     */
    public function get_end_time();

    /**
     * Get the duration (the time between start and end).
     *
     * @return \DateInterval
     */
    public function get_duration();

    /**
     * Get the sort time.
     *
     * @return \DateTimeImmutable
     */
    public function get_sort_time();

    /**
     * Get the modified time.
     *
     * @return \DateTimeImmutable
     */
    public function get_modified_time();
}
