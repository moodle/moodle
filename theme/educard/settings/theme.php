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
 * Educard theme settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Theme settings.
$page = new admin_settingpage('theme_educard_theme', get_string('themesettings', 'theme_educard'));
// Theme font @import .
$name = 'theme_educard/fontimport';
$title = get_string('fontimport', 'theme_educard');
$description = get_string('fontimportdesc', 'theme_educard');
$default = "@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');";
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Theme font family.
$name = 'theme_educard/fontfamily';
$title = get_string('fontfamily', 'theme_educard');
$description = get_string('fontfamilydesc', 'theme_educard');
$default = "font-family: 'Poppins', sans-serif;";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT, 60);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Back color.
$name = 'theme_educard/backcolor';
$title = get_string('backcolor', 'theme_educard');
$description = get_string('backcolor_desc', 'theme_educard');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Login page position select.
$name = 'theme_educard/loginposition';
$title = get_string('loginposition', 'theme_educard');
$description = get_string('loginpositiondesc', 'theme_educard');
$default = "center";
$options = [
    'Center' => 'Center',
    'Left' => 'Left',
    'Right' => 'Right',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Dashboard footer select.
$name = 'theme_educard/footerselect';
$title = get_string('footerselect', 'theme_educard');
$description = get_string('footerselectdesc', 'theme_educard');
$default = "3";
$options = [
    '1' => 'Moodle footer',
    '2' => 'Frontpage footer',
    '3' => 'Social media footer',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Theme custom block key.
$name = 'theme_educard/customblockkey';
$title = get_string('customblockkey', 'theme_educard');
$description = get_string('customblockkeydesc', 'theme_educard');
$default = "";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$settings->add($page);
