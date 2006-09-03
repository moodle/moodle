<?php // $Id$

// This file defines settingpages and externalpages under the "users" category

global $USER;


$ADMIN->add('users', new admin_externalpage('authentication', get_string('authentication'), $CFG->wwwroot . '/admin/auth.php'));
$ADMIN->add('users', new admin_externalpage('edituser', get_string('edituser'), $CFG->wwwroot . '/admin/user.php'));
$ADMIN->add('users', new admin_externalpage('addnewuser', get_string('addnewuser'), $CFG->wwwroot . '/admin/user.php?newuser=true&amp;sesskey='. (isset($USER->sesskey) ? $USER->sesskey : '')));
$ADMIN->add('users', new admin_externalpage('uploadusers', get_string('uploadusers'), $CFG->wwwroot . '/admin/uploaduser.php'));
$ADMIN->add('users', new admin_externalpage('manageroles', get_string('manageroles'), $CFG->wwwroot . '/admin/roles/manage.php'));
$ADMIN->add('users', new admin_externalpage('assignsitewideroles', get_string('assignsiteroles'), $CFG->wwwroot . '/admin/roles/assign.php?contextid=' . SITEID));

?>