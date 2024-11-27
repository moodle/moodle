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
 * Educard block 1 settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
    // Block 01 info.
    $name = 'theme_educard/block01info';
    $heading = get_string('block01info', 'theme_educard');
    $information = get_string('block01infodesc', 'theme_educard');
    $setting = new admin_setting_heading($name, $heading, $information);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Enable or disable block 01 settings.
    $name = 'theme_educard/block01enabled';
    $title = get_string('block01enabled', 'theme_educard');
    $description = get_string('block01enableddesc', 'theme_educard');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 01 background color.
    $name = 'theme_educard/block01color';
    $title = get_string('block01color', 'theme_educard');
    $description = get_string('block01colordesc', 'theme_educard');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 01 background img.
    $name = 'theme_educard/imgblock01background';
    $title = get_string('imgblock01background', 'theme_educard');
    $description = get_string('imgblock01backgrounddesc', 'theme_educard');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'imgblock01background');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Background img parallax.
    $name = 'theme_educard/block01parallax';
    $title = get_string('block01parallax', 'theme_educard');
    $description = get_string('block01parallaxdesc', 'theme_educard');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 01 background img.
    $name = 'theme_educard/block01bgimg';
    $title = get_string('block01bgimg', 'theme_educard');
    $description = get_string('block01bgimgdesc', 'theme_educard');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_URL, '1', '1');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Linear Gradient enabled-disabled.
    $name = 'theme_educard/block01gradienton';
    $title = get_string('block01gradienton', 'theme_educard');
    $description = get_string('block01gradientondesc', 'theme_educard');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 1 headline.
    $name = 'theme_educard/block01headline';
    $title = get_string('block01headline', 'theme_educard');
    $description = get_string('block01headlinedesc', 'theme_educard');
    $default = get_string('block01headlinedefault', 'theme_educard');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 1 header.
    $name = 'theme_educard/block01header';
    $title = get_string('block01header', 'theme_educard');
    $description = get_string('block01headerdesc', 'theme_educard');
    $default = get_string('block01headerdefault', 'theme_educard');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 31 main title.
    $name = 'theme_educard/block01maintitle';
    $title = get_string('block01maintitle', 'theme_educard');
    $description = get_string('block01maintitledesc', 'theme_educard');
    $default = get_string('block01maintitledefault', 'theme_educard');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 01 caption.
    $name = 'theme_educard/block01caption';
    $title = get_string('block01caption', 'theme_educard');
    $description = get_string('block01captiondesc', 'theme_educard');
    $default = get_string('block01captiondefault', 'theme_educard');
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 01 button.
    $name = 'theme_educard/block01button';
    $title = get_string('block01button', 'theme_educard');
    $description = get_string('block01buttondesc', 'theme_educard');
    $default = get_string('button', 'theme_educard');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 01 button link.
    $name = 'theme_educard/block01buttonlink';
    $title = get_string('block01buttonlink', 'theme_educard');
    $description = get_string('block01buttonlinkdesc', 'theme_educard');
    $default = get_string('buttonlink', 'theme_educard');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
