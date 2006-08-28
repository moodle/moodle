<?php // $Id$

// This file defines settingpages and externalpages under the "serverinterface" category

// "systempaths" settingpage
$temp = new admin_settingpage('systempaths', get_string('systempaths','admin'));
$temp->add(new admin_setting_configselect('gdversion', get_string('gdversion','admin'), get_string('configgdversion', 'admin'), check_gd_version(), array('0' => get_string('gdnot'), 
                                                                                                                                                          '1' => get_string('gd1'), 
																																	                      '2' => get_string('gd2'))));
$temp->add(new admin_setting_configtext('zip', get_string('pathtozip','admin'), get_string('configzip', 'admin'), '', PARAM_PATH));
$temp->add(new admin_setting_configtext('unzip', get_string('pathtounzip','admin'), get_string('configunzip', 'admin'), '', PARAM_PATH));
$temp->add(new admin_setting_configtext('pathtodu', get_string('pathtodu', 'admin'), get_string('configpathtodu', 'admin'), '', PARAM_PATH));
$temp->add(new admin_setting_configtext('aspellpath', get_string('aspellpath', 'admin'), get_string('edhelpaspellpath'), '', PARAM_PATH));
$ADMIN->add('serverinterface', $temp, 0);



// "email" settingpage
$temp = new admin_settingpage('mail', get_string('mail','admin'));
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
$ADMIN->add('serverinterface', $temp, 100);



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
$ADMIN->add('serverinterface', $temp, 50);



// "rss" settingpage
$temp = new admin_settingpage('rss', get_string('rss'));
$temp->add(new admin_setting_configcheckbox('enablerssfeeds', get_string('enablerssfeeds', 'admin'), get_string('configenablerssfeeds', 'admin'), 0));
$ADMIN->add('serverinterface', $temp);



// "http" settingpage
$temp = new admin_settingpage('http', get_string('http', 'admin'));
$temp->add(new admin_setting_configtext('framename', get_string('framename', 'admin'), get_string('configframename', 'admin'), '_top', PARAM_ALPHAEXT));
$temp->add(new admin_Setting_configcheckbox('slasharguments', get_string('slasharguments', 'admin'), get_string('configslasharguments', 'admin'), 1));
$temp->add(new admin_setting_configtext('proxyhost', get_string('proxyhost', 'admin'), get_string('configproxyhost', 'admin'), '', PARAM_HOST));
$temp->add(new admin_setting_configtext('proxyport', get_string('proxyport', 'admin'), get_string('configproxyport', 'admin'), '', PARAM_INT));
$ADMIN->add('serverinterface', $temp); 






?>