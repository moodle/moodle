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
 * @package    core
 * @subpackage stats
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** THESE CONSTANTS ARE USED FOR THE REPORTING PAGE. */

define('STATS_REPORT_LOGINS',1); // double impose logins and unique logins on a line graph. site course only.
define('STATS_REPORT_READS',2); // double impose student reads and teacher reads on a line graph.
define('STATS_REPORT_WRITES',3); // double impose student writes and teacher writes on a line graph.
define('STATS_REPORT_ACTIVITY',4); // 2+3 added up, teacher vs student.
define('STATS_REPORT_ACTIVITYBYROLE',5); // all activity, reads vs writes, selected by role.

// user level stats reports.
define('STATS_REPORT_USER_ACTIVITY',7);
define('STATS_REPORT_USER_ALLACTIVITY',8);
define('STATS_REPORT_USER_LOGINS',9);
define('STATS_REPORT_USER_VIEW',10);  // this is the report you see on the user profile.

// admin only ranking stats reports
define('STATS_REPORT_ACTIVE_COURSES',11);
define('STATS_REPORT_ACTIVE_COURSES_WEIGHTED',12);
define('STATS_REPORT_PARTICIPATORY_COURSES',13);
define('STATS_REPORT_PARTICIPATORY_COURSES_RW',14);

// start after 0 = show dailies.
define('STATS_TIME_LASTWEEK',1);
define('STATS_TIME_LAST2WEEKS',2);
define('STATS_TIME_LAST3WEEKS',3);
define('STATS_TIME_LAST4WEEKS',4);

// start after 10 = show weeklies
define('STATS_TIME_LAST2MONTHS',12);

define('STATS_TIME_LAST3MONTHS',13);
define('STATS_TIME_LAST4MONTHS',14);
define('STATS_TIME_LAST5MONTHS',15);
define('STATS_TIME_LAST6MONTHS',16);

// start after 20 = show monthlies
define('STATS_TIME_LAST7MONTHS',27);
define('STATS_TIME_LAST8MONTHS',28);
define('STATS_TIME_LAST9MONTHS',29);
define('STATS_TIME_LAST10MONTHS',30);
define('STATS_TIME_LAST11MONTHS',31);
define('STATS_TIME_LASTYEAR',32);

// different modes for what reports to offer
define('STATS_MODE_GENERAL',1);
define('STATS_MODE_DETAILED',2);
define('STATS_MODE_RANKED',3); // admins only - ranks courses

// Output string when nodebug is on
define('STATS_PLACEHOLDER_OUTPUT', '.');

/**
 * Print daily cron progress
 * @param string $ident
 */
function stats_progress($ident) {
    static $start = 0;
    static $init  = 0;

    if ($ident == 'init') {
        $init = $start = microtime(true);
        return;
    }

    $elapsed = round(microtime(true) - $start);
    $start   = microtime(true);

    if (debugging('', DEBUG_ALL)) {
        mtrace("$ident:$elapsed ", '');
    } else {
        mtrace(STATS_PLACEHOLDER_OUTPUT, '');
    }
}

/**
 * Execute individual daily statistics queries
 *
 * @param string $sql The query to run
 * @return boolean success
 */
function stats_run_query($sql, $parameters = array()) {
    global $DB;

    try {
        $DB->execute($sql, $parameters);
    } catch (dml_exception $e) {

       if (debugging('', DEBUG_ALL)) {
           mtrace($e->getMessage());
       }
       return false;
    }
    return true;
}

/**
 * Execute daily statistics gathering
 *
 * @param int $maxdays maximum number of days to be processed
 * @return boolean success
 */
function stats_cron_daily($maxdays=1) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/adminlib.php');

    $now = time();

    $fpcontext = context_course::instance(SITEID, MUST_EXIST);

    // read last execution date from db
    if (!$timestart = get_config(NULL, 'statslastdaily')) {
        $timestart = stats_get_base_daily(stats_get_start_from('daily'));
        set_config('statslastdaily', $timestart);
    }

    $nextmidnight = stats_get_next_day_start($timestart);

    // are there any days that need to be processed?
    if ($now < $nextmidnight) {
        return true; // everything ok and up-to-date
    }

    $timeout = empty($CFG->statsmaxruntime) ? 60*60*24 : $CFG->statsmaxruntime;

    if (!set_cron_lock('statsrunning', $now + $timeout)) {
        return false;
    }

    // first delete entries that should not be there yet
    $DB->delete_records_select('stats_daily',      "timeend > $timestart");
    $DB->delete_records_select('stats_user_daily', "timeend > $timestart");

    // Read in a few things we'll use later
    $viewactions = stats_get_action_names('view');
    $postactions = stats_get_action_names('post');

    $guest           = (int)$CFG->siteguest;
    $guestrole       = (int)$CFG->guestroleid;
    $defaultfproleid = (int)$CFG->defaultfrontpageroleid;

    mtrace("Running daily statistics gathering, starting at $timestart:");
    \core\cron::trace_time_and_memory();

    $days  = 0;
    $total = 0;
    $failed  = false; // failed stats flag
    $timeout = false;

    if (!stats_temp_table_create()) {
        $days = 1;
        $failed = true;
    }
    mtrace('Temporary tables created');

    if(!stats_temp_table_setup()) {
        $days = 1;
        $failed = true;
    }
    mtrace('Enrolments calculated');

    $totalactiveusers = $DB->count_records('user', array('deleted' => '0'));

    while (!$failed && ($now > $nextmidnight)) {
        if ($days >= $maxdays) {
            $timeout = true;
            break;
        }

        $days++;
        core_php_time_limit::raise($timeout - 200);

        if ($days > 1) {
            // move the lock
            set_cron_lock('statsrunning', time() + $timeout, true);
        }

        $daystart = time();

        stats_progress('init');

        if (!stats_temp_table_fill($timestart, $nextmidnight)) {
            $failed = true;
            break;
        }

        // Find out if any logs available for this day
        $sql = "SELECT 'x' FROM {temp_log1} l";
        $logspresent = $DB->get_records_sql($sql, null, 0, 1);

        if ($logspresent) {
            // Insert blank record to force Query 10 to generate additional row when no logs for
            // the site with userid 0 exist.  Added for backwards compatibility.
            $DB->insert_record('temp_log1', array('userid' => 0, 'course' => SITEID, 'action' => ''));
        }

        // Calculate the number of active users today
        $sql = 'SELECT COUNT(DISTINCT u.id)
                  FROM {user} u
                  JOIN {temp_log1} l ON l.userid = u.id
                 WHERE u.deleted = 0';
        $dailyactiveusers = $DB->count_records_sql($sql);

        stats_progress('0');

        // Process login info first
        // Note: PostgreSQL doesn't like aliases in HAVING clauses
        $sql = "INSERT INTO {temp_stats_user_daily}
                            (stattype, timeend, courseid, userid, statsreads)

                SELECT 'logins', $nextmidnight AS timeend, ".SITEID." AS courseid,
                        userid, COUNT(id) AS statsreads
                  FROM {temp_log1} l
                 WHERE action = 'login'
              GROUP BY userid
                HAVING COUNT(id) > 0";

        if ($logspresent && !stats_run_query($sql)) {
            $failed = true;
            break;
        }
        $DB->update_temp_table_stats();

        stats_progress('1');

        $sql = "INSERT INTO {temp_stats_daily} (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'logins' AS stattype, $nextmidnight AS timeend, ".SITEID." AS courseid, 0,
                       COALESCE(SUM(statsreads), 0) as stat1, COUNT('x') as stat2
                  FROM {temp_stats_user_daily}
                 WHERE stattype = 'logins' AND timeend = $nextmidnight";

        if ($logspresent && !stats_run_query($sql)) {
            $failed = true;
            break;
        }
        stats_progress('2');


        // Enrolments and active enrolled users
        //
        // Unfortunately, we do not know how many users were registered
        // at given times in history :-(
        // - stat1: enrolled users
        // - stat2: enrolled users active in this period
        // - SITEID is special case here, because it's all about default enrolment
        //   in that case, we'll count non-deleted users.
        //

        $sql = "INSERT INTO {temp_stats_daily} (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'enrolments' as stattype, $nextmidnight as timeend, courseid, roleid,
                        COUNT(DISTINCT userid) as stat1, 0 as stat2
                  FROM {temp_enroled}
              GROUP BY courseid, roleid";

        if (!stats_run_query($sql)) {
            $failed = true;
            break;
        }
        stats_progress('3');

        // Set stat2 to the number distinct users with role assignments in the course that were active
        // using table alias in UPDATE does not work in pg < 8.2
        $sql = "UPDATE {temp_stats_daily}
                   SET stat2 = (

                    SELECT COUNT(DISTINCT userid)
                      FROM {temp_enroled} te
                     WHERE roleid = {temp_stats_daily}.roleid
                       AND courseid = {temp_stats_daily}.courseid
                       AND EXISTS (

                        SELECT 'x'
                          FROM {temp_log1} l
                         WHERE l.course = {temp_stats_daily}.courseid
                           AND l.userid = te.userid
                                  )
                               )
                 WHERE {temp_stats_daily}.stattype = 'enrolments'
                   AND {temp_stats_daily}.timeend = $nextmidnight
                   AND {temp_stats_daily}.courseid IN (

                    SELECT DISTINCT course FROM {temp_log2})";

        if ($logspresent && !stats_run_query($sql, array('courselevel'=>CONTEXT_COURSE))) {
            $failed = true;
            break;
        }
        stats_progress('4');

        // Now get course total enrolments (roleid==0) - except frontpage
        $sql = "INSERT INTO {temp_stats_daily} (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'enrolments', $nextmidnight AS timeend, te.courseid AS courseid, 0 AS roleid,
                       COUNT(DISTINCT userid) AS stat1, 0 AS stat2
                  FROM {temp_enroled} te
              GROUP BY courseid
                HAVING COUNT(DISTINCT userid) > 0";

        if ($logspresent && !stats_run_query($sql)) {
            $failed = true;
            break;
        }
        stats_progress('5');

        // Set stat 2 to the number of enrolled users who were active in the course
        $sql = "UPDATE {temp_stats_daily}
                   SET stat2 = (

                    SELECT COUNT(DISTINCT te.userid)
                      FROM {temp_enroled} te
                     WHERE te.courseid = {temp_stats_daily}.courseid
                       AND EXISTS (

                        SELECT 'x'
                          FROM {temp_log1} l
                         WHERE l.course = {temp_stats_daily}.courseid
                           AND l.userid = te.userid
                                  )
                               )

                 WHERE {temp_stats_daily}.stattype = 'enrolments'
                   AND {temp_stats_daily}.timeend = $nextmidnight
                   AND {temp_stats_daily}.roleid = 0
                   AND {temp_stats_daily}.courseid IN (

                    SELECT l.course
                      FROM {temp_log2} l
                     WHERE l.course <> ".SITEID.")";

        if ($logspresent && !stats_run_query($sql, array())) {
            $failed = true;
            break;
        }
        stats_progress('6');

        // Frontpage(==site) enrolments total
        $sql = "INSERT INTO {temp_stats_daily} (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'enrolments', $nextmidnight, ".SITEID.", 0, $totalactiveusers AS stat1,
                       $dailyactiveusers AS stat2" .
                $DB->sql_null_from_clause();

        if ($logspresent && !stats_run_query($sql)) {
            $failed = true;
            break;
        }
        // The steps up until this point, all add to {temp_stats_daily} and don't use new tables.
        // There is no point updating statistics as they won't be used until the DELETE below.
        $DB->update_temp_table_stats();

        stats_progress('7');

        // Default frontpage role enrolments are all site users (not deleted)
        if ($defaultfproleid) {
            // first remove default frontpage role counts if created by previous query
            $sql = "DELETE
                      FROM {temp_stats_daily}
                     WHERE stattype = 'enrolments'
                       AND courseid = ".SITEID."
                       AND roleid = $defaultfproleid
                       AND timeend = $nextmidnight";

            if ($logspresent && !stats_run_query($sql)) {
                $failed = true;
                break;
            }
            stats_progress('8');

            $sql = "INSERT INTO {temp_stats_daily} (stattype, timeend, courseid, roleid, stat1, stat2)

                    SELECT 'enrolments', $nextmidnight, ".SITEID.", $defaultfproleid,
                           $totalactiveusers AS stat1, $dailyactiveusers AS stat2" .
                    $DB->sql_null_from_clause();

            if ($logspresent && !stats_run_query($sql)) {
                $failed = true;
                break;
            }
            stats_progress('9');

        } else {
            stats_progress('x');
            stats_progress('x');
        }


        /// individual user stats (including not-logged-in) in each course, this is slow - reuse this data if possible
        list($viewactionssql, $params1) = $DB->get_in_or_equal($viewactions, SQL_PARAMS_NAMED, 'view');
        list($postactionssql, $params2) = $DB->get_in_or_equal($postactions, SQL_PARAMS_NAMED, 'post');
        $sql = "INSERT INTO {temp_stats_user_daily} (stattype, timeend, courseid, userid, statsreads, statswrites)

                SELECT 'activity' AS stattype, $nextmidnight AS timeend, course AS courseid, userid,
                       SUM(CASE WHEN action $viewactionssql THEN 1 ELSE 0 END) AS statsreads,
                       SUM(CASE WHEN action $postactionssql THEN 1 ELSE 0 END) AS statswrites
                  FROM {temp_log1} l
              GROUP BY userid, course";

        if ($logspresent && !stats_run_query($sql, array_merge($params1, $params2))) {
            $failed = true;
            break;
        }
        stats_progress('10');


        /// How many view/post actions in each course total
        $sql = "INSERT INTO {temp_stats_daily} (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'activity' AS stattype, $nextmidnight AS timeend, c.id AS courseid, 0,
                       SUM(CASE WHEN l.action $viewactionssql THEN 1 ELSE 0 END) AS stat1,
                       SUM(CASE WHEN l.action $postactionssql THEN 1 ELSE 0 END) AS stat2
                  FROM {course} c, {temp_log1} l
                 WHERE l.course = c.id
              GROUP BY c.id";

        if ($logspresent && !stats_run_query($sql, array_merge($params1, $params2))) {
            $failed = true;
            break;
        }
        stats_progress('11');


        /// how many view actions for each course+role - excluding guests and frontpage

        $sql = "INSERT INTO {temp_stats_daily} (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'activity', $nextmidnight AS timeend, courseid, roleid, SUM(statsreads), SUM(statswrites)
                  FROM (

                    SELECT pl.courseid, pl.roleid, sud.statsreads, sud.statswrites
                      FROM {temp_stats_user_daily} sud, (

                        SELECT DISTINCT te.userid, te.roleid, te.courseid
                          FROM {temp_enroled} te
                         WHERE te.roleid <> $guestrole
                           AND te.userid <> $guest
                                                        ) pl

                     WHERE sud.userid = pl.userid
                       AND sud.courseid = pl.courseid
                       AND sud.timeend = $nextmidnight
                       AND sud.stattype='activity'
                       ) inline_view

              GROUP BY courseid, roleid
                HAVING SUM(statsreads) > 0 OR SUM(statswrites) > 0";

        if ($logspresent && !stats_run_query($sql, array('courselevel'=>CONTEXT_COURSE))) {
            $failed = true;
            break;
        }
        stats_progress('12');

        /// how many view actions from guests only in each course - excluding frontpage
        /// normal users may enter course with temporary guest access too

        $sql = "INSERT INTO {temp_stats_daily} (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'activity', $nextmidnight AS timeend, courseid, $guestrole AS roleid,
                       SUM(statsreads), SUM(statswrites)
                  FROM (

                    SELECT sud.courseid, sud.statsreads, sud.statswrites
                      FROM {temp_stats_user_daily} sud
                     WHERE sud.timeend = $nextmidnight
                       AND sud.courseid <> ".SITEID."
                       AND sud.stattype='activity'
                       AND (sud.userid = $guest OR sud.userid NOT IN (

                        SELECT userid
                          FROM {temp_enroled} te
                         WHERE te.courseid = sud.courseid
                                                                     ))
                       ) inline_view

              GROUP BY courseid
                HAVING SUM(statsreads) > 0 OR SUM(statswrites) > 0";

        if ($logspresent && !stats_run_query($sql, array())) {
            $failed = true;
            break;
        }
        stats_progress('13');


        /// How many view actions for each role on frontpage - excluding guests, not-logged-in and default frontpage role
        $sql = "INSERT INTO {temp_stats_daily} (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'activity', $nextmidnight AS timeend, courseid, roleid,
                       SUM(statsreads), SUM(statswrites)
                  FROM (
                    SELECT pl.courseid, pl.roleid, sud.statsreads, sud.statswrites
                      FROM {temp_stats_user_daily} sud, (

                        SELECT DISTINCT ra.userid, ra.roleid, c.instanceid AS courseid
                          FROM {role_assignments} ra
                          JOIN {context} c ON c.id = ra.contextid
                         WHERE ra.contextid = :fpcontext
                           AND ra.roleid <> $defaultfproleid
                           AND ra.roleid <> $guestrole
                           AND ra.userid <> $guest
                                                   ) pl
                     WHERE sud.userid = pl.userid
                       AND sud.courseid = pl.courseid
                       AND sud.timeend = $nextmidnight
                       AND sud.stattype='activity'
                       ) inline_view

              GROUP BY courseid, roleid
                HAVING SUM(statsreads) > 0 OR SUM(statswrites) > 0";

        if ($logspresent && !stats_run_query($sql, array('fpcontext'=>$fpcontext->id))) {
            $failed = true;
            break;
        }
        stats_progress('14');


        // How many view actions for default frontpage role on frontpage only
        $sql = "INSERT INTO {temp_stats_daily} (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'activity', timeend, courseid, $defaultfproleid AS roleid,
                       SUM(statsreads), SUM(statswrites)
                  FROM (
                    SELECT sud.timeend AS timeend, sud.courseid, sud.statsreads, sud.statswrites
                      FROM {temp_stats_user_daily} sud
                     WHERE sud.timeend = :nextm
                       AND sud.courseid = :siteid
                       AND sud.stattype='activity'
                       AND sud.userid <> $guest
                       AND sud.userid <> 0
                       AND sud.userid NOT IN (

                        SELECT ra.userid
                          FROM {role_assignments} ra
                         WHERE ra.roleid <> $guestrole
                           AND ra.roleid <> $defaultfproleid
                           AND ra.contextid = :fpcontext)
                       ) inline_view

              GROUP BY timeend, courseid
                HAVING SUM(statsreads) > 0 OR SUM(statswrites) > 0";

        if ($logspresent && !stats_run_query($sql, array('fpcontext'=>$fpcontext->id, 'siteid'=>SITEID, 'nextm'=>$nextmidnight))) {
            $failed = true;
            break;
        }
        $DB->update_temp_table_stats();
        stats_progress('15');

        // How many view actions for guests or not-logged-in on frontpage
        $sql = "INSERT INTO {temp_stats_daily} (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT stattype, timeend, courseid, $guestrole AS roleid,
                       SUM(statsreads) AS stat1, SUM(statswrites) AS stat2
                  FROM (
                    SELECT sud.stattype, sud.timeend, sud.courseid,
                           sud.statsreads, sud.statswrites
                      FROM {temp_stats_user_daily} sud
                     WHERE (sud.userid = $guest OR sud.userid = 0)
                       AND sud.timeend = $nextmidnight
                       AND sud.courseid = ".SITEID."
                       AND sud.stattype='activity'
                       ) inline_view
                 GROUP BY stattype, timeend, courseid
                 HAVING SUM(statsreads) > 0 OR SUM(statswrites) > 0";

        if ($logspresent && !stats_run_query($sql)) {
            $failed = true;
            break;
        }
        stats_progress('16');

        stats_temp_table_clean();

        stats_progress('out');

        // remember processed days
        set_config('statslastdaily', $nextmidnight);
        $elapsed = time()-$daystart;
        mtrace("  finished until $nextmidnight: ".userdate($nextmidnight)." (in $elapsed s)");
        $total += $elapsed;

        $timestart    = $nextmidnight;
        $nextmidnight = stats_get_next_day_start($nextmidnight);
    }

    stats_temp_table_drop();

    set_cron_lock('statsrunning', null);

    if ($failed) {
        $days--;
        mtrace("...error occurred, completed $days days of statistics in {$total} s.");
        return false;

    } else if ($timeout) {
        mtrace("...stopping early, reached maximum number of $maxdays days ({$total} s) - will continue next time.");
        return false;

    } else {
        mtrace("...completed $days days of statistics in {$total} s.");
        return true;
    }
}


/**
 * Execute weekly statistics gathering
 * @return boolean success
 */
function stats_cron_weekly() {
    global $CFG, $DB;
    require_once($CFG->libdir.'/adminlib.php');

    $now = time();

    // read last execution date from db
    if (!$timestart = get_config(NULL, 'statslastweekly')) {
        $timestart = stats_get_base_daily(stats_get_start_from('weekly'));
        set_config('statslastweekly', $timestart);
    }

    $nextstartweek = stats_get_next_week_start($timestart);

    // are there any weeks that need to be processed?
    if ($now < $nextstartweek) {
        return true; // everything ok and up-to-date
    }

    $timeout = empty($CFG->statsmaxruntime) ? 60*60*24 : $CFG->statsmaxruntime;

    if (!set_cron_lock('statsrunning', $now + $timeout)) {
        return false;
    }

    // fisrt delete entries that should not be there yet
    $DB->delete_records_select('stats_weekly',      "timeend > $timestart");
    $DB->delete_records_select('stats_user_weekly', "timeend > $timestart");

    mtrace("Running weekly statistics gathering, starting at $timestart:");
    \core\cron::trace_time_and_memory();

    $weeks = 0;
    while ($now > $nextstartweek) {
        core_php_time_limit::raise($timeout - 200);
        $weeks++;

        if ($weeks > 1) {
            // move the lock
            set_cron_lock('statsrunning', time() + $timeout, true);
        }

        $stattimesql = "timeend > $timestart AND timeend <= $nextstartweek";

        $weekstart = time();
        stats_progress('init');

    /// process login info first
        $sql = "INSERT INTO {stats_user_weekly} (stattype, timeend, courseid, userid, statsreads)

                SELECT 'logins', timeend, courseid, userid, SUM(statsreads)
                  FROM (
                           SELECT $nextstartweek AS timeend, courseid, userid, statsreads
                             FROM {stats_user_daily} sd
                            WHERE stattype = 'logins' AND $stattimesql
                       ) inline_view
              GROUP BY timeend, courseid, userid
                HAVING SUM(statsreads) > 0";

        $DB->execute($sql);

        stats_progress('1');

        $sql = "INSERT INTO {stats_weekly} (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'logins' AS stattype, $nextstartweek AS timeend, ".SITEID." as courseid, 0,
                       COALESCE((SELECT SUM(statsreads)
                                   FROM {stats_user_weekly} s1
                                  WHERE s1.stattype = 'logins' AND timeend = $nextstartweek), 0) AS nstat1,
                       (SELECT COUNT('x')
                          FROM {stats_user_weekly} s2
                         WHERE s2.stattype = 'logins' AND timeend = $nextstartweek) AS nstat2" .
                $DB->sql_null_from_clause();

        $DB->execute($sql);

        stats_progress('2');

    /// now enrolments averages
        $sql = "INSERT INTO {stats_weekly} (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'enrolments', ntimeend, courseid, roleid, " . $DB->sql_ceil('AVG(stat1)') . ", " . $DB->sql_ceil('AVG(stat2)') . "
                  FROM (
                           SELECT $nextstartweek AS ntimeend, courseid, roleid, stat1, stat2
                             FROM {stats_daily} sd
                            WHERE stattype = 'enrolments' AND $stattimesql
                       ) inline_view
              GROUP BY ntimeend, courseid, roleid";

        $DB->execute($sql);

        stats_progress('3');

    /// activity read/write averages
        $sql = "INSERT INTO {stats_weekly} (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'activity', ntimeend, courseid, roleid, SUM(stat1), SUM(stat2)
                  FROM (
                           SELECT $nextstartweek AS ntimeend, courseid, roleid, stat1, stat2
                             FROM {stats_daily}
                            WHERE stattype = 'activity' AND $stattimesql
                       ) inline_view
              GROUP BY ntimeend, courseid, roleid";

        $DB->execute($sql);

        stats_progress('4');

    /// user read/write averages
        $sql = "INSERT INTO {stats_user_weekly} (stattype, timeend, courseid, userid, statsreads, statswrites)

                SELECT 'activity', ntimeend, courseid, userid, SUM(statsreads), SUM(statswrites)
                  FROM (
                           SELECT $nextstartweek AS ntimeend, courseid, userid, statsreads, statswrites
                             FROM {stats_user_daily}
                            WHERE stattype = 'activity' AND $stattimesql
                       ) inline_view
              GROUP BY ntimeend, courseid, userid";

        $DB->execute($sql);

        stats_progress('5');

        set_config('statslastweekly', $nextstartweek);
        $elapsed = time()-$weekstart;
        mtrace(" finished until $nextstartweek: ".userdate($nextstartweek) ." (in $elapsed s)");

        $timestart     = $nextstartweek;
        $nextstartweek = stats_get_next_week_start($nextstartweek);
    }

    set_cron_lock('statsrunning', null);
    mtrace("...completed $weeks weeks of statistics.");
    return true;
}

/**
 * Execute monthly statistics gathering
 * @return boolean success
 */
function stats_cron_monthly() {
    global $CFG, $DB;
    require_once($CFG->libdir.'/adminlib.php');

    $now = time();

    // read last execution date from db
    if (!$timestart = get_config(NULL, 'statslastmonthly')) {
        $timestart = stats_get_base_monthly(stats_get_start_from('monthly'));
        set_config('statslastmonthly', $timestart);
    }

    $nextstartmonth = stats_get_next_month_start($timestart);

    // are there any months that need to be processed?
    if ($now < $nextstartmonth) {
        return true; // everything ok and up-to-date
    }

    $timeout = empty($CFG->statsmaxruntime) ? 60*60*24 : $CFG->statsmaxruntime;

    if (!set_cron_lock('statsrunning', $now + $timeout)) {
        return false;
    }

    // fisr delete entries that should not be there yet
    $DB->delete_records_select('stats_monthly', "timeend > $timestart");
    $DB->delete_records_select('stats_user_monthly', "timeend > $timestart");

    $startmonth = stats_get_base_monthly($now);


    mtrace("Running monthly statistics gathering, starting at $timestart:");
    \core\cron::trace_time_and_memory();

    $months = 0;
    while ($now > $nextstartmonth) {
        core_php_time_limit::raise($timeout - 200);
        $months++;

        if ($months > 1) {
            // move the lock
            set_cron_lock('statsrunning', time() + $timeout, true);
        }

        $stattimesql = "timeend > $timestart AND timeend <= $nextstartmonth";

        $monthstart = time();
        stats_progress('init');

    /// process login info first
        $sql = "INSERT INTO {stats_user_monthly} (stattype, timeend, courseid, userid, statsreads)

                SELECT 'logins', timeend, courseid, userid, SUM(statsreads)
                  FROM (
                           SELECT $nextstartmonth AS timeend, courseid, userid, statsreads
                             FROM {stats_user_daily} sd
                            WHERE stattype = 'logins' AND $stattimesql
                       ) inline_view
              GROUP BY timeend, courseid, userid
                HAVING SUM(statsreads) > 0";

        $DB->execute($sql);

        stats_progress('1');

        $sql = "INSERT INTO {stats_monthly} (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'logins' AS stattype, $nextstartmonth AS timeend, ".SITEID." as courseid, 0,
                       COALESCE((SELECT SUM(statsreads)
                                   FROM {stats_user_monthly} s1
                                  WHERE s1.stattype = 'logins' AND timeend = $nextstartmonth), 0) AS nstat1,
                       (SELECT COUNT('x')
                          FROM {stats_user_monthly} s2
                         WHERE s2.stattype = 'logins' AND timeend = $nextstartmonth) AS nstat2" .
                $DB->sql_null_from_clause();

        $DB->execute($sql);

        stats_progress('2');

    /// now enrolments averages
        $sql = "INSERT INTO {stats_monthly} (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'enrolments', ntimeend, courseid, roleid, " . $DB->sql_ceil('AVG(stat1)') . ", " . $DB->sql_ceil('AVG(stat2)') . "
                  FROM (
                           SELECT $nextstartmonth AS ntimeend, courseid, roleid, stat1, stat2
                             FROM {stats_daily} sd
                            WHERE stattype = 'enrolments' AND $stattimesql
                       ) inline_view
              GROUP BY ntimeend, courseid, roleid";

        $DB->execute($sql);

        stats_progress('3');

    /// activity read/write averages
        $sql = "INSERT INTO {stats_monthly} (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'activity', ntimeend, courseid, roleid, SUM(stat1), SUM(stat2)
                  FROM (
                           SELECT $nextstartmonth AS ntimeend, courseid, roleid, stat1, stat2
                             FROM {stats_daily}
                            WHERE stattype = 'activity' AND $stattimesql
                       ) inline_view
              GROUP BY ntimeend, courseid, roleid";

        $DB->execute($sql);

        stats_progress('4');

    /// user read/write averages
        $sql = "INSERT INTO {stats_user_monthly} (stattype, timeend, courseid, userid, statsreads, statswrites)

                SELECT 'activity', ntimeend, courseid, userid, SUM(statsreads), SUM(statswrites)
                  FROM (
                           SELECT $nextstartmonth AS ntimeend, courseid, userid, statsreads, statswrites
                             FROM {stats_user_daily}
                            WHERE stattype = 'activity' AND $stattimesql
                       ) inline_view
              GROUP BY ntimeend, courseid, userid";

        $DB->execute($sql);

        stats_progress('5');

        set_config('statslastmonthly', $nextstartmonth);
        $elapsed = time() - $monthstart;
        mtrace(" finished until $nextstartmonth: ".userdate($nextstartmonth) ." (in $elapsed s)");

        $timestart      = $nextstartmonth;
        $nextstartmonth = stats_get_next_month_start($nextstartmonth);
    }

    set_cron_lock('statsrunning', null);
    mtrace("...completed $months months of statistics.");
    return true;
}

/**
 * Return starting date of stats processing
 * @param string $str name of table - daily, weekly or monthly
 * @return int timestamp
 */
function stats_get_start_from($str) {
    global $CFG, $DB;

    // are there any data in stats table? Should not be...
    if ($timeend = $DB->get_field_sql('SELECT MAX(timeend) FROM {stats_'.$str.'}')) {
        return $timeend;
    }
    // decide what to do based on our config setting (either all or none or a timestamp)
    switch ($CFG->statsfirstrun) {
        case 'all':
            $manager = get_log_manager();
            $stores = $manager->get_readers();
            $firstlog = false;
            foreach ($stores as $store) {
                if ($store instanceof \core\log\sql_internal_table_reader) {
                    $logtable = $store->get_internal_log_table_name();
                    if (!$logtable) {
                        continue;
                    }
                    $first = $DB->get_field_sql("SELECT MIN(timecreated) FROM {{$logtable}}");
                    if ($first and (!$firstlog or $firstlog > $first)) {
                        $firstlog = $first;
                    }
                }
            }

            $first = $DB->get_field_sql('SELECT MIN(time) FROM {log}');
            if ($first and (!$firstlog or $firstlog > $first)) {
                $firstlog = $first;
            }

            if ($firstlog) {
                return $firstlog;
            }

        default:
            if (is_numeric($CFG->statsfirstrun)) {
                return time() - $CFG->statsfirstrun;
            }
            // not a number? use next instead
        case 'none':
            return strtotime('-3 day', time());
    }
}

/**
 * Start of day
 * @param int $time timestamp
 * @return int start of day
 */
function stats_get_base_daily($time=0) {
    if (empty($time)) {
        $time = time();
    }

    core_date::set_default_server_timezone();
    $time = strtotime(date('d-M-Y', $time));

    return $time;
}

/**
 * Start of week
 * @param int $time timestamp
 * @return int start of week
 */
function stats_get_base_weekly($time=0) {
    global $CFG;

    $datetime = new DateTime();
    $datetime->setTimestamp(stats_get_base_daily($time));
    $startday = $CFG->calendar_startwday;

    core_date::set_default_server_timezone();
    $thisday = date('w', $time);

    $days = 0;

    if ($thisday > $startday) {
        $days = $thisday - $startday;
    } else if ($thisday < $startday) {
        $days = 7 + $thisday - $startday;
    }

    $datetime->sub(new DateInterval("P{$days}D"));

    return $datetime->getTimestamp();
}

/**
 * Start of month
 * @param int $time timestamp
 * @return int start of month
 */
function stats_get_base_monthly($time=0) {
    if (empty($time)) {
        $time = time();
    }

    core_date::set_default_server_timezone();
    $return = strtotime(date('1-M-Y', $time));

    return $return;
}

/**
 * Start of next day
 * @param int $time timestamp
 * @return start of next day
 */
function stats_get_next_day_start($time) {
    $next = stats_get_base_daily($time);
    $nextdate = new DateTime();
    $nextdate->setTimestamp($next);
    $nextdate->add(new DateInterval('P1D'));
    return $nextdate->getTimestamp();
}

/**
 * Start of next week
 * @param int $time timestamp
 * @return start of next week
 */
function stats_get_next_week_start($time) {
    $next = stats_get_base_weekly($time);
    $nextdate = new DateTime();
    $nextdate->setTimestamp($next);
    $nextdate->add(new DateInterval('P1W'));
    return $nextdate->getTimestamp();
}

/**
 * Start of next month
 * @param int $time timestamp
 * @return start of next month
 */
function stats_get_next_month_start($time) {
    $next = stats_get_base_monthly($time);
    $nextdate = new DateTime();
    $nextdate->setTimestamp($next);
    $nextdate->add(new DateInterval('P1M'));
    return $nextdate->getTimestamp();
}

/**
 * Remove old stats data
 */
function stats_clean_old() {
    global $DB;
    mtrace("Running stats cleanup tasks...");
    \core\cron::trace_time_and_memory();
    $deletebefore =  stats_get_base_monthly();

    // delete dailies older than 3 months (to be safe)
    $deletebefore = strtotime('-3 months', $deletebefore);
    $DB->delete_records_select('stats_daily',      "timeend < $deletebefore");
    $DB->delete_records_select('stats_user_daily', "timeend < $deletebefore");

    // delete weeklies older than 9  months (to be safe)
    $deletebefore = strtotime('-6 months', $deletebefore);
    $DB->delete_records_select('stats_weekly',      "timeend < $deletebefore");
    $DB->delete_records_select('stats_user_weekly', "timeend < $deletebefore");

    // don't delete monthlies

    mtrace("...stats cleanup finished");
}

function stats_get_parameters($time,$report,$courseid,$mode,$roleid=0) {
    global $CFG, $DB;

    $param = new stdClass();
    $param->params = array();

    if ($time < 10) { // dailies
        // number of days to go back = 7* time
        $param->table = 'daily';
        $param->timeafter = strtotime("-".($time*7)." days",stats_get_base_daily());
    } elseif ($time < 20) { // weeklies
        // number of weeks to go back = time - 10 * 4 (weeks) + base week
        $param->table = 'weekly';
        $param->timeafter = strtotime("-".(($time - 10)*4)." weeks",stats_get_base_weekly());
    } else { // monthlies.
        // number of months to go back = time - 20 * months + base month
        $param->table = 'monthly';
        $param->timeafter = strtotime("-".($time - 20)." months",stats_get_base_monthly());
    }

    $param->extras = '';

    switch ($report) {
    // ******************** STATS_MODE_GENERAL ******************** //
    case STATS_REPORT_LOGINS:
        $param->fields = 'timeend,sum(stat1) as line1,sum(stat2) as line2';
        $param->fieldscomplete = true;
        $param->stattype = 'logins';
        $param->line1 = get_string('statslogins');
        $param->line2 = get_string('statsuniquelogins');
        if ($courseid == SITEID) {
            $param->extras = 'GROUP BY timeend';
        }
        break;

    case STATS_REPORT_READS:
        $param->fields = $DB->sql_concat('timeend','roleid').' AS uniqueid, timeend, roleid, stat1 as line1';
        $param->fieldscomplete = true; // set this to true to avoid anything adding stuff to the list and breaking complex queries.
        $param->aggregategroupby = 'roleid';
        $param->stattype = 'activity';
        $param->crosstab = true;
        $param->extras = 'GROUP BY timeend,roleid,stat1';
        if ($courseid == SITEID) {
            $param->fields = $DB->sql_concat('timeend','roleid').' AS uniqueid, timeend, roleid, sum(stat1) as line1';
            $param->extras = 'GROUP BY timeend,roleid';
        }
        break;

    case STATS_REPORT_WRITES:
        $param->fields = $DB->sql_concat('timeend','roleid').' AS uniqueid, timeend, roleid, stat2 as line1';
        $param->fieldscomplete = true; // set this to true to avoid anything adding stuff to the list and breaking complex queries.
        $param->aggregategroupby = 'roleid';
        $param->stattype = 'activity';
        $param->crosstab = true;
        $param->extras = 'GROUP BY timeend,roleid,stat2';
        if ($courseid == SITEID) {
            $param->fields = $DB->sql_concat('timeend','roleid').' AS uniqueid, timeend, roleid, sum(stat2) as line1';
            $param->extras = 'GROUP BY timeend,roleid';
        }
        break;

    case STATS_REPORT_ACTIVITY:
        $param->fields = $DB->sql_concat('timeend','roleid').' AS uniqueid, timeend, roleid, sum(stat1+stat2) as line1';
        $param->fieldscomplete = true; // set this to true to avoid anything adding stuff to the list and breaking complex queries.
        $param->aggregategroupby = 'roleid';
        $param->stattype = 'activity';
        $param->crosstab = true;
        $param->extras = 'GROUP BY timeend,roleid';
        if ($courseid == SITEID) {
            $param->extras = 'GROUP BY timeend,roleid';
        }
        break;

    case STATS_REPORT_ACTIVITYBYROLE;
        $param->fields = 'stat1 AS line1, stat2 AS line2';
        $param->stattype = 'activity';
        $rolename = '';
        if ($roleid <> 0) {
            if ($role = $DB->get_record('role', ['id' => $roleid])) {
                $rolename = role_get_name($role, context_course::instance($courseid)) . ' ';
            }
        }
        $param->line1 = $rolename . get_string('statsreads');
        $param->line2 = $rolename . get_string('statswrites');
        if ($courseid == SITEID) {
            $param->extras = 'GROUP BY timeend';
        }
        break;

    // ******************** STATS_MODE_DETAILED ******************** //
    case STATS_REPORT_USER_ACTIVITY:
        $param->fields = 'statsreads as line1, statswrites as line2';
        $param->line1 = get_string('statsuserreads');
        $param->line2 = get_string('statsuserwrites');
        $param->stattype = 'activity';
        break;

    case STATS_REPORT_USER_ALLACTIVITY:
        $param->fields = 'statsreads+statswrites as line1';
        $param->line1 = get_string('statsuseractivity');
        $param->stattype = 'activity';
        break;

    case STATS_REPORT_USER_LOGINS:
        $param->fields = 'statsreads as line1';
        $param->line1 = get_string('statsuserlogins');
        $param->stattype = 'logins';
        break;

    case STATS_REPORT_USER_VIEW:
        $param->fields = 'timeend, SUM(statsreads) AS line1, SUM(statswrites) AS line2, SUM(statsreads+statswrites) AS line3';
        $param->fieldscomplete = true;
        $param->line1 = get_string('statsuserreads');
        $param->line2 = get_string('statsuserwrites');
        $param->line3 = get_string('statsuseractivity');
        $param->stattype = 'activity';
        $param->extras = "GROUP BY timeend";
        break;

    // ******************** STATS_MODE_RANKED ******************** //
    case STATS_REPORT_ACTIVE_COURSES:
        $param->fields = 'sum(stat1+stat2) AS line1';
        $param->stattype = 'activity';
        $param->orderby = 'line1 DESC';
        $param->line1 = get_string('useractivity');
        $param->graphline = 'line1';
        break;

    case STATS_REPORT_ACTIVE_COURSES_WEIGHTED:
        $threshold = 0;
        if (!empty($CFG->statsuserthreshold) && is_numeric($CFG->statsuserthreshold)) {
            $threshold = $CFG->statsuserthreshold;
        }
        $param->fields = '';
        $param->sql = 'SELECT activity.courseid, activity.all_activity AS line1, enrolments.highest_enrolments AS line2,
                        activity.all_activity / enrolments.highest_enrolments as line3
                       FROM (
                            SELECT courseid, sum(stat1+stat2) AS all_activity
                              FROM {stats_'.$param->table.'}
                             WHERE stattype=\'activity\' AND timeend >= '.(int)$param->timeafter.' AND roleid = 0 GROUP BY courseid
                       ) activity
                       INNER JOIN
                            (
                            SELECT courseid, max(stat1) AS highest_enrolments
                              FROM {stats_'.$param->table.'}
                             WHERE stattype=\'enrolments\' AND timeend >= '.(int)$param->timeafter.' AND stat1 > '.(int)$threshold.'
                          GROUP BY courseid
                      ) enrolments
                      ON (activity.courseid = enrolments.courseid)
                      ORDER BY line3 DESC';
        $param->line1 = get_string('useractivity');
        $param->line2 = get_string('users');
        $param->line3 = get_string('activityweighted');
        $param->graphline = 'line3';
        break;

    case STATS_REPORT_PARTICIPATORY_COURSES:
        $threshold = 0;
        if (!empty($CFG->statsuserthreshold) && is_numeric($CFG->statsuserthreshold)) {
            $threshold = $CFG->statsuserthreshold;
        }
        $param->fields = '';
        $param->sql = 'SELECT courseid, ' . $DB->sql_ceil('avg(all_enrolments)') . ' as line1, ' .
                         $DB->sql_ceil('avg(active_enrolments)') . ' as line2, avg(proportion_active) AS line3
                       FROM (
                           SELECT courseid, timeend, stat2 as active_enrolments,
                                  stat1 as all_enrolments, '.$DB->sql_cast_char2real('stat2').'/'.$DB->sql_cast_char2real('stat1').' AS proportion_active
                             FROM {stats_'.$param->table.'}
                            WHERE stattype=\'enrolments\' AND roleid = 0 AND stat1 > '.(int)$threshold.'
                       ) aq
                       WHERE timeend >= '.(int)$param->timeafter.'
                       GROUP BY courseid
                       ORDER BY line3 DESC';

        $param->line1 = get_string('users');
        $param->line2 = get_string('activeusers');
        $param->line3 = get_string('participationratio');
        $param->graphline = 'line3';
        break;

    case STATS_REPORT_PARTICIPATORY_COURSES_RW:
        $param->fields = '';
        $param->sql =  'SELECT courseid, sum(views) AS line1, sum(posts) AS line2,
                           avg(proportion_active) AS line3
                         FROM (
                           SELECT courseid, timeend, stat1 as views, stat2 AS posts,
                                  '.$DB->sql_cast_char2real('stat2').'/'.$DB->sql_cast_char2real('stat1').' as proportion_active
                             FROM {stats_'.$param->table.'}
                            WHERE stattype=\'activity\' AND roleid = 0 AND stat1 > 0
                       ) aq
                       WHERE timeend >= '.(int)$param->timeafter.'
                       GROUP BY courseid
                       ORDER BY line3 DESC';
        $param->line1 = get_string('views');
        $param->line2 = get_string('posts');
        $param->line3 = get_string('participationratio');
        $param->graphline = 'line3';
        break;
    }

    /*
    if ($courseid == SITEID && $mode != STATS_MODE_RANKED) { // just aggregate all courses.
        $param->fields = preg_replace('/(?:sum)([a-zA-Z0-9+_]*)\W+as\W+([a-zA-Z0-9_]*)/i','sum($1) as $2',$param->fields);
        $param->extras = ' GROUP BY timeend'.((!empty($param->aggregategroupby)) ? ','.$param->aggregategroupby : '');
    }
    */
    //TODO must add the SITEID reports to the rest of the reports.
    return $param;
}

function stats_get_view_actions() {
    return array('view','view all','history');
}

function stats_get_post_actions() {
    return array('add','delete','edit','add mod','delete mod','edit section'.'enrol','loginas','new','unenrol','update','update mod');
}

function stats_get_action_names($str) {
    global $CFG, $DB;

    $mods = $DB->get_records('modules');
    $function = 'stats_get_'.$str.'_actions';
    $actions = $function();
    foreach ($mods as $mod) {
        $file = $CFG->dirroot.'/mod/'.$mod->name.'/lib.php';
        if (!is_readable($file)) {
            continue;
        }
        require_once($file);
        $function = $mod->name.'_get_'.$str.'_actions';
        if (function_exists($function)) {
            $mod_actions = $function();
            if (is_array($mod_actions)) {
                $actions = array_merge($actions, $mod_actions);
            }
        }
    }

    // The array_values() forces a stack-like array
    // so we can later loop over safely...
    $actions =  array_values(array_unique($actions));
    $c = count($actions);
    for ($n=0;$n<$c;$n++) {
        $actions[$n] = $actions[$n];
    }
    return $actions;
}

function stats_get_time_options($now,$lastweekend,$lastmonthend,$earliestday,$earliestweek,$earliestmonth) {

    $now = stats_get_base_daily(time());
    // it's really important that it's TIMEEND in the table. ie, tuesday 00:00:00 is monday night.
    // so we need to take a day off here (essentially add a day to $now
    $now += 60*60*24;

    $timeoptions = array();

    if ($now - (60*60*24*7) >= $earliestday) {
        $timeoptions[STATS_TIME_LASTWEEK] = get_string('numweeks','moodle',1);
    }
    if ($now - (60*60*24*14) >= $earliestday) {
        $timeoptions[STATS_TIME_LAST2WEEKS] = get_string('numweeks','moodle',2);
    }
    if ($now - (60*60*24*21) >= $earliestday) {
        $timeoptions[STATS_TIME_LAST3WEEKS] = get_string('numweeks','moodle',3);
    }
    if ($now - (60*60*24*28) >= $earliestday) {
        $timeoptions[STATS_TIME_LAST4WEEKS] = get_string('numweeks','moodle',4);// show dailies up to (including) here.
    }
    if ($lastweekend - (60*60*24*56) >= $earliestweek) {
        $timeoptions[STATS_TIME_LAST2MONTHS] = get_string('nummonths','moodle',2);
    }
    if ($lastweekend - (60*60*24*84) >= $earliestweek) {
        $timeoptions[STATS_TIME_LAST3MONTHS] = get_string('nummonths','moodle',3);
    }
    if ($lastweekend - (60*60*24*112) >= $earliestweek) {
        $timeoptions[STATS_TIME_LAST4MONTHS] = get_string('nummonths','moodle',4);
    }
    if ($lastweekend - (60*60*24*140) >= $earliestweek) {
        $timeoptions[STATS_TIME_LAST5MONTHS] = get_string('nummonths','moodle',5);
    }
    if ($lastweekend - (60*60*24*168) >= $earliestweek) {
        $timeoptions[STATS_TIME_LAST6MONTHS] = get_string('nummonths','moodle',6); // show weeklies up to (including) here
    }
    if (strtotime('-7 months',$lastmonthend) >= $earliestmonth) {
        $timeoptions[STATS_TIME_LAST7MONTHS] = get_string('nummonths','moodle',7);
    }
    if (strtotime('-8 months',$lastmonthend) >= $earliestmonth) {
        $timeoptions[STATS_TIME_LAST8MONTHS] = get_string('nummonths','moodle',8);
    }
    if (strtotime('-9 months',$lastmonthend) >= $earliestmonth) {
        $timeoptions[STATS_TIME_LAST9MONTHS] = get_string('nummonths','moodle',9);
    }
    if (strtotime('-10 months',$lastmonthend) >= $earliestmonth) {
        $timeoptions[STATS_TIME_LAST10MONTHS] = get_string('nummonths','moodle',10);
    }
    if (strtotime('-11 months',$lastmonthend) >= $earliestmonth) {
        $timeoptions[STATS_TIME_LAST11MONTHS] = get_string('nummonths','moodle',11);
    }
    if (strtotime('-1 year',$lastmonthend) >= $earliestmonth) {
        $timeoptions[STATS_TIME_LASTYEAR] = get_string('lastyear');
    }

    $years = (int)date('y', $now) - (int)date('y', $earliestmonth);
    if ($years > 1) {
        for($i = 2; $i <= $years; $i++) {
            $timeoptions[$i*12+20] = get_string('numyears', 'moodle', $i);
        }
    }

    return $timeoptions;
}

function stats_get_report_options($courseid,$mode) {
    global $CFG, $DB;

    $reportoptions = array();

    switch ($mode) {
    case STATS_MODE_GENERAL:
        $reportoptions[STATS_REPORT_ACTIVITY] = get_string('statsreport'.STATS_REPORT_ACTIVITY);
        if ($courseid != SITEID && $context = context_course::instance($courseid)) {
            $sql = 'SELECT r.id, r.name, r.shortname FROM {role} r JOIN {stats_daily} s ON s.roleid = r.id
                 WHERE s.courseid = :courseid GROUP BY r.id, r.name, r.shortname';
            if ($roles = $DB->get_records_sql($sql, array('courseid' => $courseid))) {
                $roles = array_intersect_key($roles, get_viewable_roles($context));
                foreach ($roles as $role) {
                    $reportoptions[STATS_REPORT_ACTIVITYBYROLE.$role->id] = get_string('statsreport'.STATS_REPORT_ACTIVITYBYROLE).
                        ' ' . role_get_name($role, $context);
                }
            }
        }
        $reportoptions[STATS_REPORT_READS] = get_string('statsreport'.STATS_REPORT_READS);
        $reportoptions[STATS_REPORT_WRITES] = get_string('statsreport'.STATS_REPORT_WRITES);
        if ($courseid == SITEID) {
            $reportoptions[STATS_REPORT_LOGINS] = get_string('statsreport'.STATS_REPORT_LOGINS);
        }

        break;
    case STATS_MODE_DETAILED:
        $reportoptions[STATS_REPORT_USER_ACTIVITY] = get_string('statsreport'.STATS_REPORT_USER_ACTIVITY);
        $reportoptions[STATS_REPORT_USER_ALLACTIVITY] = get_string('statsreport'.STATS_REPORT_USER_ALLACTIVITY);
        if (has_capability('report/stats:view', context_system::instance())) {
            $site = get_site();
            $reportoptions[STATS_REPORT_USER_LOGINS] = get_string('statsreport'.STATS_REPORT_USER_LOGINS);
        }
        break;
    case STATS_MODE_RANKED:
        if (has_capability('report/stats:view', context_system::instance())) {
            $reportoptions[STATS_REPORT_ACTIVE_COURSES] = get_string('statsreport'.STATS_REPORT_ACTIVE_COURSES);
            $reportoptions[STATS_REPORT_ACTIVE_COURSES_WEIGHTED] = get_string('statsreport'.STATS_REPORT_ACTIVE_COURSES_WEIGHTED);
            $reportoptions[STATS_REPORT_PARTICIPATORY_COURSES] = get_string('statsreport'.STATS_REPORT_PARTICIPATORY_COURSES);
            $reportoptions[STATS_REPORT_PARTICIPATORY_COURSES_RW] = get_string('statsreport'.STATS_REPORT_PARTICIPATORY_COURSES_RW);
        }
        break;
    }

    return $reportoptions;
}

/**
 * Fix missing entries in the statistics.
 *
 * This creates a dummy stat when nothing happened during a day/week/month.
 *
 * @param array $stats array of statistics.
 * @param int $timeafter unused.
 * @param string $timestr type of statistics to generate (dayly, weekly, monthly).
 * @param boolean $line2
 * @param boolean $line3
 * @return array of fixed statistics.
 */
function stats_fix_zeros($stats,$timeafter,$timestr,$line2=true,$line3=false) {

    if (empty($stats)) {
        return;
    }

    $timestr = str_replace('user_','',$timestr); // just in case.

    // Gets the current user base time.
    $fun = 'stats_get_base_'.$timestr;
    $now = $fun();

    // Extract the ending time of the statistics.
    $actualtimes = array();
    $actualtimeshour = null;
    foreach ($stats as $statid => $s) {
        // Normalise the month date to the 1st if for any reason it's set to later. But we ignore
        // anything above or equal to 29 because sometimes we get the end of the month. Also, we will
        // set the hours of the result to all of them, that way we prevent DST differences.
        if ($timestr == 'monthly') {
            $day = date('d', $s->timeend);
            if (date('d', $s->timeend) > 1 && date('d', $s->timeend) < 29) {
                $day = 1;
            }
            if (is_null($actualtimeshour)) {
                $actualtimeshour = date('H', $s->timeend);
            }
            $s->timeend = mktime($actualtimeshour, 0, 0, date('m', $s->timeend), $day, date('Y', $s->timeend));
        }
        $stats[$statid] = $s;
        $actualtimes[] = $s->timeend;
    }

    $actualtimesvalues = array_values($actualtimes);
    $timeafter = array_pop($actualtimesvalues);

    // Generate a base timestamp for each possible month/week/day.
    $times = array();
    while ($timeafter < $now) {
        $times[] = $timeafter;
        if ($timestr == 'daily') {
            $timeafter = stats_get_next_day_start($timeafter);
        } else if ($timestr == 'weekly') {
            $timeafter = stats_get_next_week_start($timeafter);
        } else if ($timestr == 'monthly') {
            // We can't just simply +1 month because the 31st Jan + 1 month = 2nd of March.
            $year = date('Y', $timeafter);
            $month = date('m', $timeafter);
            $day = date('d', $timeafter);
            $dayofnextmonth = $day;
            if ($day >= 29) {
                $daysinmonth = date('n', mktime(0, 0, 0, $month+1, 1, $year));
                if ($day > $daysinmonth) {
                    $dayofnextmonth = $daysinmonth;
                }
            }
            $timeafter = mktime($actualtimeshour, 0, 0, $month+1, $dayofnextmonth, $year);
        } else {
            // This will put us in a never ending loop.
            return $stats;
        }
    }

    // Add the base timestamp to the statistics if not present.
    foreach ($times as $count => $time) {
        if (!in_array($time,$actualtimes) && $count != count($times) -1) {
            $newobj = new StdClass;
            $newobj->timeend = $time;
            $newobj->id = 0;
            $newobj->roleid = 0;
            $newobj->line1 = 0;
            if (!empty($line2)) {
                $newobj->line2 = 0;
            }
            if (!empty($line3)) {
                $newobj->line3 = 0;
            }
            $newobj->zerofixed = true;
            $stats[] = $newobj;
        }
    }

    usort($stats,"stats_compare_times");
    return $stats;
}

// helper function to sort arrays by $obj->timeend
function stats_compare_times($a,$b) {
   if ($a->timeend == $b->timeend) {
       return 0;
   }
   return ($a->timeend > $b->timeend) ? -1 : 1;
}

function stats_check_uptodate($courseid=0) {
    global $CFG, $DB;

    if (empty($courseid)) {
        $courseid = SITEID;
    }

    $latestday = stats_get_start_from('daily');

    if ((time() - 60*60*24*2) < $latestday) { // we're ok
        return NULL;
    }

    $a = new stdClass();
    $a->daysdone = $DB->get_field_sql("SELECT COUNT(DISTINCT(timeend)) FROM {stats_daily}");

    // how many days between the last day and now?
    $a->dayspending = ceil((stats_get_base_daily() - $latestday)/(60*60*24));

    if ($a->dayspending == 0 && $a->daysdone != 0) {
        return NULL; // we've only just started...
    }

    //return error as string
    return get_string('statscatchupmode','error',$a);
}

/**
 * Create temporary tables to speed up log generation
 */
function stats_temp_table_create() {
    global $CFG, $DB;

    $dbman = $DB->get_manager(); // We are going to use database_manager services

    stats_temp_table_drop();

    $tables = array();

    /// Define tables user to be created
    $table = new xmldb_table('temp_stats_daily');
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('courseid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
    $table->add_field('timeend', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
    $table->add_field('roleid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
    $table->add_field('stattype', XMLDB_TYPE_CHAR, 20, null, XMLDB_NOTNULL, null, 'activity');
    $table->add_field('stat1', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
    $table->add_field('stat2', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid'));
    $table->add_index('timeend', XMLDB_INDEX_NOTUNIQUE, array('timeend'));
    $table->add_index('roleid', XMLDB_INDEX_NOTUNIQUE, array('roleid'));
    $tables['temp_stats_daily'] = $table;

    $table = new xmldb_table('temp_stats_user_daily');
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('courseid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
    $table->add_field('userid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
    $table->add_field('roleid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
    $table->add_field('timeend', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
    $table->add_field('statsreads', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
    $table->add_field('statswrites', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
    $table->add_field('stattype', XMLDB_TYPE_CHAR, 30, null, XMLDB_NOTNULL, null, null);
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid'));
    $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));
    $table->add_index('timeend', XMLDB_INDEX_NOTUNIQUE, array('timeend'));
    $table->add_index('roleid', XMLDB_INDEX_NOTUNIQUE, array('roleid'));
    $tables['temp_stats_user_daily'] = $table;

    $table = new xmldb_table('temp_enroled');
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('userid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
    $table->add_field('courseid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
    $table->add_field('roleid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, null);
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));
    $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid'));
    $table->add_index('roleid', XMLDB_INDEX_NOTUNIQUE, array('roleid'));
    $table->add_index('useridroleidcourseid', XMLDB_INDEX_NOTUNIQUE, array('userid', 'roleid', 'courseid'));
    $tables['temp_enroled'] = $table;


    $table = new xmldb_table('temp_log1');
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('userid', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
    $table->add_field('course', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, '0');
    $table->add_field('action', XMLDB_TYPE_CHAR, 40, null, XMLDB_NOTNULL, null, null);
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_index('action', XMLDB_INDEX_NOTUNIQUE, array('action'));
    $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));
    $table->add_index('user', XMLDB_INDEX_NOTUNIQUE, array('userid'));
    $table->add_index('usercourseaction', XMLDB_INDEX_NOTUNIQUE, array('userid','course','action'));
    $tables['temp_log1'] = $table;

    /// temp_log2 is exactly the same as temp_log1.
    $tables['temp_log2'] = clone $tables['temp_log1'];
    $tables['temp_log2']->setName('temp_log2');

    try {

        foreach ($tables as $table) {
            $dbman->create_temp_table($table);
        }

    } catch (Exception $e) {
        mtrace('Temporary table creation failed: '. $e->getMessage());
        return false;
    }

    return true;
}

/**
 * Deletes summary logs table for stats calculation
 */
function stats_temp_table_drop() {
    global $DB;

    $dbman = $DB->get_manager();

    $tables = array('temp_log1', 'temp_log2', 'temp_stats_daily', 'temp_stats_user_daily', 'temp_enroled');

    foreach ($tables as $name) {

        if ($dbman->table_exists($name)) {
            $table = new xmldb_table($name);

            try {
                $dbman->drop_table($table);
            } catch (Exception $e) {
                mtrace("Error occured while dropping temporary tables!");
            }
        }
    }
}

/**
 * Fills the temporary stats tables with new data
 *
 * This function is meant to be called once at the start of stats generation
 *
 * @param int timestart timestamp of the start time of logs view
 * @param int timeend timestamp of the end time of logs view
 * @return bool success (true) or failure(false)
 */
function stats_temp_table_setup() {
    global $DB;

    $sql = "INSERT INTO {temp_enroled} (userid, courseid, roleid)

               SELECT ue.userid, e.courseid, ra.roleid
                FROM {role_assignments} ra
                JOIN {context} c ON (c.id = ra.contextid AND c.contextlevel = :courselevel)
                JOIN {enrol} e ON e.courseid = c.instanceid
                JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = ra.userid)";

    return stats_run_query($sql, array('courselevel' => CONTEXT_COURSE));
}

/**
 * Fills the temporary stats tables with new data
 *
 * This function is meant to be called to get a new day of data
 *
 * @param int timestamp of the start time of logs view
 * @param int timestamp of the end time of logs view
 * @return bool success (true) or failure(false)
 */
function stats_temp_table_fill($timestart, $timeend) {
    global $DB;

    // First decide from where we want the data.

    $params = array('timestart' => $timestart,
                    'timeend' => $timeend,
                    'participating' => \core\event\base::LEVEL_PARTICIPATING,
                    'teaching' => \core\event\base::LEVEL_TEACHING,
                    'loginevent1' => '\core\event\user_loggedin',
                    'loginevent2' => '\core\event\user_loggedin',
    );

    $filled = false;
    $manager = get_log_manager();
    $stores = $manager->get_readers();
    foreach ($stores as $store) {
        if ($store instanceof \core\log\sql_internal_table_reader) {
            $logtable = $store->get_internal_log_table_name();
            if (!$logtable) {
                continue;
            }

            $sql = "SELECT COUNT('x')
                      FROM {{$logtable}}
                     WHERE timecreated >= :timestart AND timecreated < :timeend";

            if (!$DB->get_field_sql($sql, $params)) {
                continue;
            }

            // Let's fake the old records using new log data.
            // We want only data relevant to educational process
            // done by real users.

            $sql = "INSERT INTO {temp_log1} (userid, course, action)

            SELECT userid,
                   CASE
                      WHEN courseid IS NULL THEN ".SITEID."
                      WHEN courseid = 0 THEN ".SITEID."
                      ELSE courseid
                   END,
                   CASE
                       WHEN eventname = :loginevent1 THEN 'login'
                       WHEN crud = 'r' THEN 'view'
                       ELSE 'update'
                   END
              FROM {{$logtable}}
             WHERE timecreated >= :timestart AND timecreated < :timeend
                   AND (origin = 'web' OR origin = 'ws')
                   AND (edulevel = :participating OR edulevel = :teaching OR eventname = :loginevent2)";

            $DB->execute($sql, $params);
            $filled = true;
        }
    }

    if (!$filled) {
        // Fallback to legacy data.
        $sql = "INSERT INTO {temp_log1} (userid, course, action)

            SELECT userid, course, action
              FROM {log}
             WHERE time >= :timestart AND time < :timeend";

        $DB->execute($sql, $params);
    }

    $sql = 'INSERT INTO {temp_log2} (userid, course, action)

            SELECT userid, course, action FROM {temp_log1}';

    $DB->execute($sql);

    // We have just loaded all the temp tables, collect statistics for that.
    $DB->update_temp_table_stats();

    return true;
}


/**
 * Deletes summary logs table for stats calculation
 *
 * @return bool success (true) or failure(false)
 */
function stats_temp_table_clean() {
    global $DB;

    $sql = array();

    $sql['up1'] = 'INSERT INTO {stats_daily} (courseid, roleid, stattype, timeend, stat1, stat2)

                   SELECT courseid, roleid, stattype, timeend, stat1, stat2 FROM {temp_stats_daily}';

    $sql['up2'] = 'INSERT INTO {stats_user_daily}
                               (courseid, userid, roleid, timeend, statsreads, statswrites, stattype)

                   SELECT courseid, userid, roleid, timeend, statsreads, statswrites, stattype
                     FROM {temp_stats_user_daily}';

    foreach ($sql as $id => $query) {
        if (! stats_run_query($query)) {
            mtrace("Error during table cleanup!");
            return false;
        }
    }

    $tables = array('temp_log1', 'temp_log2', 'temp_stats_daily', 'temp_stats_user_daily');

    foreach ($tables as $name) {
        $DB->delete_records($name);
    }

    return true;
}
