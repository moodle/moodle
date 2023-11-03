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

/** Learner Script
 * A Moodle block for creating LearnerScript Reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
require_once("../../config.php");
use \block_learnerscript\local\ls as ls;
$id = required_param('id', PARAM_INT);
$download = optional_param('download', false, PARAM_BOOL);
$format = optional_param('format', '', PARAM_ALPHA);
$courseid = optional_param('courseid', SITEID, PARAM_INT);
$status = optional_param('status', '', PARAM_TEXT);
$cmid = optional_param('cmid', 0, PARAM_INT);
$userid = optional_param('userid', $USER->id, PARAM_INT);
$drillid = optional_param('_drillid', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);
$cid = optional_param('cid', '', PARAM_ALPHANUM);
$comp = optional_param('comp', '', PARAM_ALPHA);
$pname = optional_param('pname', '', PARAM_ALPHA);
$learnerscript = get_config('block_learnerscript', 'ls_serialkey');
global $USER, $CFG;

$lsreportconfigstatus = get_config('block_learnerscript', 'lsreportconfigstatus');

if (!$lsreportconfigstatus) {
    redirect(new moodle_url($CFG->wwwroot . '/blocks/learnerscript/lsconfig.php?import=1'));
    exit;
}

if (!is_siteadmin() && empty($_SESSION['role'])) {
	$rolelist = (new ls)->get_currentuser_roles();
	if (empty($_SESSION['role']) && !empty($rolelist)) {
        $role = empty($_SESSION['role']) ? array_shift($rolelist) : $_SESSION['role'];
    } else {
        $role = '';
    }
    $_SESSION['role'] = $role;
}
if (!is_siteadmin()) {
	if (empty($_SESSION['ls_contextlevel'])) {
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
		$limit = '';
		$querysql = "SELECT $limit DISTINCT ctx.contextlevel, r.shortname
		                   FROM {role} AS r
		                   JOIN {role_assignments} AS ra ON ra.roleid = r.id
		                   JOIN {context} as ctx ON ctx.id = ra.contextid
		                   WHERE ra.userid = :userid ORDER BY ctx.contextlevel ASC $limit"; 
		if ($CFG->dbtype == 'sqlsrv') {
            $limit = str_replace('%%TOP%%', 'TOP 1', $querysql);
        } else {
            $limit = str_replace('%%LIMIT%%', 'LIMIT 1', $querysql);
        }
        $contextlevels = $DB->get_record_sql($querysql, ['userid' => $USER->id]);
		$_SESSION['rolecontextlist'] = $rcontext;
		$_SESSION['ls_contextlevel'] = $contextlevels->contextlevel;
		$_SESSION['rolecontext'] = $_SESSION['role'] . '_' . $_SESSION['ls_contextlevel'];
	}
}
$filterrequests = array();
$datefilterrequests = array();
$datefilterrequests['ls_fstartdate'] = 0;
$datefilterrequests['ls_fenddate'] = time();
foreach ($_REQUEST as $key => $val) {
	if (strpos($key, 'filter_') !== false) {
		$filterrequests[$key] = optional_param($key, $val, PARAM_RAW);
	}
	if (strpos($key, 'ls_') !== false) {
		$datefilterrequests[$key] = optional_param($key, $val, PARAM_RAW);
	}
}
if (!$report = $DB->get_record('block_learnerscript', array('id' => $id))) {
	print_error('reportdoesnotexists', 'block_learnerscript');
}

if ($courseid and $report->global) {
	$report->courseid = $courseid;
} else {
	$courseid = $report->courseid;
}
if ($userid > 0) {
	$report->userid = $userid;
}
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
	print_error(get_string('nocourseid', 'block_learnerscript'));
}

// Force user login in course (SITE or Course)
if ($course->id == SITEID) {
	require_login();
	$context = context_system::instance();
} else {
	require_login($course);
	$context = context_course::instance($course->id);
}
$PAGE->set_context($context);
$PAGE->set_title($report->name);
$PAGE->set_pagelayout('report');

$PAGE->requires->data_for_js("M.cfg.accessls", $learnerscript , true);

if ($delete && confirm_sesskey()) {
	$components = (new block_learnerscript\local\ls)->cr_unserialize($report->components);
	$elements = isset($components[$comp]['elements']) ? $components[$comp]['elements'] : array();
	foreach ($elements as $index => $e) {
		if ($e['id'] == $cid) {
			if ($delete) {
				unset($elements[$index]);
				break;
			}
			$newindex = ($moveup) ? $index - 1 : $index + 1;
			$tmp = $elements[$newindex];
			$elements[$newindex] = $e;
			$elements[$index] = $tmp;
			break;
		}
	}
	$components[$comp]['elements'] = $elements;
	$report->components = (new block_learnerscript\local\ls)->cr_serialize($components);
	$DB->update_record('block_learnerscript', $report);
	redirect(new moodle_url('/blocks/learnerscript/viewreport.php', array('id' => $id, 'courseid' => $courseid)));
	exit;
}

require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
$properties = new stdClass();
$reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
$reportclass = new $reportclassname($report, $properties);
$reportclass->courseid = $courseid;
if (!$download) {
	$reportclass->start = 0;
	$reportclass->length = 1;
} else {
	$reportclass->length = -1;
}
$reportclass->search = '';
$reportclass->filters = $filterrequests;
$reportclass->basicparamdata = $filterrequests;
$reportclass->status = $status;
$reportclass->ls_startdate = $datefilterrequests['ls_fstartdate'];
$reportclass->ls_enddate = $datefilterrequests['ls_fenddate'];

$reportclass->cmid = $cmid;
$reportclass->userid = $userid;
if (!is_siteadmin() && !$reportclass->check_permissions($USER->id, $context)) {
	print_error("badpermissions", 'block_learnerscript');
}
$basicparamdata = new stdclass;
$request = array_merge($_POST, $_GET);
if ($request){
    foreach ($request as $key => $val) {
        if (strpos($key, 'filter_') !== false) {
        	$plugin = str_replace('filter_', '', $key);
            $basicparamdata->{$key} = $val;
            if(file_exists($CFG->dirroot . '/blocks/learnerscript/components/filters/' . $plugin . '/plugin.class.php') && !empty($val)){
	            require_once($CFG->dirroot . '/blocks/learnerscript/components/filters/' . $plugin . '/plugin.class.php');
	            $classname = 'block_learnerscript\lsreports\plugin_' . $plugin;
	            $class = new $classname($reportclass->config);
	            $selected = get_string('selectedfilter', 'block_learnerscript', ucfirst($plugin));
	            $reportclass->selectedfilters[$selected] = $class->selected_filter($val, $request);
        	}
        }
    }
}
$reportclass->params = (array)$basicparamdata;
$reportname = format_string($report->name);

$PAGE->set_url('/blocks/learnerscript/viewreport.php', array('id' => $id));

$download = ($download && $format && strpos($report->export, $format) !== false) ? true : false;

// $PAGE->requires->js(new moodle_url('https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'));
$PAGE->requires->js('/blocks/learnerscript/js/highcharts/treemap.js');
$PAGE->requires->js('/blocks/learnerscript/js/highmaps/map.js');
$PAGE->requires->js('/blocks/learnerscript/js/highmaps/world.js');
$PAGE->requires->css('/blocks/reportdashboard/css/radios-to-slider.min.css');
$PAGE->requires->css('/blocks/reportdashboard/css/flatpickr.min.css');
$PAGE->requires->css('/blocks/learnerscript/css/fixedHeader.dataTables.min.css');
$PAGE->requires->css('/blocks/learnerscript/css/responsive.dataTables.min.css');
$PAGE->requires->jquery_plugin('ui-css');
$PAGE->requires->css('/blocks/learnerscript/css/select2.min.css', true);
$PAGE->requires->css('/blocks/learnerscript/css/jquery.dataTables.min.css', true);
// No download, build navigation header etc.
if (!$download) {
    $reportshead_start = get_config('block_reportdashboard', 'header_start');
    $reportshead_end = get_config('block_reportdashboard', 'header_end');
    $reportshead_start = empty($reportshead_start) ? '#0d3c56' : $reportshead_start;
    $reportshead_end = empty($reportshead_end) ? '#35779b' : $reportshead_end;

	$columndata = (new ls)->column_definations($reportclass);
	$PAGE->requires->js_call_amd('block_learnerscript/report', 'init',
									array(array('reportid' => $id,
												'filterrequests' => $filterrequests,
												'cols' => $columndata['datacolumns'],
												'columnDefs' => $columndata['columnDefs'],
												'basicparams' =>$reportclass->basicparams
											),
								));

	$reportclass->check_filters_request($_SERVER['REQUEST_URI']);

	$navlinks = array();
	if (has_capability('block/learnerscript:managereports', $context) ||
		(has_capability('block/learnerscript:manageownreports', $context)) &&
		$report->ownerid == $USER->id) {
		if (is_siteadmin()) {
			$managereporturl = new moodle_url($CFG->wwwroot . '/blocks/learnerscript/managereport.php');
		} else {
			$managereporturl = new moodle_url($CFG->wwwroot . '/blocks/learnerscript/managereport.php?role'.$_SESSION['role'].'&contextlevel='.$_SESSION['ls_contextlevel']);
		}
		$PAGE->navbar->add(get_string('managereports', 'block_learnerscript'), $managereporturl);
	} else {
		$dashboardurl = new moodle_url($CFG->wwwroot . '/blocks/learnerscript/reports.php?role='.$_SESSION['role'].'&contextlevel='.$_SESSION['ls_contextlevel'], array());

		$PAGE->navbar->add(get_string("reports_view", 'block_learnerscript'), $dashboardurl);
	}
	if ($drillid > 0) {
		$drillreporturl = new moodle_url($CFG->wwwroot . '/blocks/learnerscript/viewreport.php', array('id' => $drillid));
		$drillreportname = $DB->get_field('block_learnerscript', 'name', array('id' => $drillid));
		$PAGE->navbar->add($drillreportname, $drillreporturl);
	}
	$PAGE->navbar->add($report->name);
	$reportsfont = get_config('block_reportdashboard', 'reportsfont');
	if ($reportsfont == 2) { /*selected font as PT Sans*/
		$PAGE->requires->css('/blocks/reportdashboard/fonts/roboto.css');
	} else if ($reportsfont == 1) { /*selected font as Open Sans*/
		$PAGE->requires->css('/blocks/reportdashboard/fonts/roboto.css');
	}
	$PAGE->set_cacheable(true);
	$event = \block_learnerscript\event\view_report::create(array(
		'objectid' => $report->id,
		'context' => $context,
	));
	$event->trigger();

	echo $OUTPUT->header();
	echo "<script src='https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'></script>";
	if ($report->type == 'sql' || $report->type == 'statistics') {
		echo $OUTPUT->heading($report->name."  "."<img src='$CFG->wwwroot/pix/help.png' id='helpimg' title='help with $report->name' alt='help' onclick = '(function(e){ require(\"block_learnerscript/report\").block_statistics_help({$report->id}) }) (event)'/>");

	} else {
		echo $OUTPUT->heading($report->name.$OUTPUT->help_icon('report_' . $report->type,
			'block_learnerscript'));
	}
	echo html_writer::start_tag('div', array('id' => 'licenceresult', 'class' => 'lsacccess'));
	$renderer = $PAGE->get_renderer('block_learnerscript');
	if ($drillid > 0) {
		echo $OUTPUT->single_button($drillreporturl, 'Go back to:' . $drillreportname);
	}
	$disabletable = !empty($report->disabletable) ? $report->disabletable : 0;
	$renderer->viewreport($report, $context, $reportclass);
	echo "<input type='hidden' name='ls_fstartdate' id='ls_fstartdate' value=0 />
    	  <input type='hidden' name='ls_fenddate' id='ls_fenddate' value=".time()." />
    	  <input type='hidden' name='reportid' value=" . $report->id . " />
          <input type='hidden' name='disabletable' id='disabletable' value=" . $disabletable . " />";
	echo html_writer::end_tag('div');
	echo $OUTPUT->footer();
} else {
	$reportclass->reporttype = 'table';
	$reportclass->create_report();
	$exportplugin = $CFG->dirroot . '/blocks/learnerscript/export/' . $format . '/export.php';
	if (file_exists($exportplugin)) {
		require_once($exportplugin);
		$reportclass->finalreport->name = $reportclass->config->name;
		export_report($reportclass, $id);
	}
	die;
}
