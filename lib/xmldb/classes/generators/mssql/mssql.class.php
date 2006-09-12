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

/// This class generate SQL code to be used against MSSQL
/// It extends XMLDBgenerator so everything can be
/// overriden as needed to generate correct SQL.

class XMLDBmssql extends XMLDBgenerator {

/// Only set values that are different from the defaults present in XMLDBgenerator

    var $statement_end = "\ngo"; // String to be automatically added at the end of each statement

    var $number_type = 'DECIMAL';    // Proper type for NUMBER(x) in this DB

    var $unsigned_allowed = false;    // To define in the generator must handle unsigned information
    var $default_for_char = '';      // To define the default to set for NOT NULLs CHARs without default (null=do nothing)

    var $foreign_keys = false; // Does the generator build foreign keys

    var $primary_index = false;// Does the generator need to build one index for primary keys
    var $unique_index = false;  // Does the generator need to build one index for unique keys
    var $foreign_index = true; // Does the generator need to build one index for foreign keys

    var $sequence_extra_code = false; //Does the generator need to add extra code to generate the sequence fields
    var $sequence_name = 'IDENTITY(1,1)'; //Particular name for inline sequences in this generator
    var $sequence_only = false; //To avoid to output the rest of the field specs, leaving only the name and the sequence_name variable

    var $enum_inline_code = false; //Does the generator need to add inline code in the column definition

    var $add_table_comments  = false;  // Does the generator need to add code for table comments

    var $concat_character = '+'; //Characters to be used as concatenation operator. If not defined
                                  //MySQL CONCAT function will be use

    /**
     * Creates one new XMLDBmssql
     */
    function XMLDBmssql() {
        parent::XMLDBgenerator();
        $this->prefix = '';
        $this->reserved_words = $this->getReservedWords();
    }

    /**
     * Given one XMLDB Type, lenght and decimals, returns the DB proper SQL type
     */
    function getTypeSQL ($xmldb_type, $xmldb_length=null, $xmldb_decimals=null) {

        switch ($xmldb_type) {
            case XMLDB_TYPE_INTEGER:    // From http://msdn.microsoft.com/library/en-us/tsqlref/ts_da-db_7msw.asp?frame=true
                if (empty($xmldb_length)) {
                    $xmldb_length = 10;
                }
                if ($xmldb_length > 9) {
                    $dbtype = 'BIGINT';
                } else if ($xmldb_length > 4) {
                    $dbtype = 'INTEGER';
                } else {
                    $dbtype = 'SMALLINT';
                }
                break;
            case XMLDB_TYPE_NUMBER:
                $dbtype = $this->number_type;
                if (!empty($xmldb_length)) {
                /// 38 is the max allowed
                    if ($xmldb_length > 38) {
                        $xmldb_length = 38;
                    }
                    $dbtype .= '(' . $xmldb_length;
                    if (!empty($xmldb_decimals)) {
                        $dbtype .= ',' . $xmldb_decimals;
                    }
                    $dbtype .= ')';
                }
                break;
            case XMLDB_TYPE_FLOAT:
                $dbtype = 'FLOAT';
                if (!empty($xmldb_decimals)) {
                    if ($xmldb_decimals < 6) {
                        $dbtype = 'REAL';
                    }
                }
                break;
            case XMLDB_TYPE_CHAR:
                $dbtype = 'NVARCHAR';
                if (empty($xmldb_length)) {
                    $xmldb_length='255';
                }
                $dbtype .= '(' . $xmldb_length . ')';
                break;
            case XMLDB_TYPE_TEXT:
                $dbtype = 'NTEXT';
                break;
            case XMLDB_TYPE_BINARY:
                $dbtype = 'IMAGE';
                break;
            case XMLDB_TYPE_DATETIME:
                $dbtype = 'DATETIME';
                break;
        }
        return $dbtype;
    }

    /**         
     * Returns the code needed to create one enum for the xmldb_table and xmldb_field passes
     */     
    function getEnumExtraSQL ($xmldb_table, $xmldb_field) {
        
        $sql = 'CONSTRAINT ' . $this->getNameForObject($xmldb_table->getName(), $xmldb_field->getName(), 'ck');
        $sql.= ' CHECK (' . $this->getEncQuoted($xmldb_field->getName()) . ' IN (' . implode(', ', $xmldb_field->getEnumValues()) . ')),';

        return $sql;
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to drop the field from the table
     * MSSQL overwrites the standard sentence because it needs to do some extra work dropping the default constraints
     */
    function getDropFieldSQL($xmldb_table, $xmldb_field) {

        global $db;

        $results = array();

    /// Get the quoted name of the table and field
        $tablename = $this->getEncQuoted($this->prefix . $xmldb_table->getName());
        $fieldname = $this->getEncQuoted($xmldb_field->getName());
    
    /// Look for any default constraint in this field and drop it
        if ($default = get_record_sql("SELECT id, object_name(cdefault) AS defaultconstraint
                                       FROM syscolumns
                                       WHERE id = object_id('{$tablename}') AND
                                             name = '$fieldname'")) {
            echo "DEFAULT FOUND";
            $results[] = 'ALTER TABLE ' . $tablename . ' DROP CONSTRAINT ' . $default->defaultconstraint;
        }
            echo "DEFAULT NOT FOUND";

    /// Build the standard alter table drop
        $results[] = 'ALTER TABLE ' . $tablename . ' DROP COLUMN ' . $fieldname;

        return $results;
    }

    /**
     * Returns an array of reserved words (lowercase) for this DB
     */
    function getReservedWords() {
    /// This file contains the reserved words for MSSQL databases
    /// from http://msdn2.microsoft.com/en-us/library/ms189822.aspx
        $reserved_words = array (
            'add', 'all', 'alter', 'and', 'any', 'as', 'asc', 'authorization', 
            'avg', 'backup', 'begin', 'between', 'break', 'browse', 'bulk', 
            'by', 'cascade', 'case', 'check', 'checkpoint', 'close', 'clustered', 
            'coalesce', 'collate', 'column', 'commit', 'committed', 'compute', 
            'confirm', 'constraint', 'contains', 'containstable', 'continue', 
            'controlrow', 'convert', 'count', 'create', 'cross', 'current', 
            'current_date', 'current_time', 'current_timestamp', 'current_user', 
            'cursor', 'database', 'dbcc', 'deallocate', 'declare', 'default', 'delete', 
            'deny', 'desc', 'disk', 'distinct', 'distributed', 'double', 'drop', 'dummy', 
            'dump', 'else', 'end', 'errlvl', 'errorexit', 'escape', 'except', 'exec', 
            'execute', 'exists', 'exit', 'fetch', 'file', 'fillfactor', 'floppy', 
            'for', 'foreign', 'freetext', 'freetexttable', 'from', 'full', 'function', 
            'goto', 'grant', 'group', 'having', 'holdlock', 'identity', 'identitycol', 
            'identity_insert', 'if', 'in', 'index', 'inner', 'insert', 'intersect', 'into', 
            'is', 'isolation', 'join', 'key', 'kill', 'left', 'level', 'like', 'lineno', 
            'load', 'max', 'min', 'mirrorexit', 'national', 'nocheck', 'nonclustered', 
            'not', 'null', 'nullif', 'of', 'off', 'offsets', 'on', 'once', 'only', 'open', 
            'opendatasource', 'openquery', 'openrowset', 'openxml', 'option', 'or', 'order', 
            'outer', 'over', 'percent', 'perm', 'permanent', 'pipe', 'plan', 'precision', 
            'prepare', 'primary', 'print', 'privileges', 'proc', 'procedure', 'processexit', 
            'public', 'raiserror', 'read', 'readtext', 'reconfigure', 'references', 
            'repeatable', 'replication', 'restore', 'restrict', 'return', 'revoke', 
            'right', 'rollback', 'rowcount', 'rowguidcol', 'rule', 'save', 'schema', 
            'select', 'serializable', 'session_user', 'set', 'setuser', 'shutdown', 'some', 
            'statistics', 'sum', 'system_user', 'table', 'tape', 'temp', 'temporary', 
            'textsize', 'then', 'to', 'top', 'tran', 'transaction', 'trigger', 'truncate', 
            'tsequal', 'uncommitted', 'union', 'unique', 'update', 'updatetext', 'use', 
            'user', 'values', 'varying', 'view', 'waitfor', 'when', 'where', 'while', 
            'with', 'work', 'writetext'
        );  
        return $reserved_words;
    }
}

?>
