<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('block_online_users_timetosee', get_string('timetosee', 'block_online_users'),
                   get_string('configtimetosee', 'block_online_users'), 5, PARAM_INT));
}

