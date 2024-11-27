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
 * Educard block 12 settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Block 12 info.
$name = 'theme_educard/block12info';
$heading = get_string('block12info', 'theme_educard');
$information = get_string('block12infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable block 12 settings.
$name = 'theme_educard/block12enabled';
$title = get_string('block12enabled', 'theme_educard');
$description = get_string('block12enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 12 headline.
$name = 'theme_educard/block12headline';
$title = get_string('block12headline', 'theme_educard');
$description = get_string('block12headlinedesc', 'theme_educard');
$default = get_string('block12headlinedefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 12 header text.
$name = 'theme_educard/block12header';
$title = get_string('block12header', 'theme_educard');
$description = get_string('block12headerdesc', 'theme_educard');
$default = get_string('block12headerdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 12 main title.
$name = 'theme_educard/block12maintitle';
$title = get_string('block12maintitle', 'theme_educard');
$description = get_string('block12maintitledesc', 'theme_educard');
$default = get_string('block12maintitledefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 12 background img.
$name = 'theme_educard/block12bgimg';
$title = get_string('block12bgimg', 'theme_educard');
$description = get_string('block12bgimgdesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_URL, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Linear Gradient enabled-disabled.
$name = 'theme_educard/block12gradienton';
$title = get_string('block12gradienton', 'theme_educard');
$description = get_string('block12gradientondesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 12 icon list.
$name = 'theme_educard/block12icon';
$title = get_string('block12icon', 'theme_educard');
$description = get_string('block12icondesc', 'theme_educard');
$default = get_string('block12icondefault', 'theme_educard');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Count block 12 settings.
$name = 'theme_educard/block12count';
$title = get_string('block12count', 'theme_educard');
$description = get_string('block12countdesc', 'theme_educard');
$default = 4;
$options = [];
for ($i = 2; $i <= 6; $i++) {
     $options[$i] = $i;
}
$description = $description.get_string('underline', 'theme_educard');
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
$count = get_config('theme_educard', 'block12count');
// Block 12 general settings END.
// ------------------------------------------------------------------------------------.
for ($i = 1; $i <= $count; $i++) {
    // Block 12 title.
    $name = 'theme_educard/block12title'.$i;
    $title = get_string('block12title', 'theme_educard', ['block' => $i]);
    $description = get_string('block12titledesc', 'theme_educard');
    $default = "Video Title";
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 12 caption.
    $name = 'theme_educard/block12caption'.$i;
    $title = get_string('block12caption', 'theme_educard', ['block' => $i]);
    $description = get_string('block12captiondesc', 'theme_educard');
    $default = "Video Caption";
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 12 video format.
    $name = 'theme_educard/block12video'.$i;
    $title = get_string('block12video', 'theme_educard', ['block' => $i]);
    $description = get_string('block12videodesc', 'theme_educard');
    $default = "1";
    $options = [];
    $options[1] = "YouTube";
    $options[2] = "Vimeo";
    $options[3] = "Custom Link";
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 12 video id/custom link .
    $name = 'theme_educard/block12link'.$i;
    $title = get_string('block12link', 'theme_educard', ['block' => $i]);
    $description = get_string('block12linkdesc', 'theme_educard');
    $default = "wzgy-9CV4lg";
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 12 video preview img.
    $name = 'theme_educard/imgblock12'.$i;
    $title = get_string('imgblock12', 'theme_educard', ['block' => $i]);
    $description = get_string('block12imgdesc', 'theme_educard');
    if ($i != $count) {
        $description = $description.get_string('underline', 'theme_educard');
    }
    $default = "";
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'imgblock12'.$i);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

}
