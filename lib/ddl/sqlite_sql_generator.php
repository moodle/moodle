<?php  //$Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas     http://dougiamas.com  //
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

require_once($CFG->libdir.'/ddl/sql_generator.php');

/// This class generate SQL code to be used against SQLite
/// It extends XMLDBgenerator so everything can be
/// overriden as needed to generate correct SQL.

class sqlite_sql_generator extends sql_generator {

/// Only set values that are different from the defaults present in XMLDBgenerator

    public $drop_default_value_required = true; //To specify if the generator must use some DEFAULT clause to drop defaults
    public $drop_default_value = NULL; //The DEFAULT clause required to drop defaults

    public $drop_primary_key = 'ALTER TABLE TABLENAME DROP PRIMARY KEY'; // Template to drop PKs
                // with automatic replace for TABLENAME and KEYNAME

    public $drop_unique_key = 'ALTER TABLE TABLENAME DROP KEY KEYNAME'; // Template to drop UKs
                // with automatic replace for TABLENAME and KEYNAME

    public $drop_foreign_key = 'ALTER TABLE TABLENAME DROP FOREIGN KEY KEYNAME'; // Template to drop FKs
                // with automatic replace for TABLENAME and KEYNAME
    public $default_for_char = '';       // To define the default to set for NOT NULLs CHARs without default (null=do nothing)

    public $sequence_only = true; //To avoid to output the rest of the field specs, leaving only the name and the sequence_name publiciable
    public $sequence_extra_code = false; //Does the generator need to add extra code to generate the sequence fields
    public $sequence_name = 'INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL'; //Particular name for inline sequences in this generator
    public $unsigned_allowed = false;    // To define in the generator must handle unsigned information

    public $enum_extra_code = false; //Does the generator need to add extra code to generate code for the enums in the table

    public $add_after_clause = true; // Does the generator need to add the after clause for fields

    public $concat_character = null; //Characters to be used as concatenation operator. If not defined
                                  //MySQL CONCAT function will be use

    public $alter_column_sql = 'ALTER TABLE TABLENAME MODIFY COLUMN COLUMNSPECS'; //The SQL template to alter columns

    public $drop_index_sql = 'ALTER TABLE TABLENAME DROP INDEX INDEXNAME'; //SQL sentence to drop one index
                                                               //TABLENAME, INDEXNAME are dinamically replaced

    public $rename_index_sql = null; //SQL sentence to rename one index (MySQL doesn't support this!)
                                      //TABLENAME, OLDINDEXNAME, NEWINDEXNAME are dinamically replaced

    public $rename_key_sql = null; //SQL sentence to rename one key (MySQL doesn't support this!)
                                      //TABLENAME, OLDKEYNAME, NEWKEYNAME are dinamically replaced

    /**
     * Creates one new XMLDBmysql
     */
    public function __construct($mdb) {
        parent::__construct($mdb);
    }

    /**
     * Given one correct xmldb_key, returns its specs
     */
    public function getKeySQL($xmldb_table, $xmldb_key) {

        $key = '';

        switch ($xmldb_key->getType()) {
            case XMLDB_KEY_PRIMARY:
                if ($this->primary_keys && count($xmldb_key->getFields())>1) {
                    if ($this->primary_key_name !== null) {
                        $key = $this->getEncQuoted($this->primary_key_name);
                    } else {
                        $key = $this->getNameForObject($xmldb_table->getName(), implode(', ', $xmldb_key->getFields()), 'pk');
                    }
                    $key .= ' PRIMARY KEY (' . implode(', ', $this->getEncQuoted($xmldb_key->getFields())) . ')';
                }
                break;
            case XMLDB_KEY_UNIQUE:
                if ($this->unique_keys) {
                    $key = $this->getNameForObject($xmldb_table->getName(), implode(', ', $xmldb_key->getFields()), 'uk');
                    $key .= ' UNIQUE (' . implode(', ', $this->getEncQuoted($xmldb_key->getFields())) . ')';
                }
                break;
            case XMLDB_KEY_FOREIGN:
            case XMLDB_KEY_FOREIGN_UNIQUE:
                if ($this->foreign_keys) {
                    $key = $this->getNameForObject($xmldb_table->getName(), implode(', ', $xmldb_key->getFields()), 'fk');
                    $key .= ' FOREIGN KEY (' . implode(', ', $this->getEncQuoted($xmldb_key->getFields())) . ')';
                    $key .= ' REFERENCES ' . $this->getEncQuoted($this->prefix . $xmldb_key->getRefTable());
                    $key .= ' (' . implode(', ', $this->getEncQuoted($xmldb_key->getRefFields())) . ')';
                }
                break;
        }

        return $key;
    }

    /**
     * Given one XMLDB Type, lenght and decimals, returns the DB proper SQL type
     */
    public function getTypeSQL($xmldb_type, $xmldb_length=null, $xmldb_decimals=null) {

        switch ($xmldb_type) {
            case XMLDB_TYPE_INTEGER:    // From http://www.sqlite.org/datatype3.html
                if (empty($xmldb_length)) {
                    $xmldb_length = 10;
                }
                $dbtype = 'INTEGER(' . $xmldb_length . ')';
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
                $dbtype = 'REAL';
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
                if (empty($xmldb_length)) {
                    $xmldb_length='255';
                }
                $dbtype .= '(' . $xmldb_length . ')';
                break;
            case XMLDB_TYPE_BINARY:
                $dbtype = 'BLOB';
                break;
            case XMLDB_TYPE_DATETIME:
                $dbtype = 'DATETIME';
            default:
            case XMLDB_TYPE_TEXT:
                $dbtype = 'TEXT';
                break;
        }
        return $dbtype;
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needded to create its enum
     * (usually invoked from getModifyEnumSQL()
     */
    public function getCreateEnumSQL($xmldb_table, $xmldb_field) {
    /// For MySQL, just alter the field
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needded to drop its enum
     * (usually invoked from getModifyEnumSQL()
     */
    public function getDropEnumSQL($xmldb_table, $xmldb_field) {
    /// For MySQL, just alter the field
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needded to create its default
     * (usually invoked from getModifyDefaultSQL()
     */
    public function getCreateDefaultSQL($xmldb_table, $xmldb_field) {
    /// Just a wrapper over the getAlterFieldSQL() function for MySQL that
    /// is capable of handling defaults
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**
     * Given one correct xmldb_field and the new name, returns the SQL statements
     * to rename it (inside one array)
     * SQLite is pretty diferent from the standard to justify this oveloading
     */
    public function getRenameFieldSQL($xmldb_table, $xmldb_field, $newname) {

    // TODO: Add code to rename column

    /// Need a clone of xmldb_field to perform the change leaving original unmodified
        $xmldb_field_clone = clone($xmldb_field);

    /// Change the name of the field to perform the change
        $xmldb_field_clone->setName($xmldb_field_clone->getName() . ' ' . $newname);

        $fieldsql = $this->getFieldSQL($xmldb_field_clone);

        $sql = 'ALTER TABLE ' . $this->getTableName($xmldb_table) . ' CHANGE ' . $fieldsql;

        return array($sql);
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needded to drop its default
     * (usually invoked from getModifyDefaultSQL()
     */
    public function getDropDefaultSQL($xmldb_table, $xmldb_field) {
    /// Just a wrapper over the getAlterFieldSQL() function for MySQL that
    /// is capable of handling defaults
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**
     * Given one XMLDB Field, return its enum SQL
     */
    public function getEnumSQL($xmldb_field) {
        return 'enum';
    }

    /**
     * Returns the code (in array) needed to add one comment to the table
     */
    function getCommentSQL ($xmldb_table) {
        return array();
    }

    /**
     * Given one xmldb_table returns one array with all the check constrainsts
     * in the table (fetched from DB)
     * Optionally the function allows one xmldb_field to be specified in
     * order to return only the check constraints belonging to one field.
     * Each element contains the name of the constraint and its description
     * If no check constraints are found, returns an empty array
     * MySQL doesn't have check constraints in this implementation, but
     * we return them based on the enum fields in the table
     */
    public function getCheckConstraintsFromDB($xmldb_table, $xmldb_field = null) {

        // TODO: add code for constraints
        return array();
    }

    /**
     * Given one object name and it's type (pk, uk, fk, ck, ix, uix, seq, trg)
     * return if such name is currently in use (true) or no (false)
     * (invoked from getNameForObject()
     */
    public function isNameInUse($object_name, $type, $table_name) {
        // TODO: add introspection code
        return false; //No name in use found
    }


    /**
     * Returns an array of reserved words (lowercase) for this DB
     */
    public static function getReservedWords() {
    /// From http://www.sqlite.org/lang_keywords.html
        $reserved_words = array (
            'ADD', 'ALL', 'ALTER', 'AND', 'AS', 'AUTOINCREMENT',
            'BETWEEN', 'BY',
            'CASE', 'CHECK',  'COLLATE', 'COLUMN', 'COMMIT', 'CONSTRAINT', 'CREATE', 'CROSS',
            'DEFAULT', 'DEFERRABLE', 'DELETE', 'DISTINCT', 'DROP',
            'ELSE', 'ESCAPE', 'EXCEPT', 'EXISTS',
            'FOREIGN', 'FROM', 'FULL',
            'GROUP',
            'HAVING',
            'IN', 'INDEX', 'INNER', 'INSERT', 'INTERSECT', 'INTO', 'IS', 'ISNULL',
            'JOIN',
            'LEFT', 'LIMIT',
            'NATURAL', 'NOT', 'NOTNULL', 'NULL',
            'ON', 'OR', 'ORDER', 'OUTER',
            'PRIMARY',
            'REFERENCES', 'REGEXP', 'RIGHT', 'ROLLBACK',
            'SELECT', 'SET',
            'TABLE', 'THEN', 'TO', 'TRANSACTION',
            'UNION', 'UNIQUE', 'UPDATE', 'USING',
            'VALUES',
            'WHEN', 'WHERE',
        );
        return $reserved_words;
    }

    public function addslashes($s) {
        // do not use php addslashes() because it depends on PHP quote settings!
        $s = str_replace("'",  "''", $s);
        return $s;
    }
}
