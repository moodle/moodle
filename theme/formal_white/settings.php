<?php

/**
 * Settings for the formalwhite theme
 */

// Create our admin page
$temp = new admin_settingpage('theme_formalwhite', get_string('configtitle','theme_formalwhite'));

// Background colour setting
$name = 'theme_formalwhite/backgroundcolor';
$title = get_string('backgroundcolor','theme_formalwhite');
$description = get_string('backgroundcolordesc', 'theme_formalwhite');
$default = '#F7F6F1';
$previewconfig = array('selector'=>'.block .content', 'style'=>'backgroundColor');
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
$temp->add($setting);

// Logo file setting
$name = 'theme_formalwhite/logo';
$title = get_string('logo','theme_formalwhite');
$description = get_string('logodesc', 'theme_formalwhite');
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$temp->add($setting);

// Block region width
$name = 'theme_formalwhite/regionwidth';
$title = get_string('regionwidth','theme_formalwhite');
$description = get_string('regionwidthdesc', 'theme_formalwhite');
$default = 200;
$choices = array(150=>'150px', 170=>'170px', 200=>'200px', 240=>'240px', 290=>'290px', 350=>'350px', 420=>'420px');
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$temp->add($setting);

// Foot note setting
$name = 'theme_formalwhite/footnote';
$title = get_string('footnote','theme_formalwhite');
$description = get_string('footnotedesc', 'theme_formalwhite');
$setting = new admin_setting_confightmleditor($name, $title, $description, '');
$temp->add($setting);

// Custom CSS file
$name = 'theme_formalwhite/customcss';
$title = get_string('customcss','theme_formalwhite');
$description = get_string('customcssdesc', 'theme_formalwhite');
$setting = new admin_setting_configtextarea($name, $title, $description, '');
$temp->add($setting);

// Add our page to the structure of the admin tree
$ADMIN->add('themes', $temp);