<?php // $Id$

// This file defines settingpages and externalpages under the "appearance" category

// "languageandlocation" settingpage
$temp = new admin_settingpage('langsettings', get_string('languagesettings', 'admin'));
$temp->add(new admin_setting_configselect('lang', get_string('lang', 'admin'), get_string('configlang', 'admin'), current_language(), get_list_of_languages())); // $CFG->lang might be set in installer already, default en or en_utf8 is in setup.php
$temp->add(new admin_setting_configcheckbox('langmenu', get_string('langmenu', 'admin'), get_string('configlangmenu', 'admin'), 1));
$temp->add(new admin_setting_configtext('langlist', get_string('langlist', 'admin'), get_string('configlanglist', 'admin'), '', PARAM_NOTAGS));
$temp->add(new admin_setting_configcheckbox('langcache', get_string('langcache', 'admin'), get_string('configlangcache', 'admin'), 1));
$temp->add(new admin_setting_configtext('locale', get_string('localetext', 'admin'), get_string('configlocale', 'admin'), '', PARAM_FILE));

// new CFG variable for excel encoding
$temp->add(new admin_setting_configselect('latinexcelexport', get_string('latinexcelexport', 'admin'), get_string('configlatinexcelexport', 'admin'), '0', array('0'=>'Unicode','1'=>'Latin')));


$ADMIN->add('language', $temp);

$ADMIN->add('language', new admin_externalpage('langedit', get_string('langedit', 'admin'), "$CFG->wwwroot/$CFG->admin/lang.php"));
$ADMIN->add('language', new admin_externalpage('langimport', get_string('langpacks', 'admin'), "$CFG->wwwroot/$CFG->admin/langimport.php"));




?>
