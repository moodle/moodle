<?php // $Id$

// This file defines settingpages and externalpages under the "server" category

// "systempaths" settingpage
$temp = new admin_settingpage('systempaths', get_string('systempaths','admin'));
$temp->add(new admin_setting_configselect('gdversion', get_string('gdversion','admin'), get_string('configgdversion', 'admin'), check_gd_version(), array('0' => get_string('gdnot'),
                                                                                                                                                          '1' => get_string('gd1'),
                                                                                                                                                          '2' => get_string('gd2'))));
$temp->add(new admin_setting_configtext('zip', get_string('pathtozip','admin'), get_string('configzip', 'admin'), '', PARAM_RAW)); // TODO: add path validation
$temp->add(new admin_setting_configtext('unzip', get_string('pathtounzip','admin'), get_string('configunzip', 'admin'), '', PARAM_RAW)); // TODO: add path validation
$temp->add(new admin_setting_configtext('pathtodu', get_string('pathtodu', 'admin'), get_string('configpathtodu', 'admin'), '', PARAM_RAW)); // TODO: add path validation
$temp->add(new admin_setting_configtext('aspellpath', get_string('aspellpath', 'admin'), get_string('edhelpaspellpath'), '', PARAM_RAW)); // TODO: add path validation
$ADMIN->add('server', $temp, 0);



// "email" settingpage
$temp = new admin_settingpage('mail', get_string('mail','admin'));
$temp->add(new admin_setting_configtext('smtphosts', get_string('smtphosts', 'admin'), get_string('configsmtphosts', 'admin'), '', PARAM_HOST));
$temp->add(new admin_setting_configtext('smtpuser', get_string('smtpuser', 'admin'), get_string('configsmtpuser', 'admin'), '', PARAM_NOTAGS));
$temp->add(new admin_setting_configpasswordunmask('smtppass', get_string('smtppass', 'admin'), get_string('configsmtpuser', 'admin'), '', PARAM_RAW));
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
$charsets = get_list_of_charsets();
unset($charsets['UTF-8']); // not needed here
$options = array();
$options['0'] = get_string('none');
$options = array_merge($options, $charsets);
$temp->add(new admin_setting_configselect('sitemailcharset', get_string('sitemailcharset', 'admin'), get_string('configsitemailcharset','admin'), '', $options));
$temp->add(new admin_setting_configcheckbox('allowusermailcharset', get_string('allowusermailcharset', 'admin'), get_string('configallowusermailcharset', 'admin'), 0));
$temp->add(new admin_setting_configtext('supportname', get_string('supportname', 'admin'), get_string('configsupportname', 'admin'), '', PARAM_NOTAGS));
$temp->add(new admin_setting_configtext('supportemail', get_string('supportemail', 'admin'), get_string('configsupportemail', 'admin'), '', PARAM_NOTAGS));
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
$ADMIN->add('server', $temp, 50);



// "rss" settingpage
$temp = new admin_settingpage('rss', get_string('rss'));
$temp->add(new admin_setting_configcheckbox('enablerssfeeds', get_string('enablerssfeeds', 'admin'), get_string('configenablerssfeeds', 'admin'), 0));
$ADMIN->add('server', $temp);


// "debugging" settingpage
$temp = new admin_settingpage('debugging', get_string('debugging', 'admin'));
$temp->add(new admin_setting_special_debug());
$temp->add(new admin_setting_special_debugdisplay());
$temp->add(new admin_setting_configcheckbox('debugsmtp', get_string('debugsmtp', 'admin'), get_string('configdebugsmtp', 'admin'), 0));
$temp->add(new admin_setting_special_perfdebug());
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
$ADMIN->add('server', $temp);


// "http" settingpage
$temp = new admin_settingpage('http', get_string('http', 'admin'));
$temp->add(new admin_setting_configtext('framename', get_string('framename', 'admin'), get_string('configframename', 'admin'), '_top', PARAM_ALPHAEXT));
$temp->add(new admin_Setting_configcheckbox('slasharguments', get_string('slasharguments', 'admin'), get_string('configslasharguments', 'admin'), 1));
$temp->add(new admin_setting_configtext('proxyhost', get_string('proxyhost', 'admin'), get_string('configproxyhost', 'admin'), '', PARAM_HOST));
$temp->add(new admin_setting_configtext('proxyport', get_string('proxyport', 'admin'), get_string('configproxyport', 'admin'), 0, PARAM_INT));
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

$temp->add(new admin_setting_configselect('loglifetime', get_string('loglifetime', 'admin'), get_string('configloglifetime', 'admin'), 0, array(0 => get_string('neverdeletelogs'),
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
$temp->add(new admin_setting_configselect('cachetype', get_string('cachetype', 'admin'), 
                                          get_string('configcachetype', 'admin'), false, 
                                          array( '' => 'none', 
                                                 'internal' => 'internal', 
                                                 'memcached' => 'memcached', 
                                                 'eaccelerator' => 'eaccelerator')));
// NOTE: $CFG->rcache is forced to bool in lib/setup.php
$temp->add(new admin_setting_configselect('rcache', get_string('rcache', 'admin'),
                                          get_string('configrcache', 'admin'), false, 
                                          array( '0' => 'false', 
                                                 '1' => 'true')));
$temp->add(new admin_setting_configtext('rcachettl', get_string('rcachettl', 'admin'),
                                        get_string('configrcachettl', 'admin'), 10));
$temp->add(new admin_setting_configtext('intcachemax', get_string('intcachemax', 'admin'),
                                        get_string('configintcachemax', 'admin'), 10));
$temp->add(new admin_setting_configtext('memcachedhosts', get_string('memcachedhosts', 'admin'),
                                        get_string('configmemcachedhosts', 'admin'), ''));
$temp->add(new admin_setting_configselect('memcachedpconn', get_string('memcachedpconn', 'admin'),
                                          get_string('configmemcachedpconn', 'admin'), false, 
                                          array( '0' => 'false', 
                                                 '1' => 'true')));
$ADMIN->add('server', $temp);

if (file_exists("$CFG->dirroot/$CFG->admin/mysql/frame.php")) {
    $ADMIN->add('server', new admin_externalpage('database', get_string('managedatabase'), "$CFG->wwwroot/$CFG->admin/mysql/frame.php"));
}

?>
