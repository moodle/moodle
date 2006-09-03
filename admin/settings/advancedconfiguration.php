<?php // $Id$

// This file defines settingpages and externalpages under the "advancedconfiguration" category

// "systempaths" settingpage
$temp = new admin_settingpage('systempaths', get_string('systempaths','admin'));
$temp->add(new admin_setting_configselect('gdversion', get_string('gdversion','admin'), get_string('configgdversion', 'admin'), check_gd_version(), array('0' => get_string('gdnot'),
                                                                                                                                                          '1' => get_string('gd1'),
                                                                                                                                                          '2' => get_string('gd2'))));
$temp->add(new admin_setting_configtext('zip', get_string('pathtozip','admin'), get_string('configzip', 'admin'), ''));
$temp->add(new admin_setting_configtext('unzip', get_string('pathtounzip','admin'), get_string('configunzip', 'admin'), ''));
$temp->add(new admin_setting_configtext('pathtodu', get_string('pathtodu', 'admin'), get_string('configpathtodu', 'admin'), ''));
$temp->add(new admin_setting_configtext('aspellpath', get_string('aspellpath', 'admin'), get_string('edhelpaspellpath'), ''));
$ADMIN->add('advancedconfiguration', $temp);

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
$ADMIN->add('advancedconfiguration', $temp);



// "antivirus" settingpage
$temp = new admin_settingpage('antivirus', get_string('antivirus', 'admin'));
$temp->add(new admin_setting_configcheckbox('runclamavonupload', get_string('runclamavonupload', 'admin'), get_string('configrunclamavonupload', 'admin'), 0));
$temp->add(new admin_setting_configtext('pathtoclam', get_string('pathtoclam', 'admin'), get_string('configpathtoclam', 'admin'), '', PARAM_PATH));
$temp->add(new admin_setting_configtext('quarantinedir', get_string('quarantinedir', 'admin'), get_string('configquarantinedir', 'admin'), '', PARAM_PATH));
$temp->add(new admin_setting_configselect('clamfailureonupload', get_string('clamfailureonupload', 'admin'), get_string('configclamfailureonupload', 'admin'), 'donothing', array('donothing' => get_string('configclamdonothing', 'admin'),
                                                                                                                                                                                  'actlikevirus' => get_string('configclamactlikevirus', 'admin'))));
$ADMIN->add('advancedconfiguration', $temp);


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
$ADMIN->add('advancedconfiguration', $temp);



// "rss" settingpage
$temp = new admin_settingpage('rss', get_string('rss'));
$temp->add(new admin_setting_configcheckbox('enablerssfeeds', get_string('enablerssfeeds', 'admin'), get_string('configenablerssfeeds', 'admin'), 0));
$ADMIN->add('advancedconfiguration', $temp);



// "http" settingpage
$temp = new admin_settingpage('http', get_string('http', 'admin'));
$temp->add(new admin_setting_configtext('framename', get_string('framename', 'admin'), get_string('configframename', 'admin'), '_top', PARAM_ALPHAEXT));
$temp->add(new admin_Setting_configcheckbox('slasharguments', get_string('slasharguments', 'admin'), get_string('configslasharguments', 'admin'), 1));
$temp->add(new admin_setting_configtext('proxyhost', get_string('proxyhost', 'admin'), get_string('configproxyhost', 'admin'), '', PARAM_HOST));
$temp->add(new admin_setting_configtext('proxyport', get_string('proxyport', 'admin'), get_string('configproxyport', 'admin'), '', PARAM_INT));
$ADMIN->add('advancedconfiguration', $temp);

// filters
$ADMIN->add('advancedconfiguration', new admin_externalpage('managefilters', get_string('managefilters'), $CFG->wwwroot . '/admin/filters.php'));
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
$ADMIN->add('advancedconfiguration', $temp);

// blocks
$ADMIN->add('advancedconfiguration', new admin_externalpage('manageblocks', get_string('manageblocks'), $CFG->wwwroot . '/admin/blocks.php'));
$ADMIN->add('advancedconfiguration', new admin_externalpage('stickyblocks', get_string('stickyblocks','admin'), $CFG->wwwroot . '/admin/stickyblocks.php'));

// modules
$ADMIN->add('advancedconfiguration', new admin_externalpage('managemodules', get_string('managemodules'), $CFG->wwwroot . '/admin/modules.php'));
$temp = new admin_settingpage('modulerestrictions', get_string('modulerestrictions', 'admin'));
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
$ADMIN->add('advancedconfiguration', $temp);

$ADMIN->add('advancedconfiguration', new admin_externalpage('langedit', get_string('langedit', 'admin'), $CFG->wwwroot . '/admin/lang.php'));
$ADMIN->add('advancedconfiguration', new admin_externalpage('langimport', get_string('langimport', 'admin'), $CFG->wwwroot . '/admin/langimport.php'));




?>