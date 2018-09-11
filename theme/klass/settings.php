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
 * settings.php
 *
 * @package   theme_klass
 * @copyright 2015 LMSACE Dev Team, lmsace.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
$settings = null;

if (is_siteadmin()) {

    $settings = new theme_boost_admin_settingspage_tabs('themesettingklass', get_string('configtitle', 'theme_klass'));
    $ADMIN->add('themes', new admin_category('theme_klass', 'Klass'));

    /* Header Settings */
    $temp = new admin_settingpage('theme_klass_header', get_string('generalheading', 'theme_klass'));

    // Logo file setting.
    $name = 'theme_klass/logo';
    $title = get_string('logo', 'theme_klass');
    $description = get_string('logodesc', 'theme_klass');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Custom CSS file.
    $name = 'theme_klass/customcss';
    $title = get_string('customcss', 'theme_klass');
    $description = get_string('customcssdesc', 'theme_klass');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $settings->add($temp);

    /* Front Page Settings */
    $temp = new admin_settingpage('theme_klass_frontpage', get_string('frontpageheading', 'theme_klass'));

     // Who we are title.
    $name = 'theme_klass/whoweare_title';
    $title = get_string('whoweare_title', 'theme_klass');
    $description = '';
    $default = get_string('whoweare_title_default', 'theme_klass');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

     // Who we are content.
    $name = 'theme_klass/whoweare_description';
    $title = get_string('whoweare_description', 'theme_klass');
    $description = get_string('whowearedesc', 'theme_klass');
    $default = get_string('whowearedefault', 'theme_klass');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $temp->add($setting);

    $settings->add($temp);

    /* Slideshow Settings Start */
    $temp = new admin_settingpage('theme_klass_slideshow', get_string('slideshowheading', 'theme_klass'));
    $temp->add(new admin_setting_heading('theme_klass_slideshow', get_string('slideshowheadingsub', 'theme_klass'),
        format_text(get_string('slideshowdesc', 'theme_klass'), FORMAT_MARKDOWN)));

    // Display Slideshow.
    $name = 'theme_klass/toggleslideshow';
    $title = get_string('toggleslideshow', 'theme_klass');
    $description = get_string('toggleslideshowdesc', 'theme_klass');
    $yes = get_string('yes');
    $no = get_string('no');
    $default = 1;
    $choices = array(1 => $yes , 0 => $no);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $temp->add($setting);

    // Number of slides.
    $name = 'theme_klass/numberofslides';
    $title = get_string('numberofslides', 'theme_klass');
    $description = get_string('numberofslides_desc', 'theme_klass');
    $default = 3;
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
    );
    $temp->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    $numberofslides = get_config('theme_klass', 'numberofslides');
    for ($i = 1; $i <= $numberofslides; $i++) {

        // This is the descriptor for Slide One.
        $name = 'theme_klass/slide' . $i . 'info';
        $heading = get_string('slideno', 'theme_klass', array('slide' => $i));
        $information = get_string('slidenodesc', 'theme_klass', array('slide' => $i));
        $setting = new admin_setting_heading($name, $heading, $information);
        $temp->add($setting);

        // Slide Image.
        $name = 'theme_klass/slide' . $i . 'image';
        $title = get_string('slideimage', 'theme_klass');
        $description = get_string('slideimagedesc', 'theme_klass');
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'slide' . $i . 'image');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $temp->add($setting);

        // Slide Caption.
        $name = 'theme_klass/slide' . $i . 'caption';
        $title = get_string('slidecaption', 'theme_klass');
        $description = get_string('slidecaptiondesc', 'theme_klass');
        $default = get_string('slidecaptiondefault', 'theme_klass', array('slideno' => sprintf('%02d', $i) ));
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
        $temp->add($setting);

        // Slide Description Text.
        $name = 'theme_klass/slide' . $i . 'url';
        $title = get_string('slideurl', 'theme_klass');
        $description = get_string('slideurldesc', 'theme_klass');
        $default = 'http://www.example.com/';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
        $temp->add($setting);

    }

    /* Slideshow Settings End*/

    $settings->add($temp);

    /* Footer Settings start */
    $temp = new admin_settingpage('theme_klass_footer', get_string('footerheading', 'theme_klass'));

    // Footer Logo file setting.
    $name = 'theme_klass/footerlogo';
    $title = get_string('footerlogo', 'theme_klass');
    $description = get_string('footerlogodesc', 'theme_klass');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'footerlogo');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    /* Footer Content */
    $name = 'theme_klass/footnote';
    $title = get_string('footnote', 'theme_klass');
    $description = get_string('footnotedesc', 'theme_klass');
    $default = get_string('footnotedefault', 'theme_klass');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $temp->add($setting);

    // INFO Link.
    $name = 'theme_klass/infolink';
    $title = get_string('infolink', 'theme_klass');
    $description = get_string('infolink_desc', 'theme_klass');
    $default = get_string('infolinkdefault', 'theme_klass');
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $temp->add($setting);

    // Copyright.
    $name = 'theme_klass/copyright_footer';
    $title = get_string('copyright_footer', 'theme_klass');
    $description = '';
    $default = get_string('copyright_default', 'theme_klass');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    /* Address , Email , Phone No */
    $name = 'theme_klass/address';
    $title = get_string('address', 'theme_klass');
    $description = '';
    $default = get_string('defaultaddress', 'theme_klass');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_klass/emailid';
    $title = get_string('emailid', 'theme_klass');
    $description = '';
    $default = get_string('defaultemailid', 'theme_klass');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_klass/phoneno';
    $title = get_string('phoneno', 'theme_klass');
    $description = '';
    $default = get_string('defaultphoneno', 'theme_klass');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    /* Facebook, Pinterest, Twitter, Google+ Settings */
    $name = 'theme_klass/fburl';
    $title = get_string('fburl', 'theme_klass');
    $description = get_string('fburldesc', 'theme_klass');
    $default = get_string('fburl_default', 'theme_klass');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_klass/pinurl';
    $title = get_string('pinurl', 'theme_klass');
    $description = get_string('pinurldesc', 'theme_klass');
    $default = get_string('pinurl_default', 'theme_klass');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_klass/twurl';
    $title = get_string('twurl', 'theme_klass');
    $description = get_string('twurldesc', 'theme_klass');
    $default = get_string('twurl_default', 'theme_klass');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_klass/gpurl';
    $title = get_string('gpurl', 'theme_klass');
    $description = get_string('gpurldesc', 'theme_klass');
    $default = get_string('gpurl_default', 'theme_klass');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $settings->add($temp);
    /*  Footer Settings end */
}