<?php

// This file defines everything related to frontpage

if (!during_initial_install()) { //do not use during installation
    $frontpagecontext = context_course::instance(SITEID);

    if ($hassiteconfig or has_any_capability(array(
            'moodle/course:update',
            'moodle/role:assign',
            'moodle/restore:restorecourse',
            'moodle/backup:backupcourse',
            'moodle/course:managefiles',
            'moodle/question:add',
            'moodle/question:editmine',
            'moodle/question:editall',
            'moodle/question:viewmine',
            'moodle/question:viewall',
            'moodle/question:movemine',
            'moodle/question:moveall'), $frontpagecontext)) {

        // "frontpage" settingpage
        $temp = new admin_settingpage('frontpagesettings', new lang_string('frontpagesettings','admin'), 'moodle/course:update', false, $frontpagecontext);
        $temp->add(new admin_setting_sitesettext('fullname', new lang_string('fullsitename'), '', NULL)); // no default
        $temp->add(new admin_setting_sitesettext('shortname', new lang_string('shortsitename'), '', NULL)); // no default
        $temp->add(new admin_setting_special_frontpagedesc());
        $temp->add(new admin_setting_courselist_frontpage(false)); // non-loggedin version of the setting (that's what the parameter is for :) )
        $temp->add(new admin_setting_courselist_frontpage(true)); // loggedin version of the setting

        $options = array();
        $options[] = new lang_string('unlimited');
        for ($i=1; $i<100; $i++) {
            $options[$i] = $i;
        }
        $temp->add(new admin_setting_configselect('maxcategorydepth', new lang_string('configsitemaxcategorydepth','admin'), new lang_string('configsitemaxcategorydepthhelp','admin'), 0, $options));

        $temp->add(new admin_setting_sitesetcheckbox('numsections', new lang_string('sitesection'), new lang_string('sitesectionhelp','admin'), 1));
        $temp->add(new admin_setting_sitesetselect('newsitems', new lang_string('newsitemsnumber'), '', 3,
             array('0' => '0',
                   '1' => '1',
                   '2' => '2',
                   '3' => '3',
                   '4' => '4',
                   '5' => '5',
                   '6' => '6',
                   '7' => '7',
                   '8' => '8',
                   '9' => '9',
                   '10' => '10')));
        $temp->add(new admin_setting_configtext('commentsperpage', new lang_string('commentsperpage', 'admin'), '', 15, PARAM_INT));

        // front page default role
        $options = array(0=>new lang_string('none')); // roles to choose from
        $defaultfrontpageroleid = 0;
        $roles = role_fix_names(get_all_roles(), null, ROLENAME_ORIGINALANDSHORT);
        foreach ($roles as $role) {
            if (empty($role->archetype) or $role->archetype === 'guest' or $role->archetype === 'frontpage' or $role->archetype === 'student') {
                $options[$role->id] = $role->localname;
                if ($role->archetype === 'frontpage') {
                    $defaultfrontpageroleid = $role->id;
                }
            }
        }
        if ($defaultfrontpageroleid and (!isset($CFG->defaultfrontpageroleid) or $CFG->defaultfrontpageroleid)) {
            //frotpage role may not exist in old upgraded sites
            unset($options[0]);
        }
        $temp->add(new admin_setting_configselect('defaultfrontpageroleid', new lang_string('frontpagedefaultrole', 'admin'), '', $defaultfrontpageroleid, $options));


        $ADMIN->add('frontpage', $temp);

        $ADMIN->add('frontpage', new admin_externalpage('frontpageroles', new lang_string('frontpageroles', 'admin'), "$CFG->wwwroot/$CFG->admin/roles/assign.php?contextid=" . $frontpagecontext->id, 'moodle/role:assign', false, $frontpagecontext));

        $ADMIN->add('frontpage', new admin_externalpage('frontpagefilters', new lang_string('frontpagefilters', 'admin'), "$CFG->wwwroot/filter/manage.php?contextid=" . $frontpagecontext->id, 'moodle/filter:manage', false, $frontpagecontext));

        $ADMIN->add('frontpage', new admin_externalpage('frontpagebackup', new lang_string('frontpagebackup', 'admin'), $CFG->wwwroot.'/backup/backup.php?id='.SITEID, 'moodle/backup:backupcourse', false, $frontpagecontext));

        $ADMIN->add('frontpage', new admin_externalpage('frontpagerestore', new lang_string('frontpagerestore', 'admin'), $CFG->wwwroot.'/backup/restorefile.php?contextid='.$frontpagecontext->id, 'moodle/restore:restorecourse', false, $frontpagecontext));

        $questioncapabilities = array(
                'moodle/question:add',
                'moodle/question:editmine',
                'moodle/question:editall',
                'moodle/question:viewmine',
                'moodle/question:viewall',
                'moodle/question:movemine',
                'moodle/question:moveall');
        $ADMIN->add('frontpage', new admin_externalpage('frontpagequestions', new lang_string('frontpagequestions', 'admin'), $CFG->wwwroot.'/question/edit.php?courseid='.SITEID, $questioncapabilities, false, $frontpagecontext));

        if (!empty($SITE->legacyfiles) and $SITE->legacyfiles === 2) {
            $ADMIN->add('frontpage', new admin_externalpage('sitefiles', new lang_string('sitelegacyfiles'), $CFG->wwwroot . '/files/index.php?id=' . SITEID, 'moodle/course:managefiles', false, $frontpagecontext));
        }
    }
}
