<?php

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    // "ip blocker" settingpage
    $temp = new admin_settingpage('ipblocker', get_string('ipblocker', 'admin'));
    $temp->add(new admin_setting_configcheckbox('allowbeforeblock', get_string('allowbeforeblock', 'admin'), get_string('allowbeforeblockdesc', 'admin'), 0));
    $temp->add(new admin_setting_configiplist('allowedip', get_string('allowediplist', 'admin'),
                                                get_string('ipblockersyntax', 'admin'), ''));
    $temp->add(new admin_setting_configiplist('blockedip', get_string('blockediplist', 'admin'),
                                                get_string('ipblockersyntax', 'admin'), ''));
    $ADMIN->add('security', $temp);

    // "sitepolicies" settingpage
    $temp = new admin_settingpage('sitepolicies', get_string('sitepolicies', 'admin'));
    $temp->add(new admin_setting_configcheckbox('protectusernames', get_string('protectusernames', 'admin'), get_string('configprotectusernames', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('forcelogin', get_string('forcelogin', 'admin'), get_string('configforcelogin', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('forceloginforprofiles', get_string('forceloginforprofiles', 'admin'), get_string('configforceloginforprofiles', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('opentogoogle', get_string('opentogoogle', 'admin'), get_string('configopentogoogle', 'admin'), 0));
    $temp->add(new admin_setting_pickroles('profileroles',
        get_string('profileroles','admin'),
        get_string('configprofileroles', 'admin'),
        array('student', 'teacher', 'editingteacher')));

    $max_upload_choices = get_max_upload_sizes();
    // maxbytes set to 0 will allow the maximum server limit for uploads
    $max_upload_choices[0] = get_string('serverlimit', 'admin');
    $temp->add(new admin_setting_configselect('maxbytes', get_string('maxbytes', 'admin'), get_string('configmaxbytes', 'admin'), 0, $max_upload_choices));
    // 100MB
    $defaultuserquota = 104857600;
    $params = new stdClass();
    $params->bytes = $defaultuserquota;
    $params->displaysize = display_size($defaultuserquota);
    $temp->add(new admin_setting_configtext('userquota', get_string('userquota', 'admin'), get_string('configuserquota', 'admin', $params), $defaultuserquota));

    $item = new admin_setting_configcheckbox('enablehtmlpurifier', get_string('enablehtmlpurifier', 'admin'), get_string('configenablehtmlpurifier', 'admin'), 1);
    $item->set_updatedcallback('reset_text_filters_cache');
    $temp->add($item);
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
                  'language', array('language' => get_string('language'),
                                              'firstname lastname' => get_string('firstname').' + '.get_string('lastname'),
                                              'lastname firstname' => get_string('lastname').' + '.get_string('firstname'),
                                              'firstname' => get_string('firstname'))));
    $temp->add(new admin_setting_configcheckbox('extendedusernamechars', get_string('extendedusernamechars', 'admin'), get_string('configextendedusernamechars', 'admin'), 0));
    $temp->add(new admin_setting_configtext('sitepolicy', get_string('sitepolicy', 'admin'), get_string('sitepolicy_help', 'admin'), '', PARAM_RAW));
    $temp->add(new admin_setting_configtext('sitepolicyguest', get_string('sitepolicyguest', 'admin'), get_string('sitepolicyguest_help', 'admin'), (isset($CFG->sitepolicy) ? $CFG->sitepolicy : ''), PARAM_RAW));
    $temp->add(new admin_setting_configcheckbox('extendedusernamechars', get_string('extendedusernamechars', 'admin'), get_string('configextendedusernamechars', 'admin'), 0));
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
    $temp->add(new admin_setting_configtext('maxconsecutiveidentchars', get_string('maxconsecutiveidentchars', 'admin'), get_string('configmaxconsecutiveidentchars', 'admin'), 0, PARAM_INT));
    $temp->add(new admin_setting_configcheckbox('groupenrolmentkeypolicy', get_string('groupenrolmentkeypolicy', 'admin'), get_string('groupenrolmentkeypolicy_desc', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('disableuserimages', get_string('disableuserimages', 'admin'), get_string('configdisableuserimages', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('emailchangeconfirmation', get_string('emailchangeconfirmation', 'admin'), get_string('configemailchangeconfirmation', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('strictformsrequired', get_string('strictformsrequired', 'admin'), get_string('configstrictformsrequired', 'admin'), 0));
    $ADMIN->add('security', $temp);




    // "httpsecurity" settingpage
    $temp = new admin_settingpage('httpsecurity', get_string('httpsecurity', 'admin'));
    $temp->add(new admin_setting_configcheckbox('loginhttps', get_string('loginhttps', 'admin'), get_string('configloginhttps', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('cookiesecure', get_string('cookiesecure', 'admin'), get_string('configcookiesecure', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('cookiehttponly', get_string('cookiehttponly', 'admin'), get_string('configcookiehttponly', 'admin'), 0));
    $temp->add(new admin_setting_configtext('excludeoldflashclients', get_string('excludeoldflashclients', 'admin'), get_string('configexcludeoldflashclients', 'admin'), '10.0.12', PARAM_TEXT));
    $ADMIN->add('security', $temp);


    // "modulesecurity" settingpage
    $temp = new admin_settingpage('modulesecurity', get_string('modulesecurity', 'admin'));
    $temp->add(new admin_setting_configselect('restrictmodulesfor', get_string('restrictmodulesfor', 'admin'), get_string('configrestrictmodulesfor', 'admin'), 'none', array('none' => get_string('nocourses'),
                                                                                                                                                                              'all' => get_string('fulllistofcourses'),
                                                                                                                                                                              'requested' => get_string('requestedcourses'))));
    $temp->add(new admin_setting_configcheckbox('restrictbydefault', get_string('restrictbydefault', 'admin'), get_string('configrestrictbydefault', 'admin'), 0));
    $temp->add(new admin_setting_configmultiselect_modules('defaultallowedmodules',
            get_string('defaultallowedmodules', 'admin'),
            get_string('configdefaultallowedmodules', 'admin')));
    $ADMIN->add('security', $temp);



    // "notifications" settingpage
    $temp = new admin_settingpage('notifications', get_string('notifications', 'admin'));
    $temp->add(new admin_setting_configselect('displayloginfailures', get_string('displayloginfailures', 'admin'), get_string('configdisplayloginfailures', 'admin'), '', array('' => get_string('nobody'),
                                                                                                                                                                                'admin' => get_string('administrators'),
                                                                                                                                                                                'teacher' => get_string('administratorsandteachers'),
                                                                                                                                                                                'everybody' => get_string('everybody'))));
    $temp->add(new admin_setting_users_with_capability('notifyloginfailures', get_string('notifyloginfailures', 'admin'), get_string('confignotifyloginfailures', 'admin'), array(), 'moodle/site:config'));
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
