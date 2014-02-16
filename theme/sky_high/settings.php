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
 * Settings for the sky_high theme
 *
 * @package   theme_sky_high
 * @copyright 2010 John Stabinger (http://newschoollearning.com/)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Logo file setting.
    $name = 'theme_sky_high/logo';
    $title = get_string('logo', 'theme_sky_high');
    $description = get_string('logodesc', 'theme_sky_high');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Block region width.
    $name = 'theme_sky_high/regionwidth';
    $title = get_string('regionwidth', 'theme_sky_high');
    $description = get_string('regionwidthdesc', 'theme_sky_high');
    $default = 240;
    $choices = array(200 => '200px', 240 => '240px', 290 => '290px', 350 => '350px', 420 => '420px');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Foot note setting.
    $name = 'theme_sky_high/footnote';
    $title = get_string('footnote', 'theme_sky_high');
    $description = get_string('footnotedesc', 'theme_sky_high');
    $setting = new admin_setting_confightmleditor($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Custom CSS file.
    $name = 'theme_sky_high/customcss';
    $title = get_string('customcss', 'theme_sky_high');
    $description = get_string('customcssdesc', 'theme_sky_high');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Add our page to the structure of the admin tree.
}