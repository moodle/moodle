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
 * Educard block 9 settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
GLOBAL  $DB;
// Block 09 info.
$name = 'theme_educard/block09info';
$heading = get_string('block09info', 'theme_educard');
$information = get_string('block09infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable block 09 settings.
$name = 'theme_educard/block09enabled';
$title = get_string('block09enabled', 'theme_educard');
$description = get_string('block09enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 9 headline.
$name = 'theme_educard/block09headline';
$title = get_string('block09headline', 'theme_educard');
$description = get_string('block09headlinedesc', 'theme_educard');
$default = get_string('block09headlinedefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 9 header text.
$name = 'theme_educard/block09header';
$title = get_string('block09header', 'theme_educard');
$description = get_string('block09headerdesc', 'theme_educard');
$default = get_string('block09headerdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 9 main title.
$name = 'theme_educard/block09maintitle';
$title = get_string('block09maintitle', 'theme_educard');
$description = get_string('block09maintitledesc', 'theme_educard');
$default = get_string('block09maintitledefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 9 background img.
$name = 'theme_educard/block09bgimg';
$title = get_string('block09bgimg', 'theme_educard');
$description = get_string('block09bgimgdesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_URL, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Linear Gradient enabled-disabled.
$name = 'theme_educard/block09gradienton';
$title = get_string('block09gradienton', 'theme_educard');
$description = get_string('block09gradientondesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 09 background color or picture select.
$name = 'theme_educard/block09background';
$title = get_string('block09background', 'theme_educard');
$description = get_string('block09backgrounddesc', 'theme_educard');
$default = "2";
$options = [
     '0' => 'none',
     '1' => 'color',
     '2' => 'picture',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 09 count .
$name = 'theme_educard/block09count';
$title = get_string('block09count', 'theme_educard');
$description = get_string('block09countdesc', 'theme_educard');
$default = get_string('block09countdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 09 select show category.
$name = 'theme_educard/block09ctselect';
$title = get_string('block09ctselect', 'theme_educard');
$description = get_string('block09ctselectdesc', 'theme_educard');
$default = [];
$options = theme_educard_all_category();
$setting = new admin_setting_configmultiselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 09 icon list.
$name = 'theme_educard/block09icon';
$title = get_string('block09icon', 'theme_educard');
$description = get_string('block09icondesc', 'theme_educard');
$default = get_string('block09icondefault', 'theme_educard');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
