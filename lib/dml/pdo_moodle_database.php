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
 * Experimental pdo database class
 *
 * @package    core_dml
 * @copyright  2008 Andrei Bautu
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/moodle_database.php');
require_once(__DIR__.'/pdo_moodle_recordset.php');

/**
 * Experimental pdo database class
 *
 * @package    core_dml
 * @copyright  2008 Andrei Bautu
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class pdo_moodle_database extends moodle_database {

    protected $pdb;
    protected $lastError = null;

    /**
     * Constructor - instantiates the database, specifying if it's external (connect to other systems) or no (Moodle DB)
     *               note this has effect to decide if prefix checks must be performed or no
     * @param bool true means external database used
     */
    public function __construct($external=false) {
        parent::__construct($external);
    }

    /**
     * Connect to db
     * Must be called before other methods.
     * @param string $dbhost The database host.
     * @param string $dbuser The database username.
     * @param string $dbpass The database username's password.
     * @param string $dbname The name of the database being connected to.
     * @param mixed $prefix string means moodle db prefix, false used for external databases where prefix not used
     * @param array $dboptions driver specific options
     * @return bool success
     */
    public function connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, array $dboptions=null) {
        $driverstatus = $this->driver_installed();

        if ($driverstatus !== true) {
            throw new dml_exception('dbdriverproblem', $driverstatus);
        }

        $this->store_settings($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions);

        try{
            $this->pdb = new PDO($this->get_dsn(), $this->dbuser, $this->dbpass, $this->get_pdooptions());
            // generic PDO settings to match adodb's default; subclasses can change this in configure_dbconnection
            $this->pdb->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
            $this->pdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->configure_dbconnection();
            return true;
        } catch (PDOException $ex) {
            throw new dml_connection_exception($ex->getMessage());
            return false;
        }
    }

    /**
     * Returns the driver-dependent DSN for PDO based on members stored by connect.
     * Must be called after connect (or after $dbname, $dbhost, etc. members have been set).
     * @return string driver-dependent DSN
     */
    abstract protected function get_dsn();

    /**
     * Returns the driver-dependent connection attributes for PDO based on members stored by connect.
     * Must be called after $dbname, $dbhost, etc. members have been set.
     * @return array A key=>value array of PDO driver-specific connection options
     */
    protected function get_pdooptions() {
        return array(PDO::ATTR_PERSISTENT => !empty($this->dboptions['dbpersist']));
    }

    protected function configure_dbconnection() {
        //TODO: not needed preconfigure_dbconnection() stuff for PDO drivers?
    }

    /**
     * Returns general database library name
     * Note: can be used before connect()
     * @return string db type pdo, native
     */
    protected function get_dblibrary() {
        return 'pdo';
    }

    /**
     * Returns localised database type name
     * Note: can be used before connect()
     * @return string
     */
    public function get_name() {
        return get_string('pdo'.$this->get_dbtype(), 'install');
    }

    /**
     * Returns localised database configuration help.
     * Note: can be used before connect()
     * @return string
     */
    public function get_configuration_help() {
        return get_string('pdo'.$this->get_dbtype().'help', 'install');
    }

    /**
     * Returns database server info array
     * @return array Array containing 'description' and 'version' info
     */
    public function get_server_info() {
        $result = array();
        try {
            $result['description'] = $this->pdb->getAttribute(PDO::ATTR_SERVER_INFO);
        } catch(PDOException $ex) {}
        try {
            $result['version'] = $this->pdb->getAttribute(PDO::ATTR_SERVER_VERSION);
        } catch(PDOException $ex) {}
        return $result;
    }

    /**
     * Returns supported query parameter types
     * @return int bitmask of accepted SQL_PARAMS_*
     */
    protected function allowed_param_types() {
        return SQL_PARAMS_QM | SQL_PARAMS_NAMED;
    }

    /**
     * Returns last error reported by database engine.
     * @return string error message
     */
    public function get_last_error() {
        return $this->lastError;
    }

    /**
     * Function to print/save/ignore debugging messages related to SQL queries.
     */
    protected function debug_query($sql, $params = null) {
        echo '<hr /> (', $this->get_dbtype(), '): ',  htmlentities($sql, ENT_QUOTES, 'UTF-8');
        if($params) {
            echo ' (parameters ';
            print_r($params);
            echo ')';
        }
        echo '<hr />';
    }

    /**
     * Do NOT use in code, to be used by database_manager only!
     * @param string|array $sql query
     * @param array|null $tablenames an array of xmldb table names affected by this request.
     * @return bool true
     * @throws ddl_change_structure_exception A DDL specific exception is thrown for any errors.
     */
    public function change_database_structure($sql, $tablenames = null) {
        $this->get_manager(); // Includes DDL exceptions classes ;-)
        $sqls = (array)$sql;

        try {
            foreach ($sqls as $sql) {
                $result = true;
                $this->query_start($sql, null, SQL_QUERY_STRUCTURE);

                try {
                    $this->pdb->exec($sql);
                } catch (PDOException $ex) {
                    $this->lastError = $ex->getMessage();
                    $result = false;
                }
                $this->query_end($result);
            }
        } catch (ddl_change_structure_exception $e) {
            $this->reset_caches($tablenames);
            throw $e;
        }

        $this->reset_caches($tablenames);
        return true;
    }

    public function delete_records_select($table, $select, array $params=null) {
        $sql = "DELETE FROM {{$table}}";
        if ($select) {
            $sql .= " WHERE $select";
        }
        return $this->execute($sql, $params);
    }

    /**
     * Factory method that creates a recordset for return by a query. The generic pdo_moodle_recordset
     * class should fit most cases, but pdo_moodle_database subclasses can override this method to return
     * a subclass of pdo_moodle_recordset.
     * @param object $sth instance of PDOStatement
     * @return object instance of pdo_moodle_recordset
     */
    protected function create_recordset($sth) {
        return new pdo_moodle_recordset($sth);
    }

    /**
     * Execute general sql query. Should be used only when no other method suitable.
     * Do NOT use this to make changes in db structure, use database_manager methods instead!
     * @param string $sql query
     * @param array $params query parameters
     * @return bool success
     */
    public function execute($sql, array $params=null) {
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        $result = true;
        $this->query_start($sql, $params, SQL_QUERY_UPDATE);

        try {
            $sth = $this->pdb->prepare($sql);
            $sth->execute($params);
        } catch (PDOException $ex) {
            $this->lastError = $ex->getMessage();
            $result = false;
        }

        $this->query_end($result);
        return $result;
    }

    /**
     * Get a number of records as an moodle_recordset.  $sql must be a complete SQL query.
     * Since this method is a little less readable, use of it should be restricted to
     * code where it's possible there might be large datasets being returned.  For known
     * small datasets use get_records_sql - it leads to simpler code.
     *
     * The return type is like:
     * @see function get_recordset.
     *
     * @param string $sql the SQL select query to execute.
     * @param array $params array of sql parameters
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return moodle_recordset instance
     */
    public function get_recordset_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {

        $result = true;

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        $sql = $this->get_limit_clauses($sql, $limitfrom, $limitnum);
        $this->query_start($sql, $params, SQL_QUERY_SELECT);

        try {
            $sth = $this->pdb->prepare($sql);
            $sth->execute($params);
            $result = $this->create_recordset($sth);
        } catch (PDOException $ex) {
            $this->lastError = $ex->getMessage();
            $result = false;
        }

        $this->query_end($result);
        return $result;
    }

    /**
     * Selects rows and return values of first column as array.
     *
     * @param string $sql The SQL query
     * @param array $params array of sql parameters
     * @return array of values
     */
    public function get_fieldset_sql($sql, array $params=null) {
        $rs = $this->get_recordset_sql($sql, $params);
        if (!$rs->valid()) {
            $rs->close(); // Not going to iterate (but exit), close rs
            return false;
        }
        $result = array();
        foreach($rs as $value) {
            $result[] = reset($value);
        }
        $rs->close();
        return $result;
    }

    /**
     * Get a number of records as an array of objects.
     *
     * Return value is like:
     * @see function get_records.
     *
     * @param string $sql the SQL select query to execute. The first column of this SELECT statement
     *   must be a unique value (usually the 'id' field), as it will be used as the key of the
     *   returned array.
     * @param array $params array of sql parameters
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return array of objects, or empty array if no records were found, or false if an error occurred.
     */
    public function get_records_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {
        global $CFG;

        $rs = $this->get_recordset_sql($sql, $params, $limitfrom, $limitnum);
        if (!$rs->valid()) {
            $rs->close(); // Not going to iterate (but exit), close rs
            return false;
        }
        $objects = array();
        foreach($rs as $value) {
            $key = reset($value);
            if ($CFG->debugdeveloper && array_key_exists($key, $objects)) {
                debugging("Did you remember to make the first column something unique in your call to get_records? Duplicate value '$key' found in column first column of '$sql'.", DEBUG_DEVELOPER);
            }
            $objects[$key] = (object)$value;
        }
        $rs->close();
        return $objects;
    }

    /**
     * Insert new record into database, as fast as possible, no safety checks, lobs not supported.
     * @param string $table name
     * @param mixed $params data record as object or array
     * @param bool $returnit return it of inserted record
     * @param bool $bulk true means repeated inserts expected
     * @param bool $customsequence true if 'id' included in $params, disables $returnid
     * @return bool|int true or new id
     */
    public function insert_record_raw($table, $params, $returnid=true, $bulk=false, $customsequence=false) {
        if (!is_array($params)) {
            $params = (array)$params;
        }

        if ($customsequence) {
            if (!isset($params['id'])) {
                throw new coding_exception('moodle_database::insert_record_raw() id field must be specified if custom sequences used.');
            }
            $returnid = false;
        } else {
            unset($params['id']);
        }

        if (empty($params)) {
            throw new coding_exception('moodle_database::insert_record_raw() no fields found.');
        }

        $fields = implode(',', array_keys($params));
        $qms    = array_fill(0, count($params), '?');
        $qms    = implode(',', $qms);

        $sql = "INSERT INTO {{$table}} ($fields) VALUES($qms)";
        if (!$this->execute($sql, $params)) {
            return false;
        }
        if (!$returnid) {
            return true;
        }
        if ($id = $this->pdb->lastInsertId()) {
            return (int)$id;
        }
        return false;
    }

    /**
     * Insert a record into a table and return the "id" field if required,
     * Some conversions and safety checks are carried out. Lobs are supported.
     * If the return ID isn't required, then this just reports success as true/false.
     * $data is an object containing needed data
     * @param string $table The database table to be inserted into
     * @param object|array $dataobject A data object with values for one or more fields in the record
     * @param bool $returnid Should the id of the newly created record entry be returned? If this option is not requested then true/false is returned.
     * @param bool $bulk true means repeated inserts expected
     * @return bool|int true or new id
     */
    public function insert_record($table, $dataobject, $returnid=true, $bulk=false) {
        $dataobject = (array)$dataobject;

        $columns = $this->get_columns($table);
        if (empty($columns)) {
            throw new dml_exception('ddltablenotexist', $table);
        }

        $cleaned = array();

        foreach ($dataobject as $field=>$value) {
            if ($field === 'id') {
                continue;
            }
            if (!isset($columns[$field])) {
                continue;
            }
            $column = $columns[$field];
            if (is_bool($value)) {
                $value = (int)$value; // prevent "false" problems
            }
            $cleaned[$field] = $value;
        }

        if (empty($cleaned)) {
            return false;
        }

        return $this->insert_record_raw($table, $cleaned, $returnid, $bulk);
    }

    /**
     * Update record in database, as fast as possible, no safety checks, lobs not supported.
     * @param string $table name
     * @param stdClass|array $params data record as object or array
     * @param bool true means repeated updates expected
     * @return bool success
     */
    public function update_record_raw($table, $params, $bulk=false) {
        $params = (array)$params;

        if (!isset($params['id'])) {
            throw new coding_exception('moodle_database::update_record_raw() id field must be specified.');
        }
        $id = $params['id'];
        unset($params['id']);

        if (empty($params)) {
            throw new coding_exception('moodle_database::update_record_raw() no fields found.');
        }

        $sets = array();
        foreach ($params as $field=>$value) {
            $sets[] = "$field = ?";
        }

        $params[] = $id; // last ? in WHERE condition

        $sets = implode(',', $sets);
        $sql = "UPDATE {{$table}} SET $sets WHERE id=?";
        return $this->execute($sql, $params);
    }

    /**
     * Update a record in a table
     *
     * $dataobject is an object containing needed data
     * Relies on $dataobject having a variable "id" to
     * specify the record to update
     *
     * @param string $table The database table to be checked against.
     * @param object $dataobject An object with contents equal to fieldname=>fieldvalue. Must have an entry for 'id' to map to the table specified.
     * @param bool true means repeated updates expected
     * @return bool success
     */
    public function update_record($table, $dataobject, $bulk=false) {
        $dataobject = (array)$dataobject;

        $columns = $this->get_columns($table);
        $cleaned = array();

        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) {
                continue;
            }
            if (is_bool($value)) {
                $value = (int)$value; // prevent "false" problems
            }
            $cleaned[$field] = $value;
        }

        return $this->update_record_raw($table, $cleaned, $bulk);
    }

    /**
     * Set a single field in every table row where the select statement evaluates to true.
     *
     * @param string $table The database table to be checked against.
     * @param string $newfield the field to set.
     * @param string $newvalue the value to set the field to.
     * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
     * @param array $params array of sql parameters
     * @return bool success
     */
    public function set_field_select($table, $newfield, $newvalue, $select, array $params=null) {
        if ($select) {
            $select = "WHERE $select";
        }
        if (is_null($params)) {
            $params = array();
        }
        list($select, $params, $type) = $this->fix_sql_params($select, $params);

        if (is_bool($newvalue)) {
            $newvalue = (int)$newvalue; // prevent "false" problems
        }
        if (is_null($newvalue)) {
            $newfield = "$newfield = NULL";
        } else {
            // make sure SET and WHERE clauses use the same type of parameters,
            // because we don't support different types in the same query
            switch($type) {
            case SQL_PARAMS_NAMED:
                $newfield = "$newfield = :newvalueforupdate";
                $params['newvalueforupdate'] = $newvalue;
                break;
            case SQL_PARAMS_QM:
                $newfield = "$newfield = ?";
                array_unshift($params, $newvalue);
                break;
            default:
                $this->lastError = __FILE__ . ' LINE: ' . __LINE__ . '.';
                throw new \moodle_exception(unknowparamtype, 'error', '', $this->lastError);
            }
        }
        $sql = "UPDATE {{$table}} SET $newfield $select";
        return $this->execute($sql, $params);
    }

    public function sql_concat(...$arr) {
        throw new \moodle_exception('TODO');
    }

    public function sql_concat_join($separator="' '", $elements=array()) {
        throw new \moodle_exception('TODO');
    }

    /**
     * Return SQL for performing group concatenation on given field/expression
     *
     * @param string $field
     * @param string $separator
     * @param string $sort
     * @return string
     */
    public function sql_group_concat(string $field, string $separator = ', ', string $sort = ''): string {
        return ''; // TODO.
    }

    protected function begin_transaction() {
        $this->query_start('', NULL, SQL_QUERY_AUX);
        try {
            $this->pdb->beginTransaction();
        } catch(PDOException $ex) {
            $this->lastError = $ex->getMessage();
        }
        $this->query_end($result);
    }

    protected function commit_transaction() {
        $this->query_start('', NULL, SQL_QUERY_AUX);

        try {
            $this->pdb->commit();
        } catch(PDOException $ex) {
            $this->lastError = $ex->getMessage();
        }
        $this->query_end($result);
    }

    protected function rollback_transaction() {
        $this->query_start('', NULL, SQL_QUERY_AUX);

        try {
            $this->pdb->rollBack();
        } catch(PDOException $ex) {
            $this->lastError = $ex->getMessage();
        }
        $this->query_end($result);
    }

    /**
     * Import a record into a table, id field is required.
     * Basic safety checks only. Lobs are supported.
     * @param string $table name of database table to be inserted into
     * @param mixed $dataobject object or array with fields in the record
     * @return bool success
     */
    public function import_record($table, $dataobject) {
        $dataobject = (object)$dataobject;

        $columns = $this->get_columns($table);
        $cleaned = array();
        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) {
                continue;
            }
            $cleaned[$field] = $value;
        }

        return $this->insert_record_raw($table, $cleaned, false, true, true);
    }

    /**
     * Called before each db query.
     *
     * Overridden to ensure $this->lastErorr is reset each query
     *
     * @param string $sql
     * @param array|null $params An array of parameters.
     * @param int $type type of query
     * @param mixed $extrainfo driver specific extra information
     * @return void
     */
    protected function query_start($sql, ?array $params, $type, $extrainfo=null) {
        $this->lastError = null;
        parent::query_start($sql, $params, $type, $extrainfo);
    }
}
