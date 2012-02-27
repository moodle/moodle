<?php

// This file defines settingpages and externalpages under the "courses" category

if ($hassiteconfig
 or has_capability('moodle/backup:backupcourse', $systemcontext)
 or has_capability('moodle/category:manage', $systemcontext)
 or has_capability('moodle/course:create', $systemcontext)
 or has_capability('moodle/site:approvecourse', $systemcontext)) { // speedup for non-admins, add all caps used on this page

    $ADMIN->add('courses', new admin_externalpage('coursemgmt', get_string('coursemgmt', 'admin'), $CFG->wwwroot . '/course/index.php?categoryedit=on',
            array('moodle/category:manage', 'moodle/course:create')));

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

    $temp->add(new admin_setting_configtext('moodlecourse/maxsections', get_string('maxnumberweeks'), get_string('maxnumberweeks_desc'), 52));

    $temp->add(new admin_settings_num_course_sections('moodlecourse/numsections', get_string('numberweeks'), get_string('coursehelpnumberweeks'), 10));

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

    if (!empty($CFG->legacyfilesinnewcourses)) {
        $choices = array('0'=>get_string('no'), '2'=>get_string('yes'));
        $temp->add(new admin_setting_configselect('moodlecourse/legacyfiles', get_string('courselegacyfiles'), get_string('courselegacyfiles_help'), key($choices), $choices));
    }

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


    $temp->add(new admin_setting_heading('language', get_string('language'), ''));
    $languages=array();
    $languages[''] = get_string('forceno');
    $languages += get_string_manager()->get_list_of_translations();
    $temp->add(new admin_setting_configselect('moodlecourse/lang', get_string('forcelanguage'), '',key($languages),$languages));

    $temp->add(new admin_setting_heading('progress', get_string('progress','completion'), ''));
    $temp->add(new admin_setting_configselect('moodlecourse/enablecompletion', get_string('completion','completion'), '',
        0, array(0 => get_string('completiondisabled','completion'), 1 => get_string('completionenabled','completion'))));

    $temp->add(new admin_setting_configcheckbox('moodlecourse/completionstartonenrol', get_string('completionstartonenrol','completion'), get_string('completionstartonenrolhelp', 'completion'), 0));
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

    // Add a category for backups
    $ADMIN->add('courses', new admin_category('backups', get_string('backups','admin')));

    // Create a page for general backup defaults
    $temp = new admin_settingpage('backupgeneralsettings', get_string('generalbackdefaults', 'backup'), 'moodle/backup:backupcourse');
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_users', get_string('generalusers','backup'), get_string('configgeneralusers','backup'), array('value'=>1, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_anonymize', get_string('generalanonymize','backup'), get_string('configgeneralanonymize','backup'), array('value'=>0, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_role_assignments', get_string('generalroleassignments','backup'), get_string('configgeneralroleassignments','backup'), array('value'=>1, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_activities', get_string('generalactivities','backup'), get_string('configgeneralactivities','backup'), array('value'=>1, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_blocks', get_string('generalblocks','backup'), get_string('configgeneralblocks','backup'), array('value'=>1, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_filters', get_string('generalfilters','backup'), get_string('configgeneralfilters','backup'), array('value'=>1, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_comments', get_string('generalcomments','backup'), get_string('configgeneralcomments','backup'), array('value'=>1, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_userscompletion', get_string('generaluserscompletion','backup'), get_string('configgeneraluserscompletion','backup'), array('value'=>1, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_logs', get_string('generallogs','backup'), get_string('configgenerallogs','backup'), array('value'=>0, 'locked'=>0)));
    $temp->add(new admin_setting_configcheckbox_with_lock('backup/backup_general_histories', get_string('generalhistories','backup'), get_string('configgeneralhistories','backup'), array('value'=>0, 'locked'=>0)));
    $ADMIN->add('backups', $temp);

/// "backups" settingpage
    $temp = new admin_settingpage('automated', get_string('automatedsetup','backup'), 'moodle/backup:backupcourse');
    $temp->add(new admin_setting_configselect('backup/backup_auto_active', get_string('active'),  get_string('autoactivedescription', 'backup'), 0, array(
        0 => get_string('autoactivedisabled', 'backup'),
        1 => get_string('autoactiveenabled', 'backup'),
        2 => get_string('autoactivemanual', 'backup')
    )));
    $temp->add(new admin_setting_special_backupdays());
    $temp->add(new admin_setting_configtime('backup/backup_auto_hour', 'backup_auto_minute', get_string('executeat'),
            get_string('backupexecuteathelp'), array('h' => 0, 'm' => 0)));
    $storageoptions = array(
        0 => get_string('storagecourseonly', 'backup'),
        1 => get_string('storageexternalonly', 'backup'),
        2 => get_string('storagecourseandexternal', 'backup')
    );
    $temp->add(new admin_setting_configselect('backup/backup_auto_storage', get_string('automatedstorage', 'backup'), get_string('automatedstoragehelp', 'backup'), 0, $storageoptions));
    $temp->add(new admin_setting_configdirectory('backup/backup_auto_destination', get_string('saveto'), get_string('backupsavetohelp'), ''));
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
    $temp->add(new admin_setting_configselect('backup/backup_auto_keep', get_string('keep'), get_string('backupkeephelp'), 1, $keepoptoins));


    $temp->add(new admin_setting_heading('automatedsettings', get_string('automatedsettings','backup'), ''));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_users', get_string('generalusers', 'backup'), get_string('configgeneralusers', 'backup'), 1));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_role_assignments', get_string('generalroleassignments','backup'), get_string('configgeneralroleassignments','backup'), 1));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_activities', get_string('generalactivities','backup'), get_string('configgeneralactivities','backup'), 1));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_blocks', get_string('generalblocks','backup'), get_string('configgeneralblocks','backup'), 1));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_filters', get_string('generalfilters','backup'), get_string('configgeneralfilters','backup'), 1));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_comments', get_string('generalcomments','backup'), get_string('configgeneralcomments','backup'), 1));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_userscompletion', get_string('generaluserscompletion','backup'), get_string('configgeneraluserscompletion','backup'), 1));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_logs', get_string('generallogs', 'backup'), get_string('configgenerallogs', 'backup'), 0));
    $temp->add(new admin_setting_configcheckbox('backup/backup_auto_histories', get_string('generalhistories','backup'), get_string('configgeneralhistories','backup'), 0));


    //$temp->add(new admin_setting_configcheckbox('backup/backup_auto_messages', get_string('messages', 'message'), get_string('backupmessageshelp','message'), 0));
    //$temp->add(new admin_setting_configcheckbox('backup/backup_auto_blogs', get_string('blogs', 'blog'), get_string('backupblogshelp','blog'), 0));

    $ADMIN->add('backups', $temp);

} // end of speedup
