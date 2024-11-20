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

// Frontpage Blocks.
$page = new admin_settingpage(
    'theme_pimenko_regions_settings',
    get_string(
        'frontpage',
        'theme_pimenko'
    ),
    'theme/pimenko:configure'
);

$page->add(new admin_setting_heading('slidersettings', get_string('slidersettings', 'theme_pimenko'),
    get_string('slidersettings_desc', 'theme_pimenko')));

$setting = new theme_pimenko_simple_theme_settings(
    $page,
    'theme_pimenko',
    'settings:frontslider:'
);

$setting->add_checkbox('enablecarousel');

$range = [];
for ($i = 1; $i <= 11; $i++) {
    $range[$i] = $i;
}
$setting->add_select(
    'slideimagenr',
    1,
    $range
);
$config = get_config('theme_pimenko');
$imagenr = 0;
if (!empty($config->slideimagenr)) {
    $imagenr = $config->slideimagenr;
}
for ($i = 1; $i <= $imagenr; $i++) {
    $setting->add_files(
        'slideimage',
        $i
    );
    $setting->add_textareas(
        'slidecaption',
        $i
    );
}

$page->add(new admin_setting_heading('frontpagecontentsettings', get_string('frontpagecontentsettings', 'theme_pimenko'),
    get_string('frontpagecontentsettings_desc', 'theme_pimenko')));

$setting = new theme_pimenko_simple_theme_settings(
    $page,
    'theme_pimenko',
    'settings:regions:'
);

$bootstrap12 = [
    '0-0-0-0' => 'disabled',
    '12-0-0-0' => '1',
    '6-6-0-0' => '6 + 6',
    '4-4-4-0' => '4 + 4 + 4',
    '3-3-3-3' => '3 + 3 + 3 + 3',
    '6-3-3-0' => '6 + 3 + 3',
    '3-3-6-0' => '3 + 3 + 6',
    '3-6-3-0' => '3 + 6 + 3',
    '4-8-0-0' => '4 + 8',
    '8-4-0-0' => '8 + 4',
    '3-9-0-0' => '3 + 9',
    '9-3-0-0' => '9 + 3',
];
$bootstrap12defaults = [
    '3-3-3-3',
    '4-4-4-0',
    '3-3-3-3',
    '0-0-0-0',
    '0-0-0-0',
    '0-0-0-0',
    '0-0-0-0',
    '0-0-0-0',
    '0-0-0-0',
    '0-0-0-0'
];

$setting->add_checkbox('frontpageblocksettingscription');

for ($i = 1; $i <= 8; $i++) {
    $setting->add_selects(
        'blockrow',
        $bootstrap12defaults[$i - 1],
        $bootstrap12,
        $i
    );
    $setting->add_colourpickers(
        'blockregionrowbackgroundcolor',
        $i
    );
    $setting->add_colourpickers(
        'blockregionrowtextcolor',
        $i
    );
    $setting->add_colourpickers(
        'blockregionrowlinkcolor',
        $i
    );
    $setting->add_colourpickers(
        'blockregionrowlinkhovercolor',
        $i
    );
}

$page->add(new admin_setting_heading('frontpagecardsettings', get_string('frontpagecardsettings', 'theme_pimenko'),
    get_string('frontpagecardsettings_desc', 'theme_pimenko')));

$setting = new theme_pimenko_simple_theme_settings(
    $page,
    'theme_pimenko',
    'settings:frontcoursecard:'
);

$setting->add_checkbox('showcustomfields');
$setting->add_checkbox('showcontacts');
$setting->add_checkbox('showstartdate');


$settings->add($page);
