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
 * Theme Pimenko settings file.
 *
 * @package    theme_pimenko
 * @copyright  Pimenko 2020
 * @author     Sylvain Revenu - Pimenko 2020 <contact@pimenko.com> <pimenko.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/libs/theme_pimenko_admin_setting_confightmleditor.php');
require_once(__DIR__ . "/libs/theme_pimenko_simple_theme_settings.php");

// This is used for performance, we don't need to know about these settings on every page in Moodle, only when
// we are looking at the admin settings pages.
if ($ADMIN->fulltree) {

    // Boost provides a nice setting page which splits settings onto separate tabs. We want to use it here.
    $settings =
        new theme_boost_admin_settingspage_tabs('themesettingpimenko', get_string('configtitle', 'theme_pimenko'));

    // Each page is a tab - the first is the "General" tab.
    $page = new admin_settingpage('theme_pimenko_general', get_string('generalsettings', 'theme_pimenko'));

    // Replicate the preset setting from boost.
    $name = 'theme_pimenko/preset';
    $title = get_string('preset', 'theme_pimenko');
    $description = get_string('preset_desc', 'theme_pimenko');
    $default = 'default.scss';

    // We list files in our own file area to add to the drop down. We will provide our own function to
    // load all the presets from the correct paths.
    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_pimenko', 'preset', 0, 'itemid, filepath, filename', false);

    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }
    // These are the built in presets from Boost.
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files setting.
    $name = 'theme_pimenko/presetfiles';
    $title = get_string('presetfiles', 'theme_pimenko');
    $description = get_string('presetfiles_desc', 'theme_pimenko');

    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
        array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Variable $brand-color.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_pimenko/brandcolor';
    $title = get_string('brandcolor', 'theme_pimenko');
    $description = get_string('brandcolor_desc', 'theme_pimenko');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Default color for button.
    $name = 'theme_pimenko/brandcolorbutton';
    $title = get_string('brandcolorbutton', 'theme_pimenko');
    $description = get_string('brandcolorbuttondesc', 'theme_pimenko');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Default text color for button.
    $name = 'theme_pimenko/brandcolortextbutton';
    $title = get_string('brandcolortextbutton', 'theme_pimenko');
    $description = get_string('brandcolortextbuttondesc', 'theme_pimenko');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Site Favicon.
    $name = 'theme_pimenko/favicon';
    $title = get_string('favicon', 'theme_pimenko');
    $description = get_string('favicondesc', 'theme_pimenko');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'favicon');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new theme_pimenko_simple_theme_settings(
        $page,
        'theme_pimenko',
        'settings:font:'
    );
    $setting->add_text(
        'googlefont',
        'Verdana'
    );

    // Background image setting.
    $name = 'theme_pimenko/backgroundimage';
    $title = get_string('backgroundimage', 'theme_pimenko');
    $description = get_string('backgroundimage_desc', 'theme_pimenko');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Must add the page after definiting all the settings!
    $settings->add($page);

    // Advanced settings.
    $page = new admin_settingpage('theme_pimenko_advanced', get_string('advancedsettings', 'theme_pimenko'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_configtextarea('theme_pimenko/scsspre',
        get_string('rawscsspre', 'theme_pimenko'), get_string('rawscsspre_desc', 'theme_pimenko'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_configtextarea('theme_pimenko/scss', get_string('rawscss', 'theme_pimenko'),
        get_string('rawscss_desc', 'theme_pimenko'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Unaddable blocks.
    // Blocks to be excluded when this theme is enabled in the "Add a block" list: Administration, Navigation, Courses and
    // Section links.
    $default = 'navigation,settings,course_list,section_links';
    $setting = new admin_setting_configtext('theme_pimenko/unaddableblocks',
        get_string('unaddableblocks', 'theme_pimenko'), get_string('unaddableblocks_desc', 'theme_pimenko'), $default, PARAM_TEXT);
    $page->add($setting);

    // SCSS for H5P.
    $name        = 'theme_pimenko/h5pcss';
    $title       = get_string(
        'h5pcss',
        'theme_pimenko'
    );
    $description = get_string(
        'h5pcss_desc',
        'theme_pimenko'
    );
    $setting = new admin_setting_configstoredfile(
        $name,
        $title,
        $description,
        'h5pcss',
        0,
        [
            'maxfiles'       => 1,
            'accepted_types' => [ '.css' ]
        ]
    );
    $page->add($setting);
    $setting->set_updatedcallback('theme_reset_all_caches');

    $settings->add($page);

    // Login settings.
    include_once(dirname(__FILE__) . '/settings/pimenkofeature.php');
    include_once(dirname(__FILE__) . '/settings/frontpage.php');
    include_once(dirname(__FILE__) . '/settings/login.php');
    include_once(dirname(__FILE__) . '/settings/navbar.php');
    include_once(dirname(__FILE__) . '/settings/footer.php');
    include_once(dirname(__FILE__) . '/settings/contact.php');
}
