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
 * Admin settings and defaults
 *
 * @package auth_manual
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Introductory explanation.
    $settings->add(new admin_setting_heading('auth_manual/pluginname',
            new lang_string('passwdexpire_settings', 'auth_manual'),
            new lang_string('auth_manualdescription', 'auth_manual')));

    $expirationoptions = array(
        new lang_string('no'),
        new lang_string('yes'),
    );

    $settings->add(new admin_setting_configselect('auth_manual/expiration',
        new lang_string('expiration', 'auth_manual'),
        new lang_string('expiration_desc', 'auth_manual'), 0, $expirationoptions));

    $expirationtimeoptions = array(
        '30' => new lang_string('numdays', '', 30),
        '60' => new lang_string('numdays', '', 60),
        '90' => new lang_string('numdays', '', 90),
        '120' => new lang_string('numdays', '', 120),
        '150' => new lang_string('numdays', '', 150),
        '180' => new lang_string('numdays', '', 180),
        '365' => new lang_string('numdays', '', 365),
    );

    $settings->add(new admin_setting_configselect('auth_manual/expirationtime',
        new lang_string('passwdexpiretime', 'auth_manual'),
        new lang_string('passwdexpiretime_desc', 'auth_manual'), 30, $expirationtimeoptions));

    $expirationwarningoptions = array(
        '0' => new lang_string('never'),
        '1' => new lang_string('numdays', '', 1),
        '2' => new lang_string('numdays', '', 2),
        '3' => new lang_string('numdays', '', 3),
        '4' => new lang_string('numdays', '', 4),
        '5' => new lang_string('numdays', '', 5),
        '6' => new lang_string('numdays', '', 6),
        '7' => new lang_string('numdays', '', 7),
        '10' => new lang_string('numdays', '', 10),
        '14' => new lang_string('numdays', '', 14),
    );

    $settings->add(new admin_setting_configselect('auth_manual/expiration_warning',
        new lang_string('expiration_warning', 'auth_manual'),
        new lang_string('expiration_warning_desc', 'auth_manual'), 0, $expirationwarningoptions));

    // Display locking / mapping of profile fields.
    $authplugin = get_auth_plugin('manual');
    display_auth_lock_options($settings, $authplugin->authtype,
        $authplugin->userfields, get_string('auth_fieldlocks_help', 'auth'), false, false);
}
