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
 * MySQL collation conversion tool.
 *
 * @package    core
 * @copyright  2012 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');      // cli only functions

if ($DB->get_dbfamily() !== 'mysql') {
    cli_error('This function is designed for MySQL databases only!');
}

// now get cli options
list($options, $unrecognized) = cli_get_params(array('help'=>false, 'list'=>false, 'collation'=>false, 'available'=>false),
    array('h'=>'help', 'l'=>'list', 'a'=>'available'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help =
    "MySQL collation conversions script.

It is strongly recommended to stop the web server before the conversion.
This script may be executed before the main upgrade - 1.9.x data for example.

Options:
--collation=COLLATION Convert MySQL tables to different collation
-l, --list            Show table and column information
-a, --available       Show list of available collations
-h, --help            Print out this help

Example:
\$ sudo -u www-data /usr/bin/php admin/cli/mysql_collation.php --collation=utf8_general_ci
";

if (!empty($options['collation'])) {
    $collations = mysql_get_collations();
    $collation = clean_param($options['collation'], PARAM_ALPHANUMEXT);
    $collation = strtolower($collation);
    if (!isset($collations[$collation])) {
        cli_error("Error: collation '$collation' is not available on this server!");
    }

    echo "Converting tables and columns to '$collation' for $CFG->wwwroot:\n";
    $prefix = $DB->get_prefix();
    $prefix = str_replace('_', '\\_', $prefix);
    $sql = "SHOW TABLE STATUS WHERE Name LIKE BINARY '$prefix%'";
    $rs = $DB->get_recordset_sql($sql);
    $converted = 0;
    $skipped   = 0;
    $errors    = 0;
    foreach ($rs as $table) {
        echo str_pad($table->name, 40). " - ";

        if ($table->collation === $collation) {
            echo "NO CHANGE\n";
            $skipped++;

        } else {
            $DB->change_database_structure("ALTER TABLE $table->name DEFAULT COLLATE = $collation");
            echo "CONVERTED\n";
            $converted++;
        }

        $sql = "SHOW FULL COLUMNS FROM $table->name WHERE collation IS NOT NULL";
        $rs2 = $DB->get_recordset_sql($sql);
        foreach ($rs2 as $column) {
            $column = (object)array_change_key_case((array)$column, CASE_LOWER);
            echo '    '.str_pad($column->field, 36). " - ";
            if ($column->collation === $collation) {
                echo "NO CHANGE\n";
                $skipped++;
                continue;
            }

            if ($column->type === 'tinytext' or $column->type === 'mediumtext' or $column->type === 'text' or $column->type === 'longtext') {
                $notnull = ($column->null === 'NO') ? 'NOT NULL' : 'NULL';
                $default = (!is_null($column->default) and $column->default !== '') ? "DEFAULT '$column->default'" : '';
                // primary, unique and inc are not supported for texts
                $sql = "ALTER TABLE $table->name MODIFY COLUMN $column->field $column->type COLLATE $collation $notnull $default";
                $DB->change_database_structure($sql);

            } else if (strpos($column->type, 'varchar') === 0) {
                $notnull = ($column->null === 'NO') ? 'NOT NULL' : 'NULL';
                $default = !is_null($column->default) ? "DEFAULT '$column->default'" : '';
                // primary, unique and inc are not supported for texts
                $sql = "ALTER TABLE $table->name MODIFY COLUMN $column->field $column->type COLLATE $collation $notnull $default";
                $DB->change_database_structure($sql);
            } else {
                echo "ERROR (unknown column type: $column->type)\n";
                $error++;
                continue;
            }
            echo "CONVERTED\n";
            $converted++;
        }
        $rs2->close();
    }
    $rs->close();
    echo "Converted: $converted, skipped: $skipped, errors: $errors\n";
    exit(0); // success

} else if (!empty($options['list'])) {
    echo "List of tables for $CFG->wwwroot:\n";
    $prefix = $DB->get_prefix();
    $prefix = str_replace('_', '\\_', $prefix);
    $sql = "SHOW TABLE STATUS WHERE Name LIKE BINARY '$prefix%'";
    $rs = $DB->get_recordset_sql($sql);
    $counts = array();
    foreach ($rs as $table) {
        if (isset($counts[$table->collation])) {
            $counts[$table->collation]++;
        } else {
            $counts[$table->collation] = 1;
        }
        echo str_pad($table->name, 40);
        echo $table->collation.  "\n";
        $collations = mysql_get_column_collations($table->name);
        foreach ($collations as $columname=>$collation) {
            if (isset($counts[$collation])) {
                $counts[$collation]++;
            } else {
                $counts[$collation] = 1;
            }
            echo '    ';
            echo str_pad($columname, 36);
            echo $collation.  "\n";
        }
    }
    $rs->close();

    echo "\n";
    echo "Table collations summary for $CFG->wwwroot:\n";
    foreach ($counts as $collation => $count) {
        echo "$collation: $count\n";
    }
    exit(0); // success

} else if (!empty($options['available'])) {
    echo "List of available MySQL collations for $CFG->wwwroot:\n";
    $collations = mysql_get_collations();
    foreach ($collations as $collation) {
        echo " $collation\n";
    }
    die;

} else {
    echo $help;
    die;
}



// ========== Some functions ==============

function mysql_get_collations() {
    global $DB;

    $collations = array();
    $sql = "SHOW COLLATION WHERE Collation LIKE 'utf8\_%' AND Charset = 'utf8'";
    $rs = $DB->get_recordset_sql($sql);
    foreach ($rs as $collation) {
        $collations[$collation->collation] = $collation->collation;
    }
    $rs->close();

    $collation = $DB->get_dbcollation();
    if (isset($collations[$collation])) {
        $collations[$collation] .= ' (default)';
    }

    return $collations;
}

function mysql_get_column_collations($tablename) {
    global $DB;

    $collations = array();
    $sql = "SELECT column_name, collation_name
              FROM INFORMATION_SCHEMA.COLUMNS
             WHERE table_schema = DATABASE() AND table_name = ? AND collation_name IS NOT NULL";
    $rs = $DB->get_recordset_sql($sql, array($tablename));
    foreach($rs as $record) {
        $collations[$record->column_name] = $record->collation_name;
    }
    $rs->close();
    return $collations;
}
