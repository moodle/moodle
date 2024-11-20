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
 * Theme Pimenko settings footer file.
 *
 * @package    theme_pimenko
 * @copyright  Pimenko 2020
 * @author     Sylvain Revenu - Pimenko 2020 <contact@pimenko.com> <pimenko.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$page = new admin_settingpage('theme_pimenko_footer', get_string('footersettings', 'theme_pimenko'));

$page->add(new admin_setting_heading('footersettings', get_string('footersettings', 'theme_pimenko'),
    ''));

// Footer color.
$name          = 'theme_pimenko/footercolor';
$title         = get_string(
        'footercolor',
        'theme_pimenko'
);
$description   = get_string(
        'footercolordesc',
        'theme_pimenko'
);
$previewconfig = null;
$setting       = new admin_setting_configcolourpicker(
        $name,
        $title,
        $description,
        '',
        $previewconfig
);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Footer text color.
$name          = 'theme_pimenko/footertextcolor';
$title         = get_string(
        'footertextcolor',
        'theme_pimenko'
);
$description   = get_string(
        'footertextcolordesc',
        'theme_pimenko'
);
$previewconfig = null;
$setting       = new admin_setting_configcolourpicker(
        $name,
        $title,
        $description,
        '',
        $previewconfig
);
$setting->set_updatedcallback('theme_reset_all_caches');

$page->add($setting);

// Footer hoover text color.
$name          = 'theme_pimenko/hooverfootercolor';
$title         = get_string(
    'hooverfootercolor',
    'theme_pimenko'
);
$description   = get_string(
    'hooverfootercolordesc',
    'theme_pimenko'
);
$previewconfig = null;
$setting       = new admin_setting_configcolourpicker(
    $name,
    $title,
    $description,
    '',
    $previewconfig
);
$setting->set_updatedcallback('theme_reset_all_caches');

$page->add($setting);

$setting = new theme_pimenko_simple_theme_settings(
        $page,
        'theme_pimenko',
        'settings:footer:'
);

for ($i = 1; $i <= 4; $i++) {
    $setting->add_headings(
            'footercolumn',
            $i
    );
    $setting->add_texts(
            'footerheading',
            $i
    );
    $setting->add_superhtmleditors(
            'footertext',
            $i
    );
}
$settings->add($page);
