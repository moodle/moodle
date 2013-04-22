<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // link color setting
    $name = 'theme_fusion/linkcolor';
    $title = get_string('linkcolor','theme_fusion');
    $description = get_string('linkcolordesc', 'theme_fusion');
    $default = '#2d83d5';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Tag line setting
    $name = 'theme_fusion/tagline';
    $title = get_string('tagline','theme_fusion');
    $description = get_string('taglinedesc', 'theme_fusion');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Foot note setting
    $name = 'theme_fusion/footertext';
    $title = get_string('footertext','theme_fusion');
    $description = get_string('footertextdesc', 'theme_fusion');
    $setting = new admin_setting_confightmleditor($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Custom CSS file
    $name = 'theme_fusion/customcss';
    $title = get_string('customcss','theme_fusion');
    $description = get_string('customcssdesc', 'theme_fusion');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

}
