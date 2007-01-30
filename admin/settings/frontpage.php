<?php // $Id$

// This file defines everything related to frontpage

if (get_site()) { //do not use during installation

$frontpagecontext = get_context_instance(CONTEXT_COURSE, SITEID);

// "frontpage" settingpage
$temp = new admin_settingpage('frontpagesettings', get_string('frontpagesettings','admin'), 'moodle/course:update', false, $frontpagecontext);
$temp->add(new admin_setting_sitesettext('fullname', get_string('fullsitename'), '', ''));
$temp->add(new admin_setting_sitesettext('shortname', get_string('shortsitename'), '', ''));
$temp->add(new admin_setting_special_frontpagedesc());
$temp->add(new admin_setting_courselist_frontpage(false)); // non-loggedin version of the setting (that's what the parameter is for :) )
$temp->add(new admin_setting_courselist_frontpage(true)); // loggedin version of the setting
$temp->add(new admin_setting_sitesetcheckbox('numsections', get_string('sitesection'), get_string('sitesectionhelp','admin'), 1));
$temp->add(new admin_setting_sitesetselect('newsitems', get_string('newsitemsnumber'), '', 3,
     array('0' => '0',
           '1' => '1',
           '2' => '2',
           '3' => '3',
           '4' => '4',
           '5' => '5',
           '6' => '6',
           '7' => '7',
           '8' => '8',
           '9' => '9',
           '10' => '10')));
$temp->add(new admin_setting_configtext('coursesperpage', get_string('coursesperpage', 'admin'), get_string('configcoursesperpage', 'admin'), 20, PARAM_INT));
$temp->add(new admin_setting_configcheckbox('allowvisiblecoursesinhiddencategories', get_string('allowvisiblecoursesinhiddencategories', 'admin'), get_string('configvisiblecourses', 'admin'), 0));
$ADMIN->add('frontpage', $temp);

$ADMIN->add('frontpage', new admin_externalpage('frontpageroles', get_string('frontpageroles', 'admin'), "$CFG->wwwroot/$CFG->admin/roles/assign.php?contextid=" . $frontpagecontext->id, 'moodle/role:assign', false, $frontpagecontext));

$ADMIN->add('frontpage', new admin_externalpage('frontpagebackup', get_string('frontpagebackup', 'admin'), $CFG->wwwroot.'/backup/backup.php?id='.SITEID, 'moodle/site:backup', false, $frontpagecontext));

$ADMIN->add('frontpage', new admin_externalpage('frontpagerestore', get_string('frontpagerestore', 'admin'), $CFG->wwwroot.'/files/index.php?id='.SITEID.'&amp;wdir=/backupdata', 'moodle/site:restore', false, $frontpagecontext));

$ADMIN->add('frontpage', new admin_externalpage('sitefiles', get_string('sitefiles'), $CFG->wwwroot . '/files/index.php?id=' . SITEID, 'moodle/course:managefiles', false, $frontpagecontext));

}
?>