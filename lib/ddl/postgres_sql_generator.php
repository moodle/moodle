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
 * @package    core_ddl
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/ddl/sql_generator.php');

/**
 * This class generate SQL code to be used against PostgreSQL
 * It extends XMLDBgenerator so everything can be
 * overridden as needed to generate correct SQL.
 *
 * @package    core_ddl
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class postgres_sql_generator extends sql_generator {

    // Only set values that are different from the defaults present in XMLDBgenerator

    /** @var string Proper type for NUMBER(x) in this DB. */
    public $number_type = 'NUMERIC';

    /** @var string To define the default to set for NOT NULLs CHARs without default (null=do nothing).*/
    public $default_for_char = '';

    /** @var bool True if the generator needs to add extra code to generate the sequence fields.*/
    public $sequence_extra_code = false;

    /** @var string The particular name for inline sequences in this generator.*/
    public $sequence_name = 'BIGSERIAL';

    /** @var string The particular name for inline sequences in this generator.*/
    public $sequence_name_small = 'SERIAL';

    /** @var bool To avoid outputting the rest of the field specs, leaving only the name and the sequence_name returned.*/
    public $sequence_only = true;

    /** @var string SQL sentence to rename one index where 'TABLENAME', 'OLDINDEXNAME' and 'NEWINDEXNAME' are dynamically replaced.*/
    public $rename_index_sql = 'ALTER TABLE OLDINDEXNAME RENAME TO NEWINDEXNAME';

    /** @var string SQL sentence to rename one key 'TABLENAME', 'OLDKEYNAME' and 'NEWKEYNAME' are dynamically replaced.*/
    public $rename_key_sql = null;

    /** @var string type of string quoting used - '' or \' quotes*/
    protected $std_strings = null;

    /**
     * Reset a sequence to the id field of a table.
     *
     * @param xmldb_table|string $table name of table or the table object.
     * @return array of sql statements
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
     * to create temporary table (inside one array).
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @return array of sql statements
     */
    public function getCreateTempTableSQL($xmldb_table) {
        $this->temptables->add_temptable($xmldb_table->getName());
        $sqlarr = $this->getCreateTableSQL($xmldb_table);
        $sqlarr = preg_replace('/^CREATE TABLE/', "CREATE TEMPORARY TABLE", $sqlarr);
        return $sqlarr;
    }

    /**
     * Given one correct xmldb_table, returns the SQL statements
     * to drop it (inside one array).
     *
     * @param xmldb_table $xmldb_table The table to drop.
     * @return array SQL statement(s) for dropping the specified table.
     */
    public function getDropTableSQL($xmldb_table) {
        $sqlarr = parent::getDropTableSQL($xmldb_table);
        if ($this->temptables->is_temptable($xmldb_table->getName())) {
            $this->temptables->delete_temptable($xmldb_table->getName());
        }
        return $sqlarr;
    }

    /**
     * Given one correct xmldb_index, returns the SQL statements
     * needed to create it (in array).
     *
     * @param xmldb_table $xmldb_table The xmldb_table instance to create the index on.
     * @param xmldb_index $xmldb_index The xmldb_index to create.
     * @return array An array of SQL statements to create the index.
     * @throws coding_exception Thrown if the xmldb_index does not validate with the xmldb_table.
     */
    public function getCreateIndexSQL($xmldb_table, $xmldb_index) {
        $sqls = parent::getCreateIndexSQL($xmldb_table, $xmldb_index);

        $hints = $xmldb_index->getHints();
        $fields = $xmldb_index->getFields();
        if (in_array('varchar_pattern_ops', $hints) and count($fields) == 1) {
            // Add the pattern index and keep the normal one, keep unique only the standard index to improve perf.
            foreach ($sqls as $sql) {
                $field = reset($fields);
                $count = 0;
                $newindex = preg_replace("/^CREATE( UNIQUE)? INDEX ([a-z0-9_]+) ON ([a-z0-9_]+) \($field\)$/", "CREATE INDEX \\2_pattern ON \\3 USING btree ($field varchar_pattern_ops)", $sql, -1, $count);
                if ($count != 1) {
                    debugging('Unexpected getCreateIndexSQL() structure.');
                    continue;
                }
                $sqls[] = $newindex;
            }
        }

        return $sqls;
    }

    /**
     * Given one XMLDB Type, length and decimals, returns the DB proper SQL type.
     *
     * @param int $xmldb_type The xmldb_type defined constant. XMLDB_TYPE_INTEGER and other XMLDB_TYPE_* constants.
     * @param int $xmldb_length The length of that data type.
     * @param int $xmldb_decimals The decimal places of precision of the data type.
     * @return string The DB defined data type.
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
     * Returns the code (array of statements) needed to add one comment to the table.
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @return array Array of SQL statements to add one comment to the table.
     */
    function getCommentSQL ($xmldb_table) {

        $comment = "COMMENT ON TABLE " . $this->getTableName($xmldb_table);
        $comment.= " IS '" . $this->addslashes(substr($xmldb_table->getComment(), 0, 250)) . "'";

        return array($comment);
    }

    /**
     * Returns the code (array of statements) needed to execute extra statements on table rename.
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @param string $newname The new name for the table.
     * @return array Array of extra SQL statements to rename a table.
     */
    public function getRenameTableExtraSQL($xmldb_table, $newname) {

        $results = array();

        $newt = new xmldb_table($newname);

        $xmldb_field = new xmldb_field('id'); // Fields having sequences should be exclusively, id.

        $oldseqname = $this->getTableName($xmldb_table) . '_' . $xmldb_field->getName() . '_seq';
        $newseqname = $this->getTableName($newt) . '_' . $xmldb_field->getName() . '_seq';

        // Rename de sequence
        $results[] = 'ALTER TABLE ' . $oldseqname . ' RENAME TO ' . $newseqname;

        return $results;
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to alter the field in the table.
     *
     * PostgreSQL has some severe limits:
     *     - Any change of type or precision requires a new temporary column to be created, values to
     *       be transfered potentially casting them, to apply defaults if the column is not null and
     *       finally, to rename it
     *     - Changes in null/not null require the SET/DROP NOT NULL clause
     *     - Changes in default require the SET/DROP DEFAULT clause
     *
     * @param xmldb_table $xmldb_table The table related to $xmldb_field.
     * @param xmldb_field $xmldb_field The instance of xmldb_field to create the SQL from.
     * @param string $skip_type_clause The type clause on alter columns, NULL by default.
     * @param string $skip_default_clause The default clause on alter columns, NULL by default.
     * @param string $skip_notnull_clause The null/notnull clause on alter columns, NULL by default.
     * @return string The field altering SQL statement.
     */
    public function getAlterFieldSQL($xmldb_table, $xmldb_field, $skip_type_clause = NULL, $skip_default_clause = NULL, $skip_notnull_clause = NULL) {
        $results = array();     // To store all the needed SQL commands

        // Get the normal names of the table and field
        $tablename = $xmldb_table->getName();
        $fieldname = $xmldb_field->getName();

        // Take a look to field metadata
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

        // Detect if we are changing the type of the column
        if (($xmldb_field->getType() == XMLDB_TYPE_INTEGER && $oldmetatype == 'I') ||
            ($xmldb_field->getType() == XMLDB_TYPE_NUMBER  && $oldmetatype == 'N') ||
            ($xmldb_field->getType() == XMLDB_TYPE_FLOAT   && $oldmetatype == 'F') ||
            ($xmldb_field->getType() == XMLDB_TYPE_CHAR    && $oldmetatype == 'C') ||
            ($xmldb_field->getType() == XMLDB_TYPE_TEXT    && $oldmetatype == 'X') ||
            ($xmldb_field->getType() == XMLDB_TYPE_BINARY  && $oldmetatype == 'B')) {
            $typechanged = false;
        }
        // Detect if we are changing the precision
        if (($xmldb_field->getType() == XMLDB_TYPE_TEXT) ||
            ($xmldb_field->getType() == XMLDB_TYPE_BINARY) ||
            ($oldlength == -1) ||
            ($xmldb_field->getLength() == $oldlength)) {
            $precisionchanged = false;
        }
        // Detect if we are changing the decimals
        if (($xmldb_field->getType() == XMLDB_TYPE_INTEGER) ||
            ($xmldb_field->getType() == XMLDB_TYPE_CHAR) ||
            ($xmldb_field->getType() == XMLDB_TYPE_TEXT) ||
            ($xmldb_field->getType() == XMLDB_TYPE_BINARY) ||
            (!$xmldb_field->getDecimals()) ||
            (!$olddecimals) ||
            ($xmldb_field->getDecimals() == $olddecimals)) {
            $decimalchanged = false;
        }
        // Detect if we are changing the default
        if (($xmldb_field->getDefault() === null && $olddefault === null) ||
            ($xmldb_field->getDefault() === $olddefault)) {
            $defaultchanged = false;
        }
        // Detect if we are changing the nullability
        if (($xmldb_field->getNotnull() === $oldnotnull)) {
            $notnullchanged = false;
        }

        // Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);
        $fieldname = $this->getEncQuoted($xmldb_field->getName());

        // Decide if we have changed the column specs (type/precision/decimals)
        $specschanged = $typechanged || $precisionchanged || $decimalchanged;

        // if specs have changed, need to alter column
        if ($specschanged) {
            // Always drop any exiting default before alter column (some type changes can cause casting error in default for column)
            if ($olddefault !== null) {
                $results[] = 'ALTER TABLE ' . $tablename . ' ALTER COLUMN ' . $fieldname . ' DROP DEFAULT';     // Drop default clause
            }
            $alterstmt = 'ALTER TABLE ' . $tablename . ' ALTER COLUMN ' . $this->getEncQuoted($xmldb_field->getName()) .
                ' TYPE' . $this->getFieldSQL($xmldb_table, $xmldb_field, null, true, true, null, false);
            // Some castings must be performed explicitly (mainly from text|char to numeric|integer)
            if (($oldmetatype == 'C' || $oldmetatype == 'X') &&
                ($xmldb_field->getType() == XMLDB_TYPE_NUMBER || $xmldb_field->getType() == XMLDB_TYPE_FLOAT)) {
                $alterstmt .= ' USING CAST('.$fieldname.' AS NUMERIC)'; // from char or text to number or float
            } else if (($oldmetatype == 'C' || $oldmetatype == 'X') &&
                $xmldb_field->getType() == XMLDB_TYPE_INTEGER) {
                $alterstmt .= ' USING CAST(CAST('.$fieldname.' AS NUMERIC) AS INTEGER)'; // From char to integer
            }
            $results[] = $alterstmt;
        }

        // If the default has changed or we have performed one change in specs
        if ($defaultchanged || $specschanged) {
            $default_clause = $this->getDefaultClause($xmldb_field);
            if ($default_clause) {
                $sql = 'ALTER TABLE ' . $tablename . ' ALTER COLUMN ' . $fieldname . ' SET' . $default_clause;     // Add default clause
                $results[] = $sql;
            } else {
                if (!$specschanged) {     // Only drop default if we haven't performed one specs change
                    $results[] = 'ALTER TABLE ' . $tablename . ' ALTER COLUMN ' . $fieldname . ' DROP DEFAULT';     // Drop default clause
                }
            }
        }

        // If the not null has changed
        if ($notnullchanged) {
            if ($xmldb_field->getNotnull()) {
                $results[] = 'ALTER TABLE ' . $tablename . ' ALTER COLUMN ' . $fieldname . ' SET NOT NULL';
            } else {
                $results[] = 'ALTER TABLE ' . $tablename . ' ALTER COLUMN ' . $fieldname . ' DROP NOT NULL';
            }
        }

        // Return the results
        return $results;
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to add its default
     * (usually invoked from getModifyDefaultSQL()
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @param xmldb_field $xmldb_field The xmldb_field object instance.
     * @return array Array of SQL statements to create a field's default.
     */
    public function getCreateDefaultSQL($xmldb_table, $xmldb_field) {
        // Just a wrapper over the getAlterFieldSQL() function for PostgreSQL that
        // is capable of handling defaults
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to drop its default
     * (usually invoked from getModifyDefaultSQL()
     *
     * Note that this method may be dropped in future.
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @param xmldb_field $xmldb_field The xmldb_field object instance.
     * @return array Array of SQL statements to create a field's default.
     *
     * @todo MDL-31147 Moodle 2.1 - Drop getDropDefaultSQL()
     */
    public function getDropDefaultSQL($xmldb_table, $xmldb_field) {
        // Just a wrapper over the getAlterFieldSQL() function for PostgreSQL that
        // is capable of handling defaults
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**
     * Adds slashes to string.
     * @param string $s
     * @return string The escaped string.
     */
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
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @return string|bool If no sequence is found, returns false
     */
    function getSequenceFromDB($xmldb_table) {

        $tablename = $this->getTableName($xmldb_table);
        $sequencename = $tablename . '_id_seq';

        if (!$this->mdb->get_record_sql("SELECT c.*
                                           FROM pg_catalog.pg_class c
                                           JOIN pg_catalog.pg_namespace as ns ON ns.oid = c.relnamespace
                                          WHERE c.relname = ? AND c.relkind = 'S'
                                                AND (ns.nspname = current_schema() OR ns.oid = pg_my_temp_schema())",
            array($sequencename))) {
            $sequencename = false;
        }

        return $sequencename;
    }

    /**
     * Given one object name and it's type (pk, uk, fk, ck, ix, uix, seq, trg).
     *
     * (MySQL requires the whole xmldb_table object to be specified, so we add it always)
     *
     * This is invoked from getNameForObject().
     * Only some DB have this implemented.
     *
     * @param string $object_name The object's name to check for.
     * @param string $type The object's type (pk, uk, fk, ck, ix, uix, seq, trg).
     * @param string $table_name The table's name to check in
     * @return bool If such name is currently in use (true) or no (false)
     */
    public function isNameInUse($object_name, $type, $table_name) {
        switch($type) {
            case 'ix':
            case 'uix':
            case 'seq':
                if ($check = $this->mdb->get_records_sql("SELECT c.relname
                                                            FROM pg_class c
                                                            JOIN pg_catalog.pg_namespace as ns ON ns.oid = c.relnamespace
                                                           WHERE lower(c.relname) = ?
                                                                 AND (ns.nspname = current_schema() OR ns.oid = pg_my_temp_schema())", array(strtolower($object_name)))) {
                    return true;
                }
                break;
            case 'pk':
            case 'uk':
            case 'fk':
            case 'ck':
                if ($check = $this->mdb->get_records_sql("SELECT c.conname
                                                            FROM pg_constraint c
                                                            JOIN pg_catalog.pg_namespace as ns ON ns.oid = c.connamespace
                                                           WHERE lower(c.conname) = ?
                                                                 AND (ns.nspname = current_schema() OR ns.oid = pg_my_temp_schema())", array(strtolower($object_name)))) {
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
     * @return array An array of database specific reserved words
     */
    public static function getReservedWords() {
        // This file contains the reserved words for PostgreSQL databases
                // This file contains the reserved words for PostgreSQL databases
        // http://www.postgresql.org/docs/current/static/sql-keywords-appendix.html
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
