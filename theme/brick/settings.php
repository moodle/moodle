<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

// Background image setting
// logo image setting
$name = 'theme_brick/logo';
$title = get_string('logo','theme_brick');
$description = get_string('logodesc', 'theme_brick');
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$settings->add($setting);

// link color setting
$name = 'theme_brick/linkcolor';
$title = get_string('linkcolor','theme_brick');
$description = get_string('linkcolordesc', 'theme_brick');
$default = '#06365b';
$previewconfig = NULL;
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
$settings->add($setting);

// link hover color setting
$name = 'theme_brick/linkhover';
$title = get_string('linkhover','theme_brick');
$description = get_string('linkhoverdesc', 'theme_brick');
$default = '#5487ad';
$previewconfig = NULL;
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
$settings->add($setting);

// main color setting
$name = 'theme_brick/maincolor';
$title = get_string('maincolor','theme_brick');
$description = get_string('maincolordesc', 'theme_brick');
$default = '#8e2800';
$previewconfig = NULL;
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
$settings->add($setting);

// main color accent setting
$name = 'theme_brick/maincolorlink';
$title = get_string('maincolorlink','theme_brick');
$description = get_string('maincolorlinkdesc', 'theme_brick');
$default = '#fff0a5';
$previewconfig = NULL;
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
$settings->add($setting);

// heading color setting
$name = 'theme_brick/headingcolor';
$title = get_string('headingcolor','theme_brick');
$description = get_string('headingcolordesc', 'theme_brick');
$default = '#5c3500';
$previewconfig = NULL;
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
$settings->add($setting);

}