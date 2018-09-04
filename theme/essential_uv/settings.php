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
 * essential_uv is a clean and customizable theme.
 *
 * @package     theme_essential_uv
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @copyright   2014 Gareth J Barnard, David Bezemer
 * @copyright   2013 Julian Ridden
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
$settings = null; // Unsets the default $settings object initialised by Moodle.

// Create own category and define pages.
$ADMIN->add('themes', new admin_category('theme_essential_uv', 'essential_uv'));

// Generic settings.
$essential_uvsettingsgeneric = new admin_settingpage('theme_essential_uv_generic', get_string('genericsettings', 'theme_essential_uv'));
// Initialise individual settings only if admin pages require them.
if ($ADMIN->fulltree) {
    global $CFG;
    if (file_exists("{$CFG->dirroot}/theme/essential_uv/essential_uv_admin_setting_configselect.php")) {
        require_once($CFG->dirroot . '/theme/essential_uv/essential_uv_admin_setting_configselect.php');
        require_once($CFG->dirroot . '/theme/essential_uv/essential_uv_admin_setting_configinteger.php');
        require_once($CFG->dirroot . '/theme/essential_uv/essential_uv_admin_setting_advertising.php');
    } else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/essential_uv/essential_uv_admin_setting_configselect.php")) {
        require_once($CFG->themedir . '/essential_uv/essential_uv_admin_setting_configselect.php');
        require_once($CFG->themedir . '/essential_uv/essential_uv_admin_setting_configinteger.php');
        require_once($CFG->themedir . '/essential_uv/essential_uv_admin_setting_advertising.php');
    }

    $sponsor = new moodle_url('http://moodle.org/user/profile.php?id=442195');
    $sponsor = html_writer::link($sponsor, get_string('paypal_click', 'theme_essential_uv'), array('target' => '_blank'));

    $essential_uvsettingsgeneric->add(new admin_setting_heading('theme_essential_uv_generalsponsor',
        get_string('sponsor_title', 'theme_essential_uv'),
        get_string('sponsor_desc', 'theme_essential_uv').get_string('paypal_desc', 'theme_essential_uv', array('url' => $sponsor)).
        get_string('sponsor_desc2', 'theme_essential_uv')));
    $essential_uvsettingsgeneric->add(new admin_setting_heading('theme_essential_uv_generalheading',
        get_string('generalheadingsub', 'theme_essential_uv'),
        format_text(get_string('generalheadingdesc', 'theme_essential_uv'), FORMAT_MARKDOWN)));

    // Toggle flat navigation.
    $name = 'theme_essential_uv/flatnavigation';
    $title = get_string('flatnavigation', 'theme_essential_uv');
    $description = get_string('flatnavigationdesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsgeneric->add($setting);

    // Page background image.
    $name = 'theme_essential_uv/pagebackground';
    $title = get_string('pagebackground', 'theme_essential_uv');
    $description = get_string('pagebackgrounddesc', 'theme_essential_uv');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'pagebackground');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsgeneric->add($setting);

    // Background style.
    $name = 'theme_essential_uv/pagebackgroundstyle';
    $title = get_string('pagebackgroundstyle', 'theme_essential_uv');
    $description = get_string('pagebackgroundstyledesc', 'theme_essential_uv');
    $default = 'fixed';
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default,
        array(
            'fixed' => get_string('stylefixed', 'theme_essential_uv'),
            'tiled' => get_string('styletiled', 'theme_essential_uv'),
            'stretch' => get_string('stylestretch', 'theme_essential_uv')
        )
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsgeneric->add($setting);

    // Fixed or variable width.
    $name = 'theme_essential_uv/pagewidth';
    $title = get_string('pagewidth', 'theme_essential_uv');
    $description = get_string('pagewidthdesc', 'theme_essential_uv');
    $default = 1200;
    $choices = array(
        960 => get_string('fixedwidthnarrow', 'theme_essential_uv'),
        1200 => get_string('fixedwidthnormal', 'theme_essential_uv'),
        1400 => get_string('fixedwidthwide', 'theme_essential_uv'),
        100 => get_string('variablewidth', 'theme_essential_uv'));
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsgeneric->add($setting);

    // Toggle page top blocks.
    $name = 'theme_essential_uv/pagetopblocks';
    $title = get_string('pagetopblocks', 'theme_essential_uv');
    $description = get_string('pagetopblocksdesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsgeneric->add($setting);

    // Page top blocks per row.
    $name = 'theme_essential_uv/pagetopblocksperrow';
    $title = get_string('pagetopblocksperrow', 'theme_essential_uv');
    $default = 1;
    $lower = 1;
    $upper = 4;
    $description = get_string('pagetopblocksperrowdesc', 'theme_essential_uv',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_uv_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $essential_uvsettingsgeneric->add($setting);

    // Page bottom blocks per row.
    $name = 'theme_essential_uv/pagebottomblocksperrow';
    $title = get_string('pagebottomblocksperrow', 'theme_essential_uv');
    $default = 4;
    $lower = 1;
    $upper = 4;
    $description = get_string('pagebottomblocksperrowdesc', 'theme_essential_uv',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_uv_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $essential_uvsettingsgeneric->add($setting);

    // User image border radius.
    $name = 'theme_essential_uv/userimageborderradius';
    $title = get_string('userimageborderradius', 'theme_essential_uv');
    $default = 90;
    $lower = 0;
    $upper = 90;
    $description = get_string('userimageborderradiusdesc', 'theme_essential_uv',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_uv_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsgeneric->add($setting);

    // Custom favicon.
    $name = 'theme_essential_uv/favicon';
    $title = get_string('favicon', 'theme_essential_uv');
    $description = get_string('favicondesc', 'theme_essential_uv');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'favicon');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsgeneric->add($setting);

    // Custom CSS file.
    $name = 'theme_essential_uv/customcss';
    $title = get_string('customcss', 'theme_essential_uv');
    $description = get_string('customcssdesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsgeneric->add($setting);

    $readme = new moodle_url('/theme/essential_uv/README.txt');
    $readme = html_writer::link($readme, get_string('readme_click', 'theme_essential_uv'), array('target' => '_blank'));

    $essential_uvreadme = new admin_setting_heading('theme_essential_uv_readme',
        get_string('readme_title', 'theme_essential_uv'), get_string('readme_desc', 'theme_essential_uv', array('url' => $readme)));
    $essential_uvsettingsgeneric->add($essential_uvreadme);

    $essential_uvadvert = new essential_uv_admin_setting_advertising('theme_essential_uv_advert',
        get_string('advert_heading', 'theme_essential_uv'), get_string('advert_tagline', 'theme_essential_uv'),
        'http://www.moodlebites.com/mod/page/view.php?id=3208',
        $OUTPUT->image_url('adverts/tdl1', 'theme_essential_uv'), get_string('advert_alttext', 'theme_essential_uv'));
    $essential_uvsettingsgeneric->add($essential_uvadvert);
}
$ADMIN->add('theme_essential_uv', $essential_uvsettingsgeneric);

// Feature settings.
$essential_uvsettingsfeature = new admin_settingpage('theme_essential_uv_feature', get_string('featureheading', 'theme_essential_uv'));
if ($ADMIN->fulltree) {
    global $CFG;
    if (file_exists("{$CFG->dirroot}/theme/essential_uv/essential_uv_admin_setting_configinteger.php")) {
        require_once($CFG->dirroot . '/theme/essential_uv/essential_uv_admin_setting_configinteger.php');
    } else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/essential_uv/essential_uv_admin_setting_configinteger.php")) {
        require_once($CFG->themedir . '/essential_uv/essential_uv_admin_setting_configinteger.php');
    }

    $essential_uvsettingsfeature->add(new admin_setting_heading('theme_essential_uv_feature',
        get_string('featureheadingsub', 'theme_essential_uv'),
        format_text(get_string('featuredesc', 'theme_essential_uv'), FORMAT_MARKDOWN)));

    // Course content search.
    $name = 'theme_essential_uv/coursecontentsearch';
    $title = get_string('coursecontentsearch', 'theme_essential_uv');
    $description = get_string('coursecontentsearchdesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfeature->add($setting);

    // Course content search type default.
    $name = 'theme_essential_uv/searchallcoursecontentdefault';
    $title = get_string('searchallcoursecontentdefault', 'theme_essential_uv');
    $description = get_string('searchallcoursecontentdefaultdesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $essentialsettingsfeature->add($setting);
    
    // Custom scrollbars.
    $name = 'theme_essential_uv/customscrollbars';
    $title = get_string('customscrollbars', 'theme_essential_uv');
    $description = get_string('customscrollbarsdesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfeature->add($setting);

    // Fitvids.
    $name = 'theme_essential_uv/fitvids';
    $title = get_string('fitvids', 'theme_essential_uv');
    $description = get_string('fitvidsdesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfeature->add($setting);

    // Floating submit buttons.
    $name = 'theme_essential_uv/floatingsubmitbuttons';
    $title = get_string('floatingsubmitbuttons', 'theme_essential_uv');
    $description = get_string('floatingsubmitbuttonsdesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $essential_uvsettingsfeature->add($setting);

    // Custom or standard layout.
    $name = 'theme_essential_uv/layout';
    $title = get_string('layout', 'theme_essential_uv');
    $description = get_string('layoutdesc', 'theme_essential_uv');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfeature->add($setting);

    // Course title position.
    $name = 'theme_essential_uv/coursetitleposition';
    $title = get_string('coursetitleposition', 'theme_essential_uv');
    $description = get_string('coursetitlepositiondesc', 'theme_essential_uv');
    $default = 'within';
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default,
        array(
            'above' => get_string('above', 'theme_essential_uv'),
            'within' => get_string('within', 'theme_essential_uv')
        )
    );
    $essential_uvsettingsfeature->add($setting);

    // Categories in the course breadcrumb.
    $name = 'theme_essential_uv/categoryincoursebreadcrumbfeature';
    $title = get_string('categoryincoursebreadcrumbfeature', 'theme_essential_uv');
    $description = get_string('categoryincoursebreadcrumbfeaturedesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $essential_uvsettingsfeature->add($setting);

    // Return to section.
    $name = 'theme_essential_uv/returntosectionfeature';
    $title = get_string('returntosectionfeature', 'theme_essential_uv');
    $description = get_string('returntosectionfeaturedesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $essential_uvsettingsfeature->add($setting);

    // Return to section name text limit.
    $name = 'theme_essential_uv/returntosectiontextlimitfeature';
    $title = get_string('returntosectiontextlimitfeature', 'theme_essential_uv');
    $default = 15;
    $lower = 5;
    $upper = 40;
    $description = get_string('returntosectiontextlimitfeaturedesc', 'theme_essential_uv',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_uv_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $essential_uvsettingsfeature->add($setting);

    // Login background image.
    $name = 'theme_essential_uv/loginbackground';
    $title = get_string('loginbackground', 'theme_essential_uv');
    $description = get_string('loginbackgrounddesc', 'theme_essential_uv');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbackground');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfeature->add($setting);

    // Login background style.
    $name = 'theme_essential_uv/loginbackgroundstyle';
    $title = get_string('loginbackgroundstyle', 'theme_essential_uv');
    $description = get_string('loginbackgroundstyledesc', 'theme_essential_uv');
    $default = 'cover';
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default,
        array(
            'cover' => get_string('stylecover', 'theme_essential_uv'),
            'stretch' => get_string('stylestretch', 'theme_essential_uv')
        )
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfeature->add($setting);

    $opactitychoices = array(
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
        '1.0' => '1.0'
    );

    // Overridden course title text background opacity setting.
    $name = 'theme_essential_uv/loginbackgroundopacity';
    $title = get_string('loginbackgroundopacity', 'theme_essential_uv');
    $description = get_string('loginbackgroundopacitydesc', 'theme_essential_uv');
    $default = '0.8';
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $opactitychoices);
    $essential_uvsettingsfeature->add($setting);

    $essential_uvsettingsfeature->add($essential_uvreadme);
    $essential_uvsettingsfeature->add($essential_uvadvert);
}
$ADMIN->add('theme_essential_uv', $essential_uvsettingsfeature);

// Colour settings.
$essential_uvsettingscolour = new admin_settingpage('theme_essential_uv_colour', get_string('colorheading', 'theme_essential_uv'));
if ($ADMIN->fulltree) {
    $essential_uvsettingscolour->add(new admin_setting_heading('theme_essential_uv_colour',
        get_string('colorheadingsub', 'theme_essential_uv'),
        format_text(get_string('colordesc', 'theme_essential_uv'), FORMAT_MARKDOWN)));

    // Main theme colour setting.
    $name = 'theme_essential_uv/themecolor';
    $title = get_string('themecolor', 'theme_essential_uv');
    $description = get_string('themecolordesc', 'theme_essential_uv');
    $default = '#30add1';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Main theme text colour setting.
    $name = 'theme_essential_uv/themetextcolor';
    $title = get_string('themetextcolor', 'theme_essential_uv');
    $description = get_string('themetextcolordesc', 'theme_essential_uv');
    $default = '#217a94';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Main theme link colour setting.
    $name = 'theme_essential_uv/themeurlcolor';
    $title = get_string('themeurlcolor', 'theme_essential_uv');
    $description = get_string('themeurlcolordesc', 'theme_essential_uv');
    $default = '#943b21';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Main theme hover colour setting.
    $name = 'theme_essential_uv/themehovercolor';
    $title = get_string('themehovercolor', 'theme_essential_uv');
    $description = get_string('themehovercolordesc', 'theme_essential_uv');
    $default = '#6a2a18';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Icon colour setting.
    $name = 'theme_essential_uv/themeiconcolor';
    $title = get_string('themeiconcolor', 'theme_essential_uv');
    $description = get_string('themeiconcolordesc', 'theme_essential_uv');
    $default = '#30add1';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Side-pre block background colour setting.
    $name = 'theme_essential_uv/themesidepreblockbackgroundcolour';
    $title = get_string('themesidepreblockbackgroundcolour', 'theme_essential_uv');
    $description = get_string('themesidepreblockbackgroundcolourdesc', 'theme_essential_uv');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Side-pre block text colour setting.
    $name = 'theme_essential_uv/themesidepreblocktextcolour';
    $title = get_string('themesidepreblocktextcolour', 'theme_essential_uv');
    $description = get_string('themesidepreblocktextcolourdesc', 'theme_essential_uv');
    $default = '#217a94';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Side-pre block url colour setting.
    $name = 'theme_essential_uv/themesidepreblockurlcolour';
    $title = get_string('themesidepreblockurlcolour', 'theme_essential_uv');
    $description = get_string('themesidepreblockurlcolourdesc', 'theme_essential_uv');
    $default = '#943b21';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Side-pre block url hover colour setting.
    $name = 'theme_essential_uv/themesidepreblockhovercolour';
    $title = get_string('themesidepreblockhovercolour', 'theme_essential_uv');
    $description = get_string('themesidepreblockhovercolourdesc', 'theme_essential_uv');
    $default = '#6a2a18';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Default button text colour setting.
    $name = 'theme_essential_uv/themedefaultbuttontextcolour';
    $title = get_string('themedefaultbuttontextcolour', 'theme_essential_uv');
    $description = get_string('themedefaultbuttontextcolourdesc', 'theme_essential_uv');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Default button text hover colour setting.
    $name = 'theme_essential_uv/themedefaultbuttontexthovercolour';
    $title = get_string('themedefaultbuttontexthovercolour', 'theme_essential_uv');
    $description = get_string('themedefaultbuttontexthovercolourdesc', 'theme_essential_uv');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Default button background colour setting.
    $name = 'theme_essential_uv/themedefaultbuttonbackgroundcolour';
    $title = get_string('themedefaultbuttonbackgroundcolour', 'theme_essential_uv');
    $description = get_string('themedefaultbuttonbackgroundcolourdesc', 'theme_essential_uv');
    $default = '#30add1';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Default button background hover colour setting.
    $name = 'theme_essential_uv/themedefaultbuttonbackgroundhovercolour';
    $title = get_string('themedefaultbuttonbackgroundhovercolour', 'theme_essential_uv');
    $description = get_string('themedefaultbuttonbackgroundhovercolourdesc', 'theme_essential_uv');
    $default = '#3ad4ff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Navigation colour setting.
    $name = 'theme_essential_uv/themenavcolor';
    $title = get_string('themenavcolor', 'theme_essential_uv');
    $description = get_string('themenavcolordesc', 'theme_essential_uv');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Theme stripe text colour setting.
    $name = 'theme_essential_uv/themestripetextcolour';
    $title = get_string('themestripetextcolour', 'theme_essential_uv');
    $description = get_string('themestripetextcolourdesc', 'theme_essential_uv');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Theme stripe background colour setting.
    $name = 'theme_essential_uv/themestripebackgroundcolour';
    $title = get_string('themestripebackgroundcolour', 'theme_essential_uv');
    $description = get_string('themestripebackgroundcolourdesc', 'theme_essential_uv');
    $default = '#ff9a34';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Theme stripe url colour setting.
    $name = 'theme_essential_uv/themestripeurlcolour';
    $title = get_string('themestripeurlcolour', 'theme_essential_uv');
    $description = get_string('themestripeurlcolourdesc', 'theme_essential_uv');
    $default = '#25849f';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Quiz \'Submit all and finish\' text colour setting.
    $name = 'theme_essential_uv/themequizsubmittextcolour';
    $title = get_string('themequizsubmittextcolour', 'theme_essential_uv');
    $description = get_string('themequizsubmittextcolourdesc', 'theme_essential_uv');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Quiz \'Submit all and finish\' text hover colour setting.
    $name = 'theme_essential_uv/themequizsubmittexthovercolour';
    $title = get_string('themequizsubmittexthovercolour', 'theme_essential_uv');
    $description = get_string('themequizsubmittexthovercolourdesc', 'theme_essential_uv');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Quiz \'Submit all and finish\' background colour setting.
    $name = 'theme_essential_uv/themequizsubmitbackgroundcolour';
    $title = get_string('themequizsubmitbackgroundcolour', 'theme_essential_uv');
    $description = get_string('themequizsubmitbackgroundcolourdesc', 'theme_essential_uv');
    $default = '#ff9a34';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Quiz \'Submit all and finish\' background hover colour setting.
    $name = 'theme_essential_uv/themequizsubmitbackgroundhovercolour';
    $title = get_string('themequizsubmitbackgroundhovercolour', 'theme_essential_uv');
    $description = get_string('themequizsubmitbackgroundhovercolourdesc', 'theme_essential_uv');
    $default = '#ffaf60';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // This is the descriptor for the footer.
    $name = 'theme_essential_uv/footercolorinfo';
    $heading = get_string('footercolors', 'theme_essential_uv');
    $information = get_string('footercolorsdesc', 'theme_essential_uv');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essential_uvsettingscolour->add($setting);

    // Footer background colour setting.
    $name = 'theme_essential_uv/footercolor';
    $title = get_string('footercolor', 'theme_essential_uv');
    $description = get_string('footercolordesc', 'theme_essential_uv');
    $default = '#30add1';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Footer text colour setting.
    $name = 'theme_essential_uv/footertextcolor';
    $title = get_string('footertextcolor', 'theme_essential_uv');
    $description = get_string('footertextcolordesc', 'theme_essential_uv');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Footer heading colour setting.
    $name = 'theme_essential_uv/footerheadingcolor';
    $title = get_string('footerheadingcolor', 'theme_essential_uv');
    $description = get_string('footerheadingcolordesc', 'theme_essential_uv');
    $default = '#cccccc';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Footer block background colour setting.
    $name = 'theme_essential_uv/footerblockbackgroundcolour';
    $title = get_string('footerblockbackgroundcolour', 'theme_essential_uv');
    $description = get_string('footerblockbackgroundcolourdesc', 'theme_essential_uv');
    $default = '#cccccc';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Footer block text colour setting.
    $name = 'theme_essential_uv/footerblocktextcolour';
    $title = get_string('footerblocktextcolour', 'theme_essential_uv');
    $description = get_string('footerblocktextcolourdesc', 'theme_essential_uv');
    $default = '#000000';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Footer block URL colour setting.
    $name = 'theme_essential_uv/footerblockurlcolour';
    $title = get_string('footerblockurlcolour', 'theme_essential_uv');
    $description = get_string('footerblockurlcolourdesc', 'theme_essential_uv');
    $default = '#000000';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Footer block URL hover colour setting.
    $name = 'theme_essential_uv/footerblockhovercolour';
    $title = get_string('footerblockhovercolour', 'theme_essential_uv');
    $description = get_string('footerblockhovercolourdesc', 'theme_essential_uv');
    $default = '#555555';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Footer seperator colour setting.
    $name = 'theme_essential_uv/footersepcolor';
    $title = get_string('footersepcolor', 'theme_essential_uv');
    $description = get_string('footersepcolordesc', 'theme_essential_uv');
    $default = '#313131';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Footer URL colour setting.
    $name = 'theme_essential_uv/footerurlcolor';
    $title = get_string('footerurlcolor', 'theme_essential_uv');
    $description = get_string('footerurlcolordesc', 'theme_essential_uv');
    $default = '#cccccc';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // Footer URL hover colour setting.
    $name = 'theme_essential_uv/footerhovercolor';
    $title = get_string('footerhovercolor', 'theme_essential_uv');
    $description = get_string('footerhovercolordesc', 'theme_essential_uv');
    $default = '#bbbbbb';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscolour->add($setting);

    // This is the descriptor for the user theme colours.
    $name = 'theme_essential_uv/alternativethemecolorsinfo';
    $heading = get_string('alternativethemecolors', 'theme_essential_uv');
    $information = get_string('alternativethemecolorsdesc', 'theme_essential_uv');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essential_uvsettingscolour->add($setting);

    $defaultalternativethemecolors = array('#a430d1', '#d15430', '#5dd130', '#006b94');
    $defaultalternativethemehovercolors = array('#9929c4', '#c44c29', '#53c429', '#4090af');
    $defaultalternativethemestripetextcolors = array('#bdfdb7', '#c3fdd0', '#9f5bfb', '#ff1ebd');
    $defaultalternativethemestripebackgroundcolors = array('#c1009f', '#bc2800', '#b4b2fd', '#0336b4');
    $defaultalternativethemestripeurlcolors = array('#bef500', '#30af67', '#ffe9a6', '#ffab00');

    foreach (range(1, 4) as $alternativethemenumber) {
        // Enables the user to select an alternative colours choice.
        $name = 'theme_essential_uv/enablealternativethemecolors' . $alternativethemenumber;
        $title = get_string('enablealternativethemecolors', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('enablealternativethemecolorsdesc', 'theme_essential_uv', $alternativethemenumber);
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // User theme colour name.
        $name = 'theme_essential_uv/alternativethemename' . $alternativethemenumber;
        $title = get_string('alternativethemename', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemenamedesc', 'theme_essential_uv', $alternativethemenumber);
        $default = get_string('alternativecolors', 'theme_essential_uv', $alternativethemenumber);
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // User theme colour setting.
        $name = 'theme_essential_uv/alternativethemecolor' . $alternativethemenumber;
        $title = get_string('alternativethemecolor', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemecolordesc', 'theme_essential_uv', $alternativethemenumber);
        $default = $defaultalternativethemecolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme text colour setting.
        $name = 'theme_essential_uv/alternativethemetextcolor' . $alternativethemenumber;
        $title = get_string('alternativethemetextcolor', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemetextcolordesc', 'theme_essential_uv', $alternativethemenumber);
        $default = $defaultalternativethemecolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme link colour setting.
        $name = 'theme_essential_uv/alternativethemeurlcolor' . $alternativethemenumber;
        $title = get_string('alternativethemeurlcolor', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemeurlcolordesc', 'theme_essential_uv', $alternativethemenumber);
        $default = $defaultalternativethemecolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme link hover colour setting.
        $name = 'theme_essential_uv/alternativethemehovercolor' . $alternativethemenumber;
        $title = get_string('alternativethemehovercolor', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemehovercolordesc', 'theme_essential_uv', $alternativethemenumber);
        $default = $defaultalternativethemehovercolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme default button text colour setting.
        $name = 'theme_essential_uv/alternativethemedefaultbuttontextcolour' . $alternativethemenumber;
        $title = get_string('alternativethemedefaultbuttontextcolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemedefaultbuttontextcolourdesc', 'theme_essential_uv', $alternativethemenumber);
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme default button text hover colour setting.
        $name = 'theme_essential_uv/alternativethemedefaultbuttontexthovercolour' . $alternativethemenumber;
        $title = get_string('alternativethemedefaultbuttontexthovercolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemedefaultbuttontexthovercolourdesc', 'theme_essential_uv',
            $alternativethemenumber);
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme default button background colour setting.
        $name = 'theme_essential_uv/alternativethemedefaultbuttonbackgroundcolour' . $alternativethemenumber;
        $title = get_string('alternativethemedefaultbuttonbackgroundcolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemedefaultbuttonbackgroundcolourdesc', 'theme_essential_uv', $alternativethemenumber);
        $default = '#30add1';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme default button background hover colour setting.
        $name = 'theme_essential_uv/alternativethemedefbuttonbackgroundhvrcolour' . $alternativethemenumber;
        $title = get_string('alternativethemedefaultbuttonbackgroundhovercolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemedefaultbuttonbackgroundhovercolourdesc', 'theme_essential_uv',
            $alternativethemenumber);
        $default = '#3ad4ff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme icon colour setting.
        $name = 'theme_essential_uv/alternativethemeiconcolor' . $alternativethemenumber;
        $title = get_string('alternativethemeiconcolor', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemeiconcolordesc', 'theme_essential_uv', $alternativethemenumber);
        $default = $defaultalternativethemecolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme side-pre block background colour setting.
        $name = 'theme_essential_uv/alternativethemesidepreblockbackgroundcolour' . $alternativethemenumber;
        $title = get_string('alternativethemesidepreblockbackgroundcolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemesidepreblockbackgroundcolourdesc', 'theme_essential_uv', $alternativethemenumber);
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme side-pre block text colour setting.
        $name = 'theme_essential_uv/alternativethemesidepreblocktextcolour' . $alternativethemenumber;
        $title = get_string('alternativethemesidepreblocktextcolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemesidepreblocktextcolourdesc', 'theme_essential_uv', $alternativethemenumber);
        $default = $defaultalternativethemecolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme side-pre block link colour setting.
        $name = 'theme_essential_uv/alternativethemesidepreblockurlcolour' . $alternativethemenumber;
        $title = get_string('alternativethemesidepreblockurlcolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemesidepreblockurlcolourdesc', 'theme_essential_uv', $alternativethemenumber);
        $default = $defaultalternativethemecolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme side-pre block text hover colour setting.
        $name = 'theme_essential_uv/alternativethemesidepreblockhovercolour' . $alternativethemenumber;
        $title = get_string('alternativethemesidepreblockhovercolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemesidepreblockhovercolourdesc', 'theme_essential_uv', $alternativethemenumber);
        $default = $defaultalternativethemehovercolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme nav colour setting.
        $name = 'theme_essential_uv/alternativethemenavcolor' . $alternativethemenumber;
        $title = get_string('alternativethemenavcolor', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemenavcolordesc', 'theme_essential_uv', $alternativethemenumber);
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme stripe text colour setting.
        $name = 'theme_essential_uv/alternativethemestripetextcolour' . $alternativethemenumber;
        $title = get_string('alternativethemestripetextcolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemestripetextcolourdesc', 'theme_essential_uv', $alternativethemenumber);
        $default = $defaultalternativethemestripetextcolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme stripe background colour setting.
        $name = 'theme_essential_uv/alternativethemestripebackgroundcolour' . $alternativethemenumber;
        $title = get_string('alternativethemestripebackgroundcolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemestripebackgroundcolourdesc', 'theme_essential_uv', $alternativethemenumber);
        $default = $defaultalternativethemestripebackgroundcolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Theme stripe url colour setting.
        $name = 'theme_essential_uv/alternativethemestripeurlcolour' . $alternativethemenumber;
        $title = get_string('alternativethemestripeurlcolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemestripeurlcolourdesc', 'theme_essential_uv', $alternativethemenumber);
        $default = $defaultalternativethemestripeurlcolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme Quiz \'Submit all and finish\' text colour setting.
        $name = 'theme_essential_uv/alternativethemequizsubmittextcolour' . $alternativethemenumber;
        $title = get_string('alternativethemequizsubmittextcolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemequizsubmittextcolourdesc', 'theme_essential_uv', $alternativethemenumber);
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme Quiz \'Submit all and finish\' text hover colour setting.
        $name = 'theme_essential_uv/alternativethemequizsubmittexthovercolour' . $alternativethemenumber;
        $title = get_string('alternativethemequizsubmittexthovercolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemequizsubmittexthovercolourdesc', 'theme_essential_uv',
            $alternativethemenumber);
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme Quiz \'Submit all and finish\' background colour setting.
        $name = 'theme_essential_uv/alternativethemequizsubmitbackgroundcolour' . $alternativethemenumber;
        $title = get_string('alternativethemequizsubmitbackgroundcolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemequizsubmitbackgroundcolourdesc', 'theme_essential_uv', $alternativethemenumber);
        $default = '#ff9a34';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Alternative theme Quiz \'Submit all and finish\' background hover colour setting.
        $name = 'theme_essential_uv/alternativethemequizsubmitbackgroundhovercolour' . $alternativethemenumber;
        $title = get_string('alternativethemequizsubmitbackgroundhovercolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemequizsubmitbackgroundhovercolourdesc', 'theme_essential_uv',
            $alternativethemenumber);
        $default = '#ffaf60';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Enrolled and not accessed course background colour.
        $name = 'theme_essential_uv/alternativethememycoursesorderenrolbackcolour'.$alternativethemenumber;
        $title = get_string('alternativethememycoursesorderenrolbackcolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethememycoursesorderenrolbackcolourdesc', 'theme_essential_uv', $alternativethemenumber);
        $default = '#a3ebff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Footer background colour setting.
        $name = 'theme_essential_uv/alternativethemefootercolor' . $alternativethemenumber;
        $title = get_string('alternativethemefootercolor', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemefootercolordesc', 'theme_essential_uv', $alternativethemenumber);
        $default = '#30add1';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Footer text colour setting.
        $name = 'theme_essential_uv/alternativethemefootertextcolor' . $alternativethemenumber;
        $title = get_string('alternativethemefootertextcolor', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemefootertextcolordesc', 'theme_essential_uv', $alternativethemenumber);
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Footer heading colour setting.
        $name = 'theme_essential_uv/alternativethemefooterheadingcolor' . $alternativethemenumber;
        $title = get_string('alternativethemefooterheadingcolor', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemefooterheadingcolordesc', 'theme_essential_uv', $alternativethemenumber);
        $default = '#cccccc';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Footer block background colour setting.
        $name = 'theme_essential_uv/alternativethemefooterblockbackgroundcolour' . $alternativethemenumber;
        $title = get_string('alternativethemefooterblockbackgroundcolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemefooterblockbackgroundcolourdesc', 'theme_essential_uv',
                $alternativethemenumber);
        $default = '#cccccc';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Footer block text colour setting.
        $name = 'theme_essential_uv/alternativethemefooterblocktextcolour' . $alternativethemenumber;
        $title = get_string('alternativethemefooterblocktextcolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemefooterblocktextcolourdesc', 'theme_essential_uv',
                $alternativethemenumber);
        $default = '#000000';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Footer block URL colour setting.
        $name = 'theme_essential_uv/alternativethemefooterblockurlcolour' . $alternativethemenumber;
        $title = get_string('alternativethemefooterblockurlcolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemefooterblockurlcolourdesc', 'theme_essential_uv', $alternativethemenumber);
        $default = '#000000';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Footer block URL hover colour setting.
        $name = 'theme_essential_uv/alternativethemefooterblockhovercolour' . $alternativethemenumber;
        $title = get_string('alternativethemefooterblockhovercolour', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemefooterblockhovercolourdesc', 'theme_essential_uv',
                $alternativethemenumber);
        $default = '#555555';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Footer seperator colour setting.
        $name = 'theme_essential_uv/alternativethemefootersepcolor' . $alternativethemenumber;
        $title = get_string('alternativethemefootersepcolor', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemefootersepcolordesc', 'theme_essential_uv', $alternativethemenumber);
        $default = '#313131';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Footer URL colour setting.
        $name = 'theme_essential_uv/alternativethemefooterurlcolor' . $alternativethemenumber;
        $title = get_string('alternativethemefooterurlcolor', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemefooterurlcolordesc', 'theme_essential_uv', $alternativethemenumber);
        $default = '#cccccc';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);

        // Footer URL hover colour setting.
        $name = 'theme_essential_uv/alternativethemefooterhovercolor' . $alternativethemenumber;
        $title = get_string('alternativethemefooterhovercolor', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemefooterhovercolordesc', 'theme_essential_uv', $alternativethemenumber);
        $default = '#bbbbbb';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscolour->add($setting);
    }

    $essential_uvsettingscolour->add($essential_uvreadme);
    $essential_uvsettingscolour->add($essential_uvadvert);
}
$ADMIN->add('theme_essential_uv', $essential_uvsettingscolour);

// Header settings.
$essential_uvsettingsheader = new admin_settingpage('theme_essential_uv_header', get_string('headerheading', 'theme_essential_uv'));
if ($ADMIN->fulltree) {
    global $CFG;
    if (file_exists("{$CFG->dirroot}/theme/essential_uv/essential_uv_admin_setting_configtext.php")) {
        require_once($CFG->dirroot . '/theme/essential_uv/essential_uv_admin_setting_configinteger.php');
        require_once($CFG->dirroot . '/theme/essential_uv/essential_uv_admin_setting_configtext.php');
        require_once($CFG->dirroot . '/theme/essential_uv/essential_uv_admin_setting_configradio.php');
    } else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/essential_uv/essential_uv_admin_setting_configtext.php")) {
        require_once($CFG->themedir . '/essential_uv/essential_uv_admin_setting_configinteger.php');
        require_once($CFG->themedir . '/essential_uv/essential_uv_admin_setting_configtext.php');
        require_once($CFG->themedir . '/essential_uv/essential_uv_admin_setting_configradio.php');
    }

    // New or old navbar.
    $name = 'theme_essential_uv/oldnavbar';
    $title = get_string('oldnavbar', 'theme_essential_uv');
    $description = get_string('oldnavbardesc', 'theme_essential_uv');
    $default = 0;
    $choices = array(
        0 => get_string('navbarabove', 'theme_essential_uv'),
        1 => get_string('navbarbelow', 'theme_essential_uv')
    );
    $images = array(
        0 => 'navbarabove',
        1 => 'navbarbelow'
    );
    $setting = new essential_uv_admin_setting_configradio($name, $title, $description, $default, $choices, false, $images);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // User menu user image border radius.
    $name = 'theme_essential_uv/usermenuuserimageborderradius';
    $title = get_string('usermenuuserimageborderradius', 'theme_essential_uv');
    $default = 4;
    $lower = 0;
    $upper = 90;
    $description = get_string('usermenuuserimageborderradiusdesc', 'theme_essential_uv',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_uv_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Scrollbars on the dropdown menus.
    $name = 'theme_essential_uv/dropdownmenuscroll';
    $title = get_string('dropdownmenuscroll', 'theme_essential_uv');
    $description = get_string('dropdownmenuscrolldesc', 'theme_essential_uv');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $essential_uvsettingsheader->add($setting);

    // Dropdown menu maximum height.
    $name = 'theme_essential_uv/dropdownmenumaxheight';
    $title = get_string('dropdownmenumaxheight', 'theme_essential_uv');
    $default = 384;
    $lower = 100;
    $upper = 800;
    $description = get_string('dropdownmenumaxheightdesc', 'theme_essential_uv',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_uv_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Use the site icon if there is no logo.
    $name = 'theme_essential_uv/usesiteicon';
    $title = get_string('usesiteicon', 'theme_essential_uv');
    $description = get_string('usesiteicondesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Default Site icon setting.
    $name = 'theme_essential_uv/siteicon';
    $title = get_string('siteicon', 'theme_essential_uv');
    $description = get_string('siteicondesc', 'theme_essential_uv');
    $default = 'laptop';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $essential_uvsettingsheader->add($setting);

    // Header title setting.
    $name = 'theme_essential_uv/headertitle';
    $title = get_string('headertitle', 'theme_essential_uv');
    $description = get_string('headertitledesc', 'theme_essential_uv');
    $default = '1';
    $choices = array(
        0 => get_string('notitle', 'theme_essential_uv'),
        1 => get_string('fullname', 'theme_essential_uv'),
        2 => get_string('shortname', 'theme_essential_uv'),
        3 => get_string('fullnamesummary', 'theme_essential_uv'),
        4 => get_string('shortnamesummary', 'theme_essential_uv')
    );
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Logo file setting.
    $name = 'theme_essential_uv/logo';
    $title = get_string('logo', 'theme_essential_uv');
    $description = get_string('logodesc', 'theme_essential_uv');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Logo desktop width setting.
    $name = 'theme_essential_uv/logodesktopwidth';
    $title = get_string('logodesktopwidth', 'theme_essential_uv');
    $default = 25;
    $lower = 1;
    $upper = 100;
    $description = get_string('logodesktopwidthdesc', 'theme_essential_uv',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_uv_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Logo mobile width setting.
    $name = 'theme_essential_uv/logomobilewidth';
    $title = get_string('logomobilewidth', 'theme_essential_uv');
    $default = 10;
    $lower = 1;
    $upper = 100;
    $description = get_string('logomobilewidthdesc', 'theme_essential_uv',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_uv_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Navbar title setting.
    $name = 'theme_essential_uv/navbartitle';
    $title = get_string('navbartitle', 'theme_essential_uv');
    $description = get_string('navbartitledesc', 'theme_essential_uv');
    $default = '2';
    $choices = array(
        0 => get_string('notitle', 'theme_essential_uv'),
        1 => get_string('fullname', 'theme_essential_uv'),
        2 => get_string('shortname', 'theme_essential_uv')
    );
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Header text colour setting.
    $name = 'theme_essential_uv/headertextcolor';
    $title = get_string('headertextcolor', 'theme_essential_uv');
    $description = get_string('headertextcolordesc', 'theme_essential_uv');
    $default = '#217a94';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Header background image.
    $name = 'theme_essential_uv/headerbackground';
    $title = get_string('headerbackground', 'theme_essential_uv');
    $description = get_string('headerbackgrounddesc', 'theme_essential_uv');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'headerbackground');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Background style.
    $name = 'theme_essential_uv/headerbackgroundstyle';
    $title = get_string('headerbackgroundstyle', 'theme_essential_uv');
    $description = get_string('headerbackgroundstyledesc', 'theme_essential_uv');
    $default = 'tiled';
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default,
        array(
            'fixed' => get_string('stylefixed', 'theme_essential_uv'),
            'tiled' => get_string('styletiled', 'theme_essential_uv')
        )
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Choose breadcrumbstyle.
    $name = 'theme_essential_uv/breadcrumbstyle';
    $title = get_string('breadcrumbstyle', 'theme_essential_uv');
    $description = get_string('breadcrumbstyledesc', 'theme_essential_uv');
    $default = 1;
    $choices = array(
        1 => get_string('breadcrumbstyled', 'theme_essential_uv'),
        4 => get_string('breadcrumbstylednocollapse', 'theme_essential_uv'),
        2 => get_string('breadcrumbsimple', 'theme_essential_uv'),
        3 => get_string('breadcrumbthin', 'theme_essential_uv'),
        0 => get_string('nobreadcrumb', 'theme_essential_uv')
    );
    $images = array(
        1 => 'breadcrumbstyled',
        4 => 'breadcrumbstylednocollapse',
        2 => 'breadcrumbsimple',
        3 => 'breadcrumbthin'
    );
    $setting = new essential_uv_admin_setting_configradio($name, $title, $description, $default, $choices, false, $images);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Header block.
    $name = 'theme_essential_uv/haveheaderblock';
    $title = get_string('haveheaderblock', 'theme_essential_uv');
    $description = get_string('haveheaderblockdesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $essential_uvsettingsheader->add($setting);

    $name = 'theme_essential_uv/headerblocksperrow';
    $title = get_string('headerblocksperrow', 'theme_essential_uv');
    $default = 4;
    $lower = 1;
    $upper = 4;
    $description = get_string('headerblocksperrowdesc', 'theme_essential_uv',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_uv_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $essential_uvsettingsheader->add($setting);

    // Course menu settings.
    $name = 'theme_essential_uv/mycoursesinfo';
    $heading = get_string('mycoursesinfo', 'theme_essential_uv');
    $information = get_string('mycoursesinfodesc', 'theme_essential_uv');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essential_uvsettingsheader->add($setting);

    // Toggle courses display in custommenu.
    $name = 'theme_essential_uv/displaymycourses';
    $title = get_string('displaymycourses', 'theme_essential_uv');
    $description = get_string('displaymycoursesdesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Toggle hidden courses display in custommenu.
    $name = 'theme_essential_uv/displayhiddenmycourses';
    $title = get_string('displayhiddenmycourses', 'theme_essential_uv');
    $description = get_string('displayhiddenmycoursesdesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    // No need for callback as CSS not changed.
    $essential_uvsettingsheader->add($setting);

    // Toggle category course sub-menu.
    $name = 'theme_essential_uv/mycoursescatsubmenu';
    $title = get_string('mycoursescatsubmenu', 'theme_essential_uv');
    $description = get_string('mycoursescatsubmenudesc', 'theme_essential_uv');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // My courses order.
    $name = 'theme_essential_uv/mycoursesorder';
    $title = get_string('mycoursesorder', 'theme_essential_uv');
    $description = get_string('mycoursesorderdesc', 'theme_essential_uv');
    $default = 1;
    $choices = array(
        1 => get_string('mycoursesordersort', 'theme_essential_uv'),
        2 => get_string('mycoursesorderid', 'theme_essential_uv'),
        3 => get_string('mycoursesorderlast', 'theme_essential_uv')
    );
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    // No need for callback as CSS not changed.
    $essential_uvsettingsheader->add($setting);

    // Course ID order.
    $name = 'theme_essential_uv/mycoursesorderidorder';
    $title = get_string('mycoursesorderidorder', 'theme_essential_uv');
    $description = get_string('mycoursesorderidorderdesc', 'theme_essential_uv');
    $default = 1;
    $choices = array(
        1 => get_string('mycoursesorderidasc', 'theme_essential_uv'),
        2 => get_string('mycoursesorderiddes', 'theme_essential_uv')
    );
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    // No need for callback as CSS not changed.
    $essential_uvsettingsheader->add($setting);

    // Max courses.
    $name = 'theme_essential_uv/mycoursesmax';
    $title = get_string('mycoursesmax', 'theme_essential_uv');
    $default = 0;
    $lower = 0;
    $upper = 20;
    $description = get_string('mycoursesmaxdesc', 'theme_essential_uv',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_uv_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    // No need for callback as CSS not changed.
    $essential_uvsettingsheader->add($setting);

    // Set terminology for dropdown course list.
    $name = 'theme_essential_uv/mycoursetitle';
    $title = get_string('mycoursetitle', 'theme_essential_uv');
    $description = get_string('mycoursetitledesc', 'theme_essential_uv');
    $default = 'course';
    $choices = array(
        'course' => get_string('mycourses', 'theme_essential_uv'),
        'unit' => get_string('myunits', 'theme_essential_uv'),
        'class' => get_string('myclasses', 'theme_essential_uv'),
        'module' => get_string('mymodules', 'theme_essential_uv')
    );
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Enrolled and not accessed course background colour.
    $name = 'theme_essential_uv/mycoursesorderenrolbackcolour';
    $title = get_string('mycoursesorderenrolbackcolour', 'theme_essential_uv');
    $description = get_string('mycoursesorderenrolbackcolourdesc', 'theme_essential_uv');
    $default = '#a3ebff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // User menu settings.
    $name = 'theme_essential_uv/usermenu';
    $heading = get_string('usermenu', 'theme_essential_uv');
    $information = get_string('usermenudesc', 'theme_essential_uv');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essential_uvsettingsheader->add($setting);

    // Helplink type.
    $name = 'theme_essential_uv/helplinktype';
    $title = get_string('helplinktype', 'theme_essential_uv');
    $description = get_string('helplinktypedesc', 'theme_essential_uv');
    $default = 1;
    $choices = array(1 => get_string('email'),
        2 => get_string('url'),
        0 => get_string('none')
    );
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Helplink.
    $name = 'theme_essential_uv/helplink';
    $title = get_string('helplink', 'theme_essential_uv');
    $description = get_string('helplinkdesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Editing menu settings.
    $name = 'theme_essential_uv/editingmenu';
    $heading = get_string('editingmenu', 'theme_essential_uv');
    $information = get_string('editingmenudesc', 'theme_essential_uv');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essential_uvsettingsheader->add($setting);

    $name = 'theme_essential_uv/displayeditingmenu';
    $title = get_string('displayeditingmenu', 'theme_essential_uv');
    $description = get_string('displayeditingmenudesc', 'theme_essential_uv');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $essential_uvsettingsheader->add($setting);

    $name = 'theme_essential_uv/hidedefaulteditingbutton';
    $title = get_string('hidedefaulteditingbutton', 'theme_essential_uv');
    $description = get_string('hidedefaulteditingbuttondesc', 'theme_essential_uv');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $essential_uvsettingsheader->add($setting);

    // Social network settings.
    $essential_uvsettingsheader->add(new admin_setting_heading('theme_essential_uv_social',
        get_string('socialheadingsub', 'theme_essential_uv'),
        format_text(get_string('socialdesc', 'theme_essential_uv'), FORMAT_MARKDOWN)));

    // Website URL setting.
    $name = 'theme_essential_uv/website';
    $title = get_string('websiteurl', 'theme_essential_uv');
    $description = get_string('websitedesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Facebook URL setting.
    $name = 'theme_essential_uv/facebook';
    $title = get_string('facebookurl', 'theme_essential_uv');
    $description = get_string('facebookdesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Flickr URL setting.
    $name = 'theme_essential_uv/flickr';
    $title = get_string('flickrurl', 'theme_essential_uv');
    $description = get_string('flickrdesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Twitter URL setting.
    $name = 'theme_essential_uv/twitter';
    $title = get_string('twitterurl', 'theme_essential_uv');
    $description = get_string('twitterdesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Google+ URL setting.
    $name = 'theme_essential_uv/googleplus';
    $title = get_string('googleplusurl', 'theme_essential_uv');
    $description = get_string('googleplusdesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // LinkedIn URL setting.
    $name = 'theme_essential_uv/linkedin';
    $title = get_string('linkedinurl', 'theme_essential_uv');
    $description = get_string('linkedindesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Pinterest URL setting.
    $name = 'theme_essential_uv/pinterest';
    $title = get_string('pinteresturl', 'theme_essential_uv');
    $description = get_string('pinterestdesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Instagram URL setting.
    $name = 'theme_essential_uv/instagram';
    $title = get_string('instagramurl', 'theme_essential_uv');
    $description = get_string('instagramdesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // YouTube URL setting.
    $name = 'theme_essential_uv/youtube';
    $title = get_string('youtubeurl', 'theme_essential_uv');
    $description = get_string('youtubedesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Skype URL setting.
    $name = 'theme_essential_uv/skype';
    $title = get_string('skypeuri', 'theme_essential_uv');
    $description = get_string('skypedesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // VKontakte URL setting.
    $name = 'theme_essential_uv/vk';
    $title = get_string('vkurl', 'theme_essential_uv');
    $description = get_string('vkdesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Apps settings.
    $essential_uvsettingsheader->add(new admin_setting_heading('theme_essential_uv_mobileapps',
        get_string('mobileappsheadingsub', 'theme_essential_uv'),
        format_text(get_string('mobileappsdesc', 'theme_essential_uv'), FORMAT_MARKDOWN)));

    // Android App URL setting.
    $name = 'theme_essential_uv/android';
    $title = get_string('androidurl', 'theme_essential_uv');
    $description = get_string('androiddesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Windows App URL setting.
    $name = 'theme_essential_uv/windows';
    $title = get_string('windowsurl', 'theme_essential_uv');
    $description = get_string('windowsdesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // Windows PhoneApp URL setting.
    $name = 'theme_essential_uv/winphone';
    $title = get_string('winphoneurl', 'theme_essential_uv');
    $description = get_string('winphonedesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // The iOS App URL setting.
    $name = 'theme_essential_uv/ios';
    $title = get_string('iosurl', 'theme_essential_uv');
    $description = get_string('iosdesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // This is the descriptor for iOS icons.
    $name = 'theme_essential_uv/iosiconinfo';
    $heading = get_string('iosicon', 'theme_essential_uv');
    $information = get_string('iosicondesc', 'theme_essential_uv');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essential_uvsettingsheader->add($setting);

    // The iPhone icon.
    $name = 'theme_essential_uv/iphoneicon';
    $title = get_string('iphoneicon', 'theme_essential_uv');
    $description = get_string('iphoneicondesc', 'theme_essential_uv');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'iphoneicon');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // The iPhone retina icon.
    $name = 'theme_essential_uv/iphoneretinaicon';
    $title = get_string('iphoneretinaicon', 'theme_essential_uv');
    $description = get_string('iphoneretinaicondesc', 'theme_essential_uv');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'iphoneretinaicon');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // The iPad icon.
    $name = 'theme_essential_uv/ipadicon';
    $title = get_string('ipadicon', 'theme_essential_uv');
    $description = get_string('ipadicondesc', 'theme_essential_uv');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'ipadicon');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    // The iPad retina icon.
    $name = 'theme_essential_uv/ipadretinaicon';
    $title = get_string('ipadretinaicon', 'theme_essential_uv');
    $description = get_string('ipadretinaicondesc', 'theme_essential_uv');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'ipadretinaicon');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsheader->add($setting);

    $essential_uvsettingsheader->add($essential_uvreadme);
    $essential_uvsettingsheader->add($essential_uvadvert);
}
$ADMIN->add('theme_essential_uv', $essential_uvsettingsheader);

// Font settings.
$essential_uvsettingsfont = new admin_settingpage('theme_essential_uv_font', get_string('fontsettings', 'theme_essential_uv'));
if ($ADMIN->fulltree) {
    // This is the descriptor for the font settings.
    $name = 'theme_essential_uv/fontheading';
    $heading = get_string('fontheadingsub', 'theme_essential_uv');
    $information = get_string('fontheadingdesc', 'theme_essential_uv');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essential_uvsettingsfont->add($setting);

    // Font selector.
    $gws = html_writer::link('//www.google.com/fonts',
        get_string('fonttypegoogle', 'theme_essential_uv'), array('target' => '_blank'));
    $name = 'theme_essential_uv/fontselect';
    $title = get_string('fontselect', 'theme_essential_uv');
    $description = get_string('fontselectdesc', 'theme_essential_uv', array('googlewebfonts' => $gws));
    $default = 1;
    $choices = array(
        1 => get_string('fonttypeuser', 'theme_essential_uv'),
        2 => get_string('fonttypegoogle', 'theme_essential_uv'),
        3 => get_string('fonttypecustom', 'theme_essential_uv')
    );
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfont->add($setting);

    // Heading font name.
    $name = 'theme_essential_uv/fontnameheading';
    $title = get_string('fontnameheading', 'theme_essential_uv');
    $description = get_string('fontnameheadingdesc', 'theme_essential_uv');
    $default = 'Verdana';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfont->add($setting);

    // Text font name.
    $name = 'theme_essential_uv/fontnamebody';
    $title = get_string('fontnamebody', 'theme_essential_uv');
    $description = get_string('fontnamebodydesc', 'theme_essential_uv');
    $default = 'Verdana';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfont->add($setting);

    if (get_config('theme_essential_uv', 'fontselect') === "2") {
        // Google font character sets.
        $name = 'theme_essential_uv/fontcharacterset';
        $title = get_string('fontcharacterset', 'theme_essential_uv');
        $description = get_string('fontcharactersetdesc', 'theme_essential_uv');
        $default = 'latin-ext';
        $setting = new admin_setting_configmulticheckbox($name, $title, $description, $default,
            array(
                'latin-ext' => get_string('fontcharactersetlatinext', 'theme_essential_uv'),
                'cyrillic' => get_string('fontcharactersetcyrillic', 'theme_essential_uv'),
                'cyrillic-ext' => get_string('fontcharactersetcyrillicext', 'theme_essential_uv'),
                'greek' => get_string('fontcharactersetgreek', 'theme_essential_uv'),
                'greek-ext' => get_string('fontcharactersetgreekext', 'theme_essential_uv'),
                'vietnamese' => get_string('fontcharactersetvietnamese', 'theme_essential_uv')
            )
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfont->add($setting);
    } else if (get_config('theme_essential_uv', 'fontselect') === "3") {
        // This is the descriptor for the font files.
        $name = 'theme_essential_uv/fontfiles';
        $heading = get_string('fontfiles', 'theme_essential_uv');
        $information = get_string('fontfilesdesc', 'theme_essential_uv');
        $setting = new admin_setting_heading($name, $heading, $information);
        $essential_uvsettingsfont->add($setting);

        // Heading fonts.
        // TTF font.
        $name = 'theme_essential_uv/fontfilettfheading';
        $title = get_string('fontfilettfheading', 'theme_essential_uv');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilettfheading');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfont->add($setting);

        // OTF font.
        $name = 'theme_essential_uv/fontfileotfheading';
        $title = get_string('fontfileotfheading', 'theme_essential_uv');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfileotfheading');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfont->add($setting);

        // WOFF font.
        $name = 'theme_essential_uv/fontfilewoffheading';
        $title = get_string('fontfilewoffheading', 'theme_essential_uv');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilewoffheading');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfont->add($setting);

        // WOFF2 font.
        $name = 'theme_essential_uv/fontfilewofftwoheading';
        $title = get_string('fontfilewofftwoheading', 'theme_essential_uv');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilewofftwoheading');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfont->add($setting);

        // EOT font.
        $name = 'theme_essential_uv/fontfileeotheading';
        $title = get_string('fontfileeotheading', 'theme_essential_uv');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfileeotheading');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfont->add($setting);

        // SVG font.
        $name = 'theme_essential_uv/fontfilesvgheading';
        $title = get_string('fontfilesvgheading', 'theme_essential_uv');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilesvgheading');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfont->add($setting);

        // Body fonts.
        // TTF font.
        $name = 'theme_essential_uv/fontfilettfbody';
        $title = get_string('fontfilettfbody', 'theme_essential_uv');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilettfbody');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfont->add($setting);

        // OTF font.
        $name = 'theme_essential_uv/fontfileotfbody';
        $title = get_string('fontfileotfbody', 'theme_essential_uv');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfileotfbody');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfont->add($setting);

        // WOFF font.
        $name = 'theme_essential_uv/fontfilewoffbody';
        $title = get_string('fontfilewoffbody', 'theme_essential_uv');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilewoffbody');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfont->add($setting);

        // WOFF2 font.
        $name = 'theme_essential_uv/fontfilewofftwobody';
        $title = get_string('fontfilewofftwobody', 'theme_essential_uv');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilewofftwobody');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfont->add($setting);

        // EOT font.
        $name = 'theme_essential_uv/fontfileeotbody';
        $title = get_string('fontfileeotbody', 'theme_essential_uv');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfileeotbody');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfont->add($setting);

        // SVG font.
        $name = 'theme_essential_uv/fontfilesvgbody';
        $title = get_string('fontfilesvgbody', 'theme_essential_uv');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilesvgbody');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfont->add($setting);
    }

    $essential_uvsettingsfont->add($essential_uvreadme);
    $essential_uvsettingsfont->add($essential_uvadvert);
}
$ADMIN->add('theme_essential_uv', $essential_uvsettingsfont);

// Footer settings.
$essential_uvsettingsfooter = new admin_settingpage('theme_essential_uv_footer', get_string('footerheading', 'theme_essential_uv'));
if ($ADMIN->fulltree) {
    // Copyright setting.
    $name = 'theme_essential_uv/copyright';
    $title = get_string('copyright', 'theme_essential_uv');
    $description = get_string('copyrightdesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $essential_uvsettingsfooter->add($setting);

    // Footnote setting.
    $name = 'theme_essential_uv/footnote';
    $title = get_string('footnote', 'theme_essential_uv');
    $description = get_string('footnotedesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfooter->add($setting);

    // Performance information display.
    $name = 'theme_essential_uv/perfinfo';
    $title = get_string('perfinfo', 'theme_essential_uv');
    $description = get_string('perfinfodesc', 'theme_essential_uv');
    $perfmax = get_string('perf_max', 'theme_essential_uv');
    $perfmin = get_string('perf_min', 'theme_essential_uv');
    $default = 'min';
    $choices = array('min' => $perfmin, 'max' => $perfmax);
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfooter->add($setting);

    $essential_uvsettingsfooter->add($essential_uvreadme);
    $essential_uvsettingsfooter->add($essential_uvadvert);
}
$ADMIN->add('theme_essential_uv', $essential_uvsettingsfooter);

// Frontpage settings.
$essential_uvsettingsfrontpage = new admin_settingpage('theme_essential_uv_frontpage', get_string('frontpageheading', 'theme_essential_uv'));
if ($ADMIN->fulltree) {

    $name = 'theme_essential_uv/courselistteachericon';
    $title = get_string('courselistteachericon', 'theme_essential_uv');
    $description = get_string('courselistteachericondesc', 'theme_essential_uv');
    $default = 'graduation-cap';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    $essential_uvsettingsfrontpage->add(new admin_setting_heading('theme_essential_uv_frontcontent',
        get_string('frontcontentheading', 'theme_essential_uv'), ''));

    // Toggle frontpage content.
    $name = 'theme_essential_uv/togglefrontcontent';
    $title = get_string('frontcontent', 'theme_essential_uv');
    $description = get_string('frontcontentdesc', 'theme_essential_uv');
    $alwaysdisplay = get_string('alwaysdisplay', 'theme_essential_uv');
    $displaybeforelogin = get_string('displaybeforelogin', 'theme_essential_uv');
    $displayafterlogin = get_string('displayafterlogin', 'theme_essential_uv');
    $dontdisplay = get_string('dontdisplay', 'theme_essential_uv');
    $default = 0;
    $choices = array(1 => $alwaysdisplay, 2 => $displaybeforelogin, 3 => $displayafterlogin, 0 => $dontdisplay);
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    // Frontpage content.
    $name = 'theme_essential_uv/frontcontentarea';
    $title = get_string('frontcontentarea', 'theme_essential_uv');
    $description = get_string('frontcontentareadesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    $name = 'theme_essential_uv_frontpageblocksheading';
    $heading = get_string('frontpageblocksheading', 'theme_essential_uv');
    $information = '';
    $setting = new admin_setting_heading($name, $heading, $information);
    $essential_uvsettingsfrontpage->add($setting);

    // Frontpage block alignment.
    $name = 'theme_essential_uv/frontpageblocks';
    $title = get_string('frontpageblocks', 'theme_essential_uv');
    $description = get_string('frontpageblocksdesc', 'theme_essential_uv');
    $before = get_string('beforecontent', 'theme_essential_uv');
    $after = get_string('aftercontent', 'theme_essential_uv');
    $default = 1;
    $choices = array(1 => $before, 0 => $after);
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    // Toggle frontpage home (was middle) blocks.
    $name = 'theme_essential_uv/frontpagemiddleblocks';
    $title = get_string('frontpagemiddleblocks', 'theme_essential_uv');
    $description = get_string('frontpagemiddleblocksdesc', 'theme_essential_uv');
    $alwaysdisplay = get_string('alwaysdisplay', 'theme_essential_uv');
    $displaybeforelogin = get_string('displaybeforelogin', 'theme_essential_uv');
    $displayafterlogin = get_string('displayafterlogin', 'theme_essential_uv');
    $dontdisplay = get_string('dontdisplay', 'theme_essential_uv');
    $default = 0;
    $choices = array(1 => $alwaysdisplay, 2 => $displaybeforelogin, 3 => $displayafterlogin, 0 => $dontdisplay);
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    // Home blocks per row.
    $name = 'theme_essential_uv/frontpagehomeblocksperrow';
    $title = get_string('frontpagehomeblocksperrow', 'theme_essential_uv');
    $default = 3;
    $lower = 1;
    $upper = 4;
    $description = get_string('frontpagehomeblocksperrowdesc', 'theme_essential_uv',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_uv_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $essential_uvsettingsfrontpage->add($setting);

    // Toggle frontpage page top blocks.
    $name = 'theme_essential_uv/fppagetopblocks';
    $title = get_string('fppagetopblocks', 'theme_essential_uv');
    $description = get_string('fppagetopblocksdesc', 'theme_essential_uv');
    $alwaysdisplay = get_string('alwaysdisplay', 'theme_essential_uv');
    $displaybeforelogin = get_string('displaybeforelogin', 'theme_essential_uv');
    $displayafterlogin = get_string('displayafterlogin', 'theme_essential_uv');
    $dontdisplay = get_string('dontdisplay', 'theme_essential_uv');
    $default = 3;
    $choices = array(1 => $alwaysdisplay, 2 => $displaybeforelogin, 3 => $displayafterlogin, 0 => $dontdisplay);
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    // Page top blocks per row.
    $name = 'theme_essential_uv/fppagetopblocksperrow';
    $title = get_string('fppagetopblocksperrow', 'theme_essential_uv');
    $default = 3;
    $lower = 1;
    $upper = 4;
    $description = get_string('fppagetopblocksperrowdesc', 'theme_essential_uv',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_uv_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $essential_uvsettingsfrontpage->add($setting);

    // Marketing spot settings.
    $essential_uvsettingsfrontpage->add(new admin_setting_heading('theme_essential_uv_marketing',
        get_string('marketingheading', 'theme_essential_uv'),
        format_text(get_string('marketingdesc', 'theme_essential_uv'), FORMAT_MARKDOWN)));

    // Toggle marketing spots.
    $name = 'theme_essential_uv/togglemarketing';
    $title = get_string('togglemarketing', 'theme_essential_uv');
    $description = get_string('togglemarketingdesc', 'theme_essential_uv');
    $alwaysdisplay = get_string('alwaysdisplay', 'theme_essential_uv');
    $displaybeforelogin = get_string('displaybeforelogin', 'theme_essential_uv');
    $displayafterlogin = get_string('displayafterlogin', 'theme_essential_uv');
    $dontdisplay = get_string('dontdisplay', 'theme_essential_uv');
    $default = 1;
    $choices = array(1 => $alwaysdisplay, 2 => $displaybeforelogin, 3 => $displayafterlogin, 0 => $dontdisplay);
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    // Marketing spot height.
    $name = 'theme_essential_uv/marketingheight';
    $title = get_string('marketingheight', 'theme_essential_uv');
    $description = get_string('marketingheightdesc', 'theme_essential_uv');
    $default = 100;
    $choices = array();
    for ($mhit = 50; $mhit <= 500; $mhit = $mhit + 2) {
        $choices[$mhit] = $mhit;
    }
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $essential_uvsettingsfrontpage->add($setting);

    // Marketing spot image height.
    $name = 'theme_essential_uv/marketingimageheight';
    $title = get_string('marketingimageheight', 'theme_essential_uv');
    $description = get_string('marketingimageheightdesc', 'theme_essential_uv');
    $default = 100;
    $choices = array(50 => '50', 100 => '100', 150 => '150', 200 => '200', 250 => '250', 300 => '300');
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $essential_uvsettingsfrontpage->add($setting);

    foreach (range(1, 3) as $marketingspotnumber) {
        // This is the descriptor for Marketing Spot in $marketingspotnumber.
        $name = 'theme_essential_uv/marketing' . $marketingspotnumber . 'info';
        $heading = get_string('marketing' . $marketingspotnumber, 'theme_essential_uv');
        $information = get_string('marketinginfodesc', 'theme_essential_uv');
        $setting = new admin_setting_heading($name, $heading, $information);
        $essential_uvsettingsfrontpage->add($setting);

        // Marketing spot.
        $name = 'theme_essential_uv/marketing' . $marketingspotnumber;
        $title = get_string('marketingtitle', 'theme_essential_uv');
        $description = get_string('marketingtitledesc', 'theme_essential_uv');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfrontpage->add($setting);

        $name = 'theme_essential_uv/marketing' . $marketingspotnumber . 'icon';
        $title = get_string('marketingicon', 'theme_essential_uv');
        $description = get_string('marketingicondesc', 'theme_essential_uv');
        $default = 'star';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfrontpage->add($setting);

        $name = 'theme_essential_uv/marketing' . $marketingspotnumber . 'image';
        $title = get_string('marketingimage', 'theme_essential_uv');
        $description = get_string('marketingimagedesc', 'theme_essential_uv');
        $setting = new admin_setting_configstoredfile($name, $title, $description,
                'marketing' . $marketingspotnumber . 'image');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfrontpage->add($setting);

        $name = 'theme_essential_uv/marketing' . $marketingspotnumber . 'content';
        $title = get_string('marketingcontent', 'theme_essential_uv');
        $description = get_string('marketingcontentdesc', 'theme_essential_uv');
        $default = '';
        $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfrontpage->add($setting);

        $name = 'theme_essential_uv/marketing' . $marketingspotnumber . 'buttontext';
        $title = get_string('marketingbuttontext', 'theme_essential_uv');
        $description = get_string('marketingbuttontextdesc', 'theme_essential_uv');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfrontpage->add($setting);

        $name = 'theme_essential_uv/marketing' . $marketingspotnumber . 'buttonurl';
        $title = get_string('marketingbuttonurl', 'theme_essential_uv');
        $description = get_string('marketingbuttonurldesc', 'theme_essential_uv');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfrontpage->add($setting);

        $name = 'theme_essential_uv/marketing' . $marketingspotnumber . 'target';
        $title = get_string('marketingurltarget', 'theme_essential_uv');
        $description = get_string('marketingurltargetdesc', 'theme_essential_uv');
        $target1 = get_string('marketingurltargetself', 'theme_essential_uv');
        $target2 = get_string('marketingurltargetnew', 'theme_essential_uv');
        $target3 = get_string('marketingurltargetparent', 'theme_essential_uv');
        $default = '_blank';
        $choices = array('_self' => $target1, '_blank' => $target2, '_parent' => $target3);
        $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsfrontpage->add($setting);
    }

    // User alerts.
    $essential_uvsettingsfrontpage->add(new admin_setting_heading('theme_essential_uv_alerts',
        get_string('alertsheadingsub', 'theme_essential_uv'),
        format_text(get_string('alertsdesc', 'theme_essential_uv'), FORMAT_MARKDOWN)));

    $information = get_string('alertinfodesc', 'theme_essential_uv');

    // This is the descriptor for alert one.
    $name = 'theme_essential_uv/alert1info';
    $heading = get_string('alert1', 'theme_essential_uv');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essential_uvsettingsfrontpage->add($setting);

    // Enable alert.
    $name = 'theme_essential_uv/enable1alert';
    $title = get_string('enablealert', 'theme_essential_uv');
    $description = get_string('enablealertdesc', 'theme_essential_uv');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    // Alert type.
    $name = 'theme_essential_uv/alert1type';
    $title = get_string('alerttype', 'theme_essential_uv');
    $description = get_string('alerttypedesc', 'theme_essential_uv');
    $alertinfo = get_string('alert_info', 'theme_essential_uv');
    $alertwarning = get_string('alert_warning', 'theme_essential_uv');
    $alertgeneral = get_string('alert_general', 'theme_essential_uv');
    $default = 'info';
    $choices = array('info' => $alertinfo, 'error' => $alertwarning, 'success' => $alertgeneral);
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    // Alert title.
    $name = 'theme_essential_uv/alert1title';
    $title = get_string('alerttitle', 'theme_essential_uv');
    $description = get_string('alerttitledesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    // Alert text.
    $name = 'theme_essential_uv/alert1text';
    $title = get_string('alerttext', 'theme_essential_uv');
    $description = get_string('alerttextdesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    // This is the descriptor for alert two.
    $name = 'theme_essential_uv/alert2info';
    $heading = get_string('alert2', 'theme_essential_uv');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essential_uvsettingsfrontpage->add($setting);

    // Enable alert.
    $name = 'theme_essential_uv/enable2alert';
    $title = get_string('enablealert', 'theme_essential_uv');
    $description = get_string('enablealertdesc', 'theme_essential_uv');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    // Alert type.
    $name = 'theme_essential_uv/alert2type';
    $title = get_string('alerttype', 'theme_essential_uv');
    $description = get_string('alerttypedesc', 'theme_essential_uv');
    $alertinfo = get_string('alert_info', 'theme_essential_uv');
    $alertwarning = get_string('alert_warning', 'theme_essential_uv');
    $alertgeneral = get_string('alert_general', 'theme_essential_uv');
    $default = 'info';
    $choices = array('info' => $alertinfo, 'error' => $alertwarning, 'success' => $alertgeneral);
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    // Alert title.
    $name = 'theme_essential_uv/alert2title';
    $title = get_string('alerttitle', 'theme_essential_uv');
    $description = get_string('alerttitledesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    // Alert text.
    $name = 'theme_essential_uv/alert2text';
    $title = get_string('alerttext', 'theme_essential_uv');
    $description = get_string('alerttextdesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    // This is the descriptor for alert three.
    $name = 'theme_essential_uv/alert3info';
    $heading = get_string('alert3', 'theme_essential_uv');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essential_uvsettingsfrontpage->add($setting);

    // Enable alert.
    $name = 'theme_essential_uv/enable3alert';
    $title = get_string('enablealert', 'theme_essential_uv');
    $description = get_string('enablealertdesc', 'theme_essential_uv');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    // Alert type.
    $name = 'theme_essential_uv/alert3type';
    $title = get_string('alerttype', 'theme_essential_uv');
    $description = get_string('alerttypedesc', 'theme_essential_uv');
    $alertinfo = get_string('alert_info', 'theme_essential_uv');
    $alertwarning = get_string('alert_warning', 'theme_essential_uv');
    $alertgeneral = get_string('alert_general', 'theme_essential_uv');
    $default = 'info';
    $choices = array('info' => $alertinfo, 'error' => $alertwarning, 'success' => $alertgeneral);
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    // Alert title.
    $name = 'theme_essential_uv/alert3title';
    $title = get_string('alerttitle', 'theme_essential_uv');
    $description = get_string('alerttitledesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    // Alert text.
    $name = 'theme_essential_uv/alert3text';
    $title = get_string('alerttext', 'theme_essential_uv');
    $description = get_string('alerttextdesc', 'theme_essential_uv');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsfrontpage->add($setting);

    $essential_uvsettingsfrontpage->add($essential_uvreadme);
    $essential_uvsettingsfrontpage->add($essential_uvadvert);
}
$ADMIN->add('theme_essential_uv', $essential_uvsettingsfrontpage);

// Slideshow settings.
$essential_uvsettingsslideshow = new admin_settingpage('theme_essential_uv_slideshow', get_string('slideshowheading', 'theme_essential_uv'));
if ($ADMIN->fulltree) {
    $essential_uvsettingsslideshow->add(new admin_setting_heading('theme_essential_uv_slideshow',
        get_string('slideshowheadingsub', 'theme_essential_uv'),
        format_text(get_string('slideshowdesc', 'theme_essential_uv'), FORMAT_MARKDOWN)));

    // Toggle slideshow.
    $name = 'theme_essential_uv/toggleslideshow';
    $title = get_string('toggleslideshow', 'theme_essential_uv');
    $description = get_string('toggleslideshowdesc', 'theme_essential_uv');
    $alwaysdisplay = get_string('alwaysdisplay', 'theme_essential_uv');
    $displaybeforelogin = get_string('displaybeforelogin', 'theme_essential_uv');
    $displayafterlogin = get_string('displayafterlogin', 'theme_essential_uv');
    $dontdisplay = get_string('dontdisplay', 'theme_essential_uv');
    $default = 1;
    $choices = array(1 => $alwaysdisplay, 2 => $displaybeforelogin, 3 => $displayafterlogin, 0 => $dontdisplay);
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsslideshow->add($setting);

    // Number of slides.
    $name = 'theme_essential_uv/numberofslides';
    $title = get_string('numberofslides', 'theme_essential_uv');
    $description = get_string('numberofslides_desc', 'theme_essential_uv');
    $default = 4;
    $choices = array(
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
        6 => '6',
        7 => '7',
        8 => '8',
        9 => '9',
        10 => '10',
        11 => '11',
        12 => '12',
        13 => '13',
        14 => '14',
        15 => '15',
        16 => '16'
    );
    $essential_uvsettingsslideshow->add(new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices));

    // Hide slideshow on phones.
    $name = 'theme_essential_uv/hideontablet';
    $title = get_string('hideontablet', 'theme_essential_uv');
    $description = get_string('hideontabletdesc', 'theme_essential_uv');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsslideshow->add($setting);

    // Hide slideshow on tablet.
    $name = 'theme_essential_uv/hideonphone';
    $title = get_string('hideonphone', 'theme_essential_uv');
    $description = get_string('hideonphonedesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsslideshow->add($setting);

    // Slide interval.
    $name = 'theme_essential_uv/slideinterval';
    $title = get_string('slideinterval', 'theme_essential_uv');
    $description = get_string('slideintervaldesc', 'theme_essential_uv');
    $default = '5000';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsslideshow->add($setting);

    // Slide caption text colour setting.
    $name = 'theme_essential_uv/slidecaptiontextcolor';
    $title = get_string('slidecaptiontextcolor', 'theme_essential_uv');
    $description = get_string('slidecaptiontextcolordesc', 'theme_essential_uv');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsslideshow->add($setting);

    // Slide caption background colour setting.
    $name = 'theme_essential_uv/slidecaptionbackgroundcolor';
    $title = get_string('slidecaptionbackgroundcolor', 'theme_essential_uv');
    $description = get_string('slidecaptionbackgroundcolordesc', 'theme_essential_uv');
    $default = '#30add1';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsslideshow->add($setting);

    // Show caption options.
    $name = 'theme_essential_uv/slidecaptionoptions';
    $title = get_string('slidecaptionoptions', 'theme_essential_uv');
    $description = get_string('slidecaptionoptionsdesc', 'theme_essential_uv');
    $default = 0;
    $choices = array(
        0 => get_string('slidecaptionbeside', 'theme_essential_uv'),
        1 => get_string('slidecaptionontop', 'theme_essential_uv'),
        2 => get_string('slidecaptionunderneath', 'theme_essential_uv')
    );
    $images = array(
        0 => 'beside',
        1 => 'on_top',
        2 => 'underneath'
    );
    $setting = new essential_uv_admin_setting_configradio($name, $title, $description, $default, $choices, false, $images);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsslideshow->add($setting);

    // Show caption centred.
    $name = 'theme_essential_uv/slidecaptioncentred';
    $title = get_string('slidecaptioncentred', 'theme_essential_uv');
    $description = get_string('slidecaptioncentreddesc', 'theme_essential_uv');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsslideshow->add($setting);

    // Slide button colour setting.
    $name = 'theme_essential_uv/slidebuttoncolor';
    $title = get_string('slidebuttoncolor', 'theme_essential_uv');
    $description = get_string('slidebuttoncolordesc', 'theme_essential_uv');
    $default = '#30add1';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsslideshow->add($setting);

    // Slide button hover colour setting.
    $name = 'theme_essential_uv/slidebuttonhovercolor';
    $title = get_string('slidebuttonhovercolor', 'theme_essential_uv');
    $description = get_string('slidebuttonhovercolordesc', 'theme_essential_uv');
    $default = '#217a94';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingsslideshow->add($setting);

    // This is the descriptor for the user theme slide colours.
    $name = 'theme_essential_uv/alternativethemeslidecolorsinfo';
    $heading = get_string('alternativethemeslidecolors', 'theme_essential_uv');
    $information = get_string('alternativethemeslidecolorsdesc', 'theme_essential_uv');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essential_uvsettingsslideshow->add($setting);

    foreach (range(1, 4) as $alternativethemenumber) {
        // Alternative theme slide caption text colour setting.
        $name = 'theme_essential_uv/alternativethemeslidecaptiontextcolor' . $alternativethemenumber;
        $title = get_string('alternativethemeslidecaptiontextcolor', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemeslidecaptiontextcolordesc', 'theme_essential_uv',
                $alternativethemenumber);
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsslideshow->add($setting);

        // Alternative theme slide caption background colour setting.
        $name = 'theme_essential_uv/alternativethemeslidecaptionbackgroundcolor' . $alternativethemenumber;
        $title = get_string('alternativethemeslidecaptionbackgroundcolor', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemeslidecaptionbackgroundcolordesc', 'theme_essential_uv',
                $alternativethemenumber);
        $default = $defaultalternativethemecolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsslideshow->add($setting);

        // Alternative theme slide button colour setting.
        $name = 'theme_essential_uv/alternativethemeslidebuttoncolor' . $alternativethemenumber;
        $title = get_string('alternativethemeslidebuttoncolor', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemeslidebuttoncolordesc', 'theme_essential_uv', $alternativethemenumber);
        $default = $defaultalternativethemecolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsslideshow->add($setting);

        // Alternative theme slide button hover colour setting.
        $name = 'theme_essential_uv/alternativethemeslidebuttonhovercolor' . $alternativethemenumber;
        $title = get_string('alternativethemeslidebuttonhovercolor', 'theme_essential_uv', $alternativethemenumber);
        $description = get_string('alternativethemeslidebuttonhovercolordesc', 'theme_essential_uv',
                $alternativethemenumber);
        $default = $defaultalternativethemehovercolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsslideshow->add($setting);
    }

    $numberofslides = get_config('theme_essential_uv', 'numberofslides');
    for ($i = 1; $i <= $numberofslides; $i++) {
        // This is the descriptor for the slide.
        $name = 'theme_essential_uv/slide'.$i.'info';
        $heading = get_string('slideno', 'theme_essential_uv', array('slide' => $i));
        $information = get_string('slidenodesc', 'theme_essential_uv', array('slide' => $i));
        $setting = new admin_setting_heading($name, $heading, $information);
        $essential_uvsettingsslideshow->add($setting);

        // Title.
        $name = 'theme_essential_uv/slide'.$i;
        $title = get_string('slidetitle', 'theme_essential_uv');
        $description = get_string('slidetitledesc', 'theme_essential_uv');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsslideshow->add($setting);

        // Image.
        $name = 'theme_essential_uv/slide'.$i.'image';
        $title = get_string('slideimage', 'theme_essential_uv');
        $description = get_string('slideimagedesc', 'theme_essential_uv');
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'slide'.$i.'image');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsslideshow->add($setting);

        // Caption text.
        $name = 'theme_essential_uv/slide'.$i.'caption';
        $title = get_string('slidecaption', 'theme_essential_uv');
        $description = get_string('slidecaptiondesc', 'theme_essential_uv');
        $default = '';
        $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsslideshow->add($setting);

        // URL.
        $name = 'theme_essential_uv/slide'.$i.'url';
        $title = get_string('slideurl', 'theme_essential_uv');
        $description = get_string('slideurldesc', 'theme_essential_uv');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsslideshow->add($setting);

        // URL target.
        $name = 'theme_essential_uv/slide'.$i.'target';
        $title = get_string('slideurltarget', 'theme_essential_uv');
        $description = get_string('slideurltargetdesc', 'theme_essential_uv');
        $target1 = get_string('slideurltargetself', 'theme_essential_uv');
        $target2 = get_string('slideurltargetnew', 'theme_essential_uv');
        $target3 = get_string('slideurltargetparent', 'theme_essential_uv');
        $default = '_blank';
        $choices = array('_self' => $target1, '_blank' => $target2, '_parent' => $target3);
        $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingsslideshow->add($setting);
    }

    $essential_uvsettingsslideshow->add($essential_uvreadme);
    $essential_uvsettingsslideshow->add($essential_uvadvert);
}
$ADMIN->add('theme_essential_uv', $essential_uvsettingsslideshow);

// Category course title image settings.
$enablecategoryctics = get_config('theme_essential_uv', 'enablecategoryctics');
if ($enablecategoryctics) {
    $essential_uvsettingscategoryctititle = get_string('categoryctiheadingcs', 'theme_essential_uv');
} else {
    $essential_uvsettingscategoryctititle = get_string('categoryctiheading', 'theme_essential_uv');
}
$essential_uvsettingscategorycti = new admin_settingpage('theme_essential_uv_categorycti', $essential_uvsettingscategoryctititle);
if ($ADMIN->fulltree) {
    global $CFG;
    if (file_exists("{$CFG->dirroot}/theme/essential_uv/essential_uv_admin_setting_configinteger.php")) {
        require_once($CFG->dirroot . '/theme/essential_uv/essential_uv_admin_setting_configinteger.php');
    } else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/essential_uv/essential_uv_admin_setting_configinteger.php")) {
        require_once($CFG->themedir . '/essential_uv/essential_uv_admin_setting_configinteger.php');
    }

    $essential_uvsettingscategorycti->add(new admin_setting_heading('theme_essential_uv_categorycti',
        get_string('categoryctiheadingsub', 'theme_essential_uv'),
        format_text(get_string('categoryctidesc', 'theme_essential_uv'), FORMAT_MARKDOWN)));

    // Category course title images.
    $name = 'theme_essential_uv/enablecategorycti';
    $title = get_string('enablecategorycti', 'theme_essential_uv');
    $description = get_string('enablecategoryctidesc', 'theme_essential_uv');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscategorycti->add($setting);

    // Category course title image setting pages.
    $name = 'theme_essential_uv/enablecategoryctics';
    $title = get_string('enablecategoryctics', 'theme_essential_uv');
    $description = get_string('enablecategorycticsdesc', 'theme_essential_uv');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscategorycti->add($setting);

    // We only want to output category course title image options if the parent setting is enabled.
    if (get_config('theme_essential_uv', 'enablecategorycti')) {
        $essential_uvsettingscategorycti->add(new admin_setting_heading('theme_essential_uv_categorycticourses',
            get_string('ctioverride', 'theme_essential_uv'), get_string('ctioverridedesc', 'theme_essential_uv')));

        // Overridden image height.
        $name = 'theme_essential_uv/ctioverrideheight';
        $title = get_string('ctioverrideheight', 'theme_essential_uv');
        $default = 200;
        $lower = 40;
        $upper = 400;
        $description = get_string('ctioverrideheightdesc', 'theme_essential_uv',
            array('lower' => $lower, 'upper' => $upper));
        $setting = new essential_uv_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
        $essential_uvsettingscategorycti->add($setting);

        // Overridden course title text colour setting.
        $name = 'theme_essential_uv/ctioverridetextcolour';
        $title = get_string('ctioverridetextcolour', 'theme_essential_uv');
        $description = get_string('ctioverridetextcolourdesc', 'theme_essential_uv');
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $essential_uvsettingscategorycti->add($setting);

        // Overridden course title text background colour setting.
        $name = 'theme_essential_uv/ctioverridetextbackgroundcolour';
        $title = get_string('ctioverridetextbackgroundcolour', 'theme_essential_uv');
        $description = get_string('ctioverridetextbackgroundcolourdesc', 'theme_essential_uv');
        $default = '#c51230';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $essential_uvsettingscategorycti->add($setting);

        $opactitychoices = array(
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
            '1.0' => '1.0'
        );

        // Overridden course title text background opacity setting.
        $name = 'theme_essential_uv/ctioverridetextbackgroundopacity';
        $title = get_string('ctioverridetextbackgroundopacity', 'theme_essential_uv');
        $description = get_string('ctioverridetextbackgroundopacitydesc', 'theme_essential_uv');
        $default = '0.8';
        $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $opactitychoices);
        $essential_uvsettingscategorycti->add($setting);
    }
}
$ADMIN->add('theme_essential_uv', $essential_uvsettingscategorycti);

// We only want to output category course title image options if the parent setting is enabled.
if (get_config('theme_essential_uv', 'enablecategorycti')) {
    // Get all category IDs and their names.
    $coursecats = \theme_essential_uv\toolbox::get_categories_list();

    if (!$enablecategoryctics) {
        $essential_uvsettingscategoryctimenu = $essential_uvsettingscategorycti;
    }

    // Go through all categories and create the necessary settings.
    foreach ($coursecats as $key => $value) {
        if (($value->depth == 1) && ($enablecategoryctics)) {
            $essential_uvsettingscategoryctimenu = new admin_settingpage('theme_essential_uv_categorycti_'.$value->id,
                get_string('categoryctiheadingcategory', 'theme_essential_uv',
                    array('category' => format_string($value->namechunks[0]))));
        }

        if ($ADMIN->fulltree) {
            $namepath = join(' / ', $value->namechunks);
            // This is the descriptor for category course title image.
            $name = 'theme_essential_uv/categoryctiinfo'.$key;
            $heading = get_string('categoryctiinfo', 'theme_essential_uv', array('category' => $namepath));
            $information = get_string('categoryctiinfodesc', 'theme_essential_uv', array('category' => $namepath));
            $setting = new admin_setting_heading($name, $heading, $information);
            $essential_uvsettingscategoryctimenu->add($setting);

            // Image.
            $name = 'theme_essential_uv/categoryct'.$key.'image';
            $title = get_string('categoryctimage', 'theme_essential_uv', array('category' => $namepath));
            $description = get_string('categoryctimagedesc', 'theme_essential_uv', array('category' => $namepath));
            $setting = new admin_setting_configstoredfile($name, $title, $description, 'categoryct'.$key.'image');
            $setting->set_updatedcallback('theme_reset_all_caches');
            $essential_uvsettingscategoryctimenu->add($setting);

            // Image URL.
            $name = 'theme_essential_uv/categoryctimageurl'.$key;
            $title = get_string('categoryctimageurl', 'theme_essential_uv', array('category' => $namepath));
            $description = get_string('categoryctimageurldesc', 'theme_essential_uv', array('category' => $namepath));
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $essential_uvsettingscategoryctimenu->add($setting);

            // Image height.
            $name = 'theme_essential_uv/categorycti'.$key.'height';
            $title = get_string('categoryctiheight', 'theme_essential_uv', array('category' => $namepath));
            $default = 200;
            $lower = 40;
            $upper = 400;
            $description = get_string('categoryctiheightdesc', 'theme_essential_uv',
                array('category' => $namepath, 'lower' => $lower, 'upper' => $upper));
            $setting = new essential_uv_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $essential_uvsettingscategoryctimenu->add($setting);

            // Category course title text colour setting.
            $name = 'theme_essential_uv/categorycti'.$key.'textcolour';
            $title = get_string('categoryctitextcolour', 'theme_essential_uv', array('category' => $namepath));
            $description = get_string('categoryctitextcolourdesc', 'theme_essential_uv', array('category' => $namepath));
            $default = '#000000';
            $previewconfig = null;
            $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $essential_uvsettingscategoryctimenu->add($setting);

            // Category course title text background colour setting.
            $name = 'theme_essential_uv/categorycti'.$key.'textbackgroundcolour';
            $title = get_string('categoryctitextbackgroundcolour', 'theme_essential_uv', array('category' => $namepath));
            $description = get_string('categoryctitextbackgroundcolourdesc', 'theme_essential_uv', array('category' => $namepath));
            $default = '#ffffff';
            $previewconfig = null;
            $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $essential_uvsettingscategoryctimenu->add($setting);

            // Category course title text background opacity setting.
            $name = 'theme_essential_uv/categorycti'.$key.'textbackgroundopactity';
            $title = get_string('categoryctitextbackgroundopacity', 'theme_essential_uv', array('category' => $namepath));
            $description = get_string('categoryctitextbackgroundopacitydesc', 'theme_essential_uv', array('category' => $namepath));
            $default = '0.8';
            $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $opactitychoices);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $essential_uvsettingscategoryctimenu->add($setting);
        }
        if (($value->depth == 1) && ($enablecategoryctics)) {
            $ADMIN->add('theme_essential_uv', $essential_uvsettingscategoryctimenu);
        }
    }
}

// Category icon settings.
$essential_uvsettingscategoryicon = new admin_settingpage('theme_essential_uv_categoryicon',
    get_string('categoryiconheading', 'theme_essential_uv'));
if ($ADMIN->fulltree) {
    $essential_uvsettingscategoryicon->add(new admin_setting_heading('theme_essential_uv_categoryicon',
        get_string('categoryiconheadingsub', 'theme_essential_uv'),
        format_text(get_string('categoryicondesc', 'theme_essential_uv'), FORMAT_MARKDOWN)));

    // Category icons.
    $name = 'theme_essential_uv/enablecategoryicon';
    $title = get_string('enablecategoryicon', 'theme_essential_uv');
    $description = get_string('enablecategoryicondesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essential_uvsettingscategoryicon->add($setting);

    // We only want to output category icon options if the parent setting is enabled.
    if (get_config('theme_essential_uv', 'enablecategoryicon')) {

        // Default icon.
        $name = 'theme_essential_uv/defaultcategoryicon';
        $title = get_string('defaultcategoryicon', 'theme_essential_uv');
        $description = get_string('defaultcategoryicondesc', 'theme_essential_uv');
        $default = 'folder-open';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscategoryicon->add($setting);

        // Default image.
        $name = 'theme_essential_uv/defaultcategoryimage';
        $title = get_string('defaultcategoryimage', 'theme_essential_uv');
        $description = get_string('defaultcategoryimagedesc', 'theme_essential_uv');
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'defaultcategoryimage');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscategoryicon->add($setting);

        // Category icons.
        $name = 'theme_essential_uv/enablecustomcategoryicon';
        $title = get_string('enablecustomcategoryicon', 'theme_essential_uv');
        $description = get_string('enablecustomcategoryicondesc', 'theme_essential_uv');
        $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essential_uvsettingscategoryicon->add($setting);

        if (get_config('theme_essential_uv', 'enablecustomcategoryicon')) {
            $iconstring = get_string('icon', 'theme_essential_uv');
            $imagestring = get_string('image', 'theme_essential_uv');

            // This is the descriptor for custom category icons.
            $name = 'theme_essential_uv/categoryiconinfo';
            $heading = get_string('categoryiconinfo', 'theme_essential_uv');
            $information = get_string('categoryiconinfodesc', 'theme_essential_uv');
            $setting = new admin_setting_heading($name, $heading, $information);
            $essential_uvsettingscategoryicon->add($setting);

            // Get the default category icon.
            $defaultcategoryicon = get_config('theme_essential_uv', 'defaultcategoryicon');
            if (empty($defaultcategoryicon)) {
                $defaultcategoryicon = 'folder-open';
            }

            // Get all category IDs and their names.
            $coursecats = \theme_essential_uv\toolbox::get_categories_list();

            // Go through all categories and create the necessary settings.
            foreach ($coursecats as $key => $value) {
                $namepath = join(' / ', $value->namechunks);
                // Category icon for each category.
                $name = 'theme_essential_uv/categoryicon';
                $title = $namepath.' '.$iconstring;
                $description = get_string('categoryiconcategory', 'theme_essential_uv', array('category' => $namepath));
                $default = $defaultcategoryicon;
                $setting = new admin_setting_configtext($name.$key, $title, $description, $default);
                $setting->set_updatedcallback('theme_reset_all_caches');
                $essential_uvsettingscategoryicon->add($setting);

                // Category image for each category.
                $name = 'theme_essential_uv/categoryimage';
                $title = $namepath.' '.$imagestring;
                $description = get_string('categoryimagecategory', 'theme_essential_uv', array('category' => $namepath));
                $setting = new admin_setting_configstoredfile($name.$key, $title, $description, 'categoryimage'.$key);
                $setting->set_updatedcallback('theme_reset_all_caches');
                $essential_uvsettingscategoryicon->add($setting);
            }
            unset($coursecats);
        }
    }

    $essential_uvsettingscategoryicon->add($essential_uvreadme);
    $essential_uvsettingscategoryicon->add($essential_uvadvert);
}
$ADMIN->add('theme_essential_uv', $essential_uvsettingscategoryicon);

// Analytics settings.
$essential_uvsettingsanalytics = new admin_settingpage('theme_essential_uv_analytics', get_string('analytics', 'theme_essential_uv'));
if ($ADMIN->fulltree) {
    $essential_uvsettingsanalytics->add(new admin_setting_heading('theme_essential_uv_analytics',
        get_string('analyticsheadingsub', 'theme_essential_uv'),
        format_text(get_string('analyticsdesc', 'theme_essential_uv'), FORMAT_MARKDOWN)));

    $name = 'theme_essential_uv/analyticsenabled';
    $title = get_string('analyticsenabled', 'theme_essential_uv');
    $description = get_string('analyticsenableddesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $essential_uvsettingsanalytics->add($setting);

    $name = 'theme_essential_uv/analytics';
    $title = get_string('analytics', 'theme_essential_uv');
    $description = get_string('analyticsdesc', 'theme_essential_uv');
    $guniversal = get_string('analyticsguniversal', 'theme_essential_uv');
    $piwik = get_string('analyticspiwik', 'theme_essential_uv');
    $default = 'piwik';
    $choices = array(
        'piwik' => $piwik,
        'guniversal' => $guniversal
    );
    $setting = new essential_uv_admin_setting_configselect($name, $title, $description, $default, $choices);
    $essential_uvsettingsanalytics->add($setting);

    if (get_config('theme_essential_uv', 'analytics') === 'piwik') {
        $name = 'theme_essential_uv/analyticssiteid';
        $title = get_string('analyticssiteid', 'theme_essential_uv');
        $description = get_string('analyticssiteiddesc', 'theme_essential_uv');
        $default = '1';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $essential_uvsettingsanalytics->add($setting);

        $name = 'theme_essential_uv/analyticsimagetrack';
        $title = get_string('analyticsimagetrack', 'theme_essential_uv');
        $description = get_string('analyticsimagetrackdesc', 'theme_essential_uv');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $essential_uvsettingsanalytics->add($setting);

        $name = 'theme_essential_uv/analyticssiteurl';
        $title = get_string('analyticssiteurl', 'theme_essential_uv');
        $description = get_string('analyticssiteurldesc', 'theme_essential_uv');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $essential_uvsettingsanalytics->add($setting);

        $name = 'theme_essential_uv/analyticsuseuserid';
        $title = get_string('analyticsuseuserid', 'theme_essential_uv');
        $description = get_string('analyticsuseuseriddesc', 'theme_essential_uv');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $essential_uvsettingsanalytics->add($setting);
    } else if (get_config('theme_essential_uv', 'analytics') === 'guniversal') {
        $name = 'theme_essential_uv/analyticstrackingid';
        $title = get_string('analyticstrackingid', 'theme_essential_uv');
        $description = get_string('analyticstrackingiddesc', 'theme_essential_uv');
        $default = 'UA-XXXXXXXX-X';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $essential_uvsettingsanalytics->add($setting);
    }

    $name = 'theme_essential_uv/analyticstrackadmin';
    $title = get_string('analyticstrackadmin', 'theme_essential_uv');
    $description = get_string('analyticstrackadmindesc', 'theme_essential_uv');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $essential_uvsettingsanalytics->add($setting);

    $name = 'theme_essential_uv/analyticscleanurl';
    $title = get_string('analyticscleanurl', 'theme_essential_uv');
    $description = get_string('analyticscleanurldesc', 'theme_essential_uv');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $essential_uvsettingsanalytics->add($setting);

    $essential_uvsettingsanalytics->add($essential_uvreadme);
    $essential_uvsettingsanalytics->add($essential_uvadvert);
}
$ADMIN->add('theme_essential_uv', $essential_uvsettingsanalytics);

// Properties.
$essential_uvsettingsprops = new admin_settingpage('theme_essential_uv_props', get_string('properties', 'theme_essential_uv'));
if ($ADMIN->fulltree) {
    if (file_exists("{$CFG->dirroot}/theme/essential_uv/essential_uv_admin_setting_getprops.php")) {
        require_once($CFG->dirroot . '/theme/essential_uv/essential_uv_admin_setting_getprops.php');
        require_once($CFG->dirroot . '/theme/essential_uv/essential_uv_admin_setting_putprops.php');
    } else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/essential_uv/essential_uv_admin_setting_getprops.php")) {
        require_once($CFG->themedir . '/essential_uv/essential_uv_admin_setting_getprops.php');
        require_once($CFG->themedir . '/essential_uv/essential_uv_admin_setting_putprops.php');
    }

    $essential_uvsettingsprops->add(new admin_setting_heading('theme_essential_uv_props',
        get_string('propertiessub', 'theme_essential_uv'),
        format_text(get_string('propertiesdesc', 'theme_essential_uv'), FORMAT_MARKDOWN)));

    $essential_uvexportprops = optional_param('theme_essential_uv_getprops_saveprops', 0, PARAM_INT);
    $essential_uvprops = \theme_essential_uv\toolbox::compile_properties('essential_uv');
    $essential_uvsettingsprops->add(new essential_uv_admin_setting_getprops('theme_essential_uv_getprops',
        get_string('propertiesproperty', 'theme_essential_uv'),
        get_string('propertiesvalue', 'theme_essential_uv'),
        $essential_uvprops,
        'theme_essential_uv_props',
        get_string('propertiesreturn', 'theme_essential_uv'),
        get_string('propertiesexport', 'theme_essential_uv'),
        $essential_uvexportprops
    ));

    $setting = new essential_uv_admin_setting_putprops('theme_essential_uv_putprops',
        get_string('putpropertiesname', 'theme_essential_uv'),
        get_string('putpropertiesdesc', 'theme_essential_uv'),
        'essential_uv',
        '\theme_essential_uv\toolbox::put_properties'
    );
    $setting->set_updatedcallback('purge_all_caches');
    $essential_uvsettingsprops->add($setting);
}
$ADMIN->add('theme_essential_uv', $essential_uvsettingsprops);

// Style guide.
$essential_uvsettingsstyleguide = new admin_settingpage('theme_essential_uv_styleguide', get_string('styleguide', 'theme_essential_uv'));
if ($ADMIN->fulltree) {
    if (file_exists("{$CFG->dirroot}/theme/essential_uv/essential_uv_admin_setting_styleguide.php")) {
        require_once($CFG->dirroot . '/theme/essential_uv/essential_uv_admin_setting_styleguide.php');
    } else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/essential_uv/essential_uv_admin_setting_styleguide.php")) {
        require_once($CFG->themedir . '/essential_uv/essential_uv_admin_setting_styleguide.php');
    }
    $essential_uvsettingsstyleguide->add(new essential_uv_admin_setting_styleguide('theme_essential_uv_styleguide',
        get_string('styleguidesub', 'theme_essential_uv'),
        get_string('styleguidedesc', 'theme_essential_uv',
            array(
                'origcodelicenseurl' => html_writer::link('http://www.apache.org/licenses/LICENSE-2.0', 'Apache License v2.0',
                    array('target' => '_blank')),
                'holderlicenseurl' => html_writer::link('https://github.com/imsky/holder#license', 'MIT',
                    array('target' => '_blank')),
                'thiscodelicenseurl' => html_writer::link('http://www.gnu.org/copyleft/gpl.html', 'GPLv3',
                    array('target' => '_blank')),
                'compatible' => html_writer::link('http://www.gnu.org/licenses/license-list.en.html#apache2', 'compatible',
                    array('target' => '_blank')),
                'contentlicenseurl' => html_writer::link('http://creativecommons.org/licenses/by/3.0/', 'CC BY 3.0',
                    array('target' => '_blank')),
                'globalsettings' => html_writer::link('http://getbootstrap.com/2.3.2/scaffolding.html#global', 'Global settings',
                    array('target' => '_blank'))
            )
        )
    ));
}
$ADMIN->add('theme_essential_uv', $essential_uvsettingsstyleguide);
