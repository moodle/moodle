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

/** LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: Naveen Kumar <naveen@eabyas.in>
 */
define('AJAX_SCRIPT', true);
require_once('../../../../config.php');
global $CFG, $DB, $USER, $OUTPUT, $PAGE;
use block_learnerscript\local\schedule;

$action = required_param('action', PARAM_TEXT);
$reportid = optional_param('reportid', 0, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$start = optional_param('start', 0, PARAM_INT);
$length = optional_param('length', 5, PARAM_INT);
$courseid = optional_param('courseid', 1, PARAM_INT);
$schuserslist = optional_param('schuserslist', '', PARAM_RAW);
$component = optional_param('component',$requests['component'],PARAM_RAW);
$pname = optional_param('pname',$requests['pname'],PARAM_RAW);

$context = context_system::instance();
require_login();
$PAGE->set_context($context);

$scheduling = new schedule();
$learnerscript = $PAGE->get_renderer('block_learnerscript');

switch ($action) {
case 'scheduledtimings':
	if ((has_capability('block/learnerscript:managereports', $context) || has_capability('block/learnerscript:manageownreports', $context) || is_siteadmin()) && !empty($reportid)) {
		$return = $learnerscript->schedulereportsdata($reportid, $courseid, false, $start, $length, $search['value']);
	} else {
		$terms_data = array();
		$terms_data['error'] = true;
		$terms_data['type'] = 'Warning';
		if (empty($reportid)) {
			$terms_data['cap'] = false;
			$terms_data['msg'] = get_string('missingparam', 'block_learnerscript', 'Reportid');
		} else {
			$terms_data['cap'] = true;
			$terms_data['msg'] = get_string('badpermissions', 'block_learnerscript');
		}
		$return = $terms_data;
	}
	break;
case 'viewschusersdata':
	if ((has_capability('block/learnerscript:managereports', $context) || has_capability('block/learnerscript:manageownreports', $context) || is_siteadmin()) && !empty($schuserslist)) {
		$stable = new stdClass();
		$stable->table = false;
		$stable->start = $start;
		$stable->length = $length;
		$stable->search = $search['value'];
		$return = $learnerscript->viewschusers($reportid, $scheduleid, $schuserslist, $stable);
	} else {
		$terms_data = array();
		$terms_data['error'] = true;
		$terms_data['type'] = 'Warning';
		if (empty($schuserslist)) {
			$terms_data['cap'] = false;
			$terms_data['msg'] = get_string('missingparam', 'block_learnerscript', 'Schedule Users List');
		} else {
			$terms_data['cap'] = true;
			$terms_data['msg'] = get_string('badpermissions', 'block_learnerscript');
		}
		$return = $terms_data;
	}
	break;
case 'plotform':
	$componentdata = $learnerscript->render_component_form($reportid, $component, $pname);
	echo $componentdata['html'].'<script>'.$componentdata['script'].'</script>';
	exit;
break;
}
echo json_encode($return, JSON_NUMERIC_CHECK);