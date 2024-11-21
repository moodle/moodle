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
 * A repository for getting activity based on course logs.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\activity;

use DateTime;
use lang_string;
use moodle_database;

/**
 * A repository for getting activity based on course logs.
 *
 * This is hardcoded based on the block_xp\local\logger\course_user_event_collection_logger.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_log_recent_activity_repository implements user_recent_activity_repository {

    /** @var string The table name */
    protected $table = 'block_xp_log';
    /** @var moodle_database The DB. */
    protected $db;
    /** @var int The course ID. */
    protected $courseid;

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
     * Get the recent user's activity.
     *
     * @param int $userid The user ID.
     * @param int $count The number of entries.
     * @return activity
     */
    public function get_user_recent_activity($userid, $count = 0) {
        $results = $this->db->get_records_select($this->table, 'courseid = :courseid AND userid = :userid AND xp > 0', [
            'courseid' => $this->courseid,
            'userid' => $userid,
        ], 'time DESC, id DESC', '*', 0, $count);

        return array_map(function($row) {
            $desc = '';
            $class = $row->eventname;

            if (class_exists($class) && is_subclass_of($class, 'core\event\base')) {
                $desc = $class::get_name();
            } else {
                $desc = new lang_string('somethinghappened', 'block_xp');
            }

            return new xp_activity(
                new DateTime('@' . $row->time),
                $desc,
                $row->xp
            );

        }, $results);
    }

}
