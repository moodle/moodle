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
 * Native mssql class representing moodle database interface.
 *
 * @package    core_dml
 * @copyright  2009 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/moodle_database.php');
require_once(__DIR__.'/mssql_native_moodle_recordset.php');
require_once(__DIR__.'/mssql_native_moodle_temptables.php');

/**
 * Native mssql class representing moodle database interface.
 *
 * @package    core_dml
 * @copyright  2009 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mssql_native_moodle_database extends moodle_database {

    protected $mssql     = null;
    protected $last_error_reporting; // To handle mssql driver default verbosity
    protected $collation;  // current DB collation cache

    /**
     * Detects if all needed PHP stuff installed.
     * Note: can be used before connect()
     * @return mixed true if ok, string if something
     */
    public function driver_installed() {
        if (!function_exists('mssql_connect')) {
            return get_string('mssqlextensionisnotpresentinphp', 'install');
        }
        return true;
    }

    /**
     * Returns database family type - describes SQL dialect
     * Note: can be used before connect()
     * @return string db family name (mysql, postgres, mssql, oracle, etc.)
     */
    public function get_dbfamily() {
        return 'mssql';
    }

    /**
     * Returns more specific database driver type
     * Note: can be used before connect()
     * @return string db type mysqli, pgsql, oci, mssql, sqlsrv
     */
    protected function get_dbtype() {
        return 'mssql';
    }

    /**
     * Returns general database library name
     * Note: can be used before connect()
     * @return string db type pdo, native
     */
    protected function get_dblibrary() {
        return 'native';
    }

    /**
     * Returns localised database type name
     * Note: can be used before connect()
     * @return string
     */
    public function get_name() {
        return get_string('nativemssql', 'install');
    }

    /**
     * Returns localised database configuration help.
     * Note: can be used before connect()
     * @return string
     */
    public function get_configuration_help() {
        return get_string('nativemssqlhelp', 'install');
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
     * @return bool true
     * @throws dml_connection_exception if error
     */
    public function connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, array $dboptions=null) {
        if ($prefix == '' and !$this->external) {
            //Enforce prefixes for everybody but mysql
            throw new dml_exception('prefixcannotbeempty', $this->get_dbfamily());
        }

        $driverstatus = $this->driver_installed();

        if ($driverstatus !== true) {
            throw new dml_exception('dbdriverproblem', $driverstatus);
        }

        $this->store_settings($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions);

        $dbhost = $this->dbhost;
        // Zero shouldn't be used as a port number so doing a check with empty() should be fine.
        if (!empty($dboptions['dbport'])) {
            if (stristr(PHP_OS, 'win') && !stristr(PHP_OS, 'darwin')) {
                $dbhost .= ','.$dboptions['dbport'];
            } else {
                $dbhost .= ':'.$dboptions['dbport'];
            }
        }
        ob_start();
        if (!empty($this->dboptions['dbpersist'])) { // persistent connection
            $this->mssql = mssql_pconnect($dbhost, $this->dbuser, $this->dbpass, true);
        } else {
            $this->mssql = mssql_connect($dbhost, $this->dbuser, $this->dbpass, true);
        }
        $dberr = ob_get_contents();
        ob_end_clean();

        if ($this->mssql === false) {
            $this->mssql = null;
            throw new dml_connection_exception($dberr);
        }

        // already connected, select database and set some env. variables
        $this->query_start("--mssql_select_db", null, SQL_QUERY_AUX);
        $result = mssql_select_db($this->dbname, $this->mssql);
        $this->query_end($result);

        // No need to set charset. It's UTF8, with transparent conversions
        // back and forth performed both by FreeTDS or ODBTP

        // Allow quoted identifiers
        $sql = "SET QUOTED_IDENTIFIER ON";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = mssql_query($sql, $this->mssql);
        $this->query_end($result);

        $this->free_result($result);

        // Force ANSI nulls so the NULL check was done by IS NULL and NOT IS NULL
        // instead of equal(=) and distinct(<>) symbols
        $sql = "SET ANSI_NULLS ON";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = mssql_query($sql, $this->mssql);
        $this->query_end($result);

        $this->free_result($result);

        // Force ANSI warnings so arithmetic/string overflows will be
        // returning error instead of transparently truncating data
        $sql = "SET ANSI_WARNINGS ON";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = mssql_query($sql, $this->mssql);
        $this->query_end($result);

        // Concatenating null with anything MUST return NULL
        $sql = "SET CONCAT_NULL_YIELDS_NULL  ON";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = mssql_query($sql, $this->mssql);
        $this->query_end($result);

        $this->free_result($result);

        // Set transactions isolation level to READ_COMMITTED
        // prevents dirty reads when using transactions +
        // is the default isolation level of MSSQL
        // Requires database to run with READ_COMMITTED_SNAPSHOT ON
        $sql = "SET TRANSACTION ISOLATION LEVEL READ COMMITTED";
        $this->query_start($sql, NULL, SQL_QUERY_AUX);
        $result = mssql_query($sql, $this->mssql);
        $this->query_end($result);

        $this->free_result($result);

        // Connection stabilised and configured, going to instantiate the temptables controller
        $this->temptables = new mssql_native_moodle_temptables($this);

        return true;
    }

    /**
     * Close database connection and release all resources
     * and memory (especially circular memory references).
     * Do NOT use connect() again, create a new instance if needed.
     */
    public function dispose() {
        parent::dispose(); // Call parent dispose to write/close session and other common stuff before closing connection
        if ($this->mssql) {
            mssql_close($this->mssql);
            $this->mssql = null;
        }
    }

    /**
     * Called before each db query.
     * @param string $sql
     * @param array array of parameters
     * @param int $type type of query
     * @param mixed $extrainfo driver specific extra information
     * @return void
     */
    protected function query_start($sql, array $params=null, $type, $extrainfo=null) {
        parent::query_start($sql, $params, $type, $extrainfo);
        // mssql driver tends to send debug to output, we do not need that ;-)
        $this->last_error_reporting = error_reporting(0);
    }

    /**
     * Called immediately after each db query.
     * @param mixed db specific result
     * @return void
     */
    protected function query_end($result) {
        // reset original debug level
        error_reporting($this->last_error_reporting);
        parent::query_end($result);
    }

    /**
     * Returns database server info array
     * @return array Array containing 'description' and 'version' info
     */
    public function get_server_info() {
        static $info;
        if (!$info) {
            $info = array();
            $sql = 'sp_server_info 2';
            $this->query_start($sql, null, SQL_QUERY_AUX);
            $result = mssql_query($sql, $this->mssql);
            $this->query_end($result);
            $row = mssql_fetch_row($result);
            $info['description'] = $row[2];
            $this->free_result($result);

            $sql = 'sp_server_info 500';
            $this->query_start($sql, null, SQL_QUERY_AUX);
            $result = mssql_query($sql, $this->mssql);
            $this->query_end($result);
            $row = mssql_fetch_row($result);
            $info['version'] = $row[2];
            $this->free_result($result);
        }
        return $info;
    }

    /**
     * Converts short table name {tablename} to real table name
     * supporting temp tables (#) if detected
     *
     * @param string sql
     * @return string sql
     */
    protected function fix_table_names($sql) {
        if (preg_match_all('/\{([a-z][a-z0-9_]*)\}/', $sql, $matches)) {
            foreach($matches[0] as $key=>$match) {
                $name = $matches[1][$key];
                if ($this->temptables->is_temptable($name)) {
                    $sql = str_replace($match, $this->temptables->get_correct_name($name), $sql);
                } else {
                    $sql = str_replace($match, $this->prefix.$name, $sql);
                }
            }
        }
        return $sql;
    }

    /**
     * Returns supported query parameter types
     * @return int bitmask of accepted SQL_PARAMS_*
     */
    protected function allowed_param_types() {
        return SQL_PARAMS_QM; // Not really, but emulated, see emulate_bound_params()
    }

    /**
     * Returns last error reported by database engine.
     * @return string error message
     */
    public function get_last_error() {
        return mssql_get_last_message();
    }

    /**
     * Return tables in database WITHOUT current prefix
     * @param bool $usecache if true, returns list of cached tables.
     * @return array of table names in lowercase and without prefix
     */
    public function get_tables($usecache=true) {
        if ($usecache and $this->tables !== null) {
            return $this->tables;
        }
        $this->tables = array();
        $sql = "SELECT table_name
                  FROM INFORMATION_SCHEMA.TABLES
                 WHERE table_name LIKE '$this->prefix%'
                   AND table_type = 'BASE TABLE'";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = mssql_query($sql, $this->mssql);
        $this->query_end($result);

        if ($result) {
            while ($row = mssql_fetch_row($result)) {
                $tablename = reset($row);
                if ($this->prefix !== false && $this->prefix !== '') {
                    if (strpos($tablename, $this->prefix) !== 0) {
                        continue;
                    }
                    $tablename = substr($tablename, strlen($this->prefix));
                }
                $this->tables[$tablename] = $tablename;
            }
            $this->free_result($result);
        }

        // Add the currently available temptables
        $this->tables = array_merge($this->tables, $this->temptables->get_temptables());
        return $this->tables;
    }

    /**
     * Return table indexes - everything lowercased.
     * @param string $table The table we want to get indexes from.
     * @return array An associative array of indexes containing 'unique' flag and 'columns' being indexed
     */
    public function get_indexes($table) {
        $indexes = array();
        $tablename = $this->prefix.$table;

        // Indexes aren't covered by information_schema metatables, so we need to
        // go to sys ones. Skipping primary key indexes on purpose.
        $sql = "SELECT i.name AS index_name, i.is_unique, ic.index_column_id, c.name AS column_name
                  FROM sys.indexes i
                  JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
                  JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
                  JOIN sys.tables t ON i.object_id = t.object_id
                 WHERE t.name = '$tablename'
                   AND i.is_primary_key = 0
              ORDER BY i.name, i.index_id, ic.index_column_id";

        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = mssql_query($sql, $this->mssql);
        $this->query_end($result);

        if ($result) {
            $lastindex = '';
            $unique = false;
            $columns = array();
            while ($row = mssql_fetch_assoc($result)) {
                if ($lastindex and $lastindex != $row['index_name']) { // Save lastindex to $indexes and reset info
                    $indexes[$lastindex] = array('unique' => $unique, 'columns' => $columns);
                    $unique = false;
                    $columns = array();
                }
                $lastindex = $row['index_name'];
                $unique = empty($row['is_unique']) ? false : true;
                $columns[] = $row['column_name'];
            }
            if ($lastindex ) { // Add the last one if exists
                $indexes[$lastindex] = array('unique' => $unique, 'columns' => $columns);
            }
            $this->free_result($result);
        }
        return $indexes;
    }

    /**
     * Returns datailed information about columns in table. This information is cached internally.
     * @param string $table name
     * @param bool $usecache
     * @return array array of database_column_info objects indexed with column names
     */
    public function get_columns($table, $usecache=true) {

        if ($usecache) {
            $properties = array('dbfamily' => $this->get_dbfamily(), 'settings' => $this->get_settings_hash());
            $cache = cache::make('core', 'databasemeta', $properties);
            if ($data = $cache->get($table)) {
                return $data;
            }
        }

        $structure = array();

        if (!$this->temptables->is_temptable($table)) { // normal table, get metadata from own schema
            $sql = "SELECT column_name AS name,
                           data_type AS type,
                           numeric_precision AS max_length,
                           character_maximum_length AS char_max_length,
                           numeric_scale AS scale,
                           is_nullable AS is_nullable,
                           columnproperty(object_id(quotename(table_schema) + '.' +
                               quotename(table_name)), column_name, 'IsIdentity') AS auto_increment,
                           column_default AS default_value
                      FROM INFORMATION_SCHEMA.COLUMNS
                     WHERE table_name = '{" . $table . "}'
                  ORDER BY ordinal_position";
        } else { // temp table, get metadata from tempdb schema
            $sql = "SELECT column_name AS name,
                           data_type AS type,
                           numeric_precision AS max_length,
                           character_maximum_length AS char_max_length,
                           numeric_scale AS scale,
                           is_nullable AS is_nullable,
                           columnproperty(object_id(quotename(table_schema) + '.' +
                               quotename(table_name)), column_name, 'IsIdentity') AS auto_increment,
                           column_default AS default_value
                      FROM tempdb.INFORMATION_SCHEMA.COLUMNS
                      JOIN tempdb..sysobjects ON name = table_name
                     WHERE id = object_id('tempdb..{" . $table . "}')
                  ORDER BY ordinal_position";
        }

        list($sql, $params, $type) = $this->fix_sql_params($sql, null);

        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = mssql_query($sql, $this->mssql);
        $this->query_end($result);

        if (!$result) {
            return array();
        }

        while ($rawcolumn = mssql_fetch_assoc($result)) {

            $rawcolumn = (object)$rawcolumn;

            $info = new stdClass();
            $info->name = $rawcolumn->name;
            $info->type = $rawcolumn->type;
            $info->meta_type = $this->mssqltype2moodletype($info->type);

            // Prepare auto_increment info
            $info->auto_increment = $rawcolumn->auto_increment ? true : false;

            // Define type for auto_increment columns
            $info->meta_type = ($info->auto_increment && $info->meta_type == 'I') ? 'R' : $info->meta_type;

            // id columns being auto_incremnt are PK by definition
            $info->primary_key = ($info->name == 'id' && $info->meta_type == 'R' && $info->auto_increment);

            if ($info->meta_type === 'C' and $rawcolumn->char_max_length == -1) {
                // This is NVARCHAR(MAX), not a normal NVARCHAR.
                $info->max_length = -1;
                $info->meta_type = 'X';
            } else {
                // Put correct length for character and LOB types
                $info->max_length = $info->meta_type == 'C' ? $rawcolumn->char_max_length : $rawcolumn->max_length;
                $info->max_length = ($info->meta_type == 'X' || $info->meta_type == 'B') ? -1 : $info->max_length;
            }

            // Scale
            $info->scale = $rawcolumn->scale;

            // Prepare not_null info
            $info->not_null = $rawcolumn->is_nullable == 'NO'  ? true : false;

            // Process defaults
            $info->has_default = !empty($rawcolumn->default_value);
            if ($rawcolumn->default_value === NULL) {
                $info->default_value = NULL;
            } else {
                $info->default_value = preg_replace("/^[\(N]+[']?(.*?)[']?[\)]+$/", '\\1', $rawcolumn->default_value);
            }

            // Process binary
            $info->binary = $info->meta_type == 'B' ? true : false;

            $structure[$info->name] = new database_column_info($info);
        }
        $this->free_result($result);

        if ($usecache) {
            $cache->set($table, $structure);
        }

        return $structure;
    }

    /**
     * Normalise values based on varying RDBMS's dependencies (booleans, LOBs...)
     *
     * @param database_column_info $column column metadata corresponding with the value we are going to normalise
     * @param mixed $value value we are going to normalise
     * @return mixed the normalised value
     */
    protected function normalise_value($column, $value) {
        $this->detect_objects($value);

        if (is_bool($value)) { // Always, convert boolean to int
            $value = (int)$value;
        } // And continue processing because text columns with numeric info need special handling below

        if ($column->meta_type == 'B') {   // BLOBs need to be properly "packed", but can be inserted directly if so.
            if (!is_null($value)) {               // If value not null, unpack it to unquoted hexadecimal byte-string format
                $value = unpack('H*hex', $value); // we leave it as array, so emulate_bound_params() can detect it
            }                                     // easily and "bind" the param ok.

        } else if ($column->meta_type == 'X') {             // MSSQL doesn't cast from int to text, so if text column
            if (is_numeric($value)) {                       // and is numeric value then cast to string
                $value = array('numstr' => (string)$value); // and put into array, so emulate_bound_params() will know how
            }                                               // to "bind" the param ok, avoiding reverse conversion to number

        } else if ($value === '') {
            if ($column->meta_type == 'I' or $column->meta_type == 'F' or $column->meta_type == 'N') {
                $value = 0; // prevent '' problems in numeric fields
            }
        }
        return $value;
    }

    /**
     * Selectively call mssql_free_result(), avoiding some warnings without using the horrible @
     *
     * @param mssql_resource $resource resource to be freed if possible
     */
    private function free_result($resource) {
        if (!is_bool($resource)) { // true/false resources cannot be freed
            mssql_free_result($resource);
        }
    }

    /**
     * Provides mapping between mssql native data types and moodle_database - database_column_info - ones)
     *
     * @param string $mssql_type native mssql data type
     * @return string 1-char database_column_info data type
     */
    private function mssqltype2moodletype($mssql_type) {
        $type = null;
        switch (strtoupper($mssql_type)) {
            case 'BIT':
                $type = 'L';
                break;
            case 'INT':
            case 'SMALLINT':
            case 'INTEGER':
            case 'BIGINT':
                $type = 'I';
                break;
            case 'DECIMAL':
            case 'REAL':
            case 'FLOAT':
                $type = 'N';
                break;
            case 'VARCHAR':
            case 'NVARCHAR':
                $type = 'C';
                break;
            case 'TEXT':
            case 'NTEXT':
            case 'VARCHAR(MAX)':
            case 'NVARCHAR(MAX)':
                $type = 'X';
                break;
            case 'IMAGE':
            case 'VARBINARY':
            case 'VARBINARY(MAX)':
                $type = 'B';
                break;
            case 'DATETIME':
                $type = 'D';
                break;
        }
        if (!$type) {
            throw new dml_exception('invalidmssqlnativetype', $mssql_type);
        }
        return $type;
    }

    /**
     * Do NOT use in code, to be used by database_manager only!
     * @param string|array $sql query
     * @return bool true
     * @throws ddl_change_structure_exception A DDL specific exception is thrown for any errors.
     */
    public function change_database_structure($sql) {
        $this->get_manager(); // Includes DDL exceptions classes ;-)
        $sqls = (array)$sql;

        try {
            foreach ($sqls as $sql) {
                $this->query_start($sql, null, SQL_QUERY_STRUCTURE);
                $result = mssql_query($sql, $this->mssql);
                $this->query_end($result);
            }
        } catch (ddl_change_structure_exception $e) {
            $this->reset_caches();
            throw $e;
        }

        $this->reset_caches();
        return true;
    }

    /**
     * Very ugly hack which emulates bound parameters in queries
     * because the mssql driver doesn't support placeholders natively at all
     */
    protected function emulate_bound_params($sql, array $params=null) {
        if (empty($params)) {
            return $sql;
        }
        // ok, we have verified sql statement with ? and correct number of params
        $parts = array_reverse(explode('?', $sql));
        $return = array_pop($parts);
        foreach ($params as $param) {
            if (is_bool($param)) {
                $return .= (int)$param;

            } else if (is_array($param) && isset($param['hex'])) { // detect hex binary, bind it specially
                $return .= '0x' . $param['hex'];

            } else if (is_array($param) && isset($param['numstr'])) { // detect numerical strings that *must not*
                $return .= "N'{$param['numstr']}'";                   // be converted back to number params, but bound as strings

            } else if (is_null($param)) {
                $return .= 'NULL';

            } else if (is_number($param)) { // we can not use is_numeric() because it eats leading zeros from strings like 0045646
                $return .= "'".$param."'"; //fix for MDL-24863 to prevent auto-cast to int.

            } else if (is_float($param)) {
                $return .= $param;

            } else {
                $param = str_replace("'", "''", $param);
                $param = str_replace("\0", "", $param);
                $return .= "N'$param'";
            }

            $return .= array_pop($parts);
        }
        return $return;
    }

    /**
     * Execute general sql query. Should be used only when no other method suitable.
     * Do NOT use this to make changes in db structure, use database_manager methods instead!
     * @param string $sql query
     * @param array $params query parameters
     * @return bool true
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function execute($sql, array $params=null) {

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        $rawsql = $this->emulate_bound_params($sql, $params);

        if (strpos($sql, ';') !== false) {
            throw new coding_exception('moodle_database::execute() Multiple sql statements found or bound parameters not used properly in query!');
        }

        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $result = mssql_query($rawsql, $this->mssql);
        $this->query_end($result);
        $this->free_result($result);

        return true;
    }

    /**
     * Get a number of records as a moodle_recordset using a SQL statement.
     *
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
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_recordset_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {

        list($limitfrom, $limitnum) = $this->normalise_limit_from_num($limitfrom, $limitnum);

        if ($limitfrom or $limitnum) {
            if ($limitnum >= 1) { // Only apply TOP clause if we have any limitnum (limitfrom offset is handled later)
                $fetch = $limitfrom + $limitnum;
                if (PHP_INT_MAX - $limitnum < $limitfrom) { // Check PHP_INT_MAX overflow
                    $fetch = PHP_INT_MAX;
                }
                $sql = preg_replace('/^([\s(])*SELECT([\s]+(DISTINCT|ALL))?(?!\s*TOP\s*\()/i',
                                    "\\1SELECT\\2 TOP $fetch", $sql);
            }
        }

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        $rawsql = $this->emulate_bound_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_SELECT);
        $result = mssql_query($rawsql, $this->mssql);
        $this->query_end($result);

        if ($limitfrom) { // Skip $limitfrom records
            if (!@mssql_data_seek($result, $limitfrom)) {
                // Nothing, most probably seek past the end.
                mssql_free_result($result);
                $result = null;
            }
        }

        return $this->create_recordset($result);
    }

    protected function create_recordset($result) {
        return new mssql_native_moodle_recordset($result);
    }

    /**
     * Get a number of records as an array of objects using a SQL statement.
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
     * @return array of objects, or empty array if no records were found
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_records_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {

        $rs = $this->get_recordset_sql($sql, $params, $limitfrom, $limitnum);

        $results = array();

        foreach ($rs as $row) {
            $id = reset($row);
            if (isset($results[$id])) {
                $colname = key($row);
                debugging("Did you remember to make the first column something unique in your call to get_records? Duplicate value '$id' found in column '$colname'.", DEBUG_DEVELOPER);
            }
            $results[$id] = $row;
        }
        $rs->close();

        return $results;
    }

    /**
     * Selects records and return values (first field) as an array using a SQL statement.
     *
     * @param string $sql The SQL query
     * @param array $params array of sql parameters
     * @return array of values
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_fieldset_sql($sql, array $params=null) {

        $rs = $this->get_recordset_sql($sql, $params);

        $results = array();

        foreach ($rs as $row) {
            $results[] = reset($row);
        }
        $rs->close();

        return $results;
    }

    /**
     * Insert new record into database, as fast as possible, no safety checks, lobs not supported.
     * @param string $table name
     * @param mixed $params data record as object or array
     * @param bool $returnit return it of inserted record
     * @param bool $bulk true means repeated inserts expected
     * @param bool $customsequence true if 'id' included in $params, disables $returnid
     * @return bool|int true or new id
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function insert_record_raw($table, $params, $returnid=true, $bulk=false, $customsequence=false) {
        if (!is_array($params)) {
            $params = (array)$params;
        }

        $returning = "";
        $isidentity = false;

        if ($customsequence) {
            if (!isset($params['id'])) {
                throw new coding_exception('moodle_database::insert_record_raw() id field must be specified if custom sequences used.');
            }
            $returnid = false;

            $columns = $this->get_columns($table);
            if (isset($columns['id']) and $columns['id']->auto_increment) {
                $isidentity = true;
            }

            // Disable IDENTITY column before inserting record with id, only if the
            // column is identity, from meta information.
            if ($isidentity) {
                $sql = 'SET IDENTITY_INSERT {' . $table . '} ON'; // Yes, it' ON!!
                list($sql, $xparams, $xtype) = $this->fix_sql_params($sql, null);
                $this->query_start($sql, null, SQL_QUERY_AUX);
                $result = mssql_query($sql, $this->mssql);
                $this->query_end($result);
                $this->free_result($result);
            }

        } else {
            unset($params['id']);
            if ($returnid) {
                $returning = "OUTPUT inserted.id";
            }
        }

        if (empty($params)) {
            throw new coding_exception('moodle_database::insert_record_raw() no fields found.');
        }

        $fields = implode(',', array_keys($params));
        $qms    = array_fill(0, count($params), '?');
        $qms    = implode(',', $qms);

        $sql = "INSERT INTO {" . $table . "} ($fields) $returning VALUES ($qms)";

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        $rawsql = $this->emulate_bound_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_INSERT);
        $result = mssql_query($rawsql, $this->mssql);
        // Expected results are:
        //     - true: insert ok and there isn't returned information.
        //     - false: insert failed and there isn't returned information.
        //     - resource: insert executed, need to look for returned (output)
        //           values to know if the insert was ok or no. Posible values
        //           are false = failed, integer = insert ok, id returned.
        $end = false;
        if (is_bool($result)) {
            $end = $result;
        } else if (is_resource($result)) {
            $end = mssql_result($result, 0, 0); // Fetch 1st column from 1st row.
        }
        $this->query_end($end); // End the query with the calculated $end.

        if ($returning !== "") {
            $params['id'] = $end;
        }
        $this->free_result($result);

        if ($customsequence) {
            // Enable IDENTITY column after inserting record with id, only if the
            // column is identity, from meta information.
            if ($isidentity) {
                $sql = 'SET IDENTITY_INSERT {' . $table . '} OFF'; // Yes, it' OFF!!
                list($sql, $xparams, $xtype) = $this->fix_sql_params($sql, null);
                $this->query_start($sql, null, SQL_QUERY_AUX);
                $result = mssql_query($sql, $this->mssql);
                $this->query_end($result);
                $this->free_result($result);
            }
        }

        if (!$returnid) {
            return true;
        }

        return (int)$params['id'];
    }

    /**
     * Insert a record into a table and return the "id" field if required.
     *
     * Some conversions and safety checks are carried out. Lobs are supported.
     * If the return ID isn't required, then this just reports success as true/false.
     * $data is an object containing needed data
     * @param string $table The database table to be inserted into
     * @param object $data A data object with values for one or more fields in the record
     * @param bool $returnid Should the id of the newly created record entry be returned? If this option is not requested then true/false is returned.
     * @return bool|int true or new id
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function insert_record($table, $dataobject, $returnid=true, $bulk=false) {
        $dataobject = (array)$dataobject;

        $columns = $this->get_columns($table);
        if (empty($columns)) {
            throw new dml_exception('ddltablenotexist', $table);
        }
        $cleaned = array();

        foreach ($dataobject as $field => $value) {
            if ($field === 'id') {
                continue;
            }
            if (!isset($columns[$field])) {
                continue;
            }
            $column = $columns[$field];
            $cleaned[$field] = $this->normalise_value($column, $value);
        }

        return $this->insert_record_raw($table, $cleaned, $returnid, $bulk);
    }

    /**
     * Import a record into a table, id field is required.
     * Safety checks are NOT carried out. Lobs are supported.
     *
     * @param string $table name of database table to be inserted into
     * @param object $dataobject A data object with values for one or more fields in the record
     * @return bool true
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function import_record($table, $dataobject) {
        $dataobject = (array)$dataobject;

        $columns = $this->get_columns($table);
        $cleaned = array();

        foreach ($dataobject as $field => $value) {
            if (!isset($columns[$field])) {
                continue;
            }
            $column = $columns[$field];
            $cleaned[$field] = $this->normalise_value($column, $value);
        }

        $this->insert_record_raw($table, $cleaned, false, false, true);

        return true;
    }

    /**
     * Update record in database, as fast as possible, no safety checks, lobs not supported.
     * @param string $table name
     * @param mixed $params data record as object or array
     * @param bool true means repeated updates expected
     * @return bool true
     * @throws dml_exception A DML specific exception is thrown for any errors.
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
        $sql = "UPDATE {" . $table . "} SET $sets WHERE id = ?";

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        $rawsql = $this->emulate_bound_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $result = mssql_query($rawsql, $this->mssql);
        $this->query_end($result);

        $this->free_result($result);
        return true;
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
     * @return bool true
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function update_record($table, $dataobject, $bulk=false) {
        $dataobject = (array)$dataobject;

        $columns = $this->get_columns($table);
        $cleaned = array();

        foreach ($dataobject as $field => $value) {
            if (!isset($columns[$field])) {
                continue;
            }
            $column = $columns[$field];
            $cleaned[$field] = $this->normalise_value($column, $value);
        }

        return $this->update_record_raw($table, $cleaned, $bulk);
    }

    /**
     * Set a single field in every table record which match a particular WHERE clause.
     *
     * @param string $table The database table to be checked against.
     * @param string $newfield the field to set.
     * @param string $newvalue the value to set the field to.
     * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
     * @param array $params array of sql parameters
     * @return bool true
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function set_field_select($table, $newfield, $newvalue, $select, array $params=null) {

        if ($select) {
            $select = "WHERE $select";
        }
        if (is_null($params)) {
            $params = array();
        }

        // convert params to ? types
        list($select, $params, $type) = $this->fix_sql_params($select, $params);

        // Get column metadata
        $columns = $this->get_columns($table);
        $column = $columns[$newfield];

        $newvalue = $this->normalise_value($column, $newvalue);

        if (is_null($newvalue)) {
            $newfield = "$newfield = NULL";
        } else {
            $newfield = "$newfield = ?";
            array_unshift($params, $newvalue);
        }
        $sql = "UPDATE {" . $table . "} SET $newfield $select";

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        $rawsql = $this->emulate_bound_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $result = mssql_query($rawsql, $this->mssql);
        $this->query_end($result);

        $this->free_result($result);

        return true;
    }

    /**
     * Delete one or more records from a table which match a particular WHERE clause.
     *
     * @param string $table The database table to be checked against.
     * @param string $select A fragment of SQL to be used in a where clause in the SQL call (used to define the selection criteria).
     * @param array $params array of sql parameters
     * @return bool true
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function delete_records_select($table, $select, array $params=null) {

        if ($select) {
            $select = "WHERE $select";
        }

        $sql = "DELETE FROM {" . $table . "} $select";

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        $rawsql = $this->emulate_bound_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $result = mssql_query($rawsql, $this->mssql);
        $this->query_end($result);

        $this->free_result($result);

        return true;
    }

    public function sql_cast_char2int($fieldname, $text=false) {
        if (!$text) {
            return ' CAST(' . $fieldname . ' AS INT) ';
        } else {
            return ' CAST(' . $this->sql_compare_text($fieldname) . ' AS INT) ';
        }
    }

    public function sql_cast_char2real($fieldname, $text=false) {
        if (!$text) {
            return ' CAST(' . $fieldname . ' AS REAL) ';
        } else {
            return ' CAST(' . $this->sql_compare_text($fieldname) . ' AS REAL) ';
        }
    }

    public function sql_ceil($fieldname) {
        return ' CEILING(' . $fieldname . ')';
    }


    protected function get_collation() {
        if (isset($this->collation)) {
            return $this->collation;
        }
        if (!empty($this->dboptions['dbcollation'])) {
            // perf speedup
            $this->collation = $this->dboptions['dbcollation'];
            return $this->collation;
        }

        // make some default
        $this->collation = 'Latin1_General_CI_AI';

        $sql = "SELECT CAST(DATABASEPROPERTYEX('$this->dbname', 'Collation') AS varchar(255)) AS SQLCollation";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = mssql_query($sql, $this->mssql);
        $this->query_end($result);

        if ($result) {
            if ($rawcolumn = mssql_fetch_assoc($result)) {
                $this->collation = reset($rawcolumn);
            }
            $this->free_result($result);
        }

        return $this->collation;
    }

    /**
     * Returns 'LIKE' part of a query.
     *
     * @param string $fieldname usually name of the table column
     * @param string $param usually bound query parameter (?, :named)
     * @param bool $casesensitive use case sensitive search
     * @param bool $accensensitive use accent sensitive search (not all databases support accent insensitive)
     * @param bool $notlike true means "NOT LIKE"
     * @param string $escapechar escape char for '%' and '_'
     * @return string SQL code fragment
     */
    public function sql_like($fieldname, $param, $casesensitive = true, $accentsensitive = true, $notlike = false, $escapechar = '\\') {
        if (strpos($param, '%') !== false) {
            debugging('Potential SQL injection detected, sql_like() expects bound parameters (? or :named)');
        }

        $collation = $this->get_collation();

        if ($casesensitive) {
            $collation = str_replace('_CI', '_CS', $collation);
        } else {
            $collation = str_replace('_CS', '_CI', $collation);
        }
        if ($accentsensitive) {
            $collation = str_replace('_AI', '_AS', $collation);
        } else {
            $collation = str_replace('_AS', '_AI', $collation);
        }

        $LIKE = $notlike ? 'NOT LIKE' : 'LIKE';

        return "$fieldname COLLATE $collation $LIKE $param ESCAPE '$escapechar'";
    }

    public function sql_concat() {
        $arr = func_get_args();
        foreach ($arr as $key => $ele) {
            $arr[$key] = ' CAST(' . $ele . ' AS NVARCHAR(255)) ';
        }
        $s = implode(' + ', $arr);
        if ($s === '') {
            return " '' ";
        }
        return " $s ";
    }

    public function sql_concat_join($separator="' '", $elements=array()) {
        for ($n=count($elements)-1; $n > 0 ; $n--) {
            array_splice($elements, $n, 0, $separator);
        }
        $s = implode(' + ', $elements);
        if ($s === '') {
            return " '' ";
        }
        return " $s ";
    }

   public function sql_isempty($tablename, $fieldname, $nullablefield, $textfield) {
        if ($textfield) {
            return ' (' . $this->sql_compare_text($fieldname) . " = '') ";
        } else {
            return " ($fieldname = '') ";
        }
    }

   /**
     * Returns the SQL text to be used to calculate the length in characters of one expression.
     * @param string fieldname or expression to calculate its length in characters.
     * @return string the piece of SQL code to be used in the statement.
     */
    public function sql_length($fieldname) {
        return ' LEN(' . $fieldname . ')';
    }

    public function sql_order_by_text($fieldname, $numchars=32) {
        return " CONVERT(varchar({$numchars}), {$fieldname})";
    }

   /**
     * Returns the SQL for returning searching one string for the location of another.
     */
    public function sql_position($needle, $haystack) {
        return "CHARINDEX(($needle), ($haystack))";
    }

    /**
     * Returns the proper substr() SQL text used to extract substrings from DB
     * NOTE: this was originally returning only function name
     *
     * @param string $expr some string field, no aggregates
     * @param mixed $start integer or expression evaluating to int
     * @param mixed $length optional integer or expression evaluating to int
     * @return string sql fragment
     */
    public function sql_substr($expr, $start, $length=false) {
        if (count(func_get_args()) < 2) {
            throw new coding_exception('moodle_database::sql_substr() requires at least two parameters', 'Originaly this function wa
s only returning name of SQL substring function, it now requires all parameters.');
        }
        if ($length === false) {
            return "SUBSTRING($expr, $start, (LEN($expr) - $start + 1))";
        } else {
            return "SUBSTRING($expr, $start, $length)";
        }
    }

    /**
     * Does this driver support tool_replace?
     *
     * @since Moodle 2.6.1
     * @return bool
     */
    public function replace_all_text_supported() {
        return true;
    }

    public function session_lock_supported() {
        return true;
    }

    /**
     * Obtain session lock
     * @param int $rowid id of the row with session record
     * @param int $timeout max allowed time to wait for the lock in seconds
     * @return bool success
     */
    public function get_session_lock($rowid, $timeout) {
        if (!$this->session_lock_supported()) {
            return;
        }
        parent::get_session_lock($rowid, $timeout);

        $timeoutmilli = $timeout * 1000;

        $fullname = $this->dbname.'-'.$this->prefix.'-session-'.$rowid;
        // There is one bug in PHP/freetds (both reproducible with mssql_query()
        // and its mssql_init()/mssql_bind()/mssql_execute() alternative) for
        // stored procedures, causing scalar results of the execution
        // to be cast to boolean (true/fals). Here there is one
        // workaround that forces the return of one recordset resource.
        // $sql = "sp_getapplock '$fullname', 'Exclusive', 'Session',  $timeoutmilli";
        $sql = "BEGIN
                    DECLARE @result INT
                    EXECUTE @result = sp_getapplock @Resource='$fullname',
                                                    @LockMode='Exclusive',
                                                    @LockOwner='Session',
                                                    @LockTimeout='$timeoutmilli'
                    SELECT @result
                END";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = mssql_query($sql, $this->mssql);
        $this->query_end($result);

        if ($result) {
            $row = mssql_fetch_row($result);
            if ($row[0] < 0) {
                throw new dml_sessionwait_exception();
            }
        }

        $this->free_result($result);
    }

    public function release_session_lock($rowid) {
        if (!$this->session_lock_supported()) {
            return;
        }
        if (!$this->used_for_db_sessions) {
            return;
        }

        parent::release_session_lock($rowid);

        $fullname = $this->dbname.'-'.$this->prefix.'-session-'.$rowid;
        $sql = "sp_releaseapplock '$fullname', 'Session'";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = mssql_query($sql, $this->mssql);
        $this->query_end($result);

        $this->free_result($result);
    }

    /**
     * Driver specific start of real database transaction,
     * this can not be used directly in code.
     * @return void
     */
    protected function begin_transaction() {
        // requires database to run with READ_COMMITTED_SNAPSHOT ON
        $sql = "BEGIN TRANSACTION"; // Will be using READ COMMITTED isolation
        $this->query_start($sql, NULL, SQL_QUERY_AUX);
        $result = mssql_query($sql, $this->mssql);
        $this->query_end($result);

        $this->free_result($result);
    }

    /**
     * Driver specific commit of real database transaction,
     * this can not be used directly in code.
     * @return void
     */
    protected function commit_transaction() {
        $sql = "COMMIT TRANSACTION";
        $this->query_start($sql, NULL, SQL_QUERY_AUX);
        $result = mssql_query($sql, $this->mssql);
        $this->query_end($result);

        $this->free_result($result);
    }

    /**
     * Driver specific abort of real database transaction,
     * this can not be used directly in code.
     * @return void
     */
    protected function rollback_transaction() {
        $sql = "ROLLBACK TRANSACTION";
        $this->query_start($sql, NULL, SQL_QUERY_AUX);
        $result = mssql_query($sql, $this->mssql);
        $this->query_end($result);

        $this->free_result($result);
    }
}
