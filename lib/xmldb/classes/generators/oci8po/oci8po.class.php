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

    var $drop_default_clause_required = true; //To specify if the generator must use some DEFAULT clause to drop defaults
    var $drop_default_clause = 'NULL'; //The DEFAULT clause required to drop defaults

    var $default_after_null = false;  //To decide if the default clause of each field must go after the null clause

    var $sequence_extra_code = true; //Does the generator need to add extra code to generate the sequence fields
    var $sequence_name = ''; //Particular name for inline sequences in this generator

    var $drop_table_extra_code = true; //Does the generator need to add code after table drop

    var $rename_table_extra_code = true; //Does the generator need to add code after table rename

    var $rename_column_extra_code = true; //Does the generator need to add code after field rename

    var $enum_inline_code = false; //Does the generator need to add inline code in the column definition

    var $alter_column_sql = 'ALTER TABLE TABLENAME MODIFY (COLUMNSPECS)'; //The SQL template to alter columns

    /**
     * Creates one new XMLDBoci8po
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
        $sql.= ' CHECK (' . $this->getEncQuoted($xmldb_field->getName()) . ' IN (' . implode(', ', $xmldb_field->getEnumValues()) . '))';

        return $sql;
    }

    /**
     * Returns the code needed to create one sequence for the xmldb_table and xmldb_field passes
     */
    function getCreateSequenceSQL ($xmldb_table, $xmldb_field) {

        $results = array();

        $sequence_name = $this->getNameForObject($xmldb_table->getName(), $xmldb_field->getName(), 'seq');

        $sequence = "CREATE SEQUENCE " . $sequence_name;
        $sequence.= "\n    START WITH 1";
        $sequence.= "\n    INCREMENT BY 1";
        $sequence.= "\n    NOMAXVALUE";

        $results[] = $sequence;

        $results = array_merge($results, $this->getCreateTriggerSQL ($xmldb_table, $xmldb_field));

        return $results;
    }

    /**
     * Returns the code needed to create one trigger for the xmldb_table and xmldb_field passed
     */
    function getCreateTriggerSQL ($xmldb_table, $xmldb_field) {

        $trigger_name = $this->getNameForObject($xmldb_table->getName(), $xmldb_field->getName(), 'trg');
        $sequence_name = $this->getNameForObject($xmldb_table->getName(), $xmldb_field->getName(), 'seq');

        $trigger = "CREATE TRIGGER " . $trigger_name;
        $trigger.= "\n    BEFORE INSERT";
        $trigger.= "\nON " . $this->getTableName($xmldb_table);
        $trigger.= "\n    FOR EACH ROW";
        $trigger.= "\nBEGIN";
        $trigger.= "\n    IF :new." . $this->getEncQuoted($xmldb_field->getName()) . ' IS NULL THEN';
        $trigger.= "\n        SELECT " . $sequence_name . '.nextval INTO :new.' . $this->getEncQuoted($xmldb_field->getName()) . " FROM dual;";
        $trigger.= "\n    END IF;";
        $trigger.= "\nEND;";

        return array($trigger);
    }

    /**
     * Returns the code needed to drop one sequence for the xmldb_table and xmldb_field passed
     * Can, optionally, specify if the underlying trigger will be also dropped
     */
    function getDropSequenceSQL ($xmldb_table, $xmldb_field, $include_trigger=false) {

        $sequence_name = $this->getSequenceFromDB($xmldb_table);

        $sequence = "DROP SEQUENCE " . $sequence_name;

        $trigger_name = $this->getTriggerFromDB($xmldb_table);

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

        $comment = "COMMENT ON TABLE " . $this->getTableName($xmldb_table);
        $comment.= " IS '" . addslashes(substr($xmldb_table->getComment(), 0, 250)) . "'";

        return array($comment);
    }

    /**
     * Returns the code (array of statements) needed to execute extra statements on field rename
     */
    function getRenameFieldExtraSQL ($xmldb_table, $xmldb_field, $newname) {

        $results = array();

    /// If the field is enum, drop and re-create the check constraint
        if ($xmldb_field->getEnum()) {
        /// Drop the current enum
            $results = array_merge($results, $this->getDropEnumSQL($xmldb_table, $xmldb_field));
        /// Change field name (over a clone to avoid some potential problems later)
            $new_xmldb_field = clone($xmldb_field);
            $new_xmldb_field->setName($newname);
        /// Recreate the enum
            $results = array_merge($results, $this->getCreateEnumSQL($xmldb_table, $new_xmldb_field));
        }

        return $results;
    }

    /**
     * Returns the code (array of statements) needed to execute extra statements on table drop
     */
    function getDropTableExtraSQL ($xmldb_table) {
        $xmldb_field = new XMLDBField('id'); // Fields having sequences should be exclusively, id.
        return $this->getDropSequenceSQL($xmldb_table, $xmldb_field, false);
    }

    /**
     * Returns the code (array of statements) needed to execute extra statements on table rename
     */
    function getRenameTableExtraSQL ($xmldb_table, $newname) {

        $results = array();

        $xmldb_field = new XMLDBField('id'); // Fields having sequences should be exclusively, id.

        $oldseqname = $this->getSequenceFromDB($xmldb_table);
        $newseqname = $this->getNameForObject($newname, $xmldb_field->getName(), 'seq');

    /// Rename de sequence
        $results[] = 'RENAME ' . $oldseqname . ' TO ' . $newseqname;

        $oldtriggername = $this->getTriggerFromDB($xmldb_table);
        $newtriggername = $this->getNameForObject($newname, $xmldb_field->getName(), 'trg');

    /// Drop old trigger
        $results[] = "DROP TRIGGER " . $oldtriggername;

        $newt = new XMLDBTable($newname); /// Temp table for trigger code generation

    /// Create new trigger
        $results = array_merge($results, $this->getCreateTriggerSQL($newt, $xmldb_field));

    /// Rename all the check constraints in the table
        $oldtablename = $this->getTableName($xmldb_table);
        $newtablename = $this->getTableName($newt);

        $oldconstraintprefix = $this->getNameForObject($xmldb_table->getName(), '');
        $newconstraintprefix = $this->getNameForObject($newt->getName(), '', '');

        if ($constraints = $this->getCheckConstraintsFromDB($xmldb_table)) {
            foreach ($constraints as $constraint) {
            /// Drop the old constraint
                $results[] = 'ALTER TABLE ' . $newtablename . ' DROP CONSTRAINT ' . $constraint->name;
            /// Calculate the new constraint name
                $newconstraintname = str_replace($oldconstraintprefix, $newconstraintprefix, $constraint->name);
            /// Add the new constraint
                $results[] = 'ALTER TABLE ' . $newtablename . ' ADD CONSTRAINT ' . $newconstraintname .
                             ' CHECK (' . $constraint->description . ')';
            }
        }

        return $results;
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to alter the field in the table
     * Oracle has some severe limits:
     *     - clob and blob fields doesn't allow type to be specified
     *     - error is dropped if the null/not null clause is specified and hasn't changed
     *     - changes in precision/decimals of numeric fields drop an ORA-1440 error
     */
    function getAlterFieldSQL($xmldb_table, $xmldb_field) {

        global $db;

        $results = array(); /// To store all the needed SQL commands

    /// Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);
        $fieldname = $this->getEncQuoted($xmldb_field->getName());

    /// Take a look to field metadata
        $meta = array_change_key_case($db->MetaColumns($tablename));
        $metac = $meta[$fieldname];
        $oldtype = strtolower($metac->type);
        $oldmetatype = column_type($xmldb_table->getName(), $fieldname);
        $oldlength = $metac->max_length;
    /// To calculate the oldlength if the field is numeric, we need to perform one extra query
    /// because ADOdb has one bug here. http://phplens.com/lens/lensforum/msgs.php?id=15883
        if ($oldmetatype == 'N') {
            $uppertablename = strtoupper($tablename);
            $upperfieldname = strtoupper($fieldname);
            if ($col = get_record_sql("SELECT cname, precision
                                   FROM col
                                   WHERE tname = '$uppertablename'
                                     AND cname = '$upperfieldname'")) {
                $oldlength = $col->precision;
            }
        }
        $olddecimals = empty($metac->scale) ? null : $metac->scale;
        $oldnotnull = empty($metac->not_null) ? false : $metac->not_null;
        $olddefault = empty($metac->default_value) || strtoupper($metac->default_value) == 'NULL' ? null : $metac->default_value;

        $typechanged = true;  //By default, assume that the column type has changed
        $precisionchanged = true;  //By default, assume that the column precision has changed
        $decimalchanged = true;  //By default, assume that the column decimal has changed
        $defaultchanged = true;  //By default, assume that the column default has changed
        $notnullchanged = true;  //By default, assume that the column notnull has changed

        $from_temp_fields = false; //By default don't assume we are going to use temporal fields

    /// Detect if we are changing the type of the column
        if (($xmldb_field->getType() == XMLDB_TYPE_INTEGER && substr($oldmetatype, 0, 1) == 'I') ||
            ($xmldb_field->getType() == XMLDB_TYPE_NUMBER  && $oldmetatype == 'N') ||
            ($xmldb_field->getType() == XMLDB_TYPE_FLOAT   && $oldmetatype == 'F') ||
            ($xmldb_field->getType() == XMLDB_TYPE_CHAR    && substr($oldmetatype, 0, 1) == 'C') ||
            ($xmldb_field->getType() == XMLDB_TYPE_TEXT    && substr($oldmetatype, 0, 1) == 'X') ||
            ($xmldb_field->getType() == XMLDB_TYPE_BINARY  && $oldmetatype == 'B')) {
            $typechanged = false;
        } 
    /// Detect if precision has changed
        if (($xmldb_field->getType() == XMLDB_TYPE_TEXT) ||
            ($xmldb_field->getType() == XMLDB_TYPE_BINARY) ||
            ($oldlength == -1) ||
            ($xmldb_field->getLength() == $oldlength)) {
            $precisionchanged = false;
        }
    /// Detect if decimal has changed
        if (($xmldb_field->getType() == XMLDB_TYPE_INTEGER) ||
            ($xmldb_field->getType() == XMLDB_TYPE_CHAR) ||
            ($xmldb_field->getType() == XMLDB_TYPE_TEXT) ||
            ($xmldb_field->getType() == XMLDB_TYPE_BINARY) ||
            (!$xmldb_field->getDecimals()) ||
            (!$olddecimals) ||
            ($xmldb_field->getDecimals() == $olddecimals)) {
            $decimalchanged = false;
        }
    /// Detect if we are changing the default
        if (($xmldb_field->getDefault() === null && $olddefault === null) ||
            ($xmldb_field->getDefault() === $olddefault) ||             //Check both equality and
            ("'" . $xmldb_field->getDefault() . "'" === $olddefault)) {  //Equality with quotes because ADOdb returns the default with quotes
            $defaultchanged = false;
        }

    /// Detect if we are changing the nullability
        if (($xmldb_field->getNotnull() === $oldnotnull)) {
            $notnullchanged = false;
        }

    /// If type has changed or precision or decimal has changed and we are in one numeric field
    ///     - create one temp column with the new specs
    ///     - fill the new column with the values from the old one
    ///     - drop the old column
    ///     - rename the temp column to the original name
        if (($typechanged) || (($oldmetatype == 'N' || $oldmetatype == 'I')  && ($precisionchanged || $decimalchanged))) {
            $tempcolname = $xmldb_field->getName() . '_alter_column_tmp';
        /// Prevent temp field to have both NULL/NOT NULL and DEFAULT constraints
            $this->alter_column_skip_notnull = true;
            $this->alter_column_skip_default = true;
            $xmldb_field->setName($tempcolname);
        /// Create the temporal column
            $results = array_merge($results, $this->getAddFieldSQL($xmldb_table, $xmldb_field));
        /// Copy contents from original col to the temporal one
            $results[] = 'UPDATE ' . $tablename . ' SET ' . $tempcolname . ' = ' . $fieldname;
        /// Drop the old column
            $xmldb_field->setName($fieldname); //Set back the original field name
            $results = array_merge($results, $this->getDropFieldSQL($xmldb_table, $xmldb_field));
        /// Rename the temp column to the original one
            $results[] = 'ALTER TABLE ' . $tablename . ' RENAME COLUMN ' . $tempcolname . ' TO ' . $fieldname;
        /// Mark we have performed one change based in temp fields
            $from_temp_fields = true;
        /// Re-enable the notnull and default sections so the general AlterFieldSQL can use it
            $this->alter_column_skip_notnull = false;
            $this->alter_column_skip_default = false;
        /// Dissable the type section because we have done it with the temp field
            $this->alter_column_skip_type = true;
        /// If new field is nullable, nullability hasn't changed
            if (!$xmldb_field->getNotnull()) {
                $notnullchanged = false;
            }
        /// If new field hasn't default, default hasn't changed
            if ($xmldb_field->getDefault() === null) {
                $defaultchanged = false;
            }
        }

    /// If type and precision and decimals hasn't changed, prevent the type clause
        if (!$typechanged && !$precisionchanged && !$decimalchanged) {
            $this->alter_column_skip_type = true;
        }

    /// If NULL/NOT NULL hasn't changed
    /// prevent null clause to be specified
        if (!$notnullchanged) {
            $this->alter_column_skip_notnull = true; /// Initially, prevent the notnull clause
        /// But, if we have used the temp field and the new field is not null, then enforce the not null clause
            if ($from_temp_fields &&  $xmldb_field->getNotnull()) {
                $this->alter_column_skip_notnull = false;
            }
        }
    /// If default hasn't changed
    /// prevent default clause to be specified
        if (!$defaultchanged) {
            $this->alter_column_skip_default = true; /// Initially, prevent the default clause
        /// But, if we have used the temp field and the new field has default clause, then enforce the default clause
            if ($from_temp_fields && $default_clause = $this->getDefaultClause($xmldb_field)) {
                $this->alter_column_skip_default = false;
            }
        }

    /// If arriving here, something is not being skiped (type, notnull, default), calculate the standar AlterFieldSQL
        if (!$this->alter_column_skip_type || !$this->alter_column_skip_notnull || !$this->alter_column_skip_default) {
            $results = array_merge($results, parent::getAlterFieldSQL($xmldb_table, $xmldb_field));
            return $results;
        }

    /// Finally return results
        return $results;
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to create its enum 
     * (usually invoked from getModifyEnumSQL()
     */
    function getCreateEnumSQL($xmldb_table, $xmldb_field) {
    /// All we have to do is to create the check constraint
        return array('ALTER TABLE ' . $this->getTableName($xmldb_table) . 
                     ' ADD ' . $this->getEnumExtraSQL($xmldb_table, $xmldb_field));
    }
                                                       
    /**     
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to drop its enum 
     * (usually invoked from getModifyEnumSQL()
     */         
    function getDropEnumSQL($xmldb_table, $xmldb_field) {
    /// Let's introspect to know the real name of the check constraint
        if ($check_constraints = $this->getCheckConstraintsFromDB($xmldb_table, $xmldb_field)) {
            $check_constraint = array_shift($check_constraints); /// Get the 1st (should be only one)
            $constraint_name = strtolower($check_constraint->name); /// Extract the REAL name
        /// All we have to do is to drop the check constraint
            return array('ALTER TABLE ' . $this->getTableName($xmldb_table) . 
                     ' DROP CONSTRAINT ' . $constraint_name);
        } else { /// Constraint not found. Nothing to do
            return array();
        }
    }   

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to create its default 
     * (usually invoked from getModifyDefaultSQL()
     */
    function getCreateDefaultSQL($xmldb_table, $xmldb_field) {
    /// Just a wrapper over the getAlterFieldSQL() function for Oracle that
    /// is capable of handling defaults
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }
                                                       
    /**     
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to drop its default 
     * (usually invoked from getModifyDefaultSQL()
     */         
    function getDropDefaultSQL($xmldb_table, $xmldb_field) {
    /// Just a wrapper over the getAlterFieldSQL() function for Oracle that
    /// is capable of handling defaults
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }   

    /**
     * Given one XMLDBTable returns one array with all the check constrainsts
     * in the table (fetched from DB)
     * Optionally the function allows one xmldb_field to be specified in
     * order to return only the check constraints belonging to one field.
     * Each element contains the name of the constraint and its description
     * If no check constraints are found, returns an empty array
     */
    function getCheckConstraintsFromDB($xmldb_table, $xmldb_field = null) {

        $results = array();

        $tablename = strtoupper($this->getTableName($xmldb_table));

        if ($constraints = get_records_sql("SELECT lower(c.constraint_name) AS name, c.search_condition AS description
                                              FROM user_constraints c
                                             WHERE c.table_name = '{$tablename}'
                                               AND c.constraint_type = 'C'
                                               AND c.constraint_name not like 'SYS%'")) {
            foreach ($constraints as $constraint) {
                $results[$constraint->name] = $constraint;
            }
        }

    /// Filter by the required field if specified
        if ($xmldb_field) {
            $filtered_results = array();
            $filter = $xmldb_field->getName();
        /// Lets clean a bit each constraint description, looking for the filtered field
            foreach ($results as $key => $result) {
            /// description starts by "$filter IN" assume it's a constraint beloging to the field
                if (preg_match("/^{$filter} IN/i", $result->description)) {
                    $filtered_results[$key] = $result;
                }
            }
        /// Assign filtered results to the final results array
            $results =  $filtered_results;
        }

        return $results;
    }

    /**
     * Given one XMLDBTable returns one string with the sequence of the table
     * in the table (fetched from DB)
     * The sequence name for oracle is calculated by looking the corresponding
     * trigger and retrieving the sequence name from it (because sequences are
     * independent elements)
     * If no sequence is found, returns false
     */
    function getSequenceFromDB($xmldb_table) {

         $tablename    = strtoupper($this->getTableName($xmldb_table));
         $prefixupper  = strtoupper($this->prefix);
         $sequencename = false;

        if ($trigger = get_record_sql("SELECT trigger_name, trigger_body
                                         FROM user_triggers
                                        WHERE table_name = '{$tablename}'
                                          AND trigger_name LIKE '{$prefixupper}%_ID%_TRG'")) {
        /// If trigger found, regexp it looking for the sequence name
            preg_match('/.*SELECT (.*)\.nextval/i', $trigger->trigger_body, $matches);
            if (isset($matches[1])) {
                $sequencename = $matches[1];
            }
        }

        return $sequencename;
    }

    /**
     * Given one XMLDBTable returns one string with the trigger
     * in the table (fetched from DB)
     * If no trigger is found, returns false
     */
    function getTriggerFromDB($xmldb_table) {

        $tablename   = strtoupper($this->getTableName($xmldb_table));
        $prefixupper = strtoupper($this->prefix);
        $triggername = false;

        if ($trigger = get_record_sql("SELECT trigger_name, trigger_body
                                         FROM user_triggers
                                        WHERE table_name = '{$tablename}'
                                          AND trigger_name LIKE '{$prefixupper}%_ID%_TRG'")) {
            $triggername = $trigger->trigger_name;
        }

        return $triggername;
    }

    /**
     * Given one object name and it's type (pk, uk, fk, ck, ix, uix, seq, trg)
     * return if such name is currently in use (true) or no (false)
     * (invoked from getNameForObject()
     */
    function isNameInUse($object_name, $type, $table_name) {
        switch($type) {
            case 'ix':
            case 'uix':
            case 'seq':
            case 'trg':
                if ($check = get_records_sql("SELECT object_name 
                                              FROM user_objects 
                                              WHERE lower(object_name) = '" . strtolower($object_name) . "'")) {
                    return true;
                }
                break;
            case 'pk':
            case 'uk':
            case 'fk':
            case 'ck':
                if ($check = get_records_sql("SELECT constraint_name 
                                              FROM user_constraints
                                              WHERE lower(constraint_name) = '" . strtolower($object_name) . "'")) {
                    return true;
                }
                break;
        }
        return false; //No name in use found
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
