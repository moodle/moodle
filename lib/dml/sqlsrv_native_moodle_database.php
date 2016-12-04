<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 2 of the License, or
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
 * Native sqlsrv class representing moodle database interface.
 *
 * @package    core_dml
 * @copyright  2009 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/moodle_database.php');
require_once(__DIR__.'/sqlsrv_native_moodle_recordset.php');
require_once(__DIR__.'/sqlsrv_native_moodle_temptables.php');

/**
 * Native sqlsrv class representing moodle database interface.
 *
 * @package    core_dml
 * @copyright  2009 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
class sqlsrv_native_moodle_database extends moodle_database {

    protected $sqlsrv = null;
    protected $last_error_reporting; // To handle SQL*Server-Native driver default verbosity
    protected $temptables; // Control existing temptables (sqlsrv_moodle_temptables object)
    protected $collation;  // current DB collation cache
    /**
     * Does the used db version support ANSI way of limiting (2012 and higher)
     * @var bool
     */
    protected $supportsoffsetfetch;

    /** @var array list of open recordsets */
    protected $recordsets = array();

    /**
     * Constructor - instantiates the database, specifying if it's external (connect to other systems) or no (Moodle DB)
     *              note this has effect to decide if prefix checks must be performed or no
     * @param bool true means external database used
     */
    public function __construct($external=false) {
        parent::__construct($external);
    }

    /**
     * Detects if all needed PHP stuff installed.
     * Note: can be used before connect()
     * @return mixed true if ok, string if something
     */
    public function driver_installed() {
        // use 'function_exists()' rather than 'extension_loaded()' because
        // the name used by 'extension_loaded()' is case specific! The extension
        // therefore *could be* mixed case and hence not found.
        if (!function_exists('sqlsrv_num_rows')) {
            if (stripos(PHP_OS, 'win') === 0) {
                return get_string('nativesqlsrvnodriver', 'install');
            } else {
                return get_string('nativesqlsrvnonwindows', 'install');
            }
        }
        return true;
    }

    /**
     * Returns database family type - describes SQL dialect
     * Note: can be used before connect()
     * @return string db family name (mysql, postgres, mssql, sqlsrv, oracle, etc.)
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
        return 'sqlsrv';
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
        return get_string('nativesqlsrv', 'install');
    }

    /**
     * Returns localised database configuration help.
     * Note: can be used before connect()
     * @return string
     */
    public function get_configuration_help() {
        return get_string('nativesqlsrvhelp', 'install');
    }

    /**
     * Diagnose database and tables, this function is used
     * to verify database and driver settings, db engine types, etc.
     *
     * @return string null means everything ok, string means problem found.
     */
    public function diagnose() {
        // Verify the database is running with READ_COMMITTED_SNAPSHOT enabled.
        // (that's required to get snapshots/row versioning on READ_COMMITED mode).
        $correctrcsmode = false;
        $sql = "SELECT is_read_committed_snapshot_on
                  FROM sys.databases
                 WHERE name = '{$this->dbname}'";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = sqlsrv_query($this->sqlsrv, $sql);
        $this->query_end($result);
        if ($result) {
            if ($row = sqlsrv_fetch_array($result)) {
                $correctrcsmode = (bool)reset($row);
            }
        }
        $this->free_result($result);

        if (!$correctrcsmode) {
            return get_string('mssqlrcsmodemissing', 'error');
        }

        // Arrived here, all right.
        return null;
    }

    /**
     * Connect to db
     * Must be called before most other methods. (you can call methods that return connection configuration parameters)
     * @param string $dbhost The database host.
     * @param string $dbuser The database username.
     * @param string $dbpass The database username's password.
     * @param string $dbname The name of the database being connected to.
     * @param mixed $prefix string|bool The moodle db table name's prefix. false is used for external databases where prefix not used
     * @param array $dboptions driver specific options
     * @return bool true
     * @throws dml_connection_exception if error
     */
    public function connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, array $dboptions=null) {
        if ($prefix == '' and !$this->external) {
            // Enforce prefixes for everybody but mysql.
            throw new dml_exception('prefixcannotbeempty', $this->get_dbfamily());
        }

        $driverstatus = $this->driver_installed();

        if ($driverstatus !== true) {
            throw new dml_exception('dbdriverproblem', $driverstatus);
        }

        /*
         * Log all Errors.
         */
        sqlsrv_configure("WarningsReturnAsErrors", FALSE);
        sqlsrv_configure("LogSubsystems", SQLSRV_LOG_SYSTEM_OFF);
        sqlsrv_configure("LogSeverity", SQLSRV_LOG_SEVERITY_ERROR);

        $this->store_settings($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions);
        $this->sqlsrv = sqlsrv_connect($this->dbhost, array
         (
          'UID' => $this->dbuser,
          'PWD' => $this->dbpass,
          'Database' => $this->dbname,
          'CharacterSet' => 'UTF-8',
          'MultipleActiveResultSets' => true,
          'ConnectionPooling' => !empty($this->dboptions['dbpersist']),
          'ReturnDatesAsStrings' => true,
         ));

        if ($this->sqlsrv === false) {
            $this->sqlsrv = null;
            $dberr = $this->get_last_error();

            throw new dml_connection_exception($dberr);
        }

        // Allow quoted identifiers
        $sql = "SET QUOTED_IDENTIFIER ON";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = sqlsrv_query($this->sqlsrv, $sql);
        $this->query_end($result);

        $this->free_result($result);

        // Force ANSI nulls so the NULL check was done by IS NULL and NOT IS NULL
        // instead of equal(=) and distinct(<>) symbols
        $sql = "SET ANSI_NULLS ON";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = sqlsrv_query($this->sqlsrv, $sql);
        $this->query_end($result);

        $this->free_result($result);

        // Force ANSI warnings so arithmetic/string overflows will be
        // returning error instead of transparently truncating data
        $sql = "SET ANSI_WARNINGS ON";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = sqlsrv_query($this->sqlsrv, $sql);
        $this->query_end($result);

        // Concatenating null with anything MUST return NULL
        $sql = "SET CONCAT_NULL_YIELDS_NULL  ON";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = sqlsrv_query($this->sqlsrv, $sql);
        $this->query_end($result);

        $this->free_result($result);

        // Set transactions isolation level to READ_COMMITTED
        // prevents dirty reads when using transactions +
        // is the default isolation level of sqlsrv
        $sql = "SET TRANSACTION ISOLATION LEVEL READ COMMITTED";
        $this->query_start($sql, NULL, SQL_QUERY_AUX);
        $result = sqlsrv_query($this->sqlsrv, $sql);
        $this->query_end($result);

        $this->free_result($result);

        $serverinfo = $this->get_server_info();
        // Fetch/offset is supported staring from SQL Server 2012.
        $this->supportsoffsetfetch = $serverinfo['version'] > '11';

        // Connection established and configured, going to instantiate the temptables controller
        $this->temptables = new sqlsrv_native_moodle_temptables($this);

        return true;
    }

    /**
     * Close database connection and release all resources
     * and memory (especially circular memory references).
     * Do NOT use connect() again, create a new instance if needed.
     */
    public function dispose() {
        parent::dispose(); // Call parent dispose to write/close session and other common stuff before closing connection

        if ($this->sqlsrv) {
            sqlsrv_close($this->sqlsrv);
            $this->sqlsrv = null;
        }
    }

    /**
     * Called before each db query.
     * @param string $sql
     * @param array $params array of parameters
     * @param int $type type of query
     * @param mixed $extrainfo driver specific extra information
     * @return void
     */
    protected function query_start($sql, array $params = null, $type, $extrainfo = null) {
        parent::query_start($sql, $params, $type, $extrainfo);
    }

    /**
     * Called immediately after each db query.
     * @param mixed db specific result
     * @return void
     */
    protected function query_end($result) {
        parent::query_end($result);
    }

    /**
     * Returns database server info array
     * @return array Array containing 'description', 'version' and 'database' (current db) info
     */
    public function get_server_info() {
        static $info;

        if (!$info) {
            $server_info = sqlsrv_server_info($this->sqlsrv);

            if ($server_info) {
                $info['description'] = $server_info['SQLServerName'];
                $info['version'] = $server_info['SQLServerVersion'];
                $info['database'] = $server_info['CurrentDatabase'];
            }
        }
        return $info;
    }

    /**
     * Override: Converts short table name {tablename} to real table name
     * supporting temp tables (#) if detected
     *
     * @param string sql
     * @return string sql
     */
    protected function fix_table_names($sql) {
        if (preg_match_all('/\{([a-z][a-z0-9_]*)\}/i', $sql, $matches)) {
            foreach ($matches[0] as $key => $match) {
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
     * @return int bitmask
     */
    protected function allowed_param_types() {
        return SQL_PARAMS_QM;  // sqlsrv 1.1 can bind
    }

    /**
     * Returns last error reported by database engine.
     * @return string error message
     */
    public function get_last_error() {
        $retErrors = sqlsrv_errors(SQLSRV_ERR_ALL);
        $errorMessage = 'No errors found';

        if ($retErrors != null) {
            $errorMessage = '';

            foreach ($retErrors as $arrError) {
                $errorMessage .= "SQLState: ".$arrError['SQLSTATE']."<br>\n";
                $errorMessage .= "Error Code: ".$arrError['code']."<br>\n";
                $errorMessage .= "Message: ".$arrError['message']."<br>\n";
            }
        }

        return $errorMessage;
    }

    /**
     * Prepare the query binding and do the actual query.
     *
     * @param string $sql The sql statement
     * @param array $params array of params for binding. If NULL, they are ignored.
     * @param int $sql_query_type - Type of operation
     * @param bool $free_result - Default true, transaction query will be freed.
     * @param bool $scrollable - Default false, to use for quickly seeking to target records
     * @return resource|bool result
     */
    private function do_query($sql, $params, $sql_query_type, $free_result = true, $scrollable = false) {
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        /*
         * Bound variables *are* supported. Until I can get it to work, emulate the bindings
         * The challenge/problem/bug is that although they work, doing a SELECT SCOPE_IDENTITY()
         * doesn't return a value (no result set)
         *
         * -- somebody from MS
         */

        $sql = $this->emulate_bound_params($sql, $params);
        $this->query_start($sql, $params, $sql_query_type);
        if (!$scrollable) { // Only supporting next row
            $result = sqlsrv_query($this->sqlsrv, $sql);
        } else { // Supporting absolute/relative rows
            $result = sqlsrv_query($this->sqlsrv, $sql, array(), array('Scrollable' => SQLSRV_CURSOR_STATIC));
        }

        if ($result === false) {
            // TODO do something with error or just use if DEV or DEBUG?
            $dberr = $this->get_last_error();
        }

        $this->query_end($result);

        if ($free_result) {
            $this->free_result($result);
            return true;
        }
        return $result;
    }

    /**
     * Return tables in database WITHOUT current prefix.
     * @param bool $usecache if true, returns list of cached tables.
     * @return array of table names in lowercase and without prefix
     */
    public function get_tables($usecache = true) {
        if ($usecache and count($this->tables) > 0) {
            return $this->tables;
        }
        $this->tables = array ();
        $prefix = str_replace('_', '\\_', $this->prefix);
        $sql = "SELECT table_name
                  FROM INFORMATION_SCHEMA.TABLES
                 WHERE table_name LIKE '$prefix%' ESCAPE '\\' AND table_type = 'BASE TABLE'";

        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = sqlsrv_query($this->sqlsrv, $sql);
        $this->query_end($result);

        if ($result) {
            while ($row = sqlsrv_fetch_array($result)) {
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
     * @return array of arrays
     */
    public function get_indexes($table) {
        $indexes = array ();
        $tablename = $this->prefix.$table;

        // Indexes aren't covered by information_schema metatables, so we need to
        // go to sys ones. Skipping primary key indexes on purpose.
        $sql = "SELECT i.name AS index_name, i.is_unique, ic.index_column_id, c.name AS column_name
                  FROM sys.indexes i
                  JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
                  JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
                  JOIN sys.tables t ON i.object_id = t.object_id
                 WHERE t.name = '$tablename' AND i.is_primary_key = 0
              ORDER BY i.name, i.index_id, ic.index_column_id";

        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = sqlsrv_query($this->sqlsrv, $sql);
        $this->query_end($result);

        if ($result) {
            $lastindex = '';
            $unique = false;
            $columns = array ();

            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                if ($lastindex and $lastindex != $row['index_name'])
                    { // Save lastindex to $indexes and reset info
                    $indexes[$lastindex] = array
                     (
                      'unique' => $unique,
                      'columns' => $columns
                     );

                    $unique = false;
                    $columns = array ();
                }
                $lastindex = $row['index_name'];
                $unique = empty($row['is_unique']) ? false : true;
                $columns[] = $row['column_name'];
            }

            if ($lastindex) { // Add the last one if exists
                $indexes[$lastindex] = array
                 (
                  'unique' => $unique,
                  'columns' => $columns
                 );
            }

            $this->free_result($result);
        }
        return $indexes;
    }

    /**
     * Returns detailed information about columns in table. This information is cached internally.
     * @param string $table name
     * @param bool $usecache
     * @return array array of database_column_info objects indexed with column names
     */
    public function get_columns($table, $usecache = true) {
        if ($usecache) {
            if ($this->temptables->is_temptable($table)) {
                if ($data = $this->get_temp_tables_cache()->get($table)) {
                    return $data;
                }
            } else {
                if ($data = $this->get_metacache()->get($table)) {
                    return $data;
                }
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
                           columnproperty(object_id(quotename(table_schema) + '.' + quotename(table_name)), column_name, 'IsIdentity') AS auto_increment,
                           column_default AS default_value
                      FROM INFORMATION_SCHEMA.COLUMNS
                     WHERE table_name = '{".$table."}'
                  ORDER BY ordinal_position";
        } else { // temp table, get metadata from tempdb schema
            $sql = "SELECT column_name AS name,
                           data_type AS type,
                           numeric_precision AS max_length,
                           character_maximum_length AS char_max_length,
                           numeric_scale AS scale,
                           is_nullable AS is_nullable,
                           columnproperty(object_id(quotename(table_schema) + '.' + quotename(table_name)), column_name, 'IsIdentity') AS auto_increment,
                           column_default AS default_value
                      FROM tempdb.INFORMATION_SCHEMA.COLUMNS ".
            // check this statement
            // JOIN tempdb..sysobjects ON name = table_name
            // WHERE id = object_id('tempdb..{".$table."}')
                    "WHERE table_name LIKE '{".$table."}__________%'
                  ORDER BY ordinal_position";
        }

        list($sql, $params, $type) = $this->fix_sql_params($sql, null);

        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = sqlsrv_query($this->sqlsrv, $sql);
        $this->query_end($result);

        if (!$result) {
            return array ();
        }

        while ($rawcolumn = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {

            $rawcolumn = (object)$rawcolumn;

            $info = new stdClass();
            $info->name = $rawcolumn->name;
            $info->type = $rawcolumn->type;
            $info->meta_type = $this->sqlsrvtype2moodletype($info->type);

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
            $info->not_null = $rawcolumn->is_nullable == 'NO' ? true : false;

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
            if ($this->temptables->is_temptable($table)) {
                $this->get_temp_tables_cache()->set($table, $structure);
            } else {
                $this->get_metacache()->set($table, $structure);
            }
        }

        return $structure;
    }

    /**
     * Normalise values based in RDBMS dependencies (booleans, LOBs...)
     *
     * @param database_column_info $column column metadata corresponding with the value we are going to normalise
     * @param mixed $value value we are going to normalise
     * @return mixed the normalised value
     */
    protected function normalise_value($column, $value) {
        $this->detect_objects($value);

        if (is_bool($value)) {                               // Always, convert boolean to int
            $value = (int)$value;
        }                                                    // And continue processing because text columns with numeric info need special handling below

        if ($column->meta_type == 'B')
            { // BLOBs need to be properly "packed", but can be inserted directly if so.
            if (!is_null($value)) {               // If value not null, unpack it to unquoted hexadecimal byte-string format
                $value = unpack('H*hex', $value); // we leave it as array, so emulate_bound_params() can detect it
            }                                                // easily and "bind" the param ok.

        } else if ($column->meta_type == 'X') {              // sqlsrv doesn't cast from int to text, so if text column
            if (is_numeric($value)) { // and is numeric value then cast to string
                $value = array('numstr' => (string)$value);  // and put into array, so emulate_bound_params() will know how
            }                                                // to "bind" the param ok, avoiding reverse conversion to number
        } else if ($value === '') {

            if ($column->meta_type == 'I' or $column->meta_type == 'F' or $column->meta_type == 'N') {
                $value = 0; // prevent '' problems in numeric fields
            }
        }
        return $value;
    }

    /**
     * Selectively call sqlsrv_free_stmt(), avoiding some warnings without using the horrible @
     *
     * @param sqlsrv_resource $resource resource to be freed if possible
     * @return bool
     */
    private function free_result($resource) {
        if (!is_bool($resource)) { // true/false resources cannot be freed
            return sqlsrv_free_stmt($resource);
        }
    }

    /**
     * Provides mapping between sqlsrv native data types and moodle_database - database_column_info - ones)
     *
     * @param string $sqlsrv_type native sqlsrv data type
     * @return string 1-char database_column_info data type
     */
    private function sqlsrvtype2moodletype($sqlsrv_type) {
        $type = null;

        switch (strtoupper($sqlsrv_type)) {
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
            throw new dml_exception('invalidsqlsrvnativetype', $sqlsrv_type);
        }
        return $type;
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
                $this->query_start($sql, null, SQL_QUERY_STRUCTURE);
                $result = sqlsrv_query($this->sqlsrv, $sql);
                $this->query_end($result);
            }
        } catch (ddl_change_structure_exception $e) {
            $this->reset_caches($tablenames);
            throw $e;
        }

        $this->reset_caches($tablenames);
        return true;
    }

    /**
     * Prepare the array of params for native binding
     */
    protected function build_native_bound_params(array $params = null) {

        return null;
    }

    /**
     * Workaround for SQL*Server Native driver similar to MSSQL driver for
     * consistent behavior.
     */
    protected function emulate_bound_params($sql, array $params = null) {

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
                $return .= '0x'.$param['hex'];
            } else if (is_array($param) && isset($param['numstr'])) { // detect numerical strings that *must not*
                $return .= "N'{$param['numstr']}'";                   // be converted back to number params, but bound as strings
            } else if (is_null($param)) {
                $return .= 'NULL';

            } else if (is_number($param)) { // we can not use is_numeric() because it eats leading zeros from strings like 0045646
                $return .= "'$param'"; // this is a hack for MDL-23997, we intentionally use string because it is compatible with both nvarchar and int types
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
    public function execute($sql, array $params = null) {
        if (strpos($sql, ';') !== false) {
            throw new coding_exception('moodle_database::execute() Multiple sql statements found or bound parameters not used properly in query!');
        }
        $this->do_query($sql, $params, SQL_QUERY_UPDATE);
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
    public function get_recordset_sql($sql, array $params = null, $limitfrom = 0, $limitnum = 0) {

        list($limitfrom, $limitnum) = $this->normalise_limit_from_num($limitfrom, $limitnum);
        $needscrollable = (bool)$limitfrom; // To determine if we'll need to perform scroll to $limitfrom.

        if ($limitfrom or $limitnum) {
            if (!$this->supportsoffsetfetch) {
                if ($limitnum >= 1) { // Only apply TOP clause if we have any limitnum (limitfrom offset is handled later).
                    $fetch = $limitfrom + $limitnum;
                    if (PHP_INT_MAX - $limitnum < $limitfrom) { // Check PHP_INT_MAX overflow.
                        $fetch = PHP_INT_MAX;
                    }
                    $sql = preg_replace('/^([\s(])*SELECT([\s]+(DISTINCT|ALL))?(?!\s*TOP\s*\()/i',
                                        "\\1SELECT\\2 TOP $fetch", $sql);
                }
            } else {
                $needscrollable = false; // Using supported fetch/offset, no need to scroll anymore.
                $sql = (substr($sql, -1) === ';') ? substr($sql, 0, -1) : $sql;
                // We need order by to use FETCH/OFFSET.
                // Ordering by first column shouldn't break anything if there was no order in the first place.
                if (!strpos(strtoupper($sql), "ORDER BY")) {
                    $sql .= " ORDER BY 1";
                }

                $sql .= " OFFSET ".$limitfrom." ROWS ";

                if ($limitnum > 0) {
                    $sql .= " FETCH NEXT ".$limitnum." ROWS ONLY";
                }
            }
        }
        $result = $this->do_query($sql, $params, SQL_QUERY_SELECT, false, $needscrollable);

        if ($needscrollable) { // Skip $limitfrom records.
            sqlsrv_fetch($result, SQLSRV_SCROLL_ABSOLUTE, $limitfrom - 1);
        }
        return $this->create_recordset($result);
    }

    /**
     * Create a record set and initialize with first row
     *
     * @param mixed $result
     * @return sqlsrv_native_moodle_recordset
     */
    protected function create_recordset($result) {
        $rs = new sqlsrv_native_moodle_recordset($result, $this);
        $this->recordsets[] = $rs;
        return $rs;
    }

    /**
     * Do not use outside of recordset class.
     * @internal
     * @param sqlsrv_native_moodle_recordset $rs
     */
    public function recordset_closed(sqlsrv_native_moodle_recordset $rs) {
        if ($key = array_search($rs, $this->recordsets, true)) {
            unset($this->recordsets[$key]);
        }
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
    public function get_records_sql($sql, array $params = null, $limitfrom = 0, $limitnum = 0) {

        $rs = $this->get_recordset_sql($sql, $params, $limitfrom, $limitnum);

        $results = array();

        foreach ($rs as $row) {
            $id = reset($row);

            if (isset($results[$id])) {
                $colname = key($row);
                debugging("Did you remember to make the first column something unique in your call to get_records? Duplicate value '$id' found in column '$colname'.", DEBUG_DEVELOPER);
            }
            $results[$id] = (object)$row;
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
    public function get_fieldset_sql($sql, array $params = null) {

        $rs = $this->get_recordset_sql($sql, $params);

        $results = array ();

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
                $sql = 'SET IDENTITY_INSERT {'.$table.'} ON'; // Yes, it' ON!!
                $this->do_query($sql, null, SQL_QUERY_AUX);
            }

        } else {
            unset($params['id']);
        }

        if (empty($params)) {
            throw new coding_exception('moodle_database::insert_record_raw() no fields found.');
        }
        $fields = implode(',', array_keys($params));
        $qms = array_fill(0, count($params), '?');
        $qms = implode(',', $qms);
        $sql = "INSERT INTO {" . $table . "} ($fields) VALUES($qms)";
        $query_id = $this->do_query($sql, $params, SQL_QUERY_INSERT);

        if ($customsequence) {
            // Enable IDENTITY column after inserting record with id, only if the
            // column is identity, from meta information.
            if ($isidentity) {
                $sql = 'SET IDENTITY_INSERT {'.$table.'} OFF'; // Yes, it' OFF!!
                $this->do_query($sql, null, SQL_QUERY_AUX);
            }
        }

        if ($returnid) {
            $id = $this->sqlsrv_fetch_id();
            return $id;
        } else {
            return true;
        }
    }

    /**
     * Get the ID of the current action
     *
     * @return mixed ID
     */
    private function sqlsrv_fetch_id() {
        $query_id = sqlsrv_query($this->sqlsrv, 'SELECT SCOPE_IDENTITY()');
        if ($query_id === false) {
            $dberr = $this->get_last_error();
            return false;
        }
        $row = $this->sqlsrv_fetchrow($query_id);
        return (int)$row[0];
    }

    /**
     * Fetch a single row into an numbered array
     *
     * @param mixed $query_id
     */
    private function sqlsrv_fetchrow($query_id) {
        $row = sqlsrv_fetch_array($query_id, SQLSRV_FETCH_NUMERIC);
        if ($row === false) {
            $dberr = $this->get_last_error();
            return false;
        }

        foreach ($row as $key => $value) {
            $row[$key] = ($value === ' ' || $value === NULL) ? '' : $value;
        }
        return $row;
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
    public function insert_record($table, $dataobject, $returnid = true, $bulk = false) {
        $dataobject = (array)$dataobject;

        $columns = $this->get_columns($table);
        if (empty($columns)) {
            throw new dml_exception('ddltablenotexist', $table);
        }

        $cleaned = array ();

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
        if (!is_object($dataobject)) {
            $dataobject = (object)$dataobject;
        }

        $columns = $this->get_columns($table);
        $cleaned = array ();

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
    public function update_record_raw($table, $params, $bulk = false) {
        $params = (array)$params;

        if (!isset($params['id'])) {
            throw new coding_exception('moodle_database::update_record_raw() id field must be specified.');
        }
        $id = $params['id'];
        unset($params['id']);

        if (empty($params)) {
            throw new coding_exception('moodle_database::update_record_raw() no fields found.');
        }

        $sets = array ();

        foreach ($params as $field => $value) {
            $sets[] = "$field = ?";
        }

        $params[] = $id; // last ? in WHERE condition

        $sets = implode(',', $sets);
        $sql = "UPDATE {".$table."} SET $sets WHERE id = ?";

        $this->do_query($sql, $params, SQL_QUERY_UPDATE);

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
    public function update_record($table, $dataobject, $bulk = false) {
        $dataobject = (array)$dataobject;

        $columns = $this->get_columns($table);
        $cleaned = array ();

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
    public function set_field_select($table, $newfield, $newvalue, $select, array $params = null) {
        if ($select) {
            $select = "WHERE $select";
        }

        if (is_null($params)) {
            $params = array ();
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
        $sql = "UPDATE {".$table."} SET $newfield $select";

        $this->do_query($sql, $params, SQL_QUERY_UPDATE);

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
    public function delete_records_select($table, $select, array $params = null) {
        if ($select) {
            $select = "WHERE $select";
        }

        $sql = "DELETE FROM {".$table."} $select";

        // we use SQL_QUERY_UPDATE because we do not know what is in general SQL, delete constant would not be accurate
        $this->do_query($sql, $params, SQL_QUERY_UPDATE);

        return true;
    }


    public function sql_cast_char2int($fieldname, $text = false) {
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
        return ' CEILING('.$fieldname.')';
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
        $result = sqlsrv_query($this->sqlsrv, $sql);
        $this->query_end($result);

        if ($result) {
            if ($rawcolumn = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                $this->collation = reset($rawcolumn);
            }
            $this->free_result($result);
        }

        return $this->collation;
    }

    public function sql_equal($fieldname, $param, $casesensitive = true, $accentsensitive = true, $notequal = false) {
        $equalop = $notequal ? '<>' : '=';
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

        return "$fieldname COLLATE $collation $equalop $param";
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
        $LIKE = $notlike ? 'NOT LIKE' : 'LIKE';

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

        return "$fieldname COLLATE $collation $LIKE $param ESCAPE '$escapechar'";
    }

    public function sql_concat() {
        $arr = func_get_args();

        foreach ($arr as $key => $ele) {
            $arr[$key] = ' CAST('.$ele.' AS NVARCHAR(255)) ';
        }
        $s = implode(' + ', $arr);

        if ($s === '') {
            return " '' ";
        }
        return " $s ";
    }

    public function sql_concat_join($separator = "' '", $elements = array ()) {
        for ($n = count($elements) - 1; $n > 0; $n--) {
            array_splice($elements, $n, 0, $separator);
        }
        return call_user_func_array(array($this, 'sql_concat'), $elements);
    }

    public function sql_isempty($tablename, $fieldname, $nullablefield, $textfield) {
        if ($textfield) {
            return ' ('.$this->sql_compare_text($fieldname)." = '') ";
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
        return ' LEN('.$fieldname.')';
    }

    public function sql_order_by_text($fieldname, $numchars = 32) {
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
    public function sql_substr($expr, $start, $length = false) {
        if (count(func_get_args()) < 2) {
            throw new coding_exception('moodle_database::sql_substr() requires at least two parameters',
                'Originally this function was only returning name of SQL substring function, it now requires all parameters.');
        }

        if ($length === false) {
            return "SUBSTRING($expr, " . $this->sql_cast_char2int($start) . ", 2^31-1)";
        } else {
            return "SUBSTRING($expr, " . $this->sql_cast_char2int($start) . ", " . $this->sql_cast_char2int($length) . ")";
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
     * @return void
     */
    public function get_session_lock($rowid, $timeout) {
        if (!$this->session_lock_supported()) {
            return;
        }
        parent::get_session_lock($rowid, $timeout);

        $timeoutmilli = $timeout * 1000;

        $fullname = $this->dbname.'-'.$this->prefix.'-session-'.$rowid;
        // While this may work using proper {call sp_...} calls + binding +
        // executing + consuming recordsets, the solution used for the mssql
        // driver is working perfectly, so 100% mimic-ing that code.
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
        $result = sqlsrv_query($this->sqlsrv, $sql);
        $this->query_end($result);

        if ($result) {
            $row = sqlsrv_fetch_array($result);
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
        $result = sqlsrv_query($this->sqlsrv, $sql);
        $this->query_end($result);
        $this->free_result($result);
    }

    /**
     * Driver specific start of real database transaction,
     * this can not be used directly in code.
     * @return void
     */
    protected function begin_transaction() {
        // Recordsets do not work well with transactions in SQL Server,
        // let's prefetch the recordsets to memory to work around these problems.
        foreach ($this->recordsets as $rs) {
            $rs->transaction_starts();
        }

        $this->query_start('native sqlsrv_begin_transaction', NULL, SQL_QUERY_AUX);
        $result = sqlsrv_begin_transaction($this->sqlsrv);
        $this->query_end($result);
    }

    /**
     * Driver specific commit of real database transaction,
     * this can not be used directly in code.
     * @return void
     */
    protected function commit_transaction() {
        $this->query_start('native sqlsrv_commit', NULL, SQL_QUERY_AUX);
        $result = sqlsrv_commit($this->sqlsrv);
        $this->query_end($result);
    }

    /**
     * Driver specific abort of real database transaction,
     * this can not be used directly in code.
     * @return void
     */
    protected function rollback_transaction() {
        $this->query_start('native sqlsrv_rollback', NULL, SQL_QUERY_AUX);
        $result = sqlsrv_rollback($this->sqlsrv);
        $this->query_end($result);
    }
}
