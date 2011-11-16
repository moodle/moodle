<?php

// This file defines settingpages and externalpages under the "appearance" category

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    $ADMIN->add('appearance', new admin_category('themes', get_string('themes')));
    // "themesettings" settingpage
    $temp = new admin_settingpage('themesettings', get_string('themesettings', 'admin'));
    $temp->add(new admin_setting_configtext('themelist', get_string('themelist', 'admin'), get_string('configthemelist','admin'), '', PARAM_NOTAGS));
    $setting = new admin_setting_configcheckbox('themedesignermode', get_string('themedesignermode', 'admin'), get_string('configthemedesignermode', 'admin'), 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    $temp->add(new admin_setting_configcheckbox('allowuserthemes', get_string('allowuserthemes', 'admin'), get_string('configallowuserthemes', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('allowcoursethemes', get_string('allowcoursethemes', 'admin'), get_string('configallowcoursethemes', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('allowcategorythemes',  get_string('allowcategorythemes', 'admin'), get_string('configallowcategorythemes', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('allowthemechangeonurl',  get_string('allowthemechangeonurl', 'admin'), get_string('configallowthemechangeonurl', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('allowuserblockhiding', get_string('allowuserblockhiding', 'admin'), get_string('configallowuserblockhiding', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('allowblockstodock', get_string('allowblockstodock', 'admin'), get_string('configallowblockstodock', 'admin'), 1));
    $temp->add(new admin_setting_configtextarea('custommenuitems', get_string('custommenuitems', 'admin'), get_string('configcustommenuitems', 'admin'), '', PARAM_TEXT, '50', '10'));
    $temp->add(new admin_setting_configcheckbox('enabledevicedetection', get_string('enabledevicedetection', 'admin'), get_string('configenabledevicedetection', 'admin'), 1));
    $temp->add(new admin_setting_devicedetectregex('devicedetectregex', get_string('devicedetectregex', 'admin'), get_string('devicedetectregex_desc', 'admin'), ''));
    $ADMIN->add('themes', $temp);
    $ADMIN->add('themes', new admin_externalpage('themeselector', get_string('themeselector','admin'), $CFG->wwwroot . '/theme/index.php'));

    // settings for each theme
    foreach (get_plugin_list('theme') as $theme => $themedir) {
        $settings_path = "$themedir/settings.php";
        if (file_exists($settings_path)) {
            $settings = new admin_settingpage('themesetting'.$theme, get_string('pluginname', 'theme_'.$theme));
            include($settings_path);
            if ($settings) {
                $ADMIN->add('themes', $settings);
            }
        }
    }


    // calendar
    $temp = new admin_settingpage('calendar', get_string('calendarsettings','admin'));
    $temp->add(new admin_setting_special_adminseesall());
    //this is hacky because we do not want to include the stuff from calendar/lib.php
    $temp->add(new admin_setting_configselect('calendar_site_timeformat', get_string('pref_timeformat', 'calendar'),
                                              get_string('explain_site_timeformat', 'calendar'), '0',
                                              array('0'        => get_string('default', 'calendar'),
                                                    '%I:%M %p' => get_string('timeformat_12', 'calendar'),
                                                    '%H:%M'    => get_string('timeformat_24', 'calendar'))));
    $temp->add(new admin_setting_configselect('calendar_startwday', get_string('configstartwday', 'admin'), get_string('helpstartofweek', 'admin'), 0,
    array(
            0 => get_string('sunday', 'calendar'),
            1 => get_string('monday', 'calendar'),
            2 => get_string('tuesday', 'calendar'),
            3 => get_string('wednesday', 'calendar'),
            4 => get_string('thursday', 'calendar'),
            5 => get_string('friday', 'calendar'),
            6 => get_string('saturday', 'calendar')
        )));
    $temp->add(new admin_setting_special_calendar_weekend());
    $options = array();
    for ($i=1; $i<=99; $i++) {
        $options[$i] = $i;
    }
    $temp->add(new admin_setting_configselect('calendar_lookahead',get_string('configlookahead','admin'),get_string('helpupcominglookahead', 'admin'),21,$options));
    $options = array();
    for ($i=1; $i<=20; $i++) {
        $options[$i] = $i;
    }
    $temp->add(new admin_setting_configselect('calendar_maxevents',get_string('configmaxevents','admin'),get_string('helpupcomingmaxevents', 'admin'),10,$options));
    $temp->add(new admin_setting_configcheckbox('enablecalendarexport', get_string('enablecalendarexport', 'admin'), get_string('configenablecalendarexport','admin'), 1));
    $temp->add(new admin_setting_configtext('calendar_exportsalt', get_string('calendarexportsalt','admin'), get_string('configcalendarexportsalt', 'admin'), random_string(60)));
    $ADMIN->add('appearance', $temp);

    // blog
    $temp = new admin_settingpage('blog', get_string('blog','blog'));
    $temp->add(new admin_setting_configcheckbox('useblogassociations', get_string('useblogassociations', 'blog'), get_string('configuseblogassociations','blog'), 1));
    $temp->add(new admin_setting_bloglevel('bloglevel', get_string('bloglevel', 'admin'), get_string('configbloglevel', 'admin'), 4, array(BLOG_GLOBAL_LEVEL => get_string('worldblogs','blog'),
                                                                                                                                           BLOG_SITE_LEVEL => get_string('siteblogs','blog'),
                                                                                                                                           BLOG_USER_LEVEL => get_string('personalblogs','blog'),
                                                                                                                                           0 => get_string('disableblogs','blog'))));
    $temp->add(new admin_setting_configcheckbox('useexternalblogs', get_string('useexternalblogs', 'blog'), get_string('configuseexternalblogs','blog'), 1));
    $temp->add(new admin_setting_configselect('externalblogcrontime', get_string('externalblogcrontime', 'blog'), get_string('configexternalblogcrontime', 'blog'), 86400,
        array(43200 => get_string('numhours', '', 12),
              86400 => get_string('numhours', '', 24),
              172800 => get_string('numdays', '', 2),
              604800 => get_string('numdays', '', 7))));
    $temp->add(new admin_setting_configtext('maxexternalblogsperuser', get_string('maxexternalblogsperuser','blog'), get_string('configmaxexternalblogsperuser', 'blog'), 1));
    $temp->add(new admin_setting_configcheckbox('blogusecomments', get_string('enablecomments', 'admin'), get_string('configenablecomments', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('blogshowcommentscount', get_string('showcommentscount', 'admin'), get_string('configshowcommentscount', 'admin'), 1));
    $ADMIN->add('appearance', $temp);

    // Navigation settings
    $temp = new admin_settingpage('navigation', get_string('navigation'));
    $choices = array(
        HOMEPAGE_SITE => get_string('site'),
        HOMEPAGE_MY => get_string('mymoodle', 'admin'),
        HOMEPAGE_USER => get_string('userpreference', 'admin')
    );
    $temp->add(new admin_setting_configselect('defaulthomepage', get_string('defaulthomepage', 'admin'), get_string('configdefaulthomepage', 'admin'), HOMEPAGE_SITE, $choices));
    $temp->add(new admin_setting_configcheckbox('navshowcategories', get_string('navshowcategories', 'admin'), get_string('confignavshowcategories', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('navshowallcourses', get_string('navshowallcourses', 'admin'), get_string('confignavshowallcourses', 'admin'), 0));
    $temp->add(new admin_setting_configtext('navcourselimit',get_string('navcourselimit','admin'),get_string('confignavcourselimit', 'admin'),20,PARAM_INT));

    $ADMIN->add('appearance', $temp);

    // "htmlsettings" settingpage
    $temp = new admin_settingpage('htmlsettings', get_string('htmlsettings', 'admin'));
    $temp->add(new admin_setting_configcheckbox('formatstringstriptags', get_string('stripalltitletags', 'admin'), get_string('configstripalltitletags', 'admin'), 1));
    $temp->add(new admin_setting_emoticons());
    $ADMIN->add('appearance', $temp);
    $ADMIN->add('appearance', new admin_externalpage('resetemoticons', get_string('emoticonsreset', 'admin'),
        new moodle_url('/admin/resetemoticons.php'), 'moodle/site:config', true));

    // "documentation" settingpage
    $temp = new admin_settingpage('documentation', get_string('moodledocs'));
    $temp->add(new admin_setting_configtext('docroot', get_string('docroot', 'admin'), get_string('configdocroot', 'admin'), 'http://docs.moodle.org', PARAM_URL));
    $temp->add(new admin_setting_configcheckbox('doctonewwindow', get_string('doctonewwindow', 'admin'), get_string('configdoctonewwindow', 'admin'), 0));
    $ADMIN->add('appearance', $temp);

    $temp = new admin_externalpage('mypage', get_string('mypage', 'admin'), $CFG->wwwroot . '/my/indexsys.php');
    $ADMIN->add('appearance', $temp);

    $temp = new admin_externalpage('profilepage', get_string('myprofile', 'admin'), $CFG->wwwroot . '/user/profilesys.php');
    $ADMIN->add('appearance', $temp);

    // coursecontact is the person responsible for course - usually manages enrolments, receives notification, etc.
    $temp = new admin_settingpage('coursecontact', get_string('coursecontact', 'admin'));
    $temp->add(new admin_setting_special_coursecontact());
    $ADMIN->add('appearance', $temp);

    $temp = new admin_settingpage('ajax', get_string('ajaxuse'));
    $temp->add(new admin_setting_configcheckbox('enableajax', get_string('enableajax', 'admin'), get_string('configenableajax', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('useexternalyui', get_string('useexternalyui', 'admin'), get_string('configuseexternalyui', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('yuicomboloading', get_string('yuicomboloading', 'admin'), get_string('configyuicomboloading', 'admin'), 1));
    $setting = new admin_setting_configcheckbox('cachejs', get_string('cachejs', 'admin'), get_string('cachejs_help', 'admin'), 1);
    $setting->set_updatedcallback('js_reset_all_caches');
    $temp->add($setting);
    $temp->add(new admin_setting_configcheckbox('enablecourseajax', get_string('enablecourseajax', 'admin'),
                                                get_string('enablecourseajax_desc', 'admin'), 1));
    $ADMIN->add('appearance', $temp);

    // link to tag management interface
    $ADMIN->add('appearance', new admin_externalpage('managetags', get_string('managetags', 'tag'), "$CFG->wwwroot/tag/manage.php"));

    $temp = new admin_settingpage('additionalhtml', get_string('additionalhtml', 'admin'));
    $temp->add(new admin_setting_heading('additionalhtml_heading', get_string('additionalhtml_heading', 'admin'), get_string('additionalhtml_desc', 'admin')));
    $temp->add(new admin_setting_configtextarea('additionalhtmlhead', get_string('additionalhtmlhead', 'admin'), get_string('additionalhtmlhead_desc', 'admin'), '', PARAM_RAW));
    $temp->add(new admin_setting_configtextarea('additionalhtmltopofbody', get_string('additionalhtmltopofbody', 'admin'), get_string('additionalhtmltopofbody_desc', 'admin'), '', PARAM_RAW));
    $temp->add(new admin_setting_configtextarea('additionalhtmlfooter', get_string('additionalhtmlfooter', 'admin'), get_string('additionalhtmlfooter_desc', 'admin'), '', PARAM_RAW));
    $ADMIN->add('appearance', $temp);

} // end of speedup

