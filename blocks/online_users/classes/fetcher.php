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
 * File containing onlineusers class.
 *
 * @package    block_online_users
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_online_users;

defined('MOODLE_INTERNAL') || die();

/**
 * Class used to list and count online users
 *
 * @package    block_online_users
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fetcher {

    /** @var string The SQL query for retrieving a list of online users */
    public $sql;
    /** @var string The SQL query for counting the number of online users */
    public $csql;
    /** @var string The params for the SQL queries */
    public $params;

    /**
     * Class constructor
     *
     * @param int $currentgroup The group (if any) to filter on
     * @param int $now Time now
     * @param int $timetoshowusers Number of seconds to show online users
     * @param context $context Context object used to generate the sql for users enrolled in a specific course
     * @param bool $sitelevel Whether to check online users at site level.
     * @param int $courseid The course id to check
     */
    public function __construct($currentgroup, $now, $timetoshowusers, $context, $sitelevel = true, $courseid = null) {
        $this->set_sql($currentgroup, $now, $timetoshowusers, $context, $sitelevel, $courseid);
    }

    /**
     * Store the SQL queries & params for listing online users
     *
     * @param int $currentgroup The group (if any) to filter on
     * @param int $now Time now
     * @param int $timetoshowusers Number of seconds to show online users
     * @param context $context Context object used to generate the sql for users enrolled in a specific course
     * @param bool $sitelevel Whether to check online users at site level.
     * @param int $courseid The course id to check
     */
    protected function set_sql($currentgroup, $now, $timetoshowusers, $context, $sitelevel, $courseid) {
        global $USER, $DB, $CFG;

        $timefrom = 100 * floor(($now - $timetoshowusers) / 100); // Round to nearest 100 seconds for better query cache.

        $groupmembers = "";
        $groupselect  = "";
        $groupby       = "";
        $lastaccess    = ", lastaccess";
        $timeaccess    = ", ul.timeaccess AS lastaccess";
        $uservisibility = "";
        $uservisibilityselect = "";
        if ($CFG->block_online_users_onlinestatushiding) {
            $uservisibility = ", up.value AS uservisibility";
            $uservisibilityselect = "AND (" . $DB->sql_cast_char2int('up.value') . " = 1
                                    OR up.value IS NULL
                                    OR u.id = :userid)";
        }
        $params = array();

        $userfields = \user_picture::fields('u', array('username', 'deleted'));

        // Add this to the SQL to show only group users.
        if ($currentgroup !== null) {
            $groupmembers = ", {groups_members} gm";
            $groupselect = "AND u.id = gm.userid AND gm.groupid = :currentgroup";
            $groupby = "GROUP BY $userfields";
            $lastaccess = ", MAX(u.lastaccess) AS lastaccess";
            $timeaccess = ", MAX(ul.timeaccess) AS lastaccess";
            if ($CFG->block_online_users_onlinestatushiding) {
                $uservisibility = ", MAX(up.value) AS uservisibility";
            }
            $params['currentgroup'] = $currentgroup;
        }

        $params['now'] = $now;
        $params['timefrom'] = $timefrom;
        $params['userid'] = $USER->id;
        $params['name'] = 'block_online_users_uservisibility';

        if ($sitelevel) {
            $sql = "SELECT $userfields $lastaccess $uservisibility
                      FROM {user} u $groupmembers
                 LEFT JOIN {user_preferences} up ON up.userid = u.id
                           AND up.name = :name
                     WHERE u.lastaccess > :timefrom
                           AND u.lastaccess <= :now
                           AND u.deleted = 0
                           $uservisibilityselect
                           $groupselect $groupby
                  ORDER BY lastaccess DESC ";

            $csql = "SELECT COUNT(u.id)
                       FROM {user} u $groupmembers
                  LEFT JOIN {user_preferences} up ON up.userid = u.id
                            AND up.name = :name
                      WHERE u.lastaccess > :timefrom
                            AND u.lastaccess <= :now
                            AND u.deleted = 0
                            $uservisibilityselect
                            $groupselect";
        } else {
            // Course level - show only enrolled users for now.
            // TODO: add a new capability for viewing of all users (guests+enrolled+viewing).
            list($esqljoin, $eparams) = get_enrolled_sql($context);
            $params = array_merge($params, $eparams);

            $sql = "SELECT $userfields $timeaccess $uservisibility
                      FROM {user_lastaccess} ul $groupmembers, {user} u
                      JOIN ($esqljoin) euj ON euj.id = u.id
                 LEFT JOIN {user_preferences} up ON up.userid = u.id
                           AND up.name = :name
                     WHERE ul.timeaccess > :timefrom
                           AND u.id = ul.userid
                           AND ul.courseid = :courseid
                           AND ul.timeaccess <= :now
                           AND u.deleted = 0
                           $uservisibilityselect
                           $groupselect $groupby
                  ORDER BY lastaccess DESC";

            $csql = "SELECT COUNT(u.id)
                      FROM {user_lastaccess} ul $groupmembers, {user} u
                      JOIN ($esqljoin) euj ON euj.id = u.id
                 LEFT JOIN {user_preferences} up ON up.userid = u.id
                           AND up.name = :name
                     WHERE ul.timeaccess > :timefrom
                           AND u.id = ul.userid
                           AND ul.courseid = :courseid
                           AND ul.timeaccess <= :now
                           AND u.deleted = 0
                           $uservisibilityselect
                           $groupselect";

            $params['courseid'] = $courseid;
        }
        $this->sql = $sql;
        $this->csql = $csql;
        $this->params = $params;
    }

    /**
     * Get a list of the most recent online users
     *
     * @param int $userlimit The maximum number of users that will be returned (optional, unlimited if not set)
     * @return array
     */
    public function get_users($userlimit = 0) {
        global $DB;
        $users = $DB->get_records_sql($this->sql, $this->params, 0, $userlimit);
        return $users;
    }

    /**
     * Count the number of online users
     *
     * @return int
     */
    public function count_users() {
        global $DB;
        return $DB->count_records_sql($this->csql, $this->params);
    }

}
