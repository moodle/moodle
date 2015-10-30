<?php

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    // "ip blocker" settingpage
    $temp = new admin_settingpage('ipblocker', new lang_string('ipblocker', 'admin'));
    $temp->add(new admin_setting_configcheckbox('allowbeforeblock', new lang_string('allowbeforeblock', 'admin'), new lang_string('allowbeforeblockdesc', 'admin'), 0));
    $temp->add(new admin_setting_configiplist('allowedip', new lang_string('allowediplist', 'admin'),
                                                new lang_string('ipblockersyntax', 'admin'), ''));
    $temp->add(new admin_setting_configiplist('blockedip', new lang_string('blockediplist', 'admin'),
                                                new lang_string('ipblockersyntax', 'admin'), ''));
    $ADMIN->add('security', $temp);

    // "sitepolicies" settingpage
    $temp = new admin_settingpage('sitepolicies', new lang_string('sitepolicies', 'admin'));
    $temp->add(new admin_setting_configcheckbox('protectusernames', new lang_string('protectusernames', 'admin'), new lang_string('configprotectusernames', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('forcelogin', new lang_string('forcelogin', 'admin'), new lang_string('configforcelogin', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('forceloginforprofiles', new lang_string('forceloginforprofiles', 'admin'), new lang_string('configforceloginforprofiles', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('forceloginforprofileimage', new lang_string('forceloginforprofileimage', 'admin'), new lang_string('forceloginforprofileimage_help', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('opentogoogle', new lang_string('opentogoogle', 'admin'), new lang_string('configopentogoogle', 'admin'), 0));
    $temp->add(new admin_setting_pickroles('profileroles',
        new lang_string('profileroles','admin'),
        new lang_string('configprofileroles', 'admin'),
        array('student', 'teacher', 'editingteacher')));

    $maxbytes = 0;
    if (!empty($CFG->maxbytes)) {
        $maxbytes = $CFG->maxbytes;
    }
    $max_upload_choices = get_max_upload_sizes(0, 0, 0, $maxbytes);
    // maxbytes set to 0 will allow the maximum server limit for uploads
    $temp->add(new admin_setting_configselect('maxbytes', new lang_string('maxbytes', 'admin'), new lang_string('configmaxbytes', 'admin'), 0, $max_upload_choices));
    // 100MB
    $defaultuserquota = 104857600;
    $params = new stdClass();
    $params->bytes = $defaultuserquota;
    $params->displaysize = display_size($defaultuserquota);
    $temp->add(new admin_setting_configtext('userquota', new lang_string('userquota', 'admin'), new lang_string('configuserquota', 'admin', $params), $defaultuserquota));

    $temp->add(new admin_setting_configcheckbox('allowobjectembed', new lang_string('allowobjectembed', 'admin'), new lang_string('configallowobjectembed', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('enabletrusttext', new lang_string('enabletrusttext', 'admin'), new lang_string('configenabletrusttext', 'admin'), 0));
    $temp->add(new admin_setting_configselect('maxeditingtime', new lang_string('maxeditingtime','admin'), new lang_string('configmaxeditingtime','admin'), 1800,
                 array(60 => new lang_string('numminutes', '', 1),
                       300 => new lang_string('numminutes', '', 5),
                       900 => new lang_string('numminutes', '', 15),
                       1800 => new lang_string('numminutes', '', 30),
                       2700 => new lang_string('numminutes', '', 45),
                       3600 => new lang_string('numminutes', '', 60))));

    $temp->add(new admin_setting_configcheckbox('extendedusernamechars', new lang_string('extendedusernamechars', 'admin'), new lang_string('configextendedusernamechars', 'admin'), 0));
    $temp->add(new admin_setting_configtext('sitepolicy', new lang_string('sitepolicy', 'admin'), new lang_string('sitepolicy_help', 'admin'), '', PARAM_RAW));
    $temp->add(new admin_setting_configtext('sitepolicyguest', new lang_string('sitepolicyguest', 'admin'), new lang_string('sitepolicyguest_help', 'admin'), (isset($CFG->sitepolicy) ? $CFG->sitepolicy : ''), PARAM_RAW));
    $temp->add(new admin_setting_configcheckbox('extendedusernamechars', new lang_string('extendedusernamechars', 'admin'), new lang_string('configextendedusernamechars', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('keeptagnamecase', new lang_string('keeptagnamecase','admin'),new lang_string('configkeeptagnamecase', 'admin'),'1'));

    $temp->add(new admin_setting_configcheckbox('profilesforenrolledusersonly', new lang_string('profilesforenrolledusersonly','admin'),new lang_string('configprofilesforenrolledusersonly', 'admin'),'1'));

    $temp->add(new admin_setting_configcheckbox('cronclionly', new lang_string('cronclionly', 'admin'), new lang_string('configcronclionly', 'admin'), 0));
    $temp->add(new admin_setting_configpasswordunmask('cronremotepassword', new lang_string('cronremotepassword', 'admin'), new lang_string('configcronremotepassword', 'admin'), ''));

    $options = array(0=>get_string('no'), 3=>3, 5=>5, 7=>7, 10=>10, 20=>20, 30=>30, 50=>50, 100=>100);
    $temp->add(new admin_setting_configselect('lockoutthreshold', new lang_string('lockoutthreshold', 'admin'), new lang_string('lockoutthreshold_desc', 'admin'), 0, $options));
    $temp->add(new admin_setting_configduration('lockoutwindow', new lang_string('lockoutwindow', 'admin'), new lang_string('lockoutwindow_desc', 'admin'), 60*30));
    $temp->add(new admin_setting_configduration('lockoutduration', new lang_string('lockoutduration', 'admin'), new lang_string('lockoutduration_desc', 'admin'), 60*30));

    $temp->add(new admin_setting_configcheckbox('passwordpolicy', new lang_string('passwordpolicy', 'admin'), new lang_string('configpasswordpolicy', 'admin'), 1));
    $temp->add(new admin_setting_configtext('minpasswordlength', new lang_string('minpasswordlength', 'admin'), new lang_string('configminpasswordlength', 'admin'), 8, PARAM_INT));
    $temp->add(new admin_setting_configtext('minpassworddigits', new lang_string('minpassworddigits', 'admin'), new lang_string('configminpassworddigits', 'admin'), 1, PARAM_INT));
    $temp->add(new admin_setting_configtext('minpasswordlower', new lang_string('minpasswordlower', 'admin'), new lang_string('configminpasswordlower', 'admin'), 1, PARAM_INT));
    $temp->add(new admin_setting_configtext('minpasswordupper', new lang_string('minpasswordupper', 'admin'), new lang_string('configminpasswordupper', 'admin'), 1, PARAM_INT));
    $temp->add(new admin_setting_configtext('minpasswordnonalphanum', new lang_string('minpasswordnonalphanum', 'admin'), new lang_string('configminpasswordnonalphanum', 'admin'), 1, PARAM_INT));
    $temp->add(new admin_setting_configtext('maxconsecutiveidentchars', new lang_string('maxconsecutiveidentchars', 'admin'), new lang_string('configmaxconsecutiveidentchars', 'admin'), 0, PARAM_INT));
    $pwresetoptions = array(
        300 => new lang_string('numminutes', '', 5),
        900 => new lang_string('numminutes', '', 15),
        1800 => new lang_string('numminutes', '', 30),
        2700 => new lang_string('numminutes', '', 45),
        3600 => new lang_string('numminutes', '', 60),
        7200 => new lang_string('numminutes', '', 120),
        14400 => new lang_string('numminutes', '', 240)
    );
    $adminsetting = new admin_setting_configselect(
            'pwresettime',
            new lang_string('passwordresettime','admin'),
            new lang_string('configpasswordresettime','admin'),
            1800,
            $pwresetoptions);
    $temp->add($adminsetting);
    $temp->add(new admin_setting_configcheckbox('groupenrolmentkeypolicy', new lang_string('groupenrolmentkeypolicy', 'admin'), new lang_string('groupenrolmentkeypolicy_desc', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('disableuserimages', new lang_string('disableuserimages', 'admin'), new lang_string('configdisableuserimages', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('emailchangeconfirmation', new lang_string('emailchangeconfirmation', 'admin'), new lang_string('configemailchangeconfirmation', 'admin'), 1));
    $temp->add(new admin_setting_configselect('rememberusername', new lang_string('rememberusername','admin'), new lang_string('rememberusername_desc','admin'), 2, array(1=>new lang_string('yes'), 0=>new lang_string('no'), 2=>new lang_string('optional'))));
    $temp->add(new admin_setting_configcheckbox('strictformsrequired', new lang_string('strictformsrequired', 'admin'), new lang_string('configstrictformsrequired', 'admin'), 0));
    $ADMIN->add('security', $temp);




    // "httpsecurity" settingpage
    $temp = new admin_settingpage('httpsecurity', new lang_string('httpsecurity', 'admin'));
    $temp->add(new admin_setting_configcheckbox('loginhttps', new lang_string('loginhttps', 'admin'), new lang_string('configloginhttps', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('cookiesecure', new lang_string('cookiesecure', 'admin'), new lang_string('configcookiesecure', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('cookiehttponly', new lang_string('cookiehttponly', 'admin'), new lang_string('configcookiehttponly', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('allowframembedding', new lang_string('allowframembedding', 'admin'), new lang_string('allowframembedding_help', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('loginpasswordautocomplete', new lang_string('loginpasswordautocomplete', 'admin'), new lang_string('loginpasswordautocomplete_help', 'admin'), 0));
    $ADMIN->add('security', $temp);


    // "notifications" settingpage
    $temp = new admin_settingpage('notifications', new lang_string('notifications', 'admin'));
    $temp->add(new admin_setting_configcheckbox('displayloginfailures', new lang_string('displayloginfailures', 'admin'),
            new lang_string('configdisplayloginfailures', 'admin'), 0));
    $temp->add(new admin_setting_users_with_capability('notifyloginfailures', new lang_string('notifyloginfailures', 'admin'), new lang_string('confignotifyloginfailures', 'admin'), array(), 'moodle/site:config'));
    $options = array();
    for ($i = 1; $i <= 100; $i++) {
        $options[$i] = $i;
    }
    $temp->add(new admin_setting_configselect('notifyloginthreshold', new lang_string('notifyloginthreshold', 'admin'), new lang_string('confignotifyloginthreshold', 'admin'), '10', $options));
    $ADMIN->add('security', $temp);






    // "antivirus" settingpage
    $temp = new admin_settingpage('antivirus', new lang_string('antivirus', 'admin'));
    $temp->add(new admin_setting_configcheckbox('runclamonupload', new lang_string('runclamavonupload', 'admin'), new lang_string('configrunclamavonupload', 'admin'), 0));
    $temp->add(new admin_setting_configexecutable('pathtoclam', new lang_string('pathtoclam', 'admin'), new lang_string('configpathtoclam', 'admin'), ''));
    $temp->add(new admin_setting_configdirectory('quarantinedir', new lang_string('quarantinedir', 'admin'), new lang_string('configquarantinedir', 'admin'), ''));
    $temp->add(new admin_setting_configselect('clamfailureonupload', new lang_string('clamfailureonupload', 'admin'), new lang_string('configclamfailureonupload', 'admin'), 'donothing', array('donothing' => new lang_string('configclamdonothing', 'admin'),
                                                                                                                                                                                      'actlikevirus' => new lang_string('configclamactlikevirus', 'admin'))));
    $ADMIN->add('security', $temp);

} // end of speedup
