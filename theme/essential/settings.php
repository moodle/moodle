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
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @copyright   2014 Gareth J Barnard, David Bezemer
 * @copyright   2013 Julian Ridden
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
$settings = null; // Unsets the default $settings object initialised by Moodle.

// Create own category and define pages.
$ADMIN->add('themes', new admin_category('theme_essential', 'Essential'));

// Generic settings.
$essentialsettingsgeneric = new admin_settingpage('theme_essential_generic', get_string('genericsettings', 'theme_essential'));
// Initialise individual settings only if admin pages require them.
if ($ADMIN->fulltree) {
    global $CFG;
    if (file_exists("{$CFG->dirroot}/theme/essential/essential_admin_setting_configselect.php")) {
        require_once($CFG->dirroot . '/theme/essential/essential_admin_setting_configselect.php');
        require_once($CFG->dirroot . '/theme/essential/essential_admin_setting_configinteger.php');
        require_once($CFG->dirroot . '/theme/essential/essential_admin_setting_advertising.php');
    } else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/essential/essential_admin_setting_configselect.php")) {
        require_once($CFG->themedir . '/essential/essential_admin_setting_configselect.php');
        require_once($CFG->themedir . '/essential/essential_admin_setting_configinteger.php');
        require_once($CFG->themedir . '/essential/essential_admin_setting_advertising.php');
    }

    $sponsor = new moodle_url('http://moodle.org/user/profile.php?id=442195');
    $sponsor = html_writer::link($sponsor, get_string('paypal_click', 'theme_essential'), array('target' => '_blank'));

    $essentialsettingsgeneric->add(new admin_setting_heading('theme_essential_generalsponsor',
        get_string('sponsor_title', 'theme_essential'),
        get_string('sponsor_desc', 'theme_essential').get_string('paypal_desc', 'theme_essential', array('url' => $sponsor)).
        get_string('sponsor_desc2', 'theme_essential')));
    $essentialsettingsgeneric->add(new admin_setting_heading('theme_essential_generalheading',
        get_string('generalheadingsub', 'theme_essential'),
        format_text(get_string('generalheadingdesc', 'theme_essential'), FORMAT_MARKDOWN)));

    // Toggle flat navigation.
    $name = 'theme_essential/flatnavigation';
    $title = get_string('flatnavigation', 'theme_essential');
    $description = get_string('flatnavigationdesc', 'theme_essential');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsgeneric->add($setting);

    // Page background image.
    $name = 'theme_essential/pagebackground';
    $title = get_string('pagebackground', 'theme_essential');
    $description = get_string('pagebackgrounddesc', 'theme_essential');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'pagebackground');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsgeneric->add($setting);

    // Background style.
    $name = 'theme_essential/pagebackgroundstyle';
    $title = get_string('pagebackgroundstyle', 'theme_essential');
    $description = get_string('pagebackgroundstyledesc', 'theme_essential');
    $default = 'fixed';
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default,
        array(
            'fixed' => get_string('stylefixed', 'theme_essential'),
            'tiled' => get_string('styletiled', 'theme_essential'),
            'stretch' => get_string('stylestretch', 'theme_essential')
        )
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsgeneric->add($setting);

    // Fixed or variable width.
    $name = 'theme_essential/pagewidth';
    $title = get_string('pagewidth', 'theme_essential');
    $description = get_string('pagewidthdesc', 'theme_essential');
    $default = 1200;
    $choices = array(
        960 => get_string('fixedwidthnarrow', 'theme_essential'),
        1200 => get_string('fixedwidthnormal', 'theme_essential'),
        1400 => get_string('fixedwidthwide', 'theme_essential'),
        100 => get_string('variablewidth', 'theme_essential'));
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsgeneric->add($setting);

    // Toggle page top blocks.
    $name = 'theme_essential/pagetopblocks';
    $title = get_string('pagetopblocks', 'theme_essential');
    $description = get_string('pagetopblocksdesc', 'theme_essential');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsgeneric->add($setting);

    // Page top blocks per row.
    $name = 'theme_essential/pagetopblocksperrow';
    $title = get_string('pagetopblocksperrow', 'theme_essential');
    $default = 1;
    $lower = 1;
    $upper = 4;
    $description = get_string('pagetopblocksperrowdesc', 'theme_essential',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $essentialsettingsgeneric->add($setting);

    // Page bottom blocks per row.
    $name = 'theme_essential/pagebottomblocksperrow';
    $title = get_string('pagebottomblocksperrow', 'theme_essential');
    $default = 4;
    $lower = 1;
    $upper = 4;
    $description = get_string('pagebottomblocksperrowdesc', 'theme_essential',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $essentialsettingsgeneric->add($setting);

    // User image border radius.
    $name = 'theme_essential/userimageborderradius';
    $title = get_string('userimageborderradius', 'theme_essential');
    $default = 90;
    $lower = 0;
    $upper = 90;
    $description = get_string('userimageborderradiusdesc', 'theme_essential',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsgeneric->add($setting);

    // Custom favicon.
    $name = 'theme_essential/favicon';
    $title = get_string('favicon', 'theme_essential');
    $description = get_string('favicondesc', 'theme_essential');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'favicon');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsgeneric->add($setting);

    // Custom CSS file.
    $name = 'theme_essential/customcss';
    $title = get_string('customcss', 'theme_essential');
    $description = get_string('customcssdesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsgeneric->add($setting);

    $readme = new moodle_url('/theme/essential/README.txt');
    $readme = html_writer::link($readme, get_string('readme_click', 'theme_essential'), array('target' => '_blank'));

    $essentialreadme = new admin_setting_heading('theme_essential_readme',
        get_string('readme_title', 'theme_essential'), get_string('readme_desc', 'theme_essential', array('url' => $readme)));
    $essentialsettingsgeneric->add($essentialreadme);

    $essentialadvert = new essential_admin_setting_advertising('theme_essential_advert',
        get_string('advert_heading', 'theme_essential'), get_string('advert_tagline', 'theme_essential'),
        'http://www.moodlebites.com/mod/page/view.php?id=3208',
        $OUTPUT->image_url('adverts/tdl1', 'theme_essential'), get_string('advert_alttext', 'theme_essential'));
    $essentialsettingsgeneric->add($essentialadvert);
}
$ADMIN->add('theme_essential', $essentialsettingsgeneric);

// Feature settings.
$essentialsettingsfeature = new admin_settingpage('theme_essential_feature', get_string('featureheading', 'theme_essential'));
if ($ADMIN->fulltree) {
    global $CFG;
    if (file_exists("{$CFG->dirroot}/theme/essential/essential_admin_setting_configinteger.php")) {
        require_once($CFG->dirroot . '/theme/essential/essential_admin_setting_configinteger.php');
    } else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/essential/essential_admin_setting_configinteger.php")) {
        require_once($CFG->themedir . '/essential/essential_admin_setting_configinteger.php');
    }

    $essentialsettingsfeature->add(new admin_setting_heading('theme_essential_feature',
        get_string('featureheadingsub', 'theme_essential'),
        format_text(get_string('featuredesc', 'theme_essential'), FORMAT_MARKDOWN)));

    // Course content search.
    $name = 'theme_essential/coursecontentsearch';
    $title = get_string('coursecontentsearch', 'theme_essential');
    $description = get_string('coursecontentsearchdesc', 'theme_essential');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfeature->add($setting);

    // Course content search type default.
    $name = 'theme_essential/searchallcoursecontentdefault';
    $title = get_string('searchallcoursecontentdefault', 'theme_essential');
    $description = get_string('searchallcoursecontentdefaultdesc', 'theme_essential');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $essentialsettingsfeature->add($setting);

    // Custom scrollbars.
    $name = 'theme_essential/customscrollbars';
    $title = get_string('customscrollbars', 'theme_essential');
    $description = get_string('customscrollbarsdesc', 'theme_essential');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfeature->add($setting);

    // Fitvids.
    $name = 'theme_essential/fitvids';
    $title = get_string('fitvids', 'theme_essential');
    $description = get_string('fitvidsdesc', 'theme_essential');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfeature->add($setting);

    // Floating submit buttons.
    $name = 'theme_essential/floatingsubmitbuttons';
    $title = get_string('floatingsubmitbuttons', 'theme_essential');
    $description = get_string('floatingsubmitbuttonsdesc', 'theme_essential');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $essentialsettingsfeature->add($setting);

    // Custom or standard layout.
    $name = 'theme_essential/layout';
    $title = get_string('layout', 'theme_essential');
    $description = get_string('layoutdesc', 'theme_essential');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfeature->add($setting);

    // Course title position.
    $name = 'theme_essential/coursetitleposition';
    $title = get_string('coursetitleposition', 'theme_essential');
    $description = get_string('coursetitlepositiondesc', 'theme_essential');
    $default = 'within';
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default,
        array(
            'above' => get_string('above', 'theme_essential'),
            'within' => get_string('within', 'theme_essential')
        )
    );
    $essentialsettingsfeature->add($setting);

    // Categories in the course breadcrumb.
    $name = 'theme_essential/categoryincoursebreadcrumbfeature';
    $title = get_string('categoryincoursebreadcrumbfeature', 'theme_essential');
    $description = get_string('categoryincoursebreadcrumbfeaturedesc', 'theme_essential');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $essentialsettingsfeature->add($setting);

    // Return to section.
    $name = 'theme_essential/returntosectionfeature';
    $title = get_string('returntosectionfeature', 'theme_essential');
    $description = get_string('returntosectionfeaturedesc', 'theme_essential');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $essentialsettingsfeature->add($setting);

    // Return to section name text limit.
    $name = 'theme_essential/returntosectiontextlimitfeature';
    $title = get_string('returntosectiontextlimitfeature', 'theme_essential');
    $default = 15;
    $lower = 5;
    $upper = 40;
    $description = get_string('returntosectiontextlimitfeaturedesc', 'theme_essential',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $essentialsettingsfeature->add($setting);

    // Login background image.
    $name = 'theme_essential/loginbackground';
    $title = get_string('loginbackground', 'theme_essential');
    $description = get_string('loginbackgrounddesc', 'theme_essential');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbackground');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfeature->add($setting);

    // Login background style.
    $name = 'theme_essential/loginbackgroundstyle';
    $title = get_string('loginbackgroundstyle', 'theme_essential');
    $description = get_string('loginbackgroundstyledesc', 'theme_essential');
    $default = 'cover';
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default,
        array(
            'cover' => get_string('stylecover', 'theme_essential'),
            'stretch' => get_string('stylestretch', 'theme_essential')
        )
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfeature->add($setting);

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
    $name = 'theme_essential/loginbackgroundopacity';
    $title = get_string('loginbackgroundopacity', 'theme_essential');
    $description = get_string('loginbackgroundopacitydesc', 'theme_essential');
    $default = '0.8';
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $opactitychoices);
    $essentialsettingsfeature->add($setting);

    $essentialsettingsfeature->add($essentialreadme);
    $essentialsettingsfeature->add($essentialadvert);
}
$ADMIN->add('theme_essential', $essentialsettingsfeature);

// Colour settings.
$essentialsettingscolour = new admin_settingpage('theme_essential_colour', get_string('colorheading', 'theme_essential'));
if ($ADMIN->fulltree) {
    $essentialsettingscolour->add(new admin_setting_heading('theme_essential_colour',
        get_string('colorheadingsub', 'theme_essential'),
        format_text(get_string('colordesc', 'theme_essential'), FORMAT_MARKDOWN)));

    // Main theme colour setting.
    $name = 'theme_essential/themecolor';
    $title = get_string('themecolor', 'theme_essential');
    $description = get_string('themecolordesc', 'theme_essential');
    $default = '#30add1';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Main theme text colour setting.
    $name = 'theme_essential/themetextcolor';
    $title = get_string('themetextcolor', 'theme_essential');
    $description = get_string('themetextcolordesc', 'theme_essential');
    $default = '#217a94';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Main theme link colour setting.
    $name = 'theme_essential/themeurlcolor';
    $title = get_string('themeurlcolor', 'theme_essential');
    $description = get_string('themeurlcolordesc', 'theme_essential');
    $default = '#943b21';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Main theme hover colour setting.
    $name = 'theme_essential/themehovercolor';
    $title = get_string('themehovercolor', 'theme_essential');
    $description = get_string('themehovercolordesc', 'theme_essential');
    $default = '#6a2a18';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Icon colour setting.
    $name = 'theme_essential/themeiconcolor';
    $title = get_string('themeiconcolor', 'theme_essential');
    $description = get_string('themeiconcolordesc', 'theme_essential');
    $default = '#30add1';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Side-pre block background colour setting.
    $name = 'theme_essential/themesidepreblockbackgroundcolour';
    $title = get_string('themesidepreblockbackgroundcolour', 'theme_essential');
    $description = get_string('themesidepreblockbackgroundcolourdesc', 'theme_essential');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Side-pre block text colour setting.
    $name = 'theme_essential/themesidepreblocktextcolour';
    $title = get_string('themesidepreblocktextcolour', 'theme_essential');
    $description = get_string('themesidepreblocktextcolourdesc', 'theme_essential');
    $default = '#217a94';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Side-pre block url colour setting.
    $name = 'theme_essential/themesidepreblockurlcolour';
    $title = get_string('themesidepreblockurlcolour', 'theme_essential');
    $description = get_string('themesidepreblockurlcolourdesc', 'theme_essential');
    $default = '#943b21';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Side-pre block url hover colour setting.
    $name = 'theme_essential/themesidepreblockhovercolour';
    $title = get_string('themesidepreblockhovercolour', 'theme_essential');
    $description = get_string('themesidepreblockhovercolourdesc', 'theme_essential');
    $default = '#6a2a18';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Default button text colour setting.
    $name = 'theme_essential/themedefaultbuttontextcolour';
    $title = get_string('themedefaultbuttontextcolour', 'theme_essential');
    $description = get_string('themedefaultbuttontextcolourdesc', 'theme_essential');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Default button text hover colour setting.
    $name = 'theme_essential/themedefaultbuttontexthovercolour';
    $title = get_string('themedefaultbuttontexthovercolour', 'theme_essential');
    $description = get_string('themedefaultbuttontexthovercolourdesc', 'theme_essential');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Default button background colour setting.
    $name = 'theme_essential/themedefaultbuttonbackgroundcolour';
    $title = get_string('themedefaultbuttonbackgroundcolour', 'theme_essential');
    $description = get_string('themedefaultbuttonbackgroundcolourdesc', 'theme_essential');
    $default = '#30add1';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Default button background hover colour setting.
    $name = 'theme_essential/themedefaultbuttonbackgroundhovercolour';
    $title = get_string('themedefaultbuttonbackgroundhovercolour', 'theme_essential');
    $description = get_string('themedefaultbuttonbackgroundhovercolourdesc', 'theme_essential');
    $default = '#3ad4ff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Navigation colour setting.
    $name = 'theme_essential/themenavcolor';
    $title = get_string('themenavcolor', 'theme_essential');
    $description = get_string('themenavcolordesc', 'theme_essential');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Theme stripe text colour setting.
    $name = 'theme_essential/themestripetextcolour';
    $title = get_string('themestripetextcolour', 'theme_essential');
    $description = get_string('themestripetextcolourdesc', 'theme_essential');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Theme stripe background colour setting.
    $name = 'theme_essential/themestripebackgroundcolour';
    $title = get_string('themestripebackgroundcolour', 'theme_essential');
    $description = get_string('themestripebackgroundcolourdesc', 'theme_essential');
    $default = '#ff9a34';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Theme stripe url colour setting.
    $name = 'theme_essential/themestripeurlcolour';
    $title = get_string('themestripeurlcolour', 'theme_essential');
    $description = get_string('themestripeurlcolourdesc', 'theme_essential');
    $default = '#25849f';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Quiz \'Submit all and finish\' text colour setting.
    $name = 'theme_essential/themequizsubmittextcolour';
    $title = get_string('themequizsubmittextcolour', 'theme_essential');
    $description = get_string('themequizsubmittextcolourdesc', 'theme_essential');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Quiz \'Submit all and finish\' text hover colour setting.
    $name = 'theme_essential/themequizsubmittexthovercolour';
    $title = get_string('themequizsubmittexthovercolour', 'theme_essential');
    $description = get_string('themequizsubmittexthovercolourdesc', 'theme_essential');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Quiz \'Submit all and finish\' background colour setting.
    $name = 'theme_essential/themequizsubmitbackgroundcolour';
    $title = get_string('themequizsubmitbackgroundcolour', 'theme_essential');
    $description = get_string('themequizsubmitbackgroundcolourdesc', 'theme_essential');
    $default = '#ff9a34';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Quiz \'Submit all and finish\' background hover colour setting.
    $name = 'theme_essential/themequizsubmitbackgroundhovercolour';
    $title = get_string('themequizsubmitbackgroundhovercolour', 'theme_essential');
    $description = get_string('themequizsubmitbackgroundhovercolourdesc', 'theme_essential');
    $default = '#ffaf60';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // This is the descriptor for the footer.
    $name = 'theme_essential/footercolorinfo';
    $heading = get_string('footercolors', 'theme_essential');
    $information = get_string('footercolorsdesc', 'theme_essential');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essentialsettingscolour->add($setting);

    // Footer background colour setting.
    $name = 'theme_essential/footercolor';
    $title = get_string('footercolor', 'theme_essential');
    $description = get_string('footercolordesc', 'theme_essential');
    $default = '#30add1';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Footer text colour setting.
    $name = 'theme_essential/footertextcolor';
    $title = get_string('footertextcolor', 'theme_essential');
    $description = get_string('footertextcolordesc', 'theme_essential');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Footer heading colour setting.
    $name = 'theme_essential/footerheadingcolor';
    $title = get_string('footerheadingcolor', 'theme_essential');
    $description = get_string('footerheadingcolordesc', 'theme_essential');
    $default = '#cccccc';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Footer block background colour setting.
    $name = 'theme_essential/footerblockbackgroundcolour';
    $title = get_string('footerblockbackgroundcolour', 'theme_essential');
    $description = get_string('footerblockbackgroundcolourdesc', 'theme_essential');
    $default = '#cccccc';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Footer block text colour setting.
    $name = 'theme_essential/footerblocktextcolour';
    $title = get_string('footerblocktextcolour', 'theme_essential');
    $description = get_string('footerblocktextcolourdesc', 'theme_essential');
    $default = '#000000';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Footer block URL colour setting.
    $name = 'theme_essential/footerblockurlcolour';
    $title = get_string('footerblockurlcolour', 'theme_essential');
    $description = get_string('footerblockurlcolourdesc', 'theme_essential');
    $default = '#000000';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Footer block URL hover colour setting.
    $name = 'theme_essential/footerblockhovercolour';
    $title = get_string('footerblockhovercolour', 'theme_essential');
    $description = get_string('footerblockhovercolourdesc', 'theme_essential');
    $default = '#555555';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Footer seperator colour setting.
    $name = 'theme_essential/footersepcolor';
    $title = get_string('footersepcolor', 'theme_essential');
    $description = get_string('footersepcolordesc', 'theme_essential');
    $default = '#313131';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Footer URL colour setting.
    $name = 'theme_essential/footerurlcolor';
    $title = get_string('footerurlcolor', 'theme_essential');
    $description = get_string('footerurlcolordesc', 'theme_essential');
    $default = '#cccccc';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // Footer URL hover colour setting.
    $name = 'theme_essential/footerhovercolor';
    $title = get_string('footerhovercolor', 'theme_essential');
    $description = get_string('footerhovercolordesc', 'theme_essential');
    $default = '#bbbbbb';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscolour->add($setting);

    // This is the descriptor for the user theme colours.
    $name = 'theme_essential/alternativethemecolorsinfo';
    $heading = get_string('alternativethemecolors', 'theme_essential');
    $information = get_string('alternativethemecolorsdesc', 'theme_essential');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essentialsettingscolour->add($setting);

    $defaultalternativethemecolors = array('#a430d1', '#d15430', '#5dd130', '#006b94');
    $defaultalternativethemehovercolors = array('#9929c4', '#c44c29', '#53c429', '#4090af');
    $defaultalternativethemestripetextcolors = array('#bdfdb7', '#c3fdd0', '#9f5bfb', '#ff1ebd');
    $defaultalternativethemestripebackgroundcolors = array('#c1009f', '#bc2800', '#b4b2fd', '#0336b4');
    $defaultalternativethemestripeurlcolors = array('#bef500', '#30af67', '#ffe9a6', '#ffab00');

    foreach (range(1, 4) as $alternativethemenumber) {
        // Enables the user to select an alternative colours choice.
        $name = 'theme_essential/enablealternativethemecolors' . $alternativethemenumber;
        $title = get_string('enablealternativethemecolors', 'theme_essential', $alternativethemenumber);
        $description = get_string('enablealternativethemecolorsdesc', 'theme_essential', $alternativethemenumber);
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // User theme colour name.
        $name = 'theme_essential/alternativethemename' . $alternativethemenumber;
        $title = get_string('alternativethemename', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemenamedesc', 'theme_essential', $alternativethemenumber);
        $default = get_string('alternativecolors', 'theme_essential', $alternativethemenumber);
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // User theme colour setting.
        $name = 'theme_essential/alternativethemecolor' . $alternativethemenumber;
        $title = get_string('alternativethemecolor', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemecolordesc', 'theme_essential', $alternativethemenumber);
        $default = $defaultalternativethemecolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme text colour setting.
        $name = 'theme_essential/alternativethemetextcolor' . $alternativethemenumber;
        $title = get_string('alternativethemetextcolor', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemetextcolordesc', 'theme_essential', $alternativethemenumber);
        $default = $defaultalternativethemecolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme link colour setting.
        $name = 'theme_essential/alternativethemeurlcolor' . $alternativethemenumber;
        $title = get_string('alternativethemeurlcolor', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemeurlcolordesc', 'theme_essential', $alternativethemenumber);
        $default = $defaultalternativethemecolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme link hover colour setting.
        $name = 'theme_essential/alternativethemehovercolor' . $alternativethemenumber;
        $title = get_string('alternativethemehovercolor', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemehovercolordesc', 'theme_essential', $alternativethemenumber);
        $default = $defaultalternativethemehovercolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme default button text colour setting.
        $name = 'theme_essential/alternativethemedefaultbuttontextcolour' . $alternativethemenumber;
        $title = get_string('alternativethemedefaultbuttontextcolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemedefaultbuttontextcolourdesc', 'theme_essential', $alternativethemenumber);
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme default button text hover colour setting.
        $name = 'theme_essential/alternativethemedefaultbuttontexthovercolour' . $alternativethemenumber;
        $title = get_string('alternativethemedefaultbuttontexthovercolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemedefaultbuttontexthovercolourdesc', 'theme_essential',
            $alternativethemenumber);
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme default button background colour setting.
        $name = 'theme_essential/alternativethemedefaultbuttonbackgroundcolour' . $alternativethemenumber;
        $title = get_string('alternativethemedefaultbuttonbackgroundcolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemedefaultbuttonbackgroundcolourdesc', 'theme_essential', $alternativethemenumber);
        $default = '#30add1';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme default button background hover colour setting.
        $name = 'theme_essential/alternativethemedefbuttonbackgroundhvrcolour' . $alternativethemenumber;
        $title = get_string('alternativethemedefaultbuttonbackgroundhovercolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemedefaultbuttonbackgroundhovercolourdesc', 'theme_essential',
            $alternativethemenumber);
        $default = '#3ad4ff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme icon colour setting.
        $name = 'theme_essential/alternativethemeiconcolor' . $alternativethemenumber;
        $title = get_string('alternativethemeiconcolor', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemeiconcolordesc', 'theme_essential', $alternativethemenumber);
        $default = $defaultalternativethemecolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme side-pre block background colour setting.
        $name = 'theme_essential/alternativethemesidepreblockbackgroundcolour' . $alternativethemenumber;
        $title = get_string('alternativethemesidepreblockbackgroundcolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemesidepreblockbackgroundcolourdesc', 'theme_essential', $alternativethemenumber);
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme side-pre block text colour setting.
        $name = 'theme_essential/alternativethemesidepreblocktextcolour' . $alternativethemenumber;
        $title = get_string('alternativethemesidepreblocktextcolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemesidepreblocktextcolourdesc', 'theme_essential', $alternativethemenumber);
        $default = $defaultalternativethemecolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme side-pre block link colour setting.
        $name = 'theme_essential/alternativethemesidepreblockurlcolour' . $alternativethemenumber;
        $title = get_string('alternativethemesidepreblockurlcolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemesidepreblockurlcolourdesc', 'theme_essential', $alternativethemenumber);
        $default = $defaultalternativethemecolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme side-pre block text hover colour setting.
        $name = 'theme_essential/alternativethemesidepreblockhovercolour' . $alternativethemenumber;
        $title = get_string('alternativethemesidepreblockhovercolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemesidepreblockhovercolourdesc', 'theme_essential', $alternativethemenumber);
        $default = $defaultalternativethemehovercolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme nav colour setting.
        $name = 'theme_essential/alternativethemenavcolor' . $alternativethemenumber;
        $title = get_string('alternativethemenavcolor', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemenavcolordesc', 'theme_essential', $alternativethemenumber);
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme stripe text colour setting.
        $name = 'theme_essential/alternativethemestripetextcolour' . $alternativethemenumber;
        $title = get_string('alternativethemestripetextcolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemestripetextcolourdesc', 'theme_essential', $alternativethemenumber);
        $default = $defaultalternativethemestripetextcolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme stripe background colour setting.
        $name = 'theme_essential/alternativethemestripebackgroundcolour' . $alternativethemenumber;
        $title = get_string('alternativethemestripebackgroundcolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemestripebackgroundcolourdesc', 'theme_essential', $alternativethemenumber);
        $default = $defaultalternativethemestripebackgroundcolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Theme stripe url colour setting.
        $name = 'theme_essential/alternativethemestripeurlcolour' . $alternativethemenumber;
        $title = get_string('alternativethemestripeurlcolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemestripeurlcolourdesc', 'theme_essential', $alternativethemenumber);
        $default = $defaultalternativethemestripeurlcolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme Quiz \'Submit all and finish\' text colour setting.
        $name = 'theme_essential/alternativethemequizsubmittextcolour' . $alternativethemenumber;
        $title = get_string('alternativethemequizsubmittextcolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemequizsubmittextcolourdesc', 'theme_essential', $alternativethemenumber);
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme Quiz \'Submit all and finish\' text hover colour setting.
        $name = 'theme_essential/alternativethemequizsubmittexthovercolour' . $alternativethemenumber;
        $title = get_string('alternativethemequizsubmittexthovercolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemequizsubmittexthovercolourdesc', 'theme_essential',
            $alternativethemenumber);
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme Quiz \'Submit all and finish\' background colour setting.
        $name = 'theme_essential/alternativethemequizsubmitbackgroundcolour' . $alternativethemenumber;
        $title = get_string('alternativethemequizsubmitbackgroundcolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemequizsubmitbackgroundcolourdesc', 'theme_essential', $alternativethemenumber);
        $default = '#ff9a34';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Alternative theme Quiz \'Submit all and finish\' background hover colour setting.
        $name = 'theme_essential/alternativethemequizsubmitbackgroundhovercolour' . $alternativethemenumber;
        $title = get_string('alternativethemequizsubmitbackgroundhovercolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemequizsubmitbackgroundhovercolourdesc', 'theme_essential',
            $alternativethemenumber);
        $default = '#ffaf60';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Enrolled and not accessed course background colour.
        $name = 'theme_essential/alternativethememycoursesorderenrolbackcolour'.$alternativethemenumber;
        $title = get_string('alternativethememycoursesorderenrolbackcolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethememycoursesorderenrolbackcolourdesc', 'theme_essential', $alternativethemenumber);
        $default = '#a3ebff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Footer background colour setting.
        $name = 'theme_essential/alternativethemefootercolor' . $alternativethemenumber;
        $title = get_string('alternativethemefootercolor', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemefootercolordesc', 'theme_essential', $alternativethemenumber);
        $default = '#30add1';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Footer text colour setting.
        $name = 'theme_essential/alternativethemefootertextcolor' . $alternativethemenumber;
        $title = get_string('alternativethemefootertextcolor', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemefootertextcolordesc', 'theme_essential', $alternativethemenumber);
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Footer heading colour setting.
        $name = 'theme_essential/alternativethemefooterheadingcolor' . $alternativethemenumber;
        $title = get_string('alternativethemefooterheadingcolor', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemefooterheadingcolordesc', 'theme_essential', $alternativethemenumber);
        $default = '#cccccc';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Footer block background colour setting.
        $name = 'theme_essential/alternativethemefooterblockbackgroundcolour' . $alternativethemenumber;
        $title = get_string('alternativethemefooterblockbackgroundcolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemefooterblockbackgroundcolourdesc', 'theme_essential',
                $alternativethemenumber);
        $default = '#cccccc';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Footer block text colour setting.
        $name = 'theme_essential/alternativethemefooterblocktextcolour' . $alternativethemenumber;
        $title = get_string('alternativethemefooterblocktextcolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemefooterblocktextcolourdesc', 'theme_essential',
                $alternativethemenumber);
        $default = '#000000';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Footer block URL colour setting.
        $name = 'theme_essential/alternativethemefooterblockurlcolour' . $alternativethemenumber;
        $title = get_string('alternativethemefooterblockurlcolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemefooterblockurlcolourdesc', 'theme_essential', $alternativethemenumber);
        $default = '#000000';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Footer block URL hover colour setting.
        $name = 'theme_essential/alternativethemefooterblockhovercolour' . $alternativethemenumber;
        $title = get_string('alternativethemefooterblockhovercolour', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemefooterblockhovercolourdesc', 'theme_essential',
                $alternativethemenumber);
        $default = '#555555';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Footer seperator colour setting.
        $name = 'theme_essential/alternativethemefootersepcolor' . $alternativethemenumber;
        $title = get_string('alternativethemefootersepcolor', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemefootersepcolordesc', 'theme_essential', $alternativethemenumber);
        $default = '#313131';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Footer URL colour setting.
        $name = 'theme_essential/alternativethemefooterurlcolor' . $alternativethemenumber;
        $title = get_string('alternativethemefooterurlcolor', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemefooterurlcolordesc', 'theme_essential', $alternativethemenumber);
        $default = '#cccccc';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);

        // Footer URL hover colour setting.
        $name = 'theme_essential/alternativethemefooterhovercolor' . $alternativethemenumber;
        $title = get_string('alternativethemefooterhovercolor', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemefooterhovercolordesc', 'theme_essential', $alternativethemenumber);
        $default = '#bbbbbb';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscolour->add($setting);
    }

    $essentialsettingscolour->add($essentialreadme);
    $essentialsettingscolour->add($essentialadvert);
}
$ADMIN->add('theme_essential', $essentialsettingscolour);

// Header settings.
$essentialsettingsheader = new admin_settingpage('theme_essential_header', get_string('headerheading', 'theme_essential'));
if ($ADMIN->fulltree) {
    global $CFG;
    if (file_exists("{$CFG->dirroot}/theme/essential/essential_admin_setting_configtext.php")) {
        require_once($CFG->dirroot . '/theme/essential/essential_admin_setting_configinteger.php');
        require_once($CFG->dirroot . '/theme/essential/essential_admin_setting_configtext.php');
        require_once($CFG->dirroot . '/theme/essential/essential_admin_setting_configradio.php');
    } else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/essential/essential_admin_setting_configtext.php")) {
        require_once($CFG->themedir . '/essential/essential_admin_setting_configinteger.php');
        require_once($CFG->themedir . '/essential/essential_admin_setting_configtext.php');
        require_once($CFG->themedir . '/essential/essential_admin_setting_configradio.php');
    }

    // New or old navbar.
    $name = 'theme_essential/oldnavbar';
    $title = get_string('oldnavbar', 'theme_essential');
    $description = get_string('oldnavbardesc', 'theme_essential');
    $default = 0;
    $choices = array(
        0 => get_string('navbarabove', 'theme_essential'),
        1 => get_string('navbarbelow', 'theme_essential')
    );
    $images = array(
        0 => 'navbarabove',
        1 => 'navbarbelow'
    );
    $setting = new essential_admin_setting_configradio($name, $title, $description, $default, $choices, false, $images);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // User menu user image border radius.
    $name = 'theme_essential/usermenuuserimageborderradius';
    $title = get_string('usermenuuserimageborderradius', 'theme_essential');
    $default = 4;
    $lower = 0;
    $upper = 90;
    $description = get_string('usermenuuserimageborderradiusdesc', 'theme_essential',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Scrollbars on the dropdown menus.
    $name = 'theme_essential/dropdownmenuscroll';
    $title = get_string('dropdownmenuscroll', 'theme_essential');
    $description = get_string('dropdownmenuscrolldesc', 'theme_essential');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $essentialsettingsheader->add($setting);

    // Dropdown menu maximum height.
    $name = 'theme_essential/dropdownmenumaxheight';
    $title = get_string('dropdownmenumaxheight', 'theme_essential');
    $default = 384;
    $lower = 100;
    $upper = 800;
    $description = get_string('dropdownmenumaxheightdesc', 'theme_essential',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Use the site icon if there is no logo.
    $name = 'theme_essential/usesiteicon';
    $title = get_string('usesiteicon', 'theme_essential');
    $description = get_string('usesiteicondesc', 'theme_essential');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Default Site icon setting.
    $name = 'theme_essential/siteicon';
    $title = get_string('siteicon', 'theme_essential');
    $description = get_string('siteicondesc', 'theme_essential');
    $default = 'laptop';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $essentialsettingsheader->add($setting);

    // Header title setting.
    $name = 'theme_essential/headertitle';
    $title = get_string('headertitle', 'theme_essential');
    $description = get_string('headertitledesc', 'theme_essential');
    $default = '1';
    $choices = array(
        0 => get_string('notitle', 'theme_essential'),
        1 => get_string('fullname', 'theme_essential'),
        2 => get_string('shortname', 'theme_essential'),
        3 => get_string('fullnamesummary', 'theme_essential'),
        4 => get_string('shortnamesummary', 'theme_essential')
    );
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Logo file setting.
    $name = 'theme_essential/logo';
    $title = get_string('logo', 'theme_essential');
    $description = get_string('logodesc', 'theme_essential');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Logo desktop width setting.
    $name = 'theme_essential/logodesktopwidth';
    $title = get_string('logodesktopwidth', 'theme_essential');
    $default = 25;
    $lower = 1;
    $upper = 100;
    $description = get_string('logodesktopwidthdesc', 'theme_essential',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Logo mobile width setting.
    $name = 'theme_essential/logomobilewidth';
    $title = get_string('logomobilewidth', 'theme_essential');
    $default = 10;
    $lower = 1;
    $upper = 100;
    $description = get_string('logomobilewidthdesc', 'theme_essential',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Navbar title setting.
    $name = 'theme_essential/navbartitle';
    $title = get_string('navbartitle', 'theme_essential');
    $description = get_string('navbartitledesc', 'theme_essential');
    $default = '2';
    $choices = array(
        0 => get_string('notitle', 'theme_essential'),
        1 => get_string('fullname', 'theme_essential'),
        2 => get_string('shortname', 'theme_essential')
    );
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Header text colour setting.
    $name = 'theme_essential/headertextcolor';
    $title = get_string('headertextcolor', 'theme_essential');
    $description = get_string('headertextcolordesc', 'theme_essential');
    $default = '#217a94';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Header background image.
    $name = 'theme_essential/headerbackground';
    $title = get_string('headerbackground', 'theme_essential');
    $description = get_string('headerbackgrounddesc', 'theme_essential');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'headerbackground');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Background style.
    $name = 'theme_essential/headerbackgroundstyle';
    $title = get_string('headerbackgroundstyle', 'theme_essential');
    $description = get_string('headerbackgroundstyledesc', 'theme_essential');
    $default = 'tiled';
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default,
        array(
            'fixed' => get_string('stylefixed', 'theme_essential'),
            'tiled' => get_string('styletiled', 'theme_essential')
        )
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Choose breadcrumbstyle.
    $name = 'theme_essential/breadcrumbstyle';
    $title = get_string('breadcrumbstyle', 'theme_essential');
    $description = get_string('breadcrumbstyledesc', 'theme_essential');
    $default = 1;
    $choices = array(
        1 => get_string('breadcrumbstyled', 'theme_essential'),
        4 => get_string('breadcrumbstylednocollapse', 'theme_essential'),
        2 => get_string('breadcrumbsimple', 'theme_essential'),
        3 => get_string('breadcrumbthin', 'theme_essential'),
        0 => get_string('nobreadcrumb', 'theme_essential')
    );
    $images = array(
        1 => 'breadcrumbstyled',
        4 => 'breadcrumbstylednocollapse',
        2 => 'breadcrumbsimple',
        3 => 'breadcrumbthin'
    );
    $setting = new essential_admin_setting_configradio($name, $title, $description, $default, $choices, false, $images);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Header block.
    $name = 'theme_essential/haveheaderblock';
    $title = get_string('haveheaderblock', 'theme_essential');
    $description = get_string('haveheaderblockdesc', 'theme_essential');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $essentialsettingsheader->add($setting);

    $name = 'theme_essential/headerblocksperrow';
    $title = get_string('headerblocksperrow', 'theme_essential');
    $default = 4;
    $lower = 1;
    $upper = 4;
    $description = get_string('headerblocksperrowdesc', 'theme_essential',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $essentialsettingsheader->add($setting);

    // Course menu settings.
    $name = 'theme_essential/mycoursesinfo';
    $heading = get_string('mycoursesinfo', 'theme_essential');
    $information = get_string('mycoursesinfodesc', 'theme_essential');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essentialsettingsheader->add($setting);

    // Toggle courses display in custommenu.
    $name = 'theme_essential/displaymycourses';
    $title = get_string('displaymycourses', 'theme_essential');
    $description = get_string('displaymycoursesdesc', 'theme_essential');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Toggle hidden courses display in custommenu.
    $name = 'theme_essential/displayhiddenmycourses';
    $title = get_string('displayhiddenmycourses', 'theme_essential');
    $description = get_string('displayhiddenmycoursesdesc', 'theme_essential');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    // No need for callback as CSS not changed.
    $essentialsettingsheader->add($setting);

    // Toggle category course sub-menu.
    $name = 'theme_essential/mycoursescatsubmenu';
    $title = get_string('mycoursescatsubmenu', 'theme_essential');
    $description = get_string('mycoursescatsubmenudesc', 'theme_essential');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // My courses order.
    $name = 'theme_essential/mycoursesorder';
    $title = get_string('mycoursesorder', 'theme_essential');
    $description = get_string('mycoursesorderdesc', 'theme_essential');
    $default = 1;
    $choices = array(
        1 => get_string('mycoursesordersort', 'theme_essential'),
        2 => get_string('mycoursesorderid', 'theme_essential'),
        3 => get_string('mycoursesorderlast', 'theme_essential')
    );
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    // No need for callback as CSS not changed.
    $essentialsettingsheader->add($setting);

    // Course ID order.
    $name = 'theme_essential/mycoursesorderidorder';
    $title = get_string('mycoursesorderidorder', 'theme_essential');
    $description = get_string('mycoursesorderidorderdesc', 'theme_essential');
    $default = 1;
    $choices = array(
        1 => get_string('mycoursesorderidasc', 'theme_essential'),
        2 => get_string('mycoursesorderiddes', 'theme_essential')
    );
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    // No need for callback as CSS not changed.
    $essentialsettingsheader->add($setting);

    // Max courses.
    $name = 'theme_essential/mycoursesmax';
    $title = get_string('mycoursesmax', 'theme_essential');
    $default = 0;
    $lower = 0;
    $upper = 20;
    $description = get_string('mycoursesmaxdesc', 'theme_essential',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    // No need for callback as CSS not changed.
    $essentialsettingsheader->add($setting);

    // Set terminology for dropdown course list.
    $name = 'theme_essential/mycoursetitle';
    $title = get_string('mycoursetitle', 'theme_essential');
    $description = get_string('mycoursetitledesc', 'theme_essential');
    $default = 'course';
    $choices = array(
        'course' => get_string('mycourses', 'theme_essential'),
        'unit' => get_string('myunits', 'theme_essential'),
        'class' => get_string('myclasses', 'theme_essential'),
        'module' => get_string('mymodules', 'theme_essential')
    );
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Enrolled and not accessed course background colour.
    $name = 'theme_essential/mycoursesorderenrolbackcolour';
    $title = get_string('mycoursesorderenrolbackcolour', 'theme_essential');
    $description = get_string('mycoursesorderenrolbackcolourdesc', 'theme_essential');
    $default = '#a3ebff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // User menu settings.
    $name = 'theme_essential/usermenu';
    $heading = get_string('usermenu', 'theme_essential');
    $information = get_string('usermenudesc', 'theme_essential');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essentialsettingsheader->add($setting);

    // Helplink type.
    $name = 'theme_essential/helplinktype';
    $title = get_string('helplinktype', 'theme_essential');
    $description = get_string('helplinktypedesc', 'theme_essential');
    $default = 1;
    $choices = array(1 => get_string('email'),
        2 => get_string('url'),
        0 => get_string('none')
    );
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Helplink.
    $name = 'theme_essential/helplink';
    $title = get_string('helplink', 'theme_essential');
    $description = get_string('helplinkdesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Editing menu settings.
    $name = 'theme_essential/editingmenu';
    $heading = get_string('editingmenu', 'theme_essential');
    $information = get_string('editingmenudesc', 'theme_essential');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essentialsettingsheader->add($setting);

    $name = 'theme_essential/displayeditingmenu';
    $title = get_string('displayeditingmenu', 'theme_essential');
    $description = get_string('displayeditingmenudesc', 'theme_essential');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $essentialsettingsheader->add($setting);

    $name = 'theme_essential/hidedefaulteditingbutton';
    $title = get_string('hidedefaulteditingbutton', 'theme_essential');
    $description = get_string('hidedefaulteditingbuttondesc', 'theme_essential');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $essentialsettingsheader->add($setting);

    // Social network settings.
    $essentialsettingsheader->add(new admin_setting_heading('theme_essential_social',
        get_string('socialheadingsub', 'theme_essential'),
        format_text(get_string('socialdesc', 'theme_essential'), FORMAT_MARKDOWN)));

    // Website URL setting.
    $name = 'theme_essential/website';
    $title = get_string('websiteurl', 'theme_essential');
    $description = get_string('websitedesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Facebook URL setting.
    $name = 'theme_essential/facebook';
    $title = get_string('facebookurl', 'theme_essential');
    $description = get_string('facebookdesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Flickr URL setting.
    $name = 'theme_essential/flickr';
    $title = get_string('flickrurl', 'theme_essential');
    $description = get_string('flickrdesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Twitter URL setting.
    $name = 'theme_essential/twitter';
    $title = get_string('twitterurl', 'theme_essential');
    $description = get_string('twitterdesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Google+ URL setting.
    $name = 'theme_essential/googleplus';
    $title = get_string('googleplusurl', 'theme_essential');
    $description = get_string('googleplusdesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // LinkedIn URL setting.
    $name = 'theme_essential/linkedin';
    $title = get_string('linkedinurl', 'theme_essential');
    $description = get_string('linkedindesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Pinterest URL setting.
    $name = 'theme_essential/pinterest';
    $title = get_string('pinteresturl', 'theme_essential');
    $description = get_string('pinterestdesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Instagram URL setting.
    $name = 'theme_essential/instagram';
    $title = get_string('instagramurl', 'theme_essential');
    $description = get_string('instagramdesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // YouTube URL setting.
    $name = 'theme_essential/youtube';
    $title = get_string('youtubeurl', 'theme_essential');
    $description = get_string('youtubedesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Skype URL setting.
    $name = 'theme_essential/skype';
    $title = get_string('skypeuri', 'theme_essential');
    $description = get_string('skypedesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // VKontakte URL setting.
    $name = 'theme_essential/vk';
    $title = get_string('vkurl', 'theme_essential');
    $description = get_string('vkdesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Apps settings.
    $essentialsettingsheader->add(new admin_setting_heading('theme_essential_mobileapps',
        get_string('mobileappsheadingsub', 'theme_essential'),
        format_text(get_string('mobileappsdesc', 'theme_essential'), FORMAT_MARKDOWN)));

    // Android App URL setting.
    $name = 'theme_essential/android';
    $title = get_string('androidurl', 'theme_essential');
    $description = get_string('androiddesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Windows App URL setting.
    $name = 'theme_essential/windows';
    $title = get_string('windowsurl', 'theme_essential');
    $description = get_string('windowsdesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // Windows PhoneApp URL setting.
    $name = 'theme_essential/winphone';
    $title = get_string('winphoneurl', 'theme_essential');
    $description = get_string('winphonedesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // The iOS App URL setting.
    $name = 'theme_essential/ios';
    $title = get_string('iosurl', 'theme_essential');
    $description = get_string('iosdesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // This is the descriptor for iOS icons.
    $name = 'theme_essential/iosiconinfo';
    $heading = get_string('iosicon', 'theme_essential');
    $information = get_string('iosicondesc', 'theme_essential');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essentialsettingsheader->add($setting);

    // The iPhone icon.
    $name = 'theme_essential/iphoneicon';
    $title = get_string('iphoneicon', 'theme_essential');
    $description = get_string('iphoneicondesc', 'theme_essential');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'iphoneicon');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // The iPhone retina icon.
    $name = 'theme_essential/iphoneretinaicon';
    $title = get_string('iphoneretinaicon', 'theme_essential');
    $description = get_string('iphoneretinaicondesc', 'theme_essential');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'iphoneretinaicon');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // The iPad icon.
    $name = 'theme_essential/ipadicon';
    $title = get_string('ipadicon', 'theme_essential');
    $description = get_string('ipadicondesc', 'theme_essential');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'ipadicon');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    // The iPad retina icon.
    $name = 'theme_essential/ipadretinaicon';
    $title = get_string('ipadretinaicon', 'theme_essential');
    $description = get_string('ipadretinaicondesc', 'theme_essential');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'ipadretinaicon');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsheader->add($setting);

    $essentialsettingsheader->add($essentialreadme);
    $essentialsettingsheader->add($essentialadvert);
}
$ADMIN->add('theme_essential', $essentialsettingsheader);

// Font settings.
$essentialsettingsfont = new admin_settingpage('theme_essential_font', get_string('fontsettings', 'theme_essential'));
if ($ADMIN->fulltree) {
    // This is the descriptor for the font settings.
    $name = 'theme_essential/fontheading';
    $heading = get_string('fontheadingsub', 'theme_essential');
    $information = get_string('fontheadingdesc', 'theme_essential');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essentialsettingsfont->add($setting);

    // Font selector.
    $gws = html_writer::link('//www.google.com/fonts',
        get_string('fonttypegoogle', 'theme_essential'), array('target' => '_blank'));
    $name = 'theme_essential/fontselect';
    $title = get_string('fontselect', 'theme_essential');
    $description = get_string('fontselectdesc', 'theme_essential', array('googlewebfonts' => $gws));
    $default = 1;
    $choices = array(
        1 => get_string('fonttypeuser', 'theme_essential'),
        2 => get_string('fonttypegoogle', 'theme_essential'),
        3 => get_string('fonttypecustom', 'theme_essential')
    );
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfont->add($setting);

    // Heading font name.
    $name = 'theme_essential/fontnameheading';
    $title = get_string('fontnameheading', 'theme_essential');
    $description = get_string('fontnameheadingdesc', 'theme_essential');
    $default = 'Verdana';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfont->add($setting);

    // Text font name.
    $name = 'theme_essential/fontnamebody';
    $title = get_string('fontnamebody', 'theme_essential');
    $description = get_string('fontnamebodydesc', 'theme_essential');
    $default = 'Verdana';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfont->add($setting);

    if (get_config('theme_essential', 'fontselect') === "2") {
        // Google font character sets.
        $name = 'theme_essential/fontcharacterset';
        $title = get_string('fontcharacterset', 'theme_essential');
        $description = get_string('fontcharactersetdesc', 'theme_essential');
        $default = 'latin-ext';
        $setting = new admin_setting_configmulticheckbox($name, $title, $description, $default,
            array(
                'latin-ext' => get_string('fontcharactersetlatinext', 'theme_essential'),
                'cyrillic' => get_string('fontcharactersetcyrillic', 'theme_essential'),
                'cyrillic-ext' => get_string('fontcharactersetcyrillicext', 'theme_essential'),
                'greek' => get_string('fontcharactersetgreek', 'theme_essential'),
                'greek-ext' => get_string('fontcharactersetgreekext', 'theme_essential'),
                'vietnamese' => get_string('fontcharactersetvietnamese', 'theme_essential')
            )
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfont->add($setting);
    } else if (get_config('theme_essential', 'fontselect') === "3") {
        // This is the descriptor for the font files.
        $name = 'theme_essential/fontfiles';
        $heading = get_string('fontfiles', 'theme_essential');
        $information = get_string('fontfilesdesc', 'theme_essential');
        $setting = new admin_setting_heading($name, $heading, $information);
        $essentialsettingsfont->add($setting);

        // Heading fonts.
        // TTF font.
        $name = 'theme_essential/fontfilettfheading';
        $title = get_string('fontfilettfheading', 'theme_essential');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilettfheading');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfont->add($setting);

        // OTF font.
        $name = 'theme_essential/fontfileotfheading';
        $title = get_string('fontfileotfheading', 'theme_essential');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfileotfheading');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfont->add($setting);

        // WOFF font.
        $name = 'theme_essential/fontfilewoffheading';
        $title = get_string('fontfilewoffheading', 'theme_essential');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilewoffheading');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfont->add($setting);

        // WOFF2 font.
        $name = 'theme_essential/fontfilewofftwoheading';
        $title = get_string('fontfilewofftwoheading', 'theme_essential');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilewofftwoheading');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfont->add($setting);

        // EOT font.
        $name = 'theme_essential/fontfileeotheading';
        $title = get_string('fontfileeotheading', 'theme_essential');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfileeotheading');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfont->add($setting);

        // SVG font.
        $name = 'theme_essential/fontfilesvgheading';
        $title = get_string('fontfilesvgheading', 'theme_essential');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilesvgheading');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfont->add($setting);

        // Body fonts.
        // TTF font.
        $name = 'theme_essential/fontfilettfbody';
        $title = get_string('fontfilettfbody', 'theme_essential');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilettfbody');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfont->add($setting);

        // OTF font.
        $name = 'theme_essential/fontfileotfbody';
        $title = get_string('fontfileotfbody', 'theme_essential');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfileotfbody');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfont->add($setting);

        // WOFF font.
        $name = 'theme_essential/fontfilewoffbody';
        $title = get_string('fontfilewoffbody', 'theme_essential');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilewoffbody');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfont->add($setting);

        // WOFF2 font.
        $name = 'theme_essential/fontfilewofftwobody';
        $title = get_string('fontfilewofftwobody', 'theme_essential');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilewofftwobody');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfont->add($setting);

        // EOT font.
        $name = 'theme_essential/fontfileeotbody';
        $title = get_string('fontfileeotbody', 'theme_essential');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfileeotbody');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfont->add($setting);

        // SVG font.
        $name = 'theme_essential/fontfilesvgbody';
        $title = get_string('fontfilesvgbody', 'theme_essential');
        $description = '';
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'fontfilesvgbody');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfont->add($setting);
    }

    $essentialsettingsfont->add($essentialreadme);
    $essentialsettingsfont->add($essentialadvert);
}
$ADMIN->add('theme_essential', $essentialsettingsfont);

// Footer settings.
$essentialsettingsfooter = new admin_settingpage('theme_essential_footer', get_string('footerheading', 'theme_essential'));
if ($ADMIN->fulltree) {
    // Copyright setting.
    $name = 'theme_essential/copyright';
    $title = get_string('copyright', 'theme_essential');
    $description = get_string('copyrightdesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $essentialsettingsfooter->add($setting);

    // Footnote setting.
    $name = 'theme_essential/footnote';
    $title = get_string('footnote', 'theme_essential');
    $description = get_string('footnotedesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfooter->add($setting);

    // Performance information display.
    $name = 'theme_essential/perfinfo';
    $title = get_string('perfinfo', 'theme_essential');
    $description = get_string('perfinfodesc', 'theme_essential');
    $perfmax = get_string('perf_max', 'theme_essential');
    $perfmin = get_string('perf_min', 'theme_essential');
    $default = 'min';
    $choices = array('min' => $perfmin, 'max' => $perfmax);
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfooter->add($setting);

    $essentialsettingsfooter->add($essentialreadme);
    $essentialsettingsfooter->add($essentialadvert);
}
$ADMIN->add('theme_essential', $essentialsettingsfooter);

// Frontpage settings.
$essentialsettingsfrontpage = new admin_settingpage('theme_essential_frontpage', get_string('frontpageheading', 'theme_essential'));
if ($ADMIN->fulltree) {

    $name = 'theme_essential/courselistteachericon';
    $title = get_string('courselistteachericon', 'theme_essential');
    $description = get_string('courselistteachericondesc', 'theme_essential');
    $default = 'graduation-cap';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    $essentialsettingsfrontpage->add(new admin_setting_heading('theme_essential_frontcontent',
        get_string('frontcontentheading', 'theme_essential'), ''));

    // Toggle frontpage content.
    $name = 'theme_essential/togglefrontcontent';
    $title = get_string('frontcontent', 'theme_essential');
    $description = get_string('frontcontentdesc', 'theme_essential');
    $alwaysdisplay = get_string('alwaysdisplay', 'theme_essential');
    $displaybeforelogin = get_string('displaybeforelogin', 'theme_essential');
    $displayafterlogin = get_string('displayafterlogin', 'theme_essential');
    $dontdisplay = get_string('dontdisplay', 'theme_essential');
    $default = 0;
    $choices = array(1 => $alwaysdisplay, 2 => $displaybeforelogin, 3 => $displayafterlogin, 0 => $dontdisplay);
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    // Frontpage content.
    $name = 'theme_essential/frontcontentarea';
    $title = get_string('frontcontentarea', 'theme_essential');
    $description = get_string('frontcontentareadesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    $name = 'theme_essential_frontpageblocksheading';
    $heading = get_string('frontpageblocksheading', 'theme_essential');
    $information = '';
    $setting = new admin_setting_heading($name, $heading, $information);
    $essentialsettingsfrontpage->add($setting);

    // Frontpage block alignment.
    $name = 'theme_essential/frontpageblocks';
    $title = get_string('frontpageblocks', 'theme_essential');
    $description = get_string('frontpageblocksdesc', 'theme_essential');
    $before = get_string('beforecontent', 'theme_essential');
    $after = get_string('aftercontent', 'theme_essential');
    $default = 1;
    $choices = array(1 => $before, 0 => $after);
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    // Toggle frontpage home (was middle) blocks.
    $name = 'theme_essential/frontpagemiddleblocks';
    $title = get_string('frontpagemiddleblocks', 'theme_essential');
    $description = get_string('frontpagemiddleblocksdesc', 'theme_essential');
    $alwaysdisplay = get_string('alwaysdisplay', 'theme_essential');
    $displaybeforelogin = get_string('displaybeforelogin', 'theme_essential');
    $displayafterlogin = get_string('displayafterlogin', 'theme_essential');
    $dontdisplay = get_string('dontdisplay', 'theme_essential');
    $default = 0;
    $choices = array(1 => $alwaysdisplay, 2 => $displaybeforelogin, 3 => $displayafterlogin, 0 => $dontdisplay);
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    // Home blocks per row.
    $name = 'theme_essential/frontpagehomeblocksperrow';
    $title = get_string('frontpagehomeblocksperrow', 'theme_essential');
    $default = 3;
    $lower = 1;
    $upper = 4;
    $description = get_string('frontpagehomeblocksperrowdesc', 'theme_essential',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $essentialsettingsfrontpage->add($setting);

    // Toggle frontpage page top blocks.
    $name = 'theme_essential/fppagetopblocks';
    $title = get_string('fppagetopblocks', 'theme_essential');
    $description = get_string('fppagetopblocksdesc', 'theme_essential');
    $alwaysdisplay = get_string('alwaysdisplay', 'theme_essential');
    $displaybeforelogin = get_string('displaybeforelogin', 'theme_essential');
    $displayafterlogin = get_string('displayafterlogin', 'theme_essential');
    $dontdisplay = get_string('dontdisplay', 'theme_essential');
    $default = 3;
    $choices = array(1 => $alwaysdisplay, 2 => $displaybeforelogin, 3 => $displayafterlogin, 0 => $dontdisplay);
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    // Page top blocks per row.
    $name = 'theme_essential/fppagetopblocksperrow';
    $title = get_string('fppagetopblocksperrow', 'theme_essential');
    $default = 3;
    $lower = 1;
    $upper = 4;
    $description = get_string('fppagetopblocksperrowdesc', 'theme_essential',
        array('lower' => $lower, 'upper' => $upper));
    $setting = new essential_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
    $essentialsettingsfrontpage->add($setting);

    // Marketing spot settings.
    $essentialsettingsfrontpage->add(new admin_setting_heading('theme_essential_marketing',
        get_string('marketingheading', 'theme_essential'),
        format_text(get_string('marketingdesc', 'theme_essential'), FORMAT_MARKDOWN)));

    // Toggle marketing spots.
    $name = 'theme_essential/togglemarketing';
    $title = get_string('togglemarketing', 'theme_essential');
    $description = get_string('togglemarketingdesc', 'theme_essential');
    $alwaysdisplay = get_string('alwaysdisplay', 'theme_essential');
    $displaybeforelogin = get_string('displaybeforelogin', 'theme_essential');
    $displayafterlogin = get_string('displayafterlogin', 'theme_essential');
    $dontdisplay = get_string('dontdisplay', 'theme_essential');
    $default = 1;
    $choices = array(1 => $alwaysdisplay, 2 => $displaybeforelogin, 3 => $displayafterlogin, 0 => $dontdisplay);
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    // Marketing spot height.
    $name = 'theme_essential/marketingheight';
    $title = get_string('marketingheight', 'theme_essential');
    $description = get_string('marketingheightdesc', 'theme_essential');
    $default = 100;
    $choices = array();
    for ($mhit = 50; $mhit <= 500; $mhit = $mhit + 2) {
        $choices[$mhit] = $mhit;
    }
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $essentialsettingsfrontpage->add($setting);

    // Marketing spot image height.
    $name = 'theme_essential/marketingimageheight';
    $title = get_string('marketingimageheight', 'theme_essential');
    $description = get_string('marketingimageheightdesc', 'theme_essential');
    $default = 100;
    $choices = array(50 => '50', 100 => '100', 150 => '150', 200 => '200', 250 => '250', 300 => '300');
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $essentialsettingsfrontpage->add($setting);

    foreach (range(1, 3) as $marketingspotnumber) {
        // This is the descriptor for Marketing Spot in $marketingspotnumber.
        $name = 'theme_essential/marketing' . $marketingspotnumber . 'info';
        $heading = get_string('marketing' . $marketingspotnumber, 'theme_essential');
        $information = get_string('marketinginfodesc', 'theme_essential');
        $setting = new admin_setting_heading($name, $heading, $information);
        $essentialsettingsfrontpage->add($setting);

        // Marketing spot.
        $name = 'theme_essential/marketing' . $marketingspotnumber;
        $title = get_string('marketingtitle', 'theme_essential');
        $description = get_string('marketingtitledesc', 'theme_essential');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfrontpage->add($setting);

        $name = 'theme_essential/marketing' . $marketingspotnumber . 'icon';
        $title = get_string('marketingicon', 'theme_essential');
        $description = get_string('marketingicondesc', 'theme_essential');
        $default = 'star';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfrontpage->add($setting);

        $name = 'theme_essential/marketing' . $marketingspotnumber . 'image';
        $title = get_string('marketingimage', 'theme_essential');
        $description = get_string('marketingimagedesc', 'theme_essential');
        $setting = new admin_setting_configstoredfile($name, $title, $description,
                'marketing' . $marketingspotnumber . 'image');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfrontpage->add($setting);

        $name = 'theme_essential/marketing' . $marketingspotnumber . 'content';
        $title = get_string('marketingcontent', 'theme_essential');
        $description = get_string('marketingcontentdesc', 'theme_essential');
        $default = '';
        $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfrontpage->add($setting);

        $name = 'theme_essential/marketing' . $marketingspotnumber . 'buttontext';
        $title = get_string('marketingbuttontext', 'theme_essential');
        $description = get_string('marketingbuttontextdesc', 'theme_essential');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfrontpage->add($setting);

        $name = 'theme_essential/marketing' . $marketingspotnumber . 'buttonurl';
        $title = get_string('marketingbuttonurl', 'theme_essential');
        $description = get_string('marketingbuttonurldesc', 'theme_essential');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfrontpage->add($setting);

        $name = 'theme_essential/marketing' . $marketingspotnumber . 'target';
        $title = get_string('marketingurltarget', 'theme_essential');
        $description = get_string('marketingurltargetdesc', 'theme_essential');
        $target1 = get_string('marketingurltargetself', 'theme_essential');
        $target2 = get_string('marketingurltargetnew', 'theme_essential');
        $target3 = get_string('marketingurltargetparent', 'theme_essential');
        $default = '_blank';
        $choices = array('_self' => $target1, '_blank' => $target2, '_parent' => $target3);
        $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsfrontpage->add($setting);
    }

    // User alerts.
    $essentialsettingsfrontpage->add(new admin_setting_heading('theme_essential_alerts',
        get_string('alertsheadingsub', 'theme_essential'),
        format_text(get_string('alertsdesc', 'theme_essential'), FORMAT_MARKDOWN)));

    $information = get_string('alertinfodesc', 'theme_essential');

    // This is the descriptor for alert one.
    $name = 'theme_essential/alert1info';
    $heading = get_string('alert1', 'theme_essential');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essentialsettingsfrontpage->add($setting);

    // Enable alert.
    $name = 'theme_essential/enable1alert';
    $title = get_string('enablealert', 'theme_essential');
    $description = get_string('enablealertdesc', 'theme_essential');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    // Alert type.
    $name = 'theme_essential/alert1type';
    $title = get_string('alerttype', 'theme_essential');
    $description = get_string('alerttypedesc', 'theme_essential');
    $alertinfo = get_string('alert_info', 'theme_essential');
    $alertwarning = get_string('alert_warning', 'theme_essential');
    $alertgeneral = get_string('alert_general', 'theme_essential');
    $default = 'info';
    $choices = array('info' => $alertinfo, 'error' => $alertwarning, 'success' => $alertgeneral);
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    // Alert title.
    $name = 'theme_essential/alert1title';
    $title = get_string('alerttitle', 'theme_essential');
    $description = get_string('alerttitledesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    // Alert text.
    $name = 'theme_essential/alert1text';
    $title = get_string('alerttext', 'theme_essential');
    $description = get_string('alerttextdesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    // This is the descriptor for alert two.
    $name = 'theme_essential/alert2info';
    $heading = get_string('alert2', 'theme_essential');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essentialsettingsfrontpage->add($setting);

    // Enable alert.
    $name = 'theme_essential/enable2alert';
    $title = get_string('enablealert', 'theme_essential');
    $description = get_string('enablealertdesc', 'theme_essential');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    // Alert type.
    $name = 'theme_essential/alert2type';
    $title = get_string('alerttype', 'theme_essential');
    $description = get_string('alerttypedesc', 'theme_essential');
    $alertinfo = get_string('alert_info', 'theme_essential');
    $alertwarning = get_string('alert_warning', 'theme_essential');
    $alertgeneral = get_string('alert_general', 'theme_essential');
    $default = 'info';
    $choices = array('info' => $alertinfo, 'error' => $alertwarning, 'success' => $alertgeneral);
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    // Alert title.
    $name = 'theme_essential/alert2title';
    $title = get_string('alerttitle', 'theme_essential');
    $description = get_string('alerttitledesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    // Alert text.
    $name = 'theme_essential/alert2text';
    $title = get_string('alerttext', 'theme_essential');
    $description = get_string('alerttextdesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    // This is the descriptor for alert three.
    $name = 'theme_essential/alert3info';
    $heading = get_string('alert3', 'theme_essential');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essentialsettingsfrontpage->add($setting);

    // Enable alert.
    $name = 'theme_essential/enable3alert';
    $title = get_string('enablealert', 'theme_essential');
    $description = get_string('enablealertdesc', 'theme_essential');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    // Alert type.
    $name = 'theme_essential/alert3type';
    $title = get_string('alerttype', 'theme_essential');
    $description = get_string('alerttypedesc', 'theme_essential');
    $alertinfo = get_string('alert_info', 'theme_essential');
    $alertwarning = get_string('alert_warning', 'theme_essential');
    $alertgeneral = get_string('alert_general', 'theme_essential');
    $default = 'info';
    $choices = array('info' => $alertinfo, 'error' => $alertwarning, 'success' => $alertgeneral);
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    // Alert title.
    $name = 'theme_essential/alert3title';
    $title = get_string('alerttitle', 'theme_essential');
    $description = get_string('alerttitledesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    // Alert text.
    $name = 'theme_essential/alert3text';
    $title = get_string('alerttext', 'theme_essential');
    $description = get_string('alerttextdesc', 'theme_essential');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsfrontpage->add($setting);

    $essentialsettingsfrontpage->add($essentialreadme);
    $essentialsettingsfrontpage->add($essentialadvert);
}
$ADMIN->add('theme_essential', $essentialsettingsfrontpage);

// Slideshow settings.
$essentialsettingsslideshow = new admin_settingpage('theme_essential_slideshow', get_string('slideshowheading', 'theme_essential'));
if ($ADMIN->fulltree) {
    $essentialsettingsslideshow->add(new admin_setting_heading('theme_essential_slideshow',
        get_string('slideshowheadingsub', 'theme_essential'),
        format_text(get_string('slideshowdesc', 'theme_essential'), FORMAT_MARKDOWN)));

    // Toggle slideshow.
    $name = 'theme_essential/toggleslideshow';
    $title = get_string('toggleslideshow', 'theme_essential');
    $description = get_string('toggleslideshowdesc', 'theme_essential');
    $alwaysdisplay = get_string('alwaysdisplay', 'theme_essential');
    $displaybeforelogin = get_string('displaybeforelogin', 'theme_essential');
    $displayafterlogin = get_string('displayafterlogin', 'theme_essential');
    $dontdisplay = get_string('dontdisplay', 'theme_essential');
    $default = 1;
    $choices = array(1 => $alwaysdisplay, 2 => $displaybeforelogin, 3 => $displayafterlogin, 0 => $dontdisplay);
    $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsslideshow->add($setting);

    // Number of slides.
    $name = 'theme_essential/numberofslides';
    $title = get_string('numberofslides', 'theme_essential');
    $description = get_string('numberofslides_desc', 'theme_essential');
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
    $essentialsettingsslideshow->add(new essential_admin_setting_configselect($name, $title, $description, $default, $choices));

    // Hide slideshow on phones.
    $name = 'theme_essential/hideontablet';
    $title = get_string('hideontablet', 'theme_essential');
    $description = get_string('hideontabletdesc', 'theme_essential');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsslideshow->add($setting);

    // Hide slideshow on tablet.
    $name = 'theme_essential/hideonphone';
    $title = get_string('hideonphone', 'theme_essential');
    $description = get_string('hideonphonedesc', 'theme_essential');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsslideshow->add($setting);

    // Slide interval.
    $name = 'theme_essential/slideinterval';
    $title = get_string('slideinterval', 'theme_essential');
    $description = get_string('slideintervaldesc', 'theme_essential');
    $default = '5000';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsslideshow->add($setting);

    // Slide caption text colour setting.
    $name = 'theme_essential/slidecaptiontextcolor';
    $title = get_string('slidecaptiontextcolor', 'theme_essential');
    $description = get_string('slidecaptiontextcolordesc', 'theme_essential');
    $default = '#ffffff';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsslideshow->add($setting);

    // Slide caption background colour setting.
    $name = 'theme_essential/slidecaptionbackgroundcolor';
    $title = get_string('slidecaptionbackgroundcolor', 'theme_essential');
    $description = get_string('slidecaptionbackgroundcolordesc', 'theme_essential');
    $default = '#30add1';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsslideshow->add($setting);

    // Show caption options.
    $name = 'theme_essential/slidecaptionoptions';
    $title = get_string('slidecaptionoptions', 'theme_essential');
    $description = get_string('slidecaptionoptionsdesc', 'theme_essential');
    $default = 0;
    $choices = array(
        0 => get_string('slidecaptionbeside', 'theme_essential'),
        1 => get_string('slidecaptionontop', 'theme_essential'),
        2 => get_string('slidecaptionunderneath', 'theme_essential')
    );
    $images = array(
        0 => 'beside',
        1 => 'on_top',
        2 => 'underneath'
    );
    $setting = new essential_admin_setting_configradio($name, $title, $description, $default, $choices, false, $images);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsslideshow->add($setting);

    // Show caption centred.
    $name = 'theme_essential/slidecaptioncentred';
    $title = get_string('slidecaptioncentred', 'theme_essential');
    $description = get_string('slidecaptioncentreddesc', 'theme_essential');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsslideshow->add($setting);

    // Slide button colour setting.
    $name = 'theme_essential/slidebuttoncolor';
    $title = get_string('slidebuttoncolor', 'theme_essential');
    $description = get_string('slidebuttoncolordesc', 'theme_essential');
    $default = '#30add1';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsslideshow->add($setting);

    // Slide button hover colour setting.
    $name = 'theme_essential/slidebuttonhovercolor';
    $title = get_string('slidebuttonhovercolor', 'theme_essential');
    $description = get_string('slidebuttonhovercolordesc', 'theme_essential');
    $default = '#217a94';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingsslideshow->add($setting);

    // This is the descriptor for the user theme slide colours.
    $name = 'theme_essential/alternativethemeslidecolorsinfo';
    $heading = get_string('alternativethemeslidecolors', 'theme_essential');
    $information = get_string('alternativethemeslidecolorsdesc', 'theme_essential');
    $setting = new admin_setting_heading($name, $heading, $information);
    $essentialsettingsslideshow->add($setting);

    foreach (range(1, 4) as $alternativethemenumber) {
        // Alternative theme slide caption text colour setting.
        $name = 'theme_essential/alternativethemeslidecaptiontextcolor' . $alternativethemenumber;
        $title = get_string('alternativethemeslidecaptiontextcolor', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemeslidecaptiontextcolordesc', 'theme_essential',
                $alternativethemenumber);
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsslideshow->add($setting);

        // Alternative theme slide caption background colour setting.
        $name = 'theme_essential/alternativethemeslidecaptionbackgroundcolor' . $alternativethemenumber;
        $title = get_string('alternativethemeslidecaptionbackgroundcolor', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemeslidecaptionbackgroundcolordesc', 'theme_essential',
                $alternativethemenumber);
        $default = $defaultalternativethemecolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsslideshow->add($setting);

        // Alternative theme slide button colour setting.
        $name = 'theme_essential/alternativethemeslidebuttoncolor' . $alternativethemenumber;
        $title = get_string('alternativethemeslidebuttoncolor', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemeslidebuttoncolordesc', 'theme_essential', $alternativethemenumber);
        $default = $defaultalternativethemecolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsslideshow->add($setting);

        // Alternative theme slide button hover colour setting.
        $name = 'theme_essential/alternativethemeslidebuttonhovercolor' . $alternativethemenumber;
        $title = get_string('alternativethemeslidebuttonhovercolor', 'theme_essential', $alternativethemenumber);
        $description = get_string('alternativethemeslidebuttonhovercolordesc', 'theme_essential',
                $alternativethemenumber);
        $default = $defaultalternativethemehovercolors[$alternativethemenumber - 1];
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsslideshow->add($setting);
    }

    $numberofslides = get_config('theme_essential', 'numberofslides');
    for ($i = 1; $i <= $numberofslides; $i++) {
        // This is the descriptor for the slide.
        $name = 'theme_essential/slide'.$i.'info';
        $heading = get_string('slideno', 'theme_essential', array('slide' => $i));
        $information = get_string('slidenodesc', 'theme_essential', array('slide' => $i));
        $setting = new admin_setting_heading($name, $heading, $information);
        $essentialsettingsslideshow->add($setting);

        // Title.
        $name = 'theme_essential/slide'.$i;
        $title = get_string('slidetitle', 'theme_essential');
        $description = get_string('slidetitledesc', 'theme_essential');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsslideshow->add($setting);

        // Image.
        $name = 'theme_essential/slide'.$i.'image';
        $title = get_string('slideimage', 'theme_essential');
        $description = get_string('slideimagedesc', 'theme_essential');
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'slide'.$i.'image');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsslideshow->add($setting);

        // Caption text.
        $name = 'theme_essential/slide'.$i.'caption';
        $title = get_string('slidecaption', 'theme_essential');
        $description = get_string('slidecaptiondesc', 'theme_essential');
        $default = '';
        $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsslideshow->add($setting);

        // URL.
        $name = 'theme_essential/slide'.$i.'url';
        $title = get_string('slideurl', 'theme_essential');
        $description = get_string('slideurldesc', 'theme_essential');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsslideshow->add($setting);

        // URL target.
        $name = 'theme_essential/slide'.$i.'target';
        $title = get_string('slideurltarget', 'theme_essential');
        $description = get_string('slideurltargetdesc', 'theme_essential');
        $target1 = get_string('slideurltargetself', 'theme_essential');
        $target2 = get_string('slideurltargetnew', 'theme_essential');
        $target3 = get_string('slideurltargetparent', 'theme_essential');
        $default = '_blank';
        $choices = array('_self' => $target1, '_blank' => $target2, '_parent' => $target3);
        $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingsslideshow->add($setting);
    }

    $essentialsettingsslideshow->add($essentialreadme);
    $essentialsettingsslideshow->add($essentialadvert);
}
$ADMIN->add('theme_essential', $essentialsettingsslideshow);

// Category course title image settings.
$enablecategoryctics = get_config('theme_essential', 'enablecategoryctics');
if ($enablecategoryctics) {
    $essentialsettingscategoryctititle = get_string('categoryctiheadingcs', 'theme_essential');
} else {
    $essentialsettingscategoryctititle = get_string('categoryctiheading', 'theme_essential');
}
$essentialsettingscategorycti = new admin_settingpage('theme_essential_categorycti', $essentialsettingscategoryctititle);
if ($ADMIN->fulltree) {
    global $CFG;
    if (file_exists("{$CFG->dirroot}/theme/essential/essential_admin_setting_configinteger.php")) {
        require_once($CFG->dirroot . '/theme/essential/essential_admin_setting_configinteger.php');
    } else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/essential/essential_admin_setting_configinteger.php")) {
        require_once($CFG->themedir . '/essential/essential_admin_setting_configinteger.php');
    }

    $essentialsettingscategorycti->add(new admin_setting_heading('theme_essential_categorycti',
        get_string('categoryctiheadingsub', 'theme_essential'),
        format_text(get_string('categoryctidesc', 'theme_essential'), FORMAT_MARKDOWN)));

    // Category course title images.
    $name = 'theme_essential/enablecategorycti';
    $title = get_string('enablecategorycti', 'theme_essential');
    $description = get_string('enablecategoryctidesc', 'theme_essential');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscategorycti->add($setting);

    // Category course title image setting pages.
    $name = 'theme_essential/enablecategoryctics';
    $title = get_string('enablecategoryctics', 'theme_essential');
    $description = get_string('enablecategorycticsdesc', 'theme_essential');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscategorycti->add($setting);

    // We only want to output category course title image options if the parent setting is enabled.
    if (get_config('theme_essential', 'enablecategorycti')) {
        $essentialsettingscategorycti->add(new admin_setting_heading('theme_essential_categorycticourses',
            get_string('ctioverride', 'theme_essential'), get_string('ctioverridedesc', 'theme_essential')));

        // Overridden image height.
        $name = 'theme_essential/ctioverrideheight';
        $title = get_string('ctioverrideheight', 'theme_essential');
        $default = 200;
        $lower = 40;
        $upper = 400;
        $description = get_string('ctioverrideheightdesc', 'theme_essential',
            array('lower' => $lower, 'upper' => $upper));
        $setting = new essential_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
        $essentialsettingscategorycti->add($setting);

        // Overridden course title text colour setting.
        $name = 'theme_essential/ctioverridetextcolour';
        $title = get_string('ctioverridetextcolour', 'theme_essential');
        $description = get_string('ctioverridetextcolourdesc', 'theme_essential');
        $default = '#ffffff';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $essentialsettingscategorycti->add($setting);

        // Overridden course title text background colour setting.
        $name = 'theme_essential/ctioverridetextbackgroundcolour';
        $title = get_string('ctioverridetextbackgroundcolour', 'theme_essential');
        $description = get_string('ctioverridetextbackgroundcolourdesc', 'theme_essential');
        $default = '#c51230';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $essentialsettingscategorycti->add($setting);

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
        $name = 'theme_essential/ctioverridetextbackgroundopacity';
        $title = get_string('ctioverridetextbackgroundopacity', 'theme_essential');
        $description = get_string('ctioverridetextbackgroundopacitydesc', 'theme_essential');
        $default = '0.8';
        $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $opactitychoices);
        $essentialsettingscategorycti->add($setting);
    }
}
$ADMIN->add('theme_essential', $essentialsettingscategorycti);

// We only want to output category course title image options if the parent setting is enabled.
if (get_config('theme_essential', 'enablecategorycti')) {
    // Get all category IDs and their names.
    $coursecats = \theme_essential\toolbox::get_categories_list();

    if (!$enablecategoryctics) {
        $essentialsettingscategoryctimenu = $essentialsettingscategorycti;
    }

    // Go through all categories and create the necessary settings.
    foreach ($coursecats as $key => $value) {
        if (($value->depth == 1) && ($enablecategoryctics)) {
            $essentialsettingscategoryctimenu = new admin_settingpage('theme_essential_categorycti_'.$value->id,
                get_string('categoryctiheadingcategory', 'theme_essential',
                    array('category' => format_string($value->namechunks[0]))));
        }

        if ($ADMIN->fulltree) {
            $namepath = join(' / ', $value->namechunks);
            // This is the descriptor for category course title image.
            $name = 'theme_essential/categoryctiinfo'.$key;
            $heading = get_string('categoryctiinfo', 'theme_essential', array('category' => $namepath));
            $information = get_string('categoryctiinfodesc', 'theme_essential', array('category' => $namepath));
            $setting = new admin_setting_heading($name, $heading, $information);
            $essentialsettingscategoryctimenu->add($setting);

            // Image.
            $name = 'theme_essential/categoryct'.$key.'image';
            $title = get_string('categoryctimage', 'theme_essential', array('category' => $namepath));
            $description = get_string('categoryctimagedesc', 'theme_essential', array('category' => $namepath));
            $setting = new admin_setting_configstoredfile($name, $title, $description, 'categoryct'.$key.'image');
            $setting->set_updatedcallback('theme_reset_all_caches');
            $essentialsettingscategoryctimenu->add($setting);

            // Image URL.
            $name = 'theme_essential/categoryctimageurl'.$key;
            $title = get_string('categoryctimageurl', 'theme_essential', array('category' => $namepath));
            $description = get_string('categoryctimageurldesc', 'theme_essential', array('category' => $namepath));
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $essentialsettingscategoryctimenu->add($setting);

            // Image height.
            $name = 'theme_essential/categorycti'.$key.'height';
            $title = get_string('categoryctiheight', 'theme_essential', array('category' => $namepath));
            $default = 200;
            $lower = 40;
            $upper = 400;
            $description = get_string('categoryctiheightdesc', 'theme_essential',
                array('category' => $namepath, 'lower' => $lower, 'upper' => $upper));
            $setting = new essential_admin_setting_configinteger($name, $title, $description, $default, $lower, $upper);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $essentialsettingscategoryctimenu->add($setting);

            // Category course title text colour setting.
            $name = 'theme_essential/categorycti'.$key.'textcolour';
            $title = get_string('categoryctitextcolour', 'theme_essential', array('category' => $namepath));
            $description = get_string('categoryctitextcolourdesc', 'theme_essential', array('category' => $namepath));
            $default = '#000000';
            $previewconfig = null;
            $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $essentialsettingscategoryctimenu->add($setting);

            // Category course title text background colour setting.
            $name = 'theme_essential/categorycti'.$key.'textbackgroundcolour';
            $title = get_string('categoryctitextbackgroundcolour', 'theme_essential', array('category' => $namepath));
            $description = get_string('categoryctitextbackgroundcolourdesc', 'theme_essential', array('category' => $namepath));
            $default = '#ffffff';
            $previewconfig = null;
            $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $essentialsettingscategoryctimenu->add($setting);

            // Category course title text background opacity setting.
            $name = 'theme_essential/categorycti'.$key.'textbackgroundopactity';
            $title = get_string('categoryctitextbackgroundopacity', 'theme_essential', array('category' => $namepath));
            $description = get_string('categoryctitextbackgroundopacitydesc', 'theme_essential', array('category' => $namepath));
            $default = '0.8';
            $setting = new essential_admin_setting_configselect($name, $title, $description, $default, $opactitychoices);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $essentialsettingscategoryctimenu->add($setting);
        }
        if (($value->depth == 1) && ($enablecategoryctics)) {
            $ADMIN->add('theme_essential', $essentialsettingscategoryctimenu);
        }
    }
}

// Category icon settings.
$essentialsettingscategoryicon = new admin_settingpage('theme_essential_categoryicon',
    get_string('categoryiconheading', 'theme_essential'));
if ($ADMIN->fulltree) {
    $essentialsettingscategoryicon->add(new admin_setting_heading('theme_essential_categoryicon',
        get_string('categoryiconheadingsub', 'theme_essential'),
        format_text(get_string('categoryicondesc', 'theme_essential'), FORMAT_MARKDOWN)));

    // Category icons.
    $name = 'theme_essential/enablecategoryicon';
    $title = get_string('enablecategoryicon', 'theme_essential');
    $description = get_string('enablecategoryicondesc', 'theme_essential');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $essentialsettingscategoryicon->add($setting);

    // We only want to output category icon options if the parent setting is enabled.
    if (get_config('theme_essential', 'enablecategoryicon')) {

        // Default icon.
        $name = 'theme_essential/defaultcategoryicon';
        $title = get_string('defaultcategoryicon', 'theme_essential');
        $description = get_string('defaultcategoryicondesc', 'theme_essential');
        $default = 'folder-open';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscategoryicon->add($setting);

        // Default image.
        $name = 'theme_essential/defaultcategoryimage';
        $title = get_string('defaultcategoryimage', 'theme_essential');
        $description = get_string('defaultcategoryimagedesc', 'theme_essential');
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'defaultcategoryimage');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscategoryicon->add($setting);

        // Category icons.
        $name = 'theme_essential/enablecustomcategoryicon';
        $title = get_string('enablecustomcategoryicon', 'theme_essential');
        $description = get_string('enablecustomcategoryicondesc', 'theme_essential');
        $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $essentialsettingscategoryicon->add($setting);

        if (get_config('theme_essential', 'enablecustomcategoryicon')) {
            $iconstring = get_string('icon', 'theme_essential');
            $imagestring = get_string('image', 'theme_essential');

            // This is the descriptor for custom category icons.
            $name = 'theme_essential/categoryiconinfo';
            $heading = get_string('categoryiconinfo', 'theme_essential');
            $information = get_string('categoryiconinfodesc', 'theme_essential');
            $setting = new admin_setting_heading($name, $heading, $information);
            $essentialsettingscategoryicon->add($setting);

            // Get the default category icon.
            $defaultcategoryicon = get_config('theme_essential', 'defaultcategoryicon');
            if (empty($defaultcategoryicon)) {
                $defaultcategoryicon = 'folder-open';
            }

            // Get all category IDs and their names.
            $coursecats = \theme_essential\toolbox::get_categories_list();

            // Go through all categories and create the necessary settings.
            foreach ($coursecats as $key => $value) {
                $namepath = join(' / ', $value->namechunks);
                // Category icon for each category.
                $name = 'theme_essential/categoryicon';
                $title = $namepath.' '.$iconstring;
                $description = get_string('categoryiconcategory', 'theme_essential', array('category' => $namepath));
                $default = $defaultcategoryicon;
                $setting = new admin_setting_configtext($name.$key, $title, $description, $default);
                $setting->set_updatedcallback('theme_reset_all_caches');
                $essentialsettingscategoryicon->add($setting);

                // Category image for each category.
                $name = 'theme_essential/categoryimage';
                $title = $namepath.' '.$imagestring;
                $description = get_string('categoryimagecategory', 'theme_essential', array('category' => $namepath));
                $setting = new admin_setting_configstoredfile($name.$key, $title, $description, 'categoryimage'.$key);
                $setting->set_updatedcallback('theme_reset_all_caches');
                $essentialsettingscategoryicon->add($setting);
            }
            unset($coursecats);
        }
    }

    $essentialsettingscategoryicon->add($essentialreadme);
    $essentialsettingscategoryicon->add($essentialadvert);
}
$ADMIN->add('theme_essential', $essentialsettingscategoryicon);

// Properties.
$essentialsettingsprops = new admin_settingpage('theme_essential_props', get_string('properties', 'theme_essential'));
if ($ADMIN->fulltree) {
    if (file_exists("{$CFG->dirroot}/theme/essential/essential_admin_setting_getprops.php")) {
        require_once($CFG->dirroot . '/theme/essential/essential_admin_setting_getprops.php');
        require_once($CFG->dirroot . '/theme/essential/essential_admin_setting_putprops.php');
    } else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/essential/essential_admin_setting_getprops.php")) {
        require_once($CFG->themedir . '/essential/essential_admin_setting_getprops.php');
        require_once($CFG->themedir . '/essential/essential_admin_setting_putprops.php');
    }

    $essentialsettingsprops->add(new admin_setting_heading('theme_essential_props',
        get_string('propertiessub', 'theme_essential'),
        format_text(get_string('propertiesdesc', 'theme_essential'), FORMAT_MARKDOWN)));

    $essentialexportprops = optional_param('theme_essential_getprops_saveprops', 0, PARAM_INT);
    $essentialprops = \theme_essential\toolbox::compile_properties('essential');
    $essentialsettingsprops->add(new essential_admin_setting_getprops('theme_essential_getprops',
        get_string('propertiesproperty', 'theme_essential'),
        get_string('propertiesvalue', 'theme_essential'),
        $essentialprops,
        'theme_essential_props',
        get_string('propertiesreturn', 'theme_essential'),
        get_string('propertiesexport', 'theme_essential'),
        $essentialexportprops
    ));

    $setting = new essential_admin_setting_putprops('theme_essential_putprops',
        get_string('putpropertiesname', 'theme_essential'),
        get_string('putpropertiesdesc', 'theme_essential'),
        'essential',
        '\theme_essential\toolbox::put_properties'
    );
    $setting->set_updatedcallback('purge_all_caches');
    $essentialsettingsprops->add($setting);
}
$ADMIN->add('theme_essential', $essentialsettingsprops);

// Style guide.
$essentialsettingsstyleguide = new admin_settingpage('theme_essential_styleguide', get_string('styleguide', 'theme_essential'));
if ($ADMIN->fulltree) {
    if (file_exists("{$CFG->dirroot}/theme/essential/essential_admin_setting_styleguide.php")) {
        require_once($CFG->dirroot . '/theme/essential/essential_admin_setting_styleguide.php');
    } else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/essential/essential_admin_setting_styleguide.php")) {
        require_once($CFG->themedir . '/essential/essential_admin_setting_styleguide.php');
    }
    $essentialsettingsstyleguide->add(new essential_admin_setting_styleguide('theme_essential_styleguide',
        get_string('styleguidesub', 'theme_essential'),
        get_string('styleguidedesc', 'theme_essential',
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
$ADMIN->add('theme_essential', $essentialsettingsstyleguide);
