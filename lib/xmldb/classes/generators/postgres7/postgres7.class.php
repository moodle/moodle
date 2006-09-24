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

/// This class generate SQL code to be used against PostgreSQL
/// It extends XMLDBgenerator so everything can be
/// overriden as needed to generate correct SQL.

class XMLDBpostgres7 extends XMLDBgenerator {

/// Only set values that are different from the defaults present in XMLDBgenerator

    var $number_type = 'NUMERIC';    // Proper type for NUMBER(x) in this DB

    var $unsigned_allowed = false;    // To define in the generator must handle unsigned information
    var $default_for_char = '';      // To define the default to set for NOT NULLs CHARs without default (null=do nothing)

    var $unique_keys = false; // Does the generator build unique keys
    var $foreign_keys = false; // Does the generator build foreign keys

    var $primary_index = false;// Does the generator need to build one index for primary keys
    var $unique_index = true;  // Does the generator need to build one index for unique keys
    var $foreign_index = true; // Does the generator need to build one index for foreign keys

    var $sequence_extra_code = false; //Does the generator need to add extra code to generate the sequence fields
    var $sequence_name = 'BIGSERIAL'; //Particular name for inline sequences in this generator
    var $sequence_only = true; //To avoid to output the rest of the field specs, leaving only the name and the sequence_name variable

    var $enum_inline_code = false; //Does the generator need to add inline code in the column definition

    /**
     * Creates one new XMLDBpostgres7
     */
    function XMLDBpostgres7() {
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
                    $dbtype .= '(' . $xmldb_length;
                    if (!empty($xmldb_decimals)) {
                        $dbtype .= ',' . $xmldb_decimals;
                    }
                    $dbtype .= ')';
                }
                break;
            case XMLDB_TYPE_FLOAT:
                $dbtype = 'DOUBLE PRECISION';
                if (!empty($xmldb_decimals)) {
                    if ($xmldb_decimals < 6) {
                        $dbtype = 'REAL';
                    }
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
                $dbtype = 'TEXT';
                break;
            case XMLDB_TYPE_BINARY:
                $dbtype = 'BYTEA';
                break;
            case XMLDB_TYPE_DATETIME:
                $dbtype = 'TIMESTAMP';
                break;
        }
        return $dbtype;
    }

    /**
     * Returns the code needed to create one enum for the xmldb_table and xmldb_field passes
     */
    function getEnumExtraSQL ($xmldb_table, $xmldb_field) {

        $sql = 'CONSTRAINT ' . $this->getNameForObject($xmldb_table->getName(), $xmldb_field->getName(), 'ck');
        $sql.= ' CHECK (' . $this->getEncQuoted($xmldb_field->getName()) . ' IN (' . implode(', ', $xmldb_field->getEnumValues()) . '))';

        return $sql;
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
      * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to alter the field in the table
      * PostgreSQL has some severe limits:
      *     - Any change of type or precision requires a new temporary column to be created, values to
      *       be transfered potentially casting them, to apply defaults if the column is not null and 
      *       finally, to rename it
      *     - Changes in null/not null require the SET/DROP NOT NULL clause
      *     - Changes in default require the SET/DROP DEFAULT clause
      */
     function getAlterFieldSQL($xmldb_table, $xmldb_field) {

         global $db;

     /// Get the quoted name of the table and field
         $tablename = $this->getEncQuoted($this->prefix . $xmldb_table->getName());
         $fieldname = $this->getEncQuoted($xmldb_field->getName());

     /// Take a look to field metadata
         $meta = array_change_key_case($db->MetaColumns($tablename));
         $metac = $meta[$fieldname];
         print_object($metac);
         $oldtype = strtolower($metac->type);
         $oldlength = $metac->max_length;
         $olddecimals = empty($metac->scale) ? null : $metac->scale;
         $oldnotnull = empty($metac->not_null) ? false : $metac->not_null;
         $olddefault = empty($metac->default_value) ? null : $metac->default_value;
                                                                                                                                   /// If field is CLOB and new one is also XMLDB_TYPE_TEXT or 
     /// if fiels is BLOB and new one is also XMLDB_TYPE_BINARY
     /// prevent type to be specified, so only NULL and DEFAULT clauses are allowed
         if (($oldtype = 'clob' && $xmldb_field->getType() == XMLDB_TYPE_TEXT) ||
             ($oldtype = 'blob' && $xmldb_field->getType() == XMLDB_TYPE_BINARY)) {
             $this->alter_column_skip_type = true;
             $islob = true;
         }

     /// If field is NOT NULL and the new one too or
     /// if field is NULL and the new one too
     /// prevent null clause to be specified
         if (($oldnotnull && $xmldb_field->getNotnull()) ||
             (!$oldnotnull && !$xmldb_field->getNotnull())) {
             $this->alter_column_skip_notnull = true;
         }

     /// In the rest of cases, use the general generator
         return parent::getAlterFieldSQL($xmldb_table, $xmldb_field);
     }

    /**
     * Returns an array of reserved words (lowercase) for this DB
     */
    function getReservedWords() {
    /// This file contains the reserved words for PostgreSQL databases
    /// http://www.postgresql.org/docs/current/static/sql-keywords-appendix.html
        $reserved_words = array (
            'all', 'analyse', 'analyze', 'and', 'any', 'array', 'as', 'asc',
            'asymmetric', 'authorization', 'between', 'binary', 'both', 'case',
            'cast', 'check', 'collate', 'column', 'constraint', 'create', 'cross',
            'current_date', 'current_role', 'current_time', 'current_timestamp',
            'current_user', 'default', 'deferrable', 'desc', 'distinct', 'do',
            'else', 'end', 'except', 'false', 'for', 'foreign', 'freeze', 'from',
            'full', 'grant', 'group', 'having', 'ilike', 'in', 'initially', 'inner',
            'intersect', 'into', 'is', 'isnull', 'join', 'leading', 'left', 'like',
            'limit', 'localtime', 'localtimestamp', 'natural', 'new', 'not',
            'notnull', 'null', 'off', 'offset', 'old', 'on', 'only', 'or', 'order',
            'outer', 'overlaps', 'placing', 'primary', 'references', 'right', 'select',
            'session_user', 'similar', 'some', 'symmetric', 'table', 'then', 'to',
            'trailing', 'true', 'union', 'unique', 'user', 'using', 'verbose',
            'when', 'where'
        );
        return $reserved_words;
    }
}

?>
