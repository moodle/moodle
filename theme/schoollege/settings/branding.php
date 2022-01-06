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
 * @package   theme_schoollege
 * @copyright 2020 Chris Kenniburg
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

    // Advanced settings.
    $page = new admin_settingpage('theme_schoollege_branding', get_string('brandingsettings', 'theme_schoollege'));

    // This is the descriptor for the page.
    $name = 'theme_schoollege/brandinginfo';
    $heading = get_string('brandinginfo', 'theme_schoollege');
    $information = get_string('brandinginfo_desc', 'theme_schoollege');
    $setting = new admin_setting_heading($name, $heading, $information);
    $page->add($setting);

    $name = 'theme_schoollege/brandlogo';
    $title = get_string('brandlogo', 'theme_schoollege');
    $description = get_string('brandlogo_desc', 'theme_schoollege');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'brandlogo');
    $setting->set_updatedcallback('theme_schoollege_update_settings_images');
    $page->add($setting);

    // footer branding
    $name = 'theme_schoollege/brandorganization';
    $title = get_string('brandorganization', 'theme_schoollege');
    $description = get_string('brandorganizationdesc', 'theme_schoollege');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // footer branding
    $name = 'theme_schoollege/brandwebsite';
    $title = get_string('brandwebsite', 'theme_schoollege');
    $description = get_string('brandwebsitedesc', 'theme_schoollege');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // footer branding
    $name = 'theme_schoollege/brandphone';
    $title = get_string('brandphone', 'theme_schoollege');
    $description = get_string('brandphonedesc', 'theme_schoollege');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // footer branding
    $name = 'theme_schoollege/brandemail';
    $title = get_string('brandemail', 'theme_schoollege');
    $description = get_string('brandemaildesc', 'theme_schoollege');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Footnote setting.
    $name = 'theme_schoollege/footnote';
    $title = get_string('footnote', 'theme_schoollege');
    $description = get_string('footnotedesc', 'theme_schoollege');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);


    $settings->add($page);
    