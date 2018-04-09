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
 * Settings that allow turning on and off various table features
 *
 * @package     atto_table
 * @copyright   2015 Joseph Inhofer <jinhofer@umn.edu>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$ADMIN->add('editoratto', new admin_category('atto_table', new lang_string('pluginname', 'atto_table')));

$settings = new admin_settingpage('atto_table_settings', new lang_string('settings', 'atto_table'));
if ($ADMIN->fulltree) {
    $name = new lang_string('allowborder', 'atto_table');
    $desc = new lang_string('allowborder_desc', 'atto_table');
    $default = 0;

    $setting = new admin_setting_configcheckbox('atto_table/allowborders',
                                                $name,
                                                $desc,
                                                $default);
    $settings->add($setting);

    $name = new lang_string('allowbackgroundcolour', 'atto_table');
    $default = 0;

    $setting = new admin_setting_configcheckbox('atto_table/allowbackgroundcolour',
                                                $name,
                                                '',
                                                $default);
    $settings->add($setting);

    $name = new lang_string('allowwidth', 'atto_table');
    $default = 0;

    $setting = new admin_setting_configcheckbox('atto_table/allowwidth',
                                                $name,
                                                '',
                                                $default);
    $settings->add($setting);
}
