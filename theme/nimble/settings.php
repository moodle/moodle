<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {


// Tagline setting
    $name = 'theme_nimble/tagline';
    $title = get_string('tagline','theme_nimble');
    $description = get_string('taglinedesc', 'theme_nimble');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $settings->add($setting);
    
    // footerline setting
    $name = 'theme_nimble/footerline';
    $title = get_string('footerline','theme_nimble');
    $description = get_string('footerlinedesc', 'theme_nimble');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $settings->add($setting);


	// Background color setting
	$name = 'theme_nimble/backgroundcolor';
	$title = get_string('backgroundcolor','theme_nimble');
	$description = get_string('backgroundcolordesc', 'theme_nimble');
	$default = '#454545';
	$previewconfig = NULL;
	$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
	$settings->add($setting);

	// link color setting
	$name = 'theme_nimble/linkcolor';
	$title = get_string('linkcolor','theme_nimble');
	$description = get_string('linkcolordesc', 'theme_nimble');
	$default = '#2a65b1';
	$previewconfig = NULL;
	$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
	$settings->add($setting);

	// link hover color setting
	$name = 'theme_nimble/linkhover';
	$title = get_string('linkhover','theme_nimble');
	$description = get_string('linkhoverdesc', 'theme_nimble');
	$default = '#222222';
	$previewconfig = NULL;
	$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
	$settings->add($setting);



}