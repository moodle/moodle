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
 * Settings for the fusion theme.
 *
 * @package    theme_fusion
 * @copyright  2010 Patrick Malley (http://newschoollearning.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // link color setting
    $name = 'theme_fusion/linkcolor';
    $title = get_string('linkcolor','theme_fusion');
    $description = get_string('linkcolordesc', 'theme_fusion');
    $default = '#2d83d5';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Tag line setting
    $name = 'theme_fusion/tagline';
    $title = get_string('tagline','theme_fusion');
    $description = get_string('taglinedesc', 'theme_fusion');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Foot note setting
    $name = 'theme_fusion/footertext';
    $title = get_string('footertext','theme_fusion');
    $description = get_string('footertextdesc', 'theme_fusion');
    $setting = new admin_setting_confightmleditor($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Custom CSS file
    $name = 'theme_fusion/customcss';
    $title = get_string('customcss','theme_fusion');
    $description = get_string('customcssdesc', 'theme_fusion');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

}
