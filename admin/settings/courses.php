<?php // $Id$

// This file defines settingpages and externalpages under the "courses" category

if ($hassiteconfig
 or has_capability('moodle/site:backup', $systemcontext)
 or has_capability('moodle/category:manage', $systemcontext)
 or has_capability('moodle/course:create', $systemcontext)
 or has_capability('moodle/site:approvecourse', $systemcontext)) { // speedup for non-admins, add all caps used on this page

    $ADMIN->add('courses', new admin_externalpage('coursemgmt', get_string('coursemgmt', 'admin'), $CFG->wwwroot . '/course/index.php?categoryedit=on',
            array('moodle/category:manage', 'moodle/course:create')));

    $ADMIN->add('courses', new admin_enrolment_page());

/// Course Default Settings Page
/// NOTE: these settings must be applied after all other settings because they depend on them
/// NOTE: these are a subset of the complete defaults available in 2.0
    ///general course settings
    $temp = new admin_settingpage('coursesettings', get_string('coursesettings'));
    $courseformats = get_list_of_plugins('course/format');
    $formcourseformats = array();
    foreach ($courseformats as $courseformat) {
        $formcourseformats["$courseformat"] = get_string("format$courseformat","format_$courseformat");
        if ($formcourseformats["$courseformat"]=="[[format$courseformat]]") {
            $formcourseformats["$courseformat"] = get_string("format$courseformat");
        }
    }
    $temp->add(new admin_setting_configselect('moodlecourse/format', get_string('format'), get_string('coursehelpformat'), 'weeks',$formcourseformats));
    for ($i=1; $i<=52; $i++) {
        $sectionmenu[$i] = "$i";
    }
    $temp->add(new admin_setting_configselect('moodlecourse/numsections', get_string('numberweeks'), get_string('coursehelpnumberweeks'), 10, $sectionmenu));
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

    $ADMIN->add('courses', $temp);

    // "courserequests" settingpage
    $temp = new admin_settingpage('courserequest', get_string('courserequest'));
    $temp->add(new admin_setting_configcheckbox('enablecourserequests', get_string('enablecourserequests', 'admin'), get_string('configenablecourserequests', 'admin'), 0));
    $temp->add(new admin_settings_coursecat_select('defaultrequestcategory', get_string('defaultrequestcategory', 'admin'), get_string('configdefaultrequestcategory', 'admin'), 1));
    $temp->add(new admin_setting_users_with_capability('courserequestnotify', get_string('courserequestnotify', 'admin'), get_string('configcourserequestnotify2', 'admin'), array(), 'moodle/site:approvecourse'));
    $ADMIN->add('courses', $temp);

    // Pending course requests.
    if (!empty($CFG->enablecourserequests)) {
        $ADMIN->add('courses', new admin_externalpage('coursespending', get_string('pendingrequests'),
                $CFG->wwwroot . '/course/pending.php', array('moodle/site:approvecourse')));
    }

    // "backups" settingpage
    if (!empty($CFG->backup_version)) {
        $bi = array();
        $bi[] = new admin_setting_configcheckbox('backup_sche_modules', get_string('includemodules'), get_string('backupincludemoduleshelp'), 0);
        $bi[] = new admin_setting_configcheckbox('backup_sche_withuserdata', get_string('includemoduleuserdata'), get_string('backupincludemoduleuserdatahelp'), 0);
        $bi[] = new admin_setting_configcheckbox('backup_sche_metacourse', get_string('metacourse'), get_string('backupmetacoursehelp'), 0);
        $bi[] = new admin_setting_configselect('backup_sche_users', get_string('users'), get_string('backupusershelp'),
                                               0, array(0 => get_string('all'), 1 => get_string('course')));
        $bi[] = new admin_setting_configcheckbox('backup_sche_logs', get_string('logs'), get_string('backuplogshelp'), 0);
        $bi[] = new admin_setting_configcheckbox('backup_sche_userfiles', get_string('userfiles'), get_string('backupuserfileshelp'), 0);
        $bi[] = new admin_setting_configcheckbox('backup_sche_coursefiles', get_string('coursefiles'), get_string('backupcoursefileshelp'), 0);
        $bi[] = new admin_setting_configcheckbox('backup_sche_sitefiles', get_string('sitefiles'), get_string('backupsitefileshelp'), 0);
        $bi[] = new admin_setting_configcheckbox('backup_sche_gradebook_history', get_string('gradebookhistories', 'grades'), get_string('backupgradebookhistoryhelp'), 0);
        $bi[] = new admin_setting_configcheckbox('backup_sche_messages', get_string('messages', 'message'), get_string('backupmessageshelp','message'), 0);
        $bi[] = new admin_setting_configcheckbox('backup_sche_blogs', get_string('blogs', 'blog'), get_string('backupblogshelp','blog'), 0);
        $bi[] = new admin_setting_configselect('backup_sche_keep', get_string('keep'),
                                               get_string('backupkeephelp'), 1, array(0 => get_string('all'), 1 => '1',
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
                                                                                                              500 => '500'));
        $bi[] = new admin_setting_configcheckbox('backup_sche_active', get_string('active'), get_string('backupactivehelp'), 0);
        $bi[] = new admin_setting_special_backupdays();
        $bi[] = new admin_setting_configtime('backup_sche_hour', 'backup_sche_minute', get_string('executeat'),
                                             get_string('backupexecuteathelp'), array('h' => 0, 'm' => 0));
        $bi[] = new admin_setting_configdirectory('backup_sche_destination', get_string('saveto'), get_string('backupsavetohelp'), '');

        $temp = new admin_settingpage('backups', get_string('backups','admin'), 'moodle/site:backup');
        foreach ($bi as $backupitem) {
            $backupitem->plugin = 'backup';
            $temp->add($backupitem);
        }
        $ADMIN->add('courses', $temp);
    }

} // end of speedup

?>
