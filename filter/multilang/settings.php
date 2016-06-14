<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configcheckbox('filter_multilang_force_old', 'filter_multilang_force_old',
                       get_string('multilangforceold', 'admin'), 0));
}
