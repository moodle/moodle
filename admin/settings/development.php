<?php

// * Miscellaneous settings

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    // Experimental settings page
    $ADMIN->add('development', new admin_category('experimental', get_string('experimental','admin')));

    require_once($CFG->dirroot .'/search/lib.php');
    $temp = new admin_settingpage('experimentalsettings', get_string('experimentalsettings', 'admin'));
    $englobalsearch = new admin_setting_configcheckbox('enableglobalsearch', get_string('enableglobalsearch', 'admin'), get_string('configenableglobalsearch', 'admin'), 0);
    $englobalsearch->set_updatedcallback('search_updatedcallback');
    $temp->add($englobalsearch);
    //TODO: Re-enable cc-import once re-implemented in 2.0.x
    //$temp->add(new admin_setting_configcheckbox('enableimsccimport', get_string('enable_cc_import', 'imscc'), get_string('enable_cc_import_description', 'imscc'), 0));
    $temp->add(new admin_setting_configcheckbox('enablesafebrowserintegration', get_string('enablesafebrowserintegration', 'admin'), get_string('configenablesafebrowserintegration', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('enablegroupmembersonly', get_string('enablegroupmembersonly', 'admin'), get_string('configenablegroupmembersonly', 'admin'), 0));

    $ADMIN->add('experimental', $temp);

    // DB transfer related pages
    $ADMIN->add('experimental', new admin_externalpage('dbtransfer', get_string('dbtransfer', 'dbtransfer'), $CFG->wwwroot.'/'.$CFG->admin.'/dbtransfer/index.php', 'moodle/site:config', true));
    $ADMIN->add('experimental', new admin_externalpage('dbexport', get_string('dbexport', 'dbtransfer'), $CFG->wwwroot.'/'.$CFG->admin.'/dbtransfer/dbexport.php', 'moodle/site:config', true));

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

    // "profiling" settingpage (conditionally if the 'xhprof' extension is available only)
    if (extension_loaded('xhprof') && function_exists('xhprof_enable')) {
        $temp = new admin_settingpage('profiling', get_string('profiling', 'admin'));
        // Main profiling switch
        $temp->add(new admin_setting_configcheckbox('profilingenabled', get_string('profilingenabled', 'admin'), get_string('profilingenabled_help', 'admin'), false));
        // List of URLs that will be automatically profiled
        $temp->add(new admin_setting_configtextarea('profilingincluded', get_string('profilingincluded', 'admin'), get_string('profilingincluded_help', 'admin'), ''));
        // List of URLs that won't be profiled ever
        $temp->add(new admin_setting_configtextarea('profilingexcluded', get_string('profilingexcluded', 'admin'), get_string('profilingexcluded_help', 'admin'), ''));
        // Allow random profiling each XX requests
        $temp->add(new admin_setting_configtext('profilingautofrec', get_string('profilingautofrec', 'admin'), get_string('profilingautofrec_help', 'admin'), 0, PARAM_INT));
        // Allow PROFILEME/DONTPROFILEME GPC
        $temp->add(new admin_setting_configcheckbox('profilingallowme', get_string('profilingallowme', 'admin'), get_string('profilingallowme_help', 'admin'), false));
        // Allow PROFILEALL/PROFILEALLSTOP GPC
        $temp->add(new admin_setting_configcheckbox('profilingallowall', get_string('profilingallowall', 'admin'), get_string('profilingallowall_help', 'admin'), false));
        // TODO: Allow to skip PHP functions (XHPROF_FLAGS_NO_BUILTINS)
        // TODO: Allow to skip call_user functions (ignored_functions array)
        // Specify the life time (in minutes) of profiling runs
        $temp->add(new admin_setting_configselect('profilinglifetime', get_string('profilinglifetime', 'admin'), get_string('profilinglifetime_help', 'admin'), 24*60, array(
             0 => get_string('neverdeleteruns', 'admin'),
      30*24*60 => get_string('numdays', '', 30),
      15*24*60 => get_string('numdays', '', 15),
       7*24*60 => get_string('numdays', '', 7),
       4*24*60 => get_string('numdays', '', 4),
       2*24*60 => get_string('numdays', '', 2),
         24*60 => get_string('numhours', '', 24),
         16*80 => get_string('numhours', '', 16),
          8*60 => get_string('numhours', '', 8),
          4*60 => get_string('numhours', '', 4),
          2*60 => get_string('numhours', '', 2),
            60 => get_string('numminutes', '', 60),
            30 => get_string('numminutes', '', 30),
            15 => get_string('numminutes', '', 15))));

        // Add the 'profiling' page to admin block
        $ADMIN->add('development', $temp);
    }


    // XMLDB editor
    $ADMIN->add('development', new admin_externalpage('xmldbeditor', get_string('xmldbeditor'), "$CFG->wwwroot/$CFG->admin/xmldb/"));


     // Web service test clients DO NOT COMMIT : THE EXTERNAL WEB PAGE IS NOT AN ADMIN PAGE !!!!!
    $ADMIN->add('development', new admin_externalpage('testclient', get_string('testclient', 'webservice'), "$CFG->wwwroot/$CFG->admin/webservice/testclient.php"));


    if ($CFG->mnet_dispatcher_mode !== 'off') {
        $ADMIN->add('development', new admin_externalpage('mnettestclient', get_string('testclient', 'mnet'), "$CFG->wwwroot/$CFG->admin/mnet/testclient.php"));
    }

    $ADMIN->add('development', new admin_externalpage('purgecaches', get_string('purgecaches','admin'), "$CFG->wwwroot/$CFG->admin/purgecaches.php"));
} // end of speedup
