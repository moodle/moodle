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
 * This script migrates data from current database to another
 *
 * This script is not intended for beginners!
 * Potential problems:
 * - su to apache account or sudo before execution
 * - already broken DB scheme or invalid data
 *
 * @package    tool_dbtransfer
 * @copyright  2012 Petr Skoda {@link http://skodak.org/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once(__DIR__.'/../locallib.php');

$help =
    "Database migration script.

It is strongly recommended to turn off the web server
or enable CLI maintenance mode before starting the migration.

Options:
--dbtype=TYPE         Database type.
--dblibrary=TYPE      Database library. Defaults to 'native'.
--dbhost=HOST         Database host.
--dbname=NAME         Database name.
--dbuser=USERNAME     Database user.
--dbpass=PASSWORD     Database password.
--dbport=NUMBER       Database port.
--prefix=STRING       Table prefix for above database tables.
--dbsocket=PATH       Use database sockets. Available for some databases only.
-h, --help            Print out this help.

Example:
\$ sudo -u www-data /usr/bin/php admin/tool/dbtransfer/cli/migrate.php
";

// Now get cli options.
list($options, $unrecognized) = cli_get_params(
    array(
        'dbtype'            => null,
        'dblibrary'         => 'native',
        'dbhost'            => null,
        'dbname'            => null,
        'dbuser'            => null,
        'dbpass'            => null,
        'dbport'            => null,
        'prefix'            => null,
        'dbsocket'          => null,
        'maintenance'       => null,
        'list'              => false,
        'help'              => false,
    ),
    array(
        'm' => 'maintenance',
        'l' => 'list',
        'h' => 'help',
    )
);

if ($options['help']) {
    echo $help;
    exit(0);
}

if (empty($CFG->version)) {
    cli_error(get_string('missingconfigversion', 'debug'));
}

echo "\n".get_string('cliheading', 'tool_dbtransfer')."\n\n";

$drivers = tool_dbtransfer_get_drivers();

if (!isset($options['dbtype'])) {
    $choose = array();
    foreach ($drivers as $driver => $name) {
        list($dbtype, $dblibrary) = explode('/', $driver);
        $choose[$dbtype] = $dbtype;
    }
    $optionsstr = implode(', ', $choose);
    cli_heading(get_string('databasetypehead', 'install')." ($optionsstr)");
    $options['dbtype'] = cli_input(get_string('clitypevalue', 'admin'), '', $choose, true);
}

$choose = array();
foreach ($drivers as $driver => $name) {
    list($dbtype, $dblibrary) = explode('/', $driver);
    if ($dbtype === $options['dbtype']) {
        $choose[$dblibrary] = $dblibrary;
    }
}
if (!isset($options['dblibrary']) or !isset($choose[$options['dblibrary']])) {
    $optionsstr = implode(', ', $choose);
    cli_heading('Database library'." ($optionsstr)"); // Note: no need to localise unless we add real PDO drivers.
    $options['dblibrary'] = cli_input(get_string('clitypevalue', 'admin'), '', $choose, true);
}

if (!isset($options['dbhost'])) {
    cli_heading(get_string('databasehost', 'install'));
    $options['dbhost'] = cli_input(get_string('clitypevalue', 'admin'));
}

if (!isset($options['dbname'])) {
    cli_heading(get_string('databasename', 'install'));
    $options['dbname'] = cli_input(get_string('clitypevalue', 'admin'));
}

if (!isset($options['dbuser'])) {
    cli_heading(get_string('databaseuser', 'install'));
    $options['dbuser'] = cli_input(get_string('clitypevalue', 'admin'));
}

if (!isset($options['dbpass'])) {
    cli_heading(get_string('databasepass', 'install'));
    $options['dbpass'] = cli_input(get_string('clitypevalue', 'admin'));
}

if (!isset($options['prefix'])) {
    cli_heading(get_string('dbprefix', 'install'));
    $options['prefix'] = cli_input(get_string('clitypevalue', 'admin'));
}

if (!isset($options['dbport'])) {
    cli_heading(get_string('dbport', 'install'));
    $options['dbport'] = cli_input(get_string('clitypevalue', 'admin'));
}

if ($CFG->ostype !== 'WINDOWS') {
    if (!isset($options['dbsocket'])) {
        cli_heading(get_string('databasesocket', 'install'));
        $options['dbsocket'] = cli_input(get_string('clitypevalue', 'admin'));
    }
}

$a = (object)array('dbtypefrom' => $CFG->dbtype, 'dbtype' => $options['dbtype'],
    'dbname' => $options['dbname'], 'dbhost' => $options['dbhost']);
cli_heading(get_string('transferringdbto', 'tool_dbtransfer', $a));

// Try target DB connection.
$problem = '';

$targetdb = moodle_database::get_driver_instance($options['dbtype'], $options['dblibrary']);
$dboptions = array();
if ($options['dbport']) {
    $dboptions['dbport'] = $options['dbport'];
}
if ($options['dbsocket']) {
    $dboptions['dbsocket'] = $options['dbsocket'];
}
try {
    $targetdb->connect($options['dbhost'], $options['dbuser'], $options['dbpass'], $options['dbname'],
        $options['prefix'], $dboptions);
    if ($targetdb->get_tables()) {
        $problem .= get_string('targetdatabasenotempty', 'tool_dbtransfer');
    }
} catch (moodle_exception $e) {
    $problem .= $e->debuginfo."\n\n";
    $problem .= get_string('notargetconectexception', 'tool_dbtransfer');
}

if ($problem !== '') {
    echo $problem."\n\n";
    exit(1);
}

$feedback = new text_progress_trace();
tool_dbtransfer_transfer_database($DB, $targetdb, $feedback);
$feedback->finished();

cli_heading(get_string('success'));
exit(0);
