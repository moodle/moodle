<?php // $Id$

// This file defines settingpages and externalpages under the "server" category

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page


// "systempaths" settingpage
$temp = new admin_settingpage('systempaths', get_string('systempaths','admin'));
$temp->add(new admin_setting_configselect('gdversion', get_string('gdversion','admin'), get_string('configgdversion', 'admin'), check_gd_version(), array('0' => get_string('gdnot'),
                                                                                                                                                          '1' => get_string('gd1'),
                                                                                                                                                          '2' => get_string('gd2'))));
$temp->add(new admin_setting_configexecutable('zip', get_string('pathtozip','admin'), get_string('configzip', 'admin'), ''));
$temp->add(new admin_setting_configexecutable('unzip', get_string('pathtounzip','admin'), get_string('configunzip', 'admin'), ''));
$temp->add(new admin_setting_configexecutable('pathtodu', get_string('pathtodu', 'admin'), get_string('configpathtodu', 'admin'), ''));
$temp->add(new admin_setting_configexecutable('aspellpath', get_string('aspellpath', 'admin'), get_string('edhelpaspellpath'), ''));
$ADMIN->add('server', $temp, 0);



// "email" settingpage
$temp = new admin_settingpage('mail', get_string('mail','admin'));
$temp->add(new admin_setting_configtext('smtphosts', get_string('smtphosts', 'admin'), get_string('configsmtphosts', 'admin'), '', PARAM_RAW));
$temp->add(new admin_setting_configtext('smtpuser', get_string('smtpuser', 'admin'), get_string('configsmtpuser', 'admin'), '', PARAM_NOTAGS));
$temp->add(new admin_setting_configpasswordunmask('smtppass', get_string('smtppass', 'admin'), get_string('configsmtpuser', 'admin'), ''));
$temp->add(new admin_setting_configtext('smtpmaxbulk', get_string('smtpmaxbulk', 'admin'), get_string('configsmtpmaxbulk', 'admin'), 1, PARAM_INT));
$temp->add(new admin_setting_configtext('noreplyaddress', get_string('noreplyaddress', 'admin'), get_string('confignoreplyaddress', 'admin'), 'noreply@' . $_SERVER['HTTP_HOST'], PARAM_NOTAGS));
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
$charsets = get_list_of_charsets();
unset($charsets['UTF-8']); // not needed here
$options = array();
$options['0'] = 'UTF-8';
$options = array_merge($options, $charsets);
$temp->add(new admin_setting_configselect('sitemailcharset', get_string('sitemailcharset', 'admin'), get_string('configsitemailcharset','admin'), '0', $options));
$temp->add(new admin_setting_configcheckbox('allowusermailcharset', get_string('allowusermailcharset', 'admin'), get_string('configallowusermailcharset', 'admin'), 0));
$options = array('LF'=>'LF', 'CRLF'=>'CRLF');
$temp->add(new admin_setting_configselect('mailnewline', get_string('mailnewline', 'admin'), get_string('configmailnewline','admin'), 'LF', $options));
if (isloggedin()) {
    global $USER;
    $primaryadminemail = $USER->email;
    $primaryadminname  = fullname($USER, true);

} else {
    // no defaults during installation - admin user must be created first
    $primaryadminemail = NULL;
    $primaryadminname  = NULL;
}
$temp->add(new admin_setting_configtext('supportname', get_string('supportname', 'admin'), get_string('configsupportname', 'admin'), $primaryadminname, PARAM_NOTAGS));
$temp->add(new admin_setting_configtext('supportemail', get_string('supportemail', 'admin'), get_string('configsupportemail', 'admin'), $primaryadminemail, PARAM_NOTAGS));
$temp->add(new admin_setting_configtext('supportpage', get_string('supportpage', 'admin'), get_string('configsupportpage', 'admin'), '', PARAM_URL));
$ADMIN->add('server', $temp, 100);



// "sessionhandling" settingpage
$temp = new admin_settingpage('sessionhandling', get_string('sessionhandling', 'admin'));
$temp->add(new admin_setting_configcheckbox('dbsessions', get_string('dbsessions', 'admin'), get_string('configdbsessions', 'admin'), 0));
$temp->add(new admin_setting_configselect('sessiontimeout', get_string('sessiontimeout', 'admin'), get_string('configsessiontimeout', 'admin'), 7200, array(14400 => get_string('numhours', '', 4),
                                                                                                                                                      10800 => get_string('numhours', '', 3),
                                                                                                                                                      7200 => get_string('numhours', '', 2),
                                                                                                                                                      5400 => get_string('numhours', '', '1.5'),
                                                                                                                                                      3600 => get_string('numminutes', '', 60),
                                                                                                                                                      2700 => get_string('numminutes', '', 45),
                                                                                                                                                      1800 => get_string('numminutes', '', 30),
                                                                                                                                                      900 => get_string('numminutes', '', 15),
                                                                                                                                                      300 => get_string('numminutes', '', 5))));
$temp->add(new admin_setting_configtext('sessioncookie', get_string('sessioncookie', 'admin'), get_string('configsessioncookie', 'admin'), '', PARAM_ALPHANUM));
$temp->add(new admin_setting_configtext('sessioncookiepath', get_string('sessioncookiepath', 'admin'), get_string('configsessioncookiepath', 'admin'), '/', PARAM_LOCALURL));
$temp->add(new admin_setting_configtext('sessioncookiedomain', get_string('sessioncookiedomain', 'admin'), get_string('configsessioncookiedomain', 'admin'), '', PARAM_TEXT, 50));
$ADMIN->add('server', $temp, 50);



// "rss" settingpage
$temp = new admin_settingpage('rss', get_string('rss'));
$temp->add(new admin_setting_configcheckbox('enablerssfeeds', get_string('enablerssfeeds', 'admin'), get_string('configenablerssfeeds', 'admin'), 0));
$ADMIN->add('server', $temp);


// "debugging" settingpage
$temp = new admin_settingpage('debugging', get_string('debugging', 'admin'));
$temp->add(new admin_setting_special_debug());
$temp->add(new admin_setting_configcheckbox('debugdisplay', get_string('debugdisplay', 'admin'), get_string('configdebugdisplay', 'admin'), ini_get_bool('display_errors')));
$temp->add(new admin_setting_configcheckbox('xmlstrictheaders', get_string('xmlstrictheaders', 'admin'), get_string('configxmlstrictheaders', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('debugsmtp', get_string('debugsmtp', 'admin'), get_string('configdebugsmtp', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('perfdebug', get_string('perfdebug', 'admin'), get_string('configperfdebug', 'admin'), '7', '15', '7'));
$ADMIN->add('server', $temp);


// "stats" settingpage
$temp = new admin_settingpage('stats', get_string('stats'));
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
$temp->add(new admin_setting_configselect('statsmaxruntime', get_string('statsmaxruntime', 'admin'), get_string('configstatsmaxruntime3', 'admin'), 0, array(0 => get_string('untilcomplete'),
                                                                                                                                                            60*30 => '10 '.get_string('minutes'),
                                                                                                                                                            60*30 => '30 '.get_string('minutes'),
                                                                                                                                                            60*60 => '1 '.get_string('hour'),
                                                                                                                                                            60*60*2 => '2 '.get_string('hours'),
                                                                                                                                                            60*60*3 => '3 '.get_string('hours'),
                                                                                                                                                            60*60*4 => '4 '.get_string('hours'),
                                                                                                                                                            60*60*5 => '5 '.get_string('hours'),
                                                                                                                                                            60*60*6 => '6 '.get_string('hours'),
                                                                                                                                                            60*60*7 => '7 '.get_string('hours'),
                                                                                                                                                            60*60*8 => '8 '.get_string('hours') )));
$temp->add(new admin_setting_configtext('statsruntimedays', get_string('statsruntimedays', 'admin'), get_string('configstatsruntimedays', 'admin'), 31, PARAM_INT));
$temp->add(new admin_setting_configtime('statsruntimestarthour', 'statsruntimestartminute', get_string('statsruntimestart', 'admin'), get_string('configstatsruntimestart', 'admin'), array('h' => 0, 'm' => 0)));
$temp->add(new admin_setting_configtext('statsuserthreshold', get_string('statsuserthreshold', 'admin'), get_string('configstatsuserthreshold', 'admin'), 0, PARAM_INT));

$options = array(0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6);
$temp->add(new admin_setting_configselect('statscatdepth', get_string('statscatdepth', 'admin'), get_string('configstatscatdepth', 'admin'), 1, $options));
$ADMIN->add('server', $temp);


// "http" settingpage
$temp = new admin_settingpage('http', get_string('http', 'admin'));
$temp->add(new admin_setting_configtext('framename', get_string('framename', 'admin'), get_string('configframename', 'admin'), '_top', PARAM_ALPHAEXT));
$temp->add(new admin_setting_configcheckbox('slasharguments', get_string('slasharguments', 'admin'), get_string('configslasharguments', 'admin'), 1));
$temp->add(new admin_setting_heading('reverseproxy', get_string('reverseproxy', 'admin'), '', ''));
$options = array(
    0 => 'HTTP_CLIENT_IP, HTTP_X_FORWARDED_FOR, REMOTE_ADDR',
    GETREMOTEADDR_SKIP_HTTP_CLIENT_IP => 'HTTP_X_FORWARDED_FOR, REMOTE_ADDR',
    GETREMOTEADDR_SKIP_HTTP_X_FORWARDED_FOR => 'HTTP_CLIENT, REMOTE_ADDR',
    GETREMOTEADDR_SKIP_HTTP_X_FORWARDED_FOR|GETREMOTEADDR_SKIP_HTTP_CLIENT_IP => 'REMOTE_ADDR');
$temp->add(new admin_setting_configselect('getremoteaddrconf', get_string('getremoteaddrconf', 'admin'), get_string('configgetremoteaddrconf', 'admin'), 0, $options));
$temp->add(new admin_setting_heading('webproxy', get_string('webproxy', 'admin'), get_string('webproxyinfo', 'admin')));
$temp->add(new admin_setting_configtext('proxyhost', get_string('proxyhost', 'admin'), get_string('configproxyhost', 'admin'), '', PARAM_HOST));
$temp->add(new admin_setting_configtext('proxyport', get_string('proxyport', 'admin'), get_string('configproxyport', 'admin'), 0, PARAM_INT));
$options = array('HTTP'=>'HTTP');
if (defined('CURLPROXY_SOCKS5')) {
    $options['SOCKS5'] = 'SOCKS5';
}
$temp->add(new admin_setting_configselect('proxytype', get_string('proxytype', 'admin'), get_string('configproxytype','admin'), 'HTTP', $options));
$temp->add(new admin_setting_configtext('proxyuser', get_string('proxyuser', 'admin'), get_string('configproxyuser', 'admin'), ''));
$temp->add(new admin_setting_configpasswordunmask('proxypassword', get_string('proxypassword', 'admin'), get_string('configproxypassword', 'admin'), ''));
$ADMIN->add('server', $temp);

$ADMIN->add('server', new admin_externalpage('maintenancemode', get_string('sitemaintenancemode', 'admin'), "$CFG->wwwroot/$CFG->admin/maintenance.php"));


$temp = new admin_settingpage('cleanup', get_string('cleanup', 'admin'));
$temp->add(new admin_setting_configselect('longtimenosee', get_string('longtimenosee', 'admin'), get_string('configlongtimenosee', 'admin'), 120, array(0 => get_string('never'),
                                                                                                                                                        1000 => get_string('numdays', '', 1000),
                                                                                                                                                        365 => get_string('numdays', '', 365),
                                                                                                                                                        180 => get_string('numdays', '', 180),
                                                                                                                                                        150 => get_string('numdays', '', 150),
                                                                                                                                                        120 => get_string('numdays', '', 120),
                                                                                                                                                        90 => get_string('numdays', '', 90),
                                                                                                                                                        60 => get_string('numdays', '', 60),
                                                                                                                                                        30 => get_string('numdays', '', 30),
                                                                                                                                                        21 => get_string('numdays', '', 21),
                                                                                                                                                        14 => get_string('numdays', '', 14),
                                                                                                                                                        7 => get_string('numdays', '', 7) )));
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

$temp->add(new admin_setting_configselect('deleteincompleteusers', get_string('deleteincompleteusers', 'admin'), get_string('configdeleteincompleteusers', 'admin'), 0, array(0 => get_string('never'),
                                                                                                                                                                    168 => get_string('numdays', '', 7),
                                                                                                                                                                    144 => get_string('numdays', '', 6),
                                                                                                                                                                    120 => get_string('numdays', '', 5),
                                                                                                                                                                    96 => get_string('numdays', '', 4),
                                                                                                                                                                    72 => get_string('numdays', '', 3),
                                                                                                                                                                    48 => get_string('numdays', '', 2),
                                                                                                                                                                    24 => get_string('numdays', '', 1))));

$temp->add(new admin_setting_configselect('loglifetime', get_string('loglifetime', 'admin'), get_string('configloglifetime', 'admin'), 0, array(0 => get_string('neverdeletelogs'),
                                                                                                                                                1000 => get_string('numdays', '', 1000),
                                                                                                                                                365 => get_string('numdays', '', 365),
                                                                                                                                                180 => get_string('numdays', '', 180),
                                                                                                                                                150 => get_string('numdays', '', 150),
                                                                                                                                                120 => get_string('numdays', '', 120),
                                                                                                                                                90 => get_string('numdays', '', 90),
                                                                                                                                                60 => get_string('numdays', '', 60),
                                                                                                                                                35 => get_string('numdays', '', 35),
                                                                                                                                                10 => get_string('numdays', '', 10),
                                                                                                                                                5 => get_string('numdays', '', 5),
                                                                                                                                                2 => get_string('numdays', '', 2))));


$temp->add(new admin_setting_configcheckbox('disablegradehistory', get_string('disablegradehistory', 'grades'),
                                            get_string('configdisablegradehistory', 'grades'), 0, PARAM_INT));

$temp->add(new admin_setting_configselect('gradehistorylifetime', get_string('gradehistorylifetime', 'grades'),
                                          get_string('configgradehistorylifetime', 'grades'), 0, array(0 => get_string('neverdeletehistory', 'grades'),
                                                                                                   1000 => get_string('numdays', '', 1000),
                                                                                                    365 => get_string('numdays', '', 365),
                                                                                                    180 => get_string('numdays', '', 180),
                                                                                                    150 => get_string('numdays', '', 150),
                                                                                                    120 => get_string('numdays', '', 120),
                                                                                                     90 => get_string('numdays', '', 90),
                                                                                                     60 => get_string('numdays', '', 60),
                                                                                                     30 => get_string('numdays', '', 30))));

$ADMIN->add('server', $temp);



$ADMIN->add('server', new admin_externalpage('environment', get_string('environment','admin'), "$CFG->wwwroot/$CFG->admin/environment.php"));
$ADMIN->add('server', new admin_externalpage('phpinfo', get_string('phpinfo'), "$CFG->wwwroot/$CFG->admin/phpinfo.php"));


// "performance" settingpage
$temp = new admin_settingpage('performance', get_string('performance', 'admin'));

$temp->add(new admin_setting_configselect('extramemorylimit', get_string('extramemorylimit', 'admin'),
                                          get_string('configextramemorylimit', 'admin'), '128M',
                                          // if this option is set to 0, default 128M will be used
                                          array( '64M' => '64M',
                                                 '128M' => '128M',
                                                 '256M' => '256M',
                                                 '512M' => '512M',
                                                 '1024M' => '1024M'
                                             )));
$temp->add(new admin_setting_special_selectsetup('cachetype', get_string('cachetype', 'admin'),
                                          get_string('configcachetype', 'admin'), '',
                                          array( '' => get_string('none'),
                                                 'internal' => 'internal',
                                                 'memcached' => 'memcached',
                                                 'eaccelerator' => 'eaccelerator')));
// NOTE: $CFG->rcache is forced to bool in lib/setup.php
$temp->add(new admin_setting_special_selectsetup('rcache', get_string('rcache', 'admin'),
                                          get_string('configrcache', 'admin'), 0,
                                          array( '0' => get_string('no'),
                                                 '1' => get_string('yes'))));
$temp->add(new admin_setting_configtext('rcachettl', get_string('rcachettl', 'admin'),
                                        get_string('configrcachettl', 'admin'), 10));
$temp->add(new admin_setting_configtext('intcachemax', get_string('intcachemax', 'admin'),
                                        get_string('configintcachemax', 'admin'), 10));
$temp->add(new admin_setting_configtext('memcachedhosts', get_string('memcachedhosts', 'admin'),
                                        get_string('configmemcachedhosts', 'admin'), ''));
$temp->add(new admin_setting_configselect('memcachedpconn', get_string('memcachedpconn', 'admin'),
                                          get_string('configmemcachedpconn', 'admin'), 0,
                                          array( '0' => get_string('no'),
                                                 '1' => get_string('yes'))));
$ADMIN->add('server', $temp);

if ($CFG->dbfamily === 'mysql') {
    if (file_exists("$CFG->dirroot/$CFG->admin/mysql/frame.php")) {
        $ADMIN->add('server', new admin_externalpage('database', get_string('managedatabase'), "$CFG->wwwroot/$CFG->admin/mysql/frame.php"));
    }
} else if ($CFG->dbfamily === 'postgres') {
    if (file_exists("$CFG->dirroot/$CFG->admin/pgsql/frame.php")) {
        $ADMIN->add('server', new admin_externalpage('database', get_string('managedatabase'), "$CFG->wwwroot/$CFG->admin/pgsql/frame.php"));
    }
}

} // end of speedup

?>
