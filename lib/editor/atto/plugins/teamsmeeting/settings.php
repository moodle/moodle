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
 * Settings for the Teams Meeting atto plugin.
 *
 * @package    atto_teamsmeeting
 * @copyright  2020 Enovation Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$ADMIN->add('editoratto', new admin_category('atto_teamsmeeting', new lang_string('pluginname', 'atto_teamsmeeting')));

$settings = new admin_settingpage('atto_teamsmeeting_settings', new lang_string('settings', 'atto_teamsmeeting'));

if ($ADMIN->fulltree) {
    // Meeting application link.
    $name = new lang_string('meetingsapplink', 'atto_teamsmeeting');
    $desc = new lang_string('meetingsapplink_desc', 'atto_teamsmeeting');
    $olddefault = 'https://enovation.ie/msteams';
    $default = 'https://enomsteams.z16.web.core.windows.net';
    $existingconfig = get_config('atto_teamsmeeting', 'meetingapplink');
    if ($existingconfig == $olddefault) {
        $desc .= get_string('legacy_setting_warning', 'atto_teamsmeeting');
    }

    $setting = new admin_setting_configtext('atto_teamsmeeting/meetingapplink', $name, $desc, $default);
    $settings->add($setting);
}
