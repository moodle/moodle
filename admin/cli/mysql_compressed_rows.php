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
 * MySQL table row compression tool tool.
 *
 * @package   core
 * @copyright 2014 Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir . '/clilib.php');

if ($DB->get_dbfamily() !== 'mysql') {
    cli_error('This script is used for MySQL databases only.');
}

$engine = strtolower($DB->get_dbengine());
if ($engine !== 'innodb' and $engine !== 'xtradb') {
    cli_error('This script is for MySQL servers using InnoDB or XtraDB engines only.');
}

list($options, $unrecognized) = cli_get_params(
    array('help' => false, 'info' => false, 'list' => false, 'fix' => false, 'showsql' => false),
    array('h' => 'help', 'i' => 'info', 'l' => 'list', 'f' => 'fix', 's' => 'showsql')
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help =
    "Script for detection of row size problems in MySQL InnoDB tables.

By default InnoDB storage table is using legacy Antelope file format
which has major restriction on database row size.
Use this script to detect and fix database tables with potential data
overflow problems.

Options:
-i, --info            Show database information
-l, --list            List problematic tables
-f, --fix             Attempt to fix all tables (requires SUPER privilege)
-s, --showsql         Print SQL statements for fixing of tables
-h, --help            Print out this help

Example:
\$ sudo -u www-data /usr/bin/php admin/cli/mysql_compressed_rows.php -l
";

/** @var mysql_sql_generator $generator */
$generator = $DB->get_manager()->generator;

$info = $DB->get_server_info();
$filepertable = $DB->get_record_sql("SHOW VARIABLES LIKE 'innodb_file_per_table'");
$filepertable = $filepertable ? $filepertable->value : '';
$fileformat = $DB->get_record_sql("SHOW VARIABLES LIKE 'innodb_file_format'");
$fileformat = $fileformat ? $fileformat->value : '';
$prefix = $DB->get_prefix();
$database = $CFG->dbname;

if (!empty($options['info'])) {
    echo "Database version:      " . $info['description'] . "\n";
    echo "Database name:         $database\n";
    echo "Database engine:       " . $DB->get_dbengine() . "\n";
    echo "innodb_file_per_table: $filepertable\n";
    echo "innodb_file_format:    $fileformat\n";

    exit(0);

} else if (!empty($options['list'])) {
    $problem = false;
    foreach ($DB->get_tables(false) as $table) {
        $columns = $DB->get_columns($table, false);
        $size = $generator->guess_antelope_row_size($columns);
        $format = $DB->get_row_format($table);
        if ($size <= $generator::ANTELOPE_MAX_ROW_SIZE) {
            continue;
        }

        echo str_pad($prefix . $table, 32, ' ', STR_PAD_RIGHT);
        echo str_pad($format, 11, ' ', STR_PAD_RIGHT);

        if ($format === 'Compact' or $format === 'Redundant') {
            $problem = true;
            echo " (needs fixing)\n";

        } else if ($format !== 'Compressed' and $format !== 'Dynamic') {
            echo " (unknown)\n";

        } else {
            echo "\n";
        }
    }

    if ($problem) {
        exit(1);
    }
    exit(0);

} else if (!empty($options['fix'])) {
    $fixtables = array();
    foreach ($DB->get_tables(false) as $table) {
        $columns = $DB->get_columns($table, false);
        $size = $generator->guess_antelope_row_size($columns);
        $format = $DB->get_row_format($table);
        if ($size <= $generator::ANTELOPE_MAX_ROW_SIZE) {
            continue;
        }
        if ($format === 'Compact' or $format === 'Redundant') {
            $fixtables[$table] = $table;
        }
    }

    if (!$fixtables) {
        echo "No changes necessary\n";
        exit(0);
    }

    if ($filepertable !== 'ON') {
        try {
            $DB->execute("SET GLOBAL innodb_file_per_table=1");
        } catch (dml_exception $e) {
            echo "Cannot enable GLOBAL innodb_file_per_table setting, use --showsql option and execute the statements manually.";
            throw $e;
        }
    }
    if ($fileformat !== 'Barracuda') {
        try {
            $DB->execute("SET GLOBAL innodb_file_format=Barracuda");
        } catch (dml_exception $e) {
            echo "Cannot change GLOBAL innodb_file_format setting, use --showsql option and execute the statements manually.";
            throw $e;
        }
    }

    if (!$DB->is_compressed_row_format_supported(false)) {
        echo "MySQL server is not compatible with compressed row format.";
        exit(1);
    }

    foreach ($fixtables as $table) {
        $DB->change_database_structure("ALTER TABLE {$prefix}$table ROW_FORMAT=Compressed");
        echo str_pad($prefix . $table, 32, ' ', STR_PAD_RIGHT) . " ... Compressed\n";
    }

    exit(0);

} else if (!empty($options['showsql'])) {
    $fixtables = array();

    foreach ($DB->get_tables(false) as $table) {
        $columns = $DB->get_columns($table, false);
        $size = $generator->guess_antelope_row_size($columns);
        $format = $DB->get_row_format($table);
        if ($size <= $generator::ANTELOPE_MAX_ROW_SIZE) {
            continue;
        }
        if ($format === 'Compact' or $format === 'Redundant') {
            $fixtables[$table] = $table;
        }
    }
    if (!$fixtables) {
        echo "No changes necessary\n";
        exit(0);
    }

    echo "Copy the following SQL statements and execute them using account with SUPER privilege:\n\n";
    echo "USE $database;\n";
    echo "SET SESSION sql_mode=STRICT_ALL_TABLES;\n";
    echo "SET GLOBAL innodb_file_per_table=1;\n";
    echo "SET GLOBAL innodb_file_format=Barracuda;\n";
    foreach ($fixtables as $table) {
        echo "ALTER TABLE {$prefix}$table ROW_FORMAT=Compressed;\n";
    }
    echo "\n";
    exit(0);

} else {
    echo $help;
    die;
}


