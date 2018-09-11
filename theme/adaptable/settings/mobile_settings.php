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
 * @copyright  2015-2017 Fernando Acedo (3-bits.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;
    $temp = new admin_settingpage('theme_adaptable_mobile', get_string('mobilesettings', 'theme_adaptable'));
    $temp->add(new admin_setting_heading('theme_adaptable_mobile', get_string('mobilesettingsheading', 'theme_adaptable'),
        '', FORMAT_MARKDOWN));

    // Hide Alerts.
    $name = 'theme_adaptable/hidealertsmobile';
    $title = get_string('hidealertsmobile', 'theme_adaptable');
    $description = get_string('hidealertsmobiledesc', 'theme_adaptable');
    $radchoices = array(
        0 => get_string('hide', 'theme_adaptable'),
        1 => get_string('show', 'theme_adaptable'),
    );
    $default = 0;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Hide Full Header.
    $name = 'theme_adaptable/hideheadermobile';
    $title = get_string('hideheadermobile', 'theme_adaptable');
    $description = get_string('hideheadermobiledesc', 'theme_adaptable');
    $radchoices = array(
        0 => get_string('hide', 'theme_adaptable'),
        1 => get_string('show', 'theme_adaptable'),
    );
    $setting = new admin_setting_configselect($name, $title, $description, 1, $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Hide Social icons.
    $name = 'theme_adaptable/hidesocialmobile';
    $title = get_string('hidesocialmobile', 'theme_adaptable');
    $description = get_string('hidesocialmobiledesc', 'theme_adaptable');
    $radchoices = array(
        0 => get_string('hide', 'theme_adaptable'),
        1 => get_string('show', 'theme_adaptable'),
    );
    $default = 0;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Top padding social icons.
    $name = 'theme_adaptable/socialboxpaddingtopmobile';
    $title = get_string('socialboxpaddingtopmobile', 'theme_adaptable');
    $description = get_string('socialboxpaddingtopmobile', 'theme_adaptable');
    $choices = array(
        '5px' => "5px",
        '6px' => "6px",
        '7px' => "7px",
        '8px' => "8px",
        '9px' => "9px",
        '10px' => "10px",
        '12px' => "12px",
        '14px' => "14px",
        '16px' => "16px",
        '18px' => "18px",
        '20px' => "20px",
        '22px' => "22px",
    );
    $default = '10px';
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);


    // Bottom padding social icons.
    $name = 'theme_adaptable/socialboxpaddingbottommobile';
    $title = get_string('socialboxpaddingbottommobile', 'theme_adaptable');
    $description = get_string('socialboxpaddingbottommobile', 'theme_adaptable');
    $choices = $from0to12px;
    $default = '10px';
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Hide Logo.
    $name = 'theme_adaptable/hidelogomobile';
    $title = get_string('hidelogomobile', 'theme_adaptable');
    $description = get_string('hidelogomobiledesc', 'theme_adaptable');
    $radchoices = array(
        0 => get_string('hide', 'theme_adaptable'),
        1 => get_string('show', 'theme_adaptable'),
    );
    $default = 0;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Hide course title.
    $name = 'theme_adaptable/hidecoursetitlemobile';
    $title = get_string('hidecoursetitlemobile', 'theme_adaptable');
    $description = get_string('hidecoursetitlemobiledesc', 'theme_adaptable');
    $radchoices = array(
        0 => get_string('hide', 'theme_adaptable'),
        1 => get_string('show', 'theme_adaptable'),
    );
    $default = 0;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Hide Slider.
    $name = 'theme_adaptable/hideslidermobile';
    $title = get_string('hideslidermobile', 'theme_adaptable');
    $description = get_string('hideslidermobiledesc', 'theme_adaptable');
    $radchoices = array(
        0 => get_string('hide', 'theme_adaptable'),
        1 => get_string('show', 'theme_adaptable'),
    );
    $setting = new admin_setting_configselect($name, $title, $description, 1, $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Hide Breadcrumb.
    $name = 'theme_adaptable/hidebreadcrumbmobile';
    $title = get_string('hidebreadcrumbmobile', 'theme_adaptable');
    $description = get_string('hidebreadcrumbmobiledesc', 'theme_adaptable');
    $radchoices = array(
        0 => get_string('hide', 'theme_adaptable'),
        1 => get_string('show', 'theme_adaptable'),
    );
    $setting = new admin_setting_configselect($name, $title, $description, 0 , $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Hide Footer.
    $name = 'theme_adaptable/hidepagefootermobile';
    $title = get_string('hidepagefootermobile', 'theme_adaptable');
    $description = get_string('hidepagefootermobiledesc', 'theme_adaptable');
    $radchoices = array(
        0 => get_string('hide', 'theme_adaptable'),
        1 => get_string('show', 'theme_adaptable'),
    );
    $default = 0;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $ADMIN->add('theme_adaptable', $temp);
