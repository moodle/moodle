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

/// This class represent the base generator class where all the
/// needed functions to generate proper SQL are defined. 

/// If fact, this class generate SQL code to be used against MySQL
/// so the rest of classes will inherit, by default, the same logic.
/// Functions will be overriden as needed to generate correct SQL.

class XMLDBmysql {

    var $quote_string = '`';   // String used to quote names
    var $quote_all    = false; // To decide if we want to quote all the names or only the reserved ones

    var $integer_to_number = false;  // To create all the integers as NUMBER(x) (also called DECIMAL, NUMERIC...)
    var $float_to_number   = false;  // To create all the floats as NUMBER(x) (also called DECIMAL, NUMERIC...)
    var $number_type = 'NUMERIC';    // Proper type for NUMBER(x) in this DB

    var $primary_keys = true;  // Does the constructor build primary keys
    var $unique_keys = false;  // Does the constructor build unique keys
    var $foreign_keys = false; // Does the constructor build foreign keys

    var $primary_index = false;// Does the constructor need to build one index for primary keys
    var $unique_index = true;  // Does the constructor need to build one index for unique keys
    var $foreign_index = true; // Does the constructor need to build one index for foreign keys

    var $prefix;         // Prefix to be used for all the DB objects

    var $reserved_words; // List of reserved words (in order to quote them properly)

 
    /**
     * Creates one new XMLDBmysql
     */
    function XMLDBmysql() {
        global $CFG;
        $this->prefix = '';
        $this->reserved_words = $this->getReservedWords();
    }

    /**
     * Set the prefix
     */
    function setPrefix($prefix) {
        $this->prefix = $prefix;
    }

    /**
     * Given one XMLDB Type, lenght and decimals, returns the DB proper SQL type
     */
    function getType ($xmldb_type, $xmldb_length=null, $xmldb_decimals=null) {

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
                    $dbtype = 'TINTINT';
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
                $dbtype = 'FLOAT';
                if (!empty($xmldb_length)) {
                    $dbtype .= '(' . $xmldb_length;
                    if (!empty($xmldb_decimals)) {
                        $dbtype .= ',' . $xmldb_decimals;
                    }
                    $dbtype .= ')';
                }
                break;
            case XMLDB_TYPE_CHAR:
                $dbtype = 'VARCHAR';
                if (!empty($xmldb_length)) {
                    $xmldb_length='255';
                }
                $dbtype .= '(' . $xmldb_length . ')';
                break;
            case XMLDB_TYPE_TEXT:
                if (empty($xmldb_length)) {
                    $xmldb_length = 'small';
                }
                if ($xmldb_length = 'small') {
                    $dbtype = 'TEXT';
                } else if ($xmldb_length = 'medium') {
                    $dbtype = 'MEDIUMTEXT';
                } else {
                    $dbtype = 'BIGTEXT';
                }
                break;
            case XMLDB_TYPE_BINARY:
                if (empty($xmldb_length)) {
                    $xmldb_length = 'small';
                }
                if ($xmldb_length = 'small') {
                    $dbtype = 'BLOB';
                } else if ($xmldb_length = 'medium') {
                    $dbtype = 'MEDIUMBLOB';
                } else {
                    $dbtype = 'BIGBLOB';
                }
                break;
            case XMLDB_TYPE_DATETIME:
                $dbtype = 'DATETIME';
        }
        return $dbtype;
    }

    /**
     * Given one correct XMLDBTable, returns the complete SQL lines to create it
     */
    function getCreateTableSQL($xmldb_table) {

    /// Table header
        $table = 'CREATE TABLE ' . $this->getEncQuoted($this->prefix . $xmldb_table->getName()) . ' (';

        if (!$xmldb_fields = $xmldb_table->getFields()) {
            return false;
        }
    /// Add the fields, separated by commas
        foreach ($xmldb_fields as $xmldb_field) {
            $table .= "\n    " . $this->getCreateFieldSQL($xmldb_field) . ',';
        }
    /// Table footer, trim the latest comma
        $table = trim($table,',');
        $table .= "\n)";
        if ($xmldb_table->getComment()) {
            $table .= " COMMENT='" . $xmldb_table->getComment() . "';\n\n";
        }
        return $table;
    }

    /**
     * Given one correct XMLDBField, returns the complete SQL line to create it
     */
    function getCreateFieldSQL($xmldb_field) {

    /// First of all, convert integers to numbers if defined
        if ($this->integer_to_number) {
            if ($xmldb_field->getType() == XMLDB_TYPE_INTEGER) {
                $xmldb_field->setType(XMLDB_TYPE_NUMBER);
            }
        }
    /// Same for floats
        if ($this->float_to_number) {
            if ($xmldb_field->getType() == XMLDB_TYPE_FLOAT) {
                $xmldb_field->setType(XMLDB_TYPE_NUMBER);
            }
        }

    /// The name
        $field = $this->getEncQuoted($xmldb_field->getName());
    /// The type and length (if the field isn't enum)
        if (!$xmldb_field->getEnum()) {
            $field .= ' ' . $this->getType($xmldb_field->getType(), $xmldb_field->getLength(), $xmldb_field->getDecimals());
        } else {
        /// If enum, do it with its values
            $field .= ' enum(' . implode(', ', $xmldb_field->getEnumValues()) . ')';
        }
    /// The unsigned
        if ($xmldb_field->getType() == XMLDB_TYPE_INTEGER ||
            $xmldb_field->getType() == XMLDB_TYPE_NUMBER ||
            $xmldb_field->getType() == XMLDB_TYPE_FLOAT) {
            if ($xmldb_field->getUnsigned()) {
                $field .= ' unsigned';
            }
        }
    /// The not null
        if ($xmldb_field->getNotNull()) {
            $field .= ' NOT NULL';
        }
    /// The sequence
        if ($xmldb_field->getSequence()) {
            $field .= ' auto_increment';
        }
    /// The default
        if ($xmldb_field->getDefault() != NULL) {
            $field .= ' default ';
            if ($xmldb_field->getType() == XMLDB_TYPE_CHAR ||
                $xmldb_field->getType() == XMLDB_TYPE_TEXT) {
                    $field .= "'" . $xmldb_field->getDefault() . "'";
            } else {
                $field .= $xmldb_field->getDefault();
            }
        } else {
        /// We force default '' for not null char columns without proper default
        /// some day this should be out!
            if ($xmldb_field->getType() == XMLDB_TYPE_CHAR &&
                $xmldb_field->getNotNull()) {
                $field .= ' default ' . "''";
            }
        }
        return $field;
    }

    /**
     * Given any string, enclose it by the proper quotes
     */
    function getEncQuoted($string) {

    /// Always lowercase
        $string = strtolower($string);
    /// if reserved or quote_all, quote it
        if ($this->quote_all || in_array($string, $this->reserved_words)) {
            $string = $this->quote_string . $string . $this->quote_string;
        }
        return $string;
    }

    /**
     * Returns an array of reserved words (lowercase) for this DB
     */
    function getReservedWords() {
    /// This file contains the reserved words for MySQL databases
    /// from http://dev.mysql.com/doc/refman/5.0/en/reserved-words.html
        $reserved_words = array (
            'add', 'all', 'alter', 'analyze', 'and', 'as', 'asc',
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
            'like', 'limit', 'lines', 'load', 'localtime', 'localtimestamp',
            'lock', 'long', 'longblob', 'longtext', 'loop', 'low_priority',
            'match', 'mediumblob', 'mediumint', 'mediumtext',
            'middleint', 'minute_microsecond', 'minute_second',
            'mod', 'modifies', 'natural', 'not', 'no_write_to_binlog',
            'null', 'numeric', 'on', 'optimize', 'option', 'optionally',
            'or', 'order', 'out', 'outer', 'outfile', 'precision', 'primary',
            'procedure', 'purge', 'raid0', 'read', 'reads', 'real',
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
