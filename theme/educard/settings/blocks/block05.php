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
 * Educard block 5 settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Block 5 info.
$name = 'theme_educard/block05info';
$heading = get_string('block05info', 'theme_educard');
$information = get_string('block05infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable block 5 settings.
$name = 'theme_educard/block05enabled';
$title = get_string('block05enabled', 'theme_educard');
$description = get_string('block05enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 5 headline.
$name = 'theme_educard/block05headline';
$title = get_string('block05headline', 'theme_educard');
$description = get_string('block05headlinedesc', 'theme_educard');
$default = get_string('block05headlinedefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 5 header text.
$name = 'theme_educard/block05header';
$title = get_string('block05header', 'theme_educard');
$description = get_string('block05headerdesc', 'theme_educard');
$default = get_string('block05headerdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 5 main title.
$name = 'theme_educard/block05maintitle';
$title = get_string('block05maintitle', 'theme_educard');
$description = get_string('block05maintitledesc', 'theme_educard');
$default = get_string('block05maintitledefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 5 background img.
$name = 'theme_educard/block05bgimg';
$title = get_string('block05bgimg', 'theme_educard');
$description = get_string('block05bgimgdesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_URL, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Linear Gradient enabled-disabled.
$name = 'theme_educard/block05gradienton';
$title = get_string('block05gradienton', 'theme_educard');
$description = get_string('block05gradientondesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 5 img select.
$name = 'theme_educard/sliderimageblock05img';
$title = get_string('sliderimageblock05img', 'theme_educard');
$description = get_string('block05imgdesc', 'theme_educard');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'sliderimageblock05img');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 05 video format.
$name = 'theme_educard/block05video';
$title = get_string('block05video', 'theme_educard');
$description = get_string('block05videodesc', 'theme_educard');
$default = "1";
$options = [];
$options[1] = "YouTube";
$options[2] = "Vimeo";
$options[3] = "Custom Link";
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 05 video id/custom link .
$name = 'theme_educard/block05videolink';
$title = get_string('block05videolink', 'theme_educard');
$description = get_string('block05videolinkdesc', 'theme_educard');
$default = "wzgy-9CV4lg";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 5 icon list.
$name = 'theme_educard/block05icon';
$title = get_string('block05icon', 'theme_educard');
$description = get_string('block05icondesc', 'theme_educard');
$default = get_string('block05icondefault', 'theme_educard');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Count block 5 settings.
$name = 'theme_educard/block05count';
$title = get_string('block05count', 'theme_educard');
$description = get_string('block05countdesc', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$default = 6;
$options = [];
for ($i = 0; $i <= 8; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
$count = get_config('theme_educard', 'block05count');
// Block 05 general settings END.
// ------------------------------------------------------------------------------------.
for ($i = 1; $i <= $count; $i++) {
    // Block 05 title.
    $name = 'theme_educard/block05title'.$i;
    $title = get_string('block05title', 'theme_educard', ['block' => $i]);
    $description = get_string('block05titledesc', 'theme_educard');
    $default = 'Lorem Ipsum';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 05 caption.
    $name = 'theme_educard/block05caption'.$i;
    $title = get_string('block05caption', 'theme_educard', ['block' => $i]);
    $description = get_string('block05captiondesc', 'theme_educard');
    $default = "At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis";
    $default .= " Voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi";
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 05 link .
    $name = 'theme_educard/block05link'.$i;
    $title = get_string('block05link', 'theme_educard', ['block' => $i]);
    $description = get_string('block05linkdesc', 'theme_educard');
    if ($i != $count) {
        $description = $description.get_string('underline', 'theme_educard');
    }
    $default = get_string('buttonlink', 'theme_educard');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}
