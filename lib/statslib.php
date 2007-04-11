<?php

    // THESE CONSTANTS ARE USED FOR THE REPORTING PAGE.

    define('STATS_REPORT_LOGINS',1); // double impose logins and unqiue logins on a line graph. site course only.
    define('STATS_REPORT_READS',2); // double impose student reads and teacher reads on a line graph. 
    define('STATS_REPORT_WRITES',3); // double impose student writes and teacher writes on a line graph.
    define('STATS_REPORT_ACTIVITY',4); // 2+3 added up, teacher vs student.
    define('STATS_REPORT_STUDENTACTIVITY',5); // all student activity, reads vs writes
    define('STATS_REPORT_TEACHERACTIVITY',6); // all teacher activity, reads vs writes

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

    // return codes - whether to rerun
    define('STATS_RUN_COMPLETE',1);
    define('STATS_RUN_ABORTED',0);

function stats_cron_daily () {
    global $CFG;

    if (empty($CFG->enablestats)) {
        return STATS_RUN_ABORTED;
    }

    if (!$timestart = stats_get_start_from('daily')) {
        return STATS_RUN_ABORTED;
    }


    $midnight = stats_getmidnight(time());
    
    // check to make sure we're due to run, at least one day after last run
    if (isset($CFG->statslastdaily) and ((time() - 24*60*60) < $CFG->statslastdaily)) {
        return STATS_RUN_ABORTED;
    }

    mtrace("Running daily statistics gathering...");
    set_config('statslastdaily',time());

    $return = STATS_RUN_COMPLETE; // optimistic

    static $daily_modules;
    
    if (empty($daily_modules)) {
        $daily_modules = array();
        $mods = get_records("modules");
        foreach ($mods as $mod) {
            $file = $CFG->dirroot .'/mod/'.$mod->name.'/lib.php';
            if (!is_readable($file)) {
                continue;
            }
            require_once($file);
            $fname = $mod->name.'_get_daily_stats';
            if (function_exists($fname)) {
                $daily_modules[$mod] = $fname;
            }
        }
    }

    $nextmidnight = stats_get_next_dayend($timestart);

    if (!$courses = get_records('course','','','','id,1')) {
        return STATS_RUN_ABORTED;
    }
    
    $days = 0;
    mtrace("starting at $timestart");
    while ($midnight > $nextmidnight && $timestart < $nextmidnight) {

        $timesql = " (l.time > $timestart AND l.time < $nextmidnight) ";
        begin_sql();
        foreach ($courses as $course) {

            $stat->students = count_records('user_students','course',$course->id);
            $stat->teachers = count_records('user_teachers','course',$course->id);
            
            $sql = 'SELECT COUNT(DISTINCT(l.userid)) FROM '.$CFG->prefix.'log l JOIN '.$CFG->prefix
                .'user_students us ON us.userid = l.userid WHERE l.course = '.$course->id.' AND us.course = '.$course->id.' AND '.$timesql;
            $stat->activestudents = count_records_sql($sql);
            
            $sql = str_replace('students','teachers',$sql);
            $stat->activeteachers = count_records_sql($sql);
            
            $sql = 'SELECT COUNT(l.id) FROM '.$CFG->prefix.'log l JOIN '.$CFG->prefix
                .'user_students us ON us.userid = l.userid WHERE l.course = '.$course->id.' AND us.course = '.$course->id
                .' AND '.$timesql .' '.stats_get_action_sql_in('view');
            $stat->studentreads = count_records_sql($sql);
            
            $sql = str_replace('students','teachers',$sql);
            $stat->teacherreads = count_records_sql($sql);

            $sql = 'SELECT COUNT(l.id) FROM '.$CFG->prefix.'log l JOIN '.$CFG->prefix
                .'user_students us ON us.userid = l.userid WHERE l.course = '.$course->id.' AND us.course = '.$course->id
                .' AND '.$timesql.' '.stats_get_action_sql_in('post');
            $stat->studentwrites = count_records_sql($sql);
            
            $sql = str_replace('students','teachers',$sql);
            $stat->teacherwrites = count_records_sql($sql);

            $stat->logins = 0;
            $stat->uniquelogins = 0;
            if ($course->id == SITEID) {
                $sql = 'SELECT count(l.id) FROM '.$CFG->prefix.'log l WHERE l.action = \'login\' AND '.$timesql;
                $stat->logins = count_records_sql($sql);
                $sql = 'SELECT COUNT(DISTINCT(l.userid)) FROM '.$CFG->prefix.'log l WHERE l.action = \'login\' AND '.$timesql;
                $stat->uniquelogins = count_records_sql($sql);
            }

            $stat->courseid = $course->id;
            $stat->timeend = $nextmidnight;
            insert_record('stats_daily',$stat,false); // don't worry about the return id, we don't need it.

            $students = array();
            $teachers = array();

            // now do logins.
            if ($course->id == SITEID) {
                $sql = 'SELECT l.userid,count(l.id) as count FROM '.$CFG->prefix.'log l WHERE action = \'login\' AND '.$timesql.' GROUP BY userid';
                
                if ($logins = get_records_sql($sql)) {
                    foreach ($logins as $l) {
                        $stat->statsreads = $l->count;
                        $stat->userid = $l->userid;
                        $stat->timeend = $nextmidnight;
                        $stat->courseid = SITEID;
                        $stat->statswrites = 0;
                        $stat->stattype = 'logins';
                        $stat->roleid = 1; 
                        insert_record('stats_user_daily',$stat,false);
                    }
                }
            }


            stats_get_course_users($course,$timesql,$students,$teachers);

            foreach ($students as $user) {
                stats_do_daily_user_cron($course,$user,1,$timesql,$nextmidnight,$daily_modules);
            }
            foreach ($teachers as $user) {
                stats_do_daily_user_cron($course,$user,2,$timesql,$nextmidnight,$daily_modules);
            }
        }
        commit_sql();
        $timestart = $nextmidnight;
        $nextmidnight = stats_get_next_dayend($nextmidnight);
        $days++;

        if (!stats_check_runtime()) {
            mtrace("Stopping early! reached maxruntime");
            $return = STATS_RUN_ABORTED;
            break;
        }
    }
    mtrace("got up to ".$timestart);
    mtrace("Completed $days days");
    return $return;

}


function stats_cron_weekly () {

    global $CFG;

    if (empty($CFG->enablestats)) {
        STATS_RUN_ABORTED;
    }

    if (!$timestart = stats_get_start_from('weekly')) {
        return STATS_RUN_ABORTED;
    }
    
    // check to make sure we're due to run, at least one week after last run
    $sunday = stats_get_base_weekly(); 

    if (isset($CFG->statslastweekly) and ((time() - (7*24*60*60)) <= $CFG->statslastweekly)) {
        return STATS_RUN_ABORTED;
    }

    mtrace("Running weekly statistics gathering...");
    set_config('statslastweekly',time());

    $return = STATS_RUN_COMPLETE; // optimistic

    static $weekly_modules;
    
    if (empty($weekly_modules)) {
        $weekly_modules = array();
        $mods = get_records("modules");
        foreach ($mods as $mod) {
            $file = $CFG->dirroot .'/mod/'.$mod->name.'/lib.php';
            if (!is_readable($file)) {
                continue;
            }
            require_once($file);
            $fname = $mod->name.'_get_weekly_stats';
            if (function_exists($fname)) {
                $weekly_modules[$mod] = $fname;
            }
        }
    }

    $nextsunday = stats_get_next_weekend($timestart);

    if (!$courses = get_records('course','','','','id,1')) {
        return STATS_RUN_ABORTED;
    }
    
    $weeks = 0;
    mtrace("starting at $timestart");
    while ($sunday > $nextsunday && $timestart < $nextsunday) {

        $timesql = " (timeend > $timestart AND timeend < $nextsunday) ";
        begin_sql();
        foreach ($courses as $course) {
            
            $sql = 'SELECT ceil(avg(students)) as students, ceil(avg(teachers)) as teachers, 
                       ceil(avg(activestudents)) as activestudents,ceil(avg(activeteachers)) as activeteachers,
                       sum(studentreads) as studentreads, sum(studentwrites) as studentwrites,
                       sum(teacherreads) as teacherreads, sum(teacherwrites) as teacherwrites,
                       sum(logins) as logins FROM '.$CFG->prefix.'stats_daily WHERE courseid = '.$course->id.' AND '.$timesql;

            $stat = get_record_sql($sql);
            
            $stat->uniquelogins = 0;
            if ($course->id == SITEID) {
                $sql = 'SELECT COUNT(DISTINCT(l.userid)) FROM '.$CFG->prefix.'log l WHERE l.action = \'login\' AND '
                    .str_replace('timeend','time',$timesql);
                $stat->uniquelogins = count_records_sql($sql);
            }
            
            $stat->courseid = $course->id;
            $stat->timeend = $nextsunday;

            foreach (array_keys((array)$stat) as $key) {
                if (!isset($stat->$key)) {
                    $stat->$key = 0; // we don't want nulls , so avoid them.
                }
            }

            insert_record('stats_weekly',$stat,false); // don't worry about the return id, we don't need it.

            $students = array();
            $teachers = array();

            stats_get_course_users($course,$timesql,$students,$teachers);

            foreach ($students as $user) {
                stats_do_aggregate_user_cron($course,$user,1,$timesql,$nextsunday,'weekly',$weekly_modules);
            }

            foreach ($teachers as $user) {
                stats_do_aggregate_user_cron($course,$user,2,$timesql,$nextsunday,'weekly',$weekly_modules);
            }

        }
        stats_do_aggregate_user_login_cron($timesql,$nextsunday,'weekly');
        commit_sql();
        $timestart = $nextsunday;
        $nextsunday = stats_get_next_weekend($nextsunday);
        $weeks++;

        if (!stats_check_runtime()) {
            mtrace("Stopping early! reached maxruntime");
            $return = STATS_RUN_ABORTED;
            break;
        }
    }
    mtrace("got up to ".$timestart);
    mtrace("Completed $weeks weeks");
    return $return;
}
    

function stats_cron_monthly () {
    global $CFG;

    if (empty($CFG->enablestats)) {
        return STATS_RUN_ABORTED;
    }

    if (!$timestart = stats_get_start_from('monthly')) {
        return STATS_RUN_ABORTED;
    }
    
    // check to make sure we're due to run, at least one month after last run
    $monthend = stats_get_base_monthly();
    
    if (isset($CFG->statslastmonthly) and ((time() - (31*24*60*60)) <= $CFG->statslastmonthly)) {
        return STATS_RUN_ABORTED;
    }
    
    mtrace("Running monthly statistics gathering...");
    set_config('statslastmonthly',time());

    $return = STATS_RUN_COMPLETE; // optimistic

    static $monthly_modules;
    
    if (empty($monthly_modules)) {
        $monthly_modules = array();
        $mods = get_records("modules");
        foreach ($mods as $mod) {
            $file = $CFG->dirroot .'/mod/'.$mod->name.'/lib.php';
            if (!is_readable($file)) {
                continue;
            }
            require_once($file);
            $fname = $mod->name.'_get_monthly_stats';
            if (function_exists($fname)) {
                $monthly_modules[$mod] = $fname;
            }
        }
    }
    
    $nextmonthend = stats_get_next_monthend($timestart);

    if (!$courses = get_records('course','','','','id,1')) {
        return STATS_RUN_ABORTED;
    }
    
    $months = 0;
    mtrace("starting from $timestart");
    while ($monthend > $nextmonthend && $timestart < $nextmonthend) {

        $timesql = " (timeend > $timestart AND timeend < $nextmonthend) ";
        begin_sql();
        foreach ($courses as $course) {

            $sql = 'SELECT ceil(avg(students)) as students, ceil(avg(teachers)) as teachers, ceil(avg(activestudents)) as activestudents,ceil(avg(activeteachers)) as activeteachers,
                       sum(studentreads) as studentreads, sum(studentwrites) as studentwrites, sum(teacherreads) as teacherreads, sum(teacherwrites) as teacherwrites,
                       sum(logins) as logins FROM '.$CFG->prefix.'stats_daily WHERE courseid = '.$course->id.' AND '.$timesql;

            $stat = get_record_sql($sql);
            
            $stat->uniquelogins = 0;
            if ($course->id == SITEID) {
                $sql = 'SELECT COUNT(DISTINCT(l.userid)) FROM '.$CFG->prefix.'log l WHERE l.action = \'login\' AND '.str_replace('timeend','time',$timesql);
                $stat->uniquelogins = count_records_sql($sql);
            }
            
            $stat->courseid = $course->id;
            $stat->timeend = $nextmonthend;

            foreach (array_keys((array)$stat) as $key) {
                if (!isset($stat->$key)) {
                    $stat->$key = 0; // we don't want nulls , so avoid them.
                }
            }

            insert_record('stats_monthly',$stat,false); // don't worry about the return id, we don't need it.

            $students = array();
            $teachers = array();
            
            stats_get_course_users($course,$timesql,$students,$teachers);
            
            foreach ($students as $user) {
                stats_do_aggregate_user_cron($course,$user,1,$timesql,$nextmonthend,'monthly',$monthly_modules);
            }
            
            foreach ($teachers as $user) {
                stats_do_aggregate_user_cron($course,$user,2,$timesql,$nextmonthend,'monthly',$monthly_modules);
            }
        }
        stats_do_aggregate_user_login_cron($timesql,$nextmonthend,'monthly');
        commit_sql();
        $timestart = $nextmonthend;
        $nextmonthend = stats_get_next_monthend($timestart);
        $months++;
        if (!stats_check_runtime()) {
            mtrace("Stopping early! reached maxruntime");
            break;
            $return = STATS_RUN_ABORTED;
        }
    }
    mtrace("got up to $timestart");
    mtrace("Completed $months months");
    return $return;
}

function stats_get_start_from($str) {
    global $CFG;

    // if it's not our first run, just return the most recent.
    if ($timeend = get_field_sql('SELECT timeend FROM '.$CFG->prefix.'stats_'.$str.' ORDER BY timeend DESC LIMIT 1')) {
        return $timeend;
    }
    
    // decide what to do based on our config setting (either all or none or a timestamp)
    $function = 'stats_get_base_'.$str;
    switch ($CFG->statsfirstrun) {
        case 'all': 
            return $function(get_field_sql('SELECT time FROM '.$CFG->prefix.'log ORDER BY time LIMIT 1'));
            break;
        case 'none': 
            return $function(strtotime('-1 day',time()));
            break;
        default:
            if (is_numeric($CFG->statsfirstrun)) {
                return $function(time() - $CFG->statsfirstrun);
            }
            return false;
            break;
    }
}

function stats_get_base_daily($time=0) {
    if (empty($time)) {
        $time = time();
    }
    return stats_getmidnight($time);
}

function stats_get_base_weekly($time=0) {
    if (empty($time)) {
        $time = time();
    }
    // if we're currently a monday, last monday will take us back a week
    $str = 'last monday';
    if (date('D',$time) == 'Mon')
        $str = 'now';

    return stats_getmidnight(strtotime($str,$time));
}

function stats_get_base_monthly($time=0) {
    if (empty($time)) {
        $time = time();
    }
    return stats_getmidnight(strtotime(date('1-M-Y',$time)));
}

function stats_get_next_monthend($lastmonth) {
    return stats_getmidnight(strtotime(date('1-M-Y',$lastmonth).' +1 month'));
}

function stats_get_next_weekend($lastweek) {
    return stats_getmidnight(strtotime('+1 week',$lastweek));
}

function stats_get_next_dayend($lastday) {
    return stats_getmidnight(strtotime('+1 day',$lastday));
}

function stats_clean_old() {
    mtrace("Running stats cleanup tasks... ");
    // delete dailies older than 2 months (to be safe)
    $deletebefore = stats_get_next_monthend(strtotime('-2 months',time()));
    delete_records_select('stats_daily',"timeend < $deletebefore");
    delete_records_select('stats_user_daily',"timeend < $deletebefore");
    
    // delete weeklies older than 8 months (to be safe)
    $deletebefore = stats_get_next_monthend(strtotime('-8 months',time()));
    delete_records_select('stats_weekly',"timeend < $deletebefore");
    delete_records_select('stats_user_weekly',"timeend < $deletebefore");

    // don't delete monthlies
}

function stats_get_parameters($time,$report,$courseid,$mode) {
    global $CFG;
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
    if ($CFG->dbtype == 'postgres7') {
        $real = '::real';
    }

    switch ($report) {
    case STATS_REPORT_LOGINS:
        $param->fields = 'logins as line1,uniquelogins as line2';
        $param->line1 = get_string('statslogins');
        $param->line2 = get_string('statsuniquelogins');
        break;
    case STATS_REPORT_READS:
        $param->fields = 'studentreads as line1,teacherreads as line2';
        $param->line1 = get_string('statsstudentreads');
        $param->line2 = get_string('statsteacherreads');
        break;
    case STATS_REPORT_WRITES:
        $param->fields = 'studentwrites as line1,teacherwrites as line2';
        $param->line1 = get_string('statsstudentwrites');
        $param->line2 = get_string('statsteacherwrites');
        break;
    case STATS_REPORT_ACTIVITY:
        $param->fields = 'studentreads+studentwrites as line1, teacherreads+teacherwrites as line2';
        $param->line1 = get_string('statsstudentactivity');
        $param->line2 = get_string('statsteacheractivity');
        break;
    case STATS_REPORT_STUDENTACTIVITY:
        $param->fields = 'studentreads as line1,studentwrites as line2';
        $param->line1 = get_string('statsstudentreads');
        $param->line2 = get_string('statsstudentwrites');
        break;
    case STATS_REPORT_TEACHERACTIVITY:
        $param->fields = 'teacherreads as line1,teacherwrites as line2';
        $param->line1 = get_string('statsteacherreads');
        $param->line2 = get_string('statsteacherwrites');
        break;
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
    case STATS_REPORT_ACTIVE_COURSES: 
        $param->fields = 'sum(studentreads+studentwrites+teacherreads+teacherwrites) AS line1';
        $param->orderby = 'line1 DESC';
        $param->line1 = get_string('activity');
        $param->graphline = 'line1';
        break;
    case STATS_REPORT_ACTIVE_COURSES_WEIGHTED:
        $param->fields = 'sum(studentreads+studentwrites+teacherreads+teacherwrites) AS line1,'
            .'max(students+teachers) AS line2,'
            .'sum(studentreads+studentwrites+teacherreads+teacherwrites)'.$real.'/max(students+teachers)'.$real.' AS line3';
        $param->extras = 'HAVING max(students+teachers) != 0';
        if (!empty($CFG->statsuserthreshold) && is_numeric($CFG->statsuserthreshold)) {
            $param->extras .= ' AND max(students+teachers) > '.$CFG->statsuserthreshold;
        }
        $param->orderby = 'line3 DESC';
        $param->line1 = get_string('activity');
        $param->line2 = get_string('users');
        $param->line3 = get_string('activityweighted');
        $param->graphline = 'line3';
        break;
    case STATS_REPORT_PARTICIPATORY_COURSES:
        $param->fields = 'max(students+teachers) as line1,max(activestudents+activeteachers) AS line2,'
            .'max(activestudents+activeteachers)'.$real.'/max(students+teachers)'.$real.' AS line3';
        $param->extras = 'HAVING max(students+teachers) != 0';
        if (!empty($CFG->statsuserthreshold) && is_numeric($CFG->statsuserthreshold)) {
            $param->extras .= ' AND max(students+teachers) > '.$CFG->statsuserthreshold;
        }
        $param->orderby = 'line3 DESC';
        $param->line1 = get_string('users');
        $param->line2 = get_string('activeusers');
        $param->line3 = get_string('participationratio');
        $param->graphline = 'line3';
        break;
    case STATS_REPORT_PARTICIPATORY_COURSES_RW:
        $param->fields = 'sum(studentreads+teacherreads) as line1,sum(studentwrites+teacherwrites) AS line2,'
            .'sum(studentwrites+teacherwrites)'.$real.'/sum(studentreads+teacherreads)'.$real.' AS line3';
        $param->extras = 'HAVING sum(studentreads+teacherreads) != 0';
        $param->orderby = 'line3 DESC';
        $param->line1 = get_string('views');
        $param->line2 = get_string('posts');
        $param->line3 = get_string('participationratio');
        $param->graphline = 'line3';
        break;
    }

    if ($courseid == SITEID && $mode != STATS_MODE_RANKED) { // just aggregate all courses.
        $param->fields = preg_replace('/([a-zA-Z0-9+_]*)\W+as\W+([a-zA-Z0-9_]*)/','sum($1) as $2',$param->fields);
        $param->extras = ' GROUP BY timeend';
    }
    
    return $param;
} 

function stats_get_view_actions() {
    return array('view','view all','history');
}

function stats_get_post_actions() {
    return array('add','delete','edit','add mod','delete mod','edit section'.'enrol','loginas','new','unenrol','update','update mod');
}

function stats_get_action_sql_in($str) {
    global $CFG;
    
    $mods = get_records('modules');
    $function = 'stats_get_'.$str.'_actions';
    $actions = $function();
    foreach ($mods as $mod) {
        $file = $CFG->dirroot .'/mod/'.$mod->name.'/lib.php';
        if (!is_readable($file)) {
            continue;
        }
        require_once($file);
        $function = $mod->name.'_get_'.$str.'_actions';
        if (function_exists($function)) {
            $actions = array_merge($actions,$function());
        }
    }
    $actions = array_unique($actions);
    if (empty($actions)) {
        return ' ';
    } else if (count($actions) == 1) {
        return ' AND l.action = '.array_pop($actions).' ';
    } else {
        return ' AND l.action IN (\''.implode('\',\'',$actions).'\') ';
    }
}


function stats_get_course_users($course,$timesql,&$students, &$teachers) {
    global $CFG;
    
    $timesql = str_replace('timeend','l.time',$timesql);

    $sql = 'SELECT DISTINCT(l.userid) as userid,1 as roleid FROM '.$CFG->prefix.'log l JOIN '.$CFG->prefix
        .'user_students us ON us.userid = l.userid WHERE l.course = '.$course->id.' AND us.course = '.$course->id.' AND '.$timesql;
    if (!$students = get_records_sql($sql)) {
        $students = array(); // avoid warnings;
    }

    $sql = str_replace('students','teachers',$sql);
    $sql = str_replace(',1',',2',$sql);

    if (!$teachers = get_records_sql($sql)) {
        $teachers = array(); // avoid warnings
    }

}

function stats_do_daily_user_cron($course,$user,$roleid,$timesql,$timeend,$mods) {

    global $CFG;

    $stat = new StdClass;
    $stat->userid   = $user->userid;
    $stat->roleid   = $roleid;
    $stat->courseid = $course->id;
    $stat->stattype = 'activity';
    $stat->timeend  = $timeend;
    
    $sql = 'SELECT COUNT(l.id) FROM '.$CFG->prefix.'log l WHERE l.userid = '.$user->userid
        .' AND  l.course = '.$course->id
        .' AND '.$timesql .' '.stats_get_action_sql_in('view');

    $stat->statsreads  = count_records_sql($sql);
    
    $sql = 'SELECT COUNT(l.id) FROM '.$CFG->prefix.'log l WHERE l.userid = '.$user->userid
        .' AND l.course = '.$course->id
        .' AND '.$timesql.' '.stats_get_action_sql_in('post');

    $stat->statswrites = count_records_sql($sql);
                
    insert_record('stats_user_daily',$stat,false);

    // now ask the modules if they want anything.
    foreach ($mods as $mod => $fname) {
        mtrace('  doing daily statistics for '.$mod->name);
        $fname($course,$user,$timeend,$roleid);
    }
}

function stats_do_aggregate_user_cron($course,$user,$roleid,$timesql,$timeend,$timestr,$mods) {

    global $CFG;

    $stat = new StdClass;
    $stat->userid   = $user->userid;
    $stat->roleid   = $roleid;
    $stat->courseid = $course->id;
    $stat->stattype = 'activity';
    $stat->timeend  = $timeend;

    $sql = 'SELECT sum(statsreads) as statsreads, sum(statswrites) as statswrites FROM '.$CFG->prefix.'stats_user_daily WHERE courseid = '.$course->id.' AND '.$timesql
        ." AND roleid=".$roleid." AND userid = ".$stat->userid." AND stattype='activity'"; // add on roleid in case they have teacher and student records.
    
    $r = get_record_sql($sql);
    $stat->statsreads = (empty($r->statsreads)) ? 0 : $r->statsreads;
    $stat->statswrites = (empty($r->statswrites)) ? 0 : $r->statswrites;
    
    insert_record('stats_user_'.$timestr,$stat,false);

    // now ask the modules if they want anything.
    foreach ($mods as $mod => $fname) {
        mtrace('  doing '.$timestr.' statistics for '.$mod->name);
        $fname($course,$user,$timeend,$roleid);
    }
}

function stats_do_aggregate_user_login_cron($timesql,$timeend,$timestr) {
    global $CFG;
    
    $sql = 'SELECT userid,roleid,sum(statsreads) as statsreads, sum(statswrites) as writes FROM '.$CFG->prefix.'stats_user_daily WHERE stattype = \'logins\' AND '.$timesql.' GROUP BY userid,roleid';
    
    if ($users = get_records_sql($sql)) {
        foreach ($users as $stat) {
            $stat->courseid = SITEID;
            $stat->timeend = $timeend;
            $stat->stattype = 'logins';
            
            insert_record('stats_user_'.$timestr,$stat,false);
        }
    }
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

    return $timeoptions;
}

function stats_get_report_options($courseid,$mode) {
    
    $reportoptions = array();

    switch ($mode) {
    case STATS_MODE_GENERAL:
        $reportoptions[STATS_REPORT_ACTIVITY] = get_string('statsreport'.STATS_REPORT_ACTIVITY);
        $reportoptions[STATS_REPORT_STUDENTACTIVITY] = get_string('statsreport'.STATS_REPORT_STUDENTACTIVITY);
        $reportoptions[STATS_REPORT_TEACHERACTIVITY] = get_string('statsreport'.STATS_REPORT_TEACHERACTIVITY);
        $reportoptions[STATS_REPORT_READS] = get_string('statsreport'.STATS_REPORT_READS);
        $reportoptions[STATS_REPORT_WRITES] = get_string('statsreport'.STATS_REPORT_WRITES);
        if ($courseid == SITEID) {
            $reportoptions[STATS_REPORT_LOGINS] = get_string('statsreport'.STATS_REPORT_LOGINS);
        }
        
        break;
    case STATS_MODE_DETAILED:
        $reportoptions[STATS_REPORT_USER_ACTIVITY] = get_string('statsreport'.STATS_REPORT_USER_ACTIVITY);
        $reportoptions[STATS_REPORT_USER_ALLACTIVITY] = get_string('statsreport'.STATS_REPORT_USER_ALLACTIVITY);
        if (isadmin()) {
            $site = get_site();
            $reportoptions[STATS_REPORT_USER_LOGINS] = get_string('statsreport'.STATS_REPORT_USER_LOGINS);
        }
        break;
    case STATS_MODE_RANKED:
        if (isadmin()) {
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
    $actualtimes = array_keys($stats);
    $timeafter = array_pop($actualtimes);

    while ($timeafter < $now) {
        $times[] = $timeafter;
        if ($timestr == 'daily') {
            $timeafter = stats_get_next_dayend($timeafter);
        } else if ($timestr == 'weekly') {
            $timeafter = stats_get_next_weekend($timeafter);
        } else if ($timestr == 'monthly') {
            $timeafter = stats_get_next_monthend($timeafter);
        } else {
            return $stats; // this will put us in a never ending loop.
        }
    }

    foreach ($times as $time) {
        if (!array_key_exists($time,$stats)) {
            $newobj = new StdClass;
            $newobj->timeend = $time;
            $newobj->id = 0;
            $newobj->line1 = 0;
            if (!empty($line2)) {
                $newobj->line2 = 0;
            }
            if (!empty($line3)) {
                $newobj->line3 = 0;
            }
            $stats[$time] = $newobj;
        }
    }
    
    krsort($stats);
    return $stats;

}

function stats_check_runtime() {
    global $CFG;
    
    if (empty($CFG->statsmaxruntime)) {
        return true;
    }
    
    if ((time() - $CFG->statsrunning) < $CFG->statsmaxruntime) {
        return true;
    }
    
    return false; // we've gone over! 
        
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


// copied from usergetmidnight, but we ignore dst
function stats_getmidnight($date, $timezone=99) {
    $timezone = get_user_timezone_offset($timezone);
    $userdate = stats_getdate($date, $timezone);
    return make_timestamp($userdate['year'], $userdate['mon'], $userdate['mday'], 0, 0, 0, $timezone,false ); // ignore dst for this.
}

function stats_getdate($time, $timezone=99) {

    $timezone = get_user_timezone_offset($timezone);

    if (abs($timezone) > 13) {    // Server time
        return getdate($time);
    }

    // There is no gmgetdate so we use gmdate instead
    $time += intval((float)$timezone * HOURSECS);
    $datestring = strftime('%S_%M_%H_%d_%m_%Y_%w_%j_%A_%B', $time);
    list(
        $getdate['seconds'],
        $getdate['minutes'],
        $getdate['hours'],
        $getdate['mday'],
        $getdate['mon'],
        $getdate['year'],
        $getdate['wday'],
        $getdate['yday'],
        $getdate['weekday'],
        $getdate['month']
    ) = explode('_', $datestring);

    return $getdate;
}


?>
