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
 * Settings for the magazine theme.
 *
 * @package    theme_magazine
 * @copyright  2010 John Stabinger (http://newschoollearning.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Background image setting.
    $name = 'theme_magazine/background';
    $title = get_string('background','theme_magazine');
    $description = get_string('backgrounddesc', 'theme_magazine');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // logo image setting.
    $name = 'theme_magazine/logo';
    $title = get_string('logo','theme_magazine');
    $description = get_string('logodesc', 'theme_magazine');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // link color setting.
    $name = 'theme_magazine/linkcolor';
    $title = get_string('linkcolor','theme_magazine');
    $description = get_string('linkcolordesc', 'theme_magazine');
    $default = '#32529a';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // link hover color setting.
    $name = 'theme_magazine/linkhover';
    $title = get_string('linkhover','theme_magazine');
    $description = get_string('linkhoverdesc', 'theme_magazine');
    $default = '#4e2300';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // main color setting.
    $name = 'theme_magazine/maincolor';
    $title = get_string('maincolor','theme_magazine');
    $description = get_string('maincolordesc', 'theme_magazine');
    $default = '#002f2f';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // main color accent setting.
    $name = 'theme_magazine/maincoloraccent';
    $title = get_string('maincoloraccent','theme_magazine');
    $description = get_string('maincoloraccentdesc', 'theme_magazine');
    $default = '#092323';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // heading color setting.
    $name = 'theme_magazine/headingcolor';
    $title = get_string('headingcolor','theme_magazine');
    $description = get_string('headingcolordesc', 'theme_magazine');
    $default = '#4e0000';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // block heading color setting.
    $name = 'theme_magazine/blockcolor';
    $title = get_string('blockcolor','theme_magazine');
    $description = get_string('blockcolordesc', 'theme_magazine');
    $default = '#002f2f';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // forum subject background color setting.
    $name = 'theme_magazine/forumback';
    $title = get_string('forumback','theme_magazine');
    $description = get_string('forumbackdesc', 'theme_magazine');
    $default = '#e6e2af';
    $previewconfig = NULL;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

}
