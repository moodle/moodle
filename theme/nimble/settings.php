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
 * Settings for the Nimble theme.
 *
 * @package   theme_nimble
 * @copyright 2010 Patrick Malley
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Tagline setting
    $name = 'theme_nimble/tagline';
    $title = get_string('tagline','theme_nimble');
    $description = get_string('taglinedesc', 'theme_nimble');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // footerline setting
    $name = 'theme_nimble/footerline';
    $title = get_string('footerline','theme_nimble');
    $description = get_string('footerlinedesc', 'theme_nimble');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Background color setting
    $name = 'theme_nimble/backgroundcolor';
    $title = get_string('backgroundcolor','theme_nimble');
    $description = get_string('backgroundcolordesc', 'theme_nimble');
    $default = '#454545';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // link color setting
    $name = 'theme_nimble/linkcolor';
    $title = get_string('linkcolor','theme_nimble');
    $description = get_string('linkcolordesc', 'theme_nimble');
    $default = '#2a65b1';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // link hover color setting
    $name = 'theme_nimble/linkhover';
    $title = get_string('linkhover','theme_nimble');
    $description = get_string('linkhoverdesc', 'theme_nimble');
    $default = '#222222';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}
