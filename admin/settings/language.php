<?php

// This file defines settingpages and externalpages under the "appearance" category

if ($hassiteconfig) {

    // "languageandlocation" settingpage
    $temp = new admin_settingpage('langsettings', new lang_string('languagesettings', 'admin'));
    $temp->add(new admin_setting_configcheckbox('autolang', new lang_string('autolang', 'admin'), new lang_string('configautolang', 'admin'), 1));
    $temp->add(new admin_setting_configselect('lang', new lang_string('lang', 'admin'), new lang_string('configlang', 'admin'), current_language(), get_string_manager()->get_list_of_translations())); // $CFG->lang might be set in installer already, default en is in setup.php
    $temp->add(new admin_setting_configcheckbox('langmenu', new lang_string('langmenu', 'admin'), new lang_string('configlangmenu', 'admin'), 1));
    $temp->add(new admin_setting_langlist());
    $temp->add(new admin_setting_configcheckbox('langcache', new lang_string('langcache', 'admin'), new lang_string('langcache_desc', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('langstringcache', new lang_string('langstringcache', 'admin'), new lang_string('configlangstringcache', 'admin'), 1));
    $temp->add(new admin_setting_configtext('locale', new lang_string('localetext', 'admin'), new lang_string('configlocale', 'admin'), '', PARAM_FILE));
    $temp->add(new admin_setting_configselect('latinexcelexport', new lang_string('latinexcelexport', 'admin'), new lang_string('configlatinexcelexport', 'admin'), '0', array('0'=>'Unicode','1'=>'Latin')));

    $ADMIN->add('language', $temp);

} // end of speedup
