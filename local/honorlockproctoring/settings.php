<?php
// This file is part of the honorlockproctoring module for Moodle - http://moodle.org/
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
 * Honorlock proctoring settings.
 *
 * @package    local_honorlockproctoring
 * @copyright  2023 Honorlock (https://honorlock.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_honorlockproctoring', get_string('settings_header', 'local_honorlockproctoring'));

    $settings->add(
        new admin_setting_configtext(
            'local_honorlockproctoring/honorlock_url',
            get_string('honorlock_url', 'local_honorlockproctoring'),
            get_string('honorlock_url_description', 'local_honorlockproctoring'),
            'https://app.honorlock.com',
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'local_honorlockproctoring/honorlock_client_id',
            get_string('honorlock_client_id', 'local_honorlockproctoring'),
            get_string('honorlock_client_id_description', 'local_honorlockproctoring'),
            '',
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'local_honorlockproctoring/honorlock_client_secret',
            get_string('honorlock_client_secret', 'local_honorlockproctoring'),
            get_string('honorlock_client_secret_description', 'local_honorlockproctoring'),
            '',
            PARAM_TEXT
        )
    );

    $ADMIN->add('localplugins', $settings);
}
