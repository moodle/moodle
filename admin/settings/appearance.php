<?php // $Id$

// This file defines settingpages and externalpages under the "appearance" category

$ADMIN->add('appearance', new admin_category('themes', get_string('themes')));
// "themesettings" settingpage
$temp = new admin_settingpage('themesettings', get_string('themesettings', 'admin'));
$temp->add(new admin_setting_configtext('themelist', get_string('themelist', 'admin'), get_string('configthemelist','admin'), '', PARAM_NOTAGS));
$temp->add(new admin_setting_configcheckbox('allowuserthemes', get_string('allowuserthemes', 'admin'), get_string('configallowuserthemes', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('allowcoursethemes', get_string('allowcoursethemes', 'admin'), get_string('configallowcoursethemes', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('allowuserblockhiding', get_string('allowuserblockhiding', 'admin'), get_string('configallowuserblockhiding', 'admin'), 1));
$temp->add(new admin_setting_configcheckbox('showblocksonmodpages', get_string('showblocksonmodpages', 'admin'), get_string('configshowblocksonmodpages', 'admin'), 0));
$temp->add(new admin_setting_configselect('hideactivitytypecrumb', get_string('hideactivitytypecrumb', 'admin'), get_string('confighideactivitytypecrumb', 'admin'), 0,
array(
        0 => get_string('hidefromnone', 'admin'),
        1 => get_string('hidefromstudents', 'admin'),
        2 => get_string('hidefromall', 'admin')
    )));
$ADMIN->add('themes', $temp);
$ADMIN->add('themes', new admin_externalpage('themeselector', get_string('themeselector','admin'), $CFG->wwwroot . '/theme/index.php'));

# for CALENDAR_TF_12 and CALENDAR_TF_24 ...
require_once($CFG->dirroot . '/calendar/lib.php');

// calendar
$temp = new admin_settingpage('calendar', get_string('calendarsettings','admin'));
$temp->add(new admin_setting_special_adminseesall());
$temp->add(new admin_setting_configselect('calendar_site_timeformat', get_string('pref_timeformat', 'calendar'), get_string('explain_site_timeformat', 'calendar'), '0',
array(  0              => get_string('default', 'calendar'),
        CALENDAR_TF_12 => get_string('timeformat_12', 'calendar'),
        CALENDAR_TF_24 => get_string('timeformat_24', 'calendar')
    )));
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
$ADMIN->add('appearance', $temp);

// "filtersettings" settingpage
$temp = new admin_settingpage('filtersettings', get_string('filtersettings', 'admin'));
$temp->add(new admin_setting_configselect('cachetext', get_string('cachetext', 'admin'), get_string('configcachetext', 'admin'), 60, array(604800 => get_string('numdays','',7),
                                                                                                                                       86400 => get_string('numdays','',1),
                                                                                                                                       43200 => get_string('numhours','',12),
                                                                                                                                       10800 => get_string('numhours','',3),
                                                                                                                                       7200 => get_string('numhours','',2),
                                                                                                                                       3600 => get_string('numhours','',1),
                                                                                                                                       2700 => get_string('numminutes','',45),
                                                                                                                                       1800 => get_string('numminutes','',30),
                                                                                                                                       900 => get_string('numminutes','',15),
                                                                                                                                       600 => get_string('numminutes','',10),
                                                                                                                                       540 => get_string('numminutes','',9),
                                                                                                                                       480 => get_string('numminutes','',8),
                                                                                                                                       420 => get_string('numminutes','',7),
                                                                                                                                       360 => get_string('numminutes','',6),
                                                                                                                                       300 => get_string('numminutes','',5),
                                                                                                                                       240 => get_string('numminutes','',4),
                                                                                                                                       180 => get_string('numminutes','',3),
                                                                                                                                       120 => get_string('numminutes','',2),
                                                                                                                                       60 => get_string('numminutes','',1),
                                                                                                                                       30 => get_string('numseconds','',30),
                                                                                                                                       0 => get_string('no'))));
$temp->add(new admin_setting_configselect('filteruploadedfiles', get_string('filteruploadedfiles', 'admin'), get_string('configfilteruploadedfiles', 'admin'), 0, array('0' => get_string('none'),
                                                                                                                                                                     '1' => get_string('allfiles'),
                                                                                                                                                                        '2' => get_string('htmlfilesonly'))));
$temp->add(new admin_setting_configcheckbox('filtermatchoneperpage', get_string('filtermatchoneperpage', 'admin'), get_string('configfiltermatchoneperpage', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('filtermatchonepertext', get_string('filtermatchonepertext', 'admin'), get_string('configfiltermatchonepertext', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('filterall', get_string('filterall', 'admin'), get_string('configfilterall', 'admin'), 0));
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
$ADMIN->add('appearance', $temp);

// "documentation" settingpage
$temp = new admin_settingpage('documentation', get_string('moodledocs'));
$temp->add(new admin_setting_configtext('docroot', get_string('docroot', 'admin'), get_string('configdocroot', 'admin'), 'http://docs.moodle.org', PARAM_URL));
$temp->add(new admin_setting_configcheckbox('doctonewwindow', get_string('doctonewwindow', 'admin'), get_string('configdoctonewwindow', 'admin'), 0));
$ADMIN->add('appearance', $temp);

$temp = new admin_settingpage('mymoodle', get_string('mymoodle', 'admin'));
$temp->add(new admin_setting_configcheckbox('mymoodleredirect', get_string('mymoodleredirect', 'admin'), get_string('configmymoodleredirect', 'admin'), 0));
$ADMIN->add('appearance', $temp);

// new CFG variable for gradebook (what roles to display)
$temp = new admin_settingpage('gradebook', get_string('gradebook', 'admin'));
$temp->add(new admin_setting_special_gradebookroles());
$ADMIN->add('appearance', $temp);

// new CFG variable for coursemanager (what roles to display)
$temp = new admin_settingpage('coursemanager', get_string('coursemanager', 'admin'));
$temp->add(new admin_setting_special_coursemanager());
$ADMIN->add('appearance', $temp);

$ADMIN->add('appearance', new admin_externalpage('stickyblocks', get_string('stickyblocks', 'admin'), "$CFG->wwwroot/$CFG->admin/stickyblocks.php"));

?>
