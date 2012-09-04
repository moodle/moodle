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
 * Tests for ../statslib.php
 *
 * @package    core
 * @subpackage stats
 * @copyright  2012 Tyler Bannister
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/statslib.php');

/**
 * Test functions that affect daily stats
 */
class statslib_daily_testcase extends advanced_testcase {
    protected $tables = array('temp_log1', 'temp_log2', 'temp_stats_daily', 'temp_stats_user_daily');

    /**
     * Setup function
     *   - Allow changes to CFG->debug for testing purposes.
     */
    protected function setUp() {
        global $CFG;
        parent::setUp();

        // Settings to force statistic to run during testing
        $CFG->timezone                = 99;
        $CFG->statsfirstrun           = 'all';
        $CFG->statslastdaily          = 0;
        $CFG->statslastexecution      = 0;
        $CFG->statsruntimestarthour   = date('H');
        $CFG->statsruntimestartminute = 0;

        $this->resetAfterTest(true);
    }

    /**
     * Function to setup database.
     *
     * @param array $logs An array of log entries to added to the database.
     */
    protected function prepare_db($logs) {
        global $CFG, $DB;

        $course1 = SITEID + 1;
        $guest   = $CFG->siteguest;
        $user1   = $guest + 1;
        $user2   = $user1 + 1;

        // Create users (user)
        $tables['user'] = array(
            array('id' => $guest, 'deleted' => 0, 'username' => 'guest'),
            array('id' => $user1, 'deleted' => 0, 'username' => 'user1'),
            array('id' => $user2, 'deleted' => 0, 'username' => 'user2'),
        );

        // Create courses (course)
        $tables['course'] = array(
            array('id' => SITEID,   'shortname' => 'site'),
            array('id' => $course1, 'shortname' => 'course1'),
        );

        // Create contexts (context)
        $tables['context'] = array(
            array('id' => 1, 'contextlevel' => CONTEXT_SYSTEM, 'instanceid' => 0),
            array('id' => 2, 'contextlevel' => CONTEXT_COURSE, 'instanceid' => SITEID),
            array('id' => 3, 'contextlevel' => CONTEXT_COURSE, 'instanceid' => $course1),
            array('id' => 4, 'contextlevel' => CONTEXT_USER,   'instanceid' => $guest),
            array('id' => 5, 'contextlevel' => CONTEXT_USER,   'instanceid' => $user1),
            array('id' => 6, 'contextlevel' => CONTEXT_USER,   'instanceid' => $user2),
        );

        // Create role assignments (role_assignment)
        $tables['role_assignments'] = array(
            array('id' => 1, 'roleid' => 5, 'contextid' => 3, 'userid' => $user1),
            array('id' => 2, 'roleid' => 5, 'contextid' => 2, 'userid' => $user2),
        );

        // Create enrolments (enrol)
        $tables['enrol'] = array(
            array('id' => 1, 'enrol' => 'manual', 'status' => 0, 'courseid' => $course1, 'roleid' => 5),
            array('id' => 2, 'enrol' => 'guest',  'status' => 1, 'courseid' => $course1, 'roleid' => 0),
            array('id' => 3, 'enrol' => 'self',   'status' => 1, 'courseid' => $course1, 'roleid' => 5),
        );

        // Create user enrolments (user_enrolments)
        $tables['user_enrolments'] = array(
            array('id' => 1, 'status' => 0, 'enrolid' => 1, 'userid' => $user1),
        );

        // Create logs (log)
        $tables['log'] = $logs;

        // Insert records
        foreach ($tables as $table => $rows) {
            $DB->delete_records($table, array());

            foreach ($rows as $row) {
                $DB->import_record($table, (object) $row);
            }
        }
    }

    public function daily_log_provider() {
        global $CFG;

        $course1  = SITEID + 1;
        $guest    = $CFG->siteguest;
        $user1    = $guest + 1;
        $user2    = $user1 + 1;
        $start    = stats_get_base_daily(1272758400);
        $end      = stats_get_next_day_start($start);
        $fpid     = (int) $CFG->defaultfrontpageroleid;
        $gr       = get_guest_role();
        $stid     = 5;
        $nologend = stats_get_next_day_start(stats_get_base_daily(stats_get_start_from('daily')));


        // Each test case contains a complete list of expected output because producing
        // more or fewer results is an error, as is changing the results in unrelated
        // stats entries.
        // All queries are identified by the number printed after the query completes in the
        // 4 temporary table stats code.
        return array(
            array( // Test #0 - No logs - Only query 3 should be processed
                array(  // Logs
                ),
                array(
                    'stats_daily' => array(  // stats_daily
                        array(  // Query 3
                            'courseid' => $course1,
                            'timeend'  => $nologend,
                            'roleid'   => 5,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                    ),
                    'stats_user_daily' => array(  // stats_user_daily
                    ),
                ),
            ),
            array( // Test #1 - No login - Tests queries 2, 3, 5, 7, 9 (and 8), 10 (read)
                array(  // Logs
                    array(
                        'id'     => 1,
                        'time'   => $start + 14410,
                        'userid' => $guest,
                        'course' => SITEID,
                        'action' => 'view'
                    ),
                ),
                array(
                    'stats_daily' => array(  // stats_daily
                        array(  // Query 2
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'logins',
                            'stat1'    => 0,
                            'stat2'    => 0
                        ),
                        array(  // Query 3
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => $stid,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 5
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 7
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'enrolments',
                            'stat1'    => 3,
                            'stat2'    => 1
                        ),
                        array(  // Query 9
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => $fpid,
                            'stattype' => 'enrolments',
                            'stat1'    => 3,
                            'stat2'    => 1
                        ),
                        array(  // Query 11
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'activity',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array( // Query 16
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => $gr->id,
                            'stattype' => 'activity',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                    ),
                    'stats_user_daily' => array(  // stats_user_daily
                        array( // Query 10 - read
                            'courseid'    => SITEID,
                            'userid'      => $guest,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 1,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                        array( // Query 10 - default record
                            'courseid'    => SITEID,
                            'userid'      => 0,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 0,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                    ),
                ),
            ),
            array( // Test #2 - Single login - Tests queries 1, 2 (with logins), 4
                array(  // Logs
                    array(
                        'id'     => 1,
                        'time'   => $start + 14410,
                        'userid' => $user1,
                        'course' => SITEID,
                        'action' => 'login'
                    ),
                ),
                array(
                    'stats_daily' => array(  // stats_daily
                        array(  // Query 2
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'logins',
                            'stat1'    => 1,
                            'stat2'    => 1
                        ),
                        array(  // Query 3
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => $stid,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 5
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 7
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'enrolments',
                            'stat1'    => 3,
                            'stat2'    => 1
                        ),
                        array(  // Query 9
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => $fpid,
                            'stattype' => 'enrolments',
                            'stat1'    => 3,
                            'stat2'    => 1
                        ),
                        array(  // Query 11
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'activity',
                            'stat1'    => 0,
                            'stat2'    => 0
                        ),
                    ),
                    'stats_user_daily' => array(  // stats_user_daily
                        array(  // Query 1
                            'courseid'    => SITEID,
                            'userid'      => $user1,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 1,
                            'statswrites' => 0,
                            'stattype'    => 'logins'
                        ),
                        array( // Query 10
                            'courseid'    => SITEID,
                            'userid'      => $user1,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 0,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                        array( // Query 10 - default record
                            'courseid'    => SITEID,
                            'userid'      => 0,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 0,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                    ),
                ),
            ),
            array( // Test #3 - Guest login and view course - Tests queries 11  (read), 13 (guest)
                array(  // logs
                    array(
                        'id'     => 1,
                        'time'   => $start + 14410,
                        'userid' => $guest,
                        'course' => SITEID,
                        'action' => 'login'
                    ),
                    array(
                        'id' => 2,
                        'time'   => $start + 14420,
                        'userid' => $guest,
                        'course' => $course1,
                        'action' => 'view'
                    ),
                ),
                array(
                    'stats_daily' => array(  // stats_daily
                        array(  // Query 2
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'logins',
                            'stat1'    => 1,
                            'stat2'    => 1
                        ),
                        array(  // Query 3
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => $stid,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 5
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 7
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'enrolments',
                            'stat1'    => 3,
                            'stat2'    => 1
                        ),
                        array(  // Query 9
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => $fpid,
                            'stattype' => 'enrolments',
                            'stat1'    => 3,
                            'stat2'    => 1
                        ),
                        array(  // Query 11
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'activity',
                            'stat1'    => 0,
                            'stat2'    => 0
                        ),
                        array(  // Query 11
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'activity',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 13
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => $gr->id,
                            'stattype' => 'activity',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                     ),
                    'stats_user_daily' => array(  // stats_user_daily
                        array(  // Query 1
                            'courseid'    => SITEID,
                            'userid'      => $guest,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 1,
                            'statswrites' => 0,
                            'stattype'    => 'logins'
                        ),
                        array(  // Query 10
                            'courseid'    => $course1,
                            'userid'      => $guest,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 1,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                        array(  // Query 10
                            'courseid'    => SITEID,
                            'userid'      => $guest,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 0,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                        array( // Query 10 - default record
                            'courseid'    => SITEID,
                            'userid'      => 0,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 0,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                    ),
                ),
            ),
            array( // Test #4 - Login, view course, upload assignment - Tests queries 10 + 11 (write), 13 (guest)
                array(  // logs
                    array(
                        'id'     => 1,
                        'time'   => $start + 14410,
                        'userid' => $guest,
                        'course' => SITEID,
                        'action' => 'login'
                    ),
                    array(
                        'id' => 2,
                        'time'   => $start + 14420,
                        'userid' => $guest,
                        'course' => $course1,
                        'action' => 'view'
                    ),
                    array(
                        'id' => 3,
                        'time'   => $start + 14430,
                        'userid' => $guest,
                        'course' => $course1,
                        'action' => 'add post'
                    ),
                ),
                array(
                    'stats_daily' => array(  // stats_daily
                        array(  // Query 2
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'logins',
                            'stat1'    => 1,
                            'stat2'    => 1
                        ),
                        array(  // Query 3
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => $stid,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 5
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 7
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'enrolments',
                            'stat1'    => 3,
                            'stat2'    => 1
                        ),
                        array(  // Query 9
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => $fpid,
                            'stattype' => 'enrolments',
                            'stat1'    => 3,
                            'stat2'    => 1
                        ),
                        array(  // Query 11
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'activity',
                            'stat1'    => 0,
                            'stat2'    => 0
                        ),
                        array(  // Query 11 - read + write
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'activity',
                            'stat1'    => 1,
                            'stat2'    => 1
                        ),
                        array(  // Query 13 - read + write
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => $gr->id,
                            'stattype' => 'activity',
                            'stat1'    => 1,
                            'stat2'    => 1
                        ),
                     ),
                    'stats_user_daily' => array(  // stats_user_daily
                        array(  // Query 1
                            'courseid'    => SITEID,
                            'userid'      => $guest,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 1,
                            'statswrites' => 0,
                            'stattype'    => 'logins'
                        ),
                        array(  // Query 10 - read + write
                            'courseid'    => $course1,
                            'userid'      => $guest,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 1,
                            'statswrites' => 1,
                            'stattype'    => 'activity'
                        ),
                        array(  // Query 10
                            'courseid'    => SITEID,
                            'userid'      => $guest,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 0,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                        array( // Query 10 - default record
                            'courseid'    => SITEID,
                            'userid'      => 0,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 0,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                     ),
                ),
            ),
            array( // Test #5 - Login and view course - Tests queries 4, 6, 10, 12, 14  (read)
                array(  // logs
                    array(
                        'id'     => 1,
                        'time'   => $start + 14410,
                        'userid' => $user1,
                        'course' => SITEID,
                        'action' => 'login'
                    ),
                    array(
                        'id' => 2,
                        'time'   => $start + 14420,
                        'userid' => $user1,
                        'course' => $course1,
                        'action' => 'view'
                    ),
                ),
                array(
                    'stats_daily' => array(  // stats_daily
                        array(  // Query 2
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'logins',
                            'stat1'    => 1,
                            'stat2'    => 1
                        ),
                        array( // Queries 3 (stat1) and 4 (stat2)
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => $stid,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 1
                        ),
                        array( // Queries 5 (stat1) and 6 (stat2)
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 1
                        ),
                        array(  // Query 7
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'enrolments',
                            'stat1'    => 3,
                            'stat2'    => 1
                        ),
                        array(  // Query 9
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => $fpid,
                            'stattype' => 'enrolments',
                            'stat1'    => 3,
                            'stat2'    => 1
                        ),
                        array(  // Query 11
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'activity',
                            'stat1'    => 0,
                            'stat2'    => 0
                        ),
                        array(  // Query 11
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'activity',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array( // Query 12 (read)
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => $stid,
                            'stattype' => 'activity',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                    ),
                    'stats_user_daily' => array(  // stats_user_daily
                        array(  // Query 1
                            'courseid'    => SITEID,
                            'userid'      => $user1,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 1,
                            'statswrites' => 0,
                            'stattype'    => 'logins'
                        ),
                        array(  // Query 10
                            'courseid'    => $course1,
                            'userid'      => $user1,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 1,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                        array(  // Query 10
                            'courseid'    => SITEID,
                            'userid'      => $user1,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 0,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                        array( // Query 10 - default record
                            'courseid'    => SITEID,
                            'userid'      => 0,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 0,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                    ),
                ),
            ),
            array( // Test #6 - Login, view course, post - Tests queries 10, 12, 14 (write)
                array(  // logs
                    array(
                        'id'     => 1,
                        'time'   => $start + 14410,
                        'userid' => $user1,
                        'course' => SITEID,
                        'action' => 'login'
                    ),
                    array(
                        'id' => 2,
                        'time'   => $start + 14420,
                        'userid' => $user1,
                        'course' => $course1,
                        'action' => 'view'
                    ),
                    array(
                        'id' => 3,
                        'time'   => $start + 14430,
                        'userid' => $user1,
                        'course' => $course1,
                        'action' => 'add post'
                    ),
                ),
                array(
                    'stats_daily' => array(  // stats_daily
                        array(  // Query 2
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'logins',
                            'stat1'    => 1,
                            'stat2'    => 1
                        ),
                        array(  // Queries 3 (stat1) and 4 (stat2)
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => $stid,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 1
                        ),
                        array(  // Queries 5 (stat1) and 6 (stat2)
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 1
                        ),
                        array(  // Query 7
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'enrolments',
                            'stat1'    => 3,
                            'stat2'    => 1
                        ),
                        array(  // Query 9
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => $fpid,
                            'stattype' => 'enrolments',
                            'stat1'    => 3,
                            'stat2'    => 1
                        ),
                        array(  // Query 11
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'activity',
                            'stat1'    => 0,
                            'stat2'    => 0
                        ),
                        array(  // Query 11
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'activity',
                            'stat1'    => 1,
                            'stat2'    => 1
                        ),
                        array(  // Query 12 (read + write)
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => $stid,
                            'stattype' => 'activity',
                            'stat1'    => 1,
                            'stat2'    => 1
                        ),
                    ),
                    'stats_user_daily' => array(  // stats_user_daily
                        array(  // Query 1
                            'courseid'    => SITEID,
                            'userid'      => $user1,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 1,
                            'statswrites' => 0,
                            'stattype'    => 'logins'
                        ),
                        array(  // Query 10
                            'courseid'    => $course1,
                            'userid'      => $user1,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 1,
                            'statswrites' => 1,
                            'stattype'    => 'activity'
                        ),
                        array(  // Query 10
                            'courseid'    => SITEID,
                            'userid'      => $user1,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 0,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                        array( // Query 10 - default record
                            'courseid'    => SITEID,
                            'userid'      => 0,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 0,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                    ),
                ),
            ),
            array( // Test #7 - Login and view course - Tests queries 13 (not enroled), 14 (not default)
                array(  // logs
                    array(
                        'id'     => 1,
                        'time'   => $start + 14410,
                        'userid' => $user2,
                        'course' => SITEID,
                        'action' => 'login'
                    ),
                    array(
                        'id' => 2,
                        'time'   => $start + 14420,
                        'userid' => $user2,
                        'course' => SITEID,
                        'action' => 'view'
                    ),
                    array(
                        'id' => 3,
                        'time'   => $start + 14430,
                        'userid' => $user2,
                        'course' => $course1,
                        'action' => 'view'
                    ),
                ),
                array(
                    'stats_daily' => array(  // stats_daily
                        array(  // Query 2
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'logins',
                            'stat1'    => 1,
                            'stat2'    => 1
                        ),
                        array(  // Query 3
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => $stid,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 5
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 7
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'enrolments',
                            'stat1'    => 3,
                            'stat2'    => 1
                        ),
                        array(  // Query 9
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => $fpid,
                            'stattype' => 'enrolments',
                            'stat1'    => 3,
                            'stat2'    => 1
                        ),
                        array(  // Query 11
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'activity',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 11
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'activity',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 13
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => $gr->id,
                            'stattype' => 'activity',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 14
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => $stid,
                            'stattype' => 'activity',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                    ),
                    'stats_user_daily' => array(  // stats_user_daily
                        array(  // Query 1
                            'courseid'    => SITEID,
                            'userid'      => $user2,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 1,
                            'statswrites' => 0,
                            'stattype'    => 'logins'
                        ),
                        array(  // Query 10
                            'courseid'    => $course1,
                            'userid'      => $user2,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 1,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                        array(  // Query 10
                            'courseid'    => SITEID,
                            'userid'      => $user2,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 1,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                        array( // Query 10 - default record
                            'courseid'    => SITEID,
                            'userid'      => 0,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 0,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                    ),
                ),
            ),
            array( // Test #8 - Login and view site - Tests queries 15 (front page views)
                array(  // logs
                    array(
                        'id'     => 1,
                        'time'   => $start + 14410,
                        'userid' => $user1,
                        'course' => SITEID,
                        'action' => 'login'
                    ),
                    array(
                        'id' => 2,
                        'time'   => $start + 14420,
                        'userid' => $user1,
                        'course' => SITEID,
                        'action' => 'view'
                    ),
                ),
                array(
                    'stats_daily' => array(  // stats_daily
                        array(  // Query 2
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'logins',
                            'stat1'    => 1,
                            'stat2'    => 1
                        ),
                        array(  // Query 3
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => $stid,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 5
                            'courseid' => $course1,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'enrolments',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 7
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'enrolments',
                            'stat1'    => 3,
                            'stat2'    => 1
                        ),
                        array(  // Query 9
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => $fpid,
                            'stattype' => 'enrolments',
                            'stat1'    => 3,
                            'stat2'    => 1
                        ),
                        array(  // Query 11
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => 0,
                            'stattype' => 'activity',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                        array(  // Query 15
                            'courseid' => SITEID,
                            'timeend'  => $end,
                            'roleid'   => $fpid,
                            'stattype' => 'activity',
                            'stat1'    => 1,
                            'stat2'    => 0
                        ),
                     ),
                    'stats_user_daily' => array(  // stats_user_daily
                        array(  // Query 1
                            'courseid'    => SITEID,
                            'userid'      => $user1,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 1,
                            'statswrites' => 0,
                            'stattype'    => 'logins'
                        ),
                        array(  // Query 10
                            'courseid'    => SITEID,
                            'userid'      => $user1,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 1,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                        array( // Query 10 - default record
                            'courseid'    => SITEID,
                            'userid'      => 0,
                            'roleid'      => 0,
                            'timeend'     => $end,
                            'statsreads'  => 0,
                            'statswrites' => 0,
                            'stattype'    => 'activity'
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * Compare the expected stats to those in the database.
     *
     * @param array $stats An array of arrays of arrays of both types of stats
     */
    protected function verify_stats($stats) {
        global $DB;

        foreach ($stats as $type => $results) {
            $records = $DB->get_records($type);

            $this->assertEquals(sizeof($results), sizeof($records),
                                'Incorrect number of results returned for '. $type);

            foreach ($results as $result) {
                $found = 0;

                foreach ($records as $key => $record) {
                    $record = (array) $record;
                    unset($record['id']);
                    $diff = array_merge(array_diff_assoc($result, $record),
                                        array_diff_assoc($record, $result));

                    if (empty($diff)) {
                        $found = $key;
                        break;
                    }
                }
                $this->assertGreaterThan(0, $found, 'Expected log '. var_export($result, true)
                                        ." was not found in $type ". var_export($records, true));
                unset($records[$found]);
            }
        }
    }

    /**
     * Test progress output when debug is on
     */
    public function test_statslib_progress_debug() {
        global $CFG;

        $CFG->debug = DEBUG_ALL;
        $this->expectOutputString('1:0 ');
        stats_progress('init');
        stats_progress('1');
    }

    /**
     * Test progress output when debug is off
     */
    public function test_statslib_progress_no_debug() {
        global $CFG;

        $CFG->debug = DEBUG_NONE;
        $this->expectOutputString('.');
        stats_progress('init');
        stats_progress('1');
    }

    /**
     * Test the function that gets the start date from the config
     */
    public function test_statslib_get_start_from() {
        global $CFG, $DB;

        $logs = array(  // logs
            array(
                'id'     => 1,
                'time'   => 1272686410,
                'userid' => 1,
                'course' => 1,
                'action' => 'login'
            ),
        );

        $stats = array(  // stats_daily
            'courseid' => SITEID,
            'timeend'  => 1272758400,
            'roleid'   => 0,
            'stattype' => 'logins',
            'stat1'    => 1,
            'stat2'    => 1
        );

        $time = time();

        // Allow 1 second difference in case we cross a second boundary.
        $this->assertLessThanOrEqual(1, stats_get_start_from('daily') - ($time - (3 * 24 * 3600)));

        $this->prepare_db($logs);
        $this->assertEquals(1272686410, stats_get_start_from('daily'));

        $CFG->statsfirstrun = 'none';
        $this->assertLessThanOrEqual(1, stats_get_start_from('daily') - ($time - (3 * 24 * 3600)));

        $CFG->statsfirstrun = 14515200;
        $this->assertLessThanOrEqual(1, stats_get_start_from('daily') - ($time - (14515200)));

        $DB->insert_record_raw('stats_daily', $stats);
        $this->assertEquals(1272758400, stats_get_start_from('daily'));
    }

    /**
     * Test the function that calculates the start of the day
     */
    public function test_statslib_get_base_daily() {
        global $CFG;

        $CFG->timezone = 0;
        $this->assertEquals(1272672000, stats_get_base_daily(1272686410));
        $CFG->timezone = 5;
        $this->assertEquals(1272654000, stats_get_base_daily(1272686410));
    }

    /**
     * Test the function that gets the start of the next day
     */
    public function test_statslib_get_next_day_start() {
        global $CFG;

        $CFG->timezone = 0;
        $this->assertEquals(1272758400, stats_get_next_day_start(1272686410));
    }

    /**
     * Test the function that gets the action names
     *
     * Note: The function results depend on installed modules.  The hard coded lists are the
     *       defaults for a new Moodle 2.3 install.
     */
    public function test_statslib_get_action_names() {
        $basepostactions = array (
            0 => 'add',
            1 => 'delete',
            2 => 'edit',
            3 => 'add mod',
            4 => 'delete mod',
            5 => 'edit sectionenrol',
            6 => 'loginas',
            7 => 'new',
            8 => 'unenrol',
            9 => 'update',
            10 => 'update mod',
            11 => 'upload',
            12 => 'submit',
            13 => 'submit for grading',
            14 => 'talk',
            15 => 'choose',
            16 => 'choose again',
            17 => 'record delete',
            18 => 'add discussion',
            19 => 'add post',
            20 => 'delete discussion',
            21 => 'delete post',
            22 => 'move discussion',
            23 => 'prune post',
            24 => 'update post',
            25 => 'add category',
            26 => 'add entry',
            27 => 'approve entry',
            28 => 'delete category',
            29 => 'delete entry',
            30 => 'edit category',
            31 => 'update entry',
            32 => 'end',
            33 => 'start',
            34 => 'attempt',
            35 => 'close attempt',
            36 => 'preview',
            37 => 'editquestions',
            38 => 'delete attempt',
            39 => 'manualgrade',
        );

         $baseviewactions = array (
            0 => 'view',
            1 => 'view all',
            2 => 'history',
            3 => 'view submission',
            4 => 'view feedback',
            5 => 'print',
            6 => 'report',
            7 => 'view discussion',
            8 => 'search',
            9 => 'forum',
            10 => 'forums',
            11 => 'subscribers',
            12 => 'view forum',
            13 => 'view entry',
            14 => 'review',
            15 => 'pre-view',
            16 => 'download',
            17 => 'view form',
            18 => 'view graph',
            19 => 'view report',
        );

        $postactions = stats_get_action_names('post');

        foreach ($basepostactions as $action) {
            $this->assertContains($action, $postactions);
        }

        $viewactions = stats_get_action_names('view');

        foreach ($baseviewactions as $action) {
            $this->assertContains($action, $viewactions);
        }
    }

    /**
     * Test the temporary table creation and deletion.
     */
    public function test_statslib_temp_table_create_and_drop() {
        global $DB;

        foreach ($this->tables as $table) {
            $this->assertFalse($DB->get_manager()->table_exists($table));
        }

        stats_temp_table_create();

        foreach ($this->tables as $table) {
            $this->assertTrue($DB->get_manager()->table_exists($table));
        }

        stats_temp_table_drop();

        foreach ($this->tables as $table) {
            $this->assertFalse($DB->get_manager()->table_exists($table));
        }
    }

    /**
     * Test the temporary table creation and deletion.
     */
    public function test_statslib_temp_table_fill() {
        global $DB;

        $logs = array(  // logs
            array(
                'id'     => 1,
                'time'   => 1272686400,
                'userid' => 1,
                'course' => 1,
                'action' => 'login'
            ),
            array(
                'id'     => 2,
                'time'   => 1272686410,
                'userid' => 1,
                'course' => 1,
                'action' => 'login'
            ),
            array(
                'id'     => 3,
                'time'   => 1272772801,
                'userid' => 1,
                'course' => 1,
                'action' => 'login'
            ),
        );
        $this->prepare_db($logs);

        stats_temp_table_create();
        stats_temp_table_fill(1272686410, 1272758400);

        $this->assertEquals(1, $DB->count_records('temp_log1'));
        $this->assertEquals(1, $DB->count_records('temp_log2'));
    }

    /**
     * Test the temporary table creation and deletion.
     */
    public function test_statslib_temp_table_setup() {
        global $DB;

        $logs = array();
        $this->prepare_db($logs);

        stats_temp_table_create();
        stats_temp_table_setup();

        $this->assertEquals(1, $DB->count_records('temp_enroled'));
    }

    /**
     * Test the function that clean out the temporary tables.
     */
    public function test_statslib_temp_table_clean() {
        global $DB;

        $rows = array(
            'temp_log1'             => array('id' => 1, 'course' => 1),
            'temp_log2'             => array('id' => 1, 'course' => 1),
            'temp_stats_daily'      => array('id' => 1, 'courseid' => 1),
            'temp_stats_user_daily' => array('id' => 1, 'courseid' => 1),
        );

        stats_temp_table_create();

        foreach ($rows as $table => $row) {
            $DB->insert_record_raw($table, $row);
            $this->assertEquals(1, $DB->count_records($table));
        }

        stats_temp_table_clean();

        foreach ($rows as $table => $row) {
            $this->assertEquals(0, $DB->count_records($table));
        }

        $this->assertEquals(1, $DB->count_records('stats_daily'));
        $this->assertEquals(1, $DB->count_records('stats_user_daily'));
    }

    /**
     * Test the daily stats function
     *
     * @depends test_statslib_get_base_daily
     * @depends test_statslib_get_next_day_start
     * @dataProvider daily_log_provider
     */
    public function test_statslib_cron_daily($logs, $stats) {
        global $CFG;

        $CFG->debug = DEBUG_NONE;

        $this->prepare_db($logs);

        // Stats cron daily uses mtrace, turn on buffering to silence output.
        ob_start();
        stats_cron_daily(1);
        ob_end_clean();

        $this->verify_stats($stats);
    }

    /**
     * Test the daily stats function
     * @depends test_statslib_get_base_daily
     * @depends test_statslib_get_next_day_start
     */
    public function test_statslib_cron_daily_no_default_profile_id() {
        global $CFG;
        $CFG->defaultfrontpageroleid = 0;

        $course1  = SITEID + 1;
        $guest    = $CFG->siteguest;
        $start    = stats_get_base_daily(1272758400);
        $end      = stats_get_next_day_start($start);
        $stid     = 5;
        $fpid     = 0;
        $gr       = get_guest_role();

        $logs = array(  // Logs
            array(
                'id'     => 1,
                'time'   => $start + 14410,
                'userid' => $guest,
                'course' => SITEID,
                'action' => 'view'
            ),
        );

        $stats = array(
            'stats_daily' => array(
                array(  // Query 2
                    'courseid' => SITEID,
                    'timeend'  => $end,
                    'roleid'   => 0,
                    'stattype' => 'logins',
                    'stat1'    => 0,
                    'stat2'    => 0
                ),
                array(  // Query 3
                    'courseid' => $course1,
                    'timeend'  => $end,
                    'roleid'   => $stid,
                    'stattype' => 'enrolments',
                    'stat1'    => 1,
                    'stat2'    => 0
                ),
                array(  // Query 5
                    'courseid' => $course1,
                    'timeend'  => $end,
                    'roleid'   => 0,
                    'stattype' => 'enrolments',
                    'stat1'    => 1,
                    'stat2'    => 0
                ),
                array(  // Query 7
                    'courseid' => SITEID,
                    'timeend'  => $end,
                    'roleid'   => 0,
                    'stattype' => 'enrolments',
                    'stat1'    => 3,
                    'stat2'    => 1
                ),
                array(  // Query 11
                    'courseid' => SITEID,
                    'timeend'  => $end,
                    'roleid'   => 0,
                    'stattype' => 'activity',
                    'stat1'    => 1,
                    'stat2'    => 0
                ),
                array( // Query 16
                    'courseid' => SITEID,
                    'timeend'  => $end,
                    'roleid'   => $gr->id,
                    'stattype' => 'activity',
                    'stat1'    => 1,
                    'stat2'    => 0
                ),
            ),
            'stats_user_daily' => array(  // stats_user_daily
                array( // Query 10 - read
                    'courseid'    => SITEID,
                    'userid'      => $guest,
                    'roleid'      => 0,
                    'timeend'     => $end,
                    'statsreads'  => 1,
                    'statswrites' => 0,
                    'stattype'    => 'activity'
                ),
                array( // Query 10 - default record
                    'courseid'    => SITEID,
                    'userid'      => 0,
                    'roleid'      => 0,
                    'timeend'     => $end,
                    'statsreads'  => 0,
                    'statswrites' => 0,
                    'stattype'    => 'activity'
                ),
            ),
        );

        $CFG->debug = DEBUG_NONE;

        $this->prepare_db($logs);

        // Stats cron daily uses mtrace, turn on buffering to silence output.
        ob_start();
        stats_cron_daily($maxdays=1);
        ob_end_clean();

        $this->verify_stats($stats);
    }
}
