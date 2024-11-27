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
 * Educard frontpage settings.
 *
 * @package   theme_educard
 * @copyright 2023 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$page = new admin_settingpage('theme_educard_frontpage', get_string('frontpageeducard', 'theme_educard'));
// Frontpage design select.
$page->add(new admin_setting_heading('theme_educard_frontpagehead', get_string('frontpageheading', 'theme_educard'),
format_text(get_string('frontpagedesc', 'theme_educard'), FORMAT_MARKDOWN)));
$name = 'theme_educard/frontpagechoice';
$title = get_string('frontpagechoice', 'theme_educard');
$description = get_string('frontpagechoicedesc', 'theme_educard');
$default = '0';
$options = [];
$options = [
    '0' => 'None',
    '1' => '1',
    '2' => '2',
    '3' => '3',
    '4' => '4',
    '5' => '5',
    '6' => '6',
    '7' => '7',
    '8' => '8',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Frontpage color select.
$name = 'theme_educard/frontpagecolor';
$title = get_string('frontpagecolor', 'theme_educard');
$description = get_string('frontpagecolordesc', 'theme_educard');
$default = '#fa4251';
$options = [
    '#4272d7' => '1',
    '#f98012' => '2',
    '#fa4251' => '3',
    '#c45e28' => '4',
    '#63c76a' => '5',
    '#024E64' => '6',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Frontpage color palet.
$name = 'theme_educard/sitecolor';
$title = get_string('sitecolor', 'theme_educard');
$description = get_string('sitecolor_desc', 'theme_educard');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Frontpage color palet secondary.
$name = 'theme_educard/sitecolor2';
$title = get_string('sitecolor2', 'theme_educard');
$description = get_string('sitecolor2_desc', 'theme_educard');
$default = "#024E64";
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page block images folder path.
$name = 'theme_educard/frontpageimglink';
$title = get_string('frontpageimglink', 'theme_educard');
$description = get_string('frontpageimglinkdesc', 'theme_educard');
$default = 'https://themesalmond.com/image/';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL, 60);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page block linear gradient 1.
$name = 'theme_educard/frontpagegradient1';
$title = get_string('frontpagegradient1', 'theme_educard');
$description = get_string('frontpagegradient1desc', 'theme_educard');
$default = 'linear-gradient(rgba(255,255,255,0.8), rgba(255,255,255,0.9))';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '3');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page block linear gradient 2.
$name = 'theme_educard/frontpagegradient2';
$title = get_string('frontpagegradient2', 'theme_educard');
$description = get_string('frontpagegradient2desc', 'theme_educard');
$default = 'linear-gradient(rgba(255, 255, 255, 0.3), rgba(var( --theme-color-rgba ), 0.9), rgba(255, 255, 255, 0.3))';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '3');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Activate the front page color palette.
$name = 'theme_educard/colorpalette';
$title = get_string('colorpalette', 'theme_educard');
$description = get_string('colorpalettedesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable the announcements bar.
$name = 'theme_educard/announcementsbar';
$title = get_string('announcementsbar', 'theme_educard');
$description = get_string('announcementsbardesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Announcements count.
$name = 'theme_educard/announcementscount';
$title = get_string('announcementscount', 'theme_educard');
$description = get_string('announcementscountdesc', 'theme_educard');
$default = 4;
$options = [];
for ($i = 1; $i <= 7; $i++) {
    $options[$i + 3] = $i + 3;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Frontpage heading select.
$page->add(new admin_setting_heading('theme_educard_frontpagenav', get_string('frontpagenav', 'theme_educard'),
format_text(get_string('frontpagenavdesc', 'theme_educard'), FORMAT_MARKDOWN)));
$name = 'theme_educard/frontpagenavchoice';
$title = get_string('frontpagenavchoice', 'theme_educard');
$description = get_string('frontpagenavchoicedesc', 'theme_educard');
$default = 2;
$options = [];
for ($i = 1; $i <= 4; $i++) {
    $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Navbar logo background color.
$name = 'theme_educard/navbarlogobackcolor';
$title = get_string('navbarlogobackcolor', 'theme_educard');
$description = get_string('navbarlogobackcolor_desc', 'theme_educard');
$default = "";
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Frontpage header logo select.
$name = 'theme_educard/headerlogo';
$title = get_string('headerlogo', 'theme_educard');
$description = get_string('headerlogodesc', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$default = "Logo";
$options = [
    'None' => 'None',
    'Logo' => 'Logo',
    'Compact logo' => 'Compact logo',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Navbar back color light mode.
$name = 'theme_educard/navbarcolor';
$title = get_string('navbarcolor', 'theme_educard');
$description = get_string('navbarcolor_desc', 'theme_educard');
$default = "";
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Navbar link color light mode.
$name = 'theme_educard/navbarlinkcolor';
$title = get_string('navbarlinkcolor', 'theme_educard');
$description = get_string('navbarlinkcolor_desc', 'theme_educard');
$default = "";
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Navbar link hover color light mode.
$name = 'theme_educard/navbarlinkhovercolor';
$title = get_string('navbarlinkhovercolor', 'theme_educard');
$description = get_string('navbarlinkhovercolor_desc', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$default = "";
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Navbar back color dark mode.
$name = 'theme_educard/navbarcolordark';
$title = get_string('navbarcolordark', 'theme_educard');
$description = get_string('navbarcolordark_desc', 'theme_educard');
$default = "";
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Navbar link color dark mode.
$name = 'theme_educard/navbarlinkcolordark';
$title = get_string('navbarlinkcolordark', 'theme_educard');
$description = get_string('navbarlinkcolordark_desc', 'theme_educard');
$default = "";
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Navbar link hover color dark mode.
$name = 'theme_educard/navbarlinkhovercolordark';
$title = get_string('navbarlinkhovercolordark', 'theme_educard');
$description = get_string('navbarlinkhovercolordark_desc', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$default = "";
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Frontpage nav link area.
$name = 'theme_educard/frontpagenavlink';
$title = get_string('frontpagenavlink', 'theme_educard');
$description = get_string('frontpagenavlinkdesc', 'theme_educard');
$default = get_string('frontpagenavlinkdefault', 'theme_educard');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '6');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Select the blocks to be displayed on the Front Page.
$page->add(new admin_setting_heading('theme_educard_frontpage1', get_string('frontpageheading1', 'theme_educard'),
format_text(get_string('frontpageheadingdesc1', 'theme_educard'), FORMAT_MARKDOWN)));
// Section 1.
$name = 'theme_educard/frontpagesection1_1';
$title = get_string('frontpagesection1_1', 'theme_educard');
$description = get_string('frontpagesectiondesc1_1', 'theme_educard');
$default = '09-3';
// Used in all combo boxes!.
$options = theme_educard_section();
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show as slide (if any).
$name = 'theme_educard/slidesection1_1';
$title = get_string('slidesection1_1', 'theme_educard');
$description = get_string('slidesectiondesc1_1', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Section 2.
$name = 'theme_educard/frontpagesection1_2';
$title = get_string('frontpagesection1_2', 'theme_educard');
$description = get_string('frontpagesectiondesc1_2', 'theme_educard');
$default = '05-3';
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show as slide (if any).
$name = 'theme_educard/slidesection1_2';
$title = get_string('slidesection1_2', 'theme_educard');
$description = get_string('slidesectiondesc1_2', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Section 3.
$name = 'theme_educard/frontpagesection1_3';
$title = get_string('frontpagesection1_3', 'theme_educard');
$description = get_string('frontpagesectiondesc1_3', 'theme_educard');
$default = '07-4';
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show as slide (if any).
$name = 'theme_educard/slidesection1_3';
$title = get_string('slidesection1_3', 'theme_educard');
$description = get_string('slidesectiondesc1_3', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Section 4.
$name = 'theme_educard/frontpagesection1_4';
$title = get_string('frontpagesection1_4', 'theme_educard');
$description = get_string('frontpagesectiondesc1_4', 'theme_educard');
$default = '02-4';
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show as slide (if any).
$name = 'theme_educard/slidesection1_4';
$title = get_string('slidesection1_4', 'theme_educard');
$description = get_string('slidesectiondesc1_4', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Section 5.
$name = 'theme_educard/frontpagesection1_5';
$title = get_string('frontpagesection1_5', 'theme_educard');
$description = get_string('frontpagesectiondesc1_5', 'theme_educard');
$default = '08-4';
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show as slide (if any).
$name = 'theme_educard/slidesection1_5';
$title = get_string('slidesection1_5', 'theme_educard');
$description = get_string('slidesectiondesc1_5', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Section 6.
$name = 'theme_educard/frontpagesection1_6';
$title = get_string('frontpagesection1_6', 'theme_educard');
$description = get_string('frontpagesectiondesc1_6', 'theme_educard');
$default = '04-2';
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show as slide (if any).
$name = 'theme_educard/slidesection1_6';
$title = get_string('slidesection1_6', 'theme_educard');
$description = get_string('slidesectiondesc1_6', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Section 7.
$name = 'theme_educard/frontpagesection1_7';
$title = get_string('frontpagesection1_7', 'theme_educard');
$description = get_string('frontpagesectiondesc1_7', 'theme_educard');
$default = '03-1';
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show as slide (if any).
$name = 'theme_educard/slidesection1_7';
$title = get_string('slidesection1_7', 'theme_educard');
$description = get_string('slidesectiondesc1_7', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Section 8.
$name = 'theme_educard/frontpagesection1_8';
$title = get_string('frontpagesection1_8', 'theme_educard');
$description = get_string('frontpagesectiondesc1_8', 'theme_educard');
$default = '11-3';
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show as slide (if any).
$name = 'theme_educard/slidesection1_8';
$title = get_string('slidesection1_8', 'theme_educard');
$description = get_string('slidesectiondesc1_8', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Section 9.
$name = 'theme_educard/frontpagesection1_9';
$title = get_string('frontpagesection1_9', 'theme_educard');
$description = get_string('frontpagesectiondesc1_9', 'theme_educard');
$default = '10-3';
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show as slide (if any).
$name = 'theme_educard/slidesection1_9';
$title = get_string('slidesection1_9', 'theme_educard');
$description = get_string('slidesectiondesc1_9', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Section 10.
$name = 'theme_educard/frontpagesection1_10';
$title = get_string('frontpagesection1_10', 'theme_educard');
$description = get_string('frontpagesectiondesc1_10', 'theme_educard');
$default = '06-1';
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Show as slide (if any).
$name = 'theme_educard/slidesection1_10';
$title = get_string('slidesection1_10', 'theme_educard');
$description = get_string('slidesectiondesc1_10', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
$page->add(new admin_setting_heading('theme_educard_frontpageend', get_string('frontpageend', 'theme_educard'),
format_text(get_string('frontpageenddesc', 'theme_educard'), FORMAT_MARKDOWN)));
$settings->add($page);
