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

require_once(dirname(__FILE__).'/lib.php');

/**
 * This function is used to generate and display the log activity graph
 *
 * @global stdClass $CFG
 * @param  stdClass $course course instance
 * @param  int    $userid id of the user whose logs are needed
 * @param  string $type type of logs graph needed (usercourse.png/userday.png)
 * @param  int    $date timestamp in GMT (seconds since epoch)
 * @return void
 */
function report_log_print_graph($course, $userid, $type, $date=0) {
    global $CFG;

    if (empty($CFG->gdversion)) {
        echo "(".get_string("gdneed").")";
    } else {
        echo '<img src="'.$CFG->wwwroot.'/report/log/graph.php?id='.$course->id.
             '&amp;user='.$userid.'&amp;type='.$type.'&amp;date='.$date.'" alt="" />';
    }
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
        $courseusers = get_enrolled_users($context, '', $selectedgroup, 'u.id, u.firstname, u.lastname, u.idnumber', null, $limitfrom, $limitnum);
    } else {
        // this may be a lot of users :-(
        $courseusers = $DB->get_records('user', array('deleted'=>0), 'lastaccess DESC', 'id, firstname, lastname, idnumber', $limitfrom, $limitnum);
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

/// Casting $course->modinfo to string prevents one notice when the field is null
    if ($modinfo = unserialize((string)$course->modinfo)) {
        $section = 0;
        foreach ($modinfo as $mod) {
            if ($mod->mod == "label") {
                continue;
            }
            if ($mod->section > 0 and $section <> $mod->section) {
                $activities["section/$mod->section"] = '--- '.get_section_name($course, $mod->section).' ---';
            }
            $section = $mod->section;
            $mod->name = strip_tags(format_string($mod->name, true));
            if (textlib::strlen($mod->name) > 55) {
                $mod->name = textlib::substr($mod->name, 0, 50)."...";
            }
            if (!$mod->visible) {
                $mod->name = "(".$mod->name.")";
            }
            $activities["$mod->cm"] = $mod->name;

            if ($mod->cm == $modid) {
                $selectedactivity = "$mod->cm";
            }
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
 * This function is used to generate and display selector form
 *
 * @global stdClass $USER
 * @global stdClass $CFG
 * @global moodle_database $DB
 * @global core_renderer $OUTPUT
 * @global stdClass $SESSION
 * @uses CONTEXT_SYSTEM
 * @uses COURSE_MAX_COURSES_PER_DROPDOWN
 * @uses CONTEXT_COURSE
 * @uses SEPARATEGROUPS
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
function report_log_print_selector_form($course, $selecteduser=0, $selecteddate='today',
                                 $modname="", $modid=0, $modaction='', $selectedgroup=-1, $showcourses=0, $showusers=0, $logformat='showashtml') {

    global $USER, $CFG, $DB, $OUTPUT, $SESSION;

    // first check to see if we can override showcourses and showusers
    $numcourses =  $DB->count_records("course");
    if ($numcourses < COURSE_MAX_COURSES_PER_DROPDOWN && !$showcourses) {
        $showcourses = 1;
    }

    $sitecontext = context_system::instance();
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

    // Get all the possible users
    $users = array();

    // Define limitfrom and limitnum for queries below
    // If $showusers is enabled... don't apply limitfrom and limitnum
    $limitfrom = empty($showusers) ? 0 : '';
    $limitnum  = empty($showusers) ? COURSE_MAX_USERS_PER_DROPDOWN + 1 : '';

    $courseusers = get_enrolled_users($context, '', $selectedgroup, 'u.id, u.firstname, u.lastname', null, $limitfrom, $limitnum);

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

    if (has_capability('report/log:view', $sitecontext) && $showcourses) {
        if ($ccc = $DB->get_records("course", null, "fullname", "id,shortname,fullname,category")) {
            foreach ($ccc as $cc) {
                if ($cc->category) {
                    $courses["$cc->id"] = format_string(get_course_display_name_for_list($cc));
                } else {
                    $courses["$cc->id"] = format_string($cc->fullname) . ' (Site)';
                }
            }
        }
        asort($courses);
    }

    $activities = array();
    $selectedactivity = "";

/// Casting $course->modinfo to string prevents one notice when the field is null
    if ($modinfo = unserialize((string)$course->modinfo)) {
        $section = 0;
        foreach ($modinfo as $mod) {
            if ($mod->mod == "label") {
                continue;
            }
            if ($mod->section > 0 and $section <> $mod->section) {
                $activities["section/$mod->section"] = '--- '.get_section_name($course, $mod->section).' ---';
            }
            $section = $mod->section;
            $mod->name = strip_tags(format_string($mod->name, true));
            if (textlib::strlen($mod->name) > 55) {
                $mod->name = textlib::substr($mod->name, 0, 50)."...";
            }
            if (!$mod->visible) {
                $mod->name = "(".$mod->name.")";
            }
            $activities["$mod->cm"] = $mod->name;

            if ($mod->cm == $modid) {
                $selectedactivity = "$mod->cm";
            }
        }
    }

    if (has_capability('report/log:view', $sitecontext) && ($course->id == SITEID)) {
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
    $dates = array("$timemidnight" => get_string("today").", ".userdate($timenow, $strftimedate) );

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

    if ($selecteddate == "today") {
        $selecteddate = $today;
    }

    echo "<form class=\"logselectform\" action=\"$CFG->wwwroot/report/log/index.php\" method=\"get\">\n";
    echo "<div>\n";
    echo "<input type=\"hidden\" name=\"chooselog\" value=\"1\" />\n";
    echo "<input type=\"hidden\" name=\"showusers\" value=\"$showusers\" />\n";
    echo "<input type=\"hidden\" name=\"showcourses\" value=\"$showcourses\" />\n";
    if (has_capability('report/log:view', $sitecontext) && $showcourses) {
        echo html_writer::label(get_string('selectacourse'), 'menuid', false, array('class' => 'accesshide'));
        echo html_writer::select($courses, "id", $course->id, false);
    } else {
        //        echo '<input type="hidden" name="id" value="'.$course->id.'" />';
        $courses = array();
        $courses[$course->id] = get_course_display_name_for_list($course) . (($course->id == SITEID) ? ' ('.get_string('site').') ' : '');
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
        echo html_writer::label(get_string('selctauser'), 'menuuser', false, array('class' => 'accesshide'));
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
        echo html_writer::label(get_string('selctauser'), 'menuuser', false, array('class' => 'accesshide'));
        echo html_writer::select($users, "user", $selecteduser, false);
        $a = new stdClass();
        $a->url = "$CFG->wwwroot/report/log/index.php?chooselog=0&group=$selectedgroup&user=$selecteduser"
            ."&id=$course->id&date=$selecteddate&modid=$selectedactivity&showusers=1&showcourses=$showcourses";
        print_string('logtoomanyusers','moodle',$a);
    }
    echo html_writer::label(get_string('date'), 'menudate', false, array('class' => 'accesshide'));
    echo html_writer::select($dates, "date", $selecteddate, get_string("alldays"));

    echo html_writer::label(get_string('activities'), 'menumodid', false, array('class' => 'accesshide'));
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
