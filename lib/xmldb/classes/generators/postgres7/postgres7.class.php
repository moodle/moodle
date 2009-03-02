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

/// This class generate SQL code to be used against PostgreSQL
/// It extends XMLDBgenerator so everything can be
/// overriden as needed to generate correct SQL.

class XMLDBpostgres7 extends XMLDBgenerator {

/// Only set values that are different from the defaults present in XMLDBgenerator

    var $number_type = 'NUMERIC';    // Proper type for NUMBER(x) in this DB

    var $unsigned_allowed = false;    // To define in the generator must handle unsigned information
    var $default_for_char = '';      // To define the default to set for NOT NULLs CHARs without default (null=do nothing)

    var $sequence_extra_code = false; //Does the generator need to add extra code to generate the sequence fields
    var $sequence_name = 'BIGSERIAL'; //Particular name for inline sequences in this generator
    var $sequence_name_small = 'SERIAL'; //Particular name for inline sequences in this generator
    var $sequence_only = true; //To avoid to output the rest of the field specs, leaving only the name and the sequence_name variable

    var $rename_table_extra_code = true; //Does the generator need to add code after table rename

    var $rename_column_extra_code = true; //Does the generator need to add code after column rename

    var $enum_inline_code = false; //Does the generator need to add inline code in the column definition

    var $rename_index_sql = 'ALTER TABLE OLDINDEXNAME RENAME TO NEWINDEXNAME'; //SQL sentence to rename one index
                                      //TABLENAME, OLDINDEXNAME, NEWINDEXNAME are dinamically replaced

    var $rename_key_sql = null; //SQL sentence to rename one key (PostgreSQL doesn't support this!)
                                          //TABLENAME, OLDKEYNAME, NEWKEYNAME are dinamically replaced

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

        $comment = "COMMENT ON TABLE " . $this->getTableName($xmldb_table);
        $comment.= " IS '" . addslashes(substr($xmldb_table->getComment(), 0, 250)) . "'";

        return array($comment);
    }

    /**
     * Returns the code (array of statements) needed to execute extra statements on table rename
     */
    function getRenameTableExtraSQL ($xmldb_table, $newname) {

        $results = array();

        $newt = new XMLDBTable($newname);

        $xmldb_field = new XMLDBField('id'); // Fields having sequences should be exclusively, id.

        $oldseqname = $this->getTableName($xmldb_table) . '_' . $xmldb_field->getName() . '_seq';
        $newseqname = $this->getTableName($newt) . '_' . $xmldb_field->getName() . '_seq';

    /// Rename de sequence
        $results[] = 'ALTER TABLE ' . $oldseqname . ' RENAME TO ' . $newseqname;

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
                             ' CHECK ' . $constraint->description;
             }
         }

        return $results;
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to add the field to the table
     * PostgreSQL is pretty standard but with one severe restriction under 7.4 that forces us to overload
     * this function: Default clause is not allowed when adding fields.
     * 
     * This function can be safely removed once min req. for PG will be 8.0
     */
    function getAddFieldSQL($xmldb_table, $xmldb_field) {
    
        $results = array();

        $tablename = $this->getTableName($xmldb_table);
        $fieldname = $this->getEncQuoted($xmldb_field->getName());

        $defaultvalue = null;

    /// Save old flags
        $old_skip_default = $this->alter_column_skip_default;
        $old_skip_notnull = $this->alter_column_skip_notnull;

    /// Prevent default clause and launch parent getAddField()
        $this->alter_column_skip_default = true;
        $this->alter_column_skip_notnull = true;
        $results = parent::getAddFieldSQL($xmldb_table, $xmldb_field);

    /// Re-set old flags
        $this->alter_column_skip_default = $old_skip_default;
        $this->alter_column_skip_notnull = $old_skip_notnull;

    /// Add default (only if not skip_default)
        if (!$this->alter_column_skip_default) {
            if ($defaultclause = $this->getDefaultClause($xmldb_field)) {
                $defaultvalue = $this->getDefaultValue($xmldb_field);
                $results[] = 'ALTER TABLE ' . $tablename . ' ALTER COLUMN ' . $fieldname . ' SET' . $defaultclause; /// Add default clause
            }
        /// Update default value (if exists) to all the records
            if ($defaultvalue !== null) {
                $results[] = 'UPDATE ' . $tablename . ' SET ' . $fieldname . '=' . $defaultvalue;
            }
        }

    /// Add not null (only if no skip_notnull)
        if (!$this->alter_column_skip_notnull) {
            if ($xmldb_field->getNotnull()) {
                $results[] = 'ALTER TABLE ' . $tablename . ' ALTER COLUMN ' . $fieldname . ' SET NOT NULL'; /// Add not null
            }
        }

        return $results;
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
        $olddecimals = empty($metac->scale) ? null : $metac->scale;
        $oldnotnull = empty($metac->not_null) ? false : $metac->not_null;
        $olddefault = empty($metac->has_default) ? null : strtok($metac->default_value, ':');

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
    /// Detect if we are changing the precision 
        if (($xmldb_field->getType() == XMLDB_TYPE_TEXT) ||
            ($xmldb_field->getType() == XMLDB_TYPE_BINARY) ||
            ($oldlength == -1) ||
            ($xmldb_field->getLength() == $oldlength)) {
            $precisionchanged = false;
        }
    /// Detect if we are changing the decimals
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

    /// TODO: Some combinations like
    /// TODO: integer->integer
    /// TODO: integer->text
    /// TODO: number->text
    /// TODO: text->text
    /// TODO: do not require the use of temp columns, because PG 8.0 supports them automatically
    /// TODO: with a simple "alter table zzz alter column yyy type new specs"
    /// TODO: Must be implemented that way. Eloy 09/2007

    /// If the type or the precision or the decimals have changed, then we need to:
    ///     - create one temp column with the new specs
    ///     - fill the new column with the values from the old one (casting if needed)
    ///     - drop the old column
    ///     - rename the temp column to the original name
        if ($typechanged || $precisionchanged || $decimalchanged) {
            $tempcolname = $xmldb_field->getName() . '_alter_column_tmp';
        /// Prevent temp field to have both NULL/NOT NULL and DEFAULT constraints
            $this->alter_column_skip_notnull = true;
            $this->alter_column_skip_default = true;
            $xmldb_field->setName($tempcolname);
        /// Create the temporal column
            $results = array_merge($results, $this->getAddFieldSQL($xmldb_table, $xmldb_field));
        /// Detect some basic casting options
            if ((substr($oldmetatype, 0, 1) == 'C' && $xmldb_field->getType() == XMLDB_TYPE_NUMBER) ||
                (substr($oldmetatype, 0, 1) == 'C' && $xmldb_field->getType() == XMLDB_TYPE_FLOAT)) {
                $copyorigin = 'CAST(CAST('.$fieldname.' AS TEXT) AS REAL)'; //From char to number or float
            } else if ((substr($oldmetatype, 0, 1) == 'C' && $xmldb_field->getType() == XMLDB_TYPE_INTEGER)) {
                $copyorigin = 'CAST(CAST('.$fieldname.' AS TEXT) AS INTEGER)'; //From char to integer
            } else {
                $copyorigin = $fieldname; //Direct copy between columns
            }
        /// Copy contents from original col to the temporal one
            $results[] = 'UPDATE ' . $tablename . ' SET ' . $tempcolname . ' = ' . $copyorigin;
        /// Drop the old column
            $xmldb_field->setName($fieldname); //Set back the original field name
            $results = array_merge($results, $this->getDropFieldSQL($xmldb_table, $xmldb_field));
        /// Rename the temp column to the original one
            $results[] = 'ALTER TABLE ' . $tablename . ' RENAME COLUMN ' . $tempcolname . ' TO ' . $fieldname;
        /// Mark we have performed one change based in temp fields
            $from_temp_fields = true;
        }
    /// If the default has changed or we have used one temp field
        if ($defaultchanged || $from_temp_fields) {
            if ($default_clause = $this->getDefaultClause($xmldb_field)) {
                $results[] = 'ALTER TABLE ' . $tablename . ' ALTER COLUMN ' . $fieldname . ' SET' . $default_clause; /// Add default clause
            } else {
                if (!$from_temp_fields) { /// Only drop default if we haven't used the temp field, i.e. old column
                    $results[] = 'ALTER TABLE ' . $tablename . ' ALTER COLUMN ' . $fieldname . ' DROP DEFAULT'; /// Drop default clause
                }
            }
        }
    /// If the not null has changed or we have used one temp field
        if ($notnullchanged || $from_temp_fields) {
            if ($xmldb_field->getNotnull()) {
                $results[] = 'ALTER TABLE ' . $tablename . ' ALTER COLUMN ' . $fieldname . ' SET NOT NULL';
            } else {
                $results[] = 'ALTER TABLE ' . $tablename . ' ALTER COLUMN ' . $fieldname . ' DROP NOT NULL';
            }
        }

    /// Return the results 
        return $results;
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
    /// Just a wrapper over the getAlterFieldSQL() function for PostgreSQL that
    /// is capable of handling defaults
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**
     * Given one XMLDBTable and one XMLDBField, return the SQL statements needded to drop its default 
     * (usually invoked from getModifyDefaultSQL()
     */
    function getDropDefaultSQL($xmldb_table, $xmldb_field) {
    /// Just a wrapper over the getAlterFieldSQL() function for PostgreSQL that
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

        $tablename = $this->getTableName($xmldb_table);

        if ($constraints = get_records_sql("SELECT co.conname AS name, co.consrc AS description
                                              FROM pg_constraint co,
                                                   pg_class cl
                                             WHERE co.conrelid = cl.oid
                                               AND co.contype = 'c'
                                               AND cl.relname = '{$tablename}'")) {
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
                $description = preg_replace('/\("(.*?)"\)/', '($1)', $result->description);// Double quotes out
                $description = preg_replace('/[\(\)]/', '', $description);                 // Parenthesis out
                $description = preg_replace('/::[a-z]+/i', '', $description);              // Casts out
                $description = preg_replace("/({$filter})/i", '@$1@', $description);
                $description = trim(preg_replace('/ or /i', ' OR ', $description));        // Uppercase or & trim
            /// description starts by @$filter@ assume it's a constraint beloging to the field
                if (preg_match("/^@{$filter}@/i", $description)) {
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
 * The sequence name for Postgres has one standard name convention: 
 *     tablename_fieldname_seq
 * so we just calculate it and confirm it's present in pg_class
 * If no sequence is found, returns false
 */
function getSequenceFromDB($xmldb_table) {

    $tablename = $this->getTableName($xmldb_table);
    $sequencename = $tablename . '_id_seq';

    if (!get_record_sql("SELECT *
                     FROM pg_class
                     WHERE relname = '{$sequencename}'
                       AND relkind = 'S'")) {
        $sequencename = false;
    }

    return $sequencename;
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
                if ($check = get_records_sql("SELECT relname 
                                              FROM pg_class 
                                              WHERE lower(relname) = '" . strtolower($object_name) . "'")) {
                    return true;
                }
                break;
            case 'pk':
            case 'uk':
            case 'fk':
            case 'ck':
                if ($check = get_records_sql("SELECT conname 
                                              FROM pg_constraint
                                              WHERE lower(conname) = '" . strtolower($object_name) . "'")) {
                    return true;
                }
                break;
            case 'trg':
                if ($check = get_records_sql("SELECT tgname 
                                              FROM pg_trigger
                                              WHERE lower(tgname) = '" . strtolower($object_name) . "'")) {
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
            'outer', 'overlaps', 'placing', 'primary', 'references', 'returning', 'right', 'select',
            'session_user', 'similar', 'some', 'symmetric', 'table', 'then', 'to',
            'trailing', 'true', 'union', 'unique', 'user', 'using', 'verbose',
            'when', 'where', 'with'
        );
        return $reserved_words;
    }
}

?>
