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
 * Settings that allow configuring various tiny Font Family plugin features.
 *
 * @package     tiny_fontfamily
 * @copyright   2024 Mikko Haiku <mikko.haiku@mediamaisteri.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin = "tiny_fontfamily";
$ADMIN->add('editortiny', new admin_category($plugin, new lang_string('pluginname', $plugin)));

$settings = new admin_settingpage('tiny_fontfamily_settings', new lang_string('settings', $plugin));
if ($ADMIN->fulltree) {

    $defaults = [
        'Arial',
        'Verdana',
        'Tahoma',
        'Trebuchet MS',
        'Times New Roman',
        'Georgia',
        'Garamond',
        'Courier New',
        'Brush Script MT'
    ];

    $settings->add(
        new admin_setting_configtextarea($plugin . '/fonts',
                new lang_string('fonts', $plugin),
                new lang_string('fonts_desc', $plugin),
                implode("\r\n", $defaults), PARAM_TEXT, 80, 10));
}
