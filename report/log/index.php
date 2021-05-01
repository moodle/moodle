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

use core\report_helper;

require('../../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/report/log/locallib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/lib/tablelib.php');

$id          = optional_param('id', 0, PARAM_INT);// Course ID.
$group       = optional_param('group', 0, PARAM_INT); // Group to display.
$user        = optional_param('user', 0, PARAM_INT); // User to display.
$date        = optional_param('date', 0, PARAM_INT); // Date to display.
$modid       = optional_param('modid', 0, PARAM_ALPHANUMEXT); // Module id or 'site_errors'.
$modaction   = optional_param('modaction', '', PARAM_ALPHAEXT); // An action as recorded in the logs.
$page        = optional_param('page', '0', PARAM_INT);     // Which page to show.
$perpage     = optional_param('perpage', '100', PARAM_INT); // How many per page.
$showcourses = optional_param('showcourses', false, PARAM_BOOL); // Whether to show courses if we're over our limit.
$showusers   = optional_param('showusers', false, PARAM_BOOL); // Whether to show users if we're over our limit.
$chooselog   = optional_param('chooselog', false, PARAM_BOOL);
$logformat   = optional_param('download', '', PARAM_ALPHA);
$logreader      = optional_param('logreader', '', PARAM_COMPONENT); // Reader which will be used for displaying logs.
$edulevel    = optional_param('edulevel', -1, PARAM_INT); // Educational level.
$origin      = optional_param('origin', '', PARAM_TEXT); // Event origin.

$params = array();
if (!empty($id)) {
    $params['id'] = $id;
} else {
    $site = get_site();
    $id = $site->id;
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
if ($showcourses) {
    $params['showcourses'] = $showcourses;
}
if ($showusers) {
    $params['showusers'] = $showusers;
}
if ($chooselog) {
    $params['chooselog'] = $chooselog;
}
if ($logformat !== '') {
    $params['download'] = $logformat;
}
if ($logreader !== '') {
    $params['logreader'] = $logreader;
}
if (($edulevel != -1)) {
    $params['edulevel'] = $edulevel;
}
if ($origin !== '') {
    $params['origin'] = $origin;
}
// Legacy store hack, as edulevel is not supported.
if ($logreader == 'logstore_legacy') {
    $params['edulevel'] = -1;
    $edulevel = -1;
}
$url = new moodle_url("/report/log/index.php", $params);

$PAGE->set_url('/report/log/index.php', array('id' => $id));
$PAGE->set_pagelayout('report');

report_helper::save_selected_report($id, $url);

// Get course details.
$course = null;
if ($id) {
    $course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
    require_login($course);
    $context = context_course::instance($course->id);
} else {
    require_login();
    $context = context_system::instance();
    $PAGE->set_context($context);
}

require_capability('report/log:view', $context);

// When user choose to view logs then only trigger event.
if ($chooselog) {
    // Trigger a report viewed event.
    $event = \report_log\event\report_viewed::create(array('context' => $context, 'relateduserid' => $user,
            'other' => array('groupid' => $group, 'date' => $date, 'modid' => $modid, 'modaction' => $modaction,
            'logformat' => $logformat)));
    $event->trigger();
}

if (!empty($page)) {
    $strlogs = get_string('logs'). ": ". get_string('page', 'report_log', $page + 1);
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

if (empty($course) || ($course->id == $SITE->id)) {
    admin_externalpage_setup('reportlog', '', null, '', array('pagelayout' => 'report'));
    $PAGE->set_title($SITE->shortname .': '. $strlogs);
} else {
    $PAGE->set_title($course->shortname .': '. $strlogs);
    $PAGE->set_heading($course->fullname);
}

$reportlog = new report_log_renderable($logreader, $course, $user, $modid, $modaction, $group, $edulevel, $showcourses, $showusers,
        $chooselog, true, $url, $date, $logformat, $page, $perpage, 'timecreated DESC', $origin);
$readers = $reportlog->get_readers();
$output = $PAGE->get_renderer('report_log');

if (empty($readers)) {
    echo $output->header();
    echo $output->heading(get_string('nologreaderenabled', 'report_log'));
} else {
    if (!empty($chooselog)) {
        // Delay creation of table, till called by user with filter.
        $reportlog->setup_table();

        if (empty($logformat)) {
            echo $output->header();
            // Print selector dropdown.
            $pluginname = get_string('pluginname', 'report_log');
            report_helper::print_report_selector($pluginname);
            $userinfo = get_string('allparticipants');
            $dateinfo = get_string('alldays');

            if ($user) {
                $u = $DB->get_record('user', array('id' => $user, 'deleted' => 0), '*', MUST_EXIST);
                $userinfo = fullname($u, has_capability('moodle/site:viewfullnames', $context));
            }
            if ($date) {
                $dateinfo = userdate($date, get_string('strftimedaydate'));
            }
            if (!empty($course) && ($course->id != SITEID)) {
                $PAGE->navbar->add("$userinfo, $dateinfo");
            }
            echo $output->render($reportlog);
        } else {
            \core\session\manager::write_close();
            $reportlog->download();
            exit();
        }
    } else {
        echo $output->header();
        // Print selector dropdown.
        $pluginname = get_string('pluginname', 'report_log');
        report_helper::print_report_selector($pluginname);
        echo $output->heading(get_string('chooselogs') .':');
        echo $output->render($reportlog);
    }
}

echo $output->footer();
