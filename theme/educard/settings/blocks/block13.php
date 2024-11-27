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
    // Block 13 info.
    $name = 'theme_educard/block13info';
    $heading = get_string('block13info', 'theme_educard');
    $information = get_string('block13infodesc', 'theme_educard');
    $setting = new admin_setting_heading($name, $heading, $information);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Enable or disable block 13 settings.
    $name = 'theme_educard/block13enabled';
    $title = get_string('block13enabled', 'theme_educard');
    $description = get_string('block13enableddesc', 'theme_educard');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 13 background img.
    $name = 'theme_educard/block13bgimg';
    $title = get_string('block13bgimg', 'theme_educard');
    $description = get_string('block13bgimgdesc', 'theme_educard');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_URL, '1', '1');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Linear Gradient enabled-disabled.
    $name = 'theme_educard/block13gradienton';
    $title = get_string('block13gradienton', 'theme_educard');
    $description = get_string('block13gradientondesc', 'theme_educard');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Course image show-hide.
    $name = 'theme_educard/block13imgenabled';
    $title = get_string('block13imgenabled', 'theme_educard');
    $description = get_string('block13imgenableddesc', 'theme_educard');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 13 count text.
    $name = 'theme_educard/block13count';
    $title = get_string('block13count', 'theme_educard');
    $description = get_string('block13countdesc', 'theme_educard');
    $default = get_string('block13countdefault', 'theme_educard');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, '2');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 13 headline.
    $name = 'theme_educard/block13headline';
    $title = get_string('block13headline', 'theme_educard');
    $description = get_string('block13headlinedesc', 'theme_educard');
    $default = get_string('block13headlinedefault', 'theme_educard');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 13 header.
    $name = 'theme_educard/block13header';
    $title = get_string('block13header', 'theme_educard');
    $description = get_string('block13headerdesc', 'theme_educard');
    $default = get_string('block13headerdefault', 'theme_educard');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 13 main title.
    $name = 'theme_educard/block13maintitle';
    $title = get_string('block13maintitle', 'theme_educard');
    $description = get_string('block13maintitledesc', 'theme_educard');
    $default = get_string('block13maintitledefault', 'theme_educard');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 13 caption.
    $name = 'theme_educard/block13caption';
    $title = get_string('block13caption', 'theme_educard');
    $description = get_string('block13captiondesc', 'theme_educard');
    $default = get_string('block13captiondefault', 'theme_educard');
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
