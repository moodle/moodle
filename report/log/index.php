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
 * Displays different views of the logs.
 *
 * @package    report_log
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/report/log/locallib.php');
require_once($CFG->libdir.'/adminlib.php');

$id          = optional_param('id', 0, PARAM_INT);// Course ID
$host_course = optional_param('host_course', '', PARAM_PATH);// Course ID

if (empty($host_course)) {
    $hostid = $CFG->mnet_localhost_id;
    if (empty($id)) {
        $site = get_site();
        $id = $site->id;
    }
} else {
    list($hostid, $id) = explode('/', $host_course);
}

$group       = optional_param('group', 0, PARAM_INT); // Group to display
$user        = optional_param('user', 0, PARAM_INT); // User to display
$date        = optional_param('date', 0, PARAM_INT); // Date to display
$modname     = optional_param('modname', '', PARAM_PLUGIN); // course_module->id
$modid       = optional_param('modid', 0, PARAM_FILE); // number or 'site_errors'
$modaction   = optional_param('modaction', '', PARAM_PATH); // an action as recorded in the logs
$page        = optional_param('page', '0', PARAM_INT);     // which page to show
$perpage     = optional_param('perpage', '100', PARAM_INT); // how many per page
$showcourses = optional_param('showcourses', 0, PARAM_INT); // whether to show courses if we're over our limit.
$showusers   = optional_param('showusers', 0, PARAM_INT); // whether to show users if we're over our limit.
$chooselog   = optional_param('chooselog', 0, PARAM_INT);
$logformat   = optional_param('logformat', 'showashtml', PARAM_ALPHA);

$params = array();
if ($id !== 0) {
    $params['id'] = $id;
}
if ($host_course !== '') {
    $params['host_course'] = $host_course;
}
if ($group !== 0) {
    $params['group'] = $group;
}
if ($user !== 0) {
    $params['user'] = $user;
}
if ($date !== 0) {
    $params['date'] = $date;
}
if ($modname !== '') {
    $params['modname'] = $modname;
}
if ($modid !== 0) {
    $params['modid'] = $modid;
}
if ($modaction !== '') {
    $params['modaction'] = $modaction;
}
if ($page !== '0') {
    $params['page'] = $page;
}
if ($perpage !== '100') {
    $params['perpage'] = $perpage;
}
if ($showcourses !== 0) {
    $params['showcourses'] = $showcourses;
}
if ($showusers !== 0) {
    $params['showusers'] = $showusers;
}
if ($chooselog !== 0) {
    $params['chooselog'] = $chooselog;
}
if ($logformat !== 'showashtml') {
    $params['logformat'] = $logformat;
}
$PAGE->set_url('/report/log/index.php', $params);
$PAGE->set_pagelayout('report');

if ($hostid == $CFG->mnet_localhost_id) {
    $course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

} else {
    $course_stub       = $DB->get_record('mnet_log', array('hostid'=>$hostid, 'course'=>$id), '*', true);
    $course->id        = $id;
    $course->shortname = $course_stub->coursename;
    $course->fullname  = $course_stub->coursename;
}

require_login($course);

$context = context_course::instance($course->id);

require_capability('report/log:view', $context);

add_to_log($course->id, "course", "report log", "report/log/index.php?id=$course->id", $course->id);

if (!empty($page)) {
    $strlogs = get_string('logs'). ": ". get_string('page', 'report_log', $page+1);
} else {
    $strlogs = get_string('logs');
}
$stradministration = get_string('administration');
$strreports = get_string('reports');

// Before we close session, make sure we have editing information in session.
$adminediting = optional_param('adminedit', -1, PARAM_BOOL);
if ($PAGE->user_allowed_editing() && $adminediting != -1) {
    $USER->editing = $adminediting;
}
session_get_instance()->write_close();

if (!empty($chooselog)) {
    $userinfo = get_string('allparticipants');
    $dateinfo = get_string('alldays');

    if ($user) {
        $u = $DB->get_record('user', array('id'=>$user, 'deleted'=>0), '*', MUST_EXIST);
        $userinfo = fullname($u, has_capability('moodle/site:viewfullnames', $context));
    }
    if ($date) {
        $dateinfo = userdate($date, get_string('strftimedaydate'));
    }

    switch ($logformat) {
        case 'showashtml':
            if ($hostid != $CFG->mnet_localhost_id || $course->id == SITEID) {
                admin_externalpage_setup('reportlog');
                $PAGE->set_title($course->shortname .': '. $strlogs);
                echo $OUTPUT->header();

            } else {
                $PAGE->set_title($course->shortname .': '. $strlogs);
                $PAGE->set_heading($course->fullname);
                $PAGE->navbar->add("$userinfo, $dateinfo");
                echo $OUTPUT->header();
            }

            echo $OUTPUT->heading(format_string($course->fullname) . ": $userinfo, $dateinfo (".usertimezone().")");
            report_log_print_mnet_selector_form($hostid, $course, $user, $date, $modname, $modid, $modaction, $group, $showcourses, $showusers, $logformat);

            if ($hostid == $CFG->mnet_localhost_id) {
                print_log($course, $user, $date, 'l.time DESC', $page, $perpage,
                        "index.php?id=$course->id&amp;chooselog=1&amp;user=$user&amp;date=$date&amp;modid=$modid&amp;modaction=$modaction&amp;group=$group",
                        $modname, $modid, $modaction, $group);
            } else {
                print_mnet_log($hostid, $id, $user, $date, 'l.time DESC', $page, $perpage, "", $modname, $modid, $modaction, $group);
            }
            break;
        case 'downloadascsv':
            if (!print_log_csv($course, $user, $date, 'l.time DESC', $modname,
                    $modid, $modaction, $group)) {
                echo $OUTPUT->notification("No logs found!");
                echo $OUTPUT->footer();
            }
            exit;
        case 'downloadasods':
            if (!print_log_ods($course, $user, $date, 'l.time DESC', $modname,
                    $modid, $modaction, $group)) {
                echo $OUTPUT->notification("No logs found!");
                echo $OUTPUT->footer();
            }
            exit;
        case 'downloadasexcel':
            if (!print_log_xls($course, $user, $date, 'l.time DESC', $modname,
                    $modid, $modaction, $group)) {
                echo $OUTPUT->notification("No logs found!");
                echo $OUTPUT->footer();
            }
            exit;
    }


} else {
    if ($hostid != $CFG->mnet_localhost_id || $course->id == SITEID) {
        admin_externalpage_setup('reportlog', '', null, '', array('pagelayout'=>'report'));
        echo $OUTPUT->header();
    } else {
        $PAGE->set_title($course->shortname .': '. $strlogs);
        $PAGE->set_heading($course->fullname);
        echo $OUTPUT->header();
    }

    echo $OUTPUT->heading(get_string('chooselogs') .':');

    report_log_print_selector_form($course, $user, $date, $modname, $modid, $modaction, $group, $showcourses, $showusers, $logformat);
}

echo $OUTPUT->footer();

