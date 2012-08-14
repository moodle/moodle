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
 * Upgrade helper functions
 *
 * This file is used for special upgrade functions - for example groups and gradebook.
 * These functions must use SQL and database related functions only- no other Moodle API,
 * because it might depend on db structures that are not yet present during upgrade.
 * (Do not use functions from accesslib.php, grades classes or group functions at all!)
 *
 * @package   core_install
 * @category  upgrade
 * @copyright 2007 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Remove all signed numbers from current database - mysql only.
 */
function upgrade_mysql_fix_unsigned_columns() {
    // we are not using standard API for changes of column
    // because everything 'signed'-related will be removed soon

    // if anybody already has numbers higher than signed limit the execution stops
    // and tables must be fixed manually before continuing upgrade

    global $DB;

    if ($DB->get_dbfamily() !== 'mysql') {
        return;
    }

    $pbar = new progress_bar('mysqlconvertunsigned', 500, true);

    $prefix = $DB->get_prefix();
    $tables = $DB->get_tables();

    $tablecount = count($tables);
    $i = 0;
    foreach ($tables as $table) {
        $i++;
        // set appropriate timeout - 5 minutes per milion of records should be enough, min 60 minutes just in case
        $count = $DB->count_records($table, array());
        $timeout = ($count/1000000)*5*60;
        $timeout = ($timeout < 60*60) ? 60*60 : (int)$timeout;

        $sql = "SHOW COLUMNS FROM `{{$table}}`";
        $rs = $DB->get_recordset_sql($sql);
        foreach ($rs as $column) {
            upgrade_set_timeout($timeout);

            $column = (object)array_change_key_case((array)$column, CASE_LOWER);
            if (stripos($column->type, 'unsigned') !== false) {
                $maxvalue = 0;
                if (preg_match('/^int/i', $column->type)) {
                    $maxvalue = 2147483647;
                } else if (preg_match('/^medium/i', $column->type)) {
                    $maxvalue = 8388607;
                } else if (preg_match('/^smallint/i', $column->type)) {
                    $maxvalue = 32767;
                } else if (preg_match('/^tinyint/i', $column->type)) {
                    $maxvalue = 127;
                }
                if ($maxvalue) {
                    // Make sure nobody is abusing our integer ranges - moodle int sizes are in digits, not bytes!!!
                    $invalidcount = $DB->get_field_sql("SELECT COUNT('x') FROM `{{$table}}` WHERE `$column->field` > :maxnumber", array('maxnumber'=>$maxvalue));
                    if ($invalidcount) {
                        throw new moodle_exception('notlocalisederrormessage', 'error', new moodle_url('/admin/'), "Database table '{$table}'' contains unsigned column '{$column->field}' with $invalidcount values that are out of allowed range, upgrade can not continue.");
                    }
                }
                $type = preg_replace('/unsigned/i', 'signed', $column->type);
                $notnull = ($column->null === 'NO') ? 'NOT NULL' : 'NULL';
                $default = (!is_null($column->default) and $column->default !== '') ? "DEFAULT '$column->default'" : '';
                $autoinc = (stripos($column->extra, 'auto_increment') !== false) ? 'AUTO_INCREMENT' : '';
                // primary and unique not necessary here, change_database_structure does not add prefix
                $sql = "ALTER TABLE `{$prefix}$table` MODIFY COLUMN `$column->field` $type $notnull $default $autoinc";
                $DB->change_database_structure($sql);
            }
        }
        $rs->close();

        $pbar->update($i, $tablecount, "Converted unsigned columns in MySQL database - $i/$tablecount.");
    }
}

/**
 * Migrate all text and binary columns to big size - mysql only.
 */
function upgrade_mysql_fix_lob_columns() {
    // we are not using standard API for changes of column intentionally

    global $DB;

    if ($DB->get_dbfamily() !== 'mysql') {
        return;
    }

    $pbar = new progress_bar('mysqlconvertlobs', 500, true);

    $prefix = $DB->get_prefix();
    $tables = $DB->get_tables();
    asort($tables);

    $tablecount = count($tables);
    $i = 0;
    foreach ($tables as $table) {
        $i++;
        // set appropriate timeout - 1 minute per thousand of records should be enough, min 60 minutes just in case
        $count = $DB->count_records($table, array());
        $timeout = ($count/1000)*60;
        $timeout = ($timeout < 60*60) ? 60*60 : (int)$timeout;

        $sql = "SHOW COLUMNS FROM `{{$table}}`";
        $rs = $DB->get_recordset_sql($sql);
        foreach ($rs as $column) {
            upgrade_set_timeout($timeout);

            $column = (object)array_change_key_case((array)$column, CASE_LOWER);
            if ($column->type === 'tinytext' or $column->type === 'mediumtext' or $column->type === 'text') {
                $notnull = ($column->null === 'NO') ? 'NOT NULL' : 'NULL';
                $default = (!is_null($column->default) and $column->default !== '') ? "DEFAULT '$column->default'" : '';
                // primary, unique and inc are not supported for texts
                $sql = "ALTER TABLE `{$prefix}$table` MODIFY COLUMN `$column->field` LONGTEXT $notnull $default";
                $DB->change_database_structure($sql);
            }
            if ($column->type === 'tinyblob' or $column->type === 'mediumblob' or $column->type === 'blob') {
                $notnull = ($column->null === 'NO') ? 'NOT NULL' : 'NULL';
                $default = (!is_null($column->default) and $column->default !== '') ? "DEFAULT '$column->default'" : '';
                // primary, unique and inc are not supported for blobs
                $sql = "ALTER TABLE `{$prefix}$table` MODIFY COLUMN `$column->field` LONGBLOB $notnull $default";
                $DB->change_database_structure($sql);
            }
        }
        $rs->close();

        $pbar->update($i, $tablecount, "Converted LOB columns in MySQL database - $i/$tablecount.");
    }
}
