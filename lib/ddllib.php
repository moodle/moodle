<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-3001 Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

// This library includes all the required functions used to handle the DB
// structure (DDL) independently of the underlying RDBMS in use. All the functions
// rely on the XMLDBDriver classes to be able to generate the correct SQL 
// syntax needed by each DB.
//
// To define any structure to be created we'll use the schema defined 
// by the XMLDB classes, for tables, fields, indexes, keys and other
// statements instead of direct handling of SQL sentences.
//
// This library should be used, exclusively, by the installation and
// upgrade process of Moodle.
//
// For further documentation, visit http://docs.moodle.org/en/DDL_functions

/// Add required XMLDB constants
    require_once($CFG->libdir . '/xmldb/classes/XMLDBConstants.php');

/// Add main XMLDB Generator
    require_once($CFG->libdir . '/xmldb/classes/generators/XMLDBGenerator.class.php');

/// Add required XMLDB DB classes
    require_once($CFG->libdir . '/xmldb/classes/XMLDBObject.class.php');
    require_once($CFG->libdir . '/xmldb/classes/XMLDBFile.class.php');
    require_once($CFG->libdir . '/xmldb/classes/XMLDBStructure.class.php');
    require_once($CFG->libdir . '/xmldb/classes/XMLDBTable.class.php');
    require_once($CFG->libdir . '/xmldb/classes/XMLDBField.class.php');
    require_once($CFG->libdir . '/xmldb/classes/XMLDBKey.class.php');
    require_once($CFG->libdir . '/xmldb/classes/XMLDBIndex.class.php');
    require_once($CFG->libdir . '/xmldb/classes/XMLDBStatement.class.php');

/// Based on $CFG->dbtype, add the proper generator class
    require_once($CFG->libdir . '/xmldb/classes/generators/' . $CFG->dbtype . '/' . $CFG->dbtype . '.class.php');

/// Add other libraries
    require_once($CFG->libdir . '/xmlize.php');
    

/**
 * Add a new field to a table, or modify an existing one (if oldfield is defined).
 * Warning: Please be careful on primary keys, as this function will eat auto_increments
 *
 * @uses $CFG
 * @uses $db
 * @param string $table the name of the table to modify. (Without the prefix.)
 * @param string $oldfield If changing an existing column, the name of that column.
 * @param string $field The name of the column at the end of the operation.
 * @param string $type The type of the column at the end of the operation. TEXT, VARCHAR, CHAR, INTEGER, REAL, or TINYINT
 * @param string $size The size of that column type. As in VARCHAR($size), or INTEGER($size).
 * @param string $signed For numeric column types, whether that column is 'signed' or 'unsigned'.
 * @param string $default The new default value for the column.
 * @param string $null 'not null', or '' to allow nulls.
 * @param string $after Which column to insert this one after. Not supported on Postgres.
 *
 * @return boolean Wheter the operation succeeded.
 */
function table_column($table, $oldfield, $field, $type='integer', $size='10',
                      $signed='unsigned', $default='0', $null='not null', $after='') {
    global $CFG, $db, $empty_rs_cache;

    if (!empty($empty_rs_cache[$table])) {  // Clear the recordset cache because it's out of date
        unset($empty_rs_cache[$table]);
    }

    switch (strtolower($CFG->dbtype)) {

        case 'mysql':
        case 'mysqlt':

            switch (strtolower($type)) {
                case 'text':
                    $type = 'TEXT';
                    $signed = '';
                    break;
                case 'integer':
                    $type = 'INTEGER('. $size .')';
                    break;
                case 'varchar':
                    $type = 'VARCHAR('. $size .')';
                    $signed = '';
                    break;
                case 'char':
                    $type = 'CHAR('. $size .')';
                    $signed = '';
                    break;
            }

            if (!empty($oldfield)) {
                $operation = 'CHANGE '. $oldfield .' '. $field;
            } else {
                $operation = 'ADD '. $field;
            }

            $default = 'DEFAULT \''. $default .'\'';

            if (!empty($after)) {
                $after = 'AFTER `'. $after .'`';
            }

            return execute_sql('ALTER TABLE '. $CFG->prefix . $table .' '. $operation .' '. $type .' '. $signed .' '. $default .' '. $null .' '. $after);

        case 'postgres7':        // From Petri Asikainen
            //Check db-version
            $dbinfo = $db->ServerInfo();
            $dbver = substr($dbinfo['version'],0,3);

            //to prevent conflicts with reserved words
            $realfield = '"'. $field .'"';
            $field = '"'. $field .'_alter_column_tmp"';
            $oldfield = '"'. $oldfield .'"';

            switch (strtolower($type)) {
                case 'tinyint':
                case 'integer':
                    if ($size <= 4) {
                        $type = 'INT2';
                    }
                    if ($size <= 10) {
                        $type = 'INT';
                    }
                    if  ($size > 10) {
                        $type = 'INT8';
                    }
                    break;
                case 'varchar':
                    $type = 'VARCHAR('. $size .')';
                    break;
                case 'char':
                    $type = 'CHAR('. $size .')';
                    $signed = '';
                    break;
            }

            $default = '\''. $default .'\'';

            //After is not implemented in postgesql
            //if (!empty($after)) {
            //    $after = "AFTER '$after'";
            //}

            //Use transactions
            execute_sql('BEGIN');

            //Always use temporary column
            execute_sql('ALTER TABLE '. $CFG->prefix . $table .' ADD COLUMN '. $field .' '. $type);
            //Add default values
            execute_sql('UPDATE '. $CFG->prefix . $table .' SET '. $field .'='. $default);


            if ($dbver >= '7.3') {
                // modifying 'not null' is posible before 7.3
                //update default values to table
                if (strtoupper($null) == 'NOT NULL') {
                    execute_sql('UPDATE '. $CFG->prefix . $table .' SET '. $field .'='. $default .' WHERE '. $field .' IS NULL');
                    execute_sql('ALTER TABLE '. $CFG->prefix . $table .' ALTER COLUMN '. $field .' SET '. $null);
                } else {
                    execute_sql('ALTER TABLE '. $CFG->prefix . $table .' ALTER COLUMN '. $field .' DROP NOT NULL');
                }
            }

            execute_sql('ALTER TABLE '. $CFG->prefix . $table .' ALTER COLUMN '. $field .' SET DEFAULT '. $default);

            if ( $oldfield != '""' ) {

                // We are changing the type of a column. This may require doing some casts...
                $casting = '';
                $oldtype = column_type($table, $oldfield);
                $newtype = column_type($table, $field);

                // Do we need a cast?
                if($newtype == 'N' && $oldtype == 'C') {
                    $casting = 'CAST(CAST('.$oldfield.' AS TEXT) AS REAL)';
                }
                else if($newtype == 'I' && $oldtype == 'C') {
                    $casting = 'CAST(CAST('.$oldfield.' AS TEXT) AS INTEGER)';
                }
                else {
                    $casting = $oldfield;
                }

                // Run the update query, casting as necessary
                execute_sql('UPDATE '. $CFG->prefix . $table .' SET '. $field .' = '. $casting);
                execute_sql('ALTER TABLE  '. $CFG->prefix . $table .' DROP COLUMN '. $oldfield);
            }

            execute_sql('ALTER TABLE '. $CFG->prefix . $table .' RENAME COLUMN '. $field .' TO '. $realfield);

            return execute_sql('COMMIT');

        default:
            switch (strtolower($type)) {
                case 'integer':
                    $type = 'INTEGER';
                    break;
                case 'varchar':
                    $type = 'VARCHAR';
                    break;
            }

            $default = 'DEFAULT \''. $default .'\'';

            if (!empty($after)) {
                $after = 'AFTER '. $after;
            }

            if (!empty($oldfield)) {
                execute_sql('ALTER TABLE '. $CFG->prefix . $table .' RENAME COLUMN '. $oldfield .' '. $field);
            } else {
                execute_sql('ALTER TABLE '. $CFG->prefix . $table .' ADD COLUMN '. $field .' '. $type);
            }

            execute_sql('ALTER TABLE '. $CFG->prefix . $table .' ALTER COLUMN '. $field .' SET '. $null);
            return execute_sql('ALTER TABLE '. $CFG->prefix . $table .' ALTER COLUMN '. $field .' SET '. $default);
    }
}

/**
 * This function will load one entire XMLDB file, generating all the needed
 * SQL statements, specific for each RDBMS ($CFG->dbtype) and, finally, it
 * will execute all those statements against the DB.
 *
 * @uses $CFG 
 * @param $file full path to the XML file to be used
 * @return boolean (true on success, false on error)
 */
function install_from_xmldb_file($file) {

    global $CFG, $db;

    $status = true;


    $xmldb_file = new XMLDBFile($file);

    if (!$xmldb_file->fileExists()) {
        return false;
    }

    $loaded = $xmldb_file->loadXMLStructure();
    if (!$loaded || !$xmldb_file->isLoaded()) {
        return false;
    }

    $structure = $xmldb_file->getStructure();

    if (!$sqlarr = $structure->getCreateStructureSQL($CFG->dbtype, $CFG->prefix, false)) {
        return false;
    }

    foreach($sqlarr as $sql) {
        if (!execute_sql($sql)) {
            $status = false;  // Allow to continue to mimic old behaviour
        }                     // perhaps it would be better to exit false
    }

    return $status;
}

?>
