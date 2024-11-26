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
 * Login page settings
 *
 * @package    theme_adaptable
 * @copyright  2015-2019 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2019 Fernando Acedo (3-bits.com)
 * @copyright  2017-2019 Manoj Solanki (Coventry University)
 * @copyright  2019 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
defined('MOODLE_INTERNAL') || die;

// Login page heading.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_login', get_string('loginsettings', 'theme_adaptable'), true);

    $page->add(new admin_setting_heading(
        'theme_adaptable_login',
        get_string('loginsettingsheading', 'theme_adaptable'),
        format_text(get_string('logindesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Login page background image.
    $name = 'theme_adaptable/loginbgimage';
    $title = get_string('loginbgimage', 'theme_adaptable');
    $description = get_string('loginbgimagedesc', 'theme_adaptable');
    $setting = new \theme_adaptable\admin_setting_configstoredfiles(
        $name, $title, $description, 'loginbgimage',
        ['accepted_types' => '*.jpg,*.jpeg,*.png', 'maxfiles' => 1]
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Login page background style.
    $name = 'theme_adaptable/loginbgstyle';
    $title = get_string('loginbgstyle', 'theme_adaptable');
    $description = get_string('loginbgstyledesc', 'theme_adaptable');
    $default = 'cover';
    $setting = new admin_setting_configselect(
        $name,
        $title,
        $description,
        $default,
        [
            'cover' => get_string('stylecover', 'theme_adaptable'),
            'stretch' => get_string('stylestretch', 'theme_adaptable'),
        ]
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Login page background opacity.
    $opactitychoices = [
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
        '1.0' => '1.0',
    ];

    $name = 'theme_adaptable/loginbgopacity';
    $title = get_string('loginbgopacity', 'theme_adaptable');
    $description = get_string('loginbgopacitydesc', 'theme_adaptable');
    $default = '0.8';
    $setting = new admin_setting_configselect($name, $title, $description, $default, $opactitychoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Login page header.
    $name = 'theme_adaptable/loginheader';
    $title = get_string('loginheader', 'theme_adaptable');
    $description = get_string('loginheaderdesc', 'theme_adaptable');
    $radchoices = [
        0 => get_string('hide', 'theme_adaptable'),
        1 => get_string('show', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 0, $radchoices);
    $page->add($setting);

    // Login page footer.
    $name = 'theme_adaptable/loginfooter';
    $title = get_string('loginfooter', 'theme_adaptable');
    $description = get_string('loginfooterdesc', 'theme_adaptable');
    $radchoices = [
        0 => get_string('hide', 'theme_adaptable'),
        1 => get_string('show', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 0, $radchoices);
    $page->add($setting);

    // Top text.
    $name = 'theme_adaptable/logintextboxtop';
    $title = get_string('logintextboxtop', 'theme_adaptable');
    $description = get_string('logintextboxtopdesc', 'theme_adaptable');
    $default = '';
    $setting = new adaptable_setting_confightmleditor($name, $title, $description, $default);
    $page->add($setting);

    // Bottom text.
    $name = 'theme_adaptable/logintextboxbottom';
    $title = get_string('logintextboxbottom', 'theme_adaptable');
    $description = get_string('logintextboxbottomdesc', 'theme_adaptable');
    $default = '';
    $setting = new adaptable_setting_confightmleditor($name, $title, $description, $default);
    $page->add($setting);

    $asettings->add($page);
}
