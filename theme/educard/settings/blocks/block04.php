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
 * Educard block 4 settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Block 4 info.
$name = 'theme_educard/block04info';
$heading = get_string('block04info', 'theme_educard');
$information = get_string('block04infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable block 4 settings.
$name = 'theme_educard/block04enabled';
$title = get_string('block04enabled', 'theme_educard');
$description = get_string('block04enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 4 headline.
$name = 'theme_educard/block04headline';
$title = get_string('block04headline', 'theme_educard');
$description = get_string('block04headlinedesc', 'theme_educard');
$default = get_string('block04headlinedefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 4 header text.
$name = 'theme_educard/block04header';
$title = get_string('block04header', 'theme_educard');
$description = get_string('block04headerdesc', 'theme_educard');
$default = get_string('block04headerdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 4 main title.
$name = 'theme_educard/block04maintitle';
$title = get_string('block04maintitle', 'theme_educard');
$description = get_string('block04maintitledesc', 'theme_educard');
$default = get_string('block04maintitledefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 04 background img.
$name = 'theme_educard/block04bgimg';
$title = get_string('block04bgimg', 'theme_educard');
$description = get_string('block04bgimgdesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_URL, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Linear Gradient enabled-disabled.
$name = 'theme_educard/block04gradienton';
$title = get_string('block04gradienton', 'theme_educard');
$description = get_string('block04gradientondesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 4 link button text and link.
$name = 'theme_educard/block04button';
$title = get_string('block04button', 'theme_educard');
$description = get_string('block04buttondesc', 'theme_educard');
$default = get_string('block04buttondefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_educard/block04buttonlink';
$title = get_string('block04buttonlink', 'theme_educard');
$description = get_string('block04buttonlinkdesc', 'theme_educard');
$default = get_string('block04buttonlinkdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 4 image height.
$name = 'theme_educard/block04imgheight';
$title = get_string('block04imgheight', 'theme_educard');
$description = get_string('block04imgheightdesc', 'theme_educard');
$description = $description . get_string('underline', 'theme_educard');
$default = "300";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 04 general settings END.
// ------------------------------------------------------------------------------------.
for ($i = 1; $i <= 8; $i++) {
    // Block 04 title.
    $name = 'theme_educard/block04title' . $i;
    $title = get_string('block04title', 'theme_educard', ['block' => $i]);
    $description = get_string('block04titledesc', 'theme_educard');
    $default = 'Latest ' . $i;
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 04 caption.
    $name = 'theme_educard/block04caption' . $i;
    $title = get_string('block04caption', 'theme_educard', ['block' => $i]);
    $description = get_string('block04captiondesc', 'theme_educard');
    $default = 'Latest caption' . $i;
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 04 img.
    $name = 'theme_educard/sliderimageblock04img' . $i;
    $title = get_string('sliderimageblock04img', 'theme_educard', ['block' => $i]);
    $description = get_string('block04imgdesc', 'theme_educard');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'sliderimageblock04img' . $i);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 04 link.
    $name = 'theme_educard/block04link' . $i;
    $title = get_string('block04link', 'theme_educard', ['block' => $i]);
    $description = get_string('block04linkdesc', 'theme_educard');
    if ($i != 8) {
        $description = $description . get_string('underline', 'theme_educard');
    }
    $default = get_string('buttonlink', 'theme_educard');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}
