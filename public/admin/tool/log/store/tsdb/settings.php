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
 * Settings for logstore_tsdb.
 *
 * @package    logstore_tsdb
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    // TSDB Type selection.
    $settings->add(new admin_setting_configselect(
        'logstore_tsdb/tsdb_type',
        get_string('setting_tsdb_type', 'logstore_tsdb'),
        get_string('setting_tsdb_type_desc', 'logstore_tsdb'),
        'influxdb',
        [
            'influxdb' => get_string('setting_tsdb_influxdb', 'logstore_tsdb'),
            'timescaledb' => get_string('setting_tsdb_timescaledb', 'logstore_tsdb'),
        ]
    ));

    // Connection settings.
    $settings->add(new admin_setting_configtext(
        'logstore_tsdb/host',
        get_string('setting_host', 'logstore_tsdb'),
        get_string('setting_host_desc', 'logstore_tsdb'),
        'localhost',
        PARAM_HOST
    ));

    $settings->add(new admin_setting_configtext(
        'logstore_tsdb/port',
        get_string('setting_port', 'logstore_tsdb'),
        get_string('setting_port_desc', 'logstore_tsdb'),
        '8086',
        PARAM_INT
    ));

    $settings->add(new admin_setting_configtext(
        'logstore_tsdb/database',
        get_string('setting_database', 'logstore_tsdb'),
        get_string('setting_database_desc', 'logstore_tsdb'),
        'moodle_logs',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'logstore_tsdb/username',
        get_string('setting_username', 'logstore_tsdb'),
        get_string('setting_username_desc', 'logstore_tsdb'),
        '',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configpasswordunmask(
        'logstore_tsdb/password',
        get_string('setting_password', 'logstore_tsdb'),
        get_string('setting_password_desc', 'logstore_tsdb'),
        ''
    ));

    // Performance settings.
    $settings->add(new admin_setting_heading(
        'logstore_tsdb_performance',
        new lang_string('performance', 'admin'),
        ''
    ));

    $settings->add(new admin_setting_configselect(
        'logstore_tsdb/writemode',
        get_string('setting_writemode', 'logstore_tsdb'),
        get_string('setting_writemode_desc', 'logstore_tsdb'),
        'async',
        [
            'sync' => get_string('setting_writemode_sync', 'logstore_tsdb'),
            'async' => get_string('setting_writemode_async', 'logstore_tsdb'),
        ]
    ));

    $settings->add(new admin_setting_configtext(
        'logstore_tsdb/buffersize',
        get_string('setting_buffersize', 'logstore_tsdb'),
        get_string('setting_buffersize_desc', 'logstore_tsdb'),
        '1000',
        PARAM_INT
    ));

    $settings->add(new admin_setting_configtext(
        'logstore_tsdb/flushinterval',
        get_string('setting_flushinterval', 'logstore_tsdb'),
        get_string('setting_flushinterval_desc', 'logstore_tsdb'),
        '60',
        PARAM_INT
    ));
}
