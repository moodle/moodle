<?php // $Id$

// * Miscellaneous settings

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    // Experimental settings page
    $ADMIN->add('development', new admin_category('experimental', get_string('experimental','admin')));

    $temp = new admin_settingpage('experimentalsettings', get_string('experimentalsettings', 'admin'));
    $temp->add(new admin_setting_configcheckbox('enableglobalsearch', get_string('enableglobalsearch', 'admin'), get_string('configenableglobalsearch', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('smartpix', get_string('smartpix', 'admin'), get_string('configsmartpix', 'admin'), 0));
    $item = new admin_setting_configcheckbox('enablehtmlpurifier', get_string('enablehtmlpurifier', 'admin'), get_string('configenablehtmlpurifier', 'admin'), 0);
    $item->set_updatedcallback('reset_text_filters_cache');
    $temp->add($item);
    $temp->add(new admin_setting_configcheckbox('experimentalsplitrestore', get_string('experimentalsplitrestore', 'admin'), get_string('configexperimentalsplitrestore', 'admin'), 0));

    $ADMIN->add('experimental', $temp);

    // DB transfer related pages
    $ADMIN->add('experimental', new admin_externalpage('dbtransfer', get_string('dbtransfer', 'dbtransfer'), $CFG->wwwroot.'/'.$CFG->admin.'/dbtransfer/index.php', 'moodle/site:config'));
    $ADMIN->add('experimental', new admin_externalpage('dbexport', get_string('dbexport', 'dbtransfer'), $CFG->wwwroot.'/'.$CFG->admin.'/dbtransfer/dbexport.php', 'moodle/site:config'));

    // XMLDB editor
    $ADMIN->add('development', new admin_externalpage('xmldbeditor', get_string('xmldbeditor'), "$CFG->wwwroot/$CFG->admin/xmldb/"));

} // end of speedup

?>
