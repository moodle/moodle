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
 * Version details
 *
 * @package    theme_adaptable
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015 Fernando Acedo (3-bits.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

    // Header heading.
    $temp = new admin_settingpage('theme_adaptable_header', get_string('headersettings', 'theme_adaptable'));
    $temp->add(new admin_setting_heading('theme_adaptable_header', get_string('headersettingsheading', 'theme_adaptable'),
    format_text(get_string('headerdesc', 'theme_adaptable'), FORMAT_MARKDOWN)));

    // Header image.
    $name = 'theme_adaptable/headerbgimage';
    $title = get_string('headerbgimage', 'theme_adaptable');
    $description = get_string('headerbgimagedesc', 'theme_adaptable');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'headerbgimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Enable front page login form in header.
    $name = 'theme_adaptable/frontpagelogin';
    $title = get_string('frontpagelogin', 'theme_adaptable');
    $description = get_string('frontpagelogindesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Select type of login.
    $name = 'theme_adaptable/displaylogin';
    $title = get_string('displaylogin', 'theme_adaptable');
    $description = get_string('displaylogindesc', 'theme_adaptable');
    $choices = array(
        'button' => get_string('displayloginbutton', 'theme_adaptable'),
        'box' => get_string('displayloginbox', 'theme_adaptable'),
        'no' => get_string('displayloginno', 'theme_adaptable')
    );
    $setting = new admin_setting_configselect($name, $title, $description, 'button', $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Enable messaging menu in header.
    $name = 'theme_adaptable/enablemessagemenu';
    $title = get_string('enablemessagemenu', 'theme_adaptable');
    $description = get_string('enablemessagemenudesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Filter admin messages.
    $name = 'theme_adaptable/filteradminmessages';
    $title = get_string('filteradminmessages', 'theme_adaptable');
    $description = get_string('filteradminmessagesdesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Logo.
    $name = 'theme_adaptable/logo';
    $title = get_string('logo', 'theme_adaptable');
    $description = get_string('logodesc', 'theme_adaptable');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Page Header Height.
    $name = 'theme_adaptable/pageheaderheight';
    $title = get_string('pageheaderheight', 'theme_adaptable');
    $description = get_string('pageheaderheightdesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '72px');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Course page header title.
    $name = 'theme_adaptable/coursepageheaderhidesitetitle';
    $title = get_string('coursepageheaderhidesitetitle', 'theme_adaptable');
    $description = get_string('coursepageheaderhidesitetitledesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Favicon file setting.
    $name = 'theme_adaptable/favicon';
    $title = get_string('favicon', 'theme_adaptable');
    $description = get_string('favicondesc', 'theme_adaptable');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'favicon');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Display Course title in the header.
    $name = 'theme_adaptable/sitetitle';
    $title = get_string('sitetitle', 'theme_adaptable');
    $description = get_string('sitetitledesc', 'theme_adaptable');
    $radchoices = array(
        'disabled' => get_string('sitetitleoff', 'theme_adaptable'),
        'default' => get_string('sitetitledefault', 'theme_adaptable'),
        'custom' => get_string('sitetitlecustom', 'theme_adaptable')
    );
    $setting = new admin_setting_configselect($name, $title, $description, 'default', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Site title.
    $name = 'theme_adaptable/sitetitletext';
    $title = get_string('sitetitletext', 'theme_adaptable');
    $description = get_string('sitetitletextdesc', 'theme_adaptable');
    $default = '';
    $setting = new adaptable_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Display Course title in the header.
    $name = 'theme_adaptable/enableheading';
    $title = get_string('enableheading', 'theme_adaptable');
    $description = get_string('enableheadingdesc', 'theme_adaptable');
    $radchoices = array(
        'fullname' => get_string('breadcrumbtitlefullname', 'theme_adaptable'),
        'shortname' => get_string('breadcrumbtitleshortname', 'theme_adaptable'),
        'off' => get_string('hide'),
    );
    $setting = new admin_setting_configselect($name, $title, $description, 'fullname', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Site Title Padding Top.
    $name = 'theme_adaptable/sitetitlepaddingtop';
    $title = get_string('sitetitlepaddingtop', 'theme_adaptable');
    $description = get_string('sitetitlepaddingtopdesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '0px');
    $setting = new admin_setting_configselect($name, $title, $description, '0px', $from0to20px);
    $temp->add($setting);

    // Site Title Padding Left.
    $name = 'theme_adaptable/sitetitlepaddingleft';
    $title = get_string('sitetitlepaddingleft', 'theme_adaptable');
    $description = get_string('sitetitlepaddingleftdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, '0px', $from0to20px);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Site Title Maximum Width.
    $name = 'theme_adaptable/sitetitlemaxwidth';
    $title = get_string('sitetitlemaxwidth', 'theme_adaptable');
    $description = get_string('sitetitlemaxwidthdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, '50%', $from35to80percent);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Course Title Maximum Width.
    $name = 'theme_adaptable/coursetitlemaxwidth';
    $title = get_string('coursetitlemaxwidth', 'theme_adaptable');
    $description = get_string('coursetitlemaxwidthdesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '20', PARAM_INT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Breadcrumb home.
    $name = 'theme_adaptable/breadcrumbhome';
    $title = get_string('breadcrumbhome', 'theme_adaptable');
    $description = get_string('breadcrumbhomedesc', 'theme_adaptable');
    $radchoices = array(
        'text' => get_string('breadcrumbhometext', 'theme_adaptable'),
        'icon' => get_string('breadcrumbhomeicon', 'theme_adaptable')
    );
    $setting = new admin_setting_configselect($name, $title, $description, 'icon', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Breadcrumb separator.
    $name = 'theme_adaptable/breadcrumbseparator';
    $title = get_string('breadcrumbseparator', 'theme_adaptable');
    $description = get_string('breadcrumbseparatordesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, 'angle-right');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Choose to display search box or social icons.
    $name = 'theme_adaptable/socialorsearch';
    $title = get_string('socialorsearch', 'theme_adaptable');
    $description = get_string('socialorsearchdesc', 'theme_adaptable');
    $radchoices = array(
        'none' => get_string('socialorsearchnone', 'theme_adaptable'),
        'social' => get_string('socialorsearchsocial', 'theme_adaptable'),
        'search' => get_string('socialorsearchsearch', 'theme_adaptable')
    );
    $setting = new admin_setting_configselect($name, $title, $description, 'search', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Search box padding.
    $name = 'theme_adaptable/searchboxpadding';
    $title = get_string('searchboxpadding', 'theme_adaptable');
    $description = get_string('searchboxpaddingdesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '15px 0px 0px 0px');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Enable save / cancel overlay at top of page.
    $name = 'theme_adaptable/enablesavecanceloverlay';
    $title = get_string('enablesavecanceloverlay', 'theme_adaptable');
    $description = get_string('enablesavecanceloverlaydesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $ADMIN->add('theme_adaptable', $temp);
