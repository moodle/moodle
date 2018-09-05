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
 * Validate that the current db structure matches the install.xml files.
 *
 * @package   core
 * @copyright 2014 Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/clilib.php');

$help = "Validate database structure

Options:
-h, --help            Print out this help.

Example:
\$ sudo -u www-data /usr/bin/php admin/cli/check_database_schema.php
";

list($options, $unrecognized) = cli_get_params(
    array(
        'help' => false,
    ),
    array(
        'h' => 'help',
    )
);

if ($options['help']) {
    echo $help;
    exit(0);
}

if (empty($CFG->version)) {
    echo "Database is not yet installed.\n";
    exit(2);
}

$dbmanager = $DB->get_manager();
$schema = $dbmanager->get_install_xml_schema();

if (!$errors = $dbmanager->check_database_schema($schema)) {
    echo "Database structure is ok.\n";
    exit(0);
}

foreach ($errors as $table => $items) {
    cli_separator();
    echo "$table\n";
    foreach ($items as $item) {
        echo " * $item\n";
    }
}
cli_separator();

exit(1);
