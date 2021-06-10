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
* Social networking settings page file.
*
* @package    theme_fordson
* @copyright  2016 Chris Kenniburg
* 
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

// Icon Navigation);
$page = new admin_settingpage('theme_fordson_customlogin', get_string('customloginheading', 'theme_fordson'));

// This is the descriptor for icon One.
$name = 'theme_fordson/customlogininfo';
$heading = get_string('customlogininfo', 'theme_fordson');
$information = get_string('customlogininfo_desc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// Show custom login form.
$name = 'theme_fordson/showcustomlogin';
$title = get_string('showcustomlogin', 'theme_fordson');
$description = get_string('showcustomlogin_desc', 'theme_fordson');
$default = 0;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Top image.
$name = 'theme_fordson/logintopimage';
$title = get_string('logintopimage', 'theme_fordson');
$description = get_string('logintopimage_desc', 'theme_fordson');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'logintopimage');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Login form color.
$name = 'theme_fordson/fploginform';
$title = get_string('fploginform', 'theme_fordson');
$description = get_string('fploginform_desc', 'theme_fordson');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon One
$name = 'theme_fordson/loginnavicon1info';
$heading = get_string('loginnavicon1', 'theme_fordson');
$information = get_string('navicondesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// icon One
$name = 'theme_fordson/loginnav1icon';
$title = get_string('navicon', 'theme_fordson');
$description = get_string('navicondesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/loginnav1titletext';
$title = get_string('loginnavicontitletext', 'theme_fordson');
$description = get_string('loginnavicontitletextdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/loginnav1icontext';
$title = get_string('loginnavicontext', 'theme_fordson');
$description = get_string('loginnavicontextdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon One
$name = 'theme_fordson/loginnavicon2info';
$heading = get_string('loginnavicon2', 'theme_fordson');
$information = get_string('navicondesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_fordson/loginnav2icon';
$title = get_string('navicon', 'theme_fordson');
$description = get_string('navicondesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/loginnav2titletext';
$title = get_string('loginnavicontitletext', 'theme_fordson');
$description = get_string('loginnavicontitletextdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/loginnav2icontext';
$title = get_string('loginnavicontext', 'theme_fordson');
$description = get_string('loginnavicontextdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon three
$name = 'theme_fordson/loginnavicon3info';
$heading = get_string('loginnavicon3', 'theme_fordson');
$information = get_string('navicondesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_fordson/loginnav3icon';
$title = get_string('navicon', 'theme_fordson');
$description = get_string('navicondesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/loginnav3titletext';
$title = get_string('loginnavicontitletext', 'theme_fordson');
$description = get_string('loginnavicontitletextdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/loginnav3icontext';
$title = get_string('loginnavicontext', 'theme_fordson');
$description = get_string('loginnavicontextdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon four
$name = 'theme_fordson/loginnavicon4info';
$heading = get_string('loginnavicon4', 'theme_fordson');
$information = get_string('navicondesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_fordson/loginnav4icon';
$title = get_string('navicon', 'theme_fordson');
$description = get_string('navicondesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/loginnav4titletext';
$title = get_string('loginnavicontitletext', 'theme_fordson');
$description = get_string('loginnavicontitletextdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/loginnav4icontext';
$title = get_string('loginnavicontext', 'theme_fordson');
$description = get_string('loginnavicontextdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for a feature.
$name = 'theme_fordson/feature1info';
$heading = get_string('feature1info', 'theme_fordson');
$information = get_string('featureinfo_desc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);
// Feature text.
$name = 'theme_fordson/feature1text';
$title = get_string('featuretext', 'theme_fordson');
$description = get_string('featuretext_desc', 'theme_fordson');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Feature image.
$name = 'theme_fordson/feature1image';
$title = get_string('featureimage', 'theme_fordson');
$description = get_string('featureimage_desc', 'theme_fordson');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'feature1image');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for a feature.
$name = 'theme_fordson/feature2info';
$heading = get_string('feature2info', 'theme_fordson');
$information = get_string('featureinfo_desc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);
// Feature text.
$name = 'theme_fordson/feature2text';
$title = get_string('featuretext', 'theme_fordson');
$description = get_string('featuretext_desc', 'theme_fordson');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Feature image.
$name = 'theme_fordson/feature2image';
$title = get_string('featureimage', 'theme_fordson');
$description = get_string('featureimage_desc', 'theme_fordson');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'feature2image');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for a feature.
$name = 'theme_fordson/feature3info';
$heading = get_string('feature3info', 'theme_fordson');
$information = get_string('featureinfo_desc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);
// Feature text.
$name = 'theme_fordson/feature3text';
$title = get_string('featuretext', 'theme_fordson');
$description = get_string('featuretext_desc', 'theme_fordson');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Feature image.
$name = 'theme_fordson/feature3image';
$title = get_string('featureimage', 'theme_fordson');
$description = get_string('featureimage_desc', 'theme_fordson');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'feature3image');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Must add the page after definiting all the settings!
$settings->add($page);
