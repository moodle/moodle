<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas        http://dougiamas.com  //
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

/// This class generate SQL code to be used against MySQL
/// It extends XMLDBgenerator so everything can be
/// overriden as needed to generate correct SQL.

class XMLDBmysql extends XMLDBGenerator {

/// Only set values that are different from the defaults present in XMLDBgenerator

    var $quote_string = '`';   // String used to quote names

    var $default_for_char = '';      // To define the default to set for NOT NULLs CHARs without default (null=do nothing)

    var $drop_default_clause_required = true; //To specify if the generator must use some DEFAULT clause to drop defaults
    var $drop_default_clause = 'NULL'; //The DEFAULT clause required to drop defaults

    var $primary_key_name = ''; //To force primary key names to one string (null=no force)

    var $drop_primary_key = 'ALTER TABLE TABLENAME DROP PRIMARY KEY'; // Template to drop PKs
                // with automatic replace for TABLENAME and KEYNAME

    var $drop_unique_key = 'ALTER TABLE TABLENAME DROP KEY KEYNAME'; // Template to drop UKs
                // with automatic replace for TABLENAME and KEYNAME

    var $drop_foreign_key = 'ALTER TABLE TABLENAME DROP FOREIGN KEY KEYNAME'; // Template to drop FKs
                // with automatic replace for TABLENAME and KEYNAME

    var $sequence_extra_code = false; //Does the generator need to add extra code to generate the sequence fields
    var $sequence_name = 'auto_increment'; //Particular name for inline sequences in this generator

    var $enum_extra_code = false; //Does the generator need to add extra code to generate code for the enums in the table

    var $add_after_clause = true; // Does the generator need to add the after clause for fields

    var $concat_character = null; //Characters to be used as concatenation operator. If not defined
                                  //MySQL CONCAT function will be use

    var $alter_column_sql = 'ALTER TABLE TABLENAME MODIFY COLUMN COLUMNSPECS'; //The SQL template to alter columns

    var $drop_index_sql = 'ALTER TABLE TABLENAME DROP INDEX INDEXNAME'; //SQL sentence to drop one index
                                                               //TABLENAME, INDEXNAME are dinamically replaced

    var $rename_index_sql = null; //SQL sentence to rename one index (MySQL doesn't support this!)
                                      //TABLENAME, OLDINDEXNAME, NEWINDEXNAME are dinamically replaced

    var $rename_key_sql = null; //SQL sentence to rename one key (MySQL doesn't support this!)
                                      //TABLENAME, OLDKEYNAME, NEWKEYNAME are dinamically replaced

    /**
     * Creates one new XMLDBmysql
     */
    function XMLDBmysql() {
        parent::XMLDBGenerator();
        global $CFG;
        $this->prefix = '';
        $this->reserved_words = $this->getReservedWords();
    }

    /**
     * Given one XMLDB Type, lenght and decimals, returns the DB proper SQL type
     */
    function getTypeSQL ($xmldb_type, $xmldb_length=null, $xmldb_decimals=null) {

        switch ($xmldb_type) {
            case XMLDB_TYPE_INTEGER:    // From http://mysql.com/doc/refman/5.0/en/numeric-types.html!
                if (empty($xmldb_length)) {
                    $xmldb_length = 10;
                }
                if ($xmldb_length > 9) {
                    $dbtype = 'BIGINT';
                } else if ($xmldb_length > 6) {
                    $dbtype = 'INT';
                } else if ($xmldb_length > 4) {
                    $dbtype = 'MEDIUMINT';
                } else if ($xmldb_length > 2) {
                    $dbtype = 'SMALLINT';
                } else {
                    $dbtype = 'TINYINT';
                }
                $dbtype .= '(' . $xmldb_length . ')';
                break;
            case XMLDB_TYPE_NUMBER:
                $dbtype = $this->number_type;
                if (!empty($xmldb_length)) {
                    $dbtype .= '(' . $xmldb_length;
                    if (!empty($xmldb_decimals)) {
                        $dbtype .= ',' . $xmldb_decimals;
                    }
                    $dbtype .= ')';
                }
                break;
            case XMLDB_TYPE_FLOAT:
                $dbtype = 'DOUBLE';
                if (!empty($xmldb_decimals)) {
                    if ($xmldb_decimals < 6) {
                        $dbtype = 'FLOAT';
                    }
                }
                if (!empty($xmldb_length)) {
                    $dbtype .= '(' . $xmldb_length;
                    if (!empty($xmldb_decimals)) {
                        $dbtype .= ',' . $xmldb_decimals;
                    } else {
                        $dbtype .= ', 0'; // In MySQL, if length is specified, decimals are mandatory for FLOATs
                    }
                    $dbtype .= ')';
                }
                break;
            case XMLDB_TYPE_CHAR:
                $dbtype = 'VARCHAR';
                if (empty($xmldb_length)) {
                    $xmldb_length='255';
                }
                $dbtype .= '(' . $xmldb_length . ')';
                break;
            case XMLDB_TYPE_TEXT:
                if (empty($xmldb_length)) {
                    $xmldb_length = 'small';
                }
                if ($xmldb_length == 'small') {
                    $dbtype = 'TEXT';
                } else if ($xmldb_length == 'medium') {
                    $dbtype = 'MEDIUMTEXT';
                } else {
                    $dbtype = 'LONGTEXT';
                }
                break;
            case XMLDB_TYPE_BINARY:
                if (empty($xmldb_length)) {
                    $xmldb_length = 'small';
                }
                if ($xmldb_length == 'small') {
                    $dbtype = 'BLOB';
                } else if ($xmldb_length == 'medium') {
                    $dbtype = 'MEDIUMBLOB';
                } else {
                    $dbtype = 'LONGBLOB';
                }
                break;
            case XMLDB_TYPE_DATETIME:
                $dbtype = 'DATETIME';
        }
        return $dbtype;
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to create its enum 
     * (usually invoked from getModifyEnumSQL()
     */
    function getCreateEnumSQL($xmldb_table, $xmldb_field) {
    /// For MySQL, just alter the field
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**     
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to drop its enum 
     * (usually invoked from getModifyEnumSQL()
     */
    function getDropEnumSQL($xmldb_table, $xmldb_field) {
    /// For MySQL, just alter the field
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to create its default 
     * (usually invoked from getModifyDefaultSQL()
     */
    function getCreateDefaultSQL($xmldb_table, $xmldb_field) {
    /// Just a wrapper over the getAlterFieldSQL() function for MySQL that
    /// is capable of handling defaults
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**
     * Given one correct XMLDBField and the new name, returns the SQL statements
     * to rename it (inside one array)
     * MySQL is pretty diferent from the standard to justify this oveloading
     */
    function getRenameFieldSQL($xmldb_table, $xmldb_field, $newname) {

        $results = array();  //Array where all the sentences will be stored

    /// Need a clone of xmldb_field to perform the change leaving original unmodified
        $xmldb_field_clone = clone($xmldb_field);

    /// Change the name of the field to perform the change
        $xmldb_field_clone->setName($xmldb_field_clone->getName() . ' ' . $newname);

        $results[] = 'ALTER TABLE ' . $this->getTableName($xmldb_table) . ' CHANGE ' .
                     $this->getFieldSQL($xmldb_field_clone);

        return $results;
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to drop its default 
     * (usually invoked from getModifyDefaultSQL()
     */
    function getDropDefaultSQL($xmldb_table, $xmldb_field) {
    /// Just a wrapper over the getAlterFieldSQL() function for MySQL that
    /// is capable of handling defaults
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**
     * Given one XMLDB Field, return its enum SQL
     */
    function getEnumSQL ($xmldb_field) {
        return 'enum(' . implode(', ', $xmldb_field->getEnumValues()) . ')';
    }

    /**
     * Returns the code (in array) needed to add one comment to the table
     */
    function getCommentSQL ($xmldb_table) {

        $comment = '';

        if ($xmldb_table->getComment()) {
            $comment .= 'ALTER TABLE ' . $this->getTableName($xmldb_table);
            $comment .= " COMMENT='" . addslashes(substr($xmldb_table->getComment(), 0, 60)) . "'";
        }
        return array($comment);
    }

    /**
     * Given one XMLDBTable returns one array with all the check constrainsts
     * in the table (fetched from DB)
     * Optionally the function allows one xmldb_field to be specified in
     * order to return only the check constraints belonging to one field.
     * Each element contains the name of the constraint and its description
     * If no check constraints are found, returns an empty array
     * MySQL doesn't have check constraints in this implementation, but
     * we return them based on the enum fields in the table
     */
    function getCheckConstraintsFromDB($xmldb_table, $xmldb_field = null) {

        global $db;

        $results = array();

        $tablename = $this->getTableName($xmldb_table);

    /// Fetch all the columns in the table
        if ($columns = $db->MetaColumns($tablename)) {
        /// Normalize array keys
            $columns = array_change_key_case($columns, CASE_LOWER);
        /// Iterate over columns searching for enums
            foreach ($columns as $key => $column) {
            /// Enum found, let's add it to the constraints list
                if (!empty($column->enums)) {
                    $result = new object;
                    $result->name = $key;
                    $result->description = implode(', ', $column->enums);
                    $results[$key] = $result;
                }
            }
        }

    /// Filter by the required field if specified
        if ($xmldb_field) {
            $filter = $xmldb_field->getName();
        /// Check if some of the checks belong to the field (easy under MySQL)
            if (array_key_exists($filter, $results)) {
                $results = array($filter => $results[$filter]);
            } else {
                $results = array();
            }
        }

        return $results;
    }

    /**
     * Given one object name and it's type (pk, uk, fk, ck, ix, uix, seq, trg)
     * return if such name is currently in use (true) or no (false)
     * (invoked from getNameForObject()
     */
    function isNameInUse($object_name, $type, $table_name) {

        global $db;

    /// Calculate the real table name
        $xmldb_table = new XMLDBTable($table_name);
        $tname = $this->getTableName($xmldb_table);
        
        switch($type) {
            case 'ix':
            case 'uix':
            /// First of all, check table exists
                $metatables = $db->MetaTables();
                $metatables = array_flip($metatables);
                $metatables = array_change_key_case($metatables, CASE_LOWER);
                if (array_key_exists($tname,  $metatables)) {
                /// Fetch all the indexes in the table
                    if ($indexes = $db->MetaIndexes($tname)) {
                    /// Normalize array keys
                        $indexes = array_change_key_case($indexes, CASE_LOWER);
                    /// Look for existing index in array
                        if (array_key_exists(strtolower($object_name), $indexes)) {
                            return true;
                        }
                    }
                }
                break;
        }
        return false; //No name in use found
    }


    /**
     * Returns an array of reserved words (lowercase) for this DB
     */
    function getReservedWords() {
    /// This file contains the reserved words for MySQL databases
    /// from http://dev.mysql.com/doc/refman/6.0/en/reserved-words.html
        $reserved_words = array (
            'accessible', 'add', 'all', 'alter', 'analyze', 'and', 'as', 'asc',
            'asensitive', 'before', 'between', 'bigint', 'binary',
            'blob', 'both', 'by', 'call', 'cascade', 'case', 'change',
            'char', 'character', 'check', 'collate', 'column',
            'condition', 'connection', 'constraint', 'continue',
            'convert', 'create', 'cross', 'current_date', 'current_time',
            'current_timestamp', 'current_user', 'cursor', 'database',
            'databases', 'day_hour', 'day_microsecond',
            'day_minute', 'day_second', 'dec', 'decimal', 'declare',
            'default', 'delayed', 'delete', 'desc', 'describe',
            'deterministic', 'distinct', 'distinctrow', 'div', 'double',
            'drop', 'dual', 'each', 'else', 'elseif', 'enclosed', 'escaped',
            'exists', 'exit', 'explain', 'false', 'fetch', 'float', 'float4',
            'float8', 'for', 'force', 'foreign', 'from', 'fulltext', 'grant',
            'group', 'having', 'high_priority', 'hour_microsecond',
            'hour_minute', 'hour_second', 'if', 'ignore', 'in', 'index',
            'infile', 'inner', 'inout', 'insensitive', 'insert', 'int', 'int1',
            'int2', 'int3', 'int4', 'int8', 'integer', 'interval', 'into', 'is',
            'iterate', 'join', 'key', 'keys', 'kill', 'leading', 'leave', 'left',
            'like', 'limit', 'linear', 'lines', 'load', 'localtime', 'localtimestamp',
            'lock', 'long', 'longblob', 'longtext', 'loop', 'low_priority', 'master_heartbeat_period',
            'master_ssl_verify_server_cert', 'match', 'mediumblob', 'mediumint', 'mediumtext',
            'middleint', 'minute_microsecond', 'minute_second',
            'mod', 'modifies', 'natural', 'not', 'no_write_to_binlog',
            'null', 'numeric', 'on', 'optimize', 'option', 'optionally',
            'or', 'order', 'out', 'outer', 'outfile', 'overwrite', 'precision', 'primary',
            'procedure', 'purge', 'raid0', 'range', 'read', 'read_only', 'read_write', 'reads', 'real',
            'references', 'regexp', 'release', 'rename', 'repeat', 'replace',
            'require', 'restrict', 'return', 'revoke', 'right', 'rlike', 'schema',
            'schemas', 'second_microsecond', 'select', 'sensitive',
            'separator', 'set', 'show', 'smallint', 'soname', 'spatial',
            'specific', 'sql', 'sqlexception', 'sqlstate', 'sqlwarning',
            'sql_big_result', 'sql_calc_found_rows', 'sql_small_result',
            'ssl', 'starting', 'straight_join', 'table', 'terminated', 'then',
            'tinyblob', 'tinyint', 'tinytext', 'to', 'trailing', 'trigger', 'true',
            'undo', 'union', 'unique', 'unlock', 'unsigned', 'update',
            'upgrade', 'usage', 'use', 'using', 'utc_date', 'utc_time',
            'utc_timestamp', 'values', 'varbinary', 'varchar', 'varcharacter',
            'varying', 'when', 'where', 'while', 'with', 'write', 'x509',
            'xor', 'year_month', 'zerofill'
        );
        return $reserved_words;
    }
}

?>
