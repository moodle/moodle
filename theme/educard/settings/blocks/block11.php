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
 * Educard block 11 settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Block 11 info.
$name = 'theme_educard/block11info';
$heading = get_string('block11info', 'theme_educard');
$information = get_string('block11infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable block 11 settings.
$name = 'theme_educard/block11enabled';
$title = get_string('block11enabled', 'theme_educard');
$description = get_string('block11enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 11 headline.
$name = 'theme_educard/block11headline';
$title = get_string('block11headline', 'theme_educard');
$description = get_string('block11headlinedesc', 'theme_educard');
$default = get_string('block11headlinedefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 11 header text.
$name = 'theme_educard/block11header';
$title = get_string('block11header', 'theme_educard');
$description = get_string('block11headerdesc', 'theme_educard');
$default = get_string('block11headerdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 11 main title.
$name = 'theme_educard/block11maintitle';
$title = get_string('block11maintitle', 'theme_educard');
$description = get_string('block11maintitledesc', 'theme_educard');
$default = get_string('block11maintitledefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 11 background img.
$name = 'theme_educard/block11bgimg';
$title = get_string('block11bgimg', 'theme_educard');
$description = get_string('block11bgimgdesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_URL, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Linear Gradient enabled-disabled.
$name = 'theme_educard/block11gradienton';
$title = get_string('block11gradienton', 'theme_educard');
$description = get_string('block11gradientondesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Count block 11 settings.
$name = 'theme_educard/block11count';
$title = get_string('block11count', 'theme_educard');
$description = get_string('block11countdesc', 'theme_educard');
$default = 3;
$options = [];
for ($i = 2; $i <= 10; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
