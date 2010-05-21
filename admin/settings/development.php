<?php

// * Miscellaneous settings

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    // Experimental settings page
    $ADMIN->add('development', new admin_category('experimental', get_string('experimental','admin')));

    $temp = new admin_settingpage('experimentalsettings', get_string('experimentalsettings', 'admin'));
    $temp->add(new admin_setting_configcheckbox('enableglobalsearch', get_string('enableglobalsearch', 'admin'), get_string('configenableglobalsearch', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('experimentalsplitrestore', get_string('experimentalsplitrestore', 'admin'), get_string('configexperimentalsplitrestore', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('enableimsccimport', get_string('enable_cc_import', 'imscc'), get_string('enable_cc_import_description', 'imscc'), 0));
    $temp->add(new admin_setting_configcheckbox('enablesafebrowserintegration', get_string('enablesafebrowserintegration', 'admin'), get_string('configenablesafebrowserintegration', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('enablegroupmembersonly', get_string('enablegroupmembersonly', 'admin'), get_string('configenablegroupmembersonly', 'admin'), 0));

    $ADMIN->add('experimental', $temp);

    // DB transfer related pages
    $ADMIN->add('experimental', new admin_externalpage('dbtransfer', get_string('dbtransfer', 'dbtransfer'), $CFG->wwwroot.'/'.$CFG->admin.'/dbtransfer/index.php', 'moodle/site:config'));
    $ADMIN->add('experimental', new admin_externalpage('dbexport', get_string('dbexport', 'dbtransfer'), $CFG->wwwroot.'/'.$CFG->admin.'/dbtransfer/dbexport.php', 'moodle/site:config'));

    // "debugging" settingpage
    $temp = new admin_settingpage('debugging', get_string('debugging', 'admin'));
    $temp->add(new admin_setting_special_debug());
    $temp->add(new admin_setting_configcheckbox('debugdisplay', get_string('debugdisplay', 'admin'), get_string('configdebugdisplay', 'admin'), ini_get_bool('display_errors')));
    $temp->add(new admin_setting_configcheckbox('xmlstrictheaders', get_string('xmlstrictheaders', 'admin'), get_string('configxmlstrictheaders', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('debugsmtp', get_string('debugsmtp', 'admin'), get_string('configdebugsmtp', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('perfdebug', get_string('perfdebug', 'admin'), get_string('configperfdebug', 'admin'), '7', '15', '7'));
    $temp->add(new admin_setting_configcheckbox('debugstringids', get_string('debugstringids', 'admin'), get_string('configdebugstringids', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('debugvalidators', get_string('debugvalidators', 'admin'), get_string('configdebugvalidators', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('debugpageinfo', get_string('debugpageinfo', 'admin'), get_string('configdebugpageinfo', 'admin'), 0));
    $ADMIN->add('development', $temp);


    // XMLDB editor
    $ADMIN->add('development', new admin_externalpage('xmldbeditor', get_string('xmldbeditor'), "$CFG->wwwroot/$CFG->admin/xmldb/"));


     // Web service test clients DO NOT COMMIT : THE EXTERNAL WEB PAGE IS NOT AN ADMIN PAGE !!!!!
    $ADMIN->add('development', new admin_externalpage('testclient', get_string('testclient', 'webservice'), "$CFG->wwwroot/$CFG->admin/webservice/testclient.php"));


    if ($CFG->mnet_dispatcher_mode !== 'off') {
        $ADMIN->add('development', new admin_externalpage('mnettestclient', get_string('testclient', 'mnet'), "$CFG->wwwroot/$CFG->admin/mnet/testclient.php"));
    }
} // end of speedup
