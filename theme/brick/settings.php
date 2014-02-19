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
 * Settings for the Brick theme.
 *
 * @package    theme_brick
 * @copyright  2010 John Stabinger (http://newschoollearning.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Background image setting logo image setting.
    $name = 'theme_brick/logo';
    $title = get_string('logo','theme_brick');
    $description = get_string('logodesc', 'theme_brick');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Link color setting.
    $name = 'theme_brick/linkcolor';
    $title = get_string('linkcolor','theme_brick');
    $description = get_string('linkcolordesc', 'theme_brick');
    $default = '#06365b';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Link hover color setting.
    $name = 'theme_brick/linkhover';
    $title = get_string('linkhover','theme_brick');
    $description = get_string('linkhoverdesc', 'theme_brick');
    $default = '#5487ad';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Main color setting.
    $name = 'theme_brick/maincolor';
    $title = get_string('maincolor','theme_brick');
    $description = get_string('maincolordesc', 'theme_brick');
    $default = '#8e2800';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Main color accent setting.
    $name = 'theme_brick/maincolorlink';
    $title = get_string('maincolorlink','theme_brick');
    $description = get_string('maincolorlinkdesc', 'theme_brick');
    $default = '#fff0a5';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Heading color setting.
    $name = 'theme_brick/headingcolor';
    $title = get_string('headingcolor','theme_brick');
    $description = get_string('headingcolordesc', 'theme_brick');
    $default = '#5c3500';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

}