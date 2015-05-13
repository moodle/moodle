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
 * Log storage reader interface.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\log;

defined('MOODLE_INTERNAL') || die();

/**
 * Sql select reader.
 *
 * @deprecated since Moodle 2.9 MDL-48595 - please do not use this interface any more.
 * @see        sql_reader
 * @todo       MDL-49291 This will be deleted in Moodle 3.1.
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface sql_select_reader extends reader {
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
}
