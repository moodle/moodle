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
 * This class represent the base generator class where all the needed functions to generate proper SQL are defined.
 *
 * The rest of classes will inherit, by default, the same logic.
 * Functions will be overridden as needed to generate correct SQL.
 *
 * @package    core_ddl
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract sql generator class, base for all db specific implementations.
 *
 * @package    core_ddl
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class sql_generator {

    // Please, avoid editing this defaults in this base class!
    // It could change the behaviour of the rest of generators
    // that, by default, inherit this configuration.
    // To change any of them, do it in extended classes instead.

    /** @var string Used to quote names. */
    public $quote_string = '"';

    /** @var string To be automatically added at the end of each statement. */
    public $statement_end = ';';

    /** @var bool To decide if we want to quote all the names or only the reserved ones. */
    public $quote_all = false;

    /** @var bool To create all the integers as NUMBER(x) (also called DECIMAL, NUMERIC...). */
    public $integer_to_number = false;

    /** @var bool To create all the floats as NUMBER(x) (also called DECIMAL, NUMERIC...). */
    public $float_to_number   = false;

    /** @var string Proper type for NUMBER(x) in this DB. */
    public $number_type = 'NUMERIC';

    /** @var string To define the default to set for NOT NULLs CHARs without default (null=do nothing).*/
    public $default_for_char = null;

    /** @var bool To specify if the generator must use some DEFAULT clause to drop defaults.*/
    public $drop_default_value_required = false;

    /** @var string The DEFAULT clause required to drop defaults.*/
    public $drop_default_value = '';

    /** @var bool To decide if the default clause of each field must go after the null clause.*/
    public $default_after_null = true;

    /** @var bool To force the generator if NULL clauses must be specified. It shouldn't be necessary.*/
    public $specify_nulls = false;

    /** @var string To force primary key names to one string (null=no force).*/
    public $primary_key_name = null;

    /** @var bool True if the generator builds primary keys.*/
    public $primary_keys = true;

    /** @var bool True if the generator builds unique keys.*/
    public $unique_keys = false;

    /** @var bool True if the generator builds foreign keys.*/
    public $foreign_keys = false;

    /** @var string Template to drop PKs. 'TABLENAME' and 'KEYNAME' will be replaced from this template.*/
    public $drop_primary_key = 'ALTER TABLE TABLENAME DROP CONSTRAINT KEYNAME';

    /** @var string Template to drop UKs. 'TABLENAME' and 'KEYNAME' will be replaced from this template.*/
    public $drop_unique_key = 'ALTER TABLE TABLENAME DROP CONSTRAINT KEYNAME';

    /** @var string Template to drop FKs. 'TABLENAME' and 'KEYNAME' will be replaced from this template.*/
    public $drop_foreign_key = 'ALTER TABLE TABLENAME DROP CONSTRAINT KEYNAME';

    /** @var bool True if the generator needs to add extra code to generate the sequence fields.*/
    public $sequence_extra_code = true;

    /** @var string The particular name for inline sequences in this generator.*/
    public $sequence_name = 'auto_increment';

    /** @var string|bool Different name for small (4byte) sequences or false if same.*/
    public $sequence_name_small = false;

    /**
     * @var bool To avoid outputting the rest of the field specs, leaving only the name and the sequence_name returned.
     * @see getFieldSQL()
     */
    public $sequence_only = false;

    /** @var bool True if the generator needs to add code for table comments.*/
    public $add_table_comments  = true;

    /** @var bool True if the generator needs to add the after clause for fields.*/
    public $add_after_clause = false;

    /**
     * @var bool True if the generator needs to prepend the prefix to all the key/index/sequence/trigger/check names.
     * @see $prefix
     */
    public $prefix_on_names = true;

    /** @var int Maximum length for key/index/sequence/trigger/check names (keep 30 for all!).*/
    public $names_max_length = 30;

    /** @var string Characters to be used as concatenation operator. If not defined, MySQL CONCAT function will be used.*/
    public $concat_character = '||';

    /** @var string SQL sentence to rename one table, both 'OLDNAME' and 'NEWNAME' keywords are dynamically replaced.*/
    public $rename_table_sql = 'ALTER TABLE OLDNAME RENAME TO NEWNAME';

    /** @var string SQL sentence to drop one table where the 'TABLENAME' keyword is dynamically replaced.*/
    public $drop_table_sql = 'DROP TABLE TABLENAME';

    /** @var string The SQL template to alter columns where the 'TABLENAME' and 'COLUMNSPECS' keywords are dynamically replaced.*/
    public $alter_column_sql = 'ALTER TABLE TABLENAME ALTER COLUMN COLUMNSPECS';

    /** @var bool The generator will skip the default clause on alter columns.*/
    public $alter_column_skip_default = false;

    /** @var bool The generator will skip the type clause on alter columns.*/
    public $alter_column_skip_type = false;

    /** @var bool The generator will skip the null/notnull clause on alter columns.*/
    public $alter_column_skip_notnull = false;

    /** @var string SQL sentence to rename one column where 'TABLENAME', 'OLDFIELDNAME' and 'NEWFIELDNAME' keywords are dynamically replaced.*/
    public $rename_column_sql = 'ALTER TABLE TABLENAME RENAME COLUMN OLDFIELDNAME TO NEWFIELDNAME';

    /** @var string SQL sentence to drop one index where 'TABLENAME', 'INDEXNAME' keywords are dynamically replaced.*/
    public $drop_index_sql = 'DROP INDEX INDEXNAME';

    /** @var string SQL sentence to rename one index where 'TABLENAME', 'OLDINDEXNAME' and 'NEWINDEXNAME' are dynamically replaced.*/
    public $rename_index_sql = 'ALTER INDEX OLDINDEXNAME RENAME TO NEWINDEXNAME';

    /** @var string SQL sentence to rename one key 'TABLENAME', 'OLDKEYNAME' and 'NEWKEYNAME' are dynamically replaced.*/
    public $rename_key_sql = 'ALTER TABLE TABLENAME CONSTRAINT OLDKEYNAME RENAME TO NEWKEYNAME';

    /** @var string The prefix to be used for all the DB objects.*/
    public $prefix;

    /** @var string List of reserved words (in order to quote them properly).*/
    public $reserved_words;

    /** @var moodle_database The moodle_database instance.*/
    public $mdb;

    /** @var Control existing temptables.*/
    protected $temptables;

    /**
     * Creates a new sql_generator.
     * @param moodle_database $mdb The moodle_database object instance.
     * @param moodle_temptables $temptables The optional moodle_temptables instance, null by default.
     */
    public function __construct($mdb, $temptables = null) {
        $this->prefix         = $mdb->get_prefix();
        $this->reserved_words = $this->getReservedWords();
        $this->mdb            = $mdb; // this creates circular reference - the other link must be unset when closing db
        $this->temptables     = $temptables;
    }

    /**
     * Releases all resources.
     */
    public function dispose() {
        $this->mdb = null;
    }

    /**
     * Given one string (or one array), ends it with $statement_end .
     *
     * @see $statement_end
     *
     * @param array|string $input SQL statement(s).
     * @return array|string
     */
    public function getEndedStatements($input) {

        if (is_array($input)) {
            foreach ($input as $key=>$content) {
                $input[$key] = $this->getEndedStatements($content);
            }
            return $input;
        } else {
            $input = trim($input).$this->statement_end;
            return $input;
        }
    }

    /**
     * Given one xmldb_table, checks if it exists in DB (true/false).
     *
     * @param mixed $table The table to be searched (string name or xmldb_table instance).
     * @return boolean true/false
     */
    public function table_exists($table) {
        if (is_string($table)) {
            $tablename = $table;
        } else {
            // Calculate the name of the table
            $tablename = $table->getName();
        }

        // get all tables in moodle database
        $tables = $this->mdb->get_tables();
        $exists = in_array($tablename, $tables);

        return $exists;
    }

    /**
     * This function will return the SQL code needed to create db tables and statements.
     * @see xmldb_structure
     *
     * @param xmldb_structure $xmldb_structure An xmldb_structure instance.
     * @return array
     */
    public function getCreateStructureSQL($xmldb_structure) {
        $results = array();

        if ($tables = $xmldb_structure->getTables()) {
            foreach ($tables as $table) {
                $results = array_merge($results, $this->getCreateTableSQL($table));
            }
        }

        return $results;
    }

    /**
     * Given one xmldb_table, this returns it's correct name, depending of all the parameterization.
     * eg: This appends $prefix to the table name.
     *
     * @see $prefix
     *
     * @param xmldb_table $xmldb_table The table whose name we want.
     * @param boolean $quoted To specify if the name must be quoted (if reserved word, only!).
     * @return string The correct name of the table.
     */
    public function getTableName(xmldb_table $xmldb_table, $quoted=true) {
        // Get the name
        $tablename = $this->prefix.$xmldb_table->getName();

        // Apply quotes optionally
        if ($quoted) {
            $tablename = $this->getEncQuoted($tablename);
        }

        return $tablename;
    }

    /**
     * Given one correct xmldb_table, returns the SQL statements
     * to create it (inside one array).
     *
     * @param xmldb_table $xmldb_table An xmldb_table instance.
     * @return array An array of SQL statements, starting with the table creation SQL followed
     * by any of its comments, indexes and sequence creation SQL statements.
     */
    public function getCreateTableSQL($xmldb_table) {
        if ($error = $xmldb_table->validateDefinition()) {
            throw new coding_exception($error);
        }

        $results = array();  //Array where all the sentences will be stored

        // Table header
        $table = 'CREATE TABLE ' . $this->getTableName($xmldb_table) . ' (';

        if (!$xmldb_fields = $xmldb_table->getFields()) {
            return $results;
        }

        $sequencefield = null;

        // Add the fields, separated by commas
        foreach ($xmldb_fields as $xmldb_field) {
            if ($xmldb_field->getSequence()) {
                $sequencefield = $xmldb_field->getName();
            }
            $table .= "\n    " . $this->getFieldSQL($xmldb_table, $xmldb_field);
            $table .= ',';
        }
        // Add the keys, separated by commas
        if ($xmldb_keys = $xmldb_table->getKeys()) {
            foreach ($xmldb_keys as $xmldb_key) {
                if ($keytext = $this->getKeySQL($xmldb_table, $xmldb_key)) {
                    $table .= "\nCONSTRAINT " . $keytext . ',';
                }
                // If the key is XMLDB_KEY_FOREIGN_UNIQUE, create it as UNIQUE too
                if ($xmldb_key->getType() == XMLDB_KEY_FOREIGN_UNIQUE) {
                    //Duplicate the key
                    $xmldb_key->setType(XMLDB_KEY_UNIQUE);
                    if ($keytext = $this->getKeySQL($xmldb_table, $xmldb_key)) {
                        $table .= "\nCONSTRAINT " . $keytext . ',';
                    }
                }
                // make sure sequence field is unique
                if ($sequencefield and $xmldb_key->getType() == XMLDB_KEY_PRIMARY) {
                    $fields = $xmldb_key->getFields();
                    $field = reset($fields);
                    if ($sequencefield === $field) {
                        $sequencefield = null;
                    }
                }
            }
        }
        // throw error if sequence field does not have unique key defined
        if ($sequencefield) {
            throw new ddl_exception('ddsequenceerror', $xmldb_table->getName());
        }

        // Table footer, trim the latest comma
        $table = trim($table,',');
        $table .= "\n)";

        // Add the CREATE TABLE to results
        $results[] = $table;

        // Add comments if specified and it exists
        if ($this->add_table_comments && $xmldb_table->getComment()) {
            $comment = $this->getCommentSQL($xmldb_table);
            // Add the COMMENT to results
            $results = array_merge($results, $comment);
        }

        // Add the indexes (each one, one statement)
        if ($xmldb_indexes = $xmldb_table->getIndexes()) {
            foreach ($xmldb_indexes as $xmldb_index) {
                //tables do not exist yet, which means indexed can not exist yet
                if ($indextext = $this->getCreateIndexSQL($xmldb_table, $xmldb_index)) {
                    $results = array_merge($results, $indextext);
                }
            }
        }

        // Also, add the indexes needed from keys, based on configuration (each one, one statement)
        if ($xmldb_keys = $xmldb_table->getKeys()) {
            foreach ($xmldb_keys as $xmldb_key) {
                // If we aren't creating the keys OR if the key is XMLDB_KEY_FOREIGN (not underlying index generated
                // automatically by the RDBMS) create the underlying (created by us) index (if doesn't exists)
                if (!$this->getKeySQL($xmldb_table, $xmldb_key) || $xmldb_key->getType() == XMLDB_KEY_FOREIGN) {
                    // Create the interim index
                    $index = new xmldb_index('anyname');
                    $index->setFields($xmldb_key->getFields());
                    //tables do not exist yet, which means indexed can not exist yet
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
                            // Add the INDEX to the array
                            $results = array_merge($results, $indextext);
                        }
                    }
                }
            }
        }

        // Add sequence extra code if needed
        if ($this->sequence_extra_code) {
            // Iterate over fields looking for sequences
            foreach ($xmldb_fields as $xmldb_field) {
                if ($xmldb_field->getSequence()) {
                    // returns an array of statements needed to create one sequence
                    $sequence_sentences = $this->getCreateSequenceSQL($xmldb_table, $xmldb_field);
                    // Add the SEQUENCE to the array
                    $results = array_merge($results, $sequence_sentences);
                }
            }
        }

        return $results;
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
        if ($error = $xmldb_index->validateDefinition($xmldb_table)) {
            throw new coding_exception($error);
        }

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
     * Given one correct xmldb_field, returns the complete SQL line to create it.
     *
     * @param xmldb_table $xmldb_table The table related to $xmldb_field.
     * @param xmldb_field $xmldb_field The instance of xmldb_field to create the SQL from.
     * @param string $skip_type_clause The type clause on alter columns, NULL by default.
     * @param string $skip_default_clause The default clause on alter columns, NULL by default.
     * @param string $skip_notnull_clause The null/notnull clause on alter columns, NULL by default.
     * @param string $specify_nulls_clause To force a specific null clause, NULL by default.
     * @param bool $specify_field_name Flag to specify fieldname in return.
     * @return string The field generating SQL statement.
     * @throws coding_exception Thrown when xmldb_field doesn't validate with the xmldb_table.
     */
    public function getFieldSQL($xmldb_table, $xmldb_field, $skip_type_clause = NULL, $skip_default_clause = NULL, $skip_notnull_clause = NULL, $specify_nulls_clause = NULL, $specify_field_name = true)  {
        if ($error = $xmldb_field->validateDefinition($xmldb_table)) {
            throw new coding_exception($error);
        }

        $skip_type_clause = is_null($skip_type_clause) ? $this->alter_column_skip_type : $skip_type_clause;
        $skip_default_clause = is_null($skip_default_clause) ? $this->alter_column_skip_default : $skip_default_clause;
        $skip_notnull_clause = is_null($skip_notnull_clause) ? $this->alter_column_skip_notnull : $skip_notnull_clause;
        $specify_nulls_clause = is_null($specify_nulls_clause) ? $this->specify_nulls : $specify_nulls_clause;

        // First of all, convert integers to numbers if defined
        if ($this->integer_to_number) {
            if ($xmldb_field->getType() == XMLDB_TYPE_INTEGER) {
                $xmldb_field->setType(XMLDB_TYPE_NUMBER);
            }
        }
        // Same for floats
        if ($this->float_to_number) {
            if ($xmldb_field->getType() == XMLDB_TYPE_FLOAT) {
                $xmldb_field->setType(XMLDB_TYPE_NUMBER);
            }
        }

        $field = ''; // Let's accumulate the whole expression based on params and settings
        // The name
        if ($specify_field_name) {
            $field .= $this->getEncQuoted($xmldb_field->getName());
        }
        // The type and length only if we don't want to skip it
        if (!$skip_type_clause) {
            // The type and length
            $field .= ' ' . $this->getTypeSQL($xmldb_field->getType(), $xmldb_field->getLength(), $xmldb_field->getDecimals());
        }
        // note: unsigned is not supported any more since moodle 2.3, all numbers are signed
        // Calculate the not null clause
        $notnull = '';
        // Only if we don't want to skip it
        if (!$skip_notnull_clause) {
            if ($xmldb_field->getNotNull()) {
                $notnull = ' NOT NULL';
            } else {
                if ($specify_nulls_clause) {
                    $notnull = ' NULL';
                }
            }
        }
        // Calculate the default clause
        $default_clause = '';
        if (!$skip_default_clause) { //Only if we don't want to skip it
            $default_clause = $this->getDefaultClause($xmldb_field);
        }
        // Based on default_after_null, set both clauses properly
        if ($this->default_after_null) {
            $field .= $notnull . $default_clause;
        } else {
            $field .= $default_clause . $notnull;
        }
        // The sequence
        if ($xmldb_field->getSequence()) {
            if($xmldb_field->getLength()<=9 && $this->sequence_name_small) {
                $sequencename=$this->sequence_name_small;
            } else {
                $sequencename=$this->sequence_name;
            }
            $field .= ' ' . $sequencename;
            if ($this->sequence_only) {
                // We only want the field name and sequence name to be printed
                // so, calculate it and return
                $sql = $this->getEncQuoted($xmldb_field->getName()) . ' ' . $sequencename;
                return $sql;
            }
        }
        return $field;
    }

    /**
     * Given one correct xmldb_key, returns its specs.
     *
     * @param xmldb_table $xmldb_table The table related to $xmldb_key.
     * @param xmldb_key $xmldb_key The xmldb_key's specifications requested.
     * @return string SQL statement about the xmldb_key.
     */
    public function getKeySQL($xmldb_table, $xmldb_key) {

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
     * Give one xmldb_field, returns the correct "default value" for the current configuration
     *
     * @param xmldb_field $xmldb_field The field.
     * @return The default value of the field.
     */
    public function getDefaultValue($xmldb_field) {

        $default = null;

        if ($xmldb_field->getDefault() !== NULL) {
            if ($xmldb_field->getType() == XMLDB_TYPE_CHAR ||
                $xmldb_field->getType() == XMLDB_TYPE_TEXT) {
                    if ($xmldb_field->getDefault() === '') { // If passing empty default, use the $default_for_char one instead
                        $default = "'" . $this->default_for_char . "'";
                    } else {
                        $default = "'" . $this->addslashes($xmldb_field->getDefault()) . "'";
                    }
            } else {
                $default = $xmldb_field->getDefault();
            }
        } else {
            // We force default '' for not null char columns without proper default
            // some day this should be out!
            if ($this->default_for_char !== NULL &&
                $xmldb_field->getType() == XMLDB_TYPE_CHAR &&
                $xmldb_field->getNotNull()) {
                $default = "'" . $this->default_for_char . "'";
            } else {
                // If the DB requires to explicity define some clause to drop one default, do it here
                // never applying defaults to TEXT and BINARY fields
                if ($this->drop_default_value_required &&
                    $xmldb_field->getType() != XMLDB_TYPE_TEXT &&
                    $xmldb_field->getType() != XMLDB_TYPE_BINARY && !$xmldb_field->getNotNull()) {
                    $default = $this->drop_default_value;
                }
            }
        }
        return $default;
    }

    /**
     * Given one xmldb_field, returns the correct "default clause" for the current configuration.
     *
     * @param xmldb_field $xmldb_field The xmldb_field.
     * @return The SQL clause for generating the default value as in $xmldb_field.
     */
    public function getDefaultClause($xmldb_field) {

        $defaultvalue = $this->getDefaultValue ($xmldb_field);

        if ($defaultvalue !== null) {
            return ' DEFAULT ' . $defaultvalue;
        } else {
            return null;
        }
    }

    /**
     * Given one correct xmldb_table and the new name, returns the SQL statements
     * to rename it (inside one array).
     *
     * @param xmldb_table $xmldb_table The table to rename.
     * @param string $newname The new name to rename the table to.
     * @return array SQL statement(s) to rename the table.
     */
    public function getRenameTableSQL($xmldb_table, $newname) {

        $results = array();  //Array where all the sentences will be stored

        $newt = new xmldb_table($newname); //Temporal table for name calculations

        $rename = str_replace('OLDNAME', $this->getTableName($xmldb_table), $this->rename_table_sql);
        $rename = str_replace('NEWNAME', $this->getTableName($newt), $rename);

        $results[] = $rename;

        // Call to getRenameTableExtraSQL() override if needed
        $extra_sentences = $this->getRenameTableExtraSQL($xmldb_table, $newname);
        $results = array_merge($results, $extra_sentences);

        return $results;
    }

    /**
     * Given one correct xmldb_table and the new name, returns the SQL statements
     * to drop it (inside one array). Works also for temporary tables.
     *
     * @param xmldb_table $xmldb_table The table to drop.
     * @return array SQL statement(s) for dropping the specified table.
     */
    public function getDropTableSQL($xmldb_table) {

        $results = array();  //Array where all the sentences will be stored

        $drop = str_replace('TABLENAME', $this->getTableName($xmldb_table), $this->drop_table_sql);

        $results[] = $drop;

        // call to getDropTableExtraSQL(), override if needed
        $extra_sentences = $this->getDropTableExtraSQL($xmldb_table);
        $results = array_merge($results, $extra_sentences);

        return $results;
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to add the field to the table.
     *
     * @param xmldb_table $xmldb_table The table related to $xmldb_field.
     * @param xmldb_field $xmldb_field The instance of xmldb_field to create the SQL from.
     * @param string $skip_type_clause The type clause on alter columns, NULL by default.
     * @param string $skip_default_clause The default clause on alter columns, NULL by default.
     * @param string $skip_notnull_clause The null/notnull clause on alter columns, NULL by default.
     * @return array The SQL statement for adding a field to the table.
     */
    public function getAddFieldSQL($xmldb_table, $xmldb_field, $skip_type_clause = NULL, $skip_default_clause = NULL, $skip_notnull_clause = NULL) {

        $skip_type_clause = is_null($skip_type_clause) ? $this->alter_column_skip_type : $skip_type_clause;
        $skip_default_clause = is_null($skip_default_clause) ? $this->alter_column_skip_default : $skip_default_clause;
        $skip_notnull_clause = is_null($skip_notnull_clause) ? $this->alter_column_skip_notnull : $skip_notnull_clause;

        $results = array();

        // Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);

        // Build the standard alter table add
        $sql = $this->getFieldSQL($xmldb_table, $xmldb_field, $skip_type_clause,
                                  $skip_default_clause,
                                  $skip_notnull_clause);
        $altertable = 'ALTER TABLE ' . $tablename . ' ADD ' . $sql;
        // Add the after clause if necessary
        if ($this->add_after_clause && $xmldb_field->getPrevious()) {
            $altertable .= ' AFTER ' . $this->getEncQuoted($xmldb_field->getPrevious());
        }
        $results[] = $altertable;

        return $results;
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to drop the field from the table.
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

        // Build the standard alter table drop
        $results[] = 'ALTER TABLE ' . $tablename . ' DROP COLUMN ' . $fieldname;

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

        $skip_type_clause = is_null($skip_type_clause) ? $this->alter_column_skip_type : $skip_type_clause;
        $skip_default_clause = is_null($skip_default_clause) ? $this->alter_column_skip_default : $skip_default_clause;
        $skip_notnull_clause = is_null($skip_notnull_clause) ? $this->alter_column_skip_notnull : $skip_notnull_clause;

        $results = array();

        // Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);
        $fieldname = $this->getEncQuoted($xmldb_field->getName());

        // Build de alter sentence using the alter_column_sql template
        $alter = str_replace('TABLENAME', $this->getTableName($xmldb_table), $this->alter_column_sql);
        $colspec = $this->getFieldSQL($xmldb_table, $xmldb_field, $skip_type_clause,
                                      $skip_default_clause,
                                      $skip_notnull_clause,
                                      true);
        $alter = str_replace('COLUMNSPECS', $colspec, $alter);

        // Add the after clause if necessary
        if ($this->add_after_clause && $xmldb_field->getPrevious()) {
            $alter .= ' after ' . $this->getEncQuoted($xmldb_field->getPrevious());
        }

        // Build the standard alter table modify
        $results[] = $alter;

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

        $results = array();

        // Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);
        $fieldname = $this->getEncQuoted($xmldb_field->getName());

        // Decide if we are going to create/modify or to drop the default
        if ($xmldb_field->getDefault() === null) {
            $results = $this->getDropDefaultSQL($xmldb_table, $xmldb_field); //Drop
        } else {
            $results = $this->getCreateDefaultSQL($xmldb_table, $xmldb_field); //Create/modify
        }

        return $results;
    }

    /**
     * Given one correct xmldb_field and the new name, returns the SQL statements
     * to rename it (inside one array).
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

        $rename = str_replace('TABLENAME', $this->getTableName($xmldb_table), $this->rename_column_sql);
        $rename = str_replace('OLDFIELDNAME', $this->getEncQuoted($xmldb_field->getName()), $rename);
        $rename = str_replace('NEWFIELDNAME', $this->getEncQuoted($newname), $rename);

        $results[] = $rename;

        // Call to getRenameFieldExtraSQL(), override if needed
        $extra_sentences = $this->getRenameFieldExtraSQL($xmldb_table, $xmldb_field, $newname);
        $results = array_merge($results, $extra_sentences);

        return $results;
    }

    /**
     * Given one xmldb_table and one xmldb_key, return the SQL statements needed to add the key to the table
     * note that undelying indexes will be added as parametrised by $xxxx_keys and $xxxx_index parameters.
     *
     * @param xmldb_table $xmldb_table The table related to $xmldb_key.
     * @param xmldb_key $xmldb_key The xmldb_key to add.
     * @return array SQL statement to add the xmldb_key.
     */
    public function getAddKeySQL($xmldb_table, $xmldb_key) {

        $results = array();

        // Just use the CreateKeySQL function
        if ($keyclause = $this->getKeySQL($xmldb_table, $xmldb_key)) {
            $key = 'ALTER TABLE ' . $this->getTableName($xmldb_table) .
               ' ADD CONSTRAINT ' . $keyclause;
            $results[] = $key;
        }

        // If we aren't creating the keys OR if the key is XMLDB_KEY_FOREIGN (not underlying index generated
        // automatically by the RDBMS) create the underlying (created by us) index (if doesn't exists)
        if (!$keyclause || $xmldb_key->getType() == XMLDB_KEY_FOREIGN) {
            // Only if they don't exist
            if ($xmldb_key->getType() == XMLDB_KEY_FOREIGN) {      //Calculate type of index based on type ok key
                $indextype = XMLDB_INDEX_NOTUNIQUE;
            } else {
                $indextype = XMLDB_INDEX_UNIQUE;
            }
            $xmldb_index = new xmldb_index('anyname', $indextype, $xmldb_key->getFields());
            if (!$this->mdb->get_manager()->index_exists($xmldb_table, $xmldb_index)) {
                $results = array_merge($results, $this->getAddIndexSQL($xmldb_table, $xmldb_index));
            }
        }

        // If the key is XMLDB_KEY_FOREIGN_UNIQUE, create it as UNIQUE too
        if ($xmldb_key->getType() == XMLDB_KEY_FOREIGN_UNIQUE && $this->unique_keys) {
            //Duplicate the key
            $xmldb_key->setType(XMLDB_KEY_UNIQUE);
            $results = array_merge($results, $this->getAddKeySQL($xmldb_table, $xmldb_key));
        }

        // Return results
        return $results;
    }

    /**
     * Given one xmldb_table and one xmldb_index, return the SQL statements needed to drop the index from the table.
     *
     * @param xmldb_table $xmldb_table The table related to $xmldb_key.
     * @param xmldb_key $xmldb_key The xmldb_key to drop.
     * @return array SQL statement to drop the xmldb_key.
     */
    public function getDropKeySQL($xmldb_table, $xmldb_key) {

        $results = array();

        // Get the key name (note that this doesn't introspect DB, so could cause some problems sometimes!)
        // TODO: We'll need to overwrite the whole getDropKeySQL() method inside each DB to do the proper queries
        // against the dictionary or require ADOdb to support it or change the find_key_name() method to
        // perform DB introspection directly. But, for now, as we aren't going to enable referential integrity
        // it won't be a problem at all
        $dbkeyname = $this->mdb->get_manager()->find_key_name($xmldb_table, $xmldb_key);

        // Only if such type of key generation is enabled
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
        // If we have decided to drop the key, let's do it
        if ($dropkey) {
            // Replace TABLENAME, CONSTRAINTTYPE and KEYNAME as needed
            $dropsql = str_replace('TABLENAME', $this->getTableName($xmldb_table), $template);
            $dropsql = str_replace('KEYNAME', $dbkeyname, $dropsql);

            $results[] = $dropsql;
        }

        // If we aren't dropping the keys OR if the key is XMLDB_KEY_FOREIGN (not underlying index generated
        // automatically by the RDBMS) drop the underlying (created by us) index (if exists)
        if (!$dropkey || $xmldb_key->getType() == XMLDB_KEY_FOREIGN) {
            // Only if they exist
            $xmldb_index = new xmldb_index('anyname', XMLDB_INDEX_UNIQUE, $xmldb_key->getFields());
            if ($this->mdb->get_manager()->index_exists($xmldb_table, $xmldb_index)) {
                $results = array_merge($results, $this->getDropIndexSQL($xmldb_table, $xmldb_index));
            }
        }

        // If the key is XMLDB_KEY_FOREIGN_UNIQUE, drop the UNIQUE too
        if ($xmldb_key->getType() == XMLDB_KEY_FOREIGN_UNIQUE && $this->unique_keys) {
            //Duplicate the key
            $xmldb_key->setType(XMLDB_KEY_UNIQUE);
            $results = array_merge($results, $this->getDropKeySQL($xmldb_table, $xmldb_key));
        }

        // Return results
        return $results;
    }

    /**
     * Given one xmldb_table and one xmldb_key, return the SQL statements needed to rename the key in the table
     * Experimental! Shouldn't be used at all!
     *
     * @param xmldb_table $xmldb_table The table related to $xmldb_key.
     * @param xmldb_key $xmldb_key The xmldb_key to rename.
     * @param string $newname The xmldb_key's new name.
     * @return array SQL statement to rename the xmldb_key.
     */
    public function getRenameKeySQL($xmldb_table, $xmldb_key, $newname) {

        $results = array();

        // Get the real key name
        $dbkeyname = $this->mdb->get_manager()->find_key_name($xmldb_table, $xmldb_key);

        // Check we are really generating this type of keys
        if (($xmldb_key->getType() == XMLDB_KEY_PRIMARY && !$this->primary_keys) ||
            ($xmldb_key->getType() == XMLDB_KEY_UNIQUE && !$this->unique_keys) ||
            ($xmldb_key->getType() == XMLDB_KEY_FOREIGN && !$this->foreign_keys) ||
            ($xmldb_key->getType() == XMLDB_KEY_FOREIGN_UNIQUE && !$this->unique_keys && !$this->foreign_keys)) {
            // We aren't generating this type of keys, delegate to child indexes
            $xmldb_index = new xmldb_index($xmldb_key->getName());
            $xmldb_index->setFields($xmldb_key->getFields());
            return $this->getRenameIndexSQL($xmldb_table, $xmldb_index, $newname);
        }

        // Arrived here so we are working with keys, lets rename them
        // Replace TABLENAME and KEYNAME as needed
        $renamesql = str_replace('TABLENAME', $this->getTableName($xmldb_table), $this->rename_key_sql);
        $renamesql = str_replace('OLDKEYNAME', $dbkeyname, $renamesql);
        $renamesql = str_replace('NEWKEYNAME', $newname, $renamesql);

        // Some DB doesn't support key renaming so this can be empty
        if ($renamesql) {
            $results[] = $renamesql;
        }

        return $results;
    }

    /**
     * Given one xmldb_table and one xmldb_index, return the SQL statements needed to add the index to the table.
     *
     * @param xmldb_table $xmldb_table The xmldb_table instance to add the index on.
     * @param xmldb_index $xmldb_index The xmldb_index to add.
     * @return array An array of SQL statements to add the index.
     */
    public function getAddIndexSQL($xmldb_table, $xmldb_index) {

        // Just use the CreateIndexSQL function
        return $this->getCreateIndexSQL($xmldb_table, $xmldb_index);
    }

    /**
     * Given one xmldb_table and one xmldb_index, return the SQL statements needed to drop the index from the table.
     *
     * @param xmldb_table $xmldb_table The xmldb_table instance to drop the index on.
     * @param xmldb_index $xmldb_index The xmldb_index to drop.
     * @return array An array of SQL statements to drop the index.
     */
    public function getDropIndexSQL($xmldb_table, $xmldb_index) {

        $results = array();

        // Get the real index name
        $dbindexnames = $this->mdb->get_manager()->find_index_name($xmldb_table, $xmldb_index, true);

        // Replace TABLENAME and INDEXNAME as needed
        if ($dbindexnames) {
            foreach ($dbindexnames as $dbindexname) {
                $dropsql = str_replace('TABLENAME', $this->getTableName($xmldb_table), $this->drop_index_sql);
                $dropsql = str_replace('INDEXNAME', $this->getEncQuoted($dbindexname), $dropsql);
                $results[] = $dropsql;
            }
        }

        return $results;
    }

    /**
     * Given one xmldb_table and one xmldb_index, return the SQL statements needed to rename the index in the table
     * Experimental! Shouldn't be used at all!
     *
     * @param xmldb_table $xmldb_table The xmldb_table instance to rename the index on.
     * @param xmldb_index $xmldb_index The xmldb_index to rename.
     * @param string $newname The xmldb_index's new name.
     * @return array An array of SQL statements to rename the index.
     */
    function getRenameIndexSQL($xmldb_table, $xmldb_index, $newname) {
        // Some DB doesn't support index renaming (MySQL) so this can be empty
        if (empty($this->rename_index_sql)) {
            return array();
        }

        // Get the real index name
        $dbindexname = $this->mdb->get_manager()->find_index_name($xmldb_table, $xmldb_index);
        // Replace TABLENAME and INDEXNAME as needed
        $renamesql = str_replace('TABLENAME', $this->getTableName($xmldb_table), $this->rename_index_sql);
        $renamesql = str_replace('OLDINDEXNAME', $this->getEncQuoted($dbindexname), $renamesql);
        $renamesql = str_replace('NEWINDEXNAME', $this->getEncQuoted($newname), $renamesql);

        return array($renamesql);
    }

    /**
     * Given three strings (table name, list of fields (comma separated) and suffix),
     * create the proper object name quoting it if necessary.
     *
     * IMPORTANT: This function must be used to CALCULATE NAMES of objects TO BE CREATED,
     *            NEVER TO GUESS NAMES of EXISTING objects!!!
     *
     * @param string $tablename The table name.
     * @param string $fields A list of comma separated fields.
     * @param string $suffix A suffix for the object name.
     * @return string Object's name.
     */
    public function getNameForObject($tablename, $fields, $suffix='') {

        $name = '';

        // Implement one basic cache to avoid object name duplication
        // along all the request life, but never to return cached results
        // We need this because sql statements are created before executing
        // them, hence names doesn't exist "physically" yet in DB, so we need
        // to known which ones have been used
        static $used_names = array();

        // Use standard naming. See http://docs.moodle.org/en/XMLDB_key_and_index_naming
        $tablearr = explode ('_', $tablename);
        foreach ($tablearr as $table) {
            $name .= substr(trim($table),0,4);
        }
        $name .= '_';
        $fieldsarr = explode (',', $fields);
        foreach ($fieldsarr as $field) {
            $name .= substr(trim($field),0,3);
        }
        // Prepend the prefix
        $name = trim($this->prefix . $name);

        // Make sure name does not exceed the maximum name length and add suffix.
        $maxlengthwithoutsuffix = $this->names_max_length - strlen($suffix) - ($suffix ? 1 : 0);
        $namewithsuffix = substr($name, 0, $maxlengthwithoutsuffix) . ($suffix ? ('_' . $suffix) : '');

        // If the calculated name is in the cache, or if we detect it by introspecting the DB let's modify if
        $counter = 1;
        while (in_array($namewithsuffix, $used_names) || $this->isNameInUse($namewithsuffix, $suffix, $tablename)) {
            // Now iterate until not used name is found, incrementing the counter
            $counter++;
            $namewithsuffix = substr($name, 0, $maxlengthwithoutsuffix - strlen($counter)) .
                    $counter . ($suffix ? ('_' . $suffix) : '');
        }

        // Add the name to the cache
        $used_names[] = $namewithsuffix;

        // Quote it if necessary (reserved words)
        $namewithsuffix = $this->getEncQuoted($namewithsuffix);

        return $namewithsuffix;
    }

    /**
     * Given any string (or one array), enclose it by the proper quotes
     * if it's a reserved word
     *
     * @param string|array $input String to quote.
     * @return string Quoted string.
     */
    public function getEncQuoted($input) {

        if (is_array($input)) {
            foreach ($input as $key=>$content) {
                $input[$key] = $this->getEncQuoted($content);
            }
            return $input;
        } else {
            // Always lowercase
            $input = strtolower($input);
            // if reserved or quote_all or has hyphens, quote it
            if ($this->quote_all || in_array($input, $this->reserved_words) || strpos($input, '-') !== false) {
                $input = $this->quote_string . $input . $this->quote_string;
            }
            return $input;
        }
    }

    /**
     * Given one XMLDB Statement, build the needed SQL insert sentences to execute it.
     *
     * @param string $statement SQL statement.
     * @return array Array of sentences in the SQL statement.
     */
    function getExecuteInsertSQL($statement) {

         $results = array();  //Array where all the sentences will be stored

         if ($sentences = $statement->getSentences()) {
             foreach ($sentences as $sentence) {
                 // Get the list of fields
                 $fields = $statement->getFieldsFromInsertSentence($sentence);
                 // Get the values of fields
                 $values = $statement->getValuesFromInsertSentence($sentence);
                 // Look if we have some CONCAT value and transform it dynamically
                 foreach($values as $key => $value) {
                     // Trim single quotes
                     $value = trim($value,"'");
                     if (stristr($value, 'CONCAT') !== false){
                         // Look for data between parenthesis
                         preg_match("/CONCAT\s*\((.*)\)$/is", trim($value), $matches);
                         if (isset($matches[1])) {
                             $part = $matches[1];
                             // Convert the comma separated string to an array
                             $arr = xmldb_object::comma2array($part);
                             if ($arr) {
                                 $value = $this->getConcatSQL($arr);
                             }
                         }
                     }
                     // Values to be sent to DB must be properly escaped
                     $value = $this->addslashes($value);
                     // Back trimmed quotes
                     $value = "'" . $value . "'";
                     // Back to the array
                     $values[$key] = $value;
                 }

                 // Iterate over fields, escaping them if necessary
                 foreach($fields as $key => $field) {
                     $fields[$key] = $this->getEncQuoted($field);
                 }
                 // Build the final SQL sentence and add it to the array of results
             $sql = 'INSERT INTO ' . $this->getEncQuoted($this->prefix . $statement->getTable()) .
                         '(' . implode(', ', $fields) . ') ' .
                         'VALUES (' . implode(', ', $values) . ')';
                 $results[] = $sql;
             }

         }
         return $results;
    }

    /**
     * Given one array of elements, build the proper CONCAT expression, based
     * in the $concat_character setting. If such setting is empty, then
     * MySQL's CONCAT function will be used instead.
     *
     * @param array $elements An array of elements to concatenate.
     * @return mixed Returns the result of moodle_database::sql_concat() or false.
     * @uses moodle_database::sql_concat()
     * @uses call_user_func_array()
     */
    public function getConcatSQL($elements) {

        // Replace double quoted elements by single quotes
        foreach($elements as $key => $element) {
            $element = trim($element);
            if (substr($element, 0, 1) == '"' &&
                substr($element, -1, 1) == '"') {
                    $elements[$key] = "'" . trim($element, '"') . "'";
            }
        }

        // Now call the standard $DB->sql_concat() DML function
        return call_user_func_array(array($this->mdb, 'sql_concat'), $elements);
    }

    /**
     * Returns the name (string) of the sequence used in the table for the autonumeric pk
     * Only some DB have this implemented.
     *
     * @param xmldb_table $xmldb_table The xmldb_table instance.
     * @return bool Returns the sequence from the DB or false.
     */
    public function getSequenceFromDB($xmldb_table) {
        return false;
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
        return false; //For generators not implementing introspection,
                      //we always return with the name being free to be used
    }


// ====== FOLLOWING FUNCTION MUST BE CUSTOMISED BY ALL THE XMLDGenerator classes ========

    /**
     * Reset a sequence to the id field of a table.
     *
     * @param xmldb_table|string $table name of table or the table object.
     * @return array of sql statements
     */
    public abstract function getResetSequenceSQL($table);

    /**
     * Given one correct xmldb_table, returns the SQL statements
     * to create temporary table (inside one array).
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @return array of sql statements
     */
    abstract public function getCreateTempTableSQL($xmldb_table);

    /**
     * Given one XMLDB Type, length and decimals, returns the DB proper SQL type.
     *
     * @param int $xmldb_type The xmldb_type defined constant. XMLDB_TYPE_INTEGER and other XMLDB_TYPE_* constants.
     * @param int $xmldb_length The length of that data type.
     * @param int $xmldb_decimals The decimal places of precision of the data type.
     * @return string The DB defined data type.
     */
    public abstract function getTypeSQL($xmldb_type, $xmldb_length=null, $xmldb_decimals=null);

    /**
     * Returns the code (array of statements) needed to execute extra statements on field rename.
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @param xmldb_field $xmldb_field The xmldb_field object instance.
     * @return array Array of extra SQL statements to run with a field being renamed.
     */
    public function getRenameFieldExtraSQL($xmldb_table, $xmldb_field) {
        return array();
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
        return array();
    }

    /**
     * Returns the code (array of statements) needed to add one comment to the table.
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @return array Array of SQL statements to add one comment to the table.
     */
    public abstract function getCommentSQL($xmldb_table);

    /**
     * Returns the code (array of statements) needed to execute extra statements on table rename.
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @param string $newname The new name for the table.
     * @return array Array of extra SQL statements to rename a table.
     */
    public function getRenameTableExtraSQL($xmldb_table, $newname) {
        return array();
    }

    /**
     * Returns the code (array of statements) needed to execute extra statements on table drop
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @return array Array of extra SQL statements to drop a table.
     */
    public function getDropTableExtraSQL($xmldb_table) {
        return array();
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
    public abstract function getDropDefaultSQL($xmldb_table, $xmldb_field);

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to add its default
     * (usually invoked from getModifyDefaultSQL()
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @param xmldb_field $xmldb_field The xmldb_field object instance.
     * @return array Array of SQL statements to create a field's default.
     */
    public abstract function getCreateDefaultSQL($xmldb_table, $xmldb_field);

    /**
     * Returns an array of reserved words (lowercase) for this DB
     * You MUST provide the real list for each DB inside every XMLDB class.
     * @return array An array of database specific reserved words.
     * @throws coding_exception Thrown if not implemented for the specific DB.
     */
    public static function getReservedWords() {
        throw new coding_exception('getReservedWords() method needs to be overridden in each subclass of sql_generator');
    }

    /**
     * Returns all reserved words in supported databases.
     * Reserved words should be lowercase.
     * @return array ('word'=>array(databases))
     */
    public static function getAllReservedWords() {
        global $CFG;

        $generators = array('mysql', 'postgres', 'oracle', 'mssql');
        $reserved_words = array();

        foreach($generators as $generator) {
            $class = $generator . '_sql_generator';
            require_once("$CFG->libdir/ddl/$class.php");
            foreach (call_user_func(array($class, 'getReservedWords')) as $word) {
                $reserved_words[$word][] = $generator;
            }
        }
        ksort($reserved_words);
        return $reserved_words;
    }

    /**
     * Adds slashes to string.
     * @param string $s
     * @return string The escaped string.
     */
    public function addslashes($s) {
        // do not use php addslashes() because it depends on PHP quote settings!
        $s = str_replace('\\','\\\\',$s);
        $s = str_replace("\0","\\\0", $s);
        $s = str_replace("'",  "\\'", $s);
        return $s;
    }
}
