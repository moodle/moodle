<?php // $Id$

// This file defines settingpages and externalpages under the "courses" category

// "courserequests" settingpage
$temp = new admin_settingpage('courserequests', get_string('courserequests', 'admin'));
$temp->add(new admin_setting_configcheckbox('enablecourserequests', get_string('enablecourserequests', 'admin'), get_string('configenablecourserequests', 'admin'), 0));
require_once($CFG->dirroot.'/course/lib.php');
$temp->add(new admin_setting_configselect('defaultrequestcategory', get_string('defaultrequestcategory', 'admin'), get_string('configdefaultrequestcategory', 'admin'), 1, make_categories_options()));
$temp->add(new admin_setting_configtext('requestedteachername', get_string('requestedteachername', 'admin'), get_string('configrequestedteachername', 'admin'), '', PARAM_ALPHA));
$temp->add(new admin_setting_configtext('requestedteachersname', get_string('requestedteachersname', 'admin'), get_string('configrequestedteachersname', 'admin'), '', PARAM_ALPHA));
$temp->add(new admin_setting_configtext('requestedstudentname', get_string('requestedstudentname', 'admin'), get_string('configrequestedstudentname', 'admin'), '', PARAM_ALPHA));
$temp->add(new admin_setting_configtext('requestedstudentsname', get_string('requestedstudentsname', 'admin'), get_string('configrequestedstudentsname', 'admin'), '', PARAM_ALPHA));
$ADMIN->add('courses', $temp);



$ADMIN->add('courses', new admin_externalpage('coursemgmt', get_string('coursemgmt', 'admin'), $CFG->wwwroot . '/course/index.php?categoryedit=on'));
$ADMIN->add('courses', new admin_externalpage('enrolment', get_string('enrolment', 'admin'), $CFG->wwwroot . '/admin/enrol.php'));



?>