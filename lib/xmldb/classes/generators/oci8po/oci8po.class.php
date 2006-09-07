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

/// This class generate SQL code to be used against Oracle
/// It extends XMLDBgenerator so everything can be
/// overriden as needed to generate correct SQL.

class XMLDBoci8po extends XMLDBgenerator {

/// Only set values that are different from the defaults present in XMLDBgenerator

    var $statement_end = "\n/"; // String to be automatically added at the end of each statement
                                // Using "/" because the standard ";" isn't good for stored procedures (triggers)

    var $number_type = 'NUMBER';    // Proper type for NUMBER(x) in this DB

    var $unsigned_allowed = false;    // To define in the generator must handle unsigned information
    var $default_for_char = ' ';      // To define the default to set for NOT NULLs CHARs without default (null=do nothing)
                                      // Using this whitespace here because Oracle doesn't distinguish empty and null! :-(

    var $default_after_null = false;  //To decide if the default clause of each field must go after the null clause

    var $foreign_keys = false; // Does the generator build foreign keys

    var $primary_index = false;// Does the generator need to build one index for primary keys
    var $unique_index = false;  // Does the generator need to build one index for unique keys
    var $foreign_index = true; // Does the generator need to build one index for foreign keys

    var $sequence_extra_code = true; //Does the generator need to add extra code to generate the sequence fields
    var $sequence_name = ''; //Particular name for inline sequences in this generator

    var $drop_table_extra_code = true; //Does the generatos need to add code after table drop

    var $enum_inline_code = false; //Does the generator need to add inline code in the column definition

    /**
     * Creates one new XMLDBpostgres7
     */
    function XMLDBoci8po() {
        parent::XMLDBgenerator();
        $this->prefix = '';
        $this->reserved_words = $this->getReservedWords();
    }

    /**
     * Given one XMLDB Type, lenght and decimals, returns the DB proper SQL type
     */
    function getTypeSQL ($xmldb_type, $xmldb_length=null, $xmldb_decimals=null) {

        switch ($xmldb_type) {
            case XMLDB_TYPE_INTEGER:    // From http://www.postgresql.org/docs/7.4/interactive/datatype.html
                if (empty($xmldb_length)) {
                    $xmldb_length = 10;
                }
                $dbtype = 'NUMBER(' .  $xmldb_length . ')';
                break;
            case XMLDB_TYPE_NUMBER:
                $dbtype = $this->number_type;
            /// 38 is the max allowed
                if ($xmldb_length > 38) {
                    $xmldb_length = 38;
                }
                if (!empty($xmldb_length)) {
                    $dbtype .= '(' . $xmldb_length;
                    if (!empty($xmldb_decimals)) {
                        $dbtype .= ',' . $xmldb_decimals;
                    }
                    $dbtype .= ')';
                }
                break;
            case XMLDB_TYPE_FLOAT:
                $dbtype = 'NUMBER';
                break;
            case XMLDB_TYPE_CHAR:
                $dbtype = 'VARCHAR2';
                if (empty($xmldb_length)) {
                    $xmldb_length='255';
                }
                $dbtype .= '(' . $xmldb_length . ')';
                break;
            case XMLDB_TYPE_TEXT:
                $dbtype = 'CLOB';
                break;
            case XMLDB_TYPE_BINARY:
                $dbtype = 'BLOB';
                break;
            case XMLDB_TYPE_DATETIME:
                $dbtype = 'DATE';
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
     * Returns the code needed to create one sequence for the xmldb_table and xmldb_field passes
     */
    function getCreateSequenceSQL ($xmldb_table, $xmldb_field) {

        $sequence_name = $this->getNameForObject($xmldb_table->getName(), $xmldb_field->getName(), 'seq');
        
        $sequence = "CREATE SEQUENCE " . $sequence_name;
        $sequence.= "\n    START WITH 1";
        $sequence.= "\n    INCREMENT BY 1";
        $sequence.= "\n    NOMAXVALUE";

        $trigger_name = $this->getNameForObject($xmldb_table->getName(), $xmldb_field->getName(), 'trg');
 
        $trigger = "CREATE OR REPLACE TRIGGER " . $trigger_name;
        $trigger.= "\n    BEFORE INSERT";
        $trigger.= "\nON " . $this->getEncQuoted($this->prefix . $xmldb_table->getName());
        $trigger.= "\n    FOR EACH ROW";
        $trigger.= "\nBEGIN";
        $trigger.= "\n    IF :new." . $this->getEncQuoted($xmldb_field->getName()) . ' IS NULL THEN';
        $trigger.= "\n        SELECT " . $sequence_name . '.nextval INTO :new.' . $this->getEncQuoted($xmldb_field->getName()) . " FROM dual;";
        $trigger.= "\n    END IF;";
        $trigger.= "\nEND;";
        return array($sequence, $trigger);
    }

    /**
     * Returns the code needed to drop one sequence for the xmldb_table and xmldb_field passed
     * Can, optionally, specify if the underlying trigger will be also dropped
     */
    function getDropSequenceSQL ($xmldb_table, $xmldb_field, $include_trigger=false) {

        $sequence_name = $this->getNameForObject($xmldb_table->getName(), $xmldb_field->getName(), 'seq');
        
        $sequence = "DROP SEQUENCE " . $sequence_name;

        $trigger_name = $this->getNameForObject($xmldb_table->getName(), $xmldb_field->getName(), 'trg');
 
        $trigger = "DROP TRIGGER " . $trigger_name;

        if ($include_trigger) {
            $result =  array($sequence, $trigger);
        } else {
            $result = array($sequence);
        }
        return $result;
    }

    /**
     * Returns the code (in array) needed to add one comment to the table
     */
    function getCommentSQL ($xmldb_table) {

        $comment = "COMMENT ON TABLE " . $this->getEncQuoted($this->prefix . $xmldb_table->getName());
        $comment.= " IS '" . substr($xmldb_table->getComment(), 0, 250) . "'";

        return array($comment);
    }

    /**
     * Returns the code (array of statements) needed to execute extra statements on table drop
     */
    function getDropTableExtraSQL ($xmldb_table) {
        $xmldb_field = new XMLDBField('id'); // Fields having sequences should be exclusively, id.
        return $this->getDropSequenceSQL($xmldb_table, $xmldb_field, false);
    }

    /**
     * Returns an array of reserved words (lowercase) for this DB
     */
    function getReservedWords() {
    /// This file contains the reserved words for Oracle databases
    /// from http://download-uk.oracle.com/docs/cd/B10501_01/server.920/a96540/ap_keywd.htm
        $reserved_words = array (
            'access', 'add', 'all', 'alter', 'and', 'any',
            'as', 'asc', 'audit', 'between', 'by', 'char',
            'check', 'cluster', 'column', 'comment',
            'compress', 'connect', 'create', 'current',
            'date', 'decimal', 'default', 'delete', 'desc',
            'distinct', 'drop', 'else', 'exclusive', 'exists',
            'file', 'float', 'for', 'from', 'grant', 'group',
            'having', 'identified', 'immediate', 'in',
            'increment', 'index', 'initial', 'insert',
            'integer', 'intersect', 'into', 'is', 'level',
            'like', 'lock', 'long', 'maxextents', 'minus',
            'mlslabel', 'mode', 'modify', 'noaudit',
            'nocompress', 'not', 'nowait', 'null', 'number',
            'of', 'offline', 'on', 'online', 'option', 'or',
            'order', 'pctfree', 'prior', 'privileges',
            'public', 'raw', 'rename', 'resource', 'revoke',
            'row', 'rowid', 'rownum', 'rows', 'select',
            'session', 'set', 'share', 'size', 'smallint',
            'start', 'successful', 'synonym', 'sysdate',
            'table', 'then', 'to', 'trigger', 'uid', 'union',
            'unique', 'update', 'user', 'validate', 'values',
            'varchar', 'varchar2', 'view', 'whenever',
            'where', 'with'
        );  
        return $reserved_words;
    }
}

?>
