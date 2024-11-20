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
 * Admin settings configuration for general section
 *
 * @package    theme_academi
 * @copyright  2023 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// General section.
$temp = new admin_settingpage('theme_academi_header', get_string('headerheading', 'theme_academi'));

// Nav style select option.
$name = 'theme_academi/navstyle';
$title = get_string('navstyle', 'theme_academi');
$description = get_string('navstyle_desc', 'theme_academi');
$default = LOGO;
$choices = [
    LOGO => get_string('logo', 'theme_academi'),
    SITENAME => get_string('sitename', 'theme_academi'),
    LOGOANDSITENAME => get_string('logoandsitename', 'theme_academi'),
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$temp->add($setting);

// Logo file upload option.
$name = 'theme_academi/logo';
$title = get_string('logo', 'theme_academi');
$description = get_string('logodesc', 'theme_academi');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'logo');
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Favicon upload option.
$name = 'theme_academi/favicon';
$title = get_string('favicon', 'theme_academi', null, true);
$description = get_string('favicon_desc', 'theme_academi', null, true);
$setting = new admin_setting_configstoredfile($name, $title, $description, 'favicon', 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Primary pattern color select option.
$name = 'theme_academi/primarycolor';
$title = get_string('primarycolor', 'theme_academi');
$description = get_string('primarycolor_desc', 'theme_academi');
$default = "";
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Secondary pattern color select option.
$name = 'theme_academi/secondarycolor';
$title = get_string('secondarycolor', 'theme_academi');
$description = get_string('secondarycolor_desc', 'theme_academi');
$default = "";
$setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Header style select option.
$name = 'theme_academi/themestyleheader';
$title = get_string('themestyleheader', 'theme_academi');
$description = get_string('themestyleheader_desc', 'theme_academi');
$default = THEMEBASED;
$choices = [
    THEMEBASED => get_string('themebased', 'theme_academi'),
    MOODLEBASED => get_string('moodlebased', 'theme_academi'),
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$temp->add($setting);

// Select the size for site inner pages.
$name = 'theme_academi/pagesize';
$title = get_string('pagesize', 'theme_academi');
$description = get_string('pagesize_desc', 'theme_academi');
$default = '1';
$choices = [
    'container' => get_string('container', 'theme_academi'),
    'default' => get_string('moodledefault', 'theme_academi'),
    'custom' => get_string('custom', 'theme_academi'),

];
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$temp->add($setting);

// Give a custom size for site inner pages.
$name = 'theme_academi/pagesizecustomval';
$title = get_string('pagesizecustomval', 'theme_academi');
$description = get_string('pagesizecustomval_desc', 'theme_academi');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_INT);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Content font size.
$name = 'theme_academi/fontsize';
$title = get_string('fontsize', 'theme_academi');
$description = get_string('fontsize_desc', 'theme_academi');
$default = THEMEDEFAULT;
$sizes = [
    THEMEDEFAULT => get_string('default'),
    SMALL => get_string('small', 'theme_academi'),
    MEDIUM => get_string('medium', 'theme_academi'),
    LARGE => get_string('large', 'theme_academi'),
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $sizes);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Select the available course type option.
$name = 'theme_academi/availablecoursetype';
$title = get_string('availablecoursetype', 'theme_academi');
$description = get_string('availablecoursetype_desc', 'theme_academi');
$default = CAROUSEL;
$choices = [
    CAROUSEL => get_string('carousel', 'theme_academi'),
    MOODLEBASED => get_string('moodlebased', 'theme_academi'),
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$temp->add($setting);

// Select the combo list box type option.
$name = 'theme_academi/comboListboxType';
$title = get_string('comboListboxType', 'theme_academi');
$description = get_string('comboListboxType_desc', 'theme_academi');
$default = COLLAPSE;
$choices = [
    EXPAND => get_string('expand', 'theme_academi'),
    COLLAPSE => get_string('collapse', 'theme_academi'),
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$temp->add($setting);

// Background image setting.
$name = 'theme_boost/backgroundimage';
$title = get_string('backgroundimage', 'theme_boost');
$description = get_string('backgroundimage_desc', 'theme_academi');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Uploaded option for login page background image.
$name = 'theme_academi/loginbg';
$title = get_string('loginbg', 'theme_academi');
$description = get_string('loginbg_desc', 'theme_academi');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbg', 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Enable or disable option for "Back to top" option.
$name = 'theme_academi/backToTop_status';
$title = get_string('backToTop_status', 'theme_academi');
$description = get_string('backToTop_statusdesc', 'theme_academi');
$default = YES;
$choices = [
    YES => get_string('yes'),
    NO => get_string('no'),
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$temp->add($setting);

// Custom CSS file.
$name = 'theme_academi/customcss';
$title = get_string('customcss', 'theme_academi');
$description = get_string('customcssdesc', 'theme_academi');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Create theme presets heading.
$name = 'theme_academi/presetheading';
$title = get_string('presetheading', 'theme_academi', null, true);
$setting = new admin_setting_heading($name, $title, null);
$temp->add($setting);

// Replicate the preset setting from theme_boost, but use our own file area.
$name = 'theme_academi/preset';
$title = get_string('preset', 'theme_boost', null, true);
$description = get_string('preset_desc', 'theme_boost', null, true);
$default = 'default.scss';

$context = context_system::instance();
$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'theme_academi', 'preset', 0, 'itemid, filepath, filename', false);

$choices = [];
foreach ($files as $file) {
    $choices[$file->get_filename()] = $file->get_filename();
}
$choices['default.scss'] = 'Academi';
$choices['eguru'] = 'Eguru';
$choices['klass'] = 'Klass';
$choices['enlightlite'] = 'Enlightlite';

$setting = new admin_setting_configthemepreset($name, $title, $description, $default, $choices, 'academi');
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Replicate the preset files setting from theme_boost.
$name = 'theme_academi/presetfiles';
$title = get_string('presetfiles', 'theme_boost', null, true);
$description = get_string('presetfiles_desc', 'theme_boost', null, true);
$setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
        ['maxfiles' => 20, 'accepted_types' => ['.scss']]);
$temp->add($setting);
$settings->add($temp);
