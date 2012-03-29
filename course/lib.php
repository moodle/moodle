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
 * @package core
 * @subpackage course
 */

defined('MOODLE_INTERNAL') || die;

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

    $groupid = 0;

    $joins = array();
    $where = '';

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
        if ($firstletter == '-') {
            $where .= " AND ".$DB->sql_like('l.action', ':modaction', false, true, true);
            $params['modaction'] = '%'.substr($modaction, 1).'%';
        } else {
            $where .= " AND ".$DB->sql_like('l.action', ':modaction', false);
            $params['modaction'] = '%'.$modaction.'%';
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
    global $DB, $SESSION, $USER;
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
    $params = array();

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
        $firstletter = substr($modaction, 0, 1);
        if ($firstletter == '-') {
            $joins[] = $DB->sql_like('l.action', ':modaction', false, true, true);
            $params['modaction'] = '%'.substr($modaction, 1).'%';
        } else {
            $joins[] = $DB->sql_like('l.action', ':modaction', false);
            $params['modaction'] = '%'.$modaction.'%';
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
        get_string('fullnameuser'),
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
    echo "<th class=\"c3 header\">".get_string('fullnameuser')."</th>\n";
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
            $courseshortname = format_string($courses[$log->course], true, array('context' => get_context_instance(CONTEXT_COURSE, SITEID)));
            echo "<td class=\"r$row c0\" >\n";
            echo "    <a href=\"{$CFG->wwwroot}/course/view.php?id={$log->course}\">".$courseshortname."</a>\n";
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
            get_string('fullnameuser')."\t".get_string('action')."\t".get_string('info');

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

    $filename = 'logs_'.userdate(time(),get_string('backupnameformat', 'langconfig'),99,false);
    $filename .= '.txt';
    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=$filename");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");

    echo get_string('savedat').userdate(time(), $strftimedatetime)."\n";
    echo $text."\n";

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

        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        $firstField = format_string($courses[$log->course], true, array('context' => $coursecontext));
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', $coursecontext));
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
    $filename = 'logs_'.userdate(time(),get_string('backupnameformat', 'langconfig'),99,false);
    $filename .= '.xls';

    $workbook = new MoodleExcelWorkbook('-');
    $workbook->send($filename);

    $worksheet = array();
    $headers = array(get_string('course'), get_string('time'), get_string('ip_address'),
                        get_string('fullnameuser'),    get_string('action'), get_string('info'));

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

        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

        $myxls->write($row, 0, format_string($courses[$log->course], true, array('context' => $coursecontext)), '');
        $myxls->write_date($row, 1, $log->time, $formatDate); // write_date() does conversion/timezone support. MDL-14934
        $myxls->write($row, 2, $log->ip, '');
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', $coursecontext));
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
    $filename = 'logs_'.userdate(time(),get_string('backupnameformat', 'langconfig'),99,false);
    $filename .= '.ods';

    $workbook = new MoodleODSWorkbook('-');
    $workbook->send($filename);

    $worksheet = array();
    $headers = array(get_string('course'), get_string('time'), get_string('ip_address'),
                        get_string('fullnameuser'),    get_string('action'), get_string('info'));

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

        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

        $myxls->write_string($row, 0, format_string($courses[$log->course], true, array('context' => $coursecontext)));
        $myxls->write_date($row, 1, $log->time);
        $myxls->write_string($row, 2, $log->ip);
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', $coursecontext));
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


function print_overview($courses, array $remote_courses=array()) {
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
        $fullname = format_string($course->fullname, true, array('context' => get_context_instance(CONTEXT_COURSE, $course->id)));
        echo $OUTPUT->box_start('coursebox');
        $attributes = array('title' => s($fullname));
        if (empty($course->visible)) {
            $attributes['class'] = 'dimmed';
        }
        echo $OUTPUT->heading(html_writer::link(
            new moodle_url('/course/view.php', array('id' => $course->id)), $fullname, $attributes), 3);
        if (array_key_exists($course->id,$htmlarray)) {
            foreach ($htmlarray[$course->id] as $modname => $html) {
                echo $html;
            }
        }
        echo $OUTPUT->box_end();
    }

    if (!empty($remote_courses)) {
        echo $OUTPUT->heading(get_string('remotecourses', 'mnet'));
    }
    foreach ($remote_courses as $course) {
        echo $OUTPUT->box_start('coursebox');
        $attributes = array('title' => s($course->fullname));
        echo $OUTPUT->heading(html_writer::link(
            new moodle_url('/auth/mnet/jump.php', array('hostid' => $course->hostid, 'wantsurl' => '/course/view.php?id='.$course->remoteid)),
            format_string($course->shortname),
            $attributes) . ' (' . format_string($course->hostname) . ')', 3);
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
            $info = explode(' ', $log->info);

            // note: in most cases I replaced hardcoding of label with use of
            // $cm->has_view() but it was not possible to do this here because
            // we don't necessarily have the $cm for it
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
                   $mod[$seq] = new stdClass();
                   $mod[$seq]->id               = $rawmods[$seq]->instance;
                   $mod[$seq]->cm               = $rawmods[$seq]->id;
                   $mod[$seq]->mod              = $rawmods[$seq]->modname;

                    // Oh dear. Inconsistent names left here for backward compatibility.
                   $mod[$seq]->section          = $section->section;
                   $mod[$seq]->sectionid        = $rawmods[$seq]->section;

                   $mod[$seq]->module           = $rawmods[$seq]->module;
                   $mod[$seq]->added            = $rawmods[$seq]->added;
                   $mod[$seq]->score            = $rawmods[$seq]->score;
                   $mod[$seq]->idnumber         = $rawmods[$seq]->idnumber;
                   $mod[$seq]->visible          = $rawmods[$seq]->visible;
                   $mod[$seq]->visibleold       = $rawmods[$seq]->visibleold;
                   $mod[$seq]->groupmode        = $rawmods[$seq]->groupmode;
                   $mod[$seq]->groupingid       = $rawmods[$seq]->groupingid;
                   $mod[$seq]->groupmembersonly = $rawmods[$seq]->groupmembersonly;
                   $mod[$seq]->indent           = $rawmods[$seq]->indent;
                   $mod[$seq]->completion       = $rawmods[$seq]->completion;
                   $mod[$seq]->extra            = "";
                   $mod[$seq]->completiongradeitemnumber =
                           $rawmods[$seq]->completiongradeitemnumber;
                   $mod[$seq]->completionview   = $rawmods[$seq]->completionview;
                   $mod[$seq]->completionexpected = $rawmods[$seq]->completionexpected;
                   $mod[$seq]->availablefrom    = $rawmods[$seq]->availablefrom;
                   $mod[$seq]->availableuntil   = $rawmods[$seq]->availableuntil;
                   $mod[$seq]->showavailability = $rawmods[$seq]->showavailability;
                   if (!empty($CFG->enableavailability)) {
                       condition_info::fill_availability_conditions($rawmods[$seq]);
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
                           if (!empty($info->icon)) {
                               $mod[$seq]->icon = $info->icon;
                           }
                           if (!empty($info->iconcomponent)) {
                               $mod[$seq]->iconcomponent = $info->iconcomponent;
                           }
                           if (!empty($info->name)) {
                               $mod[$seq]->name = $info->name;
                           }
                           if ($info instanceof cached_cm_info) {
                               // When using cached_cm_info you can include three new fields
                               // that aren't available for legacy code
                               if (!empty($info->content)) {
                                   $mod[$seq]->content = $info->content;
                               }
                               if (!empty($info->extraclasses)) {
                                   $mod[$seq]->extraclasses = $info->extraclasses;
                               }
                               if (!empty($info->onclick)) {
                                   $mod[$seq]->onclick = $info->onclick;
                               }
                               if (!empty($info->customdata)) {
                                   $mod[$seq]->customdata = $info->customdata;
                               }
                           } else {
                               // When using a stdclass, the (horrible) deprecated ->extra field
                               // is available for BC
                               if (!empty($info->extra)) {
                                   $mod[$seq]->extra = $info->extra;
                               }
                           }
                       }
                   }
                   if (!isset($mod[$seq]->name)) {
                       $mod[$seq]->name = $DB->get_field($rawmods[$seq]->modname, "name", array("id"=>$rawmods[$seq]->instance));
                   }

                   // Minimise the database size by unsetting default options when they are
                   // 'empty'. This list corresponds to code in the cm_info constructor.
                   foreach(array('idnumber', 'groupmode', 'groupingid', 'groupmembersonly',
                           'indent', 'completion', 'extra', 'extraclasses', 'onclick', 'content',
                           'icon', 'iconcomponent', 'customdata', 'showavailability', 'availablefrom',
                           'availableuntil', 'conditionscompletion', 'conditionsgrade',
                           'completionview', 'completionexpected', 'score') as $property) {
                       if (property_exists($mod[$seq], $property) &&
                               empty($mod[$seq]->{$property})) {
                           unset($mod[$seq]->{$property});
                       }
                   }
                   // Special case: this value is usually set to null, but may be 0
                   if (property_exists($mod[$seq], 'completiongradeitemnumber') &&
                           is_null($mod[$seq]->completiongradeitemnumber)) {
                       unset($mod[$seq]->completiongradeitemnumber);
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
    global $CFG, $DB, $COURSE;

    $mods          = array();    // course modules indexed by id
    $modnames      = array();    // all course module names (except resource!)
    $modnamesplural= array();    // all course module names (plural form)
    $modnamesused  = array();    // course module names used

    if ($allmods = $DB->get_records("modules")) {
        foreach ($allmods as $mod) {
            if (!file_exists("$CFG->dirroot/mod/$mod->name/lib.php")) {
                continue;
            }
            if ($mod->visible) {
                $modnames[$mod->name] = get_string("modulename", "$mod->name");
                $modnamesplural[$mod->name] = get_string("modulenameplural", "$mod->name");
            }
        }
        textlib_get_instance()->asort($modnames);
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
            textlib_get_instance()->asort($modnamesused);
        }
    }
}

/**
 * Returns an array of sections for the requested course id
 *
 * This function stores the sections against the course id within a staticvar encase
 * of subsequent requests. This is used all over + in some standard libs and course
 * format callbacks so subsequent requests are a reality.
 *
 * @staticvar array $coursesections
 * @param int $courseid
 * @return array Array of sections
 */
function get_all_sections($courseid) {
    global $DB;
    static $coursesections = array();
    if (!array_key_exists($courseid, $coursesections)) {
        $coursesections[$courseid] = $DB->get_records("course_sections", array("course"=>"$courseid"), "section",
                           "section, id, course, name, summary, summaryformat, sequence, visible");
    }
    return $coursesections[$courseid];
}

/**
 * Returns the course section to display or 0 meaning show all sections. Returns 0 for guests.
 * It also sets the $USER->display cache to array($courseid=>return value)
 *
 * @param int $courseid The course id
 * @return int Course section to display, 0 means all
 */
function course_get_display($courseid) {
    global $USER, $DB;

    if (!isloggedin() or isguestuser()) {
        //do not get settings in db for guests
        return 0; //return the implicit setting
    }

    if (!isset($USER->display[$courseid])) {
        if (!$display = $DB->get_field('course_display', 'display', array('userid' => $USER->id, 'course'=>$courseid))) {
            $display = 0; // all sections option is not stored in DB, this makes the table much smaller
        }
        //use display cache for one course only - we need to keep session small
        $USER->display = array($courseid => $display);
    }

    return $USER->display[$courseid];
}

/**
 * Show one section only or all sections.
 *
 * @param int $courseid The course id
 * @param mixed $display show only this section, 0 or 'all' means show all sections
 * @return int Course section to display, 0 means all
 */
function course_set_display($courseid, $display) {
    global $USER, $DB;

    if ($display === 'all' or empty($display)) {
        $display = 0;
    }

    if (!isloggedin() or isguestuser()) {
        //do not store settings in db for guests
        return 0;
    }

    if ($display == 0) {
        //show all, do not store anything in database
        $DB->delete_records('course_display', array('userid' => $USER->id, 'course' => $courseid));

    } else {
        if ($DB->record_exists('course_display', array('userid' => $USER->id, 'course' => $courseid))) {
            $DB->set_field('course_display', 'display', $display, array('userid' => $USER->id, 'course' => $courseid));
        } else {
            $record = new stdClass();
            $record->userid = $USER->id;
            $record->course = $courseid;
            $record->display = $display;
            $DB->insert_record('course_display', $record);
        }
    }

    //use display cache for one course only - we need to keep session small
    $USER->display = array($courseid => $display);

    return $display;
}

/**
 * Set highlighted section. Only one section can be highlighted at the time.
 *
 * @param int $courseid course id
 * @param int $marker highlight section with this number, 0 means remove higlightin
 * @return void
 */
function course_set_marker($courseid, $marker) {
    global $DB;
    $DB->set_field("course", "marker", $marker, array('id' => $courseid));
}

/**
 * For a given course section, marks it visible or hidden,
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
 * Obtains shared data that is used in print_section when displaying a
 * course-module entry.
 *
 * Calls format_text or format_string as appropriate, and obtains the correct icon.
 *
 * This data is also used in other areas of the code.
 * @param cm_info $cm Course-module data (must come from get_fast_modinfo)
 * @param object $course Moodle course object
 * @return array An array with the following values in this order:
 *   $content (optional extra content for after link),
 *   $instancename (text of link)
 */
function get_print_section_cm_text(cm_info $cm, $course) {
    global $OUTPUT;

    // Get course context
    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

    // Get content from modinfo if specified. Content displays either
    // in addition to the standard link (below), or replaces it if
    // the link is turned off by setting ->url to null.
    if (($content = $cm->get_content()) !== '') {
        $labelformatoptions = new stdClass();
        $labelformatoptions->noclean = true;
        $labelformatoptions->overflowdiv = true;
        $labelformatoptions->context = $coursecontext;
        $content = format_text($content, FORMAT_HTML, $labelformatoptions);
    } else {
        $content = '';
    }

    $stringoptions = new stdClass;
    $stringoptions->context = $coursecontext;
    $instancename = format_string($cm->name, true,  $stringoptions);
    return array($content, $instancename);
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
    static $groupings;
    static $modulenames;

    if (!isset($initialised)) {
        $groupbuttons     = ($course->groupmode or (!$course->groupmodeforce));
        $groupbuttonslink = (!$course->groupmodeforce);
        $isediting        = $PAGE->user_is_editing();
        $ismoving         = $isediting && ismoving($course->id);
        if ($ismoving) {
            $strmovehere  = get_string("movehere");
            $strmovefull  = strip_tags(get_string("movefull", "", "'$USER->activitycopyname'"));
        }
        $modulenames      = array();
        $initialised = true;
    }

    $tl = textlib_get_instance();

    $modinfo = get_fast_modinfo($course);
    $completioninfo = new completion_info($course);

    //Accessibility: replace table with list <ul>, but don't output empty list.
    if (!empty($section->sequence)) {

        // Fix bug #5027, don't want style=\"width:$width\".
        echo "<ul class=\"section img-text\">\n";
        $sectionmods = explode(",", $section->sequence);

        foreach ($sectionmods as $modnumber) {
            if (empty($mods[$modnumber])) {
                continue;
            }

            /**
             * @var cm_info
             */
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

            if (!isset($modulenames[$mod->modname])) {
                $modulenames[$mod->modname] = get_string('modulename', $mod->modname);
            }
            $modulename = $modulenames[$mod->modname];

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

            $liclasses = array();
            $liclasses[] = 'activity';
            $liclasses[] = $mod->modname;
            $liclasses[] = 'modtype_'.$mod->modname;
            $extraclasses = $mod->get_extra_classes();
            if ($extraclasses) {
                $liclasses = array_merge($liclasses, explode(' ', $extraclasses));
            }
            echo html_writer::start_tag('li', array('class'=>join(' ', $liclasses), 'id'=>'module-'.$modnumber));
            if ($ismoving) {
                echo '<a title="'.$strmovefull.'"'.
                     ' href="'.$CFG->wwwroot.'/course/mod.php?moveto='.$mod->id.'&amp;sesskey='.sesskey().'">'.
                     '<img class="movetarget" src="'.$OUTPUT->pix_url('movehere') . '" '.
                     ' alt="'.$strmovehere.'" /></a><br />
                     ';
            }

            $classes = array('mod-indent');
            if (!empty($mod->indent)) {
                $classes[] = 'mod-indent-'.$mod->indent;
                if ($mod->indent > 15) {
                    $classes[] = 'mod-indent-huge';
                }
            }
            echo html_writer::start_tag('div', array('class'=>join(' ', $classes)));

            // Get data about this course-module
            list($content, $instancename) =
                    get_print_section_cm_text($modinfo->cms[$modnumber], $course);

            //Accessibility: for files get description via icon, this is very ugly hack!
            $altname = '';
            $altname = $mod->modfullname;
            if (!empty($customicon)) {
                $archetype = plugin_supports('mod', $mod->modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
                if ($archetype == MOD_ARCHETYPE_RESOURCE) {
                    $mimetype = mimeinfo_from_icon('type', $customicon);
                    $altname = get_mimetype_description($mimetype);
                }
            }
            // Avoid unnecessary duplication: if e.g. a forum name already
            // includes the word forum (or Forum, etc) then it is unhelpful
            // to include that in the accessible description that is added.
            if (false !== strpos($tl->strtolower($instancename),
                    $tl->strtolower($altname))) {
                $altname = '';
            }
            // File type after name, for alphabetic lists (screen reader).
            if ($altname) {
                $altname = get_accesshide(' '.$altname);
            }

            // We may be displaying this just in order to show information
            // about visibility, without the actual link
            $contentpart = '';
            if ($mod->uservisible) {
                // Nope - in this case the link is fully working for user
                $linkclasses = '';
                $textclasses = '';
                if ($accessiblebutdim) {
                    $linkclasses .= ' dimmed';
                    $textclasses .= ' dimmed_text';
                    $accesstext = '<span class="accesshide">'.
                        get_string('hiddenfromstudents').': </span>';
                } else {
                    $accesstext = '';
                }
                if ($linkclasses) {
                    $linkcss = 'class="' . trim($linkclasses) . '" ';
                } else {
                    $linkcss = '';
                }
                if ($textclasses) {
                    $textcss = 'class="' . trim($textclasses) . '" ';
                } else {
                    $textcss = '';
                }

                // Get on-click attribute value if specified
                $onclick = $mod->get_on_click();
                if ($onclick) {
                    $onclick = ' onclick="' . $onclick . '"';
                }

                if ($url = $mod->get_url()) {
                    // Display link itself
                    echo '<a ' . $linkcss . $mod->extra . $onclick .
                            ' href="' . $url . '"><img src="' . $mod->get_icon_url() .
                            '" class="activityicon" alt="' .
                            $modulename . '" /> ' .
                            $accesstext . '<span class="instancename">' .
                            $instancename . $altname . '</span></a>';

                    // If specified, display extra content after link
                    if ($content) {
                        $contentpart = '<div class="' . trim('contentafterlink' . $textclasses) .
                                '">' . $content . '</div>';
                    }
                } else {
                    // No link, so display only content
                    $contentpart = '<div ' . $textcss . $mod->extra . '>' .
                            $accesstext . $content . '</div>';
                }

                if (!empty($mod->groupingid) && has_capability('moodle/course:managegroups', get_context_instance(CONTEXT_COURSE, $course->id))) {
                    if (!isset($groupings)) {
                        $groupings = groups_get_all_groupings($course->id);
                    }
                    echo " <span class=\"groupinglabel\">(".format_string($groupings[$mod->groupingid]->name).')</span>';
                }
            } else {
                $textclasses = $extraclasses;
                $textclasses .= ' dimmed_text';
                if ($textclasses) {
                    $textcss = 'class="' . trim($textclasses) . '" ';
                } else {
                    $textcss = '';
                }
                $accesstext = '<span class="accesshide">' .
                        get_string('notavailableyet', 'condition') .
                        ': </span>';

                if ($url = $mod->get_url()) {
                    // Display greyed-out text of link
                    echo '<div ' . $textcss . $mod->extra .
                            ' >' . '<img src="' . $mod->get_icon_url() .
                            '" class="activityicon" alt="' .
                            $modulename .
                            '" /> <span>'. $instancename . $altname .
                            '</span></div>';

                    // Do not display content after link when it is greyed out like this.
                } else {
                    // No link, so display only content (also greyed)
                    $contentpart = '<div ' . $textcss . $mod->extra . '>' .
                            $accesstext . $content . '</div>';
                }
            }

            // Module can put text after the link (e.g. forum unread)
            echo $mod->get_after_link();

            // If there is content but NO link (eg label), then display the
            // content here (BEFORE any icons). In this case cons must be
            // displayed after the content so that it makes more sense visually
            // and for accessibility reasons, e.g. if you have a one-line label
            // it should work similarly (at least in terms of ordering) to an
            // activity.
            if (empty($url)) {
                echo $contentpart;
            }

            if ($isediting) {
                if ($groupbuttons and plugin_supports('mod', $mod->modname, FEATURE_GROUPS, 0)) {
                    if (! $mod->groupmodelink = $groupbuttonslink) {
                        $mod->groupmode = $course->groupmode;
                    }

                } else {
                    $mod->groupmode = false;
                }
                echo '&nbsp;&nbsp;';
                echo make_editing_buttons($mod, $absolute, true, $mod->indent, $section->section);
                echo $mod->get_after_edit_icons();
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
                    $imgalt = s(get_string('completion-alt-'.$completionicon, 'completion', $mod->name));
                    if ($completion == COMPLETION_TRACKING_MANUAL && !$isediting) {
                        $imgtitle = s(get_string('completion-title-'.$completionicon, 'completion', $mod->name));
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
<form class='togglecompletion$extraclass' method='post' action='".$CFG->wwwroot."/course/togglecompletion.php'><div>
<input type='hidden' name='id' value='{$mod->id}' />
<input type='hidden' name='modulename' value='".s($mod->name)."' />
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

            // If there is content AND a link, then display the content here
            // (AFTER any icons). Otherwise it was displayed before
            if (!empty($url)) {
                echo $contentpart;
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

            echo html_writer::end_tag('div');
            echo html_writer::end_tag('li')."\n";
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
        $select->set_help_icon('resources');
        $output .= $OUTPUT->render($select);
    }

    if (!empty($activities)) {
        $select = new url_select($activities, '', array(''=>$straddactivity), "section$section");
        $select->set_help_icon('activities');
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
 * Gets the child categories of a given courses category. Uses a static cache
 * to make repeat calls efficient.
 *
 * @param int $parentid the id of a course category.
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

        $context = get_context_instance(CONTEXT_COURSECAT, $category->id);
        $categoryname = format_string($category->name, true, array('context' => $context));

        // Update $path.
        if ($path) {
            $path = $path.' / '.$categoryname;
        } else {
            $path = $categoryname;
        }

        // Add this category to $list, if the permissions check out.
        if (empty($requiredcapability)) {
            $list[$category->id] = $path;

        } else {
            $requiredcapability = (array)$requiredcapability;
            if (has_all_capabilities($requiredcapability, $context)) {
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
 * This function generates a structured array of courses and categories.
 *
 * The depth of categories is limited by $CFG->maxcategorydepth however there
 * is no limit on the number of courses!
 *
 * Suitable for use with the course renderers course_category_tree method:
 * $renderer = $PAGE->get_renderer('core','course');
 * echo $renderer->course_category_tree(get_course_category_tree());
 *
 * @global moodle_database $DB
 * @param int $id
 * @param int $depth
 */
function get_course_category_tree($id = 0, $depth = 0) {
    global $DB, $CFG;
    $viewhiddencats = has_capability('moodle/category:viewhiddencategories', get_context_instance(CONTEXT_SYSTEM));
    $categories = get_child_categories($id);
    $categoryids = array();
    foreach ($categories as $key => &$category) {
        if (!$category->visible && !$viewhiddencats) {
            unset($categories[$key]);
            continue;
        }
        $categoryids[$category->id] = $category;
        if (empty($CFG->maxcategorydepth) || $depth <= $CFG->maxcategorydepth) {
            list($category->categories, $subcategories) = get_course_category_tree($category->id, $depth+1);
            foreach ($subcategories as $subid=>$subcat) {
                $categoryids[$subid] = $subcat;
            }
            $category->courses = array();
        }
    }

    if ($depth > 0) {
        // This is a recursive call so return the required array
        return array($categories, $categoryids);
    }

    // The depth is 0 this function has just been called so we can finish it off

    list($ccselect, $ccjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
    list($catsql, $catparams) = $DB->get_in_or_equal(array_keys($categoryids));
    $sql = "SELECT
            c.id,c.sortorder,c.visible,c.fullname,c.shortname,c.summary,c.category
            $ccselect
            FROM {course} c
            $ccjoin
            WHERE c.category $catsql ORDER BY c.sortorder ASC";
    if ($courses = $DB->get_records_sql($sql, $catparams)) {
        // loop throught them
        foreach ($courses as $course) {
            if ($course->id == SITEID) {
                continue;
            }
            context_instance_preload($course);
            if (!empty($course->visible) || has_capability('moodle/course:viewhiddencourses', get_context_instance(CONTEXT_COURSE, $course->id))) {
                $categoryids[$course->category]->courses[$course->id] = $course;
            }
        }
    }
    return $categories;
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
        $category = new stdClass();
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
function print_category_info($category, $depth=0, $showcourses = false) {
    global $CFG, $DB, $OUTPUT;

    $strsummary = get_string('summary');

    $catlinkcss = null;
    if (!$category->visible) {
        $catlinkcss = array('class'=>'dimmed');
    }
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

    $courses = get_courses($category->id, 'c.sortorder ASC', 'c.id,c.sortorder,c.visible,c.fullname,c.shortname,c.summary');
    $context = get_context_instance(CONTEXT_COURSECAT, $category->id);
    $fullname = format_string($category->name, true, array('context' => $context));

    if ($showcourses and $coursecount) {
        echo '<div class="categorylist clearfix">';
        $cat = '';
        $cat .= html_writer::tag('div', $catimage, array('class'=>'image'));
        $catlink = html_writer::link(new moodle_url('/course/category.php', array('id'=>$category->id)), $fullname, $catlinkcss);
        $cat .= html_writer::tag('div', $catlink, array('class'=>'name'));

        $html = '';
        if ($depth > 0) {
            for ($i=0; $i< $depth; $i++) {
                $html = html_writer::tag('div', $html . $cat, array('class'=>'indentation'));
                $cat = '';
            }
        } else {
            $html = $cat;
        }
        echo html_writer::tag('div', $html, array('class'=>'category'));
        echo html_writer::tag('div', '', array('class'=>'clearfloat'));

        // does the depth exceed maxcategorydepth
        // maxcategorydepth == 0 or unset meant no limit
        $limit = !(isset($CFG->maxcategorydepth) && ($depth >= $CFG->maxcategorydepth-1));
        if ($courses && ($limit || $CFG->maxcategorydepth == 0)) {
            foreach ($courses as $course) {
                $linkcss = null;
                if (!$course->visible) {
                    $linkcss = array('class'=>'dimmed');
                }

                $courselink = html_writer::link(new moodle_url('/course/view.php', array('id'=>$course->id)), format_string($course->fullname), $linkcss);

                // print enrol info
                $courseicon = '';
                if ($icons = enrol_get_course_info_icons($course)) {
                    foreach ($icons as $pix_icon) {
                        $courseicon = $OUTPUT->render($pix_icon).' ';
                    }
                }

                $coursecontent = html_writer::tag('div', $courseicon.$courselink, array('class'=>'name'));

                if ($course->summary) {
                    $link = new moodle_url('/course/info.php?id='.$course->id);
                    $actionlink = $OUTPUT->action_link($link, '<img alt="'.$strsummary.'" src="'.$OUTPUT->pix_url('i/info') . '" />',
                        new popup_action('click', $link, 'courseinfo', array('height' => 400, 'width' => 500)),
                        array('title'=>$strsummary));

                    $coursecontent .= html_writer::tag('div', $actionlink, array('class'=>'info'));
                }

                $html = '';
                for ($i=0; $i <= $depth; $i++) {
                    $html = html_writer::tag('div', $html . $coursecontent , array('class'=>'indentation'));
                    $coursecontent = '';
                }
                echo html_writer::tag('div', $html, array('class'=>'course clearfloat'));
            }
        }
        echo '</div>';
    } else {
        echo '<div class="categorylist">';
        $html = '';
        $cat = html_writer::link(new moodle_url('/course/category.php', array('id'=>$category->id)), $fullname, $catlinkcss);
        if (count($courses) > 0) {
            $cat .= html_writer::tag('span', ' ('.count($courses).')', array('title'=>get_string('numberofcourses'), 'class'=>'numberofcourse'));
        }

        if ($depth > 0) {
            for ($i=0; $i< $depth; $i++) {
                $html = html_writer::tag('div', $html .$cat, array('class'=>'indentation'));
                $cat = '';
            }
        } else {
            $html = $cat;
        }

        echo html_writer::tag('div', $html, array('class'=>'category'));
        echo html_writer::tag('div', '', array('class'=>'clearfloat'));
        echo '</div>';
    }
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
                                                array('summary','summaryformat'));
        } else {
            $courses    = get_courses_wmanagers('all',
                                                'c.sortorder ASC',
                                                array('summary','summaryformat'));
        }
        unset($categories);
    } else {
        $courses    = get_courses_wmanagers($category->id,
                                            'c.sortorder ASC',
                                            array('summary','summaryformat'));
    }

    if ($courses) {
        echo html_writer::start_tag('ul', array('class'=>'unlist'));
        foreach ($courses as $course) {
            $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
            if ($course->visible == 1 || has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                echo html_writer::start_tag('li');
                print_course($course);
                echo html_writer::end_tag('li');
            }
        }
        echo html_writer::end_tag('ul');
    } else {
        echo $OUTPUT->heading(get_string("nocoursesyet"));
        $context = get_context_instance(CONTEXT_SYSTEM);
        if (has_capability('moodle/course:create', $context)) {
            $options = array();
            if (!empty($category->id)) {
                $options['category'] = $category->id;
            } else {
                $options['category'] = $CFG->defaultrequestcategory;
            }
            echo html_writer::start_tag('div', array('class'=>'addcoursebutton'));
            echo $OUTPUT->single_button(new moodle_url('/course/edit.php', $options), get_string("addnewcourse"));
            echo html_writer::end_tag('div');
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
    $course->summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php', $context->id, 'course', 'summary', NULL);

    echo html_writer::start_tag('div', array('class'=>'coursebox clearfix'));
    echo html_writer::start_tag('div', array('class'=>'info'));
    echo html_writer::start_tag('h3', array('class'=>'name'));

    $linkhref = new moodle_url('/course/view.php', array('id'=>$course->id));
    $linktext = highlight($highlightterms, format_string($course->fullname));
    $linkparams = array('title'=>get_string('entercourse'));
    if (empty($course->visible)) {
        $linkparams['class'] = 'dimmed';
    }
    echo html_writer::link($linkhref, $linktext, $linkparams);
    echo html_writer::end_tag('h3');

    /// first find all roles that are supposed to be displayed
    if (!empty($CFG->coursecontact)) {
        $managerroles = explode(',', $CFG->coursecontact);
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

                    $namesarray[] = format_string($ra->rolename).': '.
                                    html_writer::link(new moodle_url('/user/view.php', array('id'=>$ra->user->id, 'course'=>SITEID)), $fullname);
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

                    $namesarray[] = format_string($teacher->rolename).': '.
                                    html_writer::link(new moodle_url('/user/view.php', array('id'=>$teacher->id, 'course'=>SITEID)), $fullname);
                }
            }
        }

        if (!empty($namesarray)) {
            echo html_writer::start_tag('ul', array('class'=>'teachers'));
            foreach ($namesarray as $name) {
                echo html_writer::tag('li', $name);
            }
            echo html_writer::end_tag('ul');
        }
    }
    echo html_writer::end_tag('div'); // End of info div

    echo html_writer::start_tag('div', array('class'=>'summary'));
    $options = new stdClass();
    $options->noclean = true;
    $options->para = false;
    $options->overflowdiv = true;
    if (!isset($course->summaryformat)) {
        $course->summaryformat = FORMAT_MOODLE;
    }
    echo highlight($highlightterms, format_text($course->summary, $course->summaryformat, $options,  $course->id));
    if ($icons = enrol_get_course_info_icons($course)) {
        echo html_writer::start_tag('div', array('class'=>'enrolmenticons'));
        foreach ($icons as $icon) {
            echo $OUTPUT->render($icon);
        }
        echo html_writer::end_tag('div'); // End of enrolmenticons div
    }
    echo html_writer::end_tag('div'); // End of summary div
    echo html_writer::end_tag('div'); // End of coursebox div
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

    $courses  = enrol_get_my_courses('summary', 'visible DESC,sortorder ASC');
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
    $options = new stdClass();
    $options->noclean = true;
    $options->para = false;
    $options->overflowdiv = true;
    echo format_text($course->summary, $course->summaryformat, $options);
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
    $cw = new stdClass();
    $cw->course   = $courseid;
    $cw->section  = $section;
    $cw->summary  = "";
    $cw->summaryformat = FORMAT_HTML;
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

        $DB->set_field("course_sections", "sequence", $newsequence, array("id"=>$section->id));
        return $section->id;     // Return course_sections ID that was used.

    } else {  // Insert a new record
        $section->course   = $mod->course;
        $section->section  = $mod->section;
        $section->summary  = "";
        $section->summaryformat = FORMAT_HTML;
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
    global $DB, $CFG;
    require_once($CFG->libdir.'/gradelib.php');

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

    // hide the associated grade items so the teacher doesn't also have to go to the gradebook and hide them there
    $grade_items = grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$modulename, 'iteminstance'=>$cm->instance, 'courseid'=>$cm->course));
    if ($grade_items) {
        foreach ($grade_items as $grade_item) {
            $grade_item->set_hidden(!$visible);
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
    // Delete completion and availability data; it is better to do this even if the
    // features are not turned on, in case they were turned on previously (these will be
    // very quick on an empty table)
    $DB->delete_records('course_modules_completion', array('coursemoduleid' => $cm->id));
    $DB->delete_records('course_modules_availability', array('coursemoduleid'=> $cm->id));

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
 * @param object $course course object
 * @param int $section Section number (not id!!!)
 * @param int $move (-1 or 1)
 * @return boolean true if section moved successfully
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

    $DB->set_field("course_sections", "section", $sectiondest, array("id"=>$sectionrecord->id));
    $DB->set_field("course_sections", "section", $section, array("id"=>$sectiondestrecord->id));

    // Update highlighting if the move affects highlighted section
    if ($course->marker == $section) {
        course_set_marker($course->id, $sectiondest);
    } elseif ($course->marker == $sectiondest) {
        course_set_marker($course->id, $section);
    }

    // if the focus is on the section that is being moved, then move the focus along
    if (course_get_display($course->id) == $section) {
        course_set_display($course->id, $sectiondest);
    }

    // Check for duplicates and fix order if needed.
    // There is a very rare case that some sections in the same course have the same section id.
    $sections = $DB->get_records('course_sections', array('course'=>$course->id), 'section ASC');
    $n = 0;
    foreach ($sections as $section) {
        if ($section->section != $n) {
            $DB->set_field('course_sections', 'section', $n, array('id'=>$section->id));
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
    if (course_get_display($course->id) == $section) {
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
        $str->groupsnone     = get_string('clicktochangeinbrackets', 'moodle', get_string("groupsnone"));
        $str->groupsseparate = get_string('clicktochangeinbrackets', 'moodle', get_string("groupsseparate"));
        $str->groupsvisible  = get_string('clicktochangeinbrackets', 'moodle', get_string("groupsvisible"));
        $str->forcedgroupsnone     = get_string('forcedmodeinbrackets', 'moodle', get_string("groupsnone"));
        $str->forcedgroupsseparate = get_string('forcedmodeinbrackets', 'moodle', get_string("groupsseparate"));
        $str->forcedgroupsvisible  = get_string('forcedmodeinbrackets', 'moodle', get_string("groupsvisible"));
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
            $forcedgrouptitle = $str->forcedgroupsseparate;
            $groupclass = 'editing_groupsseparate';
            $groupimage = $OUTPUT->pix_url('t/groups') . '';
            $grouplink  = $path.'/mod.php?id='.$mod->id.'&amp;groupmode=0&amp;sesskey='.$sesskey;
        } else if ($mod->groupmode == VISIBLEGROUPS) {
            $grouptitle = $str->groupsvisible;
            $forcedgrouptitle = $str->forcedgroupsvisible;
            $groupclass = 'editing_groupsvisible';
            $groupimage = $OUTPUT->pix_url('t/groupv') . '';
            $grouplink  = $path.'/mod.php?id='.$mod->id.'&amp;groupmode=1&amp;sesskey='.$sesskey;
        } else {
            $grouptitle = $str->groupsnone;
            $forcedgrouptitle = $str->forcedgroupsnone;
            $groupclass = 'editing_groupsnone';
            $groupimage = $OUTPUT->pix_url('t/groupn') . '';
            $grouplink  = $path.'/mod.php?id='.$mod->id.'&amp;groupmode=2&amp;sesskey='.$sesskey;
        }
        if ($mod->groupmodelink) {
            $groupmode = '<a class="'.$groupclass.'" title="'.$grouptitle.'" href="'.$grouplink.'">'.
                         '<img src="'.$groupimage.'" class="iconsmall" '.
                         'alt="'.$grouptitle.'" /></a>';
        } else {
            $groupmode = '<img title="'.$forcedgrouptitle.'"'.
                         ' src="'.$groupimage.'" class="iconsmall" '.
                         'alt="'.$forcedgrouptitle.'" />';
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
    if (has_capability('moodle/role:assign', $modcontext)){
        $context = get_context_instance(CONTEXT_MODULE, $mod->id);
        $assign = '<a class="editing_assign" title="'.$str->assign.'" href="'.$CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.
            $context->id.'"><img src="'.$OUTPUT->pix_url('i/roles') . '" alt="'.$str->assign.'" class="iconsmall"/></a>';
    } else {
        $assign = '';
    }

    // Duplicate (require both target import caps to be able to duplicate, see modduplicate.php)
    $dupecaps = array('moodle/backup:backuptargetimport', 'moodle/restore:restoretargetimport');
    if (has_all_capabilities($dupecaps, get_context_instance(CONTEXT_COURSE, $mod->course))) {
        $duplicatemodule = '<a class="editing_duplicate" title="'.$str->duplicate.'" href="'.$path.'/mod.php?duplicate='.$mod->id.
            '&amp;sesskey='.$sesskey.$section.'"><img'.
            ' src="'.$OUTPUT->pix_url('t/copy') . '" class="iconsmall" '.
            ' alt="'.$str->duplicate.'" /></a>'."\n";
    } else {
        $duplicatemodule = '';
    }

    return '<span class="commands">'."\n".$leftright.$move.
           '<a class="editing_update" title="'.$str->update.'" href="'.$path.'/mod.php?update='.$mod->id.
           '&amp;sesskey='.$sesskey.$section.'"><img'.
           ' src="'.$OUTPUT->pix_url('t/edit') . '" class="iconsmall" '.
           ' alt="'.$str->update.'" /></a>'."\n".
           $duplicatemodule.
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

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    $shortname = format_string($course->shortname, true, array('context' => $context));
    $fullname = format_string($course->fullname, true, array('context' => get_context_instance(CONTEXT_COURSE, $course->id)));

    $str = $shortname.': '. $fullname;
    if (strlen($str) <= $max) {
        return $str;
    } else {
        return $textlib->substr($str, 0, $max-3).'...';
    }
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
        $am = new stdClass();
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
    // Originally there was a course:update test only, but it did not match the test in course edit form
    if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
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
 * @param stdClass $category
 * @param boolean $showfeedback display some notices
 * @return array return deleted courses
 */
function category_delete_full($category, $showfeedback=true) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->libdir.'/questionlib.php');
    require_once($CFG->dirroot.'/cohort/lib.php');

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

    // move or delete cohorts in this context
    cohort_delete_category($category);

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
    require_once($CFG->dirroot.'/cohort/lib.php');

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

    // move or delete cohorts in this context
    cohort_delete_category($category);

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
 * @param array $courseids is an array of course ids
 * @param int $categoryid
 * @return bool success
 */
function move_courses($courseids, $categoryid) {
    global $CFG, $DB, $OUTPUT;

    if (empty($courseids)) {
        // nothing to do
        return;
    }

    if (!$category = $DB->get_record('course_categories', array('id'=>$categoryid))) {
        return false;
    }

    $courseids = array_reverse($courseids);
    $newparent = get_context_instance(CONTEXT_COURSECAT, $category->id);
    $i = 1;

    foreach ($courseids as $courseid) {
        if ($course = $DB->get_record('course', array('id'=>$courseid), 'id, category')) {
            $course = new stdClass();
            $course->id = $courseid;
            $course->category  = $category->id;
            $course->sortorder = $category->sortorder + MAX_COURSES_IN_CATEGORY - $i++;
            if ($category->visible == 0) {
                // hide the course when moving into hidden category,
                // do not update the visibleold flag - we want to get to previous state if somebody unhides the category
                $course->visible = 0;
            }

            $DB->update_record('course', $course);

            $context   = get_context_instance(CONTEXT_COURSE, $course->id);
            context_moved($context, $newparent);
        }
    }
    fix_course_sortorder();

    return true;
}

/**
 * Hide course category and child course and subcategories
 * @param stdClass $category
 * @return void
 */
function course_category_hide($category) {
    global $DB;

    $category->visible = 0;
    $DB->set_field('course_categories', 'visible', 0, array('id'=>$category->id));
    $DB->set_field('course_categories', 'visibleold', 0, array('id'=>$category->id));
    $DB->execute("UPDATE {course} SET visibleold = visible WHERE category = ?", array($category->id)); // store visible flag so that we can return to it if we immediately unhide
    $DB->set_field('course', 'visible', 0, array('category' => $category->id));
    // get all child categories and hide too
    if ($subcats = $DB->get_records_select('course_categories', "path LIKE ?", array("$category->path/%"))) {
        foreach ($subcats as $cat) {
            $DB->set_field('course_categories', 'visibleold', $cat->visible, array('id'=>$cat->id));
            $DB->set_field('course_categories', 'visible', 0, array('id'=>$cat->id));
            $DB->execute("UPDATE {course} SET visibleold = visible WHERE category = ?", array($cat->id));
            $DB->set_field('course', 'visible', 0, array('category' => $cat->id));
        }
    }
}

/**
 * Show course category and child course and subcategories
 * @param stdClass $category
 * @return void
 */
function course_category_show($category) {
    global $DB;

    $category->visible = 1;
    $DB->set_field('course_categories', 'visible', 1, array('id'=>$category->id));
    $DB->set_field('course_categories', 'visibleold', 1, array('id'=>$category->id));
    $DB->execute("UPDATE {course} SET visible = visibleold WHERE category = ?", array($category->id));
    // get all child categories and unhide too
    if ($subcats = $DB->get_records_select('course_categories', "path LIKE ?", array("$category->path/%"))) {
        foreach ($subcats as $cat) {
            if ($cat->visibleold) {
                $DB->set_field('course_categories', 'visible', 1, array('id'=>$cat->id));
            }
            $DB->execute("UPDATE {course} SET visible = visibleold WHERE category = ?", array($cat->id));
        }
    }
}

/**
 * Efficiently moves a category - NOTE that this can have
 * a huge impact access-control-wise...
 */
function move_category($category, $newparentcat) {
    global $CFG, $DB;

    $context = get_context_instance(CONTEXT_COURSECAT, $category->id);

    $hidecat = false;
    if (empty($newparentcat->id)) {
        $DB->set_field('course_categories', 'parent', 0, array('id'=>$category->id));

        $newparent = get_context_instance(CONTEXT_SYSTEM);

    } else {
        $DB->set_field('course_categories', 'parent', $newparentcat->id, array('id'=>$category->id));
        $newparent = get_context_instance(CONTEXT_COURSECAT, $newparentcat->id);

        if (!$newparentcat->visible and $category->visible) {
            // better hide category when moving into hidden category, teachers may unhide afterwards and the hidden children will be restored properly
            $hidecat = true;
        }
    }

    context_moved($context, $newparent);

    // now make it last in new category
    $DB->set_field('course_categories', 'sortorder', MAX_COURSES_IN_CATEGORY*MAX_COURSE_CATEGORIES, array('id'=>$category->id));

    // and fix the sortorders
    fix_course_sortorder();

    if ($hidecat) {
        course_category_hide($category);
    }
}

/**
 * Returns the display name of the given section that the course prefers.
 *
 * This function utilizes a callback that can be implemented within the course
 * formats lib.php file to customize the display name that is used to reference
 * the section.
 *
 * By default (if callback is not defined) the method
 * {@see get_numeric_section_name} is called instead.
 *
 * @param stdClass $course The course to get the section name for
 * @param stdClass $section Section object from database
 * @return Display name that the course format prefers, e.g. "Week 2"
 *
 * @see get_generic_section_name
 */
function get_section_name(stdClass $course, stdClass $section) {
    global $CFG;

    /// Inelegant hack for bug 3408
    if ($course->format == 'site') {
        return get_string('site');
    }

    // Use course formatter callback if it exists
    $namingfile = $CFG->dirroot.'/course/format/'.$course->format.'/lib.php';
    $namingfunction = 'callback_'.$course->format.'_get_section_name';
    if (!function_exists($namingfunction) && file_exists($namingfile)) {
        require_once $namingfile;
    }
    if (function_exists($namingfunction)) {
        return $namingfunction($course, $section);
    }

    // else, default behavior:
    return get_generic_section_name($course->format, $section);
}

/**
 * Gets the generic section name for a courses section.
 *
 * @param string $format Course format ID e.g. 'weeks' $course->format
 * @param stdClass $section Section object from database
 * @return Display name that the course format prefers, e.g. "Week 2"
 */
function get_generic_section_name($format, stdClass $section) {
    return get_string('sectionname', "format_$format") . ' ' . $section->section;
}


function course_format_uses_sections($format) {
    global $CFG;

    $featurefile = $CFG->dirroot.'/course/format/'.$format.'/lib.php';
    $featurefunction = 'callback_'.$format.'_uses_sections';
    if (!function_exists($featurefunction) && file_exists($featurefile)) {
        require_once $featurefile;
    }
    if (function_exists($featurefunction)) {
        return $featurefunction();
    }

    return false;
}

/**
 * Returns the information about the ajax support in the given source format
 *
 * The returned object's property (boolean)capable indicates that
 * the course format supports Moodle course ajax features.
 * The property (array)testedbrowsers can be used as a parameter for {@see ajaxenabled()}.
 *
 * @param string $format
 * @return stdClass
 */
function course_format_ajax_support($format) {
    global $CFG;

    // set up default values
    $ajaxsupport = new stdClass();
    $ajaxsupport->capable = false;
    $ajaxsupport->testedbrowsers = array();

    // get the information from the course format library
    $featurefile = $CFG->dirroot.'/course/format/'.$format.'/lib.php';
    $featurefunction = 'callback_'.$format.'_ajax_support';
    if (!function_exists($featurefunction) && file_exists($featurefile)) {
        require_once $featurefile;
    }
    if (function_exists($featurefunction)) {
        $formatsupport = $featurefunction();
        if (isset($formatsupport->capable)) {
            $ajaxsupport->capable = $formatsupport->capable;
        }
        if (is_array($formatsupport->testedbrowsers)) {
            $ajaxsupport->testedbrowsers = $formatsupport->testedbrowsers;
        }
    }

    return $ajaxsupport;
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
        if (strpos($fieldname, 'role_') !== 0) {
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
 * Create a course and either return a $course object
 *
 * Please note this functions does not verify any access control,
 * the calling code is responsible for all validation (usually it is the form definition).
 *
 * @param array $editoroptions course description editor options
 * @param object $data  - all the data needed for an entry in the 'course' table
 * @return object new course instance
 */
function create_course($data, $editoroptions = NULL) {
    global $CFG, $DB;

    //check the categoryid - must be given for all new courses
    $category = $DB->get_record('course_categories', array('id'=>$data->category), '*', MUST_EXIST);

    //check if the shortname already exist
    if (!empty($data->shortname)) {
        if ($DB->record_exists('course', array('shortname' => $data->shortname))) {
            throw new moodle_exception('shortnametaken');
        }
    }

    //check if the id number already exist
    if (!empty($data->idnumber)) {
        if ($DB->record_exists('course', array('idnumber' => $data->idnumber))) {
            throw new moodle_exception('idnumbertaken');
        }
    }

    $data->timecreated  = time();
    $data->timemodified = $data->timecreated;

    // place at beginning of any category
    $data->sortorder = 0;

    if ($editoroptions) {
        // summary text is updated later, we need context to store the files first
        $data->summary = '';
        $data->summary_format = FORMAT_HTML;
    }

    if (!isset($data->visible)) {
        // data not from form, add missing visibility info
        $data->visible = $category->visible;
    }
    $data->visibleold = $data->visible;

    $newcourseid = $DB->insert_record('course', $data);
    $context = get_context_instance(CONTEXT_COURSE, $newcourseid, MUST_EXIST);

    if ($editoroptions) {
        // Save the files used in the summary editor and store
        $data = file_postupdate_standard_editor($data, 'summary', $editoroptions, $context, 'course', 'summary', 0);
        $DB->set_field('course', 'summary', $data->summary, array('id'=>$newcourseid));
        $DB->set_field('course', 'summaryformat', $data->summary_format, array('id'=>$newcourseid));
    }

    $course = $DB->get_record('course', array('id'=>$newcourseid));

    // Setup the blocks
    blocks_add_default_course_blocks($course);

    $section = new stdClass();
    $section->course        = $course->id;   // Create a default section.
    $section->section       = 0;
    $section->summaryformat = FORMAT_HTML;
    $DB->insert_record('course_sections', $section);

    fix_course_sortorder();

    // update module restrictions
    if ($course->restrictmodules) {
        if (isset($data->allowedmods)) {
            update_restricted_mods($course, $data->allowedmods);
        } else {
            if (!empty($CFG->defaultallowedmodules)) {
                update_restricted_mods($course, explode(',', $CFG->defaultallowedmodules));
            }
        }
    }

    // new context created - better mark it as dirty
    mark_context_dirty($context->path);

    // Save any custom role names.
    save_local_role_names($course->id, (array)$data);

    // set up enrolments
    enrol_course_updated(true, $course, $data);

    add_to_log(SITEID, 'course', 'new', 'view.php?id='.$course->id, $data->fullname.' (ID '.$course->id.')');

    // Trigger events
    events_trigger('course_created', $course);

    return $course;
}

/**
 * Update a course.
 *
 * Please note this functions does not verify any access control,
 * the calling code is responsible for all validation (usually it is the form definition).
 *
 * @param object $data  - all the data needed for an entry in the 'course' table
 * @param array $editoroptions course description editor options
 * @return void
 */
function update_course($data, $editoroptions = NULL) {
    global $CFG, $DB;

    $data->timemodified = time();

    $oldcourse = $DB->get_record('course', array('id'=>$data->id), '*', MUST_EXIST);
    $context   = get_context_instance(CONTEXT_COURSE, $oldcourse->id);

    if ($editoroptions) {
        $data = file_postupdate_standard_editor($data, 'summary', $editoroptions, $context, 'course', 'summary', 0);
    }

    if (!isset($data->category) or empty($data->category)) {
        // prevent nulls and 0 in category field
        unset($data->category);
    }
    $movecat = (isset($data->category) and $oldcourse->category != $data->category);

    if (!isset($data->visible)) {
        // data not from form, add missing visibility info
        $data->visible = $oldcourse->visible;
    }

    if ($data->visible != $oldcourse->visible) {
        // reset the visibleold flag when manually hiding/unhiding course
        $data->visibleold = $data->visible;
    } else {
        if ($movecat) {
            $newcategory = $DB->get_record('course_categories', array('id'=>$data->category));
            if (empty($newcategory->visible)) {
                // make sure when moving into hidden category the course is hidden automatically
                $data->visible = 0;
            }
        }
    }

    // Update with the new data
    $DB->update_record('course', $data);

    $course = $DB->get_record('course', array('id'=>$data->id));

    if ($movecat) {
        $newparent = get_context_instance(CONTEXT_COURSECAT, $course->category);
        context_moved($context, $newparent);
    }

    fix_course_sortorder();

    // Test for and remove blocks which aren't appropriate anymore
    blocks_remove_inappropriate($course);

    // update module restrictions
    if (isset($data->allowedmods)) {
        update_restricted_mods($course, $data->allowedmods);
    }

    // Save any custom role names.
    save_local_role_names($course->id, $data);

    // update enrol settings
    enrol_course_updated(false, $course, $data);

    add_to_log($course->id, "course", "update", "edit.php?id=$course->id", $course->id);

    // Trigger events
    events_trigger('course_updated', $course);
}

/**
 * Average number of participants
 * @return integer
 */
function average_number_of_participants() {
    global $DB, $SITE;

    //count total of enrolments for visible course (except front page)
    $sql = 'SELECT COUNT(*) FROM (
        SELECT DISTINCT ue.userid, e.courseid
        FROM {user_enrolments} ue, {enrol} e, {course} c
        WHERE ue.enrolid = e.id
            AND e.courseid <> :siteid
            AND c.id = e.courseid
            AND c.visible = 1) as total';
    $params = array('siteid' => $SITE->id);
    $enrolmenttotal = $DB->count_records_sql($sql, $params);


    //count total of visible courses (minus front page)
    $coursetotal = $DB->count_records('course', array('visible' => 1));
    $coursetotal = $coursetotal - 1 ;

    //average of enrolment
    if (empty($coursetotal)) {
        $participantaverage = 0;
    } else {
        $participantaverage = $enrolmenttotal / $coursetotal;
    }

    return $participantaverage;
}

/**
 * Average number of course modules
 * @return integer
 */
function average_number_of_courses_modules() {
    global $DB, $SITE;

    //count total of visible course module (except front page)
    $sql = 'SELECT COUNT(*) FROM (
        SELECT cm.course, cm.module
        FROM {course} c, {course_modules} cm
        WHERE c.id = cm.course
            AND c.id <> :siteid
            AND cm.visible = 1
            AND c.visible = 1) as total';
    $params = array('siteid' => $SITE->id);
    $moduletotal = $DB->count_records_sql($sql, $params);


    //count total of visible courses (minus front page)
    $coursetotal = $DB->count_records('course', array('visible' => 1));
    $coursetotal = $coursetotal - 1 ;

    //average of course module
    if (empty($coursetotal)) {
        $coursemoduleaverage = 0;
    } else {
        $coursemoduleaverage = $moduletotal / $coursetotal;
    }

    return $coursemoduleaverage;
}

/**
 * This class pertains to course requests and contains methods associated with
 * create, approving, and removing course requests.
 *
 * Please note we do not allow embedded images here because there is no context
 * to store them with proper access control.
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
 */
class course_request {

    /**
     * This is the stdClass that stores the properties for the course request
     * and is externally accessed through the __get magic method
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
        $data = file_prepare_standard_editor($data, 'summary', self::summary_editor_options());
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

        // Summary is a required field so copy the text over
        $data->summary       = $data->summary_editor['text'];
        $data->summaryformat = $data->summary_editor['format'];

        $data->id = $DB->insert_record('course_request', $data);

        // Create a new course_request object and return it
        $request = new course_request($data);

        // Notify the admin if required.
        if ($users = get_users_from_config($CFG->courserequestnotify, 'moodle/site:approvecourse')) {

            $a = new stdClass;
            $a->link = "$CFG->wwwroot/course/pending.php";
            $a->user = fullname($USER);
            $subject = get_string('courserequest');
            $message = get_string('courserequestnotifyemail', 'admin', $a);
            foreach ($users as $user) {
                $request->notify($user, $USER, 'courserequested', $subject, $message);
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
            self::$summaryeditoroptions = array('maxfiles' => 0, 'maxbytes'=>0);
        }
        return self::$summaryeditoroptions;
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
     * transferring any files used in the summary to the new course and then removing
     * the course request and the files associated with it.
     *
     * @return int The id of the course that was created from this request
     */
    public function approve() {
        global $CFG, $DB, $USER;

        $user = $DB->get_record('user', array('id' => $this->properties->requester, 'deleted'=>0), '*', MUST_EXIST);

        $category = get_course_category($CFG->defaultrequestcategory);
        $courseconfig = get_config('moodlecourse');

        // Transfer appropriate settings
        $data = clone($this->properties);
        unset($data->id);
        unset($data->reason);
        unset($data->requester);

        // Set category
        $data->category = $category->id;
        $data->sortorder = $category->sortorder; // place as the first in category

        // Set misc settings
        $data->requested = 1;
        if (!empty($CFG->restrictmodulesfor) && $CFG->restrictmodulesfor != 'none' && !empty($CFG->restrictbydefault)) {
            $data->restrictmodules = 1;
        }

        // Apply course default settings
        $data->format             = $courseconfig->format;
        $data->numsections        = $courseconfig->numsections;
        $data->hiddensections     = $courseconfig->hiddensections;
        $data->newsitems          = $courseconfig->newsitems;
        $data->showgrades         = $courseconfig->showgrades;
        $data->showreports        = $courseconfig->showreports;
        $data->maxbytes           = $courseconfig->maxbytes;
        $data->groupmode          = $courseconfig->groupmode;
        $data->groupmodeforce     = $courseconfig->groupmodeforce;
        $data->visible            = $courseconfig->visible;
        $data->visibleold         = $data->visible;
        $data->lang               = $courseconfig->lang;

        $course = create_course($data);
        $context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);

        // add enrol instances
        if (!$DB->record_exists('enrol', array('courseid'=>$course->id, 'enrol'=>'manual'))) {
            if ($manual = enrol_get_plugin('manual')) {
                $manual->add_default_instance($course);
            }
        }

        // enrol the requester as teacher if necessary
        if (!empty($CFG->creatornewroleid) and !is_viewing($context, $user, 'moodle/role:assign') and !is_enrolled($context, $user, 'moodle/role:assign')) {
            enrol_try_internal_enrol($course->id, $user->id, $CFG->creatornewroleid);
        }

        $this->delete();

        $a = new stdClass();
        $a->name = format_string($course->fullname, true, array('context' => get_context_instance(CONTEXT_COURSE, $course->id)));
        $a->url = $CFG->wwwroot.'/course/view.php?id=' . $course->id;
        $this->notify($user, $USER, 'courserequestapproved', get_string('courseapprovedsubject'), get_string('courseapprovedemail2', 'moodle', $a));

        return $course->id;
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
        global $USER, $DB;
        $user = $DB->get_record('user', array('id' => $this->properties->requester), '*', MUST_EXIST);
        $this->notify($user, $USER, 'courserequestrejected', get_string('courserejectsubject'), get_string('courserejectemail', 'moodle', $notice));
        $this->delete();
    }

    /**
     * Deletes the course request and any associated files
     */
    public function delete() {
        global $DB;
        $DB->delete_records('course_request', array('id' => $this->properties->id));
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
        $eventdata = new stdClass();
        $eventdata->component         = 'moodle';
        $eventdata->name              = $name;
        $eventdata->userfrom          = $fromuser;
        $eventdata->userto            = $touser;
        $eventdata->subject           = $subject;
        $eventdata->fullmessage       = $message;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        $eventdata->notification      = 1;
        message_send($eventdata);
    }
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function course_page_type_list($pagetype, $parentcontext, $currentcontext) {
    // if above course context ,display all course fomats
    list($currentcontext, $course, $cm) = get_context_info_array($currentcontext->id);
    if ($course->id == SITEID) {
        return array('*'=>get_string('page-x', 'pagetype'));
    } else {
        return array('*'=>get_string('page-x', 'pagetype'),
            'course-*'=>get_string('page-course-x', 'pagetype'),
            'course-view-*'=>get_string('page-course-view-x', 'pagetype')
        );
    }
}
