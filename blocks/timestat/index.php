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
 *
 * Displays different views of the logs.
 *
 * @package    block_timestat
 * @copyright  2014 Barbara Dębska, Łukasz Sanokowski, Łukasz Musiał
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');
global $CFG;
require_once($CFG->dirroot . '/blocks/timestat/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

$id = optional_param('id', 0, PARAM_INT);
$hostcourse = optional_param('host_course', '', PARAM_PATH);

if (empty($hostcourse)) {
    $hostid = $CFG->mnet_localhost_id;
    if (empty($id)) {
        $site = get_site();
        $id = $site->id;
    }
} else {
    list($hostid, $id) = explode('/', $hostcourse);
}

$group = optional_param('group', 0, PARAM_INT);
$user = optional_param('user', 0, PARAM_INT);

$datefromget = optional_param_array('datefrom', 0, PARAM_INT);

if (is_int($datefromget)) {
    $datefrom = $datefromget;
} else {
    $datefromyear = (int) $datefromget['year'];
    $datefrommonth = (int) $datefromget['month'];
    $datefromday = (int) $datefromget['day'];
    $datefromhour = (int) $datefromget['hour'];
    $datefromminute = (int) $datefromget['minute'];
    $datefrom = strtotime("$datefromyear-$datefrommonth-$datefromday $datefromhour:$datefromminute:00");
}

$datetoget = optional_param_array('dateto', 0, PARAM_INT);
if (is_int($datetoget)) {
    $dateto = $datetoget;
} else {
    $datetoyear = (int) $datetoget['year'];
    $datetomonth = (int) $datetoget['month'];
    $datetoday = (int) $datetoget['day'];
    $datetohour = (int) $datetoget['hour'];
    $datetominute = (int) $datetoget['minute'];
    $dateto = strtotime("$datetoyear-$datetomonth-$datetoday $datetohour:$datetominute:00");
}

$modname = optional_param('modname', '', PARAM_PLUGIN);
$modid = optional_param('modid', 0, PARAM_FILE);
$modaction = optional_param('modaction', '', PARAM_PATH);
$page = optional_param('page', '0', PARAM_INT);
$perpage = optional_param('perpage', '100', PARAM_INT);
$showcourses = optional_param('showcourses', 0, PARAM_INT);
$showusers = optional_param('showusers', 0, PARAM_INT);
$chooselog = optional_param('chooselog', 0, PARAM_INT);
$logformat = optional_param('logformat', 'showashtml', PARAM_ALPHA);

$params = array();
if ($id !== 0) {
    $params['id'] = $id;
}
if ($hostcourse !== '') {
    $params['host_course'] = $hostcourse;
}
if ($group !== 0) {
    $params['group'] = $group;
}
if ($user !== 0) {
    $params['user'] = $user;
}
if ($datefrom !== 0) {
    $params['datefrom'] = $datefrom;
}

if ($dateto !== 0) {
    $params['dateto'] = $dateto;
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
$PAGE->set_url('/block/timestat/index.php', $params);
$PAGE->set_pagelayout('report');

if ($hostid == $CFG->mnet_localhost_id) {
    $course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

} else {
    $coursestub = $DB->get_record('mnet_log', array('hostid' => $hostid, 'course' => $id), '*', true);
    $course->id = $id;
    $course->shortname = $coursestub->coursename;
    $course->fullname = $coursestub->coursename;
}

require_login($course);

$context = context_course::instance($course->id);

require_capability('block/timestat:view', $context);

if (!empty($page)) {
    $strlogs = get_string('logs') . ": " . get_string('page', 'report_log', $page + 1);
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
\core\session\manager::write_close();

if (!empty($chooselog)) {
    $userinfo = get_string('allparticipants');
    $dateinfo = get_string('alldays');

    if ($user) {
        $u = $DB->get_record('user', array('id' => $user, 'deleted' => 0), '*', MUST_EXIST);
        $userinfo = fullname($u, has_capability('moodle/site:viewfullnames', $context));
    }
    if ($datefrom) {
        $datefrominfo = userdate($datefrom, get_string('strftimedaydate'));
    }
    if ($dateto) {
        $datetoinfo = userdate($dateto, get_string('strftimedaydate'));
    }

    switch ($logformat) {
        case 'downloadasexcel':
            if (!block_timestat_print_log_xls($course, $user, $datefrom, $dateto, 'l.time DESC', $modname,
                    $modid, $modaction, $group)) {
                echo $OUTPUT->notification("No logs found!");
                echo $OUTPUT->footer();
            }
            exit;
        default:
            if ($hostid != $CFG->mnet_localhost_id || $course->id == SITEID) {
                admin_externalpage_setup('reportlog');
                $PAGE->set_title($course->shortname . ': ' . $strlogs);
                $PAGE->set_heading($course->fullname);
                $PAGE->navbar->add("Timestat");
                echo $OUTPUT->header();

            } else {
                $PAGE->set_title($course->shortname . ': ' . $strlogs);
                $PAGE->set_heading($course->fullname);
                $PAGE->navbar->add("Timestat");
                echo $OUTPUT->header();
            }

            echo $OUTPUT->heading(format_string($course->fullname) . ": $userinfo, $datefrominfo (" . usertimezone() . ")");
            block_timestat_report_log_print_mnet_selector_form($hostid, $course, $user, $datefrom, $dateto, $modname, $modid,
                    $modaction, $group, $showcourses, $showusers, $logformat);

            if ($hostid == $CFG->mnet_localhost_id) {
                block_timestat_print_log($course, $user, $datefrom, $dateto, 'l.timecreated DESC', $page, $perpage,
                        "index.php?id=$course->id&amp;chooselog=1&amp;user=$user&amp;datefrom=$datefrom&amp;dateto=$dateto&amp;
                        modid=$modid&amp;modaction=$modaction&amp;group=$group",
                        $modname, $modid, $modaction, $group);
            } else {
                block_timestat_print_mnet_log(
                        $hostid, $id, $user, $datefrom, $dateto, 'l.timecreated DESC',
                        $page, $perpage, "", $modname, $modid, $modaction, $group
                );
            }
            break;
    }

} else {
    if ($hostid != $CFG->mnet_localhost_id || $course->id == SITEID) {
        admin_externalpage_setup('reportlog', '', null, '', array('pagelayout' => 'report'));
        echo $OUTPUT->header();
    } else {
        $PAGE->set_title($course->shortname . ': ' . $strlogs);
        $PAGE->set_heading($course->fullname);
        echo $OUTPUT->header();
    }

    echo $OUTPUT->heading(get_string('chooselogs') . ':');

    block_timestat_report_log_print_selector_form($course, $user, $datefrom, $dateto, $modname, $modid, $modaction,
            $group, $showcourses, $showusers, $logformat);
}

echo $OUTPUT->footer();
