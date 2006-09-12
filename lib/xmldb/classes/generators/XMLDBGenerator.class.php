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

    var $default_after_null = true;  //To decide if the default clause of each field must go after the null clause

    var $primary_key_name = null; //To force primary key names to one string (null=no force)

    var $primary_keys = true;  // Does the generator build primary keys
    var $unique_keys = true;  // Does the generator build unique keys
    var $foreign_keys = true; // Does the generator build foreign keys

    var $primary_index = true;// Does the generator need to build one index for primary keys
    var $unique_index = true;  // Does the generator need to build one index for unique keys
    var $foreign_index = true; // Does the generator need to build one index for foreign keys

    var $sequence_extra_code = true; //Does the generator need to add extra code to generate the sequence fields
    var $sequence_name = 'auto_increment'; //Particular name for inline sequences in this generator
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

    var $rename_table_extra_code = false; //Does the generatos need to add code after table rename

    var $drop_table_sql = 'DROP TABLE TABLENAME'; //SQL sentence to drop one table
                                  //TABLENAME is dinamically replaced

    var $drop_table_extra_code = false; //Does the generatos need to add code after table drop

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
     * Given one correct XMLDBTable, returns the SQL statements
     * to create it (inside one array)
     */
    function getCreateTableSQL($xmldb_table) {

        $tempprefix = '';
    /// If the table needs to be created without prefix
        if (in_array($xmldb_table->getName(), $this->getTablesWithoutPrefix())) {
        /// Save current prefix to be restore later
            $tempprefix = $this->prefix;
        /// Empty prefix
            $this->prefix = '';
        }

        $results = array();  //Array where all the sentences will be stored

    /// Table header
        $table = 'CREATE TABLE ' . $this->getEncQuoted($this->prefix . $xmldb_table->getName()) . ' (';

        if (!$xmldb_fields = $xmldb_table->getFields()) {
            return false;
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
        $indexcombs = array(); //To store all the key combinations used
        if ($xmldb_indexes = $xmldb_table->getIndexes()) {
            foreach ($xmldb_indexes as $xmldb_index) {
                $fieldsarr = $xmldb_index->getFields();
                sort ($fieldsarr);
                $currentcomb = strtolower(implode('-', $fieldsarr));
                if ($indextext = $this->getCreateIndexSQL($xmldb_table, $xmldb_index)) {
                /// Only create the index if the combination hasn't been used before
                    if (!in_array($currentcomb, $indexcombs)) {
                    /// Add the INDEX to the array
                        $results = array_merge($results, $indextext);
                    }
                }
            /// Add the index to the array of used combinations
                $indexcombs[] = $currentcomb;
            }
        }

    /// Also, add the indexes needed from keys, based on configuration (each one, one statement)
        if ($xmldb_keys = $xmldb_table->getKeys()) {
            foreach ($xmldb_keys as $xmldb_key) {
                $index = null;
                switch ($xmldb_key->getType()) {
                    case XMLDB_KEY_PRIMARY:
                        if ($this->primary_index) {
                            $index = new XMLDBIndex('temp_index');
                            $index->setUnique(true);
                            $index->setFields($xmldb_key->getFields());
                        }
                        break;
                    case XMLDB_KEY_UNIQUE:
                    case XMLDB_KEY_FOREIGN_UNIQUE:
                        if ($this->unique_index) {
                            $index = new XMLDBIndex('temp_index');
                            $index->setUnique(true);
                            $index->setFields($xmldb_key->getFields());
                        }
                        break;
                    case XMLDB_KEY_FOREIGN:
                        if ($this->foreign_index) {
                            $index = new XMLDBIndex('temp_index');
                            $index->setUnique(false);
                            $index->setFields($xmldb_key->getFields());
                        }
                        break;
                }
                if ($index) {
                    if ($indextext = $this->getCreateIndexSQL($xmldb_table, $index)) {
                        $fieldsarr = $index->getFields();
                        sort ($fieldsarr);
                        $currentcomb = strtolower(implode('-', $fieldsarr));
                    /// Only create the index if the combination hasn't been used before
                        if (!in_array($currentcomb, $indexcombs)) {
                        /// Add the INDEX to the array
                            $results = array_merge($results, $indextext);
                        }
                    }
                /// Add the index to the array of used combinations
                    $indexcombs[] = $currentcomb;
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

    /// Re-set the original prefix if it has changed
        if ($tempprefix) {
            $this->prefix = $tempprefix;
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
        $index .= ' ON ' . $this->getEncQuoted($this->prefix . $xmldb_table->getName());
        $index .= ' (' . implode(', ', $this->getEncQuoted($xmldb_index->getFields())) . ')';

        return array($index);
    }

    /**
     * Given one correct XMLDBField, returns the complete SQL line to create it
     */
    function getFieldSQL($xmldb_field) {

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
        if (!$xmldb_field->getEnum() || $this->enum_inline_code == false) {
            $field .= ' ' . $this->getTypeSQL($xmldb_field->getType(), $xmldb_field->getLength(), $xmldb_field->getDecimals());
        } else {
        /// call to custom function
            $field .= ' ' . $this->getEnumSQL($xmldb_field);
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
        if ($xmldb_field->getNotNull()) {
            $notnull = ' NOT NULL';
        }
    /// Calculate the default clause
        $default = $this->getDefaultClause($xmldb_field);
    /// Based on default_after_null, set both clauses properly
        if ($this->default_after_null) {
            $field .= $notnull . $default;
        } else {
            $field .= $default . $notnull;
        }
    /// The sequence
        if ($xmldb_field->getSequence()) {
            $field .= ' ' . $this->sequence_name;
            if ($this->sequence_only) {
            /// We only want the field name and sequence name to be printed
            /// so, calculate it and return
                return $this->getEncQuoted($xmldb_field->getName()) . ' ' . $this->sequence_name;
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
     * Give one XMLDBField, returns the correct "default clause" for the current configuration
     */
    function getDefaultClause ($xmldb_field) {

        $default = '';

        if ($xmldb_field->getDefault() != NULL) {
            $default = ' default ';
            if ($xmldb_field->getType() == XMLDB_TYPE_CHAR ||
                $xmldb_field->getType() == XMLDB_TYPE_TEXT) {
                    $default .= "'" . addslashes($xmldb_field->getDefault()) . "'";
            } else {
                $default .= $xmldb_field->getDefault();
            }
        } else {
        /// We force default '' for not null char columns without proper default
        /// some day this should be out!
            if ($this->default_for_char !== NULL && 
                $xmldb_field->getType() == XMLDB_TYPE_CHAR &&
                $xmldb_field->getNotNull()) {
                $default .= ' default ' . "'" . $this->default_for_char . "'";
            }
        }
        return $default;
    }

    /**
     * Given one correct XMLDBTable and the new name, returns the SQL statements
     * to rename it (inside one array)
     */ 
    function getRenameTableSQL($xmldb_table, $newname) {

        $results = array();  //Array where all the sentences will be stored

        $rename = str_replace('OLDNAME', $this->getEncQuoted($this->prefix . $xmldb_table->getName()), $this->rename_table_sql);
        $rename = str_replace('NEWNAME', $this->getEncQuoted($this->prefix . $newname), $rename_table_sql);

        $results[] = $rename;

    /// TODO, call to getRenameTableExtraSQL() if $rename_table_extra_code is enabled. It will add sequence regeneration code.
        if ($this->rename_table_extra_code) {
            $extra_sentences = $this->getRenameTableExtraSQL();
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

        $drop = str_replace('TABLENAME', $this->getEncQuoted($this->prefix . $xmldb_table->getName()), $this->drop_table_sql);

        $results[] = $drop;

    /// TODO, call to getDropTableExtraSQL() if $rename_table_extra_code is enabled. It will add sequence/trigger drop code.
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
        $tablename = $this->getEncQuoted($this->prefix . $xmldb_table->getName());

    /// Build the standard alter table add
        $altertable = 'ALTER TABLE ' . $tablename . ' ADD ' . $this->getFieldSQL($xmldb_field);
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
        $tablename = $this->getEncQuoted($this->prefix . $xmldb_table->getName());
        $fieldname = $this->getEncQuoted($xmldb_field->getName());

    /// Build the standard alter table drop
        $results[] = 'ALTER TABLE ' . $tablename . ' DROP COLUMN ' . $fieldname;

        return $results;
    }

    /**
     * Given three strings (table name, list of fields (comma separated) and suffix), create the proper object name
     * quoting it if necessary
     */
    function getNameForObject($tablename, $fields, $suffix) {

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
        $namewithsuffix = $name . '_' . $suffix;

    /// If the calculated name is in the cache, let's modify if
        if (in_array($namewithsuffix, $used_names)) {
            $counter = 2;
        /// If have free space, we add 2
            if (strlen($namewithsuffix) < $this->names_max_length) {
                $newname = $name . $counter;
        /// Else replace the last char by 2
            } else {
                $newname = substr($name, 0, strlen($name)-1) . $counter;
            }
            $newnamewithsuffix = $newname . '_' . $suffix;
        /// Now iterate until not used name is found, incrementing the counter
            while (in_array($newnamewithsuffix, $used_names)) {
                $newname = substr($name, 0, strlen($newname)-1) . $counter;
                $newnamewithsuffix = $newname . '_' . $suffix;
                $counter++;
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

        if ($this->concat_character) {
            return implode (' ' . $this->concat_character . ' ', $elements);
        } else {
            return 'CONCAT(' . implode(', ', $elements) . ')';
        }
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
