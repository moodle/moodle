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
 * Library of useful functions
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/filelib.php');

define('COURSE_MAX_LOG_DISPLAY', 150);          // days
define('COURSE_MAX_LOGS_PER_PAGE', 1000);       // records
define('COURSE_LIVELOG_REFRESH', 60);           // Seconds
define('COURSE_MAX_RECENT_PERIOD', 172800);     // Two days, in seconds
define('COURSE_MAX_SUMMARIES_PER_PAGE', 10);    // courses
define('COURSE_MAX_COURSES_PER_DROPDOWN',1000); //  max courses in log dropdown before switching to optional
define('COURSE_MAX_USERS_PER_DROPDOWN',1000);   //  max users in log dropdown before switching to optional
define('FRONTPAGENEWS',           '0');
define('FRONTPAGECOURSELIST',     '1');
define('FRONTPAGECATEGORYNAMES',  '2');
define('FRONTPAGETOPICONLY',      '3');
define('FRONTPAGECATEGORYCOMBO',  '4');
define('FRONTPAGECOURSELIMIT',    200);         // maximum number of courses displayed on the frontpage
define('EXCELROWS', 65535);
define('FIRSTUSEDEXCELROW', 3);

define('MOD_CLASS_ACTIVITY', 0);
define('MOD_CLASS_RESOURCE', 1);

if (!defined('MAX_MODINFO_CACHE_SIZE')) {
    define('MAX_MODINFO_CACHE_SIZE', 10);
}

function make_log_url($module, $url) {
    switch ($module) {
        case 'course':
        case 'file':
        case 'login':
        case 'lib':
        case 'admin':
        case 'calendar':
        case 'mnet course':
            if (strpos($url, '../') === 0) {
                $url = ltrim($url, '.');
            } else {
                $url = "/course/$url";
            }
            break;
        case 'user':
        case 'blog':
            $url = "/$module/$url";
            break;
        case 'upload':
            $url = $url;
            break;
        case 'coursetags':
            $url = '/'.$url;
            break;
        case 'library':
        case '':
            $url = '/';
            break;
        case 'message':
            $url = "/message/$url";
            break;
        case 'notes':
            $url = "/notes/$url";
            break;
        case 'tag':
            $url = "/tag/$url";
            break;
        case 'role':
            $url = '/'.$url;
            break;
        default:
            $url = "/mod/$module/$url";
            break;
    }

    //now let's sanitise urls - there might be some ugly nasties:-(
    $parts = explode('?', $url);
    $script = array_shift($parts);
    if (strpos($script, 'http') === 0) {
        $script = clean_param($script, PARAM_URL);
    } else {
        $script = clean_param($script, PARAM_PATH);
    }

    $query = '';
    if ($parts) {
        $query = implode('', $parts);
        $query = str_replace('&amp;', '&', $query); // both & and &amp; are stored in db :-|
        $parts = explode('&', $query);
        $eq = urlencode('=');
        foreach ($parts as $key=>$part) {
            $part = urlencode(urldecode($part));
            $part = str_replace($eq, '=', $part);
            $parts[$key] = $part;
        }
        $query = '?'.implode('&amp;', $parts);
    }

    return $script.$query;
}


function build_mnet_logs_array($hostid, $course, $user=0, $date=0, $order="l.time ASC", $limitfrom='', $limitnum='',
                   $modname="", $modid=0, $modaction="", $groupid=0) {
    global $CFG, $DB;

    // It is assumed that $date is the GMT time of midnight for that day,
    // and so the next 86400 seconds worth of logs are printed.

    /// Setup for group handling.

    // TODO: I don't understand group/context/etc. enough to be able to do
    // something interesting with it here
    // What is the context of a remote course?

    /// If the group mode is separate, and this user does not have editing privileges,
    /// then only the user's group can be viewed.
    //if ($course->groupmode == SEPARATEGROUPS and !has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_COURSE, $course->id))) {
    //    $groupid = get_current_group($course->id);
    //}
    /// If this course doesn't have groups, no groupid can be specified.
    //else if (!$course->groupmode) {
    //    $groupid = 0;
    //}

    $ILIKE = $DB->sql_ilike();

    $groupid = 0;

    $joins = array();

    $qry = "SELECT l.*, u.firstname, u.lastname, u.picture
              FROM {mnet_log} l
               LEFT JOIN {user} u ON l.userid = u.id
              WHERE ";
    $params = array();

    $where .= "l.hostid = :hostid";
    $params['hostid'] = $hostid;

    // TODO: Is 1 really a magic number referring to the sitename?
    if ($course != SITEID || $modid != 0) {
        $where .= " AND l.course=:courseid";
        $params['courseid'] = $course;
    }

    if ($modname) {
        $where .= " AND l.module = :modname";
        $params['modname'] = $modname;
    }

    if ('site_errors' === $modid) {
        $where .= " AND ( l.action='error' OR l.action='infected' )";
    } else if ($modid) {
        //TODO: This assumes that modids are the same across sites... probably
        //not true
        $where .= " AND l.cmid = :modid";
        $params['modid'] = $modid;
    }

    if ($modaction) {
        $firstletter = substr($modaction, 0, 1);
        if (preg_match('/[[:alpha:]]/', $firstletter)) {
            $where .= " AND lower(l.action) $ILIKE :modaction";
            $params['modaction'] = "%$modaction%";
        } else if ($firstletter == '-') {
            $where .= " AND lower(l.action) NOT $ILIKE :modaction";
            $params['modaction'] = "%$modaction%";
        }
    }

    if ($user) {
        $where .= " AND l.userid = :user";
        $params['user'] = $user;
    }

    if ($date) {
        $enddate = $date + 86400;
        $where .= " AND l.time > :date AND l.time < :enddate";
        $params['date'] = $date;
        $params['enddate'] = $enddate;
    }

    $result = array();
    $result['totalcount'] = $DB->count_records_sql("SELECT COUNT('x') FROM {mnet_log} l WHERE $where", $params);
    if(!empty($result['totalcount'])) {
        $where .= " ORDER BY $order";
        $result['logs'] = $DB->get_records_sql("$qry $where", $params, $limitfrom, $limitnum);
    } else {
        $result['logs'] = array();
    }
    return $result;
}

function build_logs_array($course, $user=0, $date=0, $order="l.time ASC", $limitfrom='', $limitnum='',
                   $modname="", $modid=0, $modaction="", $groupid=0) {
    global $DB, $SESSION;
    // It is assumed that $date is the GMT time of midnight for that day,
    // and so the next 86400 seconds worth of logs are printed.

    /// Setup for group handling.

    /// If the group mode is separate, and this user does not have editing privileges,
    /// then only the user's group can be viewed.
    if ($course->groupmode == SEPARATEGROUPS and !has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_COURSE, $course->id))) {
        if (isset($SESSION->currentgroup[$course->id])) {
            $groupid =  $SESSION->currentgroup[$course->id];
        } else {
            $groupid = groups_get_all_groups($course->id, $USER->id);
            if (is_array($groupid)) {
                $groupid = array_shift(array_keys($groupid));
                $SESSION->currentgroup[$course->id] = $groupid;
            } else {
                $groupid = 0;
            }
        }
    }
    /// If this course doesn't have groups, no groupid can be specified.
    else if (!$course->groupmode) {
        $groupid = 0;
    }

    $joins = array();
    $oarams = array();

    if ($course->id != SITEID || $modid != 0) {
        $joins[] = "l.course = :courseid";
        $params['courseid'] = $course->id;
    }

    if ($modname) {
        $joins[] = "l.module = :modname";
        $params['modname'] = $modname;
    }

    if ('site_errors' === $modid) {
        $joins[] = "( l.action='error' OR l.action='infected' )";
    } else if ($modid) {
        $joins[] = "l.cmid = :modid";
        $params['modid'] = $modid;
    }

    if ($modaction) {
        $ILIKE = $DB->sql_ilike();
        $firstletter = substr($modaction, 0, 1);
        if (preg_match('/[[:alpha:]]/', $firstletter)) {
            $joins[] = "l.action $ILIKE :modaction";
            $params['modaction'] = '%'.$modaction.'%';
        } else if ($firstletter == '-') {
            $joins[] = "l.action NOT $ILIKE :modaction";
            $params['modaction'] = '%'.substr($modaction, 1).'%';
        }
    }


    /// Getting all members of a group.
    if ($groupid and !$user) {
        if ($gusers = groups_get_members($groupid)) {
            $gusers = array_keys($gusers);
            $joins[] = 'l.userid IN (' . implode(',', $gusers) . ')';
        } else {
            $joins[] = 'l.userid = 0'; // No users in groups, so we want something that will always be false.
        }
    }
    else if ($user) {
        $joins[] = "l.userid = :userid";
        $params['userid'] = $user;
    }

    if ($date) {
        $enddate = $date + 86400;
        $joins[] = "l.time > :date AND l.time < :enddate";
        $params['date'] = $date;
        $params['enddate'] = $enddate;
    }

    $selector = implode(' AND ', $joins);

    $totalcount = 0;  // Initialise
    $result = array();
    $result['logs'] = get_logs($selector, $params, $order, $limitfrom, $limitnum, $totalcount);
    $result['totalcount'] = $totalcount;
    return $result;
}


function print_log($course, $user=0, $date=0, $order="l.time ASC", $page=0, $perpage=100,
                   $url="", $modname="", $modid=0, $modaction="", $groupid=0) {

    global $CFG, $DB, $OUTPUT;

    if (!$logs = build_logs_array($course, $user, $date, $order, $page*$perpage, $perpage,
                       $modname, $modid, $modaction, $groupid)) {
        echo $OUTPUT->notification("No logs found!");
        echo $OUTPUT->footer();
        exit;
    }

    $courses = array();

    if ($course->id == SITEID) {
        $courses[0] = '';
        if ($ccc = get_courses('all', 'c.id ASC', 'c.id,c.shortname')) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = $cc->shortname;
            }
        }
    } else {
        $courses[$course->id] = $course->shortname;
    }

    $totalcount = $logs['totalcount'];
    $count=0;
    $ldcache = array();
    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);

    $strftimedatetime = get_string("strftimedatetime");

    echo "<div class=\"info\">\n";
    print_string("displayingrecords", "", $totalcount);
    echo "</div>\n";

    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "$url&perpage=$perpage");

    $table = new html_table();
    $table->classes = array('logtable','generalbox');
    $table->align = array('right', 'left', 'left');
    $table->head = array(
        get_string('time'),
        get_string('ip_address'),
        get_string('fullnamecourse'),
        get_string('action'),
        get_string('info')
    );
    $table->data = array();
    
    if ($course->id == SITEID) {
        array_unshift($table->align, 'left');
        array_unshift($table->head, get_string('course'));
    }

    // Make sure that the logs array is an array, even it is empty, to avoid warnings from the foreach.
    if (empty($logs['logs'])) {
        $logs['logs'] = array();
    }

    foreach ($logs['logs'] as $log) {

        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = $DB->get_record('log_display', array('module'=>$log->module, 'action'=>$log->action));
            $ldcache[$log->module][$log->action] = $ld;
        }
        if ($ld && is_numeric($log->info)) {
            // ugly hack to make sure fullname is shown correctly
            if ($ld->mtable == 'user' && $ld->field == $DB->sql_concat('firstname', "' '" , 'lastname')) {
                $log->info = fullname($DB->get_record($ld->mtable, array('id'=>$log->info)), true);
            } else {
                $log->info = $DB->get_field($ld->mtable, $ld->field, array('id'=>$log->info));
            }
        }

        //Filter log->info
        $log->info = format_string($log->info);

        // If $log->url has been trimmed short by the db size restriction
        // code in add_to_log, keep a note so we don't add a link to a broken url
        $tl=textlib_get_instance();
        $brokenurl=($tl->strlen($log->url)==100 && $tl->substr($log->url,97)=='...');

        $row = array();
        if ($course->id == SITEID) {
            if (empty($log->course)) {
                $row[] = get_string('site');
            } else {
                $row[] = "<a href=\"{$CFG->wwwroot}/course/view.php?id={$log->course}\">". format_string($courses[$log->course])."</a>";
            }
        }
        
        $row[] = userdate($log->time, '%a').' '.userdate($log->time, $strftimedatetime);
        
        $link = new moodle_url("/iplookup/index.php?ip=$log->ip&user=$log->userid");
        $row[] = $OUTPUT->action_link($link, $log->ip, new popup_action('click', $link, 'iplookup', array('height' => 440, 'width' => 700)));

        $row[] = html_writer::link(new moodle_url("/user/view.php?id={$log->userid}&course={$log->course}"), fullname($log, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id))));

        $displayaction="$log->module $log->action";
        if ($brokenurl) {
            $row[] = $displayaction;
        } else {
            $link = make_log_url($log->module,$log->url);
            $row[] = $OUTPUT->action_link($link, $displayaction, new popup_action('click', $link, 'fromloglive'), array('height' => 440, 'width' => 700));
        }
        $row[] = $log->info;
        $table->data[] = $row;
    }

    echo html_writer::table($table);
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "$url&perpage=$perpage");
}


function print_mnet_log($hostid, $course, $user=0, $date=0, $order="l.time ASC", $page=0, $perpage=100,
                   $url="", $modname="", $modid=0, $modaction="", $groupid=0) {

    global $CFG, $DB, $OUTPUT;

    if (!$logs = build_mnet_logs_array($hostid, $course, $user, $date, $order, $page*$perpage, $perpage,
                       $modname, $modid, $modaction, $groupid)) {
        echo $OUTPUT->notification("No logs found!");
        echo $OUTPUT->footer();
        exit;
    }

    if ($course->id == SITEID) {
        $courses[0] = '';
        if ($ccc = get_courses('all', 'c.id ASC', 'c.id,c.shortname,c.visible')) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = $cc->shortname;
            }
        }
    }

    $totalcount = $logs['totalcount'];
    $count=0;
    $ldcache = array();
    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);

    $strftimedatetime = get_string("strftimedatetime");

    echo "<div class=\"info\">\n";
    print_string("displayingrecords", "", $totalcount);
    echo "</div>\n";

    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "$url&perpage=$perpage");

    echo "<table class=\"logtable\" cellpadding=\"3\" cellspacing=\"0\">\n";
    echo "<tr>";
    if ($course->id == SITEID) {
        echo "<th class=\"c0 header\">".get_string('course')."</th>\n";
    }
    echo "<th class=\"c1 header\">".get_string('time')."</th>\n";
    echo "<th class=\"c2 header\">".get_string('ip_address')."</th>\n";
    echo "<th class=\"c3 header\">".get_string('fullnamecourse')."</th>\n";
    echo "<th class=\"c4 header\">".get_string('action')."</th>\n";
    echo "<th class=\"c5 header\">".get_string('info')."</th>\n";
    echo "</tr>\n";

    if (empty($logs['logs'])) {
        echo "</table>\n";
        return;
    }

    $row = 1;
    foreach ($logs['logs'] as $log) {

        $log->info = $log->coursename;
        $row = ($row + 1) % 2;

        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = $DB->get_record('log_display', array('module'=>$log->module, 'action'=>$log->action));
            $ldcache[$log->module][$log->action] = $ld;
        }
        if (0 && $ld && !empty($log->info)) {
            // ugly hack to make sure fullname is shown correctly
            if (($ld->mtable == 'user') and ($ld->field == $DB->sql_concat('firstname', "' '" , 'lastname'))) {
                $log->info = fullname($DB->get_record($ld->mtable, array('id'=>$log->info)), true);
            } else {
                $log->info = $DB->get_field($ld->mtable, $ld->field, array('id'=>$log->info));
            }
        }

        //Filter log->info
        $log->info = format_string($log->info);

        echo '<tr class="r'.$row.'">';
        if ($course->id == SITEID) {
            echo "<td class=\"r$row c0\" >\n";
            echo "    <a href=\"{$CFG->wwwroot}/course/view.php?id={$log->course}\">".$courses[$log->course]."</a>\n";
            echo "</td>\n";
        }
        echo "<td class=\"r$row c1\" align=\"right\">".userdate($log->time, '%a').
             ' '.userdate($log->time, $strftimedatetime)."</td>\n";
        echo "<td class=\"r$row c2\" >\n";
        $link = new moodle_url("/iplookup/index.php?ip=$log->ip&user=$log->userid");
        echo $OUTPUT->action_link($link, $log->ip, new popup_action('click', $link, 'iplookup', array('height' => 400, 'width' => 700)));
        echo "</td>\n";
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id)));
        echo "<td class=\"r$row c3\" >\n";
        echo "    <a href=\"$CFG->wwwroot/user/view.php?id={$log->userid}\">$fullname</a>\n";
        echo "</td>\n";
        echo "<td class=\"r$row c4\">\n";
        echo $log->action .': '.$log->module;
        echo "</td>\n";;
        echo "<td class=\"r$row c5\">{$log->info}</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";

    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "$url&perpage=$perpage");
}


function print_log_csv($course, $user, $date, $order='l.time DESC', $modname,
                        $modid, $modaction, $groupid) {
    global $DB;

    $text = get_string('course')."\t".get_string('time')."\t".get_string('ip_address')."\t".
            get_string('fullnamecourse')."\t".get_string('action')."\t".get_string('info');

    if (!$logs = build_logs_array($course, $user, $date, $order, '', '',
                       $modname, $modid, $modaction, $groupid)) {
        return false;
    }

    $courses = array();

    if ($course->id == SITEID) {
        $courses[0] = '';
        if ($ccc = get_courses('all', 'c.id ASC', 'c.id,c.shortname')) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = $cc->shortname;
            }
        }
    } else {
        $courses[$course->id] = $course->shortname;
    }

    $count=0;
    $ldcache = array();
    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);

    $strftimedatetime = get_string("strftimedatetime");

    $filename = 'logs_'.userdate(time(),get_string('backupnameformat'),99,false);
    $filename .= '.txt';
    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=$filename");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");

    echo get_string('savedat').userdate(time(), $strftimedatetime)."\n";
    echo $text;

    if (empty($logs['logs'])) {
        return true;
    }

    foreach ($logs['logs'] as $log) {
        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = $DB->get_record('log_display', array('module'=>$log->module, 'action'=>$log->action));
            $ldcache[$log->module][$log->action] = $ld;
        }
        if ($ld && !empty($log->info)) {
            // ugly hack to make sure fullname is shown correctly
            if (($ld->mtable == 'user') and ($ld->field ==  $DB->sql_concat('firstname', "' '" , 'lastname'))) {
                $log->info = fullname($DB->get_record($ld->mtable, array('id'=>$log->info)), true);
            } else {
                $log->info = $DB->get_field($ld->mtable, $ld->field, array('id'=>$log->info));
            }
        }

        //Filter log->info
        $log->info = format_string($log->info);
        $log->info = strip_tags(urldecode($log->info));    // Some XSS protection

        $firstField = $courses[$log->course];
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id)));
        $row = array($firstField, userdate($log->time, $strftimedatetime), $log->ip, $fullname, $log->module.' '.$log->action, $log->info);
        $text = implode("\t", $row);
        echo $text." \n";
    }
    return true;
}


function print_log_xls($course, $user, $date, $order='l.time DESC', $modname,
                        $modid, $modaction, $groupid) {

    global $CFG, $DB;

    require_once("$CFG->libdir/excellib.class.php");

    if (!$logs = build_logs_array($course, $user, $date, $order, '', '',
                       $modname, $modid, $modaction, $groupid)) {
        return false;
    }

    $courses = array();

    if ($course->id == SITEID) {
        $courses[0] = '';
        if ($ccc = get_courses('all', 'c.id ASC', 'c.id,c.shortname')) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = $cc->shortname;
            }
        }
    } else {
        $courses[$course->id] = $course->shortname;
    }

    $count=0;
    $ldcache = array();
    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);

    $strftimedatetime = get_string("strftimedatetime");

    $nroPages = ceil(count($logs)/(EXCELROWS-FIRSTUSEDEXCELROW+1));
    $filename = 'logs_'.userdate(time(),get_string('backupnameformat'),99,false);
    $filename .= '.xls';

    $workbook = new MoodleExcelWorkbook('-');
    $workbook->send($filename);

    $worksheet = array();
    $headers = array(get_string('course'), get_string('time'), get_string('ip_address'),
                        get_string('fullnamecourse'),    get_string('action'), get_string('info'));

    // Creating worksheets
    for ($wsnumber = 1; $wsnumber <= $nroPages; $wsnumber++) {
        $sheettitle = get_string('logs').' '.$wsnumber.'-'.$nroPages;
        $worksheet[$wsnumber] =& $workbook->add_worksheet($sheettitle);
        $worksheet[$wsnumber]->set_column(1, 1, 30);
        $worksheet[$wsnumber]->write_string(0, 0, get_string('savedat').
                                    userdate(time(), $strftimedatetime));
        $col = 0;
        foreach ($headers as $item) {
            $worksheet[$wsnumber]->write(FIRSTUSEDEXCELROW-1,$col,$item,'');
            $col++;
        }
    }

    if (empty($logs['logs'])) {
        $workbook->close();
        return true;
    }

    $formatDate =& $workbook->add_format();
    $formatDate->set_num_format(get_string('log_excel_date_format'));

    $row = FIRSTUSEDEXCELROW;
    $wsnumber = 1;
    $myxls =& $worksheet[$wsnumber];
    foreach ($logs['logs'] as $log) {
        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = $DB->get_record('log_display', array('module'=>$log->module, 'action'=>$log->action));
            $ldcache[$log->module][$log->action] = $ld;
        }
        if ($ld && !empty($log->info)) {
            // ugly hack to make sure fullname is shown correctly
            if (($ld->mtable == 'user') and ($ld->field == $DB->sql_concat('firstname', "' '" , 'lastname'))) {
                $log->info = fullname($DB->get_record($ld->mtable, array('id'=>$log->info)), true);
            } else {
                $log->info = $DB->get_field($ld->mtable, $ld->field, array('id'=>$log->info));
            }
        }

        // Filter log->info
        $log->info = format_string($log->info);
        $log->info = strip_tags(urldecode($log->info));  // Some XSS protection

        if ($nroPages>1) {
            if ($row > EXCELROWS) {
                $wsnumber++;
                $myxls =& $worksheet[$wsnumber];
                $row = FIRSTUSEDEXCELROW;
            }
        }

        $myxls->write($row, 0, $courses[$log->course], '');
        $myxls->write_date($row, 1, $log->time, $formatDate); // write_date() does conversion/timezone support. MDL-14934
        $myxls->write($row, 2, $log->ip, '');
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id)));
        $myxls->write($row, 3, $fullname, '');
        $myxls->write($row, 4, $log->module.' '.$log->action, '');
        $myxls->write($row, 5, $log->info, '');

        $row++;
    }

    $workbook->close();
    return true;
}

function print_log_ods($course, $user, $date, $order='l.time DESC', $modname,
                        $modid, $modaction, $groupid) {

    global $CFG, $DB;

    require_once("$CFG->libdir/odslib.class.php");

    if (!$logs = build_logs_array($course, $user, $date, $order, '', '',
                       $modname, $modid, $modaction, $groupid)) {
        return false;
    }

    $courses = array();

    if ($course->id == SITEID) {
        $courses[0] = '';
        if ($ccc = get_courses('all', 'c.id ASC', 'c.id,c.shortname')) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = $cc->shortname;
            }
        }
    } else {
        $courses[$course->id] = $course->shortname;
    }

    $count=0;
    $ldcache = array();
    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);

    $strftimedatetime = get_string("strftimedatetime");

    $nroPages = ceil(count($logs)/(EXCELROWS-FIRSTUSEDEXCELROW+1));
    $filename = 'logs_'.userdate(time(),get_string('backupnameformat'),99,false);
    $filename .= '.ods';

    $workbook = new MoodleODSWorkbook('-');
    $workbook->send($filename);

    $worksheet = array();
    $headers = array(get_string('course'), get_string('time'), get_string('ip_address'),
                        get_string('fullnamecourse'),    get_string('action'), get_string('info'));

    // Creating worksheets
    for ($wsnumber = 1; $wsnumber <= $nroPages; $wsnumber++) {
        $sheettitle = get_string('logs').' '.$wsnumber.'-'.$nroPages;
        $worksheet[$wsnumber] =& $workbook->add_worksheet($sheettitle);
        $worksheet[$wsnumber]->set_column(1, 1, 30);
        $worksheet[$wsnumber]->write_string(0, 0, get_string('savedat').
                                    userdate(time(), $strftimedatetime));
        $col = 0;
        foreach ($headers as $item) {
            $worksheet[$wsnumber]->write(FIRSTUSEDEXCELROW-1,$col,$item,'');
            $col++;
        }
    }

    if (empty($logs['logs'])) {
        $workbook->close();
        return true;
    }

    $formatDate =& $workbook->add_format();
    $formatDate->set_num_format(get_string('log_excel_date_format'));

    $row = FIRSTUSEDEXCELROW;
    $wsnumber = 1;
    $myxls =& $worksheet[$wsnumber];
    foreach ($logs['logs'] as $log) {
        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = $DB->get_record('log_display', array('module'=>$log->module, 'action'=>$log->action));
            $ldcache[$log->module][$log->action] = $ld;
        }
        if ($ld && !empty($log->info)) {
            // ugly hack to make sure fullname is shown correctly
            if (($ld->mtable == 'user') and ($ld->field == $DB->sql_concat('firstname', "' '" , 'lastname'))) {
                $log->info = fullname($DB->get_record($ld->mtable, array('id'=>$log->info)), true);
            } else {
                $log->info = $DB->get_field($ld->mtable, $ld->field, array('id'=>$log->info));
            }
        }

        // Filter log->info
        $log->info = format_string($log->info);
        $log->info = strip_tags(urldecode($log->info));  // Some XSS protection

        if ($nroPages>1) {
            if ($row > EXCELROWS) {
                $wsnumber++;
                $myxls =& $worksheet[$wsnumber];
                $row = FIRSTUSEDEXCELROW;
            }
        }

        $myxls->write_string($row, 0, $courses[$log->course]);
        $myxls->write_date($row, 1, $log->time);
        $myxls->write_string($row, 2, $log->ip);
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id)));
        $myxls->write_string($row, 3, $fullname);
        $myxls->write_string($row, 4, $log->module.' '.$log->action);
        $myxls->write_string($row, 5, $log->info);

        $row++;
    }

    $workbook->close();
    return true;
}


function print_log_graph($course, $userid=0, $type="course.png", $date=0) {
    global $CFG, $USER;
    if (empty($CFG->gdversion)) {
        echo "(".get_string("gdneed").")";
    } else {
        // MDL-10818, do not display broken graph when user has no permission to view graph
        if (has_capability('coursereport/log:view', get_context_instance(CONTEXT_COURSE, $course->id)) ||
            ($course->showreports and $USER->id == $userid)) {
            echo '<img src="'.$CFG->wwwroot.'/course/report/log/graph.php?id='.$course->id.
                 '&amp;user='.$userid.'&amp;type='.$type.'&amp;date='.$date.'" alt="" />';
        }
    }
}


function print_overview($courses) {
    global $CFG, $USER, $DB, $OUTPUT;

    $htmlarray = array();
    if ($modules = $DB->get_records('modules')) {
        foreach ($modules as $mod) {
            if (file_exists(dirname(dirname(__FILE__)).'/mod/'.$mod->name.'/lib.php')) {
                include_once(dirname(dirname(__FILE__)).'/mod/'.$mod->name.'/lib.php');
                $fname = $mod->name.'_print_overview';
                if (function_exists($fname)) {
                    $fname($courses,$htmlarray);
                }
            }
        }
    }
    foreach ($courses as $course) {
        echo $OUTPUT->box_start("coursebox");
        $linkcss = '';
        if (empty($course->visible)) {
            $linkcss = 'class="dimmed"';
        }
        echo $OUTPUT->heading('<a title="'. format_string($course->fullname).'" '.$linkcss.' href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'. format_string($course->fullname).'</a>');
        if (array_key_exists($course->id,$htmlarray)) {
            foreach ($htmlarray[$course->id] as $modname => $html) {
                echo $html;
            }
        }
        echo $OUTPUT->box_end();
    }
}


/**
 * This function trawls through the logs looking for
 * anything new since the user's last login
 */
function print_recent_activity($course) {
    // $course is an object
    global $CFG, $USER, $SESSION, $DB, $OUTPUT;

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    $viewfullnames = has_capability('moodle/site:viewfullnames', $context);

    $timestart = round(time() - COURSE_MAX_RECENT_PERIOD, -2); // better db caching for guests - 100 seconds

    if (!isguestuser()) {
        if (!empty($USER->lastcourseaccess[$course->id])) {
            if ($USER->lastcourseaccess[$course->id] > $timestart) {
                $timestart = $USER->lastcourseaccess[$course->id];
            }
        }
    }

    echo '<div class="activitydate">';
    echo get_string('activitysince', '', userdate($timestart));
    echo '</div>';
    echo '<div class="activityhead">';

    echo '<a href="'.$CFG->wwwroot.'/course/recent.php?id='.$course->id.'">'.get_string('recentactivityreport').'</a>';

    echo "</div>\n";

    $content = false;

/// Firstly, have there been any new enrolments?

    $users = get_recent_enrolments($course->id, $timestart);

    //Accessibility: new users now appear in an <OL> list.
    if ($users) {
        echo '<div class="newusers">';
        echo $OUTPUT->heading(get_string("newusers").':', 3);
        $content = true;
        echo "<ol class=\"list\">\n";
        foreach ($users as $user) {
            $fullname = fullname($user, $viewfullnames);
            echo '<li class="name"><a href="'."$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id\">$fullname</a></li>\n";
        }
        echo "</ol>\n</div>\n";
    }

/// Next, have there been any modifications to the course structure?

    $modinfo =& get_fast_modinfo($course);

    $changelist = array();

    $logs = $DB->get_records_select('log', "time > ? AND course = ? AND
                                            module = 'course' AND
                                            (action = 'add mod' OR action = 'update mod' OR action = 'delete mod')",
                                    array($timestart, $course->id), "id ASC");

    if ($logs) {
        $actions  = array('add mod', 'update mod', 'delete mod');
        $newgones = array(); // added and later deleted items
        foreach ($logs as $key => $log) {
            if (!in_array($log->action, $actions)) {
                continue;
            }
            $info = split(' ', $log->info);

            if ($info[0] == 'label') {     // Labels are ignored in recent activity
                continue;
            }

            if (count($info) != 2) {
                debugging("Incorrect log entry info: id = ".$log->id, DEBUG_DEVELOPER);
                continue;
            }

            $modname    = $info[0];
            $instanceid = $info[1];

            if ($log->action == 'delete mod') {
                // unfortunately we do not know if the mod was visible
                if (!array_key_exists($log->info, $newgones)) {
                    $strdeleted = get_string('deletedactivity', 'moodle', get_string('modulename', $modname));
                    $changelist[$log->info] = array ('operation' => 'delete', 'text' => $strdeleted);
                }
            } else {
                if (!isset($modinfo->instances[$modname][$instanceid])) {
                    if ($log->action == 'add mod') {
                        // do not display added and later deleted activities
                        $newgones[$log->info] = true;
                    }
                    continue;
                }
                $cm = $modinfo->instances[$modname][$instanceid];
                if (!$cm->uservisible) {
                    continue;
                }

                if ($log->action == 'add mod') {
                    $stradded = get_string('added', 'moodle', get_string('modulename', $modname));
                    $changelist[$log->info] = array('operation' => 'add', 'text' => "$stradded:<br /><a href=\"$CFG->wwwroot/mod/$cm->modname/view.php?id={$cm->id}\">".format_string($cm->name, true)."</a>");

                } else if ($log->action == 'update mod' and empty($changelist[$log->info])) {
                    $strupdated = get_string('updated', 'moodle', get_string('modulename', $modname));
                    $changelist[$log->info] = array('operation' => 'update', 'text' => "$strupdated:<br /><a href=\"$CFG->wwwroot/mod/$cm->modname/view.php?id={$cm->id}\">".format_string($cm->name, true)."</a>");
                }
            }
        }
    }

    if (!empty($changelist)) {
        echo $OUTPUT->heading(get_string("courseupdates").':', 3);
        $content = true;
        foreach ($changelist as $changeinfo => $change) {
            echo '<p class="activity">'.$change['text'].'</p>';
        }
    }

/// Now display new things from each module

    $usedmodules = array();
    foreach($modinfo->cms as $cm) {
        if (isset($usedmodules[$cm->modname])) {
            continue;
        }
        if (!$cm->uservisible) {
            continue;
        }
        $usedmodules[$cm->modname] = $cm->modname;
    }

    foreach ($usedmodules as $modname) {      // Each module gets it's own logs and prints them
        if (file_exists($CFG->dirroot.'/mod/'.$modname.'/lib.php')) {
            include_once($CFG->dirroot.'/mod/'.$modname.'/lib.php');
            $print_recent_activity = $modname.'_print_recent_activity';
            if (function_exists($print_recent_activity)) {
                // NOTE: original $isteacher (second parameter below) was replaced with $viewfullnames!
                $content = $print_recent_activity($course, $viewfullnames, $timestart) || $content;
            }
        } else {
            debugging("Missing lib.php in lib/{$modname} - please reinstall files or uninstall the module");
        }
    }

    if (! $content) {
        echo '<p class="message">'.get_string('nothingnew').'</p>';
    }
}

/**
 * For a given course, returns an array of course activity objects
 * Each item in the array contains he following properties:
 */
function get_array_of_activities($courseid) {
//  cm - course module id
//  mod - name of the module (eg forum)
//  section - the number of the section (eg week or topic)
//  name - the name of the instance
//  visible - is the instance visible or not
//  groupingid - grouping id
//  groupmembersonly - is this instance visible to group members only
//  extra - contains extra string to include in any link
    global $CFG, $DB;
    if(!empty($CFG->enableavailability)) {
        require_once($CFG->libdir.'/conditionlib.php');
    }

    $course = $DB->get_record('course', array('id'=>$courseid));

    if (empty($course)) {
        throw new moodle_exception('courseidnotfound');
    }

    $mod = array();

    $rawmods = get_course_mods($courseid);
    if (empty($rawmods)) {
        return $mod; // always return array
    }

    if ($sections = $DB->get_records("course_sections", array("course"=>$courseid), "section ASC")) {
       foreach ($sections as $section) {
           if (!empty($section->sequence)) {
               $sequence = explode(",", $section->sequence);
               foreach ($sequence as $seq) {
                   if (empty($rawmods[$seq])) {
                       continue;
                   }
                   $mod[$seq]->id               = $rawmods[$seq]->instance;
                   $mod[$seq]->cm               = $rawmods[$seq]->id;
                   $mod[$seq]->mod              = $rawmods[$seq]->modname;
                   $mod[$seq]->section          = $section->section;
                   $mod[$seq]->visible          = $rawmods[$seq]->visible;
                   $mod[$seq]->groupmode        = $rawmods[$seq]->groupmode;
                   $mod[$seq]->groupingid       = $rawmods[$seq]->groupingid;
                   $mod[$seq]->groupmembersonly = $rawmods[$seq]->groupmembersonly;
                   $mod[$seq]->indent           = $rawmods[$seq]->indent;
                   $mod[$seq]->completion       = $rawmods[$seq]->completion;
                   $mod[$seq]->extra            = "";
                   if(!empty($CFG->enableavailability)) {
                       condition_info::fill_availability_conditions($rawmods[$seq]);
                       $mod[$seq]->availablefrom    = $rawmods[$seq]->availablefrom;
                       $mod[$seq]->availableuntil   = $rawmods[$seq]->availableuntil;
                       $mod[$seq]->showavailability = $rawmods[$seq]->showavailability;
                       $mod[$seq]->conditionscompletion = $rawmods[$seq]->conditionscompletion;
                       $mod[$seq]->conditionsgrade  = $rawmods[$seq]->conditionsgrade;
                   }

                   $modname = $mod[$seq]->mod;
                   $functionname = $modname."_get_coursemodule_info";

                   if (!file_exists("$CFG->dirroot/mod/$modname/lib.php")) {
                       continue;
                   }

                   include_once("$CFG->dirroot/mod/$modname/lib.php");

                   if (function_exists($functionname)) {
                       if ($info = $functionname($rawmods[$seq])) {
                           if (!empty($info->extra)) {
                               $mod[$seq]->extra = $info->extra;
                           }
                           if (!empty($info->icon)) {
                               $mod[$seq]->icon = $info->icon;
                           }
                           if (!empty($info->iconcomponent)) {
                               $mod[$seq]->iconcomponent = $info->iconcomponent;
                           }
                           if (!empty($info->name)) {
                               $mod[$seq]->name = $info->name;
                           }
                       }
                   }
                   if (!isset($mod[$seq]->name)) {
                       $mod[$seq]->name = $DB->get_field($rawmods[$seq]->modname, "name", array("id"=>$rawmods[$seq]->instance));
                   }
               }
            }
        }
    }
    return $mod;
}


/**
 * Returns a number of useful structures for course displays
 */
function get_all_mods($courseid, &$mods, &$modnames, &$modnamesplural, &$modnamesused) {
    global $DB,$COURSE;

    $mods          = array();    // course modules indexed by id
    $modnames      = array();    // all course module names (except resource!)
    $modnamesplural= array();    // all course module names (plural form)
    $modnamesused  = array();    // course module names used

    if ($allmods = $DB->get_records("modules")) {
        foreach ($allmods as $mod) {
            if ($mod->visible) {
                $modnames[$mod->name] = get_string("modulename", "$mod->name");
                $modnamesplural[$mod->name] = get_string("modulenameplural", "$mod->name");
            }
        }
        asort($modnames, SORT_LOCALE_STRING);
    } else {
        print_error("nomodules", 'debug');
    }

    $course = ($courseid==$COURSE->id) ? $COURSE : $DB->get_record('course',array('id'=>$courseid));
    $modinfo = get_fast_modinfo($course);

    if ($rawmods=$modinfo->cms) {
        foreach($rawmods as $mod) {    // Index the mods
            if (empty($modnames[$mod->modname])) {
                continue;
            }
            $mods[$mod->id] = $mod;
            $mods[$mod->id]->modfullname = $modnames[$mod->modname];
            if (!$mod->visible and !has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_COURSE, $courseid))) {
                continue;
            }
            // Check groupings
            if (!groups_course_module_visible($mod)) {
                continue;
            }
            $modnamesused[$mod->modname] = $modnames[$mod->modname];
        }
        if ($modnamesused) {
            asort($modnamesused, SORT_LOCALE_STRING);
        }
    }
}


function get_all_sections($courseid) {
    global $DB;
    return $DB->get_records("course_sections", array("course"=>"$courseid"), "section",
                       "section, id, course, summary, sequence, visible");
}

function course_set_display($courseid, $display=0) {
    global $USER, $DB;

    if ($display == "all" or empty($display)) {
        $display = 0;
    }

    if (!isloggedin() or isguestuser()) {
        //do not store settings in db for guests
    } else if ($DB->record_exists("course_display", array("userid" => $USER->id, "course"=>$courseid))) {
        $DB->set_field("course_display", "display", $display, array("userid"=>$USER->id, "course"=>$courseid));
    } else {
        $record = new object();
        $record->userid = $USER->id;
        $record->course = $courseid;
        $record->display = $display;
        $DB->insert_record("course_display", $record);
    }

    return $USER->display[$courseid] = $display;  // Note: = not ==
}

/**
 * For a given course section, markes it visible or hidden,
 * and does the same for every activity in that section
 */
function set_section_visible($courseid, $sectionnumber, $visibility) {
    global $DB;

    if ($section = $DB->get_record("course_sections", array("course"=>$courseid, "section"=>$sectionnumber))) {
        $DB->set_field("course_sections", "visible", "$visibility", array("id"=>$section->id));
        if (!empty($section->sequence)) {
            $modules = explode(",", $section->sequence);
            foreach ($modules as $moduleid) {
                set_coursemodule_visible($moduleid, $visibility, true);
            }
        }
        rebuild_course_cache($courseid);
    }
}

/**
 * Prints a section full of activity modules
 */
function print_section($course, $section, $mods, $modnamesused, $absolute=false, $width="100%", $hidecompletion=false) {
    global $CFG, $USER, $DB, $PAGE, $OUTPUT;

    static $initialised;

    static $groupbuttons;
    static $groupbuttonslink;
    static $isediting;
    static $ismoving;
    static $strmovehere;
    static $strmovefull;
    static $strunreadpostsone;
    static $usetracking;
    static $groupings;

    if (!isset($initialised)) {
        $groupbuttons     = ($course->groupmode or (!$course->groupmodeforce));
        $groupbuttonslink = (!$course->groupmodeforce);
        $isediting        = $PAGE->user_is_editing();
        $ismoving         = $isediting && ismoving($course->id);
        if ($ismoving) {
            $strmovehere  = get_string("movehere");
            $strmovefull  = strip_tags(get_string("movefull", "", "'$USER->activitycopyname'"));
        }
        include_once($CFG->dirroot.'/mod/forum/lib.php');
        if ($usetracking = forum_tp_can_track_forums()) {
            $strunreadpostsone = get_string('unreadpostsone', 'forum');
        }
        $initialised = true;
    }

    $labelformatoptions = new object();
    $labelformatoptions->noclean = true;

/// Casting $course->modinfo to string prevents one notice when the field is null
    $modinfo = get_fast_modinfo($course);
    $completioninfo = new completion_info($course);

    //Acccessibility: replace table with list <ul>, but don't output empty list.
    if (!empty($section->sequence)) {

        // Fix bug #5027, don't want style=\"width:$width\".
        echo "<ul class=\"section img-text\">\n";
        $sectionmods = explode(",", $section->sequence);

        foreach ($sectionmods as $modnumber) {
            if (empty($mods[$modnumber])) {
                continue;
            }

            $mod = $mods[$modnumber];

            if ($ismoving and $mod->id == $USER->activitycopy) {
                // do not display moving mod
                continue;
            }

            if (isset($modinfo->cms[$modnumber])) {
                // We can continue (because it will not be displayed at all)
                // if:
                // 1) The activity is not visible to users
                // and
                // 2a) The 'showavailability' option is not set (if that is set,
                //     we need to display the activity so we can show
                //     availability info)
                // or
                // 2b) The 'availableinfo' is empty, i.e. the activity was
                //     hidden in a way that leaves no info, such as using the
                //     eye icon.
                if (!$modinfo->cms[$modnumber]->uservisible &&
                    (empty($modinfo->cms[$modnumber]->showavailability) ||
                      empty($modinfo->cms[$modnumber]->availableinfo))) {
                    // visibility shortcut
                    continue;
                }
            } else {
                if (!file_exists("$CFG->dirroot/mod/$mod->modname/lib.php")) {
                    // module not installed
                    continue;
                }
                if (!coursemodule_visible_for_user($mod) &&
                    empty($mod->showavailability)) {
                    // full visibility check
                    continue;
                }
            }

            // In some cases the activity is visible to user, but it is
            // dimmed. This is done if viewhiddenactivities is true and if:
            // 1. the activity is not visible, or
            // 2. the activity has dates set which do not include current, or
            // 3. the activity has any other conditions set (regardless of whether
            //    current user meets them)
            $canviewhidden = has_capability(
                'moodle/course:viewhiddenactivities',
                get_context_instance(CONTEXT_MODULE, $mod->id));
            $accessiblebutdim = false;
            if ($canviewhidden) {
                $accessiblebutdim = !$mod->visible;
                if (!empty($CFG->enableavailability)) {
                    $accessiblebutdim = $accessiblebutdim ||
                        $mod->availablefrom > time() ||
                        ($mod->availableuntil && $mod->availableuntil < time()) ||
                        count($mod->conditionsgrade) > 0 ||
                        count($mod->conditionscompletion) > 0;
                }
            }

            echo '<li class="activity '.$mod->modname.'" id="module-'.$modnumber.'">';  // Unique ID
            if ($ismoving) {
                echo '<a title="'.$strmovefull.'"'.
                     ' href="'.$CFG->wwwroot.'/course/mod.php?moveto='.$mod->id.'&amp;sesskey='.sesskey().'">'.
                     '<img class="movetarget" src="'.$OUTPUT->pix_url('movehere') . '" '.
                     ' alt="'.$strmovehere.'" /></a><br />
                     ';
            }

            if ($mod->indent) {
                echo $OUTPUT->spacer(array('height'=>12, 'width'=>(20 * $mod->indent))); // should be done with CSS instead
            }

            $extra = '';
            if (!empty($modinfo->cms[$modnumber]->extra)) {
                $extra = $modinfo->cms[$modnumber]->extra;
            }

            if ($mod->modname == "label") {
                if ($accessiblebutdim || !$mod->uservisible) {
                    echo '<div class="dimmed_text"><span class="accesshide">'.
                        get_string('hiddenfromstudents').'</span>';
                }
                echo format_text($extra, FORMAT_HTML, $labelformatoptions);
                if ($accessiblebutdim || !$mod->uservisible) {
                    echo "</div>";
                }
                if (!empty($mod->groupingid) && has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_COURSE, $course->id))) {
                    if (!isset($groupings)) {
                        $groupings = groups_get_all_groupings($course->id);
                    }
                    echo " <span class=\"groupinglabel\">(".format_string($groupings[$mod->groupingid]->name).')</span>';
                }

            } else { // Normal activity
                $instancename = format_string($modinfo->cms[$modnumber]->name, true,  $course->id);

                $customicon = $modinfo->cms[$modnumber]->icon;
                if (!empty($customicon)) {
                    if (substr($customicon, 0, 4) === 'mod/') {
                        list($modname, $iconname) = explode('/', substr($customicon, 4), 2);
                        $icon = $OUTPUT->pix_url(str_replace(array('.gif', '.png'), '', $customicon), $modname);
                    } else {
                        $icon = $OUTPUT->pix_url(str_replace(array('.gif', '.png'), '', $customicon));
                    }
                } else {
                    $icon = $OUTPUT->pix_url('icon', $mod->modname);
                }

                //Accessibility: for files get description via icon, this is very ugly hack!
                $altname = '';
                $altname = $mod->modfullname;
                if (!empty($customicon)) {
                    $archetype = plugin_supports('mod', $mod->modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
                    if ($archetype == MOD_ARCHETYPE_RESOURCE) {
                        $possaltname = str_replace(array('.gif', '.png'), '', $customicon).'.gif';

                        $mimetype = mimeinfo_from_icon('type', $possaltname);
                        $altname = get_mimetype_description($mimetype);
                    }
                }
                // Avoid unnecessary duplication.
                if (false !== stripos($instancename, $altname)) {
                    $altname = '';
                }
                // File type after name, for alphabetic lists (screen reader).
                if ($altname) {
                    $altname = get_accesshide(' '.$altname);
                }

                // We may be displaying this just in order to show information
                // about visibility, without the actual link
                if ($mod->uservisible) {
                    // Display normal module link
                    if (!$accessiblebutdim) {
                        $linkcss = '';
                        $accesstext  ='';
                    } else {
                        $linkcss = ' class="dimmed" ';
                        $accesstext = '<span class="accesshide">'.
                            get_string('hiddenfromstudents').': </span>';
                    }

                    echo '<a '.$linkcss.' '.$extra.
                         ' href="'.$CFG->wwwroot.'/mod/'.$mod->modname.'/view.php?id='.$mod->id.'">'.
                         '<img src="'.$icon.'" class="activityicon" alt="" /> '.
                         $accesstext.'<span>'.$instancename.$altname.'</span></a>';

                    if (!empty($mod->groupingid) && has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_COURSE, $course->id))) {
                        if (!isset($groupings)) {
                            $groupings = groups_get_all_groupings($course->id);
                        }
                        echo " <span class=\"groupinglabel\">(".format_string($groupings[$mod->groupingid]->name).')</span>';
                    }
                } else {
                    // Display greyed-out text of link
                    echo '<span class="dimmed_text" '.$extra.' ><span class="accesshide">'.
                        get_string('notavailableyet','condition').': </span>'.
                        '<img src="'.$icon.'" class="activityicon" alt="" /> <span>'.
                        $instancename.$altname.'</span></span>';
                }
            }
            if ($usetracking && $mod->modname == 'forum') {
                if ($unread = forum_tp_count_forum_unread_posts($mod, $course)) {
                    echo '<span class="unread"> <a href="'.$CFG->wwwroot.'/mod/forum/view.php?id='.$mod->id.'">';
                    if ($unread == 1) {
                        echo $strunreadpostsone;
                    } else {
                        print_string('unreadpostsnumber', 'forum', $unread);
                    }
                    echo '</a></span>';
                }
            }

            if ($isediting) {
                // TODO: we must define this as mod property!
                if ($groupbuttons and $mod->modname != 'label' and $mod->modname != 'resource' and $mod->modname != 'glossary') {
                    if (! $mod->groupmodelink = $groupbuttonslink) {
                        $mod->groupmode = $course->groupmode;
                    }

                } else {
                    $mod->groupmode = false;
                }
                echo '&nbsp;&nbsp;';
                echo make_editing_buttons($mod, $absolute, true, $mod->indent, $section->section);
            }

            // Completion
            $completion = $hidecompletion
                ? COMPLETION_TRACKING_NONE
                : $completioninfo->is_enabled($mod);
            if ($completion!=COMPLETION_TRACKING_NONE && isloggedin() &&
                !isguestuser() && $mod->uservisible) {
                $completiondata = $completioninfo->get_data($mod,true);
                $completionicon = '';
                if ($isediting) {
                    switch ($completion) {
                        case COMPLETION_TRACKING_MANUAL :
                            $completionicon = 'manual-enabled'; break;
                        case COMPLETION_TRACKING_AUTOMATIC :
                            $completionicon = 'auto-enabled'; break;
                        default: // wtf
                    }
                } else if ($completion==COMPLETION_TRACKING_MANUAL) {
                    switch($completiondata->completionstate) {
                        case COMPLETION_INCOMPLETE:
                            $completionicon = 'manual-n'; break;
                        case COMPLETION_COMPLETE:
                            $completionicon = 'manual-y'; break;
                    }
                } else { // Automatic
                    switch($completiondata->completionstate) {
                        case COMPLETION_INCOMPLETE:
                            $completionicon = 'auto-n'; break;
                        case COMPLETION_COMPLETE:
                            $completionicon = 'auto-y'; break;
                        case COMPLETION_COMPLETE_PASS:
                            $completionicon = 'auto-pass'; break;
                        case COMPLETION_COMPLETE_FAIL:
                            $completionicon = 'auto-fail'; break;
                    }
                }
                if ($completionicon) {
                    $imgsrc = $OUTPUT->pix_url('i/completion-'.$completionicon);
                    $imgalt = s(get_string('completion-alt-'.$completionicon, 'completion'));
                    if ($completion == COMPLETION_TRACKING_MANUAL && !$isediting) {
                        $imgtitle = s(get_string('completion-title-'.$completionicon, 'completion'));
                        $newstate =
                            $completiondata->completionstate==COMPLETION_COMPLETE
                            ? COMPLETION_INCOMPLETE
                            : COMPLETION_COMPLETE;
                        // In manual mode the icon is a toggle form...

                        // If this completion state is used by the
                        // conditional activities system, we need to turn
                        // off the JS.
                        if (!empty($CFG->enableavailability) &&
                            condition_info::completion_value_used_as_condition($course, $mod)) {
                            $extraclass = ' preventjs';
                        } else {
                            $extraclass = '';
                        }
                        echo "
<form class='togglecompletion$extraclass' method='post' action='togglecompletion.php'><div>
<input type='hidden' name='id' value='{$mod->id}' />
<input type='hidden' name='sesskey' value='".sesskey()."' />
<input type='hidden' name='completionstate' value='$newstate' />
<input type='image' src='$imgsrc' alt='$imgalt' title='$imgtitle' />
</div></form>";
                    } else {
                        // In auto mode, or when editing, the icon is just an image
                        echo "<span class='autocompletion'>";
                        echo "<img src='$imgsrc' alt='$imgalt' title='$imgalt' /></span>";
                    }
                }
            }

            // Show availability information (for someone who isn't allowed to
            // see the activity itself, or for staff)
            if (!$mod->uservisible) {
                echo '<div class="availabilityinfo">'.$mod->availableinfo.'</div>';
            } else if ($canviewhidden && !empty($CFG->enableavailability)) {
                $ci = new condition_info($mod);
                $fullinfo = $ci->get_full_information();
                if($fullinfo) {
                    echo '<div class="availabilityinfo">'.get_string($mod->showavailability
                        ? 'userrestriction_visible'
                        : 'userrestriction_hidden','condition',
                        $fullinfo).'</div>';
                }
            }

            echo "</li>\n";
        }

    } elseif ($ismoving) {
        echo "<ul class=\"section\">\n";
    }

    if ($ismoving) {
        echo '<li><a title="'.$strmovefull.'"'.
             ' href="'.$CFG->wwwroot.'/course/mod.php?movetosection='.$section->id.'&amp;sesskey='.sesskey().'">'.
             '<img class="movetarget" src="'.$OUTPUT->pix_url('movehere') . '" '.
             ' alt="'.$strmovehere.'" /></a></li>
             ';
    }
    if (!empty($section->sequence) || $ismoving) {
        echo "</ul><!--class='section'-->\n\n";
    }
}

/**
 * Prints the menus to add activities and resources.
 */
function print_section_add_menus($course, $section, $modnames, $vertical=false, $return=false) {
    global $CFG, $OUTPUT;

    // check to see if user can add menus
    if (!has_capability('moodle/course:manageactivities', get_context_instance(CONTEXT_COURSE, $course->id))) {
        return false;
    }

    $urlbase = "/course/mod.php?id=$course->id&section=$section&sesskey=".sesskey().'&add=';

    $resources = array();
    $activities = array();

    foreach($modnames as $modname=>$modnamestr) {
        if (!course_allowed_module($course, $modname)) {
            continue;
        }

        $libfile = "$CFG->dirroot/mod/$modname/lib.php";
        if (!file_exists($libfile)) {
            continue;
        }
        include_once($libfile);
        $gettypesfunc =  $modname.'_get_types';
        if (function_exists($gettypesfunc)) {
            // NOTE: this is legacy stuff, module subtypes are very strongly discouraged!!
            if ($types = $gettypesfunc()) {
                $menu = array();
                $atype = null;
                $groupname = null;
                foreach($types as $type) {
                    if ($type->typestr === '--') {
                        continue;
                    }
                    if (strpos($type->typestr, '--') === 0) {
                        $groupname = str_replace('--', '', $type->typestr);
                        continue;
                    }
                    $type->type = str_replace('&amp;', '&', $type->type);
                    if ($type->modclass == MOD_CLASS_RESOURCE) {
                        $atype = MOD_CLASS_RESOURCE;
                    }
                    $menu[$urlbase.$type->type] = $type->typestr;
                }
                if (!is_null($groupname)) {
                    if ($atype == MOD_CLASS_RESOURCE) {
                        $resources[] = array($groupname=>$menu);
                    } else {
                        $activities[] = array($groupname=>$menu);
                    }
                } else {
                    if ($atype == MOD_CLASS_RESOURCE) {
                        $resources = array_merge($resources, $menu);
                    } else {
                        $activities = array_merge($activities, $menu);
                    }
                }
            }
        } else {
            $archetype = plugin_supports('mod', $modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
            if ($archetype == MOD_ARCHETYPE_RESOURCE) {
                $resources[$urlbase.$modname] = $modnamestr;
            } else {
                // all other archetypes are considered activity
                $activities[$urlbase.$modname] = $modnamestr;
            }
        }
    }

    $straddactivity = get_string('addactivity');
    $straddresource = get_string('addresource');

    $output  = '<div class="section_add_menus">';

    if (!$vertical) {
        $output .= '<div class="horizontal">';
    }

    if (!empty($resources)) {
        $select = new url_select($resources, '', array(''=>$straddresource), "ressection$section");
        $select->set_help_icon('resource/types', $straddresource);
        $output .= $OUTPUT->render($select);
    }

    if (!empty($activities)) {
        $select = new url_select($activities, '', array(''=>$straddactivity), "section$section");
        $select->set_help_icon('mods', $straddactivity);
        $output .= $OUTPUT->render($select);
    }

    if (!$vertical) {
        $output .= '</div>';
    }

    $output .= '</div>';

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Return the course category context for the category with id $categoryid, except
 * that if $categoryid is 0, return the system context.
 *
 * @param integer $categoryid a category id or 0.
 * @return object the corresponding context
 */
function get_category_or_system_context($categoryid) {
    if ($categoryid) {
        return get_context_instance(CONTEXT_COURSECAT, $categoryid);
    } else {
        return get_context_instance(CONTEXT_SYSTEM);
    }
}

/**
 * Rebuilds the cached list of course activities stored in the database
 * @param int $courseid - id of course to rebuil, empty means all
 * @param boolean $clearonly - only clear the modinfo fields, gets rebuild automatically on the fly
 */
function rebuild_course_cache($courseid=0, $clearonly=false) {
    global $COURSE, $DB, $OUTPUT;

    if ($clearonly) {
        if (empty($courseid)) {
            $courseselect = array();
        } else {
            $courseselect = array('id'=>$courseid);
        }
        $DB->set_field('course', 'modinfo', null, $courseselect);
        // update cached global COURSE too ;-)
        if ($courseid == $COURSE->id or empty($courseid)) {
            $COURSE->modinfo = null;
        }
        // reset the fast modinfo cache
        $reset = 'reset';
        get_fast_modinfo($reset);
        return;
    }

    if ($courseid) {
        $select = array('id'=>$courseid);
    } else {
        $select = array();
        @set_time_limit(0);  // this could take a while!   MDL-10954
    }

    if ($rs = $DB->get_recordset("course", $select,'','id,fullname')) {
        foreach ($rs as $course) {
            $modinfo = serialize(get_array_of_activities($course->id));
            if (!$DB->set_field("course", "modinfo", $modinfo, array("id"=>$course->id))) {
                echo $OUTPUT->notification("Could not cache module information for course '" . format_string($course->fullname) . "'!");
            }
            // update cached global COURSE too ;-)
            if ($course->id == $COURSE->id) {
                $COURSE->modinfo = $modinfo;
            }
        }
        $rs->close();
    }
    // reset the fast modinfo cache
    $reset = 'reset';
    get_fast_modinfo($reset);
}

/**
 * Gets the child categories of a given coures category. Uses a static cache
 * to make repeat calls efficient.
 *
 * @param unknown_type $parentid the id of a course category.
 * @return array all the child course categories.
 */
function get_child_categories($parentid) {
    static $allcategories = null;

    // only fill in this variable the first time
    if (null == $allcategories) {
        $allcategories = array();

        $categories = get_categories();
        foreach ($categories as $category) {
            if (empty($allcategories[$category->parent])) {
                $allcategories[$category->parent] = array();
            }
            $allcategories[$category->parent][] = $category;
        }
    }

    if (empty($allcategories[$parentid])) {
        return array();
    } else {
        return $allcategories[$parentid];
    }
}

/**
 * This function recursively travels the categories, building up a nice list
 * for display. It also makes an array that list all the parents for each
 * category.
 *
 * For example, if you have a tree of categories like:
 *   Miscellaneous (id = 1)
 *      Subcategory (id = 2)
 *         Sub-subcategory (id = 4)
 *   Other category (id = 3)
 * Then after calling this function you will have
 * $list = array(1 => 'Miscellaneous', 2 => 'Miscellaneous / Subcategory',
 *      4 => 'Miscellaneous / Subcategory / Sub-subcategory',
 *      3 => 'Other category');
 * $parents = array(2 => array(1), 4 => array(1, 2));
 *
 * If you specify $requiredcapability, then only categories where the current
 * user has that capability will be added to $list, although all categories
 * will still be added to $parents, and if you only have $requiredcapability
 * in a child category, not the parent, then the child catgegory will still be
 * included.
 *
 * If you specify the option $excluded, then that category, and all its children,
 * are omitted from the tree. This is useful when you are doing something like
 * moving categories, where you do not want to allow people to move a category
 * to be the child of itself.
 *
 * @param array $list For output, accumulates an array categoryid => full category path name
 * @param array $parents For output, accumulates an array categoryid => list of parent category ids.
 * @param string/array $requiredcapability if given, only categories where the current
 *      user has this capability will be added to $list. Can also be an array of capabilities,
 *      in which case they are all required.
 * @param integer $excludeid Omit this category and its children from the lists built.
 * @param object $category Build the tree starting at this category - otherwise starts at the top level.
 * @param string $path For internal use, as part of recursive calls.
 */
function make_categories_list(&$list, &$parents, $requiredcapability = '',
        $excludeid = 0, $category = NULL, $path = "") {

    // initialize the arrays if needed
    if (!is_array($list)) {
        $list = array();
    }
    if (!is_array($parents)) {
        $parents = array();
    }

    if (empty($category)) {
        // Start at the top level.
        $category = new stdClass;
        $category->id = 0;
    } else {
        // This is the excluded category, don't include it.
        if ($excludeid > 0 && $excludeid == $category->id) {
            return;
        }

        // Update $path.
        if ($path) {
            $path = $path.' / '.format_string($category->name);
        } else {
            $path = format_string($category->name);
        }

        // Add this category to $list, if the permissions check out.
        if (empty($requiredcapability)) {
            $list[$category->id] = $path;

        } else {
            ensure_context_subobj_present($category, CONTEXT_COURSECAT);
            $requiredcapability = (array)$requiredcapability;
            if (has_all_capabilities($requiredcapability, $category->context)) {
                $list[$category->id] = $path;
            }
        }
    }

    // Add all the children recursively, while updating the parents array.
    if ($categories = get_child_categories($category->id)) {
        foreach ($categories as $cat) {
            if (!empty($category->id)) {
                if (isset($parents[$category->id])) {
                    $parents[$cat->id]   = $parents[$category->id];
                }
                $parents[$cat->id][] = $category->id;
            }
            make_categories_list($list, $parents, $requiredcapability, $excludeid, $cat, $path);
        }
    }
}


/**
 * Recursive function to print out all the categories in a nice format
 * with or without courses included
 */
function print_whole_category_list($category=NULL, $displaylist=NULL, $parentslist=NULL, $depth=-1, $showcourses = true) {
    global $CFG;

    // maxcategorydepth == 0 meant no limit
    if (!empty($CFG->maxcategorydepth) && $depth >= $CFG->maxcategorydepth) {
        return;
    }

    if (!$displaylist) {
        make_categories_list($displaylist, $parentslist);
    }

    if ($category) {
        if ($category->visible or has_capability('moodle/category:viewhiddencategories', get_context_instance(CONTEXT_SYSTEM))) {
            print_category_info($category, $depth, $showcourses);
        } else {
            return;  // Don't bother printing children of invisible categories
        }

    } else {
        $category->id = "0";
    }

    if ($categories = get_child_categories($category->id)) {   // Print all the children recursively
        $countcats = count($categories);
        $count = 0;
        $first = true;
        $last = false;
        foreach ($categories as $cat) {
            $count++;
            if ($count == $countcats) {
                $last = true;
            }
            $up = $first ? false : true;
            $down = $last ? false : true;
            $first = false;

            print_whole_category_list($cat, $displaylist, $parentslist, $depth + 1, $showcourses);
        }
    }
}

/**
 * This function will return $options array for html_writer::select(), with whitespace to denote nesting.
 */
function make_categories_options() {
    make_categories_list($cats,$parents);
    foreach ($cats as $key => $value) {
        if (array_key_exists($key,$parents)) {
            if ($indent = count($parents[$key])) {
                for ($i = 0; $i < $indent; $i++) {
                    $cats[$key] = '&nbsp;'.$cats[$key];
                }
            }
        }
    }
    return $cats;
}

/**
 * Prints the category info in indented fashion
 * This function is only used by print_whole_category_list() above
 */
function print_category_info($category, $depth, $showcourses = false) {
    global $CFG, $DB, $OUTPUT;
    static $strallowguests, $strrequireskey, $strsummary;

    if (empty($strsummary)) {
        $strallowguests = get_string('allowguests');
        $strrequireskey = get_string('requireskey');
        $strsummary = get_string('summary');
    }

    $catlinkcss = $category->visible ? '' : ' class="dimmed" ';

    static $coursecount = null;
    if (null === $coursecount) {
        // only need to check this once
        $coursecount = $DB->count_records('course') <= FRONTPAGECOURSELIMIT;
    }

    if ($showcourses and $coursecount) {
        $catimage = '<img src="'.$OUTPUT->pix_url('i/course') . '" alt="" />';
    } else {
        $catimage = "&nbsp;";
    }

    echo "\n\n".'<table class="categorylist">';

    $courses = get_courses($category->id, 'c.sortorder ASC', 'c.id,c.sortorder,c.visible,c.fullname,c.shortname,c.password,c.summary,c.guest,c.cost,c.currency');
    if ($showcourses and $coursecount) {

        echo '<tr>';

        if ($depth) {
            $indent = $depth*30;
            $rows = count($courses) + 1;
            echo '<td class="category indentation" rowspan="'.$rows.'" valign="top">';

            echo $OUTPUT->spacer(array('height'=>10, 'width'=>$indent, 'br'=>true)); // should be done with CSS instead
            echo '</td>';
        }

        echo '<td valign="top" class="category image">'.$catimage.'</td>';
        echo '<td valign="top" class="category name">';
        echo '<a '.$catlinkcss.' href="'.$CFG->wwwroot.'/course/category.php?id='.$category->id.'">'. format_string($category->name).'</a>';
        echo '</td>';
        echo '<td class="category info">&nbsp;</td>';
        echo '</tr>';

        // does the depth exceed maxcategorydepth
        // maxcategorydepth == 0 or unset meant no limit

        $limit = !(isset($CFG->maxcategorydepth) && ($depth >= $CFG->maxcategorydepth-1));

        if ($courses && ($limit || $CFG->maxcategorydepth == 0)) {
            foreach ($courses as $course) {
                $linkcss = $course->visible ? '' : ' class="dimmed" ';
                echo '<tr><td valign="top">&nbsp;';
                echo '</td><td valign="top" class="course name">';
                echo '<a '.$linkcss.' href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'. format_string($course->fullname).'</a>';
                echo '</td><td align="right" valign="top" class="course info">';
                if ($course->guest ) {
                    echo '<a title="'.$strallowguests.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
                    echo '<img alt="'.$strallowguests.'" src="'.$OUTPUT->pix_url('i/guest') . '" /></a>';
                } else {
                    echo '<img alt="" style="width:18px;height:16px;" src="'.$OUTPUT->pix_url('spacer') . '" />';
                }
                if ($course->password) {
                    echo '<a title="'.$strrequireskey.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
                    echo '<img alt="'.$strrequireskey.'" src="'.$OUTPUT->pix_url('i/key') . '" /></a>';
                } else {
                    echo '<img alt="" style="width:18px;height:16px;" src="'.$OUTPUT->pix_url('spacer') . '" />';
                }
                if ($course->summary) {
                    $link = new moodle_url('/course/info.php?id='.$course->id);
                    echo $OUTPUT->action_link($link, '<img alt="'.$strsummary.'" src="'.$OUTPUT->pix_url('i/info') . '" />',
                        new popup_action('click', $link, 'courseinfo', array('height' => 400, 'width' => 500)),
                        array('title'=>$strsummary));
                } else {
                    echo '<img alt="" style="width:18px;height:16px;" src="'.$OUTPUT->pix_url('spacer') . '" />';
                }
                echo '</td></tr>';
            }
        }
    } else {

        echo '<tr>';

        if ($depth) {
            $indent = $depth*20;
            echo '<td class="category indentation" valign="top">';
            echo $OUTPUT->spacer(array('height'=>10, 'width'=>$indent, 'br'=>true)); // should be done with CSS instead
            echo '</td>';
        }

        echo '<td valign="top" class="category name">';
        echo '<a '.$catlinkcss.' href="'.$CFG->wwwroot.'/course/category.php?id='.$category->id.'">'. format_string($category->name).'</a>';
        echo '</td>';
        echo '<td valign="top" class="category number">';
        if (count($courses)) {
           echo count($courses);
        }
        echo '</td></tr>';
    }
    echo '</table>';
}

/**
 * Print the buttons relating to course requests.
 *
 * @param object $systemcontext the system context.
 */
function print_course_request_buttons($systemcontext) {
    global $CFG, $DB, $OUTPUT;
    if (empty($CFG->enablecourserequests)) {
        return;
    }
    if (!has_capability('moodle/course:create', $systemcontext) && has_capability('moodle/course:request', $systemcontext)) {
    /// Print a button to request a new course
        echo $OUTPUT->single_button('request.php', get_string('requestcourse'), 'get');
    }
    /// Print a button to manage pending requests
    if (has_capability('moodle/site:approvecourse', $systemcontext)) {
        $disabled = !$DB->record_exists('course_request', array());
        echo $OUTPUT->single_button('pending.php', get_string('coursespending'), 'get', array('disabled'=>$disabled));
    }
}

/**
 * Does the user have permission to edit things in this category?
 *
 * @param integer $categoryid The id of the category we are showing, or 0 for system context.
 * @return boolean has_any_capability(array(...), ...); in the appropriate context.
 */
function can_edit_in_category($categoryid = 0) {
    $context = get_category_or_system_context($categoryid);
    return has_any_capability(array('moodle/category:manage', 'moodle/course:create'), $context);
}

/**
 * Prints the turn editing on/off button on course/index.php or course/category.php.
 *
 * @param integer $categoryid The id of the category we are showing, or 0 for system context.
 * @return string HTML of the editing button, or empty string, if this user is not allowed
 *      to see it.
 */
function update_category_button($categoryid = 0) {
    global $CFG, $PAGE, $OUTPUT;

    // Check permissions.
    if (!can_edit_in_category($categoryid)) {
        return '';
    }

    // Work out the appropriate action.
    if ($PAGE->user_is_editing()) {
        $label = get_string('turneditingoff');
        $edit = 'off';
    } else {
        $label = get_string('turneditingon');
        $edit = 'on';
    }

    // Generate the button HTML.
    $options = array('categoryedit' => $edit, 'sesskey' => sesskey());
    if ($categoryid) {
        $options['id'] = $categoryid;
        $page = 'category.php';
    } else {
        $page = 'index.php';
    }
    return $OUTPUT->single_button(new moodle_url('/course/' . $page, $options), $label, 'get');
}

/**
 * Category is 0 (for all courses) or an object
 */
function print_courses($category) {
    global $CFG, $OUTPUT;

    if (!is_object($category) && $category==0) {
        $categories = get_child_categories(0);  // Parent = 0   ie top-level categories only
        if (is_array($categories) && count($categories) == 1) {
            $category   = array_shift($categories);
            $courses    = get_courses_wmanagers($category->id,
                                                'c.sortorder ASC',
                                                array('password','summary','summaryformat','currency'));
        } else {
            $courses    = get_courses_wmanagers('all',
                                                'c.sortorder ASC',
                                                array('password','summary','summaryformat','currency'));
        }
        unset($categories);
    } else {
        $courses    = get_courses_wmanagers($category->id,
                                            'c.sortorder ASC',
                                            array('password','summary','summaryformat','currency'));
    }

    if ($courses) {
        echo '<ul class="unlist">';
        foreach ($courses as $course) {
            $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
            if ($course->visible == 1 || has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                echo '<li>';
                print_course($course);
                echo "</li>\n";
            }
        }
        echo "</ul>\n";
    } else {
        echo $OUTPUT->heading(get_string("nocoursesyet"));
        $context = get_context_instance(CONTEXT_SYSTEM);
        if (has_capability('moodle/course:create', $context)) {
            $options = array();
            $options['category'] = $category->id;
            echo '<div class="addcoursebutton">';
            echo $OUTPUT->single_button(new moodle_url('/course/edit.php', $options), get_string("addnewcourse"));
            echo '</div>';
        }
    }
}

/**
 * Print a description of a course, suitable for browsing in a list.
 *
 * @param object $course the course object.
 * @param string $highlightterms (optional) some search terms that should be highlighted in the display.
 */
function print_course($course, $highlightterms = '') {
    global $CFG, $USER, $DB, $OUTPUT;

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    // Rewrite file URLs so that they are correct
    $course->summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php', $context->id, 'course_summary', $course->id);

    $linkcss = $course->visible ? '' : ' class="dimmed" ';

    echo '<div class="coursebox clearfix">';
    echo '<div class="info">';
    echo '<h3 class="name"><a title="'.get_string('entercourse').'"'.
         $linkcss.' href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.
         highlight($highlightterms, format_string($course->fullname)).'</a></h3>';

    /// first find all roles that are supposed to be displayed

    if (!empty($CFG->coursemanager)) {
        $managerroles = split(',', $CFG->coursemanager);
        $namesarray = array();
        if (isset($course->managers)) {
            if (count($course->managers)) {
                $rusers = $course->managers;
                $canviewfullnames = has_capability('moodle/site:viewfullnames', $context);

                 /// Rename some of the role names if needed
                if (isset($context)) {
                    $aliasnames = $DB->get_records('role_names', array('contextid'=>$context->id), '', 'roleid,contextid,name');
                }

                // keep a note of users displayed to eliminate duplicates
                $usersshown = array();
                foreach ($rusers as $ra) {

                    // if we've already displayed user don't again
                    if (in_array($ra->user->id,$usersshown)) {
                        continue;
                    }
                    $usersshown[] = $ra->user->id;

                    $fullname = fullname($ra->user, $canviewfullnames);

                    if (isset($aliasnames[$ra->roleid])) {
                        $ra->rolename = $aliasnames[$ra->roleid]->name;
                    }

                    $namesarray[] = format_string($ra->rolename)
                        . ': <a href="'.$CFG->wwwroot.'/user/view.php?id='.$ra->user->id.'&amp;course='.SITEID.'">'
                        . $fullname . '</a>';
                }
            }
        } else {
            $rusers = get_role_users($managerroles, $context,
                                     true, '', 'r.sortorder ASC, u.lastname ASC');
            if (is_array($rusers) && count($rusers)) {
                $canviewfullnames = has_capability('moodle/site:viewfullnames', $context);

                /// Rename some of the role names if needed
                if (isset($context)) {
                    $aliasnames = $DB->get_records('role_names', array('contextid'=>$context->id), '', 'roleid,contextid,name');
                }

                foreach ($rusers as $teacher) {
                    $fullname = fullname($teacher, $canviewfullnames);

                    /// Apply role names
                    if (isset($aliasnames[$teacher->roleid])) {
                        $teacher->rolename = $aliasnames[$teacher->roleid]->name;
                    }

                    $namesarray[] = format_string($teacher->rolename)
                        . ': <a href="'.$CFG->wwwroot.'/user/view.php?id='.$teacher->id.'&amp;course='.SITEID.'">'
                        . $fullname . '</a>';
                }
            }
        }

        if (!empty($namesarray)) {
            echo "<ul class=\"teachers\">\n<li>";
            echo implode('</li><li>', $namesarray);
            echo "</li></ul>";
        }
    }

    require_once("$CFG->dirroot/enrol/enrol.class.php");
    $enrol = enrolment_factory::factory($course->enrol);
    echo $enrol->get_access_icons($course);

    echo '</div><div class="summary">';
    $options = NULL;
    $options->noclean = true;
    $options->para = false;
    if (!isset($course->summaryformat)) {
        $course->summaryformat = FORMAT_MOODLE;
    }
    echo highlight($highlightterms, format_text($course->summary, $course->summaryformat, $options,  $course->id));
    echo '</div>';
    echo '</div>';
}

/**
 * Prints custom user information on the home page.
 * Over time this can include all sorts of information
 */
function print_my_moodle() {
    global $USER, $CFG, $DB, $OUTPUT;

    if (!isloggedin() or isguestuser()) {
        print_error('nopermissions', '', '', 'See My Moodle');
    }

    $courses  = get_my_courses($USER->id, 'visible DESC,sortorder ASC', array('summary'));
    $rhosts   = array();
    $rcourses = array();
    if (!empty($CFG->mnet_dispatcher_mode) && $CFG->mnet_dispatcher_mode==='strict') {
        $rcourses = get_my_remotecourses($USER->id);
        $rhosts   = get_my_remotehosts();
    }

    if (!empty($courses) || !empty($rcourses) || !empty($rhosts)) {

        if (!empty($courses)) {
            echo '<ul class="unlist">';
            foreach ($courses as $course) {
                if ($course->id == SITEID) {
                    continue;
                }
                echo '<li>';
                print_course($course);
                echo "</li>\n";
            }
            echo "</ul>\n";
        }

        // MNET
        if (!empty($rcourses)) {
            // at the IDP, we know of all the remote courses
            foreach ($rcourses as $course) {
                print_remote_course($course, "100%");
            }
        } elseif (!empty($rhosts)) {
            // non-IDP, we know of all the remote servers, but not courses
            foreach ($rhosts as $host) {
                print_remote_host($host, "100%");
            }
        }
        unset($course);
        unset($host);

        if ($DB->count_records("course") > (count($courses) + 1) ) {  // Some courses not being displayed
            echo "<table width=\"100%\"><tr><td align=\"center\">";
            print_course_search("", false, "short");
            echo "</td><td align=\"center\">";
            echo $OUTPUT->single_button("$CFG->wwwroot/course/index.php", get_string("fulllistofcourses"), "get");
            echo "</td></tr></table>\n";
        }

    } else {
        if ($DB->count_records("course_categories") > 1) {
            echo $OUTPUT->box_start("categorybox");
            print_whole_category_list();
            echo $OUTPUT->box_end();
        } else {
            print_courses(0);
        }
    }
}


function print_course_search($value="", $return=false, $format="plain") {
    global $CFG;
    static $count = 0;

    $count++;

    $id = 'coursesearch';

    if ($count > 1) {
        $id .= $count;
    }

    $strsearchcourses= get_string("searchcourses");

    if ($format == 'plain') {
        $output  = '<form id="'.$id.'" action="'.$CFG->wwwroot.'/course/search.php" method="get">';
        $output .= '<fieldset class="coursesearchbox invisiblefieldset">';
        $output .= '<label for="coursesearchbox">'.$strsearchcourses.': </label>';
        $output .= '<input type="text" id="coursesearchbox" size="30" name="search" value="'.s($value).'" />';
        $output .= '<input type="submit" value="'.get_string('go').'" />';
        $output .= '</fieldset></form>';
    } else if ($format == 'short') {
        $output  = '<form id="'.$id.'" action="'.$CFG->wwwroot.'/course/search.php" method="get">';
        $output .= '<fieldset class="coursesearchbox invisiblefieldset">';
        $output .= '<label for="shortsearchbox">'.$strsearchcourses.': </label>';
        $output .= '<input type="text" id="shortsearchbox" size="12" name="search" alt="'.s($strsearchcourses).'" value="'.s($value).'" />';
        $output .= '<input type="submit" value="'.get_string('go').'" />';
        $output .= '</fieldset></form>';
    } else if ($format == 'navbar') {
        $output  = '<form id="coursesearchnavbar" action="'.$CFG->wwwroot.'/course/search.php" method="get">';
        $output .= '<fieldset class="coursesearchbox invisiblefieldset">';
        $output .= '<label for="navsearchbox">'.$strsearchcourses.': </label>';
        $output .= '<input type="text" id="navsearchbox" size="20" name="search" alt="'.s($strsearchcourses).'" value="'.s($value).'" />';
        $output .= '<input type="submit" value="'.get_string('go').'" />';
        $output .= '</fieldset></form>';
    }

    if ($return) {
        return $output;
    }
    echo $output;
}

function print_remote_course($course, $width="100%") {
    global $CFG, $USER;

    $linkcss = '';

    $url = "{$CFG->wwwroot}/auth/mnet/jump.php?hostid={$course->hostid}&amp;wantsurl=/course/view.php?id={$course->remoteid}";

    echo '<div class="coursebox remotecoursebox clearfix">';
    echo '<div class="info">';
    echo '<div class="name"><a title="'.get_string('entercourse').'"'.
         $linkcss.' href="'.$url.'">'
        .  format_string($course->fullname) .'</a><br />'
        . format_string($course->hostname) . ' : '
        . format_string($course->cat_name) . ' : '
        . format_string($course->shortname). '</div>';
    echo '</div><div class="summary">';
    $options = NULL;
    $options->noclean = true;
    $options->para = false;
    echo format_text($course->summary, FORMAT_MOODLE, $options);
    echo '</div>';
    echo '</div>';
}

function print_remote_host($host, $width="100%") {
    global $OUTPUT;

    $linkcss = '';

    echo '<div class="coursebox clearfix">';
    echo '<div class="info">';
    echo '<div class="name">';
    echo '<img src="'.$OUTPUT->pix_url('i/mnethost') . '" class="icon" alt="'.get_string('course').'" />';
    echo '<a title="'.s($host['name']).'" href="'.s($host['url']).'">'
        . s($host['name']).'</a> - ';
    echo $host['count'] . ' ' . get_string('courses');
    echo '</div>';
    echo '</div>';
    echo '</div>';
}


/// MODULE FUNCTIONS /////////////////////////////////////////////////////////////////

function add_course_module($mod) {
    global $DB;

    $mod->added = time();
    unset($mod->id);

    return $DB->insert_record("course_modules", $mod);
}

/**
 * Returns course section - creates new if does not exist yet.
 * @param int $relative section number
 * @param int $courseid
 * @return object $course_section object
 */
function get_course_section($section, $courseid) {
    global $DB;

    if ($cw = $DB->get_record("course_sections", array("section"=>$section, "course"=>$courseid))) {
        return $cw;
    }
    $cw = new object();
    $cw->course   = $courseid;
    $cw->section  = $section;
    $cw->summary  = "";
    $cw->sequence = "";
    $id = $DB->insert_record("course_sections", $cw);
    return $DB->get_record("course_sections", array("id"=>$id));
}
/**
 * Given a full mod object with section and course already defined, adds this module to that section.
 *
 * @param object $mod
 * @param int $beforemod An existing ID which we will insert the new module before
 * @return int The course_sections ID where the mod is inserted
 */
function add_mod_to_section($mod, $beforemod=NULL) {
    global $DB;

    if ($section = $DB->get_record("course_sections", array("course"=>$mod->course, "section"=>$mod->section))) {

        $section->sequence = trim($section->sequence);

        if (empty($section->sequence)) {
            $newsequence = "$mod->coursemodule";

        } else if ($beforemod) {
            $modarray = explode(",", $section->sequence);

            if ($key = array_keys($modarray, $beforemod->id)) {
                $insertarray = array($mod->id, $beforemod->id);
                array_splice($modarray, $key[0], 1, $insertarray);
                $newsequence = implode(",", $modarray);

            } else {  // Just tack it on the end anyway
                $newsequence = "$section->sequence,$mod->coursemodule";
            }

        } else {
            $newsequence = "$section->sequence,$mod->coursemodule";
        }

        if ($DB->set_field("course_sections", "sequence", $newsequence, array("id"=>$section->id))) {
            return $section->id;     // Return course_sections ID that was used.
        } else {
            return 0;
        }

    } else {  // Insert a new record
        $section->course   = $mod->course;
        $section->section  = $mod->section;
        $section->summary  = "";
        $section->sequence = $mod->coursemodule;
        return $DB->insert_record("course_sections", $section);
    }
}

function set_coursemodule_groupmode($id, $groupmode) {
    global $DB;
    return $DB->set_field("course_modules", "groupmode", $groupmode, array("id"=>$id));
}

function set_coursemodule_idnumber($id, $idnumber) {
    global $DB;
    return $DB->set_field("course_modules", "idnumber", $idnumber, array("id"=>$id));
}

/**
* $prevstateoverrides = true will set the visibility of the course module
* to what is defined in visibleold. This enables us to remember the current
* visibility when making a whole section hidden, so that when we toggle
* that section back to visible, we are able to return the visibility of
* the course module back to what it was originally.
*/
function set_coursemodule_visible($id, $visible, $prevstateoverrides=false) {
    global $DB;
    if (!$cm = $DB->get_record('course_modules', array('id'=>$id))) {
        return false;
    }
    if (!$modulename = $DB->get_field('modules', 'name', array('id'=>$cm->module))) {
        return false;
    }
    if ($events = $DB->get_records('event', array('instance'=>$cm->instance, 'modulename'=>$modulename))) {
        foreach($events as $event) {
            if ($visible) {
                show_event($event);
            } else {
                hide_event($event);
            }
        }
    }
    if ($prevstateoverrides) {
        if ($visible == '0') {
            // Remember the current visible state so we can toggle this back.
            $DB->set_field('course_modules', 'visibleold', $cm->visible, array('id'=>$id));
        } else {
            // Get the previous saved visible states.
            return $DB->set_field('course_modules', 'visible', $cm->visibleold, array('id'=>$id));
        }
    }
    return $DB->set_field("course_modules", "visible", $visible, array("id"=>$id));
}

/**
 * Delete a course module and any associated data at the course level (events)
 * Until 1.5 this function simply marked a deleted flag ... now it
 * deletes it completely.
 *
 */
function delete_course_module($id) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->dirroot.'/blog/lib.php');

    if (!$cm = $DB->get_record('course_modules', array('id'=>$id))) {
        return true;
    }
    $modulename = $DB->get_field('modules', 'name', array('id'=>$cm->module));
    //delete events from calendar
    if ($events = $DB->get_records('event', array('instance'=>$cm->instance, 'modulename'=>$modulename))) {
        foreach($events as $event) {
            delete_event($event->id);
        }
    }
    //delete grade items, outcome items and grades attached to modules
    if ($grade_items = grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$modulename,
                                                   'iteminstance'=>$cm->instance, 'courseid'=>$cm->course))) {
        foreach ($grade_items as $grade_item) {
            $grade_item->delete('moddelete');
        }
    }

    delete_context(CONTEXT_MODULE, $cm->id);
    return $DB->delete_records('course_modules', array('id'=>$cm->id));
}

function delete_mod_from_section($mod, $section) {
    global $DB;

    if ($section = $DB->get_record("course_sections", array("id"=>$section)) ) {

        $modarray = explode(",", $section->sequence);

        if ($key = array_keys ($modarray, $mod)) {
            array_splice($modarray, $key[0], 1);
            $newsequence = implode(",", $modarray);
            return $DB->set_field("course_sections", "sequence", $newsequence, array("id"=>$section->id));
        } else {
            return false;
        }

    }
    return false;
}

/**
 * Moves a section up or down by 1. CANNOT BE USED DIRECTLY BY AJAX!
 *
 * @param object $course
 * @param int $section
 * @param int $move (-1 or 1)
 */
function move_section($course, $section, $move) {
/// Moves a whole course section up and down within the course
    global $USER, $DB;

    if (!$move) {
        return true;
    }

    $sectiondest = $section + $move;

    if ($sectiondest > $course->numsections or $sectiondest < 1) {
        return false;
    }

    if (!$sectionrecord = $DB->get_record("course_sections", array("course"=>$course->id, "section"=>$section))) {
        return false;
    }

    if (!$sectiondestrecord = $DB->get_record("course_sections", array("course"=>$course->id, "section"=>$sectiondest))) {
        return false;
    }

    if (!$DB->set_field("course_sections", "section", $sectiondest, array("id"=>$sectionrecord->id))) {
        return false;
    }
    if (!$DB->set_field("course_sections", "section", $section, array("id"=>$sectiondestrecord->id))) {
        return false;
    }
    // if the focus is on the section that is being moved, then move the focus along
    if (isset($USER->display[$course->id]) and ($USER->display[$course->id] == $section)) {
        course_set_display($course->id, $sectiondest);
    }

    // Check for duplicates and fix order if needed.
    // There is a very rare case that some sections in the same course have the same section id.
    $sections = $DB->get_records('course_sections', array('course'=>$course->id), 'section ASC');
    $n = 0;
    foreach ($sections as $section) {
        if ($section->section != $n) {
            if (!$DB->set_field('course_sections', 'section', $n, array('id'=>$section->id))) {
                return false;
            }
        }
        $n++;
    }
    return true;
}

/**
 * Moves a section within a course, from a position to another.
 * Be very careful: $section and $destination refer to section number,
 * not id!.
 *
 * @param object $course
 * @param int $section Section number (not id!!!)
 * @param int $destination
 * @return boolean Result
 */
function move_section_to($course, $section, $destination) {
/// Moves a whole course section up and down within the course
    global $USER, $DB;

    if (!$destination && $destination != 0) {
        return true;
    }

    if ($destination > $course->numsections) {
        return false;
    }

    // Get all sections for this course and re-order them (2 of them should now share the same section number)
    if (!$sections = $DB->get_records_menu('course_sections', array('course' => $course->id),
            'section ASC, id ASC', 'id, section')) {
        return false;
    }

    $sections = reorder_sections($sections, $section, $destination);

    // Update all sections
    foreach ($sections as $id => $position) {
        $DB->set_field('course_sections', 'section', $position, array('id' => $id));
    }

    // if the focus is on the section that is being moved, then move the focus along
    if (isset($USER->display[$course->id]) and ($USER->display[$course->id] == $section)) {
        course_set_display($course->id, $destination);
    }
    return true;
}

/**
 * Reordering algorithm for course sections. Given an array of section->section indexed by section->id,
 * an original position number and a target position number, rebuilds the array so that the
 * move is made without any duplication of section positions.
 * Note: The target_position is the position AFTER WHICH the moved section will be inserted. If you want to
 * insert a section before the first one, you must give 0 as the target (section 0 can never be moved).
 *
 * @param array $sections
 * @param int $origin_position
 * @param int $target_position
 * @return array
 */
function reorder_sections($sections, $origin_position, $target_position) {
    if (!is_array($sections)) {
        return false;
    }

    // We can't move section position 0
    if ($origin_position < 1) {
        echo "We can't move section position 0";
        return false;
    }

    // Locate origin section in sections array
    if (!$origin_key = array_search($origin_position, $sections)) {
        echo "searched position not in sections array";
        return false; // searched position not in sections array
    }

    // Extract origin section
    $origin_section = $sections[$origin_key];
    unset($sections[$origin_key]);

    // Find offset of target position (stupid PHP's array_splice requires offset instead of key index!)
    $found = false;
    $append_array = array();
    foreach ($sections as $id => $position) {
        if ($found) {
            $append_array[$id] = $position;
            unset($sections[$id]);
        }
        if ($position == $target_position) {
            $found = true;
        }
    }

    // Append moved section
    $sections[$origin_key] = $origin_section;

    // Append rest of array (if applicable)
    if (!empty($append_array)) {
        foreach ($append_array as $id => $position) {
            $sections[$id] = $position;
        }
    }

    // Renumber positions
    $position = 0;
    foreach ($sections as $id => $p) {
        $sections[$id] = $position;
        $position++;
    }

    return $sections;

}

/**
 * Move the module object $mod to the specified $section
 * If $beforemod exists then that is the module
 * before which $modid should be inserted
 * All parameters are objects
 */
function moveto_module($mod, $section, $beforemod=NULL) {
    global $DB, $OUTPUT;

/// Remove original module from original section
    if (! delete_mod_from_section($mod->id, $mod->section)) {
        echo $OUTPUT->notification("Could not delete module from existing section");
    }

/// Update module itself if necessary

    if ($mod->section != $section->id) {
        $mod->section = $section->id;
        $DB->update_record("course_modules", $mod);
        // if moving to a hidden section then hide module
        if (!$section->visible) {
            set_coursemodule_visible($mod->id, 0);
        }
    }

/// Add the module into the new section

    $mod->course       = $section->course;
    $mod->section      = $section->section;  // need relative reference
    $mod->coursemodule = $mod->id;

    if (! add_mod_to_section($mod, $beforemod)) {
        return false;
    }

    return true;
}

function make_editing_buttons($mod, $absolute=false, $moveselect=true, $indent=-1, $section=-1) {
    global $CFG, $USER, $DB, $OUTPUT;

    static $str;
    static $sesskey;

    $modcontext = get_context_instance(CONTEXT_MODULE, $mod->id);
    // no permission to edit
    if (!has_capability('moodle/course:manageactivities', $modcontext)) {
        return false;
    }

    if (!isset($str)) {
        $str->assign         = get_string("assignroles", 'role');
        $str->delete         = get_string("delete");
        $str->move           = get_string("move");
        $str->moveup         = get_string("moveup");
        $str->movedown       = get_string("movedown");
        $str->moveright      = get_string("moveright");
        $str->moveleft       = get_string("moveleft");
        $str->update         = get_string("update");
        $str->duplicate      = get_string("duplicate");
        $str->hide           = get_string("hide");
        $str->show           = get_string("show");
        $str->clicktochange  = get_string("clicktochange");
        $str->forcedmode     = get_string("forcedmode");
        $str->groupsnone     = get_string("groupsnone");
        $str->groupsseparate = get_string("groupsseparate");
        $str->groupsvisible  = get_string("groupsvisible");
        $sesskey = sesskey();
    }

    if ($section >= 0) {
        $section = '&amp;sr='.$section;   // Section return
    } else {
        $section = '';
    }

    if ($absolute) {
        $path = $CFG->wwwroot.'/course';
    } else {
        $path = '.';
    }
    if (has_capability('moodle/course:activityvisibility', $modcontext)) {
        if ($mod->visible) {
            $hideshow = '<a class="editing_hide" title="'.$str->hide.'" href="'.$path.'/mod.php?hide='.$mod->id.
                        '&amp;sesskey='.$sesskey.$section.'"><img'.
                        ' src="'.$OUTPUT->pix_url('t/hide') . '" class="iconsmall" '.
                        ' alt="'.$str->hide.'" /></a>'."\n";
        } else {
            $hideshow = '<a class="editing_show" title="'.$str->show.'" href="'.$path.'/mod.php?show='.$mod->id.
                        '&amp;sesskey='.$sesskey.$section.'"><img'.
                        ' src="'.$OUTPUT->pix_url('t/show') . '" class="iconsmall" '.
                        ' alt="'.$str->show.'" /></a>'."\n";
        }
    } else {
        $hideshow = '';
    }

    if ($mod->groupmode !== false) {
        if ($mod->groupmode == SEPARATEGROUPS) {
            $grouptitle = $str->groupsseparate;
            $groupclass = 'editing_groupsseparate';
            $groupimage = $OUTPUT->pix_url('t/groups') . '';
            $grouplink  = $path.'/mod.php?id='.$mod->id.'&amp;groupmode=0&amp;sesskey='.$sesskey;
        } else if ($mod->groupmode == VISIBLEGROUPS) {
            $grouptitle = $str->groupsvisible;
            $groupclass = 'editing_groupsvisible';
            $groupimage = $OUTPUT->pix_url('t/groupv') . '';
            $grouplink  = $path.'/mod.php?id='.$mod->id.'&amp;groupmode=1&amp;sesskey='.$sesskey;
        } else {
            $grouptitle = $str->groupsnone;
            $groupclass = 'editing_groupsnone';
            $groupimage = $OUTPUT->pix_url('t/groupn') . '';
            $grouplink  = $path.'/mod.php?id='.$mod->id.'&amp;groupmode=2&amp;sesskey='.$sesskey;
        }
        if ($mod->groupmodelink) {
            $groupmode = '<a class="'.$groupclass.'" title="'.$grouptitle.' ('.$str->clicktochange.')" href="'.$grouplink.'">'.
                         '<img src="'.$groupimage.'" class="iconsmall" '.
                         'alt="'.$grouptitle.'" /></a>';
        } else {
            $groupmode = '<img title="'.$grouptitle.' ('.$str->forcedmode.')" '.
                         ' src="'.$groupimage.'" class="iconsmall" '.
                         'alt="'.$grouptitle.'" />';
        }
    } else {
        $groupmode = "";
    }

    if (has_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $mod->course))) {
        if ($moveselect) {
            $move =     '<a class="editing_move" title="'.$str->move.'" href="'.$path.'/mod.php?copy='.$mod->id.
                        '&amp;sesskey='.$sesskey.$section.'"><img'.
                        ' src="'.$OUTPUT->pix_url('t/move') . '" class="iconsmall" '.
                        ' alt="'.$str->move.'" /></a>'."\n";
        } else {
            $move =     '<a class="editing_moveup" title="'.$str->moveup.'" href="'.$path.'/mod.php?id='.$mod->id.
                        '&amp;move=-1&amp;sesskey='.$sesskey.$section.'"><img'.
                        ' src="'.$OUTPUT->pix_url('t/up') . '" class="iconsmall" '.
                        ' alt="'.$str->moveup.'" /></a>'."\n".
                        '<a class="editing_movedown" title="'.$str->movedown.'" href="'.$path.'/mod.php?id='.$mod->id.
                        '&amp;move=1&amp;sesskey='.$sesskey.$section.'"><img'.
                        ' src="'.$OUTPUT->pix_url('t/down') . '" class="iconsmall" '.
                        ' alt="'.$str->movedown.'" /></a>'."\n";
        }
    } else {
        $move = '';
    }

    $leftright = '';
    if (has_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $mod->course))) {

        if (right_to_left()) {   // Exchange arrows on RTL
            $rightarrow = 't/left';
            $leftarrow  = 't/right';
        } else {
            $rightarrow = 't/right';
            $leftarrow  = 't/left';
        }

        if ($indent > 0) {
            $leftright .= '<a class="editing_moveleft" title="'.$str->moveleft.'" href="'.$path.'/mod.php?id='.$mod->id.
                        '&amp;indent=-1&amp;sesskey='.$sesskey.$section.'"><img'.
                        ' src="'.$OUTPUT->pix_url($leftarrow).'" class="iconsmall" '.
                        ' alt="'.$str->moveleft.'" /></a>'."\n";
        }
        if ($indent >= 0) {
            $leftright .= '<a class="editing_moveright" title="'.$str->moveright.'" href="'.$path.'/mod.php?id='.$mod->id.
                        '&amp;indent=1&amp;sesskey='.$sesskey.$section.'"><img'.
                        ' src="'.$OUTPUT->pix_url($rightarrow).'" class="iconsmall" '.
                        ' alt="'.$str->moveright.'" /></a>'."\n";
        }
    }
    if (has_capability('moodle/course:managegroups', $modcontext)){
        $context = get_context_instance(CONTEXT_MODULE, $mod->id);
        $assign = '<a class="editing_assign" title="'.$str->assign.'" href="'.$CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.
            $context->id.'"><img src="'.$OUTPUT->pix_url('i/roles') . '" alt="'.$str->assign.'" class="iconsmall"/></a>';
    } else {
        $assign = '';
    }

    return '<span class="commands">'."\n".$leftright.$move.
           '<a class="editing_update" title="'.$str->update.'" href="'.$path.'/mod.php?update='.$mod->id.
           '&amp;sesskey='.$sesskey.$section.'"><img'.
           ' src="'.$OUTPUT->pix_url('t/edit') . '" class="iconsmall" '.
           ' alt="'.$str->update.'" /></a>'."\n".
           '<a class="editing_delete" title="'.$str->delete.'" href="'.$path.'/mod.php?delete='.$mod->id.
           '&amp;sesskey='.$sesskey.$section.'"><img'.
           ' src="'.$OUTPUT->pix_url('t/delete') . '" class="iconsmall" '.
           ' alt="'.$str->delete.'" /></a>'."\n".$hideshow.$groupmode."\n".$assign.'</span>';
}

/**
 * given a course object with shortname & fullname, this function will
 * truncate the the number of chars allowed and add ... if it was too long
 */
function course_format_name ($course,$max=100) {

    $str = $course->shortname.': '. $course->fullname;
    if (strlen($str) <= $max) {
        return $str;
    }
    else {
        return substr($str,0,$max-3).'...';
    }
}

/**
 * This function will return true if the given course is a child course at all
 */
function course_in_meta ($course) {
    global $DB;
    return $DB->record_exists("course_meta", array("child_course"=>$course->id));
}


function update_restricted_mods($course, $mods) {
    global $DB;

/// Delete all the current restricted list
    $DB->delete_records('course_allowed_modules', array('course'=>$course->id));

    if (empty($course->restrictmodules)) {
        return;   // We're done
    }

/// Insert the new list of restricted mods
    foreach ($mods as $mod) {
        if ($mod == 0) {
            continue; // this is the 'allow none' option
        }
        $am = new object();
        $am->course = $course->id;
        $am->module = $mod;
        $DB->insert_record('course_allowed_modules',$am);
    }
}

/**
 * This function will take an int (module id) or a string (module name)
 * and return true or false, whether it's allowed in the given course (object)
 * $mod is not allowed to be an object, as the field for the module id is inconsistent
 * depending on where in the code it's called from (sometimes $mod->id, sometimes $mod->module)
 */

function course_allowed_module($course,$mod) {
    global $DB;

    if (empty($course->restrictmodules)) {
        return true;
    }

    // Admins and admin-like people who can edit everything can also add anything.
    // This is a bit wierd, really.  I debated taking it out but it's enshrined in help for the setting.
    if (has_capability('moodle/course:update', get_context_instance(CONTEXT_SYSTEM))) {
        return true;
    }

    if (is_numeric($mod)) {
        $modid = $mod;
    } else if (is_string($mod)) {
        $modid = $DB->get_field('modules', 'id', array('name'=>$mod));
    }
    if (empty($modid)) {
        return false;
    }

    return $DB->record_exists('course_allowed_modules', array('course'=>$course->id, 'module'=>$modid));
}

/**
 * Recursively delete category including all subcategories and courses.
 * @param object $category
 * @param boolean $showfeedback display some notices
 * @return array return deleted courses
 */
function category_delete_full($category, $showfeedback=true) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->libdir.'/questionlib.php');

    if ($children = $DB->get_records('course_categories', array('parent'=>$category->id), 'sortorder ASC')) {
        foreach ($children as $childcat) {
            category_delete_full($childcat, $showfeedback);
        }
    }

    $deletedcourses = array();
    if ($courses = $DB->get_records('course', array('category'=>$category->id), 'sortorder ASC')) {
        foreach ($courses as $course) {
            if (!delete_course($course, false)) {
                throw new moodle_exception('cannotdeletecategorycourse','','',$course->shortname);
            }
            $deletedcourses[] = $course;
        }
    }

    // now delete anything that may depend on course category context
    grade_course_category_delete($category->id, 0, $showfeedback);
    if (!question_delete_course_category($category, 0, $showfeedback)) {
        throw new moodle_exception('cannotdeletecategoryquestions','','',$category->name);
    }

    // finally delete the category and it's context
    $DB->delete_records('course_categories', array('id'=>$category->id));
    delete_context(CONTEXT_COURSECAT, $category->id);

    events_trigger('course_category_deleted', $category);

    return $deletedcourses;
}

/**
 * Delete category, but move contents to another category.
 * @param object $ccategory
 * @param int $newparentid category id
 * @return bool status
 */
function category_delete_move($category, $newparentid, $showfeedback=true) {
    global $CFG, $DB, $OUTPUT;
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->libdir.'/questionlib.php');

    if (!$newparentcat = $DB->get_record('course_categories', array('id'=>$newparentid))) {
        return false;
    }

    if ($children = $DB->get_records('course_categories', array('parent'=>$category->id), 'sortorder ASC')) {
        foreach ($children as $childcat) {
            move_category($childcat, $newparentcat);
        }
    }

    if ($courses = $DB->get_records('course', array('category'=>$category->id), 'sortorder ASC', 'id')) {
        if (!move_courses(array_keys($courses), $newparentid)) {
            echo $OUTPUT->notification("Error moving courses");
            return false;
        }
        echo $OUTPUT->notification(get_string('coursesmovedout', '', format_string($category->name)), 'notifysuccess');
    }

    // now delete anything that may depend on course category context
    grade_course_category_delete($category->id, $newparentid, $showfeedback);
    if (!question_delete_course_category($category, $newparentcat, $showfeedback)) {
        echo $OUTPUT->notification(get_string('errordeletingquestionsfromcategory', 'question', $category), 'notifysuccess');
        return false;
    }

    // finally delete the category and it's context
    $DB->delete_records('course_categories', array('id'=>$category->id));
    delete_context(CONTEXT_COURSECAT, $category->id);

    events_trigger('course_category_deleted', $category);

    echo $OUTPUT->notification(get_string('coursecategorydeleted', '', format_string($category->name)), 'notifysuccess');

    return true;
}

/**
 * Efficiently moves many courses around while maintaining
 * sortorder in order.
 *
 * @param $courseids is an array of course ids
 */
function move_courses($courseids, $categoryid) {
    global $CFG, $DB, $OUTPUT;

    if (!empty($courseids) and $category = $DB->get_record('course_categories', array('id'=>$categoryid))) {
        $courseids = array_reverse($courseids);
        $i = 1;

        foreach ($courseids as $courseid) {
            if (!$course  = $DB->get_record("course", array("id"=>$courseid))) {
                echo $OUTPUT->notification("Error finding course $courseid");
            } else {
                $course->category  = $categoryid;
                $course->sortorder = $category->sortorder + MAX_COURSES_IN_CATEGORY - $i++;

                $DB->update_record('course', $course);

                $context   = get_context_instance(CONTEXT_COURSE, $course->id);
                $newparent = get_context_instance(CONTEXT_COURSECAT, $course->category);
                context_moved($context, $newparent);
            }
        }
        fix_course_sortorder();
    }
    return true;
}

/**
 * Efficiently moves a category - NOTE that this can have
 * a huge impact access-control-wise...
 */
function move_category($category, $newparentcat) {
    global $CFG, $DB;

    $context = get_context_instance(CONTEXT_COURSECAT, $category->id);

    if (empty($newparentcat->id)) {
        $DB->set_field('course_categories', 'parent', 0, array('id'=>$category->id));

        $newparent = get_context_instance(CONTEXT_SYSTEM);

    } else {
        $DB->set_field('course_categories', 'parent', $newparentcat->id, array('id'=>$category->id));
        $newparent = get_context_instance(CONTEXT_COURSECAT, $newparentcat->id);
    }

    context_moved($context, $newparent);

    // now make it last in new category
    $DB->set_field('course_categories', 'sortorder', MAX_COURSES_IN_CATEGORY*MAX_COURSE_CATEGORIES, array('id'=>$category->id));

    // and fix the sortorders
    fix_course_sortorder();
}

/**
 * @param string $format Course format ID e.g. 'weeks'
 * @return Name that the course format prefers for sections
 */
function get_section_name($format) {
    $sectionname = get_string("name$format","format_$format");
    if($sectionname == "[[name$format]]") {
        $sectionname = get_string("name$format");
    }
    return $sectionname;
}

/**
 * Can the current user delete this course?
 * Course creators have exception,
 * 1 day after the creation they can sill delete the course.
 * @param int $courseid
 * @return boolean
 */
function can_delete_course($courseid) {
    global $USER, $DB;

    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    if (has_capability('moodle/course:delete', $context)) {
        return true;
    }

    // hack: now try to find out if creator created this course recently (1 day)
    if (!has_capability('moodle/course:create', $context)) {
        return false;
    }

    $since = time() - 60*60*24;

    $params = array('userid'=>$USER->id, 'url'=>"view.php?id=$courseid", 'since'=>$since);
    $select = "module = 'course' AND action = 'new' AND userid = :userid AND url = :url AND time > :since";

    return $DB->record_exists_select('log', $select, $params);
}

/**
 * Save the Your name for 'Some role' strings.
 *
 * @param integer $courseid the id of this course.
 * @param array $data the data that came from the course settings form.
 */
function save_local_role_names($courseid, $data) {
    global $DB;
    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    foreach ($data as $fieldname => $value) {
        if (!strstr($fieldname, 'role_')) {
            continue;
        }
        list($ignored, $roleid) = explode('_', $fieldname);

        // make up our mind whether we want to delete, update or insert
        if (!$value) {
            $DB->delete_records('role_names', array('contextid' => $context->id, 'roleid' => $roleid));

        } else if ($rolename = $DB->get_record('role_names', array('contextid' => $context->id, 'roleid' => $roleid))) {
            $rolename->name = $value;
            $DB->update_record('role_names', $rolename);

        } else {
            $rolename = new stdClass;
            $rolename->contextid = $context->id;
            $rolename->roleid = $roleid;
            $rolename->name = $value;
            $DB->insert_record('role_names', $rolename);
        }
    }
}

/**
 * Create a course and either return a $course object or false
 *
 * @param object $data  - all the data needed for an entry in the 'course' table
 */
function create_course($data) {
    global $CFG, $USER, $DB;

    //check the categoryid
    if (!empty($data->category) && !$data->category==0) {
        $category = $DB->get_record('course_categories', array('id'=>$data->category));
        if (empty($category)) {
            throw new moodle_exception('noexistingcategory');
        }
    }

    //check if the shortname already exist
    if(!empty($data->shortname)) {
        $course = $DB->get_record('course', array('shortname' => $data->shortname));
        if (!empty($course)) {
            throw new moodle_exception('shortnametaken');
        }
    }

    //check if the id number already exist
    if(!empty($data->idnumber)) {
        $course = $DB->get_record('course', array('idnumber' => $data->idnumber));
        if (!empty($course)) {
            throw new moodle_exception('idnumbertaken');
        }
    }


    // preprocess allowed mods
    $allowedmods = empty($data->allowedmods) ? array() : $data->allowedmods;
    unset($data->allowedmods);
    if ($CFG->restrictmodulesfor == 'all') {
        $data->restrictmodules = 1;

        // if the user is not an admin, get the default allowed modules because
        // there are no modules passed by the form
        if(!has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
            if(!$allowedmods && $CFG->defaultallowedmodules) {
                $allowedmods = explode(',', $CFG->defaultallowedmodules);
            }
        }
    } else {
        $data->restrictmodules = 0;
    }

    $data->timecreated = time();

    // place at beginning of any category
    $data->sortorder = 0;

    if ($newcourseid = $DB->insert_record('course', $data)) {  // Set up new course

        $course = $DB->get_record('course', array('id'=>$newcourseid));

        // Setup the blocks
        blocks_add_default_course_blocks($course);

        update_restricted_mods($course, $allowedmods);

        $section = new object();
        $section->course  = $course->id;   // Create a default section.
        $section->section = 0;
        $section->id = $DB->insert_record('course_sections', $section);

        fix_course_sortorder();

        add_to_log(SITEID, 'course', 'new', 'view.php?id='.$course->id, $data->fullname.' (ID '.$course->id.')');

        // Save any custom role names.
        save_local_role_names($course->id, $data);

        // Trigger events
        events_trigger('course_created', $course);

        return $course;
    }

    return false;   // error
}

/**
 * Update a course and return true or false
 *
 * @param object $data  - all the data needed for an entry in the 'course' table
 */
function update_course($data) {
    global $USER, $CFG, $DB;

    // Preprocess allowed mods
    $allowedmods = empty($data->allowedmods) ? array() : $data->allowedmods;
    unset($data->allowedmods);

    // Normal teachers can't change setting
    if (!has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
        unset($data->restrictmodules);
    }

    $movecat = false;
    $oldcourse = $DB->get_record('course', array('id'=>$data->id)); // should not fail, already tested above
    // check that course id exist
    if (empty($oldcourse)) {
       throw new moodle_exception('courseidnotfound');
    }

    if (!has_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $oldcourse->category))
      or !has_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $data->category))) {
        // can not move to new category, keep the old one
        unset($data->category);

    } elseif ($oldcourse->category != $data->category) {
        $movecat = true;
    }

    // Update with the new data
    if ($DB->update_record('course', $data)) {

        $course = $DB->get_record('course', array('id'=>$data->id));

        add_to_log($course->id, "course", "update", "edit.php?id=$course->id", $course->id);

        // "Admins" can change allowed mods for a course
        if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
            update_restricted_mods($course, $allowedmods);
        }

        if ($movecat) {
            $context   = get_context_instance(CONTEXT_COURSE, $course->id);
            $newparent = get_context_instance(CONTEXT_COURSECAT, $course->category);
            context_moved($context, $newparent);
        }

        fix_course_sortorder();

        // Test for and remove blocks which aren't appropriate anymore
        blocks_remove_inappropriate($course);

        // Save any custom role names.
        save_local_role_names($course->id, $data);

        // Trigger events
        events_trigger('course_updated', $course);

        return true;

    }

    return false;
}

function get_course_by_id ($id) {
    global $DB;
    return $DB->get_record('course', array('id' => $id));
}

function get_course_by_shortname ($shortname) {
    global $DB;
    return $DB->get_record('course', array('shortname' => $shortname));
}

function get_course_by_idnumber ($idnumber) {
    global $DB;
    return $DB->get_record('course', array('idnumber' => $idnumber));
}

/**
 * This class pertains to course requests and contains methods associated with
 * create, approving, and removing course requests.
 *
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 *
 * @property-read int $id
 * @property-read string $fullname
 * @property-read string $shortname
 * @property-read string $summary
 * @property-read int $summaryformat
 * @property-read int $summarytrust
 * @property-read string $reason
 * @property-read int $requester
 * @property-read string $password
 */
class course_request {

    /**
     * This is the stdClass that stores the properties for the course request
     * and is externally acccessed through the __get magic method
     * @var stdClass
     */
    protected $properties;

    /**
     * An array of options for the summary editor used by course request forms.
     * This is initially set by {@link summary_editor_options()}
     * @var array
     * @static
     */
    protected static $summaryeditoroptions;

    /**
     * The context used when working with files for the summary editor
     * This is initially set by {@link summary_editor_context()}
     * @var stdClass
     * @static
     */
    protected static $summaryeditorcontext;

    /**
     * The string used to identify the file area for course_requests
     * This is initially set by {@link summary_editor_context()}
     * @var string
     * @static
     */
    protected static $summaryeditorfilearea = 'course_request_summary';

    /**
     * Static function to prepare the summary editor for working with a course
     * request.
     *
     * @static
     * @param null|stdClass $data Optional, an object containing the default values
     *                       for the form, these may be modified when preparing the
     *                       editor so this should be called before creating the form
     * @return stdClass An object that can be used to set the default values for
     *                   an mforms form
     */
    public static function prepare($data=null) {
        if ($data === null) {
            $data = new stdClass;
        }
        $data = file_prepare_standard_editor($data, 'summary', self::summary_editor_options(), self::summary_editor_context(), self::summary_editor_filearea(), null);
        return $data;
    }

    /**
     * Static function to create a new course request when passed an array of properties
     * for it.
     *
     * This function also handles saving any files that may have been used in the editor
     *
     * @static
     * @param stdClass $data
     * @return course_request The newly created course request
     */
    public static function create($data) {
        global $USER, $DB, $CFG;
        $data->requester = $USER->id;
        $editorused = (!empty($data->summary_editor));
        // Has summary_editor been set. If so we have come through with a editor and
        // may need to save files
        if ($editorused && empty($data->summary)) {
            // Summary is a required field so copy the text over
            $data->summary = $data->summary_editor['text'];
        }
        $data->id = $DB->insert_record('course_request', $data);
        if ($editorused) {
            // Save any files and then update the course with the fixed data
            $data = file_postupdate_standard_editor($data, 'summary', self::summary_editor_options(), self::summary_editor_context(), self::summary_editor_filearea(), $data->id);
            $DB->update_record('course_request', $data);
        }
        // Create a new course_request object and return it
        $request = new course_request($data);

        // Notify the admin if required.
        if ($CFG->courserequestnotify) {
            $users = get_users_from_config($CFG->courserequestnotify, 'moodle/site:approvecourse');

            $a = new stdClass;
            $a->link = "$CFG->wwwroot/course/pending.php";
            $a->user = fullname($USER);
            $subject = get_string('courserequest');
            $message = get_string('courserequestnotifyemail', 'admin', $a);
            foreach ($users as $user) {
                $this->notify($user, $USER, 'courserequested', $subject, $message);
            }
        }

        return $request;
    }

    /**
     * Returns an array of options to use with a summary editor
     *
     * @uses course_request::$summaryeditoroptions
     * @return array An array of options to use with the editor
     */
    public static function summary_editor_options() {
        global $CFG;
        if (self::$summaryeditoroptions === null) {
            self::$summaryeditoroptions = array('maxfiles' => 0, 'maxbytes'=>0, 'trusttext'=>true);
        }
        return self::$summaryeditoroptions;
    }

    /**
     * Returns the context to use with the summary editor
     *
     * @uses course_request::$summaryeditorcontext
     * @return stdClass The context to use
     */
    public static function summary_editor_context() {
        return null;
    }

    /**
     * Returns the filearea to use with the summary editor
     *
     * @uses course_request::$summaryeditorfilearea
     * @return string The filearea to use with the summary editor
     */
    public static function summary_editor_filearea() {
        return self::$summaryeditorfilearea;
    }

    /**
     * Loads the properties for this course request object. Id is required and if
     * only id is provided then we load the rest of the properties from the database
     *
     * @param stdClass|int $properties Either an object containing properties
     *                      or the course_request id to load
     */
    public function __construct($properties) {
        global $DB;
        if (empty($properties->id)) {
            if (empty($properties)) {
                throw new coding_exception('You must provide a course request id when creating a course_request object');
            }
            $id = $properties;
            $properties = new stdClass;
            $properties->id = (int)$id;
            unset($id);
        }
        if (empty($properties->requester)) {
            if (!($this->properties = $DB->get_record('course_request', array('id' => $properties->id)))) {
                print_error('unknowncourserequest');
            }
        } else {
            $this->properties = $properties;
        }
        $this->properties->collision = null;
    }

    /**
     * Returns the requested property
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        if ($key === 'summary' && self::summary_editor_context() !== null) {
            return file_rewrite_pluginfile_urls($this->properties->summary, 'pluginfile.php', self::summary_editor_context()->id, self::summary_editor_filearea(), $this->properties->id);
        }
        return $this->properties->$key;
    }

    /**
     * Override this to ensure empty($request->blah) calls return a reliable answer...
     *
     * This is required because we define the __get method
     *
     * @param mixed $key
     * @return bool True is it not empty, false otherwise
     */
    public function __isset($key) {
        return (!empty($this->properties->$key));
    }

    /**
     * Returns the user who requested this course
     *
     * Uses a static var to cache the results and cut down the number of db queries
     *
     * @staticvar array $requesters An array of cached users
     * @return stdClass The user who requested the course
     */
    public function get_requester() {
        global $DB;
        static $requesters= array();
        if (!array_key_exists($this->properties->requester, $requesters)) {
            $requesters[$this->properties->requester] = $DB->get_record('user', array('id'=>$this->properties->requester));
        }
        return $requesters[$this->properties->requester];
    }

    /**
     * Checks that the shortname used by the course does not conflict with any other
     * courses that exist
     *
     * @param string|null $shortnamemark The string to append to the requests shortname
     *                     should a conflict be found
     * @return bool true is there is a conflict, false otherwise
     */
    public function check_shortname_collision($shortnamemark = '[*]') {
        global $DB;

        if ($this->properties->collision !== null) {
            return $this->properties->collision;
        }

        if (empty($this->properties->shortname)) {
            debugging('Attempting to check a course request shortname before it has been set', DEBUG_DEVELOPER);
            $this->properties->collision = false;
        } else if ($DB->record_exists('course', array('shortname' => $this->properties->shortname))) {
            if (!empty($shortnamemark)) {
                $this->properties->shortname .= ' '.$shortnamemark;
            }
            $this->properties->collision = true;
        } else {
            $this->properties->collision = false;
        }
        return $this->properties->collision;
    }

    /**
     * This function approves the request turning it into a course
     *
     * This function converts the course request into a course, at the same time
     * transfering any files used in the summary to the new course and then removing
     * the course request and the files associated with it.
     *
     * @return int The id of the course that was created from this request
     */
    public function approve() {
        global $CFG, $DB, $USER;
        $category = get_course_category($CFG->defaultrequestcategory);
        $courseconfig = get_config('moodlecourse');

        // Transfer appropriate settings
        $course = clone($this->properties);
        unset($course->id);
        unset($course->reason);
        unset($course->requester);

        // Set category
        $course->category = $category->id;
        $course->sortorder = $category->sortorder; // place as the first in category

        // Set misc settings
        $course->requested = 1;
        if (!empty($CFG->restrictmodulesfor) && $CFG->restrictmodulesfor != 'none' && !empty($CFG->restrictbydefault)) {
            $course->restrictmodules = 1;
        }

        // Apply course default settings
        $course->format             = $courseconfig->format;
        $course->numsections        = $courseconfig->numsections;
        $course->hiddensections     = $courseconfig->hiddensections;
        $course->newsitems          = $courseconfig->newsitems;
        $course->showgrades         = $courseconfig->showgrades;
        $course->showreports        = $courseconfig->showreports;
        $course->maxbytes           = $courseconfig->maxbytes;
        $course->enrol              = $courseconfig->enrol;
        $course->enrollable         = $courseconfig->enrollable;
        $course->enrolperiod        = $courseconfig->enrolperiod;
        $course->expirynotify       = $courseconfig->expirynotify;
        $course->notifystudents     = $courseconfig->notifystudents;
        $course->expirythreshold    = $courseconfig->expirythreshold;
        $course->groupmode          = $courseconfig->groupmode;
        $course->groupmodeforce     = $courseconfig->groupmodeforce;
        $course->visible            = $courseconfig->visible;
        $course->enrolpassword      = $courseconfig->enrolpassword;
        $course->guest              = $courseconfig->guest;
        $course->lang               = $courseconfig->lang;

        // Insert the record
        $course->id = $DB->insert_record('course', $course);
        if ($course->id) {
            $course = $DB->get_record('course', array('id' => $course->id));
            blocks_add_default_course_blocks($course);
            $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
            role_assign($CFG->creatornewroleid, $this->properties->requester, 0, $coursecontext->id); // assing teacher role
            if (!empty($CFG->restrictmodulesfor) && $CFG->restrictmodulesfor != 'none' && !empty($CFG->restrictbydefault)) {
                // if we're all or requested we're ok.
                $allowedmods = explode(',',$CFG->defaultallowedmodules);
                update_restricted_mods($course, $allowedmods);
            }
            $this->copy_summary_files_to_course($course);
            $this->delete();
            fix_course_sortorder();

            $user = $DB->get_record('user', array('id' => $this->properties->requester));
            $a->name = $course->fullname;
            $a->url = $CFG->wwwroot.'/course/view.php?id=' . $course->id;
            $this->notify($user, $USER, 'courserequestapproved', get_string('courseapprovedsubject'), get_string('courseapprovedemail2', 'moodle', $a));

            return $course->id;
        }
        return false;
    }

    /**
     * Reject a course request
     *
     * This function rejects a course request, emailing the requesting user the
     * provided notice and then removing the request from the database
     *
     * @param string $notice The message to display to the user
     */
    public function reject($notice) {
        global $USER;
        $this->notify($user, $USER, 'courserequestrejected', get_string('courserejectsubject'), get_string('courserejectemail', 'moodle', $notice));
        $this->delete();
    }

    /**
     * Deletes the course request and any associated files
     */
    public function delete() {
        global $DB;
        $DB->delete_records('course_request', array('id' => $this->properties->id));
        if (self::summary_editor_context() !== null) {
            $fs = get_file_storage();
            $files = $fs->get_area_files(self::summary_editor_context()->id, self::summary_editor_filearea(), $this->properties->id);
            foreach ($files as $file) {
                $file->delete();
            }
        }
    }

    /**
     * This function copies all files used in the summary for the request to the
     * summary of the course.
     *
     * This function copies, original files are left associated with the request
     * and are removed only when the request is deleted
     *
     * @param stdClass $course An object representing the course to copy files to
     */
    protected function copy_summary_files_to_course($course) {
        if (self::summary_editor_context() !== null) {
            $fs = get_file_storage();
            $files = $fs->get_area_files(self::summary_editor_context()->id, self::summary_editor_filearea(), $this->properties->id);
            foreach ($files as $file) {
                $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
                if (!$file->is_directory()) {
                    $filerecord = array('contextid'=>$coursecontext->id, 'filearea'=>'course_summary', 'itemid'=>$course->id, 'filepath'=>$file->get_filepath(), 'filename'=>$file->get_filename());
                    $fs->create_file_from_storedfile($filerecord, $file);
                }
            }
        }
    }

    /**
     * Send a message from one user to another using events_trigger
     *
     * @param object $touser
     * @param object $fromuser
     * @param string $name
     * @param string $subject
     * @param string $message
     */
    protected function notify($touser, $fromuser, $name='courserequested', $subject, $message) {
        $eventdata = new object();
        $eventdata->modulename        = 'moodle';
        $eventdata->component         = 'course';
        $eventdata->name              = $name;
        $eventdata->userfrom          = $fromuser;
        $eventdata->userto            = $touser;
        $eventdata->subject           = $subject;
        $eventdata->fullmessage       = $message;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        message_send($eventdata);
    }
}
