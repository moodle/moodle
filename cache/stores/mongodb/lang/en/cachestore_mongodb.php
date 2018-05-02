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
 * The language strings for the MongoDB store plugin.
 *
 * @package    cachestore_mongodb
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['database'] = 'Database';
$string['database_help'] = 'The name of the database to make use of.';
$string['extendedmode'] = 'Use extended keys';
$string['extendedmode_help'] = 'If enabled full key sets will be used when working with the plugin. This isn\'t used internally yet but would allow you to easily search and investigate the MongoDB plugin manually if you so choose. Turning this on will add an small overhead so should only be done if you require it.';
$string['password'] = 'Password';
$string['password_help'] = 'The password of the user being used for the connection.';
$string['pleaseupgrademongo'] = 'You are using an old version of the PHP Mongo extension (< 1.3). Support for old versions of the Mongo extension will be dropped in the future. Please consider upgrading.';
$string['pluginname'] = 'MongoDB';
$string['privacy:metadata:mongodb'] = 'The MongoDB cachestore plugin stores data briefly as part of its caching functionality. This data is stored on an MongoDB server where data is regularly removed.';
$string['privacy:metadata:mongodb:data'] = 'The various data stored in the cache';
$string['replicaset'] = 'Replica set';
$string['replicaset_help'] = 'The name of the replica set to connect to. If this is given the master will be determined by using the ismaster database command on the seeds, so the driver may end up connecting to a server that was not even listed.';
$string['server'] = 'Server';
$string['server_help'] = 'This is the connection string for the server you want to use. Multiple servers can be specified using a comma-separated list.';
$string['testserver'] = 'Test server';
$string['testserver_desc'] = 'The connection string for a server to use for testing. If a test server has been specified then MongoDB performance can be tested using the cache performance page in the administration block.
As an example: mongodb://127.0.0.1:27017';
$string['username'] = 'Username';
$string['username_help'] = 'The username to use when making a connection.';
$string['usesafe'] = 'Use safe';
$string['usesafe_help'] = 'If enabled the usesafe option will be used during insert, get, and remove operations. If you\'ve specified a replica set this will be forced on anyway.';
$string['usesafevalue'] = 'Use safe value';
$string['usesafevalue_help'] = 'You can choose to provide a specific value for use safe. This will determine the number of servers that operations must be completed on before they are deemed to have been completed.';
