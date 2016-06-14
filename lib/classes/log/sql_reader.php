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
 * Log iterator reader interface.
 *
 * @package    core
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\log;

defined('MOODLE_INTERNAL') || die();

/**
 * Log iterator reader interface.
 *
 * Replaces sql_select_reader adding functions
 * to return iterators.
 *
 * @since      Moodle 2.9
 * @package    core
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface sql_reader extends reader {

    /**
     * Fetch records using given criteria.
     *
     * @param string $selectwhere
     * @param array $params
     * @param string $sort
     * @param int $limitfrom
     * @param int $limitnum
     * @return \core\event\base[]
     */
    public function get_events_select($selectwhere, array $params, $sort, $limitfrom, $limitnum);

    /**
     * Return number of events matching given criteria.
     *
     * @param string $selectwhere
     * @param array $params
     * @return int
     */
    public function get_events_select_count($selectwhere, array $params);

    /**
     * Fetch records using the given criteria returning an traversable list of events.
     *
     * Note that the returned object is Traversable, not Iterator, as we are returning
     * EmptyIterator if we know there are no events, and EmptyIterator does not implement
     * Countable {@link https://bugs.php.net/bug.php?id=60577} so valid() should be checked
     * in any case instead of a count().
     *
     * Also note that the traversable object contains a recordset and it is very important
     * that you close it after using it.
     *
     * @param string $selectwhere
     * @param array $params
     * @param string $sort
     * @param int $limitfrom
     * @param int $limitnum
     * @return \Traversable|\core\event\base[] Returns an iterator containing \core\event\base objects.
     */
    public function get_events_select_iterator($selectwhere, array $params, $sort, $limitfrom, $limitnum);

    /**
     * Returns an event from the log data.
     *
     * @param stdClass $data Log data
     * @return \core\event\base
     */
    public function get_log_event($data);
}
