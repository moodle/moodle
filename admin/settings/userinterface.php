<?php // $Id$

// This file defines settingpages and externalpages under the "userinterface" category

// "frontpage" settingpage
$temp = new admin_settingpage('frontpage', get_string('frontpage','admin'));
$temp->add(new admin_setting_special_frontpagedesc());
$temp->add(new admin_setting_sitesetcheckbox('numsections', get_string('sitesection'), get_string('sitesectionhelp'), 1));
$temp->add(new admin_setting_sitesetselect('newsitems', get_string('newsitemsnumber'), get_string('newsitemsnumberhelp'), 3, array('0' => '0 ' . get_string('newsitems'),
                                                                                                                                '1' => '1 ' . get_string('newsitem'),
                                                                                                                                '2' => '2 ' . get_string('newsitems'),
                                                                                                                                '3' => '3 ' . get_string('newsitems'),
                                                                                                                                '4' => '4 ' . get_string('newsitems'),
                                                                                                                                '5' => '5 ' . get_string('newsitems'),
                                                                                                                                '6' => '6 ' . get_string('newsitems'),
                                                                                                                                '7' => '7 ' . get_string('newsitems'),
                                                                                                                                '8' => '8 ' . get_string('newsitems'),
                                                                                                                                '9' => '9 ' . get_string('newsitems'),
                                                                                                                                '10' => '10 ' . get_string('newsitems'))));
$temp->add(new admin_setting_special_frontpage(false)); // non-loggedin version of the setting (that's what the parameter is for :) )
$temp->add(new admin_setting_special_frontpage(true)); // loggedin version of the setting
$ADMIN->add('userinterface', $temp);


// "generalsettings" settingpage
$temp = new admin_settingpage('generalsettings', get_string('generalsettings','admin'));
$temp->add(new admin_setting_sitesettext('fullname', get_string('fullsitename'), get_string('fullsitenamehelp'), '', PARAM_NOTAGS));
$temp->add(new admin_setting_sitesettext('shortname', get_string('shortsitename'), get_string('shortsitenamehelp'), '', PARAM_NOTAGS));
$temp->add(new admin_setting_sitesettext('teacher', get_string('wordforteacher'), get_string('wordforteachereg'), '', PARAM_ALPHA));
$temp->add(new admin_setting_sitesettext('teachers', get_string('wordforteachers'), get_string('wordforteacherseg'), '', PARAM_ALPHA));
$temp->add(new admin_setting_sitesettext('student', get_string('wordforstudent'), get_string('wordforstudenteg'), '', PARAM_ALPHA));
$temp->add(new admin_setting_sitesettext('students', get_string('wordforstudents'), get_string('wordforstudentseg'), '', PARAM_ALPHA));
$ADMIN->add('userinterface', $temp);


// "filtersettings" settingpage
$temp = new admin_settingpage('filtersettings', get_string('filtersettings', 'admin'));
$temp->add(new admin_setting_configselect('cachetext', get_string('cachetext', 'admin'), get_string('configcachetext', 'admin'), 60, array(604800 => get_string('numdays','',7),
                                                                                                                                       86400 => get_string('numdays','',1),
                                                                                                                                       43200 => get_string('numhours','',12),
                                                                                                                                       10800 => get_string('numhours','',3),
                                                                                                                                       7200 => get_string('numhours','',2),
                                                                                                                                       3600 => get_string('numhours','',1),
                                                                                                                                       2700 => get_string('numminutes','',45),
                                                                                                                                       1800 => get_string('numminutes','',30),
                                                                                                                                       900 => get_string('numminutes','',15),
                                                                                                                                       600 => get_string('numminutes','',10),
                                                                                                                                       540 => get_string('numminutes','',9),
                                                                                                                                       480 => get_string('numminutes','',8),
                                                                                                                                       420 => get_string('numminutes','',7),
                                                                                                                                       360 => get_string('numminutes','',6),
                                                                                                                                       300 => get_string('numminutes','',5),
                                                                                                                                       240 => get_string('numminutes','',4),
                                                                                                                                       180 => get_string('numminutes','',3),
                                                                                                                                       120 => get_string('numminutes','',2),
                                                                                                                                       60 => get_string('numminutes','',1),
                                                                                                                                       30 => get_string('numseconds','',30),
                                                                                                                                       0 => get_string('no'))));
$temp->add(new admin_setting_configselect('filteruploadedfiles', get_string('filteruploadedfiles', 'admin'), get_string('configfilteruploadedfiles', 'admin'), 0, array('0' => get_string('none'),
                                                                                                                                                                     '1' => get_string('allfiles'),
                                                                               																						 '2' => get_string('htmlfilesonly'))));
$temp->add(new admin_setting_configcheckbox('filtermatchoneperpage', get_string('filtermatchoneperpage', 'admin'), get_string('configfiltermatchoneperpage', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('filtermatchonepertext', get_string('filtermatchonepertext', 'admin'), get_string('configfiltermatchonepertext', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('filterall', get_string('filterall', 'admin'), get_string('configfilterall', 'admin'), 0));
$ADMIN->add('userinterface', $temp);


// "themesettings" settingpage
$temp = new admin_settingpage('themesettings', get_string('themes'));
$temp->add(new admin_setting_configtext('themelist', get_string('themelist', 'admin'), get_string('configthemelist','admin'), '', PARAM_NOTAGS));
$temp->add(new admin_setting_configcheckbox('allowuserthemes', get_string('allowuserthemes', 'admin'), get_string('configallowuserthemes', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('allowcoursethemes', get_string('allowcoursethemes', 'admin'), get_string('configallowcoursethemes', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('allowuserblockhiding', get_string('allowuserblockhiding', 'admin'), get_string('configallowuserblockhiding', 'admin'), 1));
$temp->add(new admin_setting_configcheckbox('showblocksonmodpages', get_string('showblocksonmodpages', 'admin'), get_string('configshowblocksonmodpages', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('tabselectedtofront', get_string('tabselectedtofronttext', 'admin'), get_string('tabselectedtofront', 'admin'), 0));
$ADMIN->add('userinterface', $temp);



// "htmleditor" settingpage
$temp = new admin_settingpage('htmleditor', get_string('htmleditor', 'admin'));
$temp->add(new admin_setting_configcheckbox('htmleditor', get_string('usehtmleditor', 'admin'), get_string('confightmleditor','admin'), 1));
$temp->add(new admin_setting_configtext('editorbackgroundcolor', get_string('editorbackgroundcolor', 'admin'), get_string('edhelpbgcolor'), '#ffffff', PARAM_NOTAGS));
$temp->add(new admin_setting_configtext('editorfontfamily', get_string('editorfontfamily', 'admin'), get_string('edhelpfontfamily'), 'Trebuchet MS,Verdana,Arial,Helvetica,sans-serif', PARAM_NOTAGS));
$temp->add(new admin_setting_configtext('editorfontsize', get_string('editorfontsize', 'admin'), get_string('edhelpfontsize'), '', PARAM_NOTAGS));
$temp->add(new admin_setting_special_editorfontlist());
$temp->add(new admin_setting_configcheckbox('editorkillword', get_string('editorkillword', 'admin'), get_string('edhelpcleanword'), 1));
if (!empty($CFG->aspellpath)) { // make aspell settings disappear if path isn't set
  $temp->add(new admin_setting_configcheckbox('editorspelling', get_string('editorspelling', 'admin'), get_string('editorspellinghelp', 'admin'), 0));
  $temp->add(new admin_setting_special_editordictionary());
}
$temp->add(new admin_setting_special_editorhidebuttons());
$ADMIN->add('userinterface', $temp);



// "languageandlocation" settingpage
$temp = new admin_settingpage('languageandlocation', get_string('languageandlocation', 'admin'));
$temp->add(new admin_setting_configselect('lang', get_string('lang', 'admin'), get_string('configlang', 'admin'), $CFG->lang, get_list_of_languages())); // $CFG->lang might be set in installer already, default en or en_utf8 is in setup.php
$temp->add(new admin_setting_configcheckbox('langmenu', get_string('langmenu', 'admin'), get_string('configlangmenu', 'admin'), 1));
$temp->add(new admin_setting_configtext('langlist', get_string('langlist', 'admin'), get_string('configlanglist', 'admin'), '', PARAM_NOTAGS));
$temp->add(new admin_setting_configcheckbox('langcache', get_string('langcache', 'admin'), get_string('configlangcache', 'admin'), 1));
$temp->add(new admin_setting_configtext('locale', get_string('localetext', 'admin'), get_string('configlocale', 'admin'), '', PARAM_FILE));
$options = get_list_of_timezones();
$options[99] = get_string('serverlocaltime');
$temp->add(new admin_setting_configselect('timezone', get_string('timezone','admin'), get_string('configtimezone', 'admin'), 99, $options));
$options = get_list_of_timezones();
$options[0] = get_string('choose') .'...';
$temp->add(new admin_setting_configselect('country', get_string('country', 'admin'), get_string('configcountry', 'admin'), 0, $options));
$options = get_list_of_timezones();
$options[99] = get_string('timezonenotforced', 'admin');
$temp->add(new admin_setting_configselect('forcetimezone', get_string('forcetimezone', 'admin'), get_string('helpforcetimezone', 'admin'), 99, $options));
$ADMIN->add('userinterface', $temp);



$ADMIN->add('userinterface', new admin_externalpage('timezoneimport', get_string('updatetimezones', 'admin'), $CFG->wwwroot . '/admin/timezoneimport.php'));
$ADMIN->add('userinterface', new admin_externalpage('themeselector', get_string('themeselector','admin'), $CFG->wwwroot . '/theme/index.php'));
$ADMIN->add('userinterface', new admin_externalpage('langedit', get_string('langedit', 'admin'), $CFG->wwwroot . '/admin/lang.php'));
$ADMIN->add('userinterface', new admin_externalpage('langimport', get_string('langimport', 'admin'), $CFG->wwwroot . '/admin/langimport.php'));

?>