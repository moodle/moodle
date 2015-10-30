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
 * Experimental SQLite specific SQL code generator.
 *
 * @package    core_ddl
 * @copyright  2008 Andrei Bautu
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/ddl/sql_generator.php');

/// This class generate SQL code to be used against SQLite
/// It extends XMLDBgenerator so everything can be
/// overridden as needed to generate correct SQL.

class sqlite_sql_generator extends sql_generator {

/// Only set values that are different from the defaults present in XMLDBgenerator

    /** @var bool To specify if the generator must use some DEFAULT clause to drop defaults.*/
    public $drop_default_value_required = true;

    /** @var string The DEFAULT clause required to drop defaults.*/
    public $drop_default_value = NULL;

    /** @var string Template to drop PKs. 'TABLENAME' and 'KEYNAME' will be replaced from this template.*/
    public $drop_primary_key = 'ALTER TABLE TABLENAME DROP PRIMARY KEY';

    /** @var string Template to drop UKs. 'TABLENAME' and 'KEYNAME' will be replaced from this template.*/
    public $drop_unique_key = 'ALTER TABLE TABLENAME DROP KEY KEYNAME';

    /** @var string Template to drop FKs. 'TABLENAME' and 'KEYNAME' will be replaced from this template.*/
    public $drop_foreign_key = 'ALTER TABLE TABLENAME DROP FOREIGN KEY KEYNAME';

    /** @var string To define the default to set for NOT NULLs CHARs without default (null=do nothing).*/
    public $default_for_char = '';

    /** @var bool To avoid outputting the rest of the field specs, leaving only the name and the sequence_name returned.*/
    public $sequence_only = true;

    /** @var bool True if the generator needs to add extra code to generate the sequence fields.*/
    public $sequence_extra_code = false;

    /** @var string The particular name for inline sequences in this generator.*/
    public $sequence_name = 'INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL';

    /** @var string SQL sentence to drop one index where 'TABLENAME', 'INDEXNAME' keywords are dynamically replaced.*/
    public $drop_index_sql = 'ALTER TABLE TABLENAME DROP INDEX INDEXNAME';

    /** @var string SQL sentence to rename one index where 'TABLENAME', 'OLDINDEXNAME' and 'NEWINDEXNAME' are dynamically replaced.*/
    public $rename_index_sql = null;

    /** @var string SQL sentence to rename one key 'TABLENAME', 'OLDKEYNAME' and 'NEWKEYNAME' are dynamically replaced.*/
    public $rename_key_sql = null;

    /**
     * Creates one new XMLDBmysql
     */
    public function __construct($mdb) {
        parent::__construct($mdb);
    }

    /**
     * Reset a sequence to the id field of a table.
     *
     * @param xmldb_table|string $table name of table or the table object.
     * @return array of sql statements
     */
    public function getResetSequenceSQL($table) {

        if ($table instanceof xmldb_table) {
            $table = $table->getName();
        }

        // From http://sqlite.org/autoinc.html
        $value = (int)$this->mdb->get_field_sql('SELECT MAX(id) FROM {'.$table.'}');
        return array("UPDATE sqlite_sequence SET seq=$value WHERE name='{$this->prefix}{$table}'");
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
     * Given one XMLDB Type, length and decimals, returns the DB proper SQL type.
     *
     * @param int $xmldb_type The xmldb_type defined constant. XMLDB_TYPE_INTEGER and other XMLDB_TYPE_* constants.
     * @param int $xmldb_length The length of that data type.
     * @param int $xmldb_decimals The decimal places of precision of the data type.
     * @return string The DB defined data type.
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
     * Function to emulate full ALTER TABLE which SQLite does not support.
     * The function can be used to drop a column ($xmldb_delete_field != null and
     * $xmldb_add_field == null), add a column ($xmldb_delete_field == null and
     * $xmldb_add_field != null), change/rename a column ($xmldb_delete_field == null
     * and $xmldb_add_field == null).
     * @param xmldb_table $xmldb_table table to change
     * @param xmldb_field $xmldb_add_field column to create/modify (full specification is required)
     * @param xmldb_field $xmldb_delete_field column to delete/modify (only name field is required)
     * @return array of strings (SQL statements to alter the table structure)
     */
    protected function getAlterTableSchema($xmldb_table, $xmldb_add_field=NULL, $xmldb_delete_field=NULL) {
    /// Get the quoted name of the table and field
        $tablename = $this->getTableName($xmldb_table);

        $oldname = $xmldb_delete_field ? $xmldb_delete_field->getName() : NULL;
        $newname = $xmldb_add_field ? $xmldb_add_field->getName() : NULL;
        if($xmldb_delete_field) {
            $xmldb_table->deleteField($oldname);
        }
        if($xmldb_add_field) {
            $xmldb_table->addField($xmldb_add_field);
        }
        if($oldname) {
            // alter indexes
            $indexes = $xmldb_table->getIndexes();
            foreach($indexes as $index) {
                $fields = $index->getFields();
                $i = array_search($oldname, $fields);
                if($i!==FALSE) {
                    if($newname) {
                        $fields[$i] = $newname;
                    } else {
                        unset($fields[$i]);
                    }
                    $xmldb_table->deleteIndex($index->getName());
                    if(count($fields)) {
                        $index->setFields($fields);
                        $xmldb_table->addIndex($index);
                    }
                }
            }
            // alter keys
            $keys = $xmldb_table->getKeys();
            foreach($keys as $key) {
                $fields = $key->getFields();
                $reffields = $key->getRefFields();
                $i = array_search($oldname, $fields);
                if($i!==FALSE) {
                    if($newname) {
                        $fields[$i] = $newname;
                    } else {
                        unset($fields[$i]);
                        unset($reffields[$i]);
                    }
                    $xmldb_table->deleteKey($key->getName());
                    if(count($fields)) {
                        $key->setFields($fields);
                        $key->setRefFields($fields);
                        $xmldb_table->addkey($key);
                    }
                }
            }
        }
        // prepare data copy
        $fields = $xmldb_table->getFields();
        foreach ($fields as $key => $field) {
            $fieldname = $field->getName();
            if($fieldname == $newname && $oldname && $oldname != $newname) {
                // field rename operation
                $fields[$key] = $this->getEncQuoted($oldname) . ' AS ' . $this->getEncQuoted($newname);
            } else {
                $fields[$key] = $this->getEncQuoted($field->getName());
            }
        }
        $fields = implode(',', $fields);
        $results[] = 'BEGIN TRANSACTION';
        $results[] = 'CREATE TEMPORARY TABLE temp_data AS SELECT * FROM ' . $tablename;
        $results[] = 'DROP TABLE ' . $tablename;
        $results = array_merge($results, $this->getCreateTableSQL($xmldb_table));
        $results[] = 'INSERT INTO ' . $tablename . ' SELECT ' . $fields . ' FROM temp_data';
        $results[] = 'DROP TABLE temp_data';
        $results[] = 'COMMIT';
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
        return $this->getAlterTableSchema($xmldb_table, $xmldb_field, $xmldb_field);
    }

    /**
     * Given one xmldb_table and one xmldb_key, return the SQL statements needed to add the key to the table
     * note that underlying indexes will be added as parametrised by $xxxx_keys and $xxxx_index parameters
     */
    public function getAddKeySQL($xmldb_table, $xmldb_key) {
        $xmldb_table->addKey($xmldb_key);
        return $this->getAlterTableSchema($xmldb_table);
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
        return $this->getAlterTableSchema($xmldb_table, $xmldb_field, $xmldb_field);
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
        $oldfield = clone($xmldb_field);
        $xmldb_field->setName($newname);
        return $this->getAlterTableSchema($xmldb_table, $xmldb_field, $oldfield);
    }

    /**
     * Given one xmldb_table and one xmldb_index, return the SQL statements needed to rename the index in the table
     */
    function getRenameIndexSQL($xmldb_table, $xmldb_index, $newname) {
    /// Get the real index name
        $dbindexname = $this->mdb->get_manager()->find_index_name($xmldb_table, $xmldb_index);
        $xmldb_index->setName($newname);
        $results = array('DROP INDEX ' . $dbindexname);
        $results = array_merge($results, $this->getCreateIndexSQL($xmldb_table, $xmldb_index));
        return $results;
    }

    /**
     * Given one xmldb_table and one xmldb_key, return the SQL statements needed to rename the key in the table
     * Experimental! Shouldn't be used at all!
     */
    public function getRenameKeySQL($xmldb_table, $xmldb_key, $newname) {
        $xmldb_table->deleteKey($xmldb_key->getName());
        $xmldb_key->setName($newname);
        $xmldb_table->addkey($xmldb_key);
        return $this->getAlterTableSchema($xmldb_table);
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to drop the field from the table.
     *
     * @param xmldb_table $xmldb_table The table related to $xmldb_field.
     * @param xmldb_field $xmldb_field The instance of xmldb_field to create the SQL from.
     * @return array The SQL statement for dropping a field from the table.
     */
    public function getDropFieldSQL($xmldb_table, $xmldb_field) {
        return $this->getAlterTableSchema($xmldb_table, NULL, $xmldb_field);
    }

    /**
     * Given one xmldb_table and one xmldb_index, return the SQL statements needed to drop the index from the table
     */
    public function getDropIndexSQL($xmldb_table, $xmldb_index) {
        $xmldb_table->deleteIndex($xmldb_index->getName());
        return $this->getAlterTableSchema($xmldb_table);
    }

    /**
     * Given one xmldb_table and one xmldb_index, return the SQL statements needed to drop the index from the table
     */
    public function getDropKeySQL($xmldb_table, $xmldb_key) {
        $xmldb_table->deleteKey($xmldb_key->getName());
        return $this->getAlterTableSchema($xmldb_table);
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
        return $this->getAlterTableSchema($xmldb_table, $xmldb_field, $xmldb_field);
    }

    /**
     * Returns the code (array of statements) needed to add one comment to the table.
     *
     * @param xmldb_table $xmldb_table The xmldb_table object instance.
     * @return array Array of SQL statements to add one comment to the table.
     */
    function getCommentSQL ($xmldb_table) {
        return array();
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
        // TODO: add introspection code
        return false; //No name in use found
    }

    /**
     * Returns an array of reserved words (lowercase) for this DB
     * @return array An array of database specific reserved words
     */
    public static function getReservedWords() {
    /// From http://www.sqlite.org/lang_keywords.html
        $reserved_words = array (
            'add', 'all', 'alter', 'and', 'as', 'autoincrement',
            'between', 'by',
            'case', 'check', 'collate', 'column', 'commit', 'constraint', 'create', 'cross',
            'default', 'deferrable', 'delete', 'distinct', 'drop',
            'else', 'escape', 'except', 'exists',
            'foreign', 'from', 'full',
            'group',
            'having',
            'in', 'index', 'inner', 'insert', 'intersect', 'into', 'is', 'isnull',
            'join',
            'left', 'limit',
            'natural', 'not', 'notnull', 'null',
            'on', 'or', 'order', 'outer',
            'primary',
            'references', 'regexp', 'right', 'rollback',
            'select', 'set',
            'table', 'then', 'to', 'transaction',
            'union', 'unique', 'update', 'using',
            'values',
            'when', 'where'
        );
        return $reserved_words;
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
}
