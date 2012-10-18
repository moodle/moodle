<?php

// This file defines settingpages and externalpages under the "courses" category

if ($hassiteconfig
 or has_capability('moodle/backup:backupcourse', $systemcontext)
 or has_capability('moodle/category:manage', $systemcontext)
 or has_capability('moodle/course:create', $systemcontext)
 or has_capability('moodle/site:approvecourse', $systemcontext)) { // speedup for non-admins, add all caps used on this page

    $ADMIN->add('courses', new admin_externalpage('coursemgmt', new lang_string('coursemgmt', 'admin'), $CFG->wwwroot . '/course/index.php?categoryedit=on',
            array('moodle/category:manage', 'moodle/course:create')));

/// Course Default Settings Page
/// NOTE: these settings must be applied after all other settings because they depend on them
    ///main course settings
    $temp = new admin_settingpage('coursesettings', new lang_string('coursesettings'));
    require_once($CFG->dirroot.'/course/lib.php');
    $courseformats = get_sorted_course_formats(true);
    $formcourseformats = array();
    foreach ($courseformats as $courseformat) {
        $formcourseformats[$courseformat] = new lang_string('pluginname', "format_$courseformat");
    }
    $temp->add(new admin_setting_configselect('moodlecourse/format', new lang_string('format'), new lang_string('coursehelpformat'), 'weeks',$formcourseformats));

    $temp->add(new admin_setting_configtext('moodlecourse/maxsections', new lang_string('maxnumberweeks'), new lang_string('maxnumberweeks_desc'), 52));

    $temp->add(new admin_settings_num_course_sections('moodlecourse/numsections', new lang_string('numberweeks'), new lang_string('coursehelpnumberweeks'), 10));

    $choices = array();
    $choices['0'] = new lang_string('hiddensectionscollapsed');
    $choices['1'] = new lang_string('hiddensectionsinvisible');
    $temp->add(new admin_setting_configselect('moodlecourse/hiddensections', new lang_string('hiddensections'), new lang_string('coursehelphiddensections'), 0,$choices));
    $options = range(0, 10);
    $temp->add(new admin_setting_configselect('moodlecourse/newsitems', new lang_string('newsitemsnumber'), new lang_string('coursehelpnewsitemsnumber'), 5,$options));
    $temp->add(new admin_setting_configselect('moodlecourse/showgrades', new lang_string('showgrades'), new lang_string('coursehelpshowgrades'), 1,array(0 => new lang_string('no'), 1 => new lang_string('yes'))));
    $temp->add(new admin_setting_configselect('moodlecourse/showreports', new lang_string('showreports'), '', 0,array(0 => new lang_string('no'), 1 => new lang_string('yes'))));

    $currentmaxbytes = get_config('moodlecourse', 'maxbytes');
    if (isset($CFG->maxbytes)) {
        $choices = get_max_upload_sizes($CFG->maxbytes, 0, 0, $currentmaxbytes);
    } else {
        $choices = get_max_upload_sizes(0, 0, 0, $currentmaxbytes);
    }
    $temp->add(new admin_setting_configselect('moodlecourse/maxbytes', new lang_string('maximumupload'), new lang_string('coursehelpmaximumupload'), key($choices), $choices));

    if (!empty($CFG->legacyfilesinnewcourses)) {
        $choices = array('0'=>new lang_string('no'), '2'=>new lang_string('yes'));
        $temp->add(new admin_setting_configselect('moodlecourse/legacyfiles', new lang_string('courselegacyfiles'), new lang_string('courselegacyfiles_help'), key($choices), $choices));
    }

    $choices = array();
    $choices[COURSE_DISPLAY_SINGLEPAGE] = new lang_string('coursedisplay_single');
    $choices[COURSE_DISPLAY_MULTIPAGE] = new lang_string('coursedisplay_multi');
    $temp->add(new admin_setting_configselect('moodlecourse/coursedisplay', new lang_string('coursedisplay'), new lang_string('coursedisplay_help'), COURSE_DISPLAY_SINGLEPAGE, $choices));

    $temp->add(new admin_setting_heading('groups', new lang_string('groups', 'group'), ''));
    $choices = array();
    $choices[NOGROUPS] = new lang_string('groupsnone', 'group');
    $choices[SEPARATEGROUPS] = new lang_string('groupsseparate', 'group');
    $choices[VISIBLEGROUPS] = new lang_string('groupsvisible', 'group');
    $temp->add(new admin_setting_configselect('moodlecourse/groupmode', new lang_string('groupmode'), '', key($choices),$choices));
    $temp->add(new admin_setting_configselect('moodlecourse/groupmodeforce', new lang_string('force'), new lang_string('coursehelpforce'), 0,array(0 => new lang_string('no'), 1 => new lang_string('yes'))));


    $temp->add(new admin_setting_heading('availability', new lang_string('availability'), ''));
    $choices = array();
    $choices['0'] = new lang_string('courseavailablenot');
    $choices['1'] = new lang_string('courseavailable');
    $temp->add(new admin_setting_configselect('moodlecourse/visible', new lang_string('visible'), '', 1,$choices));


    $temp->add(new admin_setting_heading('language', new lang_string('language'), ''));
    $languages=array();
    $languages[''] = new lang_string('forceno');
    $languages += get_string_manager()->get_list_of_translations();
    $temp->add(new admin_setting_configselect('moodlecourse/lang', new lang_string('forcelanguage'), '',key($languages),$languages));

    $temp->add(new admin_setting_heading('progress', new lang_string('progress','completion'), ''));
    $temp->add(new admin_setting_configselect('moodlecourse/enablecompletion', new lang_string('completion','completion'), '',
        0, array(0 => new lang_string('completiondisabled','completion'), 1 => new lang_string('completionenabled','completion'))));

    $temp->add(new admin_setting_configcheckbox('moodlecourse/completionstartonenrol', new lang_string('completionstartonenrol','completion'), new lang_string('completionstartonenrolhelp', 'completion'), 0));
    $ADMIN->add('courses', $temp);

/// "courserequests" settingpage
    $temp = new admin_settingpage('courserequest', new lang_string('courserequest'));
    $temp->add(new admin_setting_configcheckbox('enablecourserequests', new lang_string('enablecourserequests', 'admin'), new lang_string('configenablecourserequests', 'admin'), 0));
    $temp->add(new admin_settings_coursecat_select('defaultrequestcategory', new lang_string('defaultrequestcategory', 'admin'), new lang_string('configdefaultrequestcategory', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('requestcategoryselection', new lang_string('requestcategoryselection', 'admin'), new lang_string('configrequestcategoryselection', 'admin'), 0));
    $temp->add(new admin_setting_users_with_capability('courserequestnotify', new lang_string('courserequestnotify', 'admin'), new lang_string('configcourserequestnotify2', 'admin'), array(), 'moodle/site:approvecourse'));
    $ADMIN->add('courses', $temp);

/// Pending course requests.
    if (!empty($CFG->enablecourserequests)) {
        $ADMIN->add('courses', new admin_externalpage('coursespending', new lang_string('pendingrequests'),
                $CFG->wwwroot . '/course/pending.php', array('moodle/site:approvecourse')));
    }

    // Add a category for backups
    $ADMIN->add('courses', new admin_category('backups', new lang_string('backups','admin')));

    // Create a page for general backups configuration and defaults.
    $temp = new admin_settingpage('backupgeneralsettings', new lang_string('generalbackdefaults', 'backup'), 'moodle/backup:backupcourse');

    // General configuration section.
    $temp->add(new admin_setting_configselect('backup/loglifetime', new lang_string('loglifetime', 'backup'), new lang_string('configloglifetime', 'backup'), 30, array(
        1   => new lang_string('numdays', '', 1),
        2   => new lang_string('numdays', '', 2),
        3   => new lang_string('numdays', '', 3),
        5   => new lang_string('numdays', '', 5),
        7   => new lang_string('numdays', '', 7),
        10  => new lang_string('numdays', '', 10),
        14  => new lang_string('numdays', '', 14),
        20  => new lang_string('numdays', '', 20),
        30  => new lang_string('numdays', '', 30),
        60  => new lang_string('numdays', '', 60),
        90  => new lang_string('numdays', '', 90),
        120 => new lang_string('numdays', '', 120),
        180 => new lang_string('numdays', '', 180),
        365 => new lang_string('numdays', '', 365)
    )));

    // General defaults section.
    $temp->add(new admin_setting_heading('generalsettings', new lang_string('generalsettings', 'backup'), ''));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_users', new lang_string('generalusers','backup'), new lang_string('configgeneralusers','backup'), array('value'=>1, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_anonymize', new lang_string('generalanonymize','backup'), new lang_string('configgeneralanonymize','backup'), array('value'=>0, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_role_assignments', new lang_string('generalroleassignments','backup'), new lang_string('configgeneralroleassignments','backup'), array('value'=>1, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_activities', new lang_string('generalactivities','backup'), new lang_string('configgeneralactivities','backup'), array('value'=>1, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_blocks', new lang_string('generalblocks','backup'), new lang_string('configgeneralblocks','backup'), array('value'=>1, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_filters', new lang_string('generalfilters','backup'), new lang_string('configgeneralfilters','backup'), array('value'=>1, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_comments', new lang_string('generalcomments','backup'), new lang_string('configgeneralcomments','backup'), array('value'=>1, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_userscompletion', new lang_string('generaluserscompletion','backup'), new lang_string('configgeneraluserscompletion','backup'), array('value'=>1, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_logs', new lang_string('generallogs','backup'), new lang_string('configgenerallogs','backup'), array('value'=>0, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_histories', new lang_string('generalhistories','backup'), new lang_string('configgeneralhistories','backup'), array('value'=>0, 'locked'=>0)));
    $ADMIN->add('backups', $temp);

    // Create a page for automated backups configuration and defaults.
    $temp = new admin_settingpage('automated', new lang_string('automatedsetup','backup'), 'moodle/backup:backupcourse');

    // Automated configuration section.
    $temp->add(new admin_setting_configselect('backup/backup_auto_active', new lang_string('active'),  new lang_string('autoactivedescription', 'backup'), 0, array(
        0 => new lang_string('autoactivedisabled', 'backup'),
        1 => new lang_string('autoactiveenabled', 'backup'),
        2 => new lang_string('autoactivemanual', 'backup')
    )));
    $temp->add(new admin_setting_special_backupdays());
    $temp->add(new admin_setting_configtime('backup/backup_auto_hour', 'backup_auto_minute', new lang_string('executeat'),
            new lang_string('backupexecuteathelp'), array('h' => 0, 'm' => 0)));
    $storageoptions = array(
        0 => new lang_string('storagecourseonly', 'backup'),
        1 => new lang_string('storageexternalonly', 'backup'),
        2 => new lang_string('storagecourseandexternal', 'backup')
    );
    $temp->add(new admin_setting_configselect('backup/backup_auto_storage', new lang_string('automatedstorage', 'backup'), new lang_string('automatedstoragehelp', 'backup'), 0, $storageoptions));
    $temp->add(new admin_setting_configdirectory('backup/backup_auto_destination', new lang_string('saveto'), new lang_string('backupsavetohelp'), ''));
    $keepoptoins = array(
        0 => new lang_string('all'), 1 => '1',
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
    $temp->add(new admin_setting_configselect('backup/backup_auto_keep', new lang_string('keep'), new lang_string('backupkeephelp'), 1, $keepoptoins));
    $temp->add(new admin_setting_configcheckbox('backup/backup_shortname', new lang_string('backup_shortname', 'admin'), new lang_string('backup_shortnamehelp', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_skip_hidden', new lang_string('skiphidden', 'backup'), new lang_string('skiphiddenhelp', 'backup'), 1));
    $temp->add(new admin_setting_configselect('backup/backup_auto_skip_modif_days', new lang_string('skipmodifdays', 'backup'), new lang_string('skipmodifdayshelp', 'backup'), 30, array(
        0 => new lang_string('never'),
        1 => new lang_string('numdays', '', 1),
        2 => new lang_string('numdays', '', 2),
        3 => new lang_string('numdays', '', 3),
        5 => new lang_string('numdays', '', 5),
        7 => new lang_string('numdays', '', 7),
        10 => new lang_string('numdays', '', 10),
        14 => new lang_string('numdays', '', 14),
        20 => new lang_string('numdays', '', 20),
        30 => new lang_string('numdays', '', 30),
        60 => new lang_string('numdays', '', 60),
        90 => new lang_string('numdays', '', 90),
        120 => new lang_string('numdays', '', 120),
        180 => new lang_string('numdays', '', 180),
        365 => new lang_string('numdays', '', 365)
    )));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_skip_modif_prev', new lang_string('skipmodifprev', 'backup'), new lang_string('skipmodifprevhelp', 'backup'), 0));

    // Automated defaults section.
    $temp->add(new admin_setting_heading('automatedsettings', new lang_string('automatedsettings','backup'), ''));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_users', new lang_string('generalusers', 'backup'), new lang_string('configgeneralusers', 'backup'), 1));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_role_assignments', new lang_string('generalroleassignments','backup'), new lang_string('configgeneralroleassignments','backup'), 1));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_activities', new lang_string('generalactivities','backup'), new lang_string('configgeneralactivities','backup'), 1));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_blocks', new lang_string('generalblocks','backup'), new lang_string('configgeneralblocks','backup'), 1));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_filters', new lang_string('generalfilters','backup'), new lang_string('configgeneralfilters','backup'), 1));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_comments', new lang_string('generalcomments','backup'), new lang_string('configgeneralcomments','backup'), 1));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_userscompletion', new lang_string('generaluserscompletion','backup'), new lang_string('configgeneraluserscompletion','backup'), 1));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_logs', new lang_string('generallogs', 'backup'), new lang_string('configgenerallogs', 'backup'), 0));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_histories', new lang_string('generalhistories','backup'), new lang_string('configgeneralhistories','backup'), 0));


    //$temp->add(new admin_setting_configcheckbox('backup/backup_auto_messages', new lang_string('messages', 'message'), new lang_string('backupmessageshelp','message'), 0));
    //$temp->add(new admin_setting_configcheckbox('backup/backup_auto_blogs', new lang_string('blogs', 'blog'), new lang_string('backupblogshelp','blog'), 0));

    $ADMIN->add('backups', $temp);

} // end of speedup
