<?php

    // THESE CONSTANTS ARE USED FOR THE REPORTING PAGE.

    define('STATS_REPORT_LOGINS',1); // double impose logins and unqiue logins on a line graph. site course only.
    define('STATS_REPORT_READS',2); // double impose student reads and teacher reads on a line graph.
    define('STATS_REPORT_WRITES',3); // double impose student writes and teacher writes on a line graph.
    define('STATS_REPORT_ACTIVITY',4); // 2+3 added up, teacher vs student.
    define('STATS_REPORT_ACTIVITYBYROLE',5); // all activity, reads vs writes, seleted by role.

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

/**
 * Print daily cron progress
 * @param string $ident 
 */
function stats_daily_progress($ident) {
    static $start = 0;
    static $init  = 0;

    if ($ident == 'init') {
        $init = $start = time();
        return;
    }

    $elapsed = time() - $start;
    $start   = time();

    if (debugging('', DEBUG_ALL)) {
        mtrace("$ident:$elapsed ", '');
    } else {
        mtrace('.', '');
    }
}

/**
 * Execute daily statistics gathering
 * @param int $maxdays maximum number of days to be processed
 * @return boolean success
 */
function stats_cron_daily($maxdays=1) {
    global $CFG;

    $now = time();

    // read last execution date from db
    if (!$timestart = get_config(NULL, 'statslastdaily')) {
        $timestart = stats_get_base_daily(stats_get_start_from('daily'));
        set_config('statslastdaily', $timestart);
    }

    // calculate scheduled time
    $scheduledtime = stats_get_base_daily() + $CFG->statsruntimestarthour*60*60 + $CFG->statsruntimestartminute*60;

    // Note: This will work fine for sites running cron each 4 hours or less (hoppefully, 99.99% of sites). MDL-16709
    // check to make sure we're due to run, at least 20 hours after last run
    if (isset($CFG->statslastexecution) and ((time() - 20*60*60) < $CFG->statslastexecution)) {
        mtrace("...preventing stats to run, last execution was less than 20 hours ago.");
        return false;
    // also check that we are a max of 4 hours after scheduled time, stats won't run after that
    } else if (time() > $scheduledtime + 4*60*60) {
        mtrace("...preventing stats to run, more than 4 hours since scheduled time.");
        return false;
    } else {
        set_config('statslastexecution', time()); /// Grab this execution as last one
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

    // fisrt delete entries that should not be there yet
    delete_records_select('stats_daily',      "timeend > $timestart");
    delete_records_select('stats_user_daily', "timeend > $timestart");

    // Read in a few things we'll use later
    $viewactions = implode(',', stats_get_action_names('view'));
    $postactions = implode(',', stats_get_action_names('post'));

    $guest     = get_guest();
    $guestrole = get_guest_role();

    list($enroljoin, $enrolwhere)       = stats_get_enrolled_sql($CFG->statscatdepth, true);
    list($enroljoin_na, $enrolwhere_na) = stats_get_enrolled_sql($CFG->statscatdepth, false);
    list($fpjoin, $fpwhere)             = stats_get_enrolled_sql(0, true);

    mtrace("Running daily statistics gathering, starting at $timestart:");

    $days = 0;
    $failed = false; // failed stats flag

    while ($now > $nextmidnight) {
        if ($days >= $maxdays) {
            mtrace("...stopping early, reached maximum number of $maxdays days - will continue next time.");
            set_cron_lock('statsrunning', null);
            return false;
        }

        $days++;
        @set_time_limit($timeout - 200);

        if ($days > 1) {
            // move the lock
            set_cron_lock('statsrunning', time() + $timeout, true);
        }

        $daystart = time();

        $timesql  = "l.time >= $timestart  AND l.time  < $nextmidnight";
        $timesql1 = "l1.time >= $timestart AND l1.time < $nextmidnight";
        $timesql2 = "l2.time >= $timestart AND l2.time < $nextmidnight";

        stats_daily_progress('init');


    /// find out if any logs available for this day
        $sql = "SELECT 'x'
                  FROM {$CFG->prefix}log l
                 WHERE $timesql";
        $logspresent = get_records_sql($sql, 0, 1);

    /// process login info first
        $sql = "INSERT INTO {$CFG->prefix}stats_user_daily (stattype, timeend, courseid, userid, statsreads)

                SELECT 'logins', timeend, courseid, userid, count(statsreads)
                  FROM (
                           SELECT $nextmidnight AS timeend, ".SITEID." AS courseid, l.userid, l.id AS statsreads
                             FROM {$CFG->prefix}log l
                            WHERE action = 'login' AND $timesql
                       ) inline_view
              GROUP BY timeend, courseid, userid
                HAVING count(statsreads) > 0";

        if ($logspresent and !execute_sql($sql, false)) {
            $failed = true;
            break;
        }
        stats_daily_progress('1');

        $sql = "INSERT INTO {$CFG->prefix}stats_daily (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'logins' AS stattype, $nextmidnight AS timeend, ".SITEID." as courseid, 0,
                       COALESCE((SELECT SUM(statsreads)
                                       FROM {$CFG->prefix}stats_user_daily s1
                                      WHERE s1.stattype = 'logins' AND timeend = $nextmidnight), 0) AS stat1,
                       (SELECT COUNT('x')
                          FROM {$CFG->prefix}stats_user_daily s2
                         WHERE s2.stattype = 'logins' AND timeend = $nextmidnight) AS stat2" .
                sql_null_from_clause();

        if ($logspresent and !execute_sql($sql, false)) {
            $failed = true;
            break;
        }
        stats_daily_progress('2');


        // Enrolments and active enrolled users
        //
        // Unfortunately, we do not know how many users were registered
        // at given times in history :-(
        // - stat1: enrolled users
        // - stat2: enrolled users active in this period
        // - enrolment is defined now as having course:view capability in
        //   course context or above, we look 3 cats upwards only and ignore prevent
        //   and prohibit caps to simplify it
        // - SITEID is specialcased here, because it's all about default enrolment
        //   in that case, we'll count non-deleted users.
        //

        $sql = "INSERT INTO {$CFG->prefix}stats_daily (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'enrolments', timeend, courseid, roleid, COUNT(DISTINCT userid), 0
                  FROM (
                           SELECT $nextmidnight AS timeend, pl.courseid, pl.roleid, pl.userid
                             FROM (
                                      SELECT DISTINCT ra.roleid, ra.userid, c.id as courseid
                                        FROM {$CFG->prefix}role_assignments ra $enroljoin_na
                                       WHERE $enrolwhere_na
                                   ) pl
                       ) inline_view
              GROUP BY timeend, courseid, roleid";

        if (!execute_sql($sql, false)) {
            $failed = true;
            break;
        }
        stats_daily_progress('3');

        // using table alias in UPDATE does not work in pg < 8.2
        $sql = "UPDATE {$CFG->prefix}stats_daily
                   SET stat2 = (SELECT COUNT(DISTINCT ra.userid)
                                  FROM {$CFG->prefix}role_assignments ra $enroljoin_na
                                 WHERE ra.roleid = {$CFG->prefix}stats_daily.roleid AND
                                       c.id = {$CFG->prefix}stats_daily.courseid AND
                                       $enrolwhere_na AND
                                       EXISTS (SELECT 'x'
                                                 FROM {$CFG->prefix}log l
                                                WHERE l.course = {$CFG->prefix}stats_daily.courseid AND
                                                      l.userid = ra.userid AND $timesql))
                 WHERE {$CFG->prefix}stats_daily.stattype = 'enrolments' AND
                       {$CFG->prefix}stats_daily.timeend = $nextmidnight AND
                       {$CFG->prefix}stats_daily.courseid IN
                          (SELECT DISTINCT l.course
                             FROM {$CFG->prefix}log l
                            WHERE $timesql)";

        if ($logspresent and !execute_sql($sql, false)) {
            $failed = true;
            break;
        }
        stats_daily_progress('4');

    /// now get course total enrolments (roleid==0) - except frontpage
        $sql = "INSERT INTO {$CFG->prefix}stats_daily (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'enrolments', timeend, id, nroleid, COUNT(DISTINCT userid), 0
                  FROM (
                           SELECT $nextmidnight AS timeend, c.id, 0 AS nroleid, ra.userid
                             FROM {$CFG->prefix}role_assignments ra $enroljoin_na
                            WHERE c.id <> ".SITEID." AND $enrolwhere_na
                       ) inline_view
              GROUP BY timeend, id, nroleid
              HAVING COUNT(DISTINCT userid) > 0";

        if ($logspresent and !execute_sql($sql, false)) {
            $failed = true;
            break;
        }
        stats_daily_progress('5');

        $sql = "UPDATE {$CFG->prefix}stats_daily
                   SET stat2 = (SELECT COUNT(DISTINCT ra.userid)
                                  FROM {$CFG->prefix}role_assignments ra $enroljoin_na
                                 WHERE c.id = {$CFG->prefix}stats_daily.courseid AND
                                       $enrolwhere_na AND
                                       EXISTS (SELECT 'x'
                                                 FROM {$CFG->prefix}log l
                                                WHERE l.course = {$CFG->prefix}stats_daily.courseid AND
                                                      l.userid = ra.userid AND $timesql))
                 WHERE {$CFG->prefix}stats_daily.stattype = 'enrolments' AND
                       {$CFG->prefix}stats_daily.timeend = $nextmidnight AND
                       {$CFG->prefix}stats_daily.roleid = 0 AND
                       {$CFG->prefix}stats_daily.courseid IN
                          (SELECT l.course
                             FROM {$CFG->prefix}log l
                            WHERE $timesql AND l.course <> ".SITEID.")";

        if ($logspresent and !execute_sql($sql, false)) {
            $failed = true;
            break;
        }
        stats_daily_progress('6');

    /// frontapge(==site) enrolments total
        $sql = "INSERT INTO {$CFG->prefix}stats_daily (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'enrolments', $nextmidnight, ".SITEID.", 0,
                       (SELECT COUNT('x')
                          FROM {$CFG->prefix}user u
                         WHERE u.deleted = 0) AS stat1,
                       (SELECT COUNT(DISTINCT u.id)
                          FROM {$CFG->prefix}user u
                               JOIN {$CFG->prefix}log l ON l.userid = u.id
                         WHERE u.deleted = 0 AND $timesql) AS stat2" .
                sql_null_from_clause();

        if ($logspresent and !execute_sql($sql, false)) {
            $failed = true;
            break;
        }
        stats_daily_progress('7');

        if (empty($CFG->defaultfrontpageroleid)) { // 1.9 only, so far
            $defaultfproleid = 0;
        } else {
            $defaultfproleid = $CFG->defaultfrontpageroleid;
        }

    /// Default frontpage role enrolments are all site users (not deleted)
        if ($defaultfproleid) {
            // first remove default frontpage role counts if created by previous query
            $sql = "DELETE
                      FROM {$CFG->prefix}stats_daily
                     WHERE stattype = 'enrolments' AND courseid = ".SITEID." AND
                           roleid = $defaultfproleid AND timeend = $nextmidnight";
            if ($logspresent and !execute_sql($sql, false)) {
                $failed = true;
                break;
            }
            stats_daily_progress('8');

            $sql = "INSERT INTO {$CFG->prefix}stats_daily (stattype, timeend, courseid, roleid, stat1, stat2)

                    SELECT 'enrolments', $nextmidnight, ".SITEID.", $defaultfproleid,
                           (SELECT COUNT('x')
                              FROM {$CFG->prefix}user u
                             WHERE u.deleted = 0) AS stat1,
                           (SELECT COUNT(DISTINCT u.id)
                              FROM {$CFG->prefix}user u
                                   JOIN {$CFG->prefix}log l ON l.userid = u.id
                             WHERE u.deleted = 0 AND $timesql) AS stat2" .
                    sql_null_from_clause();

            if ($logspresent and !execute_sql($sql, false)) {
                $failed = true;
                break;
            }
            stats_daily_progress('9');

        } else {
            stats_daily_progress('x');
            stats_daily_progress('x');
        }



    /// individual user stats (including not-logged-in) in each course, this is slow - reuse this data if possible
        $sql = "INSERT INTO {$CFG->prefix}stats_user_daily (stattype, timeend, courseid, userid, statsreads, statswrites)

                SELECT 'activity' AS stattype, $nextmidnight AS timeend, d.courseid, d.userid,
                       (SELECT COUNT('x')
                          FROM {$CFG->prefix}log l
                         WHERE l.userid = d.userid AND
                               l.course = d.courseid AND $timesql AND
                               l.action IN ($viewactions)) AS statsreads,
                       (SELECT COUNT('x')
                          FROM {$CFG->prefix}log l
                         WHERE l.userid = d.userid AND
                               l.course = d.courseid AND $timesql AND
                               l.action IN ($postactions)) AS statswrites
                  FROM (SELECT DISTINCT u.id AS userid, l.course AS courseid
                          FROM {$CFG->prefix}user u, {$CFG->prefix}log l
                         WHERE u.id = l.userid AND $timesql
                       UNION
                        SELECT 0 AS userid, ".SITEID." AS courseid" . sql_null_from_clause() . ") d";
                        // can not use group by here because pg can not handle it :-(

        if ($logspresent and !execute_sql($sql, false)) {
            $failed = true;
            break;
        }
        stats_daily_progress('10');


    /// how many view/post actions in each course total
        $sql = "INSERT INTO {$CFG->prefix}stats_daily (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'activity' AS stattype, $nextmidnight AS timeend, c.id AS courseid, 0,
                       (SELECT COUNT('x')
                          FROM {$CFG->prefix}log l1
                         WHERE l1.course = c.id AND l1.action IN ($viewactions) AND
                               $timesql1) AS stat1,
                       (SELECT COUNT('x')
                          FROM {$CFG->prefix}log l2
                         WHERE l2.course = c.id AND l2.action IN ($postactions) AND
                               $timesql2) AS stat2
                  FROM {$CFG->prefix}course c
                 WHERE EXISTS (SELECT 'x'
                                 FROM {$CFG->prefix}log l
                                WHERE l.course = c.id and $timesql)";

        if ($logspresent and !execute_sql($sql, false)) {
            $failed = true;
            break;
        }
        stats_daily_progress('11');


    /// how many view actions for each course+role - excluding guests and frontpage

        $sql = "INSERT INTO {$CFG->prefix}stats_daily (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'activity', timeend, courseid, roleid, SUM(statsreads), SUM(statswrites)
                FROM (
                         SELECT $nextmidnight AS timeend, pl.courseid, pl.roleid, sud.statsreads, sud.statswrites
                         FROM {$CFG->prefix}stats_user_daily sud,
                                  (SELECT DISTINCT ra.userid, ra.roleid, c.id AS courseid
                                     FROM {$CFG->prefix}role_assignments ra $enroljoin
                                    WHERE c.id <> ".SITEID." AND
                                          ra.roleid <> $guestrole->id AND
                                          ra.userid <> $guest->id AND
                                          $enrolwhere
                                  ) pl
                         WHERE sud.userid = pl.userid AND
                               sud.courseid = pl.courseid AND
                               sud.timeend = $nextmidnight AND
                               sud.stattype='activity'
                     ) inline_view
            GROUP BY timeend, courseid, roleid
              HAVING SUM(statsreads) > 0 OR SUM(statswrites) > 0";

        if ($logspresent and !execute_sql($sql, false)) {
            $failed = true;
            break;
        }
        stats_daily_progress('12');

    /// how many view actions from guests only in each course - excluding frontpage
    /// (guest is anybody with guest role or no role with course:view in course - this may not work properly if category limit too low)
    /// normal users may enter course with temporary guest acces too

        $sql = "INSERT INTO {$CFG->prefix}stats_daily (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'activity', timeend, courseid, nroleid, SUM(statsreads), SUM(statswrites)
                  FROM (
                           SELECT $nextmidnight AS timeend, sud.courseid, $guestrole->id AS nroleid, sud.statsreads, sud.statswrites
                             FROM {$CFG->prefix}stats_user_daily sud
                            WHERE sud.timeend = $nextmidnight AND sud.courseid <> ".SITEID." AND
                                  sud.stattype='activity' AND
                                  (sud.userid = $guest->id OR sud.userid
                                    NOT IN (SELECT ra.userid
                                              FROM {$CFG->prefix}role_assignments ra $enroljoin
                                             WHERE c.id <> ".SITEID." AND  ra.roleid <> $guestrole->id AND
                                                   $enrolwhere))
                       ) inline_view
              GROUP BY timeend, courseid, nroleid
                HAVING SUM(statsreads) > 0 OR SUM(statswrites) > 0";

        if ($logspresent and !execute_sql($sql, false)) {
            $failed = true;
            break;
        }
        stats_daily_progress('13');


    /// how many view actions for each role on frontpage - excluding guests, not-logged-in and default frontpage role
        $sql = "INSERT INTO {$CFG->prefix}stats_daily (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'activity', timeend, courseid, roleid, SUM(statsreads), SUM(statswrites)
                  FROM (
                           SELECT $nextmidnight AS timeend, pl.courseid, pl.roleid, sud.statsreads, sud.statswrites
                             FROM {$CFG->prefix}stats_user_daily sud,
                                      (SELECT DISTINCT ra.userid, ra.roleid, c.id AS courseid
                                         FROM {$CFG->prefix}role_assignments ra $enroljoin
                                        WHERE c.id = ".SITEID." AND
                                              ra.roleid <> $defaultfproleid AND
                                              ra.roleid <> $guestrole->id AND
                                              ra.userid <> $guest->id AND
                                              $enrolwhere
                                      ) pl
                            WHERE sud.userid = pl.userid AND
                                  sud.courseid = pl.courseid AND
                                  sud.timeend = $nextmidnight AND
                                  sud.stattype='activity'
                       ) inline_view
              GROUP BY timeend, courseid, roleid
                HAVING SUM(statsreads) > 0 OR SUM(statswrites) > 0";

        if ($logspresent and !execute_sql($sql, false)) {
            $failed = true;
            break;
        }
        stats_daily_progress('14');


    /// how many view actions for default frontpage role on frontpage only
        $sql = "INSERT INTO {$CFG->prefix}stats_daily (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'activity', timeend, courseid, nroleid, SUM(statsreads), SUM(statswrites)
                  FROM (
                           SELECT $nextmidnight AS timeend, sud.courseid, $defaultfproleid AS nroleid, sud.statsreads, sud.statswrites
                             FROM {$CFG->prefix}stats_user_daily sud
                             WHERE sud.timeend = $nextmidnight AND sud.courseid = ".SITEID." AND
                                   sud.stattype='activity' AND
                                   sud.userid <> $guest->id AND sud.userid <> 0 AND sud.userid
                                   NOT IN (SELECT ra.userid
                                             FROM {$CFG->prefix}role_assignments ra $fpjoin
                                            WHERE c.id = ".SITEID." AND  ra.roleid <> $guestrole->id AND
                                                  ra.roleid <> $defaultfproleid AND $fpwhere)
                       ) inline_view
              GROUP BY timeend, courseid, nroleid
                HAVING SUM(statsreads) > 0 OR SUM(statswrites) > 0";

        if ($logspresent and !execute_sql($sql, false)) {
            $failed = true;
            break;
        }
        stats_daily_progress('15');

    /// how many view actions for guests or not-logged-in on frontpage
        $sql = "INSERT INTO {$CFG->prefix}stats_daily (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'activity', timeend, courseid, nroleid, SUM(statsreads), SUM(statswrites)
                  FROM (
                           SELECT $nextmidnight AS timeend, ".SITEID." AS courseid, $guestrole->id AS nroleid, pl.statsreads, pl.statswrites
                             FROM (
                                      SELECT sud.statsreads, sud.statswrites
                                        FROM {$CFG->prefix}stats_user_daily sud
                                      WHERE (sud.userid = $guest->id OR sud.userid = 0) AND
                                            sud.timeend = $nextmidnight AND sud.courseid = ".SITEID." AND
                                            sud.stattype='activity'
                                  ) pl
                       ) inline_view
              GROUP BY timeend, courseid, nroleid
                HAVING SUM(statsreads) > 0 OR SUM(statswrites) > 0";

        if ($logspresent and !execute_sql($sql, false)) {
            $failed = true;
            break;
        }
        stats_daily_progress('16');

        // remember processed days
        set_config('statslastdaily', $nextmidnight);
        mtrace("  finished until $nextmidnight: ".userdate($nextmidnight)." (in ".(time()-$daystart)." s)");

        $timestart    = $nextmidnight;
        $nextmidnight = stats_get_next_day_start($nextmidnight);
    }

    set_cron_lock('statsrunning', null);

    if ($failed) {
        $days--;
        mtrace("...error occured, completed $days days of statistics.");
        return false;

    } else {
        mtrace("...completed $days days of statistics.");
        return true;
    }
}


/**
 * Execute weekly statistics gathering
 * @return boolean success
 */
function stats_cron_weekly() {
    global $CFG;

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
    delete_records_select('stats_weekly',      "timeend > $timestart");
    delete_records_select('stats_user_weekly', "timeend > $timestart");

    mtrace("Running weekly statistics gathering, starting at $timestart:");

    $weeks = 0;
    while ($now > $nextstartweek) {
        @set_time_limit($timeout - 200);
        $weeks++;

        if ($weeks > 1) {
            // move the lock
            set_cron_lock('statsrunning', time() + $timeout, true);
        }

        $logtimesql  = "l.time >= $timestart AND l.time < $nextstartweek";
        $stattimesql = "timeend > $timestart AND timeend <= $nextstartweek";

    /// process login info first
        $sql = "INSERT INTO {$CFG->prefix}stats_user_weekly (stattype, timeend, courseid, userid, statsreads)

                SELECT 'logins', timeend, courseid, userid, COUNT(statsreads)
                  FROM (
                           SELECT $nextstartweek AS timeend, ".SITEID." as courseid, l.userid, l.id AS statsreads
                             FROM {$CFG->prefix}log l
                            WHERE action = 'login' AND $logtimesql
                       ) inline_view
              GROUP BY timeend, courseid, userid
                HAVING COUNT(statsreads) > 0";

        execute_sql($sql, false);


        $sql = "INSERT INTO {$CFG->prefix}stats_weekly (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'logins' AS stattype, $nextstartweek AS timeend, ".SITEID." as courseid, 0,
                       COALESCE((SELECT SUM(statsreads)
                                   FROM {$CFG->prefix}stats_user_weekly s1
                                  WHERE s1.stattype = 'logins' AND timeend = $nextstartweek), 0) AS nstat1,
                       (SELECT COUNT('x')
                          FROM {$CFG->prefix}stats_user_weekly s2
                         WHERE s2.stattype = 'logins' AND timeend = $nextstartweek) AS nstat2" .
                sql_null_from_clause();

        execute_sql($sql, false);


    /// now enrolments averages
        $sql = "INSERT INTO {$CFG->prefix}stats_weekly (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'enrolments', ntimeend, courseid, roleid, " . sql_ceil('AVG(stat1)') . ", " . sql_ceil('AVG(stat2)') . "
                  FROM (
                           SELECT $nextstartweek AS ntimeend, courseid, roleid, stat1, stat2
                             FROM {$CFG->prefix}stats_daily sd
                            WHERE stattype = 'enrolments' AND $stattimesql
                       ) inline_view
              GROUP BY ntimeend, courseid, roleid";

        execute_sql($sql, false);


    /// activity read/write averages
        $sql = "INSERT INTO {$CFG->prefix}stats_weekly (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'activity', ntimeend, courseid, roleid, SUM(stat1), SUM(stat2)
                  FROM (
                           SELECT $nextstartweek AS ntimeend, courseid, roleid, stat1, stat2
                             FROM {$CFG->prefix}stats_daily
                            WHERE stattype = 'activity' AND $stattimesql
                       ) inline_view
              GROUP BY ntimeend, courseid, roleid";

        execute_sql($sql, false);


    /// user read/write averages
        $sql = "INSERT INTO {$CFG->prefix}stats_user_weekly (stattype, timeend, courseid, userid, statsreads, statswrites)

                SELECT 'activity', ntimeend, courseid, userid, SUM(statsreads), SUM(statswrites)
                  FROM (
                           SELECT $nextstartweek AS ntimeend, courseid, userid, statsreads, statswrites
                             FROM {$CFG->prefix}stats_user_daily
                            WHERE stattype = 'activity' AND $stattimesql
                       ) inline_view
              GROUP BY ntimeend, courseid, userid";

        execute_sql($sql, false);

        set_config('statslastweekly', $nextstartweek);
        mtrace(" finished until $nextstartweek: ".userdate($nextstartweek));

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
    global $CFG;

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
    delete_records_select('stats_monthly', "timeend > $timestart");
    delete_records_select('stats_user_monthly', "timeend > $timestart");

    $startmonth = stats_get_base_monthly($now);


    mtrace("Running monthly statistics gathering, starting at $timestart:");

    $months = 0;
    while ($now > $nextstartmonth) {
        @set_time_limit($timeout - 200);
        $months++;

        if ($months > 1) {
            // move the lock
            set_cron_lock('statsrunning', time() + $timeout, true);
        }

        $logtimesql  = "l.time >= $timestart AND l.time < $nextstartmonth";
        $stattimesql = "timeend > $timestart AND timeend <= $nextstartmonth";

    /// process login info first
        $sql = "INSERT INTO {$CFG->prefix}stats_user_monthly (stattype, timeend, courseid, userid, statsreads)

                SELECT 'logins', timeend, courseid, userid, COUNT(statsreads)
                  FROM (
                           SELECT $nextstartmonth AS timeend, ".SITEID." as courseid, l.userid, l.id AS statsreads
                             FROM {$CFG->prefix}log l
                            WHERE action = 'login' AND $logtimesql
                       ) inline_view
              GROUP BY timeend, courseid, userid";

        execute_sql($sql, false);


        $sql = "INSERT INTO {$CFG->prefix}stats_monthly (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'logins' AS stattype, $nextstartmonth AS timeend, ".SITEID." as courseid, 0,
                       COALESCE((SELECT SUM(statsreads)
                                   FROM {$CFG->prefix}stats_user_monthly s1
                                  WHERE s1.stattype = 'logins' AND timeend = $nextstartmonth), 0) AS nstat1,
                       (SELECT COUNT('x')
                          FROM {$CFG->prefix}stats_user_monthly s2
                         WHERE s2.stattype = 'logins' AND timeend = $nextstartmonth) AS nstat2" .
                 sql_null_from_clause();

        execute_sql($sql, false);


    /// now enrolments averages
        $sql = "INSERT INTO {$CFG->prefix}stats_monthly (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'enrolments', ntimeend, courseid, roleid, " . sql_ceil('AVG(stat1)') . ", " . sql_ceil('AVG(stat2)') . "
                  FROM (
                           SELECT $nextstartmonth AS ntimeend, courseid, roleid, stat1, stat2
                             FROM {$CFG->prefix}stats_daily sd
                            WHERE stattype = 'enrolments' AND $stattimesql
                       ) inline_view
              GROUP BY ntimeend, courseid, roleid";

        execute_sql($sql, false);


    /// activity read/write averages
        $sql = "INSERT INTO {$CFG->prefix}stats_monthly (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT 'activity', ntimeend, courseid, roleid, SUM(stat1), SUM(stat2)
                  FROM (
                           SELECT $nextstartmonth AS ntimeend, courseid, roleid, stat1, stat2
                             FROM {$CFG->prefix}stats_daily
                            WHERE stattype = 'activity' AND $stattimesql
                       ) inline_view
              GROUP BY ntimeend, courseid, roleid";

        execute_sql($sql, false);


    /// user read/write averages
        $sql = "INSERT INTO {$CFG->prefix}stats_user_monthly (stattype, timeend, courseid, userid, statsreads, statswrites)

                SELECT 'activity', ntimeend, courseid, userid, SUM(statsreads), SUM(statswrites)
                  FROM (
                           SELECT $nextstartmonth AS ntimeend, courseid, userid, statsreads, statswrites
                             FROM {$CFG->prefix}stats_user_daily
                            WHERE stattype = 'activity' AND $stattimesql
                       ) inline_view
              GROUP BY ntimeend, courseid, userid";

        execute_sql($sql, false);

        set_config('statslastmonthly', $nextstartmonth);
        mtrace(" finished until $nextstartmonth: ".userdate($nextstartmonth));

        $timestart      = $nextstartmonth;
        $nextstartmonth = stats_get_next_month_start($nextstartmonth);
    }

    set_cron_lock('statsrunning', null);
    mtrace("...completed $months months of statistics.");
    return true;
}

/**
 * Returns simplified enrolment sql join data
 * @param int $limit number of max parent course categories
 * @param bool $includedoanything include also admins
 * @return array ra join and where string
 */
function stats_get_enrolled_sql($limit, $includedoanything) {
    global $CFG;

    $adm = $includedoanything ? " OR rc.capability = 'moodle/site:doanything'" : "";

    $join = "JOIN {$CFG->prefix}context ctx
                  ON ctx.id = ra.contextid
             CROSS JOIN {$CFG->prefix}course c
             JOIN {$CFG->prefix}role_capabilities rc
                  ON rc.roleid = ra.roleid";
    $where = "((rc.capability = 'moodle/course:view' $adm)
               AND rc.permission = 1 AND rc.contextid = ".SYSCONTEXTID."
               AND (ctx.contextlevel = ".CONTEXT_SYSTEM."
                    OR (c.id = ctx.instanceid AND ctx.contextlevel = ".CONTEXT_COURSE.")";

    for($i=1; $i<=$limit; $i++) {
        if ($i == 1) {
            $join .= " LEFT OUTER JOIN {$CFG->prefix}course_categories cc1
                            ON cc1.id = c.category";
            $where .= " OR (cc1.id = ctx.instanceid AND ctx.contextlevel = ".CONTEXT_COURSECAT.")";
        } else {
            $j = $i-1;
            $join .= " LEFT OUTER JOIN {$CFG->prefix}course_categories cc$i
                            ON cc$i.id = cc$j.parent";
            $where .= " OR (cc$i.id = ctx.instanceid AND ctx.contextlevel = ".CONTEXT_COURSECAT.")";
        }
    }

    $where .= "))";

    return array($join, $where);
}

/**
 * Return starting date of stats processing
 * @param string $str name of table - daily, weekly or monthly
 * @return int timestamp
 */
function stats_get_start_from($str) {
    global $CFG;

    // are there any data in stats table? Should not be...
    if ($timeend = get_field_sql('SELECT timeend FROM '.$CFG->prefix.'stats_'.$str.' ORDER BY timeend DESC')) {
        return $timeend;
    }
    // decide what to do based on our config setting (either all or none or a timestamp)
    switch ($CFG->statsfirstrun) {
        case 'all':
            if ($firstlog = get_field_sql('SELECT time FROM '.$CFG->prefix.'log ORDER BY time ASC')) {
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
 * @return start of day
 */
function stats_get_base_daily($time=0) {
    global $CFG;

    if (empty($time)) {
        $time = time();
    }
    if ($CFG->timezone == 99) {
        $time = strtotime(date('d-M-Y', $time));
        return $time;
    } else {
        $offset = get_timezone_offset($CFG->timezone);
        $gtime = $time + $offset;
        $gtime = intval($gtime / (60*60*24)) * 60*60*24;
        return $gtime - $offset;
    }
}

/**
 * Start of week
 * @param int $time timestamp
 * @return start of week
 */
function stats_get_base_weekly($time=0) {
    global $CFG;

    $time = stats_get_base_daily($time);
    $startday = $CFG->calendar_startwday;
    if ($CFG->timezone == 99) {
        $thisday = date('w', $time);
    } else {
        $offset = get_timezone_offset($CFG->timezone);
        $gtime = $time + $offset;
        $thisday = gmdate('w', $gtime);
    }
    if ($thisday > $startday) {
        $time = $time - (($thisday - $startday) * 60*60*24);
    } else if ($thisday < $startday) {
        $time = $time - ((7 + $thisday - $startday) * 60*60*24);
    }
    return $time;
}

/**
 * Start of month
 * @param int $time timestamp
 * @return start of month
 */
function stats_get_base_monthly($time=0) {
    global $CFG;

    if (empty($time)) {
        $time = time();
    }
    if ($CFG->timezone == 99) {
        return strtotime(date('1-M-Y', $time));

    } else {
        $time = stats_get_base_daily($time);
        $offset = get_timezone_offset($CFG->timezone);
        $gtime = $time + $offset;
        $day = gmdate('d', $gtime);
        if ($day == 1) {
            return $time;
        }
        return $gtime - (($day-1) * 60*60*24);
    }
}

/**
 * Start of next day
 * @param int $time timestamp
 * @return start of next day
 */
function stats_get_next_day_start($time) {
    $next = stats_get_base_daily($time);
    $next = $next + 60*60*26;
    $next = stats_get_base_daily($next);
    if ($next <= $time) {
        //DST trouble - prevent infinite loops
        $next = $next + 60*60*24;
    }
    return $next;
}

/**
 * Start of next week
 * @param int $time timestamp
 * @return start of next week
 */
function stats_get_next_week_start($time) {
    $next = stats_get_base_weekly($time);
    $next = $next + 60*60*24*9;
    $next = stats_get_base_weekly($next);
    if ($next <= $time) {
        //DST trouble - prevent infinite loops
        $next = $next + 60*60*24*7;
    }
    return $next;
}

/**
 * Start of next month
 * @param int $time timestamp
 * @return start of next month
 */
function stats_get_next_month_start($time) {
    $next = stats_get_base_monthly($time);
    $next = $next + 60*60*24*33;
    $next = stats_get_base_monthly($next);
    if ($next <= $time) {
        //DST trouble - prevent infinite loops
        $next = $next + 60*60*24*31;
    }
    return $next;
}

/**
 * Remove old stats data
 */
function stats_clean_old() {
    mtrace("Running stats cleanup tasks...");
    $deletebefore =  stats_get_base_monthly();

    // delete dailies older than 3 months (to be safe)
    $deletebefore = strtotime('-3 months', $deletebefore);
    delete_records_select('stats_daily',      "timeend < $deletebefore");
    delete_records_select('stats_user_daily', "timeend < $deletebefore");

    // delete weeklies older than 9  months (to be safe)
    $deletebefore = strtotime('-6 months', $deletebefore);
    delete_records_select('stats_weekly',      "timeend < $deletebefore");
    delete_records_select('stats_user_weekly', "timeend < $deletebefore");

    // don't delete monthlies

    mtrace("...stats cleanup finished");
}

function stats_get_parameters($time,$report,$courseid,$mode,$roleid=0) {
    global $CFG,$db;

    $param = new object();

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

    // compatibility - if we're in postgres, cast to real for some reports.
    $real = '';
    if ($CFG->dbfamily == 'postgres') {
        $real = '::real';
    }

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
        $param->fields = sql_concat('timeend','roleid').' AS uniqueid, timeend, roleid, stat1 as line1';
        $param->fieldscomplete = true; // set this to true to avoid anything adding stuff to the list and breaking complex queries.
        $param->aggregategroupby = 'roleid';
        $param->stattype = 'activity';
        $param->crosstab = true;
        $param->extras = 'GROUP BY timeend,roleid,stat1';
        if ($courseid == SITEID) {
            $param->fields = sql_concat('timeend','roleid').' AS uniqueid, timeend, roleid, sum(stat1) as line1';
            $param->extras = 'GROUP BY timeend,roleid';
        }
        break;

    case STATS_REPORT_WRITES:
        $param->fields = sql_concat('timeend','roleid').' AS uniqueid, timeend, roleid, stat2 as line1';
        $param->fieldscomplete = true; // set this to true to avoid anything adding stuff to the list and breaking complex queries.
        $param->aggregategroupby = 'roleid';
        $param->stattype = 'activity';
        $param->crosstab = true;
        $param->extras = 'GROUP BY timeend,roleid,stat2';
        if ($courseid == SITEID) {
            $param->fields = sql_concat('timeend','roleid').' AS uniqueid, timeend, roleid, sum(stat2) as line1';
            $param->extras = 'GROUP BY timeend,roleid';
        }
        break;

    case STATS_REPORT_ACTIVITY:
        $param->fields = sql_concat('timeend','roleid').' AS uniqueid, timeend, roleid, sum(stat1+stat2) as line1';
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
        $rolename = get_field('role','name','id',$roleid);
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
        $param->fields = 'statsreads as line1, statswrites as line2, statsreads+statswrites as line3';
        $param->line1 = get_string('statsuserreads');
        $param->line2 = get_string('statsuserwrites');
        $param->line3 = get_string('statsuseractivity');
        $param->stattype = 'activity';
        break;

    // ******************** STATS_MODE_RANKED ******************** //
    case STATS_REPORT_ACTIVE_COURSES:
        $param->fields = 'sum(stat1+stat2) AS line1';
        $param->stattype = 'activity';
        $param->orderby = 'line1 DESC';
        $param->line1 = get_string('activity');
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
                            SELECT courseid, (stat1+stat2) AS all_activity
                              FROM '.$CFG->prefix.'stats_'.$param->table.'
                             WHERE stattype=\'activity\' AND timeend >= '.$param->timeafter.' AND roleid = 0
                       ) activity
                       INNER JOIN
                            (
                            SELECT courseid, max(stat1) AS highest_enrolments 
                              FROM '.$CFG->prefix.'stats_'.$param->table.'
                             WHERE stattype=\'enrolments\' AND timeend >= '.$param->timeafter.' AND stat1 > '.$threshold.' 
                          GROUP BY courseid
                      ) enrolments
                      ON (activity.courseid = enrolments.courseid)
                      ORDER BY line3 DESC';
        $param->line1 = get_string('activity');
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
        $param->sql = 'SELECT courseid, ' . sql_ceil('avg(all_enrolments)') . ' as line1, ' .
                         sql_ceil('avg(active_enrolments)') . ' as line2, avg(proportion_active) AS line3
                       FROM (
                           SELECT courseid, timeend, stat2 as active_enrolments,
                                  stat1 as all_enrolments, stat2'.$real.'/stat1'.$real.' as proportion_active
                             FROM '.$CFG->prefix.'stats_'.$param->table.'
                            WHERE stattype=\'enrolments\' AND roleid = 0 AND stat1 > '.$threshold.'
                       ) aq
                       WHERE timeend >= '.$param->timeafter.'
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
                                  stat2'.$real.'/stat1'.$real.' as proportion_active
                             FROM '.$CFG->prefix.'stats_'.$param->table.'
                            WHERE stattype=\'activity\' AND roleid = 0 AND stat1 > 0
                       ) aq
                       WHERE timeend >= '.$param->timeafter.'
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
    global $CFG;

    $mods = get_records('modules');
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
        $actions[$n] = "'" . $actions[$n] . "'"; // quote them for SQL
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
    global $CFG;

    $reportoptions = array();

    switch ($mode) {
    case STATS_MODE_GENERAL:
        $reportoptions[STATS_REPORT_ACTIVITY] = get_string('statsreport'.STATS_REPORT_ACTIVITY);
        if ($courseid != SITEID && $context = get_context_instance(CONTEXT_COURSE, $courseid)) {
            $sql = 'SELECT r.id,r.name FROM '.$CFG->prefix.'role r JOIN '.$CFG->prefix.'stats_daily s ON s.roleid = r.id WHERE s.courseid = '.$courseid;
            if ($roles = get_records_sql($sql)) {
                foreach ($roles as $role) {
                    $reportoptions[STATS_REPORT_ACTIVITYBYROLE.$role->id] = get_string('statsreport'.STATS_REPORT_ACTIVITYBYROLE). ' '.$role->name;
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
        if (has_capability('coursereport/stats:view', get_context_instance(CONTEXT_SYSTEM))) {
            $site = get_site();
            $reportoptions[STATS_REPORT_USER_LOGINS] = get_string('statsreport'.STATS_REPORT_USER_LOGINS);
        }
        break;
    case STATS_MODE_RANKED:
        if (has_capability('coursereport/stats:view', get_context_instance(CONTEXT_SYSTEM))) {
            $reportoptions[STATS_REPORT_ACTIVE_COURSES] = get_string('statsreport'.STATS_REPORT_ACTIVE_COURSES);
            $reportoptions[STATS_REPORT_ACTIVE_COURSES_WEIGHTED] = get_string('statsreport'.STATS_REPORT_ACTIVE_COURSES_WEIGHTED);
            $reportoptions[STATS_REPORT_PARTICIPATORY_COURSES] = get_string('statsreport'.STATS_REPORT_PARTICIPATORY_COURSES);
            $reportoptions[STATS_REPORT_PARTICIPATORY_COURSES_RW] = get_string('statsreport'.STATS_REPORT_PARTICIPATORY_COURSES_RW);
        }
     break;
    }

    return $reportoptions;
}

function stats_fix_zeros($stats,$timeafter,$timestr,$line2=true,$line3=false) {

    if (empty($stats)) {
        return;
    }

    $timestr = str_replace('user_','',$timestr); // just in case.
    $fun = 'stats_get_base_'.$timestr;

    $now = $fun();

    $times = array();
    // add something to timeafter since it is our absolute base
    $actualtimes = array();
    foreach ($stats as $statid=>$s) {
        //normalize the times in stats - those might have been created in different timezone, DST etc.
        $s->timeend = $fun($s->timeend + 60*60*5);
        $stats[$statid] = $s;

        $actualtimes[] = $s->timeend;
    }

    $timeafter = array_pop(array_values($actualtimes));

    while ($timeafter < $now) {
        $times[] = $timeafter;
        if ($timestr == 'daily') {
            $timeafter = stats_get_next_day_start($timeafter);
        } else if ($timestr == 'weekly') {
            $timeafter = stats_get_next_week_start($timeafter);
        } else if ($timestr == 'monthly') {
            $timeafter = stats_get_next_month_start($timeafter);
        } else {
            return $stats; // this will put us in a never ending loop.
        }
    }

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
    global $CFG;

    if (empty($courseid)) {
        $courseid = SITEID;
    }

    $latestday = stats_get_start_from('daily');

    if ((time() - 60*60*24*2) < $latestday) { // we're ok
        return NULL;
    }

    $a = new object();
    $a->daysdone = get_field_sql("SELECT count(distinct(timeend)) from {$CFG->prefix}stats_daily");

    // how many days between the last day and now?
    $a->dayspending = ceil((stats_get_base_daily() - $latestday)/(60*60*24));

    if ($a->dayspending == 0 && $a->daysdone != 0) {
        return NULL; // we've only just started...
    }

    //return error as string
    return get_string('statscatchupmode','error',$a);
}

/**
 * Calculate missing course totals in stats
 */
function stats_upgrade_totals() {
    global $CFG;

    if (empty($CFG->statsrolesupgraded)) {
        // stats not yet upgraded to cope with roles...
        return;
    }

    $types = array('daily', 'weekly', 'monthly');

    $now = time();
    $y30 = 60*60*24*365*30;              // 30 years ago :-O
    $y20 = 60*60*24*365*20;              // 20 years ago :-O
    $limit = $now - $y20;

    foreach ($types as $i => $type) {
        $type2 = $types[($i+1) % count($types)];

        // delete previous incomplete data
        $sql = "DELETE FROM {$CFG->prefix}stats_$type2
                      WHERE timeend < $limit";
        execute_sql($sql);

        // clear the totals if already exist
        $sql = "DELETE FROM {$CFG->prefix}stats_$type
                      WHERE (stattype = 'enrolments' OR stattype = 'activity') AND
                            roleid = 0";
        execute_sql($sql);

        $sql = "INSERT INTO {$CFG->prefix}stats_$type2 (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT stattype, (timeend - $y30), courseid, 0, SUM(stat1), SUM(stat2)
                  FROM {$CFG->prefix}stats_$type
                 WHERE (stattype = 'enrolments' OR stattype = 'activity') AND
                       roleid <> 0
              GROUP BY stattype, timeend, courseid";
        execute_sql($sql);

        $sql = "INSERT INTO {$CFG->prefix}stats_$type (stattype, timeend, courseid, roleid, stat1, stat2)

                SELECT stattype, (timeend + $y30), courseid, roleid, stat1, stat2
                  FROM {$CFG->prefix}stats_$type2
                 WHERE (stattype = 'enrolments' OR stattype = 'activity') AND
                       roleid = 0 AND timeend < $y20";
        execute_sql($sql);

        $sql = "DELETE FROM {$CFG->prefix}stats_$type2
                      WHERE timeend < $limit";
        execute_sql($sql);
    }
}


function stats_upgrade_for_roles_wrapper() {
    global $CFG;
    if (!empty($CFG->statsrolesupgraded)) {
        return true;
    }

    $result = begin_sql();

    $result = $result && stats_upgrade_user_table_for_roles('daily');
    $result = $result && stats_upgrade_user_table_for_roles('weekly');
    $result = $result && stats_upgrade_user_table_for_roles('monthly');

    $result = $result && stats_upgrade_table_for_roles('daily');
    $result = $result && stats_upgrade_table_for_roles('weekly');
    $result = $result && stats_upgrade_table_for_roles('monthly');


    $result = $result && commit_sql();

    if (!empty($result)) {
        set_config('statsrolesupgraded',time());
    }

    // finally upgade totals, no big deal if it fails
    stats_upgrade_totals();

    return $result;
}

/**
 * Upgrades a prefix_stats_user_* table for the new role based permission
 * system.
 *
 * @param string $period  daily, weekly or monthly: the stat period to upgrade
 * @return boolean @todo maybe something else (error message) depending on
 * how this will be called.
 */
function stats_upgrade_user_table_for_roles($period) {
    global $CFG;
    static $teacher_role_id, $student_role_id;

    if (!in_array($period, array('daily', 'weekly', 'monthly'))) {
        error_log('stats upgrade:  invalid period: ' . $period);
        return false;
    }

    if (!$teacher_role_id) {
        $role            = get_roles_with_capability('moodle/legacy:editingteacher', CAP_ALLOW);
        $role            = array_keys($role);
        $teacher_role_id = $role[0];
        $role            = get_roles_with_capability('moodle/legacy:student', CAP_ALLOW);
        $role            = array_keys($role);
        $student_role_id = $role[0];
    }

    if (empty($teacher_role_id) || empty($student_role_id)) {
        error_log("Couldn't find legacy roles for teacher or student");
        return false;
    }

    $status = true;

    $status = $status && execute_sql("UPDATE {$CFG->prefix}stats_user_{$period}
        SET roleid = $teacher_role_id
        WHERE roleid = 1");
    $status = $status && execute_sql("UPDATE {$CFG->prefix}stats_user_{$period}
        SET roleid = $student_role_id
        WHERE roleid = 2");

    return $status;
}

/**
 * Upgrades a prefix_stats_* table for the new role based permission system.
 *
 * @param string $period  daily, weekly or monthly: the stat period to upgrade
 * @return boolean        @todo depends on how this will be called
 */
function stats_upgrade_table_for_roles ($period) {
    global $CFG;
    static $teacher_role_id, $student_role_id;

    if (!in_array($period, array('daily', 'weekly', 'monthly'))) {
        return false;
    }

    if (!$teacher_role_id) {
        $role            = get_roles_with_capability('moodle/legacy:editingteacher', CAP_ALLOW);
        $role            = array_keys($role);
        $teacher_role_id = $role[0];
        $role            = get_roles_with_capability('moodle/legacy:student', CAP_ALLOW);
        $role            = array_keys($role);
        $student_role_id = $role[0];
    }

    if (empty($teacher_role_id) || empty($student_role_id)) {
        error_log("Couldn't find legacy roles for teacher or student");
        return false;
    }

    execute_sql("CREATE TABLE {$CFG->prefix}stats_{$period}_tmp AS
        SELECT * FROM {$CFG->prefix}stats_{$period}");

    $table = new XMLDBTable('stats_' . $period);
    if (!drop_table($table)) {
        return false;
    }

    // Create a new stats table
    // @todo this definition I have made blindly by looking at how definitions are
    // made, it needs work to make sure it works properly
    require_once("$CFG->libdir/xmldb/classes/XMLDBTable.class.php");

    $table = new XMLDBTable('stats_' . $period);
    $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
        XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);

    $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
        XMLDB_NOTNULL, null, null, null, null);

    $table->addFieldInfo('roleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
        XMLDB_NOTNULL, null, null, null, null);
    $table->addFieldInfo('timeend', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
        XMLDB_NOTNULL, null, null, null, null);
    $table->addFieldInfo('stattype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL,
        null, XMLDB_ENUM, array('enrolments', 'activity', 'logins'), 'activity');
    $table->addFieldInfo('stat1', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
        XMLDB_NOTNULL, null, null, null, null);
    $table->addFieldInfo('stat2', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
        XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table stats_daily
    $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table stats_daily
    $table->addIndexInfo('courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid'));
    $table->addIndexInfo('timeend', XMLDB_INDEX_NOTUNIQUE, array('timeend'));
    $table->addIndexInfo('roleid', XMLDB_INDEX_NOTUNIQUE, array('roleid'));

    if (!create_table($table)) {
        return false;
    }

    //
    // Now insert the data from the temporary table into the new one
    //

    // Student enrolments
    execute_sql("INSERT INTO {$CFG->prefix}stats_{$period}
       (courseid, roleid, timeend, stattype, stat1, stat2)
       SELECT courseid, $student_role_id, timeend, 'enrolments', students, activestudents
       FROM {$CFG->prefix}stats_{$period}_tmp");

    // Teacher enrolments
    execute_sql("INSERT INTO {$CFG->prefix}stats_{$period}
       (courseid, roleid, timeend, stattype, stat1, stat2)
       SELECT courseid, $teacher_role_id, timeend, 'enrolments', teachers, activeteachers
       FROM {$CFG->prefix}stats_{$period}_tmp");

    // Student activity
    execute_sql("INSERT INTO {$CFG->prefix}stats_{$period}
       (courseid, roleid, timeend, stattype, stat1, stat2)
       SELECT courseid, $student_role_id, timeend, 'activity', studentreads, studentwrites
       FROM {$CFG->prefix}stats_{$period}_tmp");

    // Teacher activity
    execute_sql("INSERT INTO {$CFG->prefix}stats_{$period}
       (courseid, roleid, timeend, stattype, stat1, stat2)
       SELECT courseid, $teacher_role_id, timeend, 'activity', teacherreads, teacherwrites
       FROM {$CFG->prefix}stats_{$period}_tmp");

    // Logins
    execute_sql("INSERT INTO {$CFG->prefix}stats_{$period}
       (courseid, roleid, timeend, stattype, stat1, stat2)
       SELECT courseid, 0, timeend, 'logins', logins, uniquelogins
       FROM {$CFG->prefix}stats_{$period}_tmp WHERE courseid = ".SITEID);

    // Drop the temporary table
    $table = new XMLDBTable('stats_' . $period . '_tmp');
    if (!drop_table($table)) {
        return false;
    }

    return true;
}

?>
