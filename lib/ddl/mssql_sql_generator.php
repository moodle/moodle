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
 * MSSQL specific SQL code generator.
 *
 * @package    core_ddl
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/ddl/sql_generator.php');

/**
 * This class generate SQL code to be used against MSSQL
 * It extends XMLDBgenerator so everything can be
 * overridden as needed to generate correct SQL.
 *
 * @package    core_ddl
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mssql_sql_generator extends sql_generator {

    // Only set values that are different from the defaults present in XMLDBgenerator

    /** @var string To be automatically added at the end of each statement. */
    public $statement_end = "\ngo";

    /** @var string Proper type for NUMBER(x) in this DB. */
    public $number_type = 'DECIMAL';

    /** @var string To define the default to set for NOT NULLs CHARs without default (null=do nothing).*/
    public $default_for_char = '';

    /**
     * @var bool To force the generator if NULL clauses must be specified. It shouldn't be necessary.
     * note: some mssql drivers require them or everything is created as NOT NULL :-(
     */
    public $specify_nulls = true;

    /** @var bool True if the generator needs to add extra code to generate the sequence fields.*/
    public $sequence_extra_code = false;

    /** @var string The particular name for inline sequences in this generator.*/
    public $sequence_name = 'IDENTITY(1,1)';

    /** @var bool To avoid outputting the rest of the field specs, leaving only the name and the sequence_name returned.*/
    public $sequence_only = false;

    /** @var bool True if the generator needs to add code for table comments.*/
    public $add_table_comments = false;

    /** @var string Characters to be used as concatenation operator.*/
    public $concat_character = '+';

    /** @var string SQL sentence to rename one table, both 'OLDNAME' and 'NEWNAME' keywords are dynamically replaced.*/
    public $rename_table_sql = "sp_rename 'OLDNAME', 'NEWNAME'";

    /** @var string SQL sentence to rename one column where 'TABLENAME', 'OLDFIELDNAME' and 'NEWFIELDNAME' keywords are dynamically replaced.*/
    public $rename_column_sql = "sp_rename 'TABLENAME.OLDFIELDNAME', 'NEWFIELDNAME', 'COLUMN'";

    /** @var string SQL sentence to drop one index where 'TABLENAME', 'INDEXNAME' keywords are dynamically replaced.*/
    public $drop_index_sql = 'DROP INDEX TABLENAME.INDEXNAME';

    /** @var string SQL sentence to rename one index where 'TABLENAME', 'OLDINDEXNAME' and 'NEWINDEXNAME' are dynamically replaced.*/
    public $rename_index_sql = "sp_rename 'TABLENAME.OLDINDEXNAME', 'NEWINDEXNAME', 'INDEX'";

    /** @var string SQL sentence to rename one key 'TABLENAME', 'OLDKEYNAME' and 'NEWKEYNAME' are dynamically replaced.*/
    public $rename_key_sql = null;

    /**
     * Reset a sequence to the id field of a table.
     *
     * @param xmldb_table|string $table name of table or the table object.
     * @return array of sql statements
     */
    public function getResetSequenceSQL($table) {

        if (is_string($table)) {
            $table = new xmldb_table($table);
        }

        $value = (int)$this->mdb->get_field_sql('SELECT MAX(id) FROM {'. $table->getName() . '}');
        $sqls = array();

        // MSSQL has one non-consistent behavior to create the first identity value, depending
        // if the table has been truncated or no. If you are really interested, you can find the
        // whole description of the problem at:
        //     http://www.justinneff.com/archive/tag/dbcc-checkident
        if ($value == 0) {
            // truncate to get consistent result from reseed
            $sqls[] = "TRUNCATE TABLE " . $this->getTableName($table);
            $value = 1;
        }

        // From http://msdn.microsoft.com/en-us/library/ms176057.aspx
        $sqls[] = "DBCC CHECKIDENT ('" . $this->getTableName($table) . "', RESEED, $value)";
        return $sqls;
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
        // Get the name, supporting special mssql names for temp tables
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

    public function getCreateIndexSQL($xmldb_table, $xmldb_index) {
        list($indexsql) = parent::getCreateIndexSQL($xmldb_table, $xmldb_index);

        // Unique indexes need to work-around non-standard SQL server behaviour.
        if ($xmldb_index->getUnique()) {
            // Find any nullable columns. We need to add a
            // WHERE field IS NOT NULL to the index definition for each one.
            //
            // For example if you have a unique index on the three columns
            // (required, option1, option2) where the first one is non-null,
            // and the others nullable, then the SQL will end up as
            //
            // CREATE UNIQUE INDEX index_name ON table_name (required, option1, option2)
            // WHERE option1 IS NOT NULL AND option2 IS NOT NULL
            //
            // The first line comes from parent calls above. The WHERE is added below.
            $extraconditions = [];
            foreach ($this->get_nullable_fields_in_index($xmldb_table, $xmldb_index) as $fieldname) {
                $extraconditions[] = $this->getEncQuoted($fieldname) .
                        ' IS NOT NULL';
            }

            if ($extraconditions) {
                $indexsql .= ' WHERE ' . implode(' AND ', $extraconditions);
            }
        }

        return [$indexsql];
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
            case XMLDB_TYPE_INTEGER:    // From http://msdn.microsoft.com/library/en-us/tsqlref/ts_da-db_7msw.asp?frame=true
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
                $dbtype = 'FLOAT';
                if (!empty($xmldb_decimals)) {
                    if ($xmldb_decimals < 6) {
                        $dbtype = 'REAL';
                    }
                }
                break;
            case XMLDB_TYPE_CHAR:
                $dbtype = 'NVARCHAR';
                if (empty($xmldb_length)) {
                    $xmldb_length='255';
                }
                $dbtype .= '(' . $xmldb_length . ') COLLATE database_default';
                break;
            case XMLDB_TYPE_TEXT:
                $dbtype = 'NVARCHAR(MAX) COLLATE database_default';
                break;
            case XMLDB_TYPE_BINARY:
                $dbtype = 'VARBINARY(MAX)';
                break;
            case XMLDB_TYPE_DATETIME:
                $dbtype = 'DATETIME';
                break;
        }
        return $dbtype;
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to drop the field from the table.
     * MSSQL overwrites the standard sentence because it needs to do some extra work dropping the default and
     * check constraints
     *
     * @param xmldb_table $xmldb_table The table related to $xmldb_field.
     * @param xmldb_field $xmldb_field The instance of xmldb_field to create the SQL from.
     * @return array The SQL statement for dropping a field from the table.
     */
    public function getDropFieldSQL($xmldb_table, $xmldb_field) {
        $results = array();

        // Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);
        $fieldname = $this->getEncQuoted($xmldb_field->getName());

        // Look for any default constraint in this field and drop it
        if ($defaultname = $this->getDefaultConstraintName($xmldb_table, $xmldb_field)) {
            $results[] = 'ALTER TABLE ' . $tablename . ' DROP CONSTRAINT ' . $defaultname;
        }

        // Build the standard alter table drop column
        $results[] = 'ALTER TABLE ' . $tablename . ' DROP COLUMN ' . $fieldname;

        return $results;
    }

    /**
     * Given one correct xmldb_field and the new name, returns the SQL statements
     * to rename it (inside one array).
     *
     * MSSQL is special, so we overload the function here. It needs to
     * drop the constraints BEFORE renaming the field
     *
     * @param xmldb_table $xmldb_table The table related to $xmldb_field.
     * @param xmldb_field $xmldb_field The instance of xmldb_field to get the renamed field from.
     * @param string $newname The new name to rename the field to.
     * @return array The SQL statements for renaming the field.
     */
    public function getRenameFieldSQL($xmldb_table, $xmldb_field, $newname) {

        $results = array();  //Array where all the sentences will be stored

        // Although this is checked in database_manager::rename_field() - double check
        // that we aren't trying to rename one "id" field. Although it could be
        // implemented (if adding the necessary code to rename sequences, defaults,
        // triggers... and so on under each getRenameFieldExtraSQL() function, it's
        // better to forbid it, mainly because this field is the default PK and
        // in the future, a lot of FKs can be pointing here. So, this field, more
        // or less, must be considered immutable!
        if ($xmldb_field->getName() == 'id') {
            return array();
        }

        // We can't call to standard (parent) getRenameFieldSQL() function since it would enclose the field name
        // with improper quotes in MSSQL: here, we use a stored procedure to rename the field i.e. a column object;
        // we need to take care about how this stored procedure expects parameters to be "qualified".
        $rename = str_replace('TABLENAME', $this->getTableName($xmldb_table), $this->rename_column_sql);
        // Qualifying the column object could require brackets: use them, regardless the column name not being a reserved word.
        $rename = str_replace('OLDFIELDNAME', '[' . $xmldb_field->getName() . ']', $rename);
        // The new field name should be passed as the actual name, w/o any quote.
        $rename = str_replace('NEWFIELDNAME', $newname, $rename);

        $results[] = $rename;

        return $results;
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

        return $results;
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to alter the field in the table.
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

        // Get the quoted name of the table and field
        $tablename = $xmldb_table->getName();
        $fieldname = $xmldb_field->getName();

        // Take a look to field metadata
        $meta = $this->mdb->get_columns($tablename);
        $metac = $meta[$fieldname];
        $oldmetatype = $metac->meta_type;

        $oldlength = $metac->max_length;
        $olddecimals = empty($metac->scale) ? null : $metac->scale;
        $oldnotnull = empty($metac->not_null) ? false : $metac->not_null;
        //$olddefault = empty($metac->has_default) ? null : strtok($metac->default_value, ':');

        $typechanged = true;  //By default, assume that the column type has changed
        $lengthchanged = true;  //By default, assume that the column length has changed

        // Detect if we are changing the type of the column
        if (($xmldb_field->getType() == XMLDB_TYPE_INTEGER && $oldmetatype == 'I') ||
            ($xmldb_field->getType() == XMLDB_TYPE_NUMBER  && $oldmetatype == 'N') ||
            ($xmldb_field->getType() == XMLDB_TYPE_FLOAT   && $oldmetatype == 'F') ||
            ($xmldb_field->getType() == XMLDB_TYPE_CHAR    && $oldmetatype == 'C') ||
            ($xmldb_field->getType() == XMLDB_TYPE_TEXT    && $oldmetatype == 'X') ||
            ($xmldb_field->getType() == XMLDB_TYPE_BINARY  && $oldmetatype == 'B')) {
            $typechanged = false;
        }

        // If the new field (and old) specs are for integer, let's be a bit more specific differentiating
        // types of integers. Else, some combinations can cause things like MDL-21868
        if ($xmldb_field->getType() == XMLDB_TYPE_INTEGER && $oldmetatype == 'I') {
            if ($xmldb_field->getLength() > 9) { // Convert our new lenghts to detailed meta types
                $newmssqlinttype = 'I8';
            } else if ($xmldb_field->getLength() > 4) {
                $newmssqlinttype = 'I';
            } else {
                $newmssqlinttype = 'I2';
            }
            if ($metac->type == 'bigint') { // Convert current DB type to detailed meta type (our metatype is not enough!)
                $oldmssqlinttype = 'I8';
            } else if ($metac->type == 'smallint') {
                $oldmssqlinttype = 'I2';
            } else {
                $oldmssqlinttype = 'I';
            }
            if ($newmssqlinttype != $oldmssqlinttype) { // Compare new and old meta types
                $typechanged = true; // Change in meta type means change in type at all effects
            }
        }

        // Detect if we are changing the length of the column, not always necessary to drop defaults
        // if only the length changes, but it's safe to do it always
        if ($xmldb_field->getLength() == $oldlength) {
            $lengthchanged = false;
        }

        // If type or length have changed drop the default if exists
        if ($typechanged || $lengthchanged) {
            $results = $this->getDropDefaultSQL($xmldb_table, $xmldb_field);
        }

        // Some changes of type require multiple alter statements, because mssql lacks direct implicit cast between such types
        // Here it is the matrix: http://msdn.microsoft.com/en-us/library/ms187928(SQL.90).aspx
        // Going to store such intermediate alters in array of objects, storing all the info needed
        $multiple_alter_stmt = array();
        $targettype = $xmldb_field->getType();

        if ($targettype == XMLDB_TYPE_TEXT && $oldmetatype == 'I') { // integer to text
            $multiple_alter_stmt[0] = new stdClass;                  // needs conversion to varchar
            $multiple_alter_stmt[0]->type = XMLDB_TYPE_CHAR;
            $multiple_alter_stmt[0]->length = 255;

        } else if ($targettype == XMLDB_TYPE_TEXT && $oldmetatype == 'N') { // decimal to text
            $multiple_alter_stmt[0] = new stdClass;                         // needs conversion to varchar
            $multiple_alter_stmt[0]->type = XMLDB_TYPE_CHAR;
            $multiple_alter_stmt[0]->length = 255;

        } else if ($targettype == XMLDB_TYPE_TEXT && $oldmetatype == 'F') { // float to text
            $multiple_alter_stmt[0] = new stdClass;                         // needs conversion to varchar
            $multiple_alter_stmt[0]->type = XMLDB_TYPE_CHAR;
            $multiple_alter_stmt[0]->length = 255;

        } else if ($targettype == XMLDB_TYPE_INTEGER && $oldmetatype == 'X') { // text to integer
            $multiple_alter_stmt[0] = new stdClass;                            // needs conversion to varchar
            $multiple_alter_stmt[0]->type = XMLDB_TYPE_CHAR;
            $multiple_alter_stmt[0]->length = 255;
            $multiple_alter_stmt[1] = new stdClass;                            // and also needs conversion to decimal
            $multiple_alter_stmt[1]->type = XMLDB_TYPE_NUMBER;                 // without decimal positions
            $multiple_alter_stmt[1]->length = 10;

        } else if ($targettype == XMLDB_TYPE_NUMBER && $oldmetatype == 'X') { // text to decimal
            $multiple_alter_stmt[0] = new stdClass;                           // needs conversion to varchar
            $multiple_alter_stmt[0]->type = XMLDB_TYPE_CHAR;
            $multiple_alter_stmt[0]->length = 255;

        } else if ($targettype ==  XMLDB_TYPE_FLOAT && $oldmetatype == 'X') { // text to float
            $multiple_alter_stmt[0] = new stdClass;                           // needs conversion to varchar
            $multiple_alter_stmt[0]->type = XMLDB_TYPE_CHAR;
            $multiple_alter_stmt[0]->length = 255;
        }

        // Just prevent default clauses in this type of sentences for mssql and launch the parent one
        if (empty($multiple_alter_stmt)) { // Direct implicit conversion allowed, launch it
            $results = array_merge($results, parent::getAlterFieldSQL($xmldb_table, $xmldb_field, NULL, true, NULL));

        } else { // Direct implicit conversion forbidden, use the intermediate ones
            $final_type = $xmldb_field->getType(); // Save final type and length
            $final_length = $xmldb_field->getLength();
            foreach ($multiple_alter_stmt as $alter) {
                $xmldb_field->setType($alter->type);  // Put our intermediate type and length and alter to it
                $xmldb_field->setLength($alter->length);
                $results = array_merge($results, parent::getAlterFieldSQL($xmldb_table, $xmldb_field, NULL, true, NULL));
            }
            $xmldb_field->setType($final_type); // Set the final type and length and alter to it
            $xmldb_field->setLength($final_length);
            $results = array_merge($results, parent::getAlterFieldSQL($xmldb_table, $xmldb_field, NULL, true, NULL));
        }

        // Finally, process the default clause to add it back if necessary
        if ($typechanged || $lengthchanged) {
            $results = array_merge($results, $this->getCreateDefaultSQL($xmldb_table, $xmldb_field));
        }

        // Return results
        return $results;
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to modify the default of the field in the table.
     *
     * @param xmldb_table $xmldb_table The table related to $xmldb_field.
     * @param xmldb_field $xmldb_field The instance of xmldb_field to get the modified default value from.
     * @return array The SQL statement for modifying the default value.
     */
    public function getModifyDefaultSQL($xmldb_table, $xmldb_field) {
        // MSSQL is a bit special with default constraints because it implements them as external constraints so
        // normal ALTER TABLE ALTER COLUMN don't work to change defaults. Because this, we have this method overloaded here

        $results = array();

        // Decide if we are going to create/modify or to drop the default
        if ($xmldb_field->getDefault() === null) {
            $results = $this->getDropDefaultSQL($xmldb_table, $xmldb_field); //Drop but, under some circumstances, re-enable
            $default_clause = $this->getDefaultClause($xmldb_field);
            if ($default_clause) { //If getDefaultClause() it must have one default, create it
                $results = array_merge($results, $this->getCreateDefaultSQL($xmldb_table, $xmldb_field)); //Create/modify
            }
        } else {
            $results = $this->getDropDefaultSQL($xmldb_table, $xmldb_field); //Drop (only if exists)
            $results = array_merge($results, $this->getCreateDefaultSQL($xmldb_table, $xmldb_field)); //Create/modify
        }

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
        // MSSQL is a bit special and it requires the corresponding DEFAULT CONSTRAINT to be dropped

        $results = array();

        // Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);
        $fieldname = $this->getEncQuoted($xmldb_field->getName());

        // Now, check if, with the current field attributes, we have to build one default
        $default_clause = $this->getDefaultClause($xmldb_field);
        if ($default_clause) {
            // We need to build the default (Moodle) default, so do it
            $sql = 'ALTER TABLE ' . $tablename . ' ADD' . $default_clause . ' FOR ' . $fieldname;
            $results[] = $sql;
        }

        return $results;
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
        // MSSQL is a bit special and it requires the corresponding DEFAULT CONSTRAINT to be dropped

        $results = array();

        // Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);
        $fieldname = $this->getEncQuoted($xmldb_field->getName());

        // Look for the default contraint and, if found, drop it
        if ($defaultname = $this->getDefaultConstraintName($xmldb_table, $xmldb_field)) {
            $results[] = 'ALTER TABLE ' . $tablename . ' DROP CONSTRAINT ' . $defaultname;
        }

        return $results;
    }

    /**
     * Given one xmldb_table and one xmldb_field, returns the name of its default constraint in DB
     * or false if not found
     * This function should be considered internal and never used outside from generator
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @param xmldb_field $xmldb_field The xmldb_field object instance.
     * @return mixed
     */
    protected function getDefaultConstraintName($xmldb_table, $xmldb_field) {

        // Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);
        $fieldname = $xmldb_field->getName();

        // Look for any default constraint in this field and drop it
        if ($default = $this->mdb->get_record_sql("SELECT object_id, object_name(default_object_id) AS defaultconstraint
                                                     FROM sys.columns
                                                    WHERE object_id = object_id(?)
                                                          AND name = ?", array($tablename, $fieldname))) {
            return $default->defaultconstraint;
        } else {
            return false;
        }
    }

    /**
     * Given three strings (table name, list of fields (comma separated) and suffix),
     * create the proper object name quoting it if necessary.
     *
     * IMPORTANT: This function must be used to CALCULATE NAMES of objects TO BE CREATED,
     *            NEVER TO GUESS NAMES of EXISTING objects!!!
     *
     * IMPORTANT: We are overriding this function for the MSSQL generator because objects
     * belonging to temporary tables aren't searchable in the catalog neither in information
     * schema tables. So, for temporary tables, we are going to add 4 randomly named "virtual"
     * fields, so the generated names won't cause concurrency problems. Really nasty hack,
     * but the alternative involves modifying all the creation table code to avoid naming
     * constraints for temp objects and that will dupe a lot of code.
     *
     * @param string $tablename The table name.
     * @param string $fields A list of comma separated fields.
     * @param string $suffix A suffix for the object name.
     * @return string Object's name.
     */
    public function getNameForObject($tablename, $fields, $suffix='') {
        if ($this->temptables->is_temptable($tablename)) { // Is temp table, inject random field names
            $random = strtolower(random_string(12)); // 12cc to be split in 4 parts
            $fields = $fields . ', ' . implode(', ', str_split($random, 3));
        }
        return parent::getNameForObject($tablename, $fields, $suffix); // Delegate to parent (common) algorithm
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
            case 'seq':
            case 'trg':
            case 'pk':
            case 'uk':
            case 'fk':
            case 'ck':
                if ($check = $this->mdb->get_records_sql("SELECT name
                                                            FROM sys.objects
                                                           WHERE lower(name) = ?", array(strtolower($object_name)))) {
                    return true;
                }
                break;
            case 'ix':
            case 'uix':
                if ($check = $this->mdb->get_records_sql("SELECT name
                                                            FROM sys.indexes
                                                           WHERE lower(name) = ?", array(strtolower($object_name)))) {
                    return true;
                }
                break;
        }
        return false; //No name in use found
    }

    /**
     * Returns the code (array of statements) needed to add one comment to the table.
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @return array Array of SQL statements to add one comment to the table.
     */
    public function getCommentSQL($xmldb_table) {
        return array();
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
        // This file contains the reserved words for MSSQL databases
        // from http://msdn2.microsoft.com/en-us/library/ms189822.aspx
        // Should be identical to sqlsrv_native_moodle_database::$reservewords.
        $reserved_words = array (
            "add", "all", "alter", "and", "any", "as", "asc", "authorization", "avg", "backup", "begin", "between", "break",
            "browse", "bulk", "by", "cascade", "case", "check", "checkpoint", "close", "clustered", "coalesce", "collate", "column",
            "commit", "committed", "compute", "confirm", "constraint", "contains", "containstable", "continue", "controlrow",
            "convert", "count", "create", "cross", "current", "current_date", "current_time", "current_timestamp", "current_user",
            "cursor", "database", "dbcc", "deallocate", "declare", "default", "delete", "deny", "desc", "disk", "distinct",
            "distributed", "double", "drop", "dummy", "dump", "else", "end", "errlvl", "errorexit", "escape", "except", "exec",
            "execute", "exists", "exit", "external", "fetch", "file", "fillfactor", "floppy", "for", "foreign", "freetext",
            "freetexttable", "from", "full", "function", "goto", "grant", "group", "having", "holdlock", "identity",
            "identity_insert", "identitycol", "if", "in", "index", "inner", "insert", "intersect", "into", "is", "isolation",
            "join", "key", "kill", "left", "level", "like", "lineno", "load", "max", "merge", "min", "mirrorexit", "national",
            "nocheck", "nonclustered", "not", "null", "nullif", "of", "off", "offsets", "on", "once", "only", "open",
            "opendatasource", "openquery", "openrowset", "openxml", "option", "or", "order", "outer", "over", "percent", "perm",
            "permanent", "pipe", "pivot", "plan", "precision", "prepare", "primary", "print", "privileges", "proc", "procedure",
            "processexit", "public", "raiserror", "read", "readtext", "reconfigure", "references", "repeatable", "replication",
            "restore", "restrict", "return", "revert", "revoke", "right", "rollback", "rowcount", "rowguidcol", "rule", "save",
            "schema", "securityaudit", "select", "semantickeyphrasetable", "semanticsimilaritydetailstable",
            "semanticsimilaritytable", "serializable", "session_user", "set", "setuser", "shutdown", "some", "statistics", "sum",
            "system_user", "table", "tablesample", "tape", "temp", "temporary", "textsize", "then", "to", "top", "tran",
            "transaction", "trigger", "truncate", "try_convert", "tsequal", "uncommitted", "union", "unique", "unpivot", "update",
            "updatetext", "use", "user", "values", "varying", "view", "waitfor", "when", "where", "while", "with", "within group",
            "work", "writetext"
        );
        return $reserved_words;
    }
}
