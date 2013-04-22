<?php

/**
 * Settings for the arialist theme
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    // Logo file setting
    $name = 'theme_arialist/logo';
    $title = get_string('logo','theme_arialist');
    $description = get_string('logodesc', 'theme_arialist');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Tagline setting
    $name = 'theme_arialist/tagline';
    $title = get_string('tagline','theme_arialist');
    $description = get_string('taglinedesc', 'theme_arialist');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Link colour setting
    $name = 'theme_arialist/linkcolor';
    $title = get_string('linkcolor','theme_arialist');
    $description = get_string('linkcolordesc', 'theme_arialist');
    $default = '#f25f0f';
    $previewconfig = array('selector'=>'.block .content', 'style'=>'linkcolor');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Block region width
    $name = 'theme_arialist/regionwidth';
    $title = get_string('regionwidth','theme_arialist');
    $description = get_string('regionwidthdesc', 'theme_arialist');
    $default = 250;
    $choices = array(180=>'180px', 190=>'190px', 200=>'200px', 210=>'210px', 220=>'220px', 240=>'240px', 250=>'250px');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Custom CSS file
    $name = 'theme_arialist/customcss';
    $title = get_string('customcss','theme_arialist');
    $description = get_string('customcssdesc', 'theme_arialist');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}