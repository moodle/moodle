<?php // $Id$

// * Miscellaneous settings

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    // Experimental settings page
    $temp = new admin_settingpage('experimental', get_string('experimental', 'admin'));
    $temp->add(new admin_setting_configcheckbox('enableglobalsearch', get_string('enableglobalsearch', 'admin'), get_string('configenableglobalsearch', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('smartpix', get_string('smartpix', 'admin'), get_string('configsmartpix', 'admin'), 0));
    $item = new admin_setting_configcheckbox('enablehtmlpurifier', get_string('enablehtmlpurifier', 'admin'), get_string('configenablehtmlpurifier', 'admin'), 0); 
    $item->set_updatedcallback('reset_text_filters_cache');
    $temp->add($item);
    $temp->add(new admin_setting_configcheckbox('enablegroupings', get_string('enablegroupings', 'admin'), get_string('configenablegroupings', 'admin'), 0));
    $rqsetting = new admin_setting_configcheckbox('selectmanual', get_string('selectmanualquestions', 'qtype_random'), get_string('configselectmanualquestions', 'qtype_random'), 0);
    $rqsetting->plugin = 'qtype_random';
    $temp->add($rqsetting);
    $temp->add(new admin_setting_configcheckbox('experimentalsplitrestore', get_string('experimentalsplitrestore', 'admin'), get_string('configexperimentalsplitrestore', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('enableimsccimport', get_string('enable_cc_import', 'imscc'), get_string('enable_cc_import_description', 'imscc'), 0));
    $temp->add(new admin_setting_configcheckbox('enablesafebrowserintegration', get_string('enablesafebrowserintegration', 'admin'), get_string('configenablesafebrowserintegration', 'admin'), 0));

    $ADMIN->add('misc', $temp);

    // XMLDB editor
    $ADMIN->add('misc', new admin_externalpage('xmldbeditor', get_string('xmldbeditor'), "$CFG->wwwroot/$CFG->admin/xmldb/"));


    // hidden scripts linked from elsewhere
    $ADMIN->add('misc', new admin_externalpage('oacleanup', 'Online Assignment Cleanup', $CFG->wwwroot.'/'.$CFG->admin.'/oacleanup.php', 'moodle/site:config', true));
    $ADMIN->add('misc', new admin_externalpage('upgradeforumread', 'Upgrade forum', $CFG->wwwroot.'/'.$CFG->admin.'/upgradeforumread.php', 'moodle/site:config', true));
    $ADMIN->add('misc', new admin_externalpage('upgradelogs', 'Upgrade logs', $CFG->wwwroot.'/'.$CFG->admin.'/upgradelogs.php', 'moodle/site:config', true));
    $ADMIN->add('misc', new admin_externalpage('multilangupgrade', get_string('multilangupgrade', 'admin'), $CFG->wwwroot.'/'.$CFG->admin.'/multilangupgrade.php', 'moodle/site:config', !empty($CFG->filter_multilang_converted)));

} // end of speedup

?>
