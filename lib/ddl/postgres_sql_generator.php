<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * PostgreSQL specific SQL code generator.
 *
 * @package    core
 * @subpackage ddl
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/ddl/sql_generator.php');

/// This class generate SQL code to be used against PostgreSQL
/// It extends XMLDBgenerator so everything can be
/// overridden as needed to generate correct SQL.

class postgres_sql_generator extends sql_generator {

/// Only set values that are different from the defaults present in XMLDBgenerator

    public $number_type = 'NUMERIC';    // Proper type for NUMBER(x) in this DB

    public $unsigned_allowed = false;    // To define in the generator must handle unsigned information
    public $default_for_char = '';      // To define the default to set for NOT NULLs CHARs without default (null=do nothing)

    public $sequence_extra_code = false; //Does the generator need to add extra code to generate the sequence fields
    public $sequence_name = 'BIGSERIAL'; //Particular name for inline sequences in this generator
    public $sequence_name_small = 'SERIAL'; //Particular name for inline sequences in this generator
    public $sequence_only = true; //To avoid to output the rest of the field specs, leaving only the name and the sequence_name variable

    public $enum_inline_code = false; //Does the generator need to add inline code in the column definition

    public $rename_index_sql = 'ALTER TABLE OLDINDEXNAME RENAME TO NEWINDEXNAME'; //SQL sentence to rename one index
                                      //TABLENAME, OLDINDEXNAME, NEWINDEXNAME are dynamically replaced

    public $rename_key_sql = null; //SQL sentence to rename one key (PostgreSQL doesn't support this!)
                                          //TABLENAME, OLDKEYNAME, NEWKEYNAME are dynamically replaced

    protected $std_strings = null;  // '' or \' quotes

    /**
     * Reset a sequence to the id field of a table.
     * @param string $table name of table or xmldb_table object
     * @return array sql commands to execute
     */
    public function getResetSequenceSQL($table) {

        if ($table instanceof xmldb_table) {
            $tablename = $table->getName();
        } else {
            $tablename = $table;
        }

        // From http://www.postgresql.org/docs/7.4/static/sql-altersequence.html
        $value = (int)$this->mdb->get_field_sql('SELECT MAX(id) FROM {'.$tablename.'}');
        $value++;
        return array("ALTER SEQUENCE $this->prefix{$tablename}_id_seq RESTART WITH $value");
    }

    /**
     * Given one correct xmldb_table, returns the SQL statements
     * to create temporary table (inside one array)
     */
    public function getCreateTempTableSQL($xmldb_table) {
        $this->temptables->add_temptable($xmldb_table->getName());
        $sqlarr = $this->getCreateTableSQL($xmldb_table);
        $sqlarr = preg_replace('/^CREATE TABLE/', "CREATE TEMPORARY TABLE", $sqlarr);
        return $sqlarr;
    }

    /**
     * Given one correct xmldb_table and the new name, returns the SQL statements
     * to drop it (inside one array)
     */
    public function getDropTempTableSQL($xmldb_table) {
        $sqlarr = $this->getDropTableSQL($xmldb_table);
        $this->temptables->delete_temptable($xmldb_table->getName());
        return $sqlarr;
    }

    /**
     * Given one XMLDB Type, length and decimals, returns the DB proper SQL type
     */
    public function getTypeSQL($xmldb_type, $xmldb_length=null, $xmldb_decimals=null) {

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
     * Returns the code (in array) needed to add one comment to the table
     */
    function getCommentSQL ($xmldb_table) {

        $comment = "COMMENT ON TABLE " . $this->getTableName($xmldb_table);
        $comment.= " IS '" . $this->addslashes(substr($xmldb_table->getComment(), 0, 250)) . "'";

        return array($comment);
    }

    /**
     * Returns the code (array of statements) needed to execute extra statements on table rename
     */
    public function getRenameTableExtraSQL($xmldb_table, $newname) {

        $results = array();

        $newt = new xmldb_table($newname);

        $xmldb_field = new xmldb_field('id'); // Fields having sequences should be exclusively, id.

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
            }
         }

        return $results;
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to alter the field in the table
     * PostgreSQL has some severe limits:
     *     - Any change of type or precision requires a new temporary column to be created, values to
     *       be transfered potentially casting them, to apply defaults if the column is not null and
     *       finally, to rename it
     *     - Changes in null/not null require the SET/DROP NOT NULL clause
     *     - Changes in default require the SET/DROP DEFAULT clause
     */
    public function getAlterFieldSQL($xmldb_table, $xmldb_field, $skip_type_clause = NULL, $skip_default_clause = NULL, $skip_notnull_clause = NULL) {
        $results = array(); /// To store all the needed SQL commands

    /// Get the normla names of the table and field
        $tablename = $xmldb_table->getName();
        $fieldname = $xmldb_field->getName();

    /// Take a look to field metadata
        $meta = $this->mdb->get_columns($tablename);
        $metac = $meta[$xmldb_field->getName()];
        $oldmetatype = $metac->meta_type;
        $oldlength = $metac->max_length;
        $olddecimals = empty($metac->scale) ? null : $metac->scale;
        $oldnotnull = empty($metac->not_null) ? false : $metac->not_null;
        $olddefault = empty($metac->has_default) ? null : $metac->default_value;

        $typechanged = true;  //By default, assume that the column type has changed
        $precisionchanged = true;  //By default, assume that the column precision has changed
        $decimalchanged = true;  //By default, assume that the column decimal has changed
        $defaultchanged = true;  //By default, assume that the column default has changed
        $notnullchanged = true;  //By default, assume that the column notnull has changed

    /// Detect if we are changing the type of the column
        if (($xmldb_field->getType() == XMLDB_TYPE_INTEGER && $oldmetatype == 'I') ||
            ($xmldb_field->getType() == XMLDB_TYPE_NUMBER  && $oldmetatype == 'N') ||
            ($xmldb_field->getType() == XMLDB_TYPE_FLOAT   && $oldmetatype == 'F') ||
            ($xmldb_field->getType() == XMLDB_TYPE_CHAR    && $oldmetatype == 'C') ||
            ($xmldb_field->getType() == XMLDB_TYPE_TEXT    && $oldmetatype == 'X') ||
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
            ($xmldb_field->getDefault() === $olddefault)) {
            $defaultchanged = false;
        }
    /// Detect if we are changing the nullability
        if (($xmldb_field->getNotnull() === $oldnotnull)) {
            $notnullchanged = false;
        }

    /// Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);
        $fieldname = $this->getEncQuoted($xmldb_field->getName());

    /// Decide if we have changed the column specs (type/precision/decimals)
        $specschanged = $typechanged || $precisionchanged || $decimalchanged;

    /// if specs have changed, need to alter column
        if ($specschanged) {
        /// Always drop any exiting default before alter column (some type changes can cause casting error in default for column)
            if ($olddefault !== null) {
                $results[] = 'ALTER TABLE ' . $tablename . ' ALTER COLUMN ' . $fieldname . ' DROP DEFAULT'; /// Drop default clause
            }
            $alterstmt = 'ALTER TABLE ' . $tablename . ' ALTER COLUMN ' . $this->getEncQuoted($xmldb_field->getName()) .
                         ' TYPE' . $this->getFieldSQL($xmldb_field, null, true, true, null, false);
        /// Some castings must be performed explicity (mainly from text|char to numeric|integer)
            if (($oldmetatype == 'C' || $oldmetatype == 'X') &&
                ($xmldb_field->getType() == XMLDB_TYPE_NUMBER || $xmldb_field->getType() == XMLDB_TYPE_FLOAT)) {
                $alterstmt .= ' USING CAST('.$fieldname.' AS NUMERIC)'; // from char or text to number or float
            } else if (($oldmetatype == 'C' || $oldmetatype == 'X') &&
                $xmldb_field->getType() == XMLDB_TYPE_INTEGER) {
                $alterstmt .= ' USING CAST(CAST('.$fieldname.' AS NUMERIC) AS INTEGER)'; // From char to integer
            }
            $results[] = $alterstmt;
        }

    /// If the default has changed or we have performed one change in specs
        if ($defaultchanged || $specschanged) {
            $default_clause = $this->getDefaultClause($xmldb_field);
            if ($default_clause) {
                $sql = 'ALTER TABLE ' . $tablename . ' ALTER COLUMN ' . $fieldname . ' SET' . $default_clause; /// Add default clause
                $results[] = $sql;
            } else {
                if (!$specschanged) { /// Only drop default if we haven't performed one specs change
                    $results[] = 'ALTER TABLE ' . $tablename . ' ALTER COLUMN ' . $fieldname . ' DROP DEFAULT'; /// Drop default clause
                }
            }
        }

    /// If the not null has changed
        if ($notnullchanged) {
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
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to drop its enum
     * (usually invoked from getModifyEnumSQL()
     *
     * TODO: Moodle 2.1 - drop in Moodle 2.1
     */
    public function getDropEnumSQL($xmldb_table, $xmldb_field) {
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
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to create its default
     * (usually invoked from getModifyDefaultSQL()
     */
    public function getCreateDefaultSQL($xmldb_table, $xmldb_field) {
    /// Just a wrapper over the getAlterFieldSQL() function for PostgreSQL that
    /// is capable of handling defaults
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to drop its default
     * (usually invoked from getModifyDefaultSQL()
     */
    public function getDropDefaultSQL($xmldb_table, $xmldb_field) {
    /// Just a wrapper over the getAlterFieldSQL() function for PostgreSQL that
    /// is capable of handling defaults
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**
     * Given one xmldb_table returns one array with all the check constraints
     * in the table (fetched from DB)
     * Optionally the function allows one xmldb_field to be specified in
     * order to return only the check constraints belonging to one field.
     * Each element contains the name of the constraint and its description
     * If no check constraints are found, returns an empty array
     *
     * TODO: Moodle 2.1 - drop in Moodle 2.1
     */
    public function getCheckConstraintsFromDB($xmldb_table, $xmldb_field = null) {

        $results = array();

        $tablename = $this->getTableName($xmldb_table);

        if ($constraints = $this->mdb->get_records_sql("SELECT co.conname AS name, co.consrc AS description
                                                          FROM pg_constraint co, pg_class cl
                                                         WHERE co.conrelid = cl.oid
                                                               AND co.contype = 'c' AND cl.relname = ?",
                                                       array($tablename))) {
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
            /// description starts by @$filter@ assume it's a constraint belonging to the field
                if (preg_match("/^@{$filter}@/i", $description)) {
                    $filtered_results[$key] = $result;
                }
            }
        /// Assign filtered results to the final results array
            $results =  $filtered_results;
        }

        return $results;
    }

    public function addslashes($s) {
        // Postgres is gradually switching to ANSI quotes, we need to check what is expected
        if (!isset($this->std_strings)) {
            $this->std_strings = ($this->mdb->get_field_sql("select setting from pg_settings where name = 'standard_conforming_strings'") === 'on');
        }

        if ($this->std_strings) {
            $s = str_replace("'",  "''", $s);
        } else {
            // do not use php addslashes() because it depends on PHP quote settings!
            $s = str_replace('\\','\\\\',$s);
            $s = str_replace("\0","\\\0", $s);
            $s = str_replace("'",  "\\'", $s);
        }

        return $s;
    }

/**
 * Given one xmldb_table returns one string with the sequence of the table
 * in the table (fetched from DB)
 * The sequence name for Postgres has one standard name convention:
 *     tablename_fieldname_seq
 * so we just calculate it and confirm it's present in pg_class
 * If no sequence is found, returns false
 */
function getSequenceFromDB($xmldb_table) {

    $tablename = $this->getTableName($xmldb_table);
    $sequencename = $tablename . '_id_seq';

    if (!$this->mdb->get_record_sql("SELECT *
                                       FROM pg_class
                                      WHERE relname = ? AND relkind = 'S'",
                                    array($sequencename))) {
        $sequencename = false;
    }

    return $sequencename;
}

    /**
     * Given one object name and it's type (pk, uk, fk, ck, ix, uix, seq, trg)
     * return if such name is currently in use (true) or no (false)
     * (invoked from getNameForObject()
     */
    public function isNameInUse($object_name, $type, $table_name) {
        switch($type) {
            case 'ix':
            case 'uix':
            case 'seq':
                if ($check = $this->mdb->get_records_sql("SELECT relname
                                                            FROM pg_class
                                                           WHERE lower(relname) = ?", array(strtolower($object_name)))) {
                    return true;
                }
                break;
            case 'pk':
            case 'uk':
            case 'fk':
            case 'ck':
                if ($check = $this->mdb->get_records_sql("SELECT conname
                                                            FROM pg_constraint
                                                           WHERE lower(conname) = ?", array(strtolower($object_name)))) {
                    return true;
                }
                break;
            case 'trg':
                if ($check = $this->mdb->get_records_sql("SELECT tgname
                                                            FROM pg_trigger
                                                           WHERE lower(tgname) = ?", array(strtolower($object_name)))) {
                    return true;
                }
                break;
        }
        return false; //No name in use found
    }

    /**
     * Returns an array of reserved words (lowercase) for this DB
     */
    public static function getReservedWords() {
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
