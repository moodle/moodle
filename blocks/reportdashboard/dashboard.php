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
 * Form for editing LearnerScript dashboard block instances.
 * @package  block_reportdashboard
 * @author Naveen kumar <naveen@eabyas.in>
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/accesslib.php');
require_once($CFG->dirroot . '/blocks/reportdashboard/reportselect_form.php');
require_once($CFG->dirroot . '/blocks/reportdashboard/reporttiles_form.php');
require_once($CFG->dirroot . '/blocks/learnerscript/classes/observer.php');

use block_reportdashboard\local\reportdashboard;
use block_learnerscript\lsform as lsform;
use block_learnerscript\local\ls as ls;
use block_learnerscript\local\querylib as querylib;
global $CFG, $PAGE, $OUTPUT, $THEME, $ADMIN, $DB;
$adminediting = optional_param('adminedit', -1, PARAM_BOOL);
$dashboardurl = optional_param('dashboardurl', '', PARAM_RAW_TRIMMED);
$delete = optional_param('delete', '', PARAM_RAW);
$deletereport = optional_param('deletereport', 0, PARAM_INT);
$blockinstanceid = optional_param('blockinstanceid', 0, PARAM_INT);
$reportid = optional_param('reportid', 0, PARAM_INT);
$role = optional_param('role', '', PARAM_RAW);
$sesskey = optional_param('sesskey', '', PARAM_RAW);
$learnerscript = get_config('block_learnerscript', 'ls_serialkey');
$contextlevel = optional_param('contextlevel', 10, PARAM_INT);

if (empty($learnerscript)) {
    throw new moodle_exception("License Key Is Required");
    exit();
}

require_login();
if (isguestuser()) {
    print_error('noguest');
}
$context = context_system::instance();
$PAGE->set_context($context);

$lsreportconfigstatus = get_config('block_learnerscript', 'lsreportconfigstatus');

if (!$lsreportconfigstatus) {
    redirect(new moodle_url($CFG->wwwroot . '/blocks/learnerscript/lsconfig.php?import=1'));
    exit;
}

if ($PAGE->user_allowed_editing() && $adminediting != -1) {
    $USER->editing = $adminediting;
}

$_SESSION['ls_contextlevel'] = $contextlevel;
$rolelist = (new ls)->get_currentuser_roles();
$_SESSION['rolecontext'] = $role . '_' . $contextlevel;
$rolecontexts = $DB->get_records_sql("SELECT DISTINCT CONCAT(r.id, '@', rcl.id),
                        r.shortname, rcl.contextlevel
                        FROM {role} AS r
                        JOIN {role_context_levels} AS rcl ON rcl.roleid = r.id AND rcl.contextlevel NOT IN (70)
                        WHERE 1 = 1
                        ORDER BY rcl.contextlevel ASC");
foreach ($rolecontexts as $rc) {
   if ($rc->contextlevel == 10 && ($rc->shortname == 'manager')) {
    continue;
   }
   $rcontext[] = $rc->shortname .'_'.$rc->contextlevel;
}
$_SESSION['rolecontextlist'] = $rcontext;

if (!is_siteadmin()) {
    if (!empty($role) && in_array($role, $rolelist)) {
        $role = empty($role) ? array_shift($rolelist) : $role;
    } else if (empty($role)) {
        $role = empty($role) ? array_shift($rolelist) : $role;
    } else {
        $role = $role;
    }
    $_SESSION['role'] = $role;
} else {
    $_SESSION['role'] = $role;
}

$dashboardcourseid = SITEID;
if ($dashboardurl == 'Course') {
    if (is_siteadmin($USER->id) || (new ls)->is_manager($USER->id, $_SESSION['ls_contextlevel'], $_SESSION['role'])) { 
        if ($CFG->dbtype == 'sqlsrv') {
            $query = "SELECT TOP 1 id FROM {course} WHERE visible = 1 AND id <> :siteid ORDER BY id ASC";
            $params = array('siteid' => SITEID);
            $dashboardcourse = $DB->get_field_sql($query, $params);
        } else {
            $query = "SELECT id FROM {course} WHERE visible = 1 AND id <> :siteid ORDER BY id ASC LIMIT 1";
            $params = array('siteid' => SITEID);
            $dashboardcourse = $DB->get_field_sql($query, $params);
        }
        if ($dashboardcourse == SITEID || $dashboardcourse == 0) {
            $dashboardcourseid = SITEID;
        } else {
            $dashboardcourseid = $dashboardcourse;
        }
    } else {
        $dashboardcourse = (new querylib)->get_rolecourses($USER->id, $role, $contextlevel, SITEID, '', ' LIMIT 1', false, false, '', true);
        if (!empty($dashboardcourse)) {
            $dashboardcourseid = array_keys($dashboardcourse)[0];
        }
    }
    $coursels = true;
    $parentcheck = false;
}
$seturl = !empty($role) ? '/blocks/reportdashboard/dashboard.php?role=' . $role : '/blocks/reportdashboard/dashboard.php';
$pagepattentype = !empty($role) ? 'blocks-reportdashboard-dashboard-' . $role . '_' . $contextlevel : 'blocks-reportdashboard-dashboard';
if ($dashboardurl != '') {
    $seturl = !empty($role) ? '/blocks/reportdashboard/dashboard.php?role=' . $role . '&contextlevel=' . $contextlevel . '&dashboardurl=' . $dashboardurl .
                        '' : '/blocks/reportdashboard/dashboard.php?dashboardurl=' . $dashboardurl. '';
}
if ($dashboardurl == ''  || $dashboardurl == 'Dashboard') {
    $dashboardurl = 'Dashboard';
}
$subpagepatterntype = $dashboardurl;

$PAGE->set_url($seturl);
$PAGE->set_pagetype($pagepattentype);
$PAGE->set_subpage($subpagepatterntype);
$PAGE->set_pagelayout('base');
$PAGE->add_body_class('reportdashboard');
$PAGE->navbar->ignore_active();
$navdashboardurl = new moodle_url($seturl);
$PAGE->requires->data_for_js("M.cfg.accessls", $learnerscript , true);

$managereporturl = $CFG->wwwroot. '/blocks/reportdashboard/dashboard.php';
$PAGE->navbar->add(get_string('dashboard', 'block_learnerscript'), $managereporturl);
if (!$dashboardurl) {
    $PAGE->navbar->add(get_string('dashboard', 'block_reportdashboard'));
} else {
    $PAGE->navbar->add($dashboardurl);
}
$PAGE->requires->jquery();
// $PAGE->requires->js(new moodle_url('https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'));
$PAGE->requires->js_call_amd('block_reportdashboard/reportdashboard', 'init');

$PAGE->requires->css('/blocks/reportdashboard/css/radios-to-slider.min.css');
$PAGE->requires->css('/blocks/reportdashboard/css/flatpickr.min.css');

$output = $PAGE->get_renderer('block_reportdashboard');

$regions = array('side-db-first', 'side-db-second', 'side-db-third', 'side-db-four',
                 'side-db-one', 'side-db-two', 'side-db-three', 'side-db-main',
                 'center-first', 'center-second', 'reports-db-one', 'reports-db-two',
                 'reportdb-one', 'reportdb-second', 'reportdb-third', 'first-maindb');
$PAGE->blocks->add_regions($regions);

if ($delete && confirm_sesskey()) {
    $deleteinstance = (new reportdashboard)->delete_dashboard_instances($role, $delete, $blockinstanceid, $contextlevel);
    (new reportdashboard)->delete_widget($deletereport, $blockinstanceid, $reportid);
}

$header = get_string('reports', 'block_reportdashboard');
$PAGE->set_title($header);

$reportsfont = get_config('block_reportdashboard', 'reportsfont');
if ($reportsfont == 2) { // Selected font as PT Sans.
    $PAGE->requires->css('/blocks/reportdashboard/fonts/roboto.css');
} else if ($reportsfont == 1) { // Selected font as Open Sans.
    $PAGE->requires->css('/blocks/reportdashboard/fonts/roboto.css');
}

$reportdashboardstatus = $PAGE->blocks->is_known_block_type('reportdashboard', false);
$reporttilestatus = $PAGE->blocks->is_known_block_type('reporttiles', false);
$data = data_submitted();

$reportdashboard = new reportdashboard;
if ($reportdashboardstatus) {
    if (isset($data->_qf__reportselect_form)) {
        $reportdashboard->add_dashboardblocks($data);
        redirect($PAGE->url);
    }
}
if ($reporttilestatus) {
    if (isset($data->_qf__reporttiles_form)) {
        $reportdashboard->add_tilesblocks($data);
        redirect($PAGE->url);
    }
}

if (is_siteadmin() && $PAGE->user_allowed_editing()) {
    $url = clone($PAGE->url);
    if ($PAGE->user_is_editing()) {
        $caption = get_string('blockseditoff');
        $url->param('adminedit', 'off');
    } else {
        $caption = get_string('blocksediton');
        $url->param('adminedit', 'on');
    }
    $buttons = $OUTPUT->single_button($url, $caption, 'get');
    $PAGE->set_button($buttons);
}

$dataaction = isset($data->action) ? $data->action : '';
if (!empty($data) && $dataaction == 'sendemails') {
    $roleid = 0;
    if (!empty($_SESSION['role'])) {
        $roleid = $DB->get_field('role', 'id', array('shortname' => $_SESSION['role']));
    }
    $userlist = implode(',', $data->email);
    $data->sendinguserid = $userlist;
    $data->exportformat = $data->format;
    $data->frequency = -1;
    $data->schedule = 0;
    $data->exporttofilesystem = 1;
    $data->reportid = $data->reportid;
    $data->timecreated = time();
    $data->timemodified = 0;
    $data->userid = $USER->id;
    $data->roleid = $roleid;
    $data->nextschedule = 0;
    $insert = $DB->insert_record('block_ls_schedule', $data);
    if ($insert) {
        redirect($PAGE->url);
    }
}
echo $OUTPUT->header();
$themename=$PAGE->theme->name ;
$themelist = array('academi');
    if(in_array($themename, $themelist)){
        echo '<h3>LearnerScript</h3>';
    }
echo "<script src='https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'></script>";

echo html_writer::start_tag('div', array('id' => 'licenceresult', 'class' => 'lsacccess'));

if (!empty($role) || is_siteadmin()) {
    $configuredinstances = $DB->count_records('block_instances', array(
                                'pagetypepattern' => $pagepattentype, 'subpagepattern' => $subpagepatterntype));
    $reports = $DB->get_records_sql("SELECT id FROM {block_learnerscript} WHERE visible = :visible AND global = :global", ['visible' => 1, 'global' => 1]);
    if (!empty($reports)) {
        $editingon = false;
        if (is_siteadmin() && isset($USER->editing) && $USER->editing) {
            $editingon = true;
        }
        $turnediting = '';
        if ($PAGE->user_allowed_editing()) {
            $url = clone ($PAGE->url);
            if ($PAGE->user_is_editing()) {
                $caption = get_string('blockseditoff');
                $url->param('adminedit', 'off');
            } else {
                $caption = get_string('blocksediton');
                $url->param('adminedit', 'on');
            }
            $turnediting = $OUTPUT->single_button($url, $caption, 'get') . '</span>';
        }
        $output = $PAGE->get_renderer('block_reportdashboard');
        $dashboardheader = new \block_reportdashboard\output\dashboardheader((object)array("editingon" => $editingon,
                             'configuredinstances' => $configuredinstances, 'dashboardurl' => $dashboardurl));
        echo  $output->render($dashboardheader);

        echo html_writer::start_tag('div', array('class' => 'width-container'));
        echo $OUTPUT->blocks('side-db-first', 'width-default width-3');
        echo $OUTPUT->blocks('side-db-second', 'width-default width-3');
        echo $OUTPUT->blocks('side-db-third', 'width-default width-3');
        echo $OUTPUT->blocks('side-db-four', 'width-default width-3');
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('class' => 'width-container'));
        echo $OUTPUT->blocks('first-maindb', 'width-default width-12');
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('class' => 'width-container reports-act-graphs'));
        echo $OUTPUT->blocks('reportdb-one', 'width-default width-4 ml0');
        echo $OUTPUT->blocks('reportdb-second', 'width-default width-4');
        echo $OUTPUT->blocks('reportdb-third', 'width-default width-4');
        echo html_writer::end_tag('div');

        echo html_writer::start_tag('div', array('class' => 'width-container'));
        echo $OUTPUT->blocks('center-first', 'width-default width-9');
        echo $OUTPUT->blocks('center-second', 'width-default width-3');
        echo html_writer::end_tag('div');

        echo html_writer::start_tag('div', array('class' => 'width-container'));
        echo $OUTPUT->blocks('reports-db-one', 'width-default width-6');
        echo $OUTPUT->blocks('reports-db-two', 'width-default width-6');
        echo html_writer::end_tag('div');

        echo html_writer::start_tag('div', array('class' => 'width-container'));
        echo $OUTPUT->blocks('side-db-main', 'width-default width-12');
        echo html_writer::end_tag('div');

        echo html_writer::start_tag('div', array('class' => 'width-container'));
        echo $OUTPUT->blocks('side-db-one', 'width-default width-6');
        echo $OUTPUT->blocks('side-db-two', 'width-default width-6');
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('class' => 'width-container'));
        echo $OUTPUT->blocks('side-db-three', 'width-default width-12');
        echo html_writer::end_tag('div');
        echo '<div class="reportslist" style="display:none;">';
        echo '</div>';
        echo '<div class="statistics_reportslist" style="display:none;">';
        echo '</div>';
    } else {
        $action = html_writer::tag('a', get_string('addreport', 'block_learnerscript'),
                                            array('href' => $CFG->wwwroot . '/blocks/learnerscript/managereport.php'));
        echo '<div class="alert alert-info">' . get_string('reportsnotavailable', 'block_reportdashboard', $action) . '</div>';
    }
    if($configuredinstances > 0){
        // echo "<input type='hidden' name='ls_fstartdate' id='ls_fstartdate' value='0' />";
        // echo "<input type='hidden' name='ls_fenddate' id='ls_fenddate' value='" . time() . "' />";
        echo "<input type='hidden' name='filter_courses' id='ls_courseid' class = 'report_courses'  value='" . $dashboardcourseid . "' />";
        echo '<div class="loader"></div>';
    }
} else {
    print_error("notasssignedrole", 'block_learnerscript');
}
echo html_writer::end_tag('div');
echo $OUTPUT->footer();
exit;
