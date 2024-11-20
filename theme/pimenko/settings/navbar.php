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
 * Theme Pimenko settings navbar file.
 *
 * @package    theme_pimenko
 * @copyright  Pimenko 2020
 * @author     Sylvain Revenu - Pimenko 2020 <contact@pimenko.com> <pimenko.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$page = new admin_settingpage('theme_pimenko_navbar',
    get_string('navbarsettings', 'theme_pimenko'));

$page->add(new admin_setting_heading('navbarsettings', get_string('navbarsettings', 'theme_pimenko'),
    ''));

// Site logo.
$name = 'theme_pimenko/sitelogo';
$title = get_string('sitelogo', 'theme_pimenko');
$description = get_string('sitelogodesc', 'theme_pimenko');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'sitelogo');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Header picture.
$name = 'theme_pimenko/navbarpicture';
$title = get_string('navbarpicture', 'theme_pimenko');
$description = get_string('navbarpicturedesc', 'theme_pimenko');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'navbarpicture');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Hide site name.
$name = 'theme_pimenko/hidesitename';
$title = get_string(
    'hidesitename',
    'theme_pimenko'
);
$description = get_string(
    'hidesitename_desc',
    'theme_pimenko'
);
$setting = new admin_setting_configcheckbox(
    $name,
    $title,
    $description,
    '0'
);
$page->add($setting);

// Navbar color.
$name = 'theme_pimenko/navbarcolor';
$title = get_string(
    'navbarcolor',
    'theme_pimenko'
);
$description = get_string(
    'navbarcolordesc',
    'theme_pimenko'
);
$previewconfig = null;
$setting = new admin_setting_configcolourpicker(
    $name,
    $title,
    $description,
    '',
    $previewconfig
);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Navbar text color.

$name = 'theme_pimenko/navbartextcolor';
$title = get_string(
    'navbartextcolor',
    'theme_pimenko'
);
$description = get_string(
    'navbartextcolordesc',
    'theme_pimenko'
);
$previewconfig = null;
$setting = new admin_setting_configcolourpicker(
    $name,
    $title,
    $description,
    '',
    $previewconfig
);
$setting->set_updatedcallback('theme_reset_all_caches');

$page->add($setting);

// Navbar hoover text color.
$name = 'theme_pimenko/hoovernavbarcolor';
$title = get_string(
    'hoovernavbarcolor',
    'theme_pimenko'
);
$description = get_string(
    'hoovernavbarcolordesc',
    'theme_pimenko'
);
$previewconfig = null;
$setting = new admin_setting_configcolourpicker(
    $name,
    $title,
    $description,
    '',
    $previewconfig
);
$setting->set_updatedcallback('theme_reset_all_caches');

$page->add($setting);

$page->add(new admin_setting_heading('customnavbarmenu', get_string('customnavbarmenu', 'theme_pimenko'),
    get_string('customnavbarmenu_desc', 'theme_pimenko')));

$options = [
    'excludehidden' => get_string(
        'menuheadercateg:excludehidden',
        'theme_pimenko'
    ),
    'includehidden' => get_string(
        'menuheadercateg:includehidden',
        'theme_pimenko'
    ),
    'disabled'      => get_string(
        'menuheadercateg:disabled',
        'theme_pimenko'
    )
];
$setting = new admin_setting_configselect('theme_pimenko/menuheadercateg',
    get_string('menuheadercateg', 'theme_pimenko'),
    get_string('menuheadercategdesc', 'theme_pimenko'),
'disabled',
    $options);
$page->add($setting);

// Unaddable blocks.
// Blocks to be excluded when this theme is enabled in the "Add a block" list: Administration, Navigation, Courses and
// Section links.
$default = '';
$setting = new admin_setting_configtext('theme_pimenko/removedprimarynavitems',
    get_string('removedprimarynavitems', 'theme_pimenko'),
    get_string('removedprimarynavitems_desc', 'theme_pimenko'), $default, PARAM_TEXT);
$page->add($setting);

$page->add(new admin_setting_configtextarea('custommenuitems', new lang_string('custommenuitems', 'admin'),
    new lang_string('configcustommenuitems', 'admin'), '', PARAM_RAW, '50', '10'));

$page->add(
    new admin_setting_configtextarea(
        'theme_pimenko/custommenuitemslogin',
        new lang_string('custommenuitemslogin', 'theme_pimenko'),
        new lang_string('configcustommenuitemslogin', 'theme_pimenko'),
        '',
        PARAM_RAW, '50', '10'));

$settings->add($page);
