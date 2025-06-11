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

defined('MOODLE_INTERNAL') || die;// Main settings.

use theme_snap\admin_setting_configurl;

$snapsettings = new admin_settingpage('themesnapsocialmedia', get_string('socialmedia', 'theme_snap'));

    // Social media.
    $name = 'theme_snap/facebook';
    $title = new lang_string('facebook', 'theme_snap');
    $description = new lang_string('facebookdesc', 'theme_snap');
    $default = '';
    $setting = new admin_setting_configurl($name, $title, $description, $default);
    $snapsettings->add($setting);

    $name = 'theme_snap/x';
    $title = new lang_string('xakatwitter', 'theme_snap');
    $description = new lang_string('xakatwitterdesc', 'theme_snap');
    $default = '';
    $setting = new admin_setting_configurl($name, $title, $description, $default);
    $snapsettings->add($setting);

    $name = 'theme_snap/linkedin';
    $title = new lang_string('linkedin', 'theme_snap');
    $description = new lang_string('linkedindesc', 'theme_snap');
    $default = '';
    $setting = new admin_setting_configurl($name, $title, $description, $default);
    $snapsettings->add($setting);

    $name = 'theme_snap/youtube';
    $title = new lang_string('youtube', 'theme_snap');
    $description = new lang_string('youtubedesc', 'theme_snap');
    $default = '';
    $setting = new admin_setting_configurl($name, $title, $description, $default);
    $snapsettings->add($setting);

    $name = 'theme_snap/instagram';
    $title = new lang_string('instagram', 'theme_snap');
    $description = new lang_string('instagramdesc', 'theme_snap');
    $default = '';
    $setting = new admin_setting_configurl($name, $title, $description, $default);
    $snapsettings->add($setting);

    $name = 'theme_snap/tiktok';
    $title = new lang_string('tiktok', 'theme_snap');
    $description = new lang_string('tiktokdesc', 'theme_snap');
    $default = '';
    $setting = new admin_setting_configurl($name, $title, $description, $default);
    $snapsettings->add($setting);

    $settings->add($snapsettings);
