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
 * Educard block 7 settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Block 07 info.
$name = 'theme_educard/block07info';
$heading = get_string('block07info', 'theme_educard');
$information = get_string('block07infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable block 07 settings.
$name = 'theme_educard/block07enabled';
$title = get_string('block07enabled', 'theme_educard');
$description = get_string('block07enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 7 headline.
$name = 'theme_educard/block07headline';
$title = get_string('block07headline', 'theme_educard');
$description = get_string('block07headlinedesc', 'theme_educard');
$default = get_string('block07headlinedefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 7 header text.
$name = 'theme_educard/block07header';
$title = get_string('block07header', 'theme_educard');
$description = get_string('block07headerdesc', 'theme_educard');
$default = get_string('block07headerdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 7 main title.
$name = 'theme_educard/block07maintitle';
$title = get_string('block07maintitle', 'theme_educard');
$description = get_string('block07maintitledesc', 'theme_educard');
$default = get_string('block07maintitledefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 7 background img.
$name = 'theme_educard/block07bgimg';
$title = get_string('block07bgimg', 'theme_educard');
$description = get_string('block07bgimgdesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_URL, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Linear Gradient enabled-disabled.
$name = 'theme_educard/block07gradienton';
$title = get_string('block07gradienton', 'theme_educard');
$description = get_string('block07gradientondesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 07 count text.
$name = 'theme_educard/block07count';
$title = get_string('block07count', 'theme_educard');
$description = get_string('block07countdesc', 'theme_educard');
$default = "6";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, '2');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 07 teacher role.
$options = [];
$role = $DB->get_records('role');
foreach ($role as $roles) {
     $options[$roles->id] = $roles->shortname;
}
$name = 'theme_educard/block07teacherrole';
$title = get_string('block07teacherrole', 'theme_educard');
$description = get_string('block07teacherroledesc', 'theme_educard');
$default = 3;
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 07 student role.
$name = 'theme_educard/block07studentrole';
$title = get_string('block07studentrole', 'theme_educard');
$description = get_string('block07studentroledesc', 'theme_educard');
$default = 5;
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Course image show and hide .
$name = 'theme_educard/block07imgenabled';
$title = get_string('block07imgenabled', 'theme_educard');
$description = get_string('block07imgenableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Teacher enabled/disabled .
$name = 'theme_educard/block07teacherenabled';
$title = get_string('block07teacherenabled', 'theme_educard');
$description = get_string('block07teacherenableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show price .
$name = 'theme_educard/block07priceshow';
$title = get_string('block07priceshow', 'theme_educard');
$description = get_string('block07priceshowdesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 07 title select.
$name = 'theme_educard/block07title';
$title = get_string('block07title', 'theme_educard');
$description = get_string('block07titledesc', 'theme_educard');
$default = "shortname";
$options = [
'shortname' => 'shortname',
'fullname' => 'fullname',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 07 select show course.
$name = 'theme_educard/block07crselect';
$title = get_string('block07crselect', 'theme_educard');
$description = get_string('block07crselectdesc', 'theme_educard');
$default = [];
$options = theme_educard_all_course();
$setting = new admin_setting_configmultiselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 07 button.
$name = 'theme_educard/block07button';
$title = get_string('block07button', 'theme_educard');
$description = get_string('block07buttondesc', 'theme_educard');
$default = get_string('button', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 07 button link.
$name = 'theme_educard/block07buttonlink';
$title = get_string('block07buttonlink', 'theme_educard');
$description = get_string('block07buttonlinkdesc', 'theme_educard');
$default = 'course/index.php';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
