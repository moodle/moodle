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
 * Educard block 3 settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Block 03 info.
$name = 'theme_educard/block03info';
$heading = get_string('block03info', 'theme_educard');
$information = get_string('block03infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable block 03 settings.
$name = 'theme_educard/block03enabled';
$title = get_string('block03enabled', 'theme_educard');
$description = get_string('block03enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 3 headline.
$name = 'theme_educard/block03headline';
$title = get_string('block03headline', 'theme_educard');
$description = get_string('block03headlinedesc', 'theme_educard');
$default = get_string('block03headlinedefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 3 header.
$name = 'theme_educard/block03header';
$title = get_string('block03header', 'theme_educard');
$description = get_string('block03headerdesc', 'theme_educard');
$default = get_string('block03headerdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 3 main title.
$name = 'theme_educard/block03maintitle';
$title = get_string('block03maintitle', 'theme_educard');
$description = get_string('block03maintitledesc', 'theme_educard');
$default = get_string('block03maintitledefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 03 background img.
$name = 'theme_educard/block03bgimg';
$title = get_string('block03bgimg', 'theme_educard');
$description = get_string('block03bgimgdesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_URL, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Linear Gradient enabled-disabled.
$name = 'theme_educard/block03gradienton';
$title = get_string('block03gradienton', 'theme_educard');
$description = get_string('block03gradientondesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 03 icon list.
$name = 'theme_educard/block03icon';
$title = get_string('block03icon', 'theme_educard');
$description = get_string('block03icondesc', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$default = get_string('block03icondefault', 'theme_educard');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 03 general settings END.
// ------------------------------------------------------------------------------------.
for ($i = 1; $i <= 6; $i++) {
    // Block 03 title.
    $name = 'theme_educard/block03title'.$i;
    $title = get_string('block03title', 'theme_educard', ['block' => $i]);
    $description = get_string('block03titledesc', 'theme_educard');
    $default = 'Top Investment Advisors';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 03 caption.
    $name = 'theme_educard/block03caption'.$i;
    $title = get_string('block03caption', 'theme_educard', ['block' => $i]);
    $description = get_string('block03captiondesc', 'theme_educard');
    $default = get_string('block03captiondefault', 'theme_educard');
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Block 03 link.
    $name = 'theme_educard/block03link'.$i;
    $title = get_string('block03link', 'theme_educard', ['block' => $i]);
    $description = get_string('block03linkdesc', 'theme_educard');
    if ($i != 6) {
        $description = $description.get_string('underline', 'theme_educard');
    }
    $default = get_string('buttonlink', 'theme_educard');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}
