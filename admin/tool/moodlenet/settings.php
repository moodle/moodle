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
 * Puts the plugin actions into the admin settings tree.
 *
 * @package     tool_moodlenet
 * @copyright   2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Create a MoodleNet category.
    $ADMIN->add('root', new admin_category('moodlenet', get_string('pluginname', 'tool_moodlenet')));
    // Our settings page.
    $settings = new admin_settingpage('tool_moodlenet', get_string('moodlenetsettings', 'tool_moodlenet'));
    $ADMIN->add('moodlenet', $settings);

    $temp = new admin_setting_configcheckbox('tool_moodlenet/enablemoodlenet', get_string('enablemoodlenet', 'tool_moodlenet'),
        new lang_string('enablemoodlenet_desc', 'tool_moodlenet'), 1, 1, 0);
    $settings->add($temp);

    $temp = new admin_setting_configtext('tool_moodlenet/defaultmoodlenetname',
        get_string('defaultmoodlenetname', 'tool_moodlenet'), new lang_string('defaultmoodlenetname_desc', 'tool_moodlenet'),
        'Moodle HQ MoodleNet');
    $settings->add($temp);

    $temp = new admin_setting_configtext('tool_moodlenet/defaultmoodlenet', get_string('defaultmoodlenet', 'tool_moodlenet'),
        new lang_string('defaultmoodlenet_desc', 'tool_moodlenet'), 'https://home.moodle.net');
    $settings->add($temp);
}
