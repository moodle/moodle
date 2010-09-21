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
 * Mysql specific SQL code generator.
 *
 * @package    core
 * @subpackage ddl
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/ddl/sql_generator.php');

/// This class generate SQL code to be used against MySQL
/// It extends XMLDBgenerator so everything can be
/// overridden as needed to generate correct SQL.

class mysql_sql_generator extends sql_generator {

/// Only set values that are different from the defaults present in XMLDBgenerator

    public $quote_string = '`';   // String used to quote names

    public $default_for_char = '';      // To define the default to set for NOT NULLs CHARs without default (null=do nothing)

    public $drop_default_value_required = true; //To specify if the generator must use some DEFAULT clause to drop defaults
    public $drop_default_value = NULL; //The DEFAULT clause required to drop defaults

    public $primary_key_name = ''; //To force primary key names to one string (null=no force)

    public $drop_primary_key = 'ALTER TABLE TABLENAME DROP PRIMARY KEY'; // Template to drop PKs
                // with automatic replace for TABLENAME and KEYNAME

    public $drop_unique_key = 'ALTER TABLE TABLENAME DROP KEY KEYNAME'; // Template to drop UKs
                // with automatic replace for TABLENAME and KEYNAME

    public $drop_foreign_key = 'ALTER TABLE TABLENAME DROP FOREIGN KEY KEYNAME'; // Template to drop FKs
                // with automatic replace for TABLENAME and KEYNAME

    public $sequence_extra_code = false; //Does the generator need to add extra code to generate the sequence fields
    public $sequence_name = 'auto_increment'; //Particular name for inline sequences in this generator

    public $enum_extra_code = false; //Does the generator need to add extra code to generate code for the enums in the table

    public $add_after_clause = true; // Does the generator need to add the after clause for fields

    public $concat_character = null; //Characters to be used as concatenation operator. If not defined
                                  //MySQL CONCAT function will be use

    public $alter_column_sql = 'ALTER TABLE TABLENAME MODIFY COLUMN COLUMNSPECS'; //The SQL template to alter columns

    public $drop_index_sql = 'ALTER TABLE TABLENAME DROP INDEX INDEXNAME'; //SQL sentence to drop one index
                                                               //TABLENAME, INDEXNAME are dynamically replaced

    public $rename_index_sql = null; //SQL sentence to rename one index (MySQL doesn't support this!)
                                      //TABLENAME, OLDINDEXNAME, NEWINDEXNAME are dynamically replaced

    public $rename_key_sql = null; //SQL sentence to rename one key (MySQL doesn't support this!)
                                      //TABLENAME, OLDKEYNAME, NEWKEYNAME are dynamically replaced

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

        // From http://dev.mysql.com/doc/refman/5.0/en/alter-table.html
        $value = (int)$this->mdb->get_field_sql('SELECT MAX(id) FROM {'.$tablename.'}');
        $value++;
        return array("ALTER TABLE $this->prefix$tablename AUTO_INCREMENT = $value");
    }

    /**
     * Given one correct xmldb_table, returns the SQL statements
     * to create it (inside one array)
     */
    public function getCreateTableSQL($xmldb_table) {
        // first find out if want some special db engine
        $engine = null;
        if (method_exists($this->mdb, 'get_dbengine')) {
            $engine = $this->mdb->get_dbengine();
        }

        $sqlarr = parent::getCreateTableSQL($xmldb_table);

        if (!$engine) {
            // we rely on database defaults
            return $sqlarr;
        }

        // let's inject the engine into SQL
        foreach ($sqlarr as $i=>$sql) {
            if (strpos($sql, 'CREATE TABLE ') === 0) {
                $sqlarr[$i] .= " ENGINE = $engine";
            }
        }

        return $sqlarr;
    }

    /**
     * Given one correct xmldb_table, returns the SQL statements
     * to create temporary table (inside one array)
     */
    public function getCreateTempTableSQL($xmldb_table) {
        $this->temptables->add_temptable($xmldb_table->getName());
        $sqlarr = parent::getCreateTableSQL($xmldb_table); // we do not want the engine hack included in create table SQL
        $sqlarr = preg_replace('/^CREATE TABLE (.*)/s', 'CREATE TEMPORARY TABLE $1', $sqlarr);
        return $sqlarr;
    }

    /**
     * Given one correct xmldb_table and the new name, returns the SQL statements
     * to drop it (inside one array)
     */
    public function getDropTempTableSQL($xmldb_table) {
        $sqlarr = $this->getDropTableSQL($xmldb_table);
        $sqlarr = preg_replace('/^DROP TABLE/', "DROP TEMPORARY TABLE", $sqlarr);
        $this->temptables->delete_temptable($xmldb_table->getName());
        return $sqlarr;
    }

    /**
     * Given one XMLDB Type, length and decimals, returns the DB proper SQL type
     */
    public function getTypeSQL($xmldb_type, $xmldb_length=null, $xmldb_decimals=null) {

        switch ($xmldb_type) {
            case XMLDB_TYPE_INTEGER:    // From http://mysql.com/doc/refman/5.0/en/numeric-types.html!
                if (empty($xmldb_length)) {
                    $xmldb_length = 10;
                }
                if ($xmldb_length > 9) {
                    $dbtype = 'BIGINT';
                } else if ($xmldb_length > 6) {
                    $dbtype = 'INT';
                } else if ($xmldb_length > 4) {
                    $dbtype = 'MEDIUMINT';
                } else if ($xmldb_length > 2) {
                    $dbtype = 'SMALLINT';
                } else {
                    $dbtype = 'TINYINT';
                }
                $dbtype .= '(' . $xmldb_length . ')';
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
                $dbtype = 'DOUBLE';
                if (!empty($xmldb_decimals)) {
                    if ($xmldb_decimals < 6) {
                        $dbtype = 'FLOAT';
                    }
                }
                if (!empty($xmldb_length)) {
                    $dbtype .= '(' . $xmldb_length;
                    if (!empty($xmldb_decimals)) {
                        $dbtype .= ',' . $xmldb_decimals;
                    } else {
                        $dbtype .= ', 0'; // In MySQL, if length is specified, decimals are mandatory for FLOATs
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
            case XMLDB_TYPE_TEXT:
                if (empty($xmldb_length)) {
                    $xmldb_length = 'small';
                }
                if ($xmldb_length == 'small') {
                    $dbtype = 'TEXT';
                } else if ($xmldb_length == 'medium') {
                    $dbtype = 'MEDIUMTEXT';
                } else {
                    $dbtype = 'LONGTEXT';
                }
                break;
            case XMLDB_TYPE_BINARY:
                if (empty($xmldb_length)) {
                    $xmldb_length = 'small';
                }
                if ($xmldb_length == 'small') {
                    $dbtype = 'BLOB';
                } else if ($xmldb_length == 'medium') {
                    $dbtype = 'MEDIUMBLOB';
                } else {
                    $dbtype = 'LONGBLOB';
                }
                break;
            case XMLDB_TYPE_DATETIME:
                $dbtype = 'DATETIME';
        }
        return $dbtype;
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to create its enum
     * (usually invoked from getModifyEnumSQL()
     */
    public function getCreateEnumSQL($xmldb_table, $xmldb_field) {
    /// For MySQL, just alter the field
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to drop its enum
     * (usually invoked from getModifyEnumSQL()
     *
     * TODO: Moodle 2.1 - drop in Moodle 2.1
     */
    public function getDropEnumSQL($xmldb_table, $xmldb_field) {
    /// Let's introspect to know if there is one enum
        if ($check_constraints = $this->getCheckConstraintsFromDB($xmldb_table, $xmldb_field)) {
        /// For MySQL, just alter the field
            return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
        } else {
            return array(); /// Enum not found. Nothing to do
        }
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to create its default
     * (usually invoked from getModifyDefaultSQL()
     */
    public function getCreateDefaultSQL($xmldb_table, $xmldb_field) {
    /// Just a wrapper over the getAlterFieldSQL() function for MySQL that
    /// is capable of handling defaults
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**
     * Given one correct xmldb_field and the new name, returns the SQL statements
     * to rename it (inside one array)
     * MySQL is pretty different from the standard to justify this overloading
     */
    public function getRenameFieldSQL($xmldb_table, $xmldb_field, $newname) {

    /// Need a clone of xmldb_field to perform the change leaving original unmodified
        $xmldb_field_clone = clone($xmldb_field);

    /// Change the name of the field to perform the change
        $xmldb_field_clone->setName($xmldb_field_clone->getName() . ' ' . $newname);

        $fieldsql = $this->getFieldSQL($xmldb_field_clone);

        $sql = 'ALTER TABLE ' . $this->getTableName($xmldb_table) . ' CHANGE ' . $fieldsql;

        return array($sql);
    }

    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to drop its default
     * (usually invoked from getModifyDefaultSQL()
     */
    public function getDropDefaultSQL($xmldb_table, $xmldb_field) {
    /// Just a wrapper over the getAlterFieldSQL() function for MySQL that
    /// is capable of handling defaults
        return $this->getAlterFieldSQL($xmldb_table, $xmldb_field);
    }

    /**
     * Returns the code (in array) needed to add one comment to the table
     */
    function getCommentSQL ($xmldb_table) {
        $comment = '';

        if ($xmldb_table->getComment()) {
            $comment .= 'ALTER TABLE ' . $this->getTableName($xmldb_table);
            $comment .= " COMMENT='" . $this->addslashes(substr($xmldb_table->getComment(), 0, 60)) . "'";
        }
        return array($comment);
    }

    /**
     * Given one xmldb_table returns one array with all the check constraints
     * in the table (fetched from DB)
     * Optionally the function allows one xmldb_field to be specified in
     * order to return only the check constraints belonging to one field.
     * Each element contains the name of the constraint and its description
     * If no check constraints are found, returns an empty array
     * MySQL doesn't have check constraints in this implementation, but
     * we return them based on the enum fields in the table
     *
     * TODO: Moodle 2.1 - drop in Moodle 2.1
     */
    public function getCheckConstraintsFromDB($xmldb_table, $xmldb_field = null) {

        $tablename = $xmldb_table->getName($xmldb_table);

    /// Fetch all the columns in the table
        if (!$columns = $this->mdb->get_columns($tablename)) {
            return array();
        }

    /// Filter by the required field if specified
        if ($xmldb_field) {
            $filter = $xmldb_field->getName();
            if (!isset($columns[$filter])) {
                return array();
            }
            $column = ($columns[$filter]);
            if (!empty($column->enums)) {
                $result = new stdClass();
                $result->name = $filter;
                $result->description = implode(', ', $column->enums);
                return array($result);
            } else {
                return array();
            }

        } else {
            $results = array();
        /// Iterate over columns searching for enums
            foreach ($columns as $key => $column) {
            /// Enum found, let's add it to the constraints list
                if (!empty($column->enums)) {
                    $result = new stdClass();
                    $result->name = $key;
                    $result->description = implode(', ', $column->enums);
                    $results[$key] = $result;
                }
            }
            return $results;
        }
    }

    /**
     * Given one object name and it's type (pk, uk, fk, ck, ix, uix, seq, trg)
     * return if such name is currently in use (true) or no (false)
     * (invoked from getNameForObject()
     */
    public function isNameInUse($object_name, $type, $table_name) {

    /// Calculate the real table name
        $xmldb_table = new xmldb_table($table_name);
        $tname = $this->getTableName($xmldb_table);

        switch($type) {
            case 'ix':
            case 'uix':
            /// First of all, check table exists
                $metatables = $this->mdb->get_tables();
                if (isset($metatables[$tname])) {
                /// Fetch all the indexes in the table
                    if ($indexes = $this->mdb->get_indexes($tname)) {
                    /// Look for existing index in array
                        if (isset($indexes[$object_name])) {
                            return true;
                        }
                    }
                }
                break;
        }
        return false; //No name in use found
    }


    /**
     * Returns an array of reserved words (lowercase) for this DB
     */
    public static function getReservedWords() {
    /// This file contains the reserved words for MySQL databases
    /// from http://dev.mysql.com/doc/refman/6.0/en/reserved-words.html
        $reserved_words = array (
            'accessible', 'add', 'all', 'alter', 'analyze', 'and', 'as', 'asc',
            'asensitive', 'before', 'between', 'bigint', 'binary',
            'blob', 'both', 'by', 'call', 'cascade', 'case', 'change',
            'char', 'character', 'check', 'collate', 'column',
            'condition', 'connection', 'constraint', 'continue',
            'convert', 'create', 'cross', 'current_date', 'current_time',
            'current_timestamp', 'current_user', 'cursor', 'database',
            'databases', 'day_hour', 'day_microsecond',
            'day_minute', 'day_second', 'dec', 'decimal', 'declare',
            'default', 'delayed', 'delete', 'desc', 'describe',
            'deterministic', 'distinct', 'distinctrow', 'div', 'double',
            'drop', 'dual', 'each', 'else', 'elseif', 'enclosed', 'escaped',
            'exists', 'exit', 'explain', 'false', 'fetch', 'float', 'float4',
            'float8', 'for', 'force', 'foreign', 'from', 'fulltext', 'grant',
            'group', 'having', 'high_priority', 'hour_microsecond',
            'hour_minute', 'hour_second', 'if', 'ignore', 'in', 'index',
            'infile', 'inner', 'inout', 'insensitive', 'insert', 'int', 'int1',
            'int2', 'int3', 'int4', 'int8', 'integer', 'interval', 'into', 'is',
            'iterate', 'join', 'key', 'keys', 'kill', 'leading', 'leave', 'left',
            'like', 'limit', 'linear', 'lines', 'load', 'localtime', 'localtimestamp',
            'lock', 'long', 'longblob', 'longtext', 'loop', 'low_priority', 'master_heartbeat_period',
            'master_ssl_verify_server_cert', 'match', 'mediumblob', 'mediumint', 'mediumtext',
            'middleint', 'minute_microsecond', 'minute_second',
            'mod', 'modifies', 'natural', 'not', 'no_write_to_binlog',
            'null', 'numeric', 'on', 'optimize', 'option', 'optionally',
            'or', 'order', 'out', 'outer', 'outfile', 'overwrite', 'precision', 'primary',
            'procedure', 'purge', 'raid0', 'range', 'read', 'read_only', 'read_write', 'reads', 'real',
            'references', 'regexp', 'release', 'rename', 'repeat', 'replace',
            'require', 'restrict', 'return', 'revoke', 'right', 'rlike', 'schema',
            'schemas', 'second_microsecond', 'select', 'sensitive',
            'separator', 'set', 'show', 'smallint', 'soname', 'spatial',
            'specific', 'sql', 'sqlexception', 'sqlstate', 'sqlwarning',
            'sql_big_result', 'sql_calc_found_rows', 'sql_small_result',
            'ssl', 'starting', 'straight_join', 'table', 'terminated', 'then',
            'tinyblob', 'tinyint', 'tinytext', 'to', 'trailing', 'trigger', 'true',
            'undo', 'union', 'unique', 'unlock', 'unsigned', 'update',
            'upgrade', 'usage', 'use', 'using', 'utc_date', 'utc_time',
            'utc_timestamp', 'values', 'varbinary', 'varchar', 'varcharacter',
            'varying', 'when', 'where', 'while', 'with', 'write', 'x509',
            'xor', 'year_month', 'zerofill'
        );
        return $reserved_words;
    }
}
