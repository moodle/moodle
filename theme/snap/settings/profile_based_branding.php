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

$snapsettings = new admin_settingpage('themesnappbb', get_string('pbb', 'theme_snap'));

$name = 'theme_snap/pbb';
$heading = new lang_string('pbb', 'theme_snap');
$description = new lang_string('pbb_description', 'theme_snap');
$setting = new admin_setting_heading($name, $heading, $description);
$snapsettings->add($setting);

$name = 'theme_snap/pbb_enable';
$title = get_string('pbb_enable', 'theme_snap');
$description = get_string('pbb_enable_description', 'theme_snap');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$snapsettings->add($setting);

$fields = [
    'user|institution' => \core_user\fields::get_display_name('institution'),
    'user|department' => \core_user\fields::get_display_name('department'),
    'user|address' => \core_user\fields::get_display_name('address'),
    'user|city' => \core_user\fields::get_display_name('city'),
    'user|country' => \core_user\fields::get_display_name('country'),
];

// Get the profile fields which are string type.
$params = [
    'datatype' => 'text',
];
$sql = <<<SQL
        SELECT uf.id, uf.name, cat.name as category
          FROM {user_info_field} uf
          JOIN {user_info_category} cat ON uf.categoryid = cat.id
         WHERE uf.datatype = :datatype
SQL;
$pfields = $DB->get_records_sql($sql, $params);
foreach ($pfields as $pfield) {
    $fields['profile|' . $pfield->id] = '(' . $pfield->category . ') ' . $pfield->name;
}

$name = 'theme_snap/pbb_field';
$title = get_string('pbb_field', 'theme_snap');
$description = get_string('pbb_field_description', 'theme_snap');
$setting = new admin_setting_configselect($name, $title, $description, 'user|department', $fields);
$setting->set_updatedcallback('\\theme_snap\\local::clean_profile_based_branding_cache');
$snapsettings->add($setting);
// Only show pbb field if pbb is enabled.
$tohide = 'theme_snap/pbb_field';
$dependency = 'theme_snap/pbb_enable';
$settings->hide_if($tohide, $dependency, 'notchecked');

$settings->add($snapsettings);
