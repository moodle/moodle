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
 * Course user event collection logger.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\logger;

use DateTime;
use dml_exception;
use moodle_database;
use stdClass;
use block_xp\local\reason\reason;

/**
 * Course user event collection logger.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_user_event_collection_logger implements
        reason_collection_logger,
        collection_logger_with_group_reset,
        collection_logger_with_id_reset {

    /** The table name. */
    const TABLE = 'block_xp_log';

    /** @var int The course ID. */
    protected $courseid;
    /** @var moodle_database The DB. */
    protected $db;

    /**
     * Constructor.
     *
     * @param moodle_database $db The DB.
     * @param int $courseid The course ID.
     */
    public function __construct(moodle_database $db, $courseid) {
        $this->db = $db;
        $this->courseid = $courseid;
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
            'courseid = :courseid AND time < :time',
            [
                'courseid' => $this->courseid,
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
        $time = $time ? $time : new DateTime();
        $record = new stdClass();
        $record->courseid = $this->courseid;
        $record->userid = $id;
        $record->eventname = $signature;
        $record->xp = $points;
        $record->time = $time->getTimestamp();
        try {
            $this->db->insert_record(static::TABLE, $record);
        } catch (dml_exception $e) {
            // Ignore, but please the linter.
            $pleaselinter = true;
        }
    }

    /**
     * Log a thing.
     *
     * @param int $id The target.
     * @param int $points The points.
     * @param reason $reason The reason.
     * @param DateTime|null $time When that happened.
     * @return void
     */
    public function log_reason($id, $points, reason $reason, DateTime $time = null) {
        $this->log($id, $points, $reason->get_signature(), $time);
    }

    /**
     * Purge all logs.
     *
     * @return void
     */
    public function reset() {
        $this->db->delete_records_select(
            static::TABLE,
            'courseid = :courseid',
            [
                'courseid' => $this->courseid,
            ]
        );
    }

    /**
     * Purge logs for users in a group.
     *
     * @param int $groupid The group ID.
     * @return void
     */
    public function reset_by_group($groupid) {
        $table = static::TABLE;
        $sql = "DELETE
                  FROM {{$table}}
                 WHERE courseid = :courseid
                   AND userid IN
               (SELECT gm.userid
                  FROM {groups_members} gm
                 WHERE gm.groupid = :groupid)";

        $params = [
            'courseid' => $this->courseid,
            'groupid' => $groupid,
        ];

        $this->db->execute($sql, $params);
    }

    /**
     * Purge logs for an ID.
     *
     * @param int $id The ID.
     * @return void
     */
    public function reset_by_id($id) {
        $this->db->delete_records(
            static::TABLE,
            [
                'courseid' => $this->courseid,
                'userid' => $id,
            ]
        );
    }

}
