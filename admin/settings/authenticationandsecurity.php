<?php // $Id$

// This file defines settingpages and externalpages under the "authenticationandsecurity" category

global $USER;

// this depends on what file is including us
if (!isset($site)) {
    $site = get_site();
}

// stuff under the "usermanagement" subcategory
$ADMIN->add('authenticationandsecurity', new admin_category('usermanagement', get_string('users')), 0);
$ADMIN->add('usermanagement', new admin_externalpage('editusers', get_string('userlist','admin'), $CFG->wwwroot . '/admin/user.php'), 1);
$ADMIN->add('usermanagement', new admin_externalpage('addnewuser', get_string('addnewuser'), $CFG->wwwroot . '/admin/user.php?newuser=true&amp;sesskey='. (isset($USER->sesskey) ? $USER->sesskey : '')), 0);
$ADMIN->add('usermanagement', new admin_externalpage('uploadusers', get_string('uploadusers'), $CFG->wwwroot . '/admin/uploaduser.php'), 2);


// stuff under the "roles" subcategory
$ADMIN->add('authenticationandsecurity', new admin_category('roles', get_string('roles')));
$ADMIN->add('roles', new admin_externalpage('manageroles', get_string('manageroles'), $CFG->wwwroot . '/admin/roles/manage.php'));
$ADMIN->add('roles', new admin_externalpage('assignsitewideroles', get_string('assignsiteroles'), $CFG->wwwroot . '/admin/roles/assign.php?contextid=' . $site->id));



// "httpsecurity" settingpage
$temp = new admin_settingpage('httpsecurity', get_string('httpsecurity', 'admin'));
$temp->add(new admin_setting_configcheckbox('loginhttps', get_string('loginhttps', 'admin'), get_string('configloginhttps', 'admin')));
$temp->add(new admin_setting_configcheckbox('secureforms', get_string('secureforms', 'admin'), get_string('configsecureforms', 'admin')));
$ADMIN->add('authenticationandsecurity', $temp);




// "modulesecurity" settingpage
$temp = new admin_settingpage('modulesecurity', get_string('modulesecurity', 'admin'));
$temp->add(new admin_setting_configselect('restrictmodulesfor', get_string('restrictmodulesfor', 'admin'), get_string('configrestrictmodulesfor', 'admin'), array('none' => 'No courses',
                                                                                                                                                                  'all' => 'All courses',
																																								  'requested' => 'Requested courses')));
$temp->add(new admin_setting_configcheckbox('restrictbydefault', get_string('restrictbydefault', 'admin'), get_string('configrestrictbydefault', 'admin')));																																								  
if (!$options = get_records("modules")) {
    $options = array();
}
$options2 = array();
foreach ($options as $option) {
    $options2[$option->id] = $option->name;
}
$temp->add(new admin_setting_configmultiselect('defaultallowedmodules', get_string('defaultallowedmodules', 'admin'), get_string('configdefaultallowedmodules', 'admin'), $options2));
$ADMIN->add('authenticationandsecurity', $temp);



// "notifications" settingpage
$temp = new admin_settingpage('notifications', get_string('notifications', 'admin'));
$temp->add(new admin_setting_configselect('displayloginfailures', get_string('displayloginfailures', 'admin'), get_string('configdisplayloginfailures', 'admin'), array('' => get_string('nobody'),
                                                                                                                                                                        'admin' => get_string('administrators'),
																																										'teacher' => get_string('administratorsandteachers'),
																																										'everybody' => get_string('everybody'))));
$temp->add(new admin_setting_configselect('notifyloginfailures', get_string('notifyloginfailures', 'admin'), get_string('confignotifyloginfailures', 'admin'), array('' => get_string('nobody'),
                                                                                                                                                                     'mainadmin' => get_string('administrator'),
																																									 'alladmins' => get_string('administratorsall'))));
$options = array();
for ($i = 1; $i <= 100; $i++) {
    $options[$i] = $i;
}
$temp->add(new admin_setting_configselect('notifyloginthreshold', get_string('notifyloginthreshold', 'admin'), get_string('confignotifyloginthreshold', 'admin'), $options));
$ADMIN->add('authenticationandsecurity', $temp);



// "sitepolicies" settingpage
$temp = new admin_settingpage('sitepolicies', get_string('sitepolicies', 'admin'));
$temp->add(new admin_setting_configselect('showsiteparticipantslist', get_string('showsiteparticipantslist', 'admin'), get_string('configshowsiteparticipantslist', 'admin'), 	array(0 => get_string('siteteachers'), 1 => get_string('allteachers'), 2 => get_string('studentsandteachers'))));
$temp->add(new admin_setting_configcheckbox('forcelogin', get_string('forcelogin', 'admin'), get_string('configforcelogin', 'admin')));
$temp->add(new admin_setting_configcheckbox('forceloginforprofiles', get_string('forceloginforprofiles', 'admin'), get_string('configforceloginforprofiles', 'admin')));
$temp->add(new admin_setting_configcheckbox('opentogoogle', get_string('opentogoogle', 'admin'), get_string('configopentogoogle', 'admin')));
$temp->add(new admin_setting_configtext('maxbytes', get_string('maxbytes', 'admin'), get_string('configmaxbytes', 'admin'), PARAM_INT));
$temp->add(new admin_setting_configcheckbox('messaging', get_string('messaging', 'admin'), get_string('configmessaging','admin')));
$temp->add(new admin_setting_configcheckbox('allowobjectembed', get_string('allowobjectembed', 'admin'), get_string('configallowobjectembed', 'admin')));
$temp->add(new admin_setting_configcheckbox('enabletrusttext', get_string('enabletrusttext', 'admin'), get_string('configenabletrusttext', 'admin')));
$temp->add(new admin_setting_configselect('maxeditingtime', get_string('maxeditingtime','admin'), get_string('configmaxeditingtime','admin'), array(60 => get_string('numminutes', '', 1),
                                                                                                                                                    300 => get_string('numminutes', '', 5),
																																					900 => get_string('numminutes', '', 15),
																																					1800 => get_string('numminutes', '', 30),
																																					2700 => get_string('numminutes', '', 45),
																																					3600 => get_string('numminutes', '', 60))));
$ADMIN->add('authenticationandsecurity', $temp);



// "userpolicies" settingpage
$temp = new admin_settingpage('userpolicies', get_string('userpolicies', 'admin'));
$temp->add(new admin_setting_configcheckbox('autologinguests', get_string('autologinguests', 'admin'), get_string('configautologinguests', 'admin')));
$temp->add(new admin_setting_configmultiselect('hiddenuserfields', get_string('hiddenuserfields', 'admin'), get_string('confighiddenuserfields', 'admin'), array('none' => get_string('none'),
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
$temp->add(new admin_setting_configcheckbox('teacherassignteachers', get_string('teacherassignteachers', 'admin'), get_string('configteacherassignteachers', 'admin')));
$temp->add(new admin_setting_configcheckbox('allowunenroll', get_string('allowunenroll', 'admin'), get_string('configallowunenroll', 'admin')));
$temp->add(new admin_setting_configcheckbox('allusersaresitestudents', get_string('allusersaresitestudents', 'admin'), get_string('configallusersaresitestudents','admin')));
$temp->add(new admin_setting_special_adminseesall());
$ADMIN->add('authenticationandsecurity', $temp);




// "antivirus" settingpage
$temp = new admin_settingpage('antivirus', get_string('antivirus', 'admin'));
$temp->add(new admin_setting_configcheckbox('runclamavonupload', get_string('runclamavonupload', 'admin'), get_string('configrunclamavonupload', 'admin')));
$temp->add(new admin_setting_configtext('pathtoclam', get_string('pathtoclam', 'admin'), get_string('configpathtoclam', 'admin'), PARAM_PATH));
$temp->add(new admin_setting_configtext('quarantinedir', get_string('quarantinedir', 'admin'), get_string('configquarantinedir', 'admin'), PARAM_PATH));
$temp->add(new admin_setting_configselect('clamfailureonupload', get_string('clamfailureonupload', 'admin'), get_string('configclamfailureonupload', 'admin'), array('donothing' => get_string('configclamdonothing', 'admin'),
                                                                                                                                                                   'actlikevirus' => get_string('configclamactlikevirus', 'admin'))));
$ADMIN->add('authenticationandsecurity', $temp);


$ADMIN->add('authenticationandsecurity', new admin_externalpage('userauthentication', get_string('authentication','admin'), $CFG->wwwroot . '/admin/auth.php'));

?>
