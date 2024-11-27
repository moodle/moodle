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
 * Educard block 10 settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Block 10 info.
$name = 'theme_educard/block10info';
$heading = get_string('block10info', 'theme_educard');
$information = get_string('block10infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable block 10 settings.
$name = 'theme_educard/block10enabled';
$title = get_string('block10enabled', 'theme_educard');
$description = get_string('block10enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 10 headline.
$name = 'theme_educard/block10headline';
$title = get_string('block10headline', 'theme_educard');
$description = get_string('block10headlinedesc', 'theme_educard');
$default = get_string('block10headlinedefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 10 header text.
$name = 'theme_educard/block10header';
$title = get_string('block10header', 'theme_educard');
$description = get_string('block10headerdesc', 'theme_educard');
$default = get_string('block10headerdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 10 main title.
$name = 'theme_educard/block10maintitle';
$title = get_string('block10maintitle', 'theme_educard');
$description = get_string('block10maintitledesc', 'theme_educard');
$default = get_string('block10maintitledefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 10 background img.
$name = 'theme_educard/block10bgimg';
$title = get_string('block10bgimg', 'theme_educard');
$description = get_string('block10bgimgdesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_URL, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Linear Gradient enabled-disabled.
$name = 'theme_educard/block10gradienton';
$title = get_string('block10gradienton', 'theme_educard');
$description = get_string('block10gradientondesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Count block 2 settings.
$name = 'theme_educard/block10count';
$title = get_string('block10count', 'theme_educard');
$description = get_string('block10countdesc', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$default = 3;
$options = [];
for ($i = 2; $i < 9; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
$count = get_config('theme_educard', 'block10count');
// Block 10 general settings END.
// ------------------------------------------------------------------------------------.
for ($i = 1; $i < $count + 1; $i++) {
    // Block 10 img.
    $name = 'theme_educard/sliderimageblock10img'.$i;
    $title = get_string('sliderimageblock10img', 'theme_educard', ['block' => $i]);
    $description = get_string('block10imgdesc', 'theme_educard');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'sliderimageblock10img'.$i);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 10 name.
    $name = 'theme_educard/block10name'.$i;
    $title = get_string('block10name', 'theme_educard', ['block' => $i]);
    $description = get_string('block10namedesc', 'theme_educard');
    $default = "John Doe";
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 10 job.
    $name = 'theme_educard/block10job'.$i;
    $title = get_string('block10job', 'theme_educard', ['block' => $i]);
    $description = get_string('block10jobdesc', 'theme_educard');
    $default = "IT Manager";
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 10 caption.
    $name = 'theme_educard/block10caption'.$i;
    $title = get_string('block10caption', 'theme_educard', ['block' => $i]);
    $description = get_string('block10captiondesc', 'theme_educard');
    $default = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do mode tempor incididunt ut labore";
    $default .= "et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut";
    $default .= " aliquip ex ea commodo consequat.";
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '3');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 10 button link.
    $name = 'theme_educard/block10link'.$i;
    $title = get_string('block10link', 'theme_educard', ['block' => $i]);
    $description = get_string('block10linkdesc', 'theme_educard');
    if ($i != $count) {
        $description = $description.get_string('underline', 'theme_educard');
    }
    $default = get_string('buttonlink', 'theme_educard');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}
