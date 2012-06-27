<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $configs = array();
    $configs[] = new admin_setting_configcheckbox('showchildren', get_string('showchildren', 'block_course_overview'),
                       get_string('showchildren_desc', 'block_course_overview'), '');
    $configs[] = new admin_setting_configcheckbox('showwelcomearea', get_string('showwelcomearea', 'block_course_overview'),
                       get_string('showwelcomearea_desc', 'block_course_overview'), 1);

    foreach ($configs as $config) {
        $config->plugin = 'block_course_overview';
        $settings->add($config);
    }
}
