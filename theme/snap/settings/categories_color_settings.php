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
 * Snap settings.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$snapsettings = new admin_settingpage('themesnapcolorcategories', get_string('category_color', 'theme_snap'));

$name = 'theme_snap/categorycorlor';

$heading = new lang_string('category_color', 'theme_snap');
$description = new lang_string('category_color_description', 'theme_snap');
$setting = new admin_setting_heading($name, $heading, $description);
$snapsettings->add($setting);

$name = 'theme_snap/category_color_palette';
$title = get_string('category_color_palette', 'theme_snap');
$description = get_string('category_color_palette_description', 'theme_snap');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$snapsettings->add($setting);

$name = 'theme_snap/category_color';
$title = get_string('jsontext', 'theme_snap');
$description = get_string('jsontextdescription', 'theme_snap');
$default = '';
$setting = new \theme_snap\admin_setting_configcolorcategory($name, $title, $description, $default);
$snapsettings->add($setting);
$setting->set_updatedcallback('theme_reset_all_caches');
$snapsettings->add($setting);

$settings->add($snapsettings);
