<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Tagline setting
    $name = 'theme_anomaly/tagline';
    $title = get_string('tagline','theme_anomaly');
    $description = get_string('taglinedesc', 'theme_anomaly');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Custom CSS file
    $name = 'theme_anomaly/customcss';
    $title = get_string('customcss','theme_anomaly');
    $description = get_string('customcssdesc', 'theme_anomaly');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

}
