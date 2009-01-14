<?php // $Id$

// This file defines everything related to frontpage

if (get_site()) { //do not use during installation
    $frontpagecontext = get_context_instance(CONTEXT_COURSE, SITEID);

    if ($hassiteconfig or has_any_capability(array(
            'moodle/course:update',
            'moodle/role:assign',
            'moodle/site:restore',
            'moodle/site:backup',
            'moodle/course:managefiles',
            'moodle/question:add',
            'moodle/question:editmine',
            'moodle/question:editall',
            'moodle/question:viewmine',
            'moodle/question:viewall',
            'moodle/question:movemine',
            'moodle/question:moveall'), $frontpagecontext)) {

        // "frontpage" settingpage
        $temp = new admin_settingpage('frontpagesettings', get_string('frontpagesettings','admin'), 'moodle/course:update', false, $frontpagecontext);
        $temp->add(new admin_setting_sitesettext('fullname', get_string('fullsitename'), '', NULL)); // no default
        $temp->add(new admin_setting_sitesettext('shortname', get_string('shortsitename'), '', NULL)); // no default
        $temp->add(new admin_setting_special_frontpagedesc());
        $temp->add(new admin_setting_courselist_frontpage(false)); // non-loggedin version of the setting (that's what the parameter is for :) )
        $temp->add(new admin_setting_courselist_frontpage(true)); // loggedin version of the setting
        
        $options = array();
        $options[] = get_string('unlimited');
        for ($i=1; $i<100; $i++) {
            $options[$i] = $i;
        }
        $temp->add(new admin_setting_configselect('maxcategorydepth', get_string('configsitemaxcategorydepth','admin'), get_string('configsitemaxcategorydepthhelp','admin'), 0, $options));
        
        $temp->add(new admin_setting_sitesetcheckbox('numsections', get_string('sitesection'), get_string('sitesectionhelp','admin'), 1));
        $temp->add(new admin_setting_sitesetselect('newsitems', get_string('newsitemsnumber'), '', 3,
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
        $temp->add(new admin_setting_configtext('coursesperpage', get_string('coursesperpage', 'admin'), get_string('configcoursesperpage', 'admin'), 20, PARAM_INT));
        $temp->add(new admin_setting_configcheckbox('allowvisiblecoursesinhiddencategories', get_string('allowvisiblecoursesinhiddencategories', 'admin'), get_string('configvisiblecourses', 'admin'), 0));

        // front page default role
        $roleoptions = array(0=>get_string('none')); // roles to choose from
        if ($roles = get_all_roles()) {
            foreach ($roles as $role) {
                $roleoptions[$role->id] = strip_tags(format_string($role->name, true));
            }
        }
        $temp->add(new admin_setting_configselect('defaultfrontpageroleid', get_string('frontpagedefaultrole', 'admin'), '', 0, $roleoptions));

        $ADMIN->add('frontpage', $temp);

        $ADMIN->add('frontpage', new admin_externalpage('frontpageroles', get_string('frontpageroles', 'admin'), "$CFG->wwwroot/$CFG->admin/roles/assign.php?contextid=" . $frontpagecontext->id, 'moodle/role:assign', false, $frontpagecontext));

        $ADMIN->add('frontpage', new admin_externalpage('frontpagebackup', get_string('frontpagebackup', 'admin'), $CFG->wwwroot.'/backup/backup.php?id='.SITEID, 'moodle/site:backup', false, $frontpagecontext));

        $ADMIN->add('frontpage', new admin_externalpage('frontpagerestore', get_string('frontpagerestore', 'admin'), $CFG->wwwroot.'/files/index.php?id='.SITEID.'&amp;wdir=/backupdata', 'moodle/site:restore', false, $frontpagecontext));

        $questioncapabilites = array(
                'moodle/question:add',
                'moodle/question:editmine',
                'moodle/question:editall',
                'moodle/question:viewmine',
                'moodle/question:viewall',
                'moodle/question:movemine',
                'moodle/question:moveall');
        $ADMIN->add('frontpage', new admin_externalpage('frontpagequestions', get_string('frontpagequestions', 'admin'), $CFG->wwwroot.'/question/edit.php?courseid='.SITEID, $questioncapabilites, false, $frontpagecontext));

        $ADMIN->add('frontpage', new admin_externalpage('sitefiles', get_string('sitefiles'), $CFG->wwwroot . '/files/index.php?id=' . SITEID, 'moodle/course:managefiles', false, $frontpagecontext));
    }
}
?>
