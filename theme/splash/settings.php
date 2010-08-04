<?php
 
/**
 * Settings for the splash theme
 */
 
// Create our admin page
$temp = new admin_settingpage('theme_splash', get_string('configtitle','theme_splash'));
 
// Logo file setting
$name = 'theme_splash/logo';
$title = get_string('logo','theme_splash');
$description = get_string('logodesc', 'theme_splash');
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$temp->add($setting);

// Tagline setting
$name = 'theme_splash/tagline';
$title = get_string('tagline','theme_splash');
$description = get_string('taglinedesc', 'theme_splash');
$setting = new admin_setting_configtextarea($name, $title, $description, 'Virtual Learning Center');
$temp->add($setting);

$name = 'theme_splash/hide_tagline';
$title = get_string('hide_tagline','theme_splash');
$description = get_string('hide_taglinedesc', 'theme_splash');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$temp->add($setting);

 /*
// Block region width
$name = 'theme_splash/regionwidth';
$title = get_string('regionwidth','theme_splash');
$description = get_string('regionwidthdesc', 'theme_splash');
$default = 240;
$choices = array(200=>'200px', 240=>'240px', 290=>'290px', 350=>'350px', 420=>'420px');
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$temp->add($setting); */
 
// Foot note setting
$name = 'theme_splash/footnote';
$title = get_string('footnote','theme_splash');
$description = get_string('footnotedesc', 'theme_splash');
$setting = new admin_setting_confightmleditor($name, $title, $description, '');
$temp->add($setting);

// Custom CSS file
$name = 'theme_splash/customcss';
$title = get_string('customcss','theme_splash');
$description = get_string('customcssdesc', 'theme_splash');
$setting = new admin_setting_configtextarea($name, $title, $description, '');
$temp->add($setting);
 
// Add our page to the structure of the admin tree
$ADMIN->add('themes', $temp);
?>