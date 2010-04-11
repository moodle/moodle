<?php

// This file defines settingpages and externalpages under the "courses" category

if ($hassiteconfig
 or has_capability('moodle/backup:backupcourse', $systemcontext)
 or has_capability('moodle/category:manage', $systemcontext)
 or has_capability('moodle/course:create', $systemcontext)
 or has_capability('moodle/site:approvecourse', $systemcontext)) { // speedup for non-admins, add all caps used on this page

    $ADMIN->add('courses', new admin_externalpage('coursemgmt', get_string('coursemgmt', 'admin'), $CFG->wwwroot . '/course/index.php?categoryedit=on',
            array('moodle/category:manage', 'moodle/course:create')));

    $ADMIN->add('courses', new admin_enrolment_page());

/// Course Default Settings Page
/// NOTE: these settings must be applied after all other settings because they depend on them
    ///main course settings
    $temp = new admin_settingpage('coursesettings', get_string('coursesettings'));
    $courseformats = get_plugin_list('format');
    $formcourseformats = array();
    foreach ($courseformats as $courseformat => $courseformatdir) {
        $formcourseformats[$courseformat] = get_string('pluginname', "format_$courseformat");
    }
    $temp->add(new admin_setting_configselect('moodlecourse/format', get_string('format'), get_string('coursehelpformat'), 'weeks',$formcourseformats));
    for ($i=1; $i<=52; $i++) {
        $sectionmenu[$i] = "$i";
    }
    $temp->add(new admin_setting_configselect('moodlecourse/numsections', get_string('numberweeks'), get_string('coursehelpnumberweeks'), 10,$sectionmenu));
    $choices = array();
    $choices['0'] = get_string('hiddensectionscollapsed');
    $choices['1'] = get_string('hiddensectionsinvisible');
    $temp->add(new admin_setting_configselect('moodlecourse/hiddensections', get_string('hiddensections'), get_string('coursehelphiddensections'), 0,$choices));
    $options = range(0, 10);
    $temp->add(new admin_setting_configselect('moodlecourse/newsitems', get_string('newsitemsnumber'), get_string('coursehelpnewsitemsnumber'), 5,$options));
    $temp->add(new admin_setting_configselect('moodlecourse/showgrades', get_string('showgrades'), get_string('coursehelpshowgrades'), 1,array(0 => get_string('no'), 1 => get_string('yes'))));
    $temp->add(new admin_setting_configselect('moodlecourse/showreports', get_string('showreports'), '', 0,array(0 => get_string('no'), 1 => get_string('yes'))));
    if (isset($CFG->maxbytes)) {
        $choices = get_max_upload_sizes($CFG->maxbytes);
    } else {
        $choices = get_max_upload_sizes();
    }
    $temp->add(new admin_setting_configselect('moodlecourse/maxbytes', get_string('maximumupload'), get_string('coursehelpmaximumupload'), key($choices), $choices));
    $temp->add(new admin_setting_configselect('moodlecourse/metacourse', get_string('metacourse'), get_string('coursehelpmetacourse'), 0,array(0 => get_string('no'), 1 => get_string('yes'))));

    ///enrolement course settings
    $temp->add(new admin_setting_heading('enrolhdr', get_string('enrolments'), ''));
    require_once($CFG->dirroot.'/enrol/enrol.class.php');
    $choices = array();
    $modules = explode(',', $CFG->enrol_plugins_enabled);
    foreach ($modules as $module) {
        $name = get_string('enrolname', "enrol_$module");
        $plugin = enrolment_factory::factory($module);
        if (method_exists($plugin, 'print_entry')) {
            $choices[$name] = $module;
        }
    }
    asort($choices);
    $choices = array_flip($choices);
    $choices = array_merge(array('' => get_string('sitedefault').' ('.get_string('enrolname', "enrol_$CFG->enrol").')'), $choices);
    $temp->add(new admin_setting_configselect('moodlecourse/enrol', get_string('enrolmentplugins'), get_string('coursehelpenrolmentplugins'), key($choices),$choices));
    $choices = array(0 => get_string('no'), 1 => get_string('yes'), 2 => get_string('enroldate'));
    $temp->add(new admin_setting_configselect('moodlecourse/enrollable', get_string('enrollable'), get_string('coursehelpenrollable'), 1,$choices));
    $periodmenu=array();
    $periodmenu[0] = get_string('unlimited');
    for ($i=1; $i<=365; $i++) {
        $seconds = $i * 86400;
        $periodmenu[$seconds] = get_string('numdays', '', $i);
    }
    $temp->add(new admin_setting_configselect('moodlecourse/enrolperiod', get_string('enrolperiod'), '', 0,$periodmenu));

    ///
    $temp->add(new admin_setting_heading('expirynotifyhdr', get_string('expirynotify'), ''));
    $temp->add(new admin_setting_configselect('moodlecourse/expirynotify', get_string('notify'), get_string('coursehelpnotify'), 0,array(0 => get_string('no'), 1 => get_string('yes'))));
    $temp->add(new admin_setting_configselect('moodlecourse/notifystudents', get_string('expirynotifystudents'), get_string('coursehelpexpirynotifystudents'), 0,array(0 => get_string('no'), 1 => get_string('yes'))));
    $thresholdmenu=array();
    for ($i=1; $i<=30; $i++) {
        $seconds = $i * 86400;
        $thresholdmenu[$seconds] = get_string('numdays', '', $i);
    }
    $temp->add(new admin_setting_configselect('moodlecourse/expirythreshold', get_string('expirythreshold'), get_string('coursehelpexpirythreshold'), 10 * 86400,$thresholdmenu));


    $temp->add(new admin_setting_heading('groups', get_string('groups', 'group'), ''));
    $choices = array();
    $choices[NOGROUPS] = get_string('groupsnone', 'group');
    $choices[SEPARATEGROUPS] = get_string('groupsseparate', 'group');
    $choices[VISIBLEGROUPS] = get_string('groupsvisible', 'group');
    $temp->add(new admin_setting_configselect('moodlecourse/groupmode', get_string('groupmode'), '', key($choices),$choices));
    $temp->add(new admin_setting_configselect('moodlecourse/groupmodeforce', get_string('force'), get_string('coursehelpforce'), 0,array(0 => get_string('no'), 1 => get_string('yes'))));


    $temp->add(new admin_setting_heading('availability', get_string('availability'), ''));
    $choices = array();
    $choices['0'] = get_string('courseavailablenot');
    $choices['1'] = get_string('courseavailable');
    $temp->add(new admin_setting_configselect('moodlecourse/visible', get_string('visible'), '', 1,$choices));
    $temp->add(new admin_setting_configpasswordunmask('moodlecourse/enrolpassword', get_string('enrolmentkey'), get_string('coursehelpenrolmentkey'),''));
    $choices = array();
    $choices['0'] = get_string('guestsno');
    $choices['1'] = get_string('guestsyes');
    $choices['2'] = get_string('guestskey');
    $temp->add(new admin_setting_configselect('moodlecourse/guest', get_string('opentoguests'), '', 0,$choices));


    $temp->add(new admin_setting_heading('language', get_string('language'), ''));
    $languages=array();
    $languages[''] = get_string('forceno');
    $languages += get_list_of_languages();
    $temp->add(new admin_setting_configselect('moodlecourse/lang', get_string('forcelanguage'), '',key($languages),$languages));

    if (!empty($CFG->enablecompletion)) {
        $temp->add(new admin_setting_heading('progress', get_string('progress','completion'), ''));
        $temp->add(new admin_setting_configselect('moodlecourse/enablecompletion', get_string('completion','completion'), '',
            1, array(0 => get_string('completiondisabled','completion'), 1 => get_string('completionenabled','completion'))));
    }
    $ADMIN->add('courses', $temp);

/// "courserequests" settingpage
    $temp = new admin_settingpage('courserequest', get_string('courserequest'));
    $temp->add(new admin_setting_configcheckbox('enablecourserequests', get_string('enablecourserequests', 'admin'), get_string('configenablecourserequests', 'admin'), 0));
    $temp->add(new admin_settings_coursecat_select('defaultrequestcategory', get_string('defaultrequestcategory', 'admin'), get_string('configdefaultrequestcategory', 'admin'), 1));
    $temp->add(new admin_setting_users_with_capability('courserequestnotify', get_string('courserequestnotify', 'admin'), get_string('configcourserequestnotify2', 'admin'), array(), 'moodle/site:approvecourse'));
    $ADMIN->add('courses', $temp);

/// Pending course requests.
    if (!empty($CFG->enablecourserequests)) {
        $ADMIN->add('courses', new admin_externalpage('coursespending', get_string('pendingrequests'),
                $CFG->wwwroot . '/course/pending.php', array('moodle/site:approvecourse')));
    }

/// "backups" settingpage
    $temp = new admin_settingpage('backups', get_string('backups','admin'), 'moodle/backup:backupcourse');
    $temp->add(new admin_setting_configcheckbox('backup/backup_sche_modules', get_string('includemodules'), get_string('backupincludemoduleshelp'), 0));
    $temp->add(new admin_setting_configcheckbox('backup/backup_sche_withuserdata', get_string('includemoduleuserdata'), get_string('backupincludemoduleuserdatahelp'), 0));
    $temp->add(new admin_setting_configcheckbox('backup/backup_sche_metacourse', get_string('metacourse'), get_string('backupmetacoursehelp'), 0));
    $temp->add(new admin_setting_configselect('backup/backup_sche_users', get_string('users'), get_string('backupusershelp'),
            0, array(0 => get_string('all'), 1 => get_string('course'))));
    $temp->add(new admin_setting_configcheckbox('backup/backup_sche_logs', get_string('logs'), get_string('backuplogshelp'), 0));
    $temp->add(new admin_setting_configcheckbox('backup/backup_sche_userfiles', get_string('userfiles'), get_string('backupuserfileshelp'), 0));
    $temp->add(new admin_setting_configcheckbox('backup/backup_sche_coursefiles', get_string('coursefiles'), get_string('backupcoursefileshelp'), 0));
    $temp->add(new admin_setting_configcheckbox('backup/backup_sche_sitefiles', get_string('sitefiles'), get_string('backupsitefileshelp'), 0));
    $temp->add(new admin_setting_configcheckbox('backup_sche_gradebook_history', get_string('gradebookhistories', 'grades'), get_string('backupgradebookhistoryhelp'), 0));
    $temp->add(new admin_setting_configcheckbox('backup/backup_sche_messages', get_string('messages', 'message'), get_string('backupmessageshelp','message'), 0));
    $temp->add(new admin_setting_configcheckbox('backup/backup_sche_blogs', get_string('blogs', 'blog'), get_string('backupblogshelp','blog'), 0));

    $keepoptoins = array(
        0 => get_string('all'), 1 => '1',
        2 => '2',
        5 => '5',
        10 => '10',
        20 => '20',
        30 => '30',
        40 => '40',
        50 => '50',
        100 => '100',
        200 => '200',
        300 => '300',
        400 => '400',
        500 => '500');
    $temp->add(new admin_setting_configselect('backup/backup_sche_keep', get_string('keep'),
            get_string('backupkeephelp'), 1, $keepoptoins));
    $temp->add(new admin_setting_configcheckbox('backup/backup_sche_active', get_string('active'), get_string('backupactivehelp'), 0));
    $temp->add(new admin_setting_special_backupdays());
    $temp->add(new admin_setting_configtime('backup/backup_sche_hour', 'backup_sche_minute', get_string('executeat'),
            get_string('backupexecuteathelp'), array('h' => 0, 'm' => 0)));
    $temp->add(new admin_setting_configdirectory('backup/backup_sche_destination', get_string('saveto'), get_string('backupsavetohelp'), ''));

    $ADMIN->add('courses', $temp);

} // end of speedup
