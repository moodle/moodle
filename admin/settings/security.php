<?php // $Id$

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page


    // "sitepolicies" settingpage
    $temp = new admin_settingpage('sitepolicies', get_string('sitepolicies', 'admin'));
    $temp->add(new admin_setting_configcheckbox('protectusernames', get_string('protectusernames', 'admin'), get_string('configprotectusernames', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('forcelogin', get_string('forcelogin', 'admin'), get_string('configforcelogin', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('forceloginforprofiles', get_string('forceloginforprofiles', 'admin'), get_string('configforceloginforprofiles', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('opentogoogle', get_string('opentogoogle', 'admin'), get_string('configopentogoogle', 'admin'), 0));

    $max_upload_choices = get_max_upload_sizes();
    // maxbytes set to 0 will allow the maxium server lmit for uploads
    $max_upload_choices[0] = get_string('serverlimit', 'admin');
    $temp->add(new admin_setting_configselect('maxbytes', get_string('maxbytes', 'admin'), get_string('configmaxbytes', 'admin'), 0, $max_upload_choices));

    $temp->add(new admin_setting_configcheckbox('messaging', get_string('messaging', 'admin'), get_string('configmessaging','admin'), 1));
    $temp->add(new admin_setting_configcheckbox('allowobjectembed', get_string('allowobjectembed', 'admin'), get_string('configallowobjectembed', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('enabletrusttext', get_string('enabletrusttext', 'admin'), get_string('configenabletrusttext', 'admin'), 0));
    $temp->add(new admin_setting_configselect('maxeditingtime', get_string('maxeditingtime','admin'), get_string('configmaxeditingtime','admin'), 1800,
                 array(60 => get_string('numminutes', '', 1),
                       300 => get_string('numminutes', '', 5),
                       900 => get_string('numminutes', '', 15),
                       1800 => get_string('numminutes', '', 30),
                       2700 => get_string('numminutes', '', 45),
                       3600 => get_string('numminutes', '', 60))));
    $temp->add(new admin_setting_configselect('fullnamedisplay', get_string('fullnamedisplay', 'admin'), get_string('configfullnamedisplay', 'admin'),
                  'firstname lastname', array('language' => get_string('language'),
                                              'firstname lastname' => get_string('firstname').' + '.get_string('lastname'),
                                              'lastname firstname' => get_string('lastname').' + '.get_string('firstname'),
                                              'firstname' => get_string('firstname'))));
    $temp->add(new admin_setting_configcheckbox('extendedusernamechars', get_string('extendedusernamechars', 'admin'), get_string('configextendedusernamechars', 'admin'), 0));
    $temp->add(new admin_setting_configtext('sitepolicy', get_string('sitepolicy', 'admin'), get_string('configsitepolicy', 'admin'), '', PARAM_RAW));
    $temp->add(new admin_setting_configselect('bloglevel', get_string('bloglevel', 'admin'), get_string('configbloglevel', 'admin'), 4, array(5 => get_string('worldblogs','blog'),
                                                                                                                                              4 => get_string('siteblogs','blog'),
                                                                                                                                              3 => get_string('courseblogs','blog'),
                                                                                                                                              2 => get_string('groupblogs','blog'),
                                                                                                                                              1 => get_string('personalblogs','blog'),
                                                                                                                                              0 => get_string('disableblogs','blog'))));
    $temp->add(new admin_setting_configcheckbox('usetags', get_string('usetags','admin'),get_string('configusetags', 'admin'),'1'));
    $temp->add(new admin_setting_configcheckbox('keeptagnamecase', get_string('keeptagnamecase','admin'),get_string('configkeeptagnamecase', 'admin'),'1'));

    $temp->add(new admin_setting_configcheckbox('profilesforenrolledusersonly', get_string('profilesforenrolledusersonly','admin'),get_string('configprofilesforenrolledusersonly', 'admin'),'1'));

    $temp->add(new admin_setting_configcheckbox('cronclionly', get_string('cronclionly', 'admin'), get_string('configcronclionly', 'admin'), 0));
    $temp->add(new admin_setting_configpasswordunmask('cronremotepassword', get_string('cronremotepassword', 'admin'), get_string('configcronremotepassword', 'admin'), ''));

    $temp->add(new admin_setting_configcheckbox('passwordpolicy', get_string('passwordpolicy', 'admin'), get_string('configpasswordpolicy', 'admin'), 1));
    $temp->add(new admin_setting_configtext('minpasswordlength', get_string('minpasswordlength', 'admin'), get_string('configminpasswordlength', 'admin'), 8, PARAM_INT));
    $temp->add(new admin_setting_configtext('minpassworddigits', get_string('minpassworddigits', 'admin'), get_string('configminpassworddigits', 'admin'), 1, PARAM_INT));
    $temp->add(new admin_setting_configtext('minpasswordlower', get_string('minpasswordlower', 'admin'), get_string('configminpasswordlower', 'admin'), 1, PARAM_INT));
    $temp->add(new admin_setting_configtext('minpasswordupper', get_string('minpasswordupper', 'admin'), get_string('configminpasswordupper', 'admin'), 1, PARAM_INT));
    $temp->add(new admin_setting_configtext('minpasswordnonalphanum', get_string('minpasswordnonalphanum', 'admin'), get_string('configminpasswordnonalphanum', 'admin'), 1, PARAM_INT));
    $temp->add(new admin_setting_configcheckbox('disableuserimages', get_string('disableuserimages', 'admin'), get_string('configdisableuserimages', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('emailchangeconfirmation', get_string('emailchangeconfirmation', 'admin'), get_string('configemailchangeconfirmation', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('enablenotes', get_string('enablenotes', 'notes'), get_string('configenablenotes', 'notes'), 1));
    $ADMIN->add('security', $temp);




    // "httpsecurity" settingpage
    $temp = new admin_settingpage('httpsecurity', get_string('httpsecurity', 'admin'));
    $temp->add(new admin_setting_configcheckbox('loginhttps', get_string('loginhttps', 'admin'), get_string('configloginhttps', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('cookiesecure', get_string('cookiesecure', 'admin'), get_string('configcookiesecure', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('cookiehttponly', get_string('cookiehttponly', 'admin'), get_string('configcookiehttponly', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('regenloginsession', get_string('regenloginsession', 'admin'), get_string('configregenloginsession', 'admin'), 0));
    $temp->add(new admin_setting_configtext('excludeoldflashclients', get_string('excludeoldflashclients', 'admin'), get_string('configexcludeoldflashclients', 'admin'), '10.0.12', PARAM_TEXT));
    $ADMIN->add('security', $temp);


    // "modulesecurity" settingpage
    $temp = new admin_settingpage('modulesecurity', get_string('modulesecurity', 'admin'));
    $temp->add(new admin_setting_configselect('restrictmodulesfor', get_string('restrictmodulesfor', 'admin'), get_string('configrestrictmodulesfor', 'admin'), 'none', array('none' => 'No courses',
                                                                                                                                                                              'all' => 'All courses',
                                                                                                                                                                              'requested' => 'Requested courses')));
    $temp->add(new admin_setting_configcheckbox('restrictbydefault', get_string('restrictbydefault', 'admin'), get_string('configrestrictbydefault', 'admin'), 0));
    if (!$options = get_records("modules")) {
        $options = array();
    }
    $options2 = array();
    foreach ($options as $option) {
        $options2[$option->id] = $option->name;
    }
    $temp->add(new admin_setting_configmultiselect('defaultallowedmodules', get_string('defaultallowedmodules', 'admin'), get_string('configdefaultallowedmodules', 'admin'), array(), $options2));
    $ADMIN->add('security', $temp);



    // "notifications" settingpage
    $temp = new admin_settingpage('notifications', get_string('notifications', 'admin'));
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
    $ADMIN->add('security', $temp);






    // "antivirus" settingpage
    $temp = new admin_settingpage('antivirus', get_string('antivirus', 'admin'));
    $temp->add(new admin_setting_configcheckbox('runclamonupload', get_string('runclamavonupload', 'admin'), get_string('configrunclamavonupload', 'admin'), 0));
    $temp->add(new admin_setting_configexecutable('pathtoclam', get_string('pathtoclam', 'admin'), get_string('configpathtoclam', 'admin'), ''));
    $temp->add(new admin_setting_configdirectory('quarantinedir', get_string('quarantinedir', 'admin'), get_string('configquarantinedir', 'admin'), ''));
    $temp->add(new admin_setting_configselect('clamfailureonupload', get_string('clamfailureonupload', 'admin'), get_string('configclamfailureonupload', 'admin'), 'donothing', array('donothing' => get_string('configclamdonothing', 'admin'),
                                                                                                                                                                                      'actlikevirus' => get_string('configclamactlikevirus', 'admin'))));
    $ADMIN->add('security', $temp);

} // end of speedup

?>
