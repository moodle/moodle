<?php

// This file defines settingpages and externalpages under the "appearance" category

if ($hassiteconfig) {

    // "languageandlocation" settingpage
    $temp = new admin_settingpage('langsettings', get_string('languagesettings', 'admin'));
    $temp->add(new admin_setting_configcheckbox('autolang', get_string('autolang', 'admin'), get_string('configautolang', 'admin'), 1));
    $temp->add(new admin_setting_configselect('lang', get_string('lang', 'admin'), get_string('configlang', 'admin'), current_language(), get_string_manager()->get_list_of_translations())); // $CFG->lang might be set in installer already, default en is in setup.php
    $temp->add(new admin_setting_configcheckbox('langmenu', get_string('langmenu', 'admin'), get_string('configlangmenu', 'admin'), 1));
    $temp->add(new admin_setting_langlist());
    $temp->add(new admin_setting_configcheckbox('langcache', get_string('langcache', 'admin'), get_string('langcache_desc', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('langstringcache', get_string('langstringcache', 'admin'), get_string('configlangstringcache', 'admin'), 1));
    $temp->add(new admin_setting_configtext('locale', get_string('localetext', 'admin'), get_string('configlocale', 'admin'), '', PARAM_FILE));
    $temp->add(new admin_setting_configselect('latinexcelexport', get_string('latinexcelexport', 'admin'), get_string('configlatinexcelexport', 'admin'), '0', array('0'=>'Unicode','1'=>'Latin')));

    $ADMIN->add('language', $temp);

    $ADMIN->add('language', new admin_externalpage('langimport', get_string('langpacks', 'admin'), "$CFG->wwwroot/$CFG->admin/langimport.php"));

    // Hidden multilang upgrade page.
    $ADMIN->add('language', new admin_externalpage('multilangupgrade', get_string('multilangupgrade', 'admin'), $CFG->wwwroot.'/'.$CFG->admin.'/multilangupgrade.php', 'moodle/site:config', !empty($CFG->filter_multilang_converted)));

} // end of speedup
