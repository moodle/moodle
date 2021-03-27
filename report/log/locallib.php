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
 * This file contains functions used by the log reports
 *
 * This files lists the functions that are used during the log report generation.
 *
 * @package    report_log
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if (!defined('REPORT_LOG_MAX_DISPLAY')) {
    define('REPORT_LOG_MAX_DISPLAY', 150); // days
}

require_once(__DIR__.'/lib.php');

/**
 * This function is used to generate and display the log activity graph
 *
 * @global stdClass $CFG
 * @param  stdClass $course course instance
 * @param  int|stdClass    $user id/object of the user whose logs are needed
 * @param  string $typeormode type of logs graph needed (usercourse.png/userday.png) or the mode (today, all).
 * @param  int $date timestamp in GMT (seconds since epoch)
 * @param  string $logreader Log reader.
 * @return void
 */
function report_log_print_graph($course, $user, $typeormode, $date=0, $logreader='') {
    global $CFG, $OUTPUT;

    if (!is_object($user)) {
        $user = core_user::get_user($user);
    }

    $logmanager = get_log_manager();
    $readers = $logmanager->get_readers();

    if (empty($logreader)) {
        $reader = reset($readers);
    } else {
        $reader = $readers[$logreader];
    }
    // If reader is not a sql_internal_table_reader and not legacy store then don't show graph.
    if (!($reader instanceof \core\log\sql_internal_table_reader) && !($reader instanceof logstore_legacy\log\store)) {
        return array();
    }
    $coursecontext = context_course::instance($course->id);

    $a = new stdClass();
    $a->coursename = format_string($course->shortname, true, array('context' => $coursecontext));
    $a->username = fullname($user, true);

    if ($typeormode == 'today' || $typeormode == 'userday.png') {
        $logs = report_log_usertoday_data($course, $user, $date, $logreader);
        $title = get_string("hitsoncoursetoday", "", $a);
    } else if ($typeormode == 'all' || $typeormode == 'usercourse.png') {
        $logs = report_log_userall_data($course, $user, $logreader);
        $title = get_string("hitsoncourse", "", $a);
    }

    if (!empty($CFG->preferlinegraphs)) {
        $chart = new \core\chart_line();
    } else {
        $chart = new \core\chart_bar();
    }

    $series = new \core\chart_series(get_string("hits"), $logs['series']);
    $chart->add_series($series);
    $chart->set_title($title);
    $chart->set_labels($logs['labels']);
    $yaxis = $chart->get_yaxis(0, true);
    $yaxis->set_label(get_string("hits"));
    $yaxis->set_stepsize(max(1, round(max($logs['series']) / 10)));

    echo $OUTPUT->render($chart);
}

/**
 * Select all log records for a given course and user
 *
 * @param int $userid The id of the user as found in the 'user' table.
 * @param int $courseid The id of the course as found in the 'course' table.
 * @param string $coursestart unix timestamp representing course start date and time.
 * @param string $logreader log reader to use.
 * @return array
 */
function report_log_usercourse($userid, $courseid, $coursestart, $logreader = '') {
    global $DB;

    $logmanager = get_log_manager();
    $readers = $logmanager->get_readers();
    if (empty($logreader)) {
        $reader = reset($readers);
    } else {
        $reader = $readers[$logreader];
    }

    // If reader is not a sql_internal_table_reader and not legacy store then return.
    if (!($reader instanceof \core\log\sql_internal_table_reader) && !($reader instanceof logstore_legacy\log\store)) {
        return array();
    }

    $coursestart = (int)$coursestart; // Note: unfortunately pg complains if you use name parameter or column alias in GROUP BY.
    if ($reader instanceof logstore_legacy\log\store) {
        $logtable = 'log';
        $timefield = 'time';
        $coursefield = 'course';
        // Anonymous actions are never logged in legacy log.
        $nonanonymous = '';
    } else {
        $logtable = $reader->get_internal_log_table_name();
        $timefield = 'timecreated';
        $coursefield = 'courseid';
        $nonanonymous = 'AND anonymous = 0';
    }

    $params = array();
    $courseselect = '';
    if ($courseid) {
        $courseselect = "AND $coursefield = :courseid";
        $params['courseid'] = $courseid;
    }
    $params['userid'] = $userid;
    return $DB->get_records_sql("SELECT FLOOR(($timefield - $coursestart)/" . DAYSECS . ") AS day, COUNT(*) AS num
                                   FROM {" . $logtable . "}
                                  WHERE userid = :userid
                                        AND $timefield > $coursestart $courseselect $nonanonymous
                               GROUP BY FLOOR(($timefield - $coursestart)/" . DAYSECS .")", $params);
}

/**
 * Select all log records for a given course, user, and day
 *
 * @param int $userid The id of the user as found in the 'user' table.
 * @param int $courseid The id of the course as found in the 'course' table.
 * @param string $daystart unix timestamp of the start of the day for which the logs needs to be retrived
 * @param string $logreader log reader to use.
 * @return array
 */
function report_log_userday($userid, $courseid, $daystart, $logreader = '') {
    global $DB;
    $logmanager = get_log_manager();
    $readers = $logmanager->get_readers();
    if (empty($logreader)) {
        $reader = reset($readers);
    } else {
        $reader = $readers[$logreader];
    }

    // If reader is not a sql_internal_table_reader and not legacy store then return.
    if (!($reader instanceof \core\log\sql_internal_table_reader) && !($reader instanceof logstore_legacy\log\store)) {
        return array();
    }

    $daystart = (int)$daystart; // Note: unfortunately pg complains if you use name parameter or column alias in GROUP BY.

    if ($reader instanceof logstore_legacy\log\store) {
        $logtable = 'log';
        $timefield = 'time';
        $coursefield = 'course';
        // Anonymous actions are never logged in legacy log.
        $nonanonymous = '';
    } else {
        $logtable = $reader->get_internal_log_table_name();
        $timefield = 'timecreated';
        $coursefield = 'courseid';
        $nonanonymous = 'AND anonymous = 0';
    }
    $params = array('userid' => $userid);

    $courseselect = '';
    if ($courseid) {
        $courseselect = "AND $coursefield = :courseid";
        $params['courseid'] = $courseid;
    }
    return $DB->get_records_sql("SELECT FLOOR(($timefield - $daystart)/" . HOURSECS . ") AS hour, COUNT(*) AS num
                                   FROM {" . $logtable . "}
                                  WHERE userid = :userid
                                        AND $timefield > $daystart $courseselect $nonanonymous
                               GROUP BY FLOOR(($timefield - $daystart)/" . HOURSECS . ") ", $params);
}

/**
 * This function is used to generate and display Mnet selector form
 *
 * @global stdClass $USER
 * @global stdClass $CFG
 * @global stdClass $SITE
 * @global moodle_database $DB
 * @global core_renderer $OUTPUT
 * @global stdClass $SESSION
 * @uses CONTEXT_SYSTEM
 * @uses COURSE_MAX_COURSES_PER_DROPDOWN
 * @uses CONTEXT_COURSE
 * @uses SEPARATEGROUPS
 * @param  int      $hostid host id
 * @param  stdClass $course course instance
 * @param  int      $selecteduser id of the selected user
 * @param  string   $selecteddate Date selected
 * @param  string   $modname course_module->id
 * @param  string   $modid number or 'site_errors'
 * @param  string   $modaction an action as recorded in the logs
 * @param  int      $selectedgroup Group to display
 * @param  int      $showcourses whether to show courses if we're over our limit.
 * @param  int      $showusers whether to show users if we're over our limit.
 * @param  string   $logformat Format of the logs (downloadascsv, showashtml, downloadasods, downloadasexcel)
 * @return void
 */
function report_log_print_mnet_selector_form($hostid, $course, $selecteduser=0, $selecteddate='today',
                                 $modname="", $modid=0, $modaction='', $selectedgroup=-1, $showcourses=0, $showusers=0, $logformat='showashtml') {

    global $USER, $CFG, $SITE, $DB, $OUTPUT, $SESSION;
    require_once $CFG->dirroot.'/mnet/peer.php';

    $mnet_peer = new mnet_peer();
    $mnet_peer->set_id($hostid);

    $sql = "SELECT DISTINCT course, hostid, coursename FROM {mnet_log}";
    $courses = $DB->get_records_sql($sql);
    $remotecoursecount = count($courses);

    // first check to see if we can override showcourses and showusers
    $numcourses = $remotecoursecount + $DB->count_records('course');
    if ($numcourses < COURSE_MAX_COURSES_PER_DROPDOWN && !$showcourses) {
        $showcourses = 1;
    }

    $sitecontext = context_system::instance();

    // Context for remote data is always SITE
    // Groups for remote data are always OFF
    if ($hostid == $CFG->mnet_localhost_id) {
        $context = context_course::instance($course->id);

        /// Setup for group handling.
        if ($course->groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
            $selectedgroup = -1;
            $showgroups = false;
        } else if ($course->groupmode) {
            $showgroups = true;
        } else {
            $selectedgroup = 0;
            $showgroups = false;
        }

        if ($selectedgroup === -1) {
            if (isset($SESSION->currentgroup[$course->id])) {
                $selectedgroup =  $SESSION->currentgroup[$course->id];
            } else {
                $selectedgroup = groups_get_all_groups($course->id, $USER->id);
                if (is_array($selectedgroup)) {
                    $selectedgroup = array_shift(array_keys($selectedgroup));
                    $SESSION->currentgroup[$course->id] = $selectedgroup;
                } else {
                    $selectedgroup = 0;
                }
            }
        }

    } else {
        $context = $sitecontext;
    }

    // Get all the possible users
    $users = array();

    // Define limitfrom and limitnum for queries below
    // If $showusers is enabled... don't apply limitfrom and limitnum
    $limitfrom = empty($showusers) ? 0 : '';
    $limitnum  = empty($showusers) ? COURSE_MAX_USERS_PER_DROPDOWN + 1 : '';

    // If looking at a different host, we're interested in all our site users
    if ($hostid == $CFG->mnet_localhost_id && $course->id != SITEID) {
        $userfieldsapi = \core_user\fields::for_name();
        $courseusers = get_enrolled_users($context, '', $selectedgroup, 'u.id, ' .
                $userfieldsapi->get_sql('u', false, '', '', false)->selects,
                null, $limitfrom, $limitnum);
    } else {
        // this may be a lot of users :-(
        $userfieldsapi = \core_user\fields::for_name();
        $courseusers = $DB->get_records('user', array('deleted' => 0), 'lastaccess DESC', 'id, ' .
                $userfieldsapi->get_sql('', false, '', '', false)->selects,
                $limitfrom, $limitnum);
    }

    if (count($courseusers) < COURSE_MAX_USERS_PER_DROPDOWN && !$showusers) {
        $showusers = 1;
    }

    if ($showusers) {
        if ($courseusers) {
            foreach ($courseusers as $courseuser) {
                $users[$courseuser->id] = fullname($courseuser, has_capability('moodle/site:viewfullnames', $context));
            }
        }
        $users[$CFG->siteguest] = get_string('guestuser');
    }

    // Get all the hosts that have log records
    $sql = "select distinct
                h.id,
                h.name
            from
                {mnet_host} h,
                {mnet_log} l
            where
                h.id = l.hostid
            order by
                h.name";

    if ($hosts = $DB->get_records_sql($sql)) {
        foreach($hosts as $host) {
            $hostarray[$host->id] = $host->name;
        }
    }

    $hostarray[$CFG->mnet_localhost_id] = $SITE->fullname;
    asort($hostarray);

    $dropdown = array();

    foreach($hostarray as $hostid => $name) {
        $courses = array();
        $sites = array();
        if ($CFG->mnet_localhost_id == $hostid) {
            if (has_capability('report/log:view', $sitecontext) && $showcourses) {
                if ($ccc = $DB->get_records("course", null, "fullname","id,shortname,fullname,category")) {
                    foreach ($ccc as $cc) {
                        if ($cc->id == SITEID) {
                            $sites["$hostid/$cc->id"]   = format_string($cc->fullname).' ('.get_string('site').')';
                        } else {
                            $courses["$hostid/$cc->id"] = format_string(get_course_display_name_for_list($cc));
                        }
                    }
                }
            }
        } else {
            if (has_capability('report/log:view', $sitecontext) && $showcourses) {
                $sql = "SELECT DISTINCT course, coursename FROM {mnet_log} where hostid = ?";
                if ($ccc = $DB->get_records_sql($sql, array($hostid))) {
                    foreach ($ccc as $cc) {
                        if (1 == $cc->course) { // TODO: this might be wrong - site course may have another id
                            $sites["$hostid/$cc->course"]   = $cc->coursename.' ('.get_string('site').')';
                        } else {
                            $courses["$hostid/$cc->course"] = $cc->coursename;
                        }
                    }
                }
            }
        }

        asort($courses);
        $dropdown[] = array($name=>($sites + $courses));
    }


    $activities = array();
    $selectedactivity = "";

    $modinfo = get_fast_modinfo($course);
    if (!empty($modinfo->cms)) {
        $section = 0;
        $thissection = array();
        foreach ($modinfo->cms as $cm) {
            // Exclude activities that aren't visible or have no view link (e.g. label). Account for folder being displayed inline.
            if (!$cm->uservisible || (!$cm->has_view() && strcmp($cm->modname, 'folder') !== 0)) {
                continue;
            }
            if ($cm->sectionnum > 0 and $section <> $cm->sectionnum) {
                $activities[] = $thissection;
                $thissection = array();
            }
            $section = $cm->sectionnum;
            $modname = strip_tags($cm->get_formatted_name());
            if (core_text::strlen($modname) > 55) {
                $modname = core_text::substr($modname, 0, 50)."...";
            }
            if (!$cm->visible) {
                $modname = "(".$modname.")";
            }
            $key = get_section_name($course, $cm->sectionnum);
            if (!isset($thissection[$key])) {
                $thissection[$key] = array();
            }
            $thissection[$key][$cm->id] = $modname;

            if ($cm->id == $modid) {
                $selectedactivity = "$cm->id";
            }
        }
        if (!empty($thissection)) {
            $activities[] = $thissection;
        }
    }

    if (has_capability('report/log:view', $sitecontext) && !$course->category) {
        $activities["site_errors"] = get_string("siteerrors");
        if ($modid === "site_errors") {
            $selectedactivity = "site_errors";
        }
    }

    $strftimedate = get_string("strftimedate");
    $strftimedaydate = get_string("strftimedaydate");

    asort($users);

    // Prepare the list of action options.
    $actions = array(
        'view' => get_string('view'),
        'add' => get_string('add'),
        'update' => get_string('update'),
        'delete' => get_string('delete'),
        '-view' => get_string('allchanges')
    );

    // Get all the possible dates
    // Note that we are keeping track of real (GMT) time and user time
    // User time is only used in displays - all calcs and passing is GMT

    $timenow = time(); // GMT

    // What day is it now for the user, and when is midnight that day (in GMT).
    $timemidnight = $today = usergetmidnight($timenow);

    // Put today up the top of the list
    $dates = array(
        "0" => get_string('alldays'),
        "$timemidnight" => get_string("today").", ".userdate($timenow, $strftimedate)
    );

    if (!$course->startdate or ($course->startdate > $timenow)) {
        $course->startdate = $course->timecreated;
    }

    $numdates = 1;
    while ($timemidnight > $course->startdate and $numdates < 365) {
        $timemidnight = $timemidnight - 86400;
        $timenow = $timenow - 86400;
        $dates["$timemidnight"] = userdate($timenow, $strftimedaydate);
        $numdates++;
    }

    if ($selecteddate === "today") {
        $selecteddate = $today;
    }

    echo "<form class=\"logselectform\" action=\"$CFG->wwwroot/report/log/index.php\" method=\"get\">\n";
    echo "<div>\n";//invisible fieldset here breaks wrapping
    echo "<input type=\"hidden\" name=\"chooselog\" value=\"1\" />\n";
    echo "<input type=\"hidden\" name=\"showusers\" value=\"$showusers\" />\n";
    echo "<input type=\"hidden\" name=\"showcourses\" value=\"$showcourses\" />\n";
    if (has_capability('report/log:view', $sitecontext) && $showcourses) {
        $cid = empty($course->id)? '1' : $course->id;
        echo html_writer::label(get_string('selectacoursesite'), 'menuhost_course', false, array('class' => 'accesshide'));
        echo html_writer::select($dropdown, "host_course", $hostid.'/'.$cid);
    } else {
        $courses = array();
        $courses[$course->id] = get_course_display_name_for_list($course) . ((empty($course->category)) ? ' ('.get_string('site').') ' : '');
        echo html_writer::label(get_string('selectacourse'), 'menuid', false, array('class' => 'accesshide'));
        echo html_writer::select($courses,"id",$course->id, false);
        if (has_capability('report/log:view', $sitecontext)) {
            $a = new stdClass();
            $a->url = "$CFG->wwwroot/report/log/index.php?chooselog=0&group=$selectedgroup&user=$selecteduser"
                ."&id=$course->id&date=$selecteddate&modid=$selectedactivity&showcourses=1&showusers=$showusers";
            print_string('logtoomanycourses','moodle',$a);
        }
    }

    if ($showgroups) {
        if ($cgroups = groups_get_all_groups($course->id)) {
            foreach ($cgroups as $cgroup) {
                $groups[$cgroup->id] = $cgroup->name;
            }
        }
        else {
            $groups = array();
        }
        echo html_writer::label(get_string('selectagroup'), 'menugroup', false, array('class' => 'accesshide'));
        echo html_writer::select($groups, "group", $selectedgroup, get_string("allgroups"));
    }

    if ($showusers) {
        echo html_writer::label(get_string('participantslist'), 'menuuser', false, array('class' => 'accesshide'));
        echo html_writer::select($users, "user", $selecteduser, get_string("allparticipants"));
    }
    else {
        $users = array();
        if (!empty($selecteduser)) {
            $user = $DB->get_record('user', array('id'=>$selecteduser));
            $users[$selecteduser] = fullname($user);
        }
        else {
            $users[0] = get_string('allparticipants');
        }
        echo html_writer::label(get_string('participantslist'), 'menuuser', false, array('class' => 'accesshide'));
        echo html_writer::select($users, "user", $selecteduser, false);
        $a = new stdClass();
        $a->url = "$CFG->wwwroot/report/log/index.php?chooselog=0&group=$selectedgroup&user=$selecteduser"
            ."&id=$course->id&date=$selecteddate&modid=$selectedactivity&showusers=1&showcourses=$showcourses";
        print_string('logtoomanyusers','moodle',$a);
    }

    echo html_writer::label(get_string('date'), 'menudate', false, array('class' => 'accesshide'));
    echo html_writer::select($dates, "date", $selecteddate, false);
    echo html_writer::label(get_string('showreports'), 'menumodid', false, array('class' => 'accesshide'));
    echo html_writer::select($activities, "modid", $selectedactivity, get_string("allactivities"));
    echo html_writer::label(get_string('actions'), 'menumodaction', false, array('class' => 'accesshide'));
    echo html_writer::select($actions, 'modaction', $modaction, get_string("allactions"));

    $logformats = array('showashtml' => get_string('displayonpage'),
                        'downloadascsv' => get_string('downloadtext'),
                        'downloadasods' => get_string('downloadods'),
                        'downloadasexcel' => get_string('downloadexcel'));
    echo html_writer::label(get_string('logsformat', 'report_log'), 'menulogformat', false, array('class' => 'accesshide'));
    echo html_writer::select($logformats, 'logformat', $logformat, false);
    echo '<input type="submit" value="'.get_string('gettheselogs').'" />';
    echo '</div>';
    echo '</form>';
}

/**
 * Fetch logs since the start of the courses and structure in series and labels to be sent to Chart API.
 *
 * @param stdClass $course the course object
 * @param stdClass $user user object
 * @param string $logreader the log reader where the logs are.
 * @return array structured array to be sent to chart API, split in two indexes (series and labels).
 */
function report_log_userall_data($course, $user, $logreader) {
    global $CFG;
    $site = get_site();
    $timenow = time();
    $logs = [];
    if ($course->id == $site->id) {
        $courseselect = 0;
    } else {
        $courseselect = $course->id;
    }

    $maxseconds = REPORT_LOG_MAX_DISPLAY * 3600 * 24;  // Seconds.
    if ($timenow - $course->startdate > $maxseconds) {
        $course->startdate = $timenow - $maxseconds;
    }

    if (!empty($CFG->loglifetime)) {
        $maxseconds = $CFG->loglifetime * 3600 * 24;  // Seconds.
        if ($timenow - $course->startdate > $maxseconds) {
            $course->startdate = $timenow - $maxseconds;
        }
    }

    $timestart = $coursestart = usergetmidnight($course->startdate);

    $i = 0;
    $logs['series'][$i] = 0;
    $logs['labels'][$i] = 0;
    while ($timestart < $timenow) {
        $timefinish = $timestart + 86400;
        $logs['labels'][$i] = userdate($timestart, "%a %d %b");
        $logs['series'][$i] = 0;
        $i++;
        $timestart = $timefinish;
    }
    $rawlogs = report_log_usercourse($user->id, $courseselect, $coursestart, $logreader);

    foreach ($rawlogs as $rawlog) {
        if (isset($logs['labels'][$rawlog->day])) {
            $logs['series'][$rawlog->day] = $rawlog->num;
        }
    }

    return $logs;
}

/**
 * Fetch logs of the current day and structure in series and labels to be sent to Chart API.
 *
 * @param stdClass $course the course object
 * @param stdClass $user user object
 * @param int $date A time of a day (in GMT).
 * @param string $logreader the log reader where the logs are.
 * @return array $logs structured array to be sent to chart API, split in two indexes (series and labels).
 */
function report_log_usertoday_data($course, $user, $date, $logreader) {
    $site = get_site();
    $logs = [];

    if ($course->id == $site->id) {
        $courseselect = 0;
    } else {
        $courseselect = $course->id;
    }

    if ($date) {
        $daystart = usergetmidnight($date);
    } else {
        $daystart = usergetmidnight(time());
    }

    for ($i = 0; $i <= 23; $i++) {
        $hour = $daystart + $i * 3600;
        $logs['series'][$i] = 0;
        $logs['labels'][$i] = userdate($hour, "%H:00");
    }

    $rawlogs = report_log_userday($user->id, $courseselect, $daystart, $logreader);

    foreach ($rawlogs as $rawlog) {
        if (isset($logs['labels'][$rawlog->hour])) {
            $logs['series'][$rawlog->hour] = $rawlog->num;
        }
    }

    return $logs;
}
