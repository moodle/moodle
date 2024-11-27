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
 * Educard block 14 settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Block 14 info.
$name = 'theme_educard/block14info';
$heading = get_string('block14info', 'theme_educard');
$information = get_string('block14infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable settings.
$name = 'theme_educard/block14enabled';
$title = get_string('block14enabled', 'theme_educard');
$description = get_string('block14enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 14 background img.
$name = 'theme_educard/block14bgimg';
$title = get_string('block14bgimg', 'theme_educard');
$description = get_string('block14bgimgdesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_URL, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Linear Gradient enabled-disabled.
$name = 'theme_educard/block14gradienton';
$title = get_string('block14gradienton', 'theme_educard');
$description = get_string('block14gradientondesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 14 headline.
$name = 'theme_educard/block14headline';
$title = get_string('block14headline', 'theme_educard');
$description = get_string('block14headlinedesc', 'theme_educard');
$default = get_string('block14headlinedefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 14 header text.
$name = 'theme_educard/block14header';
$title = get_string('block14header', 'theme_educard');
$description = get_string('block14headerdesc', 'theme_educard');
$default = get_string('block14headerdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 14 main title.
$name = 'theme_educard/block14maintitle';
$title = get_string('block14maintitle', 'theme_educard');
$description = get_string('block14maintitledesc', 'theme_educard');
$default = get_string('block14maintitledefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Count block 14 settings.
$name = 'theme_educard/block14count';
$title = get_string('block14count', 'theme_educard');
$description = get_string('block14countdesc', 'theme_educard');
$default = 3;
$options = [];
for ($i = 2; $i < 9; $i++) {
     $options[$i] = $i;
}
$description = $description.get_string('underline', 'theme_educard');
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
$count = get_config('theme_educard', 'block14count');
// Block 10 general settings END.
// ------------------------------------------------------------------------------------.
for ($i = 1; $i < $count + 1; $i++) {
    // Block 14 img.
    $name = 'theme_educard/sliderimageblock14img'.$i;
    $title = get_string('sliderimageblock14img', 'theme_educard', ['block' => $i]);
    $description = get_string('block14imgdesc', 'theme_educard');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'sliderimageblock14img'.$i);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 14 event header.
    $name = 'theme_educard/block14eventheader'.$i;
    $title = get_string('block14eventheader', 'theme_educard', ['block' => $i]);
    $description = get_string('block14eventheaderdesc', 'theme_educard');
    $default = "Spring Festivals";
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 14 caption.
    $name = 'theme_educard/block14caption'.$i;
    $title = get_string('block14caption', 'theme_educard', ['block' => $i]);
    $description = get_string('block14captiondesc', 'theme_educard');
    $default = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse facilisis ultricies tortor,";
    $default .= " nec sollicitudin lorem sagittis vitae. Curabitur rhoncus commodo.";
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '4');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 14 event link.
    $name = 'theme_educard/block14link'.$i;
    $title = get_string('block14link', 'theme_educard', ['block' => $i]);
    $description = get_string('block14linkdesc', 'theme_educard');
    $default = "";
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 14 date.
    $name = 'theme_educard/block14eventdate'.$i;
    $title = get_string('block14eventdate', 'theme_educard', ['block' => $i]);
    $description = get_string('block14eventdatedesc', 'theme_educard');
    $default = "23 March 2023";
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 14 detail.
    $name = 'theme_educard/block14detail'.$i;
    $title = get_string('block14detail', 'theme_educard', ['block' => $i]);
    $description = get_string('block14detaildesc', 'theme_educard');
    if ($i != $count) {
        $description = $description.get_string('underline', 'theme_educard');
    }
    $default = "";
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}
