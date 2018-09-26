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
 * Abstract database driver test class providing some moodle database interface
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Srdjan Janković, Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../moodle_database.php');
require_once(__DIR__.'/../../moodle_temptables.php');
require_once(__DIR__.'/../../../ddl/database_manager.php');
require_once(__DIR__.'/../../../ddl/sql_generator.php');

/**
 * Test sql generator class
 *
 * @package    core
 * @category   ddl
 * @copyright  2018 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_sql_generator extends sql_generator {
    // @codingStandardsIgnoreStart
    /**
     * Reset a sequence to the id field of a table.
     *
     * @param xmldb_table|string $table name of table or the table object.
     * @return array of sql statements
     */
    public function getResetSequenceSQL($table) {
    // @codingStandardsIgnoreEnd
        return [];
    }

    // @codingStandardsIgnoreStart
    /**
     * Given one correct xmldb_table, returns the SQL statements
     * to create temporary table (inside one array).
     *
     * @param xmldb_table $xmldbtable The xmldb_table object instance.
     * @return array of sql statements
     */
    public function getCreateTempTableSQL($xmldbtable) {
    // @codingStandardsIgnoreEnd
        return [];
    }

    // @codingStandardsIgnoreStart
    /**
     * Given one XMLDB Type, length and decimals, returns the DB proper SQL type.
     *
     * @param int $xmldbtype The xmldb_type defined constant. XMLDB_TYPE_INTEGER and other XMLDB_TYPE_* constants.
     * @param int $xmldblength The length of that data type.
     * @param int $xmldbdecimals The decimal places of precision of the data type.
     * @return string The DB defined data type.
     */
    public function getTypeSQL($xmldbtype, $xmldblength=null, $xmldbdecimals=null) {
    // @codingStandardsIgnoreEnd
        return '';
    }

    // @codingStandardsIgnoreStart
    /**
     * Returns the code (array of statements) needed to add one comment to the table.
     *
     * @param xmldb_table $xmldbtable The xmldb_table object instance.
     * @return array Array of SQL statements to add one comment to the table.
     */
    function getCommentSQL ($xmldbtable) {
    // @codingStandardsIgnoreEnd
        return [];
    }

    // @codingStandardsIgnoreStart
    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to add its default
     * (usually invoked from getModifyDefaultSQL()
     *
     * @param xmldb_table $xmldbtable The xmldb_table object instance.
     * @param xmldb_field $xmldbfield The xmldb_field object instance.
     * @return array Array of SQL statements to create a field's default.
     */
    public function getCreateDefaultSQL($xmldbtable, $xmldbfield) {
    // @codingStandardsIgnoreEnd
        return [];
    }

    // @codingStandardsIgnoreStart
    /**
     * Given one xmldb_table and one xmldb_field, return the SQL statements needed to drop its default
     * (usually invoked from getModifyDefaultSQL()
     *
     * @param xmldb_table $xmldbtable The xmldb_table object instance.
     * @param xmldb_field $xmldbfield The xmldb_field object instance.
     * @return array Array of SQL statements to create a field's default.
     */
    public function getDropDefaultSQL($xmldbtable, $xmldbfield) {
    // @codingStandardsIgnoreEnd
        return [];
    }

    // @codingStandardsIgnoreStart
    /**
     * Returns an array of reserved words (lowercase) for this DB
     * @return array An array of database specific reserved words
     */
    public static function getReservedWords() {
    // @codingStandardsIgnoreEnd
        return [];
    }

}

/**
 * Abstract database driver test class
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class test_moodle_database extends moodle_database {

    /** @var string */
    private $error;

    /** @var array */
    private $_tables = [];

    /**
     * Constructor - Instantiates the database
     * @param bool $external True means that an external database is used.
     */
    public function __construct($external=false) {
        parent::__construct($external);

        $this->temptables = new moodle_temptables($this);
    }

    /**
     * Default implementation
     * @return boolean true
     */
    public function driver_installed() {
        return true;
    }

    /**
     * Default implementation
     * @return string 'test'
     */
    public function get_dbfamily() {
        return 'test';
    }

    /**
     * Default implementation
     * @return string 'test'
     */
    protected function get_dbtype() {
        return 'test';
    }

    /**
     * Default implementation
     * @return string 'test'
     */
    protected function get_dblibrary() {
        return 'test';
    }

    /**
     * Default implementation
     * @return string 'test'
     */
    public function get_name() {
        return 'test';
    }

    /**
     * Default implementation
     * @return string
     */
    public function get_configuration_help() {
        return 'test database driver';
    }

    /**
     * Default implementation
     * @return array
     */
    public function get_server_info() {
        return ['description' => $this->name(), 'version' => '0'];
    }

    /**
     * Default implementation
     * @return int 0
     */
    protected function allowed_param_types() {
        return 0;
    }

    /**
     * Returns error property
     * @return string $error
     */
    public function get_last_error() {
        return $this->error;
    }

    /**
     * Sets tables property
     * @param array $tables
     * @return void
     */
    public function set_tables($tables) {
        $this->_tables = $tables;
    }

    /**
     * Returns keys of tables property
     * @param bool $usecache
     * @return array $tablenames
     */
    public function get_tables($usecache=true) {
        return array_keys($this->_tables);
    }

    /**
     * Return table indexes
     * @param string $table
     * @return array $indexes
     */
    public function get_indexes($table) {
        return isset($this->_tables[$table]['indexes']) ? $this->_tables[$table]['indexes'] : [];
    }

    /**
     * Return table columns
     * @param string $table
     * @param bool $usecache
     * @return array $indexes
     */
    public function get_columns($table, $usecache=true) {
        return $this->_tables[$table]['columns'];
    }

    /**
     * Default implementation
     * @param StdClass $column metadata
     * @param mixed $value
     * @return mixed $value
     */
    protected function normalise_value($column, $value) {
        return $value;
    }

    /**
     * Default implementation
     * @param string|array $sql
     * @param array|null $tablenames
     * @return bool true
     */
    public function change_database_structure($sql, $tablenames = null) {
        return true;
    }

    /**
     * Default implementation, throws Exception
     * @param string $sql
     * @param array $params
     * @return bool true
     * @throws Exception
     */
    public function execute($sql, array $params = null) {
        throw new Exception("execute() not implemented");
    }

    /**
     * Default implementation, throws Exception
     * @param string $sql
     * @param array $params
     * @param int $limitfrom
     * @param int $limitnum
     * @return bool true
     * @throws Exception
     */
    public function get_recordset_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {
        throw new Exception("get_recordset_sql() not implemented");
    }

    /**
     * Default implementation, throws Exception
     * @param string $sql
     * @param array $params
     * @param int $limitfrom
     * @param int $limitnum
     * @return bool true
     * @throws Exception
     */
    public function get_records_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {
        throw new Exception("get_records_sql() not implemented");
    }

    /**
     * Default implementation, throws Exception
     * @param string $sql
     * @param array $params
     * @return bool true
     * @throws Exception
     */
    public function get_fieldset_sql($sql, array $params=null) {
        throw new Exception("get_fieldset_sql() not implemented");
    }

    /**
     * Default implementation, throws Exception
     * @param string $table
     * @param array $params
     * @param bool $returnid
     * @param bool $bulk
     * @param bool $customsequence
     * @return bool|int true or new id
     * @throws Exception
     */
    public function insert_record_raw($table, $params, $returnid=true, $bulk=false, $customsequence=false) {
        throw new Exception("insert_record_raw() not implemented");
    }

    /**
     * Default implementation, throws Exception
     * @param string $table
     * @param StdObject $dataobject
     * @param bool $returnid
     * @param bool $bulk
     * @return bool|int true or new id
     * @throws Exception
     */
    public function insert_record($table, $dataobject, $returnid=true, $bulk=false) {
        throw new Exception("insert_record() not implemented");
    }

    /**
     * Default implementation, throws Exception
     * @param string $table
     * @param StdObject $dataobject
     * @return bool true
     * @throws Exception
     */
    public function import_record($table, $dataobject) {
        throw new Exception("import_record() not implemented");
    }

    /**
     * Default implementation, throws Exception
     * @param string $table
     * @param array $params
     * @param bool $bulk
     * @return bool true
     * @throws Exception
     */
    public function update_record_raw($table, $params, $bulk=false) {
        throw new Exception("update_record_raw() not implemented");
    }

    /**
     * Default implementation, throws Exception
     * @param string $table
     * @param StdObject $dataobject
     * @param bool $bulk
     * @return bool true
     * @throws Exception
     */
    public function update_record($table, $dataobject, $bulk=false) {
        throw new Exception("update_record() not implemented");
    }

    /**
     * Default implementation, throws Exception
     * @param string $table
     * @param string $newfield
     * @param string $newvalue
     * @param string $select
     * @param array $params
     * @return bool true
     * @throws Exception
     */
    public function set_field_select($table, $newfield, $newvalue, $select, array $params=null) {
        throw new Exception("set_field_select() not implemented");
    }

    /**
     * Default implementation, throws Exception
     * @param string $table
     * @param string $select
     * @param array $params
     * @return bool true
     * @throws Exception
     */
    public function delete_records_select($table, $select, array $params=null) {
        throw new Exception("delete_records_select() not implemented");
    }

    /**
     * Default implementation, throws Exception
     * @return string $sql
     * @throws Exception
     */
    public function sql_concat() {
        throw new Exception("sql_concat() not implemented");
    }

    /**
     * Default implementation, throws Exception
     * @param string $separator
     * @param array  $elements
     * @return string $sql
     * @throws Exception
     */
    public function sql_concat_join($separator="' '", $elements=[]) {
        throw new Exception("sql_concat_join() not implemented");
    }

    /**
     * Default implementation, throws Exception
     * @return void
     * @throws Exception
     */
    protected function begin_transaction() {
        throw new Exception("begin_transaction() not implemented");
    }

    /**
     * Default implementation, throws Exception
     * @return void
     * @throws Exception
     */
    protected function commit_transaction() {
        throw new Exception("commit_transaction() not implemented");
    }

    /**
     * Default implementation, throws Exception
     * @return void
     * @throws Exception
     */
    protected function rollback_transaction() {
        throw new Exception("rollback_transaction() not implemented");
    }

    /**
     * Returns the sql generator used for db manipulation.
     * Used mostly in upgrade.php scripts.
     * @return database_manager The instance used to perform ddl operations.
     * @see lib/ddl/database_manager.php
     */
    public function get_manager() {
        if (!$this->database_manager) {
            $generator = new test_sql_generator($this, $this->temptables);

            $this->database_manager = new database_manager($this, $generator);
        }
        return $this->database_manager;
    }
}
