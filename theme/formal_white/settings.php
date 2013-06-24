<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Moodle's formal_white theme
 *
 * DO NOT MODIFY THIS THEME!
 * COPY IT FIRST, THEN RENAME THE COPY AND MODIFY IT INSTEAD.
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package   theme_formal_white
 * @copyright 2013 Mediatouch 2000, mediatouch.it
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    // font size reference
    $name = 'theme_formal_white/fontsizereference';
    $title = get_string('fontsizereference', 'theme_formal_white');
    $description = get_string('fontsizereferencedesc', 'theme_formal_white');
    $default = '13';
    $choices = array(11=>'11px', 12=>'12px', 13=>'13px', 14=>'14px', 15=>'15px', 16=>'16px');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // moodle 1.* like setting
    $name = 'theme_formal_white/noframe';
    $title = get_string('noframe', 'theme_formal_white');
    $description = get_string('noframedesc', 'theme_formal_white');
    $default = '0';
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Frame margin
    $name = 'theme_formal_white/framemargin';
    $title = get_string('framemargin', 'theme_formal_white');
    $description = get_string('framemargindesc', 'theme_formal_white', get_string('noframe', 'theme_formal_white'));
    $default = '15';
    $choices = array(0=>'0px', 5=>'5px', 10=>'10px', 15=>'15px', 20=>'20px', 25=>'25px', 30=>'30px', 35=>'35px', 40=>'40px', 45=>'45px', 50=>'50px');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Display logo or heading
    $name = 'theme_formal_white/headercontent';
    $title = get_string('headercontent', 'theme_formal_white');
    $description = get_string('headercontentdesc', 'theme_formal_white');
    $default = '1';
    $choices = array(1=>get_string('displaylogo', 'theme_formal_white'), 0=>get_string('displayheading', 'theme_formal_white'));
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Trend colour settings
    $name = 'theme_formal_white/trendcolor';
    $title = get_string('trendcolor', 'theme_formal_white');
    $description = get_string('trendcolordesc', 'theme_formal_white');
    $default = 'mink';
    $trends = get_directory_list($CFG->dirroot.'/theme/formal_white/pix/trend/', '', false, true, false);
    $choices = array();
    foreach ($trends as $trend) {
        $choices[$trend] = get_string($trend, 'theme_formal_white');
    }
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Custom site logo setting
    $name = 'theme_formal_white/customlogourl';
    $title = get_string('customlogourl', 'theme_formal_white');
    $description = get_string('customlogourldesc', 'theme_formal_white');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'customlogourl');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Custom front page site logo setting
    $name = 'theme_formal_white/frontpagelogourl';
    $title = get_string('frontpagelogourl', 'theme_formal_white');
    $description = get_string('frontpagelogourldesc', 'theme_formal_white');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'frontpagelogourl');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // page header background colour setting
    $name = 'theme_formal_white/headerbgc';
    $title = get_string('headerbgc', 'theme_formal_white');
    $description = get_string('headerbgcdesc', 'theme_formal_white');
    $default = '#E3DFD4';
    $previewconfig = array('selector'=>'#page-header', 'style'=>'backgroundColor');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // creditstomoodleorg: ctmo
    $name = 'theme_formal_white/creditstomoodleorg';
    $title = get_string('creditstomoodleorg', 'theme_formal_white');
    $description = get_string('creditstomoodleorgdesc', 'theme_formal_white');
    $default = '2';
    $choices = array(2 => get_string('ctmo_ineverypage', 'theme_formal_white'), 1 => get_string('ctmo_onfrontpageonly', 'theme_formal_white'), 0 => get_string('ctmo_no', 'theme_formal_white'));
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Block region width
    $name = 'theme_formal_white/blockcolumnwidth';
    $title = get_string('blockcolumnwidth', 'theme_formal_white');
    $description = get_string('blockcolumnwidthdesc', 'theme_formal_white');
    $default = '200';
    $choices = array(150=>'150px', 170=>'170px', 200=>'200px', 240=>'240px', 290=>'290px', 350=>'350px', 420=>'420px');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Block padding
    $name = 'theme_formal_white/blockpadding';
    $title = get_string('blockpadding', 'theme_formal_white');
    $description = get_string('blockpaddingdesc', 'theme_formal_white');
    $default = '8';
    $choices = array(1=>'1px', 2=>'2px', 4=>'4px', 8=>'8px', 12=>'12px', 16=>'16px');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Block content background colour setting
    $name = 'theme_formal_white/blockcontentbgc';
    $title = get_string('blockcontentbgc', 'theme_formal_white');
    $description = get_string('blockcontentbgcdesc', 'theme_formal_white');
    $default = '#F6F6F6';
    $previewconfig = array('selector'=>'.block .content', 'style'=>'backgroundColor');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Left column colour setting
    $name = 'theme_formal_white/lblockcolumnbgc';
    $title = get_string('lblockcolumnbgc', 'theme_formal_white');
    $description = get_string('lblockcolumnbgcdesc', 'theme_formal_white');
    $default = '#E3DFD4';
    $previewconfig = array('selector'=>'#page-content, #page-content #region-pre, #page-content #region-post-box', 'style'=>'backgroundColor');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Right column colour setting
    $name = 'theme_formal_white/rblockcolumnbgc';
    $title = get_string('rblockcolumnbgc', 'theme_formal_white');
    $description = get_string('rblockcolumnbgcdesc', 'theme_formal_white');
    $default = '';
    $previewconfig = array('selector'=>'#page-content #region-post-box, #page-content #region-post', 'style'=>'backgroundColor');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Foot note setting
    $name = 'theme_formal_white/footnote';
    $title = get_string('footnote', 'theme_formal_white');
    $description = get_string('footnotedesc', 'theme_formal_white');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Custom CSS file
    $name = 'theme_formal_white/customcss';
    $title = get_string('customcss', 'theme_formal_white');
    $description = get_string('customcssdesc', 'theme_formal_white');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}
