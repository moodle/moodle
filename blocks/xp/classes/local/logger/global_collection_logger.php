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
 * Global collection logger.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\logger;

use DateTime;
use moodle_database;

/**
 * Global collection logger.
 *
 * This class points to the same logs as the instances specific to a course,
 * it uses the same table, but it's useful as an implementation for quickly
 * deleting all the logs which should not be kept.
 *
 * Apart from that, this class has no use.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class global_collection_logger implements collection_logger {

    /** The table name. */
    const TABLE = 'block_xp_log';

    /** @var moodle_database The DB. */
    protected $db;

    /**
     * Constructor.
     *
     * @param moodle_database $db The DB.
     */
    public function __construct(moodle_database $db) {
        $this->db = $db;
    }

    /**
     * Delete logs older than a certain date.
     *
     * @param \DateTime $dt The date.
     * @return void
     */
    public function delete_older_than(DateTime $dt) {
        $this->db->delete_records_select(
            static::TABLE,
            'time < :time',
            [
                'time' => $dt->getTimestamp(),
            ]
        );
    }

    /**
     * Log a thing.
     *
     * @param int $id The target.
     * @param int $points The points.
     * @param string $signature A signature.
     * @param DateTime|null $time When that happened.
     * @return void
     */
    public function log($id, $points, $signature, DateTime $time = null) {
        // Do nothing. We should not be using this to log.
    }

    /**
     * Purge all logs.
     *
     * @return void
     */
    public function reset() {
        // Unlikely that this was intentional, so we do nothing.
    }

}
