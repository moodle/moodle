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
 * Educard page persons settings.
 *
 * @package   theme_educard
 * @copyright 2024 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
GLOBAL  $DB;
// Person page info.
$name = 'theme_educard/page02info';
$heading = get_string('page02info', 'theme_educard');
$information = get_string('page02infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Page 2 banner img.
$name = 'theme_educard/imgpage02img';
$title = get_string('imgpage02img', 'theme_educard');
$description = get_string('imgpage02imgdesc', 'theme_educard');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'imgpage02img');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Person page background img.
$name = 'theme_educard/page02bgimg';
$title = get_string('page02bgimg', 'theme_educard');
$description = get_string('page02bgimgdesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_URL, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Linear Gradient enabled-disabled.
$name = 'theme_educard/page02gradienton';
$title = get_string('page02gradienton', 'theme_educard');
$description = get_string('page02gradientondesc', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable block 08 settings.
$name = 'theme_educard/page02enabled';
$title = get_string('page02enabled', 'theme_educard');
$description = get_string('page02enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Person page design select.
$name = 'theme_educard/page02desing';
$title = get_string('page02desing', 'theme_educard');
$description = get_string('page02desingdesc', 'theme_educard');
$default = 1;
$options = [];
for ($i = 1; $i <= 4; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Person page explanation.
$name = 'theme_educard/page02explanation';
$title = get_string('page02explanation', 'theme_educard');
$description = get_string('page02explanationdesc', 'theme_educard');
$default = get_string('page02explanationdefault', 'theme_educard');
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show total number of courses and students.
$name = 'theme_educard/page02total';
$title = get_string('page02total', 'theme_educard');
$description = get_string('page02totaldesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show description.
$name = 'theme_educard/page02description';
$title = get_string('page02description', 'theme_educard');
$description = get_string('page02descriptiondesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Person page select show role.
$name = 'theme_educard/page02showrole';
$title = get_string('page02showrole', 'theme_educard');
$description = get_string('page02showroledesc', 'theme_educard');
$default = '3';
$options = [];
$role = $DB->get_records('role');
foreach ($role as $roles) {
     $options[$roles->id] = $roles->shortname;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Person page count text.
$name = 'theme_educard/page02count';
$title = get_string('page02count', 'theme_educard');
$description = get_string('page02countdesc', 'theme_educard');
$default = get_string('page02countdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enter the ID of the persons to be shown.
$name = 'theme_educard/page02id';
$title = get_string('page02id', 'theme_educard');
$description = get_string('page02iddesc', 'theme_educard');
$default = get_string('page02iddefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
