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
 * @package   theme_greentheme
 * @copyright 2016 Ryan Wyllie
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_greentheme_admin_settingspage_tabs('themesettinggreentheme', get_string('configtitle', 'theme_greentheme'));
    $page = new admin_settingpage('theme_greentheme_general', get_string('generalsettings', 'theme_greentheme'));

    // Unaddable blocks.
    // Blocks to be excluded when this theme is enabled in the "Add a block" list: Administration, Navigation, Courses and
    // Section links.
    $default = 'navigation,settings,course_list,section_links';
    $setting = new admin_setting_configtext('theme_greentheme/unaddableblocks',
        get_string('unaddableblocks', 'theme_greentheme'), get_string('unaddableblocks_desc', 'theme_greentheme'), $default, PARAM_TEXT);
    $page->add($setting);

    // Preset.
    $name = 'theme_greentheme/preset';
    $title = get_string('preset', 'theme_greentheme');
    $description = get_string('preset_desc', 'theme_greentheme');
    $default = 'default.scss';

    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_greentheme', 'preset', 0, 'itemid, filepath, filename', false);

    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }
    // These are the built in presets.
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configthemepreset($name, $title, $description, $default, $choices, 'greentheme');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files setting.
    $name = 'theme_greentheme/presetfiles';
    $title = get_string('presetfiles','theme_greentheme');
    $description = get_string('presetfiles_desc', 'theme_greentheme');

    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
        array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Background image setting.
    $name = 'theme_greentheme/backgroundimage';
    $title = get_string('backgroundimage', 'theme_greentheme');
    $description = get_string('backgroundimage_desc', 'theme_greentheme');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Login Background image setting.
    $name = 'theme_greentheme/loginbackgroundimage';
    $title = get_string('loginbackgroundimage', 'theme_greentheme');
    $description = get_string('loginbackgroundimage_desc', 'theme_greentheme');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbackgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_greentheme/brandcolor';
    $title = get_string('brandcolor', 'theme_greentheme');
    $description = get_string('brandcolor_desc', 'theme_greentheme');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Must add the page after definiting all the settings!
    $settings->add($page);

    // Advanced settings.
    $page = new admin_settingpage('theme_greentheme_advanced', get_string('advancedsettings', 'theme_greentheme'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode('theme_greentheme/scsspre',
        get_string('rawscsspre', 'theme_greentheme'), get_string('rawscsspre_desc', 'theme_greentheme'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode('theme_greentheme/scss', get_string('rawscss', 'theme_greentheme'),
        get_string('rawscss_desc', 'theme_greentheme'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
