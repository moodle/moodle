<?php

/**
 * Settings for the formalwhite theme
 */

// Create our admin page
$temp = new admin_settingpage('theme_formal_white', get_string('configtitle','theme_formal_white'));

// Background colour setting
$name = 'theme_formal_white/backgroundcolor';
$title = get_string('backgroundcolor','theme_formal_white');
$description = get_string('backgroundcolordesc', 'theme_formal_white');
$default = '#F7F6F1';
$previewconfig = array('selector'=>'.block .content', 'style'=>'backgroundColor');
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
$temp->add($setting);

// Logo file setting
$name = 'theme_formal_white/logo';
$title = get_string('logo','theme_formal_white');
$description = get_string('logodesc', 'theme_formal_white');
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$temp->add($setting);

// Block region width
$name = 'theme_formal_white/regionwidth';
$title = get_string('regionwidth','theme_formal_white');
$description = get_string('regionwidthdesc', 'theme_formal_white');
$default = 200;
$choices = array(150=>'150px', 170=>'170px', 200=>'200px', 240=>'240px', 290=>'290px', 350=>'350px', 420=>'420px');
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$temp->add($setting);

// Foot note setting
$name = 'theme_formal_white/footnote';
$title = get_string('footnote','theme_formal_white');
$description = get_string('footnotedesc', 'theme_formal_white');
$setting = new admin_setting_confightmleditor($name, $title, $description, '');
$temp->add($setting);

// Custom CSS file
$name = 'theme_formal_white/customcss';
$title = get_string('customcss','theme_formal_white');
$description = get_string('customcssdesc', 'theme_formal_white');
$setting = new admin_setting_configtextarea($name, $title, $description, '');
$temp->add($setting);

// Add our page to the structure of the admin tree
$ADMIN->add('themes', $temp);