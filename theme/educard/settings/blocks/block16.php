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
 * Educard block 16 settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Block 16 info.
$name = 'theme_educard/block16info';
$heading = get_string('block16info', 'theme_educard');
$information = get_string('block16infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable settings.
$name = 'theme_educard/block16enabled';
$title = get_string('block16enabled', 'theme_educard');
$description = get_string('block16enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block title.
$name = 'theme_educard/block16title';
$title = get_string('block16title', 'theme_educard');
$description = get_string('block16titledesc', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Page caption.
$name = 'theme_educard/block16caption';
$title = get_string('block16caption', 'theme_educard');
$description = get_string('block16captiondesc', 'theme_educard');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Page css link.
$name = 'theme_educard/block16csslink';
$title = get_string('block16csslink', 'theme_educard');
$description = get_string('block16csslinkdesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Page css.
$name = 'theme_educard/block16css';
$title = get_string('block16css', 'theme_educard');
$description = get_string('block16cssdesc', 'theme_educard');
$default = '';
$setting = new admin_setting_scsscode($name, $title, $description, $default, PARAM_RAW);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
