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
 * Educard custom block 7 settings.
 *
 * @package   theme_educard
 * @copyright 2023 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Block 21 info.
$name = 'theme_educard/block21info';
$heading = get_string('block21info', 'theme_educard');
$information = get_string('block21infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable Block 21 settings.
$name = 'theme_educard/block21enabled';
$title = get_string('block21enabled', 'theme_educard');
$description = get_string('block21enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 21 headline.
$name = 'theme_educard/block21headline';
$title = get_string('block21headline', 'theme_educard');
$description = get_string('block21headlinedesc', 'theme_educard');
$default = get_string('block21headlinedefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 21 header text.
$name = 'theme_educard/block21header';
$title = get_string('block21header', 'theme_educard');
$description = get_string('block21headerdesc', 'theme_educard');
$default = get_string('block21headerdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 21 main title.
$name = 'theme_educard/block21maintitle';
$title = get_string('block21maintitle', 'theme_educard');
$description = get_string('block21maintitledesc', 'theme_educard');
$default = get_string('block21maintitledefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 21 background img.
$name = 'theme_educard/block21bgimg';
$title = get_string('block21bgimg', 'theme_educard');
$description = get_string('block21bgimgdesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_URL, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Linear Gradient enabled-disabled.
$name = 'theme_educard/block21gradienton';
$title = get_string('block21gradienton', 'theme_educard');
$description = get_string('block21gradientondesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 21 count text.
$name = 'theme_educard/block21count';
$title = get_string('block21count', 'theme_educard');
$description = get_string('block21countdesc', 'theme_educard');
$default = "6";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, '2');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 21 teacher role.
$options = [];
$role = $DB->get_records('role');
foreach ($role as $roles) {
     $options[$roles->id] = $roles->shortname;
}
$name = 'theme_educard/block21teacherrole';
$title = get_string('block21teacherrole', 'theme_educard');
$description = get_string('block21teacherroledesc', 'theme_educard');
$default = 3;
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 21 student role.
$name = 'theme_educard/block21studentrole';
$title = get_string('block21studentrole', 'theme_educard');
$description = get_string('block21studentroledesc', 'theme_educard');
$default = 5;
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Course image show and hide .
$name = 'theme_educard/block21imgenabled';
$title = get_string('block21imgenabled', 'theme_educard');
$description = get_string('block21imgenableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Teacher enabled/disabled .
$name = 'theme_educard/block21teacherenabled';
$title = get_string('block21teacherenabled', 'theme_educard');
$description = get_string('block21teacherenableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show price .
$name = 'theme_educard/block21priceshow';
$title = get_string('block21priceshow', 'theme_educard');
$description = get_string('block21priceshowdesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 21 title select.
$name = 'theme_educard/block21title';
$title = get_string('block21title', 'theme_educard');
$description = get_string('block21titledesc', 'theme_educard');
$default = "shortname";
$options = [
'shortname' => 'shortname',
'fullname' => 'fullname',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 21 live event date id.
$name = 'theme_educard/block21led';
$title = get_string('block21led', 'theme_educard');
$description = get_string('block21leddesc', 'theme_educard');
$default = "";
$options = theme_educard_customfield_id();
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 21 publish in live id.
$name = 'theme_educard/block21plive';
$title = get_string('block21plive', 'theme_educard');
$description = get_string('block21plivedesc', 'theme_educard');
$default = "";
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 21 live event date sort.
$name = 'theme_educard/block21ledsort';
$title = get_string('block21ledsort', 'theme_educard');
$description = get_string('block21ledsortdesc', 'theme_educard');
$default = "DESC";
$options = [
'DESC' => 'Desc',
'ASC' => 'Asc',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 21 live event date format.
$name = 'theme_educard/block21ledfrmt';
$title = get_string('block21ledfrmt', 'theme_educard');
$description = get_string('block21ledfrmtdesc', 'theme_educard');
$default = "%d %b %Y";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, '10');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 21 button.
$name = 'theme_educard/block21button';
$title = get_string('block21button', 'theme_educard');
$description = get_string('block21buttondesc', 'theme_educard');
$default = get_string('button', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 21 button link.
$name = 'theme_educard/block21buttonlink';
$title = get_string('block21buttonlink', 'theme_educard');
$description = get_string('block21buttonlinkdesc', 'theme_educard');
$default = 'course/index.php';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
