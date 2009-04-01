<?php // $Id$

// This file defines settingpages and externalpages under the "appearance" category

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    $ADMIN->add('appearance', new admin_category('themes', get_string('themes')));
    // "themesettings" settingpage
    $temp = new admin_settingpage('themesettings', get_string('themesettings', 'admin'));
    $temp->add(new admin_setting_configtext('themelist', get_string('themelist', 'admin'), get_string('configthemelist','admin'), '', PARAM_NOTAGS));
    $temp->add(new admin_setting_configcheckbox('allowuserthemes', get_string('allowuserthemes', 'admin'), get_string('configallowuserthemes', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('allowcoursethemes', get_string('allowcoursethemes', 'admin'), get_string('configallowcoursethemes', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('allowcategorythemes',  get_string('allowcategorythemes', 'admin'), get_string('configallowcategorythemes', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('allowuserblockhiding', get_string('allowuserblockhiding', 'admin'), get_string('configallowuserblockhiding', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('showblocksonmodpages', get_string('showblocksonmodpages', 'admin'), get_string('configshowblocksonmodpages', 'admin'), 0));
    $temp->add(new admin_setting_configselect('hideactivitytypenavlink', get_string('hideactivitytypenavlink', 'admin'), get_string('confighideactivitytypenavlink', 'admin'), 0,
    array(
            0 => get_string('hidefromnone', 'admin'),
            1 => get_string('hidefromstudents', 'admin'),
            2 => get_string('hidefromall', 'admin')
        )));
    $ADMIN->add('themes', $temp);
    $ADMIN->add('themes', new admin_externalpage('themeselector', get_string('themeselector','admin'), $CFG->wwwroot . '/theme/index.php'));

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
    $temp->add(new admin_setting_configtext('calendar_lookahead',get_string('configlookahead','admin'),get_string('helpupcominglookahead', 'admin'),21,PARAM_INT));
    $temp->add(new admin_setting_configtext('calendar_maxevents',get_string('configmaxevents','admin'),get_string('helpupcomingmaxevents', 'admin'),10,PARAM_INT));
    $temp->add(new admin_setting_configcheckbox('enablecalendarexport', get_string('enablecalendarexport', 'admin'), get_string('configenablecalendarexport','admin'), 1));
    $temp->add(new admin_setting_configtext('calendar_exportsalt', get_string('calendarexportsalt','admin'), get_string('configcalendarexportsalt', 'admin'), random_string(60)));
    $ADMIN->add('appearance', $temp);

    // "htmleditor" settingpage
    $temp = new admin_settingpage('htmleditor', get_string('htmleditor', 'admin'));
    $temp->add(new admin_setting_configcheckbox('htmleditor', get_string('usehtmleditor', 'admin'), get_string('confightmleditor','admin'), 1));
    $temp->add(new admin_setting_configtext('editorbackgroundcolor', get_string('editorbackgroundcolor', 'admin'), get_string('edhelpbgcolor'), '#ffffff', PARAM_NOTAGS));
    $temp->add(new admin_setting_configtext('editorfontfamily', get_string('editorfontfamily', 'admin'), get_string('edhelpfontfamily'), 'Trebuchet MS,Verdana,Arial,Helvetica,sans-serif', PARAM_NOTAGS));
    $temp->add(new admin_setting_configtext('editorfontsize', get_string('editorfontsize', 'admin'), get_string('edhelpfontsize'), '', PARAM_NOTAGS));
    $temp->add(new admin_setting_special_editorfontlist());
    $temp->add(new admin_setting_configcheckbox('editorkillword', get_string('editorkillword', 'admin'), get_string('edhelpcleanword'), 1));
    if (!empty($CFG->aspellpath)) { // make aspell settings disappear if path isn't set
      $temp->add(new admin_setting_configcheckbox('editorspelling', get_string('editorspelling', 'admin'), get_string('editorspellinghelp', 'admin'), 0));
      $temp->add(new admin_setting_special_editordictionary());
    }
    $temp->add(new admin_setting_special_editorhidebuttons());
    $temp->add(new admin_setting_emoticons());
    $ADMIN->add('appearance', $temp);

    // "htmlsettings" settingpage
    $temp = new admin_settingpage('htmlsettings', get_string('htmlsettings', 'admin'));
    $temp->add(new admin_setting_configcheckbox('formatstringstriptags', get_string('stripalltitletags', 'admin'), get_string('configstripalltitletags', 'admin'), 1));
    $ADMIN->add('appearance', $temp);

    // "documentation" settingpage
    $temp = new admin_settingpage('documentation', get_string('moodledocs'));
    $temp->add(new admin_setting_configtext('docroot', get_string('docroot', 'admin'), get_string('configdocroot', 'admin'), 'http://docs.moodle.org', PARAM_URL));
    $temp->add(new admin_setting_configcheckbox('doctonewwindow', get_string('doctonewwindow', 'admin'), get_string('configdoctonewwindow', 'admin'), 0));
    $ADMIN->add('appearance', $temp);

    $temp = new admin_settingpage('mymoodle', get_string('mymoodle', 'admin'));
    $temp->add(new admin_setting_configcheckbox('mymoodleredirect', get_string('mymoodleredirect', 'admin'), get_string('configmymoodleredirect', 'admin'), 0));
    $temp->add(new admin_setting_configtext('mycoursesperpage', get_string('mycoursesperpage', 'admin'), get_string('configmycoursesperpage', 'admin'), 21, PARAM_INT));
    $ADMIN->add('appearance', $temp);

    // new CFG variable for coursemanager (what roles to display)
    $temp = new admin_settingpage('coursemanager', get_string('coursemanager', 'admin'));
    $temp->add(new admin_setting_special_coursemanager());
    $ADMIN->add('appearance', $temp);

    $temp = new admin_settingpage('ajax', get_string('ajaxuse'));
    $temp->add(new admin_setting_configcheckbox('enableajax', get_string('enableajax', 'admin'), get_string('configenableajax', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('disablecourseajax', get_string('disablecourseajax', 'admin'), get_string('configdisablecourseajax', 'admin'),
                                                isset($CFG->disablecourseajax) ? 1 : empty($CFG->enableajax)));
    $ADMIN->add('appearance', $temp);

    // link to tag management interface
    $ADMIN->add('appearance', new admin_externalpage('managetags', get_string('managetags', 'tag'), "$CFG->wwwroot/tag/manage.php"));

} // end of speedup
?>
