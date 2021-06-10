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
* @credits    theme_boost - MoodleHQ
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

/* Social Network Settings */
$page = new admin_settingpage('theme_fordson_footer', get_string('footerheading', 'theme_fordson'));
$page->add(new admin_setting_heading('theme_fordson_footer', get_string('footerheadingsub', 'theme_fordson'), format_text(get_string('footerdesc' , 'theme_fordson'), FORMAT_MARKDOWN)));

// footer branding
$name = 'theme_fordson/brandorganization';
$title = get_string('brandorganization', 'theme_fordson');
$description = get_string('brandorganizationdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// footer branding
$name = 'theme_fordson/brandwebsite';
$title = get_string('brandwebsite', 'theme_fordson');
$description = get_string('brandwebsitedesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// footer branding
$name = 'theme_fordson/brandphone';
$title = get_string('brandphone', 'theme_fordson');
$description = get_string('brandphonedesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// footer branding
$name = 'theme_fordson/brandemail';
$title = get_string('brandemail', 'theme_fordson');
$description = get_string('brandemaildesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Footnote setting.
$name = 'theme_fordson/footnote';
$title = get_string('footnote', 'theme_fordson');
$description = get_string('footnotedesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);


// This is the descriptor for socialicons
$name = 'theme_fordson/socialiconsinfo';
$heading = get_string('footerheadingsocial', 'theme_fordson');
$information = get_string('footerdesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// Website url setting.
$name = 'theme_fordson/website';
$title = get_string('website', 'theme_fordson');
$description = get_string('websitedesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Blog url setting.
$name = 'theme_fordson/blog';
$title = get_string('blog', 'theme_fordson');
$description = get_string('blogdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Facebook url setting.
$name = 'theme_fordson/facebook';
$title = get_string(        'facebook', 'theme_fordson');
$description = get_string(      'facebookdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Flickr url setting.
$name = 'theme_fordson/flickr';
$title = get_string('flickr', 'theme_fordson');
$description = get_string('flickrdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Twitter url setting.
$name = 'theme_fordson/twitter';
$title = get_string('twitter', 'theme_fordson');
$description = get_string('twitterdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Google+ url setting.
$name = 'theme_fordson/googleplus';
$title = get_string('googleplus', 'theme_fordson');
$description = get_string('googleplusdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// LinkedIn url setting.
$name = 'theme_fordson/linkedin';
$title = get_string('linkedin', 'theme_fordson');
$description = get_string('linkedindesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Tumblr url setting.
$name = 'theme_fordson/tumblr';
$title = get_string('tumblr', 'theme_fordson');
$description = get_string('tumblrdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Pinterest url setting.
$name = 'theme_fordson/pinterest';
$title = get_string('pinterest', 'theme_fordson');
$description = get_string('pinterestdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Instagram url setting.
$name = 'theme_fordson/instagram';
$title = get_string('instagram', 'theme_fordson');
$description = get_string('instagramdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// YouTube url setting.
$name = 'theme_fordson/youtube';
$title = get_string('youtube', 'theme_fordson');
$description = get_string('youtubedesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Vimeo url setting.
$name = 'theme_fordson/vimeo';
$title = get_string('vimeo', 'theme_fordson');
$description = get_string('vimeodesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Skype url setting.
$name = 'theme_fordson/skype';
$title = get_string('skype', 'theme_fordson');
$description = get_string('skypedesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// General social url setting 1.
$name = 'theme_fordson/social1';
$title = get_string('sociallink', 'theme_fordson');
$description = get_string('sociallinkdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Social icon setting 1.
$name = 'theme_fordson/socialicon1';
$title = get_string('sociallinkicon', 'theme_fordson');
$description = get_string('sociallinkicondesc', 'theme_fordson');
$default = 'home';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// General social url setting 2.
$name = 'theme_fordson/social2';
$title = get_string('sociallink', 'theme_fordson');
$description = get_string('sociallinkdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Social icon setting 2.
$name = 'theme_fordson/socialicon2';
$title = get_string('sociallinkicon', 'theme_fordson');
$description = get_string('sociallinkicondesc', 'theme_fordson');
$default = 'home';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// General social url setting 3.
$name = 'theme_fordson/social3';
$title = get_string('sociallink', 'theme_fordson');
$description = get_string('sociallinkdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Social icon setting 3.
$name = 'theme_fordson/socialicon3';
$title = get_string('sociallinkicon', 'theme_fordson');
$description = get_string('sociallinkicondesc', 'theme_fordson');
$default = 'home';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// Must add the page after definiting all the settings!
$settings->add($page);
