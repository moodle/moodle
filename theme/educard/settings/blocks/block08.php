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
 * Educard block 8 settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
GLOBAL  $DB;
// Block 08 info.
$name = 'theme_educard/block08info';
$heading = get_string('block08info', 'theme_educard');
$information = get_string('block08infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable block 08 settings.
$name = 'theme_educard/block08enabled';
$title = get_string('block08enabled', 'theme_educard');
$description = get_string('block08enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 8 headline.
$name = 'theme_educard/block08headline';
$title = get_string('block08headline', 'theme_educard');
$description = get_string('block08headlinedesc', 'theme_educard');
$default = get_string('block08headlinedefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 8 header text.
$name = 'theme_educard/block08header';
$title = get_string('block08header', 'theme_educard');
$description = get_string('block08headerdesc', 'theme_educard');
$default = get_string('block08headerdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 8 main title.
$name = 'theme_educard/block08maintitle';
$title = get_string('block08maintitle', 'theme_educard');
$description = get_string('block08maintitledesc', 'theme_educard');
$default = get_string('block08maintitledefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 8 background img.
$name = 'theme_educard/block08bgimg';
$title = get_string('block08bgimg', 'theme_educard');
$description = get_string('block08bgimgdesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_URL, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Linear Gradient enabled-disabled.
$name = 'theme_educard/block08gradienton';
$title = get_string('block08gradienton', 'theme_educard');
$description = get_string('block08gradientondesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show total number of courses and students.
$name = 'theme_educard/block08total';
$title = get_string('block08total', 'theme_educard');
$description = get_string('block08totaldesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show description.
$name = 'theme_educard/block08description';
$title = get_string('block08description', 'theme_educard');
$description = get_string('block08descriptiondesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 08 select show role.
$name = 'theme_educard/block08showrole';
$title = get_string('block08showrole', 'theme_educard');
$description = get_string('block08showroledesc', 'theme_educard');
$default = '3';
$options = [];
$role = $DB->get_records('role');
foreach ($role as $roles) {
     $options[$roles->id] = $roles->shortname;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 08 count text.
$name = 'theme_educard/block08count';
$title = get_string('block08count', 'theme_educard');
$description = get_string('block08countdesc', 'theme_educard');
$default = get_string('block08countdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
