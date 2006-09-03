<?php // $Id$

// This file defines settingpages and externalpages under the "settings" category

// "frontpage" settingpage
$temp = new admin_settingpage('frontpage', get_string('frontpage','admin'));
$temp->add(new admin_setting_sitesettext('fullname', get_string('fullsitename'), get_string('fullsitenamehelp'), '', PARAM_NOTAGS));
$temp->add(new admin_setting_sitesettext('shortname', get_string('shortsitename'), get_string('shortsitenamehelp'), '', PARAM_NOTAGS));
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
$temp->add(new admin_setting_courselist_frontpage(false)); // non-loggedin version of the setting (that's what the parameter is for :) )
$temp->add(new admin_setting_courselist_frontpage(true)); // loggedin version of the setting
$temp->add(new admin_setting_configcheckbox('mymoodleredirect', get_string('mymoodleredirect', 'admin'), get_string('configmymoodleredirect', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('allusersaresitestudents', get_string('allusersaresitestudents', 'admin'), get_string('configallusersaresitestudents','admin'), 1));
$temp->add(new admin_setting_configselect('showsiteparticipantslist', get_string('showsiteparticipantslist', 'admin'), get_string('configshowsiteparticipantslist', 'admin'), 0, array(0 => get_string('siteteachers'), 1 => get_string('allteachers'), 2 => get_string('studentsandteachers'))));
$ADMIN->add('settings', $temp);


// user settings
$temp = new admin_settingpage('usersettings', get_string('usersettings','admin'));
$temp->add(new admin_setting_configcheckbox('autologinguests', get_string('autologinguests', 'admin'), get_string('configautologinguests', 'admin'), 0));
$temp->add(new admin_setting_configmultiselect('hiddenuserfields', get_string('hiddenuserfields', 'admin'), get_string('confighiddenuserfields', 'admin'), array(), array('none' => get_string('none'),
                                                                                                                                                                          'description' => get_string('description'),
                                                                                                                                                                          'city' => get_string('city'),
                                                                                                                                                                          'country' => get_string('country'),
                                                                                                                                                                          'webpage' => get_string('webpage'),
                                                                                                                                                                          'icqnumber' => get_string('icqnumber'),
                                                                                                                                                                          'skypeid' => get_string('skypeid'),
                                                                                                                                                                          'yahooid' => get_string('yahooid'),
                                                                                                                                                                          'aimid' => get_string('aimid'),
                                                                                                                                                                          'msnid' => get_string('msnid'),
                                                                                                                                                                          'lastaccess' => get_string('lastaccess'))));
$temp->add(new admin_setting_configcheckbox('allowunenroll', get_string('allowunenroll', 'admin'), get_string('configallowunenroll', 'admin'), 1));
$temp->add(new admin_setting_configtext('maxbytes', get_string('maxbytes', 'admin'), get_string('configmaxbytes', 'admin'), 0, PARAM_INT));
$temp->add(new admin_setting_configcheckbox('messaging', get_string('messaging', 'admin'), get_string('configmessaging','admin'), 1));
$temp->add(new admin_setting_configselect('maxeditingtime', get_string('maxeditingtime','admin'), get_string('configmaxeditingtime','admin'), 1800, array(60 => get_string('numminutes', '', 1),
                                                                                                                                                          300 => get_string('numminutes', '', 5),
                                                                                                                                                          900 => get_string('numminutes', '', 15),
                                                                                                                                                          1800 => get_string('numminutes', '', 30),
                                                                                                                                                          2700 => get_string('numminutes', '', 45),
                                                                                                                                                          3600 => get_string('numminutes', '', 60))));
$temp->add(new admin_setting_configselect('deleteunconfirmed', get_string('deleteunconfirmed', 'admin'), get_string('configdeleteunconfirmed', 'admin'), 168, array(0 => get_string('never'),
                                                                                                                                                                    168 => get_string('numdays', '', 7),
                                                                                                                                                                    144 => get_string('numdays', '', 6),
                                                                                                                                                                    120 => get_string('numdays', '', 5),
                                                                                                                                                                    96 => get_string('numdays', '', 4),
                                                                                                                                                                    72 => get_string('numdays', '', 3),
                                                                                                                                                                    48 => get_string('numdays', '', 2),
                                                                                                                                                                    24 => get_string('numdays', '', 1),
                                                                                                                                                                    12 => get_string('numhours', '', 12),
                                                                                                                                                                    6 => get_string('numhours', '', 6),
                                                                                                                                                                    1 => get_string('numhours', '', 1))));
$temp->add(new admin_setting_configselect('fullnamedisplay', get_string('fullnamedisplay', 'admin'), get_string('configfullnamedisplay', 'admin'), 'firstname lastname', array('language' => get_string('language'),
                                                                                                                                                                               'firstname lastname' => get_string('firstname') . ' + ' . get_string('lastname'),
                                                                                                                                                                               'lastname firstname' => get_string('lastname') . ' + ' . get_string('firstname'),
                                                                                                                                                                               'firstname' => get_string('firstname'))));
$temp->add(new admin_setting_configcheckbox('extendedusernamechars', get_string('extendedusernamechars', 'admin'), get_string('configextendedusernamechars', 'admin'), 0));
$ADMIN->add('settings', $temp);



// "themesettings" settingpage
$temp = new admin_settingpage('themesettings', get_string('themesettings'));
$temp->add(new admin_setting_configtext('themelist', get_string('themelist', 'admin'), get_string('configthemelist','admin'), '', PARAM_NOTAGS));
$temp->add(new admin_setting_configcheckbox('allowuserthemes', get_string('allowuserthemes', 'admin'), get_string('configallowuserthemes', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('allowcoursethemes', get_string('allowcoursethemes', 'admin'), get_string('configallowcoursethemes', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('allowuserblockhiding', get_string('allowuserblockhiding', 'admin'), get_string('configallowuserblockhiding', 'admin'), 1));
$temp->add(new admin_setting_configcheckbox('showblocksonmodpages', get_string('showblocksonmodpages', 'admin'), get_string('configshowblocksonmodpages', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('tabselectedtofront', get_string('tabselectedtofronttext', 'admin'), get_string('tabselectedtofront', 'admin'), 0));
$ADMIN->add('settings', $temp);
$ADMIN->add('settings', new admin_externalpage('themeselector', get_string('defaulttheme','admin'), $CFG->wwwroot . '/theme/index.php'));




// calendar settingpage
$temp = new admin_settingpage('calendar', get_string('calendar', 'calendar'));
$temp->add(new admin_setting_special_adminseesall());
$temp->add(new admin_setting_configselect('startwday', get_string('startwday', 'admin'), get_string('helpstartofweek', 'admin'), 1, array(0 => get_string('sunday', 'calendar'),
                                                                                                                                          1 => get_string('monday', 'calendar'),
                                                                                                                                          2 => get_string('tuesday', 'calendar'),
                                                                                                                                          3 => get_string('wednesday', 'calendar'),
                                                                                                                                          4 => get_string('thursday', 'calendar'),
                                                                                                                                          5 => get_string('friday', 'calendar'),
                                                                                                                                          6 => get_string('saturday', 'calendar'))));
$temp->add(new admin_setting_configtext('calendar_lookahead', get_string('calendar_lookahead', 'admin'), get_string('helpupcominglookahead', 'admin'), 10, PARAM_INT));
$temp->add(new admin_setting_configtext('calendar_maxevents', get_string('calendar_maxevents', 'admin'), get_string('helpupcomingmaxevents', 'admin'), 5, PARAM_INT));
$temp->add(new admin_setting_special_calendar_weekend());

$ADMIN->add('settings', $temp);


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
$ADMIN->add('settings', $temp);



$ADMIN->add('settings', new admin_externalpage('timezoneimport', get_string('updatetimezones', 'admin'), $CFG->wwwroot . '/admin/timezoneimport.php'));

// "email" settingpage
$temp = new admin_settingpage('emailsettings', get_string('emailsettings','admin'));
$temp->add(new admin_setting_configtext('smtphosts', get_string('smtphosts', 'admin'), get_string('configsmtphosts', 'admin'), '', PARAM_HOST));
$temp->add(new admin_setting_configtext('smtpuser', get_string('smtpuser', 'admin'), get_string('configsmtpuser', 'admin'), '', PARAM_NOTAGS));
$temp->add(new admin_setting_configtext('smtppass', get_string('smtppass', 'admin'), get_string('configsmtpuser', 'admin'), '', PARAM_RAW));
$temp->add(new admin_setting_configtext('noreplyaddress', get_string('noreplyaddress', 'admin'), get_string('confignoreplyaddress', 'admin'), 'noreply@' . $_SERVER['HTTP_HOST'], PARAM_NOTAGS));
$temp->add(new admin_setting_configtext('allowemailaddresses', get_string('allowemailaddresses', 'admin'), get_string('configallowemailaddresses', 'admin'), '', PARAM_NOTAGS));
$temp->add(new admin_setting_configtext('denyemailaddresses', get_string('denyemailaddresses', 'admin'), get_string('configdenyemailaddresses', 'admin'), '', PARAM_NOTAGS));
$temp->add(new admin_setting_configselect('digestmailtime', get_string('digestmailtime', 'admin'), get_string('configdigestmailtime', 'admin'), 17, array('00' => '00',
                                                                                                                                                          '01' => '01',
                                                                                                                                                          '02' => '02',
                                                                                                                                                          '03' => '03',
                                                                                                                                                          '04' => '04',
                                                                                                                                                          '05' => '05',
                                                                                                                                                          '06' => '06',
                                                                                                                                                          '07' => '07',
                                                                                                                                                          '08' => '08',
                                                                                                                                                          '09' => '09',
                                                                                                                                                          '10' => '10',
                                                                                                                                                          '11' => '11',
                                                                                                                                                          '12' => '12',
                                                                                                                                                          '13' => '13',
                                                                                                                                                          '14' => '14',
                                                                                                                                                          '15' => '15',
                                                                                                                                                          '16' => '16',
                                                                                                                                                          '17' => '17',
                                                                                                                                                          '18' => '18',
                                                                                                                                                          '19' => '19',
                                                                                                                                                          '20' => '20',
                                                                                                                                                          '21' => '21',
                                                                                                                                                          '22' => '22',
                                                                                                                                                          '23' => '23')));
if (!empty($CFG->unicodedb)) { // These options are only available if running under unicodedb
    unset($options);
    unset($charsets);
    $charsets = get_list_of_charsets();
    $options['0'] = get_string('none');
    $options = array_merge($options, $charsets);
    $temp->add(new admin_setting_configselect('sitemailcharset', get_string('sitemailcharset', 'admin'), get_string('configsitemailcharset','admin'), '', $options));
    $temp->add(new admin_setting_configcheckbox('allowusermailcharset', get_string('allowusermailcharset', 'admin'), get_string('configallowusermailcharset', 'admin'), 0));
}
$ADMIN->add('settings', $temp);

// security related settings
$temp = new admin_settingpage('security', get_string('security','admin'));
$temp->add(new admin_setting_configcheckbox('forcelogin', get_string('forcelogin', 'admin'), get_string('configforcelogin', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('forceloginforprofiles', get_string('forceloginforprofiles', 'admin'), get_string('configforceloginforprofiles', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('opentogoogle', get_string('opentogoogle', 'admin'), get_string('configopentogoogle', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('allowobjectembed', get_string('allowobjectembed', 'admin'), get_string('configallowobjectembed', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('enabletrusttext', get_string('enabletrusttext', 'admin'), get_string('configenabletrusttext', 'admin'), 0));
$temp->add(new admin_setting_configselect('displayloginfailures', get_string('displayloginfailures', 'admin'), get_string('configdisplayloginfailures', 'admin'), '', array('' => get_string('nobody'),
                                                                                                                                                                            'admin' => get_string('administrators'),
                                                                                                                                                                            'teacher' => get_string('administratorsandteachers'),
                                                                                                                                                                            'everybody' => get_string('everybody'))));
$temp->add(new admin_setting_configselect('notifyloginfailures', get_string('notifyloginfailures', 'admin'), get_string('confignotifyloginfailures', 'admin'), '', array('' => get_string('nobody'),
                                                                                                                                                                         'mainadmin' => get_string('administrator'),
                                                                                                                                                                         'alladmins' => get_string('administratorsall'))));
$options = array();
for ($i = 1; $i <= 100; $i++) {
    $options[$i] = $i;
}
$temp->add(new admin_setting_configselect('notifyloginthreshold', get_string('notifyloginthreshold', 'admin'), get_string('confignotifyloginthreshold', 'admin'), '10', $options));

$temp->add(new admin_setting_configcheckbox('loginhttps', get_string('loginhttps', 'admin'), get_string('configloginhttps', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('secureforms', get_string('secureforms', 'admin'), get_string('configsecureforms', 'admin'), 0));
$temp->add(new admin_setting_configtext('sitepolicy', get_string('sitepolicy', 'admin'), get_string('configsitepolicy', 'admin'), '', PARAM_URL));


$ADMIN->add('settings', $temp);

?>