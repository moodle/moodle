<?php // $Id$

// This file defines settingpages and externalpages under the "courses" category


$ADMIN->add('courses', new admin_externalpage('coursemgmt', get_string('coursemgmt', 'admin'), $CFG->wwwroot . '/course/index.php?categoryedit=on'));

$ADMIN->add('courses', new admin_externalpage('enrolment', get_string('enrolments'), $CFG->wwwroot . '/'.$CFG->admin.'/enrol.php'));

// "courserequests" settingpage
$temp = new admin_settingpage('courserequest', get_string('courserequest'));
$temp->add(new admin_setting_configcheckbox('enablecourserequests', get_string('enablecourserequests', 'admin'), get_string('configenablecourserequests', 'admin'), 0));
require_once($CFG->dirroot.'/course/lib.php');
$temp->add(new admin_setting_configselect('defaultrequestcategory', get_string('defaultrequestcategory', 'admin'), get_string('configdefaultrequestcategory', 'admin'), 1, make_categories_options()));
$ADMIN->add('courses', $temp);



// "backups" settingpage
$temp = new admin_settingpage('backups', get_string('backups','admin'));
$temp->add(new admin_setting_backupcheckbox('backup_sche_modules', get_string('includemodules'), get_string('backupincludemoduleshelp'), 0));
$temp->add(new admin_setting_backupcheckbox('backup_sche_withuserdata', get_string('includemoduleuserdata'), get_string('backupincludemoduleuserdatahelp'), 0));
$temp->add(new admin_setting_backupcheckbox('backup_sche_metacourse', get_string('metacourse'), get_string('backupmetacoursehelp'), 0));
$temp->add(new admin_setting_backupselect('backup_sche_users', get_string('users'), get_string('backupusershelp'), 0, array(0 => get_string('all'),
                                                                                                                            1 => get_string('course'))));
$temp->add(new admin_setting_backupcheckbox('backup_sche_logs', get_string('logs'), get_string('backuplogshelp'), 0));
$temp->add(new admin_setting_backupcheckbox('backup_sche_userfiles', get_string('userfiles'), get_string('backupuserfileshelp'), 0));
$temp->add(new admin_setting_backupcheckbox('backup_sche_coursefiles', get_string('coursefiles'), get_string('backupcoursefileshelp'), 0));
$temp->add(new admin_setting_backupcheckbox('backup_sche_messages', get_string('messages', 'message'), get_string('backupmessageshelp','message'), 0));
$temp->add(new admin_setting_backupselect('backup_sche_keep', get_string('keep'), get_string('backupkeephelp'), 1, array(0 => get_string('all'),
                                                                                                                         1 => '1',
                                                                                                                         2 => '2',
                                                                                                                         5 => '5',
                                                                                                                         10 => '10',
                                                                                                                         20 => '20',
                                                                                                                         30 => '30',
                                                                                                                         40 => '40',
                                                                                                                         50 => '50',
                                                                                                                         100 => '100',
                                                                                                                         200 => '200',
                                                                                                                         300 => '300',
                                                                                                                         400 => '400',
                                                                                                                         500 => '500')));
$temp->add(new admin_setting_backupcheckbox('backup_sche_active', get_string('active'), get_string('backupactivehelp'), 0));
$temp->add(new admin_setting_special_backupdays());
$temp->add(new admin_setting_special_backuptime());
$temp->add(new admin_setting_special_backupsaveto());
$ADMIN->add('courses', $temp);


?>
