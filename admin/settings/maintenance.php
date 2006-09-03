<?php // $Id$

// This file defines settingpages and externalpages under the "maintenance" category



// "backups" settingpage
if (empty($CFG->disablescheduledbackups)) {   // Defined in config.php
    $temp = new admin_settingpage('backup', get_string('backup'));
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
    $ADMIN->add('maintenance', $temp);
}

$ADMIN->add('maintenance', new admin_externalpage('reports', get_string('reports'), $CFG->wwwroot . '/admin/report.php'));


// "performanceandstats" settingpage
$temp = new admin_settingpage('performanceandstats', get_string('performanceandstats', 'admin'));
$temp->add(new admin_setting_special_debug());
$temp->add(new admin_setting_special_perfdebug());
$temp->add(new admin_setting_configcheckbox('enablestats', get_string('enablestats', 'admin'), get_string('configenablestats', 'admin'), 0));
$temp->add(new admin_setting_configselect('statsfirstrun', get_string('statsfirstrun', 'admin'), get_string('configstatsfirstrun', 'admin'), 'none', array('none' => get_string('none'),
                                                                                                                                                           60*60*24*7 => get_string('numweeks','moodle',1),
                                                                                                                                                           60*60*24*14 => get_string('numweeks','moodle',2),
                                                                                                                                                           60*60*24*21 => get_string('numweeks','moodle',3),
                                                                                                                                                           60*60*24*28 => get_string('nummonths','moodle',1),
                                                                                                                                                           60*60*24*56 => get_string('nummonths','moodle',2),
                                                                                                                                                           60*60*24*84 => get_string('nummonths','moodle',3),
                                                                                                                                                           60*60*24*112 => get_string('nummonths','moodle',4),
                                                                                                                                                           60*60*24*140 => get_string('nummonths','moodle',5),
                                                                                                                                                           60*60*24*168 => get_string('nummonths','moodle',6),
                                                                                                                                                           'all' => get_string('all') )));
$temp->add(new admin_setting_configselect('statsmaxruntime', get_string('statsmaxruntime', 'admin'), get_string('configstatsmaxruntime', 'admin'), 0, array(0 => get_string('untilcomplete'),
                                                                                                                                                            60*60 => '1 '.get_string('hour'),
                                                                                                                                                            60*60*2 => '2 '.get_string('hours'),
                                                                                                                                                            60*60*3 => '3 '.get_string('hours'),
                                                                                                                                                            60*60*4 => '4 '.get_string('hours'),
                                                                                                                                                            60*60*5 => '5 '.get_string('hours'),
                                                                                                                                                            60*60*6 => '6 '.get_string('hours'),
                                                                                                                                                            60*60*7 => '7 '.get_string('hours'),
                                                                                                                                                            60*60*8 => '8 '.get_string('hours') )));
$temp->add(new admin_setting_configtime('statsruntimestarthour', 'statsruntimestartminute', get_string('statsruntimestart', 'admin'), get_string('configstatsruntimestart', 'admin'), array('h' => 0, 'm' => 0)));
$temp->add(new admin_setting_configtext('statsuserthreshold', get_string('statsuserthreshold', 'admin'), get_string('configstatsuserthreshold', 'admin'), 0, PARAM_INT));
$ADMIN->add('maintenance', $temp);





$ADMIN->add('maintenance', new admin_externalpage('environment', get_string('environment', 'admin'), $CFG->wwwroot . '/admin/environment.php'));
$ADMIN->add('maintenance', new admin_externalpage('sitemaintenancemode', get_string('sitemaintenancemode', 'admin'), $CFG->wwwroot . '/admin/maintenance.php'));


?>