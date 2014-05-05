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
$string['databasetable'] = 'Database table';
$string['databasetable_help'] = 'Name of the table where logs will be stored. This table should have a structure identical to the one used by logstore_standard (mdl_logstore_standard_log).';
$string['includeactions'] = 'Include actions of these types';
$string['includelevels'] = 'Include actions with these educational levels';
$string['filters'] = 'Filter logs';
$string['filters_help'] = 'Enable filters that exclude some actions from being logged.';
$string['logguests'] = 'Log guest actions';
$string['other'] = 'Other';
$string['participating'] = 'Participating';
$string['pluginname'] = 'External database log';
$string['pluginname_desc'] = 'A log plugin that stores log entries in an external database table.';
$string['read'] = 'Read';
$string['tablenotfound'] = 'Specified table was not found';
$string['teaching'] = 'Teaching';
$string['testsettings'] = 'Test connection';
$string['testingsettings'] = 'Testing database settings...';
$string['update'] = 'Update';

