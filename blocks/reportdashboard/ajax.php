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
 * LearnerScript report dashboard Services.
 * @package  block_reportdashboard
 * @author Naveen kumar <naveen@eabyas.in>
 */
define('AJAX_SCRIPT', true);
require_once('../../config.php');
use block_learnerscript\local\reportbase;
use block_learnerscript\local\ls as ls;
global $CFG, $DB, $USER, $OUTPUT, $COURSE;
$rawjson = file_get_contents('php://input');

$requests = json_decode($rawjson, true);
$action = optional_param('action', $requests['action'], PARAM_TEXT);
$search = optional_param('term', $requests['term'], PARAM_RAW);
$frequency = optional_param('frequency', $requests['frequency'], PARAM_TEXT);
$reportid = optional_param('reportid', $requests['reportid'], PARAM_INT);
$reporttype = optional_param('selreport', $requests['selreport'], PARAM_RAW);
$blockinstanceid = optional_param('blockinstanceid', $requests['blockinstanceid'], PARAM_INT);
$usersearch = optional_param('term', $requests['term'], PARAM_TEXT);
$instance = optional_param('instance', $requests['instance'], PARAM_INT);
$oldname = optional_param('oldname', $requests['oldname'], PARAM_RAW);
$newname = optional_param('newname', $requests['newname'], PARAM_RAW);
$role = optional_param('role', $requests['role'], PARAM_RAW);
require_login();
$context = context_system::instance();
$PAGE->set_context($context);

switch ($action) {
    case 'generatedreport':
        $html = '';
        $report = $DB->get_record('block_learnerscript', array('id' => $reportid));
        if (!$report) {
            print_error('reportdoesnotexists', 'block_learnerscript');
        }

        if (!$report->global) {
            $html .= "";
        } else {
            require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
            require_once($CFG->dirroot . '/blocks/learnerscript/locallib.php');
            $reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
            $reportclass = new $reportclassname($report);
            $renderer = $PAGE->get_renderer('block_reportdashboard');
            $html .= $renderer->generate_dashboardreport($reportclass, $reporttype, $blockinstanceid);
        }
        echo $html;
        exit;
        break;
    case 'userlist':
        $users = get_users(true, $usersearch);
        foreach ($users as $user) {
            $data[] = ['id' => $user->id, 'text' => $user->firstname.' '.$user->lastname];
        }
        $return = ['total' => count($data), 'items' => $data];
        break;
        break;
    case 'reportlist':
        $sql = "SELECT id,name
                FROM {block_learnerscript}
                WHERE visible = 1 AND name LIKE :search";
        $params = array('search' => "'%" . $search . "%'");
        $courselist = $DB->get_records_sql($sql, $params);
        $activitylist = array();
        foreach ($courselist as $cl) {
            global $CFG;
            if (!empty($cl)) {
                $checkpermissions = (new reportbase($cl->id))->check_permissions($USER->id,
                    $context);
                if (!empty($checkpermissions) || has_capability('block/learnerscript:managereports', $context)) {
                    $modulelink = html_writer::link(new moodle_url('/blocks/learnerscript/viewreport.php',
                        array('id' => $cl->id)), $cl->name, array('id' => 'viewmore_id'));
                    $activitylist[] = ['id' => $cl->id, 'text' => $modulelink];
                }
            }
        }
        $termsdata = array();
        $termsdata['total_count'] = count($activitylist);
        $termsdata['incomplete_results'] = true;
        $termsdata['items'] = $activitylist;
        $return = $termsdata;
        break;
    case 'sendemails':
        require_once($CFG->dirroot . '/blocks/reportdashboard/email_form.php');
        $emailform = new analytics_emailform($CFG->wwwroot . '/blocks/reportdashboard/dashboard.php',
            array('reportid' => $reportid, 'AjaxForm' => true, 'instance' => $instance));
        $return = $emailform->render();
        break;
    case 'dashboardtiles':
        $reportclass = (new ls)->create_reportclass($reportid);
        $reportclass->create_report($blockinstanceid);
        $return = $reportclass->finalreport->table;
        break;
    case 'updatedashboard':
        $subpagepattern = strtolower($oldname);
        $newsubpagepattern = strtolower($newname);
        $subpagetype = $newname;
        $instances = $DB->get_records('block_instances',
            array('subpagepattern' => $subpagepattern));
        foreach ($instances as $instance) {
            $instance->subpagepattern = $newname;
            $DB->update_record('block_instances', $instance);
            $positions = $DB->get_records('block_positions',
                array('blockinstanceid' => $instance->id));
            foreach ($positions as $position) {
                $position->subpage = $subpagetype;
                $DB->update_record('block_positions', $position);
            }
        }
        break;
}
echo json_encode($return);
