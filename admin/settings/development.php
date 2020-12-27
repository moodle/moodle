<?php

// * Miscellaneous settings

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    // Experimental settings page
    $ADMIN->add('development', new admin_category('experimental', new lang_string('experimental','admin')));

    $temp = new admin_settingpage('experimentalsettings', new lang_string('experimentalsettings', 'admin'));
    //TODO: Re-enable cc-import once re-implemented in 2.0.x
    //$temp->add(new admin_setting_configcheckbox('enableimsccimport', new lang_string('enable_cc_import', 'imscc'), new lang_string('enable_cc_import_description', 'imscc'), 0));

    $temp->add(new admin_setting_configcheckbox('dndallowtextandlinks', new lang_string('dndallowtextandlinks', 'admin'), new lang_string('configdndallowtextandlinks', 'admin'), 0));

    $temp->add(new admin_setting_configexecutable('pathtosassc', new lang_string('pathtosassc', 'admin'), new lang_string('pathtosassc_help', 'admin'), ''));

    $temp->add(new admin_setting_configcheckbox('contextlocking', new lang_string('contextlocking', 'core_admin'),
        new lang_string('contextlocking_desc', 'core_admin'), 0));

    $temp->add(new admin_setting_configcheckbox(
            'contextlockappliestoadmin',
            new lang_string('contextlockappliestoadmin', 'core_admin'),
            new lang_string('contextlockappliestoadmin_desc', 'core_admin'),
            1
        ));

    $temp->add(new admin_setting_configcheckbox('forceclean', new lang_string('forceclean', 'core_admin'),
        new lang_string('forceclean_desc', 'core_admin'), 0));

    // Relative course dates mode setting.
    $temp->add(new admin_setting_configcheckbox('enablecourserelativedates',
        new lang_string('enablecourserelativedates', 'core_admin'),
        new lang_string('enablecourserelativedates_desc', 'core_admin'), 0));

    $ADMIN->add('experimental', $temp);

    // "debugging" settingpage
    $temp = new admin_settingpage('debugging', new lang_string('debugging', 'admin'));
    $temp->add(new admin_setting_special_debug());
    $temp->add(new admin_setting_configcheckbox('debugdisplay', new lang_string('debugdisplay', 'admin'), new lang_string('configdebugdisplay', 'admin'), ini_get_bool('display_errors')));
    $temp->add(new admin_setting_configcheckbox('perfdebug', new lang_string('perfdebug', 'admin'), new lang_string('configperfdebug', 'admin'), '7', '15', '7'));
    $temp->add(new admin_setting_configcheckbox('debugstringids', new lang_string('debugstringids', 'admin'), new lang_string('debugstringids_desc', 'admin'), 0));
    $temp->add(new admin_setting_configselect('debugsqltrace',
            new lang_string('debugsqltrace', 'admin'),
            new lang_string('debugsqltrace_desc', 'admin'), 0, array(
               0 => new lang_string('disabled', 'admin'),
               1 => new lang_string('debugsqltrace1', 'admin'),
               2 => new lang_string('debugsqltrace2', 'admin'),
             100 => new lang_string('debugsqltrace100', 'admin'))));
    $temp->add(new admin_setting_configcheckbox('debugvalidators', new lang_string('debugvalidators', 'admin'), new lang_string('configdebugvalidators', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('debugpageinfo', new lang_string('debugpageinfo', 'admin'), new lang_string('configdebugpageinfo', 'admin'), 0));
    $ADMIN->add('development', $temp);

    // "Profiling" settingpage (conditionally if the 'xhprof' extension is available only).
    $xhprofenabled = extension_loaded('tideways_xhprof');
    $xhprofenabled = $xhprofenabled || extension_loaded('tideways');
    $xhprofenabled = $xhprofenabled || extension_loaded('xhprof');
    $temp = new admin_settingpage('profiling', new lang_string('profiling', 'admin'), 'moodle/site:config', !$xhprofenabled);
    // Main profiling switch.
    $temp->add(new admin_setting_configcheckbox('profilingenabled', new lang_string('profilingenabled', 'admin'), new lang_string('profilingenabled_help', 'admin'), false));
    // List of URLs that will be automatically profiled.
    $temp->add(new admin_setting_configtextarea('profilingincluded', new lang_string('profilingincluded', 'admin'), new lang_string('profilingincluded_help', 'admin'), ''));
    // List of URLs that won't be profiled ever.
    $temp->add(new admin_setting_configtextarea('profilingexcluded', new lang_string('profilingexcluded', 'admin'), new lang_string('profilingexcluded_help', 'admin'), ''));
    // Allow random profiling each XX requests.
    $temp->add(new admin_setting_configtext('profilingautofrec', new lang_string('profilingautofrec', 'admin'), new lang_string('profilingautofrec_help', 'admin'), 0, PARAM_INT));
    // Allow PROFILEME/DONTPROFILEME GPC.
    $temp->add(new admin_setting_configcheckbox('profilingallowme', new lang_string('profilingallowme', 'admin'), new lang_string('profilingallowme_help', 'admin'), false));
    // Allow PROFILEALL/PROFILEALLSTOP GPC.
    $temp->add(new admin_setting_configcheckbox('profilingallowall', new lang_string('profilingallowall', 'admin'), new lang_string('profilingallowall_help', 'admin'), false));
    $temp->add(new admin_setting_configtext('profilingslow', new lang_string('profilingslow', 'admin'),
        new lang_string('profilingslow_help', 'admin'), 0, PARAM_FLOAT));
    // TODO: Allow to skip PHP functions (XHPROF_FLAGS_NO_BUILTINS)
    // TODO: Allow to skip call_user functions (ignored_functions array)
    // Specify the life time (in minutes) of profiling runs.
    $temp->add(new admin_setting_configselect('profilinglifetime', new lang_string('profilinglifetime', 'admin'), new lang_string('profilinglifetime_help', 'admin'), 24*60, array(
               0 => new lang_string('neverdeleteruns', 'admin'),
        30*24*60 => new lang_string('numdays', '', 30),
        15*24*60 => new lang_string('numdays', '', 15),
         7*24*60 => new lang_string('numdays', '', 7),
         4*24*60 => new lang_string('numdays', '', 4),
         2*24*60 => new lang_string('numdays', '', 2),
           24*60 => new lang_string('numhours', '', 24),
           16*80 => new lang_string('numhours', '', 16),
            8*60 => new lang_string('numhours', '', 8),
            4*60 => new lang_string('numhours', '', 4),
            2*60 => new lang_string('numhours', '', 2),
              60 => new lang_string('numminutes', '', 60),
              30 => new lang_string('numminutes', '', 30),
              15 => new lang_string('numminutes', '', 15))));
    // Define the prefix to be added to imported profiling runs.
    $temp->add(new admin_setting_configtext('profilingimportprefix',
            new lang_string('profilingimportprefix', 'admin'),
            new lang_string('profilingimportprefix_desc', 'admin'), '(I)', PARAM_TAG, 10));

    // Add the 'profiling' page to admin block.
    $ADMIN->add('development', $temp);

     // Web service test clients DO NOT COMMIT : THE EXTERNAL WEB PAGE IS NOT AN ADMIN PAGE !!!!!
    $ADMIN->add('development', new admin_externalpage('testclient', new lang_string('testclient', 'webservice'), "$CFG->wwwroot/$CFG->admin/webservice/testclient.php"));


    if ($CFG->mnet_dispatcher_mode !== 'off') {
        $ADMIN->add('development', new admin_externalpage('mnettestclient', new lang_string('testclient', 'mnet'), "$CFG->wwwroot/$CFG->admin/mnet/testclient.php"));
    }

    $ADMIN->add('development', new admin_externalpage('purgecaches', new lang_string('purgecachespage', 'admin'),
            "$CFG->wwwroot/$CFG->admin/purgecaches.php"));

    $ADMIN->add('development', new admin_externalpage('thirdpartylibs', new lang_string('thirdpartylibs','admin'), "$CFG->wwwroot/$CFG->admin/thirdpartylibs.php"));
} // end of speedup
