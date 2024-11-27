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
 * Educard block 6 settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Block 6 info.
$name = 'theme_educard/block06info';
$heading = get_string('block06info', 'theme_educard');
$information = get_string('block06infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable block 6 settings.
$name = 'theme_educard/block06enabled';
$title = get_string('block06enabled', 'theme_educard');
$description = get_string('block06enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 6 headline.
$name = 'theme_educard/block06headline';
$title = get_string('block06headline', 'theme_educard');
$description = get_string('block06headlinedesc', 'theme_educard');
$default = get_string('block06headlinedefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 6 header text.
$name = 'theme_educard/block06header';
$title = get_string('block06header', 'theme_educard');
$description = get_string('block06headerdesc', 'theme_educard');
$default = get_string('block06headerdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 6 main title.
$name = 'theme_educard/block06maintitle';
$title = get_string('block06maintitle', 'theme_educard');
$description = get_string('block06maintitledesc', 'theme_educard');
$default = get_string('block06maintitledefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 06 background img.
$name = 'theme_educard/block06bgimg';
$title = get_string('block06bgimg', 'theme_educard');
$description = get_string('block06bgimgdesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_URL, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Linear Gradient enabled-disabled.
$name = 'theme_educard/block06gradienton';
$title = get_string('block06gradienton', 'theme_educard');
$description = get_string('block06gradientondesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 6 background color.
$name = 'theme_educard/block06color';
$title = get_string('block06color', 'theme_educard');
$description = get_string('block06colordesc', 'theme_educard');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 6 img select.
$name = 'theme_educard/sliderimageblock06img';
$title = get_string('sliderimageblock06img', 'theme_educard');
$description = get_string('block06imgdesc', 'theme_educard');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'sliderimageblock06img');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Block 6 caption.
$name = 'theme_educard/block06caption';
$title = get_string('block06caption', 'theme_educard');
$description = get_string('block06captiondesc', 'theme_educard');
$default = get_string('block06captiondefault', 'theme_educard');
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 6 button.
$name = 'theme_educard/block06button';
$title = get_string('block06button', 'theme_educard');
$description = get_string('block06buttondesc', 'theme_educard');
$default = get_string('button', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 6 button link.
$name = 'theme_educard/block06buttonlink';
$title = get_string('block06buttonlink', 'theme_educard');
$description = get_string('block06buttonlinkdesc', 'theme_educard');
$default = get_string('buttonlink', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
