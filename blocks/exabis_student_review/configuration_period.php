<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 exabis internet solutions <info@exabis.at>
*  All rights reserved
*
*  You can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  This module is based on the Collaborative Moodle Modules from
*  NCSA Education Division (http://www.ncsa.uiuc.edu)
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require("inc.php");
require_once($CFG->dirroot . '/blocks/exabis_student_review/lib/edit_form.php');

$courseid = optional_param('courseid', 1, PARAM_INT); // Course ID
$periodid = optional_param('periodid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('block/exabis_student_review:use', $context);
require_capability('block/exabis_student_review:editperiods', $context);

$periodform = new period_edit_form();

if ($periodedit = $periodform->get_data()) {
	if(!confirm_sesskey()) {
		print_error("badsessionkey","block_exabis_student_review");
	}
	
	$newperiod = new stdClass();
	$newperiod->timemodified = time();
	$newperiod->userid=$USER->id;
	$newperiod->description = $periodedit->description;
	$newperiod->starttime = $periodedit->starttime;
	$newperiod->endtime = $periodedit->endtime;
	
	if(isset($periodedit->id) && ($periodedit->action == 'edit')) {
		$newperiod->id = $periodedit->id;
		
		if (!update_record('block_exabstudreviperi', $newperiod)) {
			print_error('errorupdateingperiod', 'block_exabis_student_review');
		}
		add_to_log($courseid, 'exabis_student_review', 'new', 'configuration_period.php?courseid=' . $courseid . '&action=edit', $periodedit->id);
	}
	else if($periodedit->action == 'new') {
		if (!(insert_record('block_exabstudreviperi', $newperiod))) {
			print_error('errorinsertingperiod', 'block_exabis_student_review');
		}
		add_to_log($courseid, 'exabis_student_review', 'new', 'configuration_period.php?courseid=' . $courseid . '&action=new', '');
	}
	redirect('periods.php?courseid=' . $courseid);
}

if($action == 'edit') {
	if(!confirm_sesskey()) {
		print_error("badsessionkey","block_exabis_student_review");
	}
	if (!$period = get_record('block_exabstudreviperi', 'id', $periodid)) {
		print_error("invalidperiodid","block_exbais_student_review");
	}
	$period->action = 'edit';
}
else if($action == 'delete') {
	if(!confirm_sesskey()) {
		print_error("badsessionkey","block_exabis_student_review");
	}
	delete_records('block_exabstudreviperi', 'id', $periodid);
	redirect('periods.php?courseid=' . $courseid);
}
else {
	$period->action = 'new';
	$period->description = '';
	$period->starttime = time();
	$period->endtime = time();
	$period->id = 0;
}




block_exabis_student_review_print_header(array('periods', 'periodinput'));

$periodform->set_data($period);
$periodform->display();

print_single_button($CFG->wwwroot . '/blocks/exabis_student_review/periods.php',
					array('courseid' => $courseid),
					get_string('back', 'block_exabis_student_review'));

block_exabis_student_review_print_footer();
