<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $options = array('all'=>get_string('allcourses', 'block_course_list'), 'own'=>get_string('owncourses', 'block_course_list'));

    $settings->add(new admin_setting_configselect('block_course_list_adminview', get_string('adminview', 'block_course_list'),
                       get_string('configadminview', 'block_course_list'), 'all', $options));

    $settings->add(new admin_setting_configcheckbox('block_course_list_hideallcourseslink', get_string('hideallcourseslink', 'block_course_list'),
                       get_string('confighideallcourseslink', 'block_course_list'), 0));
}


