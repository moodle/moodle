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

define("MAX_USERS_PER_PAGE", 5000);

$courseid = optional_param('courseid', 1, PARAM_INT); // Course ID
$showall        = optional_param('showall', 0, PARAM_BOOL);
$searchtext     = optional_param('searchtext', '', PARAM_TEXT); // search string
$add            = optional_param('add', 0, PARAM_BOOL);
$remove         = optional_param('remove', 0, PARAM_BOOL);

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('block/exabis_student_review:use', $context);
require_capability('block/exabis_student_review:head', $context);

if (!$class = get_record('block_exabstudreviclas', 'userid', $USER->id)) {
	print_error('noclassfound', 'block_exabis_student_review');
}

$header = get_string('configmember', 'block_exabis_student_review', $class->class);

block_exabis_student_review_print_header(array('configuration', '='.$header));

if ($frm = data_submitted()) {
	if(!confirm_sesskey()) {
		print_error("badsessionkey","block_exabis_student_review");
	}
	if ($add and !empty($frm->addselect)) {
		foreach ($frm->addselect as $adduser) {
			if (!$adduser = clean_param($adduser, PARAM_INT)) {
				continue;
			}
			
			$newuser = new stdClass();
			$newuser->studentid = $adduser;
			$newuser->classid = $class->id;
			$newuser->timemodified = time();
			
			if (!insert_record('block_exabstudrevistudtoclas', $newuser)) {
				print_error('errorinsertingstudents', 'block_exabis_student_review');
			}
		}
	} else if ($remove and !empty($frm->removeselect)) {
		foreach ($frm->removeselect as $removeuser) {
			if (!$removeuser = clean_param($removeuser, PARAM_INT)) {
				continue;
			}
			
			if (!delete_records('block_exabstudrevistudtoclas', 'studentid', $removeuser, 'classid', $class->id)) {
				print_error('errorremovingstudents', 'block_exabis_student_review');
			}
		}
	} else if ($showall) {
		$searchtext = '';
	}
}

$select  = "username <> 'guest' AND deleted = 0 AND confirmed = 1";
	
if ($searchtext !== '') {   // Search for a subset of remaining users
	$LIKE      = sql_ilike();
	$FULLNAME  = sql_fullname();

	$selectsql = " AND ($FULLNAME $LIKE '%$searchtext%' OR email $LIKE '%$searchtext%') ";
	$select  .= $selectsql;
} else { 
	$selectsql = ""; 
}

$availableusers = get_records_sql('SELECT id, firstname, lastname, email
									 FROM '.$CFG->prefix.'user
									 WHERE '.$select.'
									 AND id NOT IN (
											 SELECT studentid
											 FROM '.$CFG->prefix.'block_exabstudrevistudtoclas
												   WHERE classid = '.$class->id.'
												   '.$selectsql.')
									 ORDER BY lastname ASC, firstname ASC');

print_heading($header);

$usertoclasses = get_records('block_exabstudrevistudtoclas', 'classid', $class->id, 'studentid');

$classusers = array();
if ($usertoclasses) {
	foreach($usertoclasses as $usertoclass) {
		$classusers[] = get_record('user', 'id', $usertoclass->studentid);
	}
}

print_simple_box_start('center');
$form_target = 'configuration_classmembers.php';
include('configuration_userlist.html');
print_simple_box_end();
	
print_single_button($CFG->wwwroot . '/blocks/exabis_student_review/configuration.php',
					array('courseid' => $courseid),
					get_string('back', 'block_exabis_student_review'));

block_exabis_student_review_print_footer();
