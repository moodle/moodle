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
 * Plugin administration pages are defined here.
 *
 * @package     mod_googlemeet
 * @category    admin
 * @copyright   2020 Rone Santos <ronefel@hotmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext(
        'googlemeet/apikey',
        get_string('apikey', 'googlemeet'),
        '',
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'googlemeet/clientid',
        get_string('clientid', 'googlemeet'),
        get_string('clientid_desc', 'googlemeet'),
        ''
    ));

    $settings->add(new admin_setting_configcheckbox(
        'googlemeet/multieventdateexpanded',
        get_string('multieventdateexpanded', 'googlemeet'),
        get_string('multieventdateexpanded_desc', 'googlemeet'),
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'googlemeet/roomurlexpanded',
        get_string('roomurlexpanded', 'googlemeet'),
        get_string('roomurlexpanded_desc', 'googlemeet'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'googlemeet/notificationexpanded',
        get_string('notificationexpanded', 'googlemeet'),
        get_string('notifycationexpanded_desc', 'googlemeet'),
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'googlemeet/notify',
        get_string('notify', 'googlemeet'),
        get_string('notify_help', 'googlemeet'),
        1
    ));

    $minutes = array();
    for ($i = 0; $i <= 120; $i = $i + 5) {
        $minutes[$i] = $i;
    }

    $settings->add(new admin_setting_configselect(
        'googlemeet/minutesbefore',
        get_string('minutesbefore', 'googlemeet'),
        get_string('minutesbefore_help', 'googlemeet'),
        10,
        $minutes
    ));

    $settings->add(new admin_setting_confightmleditor(
        'googlemeet/emailcontent',
        get_string('emailcontent', 'googlemeet'),
        get_string('emailcontent_help', 'googlemeet'),
        get_string('emailcontent_default', 'googlemeet'),
        PARAM_RAW
    ));
}
