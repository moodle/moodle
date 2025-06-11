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

defined('MOODLE_INTERNAL') || die;// Main settings.

$snapsettings = new admin_settingpage('themesnapbranding', get_string('basics', 'theme_snap'));

if (!during_initial_install() && !empty(get_site()->fullname)) {
    // Site name setting.
    $name = 'fullname';
    $title = new lang_string('fullname', 'theme_snap');
    $description = new lang_string('fullnamedesc', 'theme_snap');
    $description = '';
    $setting = new admin_setting_sitesettext($name, $title, $description, null);
    $snapsettings->add($setting);
}

// Main theme colour setting.
$name = 'theme_snap/themecolor';
$title = new lang_string('themecolor', 'theme_snap');
$description = '';
$default = '#82009E'; // Open LMS EDU Purple - To pass WCAG color contrast ratio check.
$previewconfig = null;
$setting = new \theme_snap\admin_setting_configcolorwithcontrast(
    \theme_snap\admin_setting_configcolorwithcontrast::BASICS, $name, $title, $description, $default, $previewconfig);
$setting->set_updatedcallback('theme_reset_all_caches');
$snapsettings->add($setting);

// Site description setting.
$name = 'theme_snap/subtitle';
$title = new lang_string('sitedescription', 'theme_snap');
$description = new lang_string('subtitle_desc', 'theme_snap');
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW_TRIMMED, 50);
$snapsettings->add($setting);

$name = 'theme_snap/imagesheading';
$title = new lang_string('images', 'theme_snap');
$description = '';
$setting = new admin_setting_heading($name, $title, $description);
$snapsettings->add($setting);

 // Logo file setting.
$name = 'theme_snap/logo';
$title = new lang_string('logo', 'theme_snap');
$description = new lang_string('logodesc', 'theme_snap');
$opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'logo', 0, $opts);
$setting->set_updatedcallback('theme_reset_all_caches');
$snapsettings->add($setting);


// Favicon file setting.
$name = 'theme_snap/favicon';
$title = new lang_string('favicon', 'theme_snap');
$description = new lang_string('favicondesc', 'theme_snap');
$opts = array('accepted_types' => array('.ico', '.png', '.gif'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'favicon', 0, $opts);
$setting->set_updatedcallback('theme_reset_all_caches');
$snapsettings->add($setting);

// Advanced branding heading.
$name = 'theme_snap/advancedbrandingheading';
$title = new lang_string('advancedbrandingheading', 'theme_snap');
$description = new lang_string('advancedbrandingheadingdesc', 'theme_snap');
$setting = new admin_setting_heading($name, $title, $description);
$snapsettings->add($setting);

// Heading font setting.
$name = 'theme_snap/headingfont';
$title = new lang_string('headingfont', 'theme_snap');
$description = new lang_string('headingfont_desc', 'theme_snap');
$default = '"Roboto"';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$snapsettings->add($setting);

// Custom CSS file.
$name = 'theme_snap/customcss';
$title = new lang_string('customcss', 'theme_snap');
$description = new lang_string('customcssdesc', 'theme_snap');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$snapsettings->add($setting);

$settings->add($snapsettings);
