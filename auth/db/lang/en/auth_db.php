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
 * Strings for component 'auth_db', language 'en'.
 *
 * @package   auth_db
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['auth_dbcantconnect'] = 'Could not connect to the specified authentication database...';
$string['auth_dbdebugauthdb'] = 'Debug ADOdb';
$string['auth_dbdebugauthdbhelp'] = 'Debug ADOdb connection to external database - use when getting empty page during login. Not suitable for production sites.';
$string['auth_dbdeleteuser'] = 'Deleted user {$a->name} id {$a->id}';
$string['auth_dbdeleteusererror'] = 'Error deleting user {$a}';
$string['auth_dbdescription'] = 'This method uses an external database table to check whether a given username and password is valid.  If the account is a new one, then information from other fields may also be copied across into Moodle.';
$string['auth_dbextencoding'] = 'External db encoding';
$string['auth_dbextencodinghelp'] = 'Encoding used in external database';
$string['auth_dbextrafields'] = 'These fields are optional.  You can choose to pre-fill some Moodle user fields with information from the <b>external database fields</b> that you specify here. <p>If you leave these blank, then defaults will be used.</p><p>In either case, the user will be able to edit all of these fields after they log in.</p>';
$string['auth_dbfieldpass'] = 'Name of the field containing passwords';
$string['auth_dbfieldpass_key'] = 'Password field';
$string['auth_dbfielduser'] = 'Name of the field containing usernames. This field must be a varchar data type.';
$string['auth_dbfielduser_key'] = 'Username field';
$string['auth_dbhost'] = 'The computer hosting the database server. Use a system DSN entry if using ODBC. Use a PDO DSN entry if using PDO.';
$string['auth_dbhost_key'] = 'Host';
$string['auth_dbchangepasswordurl_key'] = 'Password-change URL';
$string['auth_dbinsertuser'] = 'Inserted user {$a->name} id {$a->id}';
$string['auth_dbinsertuserduplicate'] = 'Error inserting user {$a->username} - user with this username was already created through \'{$a->auth}\' plugin.';
$string['auth_dbinsertusererror'] = 'Error inserting user {$a}';
$string['auth_dbname'] = 'Name of the database itself. Leave empty if using an ODBC DSN. Leave empty if your PDO DSN already contains the database name.';
$string['auth_dbname_key'] = 'DB name';
$string['auth_dbpass'] = 'Password matching the above username';
$string['auth_dbpass_key'] = 'Password';
$string['auth_dbpasstype'] = '<p>Specify the format that the password field is using.</p> <p>Use \'internal\' if you want the external database to manage usernames and email addresses, but Moodle to manage passwords. If you use \'internal\', you must provide a populated email address field in the external database, and you must enable the \auth_db\task\sync_users scheduled task. Moodle will send an email to new users with a temporary password.</p>';
$string['auth_dbpasstype_key'] = 'Password format';
$string['auth_dbreviveduser'] = 'Revived user {$a->name} id {$a->id}';
$string['auth_dbrevivedusererror'] = 'Error reviving user {$a}';
$string['auth_dbsaltedcrypt'] = 'Crypt one-way string hashing';
$string['auth_dbsetupsql'] = 'SQL setup command';
$string['auth_dbsetupsqlhelp'] = 'SQL command for special database setup, often used to setup communication encoding - example for MySQL and PostgreSQL: <em>SET NAMES \'utf8\'</em>';
$string['auth_dbsuspenduser'] = 'Suspended user {$a->name} id {$a->id}';
$string['auth_dbsuspendusererror'] = 'Error suspending user {$a}';
$string['auth_dbsybasequoting'] = 'Use sybase quotes';
$string['auth_dbsybasequotinghelp'] = 'Sybase style single quote escaping - needed for Oracle, MS SQL and some other databases. Do not use for MySQL!';
$string['auth_dbsyncuserstask'] = 'Synchronise users task';
$string['auth_dbtable'] = 'Name of the table in the database';
$string['auth_dbtable_key'] = 'Table';
$string['auth_dbtype'] = 'The database type (see the documentation <a href="http://adodb.org/dokuwiki/doku.php" target="_blank">ADOdb - Database Abstraction Layer for PHP</a> for details).';
$string['auth_dbtype_key'] = 'Database';
$string['auth_dbupdateusers'] = 'Update users';
$string['auth_dbupdateusers_description'] = 'As well as inserting new users, update existing users.';
$string['auth_dbupdatinguser'] = 'Updating user {$a->name} id {$a->id}';
$string['auth_dbuser'] = 'Username with read access to the database';
$string['auth_dbuser_key'] = 'DB user';
$string['auth_dbuserstoadd'] = 'User entries to add: {$a}';
$string['auth_dbuserstoremove'] = 'User entries to remove: {$a}';
$string['auth_dbnoexttable'] = 'External table not specified.';
$string['auth_dbnouserfield'] = 'External user field not specified.';
$string['auth_dbcannotconnect'] = 'Cannot connect to external database.';
$string['auth_dbcannotreadtable'] = 'Cannot read external table.';
$string['auth_dbtableempty'] = 'External table is empty.';
$string['auth_dbcolumnlist'] = 'External table contains the following columns:<br />{$a}';
$string['auth_dbupdateerror'] = 'Error updating external database.';
$string['pluginname'] = 'External database';
$string['privacy:metadata'] = 'The External database authentication plugin does not store any personal data.';
