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

/// This class represent the base generator class where all the
/// needed functions to generate proper SQL are defined.

/// The rest of classes will inherit, by default, the same logic.
/// Functions will be overriden as needed to generate correct SQL.

class XMLDBgenerator {

/// Please, avoid editing this defaults in this base class!
/// It could change the behaviour of the rest of generators
/// that, by default, inherit this configuration.
/// To change any of them, do it in extended classes instead.

    var $quote_string = '"';   // String used to quote names

    var $quote_all    = false; // To decide if we want to quote all the names or only the reserved ones

    var $statement_end = ';'; // String to be automatically added at the end of each statement

    var $integer_to_number = false;  // To create all the integers as NUMBER(x) (also called DECIMAL, NUMERIC...)
    var $float_to_number   = false;  // To create all the floats as NUMBER(x) (also called DECIMAL, NUMERIC...)

    var $number_type = 'NUMERIC';    // Proper type for NUMBER(x) in this DB

    var $unsigned_allowed = true;    // To define in the generator must handle unsigned information
    var $default_for_char = null;      // To define the default to set for NOT NULLs CHARs without default (null=do nothing)

    var $drop_default_clause_required = false; //To specify if the generator must use some DEFAULT clause to drop defaults
    var $drop_default_clause = ''; //The DEFAULT clause required to drop defaults

    var $default_after_null = true;  //To decide if the default clause of each field must go after the null clause

    var $specify_nulls = false;  //To force the generator if NULL clauses must be specified. It shouldn't be necessary
                                 //but some mssql drivers require them or everything is created as NOT NULL :-(

    var $primary_key_name = null; //To force primary key names to one string (null=no force)

    var $primary_keys = true;  // Does the generator build primary keys
    var $unique_keys = false;  // Does the generator build unique keys
    var $foreign_keys = false; // Does the generator build foreign keys

    var $drop_primary_key = 'ALTER TABLE TABLENAME DROP CONSTRAINT KEYNAME'; // Template to drop PKs
                               // with automatic replace for TABLENAME and KEYNAME

    var $drop_unique_key = 'ALTER TABLE TABLENAME DROP CONSTRAINT KEYNAME'; // Template to drop UKs
                               // with automatic replace for TABLENAME and KEYNAME

    var $drop_foreign_key = 'ALTER TABLE TABLENAME DROP CONSTRAINT KEYNAME'; // Template to drop FKs
                               // with automatic replace for TABLENAME and KEYNAME

    var $sequence_extra_code = true; //Does the generator need to add extra code to generate the sequence fields
    var $sequence_name = 'auto_increment'; //Particular name for inline sequences in this generator
    var $sequence_name_small = false; //Different name for small (4byte) sequences or false if same
    var $sequence_only = false; //To avoid to output the rest of the field specs, leaving only the name and the sequence_name variable

    var $enum_inline_code = true; //Does the generator need to add inline code in the column definition
    var $enum_extra_code = true; //Does the generator need to add extra code to generate code for the enums in the table

    var $add_table_comments  = true;  // Does the generator need to add code for table comments

    var $add_after_clause = false; // Does the generator need to add the after clause for fields

    var $prefix_on_names = true; //Does the generator need to prepend the prefix to all the key/index/sequence/trigger/check names

    var $names_max_length = 30; //Max length for key/index/sequence/trigger/check names (keep 30 for all!)

    var $concat_character = '||'; //Characters to be used as concatenation operator. If not defined
                                  //MySQL CONCAT function will be used

    var $rename_table_sql = 'ALTER TABLE OLDNAME RENAME TO NEWNAME'; //SQL sentence to rename one table, both
                                  //OLDNAME and NEWNAME are dinamically replaced

    var $rename_table_extra_code = false; //Does the generator need to add code after table rename

    var $drop_table_sql = 'DROP TABLE TABLENAME'; //SQL sentence to drop one table
                                  //TABLENAME is dinamically replaced

    var $drop_table_extra_code = false; //Does the generator need to add code after table drop

    var $alter_column_sql = 'ALTER TABLE TABLENAME ALTER COLUMN COLUMNSPECS'; //The SQL template to alter columns

    var $alter_column_skip_default = false; //The generator will skip the default clause on alter columns

    var $alter_column_skip_type = false; //The generator will skip the type clause on alter columns

    var $alter_column_skip_notnull = false; //The generator will skip the null/notnull clause on alter columns

    var $rename_column_sql = 'ALTER TABLE TABLENAME RENAME COLUMN OLDFIELDNAME TO NEWFIELDNAME';
                                  ///TABLENAME, OLDFIELDNAME and NEWFIELDNAME are dianmically replaced

    var $rename_column_extra_code = false; //Does the generator need to add code after column rename

    var $drop_index_sql = 'DROP INDEX INDEXNAME'; //SQL sentence to drop one index
                                  //TABLENAME, INDEXNAME are dinamically replaced

    var $rename_index_sql = 'ALTER INDEX OLDINDEXNAME RENAME TO NEWINDEXNAME'; //SQL sentence to rename one index
                                  //TABLENAME, OLDINDEXNAME, NEWINDEXNAME are dinamically replaced

    var $rename_key_sql = 'ALTER TABLE TABLENAME CONSTRAINT OLDKEYNAME RENAME TO NEWKEYNAME'; //SQL sentence to rename one key
                                  //TABLENAME, OLDKEYNAME, NEWKEYNAME are dinamically replaced

    var $prefix;         // Prefix to be used for all the DB objects

    var $reserved_words; // List of reserved words (in order to quote them properly)

    /**
     * Creates one new XMLDBGenerator
     */
    function XMLDBgenerator() {
        global $CFG;
        $this->prefix = '';
        $this->reserved_words = $this->getReservedWords();
    }

/// ALL THESE FUNCTION ARE SHARED BY ALL THE XMLDGenerator classes

    /**
     * Set the prefix
     */
    function setPrefix($prefix) {
        if ($this->prefix_on_names) { // Only if we want prefix on names
            $this->prefix = $prefix;
        }
    }

    /**
     * Given one XMLDBTable, returns it's correct name, depending of all the parametrization
     *
     * @param XMLDBTable table whose name we want
     * @param boolean to specify if the name must be quoted (if reserved word, only!)
     * @return string the correct name of the table
     */
    function getTableName($xmldb_table, $quoted = true) {

        $prefixtouse = $this->prefix;
    /// Determinate if this table must have prefix or no
        if (in_array($xmldb_table->getName(), $this->getTablesWithoutPrefix())) {
            $prefixtouse = '';
        }
    /// Get the name
        $tablename = $prefixtouse . $xmldb_table->getName();
    /// Apply quotes conditionally
        if ($quoted) {
            $tablename = $this->getEncQuoted($tablename);
        }

        return $tablename;
    }

    /**
     * Given one correct XMLDBTable, returns the SQL statements
     * to create it (inside one array)
     */
    function getCreateTableSQL($xmldb_table) {

        $results = array();  //Array where all the sentences will be stored

    /// Table header
        $table = 'CREATE TABLE ' . $this->getTableName($xmldb_table) . ' (';

        if (!$xmldb_fields = $xmldb_table->getFields()) {
            return $results;
        }

    /// Prevent tables without prefix to be duplicated (part of MDL-6614)
        if (in_array($xmldb_table->getName(), $this->getTablesWithoutPrefix()) &&
            table_exists($xmldb_table)) {
            return $results; // false here would break the install, empty array is better ;-)
        }

    /// Add the fields, separated by commas
        foreach ($xmldb_fields as $xmldb_field) {
            $table .= "\n    " . $this->getFieldSQL($xmldb_field);
            $table .= ',';
        }
    /// Add the keys, separated by commas
        if ($xmldb_keys = $xmldb_table->getKeys()) {
            foreach ($xmldb_keys as $xmldb_key) {
                if ($keytext = $this->getKeySQL($xmldb_table, $xmldb_key)) {
                    $table .= "\nCONSTRAINT " . $keytext . ',';
                }
            /// If the key is XMLDB_KEY_FOREIGN_UNIQUE, create it as UNIQUE too
                if ($xmldb_key->getType() == XMLDB_KEY_FOREIGN_UNIQUE) {
                ///Duplicate the key
                    $xmldb_key->setType(XMLDB_KEY_UNIQUE);
                    if ($keytext = $this->getKeySQL($xmldb_table, $xmldb_key)) {
                        $table .= "\nCONSTRAINT " . $keytext . ',';
                    }
                }
            }
        }
    /// Add enum extra code if needed
        if ($this->enum_extra_code) {
        /// Iterate over fields looking for enums
            foreach ($xmldb_fields as $xmldb_field) {
                if ($xmldb_field->getEnum()) {
                    $table .= "\n" . $this->getEnumExtraSQL($xmldb_table, $xmldb_field) . ',';
                }
            }
        }
    /// Table footer, trim the latest comma
        $table = trim($table,',');
        $table .= "\n)";

    /// Add the CREATE TABLE to results
        $results[] = $table;

    /// Add comments if specified and it exists
        if ($this->add_table_comments && $xmldb_table->getComment()) {
            $comment = $this->getCommentSQL ($xmldb_table);
        /// Add the COMMENT to results
            $results = array_merge($results, $comment);
        }

    /// Add the indexes (each one, one statement)
        if ($xmldb_indexes = $xmldb_table->getIndexes()) {
            foreach ($xmldb_indexes as $xmldb_index) {
            ///Only process all this if the index doesn't exist in DB
                if (!index_exists($xmldb_table, $xmldb_index)) {
                    if ($indextext = $this->getCreateIndexSQL($xmldb_table, $xmldb_index)) {
                        $results = array_merge($results, $indextext);
                    }
                }
            }
        }

    /// Also, add the indexes needed from keys, based on configuration (each one, one statement)
        if ($xmldb_keys = $xmldb_table->getKeys()) {
            foreach ($xmldb_keys as $xmldb_key) {
            /// If we aren't creating the keys OR if the key is XMLDB_KEY_FOREIGN (not underlying index generated 
            /// automatically by the RDBMS) create the underlying (created by us) index (if doesn't exists)
                if (!$this->getKeySQL($xmldb_table, $xmldb_key) || $xmldb_key->getType() == XMLDB_KEY_FOREIGN) {
                /// Create the interim index   
                    $index = new XMLDBIndex('anyname');
                    $index->setFields($xmldb_key->getFields());
                ///Only process all this if the index doesn't exist in DB
                    if (!index_exists($xmldb_table, $index)) {
                        $createindex = false; //By default
                        switch ($xmldb_key->getType()) {
                            case XMLDB_KEY_UNIQUE:
                            case XMLDB_KEY_FOREIGN_UNIQUE:
                                $index->setUnique(true);
                                $createindex = true;
                                break;
                            case XMLDB_KEY_FOREIGN:
                                $index->setUnique(false);
                                $createindex = true;
                                break;
                        }
                        if ($createindex) {
                            if ($indextext = $this->getCreateIndexSQL($xmldb_table, $index)) {
                            /// Add the INDEX to the array
                                $results = array_merge($results, $indextext);
                            }
                        }
                    }
                }
            }
        }

    /// Add sequence extra code if needed
        if ($this->sequence_extra_code) {
        /// Iterate over fields looking for sequences
            foreach ($xmldb_fields as $xmldb_field) {
                if ($xmldb_field->getSequence()) {
                /// returns an array of statements needed to create one sequence
                    $sequence_sentences = $this->getCreateSequenceSQL($xmldb_table, $xmldb_field);
                /// Add the SEQUENCE to the array
                    $results = array_merge($results, $sequence_sentences);
                }
            }
        }

        return $results;
    }

    /**
     * Given one correct XMLDBIndex, returns the SQL statements
     * needed to create it (in array)
     */
    function getCreateIndexSQL ($xmldb_table, $xmldb_index) {

        $unique = '';
        $suffix = 'ix';
        if ($xmldb_index->getUnique()) {
            $unique = ' UNIQUE';
            $suffix = 'uix';
        }

        $index = 'CREATE' . $unique . ' INDEX ';
        $index .= $this->getNameForObject($xmldb_table->getName(), implode(', ', $xmldb_index->getFields()), $suffix);
        $index .= ' ON ' . $this->getTableName($xmldb_table);
        $index .= ' (' . implode(', ', $this->getEncQuoted($xmldb_index->getFields())) . ')';

        return array($index);
    }

    /**
     * Given one correct XMLDBField, returns the complete SQL line to create it
     */
    function getFieldSQL($xmldb_field, $skip_type_clause = false, $skip_default_clause = false, $skip_notnull_clause = false)  {

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
    /// The type and length only if we don't want to skip it
        if (!$skip_type_clause) {
        /// The type and length (if the field isn't enum)
            if (!$xmldb_field->getEnum() || $this->enum_inline_code == false) {
                $field .= ' ' . $this->getTypeSQL($xmldb_field->getType(), $xmldb_field->getLength(), $xmldb_field->getDecimals());
            } else {
            /// call to custom function
                $field .= ' ' . $this->getEnumSQL($xmldb_field);
            }
        }
    /// The unsigned if supported
        if ($this->unsigned_allowed && ($xmldb_field->getType() == XMLDB_TYPE_INTEGER ||
                                      $xmldb_field->getType() == XMLDB_TYPE_NUMBER ||
                                      $xmldb_field->getType() == XMLDB_TYPE_FLOAT)) {
            if ($xmldb_field->getUnsigned()) {
                $field .= ' unsigned';
            }
        }
    /// Calculate the not null clause
        $notnull = '';
    /// Only if we don't want to skip it
        if (!$skip_notnull_clause) {
            if ($xmldb_field->getNotNull()) {
                $notnull = ' NOT NULL';
            } else {
                if ($this->specify_nulls) {
                    $notnull = ' NULL';
                }
            }
        }
    /// Calculate the default clause
        if (!$skip_default_clause) { //Only if we don't want to skip it
            $default = $this->getDefaultClause($xmldb_field);
        } else {
            $default = '';
        }
    /// Based on default_after_null, set both clauses properly
        if ($this->default_after_null) {
            $field .= $notnull . $default;
        } else {
            $field .= $default . $notnull;
        }
    /// The sequence
        if ($xmldb_field->getSequence()) {
            if($xmldb_field->getLength()<=9 && $this->sequence_name_small) {
                $sequencename=$this->sequence_name_small;
            } else {
                $sequencename=$this->sequence_name;
            }
            $field .= ' ' . $sequencename;
            if ($this->sequence_only) {
            /// We only want the field name and sequence name to be printed
            /// so, calculate it and return
                return $this->getEncQuoted($xmldb_field->getName()) . ' ' . $sequencename;
            }
        }
        return $field;
    }

    /**
     * Given one correct XMLDBKey, returns its specs
     */
    function getKeySQL ($xmldb_table, $xmldb_key) {

        $key = '';

        switch ($xmldb_key->getType()) {
            case XMLDB_KEY_PRIMARY:
                if ($this->primary_keys) {
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
     * Give one XMLDBField, returns the correct "default value" for the current configuration
     */
    function getDefaultValue ($xmldb_field) {

        $default = null;

        if ($xmldb_field->getDefault() !== NULL) {
            if ($xmldb_field->getType() == XMLDB_TYPE_CHAR ||
                $xmldb_field->getType() == XMLDB_TYPE_TEXT) {
                    $default = "'" . addslashes($xmldb_field->getDefault()) . "'";
            } else {
                $default = $xmldb_field->getDefault();
            }
        } else {
        /// We force default '' for not null char columns without proper default
        /// some day this should be out!
            if ($this->default_for_char !== NULL &&
                $xmldb_field->getType() == XMLDB_TYPE_CHAR &&
                $xmldb_field->getNotNull()) {
                $default = "'" . $this->default_for_char . "'";
            } else {
            /// If the DB requires to explicity define some clause to drop one default, do it here
            /// never applying defaults to TEXT and BINARY fields
                if ($this->drop_default_clause_required &&
                    $xmldb_field->getType() != XMLDB_TYPE_TEXT &&
                    $xmldb_field->getType() != XMLDB_TYPE_BINARY && !$xmldb_field->getNotNull()) {
                    $default = $this->drop_default_clause;
                }
            }
        }
        return $default;
    }

    /**
     * Given one XMLDBField, returns the correct "default clause" for the current configuration
     */
    function getDefaultClause ($xmldb_field) {

        $defaultvalue = $this->getDefaultValue ($xmldb_field);

        if ($defaultvalue !== null) {
            return ' DEFAULT ' . $defaultvalue;
        } else {
            return null;
        }
    }

    /**
     * Given one correct XMLDBTable and the new name, returns the SQL statements
     * to rename it (inside one array)
     */
    function getRenameTableSQL($xmldb_table, $newname) {

        $results = array();  //Array where all the sentences will be stored

        $newt = new XMLDBTable($newname); //Temporal table for name calculations

        $rename = str_replace('OLDNAME', $this->getTableName($xmldb_table), $this->rename_table_sql);
        $rename = str_replace('NEWNAME', $this->getTableName($newt), $rename);

        $results[] = $rename;

    /// Call to getRenameTableExtraSQL() if $rename_table_extra_code is enabled. It will add sequence regeneration code.
        if ($this->rename_table_extra_code) {
            $extra_sentences = $this->getRenameTableExtraSQL($xmldb_table, $newname);
            $results = array_merge($results, $extra_sentences);
        }

        return $results;
    }

    /**
     * Given one correct XMLDBTable and the new name, returns the SQL statements
     * to drop it (inside one array)
     */
    function getDropTableSQL($xmldb_table) {

        $results = array();  //Array where all the sentences will be stored

        $drop = str_replace('TABLENAME', $this->getTableName($xmldb_table), $this->drop_table_sql);

        $results[] = $drop;

    /// call to getDropTableExtraSQL() if $drop_table_extra_code is enabled. It will add sequence/trigger drop code.
        if ($this->drop_table_extra_code) {
            $extra_sentences = $this->getDropTableExtraSQL($xmldb_table);
            $results = array_merge($results, $extra_sentences);
        }

        return $results;
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to add the field to the table
     */
    function getAddFieldSQL($xmldb_table, $xmldb_field) {

        $results = array();

    /// Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);

    /// Build the standard alter table add
        $altertable = 'ALTER TABLE ' . $tablename . ' ADD ' . 
                           $this->getFieldSQL($xmldb_field, $this->alter_column_skip_type,
                                                            $this->alter_column_skip_default,
                                                            $this->alter_column_skip_notnull);
    /// Add the after clause if necesary
        if ($this->add_after_clause && $xmldb_field->getPrevious()) {
            $altertable .= ' after ' . $this->getEncQuoted($xmldb_field->getPrevious());
        }
        $results[] = $altertable;

    /// If the DB has extra enum code
        if ($this->enum_extra_code) {
        /// If it's enum add the extra code
            if ($xmldb_field->getEnum()) {
                $results[] = 'ALTER TABLE ' . $tablename . ' ADD ' . $this->getEnumExtraSQL($xmldb_table, $xmldb_field);
            }
        }

        return $results;
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to drop the field from the table
     */
    function getDropFieldSQL($xmldb_table, $xmldb_field) {

        $results = array();

    /// Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);
        $fieldname = $this->getEncQuoted($xmldb_field->getName());

    /// Build the standard alter table drop
        $results[] = 'ALTER TABLE ' . $tablename . ' DROP COLUMN ' . $fieldname;

        return $results;
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to alter the field in the table
     */
    function getAlterFieldSQL($xmldb_table, $xmldb_field) {

        $results = array();

    /// Always specify NULLs in alter fields because we can change not nulls to nulls
        $this->specify_nulls = true;

    /// Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);
        $fieldname = $this->getEncQuoted($xmldb_field->getName());

    /// Build de alter sentence using the alter_column_sql template
        $alter = str_replace('TABLENAME', $this->getTableName($xmldb_table), $this->alter_column_sql);
        $alter = str_replace('COLUMNSPECS', $this->getFieldSQL($xmldb_field, $this->alter_column_skip_type,
                                                                             $this->alter_column_skip_default,
                                                                             $this->alter_column_skip_notnull), $alter);

    /// Add the after clause if necesary
        if ($this->add_after_clause && $xmldb_field->getPrevious()) {
            $alter .= ' after ' . $this->getEncQuoted($xmldb_field->getPrevious());
        }

    /// Build the standard alter table modify
        $results[] = $alter;

        return $results;
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to modify the enum of the field in the table
     */
    function getModifyEnumSQL($xmldb_table, $xmldb_field) {

        $results = array();

    /// Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);
        $fieldname = $this->getEncQuoted($xmldb_field->getName());

    /// Decide if we are going to create or to drop the enum (based exclusively in the values passed!)
        if (!$xmldb_field->getEnum()) {
            $results = $this->getDropEnumSQL($xmldb_table, $xmldb_field); //Drop
        } else {
            $results = $this->getCreateEnumSQL($xmldb_table, $xmldb_field); //Create/modify
        }

        return $results;
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to modify the default of the field in the table
     */
    function getModifyDefaultSQL($xmldb_table, $xmldb_field) {

        $results = array();

    /// Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);
        $fieldname = $this->getEncQuoted($xmldb_field->getName());

    /// Decide if we are going to create/modify or to drop the default
        if ($xmldb_field->getDefault() === null) {
            $results = $this->getDropDefaultSQL($xmldb_table, $xmldb_field); //Drop
        } else {
            $results = $this->getCreateDefaultSQL($xmldb_table, $xmldb_field); //Create/modify
        }

        return $results;
    }

    /**
     * Given one correct XMLDBField and the new name, returns the SQL statements
     * to rename it (inside one array)
     */
    function getRenameFieldSQL($xmldb_table, $xmldb_field, $newname) {

        $results = array();  //Array where all the sentences will be stored

    /// Although this is checked in ddllib - rename_field() - double check
    /// that we aren't trying to rename one "id" field. Although it could be
    /// implemented (if adding the necessary code to rename sequences, defaults,
    /// triggers... and so on under each getRenameFieldExtraSQL() function, it's
    /// better to forbide it, mainly because this field is the default PK and
    /// in the future, a lot of FKs can be pointing here. So, this field, more
    /// or less, must be considered inmutable!
        if ($xmldb_field->getName() == 'id') {
            return array();
        }

        $rename = str_replace('TABLENAME', $this->getTableName($xmldb_table), $this->rename_column_sql);
        $rename = str_replace('OLDFIELDNAME', $this->getEncQuoted($xmldb_field->getName()), $rename);
        $rename = str_replace('NEWFIELDNAME', $this->getEncQuoted($newname), $rename);

        $results[] = $rename;

    /// Call to getRenameFieldExtraSQL() if $rename_column_extra_code is enabled (will add some required sentences)
        if ($this->rename_column_extra_code) {
            $extra_sentences = $this->getRenameFieldExtraSQL($xmldb_table, $xmldb_field, $newname);
            $results = array_merge($results, $extra_sentences);
        }

        return $results;
    }

    /**
     * Given one XMLDBTable and one XMLDBKey, return the SQL statements needded to add the key to the table
     * note that undelying indexes will be added as parametrised by $xxxx_keys and $xxxx_index parameters
     */
    function getAddKeySQL($xmldb_table, $xmldb_key) {

        $results = array();

    /// Just use the CreateKeySQL function
        if ($keyclause = $this->getKeySQL($xmldb_table, $xmldb_key)) {
            $key = 'ALTER TABLE ' . $this->getTableName($xmldb_table) .
               ' ADD CONSTRAINT ' . $keyclause;
            $results[] = $key;
        }

    /// If we aren't creating the keys OR if the key is XMLDB_KEY_FOREIGN (not underlying index generated 
    /// automatically by the RDBMS) create the underlying (created by us) index (if doesn't exists)
        if (!$keyclause || $xmldb_key->getType() == XMLDB_KEY_FOREIGN) {
        /// Only if they don't exist
            if ($xmldb_key->getType() == XMLDB_KEY_FOREIGN) {  ///Calculate type of index based on type ok key
                $indextype = XMLDB_INDEX_NOTUNIQUE;
            } else {
                $indextype = XMLDB_INDEX_UNIQUE;
            }
            $xmldb_index = new XMLDBIndex('anyname');
            $xmldb_index->setAttributes($indextype, $xmldb_key->getFields());
            if (!index_exists($xmldb_table, $xmldb_index)) {
                $results = array_merge($results, $this->getAddIndexSQL($xmldb_table, $xmldb_index));
            }
        }

    /// If the key is XMLDB_KEY_FOREIGN_UNIQUE, create it as UNIQUE too
        if ($xmldb_key->getType() == XMLDB_KEY_FOREIGN_UNIQUE && $this->unique_keys) {
        ///Duplicate the key
            $xmldb_key->setType(XMLDB_KEY_UNIQUE);
            $results = array_merge($results, $this->getAddKeySQL($xmldb_table, $xmldb_key));
        }
        
    /// Return results
        return $results;
    }

    /**
     * Given one XMLDBTable and one XMLDBIndex, return the SQL statements needded to drop the index from the table
     */
    function getDropKeySQL($xmldb_table, $xmldb_key) {

        $results = array();

    /// Get the key name (note that this doesn't introspect DB, so could cause some problems sometimes!)
    /// TODO: We'll need to overwrite the whole getDropKeySQL() method inside each DB to do the proper queries
    /// against the dictionary or require ADOdb to support it or change the find_key_name() method to
    /// perform DB introspection directly. But, for now, as we aren't going to enable referential integrity
    /// it won't be a problem at all
        $dbkeyname = find_key_name($xmldb_table, $xmldb_key);

    /// Only if such type of key generation is enabled
        $dropkey = false;
        switch ($xmldb_key->getType()) {
            case XMLDB_KEY_PRIMARY:
                if ($this->primary_keys) {
                    $template = $this->drop_primary_key;
                    $dropkey = true;
                }
                break;
            case XMLDB_KEY_UNIQUE:
                if ($this->unique_keys) {
                    $template = $this->drop_unique_key;
                    $dropkey = true;
                }
                break;
            case XMLDB_KEY_FOREIGN_UNIQUE:
            case XMLDB_KEY_FOREIGN:
                if ($this->foreign_keys) {
                    $template = $this->drop_foreign_key;
                    $dropkey = true;
                }
                break;
        }
    /// If we have decided to drop the key, let's do it
        if ($dropkey) {
        /// Replace TABLENAME, CONSTRAINTTYPE and KEYNAME as needed
            $dropsql = str_replace('TABLENAME', $this->getTableName($xmldb_table), $template);
            $dropsql = str_replace('KEYNAME', $dbkeyname, $dropsql);

            $results[] = $dropsql;
        }

    /// If we aren't dropping the keys OR if the key is XMLDB_KEY_FOREIGN (not underlying index generated 
    /// automatically by the RDBMS) drop the underlying (created by us) index (if exists)
        if (!$dropkey || $xmldb_key->getType() == XMLDB_KEY_FOREIGN) {
        /// Only if they exist
            $xmldb_index = new XMLDBIndex('anyname');
            $xmldb_index->setAttributes(XMLDB_INDEX_UNIQUE, $xmldb_key->getFields());
            if (index_exists($xmldb_table, $xmldb_index)) {
                $results = array_merge($results, $this->getDropIndexSQL($xmldb_table, $xmldb_index));
            }
        }

    /// If the key is XMLDB_KEY_FOREIGN_UNIQUE, drop the UNIQUE too
        if ($xmldb_key->getType() == XMLDB_KEY_FOREIGN_UNIQUE && $this->unique_keys) {
        ///Duplicate the key
            $xmldb_key->setType(XMLDB_KEY_UNIQUE);
            $results = array_merge($results, $this->getDropKeySQL($xmldb_table, $xmldb_key));
        }
        
    /// Return results
        return $results;
    }

    /**
     * Given one XMLDBTable and one XMLDBKey, return the SQL statements needded to rename the key in the table
     * Experimental! Shouldn't be used at all!
     */

    function getRenameKeySQL($xmldb_table, $xmldb_key, $newname) {

        $results = array();

    /// Get the real key name
        $dbkeyname = find_key_name($xmldb_table, $xmldb_key);

    /// Check we are really generating this type of keys
        if (($xmldb_key->getType() == XMLDB_KEY_PRIMARY && !$this->primary_keys) ||
            ($xmldb_key->getType() == XMLDB_KEY_UNIQUE && !$this->unique_keys) ||
            ($xmldb_key->getType() == XMLDB_KEY_FOREIGN && !$this->foreign_keys) ||
            ($xmldb_key->getType() == XMLDB_KEY_FOREIGN_UNIQUE && !$this->unique_keys && !$this->foreign_keys)) {
        /// We aren't generating this type of keys, delegate to child indexes
            $xmldb_index = new XMLDBIndex($xmldb_key->getName());
            $xmldb_index->setFields($xmldb_key->getFields());
            return $this->getRenameIndexSQL($xmldb_table, $xmldb_index, $newname);
        }

    /// Arrived here so we are working with keys, lets rename them
    /// Replace TABLENAME and KEYNAME as needed
        $renamesql = str_replace('TABLENAME', $this->getTableName($xmldb_table), $this->rename_key_sql);
        $renamesql = str_replace('OLDKEYNAME', $dbkeyname, $renamesql);
        $renamesql = str_replace('NEWKEYNAME', $newname, $renamesql);

    /// Some DB doesn't support key renaming so this can be empty
        if ($renamesql) {
            $results[] = $renamesql;
        }

        return $results;
    }

    /**
     * Given one XMLDBTable and one XMLDBIndex, return the SQL statements needded to add the index to the table
     */
    function getAddIndexSQL($xmldb_table, $xmldb_index) {

    /// Just use the CreateIndexSQL function
        return $this->getCreateIndexSQL($xmldb_table, $xmldb_index);
    }

    /**
     * Given one XMLDBTable and one XMLDBIndex, return the SQL statements needded to drop the index from the table
     */
    function getDropIndexSQL($xmldb_table, $xmldb_index) {

        $results = array();

    /// Get the real index name
        $dbindexname = find_index_name($xmldb_table, $xmldb_index);

    /// Replace TABLENAME and INDEXNAME as needed
        $dropsql = str_replace('TABLENAME', $this->getTableName($xmldb_table), $this->drop_index_sql);
        $dropsql = str_replace('INDEXNAME', $dbindexname, $dropsql);

        $results[] = $dropsql;

        return $results;
    }

    /**
     * Given one XMLDBTable and one XMLDBIndex, return the SQL statements needded to rename the index in the table
     * Experimental! Shouldn't be used at all!
     */

    function getRenameIndexSQL($xmldb_table, $xmldb_index, $newname) {

        $results = array();

    /// Get the real index name
        $dbindexname = find_index_name($xmldb_table, $xmldb_index);

    /// Replace TABLENAME and INDEXNAME as needed
        $renamesql = str_replace('TABLENAME', $this->getTableName($xmldb_table), $this->rename_index_sql);
        $renamesql = str_replace('OLDINDEXNAME', $dbindexname, $renamesql);
        $renamesql = str_replace('NEWINDEXNAME', $newname, $renamesql);

    /// Some DB doesn't support index renaming (MySQL) so this can be empty
        if ($renamesql) {
            $results[] = $renamesql;
        }

        return $results;
    }

    /**
     * Given three strings (table name, list of fields (comma separated) and suffix),
     * create the proper object name quoting it if necessary.
     *
     * IMPORTANT: This function must be used to CALCULATE NAMES of objects TO BE CREATED,
     *            NEVER TO GUESS NAMES of EXISTING objects!!!
     */
    function getNameForObject($tablename, $fields, $suffix='') {

        $name = '';

    /// Implement one basic cache to avoid object name duplication
    /// and to speed up repeated queries for the same objects
        if (!isset($used_names)) {
            static $used_names = array();
        }

    /// If this exact object has been requested, return it
        if (array_key_exists($tablename.'-'.$fields.'-'.$suffix, $used_names)) {
            return $used_names[$tablename.'-'.$fields.'-'.$suffix];
        }

    /// Use standard naming. See http://docs.moodle.org/en/XMLDB_key_and_index_naming
        $tablearr = explode ('_', $tablename);
        foreach ($tablearr as $table) {
            $name .= substr(trim($table),0,4);
        }
        $name .= '_';
        $fieldsarr = explode (',', $fields);
        foreach ($fieldsarr as $field) {
            $name .= substr(trim($field),0,3);
        }
    /// Prepend the prefix
        $name = $this->prefix . $name;

        $name = substr(trim($name), 0, $this->names_max_length - 1 - strlen($suffix)); //Max names_max_length

    /// Add the suffix
        $namewithsuffix = $name;
        if ($suffix) {
            $namewithsuffix = $namewithsuffix . '_' . $suffix;
        }

    /// If the calculated name is in the cache, or if we detect it by introspecting the DB let's modify if
        if (in_array($namewithsuffix, $used_names) || $this->isNameInUse($namewithsuffix, $suffix, $tablename)) {
            $counter = 2;
        /// If have free space, we add 2
            if (strlen($namewithsuffix) < $this->names_max_length) {
                $newname = $name . $counter;
        /// Else replace the last char by 2
            } else {
                $newname = substr($name, 0, strlen($name)-1) . $counter;
            }
            $newnamewithsuffix = $newname;
            if ($suffix) {
                $newnamewithsuffix = $newnamewithsuffix . '_' . $suffix;
            }
        /// Now iterate until not used name is found, incrementing the counter
            while (in_array($newnamewithsuffix, $used_names) || $this->isNameInUse($newnamewithsuffix, $suffix, $tablename)) {
                $counter++;
                $newname = substr($name, 0, strlen($newname)-1) . $counter;
                $newnamewithsuffix = $newname;
                if ($suffix) {
                    $newnamewithsuffix = $newnamewithsuffix . '_' . $suffix;
                }
            }
            $namewithsuffix = $newnamewithsuffix;
        }

    /// Add the name to the cache
        $used_names[$tablename.'-'.$fields.'-'.$suffix] = $namewithsuffix;

    /// Quote it if necessary (reserved words)
        $namewithsuffix = $this->getEncQuoted($namewithsuffix);

        return $namewithsuffix;
    }

    /**
     * Given any string (or one array), enclose it by the proper quotes
     * if it's a reserved word
     */
    function getEncQuoted($input) {

        if (is_array($input)) {
            foreach ($input as $key=>$content) {
                $input[$key] = $this->getEncQuoted($content);
            }
            return $input;
        } else {
        /// Always lowercase
            $input = strtolower($input);
        /// if reserved or quote_all, quote it
            if ($this->quote_all || in_array($input, $this->reserved_words)) {
                $input = $this->quote_string . $input . $this->quote_string;
            }
            return $input;
        }
    }

    /**
     * Given one XMLDB Statement, build the needed SQL insert sentences to execute it
     */
    function getExecuteInsertSQL($statement) {

         $results = array();  //Array where all the sentences will be stored

         if ($sentences = $statement->getSentences()) {
             foreach ($sentences as $sentence) {
             /// Get the list of fields
                 $fields = $statement->getFieldsFromInsertSentence($sentence);
             /// Get the values of fields
                 $values = $statement->getValuesFromInsertSentence($sentence);
             /// Look if we have some CONCAT value and transform it dinamically
                 foreach($values as $key => $value) {
                 /// Trim single quotes
                     $value = trim($value,"'");
                     if (stristr($value, 'CONCAT') !== false){
                     /// Look for data between parentesis
                         preg_match("/CONCAT\s*\((.*)\)$/is", trim($value), $matches);
                         if (isset($matches[1])) {
                             $part = $matches[1];
                         /// Convert the comma separated string to an array
                             $arr = XMLDBObject::comma2array($part);
                             if ($arr) {
                                 $value = $this->getConcatSQL($arr);
                             }
                         }
                     }
                 /// Values to be sent to DB must be properly escaped
                     $value = addslashes($value);
                 /// Back trimmed quotes
                     $value = "'" . $value . "'";
                 /// Back to the array
                     $values[$key] = $value;
                 }

             /// Iterate over fields, escaping them if necessary
                 foreach($fields as $key => $field) {
                     $fields[$key] = $this->getEncQuoted($field);
                 }
             /// Build the final SQL sentence and add it to the array of results
             $sql = 'INSERT INTO ' . $this->getEncQuoted($this->prefix . $statement->getTable()) .
                         '(' . implode(', ', $fields) . ') ' .
                         'VALUES (' . implode(', ', $values) . ')';
                 $results[] = $sql;
             }

         }
         return $results;
    }

    /**
     * Given one array of elements, build de proper CONCAT expresion, based
     * in the $concat_character setting. If such setting is empty, then
     * MySQL's CONCAT function will be used instead
     */
    function getConcatSQL($elements) {

    /// Replace double quoted elements by single quotes
        foreach($elements as $key => $element) {
            $element = trim($element);
            if (substr($element, 0, 1) == '"' &&
                substr($element, -1, 1) == '"') {
                    $elements[$key] = "'" . trim($element, '"') . "'";
            }
        }

    /// Now call the standard sql_concat() DML function
        return call_user_func_array('sql_concat', $elements);
    }

    /**
     * Given one string (or one array), ends it with statement_end
     */
    function getEndedStatements ($input) {

        if (is_array($input)) {
            foreach ($input as $key=>$content) {
                $input[$key] = $this->getEndedStatements($content);
            }
            return $input;
        } else {
            $input = trim($input) . $this->statement_end;
            return $input;
        }
    }

    /**
     * Returns the name (string) of the sequence used in the table for the autonumeric pk
     * Only some DB have this implemented
     */
    function getSequenceFromDB($xmldb_table) {
        return false;
    }

    /**
     * Given one object name and it's type (pk, uk, fk, ck, ix, uix, seq, trg)
     * return if such name is currently in use (true) or no (false)
     * (MySQL requires the whole XMLDBTable object to be specified, so we add it always)
     * (invoked from getNameForObject()
     * Only some DB have this implemented
     */
    function isNameInUse($object_name, $type, $table_name) {
        return false; //For generators not implementing introspecion, 
                      //we always return with the name being free to be used
    }


/// ALL THESE FUNCTION MUST BE CUSTOMISED BY ALL THE XMLDGenerator classes

    /**
     * Given one XMLDB Type, lenght and decimals, returns the DB proper SQL type
     */
    function getTypeSQL ($xmldb_type, $xmldb_length=null, $xmldb_decimals=null) {
        return 'code for type(precision) goes to function getTypeSQL()';
    }

    /**
     * Given one XMLDB Field, return its enum SQL to be added inline with the column definition
     */
    function getEnumSQL ($xmldb_field) {
        return 'code for inline enum declaration goes to function getEnumSQL(). Can be disabled with enum_inline_code=false';
    }

    /**
     * Returns the code needed to create one enum for the xmldb_table and xmldb_field passes
     */
    function getEnumExtraSQL ($xmldb_table, $xmldb_field) {
        return 'Code for extra enum SQL goes to getEnumExtraSQL(). Can be disabled with enum_extra_code=false';
    }

    /**
     * Returns the code (array of statements) needed to execute extra statements on field rename
     */
    function getRenameFieldExtraSQL ($xmldb_table, $xmldb_field) {
        return array('Code for field rename goes to getRenameFieldExtraSQL(). Can be disabled with rename_column_extra_code=false;');
    }

    /**
     * Returns the code (array of statements) needed
     * to create one sequence for the xmldb_table and xmldb_field passes
     */
    function getCreateSequenceSQL ($xmldb_table, $xmldb_field) {
        return array('Code for extra sequence SQL goes to getCreateSequenceSQL(). Can be disabled with sequence_extra_code=false');
    }

    /**
     * Returns the code (array of statements) needed to add one comment to the table
     */
    function getCommentSQL ($xmldb_table) {
        return array('Code for table comment goes to getCommentSQL(). Can be disabled with add_table_comments=false;');
    }

    /**
     * Returns the code (array of statements) needed to execute extra statements on table rename
     */
    function getRenameTableExtraSQL ($xmldb_table) {
        return array('Code for table rename goes to getRenameTableExtraSQL(). Can be disabled with rename_table_extra_code=false;');
    }

    /**
     * Returns the code (array of statements) needed to execute extra statements on table drop
     */
    function getDropTableExtraSQL ($xmldb_table) {
        return array('Code for table drop goes to getDropTableExtraSQL(). Can be disabled with drop_table_extra_code=false;');
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to drop its enum 
     * (usually invoked from getModifyEnumSQL()
     */
    function getDropEnumSQL($xmldb_table, $xmldb_field) {
        return array('Code to drop one enum goes to getDropEnumSQL()');
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to add its enum 
     * (usually invoked from getModifyEnumSQL()
     */
    function getCreateEnumSQL($xmldb_table, $xmldb_field) {
        return array('Code to create one enum goes to getCreateEnumSQL()');
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to drop its default 
     * (usually invoked from getModifyDefaultSQL()
     */
    function getDropDefaultSQL($xmldb_table, $xmldb_field) {
        return array('Code to drop one default goes to getDropDefaultSQL()');
    }

    /**
     * Given one XMLDBTable and one optional XMLDBField, return one array with all the check
     * constrainst found for that table (or field). Must exist for each DB supported.
     * (usually invoked from find_check_constraint_name)
     */
    function getCheckConstraintsFromDB($xmldb_table, $xmldb_field=null) {
        return array('Code to fetch check constraints goes to getCheckConstraintsFromDB()');
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to add its default 
     * (usually invoked from getModifyDefaultSQL()
     */
    function getCreateDefaultSQL($xmldb_table, $xmldb_field) {
        return array('Code to create one default goes to getCreateDefaultSQL()');
    }

    /**
     * Returns an array of reserved words (lowercase) for this DB
     * You MUST provide the real list for each DB inside every XMLDB class
     */
    function getReservedWords() {
    /// Some well-know reserved words
        $reserved_words = array (
            'user', 'scale', 'type', 'comment', 'view', 'value', 'table', 'index', 'key', 'sequence', 'trigger'
        );
        return $reserved_words;
    }

    /**
     * Returns an array of tables to be built without prefix (lowercase)
     * It's enough to keep updated here this function.
     */
    function getTablesWithoutPrefix() {
    /// Some well-known tables to be created without prefix
        $tables = array (
            'adodb_logsql'
        );
        return $tables;
    }
}

?>
