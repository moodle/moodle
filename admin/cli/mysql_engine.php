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
 * MySQL engine conversion tool.
 *
 * @package    core
 * @subpackage cli
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir.'/clilib.php');      // cli only functions

if ($DB->get_dbfamily() !== 'mysql') {
    cli_error('This function is designed for MySQL databases only!');
}

// now get cli options
list($options, $unrecognized) = cli_get_params(array('help'=>false, 'list'=>false, 'engine'=>false),
                                               array('h'=>'help', 'l'=>'list'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help =
"MySQL engine conversions script.

It is recommended to stop the web server before the conversion.
Do not use MyISAM if possible, because it is not ACID compliant
and does not support transactions.

Options:
--engine=ENGINE       Convert MySQL tables to different engine
-l, --list            Show table information
-h, --help            Print out this help

Example:
\$sudo -u www-data /usr/bin/php admin/cli/mysql_engine.php --engine=InnoDB
";

if (!empty($options['engine'])) {
    $engine = clean_param($options['engine'], PARAM_ALPHA);

    echo "Converting tables to '$engine' for $CFG->wwwroot:\n";
    $prefix = $DB->get_prefix();
    $prefix = str_replace('_', '\\_', $prefix);
    $sql = "SHOW TABLE STATUS WHERE Name LIKE BINARY '$prefix%'";
    $rs = $DB->get_recordset_sql($sql);
    $converted = 0;
    $skipped   = 0;
    foreach ($rs as $table) {
        if ($table->engine === $engine) {
            echo str_pad($table->name, 40). " - NO CONVERSION NEEDED\n";
            $skipped++;
            continue;
        }
        echo str_pad($table->name, 40). " - ";

        $DB->change_database_structure("ALTER TABLE {$table->name} ENGINE = $engine");
        echo "DONE\n";
        $converted++;
    }
    $rs->close();
    echo "Converted: $converted, skipped: $skipped\n";
    exit(0); // success

} else if (!empty($options['list'])) {
    echo "List of tables for $CFG->wwwroot:\n";
    $prefix = $DB->get_prefix();
    $prefix = str_replace('_', '\\_', $prefix);
    $sql = "SHOW TABLE STATUS WHERE Name LIKE BINARY '$prefix%'";
    $rs = $DB->get_recordset_sql($sql);
    $counts = array();
    foreach ($rs as $table) {
        if (isset($counts[$table->engine])) {
            $counts[$table->engine]++;
        } else {
            $counts[$table->engine] = 1;
        }
        echo str_pad($table->engine, 10);
        echo $table->name .  "\n";
    }
    $rs->close();

    echo "\n";
    echo "Table engines summary for $CFG->wwwroot:\n";
    foreach ($counts as $engine => $count) {
        echo "$engine: $count\n";
    }
    exit(0); // success

} else {
    echo $help;
    die;
}
