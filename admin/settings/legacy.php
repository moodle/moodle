<?php // $Id$

// This file defines settingpages and externalpages under the "userinterface" category
// "generalsettings" settingpage
$temp = new admin_settingpage('generallegacy', get_string('generallegacy','admin'));
$temp->add(new admin_setting_sitesettext('teacher', get_string('wordforteacher'), get_string('wordforteachereg'), '', PARAM_ALPHA));
$temp->add(new admin_setting_sitesettext('teachers', get_string('wordforteachers'), get_string('wordforteacherseg'), '', PARAM_ALPHA));
$temp->add(new admin_setting_sitesettext('student', get_string('wordforstudent'), get_string('wordforstudenteg'), '', PARAM_ALPHA));
$temp->add(new admin_setting_sitesettext('students', get_string('wordforstudents'), get_string('wordforstudentseg'), '', PARAM_ALPHA));
$ADMIN->add('legacy', $temp);


?>