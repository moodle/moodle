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
 * Oracle specific SQL code generator.
 *
 * @package    core_ddl
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/ddl/sql_generator.php');

/**
 * This class generate SQL code to be used against Oracle
 * It extends XMLDBgenerator so everything can be
 * overridden as needed to generate correct SQL.
 *
 * @package    core_ddl
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class oracle_sql_generator extends sql_generator {

    // Only set values that are different from the defaults present in XMLDBgenerator

    /**
     * @var string To be automatically added at the end of each statement.
     * note: Using "/" because the standard ";" isn't good for stored procedures (triggers)
     */
    public $statement_end = "\n/";

    /** @var string Proper type for NUMBER(x) in this DB. */
    public $number_type = 'NUMBER';

    /**
     * @var string To define the default to set for NOT NULLs CHARs without default (null=do nothing).
     * note: Using this whitespace here because Oracle doesn't distinguish empty and null! :-(
     */
    public $default_for_char = ' ';

    /** @var bool To specify if the generator must use some DEFAULT clause to drop defaults.*/
    public $drop_default_value_required = true;

    /** @var string The DEFAULT clause required to drop defaults.*/
    public $drop_default_value = null;

    /** @var bool To decide if the default clause of each field must go after the null clause.*/
    public $default_after_null = false;

    /** @var bool True if the generator needs to add extra code to generate the sequence fields.*/
    public $sequence_extra_code = true;

    /** @var string The particular name for inline sequences in this generator.*/
    public $sequence_name = '';

    /** @var string The SQL template to alter columns where the 'TABLENAME' and 'COLUMNSPECS' keywords are dynamically replaced.*/
    public $alter_column_sql = 'ALTER TABLE TABLENAME MODIFY (COLUMNSPECS)';

    /** @var int var ugly Oracle hack - size of the sequences values cache (20 = Default)*/
    public $sequence_cache_size = 20;

    /**
     * Reset a sequence to the id field of a table.
     *
     * @param xmldb_table|string $table name of table or the table object.
     * @return array of sql statements
     */
    public function getResetSequenceSQL($table) {

        if (is_string($table)) {
            $tablename = $table;
            $xmldb_table = new xmldb_table($tablename);
        } else {
            $tablename = $table->getName();
            $xmldb_table = $table;
        }
        // From http://www.acs.ilstu.edu/docs/oracle/server.101/b10759/statements_2011.htm
        $value = (int)$this->mdb->get_field_sql('SELECT MAX(id) FROM {'.$tablename.'}');
        $value++;

        $seqname = $this->getSequenceFromDB($xmldb_table);

        if (!$seqname) {
            // Fallback, seqname not found, something is wrong. Inform and use the alternative getNameForObject() method
            $seqname = $this->getNameForObject($table, 'id', 'seq');
        }

        return array ("DROP SEQUENCE $seqname",
                      "CREATE SEQUENCE $seqname START WITH $value INCREMENT BY 1 NOMAXVALUE CACHE $this->sequence_cache_size");
    }

    /**
     * Given one xmldb_table, returns it's correct name, depending of all the parametrization
     * Overridden to allow change of names in temp tables
     *
     * @param xmldb_table table whose name we want
     * @param boolean to specify if the name must be quoted (if reserved word, only!)
     * @return string the correct name of the table
     */
    public function getTableName(xmldb_table $xmldb_table, $quoted=true) {
        // Get the name, supporting special oci names for temp tables
        if ($this->temptables->is_temptable($xmldb_table->getName())) {
            $tablename = $this->temptables->get_correct_name($xmldb_table->getName());
        } else {
            $tablename = $this->prefix . $xmldb_table->getName();
        }

        // Apply quotes optionally
        if ($quoted) {
            $tablename = $this->getEncQuoted($tablename);
        }

        return $tablename;
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
        $sqlarr = preg_replace('/^CREATE TABLE (.*)/s', 'CREATE GLOBAL TEMPORARY TABLE $1 ON COMMIT PRESERVE ROWS', $sqlarr);
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
            array_unshift($sqlarr, "TRUNCATE TABLE ". $this->getTableName($xmldb_table)); // oracle requires truncate before being able to drop a temp table
            $this->temptables->delete_temptable($xmldb_table->getName());
        }
        return $sqlarr;
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
            case XMLDB_TYPE_INTEGER:    // See http://www.acs.ilstu.edu/docs/oracle/server.101/b10759/sql_elements001.htm#sthref86.
                if (empty($xmldb_length)) {
                    $xmldb_length = 10;
                }
                $dbtype = 'NUMBER(' .  $xmldb_length . ')';
                break;
            case XMLDB_TYPE_FLOAT:
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
            case XMLDB_TYPE_CHAR:
                // Do not use NVARCHAR2 here because it has hardcoded 1333 char limit,
                // VARCHAR2 allows us to create larger fields that error out later during runtime
                // only when too many non-ascii utf-8 chars present.
                $dbtype = 'VARCHAR2';
                if (empty($xmldb_length)) {
                    $xmldb_length='255';
                }
                $dbtype .= '(' . $xmldb_length . ' CHAR)'; // CHAR is required because BYTE is the default
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
     * Returns the code (array of statements) needed
     * to create one sequence for the xmldb_table and xmldb_field passed in.
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @param xmldb_field $xmldb_field The xmldb_field object instance.
     * @return array Array of SQL statements to create the sequence.
     */
    public function getCreateSequenceSQL($xmldb_table, $xmldb_field) {

        $results = array();

        $sequence_name = $this->getNameForObject($xmldb_table->getName(), $xmldb_field->getName(), 'seq');

        $sequence = "CREATE SEQUENCE $sequence_name START WITH 1 INCREMENT BY 1 NOMAXVALUE CACHE $this->sequence_cache_size";

        $results[] = $sequence;

        $results = array_merge($results, $this->getCreateTriggerSQL ($xmldb_table, $xmldb_field, $sequence_name));

        return $results;
    }

    /**
     * Returns the code needed to create one trigger for the xmldb_table and xmldb_field passed
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @param xmldb_field $xmldb_field The xmldb_field object instance.
     * @param string $sequence_name
     * @return array Array of SQL statements to create the sequence.
     */
    public function getCreateTriggerSQL($xmldb_table, $xmldb_field, $sequence_name) {

        $trigger_name = $this->getNameForObject($xmldb_table->getName(), $xmldb_field->getName(), 'trg');

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
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @param xmldb_field $xmldb_field The xmldb_field object instance.
     * @param bool $include_trigger
     * @return array Array of SQL statements to create the sequence.
     */
    public function getDropSequenceSQL($xmldb_table, $xmldb_field, $include_trigger=false) {

        $result = array();

        if ($sequence_name = $this->getSequenceFromDB($xmldb_table)) {
            $result[] = "DROP SEQUENCE " . $sequence_name;
        }

        if ($trigger_name = $this->getTriggerFromDB($xmldb_table) && $include_trigger) {
            $result[] = "DROP TRIGGER " . $trigger_name;
        }

        return $result;
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
     * Returns the code (array of statements) needed to execute extra statements on table drop
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @return array Array of extra SQL statements to drop a table.
     */
    public function getDropTableExtraSQL($xmldb_table) {
        $xmldb_field = new xmldb_field('id'); // Fields having sequences should be exclusively, id.
        return $this->getDropSequenceSQL($xmldb_table, $xmldb_field, false);
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

        $xmldb_field = new xmldb_field('id'); // Fields having sequences should be exclusively, id.

        $oldseqname = $this->getSequenceFromDB($xmldb_table);
        $newseqname = $this->getNameForObject($newname, $xmldb_field->getName(), 'seq');

        $oldtriggername = $this->getTriggerFromDB($xmldb_table);
        $newtriggername = $this->getNameForObject($newname, $xmldb_field->getName(), 'trg');

        // Drop old trigger (first of all)
        $results[] = "DROP TRIGGER " . $oldtriggername;

        // Rename the sequence, disablig CACHE before and enablig it later
        // to avoid consuming of values on rename
        $results[] = 'ALTER SEQUENCE ' . $oldseqname . ' NOCACHE';
        $results[] = 'RENAME ' . $oldseqname . ' TO ' . $newseqname;
        $results[] = 'ALTER SEQUENCE ' . $newseqname . ' CACHE ' . $this->sequence_cache_size;

        // Create new trigger
        $newt = new xmldb_table($newname);     // Temp table for trigger code generation
        $results = array_merge($results, $this->getCreateTriggerSQL($newt, $xmldb_field, $newseqname));

        return $results;
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to alter the field in the table.
     *
     * Oracle has some severe limits:
     *     - clob and blob fields doesn't allow type to be specified
     *     - error is dropped if the null/not null clause is specified and hasn't changed
     *     - changes in precision/decimals of numeric fields drop an ORA-1440 error
     *
     * @param xmldb_table $xmldb_table The table related to $xmldb_field.
     * @param xmldb_field $xmldb_field The instance of xmldb_field to create the SQL from.
     * @param string $skip_type_clause The type clause on alter columns, NULL by default.
     * @param string $skip_default_clause The default clause on alter columns, NULL by default.
     * @param string $skip_notnull_clause The null/notnull clause on alter columns, NULL by default.
     * @return string The field altering SQL statement.
     */
    public function getAlterFieldSQL($xmldb_table, $xmldb_field, $skip_type_clause = NULL, $skip_default_clause = NULL, $skip_notnull_clause = NULL) {

        $skip_type_clause = is_null($skip_type_clause) ? $this->alter_column_skip_type : $skip_type_clause;
        $skip_default_clause = is_null($skip_default_clause) ? $this->alter_column_skip_default : $skip_default_clause;
        $skip_notnull_clause = is_null($skip_notnull_clause) ? $this->alter_column_skip_notnull : $skip_notnull_clause;

        $results = array();     // To store all the needed SQL commands

        // Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);
        $fieldname = $xmldb_field->getName();

        // Take a look to field metadata
        $meta = $this->mdb->get_columns($xmldb_table->getName());
        $metac = $meta[$fieldname];
        $oldmetatype = $metac->meta_type;

        $oldlength = $metac->max_length;
        // To calculate the oldlength if the field is numeric, we need to perform one extra query
        // because ADOdb has one bug here. http://phplens.com/lens/lensforum/msgs.php?id=15883
        if ($oldmetatype == 'N') {
            $uppertablename = strtoupper($tablename);
            $upperfieldname = strtoupper($fieldname);
            if ($col = $this->mdb->get_record_sql("SELECT cname, precision
                                                     FROM col
                                                     WHERE tname = ? AND cname = ?",
                                                  array($uppertablename, $upperfieldname))) {
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

        // Detect if we are changing the type of the column
        if (($xmldb_field->getType() == XMLDB_TYPE_INTEGER && $oldmetatype == 'I') ||
            ($xmldb_field->getType() == XMLDB_TYPE_NUMBER  && $oldmetatype == 'N') ||
            ($xmldb_field->getType() == XMLDB_TYPE_FLOAT   && $oldmetatype == 'F') ||
            ($xmldb_field->getType() == XMLDB_TYPE_CHAR    && $oldmetatype == 'C') ||
            ($xmldb_field->getType() == XMLDB_TYPE_TEXT    && $oldmetatype == 'X') ||
            ($xmldb_field->getType() == XMLDB_TYPE_BINARY  && $oldmetatype == 'B')) {
            $typechanged = false;
        }
        // Detect if precision has changed
        if (($xmldb_field->getType() == XMLDB_TYPE_TEXT) ||
            ($xmldb_field->getType() == XMLDB_TYPE_BINARY) ||
            ($oldlength == -1) ||
            ($xmldb_field->getLength() == $oldlength)) {
            $precisionchanged = false;
        }
        // Detect if decimal has changed
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
            ($xmldb_field->getDefault() === $olddefault) ||             //Check both equality and
            ("'" . $xmldb_field->getDefault() . "'" === $olddefault)) {  //Equality with quotes because ADOdb returns the default with quotes
            $defaultchanged = false;
        }

        // Detect if we are changing the nullability
        if (($xmldb_field->getNotnull() === $oldnotnull)) {
            $notnullchanged = false;
        }

        // If type has changed or precision or decimal has changed and we are in one numeric field
        //     - create one temp column with the new specs
        //     - fill the new column with the values from the old one
        //     - drop the old column
        //     - rename the temp column to the original name
        if (($typechanged) || (($oldmetatype == 'N' || $oldmetatype == 'I')  && ($precisionchanged || $decimalchanged))) {
            $tempcolname = $xmldb_field->getName() . '___tmp'; // Short tmp name, surely not conflicting ever
            if (strlen($tempcolname) > 30) { // Safeguard we don't excess the 30cc limit
                $tempcolname = 'ongoing_alter_column_tmp';
            }
            // Prevent temp field to have both NULL/NOT NULL and DEFAULT constraints
            $skip_notnull_clause = true;
            $skip_default_clause = true;
            $xmldb_field->setName($tempcolname);
            // Drop the temp column, in case it exists (due to one previous failure in conversion)
            // really ugly but we cannot enclose DDL into transaction :-(
            if (isset($meta[$tempcolname])) {
                $results = array_merge($results, $this->getDropFieldSQL($xmldb_table, $xmldb_field));
            }
            // Create the temporal column
            $results = array_merge($results, $this->getAddFieldSQL($xmldb_table, $xmldb_field, $skip_type_clause, $skip_type_clause, $skip_notnull_clause));
            // Copy contents from original col to the temporal one

            // From TEXT to integer/number we need explicit conversion
            if ($oldmetatype == 'X' && $xmldb_field->GetType() == XMLDB_TYPE_INTEGER) {
                $results[] = 'UPDATE ' . $tablename . ' SET ' . $tempcolname . ' = CAST(' . $this->mdb->sql_compare_text($fieldname) . ' AS INT)';
            } else if ($oldmetatype == 'X' && $xmldb_field->GetType() == XMLDB_TYPE_NUMBER) {
                $results[] = 'UPDATE ' . $tablename . ' SET ' . $tempcolname . ' = CAST(' . $this->mdb->sql_compare_text($fieldname) . ' AS NUMBER)';

            // Normal cases, implicit conversion
            } else {
                $results[] = 'UPDATE ' . $tablename . ' SET ' . $tempcolname . ' = ' . $fieldname;
            }
            // Drop the old column
            $xmldb_field->setName($fieldname); //Set back the original field name
            $results = array_merge($results, $this->getDropFieldSQL($xmldb_table, $xmldb_field));
            // Rename the temp column to the original one
            $results[] = 'ALTER TABLE ' . $tablename . ' RENAME COLUMN ' . $tempcolname . ' TO ' . $fieldname;
            // Mark we have performed one change based in temp fields
            $from_temp_fields = true;
            // Re-enable the notnull and default sections so the general AlterFieldSQL can use it
            $skip_notnull_clause = false;
            $skip_default_clause = false;
            // Disable the type section because we have done it with the temp field
            $skip_type_clause = true;
            // If new field is nullable, nullability hasn't changed
            if (!$xmldb_field->getNotnull()) {
                $notnullchanged = false;
            }
            // If new field hasn't default, default hasn't changed
            if ($xmldb_field->getDefault() === null) {
                $defaultchanged = false;
            }
        }

        // If type and precision and decimals hasn't changed, prevent the type clause
        if (!$typechanged && !$precisionchanged && !$decimalchanged) {
            $skip_type_clause = true;
        }

        // If NULL/NOT NULL hasn't changed
        // prevent null clause to be specified
        if (!$notnullchanged) {
            $skip_notnull_clause = true;     // Initially, prevent the notnull clause
            // But, if we have used the temp field and the new field is not null, then enforce the not null clause
            if ($from_temp_fields &&  $xmldb_field->getNotnull()) {
                $skip_notnull_clause = false;
            }
        }
        // If default hasn't changed
        // prevent default clause to be specified
        if (!$defaultchanged) {
            $skip_default_clause = true;     // Initially, prevent the default clause
            // But, if we have used the temp field and the new field has default clause, then enforce the default clause
            if ($from_temp_fields) {
                $default_clause = $this->getDefaultClause($xmldb_field);
                if ($default_clause) {
                    $skip_notnull_clause = false;
                }
            }
        }

        // If arriving here, something is not being skipped (type, notnull, default), calculate the standard AlterFieldSQL
        if (!$skip_type_clause || !$skip_notnull_clause || !$skip_default_clause) {
            $results = array_merge($results, parent::getAlterFieldSQL($xmldb_table, $xmldb_field, $skip_type_clause, $skip_default_clause, $skip_notnull_clause));
            return $results;
        }

        // Finally return results
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
        // Just a wrapper over the getAlterFieldSQL() function for Oracle that
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
        // Just a wrapper over the getAlterFieldSQL() function for Oracle that
        // is capable of handling defaults
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**
     * Given one xmldb_table returns one string with the sequence of the table
     * in the table (fetched from DB)
     * The sequence name for oracle is calculated by looking the corresponding
     * trigger and retrieving the sequence name from it (because sequences are
     * independent elements)
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @return string|bool If no sequence is found, returns false
     */
    public function getSequenceFromDB($xmldb_table) {

         $tablename    = strtoupper($this->getTableName($xmldb_table));
         $prefixupper  = strtoupper($this->prefix);
         $sequencename = false;

        if ($trigger = $this->mdb->get_record_sql("SELECT trigger_name, trigger_body
                                                     FROM user_triggers
                                                    WHERE table_name = ? AND trigger_name LIKE ?",
                                                  array($tablename, "{$prefixupper}%_ID%_TRG"))) {
            // If trigger found, regexp it looking for the sequence name
            preg_match('/.*SELECT (.*)\.nextval/i', $trigger->trigger_body, $matches);
            if (isset($matches[1])) {
                $sequencename = $matches[1];
            }
        }

        return $sequencename;
    }

    /**
     * Given one xmldb_table returns one string with the trigger
     * in the table (fetched from DB)
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @return string|bool If no trigger is found, returns false
     */
    public function getTriggerFromDB($xmldb_table) {

        $tablename   = strtoupper($this->getTableName($xmldb_table));
        $prefixupper = strtoupper($this->prefix);
        $triggername = false;

        if ($trigger = $this->mdb->get_record_sql("SELECT trigger_name, trigger_body
                                                     FROM user_triggers
                                                    WHERE table_name = ? AND trigger_name LIKE ?",
                                                  array($tablename, "{$prefixupper}%_ID%_TRG"))) {
            $triggername = $trigger->trigger_name;
        }

        return $triggername;
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
            case 'trg':
                if ($check = $this->mdb->get_records_sql("SELECT object_name
                                                            FROM user_objects
                                                           WHERE lower(object_name) = ?", array(strtolower($object_name)))) {
                    return true;
                }
                break;
            case 'pk':
            case 'uk':
            case 'fk':
            case 'ck':
                if ($check = $this->mdb->get_records_sql("SELECT constraint_name
                                                            FROM user_constraints
                                                           WHERE lower(constraint_name) = ?", array(strtolower($object_name)))) {
                    return true;
                }
                break;
        }
        return false; //No name in use found
    }

    /**
     * Adds slashes to string.
     * @param string $s
     * @return string The escaped string.
     */
    public function addslashes($s) {
        // do not use php addslashes() because it depends on PHP quote settings!
        $s = str_replace("'",  "''", $s);
        return $s;
    }

    /**
     * Returns an array of reserved words (lowercase) for this DB
     * @return array An array of database specific reserved words
     */
    public static function getReservedWords() {
        // This file contains the reserved words for Oracle databases
        // from http://download-uk.oracle.com/docs/cd/B10501_01/server.920/a96540/ap_keywd.htm
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
            'mlslabel', 'mode', 'modify', 'nchar', 'nclob', 'noaudit',
            'nocompress', 'not', 'nowait', 'null', 'number', 'nvarchar2',
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
