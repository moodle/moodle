<?php
// This file is part of the Pimenko theme for Moodle
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
 * Theme Pimenko settings login file.
 *
 * @package    theme_pimenko
 * @copyright  Pimenko 2020
 * @author     Sylvain Revenu - Pimenko 2020 <contact@pimenko.com> <pimenko.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Some parts there are from 'adaptable' theme.
$page = new admin_settingpage('theme_pimenko_login', get_string('loginsettings', 'theme_pimenko'));

$page->add(new admin_setting_heading('optionlogint', get_string('loginsettings', 'theme_pimenko'),
    ''));

$setting = new theme_pimenko_simple_theme_settings(
    $page,
    'theme_pimenko',
    'settings:loginsettings:'
);
$setting->add_checkbox('vanillalogintemplate');

// Login page background image.
$name = 'theme_pimenko/loginbgimage';
$title = get_string('loginbgimage', 'theme_pimenko');
$description = get_string('loginbgimagedesc', 'theme_pimenko');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbgimage');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Login page background style.
$name = 'theme_pimenko/loginbgstyle';
$title = get_string('loginbgstyle', 'theme_pimenko');
$description = get_string('loginbgstyledesc', 'theme_pimenko');
$default = 'cover';
$setting = new admin_setting_configselect($name, $title, $description, $default,
        array(
                'cover' => get_string('stylecover', 'theme_pimenko'),
                'stretch' => get_string('stylestretch', 'theme_pimenko')
        )
);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Login page background opacity.
$opactitychoices = array(
        '0.0' => '0.0',
        '0.1' => '0.1',
        '0.2' => '0.2',
        '0.3' => '0.3',
        '0.4' => '0.4',
        '0.5' => '0.5',
        '0.6' => '0.6',
        '0.7' => '0.7',
        '0.8' => '0.8',
        '0.9' => '0.9',
        '1.0' => '1.0'
);

$name = 'theme_pimenko/loginbgopacity';
$title = get_string('loginbgopacity', 'theme_pimenko');
$description = get_string('loginbgopacitydesc', 'theme_pimenko');
$default = '0.8';
$setting = new admin_setting_configselect($name, $title, $description, $default, $opactitychoices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$page->add(new admin_setting_heading('optionloginhtmlcontent', get_string('optionloginhtmlcontent', 'theme_pimenko'),
    get_string('optionloginhtmlcontentdesc', 'theme_pimenko')));

// Top text.
$name = 'theme_pimenko/logintextboxtop';
$title = get_string('logintextboxtop', 'theme_pimenko');
$description = get_string('logintextboxtopdesc', 'theme_pimenko');
$default = '';
$setting = new theme_pimenko_admin_setting_confightmleditor($name, $title, $description, $default);
$page->add($setting);

// Bottom text.
$name = 'theme_pimenko/logintextboxbottom';
$title = get_string('logintextboxbottom', 'theme_pimenko');
$description = get_string('logintextboxbottomdesc', 'theme_pimenko');
$setting = new theme_pimenko_admin_setting_confightmleditor($name, $title, $description, $default);
$page->add($setting);

// HTML block content will appear at the top of the left block.
$name = 'theme_pimenko/leftblockloginhtmlcontent';
$title = get_string('leftblockloginhtmlcontent', 'theme_pimenko');
$description = get_string('leftblockloginhtmlcontentdesc', 'theme_pimenko');
$setting = new theme_pimenko_admin_setting_confightmleditor($name, $title, $description, $default);
$page->add($setting);

// HTML block content will appear at the top of the right block.
$name = 'theme_pimenko/rightblockloginhtmlcontent';
$title = get_string('rightblockloginhtmlcontent', 'theme_pimenko');
$description = get_string('rightblockloginhtmlcontentdesc', 'theme_pimenko');
$setting = new theme_pimenko_admin_setting_confightmleditor($name, $title, $description, $default);
$page->add($setting);

// Option to control displaying course title under image.
$name = 'theme_pimenko/hidemanuelauth';
$title = get_string(
    'hidemanuelauth', // Updated option name
    'theme_pimenko'
);
$description = get_string(
    'hidemanuelauth_desc', // Corresponding description
    'theme_pimenko'
);
$setting = new admin_setting_configcheckbox(
    $name,
    $title,
    $description,
    '0'
);
$page->add($setting);

$settings->add($page);
