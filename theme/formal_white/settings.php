<?php

/**
 * Settings for the formal_white theme
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    // font size reference
    $name = 'theme_formal_white/fontsizereference';
    $title = get_string('fontsizereference','theme_formal_white');
    $description = get_string('fontsizereferencedesc', 'theme_formal_white');
    $default = '13';
    $choices = array(11=>'11px', 12=>'12px', 13=>'13px', 14=>'14px', 15=>'15px', 16=>'16px');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $settings->add($setting);

    // moodle 1.* like setting
    $name = 'theme_formal_white/noframe';
    $title = get_string('noframe','theme_formal_white');
    $description = get_string('noframedesc', 'theme_formal_white');
    $default = '0';
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $settings->add($setting);

    // Display logo or heading
    $name = 'theme_formal_white/displaylogo';
    $title = get_string('displaylogo','theme_formal_white');
    $description = get_string('displaylogodesc', 'theme_formal_white');
    $default = '1';
    $choices = array(1=>get_string('moodlelogo', 'theme_formal_white'),0=>get_string('heading', 'theme_formal_white'));
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $settings->add($setting);

    // Logo file setting
    $name = 'theme_formal_white/logo';
    $title = get_string('logo','theme_formal_white');
    $description = get_string('logodesc', 'theme_formal_white');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $settings->add($setting);

    // page header background colour setting
    $name = 'theme_formal_white/headerbgc';
    $title = get_string('headerbgc','theme_formal_white');
    $description = get_string('headerbgcdesc', 'theme_formal_white');
    $default = '#E3DFD4';
    $previewconfig = array('selector'=>'#page-header', 'style'=>'backgroundColor');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $settings->add($setting);

    // Block content background colour setting
    $name = 'theme_formal_white/blockcontentbgc';
    $title = get_string('blockcontentbgc','theme_formal_white');
    $description = get_string('blockcontentbgcdesc', 'theme_formal_white');
    $default = '#F6F6F6';
    $previewconfig = array('selector'=>'.block .content', 'style'=>'backgroundColor');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $settings->add($setting);

    // Left column colour setting
    $name = 'theme_formal_white/lblockcolumnbgc';
    $title = get_string('lblockcolumnbgc','theme_formal_white');
    $description = get_string('lblockcolumnbgcdesc', 'theme_formal_white');
    $default = '#E3DFD4';
    $previewconfig = array('selector'=>'#page-content, #page-content #region-pre, #page-content #region-post-box', 'style'=>'backgroundColor');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $settings->add($setting);

    // Right column colour setting
    $name = 'theme_formal_white/rblockcolumnbgc';
    $title = get_string('rblockcolumnbgc','theme_formal_white');
    $description = get_string('rblockcolumnbgcdesc', 'theme_formal_white');
    $default = '';
    $previewconfig = array('selector'=>'#page-content #region-post-box, #page-content #region-post', 'style'=>'backgroundColor');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $settings->add($setting);

    // Block region width
    $name = 'theme_formal_white/blockcolumnwidth';
    $title = get_string('blockcolumnwidth','theme_formal_white');
    $description = get_string('blockcolumnwidthdesc', 'theme_formal_white');
    $default = '200';
    $choices = array(150=>'150px', 170=>'170px', 200=>'200px', 240=>'240px', 290=>'290px', 350=>'350px', 420=>'420px');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $settings->add($setting);

    // Foot note setting
    $name = 'theme_formal_white/footnote';
    $title = get_string('footnote','theme_formal_white');
    $description = get_string('footnotedesc', 'theme_formal_white');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $settings->add($setting);

    // Custom CSS file
    $name = 'theme_formal_white/customcss';
    $title = get_string('customcss','theme_formal_white');
    $description = get_string('customcssdesc', 'theme_formal_white');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $settings->add($setting);
}