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
 * @package   theme_noname
 * @copyright 2016 Ryan Wyllie
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_noname_admin_settingspage_tabs('themesettingnoname', get_string('configtitle', 'theme_noname'));
    $page = new admin_settingpage('theme_noname_general', get_string('generalsettings', 'theme_noname'));

    // Preset.
    $name = 'theme_noname/preset';
    $title = get_string('preset', 'theme_noname');
    $description = get_string('preset_desc', 'theme_noname');
    $choices = [
        // A file named 'preset-' . key . '.scss' is expected.
        'default' => get_string('presetdefault', 'theme_noname')
    ];
    $default = 'default';
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $body-color.
    $name = 'theme_noname/brandcolor';
    $title = get_string('brandcolor', 'theme_noname');
    $description = get_string('brandcolor_desc', 'theme_noname');
    $default = '#373A3C';   // Straight from bootstrap variables.
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, null, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Must add the page after definiting all the settings!
    $settings->add($page);

    // Advanced settings.
    $page = new admin_settingpage('theme_noname_advanced', get_string('advancedsettings', 'theme_noname'));

    // Raw SCSS for before the content.
    $setting = new theme_noname_admin_setting_scss_variables('theme_noname/scss_variables',
        get_string('scssvariables', 'theme_noname'), get_string('scssvariables_desc', 'theme_noname'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS for after the content.
    $setting = new admin_setting_configtextarea('theme_noname/scss', get_string('rawscss', 'theme_noname'),
        get_string('rawscss_desc', 'theme_noname'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
