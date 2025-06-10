<?php

defined('MOODLE_INTERNAL') or die();

if ($ADMIN->fulltree) {
    $mods = get_plugin_list('mod');
    $active_mods = array();
    foreach ($mods as $key => $mod) {
        $active_mods[$key] = get_string('pluginname', 'mod_' . $key);
    }

    $settings->add(new admin_setting_configmultiselect(
        'grade_builder/acceptable_mods',
        get_string('acceptable_mods', 'gradereport_gradebook_builder'),
        get_string('acceptable_mods_help', 'gradereport_gradebook_builder'),
        array('quiz'), $active_mods
    ));
}
