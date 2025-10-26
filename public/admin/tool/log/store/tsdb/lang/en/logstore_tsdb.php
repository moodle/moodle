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
 * Language strings for logstore_tsdb.
 *
 * @package    logstore_tsdb
 * @copyright  2025 Your Name <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin information.
$string['pluginname'] = 'Time Series Database Log Store';
$string['pluginname_desc'] = 'A log store plugin that saves Moodle events to a Time Series Database (InfluxDB or TimescaleDB) for efficient long-term storage and analytics.';
$string['privacy:metadata'] = 'The Time Series Database Log Store plugin does not store any personal data itself. It forwards event data to an external TSDB system.';

// Settings.
$string['setting_tsdb_type'] = 'TSDB Type';
$string['setting_tsdb_type_desc'] = 'Select the Time Series Database implementation to use';
$string['setting_tsdb_influxdb'] = 'InfluxDB';
$string['setting_tsdb_timescaledb'] = 'TimescaleDB';

$string['setting_host'] = 'Host';
$string['setting_host_desc'] = 'Hostname or IP address of the TSDB server';

$string['setting_port'] = 'Port';
$string['setting_port_desc'] = 'Port number of the TSDB server (8086 for InfluxDB, 5432 for TimescaleDB)';

$string['setting_database'] = 'Database/Bucket';
$string['setting_database_desc'] = 'Database name (TimescaleDB) or Bucket name (InfluxDB)';

$string['setting_username'] = 'Username';
$string['setting_username_desc'] = 'Username for TSDB authentication';

$string['setting_password'] = 'Password';
$string['setting_password_desc'] = 'Password for TSDB authentication';

$string['setting_writemode'] = 'Write Mode';
$string['setting_writemode_desc'] = 'Synchronous writes block request until data is written. Asynchronous writes buffer data and write in batches (recommended for production).';
$string['setting_writemode_sync'] = 'Synchronous';
$string['setting_writemode_async'] = 'Asynchronous (recommended)';

$string['setting_buffersize'] = 'Buffer Size';
$string['setting_buffersize_desc'] = 'Number of events to buffer before flushing (only used in asynchronous mode)';

$string['setting_flushinterval'] = 'Flush Interval';
$string['setting_flushinterval_desc'] = 'Maximum seconds between buffer flushes (only used in asynchronous mode)';

// Errors.
$string['error_connection'] = 'Could not connect to TSDB server at {$a->host}:{$a->port}';
$string['error_write'] = 'Error writing events to TSDB: {$a}';
$string['error_config'] = 'TSDB configuration is incomplete. Please configure all required settings.';

// Tasks.
$string['task_buffer_flush'] = 'Flush buffered events to TSDB';
