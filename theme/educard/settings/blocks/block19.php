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
 * Educard block 19 settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Block 19 info.
$name = 'theme_educard/block19info';
$heading = get_string('block19info', 'theme_educard');
$information = get_string('block19infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable block 19 settings.
$name = 'theme_educard/block19enabled';
$title = get_string('block19enabled', 'theme_educard');
$description = get_string('block19enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable block 19 header settings.
$name = 'theme_educard/block19headerenabled';
$title = get_string('block19headerenabled', 'theme_educard');
$description = get_string('block19headerenableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 19 count select.
$name = 'theme_educard/block19count';
$title = get_string('block19count', 'theme_educard');
$description = get_string('block19countdesc', 'theme_educard');
$default = 4;
$options = [];
for ($i = 3; $i <= 12; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 19 headline.
$name = 'theme_educard/block19headline';
$title = get_string('block19headline', 'theme_educard');
$description = get_string('block19headlinedesc', 'theme_educard');
$default = get_string('block19headlinedefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 19 header text.
$name = 'theme_educard/block19header';
$title = get_string('block19header', 'theme_educard');
$description = get_string('block19headerdesc', 'theme_educard');
$default = get_string('block19headerdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 19 main title.
$name = 'theme_educard/block19maintitle';
$title = get_string('block19maintitle', 'theme_educard');
$description = get_string('block19maintitledesc', 'theme_educard');
$default = get_string('block19maintitledefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 19 background img.
$name = 'theme_educard/block19bgimg';
$title = get_string('block19bgimg', 'theme_educard');
$description = get_string('block19bgimgdesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_URL, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Linear Gradient enabled-disabled.
$name = 'theme_educard/block19gradienton';
$title = get_string('block19gradienton', 'theme_educard');
$description = get_string('block19gradientondesc', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
$count = get_config('theme_educard', 'block19count');
// Block 19 general settings END.
// ------------------------------------------------------------------------------------.
for ($i = 1; $i <= $count; $i++) {
    // Block 19 img.
    $name = 'theme_educard/sliderimageblock19img'.$i;
    $title = get_string('sliderimageblock19img', 'theme_educard', ['block' => $i]);
    $description = get_string('block19imgdesc', 'theme_educard');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'sliderimageblock19img'.$i);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 19 link .
    $name = 'theme_educard/block19link'.$i;
    $title = get_string('block19link', 'theme_educard', ['block' => $i]);
    $description = get_string('block19linkdesc', 'theme_educard');
    if ($i != $count) {
        $description = $description.get_string('underline', 'theme_educard');
    }
    $default = get_string('buttonlink', 'theme_educard');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}
