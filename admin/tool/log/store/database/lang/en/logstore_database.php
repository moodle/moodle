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
 * Log store lang strings.
 *
 * @package    logstore_database
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['buffersize'] = 'Buffer size';
$string['buffersize_help'] = 'Number of log entries inserted in one batch database operation, which improves performance.';
$string['conectexception'] = 'Cannot connect to the database.';
$string['create'] = 'Create';
$string['databasesettings'] = 'Database settings';
$string['databasesettings_help'] = 'Connection details for the external log database: {$a}';
$string['databasepersist'] = 'Persistent database connections';
$string['databaseschema'] = 'Database schema';
$string['databasecollation'] = 'Database collation';
$string['databasehandlesoptions'] = 'Database handles options';
$string['databasehandlesoptions_help'] = 'Does the remote database handle its own options.';
$string['databasetable'] = 'Database table';
$string['databasetable_help'] = 'Name of the table where logs will be stored. This table should have a structure identical to the one used by logstore_standard (mdl_logstore_standard_log).';
$string['includeactions'] = 'Include actions of these types';
$string['includelevels'] = 'Include actions with these educational levels';
$string['filters'] = 'Filter logs';
$string['filters_help'] = 'Enable filters that exclude some actions from being logged.';
$string['jsonformat'] = 'JSON format';
$string['jsonformat_desc'] = 'Use standard JSON format instead of PHP serialised data in the \'other\' database field.';
$string['logguests'] = 'Log guest actions';
$string['other'] = 'Other';
$string['participating'] = 'Participating';
$string['pluginname'] = 'External database log';
$string['pluginname_desc'] = 'A log plugin that stores log entries in an external database table.';
$string['privacy:metadata:log'] = 'A collection of past events';
$string['privacy:metadata:log:anonymous'] = 'Whether the event was flagged as anonymous';
$string['privacy:metadata:log:eventname'] = 'The event name';
$string['privacy:metadata:log:ip'] = 'The IP address used at the time of the event';
$string['privacy:metadata:log:origin'] = 'The origin of the event';
$string['privacy:metadata:log:other'] = 'Additional information about the event';
$string['privacy:metadata:log:realuserid'] = 'The ID of the real user behind the event, when masquerading a user.';
$string['privacy:metadata:log:relateduserid'] = 'The ID of a user related to this event';
$string['privacy:metadata:log:timecreated'] = 'The time when the event occurred';
$string['privacy:metadata:log:userid'] = 'The ID of the user who triggered this event';
$string['read'] = 'Read';
$string['tablenotfound'] = 'Specified table was not found';
$string['teaching'] = 'Teaching';
$string['testsettings'] = 'Test connection';
$string['testingsettings'] = 'Testing database settings...';
$string['update'] = 'Update';

