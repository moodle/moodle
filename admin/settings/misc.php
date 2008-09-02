<?php // $Id$

// * Miscellaneous settings

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    // Experimental settings page
    $ADMIN->add('misc', new admin_category('experimental', get_string('experimental','admin')));

    $temp = new admin_settingpage('experimentalsettings', get_string('experimentalsettings', 'admin'));
    $temp->add(new admin_setting_configcheckbox('enableglobalsearch', get_string('enableglobalsearch', 'admin'), get_string('configenableglobalsearch', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('smartpix', get_string('smartpix', 'admin'), get_string('configsmartpix', 'admin'), 0));
    $item = new admin_setting_configcheckbox('enablehtmlpurifier', get_string('enablehtmlpurifier', 'admin'), get_string('configenablehtmlpurifier', 'admin'), 0);
    $item->set_updatedcallback('reset_text_filters_cache');
    $temp->add($item);

    // Completion system
    require_once($CFG->libdir.'/completionlib.php');
    $temp->add(new admin_setting_configcheckbox('enablecompletion', get_string('enablecompletion','completion'), get_string('configenablecompletion','completion'), COMPLETION_DISABLED));
    $temp->add(new admin_setting_pickroles('progresstrackedroles', get_string('progresstrackedroles','completion'), get_string('configprogresstrackedroles', 'completion'), array('moodle/legacy:student')));

    $ADMIN->add('experimental', $temp);

    // DB transfer related pages
    $ADMIN->add('experimental', new admin_externalpage('dbtransfer', get_string('dbtransfer', 'dbtransfer'), $CFG->wwwroot.'/'.$CFG->admin.'/dbtransfer/index.php', 'moodle/site:config', false));
    $ADMIN->add('experimental', new admin_externalpage('dbexport', get_string('dbexport', 'dbtransfer'), $CFG->wwwroot.'/'.$CFG->admin.'/dbtransfer/dbexport.php', 'moodle/site:config', false));

    // XMLDB editor
    $ADMIN->add('misc', new admin_externalpage('xmldbeditor', get_string('xmldbeditor'), "$CFG->wwwroot/$CFG->admin/xmldb/"));


    // hidden scripts linked from elsewhere
    $ADMIN->add('misc', new admin_externalpage('oacleanup', 'Online Assignment Cleanup', $CFG->wwwroot.'/'.$CFG->admin.'/oacleanup.php', 'moodle/site:config', true));
    $ADMIN->add('misc', new admin_externalpage('multilangupgrade', get_string('multilangupgrade', 'admin'), $CFG->wwwroot.'/'.$CFG->admin.'/multilangupgrade.php', 'moodle/site:config', !empty($CFG->filter_multilang_converted)));

} // end of speedup

?>
