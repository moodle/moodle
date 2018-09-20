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
 * Settings that allow configuration of the fullscreen appearance
 *
 * @package    atto_fullscreen
 * @copyright  2015 Daniel Thies (dthies@ccal.edu)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$ADMIN->add('editoratto', new admin_category('atto_fullscreen', new lang_string('pluginname', 'atto_fullscreen')));

$settings = new admin_settingpage('atto_fullscreen_settings', new lang_string('settings', 'atto_fullscreen'));
if ($ADMIN->fulltree) {
    $setting = new admin_setting_configcheckbox('atto_fullscreen/requireedit',
        get_string('requireedit', 'atto_fullscreen'),
        get_string('requireedit_desc', 'atto_fullscreen'),
        0);

    $settings->add($setting);
}
