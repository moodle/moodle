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
 * @package   theme_darktheme
 * @copyright 2016 Ryan Wyllie
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_darktheme_admin_settingspage_tabs('themesettingdarktheme', get_string('configtitle', 'theme_darktheme'));
    $page = new admin_settingpage('theme_darktheme_general', get_string('generalsettings', 'theme_darktheme'));

    // Unaddable blocks.
    // Blocks to be excluded when this theme is enabled in the "Add a block" list: Administration, Navigation, Courses and
    // Section links.
    $default = 'navigation,settings,course_list,section_links';
    $setting = new admin_setting_configtext('theme_darktheme/unaddableblocks',
        get_string('unaddableblocks', 'theme_darktheme'), get_string('unaddableblocks_desc', 'theme_darktheme'), $default, PARAM_TEXT);
    $page->add($setting);

    // Preset.
    $name = 'theme_darktheme/preset';
    $title = get_string('preset', 'theme_darktheme');
    $description = get_string('preset_desc', 'theme_darktheme');
    $default = 'default.scss';

    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_darktheme', 'preset', 0, 'itemid, filepath, filename', false);

    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }
    // These are the built in presets.
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configthemepreset($name, $title, $description, $default, $choices, 'darktheme');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files setting.
    $name = 'theme_darktheme/presetfiles';
    $title = get_string('presetfiles','theme_darktheme');
    $description = get_string('presetfiles_desc', 'theme_darktheme');

    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
        array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Background image setting.
    $name = 'theme_darktheme/backgroundimage';
    $title = get_string('backgroundimage', 'theme_darktheme');
    $description = get_string('backgroundimage_desc', 'theme_darktheme');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Login Background image setting.
    $name = 'theme_darktheme/loginbackgroundimage';
    $title = get_string('loginbackgroundimage', 'theme_darktheme');
    $description = get_string('loginbackgroundimage_desc', 'theme_darktheme');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbackgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_darktheme/brandcolor';
    $title = get_string('brandcolor', 'theme_darktheme');
    $description = get_string('brandcolor_desc', 'theme_darktheme');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Must add the page after definiting all the settings!
    $settings->add($page);

    // Advanced settings.
    $page = new admin_settingpage('theme_darktheme_advanced', get_string('advancedsettings', 'theme_darktheme'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode('theme_darktheme/scsspre',
        get_string('rawscsspre', 'theme_darktheme'), get_string('rawscsspre_desc', 'theme_darktheme'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode('theme_darktheme/scss', get_string('rawscss', 'theme_darktheme'),
        get_string('rawscss_desc', 'theme_darktheme'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
