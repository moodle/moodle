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
require_once($CFG->dirroot.'/course/dnduploadlib.php');
require_once($CFG->dirroot.'/course/format/lib.php');

define('COURSE_MAX_LOGS_PER_PAGE', 1000);       // records
define('COURSE_MAX_RECENT_PERIOD', 172800);     // Two days, in seconds

/**
 * Number of courses to display when summaries are included.
 * @var int
 * @deprecated since 2.4, use $CFG->courseswithsummarieslimit instead.
 */
define('COURSE_MAX_SUMMARIES_PER_PAGE', 10);

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
            if (strpos($url, 'report/') === 0) {
                // there is only one report type, course reports are deprecated
                $url = "/$url";
                break;
            }
        case 'file':
        case 'login':
        case 'lib':
        case 'admin':
        case 'calendar':
        case 'category':
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
        case 'grade':
            $url = "/grade/$url";
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
    //if ($course->groupmode == SEPARATEGROUPS and !has_capability('moodle/course:managegroups', context_course::instance($course->id))) {
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
    if ($course->groupmode == SEPARATEGROUPS and !has_capability('moodle/course:managegroups', context_course::instance($course->id))) {
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
        $brokenurl=(textlib::strlen($log->url)==100 && textlib::substr($log->url,97)=='...');

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

        $row[] = html_writer::link(new moodle_url("/user/view.php?id={$log->userid}&course={$log->course}"), fullname($log, has_capability('moodle/site:viewfullnames', context_course::instance($course->id))));

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
            $courseshortname = format_string($courses[$log->course], true, array('context' => context_course::instance(SITEID)));
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
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', context_course::instance($course->id)));
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
    global $DB, $CFG;

    require_once($CFG->libdir . '/csvlib.class.php');

    $csvexporter = new csv_export_writer('tab');

    $header = array();
    $header[] = get_string('course');
    $header[] = get_string('time');
    $header[] = get_string('ip_address');
    $header[] = get_string('fullnameuser');
    $header[] = get_string('action');
    $header[] = get_string('info');

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

    $csvexporter->set_filename('logs', '.txt');
    $title = array(get_string('savedat').userdate(time(), $strftimedatetime));
    $csvexporter->add_data($title);
    $csvexporter->add_data($header);

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
        if ($ld && is_numeric($log->info)) {
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

        $coursecontext = context_course::instance($course->id);
        $firstField = format_string($courses[$log->course], true, array('context' => $coursecontext));
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', $coursecontext));
        $actionurl = $CFG->wwwroot. make_log_url($log->module,$log->url);
        $row = array($firstField, userdate($log->time, $strftimedatetime), $log->ip, $fullname, $log->module.' '.$log->action.' ('.$actionurl.')', $log->info);
        $csvexporter->add_data($row);
    }
    $csvexporter->download_file();
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
        $worksheet[$wsnumber] = $workbook->add_worksheet($sheettitle);
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
        if ($ld && is_numeric($log->info)) {
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

        $coursecontext = context_course::instance($course->id);

        $myxls->write($row, 0, format_string($courses[$log->course], true, array('context' => $coursecontext)), '');
        $myxls->write_date($row, 1, $log->time, $formatDate); // write_date() does conversion/timezone support. MDL-14934
        $myxls->write($row, 2, $log->ip, '');
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', $coursecontext));
        $myxls->write($row, 3, $fullname, '');
        $actionurl = $CFG->wwwroot. make_log_url($log->module,$log->url);
        $myxls->write($row, 4, $log->module.' '.$log->action.' ('.$actionurl.')', '');
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
        $worksheet[$wsnumber] = $workbook->add_worksheet($sheettitle);
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
        if ($ld && is_numeric($log->info)) {
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

        $coursecontext = context_course::instance($course->id);

        $myxls->write_string($row, 0, format_string($courses[$log->course], true, array('context' => $coursecontext)));
        $myxls->write_date($row, 1, $log->time);
        $myxls->write_string($row, 2, $log->ip);
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', $coursecontext));
        $myxls->write_string($row, 3, $fullname);
        $actionurl = $CFG->wwwroot. make_log_url($log->module,$log->url);
        $myxls->write_string($row, 4, $log->module.' '.$log->action.' ('.$actionurl.')');
        $myxls->write_string($row, 5, $log->info);

        $row++;
    }

    $workbook->close();
    return true;
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
        $fullname = format_string($course->fullname, true, array('context' => context_course::instance($course->id)));
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

    $context = context_course::instance($course->id);

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

    $modinfo = get_fast_modinfo($course);

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
                   $mod[$seq]->showdescription  = $rawmods[$seq]->showdescription;
                   if (!empty($CFG->enableavailability)) {
                       condition_info::fill_availability_conditions($rawmods[$seq]);
                       $mod[$seq]->conditionscompletion = $rawmods[$seq]->conditionscompletion;
                       $mod[$seq]->conditionsgrade  = $rawmods[$seq]->conditionsgrade;
                       $mod[$seq]->conditionsfield  = $rawmods[$seq]->conditionsfield;
                   }

                   $modname = $mod[$seq]->mod;
                   $functionname = $modname."_get_coursemodule_info";

                   if (!file_exists("$CFG->dirroot/mod/$modname/lib.php")) {
                       continue;
                   }

                   include_once("$CFG->dirroot/mod/$modname/lib.php");

                   if ($hasfunction = function_exists($functionname)) {
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
                               if (!empty($info->iconurl)) {
                                   $mod[$seq]->iconurl = $info->iconurl;
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
                   // When there is no modname_get_coursemodule_info function,
                   // but showdescriptions is enabled, then we use the 'intro'
                   // and 'introformat' fields in the module table
                   if (!$hasfunction && $rawmods[$seq]->showdescription) {
                       if ($modvalues = $DB->get_record($rawmods[$seq]->modname,
                               array('id' => $rawmods[$seq]->instance), 'name, intro, introformat')) {
                           // Set content from intro and introformat. Filters are disabled
                           // because we  filter it with format_text at display time
                           $mod[$seq]->content = format_module_intro($rawmods[$seq]->modname,
                                   $modvalues, $rawmods[$seq]->id, false);

                           // To save making another query just below, put name in here
                           $mod[$seq]->name = $modvalues->name;
                       }
                   }
                   if (!isset($mod[$seq]->name)) {
                       $mod[$seq]->name = $DB->get_field($rawmods[$seq]->modname, "name", array("id"=>$rawmods[$seq]->instance));
                   }

                   // Minimise the database size by unsetting default options when they are
                   // 'empty'. This list corresponds to code in the cm_info constructor.
                   foreach (array('idnumber', 'groupmode', 'groupingid', 'groupmembersonly',
                           'indent', 'completion', 'extra', 'extraclasses', 'iconurl', 'onclick', 'content',
                           'icon', 'iconcomponent', 'customdata', 'showavailability', 'availablefrom',
                           'availableuntil', 'conditionscompletion', 'conditionsgrade',
                           'completionview', 'completionexpected', 'score', 'showdescription')
                           as $property) {
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
 * Returns the localised human-readable names of all used modules
 *
 * @param bool $plural if true returns the plural forms of the names
 * @return array where key is the module name (component name without 'mod_') and
 *     the value is the human-readable string. Array sorted alphabetically by value
 */
function get_module_types_names($plural = false) {
    static $modnames = null;
    global $DB, $CFG;
    if ($modnames === null) {
        $modnames = array(0 => array(), 1 => array());
        if ($allmods = $DB->get_records("modules")) {
            foreach ($allmods as $mod) {
                if (file_exists("$CFG->dirroot/mod/$mod->name/lib.php") && $mod->visible) {
                    $modnames[0][$mod->name] = get_string("modulename", "$mod->name");
                    $modnames[1][$mod->name] = get_string("modulenameplural", "$mod->name");
                }
            }
            collatorlib::asort($modnames[0]);
            collatorlib::asort($modnames[1]);
        }
    }
    return $modnames[(int)$plural];
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
    format_base::reset_course_cache($courseid);
}

/**
 * For a given course section, marks it visible or hidden,
 * and does the same for every activity in that section
 *
 * @param int $courseid course id
 * @param int $sectionnumber The section number to adjust
 * @param int $visibility The new visibility
 * @return array A list of resources which were hidden in the section
 */
function set_section_visible($courseid, $sectionnumber, $visibility) {
    global $DB;

    $resourcestotoggle = array();
    if ($section = $DB->get_record("course_sections", array("course"=>$courseid, "section"=>$sectionnumber))) {
        $DB->set_field("course_sections", "visible", "$visibility", array("id"=>$section->id));
        if (!empty($section->sequence)) {
            $modules = explode(",", $section->sequence);
            foreach ($modules as $moduleid) {
                if ($cm = $DB->get_record('course_modules', array('id' => $moduleid), 'visible, visibleold')) {
                    if ($visibility) {
                        // As we unhide the section, we use the previously saved visibility stored in visibleold.
                        set_coursemodule_visible($moduleid, $cm->visibleold);
                    } else {
                        // We hide the section, so we hide the module but we store the original state in visibleold.
                        set_coursemodule_visible($moduleid, 0);
                        $DB->set_field('course_modules', 'visibleold', $cm->visible, array('id' => $moduleid));
                    }
                }
            }
        }
        rebuild_course_cache($courseid, true);

        // Determine which modules are visible for AJAX update
        if (!empty($modules)) {
            list($insql, $params) = $DB->get_in_or_equal($modules);
            $select = 'id ' . $insql . ' AND visible = ?';
            array_push($params, $visibility);
            if (!$visibility) {
                $select .= ' AND visibleold = 1';
            }
            $resourcestotoggle = $DB->get_fieldset_select('course_modules', 'id', $select, $params);
        }
    }
    return $resourcestotoggle;
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

    // Get content from modinfo if specified. Content displays either
    // in addition to the standard link (below), or replaces it if
    // the link is turned off by setting ->url to null.
    if (($content = $cm->get_content()) !== '') {
        // Improve filter performance by preloading filter setttings for all
        // activities on the course (this does nothing if called multiple
        // times)
        filter_preload_activities($cm->get_modinfo());

        // Get module context
        $modulecontext = context_module::instance($cm->id);
        $labelformatoptions = new stdClass();
        $labelformatoptions->noclean = true;
        $labelformatoptions->overflowdiv = true;
        $labelformatoptions->context = $modulecontext;
        $content = format_text($content, FORMAT_HTML, $labelformatoptions);
    } else {
        $content = '';
    }

    // Get course context
    $coursecontext = context_course::instance($course->id);
    $stringoptions = new stdClass;
    $stringoptions->context = $coursecontext;
    $instancename = format_string($cm->name, true,  $stringoptions);
    return array($content, $instancename);
}

/**
 * Prints a section full of activity modules
 *
 * @param stdClass $course The course
 * @param stdClass|section_info $section The section object containing properties id and section
 * @param array $mods (argument not used)
 * @param array $modnamesused (argument not used)
 * @param bool $absolute All links are absolute
 * @param string $width Width of the container
 * @param bool $hidecompletion Hide completion status
 * @param int $sectionreturn The section to return to
 * @return void
 */
function print_section($course, $section, $mods, $modnamesused, $absolute=false, $width="100%", $hidecompletion=false, $sectionreturn=null) {
    global $CFG, $USER, $DB, $PAGE, $OUTPUT;

    static $initialised;

    static $groupbuttons;
    static $groupbuttonslink;
    static $isediting;
    static $ismoving;
    static $strmovehere;
    static $strmovefull;
    static $strunreadpostsone;

    if (!isset($initialised)) {
        $groupbuttons     = ($course->groupmode or (!$course->groupmodeforce));
        $groupbuttonslink = (!$course->groupmodeforce);
        $isediting        = $PAGE->user_is_editing();
        $ismoving         = $isediting && ismoving($course->id);
        if ($ismoving) {
            $strmovehere  = get_string("movehere");
            $strmovefull  = strip_tags(get_string("movefull", "", "'$USER->activitycopyname'"));
        }
        $initialised = true;
    }

    $modinfo = get_fast_modinfo($course);
    $completioninfo = new completion_info($course);

    //Accessibility: replace table with list <ul>, but don't output empty list.
    if (!empty($modinfo->sections[$section->section])) {

        // Fix bug #5027, don't want style=\"width:$width\".
        echo "<ul class=\"section img-text\">\n";

        foreach ($modinfo->sections[$section->section] as $modnumber) {
            $mod = $modinfo->cms[$modnumber];

            if ($ismoving and $mod->id == $USER->activitycopy) {
                // do not display moving mod
                continue;
            }

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
            if (!$mod->uservisible &&
                (empty($mod->showavailability) ||
                  empty($mod->availableinfo))) {
                // visibility shortcut
                continue;
            }

            // In some cases the activity is visible to user, but it is
            // dimmed. This is done if viewhiddenactivities is true and if:
            // 1. the activity is not visible, or
            // 2. the activity has dates set which do not include current, or
            // 3. the activity has any other conditions set (regardless of whether
            //    current user meets them)
            $modcontext = context_module::instance($mod->id);
            $canviewhidden = has_capability('moodle/course:viewhiddenactivities', $modcontext);
            $accessiblebutdim = false;
            $conditionalhidden = false;
            if ($canviewhidden) {
                $accessiblebutdim = !$mod->visible;
                if (!empty($CFG->enableavailability)) {
                    $conditionalhidden = $mod->availablefrom > time() ||
                        ($mod->availableuntil && $mod->availableuntil < time()) ||
                        count($mod->conditionsgrade) > 0 ||
                        count($mod->conditionscompletion) > 0;
                }
                $accessiblebutdim = $conditionalhidden || $accessiblebutdim;
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
            // Avoid unnecessary duplication: if e.g. a forum name already
            // includes the word forum (or Forum, etc) then it is unhelpful
            // to include that in the accessible description that is added.
            if (false !== strpos(textlib::strtolower($instancename),
                    textlib::strtolower($altname))) {
                $altname = '';
            }
            // File type after name, for alphabetic lists (screen reader).
            if ($altname) {
                $altname = get_accesshide(' '.$altname);
            }

            // Start the div for the activity title, excluding the edit icons.
            echo html_writer::start_tag('div', array('class' => 'activityinstance'));

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
                    if ($conditionalhidden) {
                        $linkclasses .= ' conditionalhidden';
                        $textclasses .= ' conditionalhidden';
                    }
                    $accesstext = get_accesshide(get_string('hiddenfromstudents').': ');
                } else {
                    $accesstext = '';
                }
                if ($linkclasses) {
                    $linkcss = trim($linkclasses) . ' ';
                } else {
                    $linkcss = '';
                }
                if ($textclasses) {
                    $textcss = trim($textclasses) . ' ';
                } else {
                    $textcss = '';
                }

                // Get on-click attribute value if specified and decode the onclick - it
                // has already been encoded for display (puke).
                $onclick = htmlspecialchars_decode($mod->get_on_click(), ENT_QUOTES);

                $groupinglabel = '';
                if (!empty($mod->groupingid) && has_capability('moodle/course:managegroups', context_course::instance($course->id))) {
                    $groupings = groups_get_all_groupings($course->id);
                    $groupinglabel = html_writer::tag('span', '('.format_string($groupings[$mod->groupingid]->name).')',
                            array('class' => 'groupinglabel'));
                }

                if ($url = $mod->get_url()) {
                    // Display link itself.
                    $activitylink = html_writer::empty_tag('img', array('src' => $mod->get_icon_url(),
                            'class' => 'iconlarge activityicon', 'alt' => $mod->modfullname)) . $accesstext .
                            html_writer::tag('span', $instancename . $altname, array('class' => 'instancename'));
                    echo html_writer::link($url, $activitylink, array('class' => $linkcss, 'onclick' => $onclick)) .
                            $groupinglabel;

                    // If specified, display extra content after link.
                    if ($content) {
                        $contentpart = html_writer::tag('div', $content, array('class' =>
                                trim('contentafterlink ' . $textclasses)));
                    }
                } else {
                    // No link, so display only content.
                    $contentpart = html_writer::tag('div', $accesstext . $content, array('class' => $textcss));
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
                            '" class="activityicon" alt="" /> <span>'. $instancename . $altname .
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

            // Closing the tag which contains everything but edit icons. $contentpart should not be part of this.
            echo html_writer::end_tag('div');

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
                echo make_editing_buttons($mod, $absolute, true, $mod->indent, $sectionreturn);
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
                    $formattedname = format_string($mod->name, true, array('context' => $modcontext));
                    $imgalt = get_string('completion-alt-' . $completionicon, 'completion', $formattedname);
                    if ($completion == COMPLETION_TRACKING_MANUAL && !$isediting) {
                        $imgtitle = get_string('completion-title-' . $completionicon, 'completion', $formattedname);
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
                        echo html_writer::start_tag('form', array(
                                'class' => 'togglecompletion' . $extraclass,
                                'method' => 'post',
                                'action' => $CFG->wwwroot . '/course/togglecompletion.php'));
                        echo html_writer::start_tag('div');
                        echo html_writer::empty_tag('input', array(
                                'type' => 'hidden', 'name' => 'id', 'value' => $mod->id));
                        echo html_writer::empty_tag('input', array(
                                'type' => 'hidden', 'name' => 'modulename',
                                'value' => $mod->name));
                        echo html_writer::empty_tag('input', array(
                                'type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
                        echo html_writer::empty_tag('input', array(
                                'type' => 'hidden', 'name' => 'completionstate',
                                'value' => $newstate));
                        echo html_writer::empty_tag('input', array(
                                'type' => 'image', 'src' => $imgsrc, 'alt' => $imgalt, 'title' => $imgtitle,
                                'aria-live' => 'polite'));
                        echo html_writer::end_tag('div');
                        echo html_writer::end_tag('form');
                    } else {
                        // In auto mode, or when editing, the icon is just an image.
                        echo html_writer::tag('span', html_writer::empty_tag('img', array(
                                'src' => $imgsrc, 'alt' => $imgalt, 'title' => $imgalt)),
                                array('class' => 'autocompletion'));
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
                // Don't add availability information if user is not editing and activity is hidden.
                if ($mod->visible || $PAGE->user_is_editing()) {
                    $hidinfoclass = '';
                    if (!$mod->visible) {
                        $hidinfoclass = 'hide';
                    }
                    $ci = new condition_info($mod);
                    $fullinfo = $ci->get_full_information();
                    if($fullinfo) {
                        echo '<div class="availabilityinfo '.$hidinfoclass.'">'.get_string($mod->showavailability
                            ? 'userrestriction_visible'
                            : 'userrestriction_hidden','condition',
                            $fullinfo).'</div>';
                    }
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
    if (!empty($modinfo->sections[$section->section]) || $ismoving) {
        echo "</ul><!--class='section'-->\n\n";
    }
}

/**
 * Prints the menus to add activities and resources.
 *
 * @param stdClass $course The course
 * @param int $section relative section number (field course_sections.section)
 * @param null|array $modnames An array containing the list of modules and their names
 *     if omitted will be taken from get_module_types_names()
 * @param bool $vertical Vertical orientation
 * @param bool $return Return the menus or send them to output
 * @param int $sectionreturn The section to link back to
 * @return void|string depending on $return
 */
function print_section_add_menus($course, $section, $modnames = null, $vertical=false, $return=false, $sectionreturn=null) {
    global $CFG, $OUTPUT;

    if ($modnames === null) {
        $modnames = get_module_types_names();
    }

    // check to see if user can add menus and there are modules to add
    if (!has_capability('moodle/course:manageactivities', context_course::instance($course->id))
            || empty($modnames)) {
        if ($return) {
            return '';
        } else {
            return false;
        }
    }

    // Retrieve all modules with associated metadata
    $modules = get_module_metadata($course, $modnames, $sectionreturn);

    // We'll sort resources and activities into two lists
    $resources = array();
    $activities = array();

    // We need to add the section section to the link for each module
    $sectionlink = '&section=' . $section . '&sr=' . $sectionreturn;

    foreach ($modules as $module) {
        if (isset($module->types)) {
            // This module has a subtype
            // NOTE: this is legacy stuff, module subtypes are very strongly discouraged!!
            $subtypes = array();
            foreach ($module->types as $subtype) {
                $subtypes[$subtype->link . $sectionlink] = $subtype->title;
            }

            // Sort module subtypes into the list
            if (!empty($module->title)) {
                // This grouping has a name
                if ($module->archetype == MOD_CLASS_RESOURCE) {
                    $resources[] = array($module->title=>$subtypes);
                } else {
                    $activities[] = array($module->title=>$subtypes);
                }
            } else {
                // This grouping does not have a name
                if ($module->archetype == MOD_CLASS_RESOURCE) {
                    $resources = array_merge($resources, $subtypes);
                } else {
                    $activities = array_merge($activities, $subtypes);
                }
            }
        } else {
            // This module has no subtypes
            if ($module->archetype == MOD_ARCHETYPE_RESOURCE) {
                $resources[$module->link . $sectionlink] = $module->title;
            } else if ($module->archetype === MOD_ARCHETYPE_SYSTEM) {
                // System modules cannot be added by user, do not add to dropdown
            } else {
                $activities[$module->link . $sectionlink] = $module->title;
            }
        }
    }

    $straddactivity = get_string('addactivity');
    $straddresource = get_string('addresource');
    $sectionname = get_section_name($course, $section);
    $strresourcelabel = get_string('addresourcetosection', null, $sectionname);
    $stractivitylabel = get_string('addactivitytosection', null, $sectionname);

    $output = html_writer::start_tag('div', array('class' => 'section_add_menus', 'id' => 'add_menus-section-' . $section));

    if (!$vertical) {
        $output .= html_writer::start_tag('div', array('class' => 'horizontal'));
    }

    if (!empty($resources)) {
        $select = new url_select($resources, '', array(''=>$straddresource), "ressection$section");
        $select->set_help_icon('resources');
        $select->set_label($strresourcelabel, array('class' => 'accesshide'));
        $output .= $OUTPUT->render($select);
    }

    if (!empty($activities)) {
        $select = new url_select($activities, '', array(''=>$straddactivity), "section$section");
        $select->set_help_icon('activities');
        $select->set_label($stractivitylabel, array('class' => 'accesshide'));
        $output .= $OUTPUT->render($select);
    }

    if (!$vertical) {
        $output .= html_writer::end_tag('div');
    }

    $output .= html_writer::end_tag('div');

    if (course_ajax_enabled($course)) {
        $straddeither = get_string('addresourceoractivity');
        // The module chooser link
        $modchooser = html_writer::start_tag('div', array('class' => 'mdl-right'));
        $modchooser.= html_writer::start_tag('div', array('class' => 'section-modchooser'));
        $icon = $OUTPUT->pix_icon('t/add', '');
        $span = html_writer::tag('span', $straddeither, array('class' => 'section-modchooser-text'));
        $modchooser .= html_writer::tag('span', $icon . $span, array('class' => 'section-modchooser-link'));
        $modchooser.= html_writer::end_tag('div');
        $modchooser.= html_writer::end_tag('div');

        // Wrap the normal output in a noscript div
        $usemodchooser = get_user_preferences('usemodchooser', $CFG->modchooserdefault);
        if ($usemodchooser) {
            $output = html_writer::tag('div', $output, array('class' => 'hiddenifjs addresourcedropdown'));
            $modchooser = html_writer::tag('div', $modchooser, array('class' => 'visibleifjs addresourcemodchooser'));
        } else {
            // If the module chooser is disabled, we need to ensure that the dropdowns are shown even if javascript is disabled
            $output = html_writer::tag('div', $output, array('class' => 'show addresourcedropdown'));
            $modchooser = html_writer::tag('div', $modchooser, array('class' => 'hide addresourcemodchooser'));
        }
        $output = $modchooser . $output;
    }

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Retrieve all metadata for the requested modules
 *
 * @param object $course The Course
 * @param array $modnames An array containing the list of modules and their
 * names
 * @param int $sectionreturn The section to return to
 * @return array A list of stdClass objects containing metadata about each
 * module
 */
function get_module_metadata($course, $modnames, $sectionreturn = null) {
    global $CFG, $OUTPUT;

    // get_module_metadata will be called once per section on the page and courses may show
    // different modules to one another
    static $modlist = array();
    if (!isset($modlist[$course->id])) {
        $modlist[$course->id] = array();
    }

    $return = array();
    $urlbase = "/course/mod.php?id=$course->id&sesskey=".sesskey().'&sr='.$sectionreturn.'&add=';
    foreach($modnames as $modname => $modnamestr) {
        if (!course_allowed_module($course, $modname)) {
            continue;
        }
        if (isset($modlist[$course->id][$modname])) {
            // This module is already cached
            $return[$modname] = $modlist[$course->id][$modname];
            continue;
        }

        // Include the module lib
        $libfile = "$CFG->dirroot/mod/$modname/lib.php";
        if (!file_exists($libfile)) {
            continue;
        }
        include_once($libfile);

        // NOTE: this is legacy stuff, module subtypes are very strongly discouraged!!
        $gettypesfunc =  $modname.'_get_types';
        if (function_exists($gettypesfunc)) {
            $types = $gettypesfunc();
            if (is_array($types) && count($types) > 0) {
                $group = new stdClass();
                $group->name = $modname;
                $group->icon = $OUTPUT->pix_icon('icon', '', $modname, array('class' => 'icon'));
                foreach($types as $type) {
                    if ($type->typestr === '--') {
                        continue;
                    }
                    if (strpos($type->typestr, '--') === 0) {
                        $group->title = str_replace('--', '', $type->typestr);
                        continue;
                    }
                    // Set the Sub Type metadata
                    $subtype = new stdClass();
                    $subtype->title = $type->typestr;
                    $subtype->type = str_replace('&amp;', '&', $type->type);
                    $subtype->name = preg_replace('/.*type=/', '', $subtype->type);
                    $subtype->archetype = $type->modclass;

                    // The group archetype should match the subtype archetypes and all subtypes
                    // should have the same archetype
                    $group->archetype = $subtype->archetype;

                    if (get_string_manager()->string_exists('help' . $subtype->name, $modname)) {
                        $subtype->help = get_string('help' . $subtype->name, $modname);
                    }
                    $subtype->link = $urlbase . $subtype->type;
                    $group->types[] = $subtype;
                }
                $modlist[$course->id][$modname] = $group;
            }
        } else {
            $module = new stdClass();
            $module->title = get_string('modulename', $modname);
            $module->name = $modname;
            $module->link = $urlbase . $modname;
            $module->icon = $OUTPUT->pix_icon('icon', '', $module->name, array('class' => 'icon'));
            $sm = get_string_manager();
            if ($sm->string_exists('modulename_help', $modname)) {
                $module->help = get_string('modulename_help', $modname);
                if ($sm->string_exists('modulename_link', $modname)) {  // Link to further info in Moodle docs
                    $link = get_string('modulename_link', $modname);
                    $linktext = get_string('morehelp');
                    $module->help .= html_writer::tag('div', $OUTPUT->doc_link($link, $linktext, true), array('class' => 'helpdoclink'));
                }
            }
            $module->archetype = plugin_supports('mod', $modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
            $modlist[$course->id][$modname] = $module;
        }
        if (isset($modlist[$course->id][$modname])) {
            $return[$modname] = $modlist[$course->id][$modname];
        } else {
            debugging("Invalid module metadata configuration for {$modname}");
        }
    }

    return $return;
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
        return context_coursecat::instance($categoryid, IGNORE_MISSING);
    } else {
        return context_system::instance();
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

        $context = context_coursecat::instance($category->id);
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
    $viewhiddencats = has_capability('moodle/category:viewhiddencategories', context_system::instance());
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

    if (empty($categoryids)) {
        // No categories available (probably all hidden).
        return array();
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
            if (!empty($course->visible) || has_capability('moodle/course:viewhiddencourses', context_course::instance($course->id))) {
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
        if ($category->visible or has_capability('moodle/category:viewhiddencategories', context_system::instance())) {
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
    $context = context_coursecat::instance($category->id);
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

                $coursename = get_course_display_name_for_list($course);
                $courselink = html_writer::link(new moodle_url('/course/view.php', array('id'=>$course->id)), format_string($coursename), $linkcss);

                // print enrol info
                $courseicon = '';
                if ($icons = enrol_get_course_info_icons($course)) {
                    foreach ($icons as $pix_icon) {
                        $courseicon = $OUTPUT->render($pix_icon);
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
 * Print courses in category. If category is 0 then all courses are printed.
 * @param int|stdClass $category category object or id.
 * @return bool true if courses found and printed, else false.
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
            $coursecontext = context_course::instance($course->id);
            if ($course->visible == 1 || has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                echo html_writer::start_tag('li');
                print_course($course);
                echo html_writer::end_tag('li');
            }
        }
        echo html_writer::end_tag('ul');
    } else {
        echo $OUTPUT->heading(get_string("nocoursesyet"));
        $context = context_system::instance();
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
            return false;
        }
    }
    return true;
}

/**
 * Print a description of a course, suitable for browsing in a list.
 *
 * @param object $course the course object.
 * @param string $highlightterms (optional) some search terms that should be highlighted in the display.
 */
function print_course($course, $highlightterms = '') {
    global $CFG, $USER, $DB, $OUTPUT;

    $context = context_course::instance($course->id);

    // Rewrite file URLs so that they are correct
    $course->summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php', $context->id, 'course', 'summary', NULL);

    echo html_writer::start_tag('div', array('class'=>'coursebox clearfix'));
    echo html_writer::start_tag('div', array('class'=>'info'));
    echo html_writer::start_tag('h3', array('class'=>'name'));

    $linkhref = new moodle_url('/course/view.php', array('id'=>$course->id));

    $coursename = get_course_display_name_for_list($course);
    $linktext = highlight($highlightterms, format_string($coursename));
    $linkparams = array('title'=>get_string('entercourse'));
    if (empty($course->visible)) {
        $linkparams['class'] = 'dimmed';
    }
    echo html_writer::link($linkhref, $linktext, $linkparams);
    echo html_writer::end_tag('h3');

    /// first find all roles that are supposed to be displayed
    if (!empty($CFG->coursecontact)) {
        $managerroles = explode(',', $CFG->coursecontact);
        $rusers = array();

        if (!isset($course->managers)) {
            list($sort, $sortparams) = users_order_by_sql('u');
            $rusers = get_role_users($managerroles, $context, true,
                'ra.id AS raid, u.id, u.username, u.firstname, u.lastname, rn.name AS rolecoursealias,
                 r.name AS rolename, r.sortorder, r.id AS roleid, r.shortname AS roleshortname',
                'r.sortorder ASC, ' . $sort, null, '', '', '', '', $sortparams);
        } else {
            //  use the managers array if we have it for perf reasosn
            //  populate the datastructure like output of get_role_users();
            foreach ($course->managers as $manager) {
                $user = clone($manager->user);
                $user->roleid = $manager->roleid;
                $user->rolename = $manager->rolename;
                $user->roleshortname = $manager->roleshortname;
                $user->rolecoursealias = $manager->rolecoursealias;
                $rusers[$user->id] = $user;
            }
        }

        $namesarray = array();
        $canviewfullnames = has_capability('moodle/site:viewfullnames', $context);
        foreach ($rusers as $ra) {
            if (isset($namesarray[$ra->id])) {
                //  only display a user once with the higest sortorder role
                continue;
            }

            $role = new stdClass();
            $role->id = $ra->roleid;
            $role->name = $ra->rolename;
            $role->shortname = $ra->roleshortname;
            $role->coursealias = $ra->rolecoursealias;
            $rolename = role_get_name($role, $context, ROLENAME_ALIAS);

            $fullname = fullname($ra, $canviewfullnames);
            $namesarray[$ra->id] = $rolename.': '.
                html_writer::link(new moodle_url('/user/view.php', array('id'=>$ra->id, 'course'=>SITEID)), $fullname);
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
            $icon->attributes["alt"] .= ": ". format_string($coursename, true, array('context'=>$context));
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
        $output .= '<input type="text" id="shortsearchbox" size="12" name="search" value="'.s($value).'" />';
        $output .= '<input type="submit" value="'.get_string('go').'" />';
        $output .= '</fieldset></form>';
    } else if ($format == 'navbar') {
        $output  = '<form id="coursesearchnavbar" action="'.$CFG->wwwroot.'/course/search.php" method="get">';
        $output .= '<fieldset class="coursesearchbox invisiblefieldset">';
        $output .= '<label for="navsearchbox">'.$strsearchcourses.': </label>';
        $output .= '<input type="text" id="navsearchbox" size="20" name="search" value="'.s($value).'" />';
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

    $cmid = $DB->insert_record("course_modules", $mod);
    rebuild_course_cache($mod->course, true);
    return $cmid;
}

/**
 * Creates missing course section(s) and rebuilds course cache
 *
 * @param int|stdClass $courseorid course id or course object
 * @param int|array $sections list of relative section numbers to create
 * @return bool if there were any sections created
 */
function course_create_sections_if_missing($courseorid, $sections) {
    global $DB;
    if (!is_array($sections)) {
        $sections = array($sections);
    }
    $existing = array_keys(get_fast_modinfo($courseorid)->get_section_info_all());
    if (is_object($courseorid)) {
        $courseorid = $courseorid->id;
    }
    $coursechanged = false;
    foreach ($sections as $sectionnum) {
        if (!in_array($sectionnum, $existing)) {
            $cw = new stdClass();
            $cw->course   = $courseorid;
            $cw->section  = $sectionnum;
            $cw->summary  = '';
            $cw->summaryformat = FORMAT_HTML;
            $cw->sequence = '';
            $id = $DB->insert_record("course_sections", $cw);
            $coursechanged = true;
        }
    }
    if ($coursechanged) {
        rebuild_course_cache($courseorid, true);
    }
    return $coursechanged;
}

/**
 * Adds an existing module to the section
 *
 * Updates both tables {course_sections} and {course_modules}
 *
 * @param int|stdClass $courseorid course id or course object
 * @param int $cmid id of the module already existing in course_modules table
 * @param int $sectionnum relative number of the section (field course_sections.section)
 *     If section does not exist it will be created
 * @param int|stdClass $beforemod id or object with field id corresponding to the module
 *     before which the module needs to be included. Null for inserting in the
 *     end of the section
 * @return int The course_sections ID where the module is inserted
 */
function course_add_cm_to_section($courseorid, $cmid, $sectionnum, $beforemod = null) {
    global $DB, $COURSE;
    if (is_object($beforemod)) {
        $beforemod = $beforemod->id;
    }
    if (is_object($courseorid)) {
        $courseid = $courseorid->id;
    } else {
        $courseid = $courseorid;
    }
    course_create_sections_if_missing($courseorid, $sectionnum);
    // Do not try to use modinfo here, there is no guarantee it is valid!
    $section = $DB->get_record('course_sections', array('course'=>$courseid, 'section'=>$sectionnum), '*', MUST_EXIST);
    $modarray = explode(",", trim($section->sequence));
    if (empty($section->sequence)) {
        $newsequence = "$cmid";
    } else if ($beforemod && ($key = array_keys($modarray, $beforemod))) {
        $insertarray = array($cmid, $beforemod);
        array_splice($modarray, $key[0], 1, $insertarray);
        $newsequence = implode(",", $modarray);
    } else {
        $newsequence = "$section->sequence,$cmid";
    }
    $DB->set_field("course_sections", "sequence", $newsequence, array("id" => $section->id));
    $DB->set_field('course_modules', 'section', $section->id, array('id' => $cmid));
    if (is_object($courseorid)) {
        rebuild_course_cache($courseorid->id, true);
    } else {
        rebuild_course_cache($courseorid, true);
    }
    return $section->id;     // Return course_sections ID that was used.
}

function set_coursemodule_groupmode($id, $groupmode) {
    global $DB;
    $cm = $DB->get_record('course_modules', array('id' => $id), 'id,course,groupmode', MUST_EXIST);
    if ($cm->groupmode != $groupmode) {
        $DB->set_field('course_modules', 'groupmode', $groupmode, array('id' => $cm->id));
        rebuild_course_cache($cm->course, true);
    }
    return ($cm->groupmode != $groupmode);
}

function set_coursemodule_idnumber($id, $idnumber) {
    global $DB;
    $cm = $DB->get_record('course_modules', array('id' => $id), 'id,course,idnumber', MUST_EXIST);
    if ($cm->idnumber != $idnumber) {
        $DB->set_field('course_modules', 'idnumber', $idnumber, array('id' => $cm->id));
        rebuild_course_cache($cm->course, true);
    }
    return ($cm->idnumber != $idnumber);
}

/**
 * Set the visibility of a module and inherent properties.
 *
 * From 2.4 the parameter $prevstateoverrides has been removed, the logic it triggered
 * has been moved to {@link set_section_visible()} which was the only place from which
 * the parameter was used.
 *
 * @param int $id of the module
 * @param int $visible state of the module
 * @return bool false when the module was not found, true otherwise
 */
function set_coursemodule_visible($id, $visible) {
    global $DB, $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    // Trigger developer's attention when using the previously removed argument.
    if (func_num_args() > 2) {
        debugging('Wrong number of arguments passed to set_coursemodule_visible(), $prevstateoverrides
            has been removed.', DEBUG_DEVELOPER);
    }

    if (!$cm = $DB->get_record('course_modules', array('id'=>$id))) {
        return false;
    }

    // Create events and propagate visibility to associated grade items if the value has changed.
    // Only do this if it's changed to avoid accidently overwriting manual showing/hiding of student grades.
    if ($cm->visible == $visible) {
        return true;
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

    // Hide the associated grade items so the teacher doesn't also have to go to the gradebook and hide them there.
    $grade_items = grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$modulename, 'iteminstance'=>$cm->instance, 'courseid'=>$cm->course));
    if ($grade_items) {
        foreach ($grade_items as $grade_item) {
            $grade_item->set_hidden(!$visible);
        }
    }

    // Updating visible and visibleold to keep them in sync. Only changing a section visibility will
    // affect visibleold to allow for an original visibility restore. See set_section_visible().
    $cminfo = new stdClass();
    $cminfo->id = $id;
    $cminfo->visible = $visible;
    $cminfo->visibleold = $visible;
    $DB->update_record('course_modules', $cminfo);

    rebuild_course_cache($cm->course, true);
    return true;
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
    $DB->delete_records('course_modules_avail_fields', array('coursemoduleid' => $cm->id));
    $DB->delete_records('course_completion_criteria', array('moduleinstance' => $cm->id,
                                                            'criteriatype' => COMPLETION_CRITERIA_TYPE_ACTIVITY));

    delete_context(CONTEXT_MODULE, $cm->id);
    $DB->delete_records('course_modules', array('id'=>$cm->id));
    rebuild_course_cache($cm->course, true);
    return true;
}

function delete_mod_from_section($modid, $sectionid) {
    global $DB;

    if ($section = $DB->get_record("course_sections", array("id"=>$sectionid)) ) {

        $modarray = explode(",", $section->sequence);

        if ($key = array_keys ($modarray, $modid)) {
            array_splice($modarray, $key[0], 1);
            $newsequence = implode(",", $modarray);
            $DB->set_field("course_sections", "sequence", $newsequence, array("id"=>$section->id));
            rebuild_course_cache($section->course, true);
            return true;
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
 * @todo MDL-33379 remove this function in 2.5
 */
function move_section($course, $section, $move) {
    debugging('This function will be removed before 2.5 is released please use move_section_to', DEBUG_DEVELOPER);

/// Moves a whole course section up and down within the course
    global $USER;

    if (!$move) {
        return true;
    }

    $sectiondest = $section + $move;

    // compartibility with course formats using field 'numsections'
    $courseformatoptions = course_get_format($course)->get_format_options();
    if (array_key_exists('numsections', $courseformatoptions) &&
            $sectiondest > $courseformatoptions['numsections'] or $sectiondest < 1) {
        return false;
    }

    $retval = move_section_to($course, $section, $sectiondest);
    return $retval;
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

    // compartibility with course formats using field 'numsections'
    $courseformatoptions = course_get_format($course)->get_format_options();
    if ((array_key_exists('numsections', $courseformatoptions) &&
            ($destination > $courseformatoptions['numsections'])) || ($destination < 1)) {
        return false;
    }

    // Get all sections for this course and re-order them (2 of them should now share the same section number)
    if (!$sections = $DB->get_records_menu('course_sections', array('course' => $course->id),
            'section ASC, id ASC', 'id, section')) {
        return false;
    }

    $movedsections = reorder_sections($sections, $section, $destination);

    // Update all sections. Do this in 2 steps to avoid breaking database
    // uniqueness constraint
    $transaction = $DB->start_delegated_transaction();
    foreach ($movedsections as $id => $position) {
        if ($sections[$id] !== $position) {
            $DB->set_field('course_sections', 'section', -$position, array('id' => $id));
        }
    }
    foreach ($movedsections as $id => $position) {
        if ($sections[$id] !== $position) {
            $DB->set_field('course_sections', 'section', $position, array('id' => $id));
        }
    }

    // If we move the highlighted section itself, then just highlight the destination.
    // Adjust the higlighted section location if we move something over it either direction.
    if ($section == $course->marker) {
        course_set_marker($course->id, $destination);
    } elseif ($section > $course->marker && $course->marker >= $destination) {
        course_set_marker($course->id, $course->marker+1);
    } elseif ($section < $course->marker && $course->marker <= $destination) {
        course_set_marker($course->id, $course->marker-1);
    }

    $transaction->allow_commit();
    rebuild_course_cache($course->id, true);
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
            if ($target_position < $origin_position) {
                $append_array[$id] = $position;
                unset($sections[$id]);
            }
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
    global $OUTPUT, $DB;

/// Remove original module from original section
    if (! delete_mod_from_section($mod->id, $mod->section)) {
        echo $OUTPUT->notification("Could not delete module from existing section");
    }

    // if moving to a hidden section then hide module
    if ($mod->section != $section->id) {
        if (!$section->visible && $mod->visible) {
            // Set this in the object because it is sent as a response to ajax calls.
            $mod->visible = 0;
            set_coursemodule_visible($mod->id, 0);
            // Set visibleold to 1 so module will be visible when section is made visible.
            $DB->set_field('course_modules', 'visibleold', 1, array('id' => $mod->id));
        }
        if ($section->visible && !$mod->visible) {
            set_coursemodule_visible($mod->id, $mod->visibleold);
            // Set this in the object because it is sent as a response to ajax calls.
            $mod->visible = $mod->visibleold;
        }
    }

/// Add the module into the new section
    course_add_cm_to_section($section->course, $mod->id, $section->section, $beforemod);
    return true;
}

/**
 * Produces the editing buttons for a module
 *
 * @global core_renderer $OUTPUT
 * @staticvar type $str
 * @param stdClass $mod The module to produce editing buttons for
 * @param bool $absolute_ignored ignored - all links are absolute
 * @param bool $moveselect If true a move seleciton process is used (default true)
 * @param int $indent The current indenting
 * @param int $section The section to link back to
 * @return string XHTML for the editing buttons
 */
function make_editing_buttons(stdClass $mod, $absolute_ignored = true, $moveselect = true, $indent=-1, $section=null) {
    global $CFG, $OUTPUT, $COURSE;

    static $str;

    $coursecontext = context_course::instance($mod->course);
    $modcontext = context_module::instance($mod->id);

    $editcaps = array('moodle/course:manageactivities', 'moodle/course:activityvisibility', 'moodle/role:assign');
    $dupecaps = array('moodle/backup:backuptargetimport', 'moodle/restore:restoretargetimport');

    // no permission to edit anything
    if (!has_any_capability($editcaps, $modcontext) and !has_all_capabilities($dupecaps, $coursecontext)) {
        return false;
    }

    $hasmanageactivities = has_capability('moodle/course:manageactivities', $modcontext);

    if (!isset($str)) {
        $str = new stdClass;
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
        $str->edittitle = get_string('edittitle', 'moodle');
    }

    $baseurl = new moodle_url('/course/mod.php', array('sesskey' => sesskey()));

    if ($section !== null) {
        $baseurl->param('sr', $section);
    }
    $actions = array();

    // AJAX edit title
    if ($mod->modname !== 'label' && $hasmanageactivities && course_ajax_enabled($COURSE)) {
        $actions[] = new action_link(
            new moodle_url($baseurl, array('update' => $mod->id)),
            new pix_icon('t/editstring', $str->edittitle, 'moodle', array('class' => 'iconsmall visibleifjs', 'title' => '')),
            null,
            array('class' => 'editing_title', 'title' => $str->edittitle)
        );
    }

    // leftright
    if ($hasmanageactivities) {
        if (right_to_left()) {   // Exchange arrows on RTL
            $rightarrow = 't/left';
            $leftarrow  = 't/right';
        } else {
            $rightarrow = 't/right';
            $leftarrow  = 't/left';
        }

        if ($indent > 0) {
            $actions[] = new action_link(
                new moodle_url($baseurl, array('id' => $mod->id, 'indent' => '-1')),
                new pix_icon($leftarrow, $str->moveleft, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                null,
                array('class' => 'editing_moveleft', 'title' => $str->moveleft)
            );
        }
        if ($indent >= 0) {
            $actions[] = new action_link(
                new moodle_url($baseurl, array('id' => $mod->id, 'indent' => '1')),
                new pix_icon($rightarrow, $str->moveright, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                null,
                array('class' => 'editing_moveright', 'title' => $str->moveright)
            );
        }
    }

    // move
    if ($hasmanageactivities) {
        if ($moveselect) {
            $actions[] = new action_link(
                new moodle_url($baseurl, array('copy' => $mod->id)),
                new pix_icon('t/move', $str->move, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                null,
                array('class' => 'editing_move', 'title' => $str->move)
            );
        } else {
            $actions[] = new action_link(
                new moodle_url($baseurl, array('id' => $mod->id, 'move' => '-1')),
                new pix_icon('t/up', $str->moveup, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                null,
                array('class' => 'editing_moveup', 'title' => $str->moveup)
            );
            $actions[] = new action_link(
                new moodle_url($baseurl, array('id' => $mod->id, 'move' => '1')),
                new pix_icon('t/down', $str->movedown, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                null,
                array('class' => 'editing_movedown', 'title' => $str->movedown)
            );
        }
    }

    // Update
    if ($hasmanageactivities) {
        $actions[] = new action_link(
            new moodle_url($baseurl, array('update' => $mod->id)),
            new pix_icon('t/edit', $str->update, 'moodle', array('class' => 'iconsmall', 'title' => '')),
            null,
            array('class' => 'editing_update', 'title' => $str->update)
        );
    }

    // Duplicate (require both target import caps to be able to duplicate and backup2 support, see modduplicate.php)
    if (has_all_capabilities($dupecaps, $coursecontext) && plugin_supports('mod', $mod->modname, FEATURE_BACKUP_MOODLE2)) {
        $actions[] = new action_link(
            new moodle_url($baseurl, array('duplicate' => $mod->id)),
            new pix_icon('t/copy', $str->duplicate, 'moodle', array('class' => 'iconsmall', 'title' => '')),
            null,
            array('class' => 'editing_duplicate', 'title' => $str->duplicate)
        );
    }

    // Delete
    if ($hasmanageactivities) {
        $actions[] = new action_link(
            new moodle_url($baseurl, array('delete' => $mod->id)),
            new pix_icon('t/delete', $str->delete, 'moodle', array('class' => 'iconsmall', 'title' => '')),
            null,
            array('class' => 'editing_delete', 'title' => $str->delete)
        );
    }

    // hideshow
    if (has_capability('moodle/course:activityvisibility', $modcontext)) {
        if ($mod->visible) {
            $actions[] = new action_link(
                new moodle_url($baseurl, array('hide' => $mod->id)),
                new pix_icon('t/hide', $str->hide, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                null,
                array('class' => 'editing_hide', 'title' => $str->hide)
            );
        } else {
            $actions[] = new action_link(
                new moodle_url($baseurl, array('show' => $mod->id)),
                new pix_icon('t/show', $str->show, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                null,
                array('class' => 'editing_show', 'title' => $str->show)
            );
        }
    }

    // groupmode
    if ($hasmanageactivities and $mod->groupmode !== false) {
        if ($mod->groupmode == SEPARATEGROUPS) {
            $groupmode = 0;
            $grouptitle = $str->groupsseparate;
            $forcedgrouptitle = $str->forcedgroupsseparate;
            $groupclass = 'editing_groupsseparate';
            $groupimage = 't/groups';
        } else if ($mod->groupmode == VISIBLEGROUPS) {
            $groupmode = 1;
            $grouptitle = $str->groupsvisible;
            $forcedgrouptitle = $str->forcedgroupsvisible;
            $groupclass = 'editing_groupsvisible';
            $groupimage = 't/groupv';
        } else {
            $groupmode = 2;
            $grouptitle = $str->groupsnone;
            $forcedgrouptitle = $str->forcedgroupsnone;
            $groupclass = 'editing_groupsnone';
            $groupimage = 't/groupn';
        }
        if ($mod->groupmodelink) {
            $actions[] = new action_link(
                new moodle_url($baseurl, array('id' => $mod->id, 'groupmode' => $groupmode)),
                new pix_icon($groupimage, $grouptitle, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                null,
                array('class' => $groupclass, 'title' => $grouptitle)
            );
        } else {
            $actions[] = new pix_icon($groupimage, $forcedgrouptitle, 'moodle', array('title' => $forcedgrouptitle, 'class' => 'iconsmall'));
        }
    }

    // Assign
    if (has_capability('moodle/role:assign', $modcontext)){
        $actions[] = new action_link(
            new moodle_url('/'.$CFG->admin.'/roles/assign.php', array('contextid' => $modcontext->id)),
            new pix_icon('t/assignroles', $str->assign, 'moodle', array('class' => 'iconsmall', 'title' => '')),
            null,
            array('class' => 'editing_assign', 'title' => $str->assign)
        );
    }

    // The space added before the <span> is a ugly hack but required to set the CSS property white-space: nowrap
    // and having it to work without attaching the preceding text along with it. Hopefully the refactoring of
    // the course page HTML will allow this to be removed.
    $output = ' ' . html_writer::start_tag('span', array('class' => 'commands'));
    foreach ($actions as $action) {
        if ($action instanceof renderable) {
            $output .= $OUTPUT->render($action);
        } else {
            $output .= $action;
        }
    }
    $output .= html_writer::end_tag('span');
    return $output;
}

/**
 * given a course object with shortname & fullname, this function will
 * truncate the the number of chars allowed and add ... if it was too long
 */
function course_format_name ($course,$max=100) {

    $context = context_course::instance($course->id);
    $shortname = format_string($course->shortname, true, array('context' => $context));
    $fullname = format_string($course->fullname, true, array('context' => context_course::instance($course->id)));
    $str = $shortname.': '. $fullname;
    if (textlib::strlen($str) <= $max) {
        return $str;
    }
    else {
        return textlib::substr($str,0,$max-3).'...';
    }
}

/**
 * Is the user allowed to add this type of module to this course?
 * @param object $course the course settings. Only $course->id is used.
 * @param string $modname the module name. E.g. 'forum' or 'quiz'.
 * @return bool whether the current user is allowed to add this type of module to this course.
 */
function course_allowed_module($course, $modname) {
    if (is_numeric($modname)) {
        throw new coding_exception('Function course_allowed_module no longer
                supports numeric module ids. Please update your code to pass the module name.');
    }

    $capability = 'mod/' . $modname . ':addinstance';
    if (!get_capability_info($capability)) {
        // Debug warning that the capability does not exist, but no more than once per page.
        static $warned = array();
        $archetype = plugin_supports('mod', $modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
        if (!isset($warned[$modname]) && $archetype !== MOD_ARCHETYPE_SYSTEM) {
            debugging('The module ' . $modname . ' does not define the standard capability ' .
                    $capability , DEBUG_DEVELOPER);
            $warned[$modname] = 1;
        }

        // If the capability does not exist, the module can always be added.
        return true;
    }

    $coursecontext = context_course::instance($course->id);
    return has_capability($capability, $coursecontext);
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
    add_to_log(SITEID, "category", "delete", "index.php", "$category->name (ID $category->id)");

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
            if ($showfeedback) {
                echo $OUTPUT->notification("Error moving courses");
            }
            return false;
        }
        if ($showfeedback) {
            echo $OUTPUT->notification(get_string('coursesmovedout', '', format_string($category->name)), 'notifysuccess');
        }
    }

    // move or delete cohorts in this context
    cohort_delete_category($category);

    // now delete anything that may depend on course category context
    grade_course_category_delete($category->id, $newparentid, $showfeedback);
    if (!question_delete_course_category($category, $newparentcat, $showfeedback)) {
        if ($showfeedback) {
            echo $OUTPUT->notification(get_string('errordeletingquestionsfromcategory', 'question', $category), 'notifysuccess');
        }
        return false;
    }

    // finally delete the category and it's context
    $DB->delete_records('course_categories', array('id'=>$category->id));
    delete_context(CONTEXT_COURSECAT, $category->id);
    add_to_log(SITEID, "category", "delete", "index.php", "$category->name (ID $category->id)");

    events_trigger('course_category_deleted', $category);

    if ($showfeedback) {
        echo $OUTPUT->notification(get_string('coursecategorydeleted', '', format_string($category->name)), 'notifysuccess');
    }
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
    $newparent = context_coursecat::instance($category->id);
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
            add_to_log($course->id, "course", "move", "edit.php?id=$course->id", $course->id);

            $context   = context_course::instance($course->id);
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
    add_to_log(SITEID, "category", "hide", "editcategory.php?id=$category->id", $category->id);
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
    add_to_log(SITEID, "category", "show", "editcategory.php?id=$category->id", $category->id);
}

/**
 * Efficiently moves a category - NOTE that this can have
 * a huge impact access-control-wise...
 */
function move_category($category, $newparentcat) {
    global $CFG, $DB;

    $context = context_coursecat::instance($category->id);

    $hidecat = false;
    if (empty($newparentcat->id)) {
        $DB->set_field('course_categories', 'parent', 0, array('id' => $category->id));
        $newparent = context_system::instance();
    } else {
        $DB->set_field('course_categories', 'parent', $newparentcat->id, array('id' => $category->id));
        $newparent = context_coursecat::instance($newparentcat->id);

        if (!$newparentcat->visible and $category->visible) {
            // better hide category when moving into hidden category, teachers may unhide afterwards and the hidden children will be restored properly
            $hidecat = true;
        }
    }

    context_moved($context, $newparent);

    // now make it last in new category
    $DB->set_field('course_categories', 'sortorder', MAX_COURSES_IN_CATEGORY*MAX_COURSE_CATEGORIES, array('id'=>$category->id));

    // Log action.
    add_to_log(SITEID, "category", "move", "editcategory.php?id=$category->id", $category->id);

    // and fix the sortorders
    fix_course_sortorder();

    if ($hidecat) {
        course_category_hide($category);
    }
}

/**
 * Returns the display name of the given section that the course prefers
 *
 * Implementation of this function is provided by course format
 * @see format_base::get_section_name()
 *
 * @param int|stdClass $courseorid The course to get the section name for (object or just course id)
 * @param int|stdClass $section Section object from database or just field course_sections.section
 * @return string Display name that the course format prefers, e.g. "Week 2"
 */
function get_section_name($courseorid, $section) {
    return course_get_format($courseorid)->get_section_name($section);
}

/**
 * Tells if current course format uses sections
 *
 * @param string $format Course format ID e.g. 'weeks' $course->format
 * @return bool
 */
function course_format_uses_sections($format) {
    $course = new stdClass();
    $course->format = $format;
    return course_get_format($course)->uses_sections();
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
    $course = new stdClass();
    $course->format = $format;
    return course_get_format($course)->supports_ajax();
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

    $context = context_course::instance($courseid);

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
    $context = context_course::instance($courseid);

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
    $context = context_course::instance($newcourseid, MUST_EXIST);

    if ($editoroptions) {
        // Save the files used in the summary editor and store
        $data = file_postupdate_standard_editor($data, 'summary', $editoroptions, $context, 'course', 'summary', 0);
        $DB->set_field('course', 'summary', $data->summary, array('id'=>$newcourseid));
        $DB->set_field('course', 'summaryformat', $data->summary_format, array('id'=>$newcourseid));
    }

    // update course format options
    course_get_format($newcourseid)->update_course_format_options($data);

    $course = course_get_format($newcourseid)->get_course();

    // Setup the blocks
    blocks_add_default_course_blocks($course);

    // Create a default section.
    course_create_sections_if_missing($course, 0);

    fix_course_sortorder();

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
 * Create a new course category and marks the context as dirty
 *
 * This function does not set the sortorder for the new category and
 * @see{fix_course_sortorder} should be called after creating a new course
 * category
 *
 * Please note that this function does not verify access control.
 *
 * @param object $category All of the data required for an entry in the course_categories table
 * @return object new course category
 */
function create_course_category($category) {
    global $DB;

    $category->timemodified = time();
    $category->id = $DB->insert_record('course_categories', $category);
    $category = $DB->get_record('course_categories', array('id' => $category->id));

    // We should mark the context as dirty
    $category->context = context_coursecat::instance($category->id);
    $category->context->mark_dirty();

    return $category;
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

    $oldcourse = course_get_format($data->id)->get_course();
    $context   = context_course::instance($oldcourse->id);

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
    // make sure the modinfo cache is reset
    rebuild_course_cache($data->id);

    // update course format options with full course data
    course_get_format($data->id)->update_course_format_options($data, $oldcourse);

    $course = $DB->get_record('course', array('id'=>$data->id));

    if ($movecat) {
        $newparent = context_coursecat::instance($course->category);
        context_moved($context, $newparent);
    }

    fix_course_sortorder();

    // Test for and remove blocks which aren't appropriate anymore
    blocks_remove_inappropriate($course);

    // Save any custom role names.
    save_local_role_names($course->id, $data);

    // update enrol settings
    enrol_course_updated(false, $course, $data);

    add_to_log($course->id, "course", "update", "edit.php?id=$course->id", $course->id);

    // Trigger events
    events_trigger('course_updated', $course);

    if ($oldcourse->format !== $course->format) {
        // Remove all options stored for the previous format
        // We assume that new course format migrated everything it needed watching trigger
        // 'course_updated' and in method format_XXX::update_course_format_options()
        $DB->delete_records('course_format_options',
                array('courseid' => $course->id, 'format' => $oldcourse->format));
    }
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
            AND c.visible = 1) total';
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
            AND c.visible = 1) total';
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

        // Setting the default category if none set.
        if (empty($data->category) || empty($CFG->requestcategoryselection)) {
            $data->category = $CFG->defaultrequestcategory;
        }

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

        $courseconfig = get_config('moodlecourse');

        // Transfer appropriate settings
        $data = clone($this->properties);
        unset($data->id);
        unset($data->reason);
        unset($data->requester);

        // If the category is not set, if the current user does not have the rights to change the category, or if the
        // category does not exist, we set the default category to the course to be approved.
        // The system level is used because the capability moodle/site:approvecourse is based on a system level.
        if (empty($data->category) || !has_capability('moodle/course:changecategory', context_system::instance()) ||
                (!$category = get_course_category($data->category))) {
            $category = get_course_category($CFG->defaultrequestcategory);
        }

        // Set category
        $data->category = $category->id;
        $data->sortorder = $category->sortorder; // place as the first in category

        // Set misc settings
        $data->requested = 1;

        // Apply course default settings
        $data->format             = $courseconfig->format;
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
        $context = context_course::instance($course->id, MUST_EXIST);

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
        $a->name = format_string($course->fullname, true, array('context' => context_course::instance($course->id)));
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
    // $currentcontext could be null, get_context_info_array() will throw an error if this is the case.
    if (isset($currentcontext)) {
        // if above course context ,display all course fomats
        list($currentcontext, $course, $cm) = get_context_info_array($currentcontext->id);
        if ($course->id == SITEID) {
            return array('*'=>get_string('page-x', 'pagetype'));
        }
    }
    return array('*'=>get_string('page-x', 'pagetype'),
        'course-*'=>get_string('page-course-x', 'pagetype'),
        'course-view-*'=>get_string('page-course-view-x', 'pagetype')
    );
}

/**
 * Determine whether course ajax should be enabled for the specified course
 *
 * @param stdClass $course The course to test against
 * @return boolean Whether course ajax is enabled or note
 */
function course_ajax_enabled($course) {
    global $CFG, $PAGE, $SITE;

    // Ajax must be enabled globally
    if (!$CFG->enableajax) {
        return false;
    }

    // The user must be editing for AJAX to be included
    if (!$PAGE->user_is_editing()) {
        return false;
    }

    // Check that the theme suports
    if (!$PAGE->theme->enablecourseajax) {
        return false;
    }

    // Check that the course format supports ajax functionality
    // The site 'format' doesn't have information on course format support
    if ($SITE->id !== $course->id) {
        $courseformatajaxsupport = course_format_ajax_support($course->format);
        if (!$courseformatajaxsupport->capable) {
            return false;
        }
    }

    // All conditions have been met so course ajax should be enabled
    return true;
}

/**
 * Include the relevant javascript and language strings for the resource
 * toolbox YUI module
 *
 * @param integer $id The ID of the course being applied to
 * @param array $usedmodules An array containing the names of the modules in use on the page
 * @param array $enabledmodules An array containing the names of the enabled (visible) modules on this site
 * @param stdClass $config An object containing configuration parameters for ajax modules including:
 *          * resourceurl   The URL to post changes to for resource changes
 *          * sectionurl    The URL to post changes to for section changes
 *          * pageparams    Additional parameters to pass through in the post
 * @return bool
 */
function include_course_ajax($course, $usedmodules = array(), $enabledmodules = null, $config = null) {
    global $PAGE, $SITE;

    // Ensure that ajax should be included
    if (!course_ajax_enabled($course)) {
        return false;
    }

    if (!$config) {
        $config = new stdClass();
    }

    // The URL to use for resource changes
    if (!isset($config->resourceurl)) {
        $config->resourceurl = '/course/rest.php';
    }

    // The URL to use for section changes
    if (!isset($config->sectionurl)) {
        $config->sectionurl = '/course/rest.php';
    }

    // Any additional parameters which need to be included on page submission
    if (!isset($config->pageparams)) {
        $config->pageparams = array();
    }

    // Include toolboxes
    $PAGE->requires->yui_module('moodle-course-toolboxes',
            'M.course.init_resource_toolbox',
            array(array(
                'courseid' => $course->id,
                'ajaxurl' => $config->resourceurl,
                'config' => $config,
            ))
    );
    $PAGE->requires->yui_module('moodle-course-toolboxes',
            'M.course.init_section_toolbox',
            array(array(
                'courseid' => $course->id,
                'format' => $course->format,
                'ajaxurl' => $config->sectionurl,
                'config' => $config,
            ))
    );

    // Include course dragdrop
    if ($course->id != $SITE->id) {
        $PAGE->requires->yui_module('moodle-course-dragdrop', 'M.course.init_section_dragdrop',
            array(array(
                'courseid' => $course->id,
                'ajaxurl' => $config->sectionurl,
                'config' => $config,
            )), null, true);

        $PAGE->requires->yui_module('moodle-course-dragdrop', 'M.course.init_resource_dragdrop',
            array(array(
                'courseid' => $course->id,
                'ajaxurl' => $config->resourceurl,
                'config' => $config,
            )), null, true);
    }

    // Include blocks dragdrop
    $params = array(
        'courseid' => $course->id,
        'pagetype' => $PAGE->pagetype,
        'pagelayout' => $PAGE->pagelayout,
        'subpage' => $PAGE->subpage,
        'regions' => $PAGE->blocks->get_regions(),
    );
    $PAGE->requires->yui_module('moodle-core-blocks', 'M.core_blocks.init_dragdrop', array($params), null, true);

    // Require various strings for the command toolbox
    $PAGE->requires->strings_for_js(array(
            'moveleft',
            'deletechecktype',
            'deletechecktypename',
            'edittitle',
            'edittitleinstructions',
            'show',
            'hide',
            'groupsnone',
            'groupsvisible',
            'groupsseparate',
            'clicktochangeinbrackets',
            'markthistopic',
            'markedthistopic',
            'move',
            'movesection',
        ), 'moodle');

    // Include format-specific strings
    if ($course->id != $SITE->id) {
        $PAGE->requires->strings_for_js(array(
                'showfromothers',
                'hidefromothers',
            ), 'format_' . $course->format);
    }

    // For confirming resource deletion we need the name of the module in question
    foreach ($usedmodules as $module => $modname) {
        $PAGE->requires->string_for_js('pluginname', $module);
    }

    // Load drag and drop upload AJAX.
    dndupload_add_to_course($course, $enabledmodules);

    // Add the module chooser
    $PAGE->requires->yui_module('moodle-course-modchooser',
        'M.course.init_chooser',
        array(array('courseid' => $course->id, 'closeButtonTitle' => get_string('close', 'editor')))
    );
    $PAGE->requires->strings_for_js(array(
            'addresourceoractivity',
            'modchooserenable',
            'modchooserdisable',
    ), 'moodle');

    return true;
}

/**
 * Returns the sorted list of available course formats, filtered by enabled if necessary
 *
 * @param bool $enabledonly return only formats that are enabled
 * @return array array of sorted format names
 */
function get_sorted_course_formats($enabledonly = false) {
    global $CFG;
    $formats = get_plugin_list('format');

    if (!empty($CFG->format_plugins_sortorder)) {
        $order = explode(',', $CFG->format_plugins_sortorder);
        $order = array_merge(array_intersect($order, array_keys($formats)),
                    array_diff(array_keys($formats), $order));
    } else {
        $order = array_keys($formats);
    }
    if (!$enabledonly) {
        return $order;
    }
    $sortedformats = array();
    foreach ($order as $formatname) {
        if (!get_config('format_'.$formatname, 'disabled')) {
            $sortedformats[] = $formatname;
        }
    }
    return $sortedformats;
}

/**
 * The URL to use for the specified course (with section)
 *
 * @param int|stdClass $courseorid The course to get the section name for (either object or just course id)
 * @param int|stdClass $section Section object from database or just field course_sections.section
 *     if omitted the course view page is returned
 * @param array $options options for view URL. At the moment core uses:
 *     'navigation' (bool) if true and section has no separate page, the function returns null
 *     'sr' (int) used by multipage formats to specify to which section to return
 * @return moodle_url The url of course
 */
function course_get_url($courseorid, $section = null, $options = array()) {
    return course_get_format($courseorid)->get_view_url($section, $options);
}
