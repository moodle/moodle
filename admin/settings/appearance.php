<?php

// This file defines settingpages and externalpages under the "appearance" category

$capabilities = array(
    'moodle/my:configsyspages',
    'moodle/tag:manage'
);

if ($hassiteconfig or has_any_capability($capabilities, $systemcontext)) { // speedup for non-admins, add all caps used on this page

    $ADMIN->add('appearance', new admin_category('themes', new lang_string('themes')));
    // "themesettings" settingpage
    $temp = new admin_settingpage('themesettings', new lang_string('themesettings', 'admin'));
    $setting = new admin_setting_configtext('themelist', new lang_string('themelist', 'admin'),
        new lang_string('configthemelist', 'admin'), '', PARAM_NOTAGS);
    $setting->set_force_ltr(true);
    $temp->add($setting);
    $setting = new admin_setting_configcheckbox('themedesignermode', new lang_string('themedesignermode', 'admin'), new lang_string('configthemedesignermode', 'admin'), 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    $temp->add(new admin_setting_configcheckbox('allowuserthemes', new lang_string('allowuserthemes', 'admin'), new lang_string('configallowuserthemes', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('allowcoursethemes', new lang_string('allowcoursethemes', 'admin'), new lang_string('configallowcoursethemes', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('allowcategorythemes',  new lang_string('allowcategorythemes', 'admin'), new lang_string('configallowcategorythemes', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('allowcohortthemes',  new lang_string('allowcohortthemes', 'admin'), new lang_string('configallowcohortthemes', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('allowthemechangeonurl',  new lang_string('allowthemechangeonurl', 'admin'), new lang_string('configallowthemechangeonurl', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('allowuserblockhiding', new lang_string('allowuserblockhiding', 'admin'), new lang_string('configallowuserblockhiding', 'admin'), 1));
    $temp->add(new admin_setting_configtextarea('custommenuitems', new lang_string('custommenuitems', 'admin'),
        new lang_string('configcustommenuitems', 'admin'), '', PARAM_RAW, '50', '10'));
    $temp->add(new admin_setting_configtextarea(
        'customusermenuitems',
        new lang_string('customusermenuitems', 'admin'),
        new lang_string('configcustomusermenuitems', 'admin'),
        'grades,grades|/grade/report/mygrades.php|t/grades
messages,message|/message/index.php|t/message
preferences,moodle|/user/preferences.php|t/preferences',
        PARAM_RAW,
        '50',
        '10'
    ));
    $temp->add(new admin_setting_configcheckbox('enabledevicedetection', new lang_string('enabledevicedetection', 'admin'), new lang_string('configenabledevicedetection', 'admin'), 1));
    $temp->add(new admin_setting_devicedetectregex('devicedetectregex', new lang_string('devicedetectregex', 'admin'), new lang_string('devicedetectregex_desc', 'admin'), ''));
    $ADMIN->add('themes', $temp);
    $ADMIN->add('themes', new admin_externalpage('themeselector', new lang_string('themeselector','admin'), $CFG->wwwroot . '/theme/index.php'));

    // settings for each theme
    foreach (core_component::get_plugin_list('theme') as $theme => $themedir) {
        $settings_path = "$themedir/settings.php";
        if (file_exists($settings_path)) {
            $settings = new admin_settingpage('themesetting'.$theme, new lang_string('pluginname', 'theme_'.$theme));
            include($settings_path);
            if ($settings) {
                $ADMIN->add('themes', $settings);
            }
        }
    }

    // Logos section.
    $temp = new admin_settingpage('logos', new lang_string('logossettings', 'admin'));

    // Logo file setting.
    $title = get_string('logo', 'admin');
    $description = get_string('logo_desc', 'admin');
    $setting = new admin_setting_configstoredfile('core_admin/logo', $title, $description, 'logo', 0,
        ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.png']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Small logo file setting.
    $title = get_string('logocompact', 'admin');
    $description = get_string('logocompact_desc', 'admin');
    $setting = new admin_setting_configstoredfile('core_admin/logocompact', $title, $description, 'logocompact', 0,
        ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.png']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $ADMIN->add('appearance', $temp);

    // Calendar settings.
    $temp = new admin_settingpage('calendar', new lang_string('calendarsettings','admin'));

    $temp->add(new admin_setting_configselect('calendartype', new lang_string('calendartype', 'admin'),
        new lang_string('calendartype_desc', 'admin'), 'gregorian', \core_calendar\type_factory::get_list_of_calendar_types()));
    $temp->add(new admin_setting_special_adminseesall());
    //this is hacky because we do not want to include the stuff from calendar/lib.php
    $temp->add(new admin_setting_configselect('calendar_site_timeformat', new lang_string('pref_timeformat', 'calendar'),
                                              new lang_string('explain_site_timeformat', 'calendar'), '0',
                                              array('0'        => new lang_string('default', 'calendar'),
                                                    '%I:%M %p' => new lang_string('timeformat_12', 'calendar'),
                                                    '%H:%M'    => new lang_string('timeformat_24', 'calendar'))));
    $temp->add(new admin_setting_configselect('calendar_startwday', new lang_string('configstartwday', 'admin'),
        new lang_string('helpstartofweek', 'admin'), get_string('firstdayofweek', 'langconfig'),
    array(
            0 => new lang_string('sunday', 'calendar'),
            1 => new lang_string('monday', 'calendar'),
            2 => new lang_string('tuesday', 'calendar'),
            3 => new lang_string('wednesday', 'calendar'),
            4 => new lang_string('thursday', 'calendar'),
            5 => new lang_string('friday', 'calendar'),
            6 => new lang_string('saturday', 'calendar')
        )));
    $temp->add(new admin_setting_special_calendar_weekend());
    $options = array(365 => new lang_string('numyear', '', 1),
            270 => new lang_string('nummonths', '', 9),
            180 => new lang_string('nummonths', '', 6),
            150 => new lang_string('nummonths', '', 5),
            120 => new lang_string('nummonths', '', 4),
            90  => new lang_string('nummonths', '', 3),
            60  => new lang_string('nummonths', '', 2),
            30  => new lang_string('nummonth', '', 1),
            21  => new lang_string('numweeks', '', 3),
            14  => new lang_string('numweeks', '', 2),
            7  => new lang_string('numweek', '', 1),
            6  => new lang_string('numdays', '', 6),
            5  => new lang_string('numdays', '', 5),
            4  => new lang_string('numdays', '', 4),
            3  => new lang_string('numdays', '', 3),
            2  => new lang_string('numdays', '', 2),
            1  => new lang_string('numday', '', 1));
    $temp->add(new admin_setting_configselect('calendar_lookahead', new lang_string('configlookahead', 'admin'), new lang_string('helpupcominglookahead', 'admin'), 21, $options));
    $options = array();
    for ($i=1; $i<=20; $i++) {
        $options[$i] = $i;
    }
    $temp->add(new admin_setting_configselect('calendar_maxevents',new lang_string('configmaxevents','admin'),new lang_string('helpupcomingmaxevents', 'admin'),10,$options));
    $temp->add(new admin_setting_configcheckbox('enablecalendarexport', new lang_string('enablecalendarexport', 'admin'), new lang_string('configenablecalendarexport','admin'), 1));

    // Calendar custom export settings.
    $days = array(365 => new lang_string('numdays', '', 365),
            180 => new lang_string('numdays', '', 180),
            150 => new lang_string('numdays', '', 150),
            120 => new lang_string('numdays', '', 120),
            90  => new lang_string('numdays', '', 90),
            60  => new lang_string('numdays', '', 60),
            30  => new lang_string('numdays', '', 30),
            5  => new lang_string('numdays', '', 5));
    $temp->add(new admin_setting_configcheckbox('calendar_customexport', new lang_string('configcalendarcustomexport', 'admin'), new lang_string('helpcalendarcustomexport','admin'), 1));
    $temp->add(new admin_setting_configselect('calendar_exportlookahead', new lang_string('configexportlookahead','admin'), new lang_string('helpexportlookahead', 'admin'), 365, $days));
    $temp->add(new admin_setting_configselect('calendar_exportlookback', new lang_string('configexportlookback','admin'), new lang_string('helpexportlookback', 'admin'), 5, $days));
    $temp->add(new admin_setting_configtext('calendar_exportsalt', new lang_string('calendarexportsalt','admin'), new lang_string('configcalendarexportsalt', 'admin'), random_string(60)));
    $temp->add(new admin_setting_configcheckbox('calendar_showicalsource', new lang_string('configshowicalsource', 'admin'), new lang_string('helpshowicalsource','admin'), 1));
    $ADMIN->add('appearance', $temp);

    // blog
    $temp = new admin_settingpage('blog', new lang_string('blog','blog'), 'moodle/site:config', empty($CFG->enableblogs));
    $temp->add(new admin_setting_configcheckbox('useblogassociations', new lang_string('useblogassociations', 'blog'), new lang_string('configuseblogassociations','blog'), 1));
    $temp->add(new admin_setting_bloglevel('bloglevel', new lang_string('bloglevel', 'admin'), new lang_string('configbloglevel', 'admin'), 4, array(BLOG_GLOBAL_LEVEL => new lang_string('worldblogs','blog'),
                                                                                                                                           BLOG_SITE_LEVEL => new lang_string('siteblogs','blog'),
                                                                                                                                           BLOG_USER_LEVEL => new lang_string('personalblogs','blog'))));
    $temp->add(new admin_setting_configcheckbox('useexternalblogs', new lang_string('useexternalblogs', 'blog'), new lang_string('configuseexternalblogs','blog'), 1));
    $temp->add(new admin_setting_configselect('externalblogcrontime', new lang_string('externalblogcrontime', 'blog'), new lang_string('configexternalblogcrontime', 'blog'), 86400,
        array(43200 => new lang_string('numhours', '', 12),
              86400 => new lang_string('numhours', '', 24),
              172800 => new lang_string('numdays', '', 2),
              604800 => new lang_string('numdays', '', 7))));
    $temp->add(new admin_setting_configtext('maxexternalblogsperuser', new lang_string('maxexternalblogsperuser','blog'), new lang_string('configmaxexternalblogsperuser', 'blog'), 1));
    $temp->add(new admin_setting_configcheckbox('blogusecomments', new lang_string('enablecomments', 'admin'), new lang_string('configenablecomments', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('blogshowcommentscount', new lang_string('showcommentscount', 'admin'), new lang_string('configshowcommentscount', 'admin'), 1));
    $ADMIN->add('appearance', $temp);

    // Navigation settings
    $temp = new admin_settingpage('navigation', new lang_string('navigation'));
    $choices = array(
        HOMEPAGE_SITE => new lang_string('site'),
        HOMEPAGE_MY => new lang_string('mymoodle', 'admin'),
        HOMEPAGE_USER => new lang_string('userpreference', 'admin')
    );
    $temp->add(new admin_setting_configselect('defaulthomepage', new lang_string('defaulthomepage', 'admin'),
            new lang_string('configdefaulthomepage', 'admin'), HOMEPAGE_MY, $choices));
    $temp->add(new admin_setting_configcheckbox('allowguestmymoodle', new lang_string('allowguestmymoodle', 'admin'), new lang_string('configallowguestmymoodle', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('navshowfullcoursenames', new lang_string('navshowfullcoursenames', 'admin'), new lang_string('navshowfullcoursenames_help', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('navshowcategories', new lang_string('navshowcategories', 'admin'), new lang_string('confignavshowcategories', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('navshowmycoursecategories', new lang_string('navshowmycoursecategories', 'admin'), new lang_string('navshowmycoursecategories_help', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('navshowallcourses', new lang_string('navshowallcourses', 'admin'), new lang_string('confignavshowallcourses', 'admin'), 0));
    $sortoptions = array(
        'sortorder' => new lang_string('sort_sortorder', 'admin'),
        'fullname' => new lang_string('sort_fullname', 'admin'),
        'shortname' => new lang_string('sort_shortname', 'admin'),
        'idnumber' => new lang_string('sort_idnumber', 'admin'),
    );
    $temp->add(new admin_setting_configselect('navsortmycoursessort', new lang_string('navsortmycoursessort', 'admin'), new lang_string('navsortmycoursessort_help', 'admin'), 'sortorder', $sortoptions));
    $temp->add(new admin_setting_configtext('navcourselimit', new lang_string('navcourselimit', 'admin'),
        new lang_string('confignavcourselimit', 'admin'), 10, PARAM_INT));
    $temp->add(new admin_setting_configcheckbox('usesitenameforsitepages', new lang_string('usesitenameforsitepages', 'admin'), new lang_string('configusesitenameforsitepages', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('linkadmincategories', new lang_string('linkadmincategories', 'admin'), new lang_string('linkadmincategories_help', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('linkcoursesections', new lang_string('linkcoursesections', 'admin'), new lang_string('linkcoursesections_help', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('navshowfrontpagemods', new lang_string('navshowfrontpagemods', 'admin'), new lang_string('navshowfrontpagemods_help', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('navadduserpostslinks', new lang_string('navadduserpostslinks', 'admin'), new lang_string('navadduserpostslinks_help', 'admin'), 1));

    $ADMIN->add('appearance', $temp);

    // "htmlsettings" settingpage
    $temp = new admin_settingpage('htmlsettings', new lang_string('htmlsettings', 'admin'));
    $temp->add(new admin_setting_configcheckbox('formatstringstriptags', new lang_string('stripalltitletags', 'admin'), new lang_string('configstripalltitletags', 'admin'), 1));
    $temp->add(new admin_setting_emoticons());
    $ADMIN->add('appearance', $temp);
    $ADMIN->add('appearance', new admin_externalpage('resetemoticons', new lang_string('emoticonsreset', 'admin'),
        new moodle_url('/admin/resetemoticons.php'), 'moodle/site:config', true));

    // "documentation" settingpage
    $temp = new admin_settingpage('documentation', new lang_string('moodledocs'));
    $temp->add(new admin_setting_configtext('docroot', new lang_string('docroot', 'admin'), new lang_string('configdocroot', 'admin'), 'https://docs.moodle.org', PARAM_URL));
    $ltemp = array('' => get_string('forceno'));
    $ltemp += get_string_manager()->get_list_of_translations(true);
    $temp->add(new admin_setting_configselect('doclang', get_string('doclang', 'admin'), get_string('configdoclang', 'admin'), '', $ltemp));
    $temp->add(new admin_setting_configcheckbox('doctonewwindow', new lang_string('doctonewwindow', 'admin'), new lang_string('configdoctonewwindow', 'admin'), 0));
    $ADMIN->add('appearance', $temp);

    $temp = new admin_externalpage('mypage', new lang_string('mypage', 'admin'), $CFG->wwwroot . '/my/indexsys.php',
            'moodle/my:configsyspages');
    $ADMIN->add('appearance', $temp);

    $temp = new admin_externalpage('profilepage', new lang_string('myprofile', 'admin'), $CFG->wwwroot . '/user/profilesys.php',
            'moodle/my:configsyspages');
    $ADMIN->add('appearance', $temp);

    // coursecontact is the person responsible for course - usually manages enrolments, receives notification, etc.
    $temp = new admin_settingpage('coursecontact', new lang_string('courses'));
    $temp->add(new admin_setting_special_coursecontact());
    $temp->add(new admin_setting_configcheckbox('coursecontactduplicates',
            new lang_string('coursecontactduplicates', 'admin'),
            new lang_string('coursecontactduplicates_desc', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('courselistshortnames',
            new lang_string('courselistshortnames', 'admin'),
            new lang_string('courselistshortnames_desc', 'admin'), 0));
    $temp->add(new admin_setting_configtext('coursesperpage', new lang_string('coursesperpage', 'admin'), new lang_string('configcoursesperpage', 'admin'), 20, PARAM_INT));
    $temp->add(new admin_setting_configtext('courseswithsummarieslimit', new lang_string('courseswithsummarieslimit', 'admin'), new lang_string('configcourseswithsummarieslimit', 'admin'), 10, PARAM_INT));
    $temp->add(new admin_setting_configtext('courseoverviewfileslimit', new lang_string('courseoverviewfileslimit'),
            new lang_string('configcourseoverviewfileslimit', 'admin'), 1, PARAM_INT));
    $temp->add(new admin_setting_configtext('courseoverviewfilesext', new lang_string('courseoverviewfilesext'),
            new lang_string('configcourseoverviewfilesext', 'admin'), '.jpg,.gif,.png'));
    $temp->add(new admin_setting_configtext('coursegraceperiodbefore', new lang_string('coursegraceperiodbefore', 'admin'),
        new lang_string('configcoursegraceperiodbefore', 'admin'), 0, PARAM_INT));
    $temp->add(new admin_setting_configtext('coursegraceperiodafter', new lang_string('coursegraceperiodafter', 'admin'),
        new lang_string('configcoursegraceperiodafter', 'admin'), 0, PARAM_INT));
    $ADMIN->add('appearance', $temp);

    $temp = new admin_settingpage('ajax', new lang_string('ajaxuse'));
    $temp->add(new admin_setting_configcheckbox('useexternalyui', new lang_string('useexternalyui', 'admin'), new lang_string('configuseexternalyui', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('yuicomboloading', new lang_string('yuicomboloading', 'admin'), new lang_string('configyuicomboloading', 'admin'), 1));
    $setting = new admin_setting_configcheckbox('cachejs', new lang_string('cachejs', 'admin'), new lang_string('cachejs_help', 'admin'), 1);
    $setting->set_updatedcallback('js_reset_all_caches');
    $temp->add($setting);
    $temp->add(new admin_setting_configcheckbox('modchooserdefault', new lang_string('modchooserdefault', 'admin'), new lang_string('configmodchooserdefault', 'admin'), 1));
    $ADMIN->add('appearance', $temp);

    // Link to tag management interface.
    $url = new moodle_url('/tag/manage.php');
    $hidden = empty($CFG->usetags);
    $page = new admin_externalpage('managetags', new lang_string('managetags', 'tag'), $url, 'moodle/tag:manage', $hidden);
    $ADMIN->add('appearance', $page);

    $temp = new admin_settingpage('additionalhtml', new lang_string('additionalhtml', 'admin'));
    $temp->add(new admin_setting_heading('additionalhtml_heading', new lang_string('additionalhtml_heading', 'admin'), new lang_string('additionalhtml_desc', 'admin')));
    $temp->add(new admin_setting_configtextarea('additionalhtmlhead', new lang_string('additionalhtmlhead', 'admin'), new lang_string('additionalhtmlhead_desc', 'admin'), '', PARAM_RAW));
    $temp->add(new admin_setting_configtextarea('additionalhtmltopofbody', new lang_string('additionalhtmltopofbody', 'admin'), new lang_string('additionalhtmltopofbody_desc', 'admin'), '', PARAM_RAW));
    $temp->add(new admin_setting_configtextarea('additionalhtmlfooter', new lang_string('additionalhtmlfooter', 'admin'), new lang_string('additionalhtmlfooter_desc', 'admin'), '', PARAM_RAW));
    $ADMIN->add('appearance', $temp);

} // end of speedup
