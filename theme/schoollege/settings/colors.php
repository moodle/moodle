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
    $page = new admin_settingpage('theme_schoollege_advanced', get_string('advancedsettings', 'theme_schoollege'));

    $name = 'theme_schoollege/brandcolor';
    $title = get_string('brandcolor', 'theme_schoollege');
    $description = get_string('brandcolor_desc', 'theme_schoollege');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_schoollege/navbarbg';
    $title = get_string('navbar-bg', 'theme_schoollege');
    $description = get_string('color_desc', 'theme_schoollege');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_schoollege/headerbg';
    $title = get_string('header-bg', 'theme_schoollege');
    $description = get_string('color_desc', 'theme_schoollege');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_schoollege/sidebarbg';
    $title = get_string('sidebar-bg', 'theme_schoollege');
    $description = get_string('color_desc', 'theme_schoollege');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_schoollege/sidebarahoverbg';
    $title = get_string('sidebar-ahover-bg', 'theme_schoollege');
    $description = get_string('color_desc', 'theme_schoollege');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_schoollege/footerbg';
    $title = get_string('footer-bg', 'theme_schoollege');
    $description = get_string('color_desc', 'theme_schoollege');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include before the content.
    $setting = new admin_setting_configtextarea('theme_schoollege/scsspre',
        get_string('rawscsspre', 'theme_schoollege'), get_string('rawscsspre_desc', 'theme_schoollege'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_configtextarea('theme_schoollege/scss', get_string('rawscss', 'theme_schoollege'),
        get_string('rawscss_desc', 'theme_schoollege'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
    