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
 * Native oci class representing moodle database interface.
 *
 * @package    core_dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/moodle_database.php');
require_once(__DIR__.'/oci_native_moodle_recordset.php');
require_once(__DIR__.'/oci_native_moodle_temptables.php');

/**
 * Native oci class representing moodle database interface.
 *
 * One complete reference for PHP + OCI:
 * http://www.oracle.com/technology/tech/php/underground-php-oracle-manual.html
 *
 * @package    core_dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class oci_native_moodle_database extends moodle_database {

    protected $oci     = null;

    /** @var To store stmt errors and enable get_last_error() to detect them.*/
    private $last_stmt_error = null;
    /** @var Default value initialised in connect method, we need the driver to be present.*/
    private $commit_status = null;

    /** @var To handle oci driver default verbosity.*/
    private $last_error_reporting;
    /** @var To store unique_session_id. Needed for temp tables unique naming.*/
    private $unique_session_id;

    /**
     * Detects if all needed PHP stuff installed.
     * Note: can be used before connect()
     * @return mixed true if ok, string if something
     */
    public function driver_installed() {
        if (!extension_loaded('oci8')) {
            return get_string('ociextensionisnotpresentinphp', 'install');
        }
        return true;
    }

    /**
     * Returns database family type - describes SQL dialect
     * Note: can be used before connect()
     * @return string db family name (mysql, postgres, mssql, oracle, etc.)
     */
    public function get_dbfamily() {
        return 'oracle';
    }

    /**
     * Returns more specific database driver type
     * Note: can be used before connect()
     * @return string db type mysqli, pgsql, oci, mssql, sqlsrv
     */
    protected function get_dbtype() {
        return 'oci';
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
        return get_string('nativeoci', 'install');
    }

    /**
     * Returns localised database configuration help.
     * Note: can be used before connect()
     * @return string
     */
    public function get_configuration_help() {
        return get_string('nativeocihelp', 'install');
    }

    /**
     * Diagnose database and tables, this function is used
     * to verify database and driver settings, db engine types, etc.
     *
     * @return string null means everything ok, string means problem found.
     */
    public function diagnose() {
        return null;
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
        if (!$this->external and strlen($prefix) > 2) {
            //Max prefix length for Oracle is 2cc
            $a = (object)array('dbfamily'=>'oracle', 'maxlength'=>2);
            throw new dml_exception('prefixtoolong', $a);
        }

        $driverstatus = $this->driver_installed();

        if ($driverstatus !== true) {
            throw new dml_exception('dbdriverproblem', $driverstatus);
        }

        // Autocommit ON by default.
        // Switching to OFF (OCI_DEFAULT), when playing with transactions
        // please note this thing is not defined if oracle driver not present in PHP
        // which means it can not be used as default value of object property!
        $this->commit_status = OCI_COMMIT_ON_SUCCESS;

        $this->store_settings($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions);
        unset($this->dboptions['dbsocket']);

        // NOTE: use of ', ", / and \ is very problematic, even native oracle tools seem to have
        //       problems with these, so just forget them and do not report problems into tracker...

        if (empty($this->dbhost)) {
            // old style full address (TNS)
            $dbstring = $this->dbname;
        } else {
            if (empty($this->dboptions['dbport'])) {
                $this->dboptions['dbport'] = 1521;
            }
            $dbstring = '//'.$this->dbhost.':'.$this->dboptions['dbport'].'/'.$this->dbname;
        }

        ob_start();
        if (empty($this->dboptions['dbpersist'])) {
            $this->oci = oci_new_connect($this->dbuser, $this->dbpass, $dbstring, 'AL32UTF8');
        } else {
            $this->oci = oci_pconnect($this->dbuser, $this->dbpass, $dbstring, 'AL32UTF8');
        }
        $dberr = ob_get_contents();
        ob_end_clean();


        if ($this->oci === false) {
            $this->oci = null;
            $e = oci_error();
            if (isset($e['message'])) {
                $dberr = $e['message'];
            }
            throw new dml_connection_exception($dberr);
        }

        // Make sure moodle package is installed - now required.
        if (!$this->oci_package_installed()) {
            try {
                $this->attempt_oci_package_install();
            } catch (Exception $e) {
                // Ignore problems, only the result counts,
                // admins have to fix it manually if necessary.
            }
            if (!$this->oci_package_installed()) {
                throw new dml_exception('dbdriverproblem', 'Oracle PL/SQL Moodle support package MOODLELIB is not installed! Database administrator has to execute /lib/dml/oci_native_moodle_package.sql script.');
            }
        }

        // get unique session id, to be used later for temp tables stuff
        $sql = 'SELECT DBMS_SESSION.UNIQUE_SESSION_ID() FROM DUAL';
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $stmt = $this->parse_query($sql);
        $result = oci_execute($stmt, $this->commit_status);
        $this->query_end($result, $stmt);
        $records = null;
        oci_fetch_all($stmt, $records, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
        oci_free_statement($stmt);
        $this->unique_session_id = reset($records[0]);

        //note: do not send "ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.,'" !
        //      instead fix our PHP code to convert "," to "." properly!

        // Connection stabilised and configured, going to instantiate the temptables controller
        $this->temptables = new oci_native_moodle_temptables($this, $this->unique_session_id);

        return true;
    }

    /**
     * Close database connection and release all resources
     * and memory (especially circular memory references).
     * Do NOT use connect() again, create a new instance if needed.
     */
    public function dispose() {
        parent::dispose(); // Call parent dispose to write/close session and other common stuff before closing connection
        if ($this->oci) {
            oci_close($this->oci);
            $this->oci = null;
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
        // oci driver tents to send debug to output, we do not need that ;-)
        $this->last_error_reporting = error_reporting(0);
    }

    /**
     * Called immediately after each db query.
     * @param mixed db specific result
     * @return void
     */
    protected function query_end($result, $stmt=null) {
        // reset original debug level
        error_reporting($this->last_error_reporting);
        if ($stmt and $result === false) {
            // Look for stmt error and store it
            if (is_resource($stmt)) {
                $e = oci_error($stmt);
                if ($e !== false) {
                    $this->last_stmt_error = $e['message'];
                }
            }
            oci_free_statement($stmt);
        }
        parent::query_end($result);
    }

    /**
     * Returns database server info array
     * @return array Array containing 'description' and 'version' info
     */
    public function get_server_info() {
        static $info = null; // TODO: move to real object property

        if (is_null($info)) {
            $this->query_start("--oci_server_version()", null, SQL_QUERY_AUX);
            $description = oci_server_version($this->oci);
            $this->query_end(true);
            preg_match('/(\d+\.)+\d+/', $description, $matches);
            $info = array('description'=>$description, 'version'=>$matches[0]);
        }

        return $info;
    }

    /**
     * Converts short table name {tablename} to real table name
     * supporting temp tables ($this->unique_session_id based) if detected
     *
     * @param string sql
     * @return string sql
     */
    protected function fix_table_names($sql) {
        if (preg_match_all('/\{([a-z][a-z0-9_]*)\}/', $sql, $matches)) {
            foreach($matches[0] as $key=>$match) {
                $name = $matches[1][$key];
                if ($this->temptables && $this->temptables->is_temptable($name)) {
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
        return SQL_PARAMS_NAMED;
    }

    /**
     * Returns last error reported by database engine.
     * @return string error message
     */
    public function get_last_error() {
        $error = false;
        // First look for any previously saved stmt error
        if (!empty($this->last_stmt_error)) {
            $error = $this->last_stmt_error;
            $this->last_stmt_error = null;
        } else { // Now try connection error
            $e = oci_error($this->oci);
            if ($e !== false) {
                $error = $e['message'];
            }
        }
        return $error;
    }

    /**
     * Prepare the statement for execution
     * @throws dml_connection_exception
     * @param string $sql
     * @return resource
     */
    protected function parse_query($sql) {
        $stmt = oci_parse($this->oci, $sql);
        if ($stmt == false) {
            throw new dml_connection_exception('Can not parse sql query'); //TODO: maybe add better info
        }
        return $stmt;
    }

    /**
     * Make sure there are no reserved words in param names...
     * @param string $sql
     * @param array $params
     * @return array ($sql, $params) updated query and parameters
     */
    protected function tweak_param_names($sql, array $params) {
        if (empty($params)) {
            return array($sql, $params);
        }

        $newparams = array();
        $searcharr = array(); // search => replace pairs
        foreach ($params as $name => $value) {
            // Keep the name within the 30 chars limit always (prefixing/replacing)
            if (strlen($name) <= 28) {
                $newname = 'o_' . $name;
            } else {
                $newname = 'o_' . substr($name, 2);
            }
            $newparams[$newname] = $value;
            $searcharr[':' . $name] = ':' . $newname;
        }
        // sort by length desc to avoid potential str_replace() overlap
        uksort($searcharr, array('oci_native_moodle_database', 'compare_by_length_desc'));

        $sql = str_replace(array_keys($searcharr), $searcharr, $sql);
        return array($sql, $newparams);
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
        $prefix = str_replace('_', "\\_", strtoupper($this->prefix));
        $sql = "SELECT TABLE_NAME
                  FROM CAT
                 WHERE TABLE_TYPE='TABLE'
                       AND TABLE_NAME NOT LIKE 'BIN\$%'
                       AND TABLE_NAME LIKE '$prefix%' ESCAPE '\\'";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $stmt = $this->parse_query($sql);
        $result = oci_execute($stmt, $this->commit_status);
        $this->query_end($result, $stmt);
        $records = null;
        oci_fetch_all($stmt, $records, 0, -1, OCI_ASSOC);
        oci_free_statement($stmt);
        $records = array_map('strtolower', $records['TABLE_NAME']);
        foreach ($records as $tablename) {
            if ($this->prefix !== false && $this->prefix !== '') {
                if (strpos($tablename, $this->prefix) !== 0) {
                    continue;
                }
                $tablename = substr($tablename, strlen($this->prefix));
            }
            $this->tables[$tablename] = $tablename;
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
        $tablename = strtoupper($this->prefix.$table);

        $sql = "SELECT i.INDEX_NAME, i.UNIQUENESS, c.COLUMN_POSITION, c.COLUMN_NAME, ac.CONSTRAINT_TYPE
                  FROM ALL_INDEXES i
                  JOIN ALL_IND_COLUMNS c ON c.INDEX_NAME=i.INDEX_NAME
             LEFT JOIN ALL_CONSTRAINTS ac ON (ac.TABLE_NAME=i.TABLE_NAME AND ac.CONSTRAINT_NAME=i.INDEX_NAME AND ac.CONSTRAINT_TYPE='P')
                 WHERE i.TABLE_NAME = '$tablename'
              ORDER BY i.INDEX_NAME, c.COLUMN_POSITION";

        $stmt = $this->parse_query($sql);
        $result = oci_execute($stmt, $this->commit_status);
        $this->query_end($result, $stmt);
        $records = null;
        oci_fetch_all($stmt, $records, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
        oci_free_statement($stmt);

        foreach ($records as $record) {
            if ($record['CONSTRAINT_TYPE'] === 'P') {
                //ignore for now;
                continue;
            }
            $indexname = strtolower($record['INDEX_NAME']);
            if (!isset($indexes[$indexname])) {
                $indexes[$indexname] = array('primary' => ($record['CONSTRAINT_TYPE'] === 'P'),
                                             'unique'  => ($record['UNIQUENESS'] === 'UNIQUE'),
                                             'columns' => array());
            }
            $indexes[$indexname]['columns'][] = strtolower($record['COLUMN_NAME']);
        }

        return $indexes;
    }

    /**
     * Returns detailed information about columns in table. This information is cached internally.
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

        if (!$table) { // table not specified, return empty array directly
            return array();
        }

        $structure = array();

        // We give precedence to CHAR_LENGTH for VARCHAR2 columns over WIDTH because the former is always
        // BYTE based and, for cross-db operations, we want CHAR based results. See MDL-29415
        // Instead of guessing sequence based exclusively on name, check tables against user_triggers to
        // ensure the table has a 'before each row' trigger to assume 'id' is auto_increment. MDL-32365
        $sql = "SELECT CNAME, COLTYPE, nvl(CHAR_LENGTH, WIDTH) AS WIDTH, SCALE, PRECISION, NULLS, DEFAULTVAL,
                  DECODE(NVL(TRIGGER_NAME, '0'), '0', '0', '1') HASTRIGGER
                  FROM COL c
             LEFT JOIN USER_TAB_COLUMNS u ON (u.TABLE_NAME = c.TNAME AND u.COLUMN_NAME = c.CNAME AND u.DATA_TYPE = 'VARCHAR2')
             LEFT JOIN USER_TRIGGERS t ON (t.TABLE_NAME = c.TNAME AND TRIGGER_TYPE = 'BEFORE EACH ROW' AND c.CNAME = 'ID')
                 WHERE TNAME = UPPER('{" . $table . "}')
              ORDER BY COLNO";

        list($sql, $params, $type) = $this->fix_sql_params($sql, null);

        $this->query_start($sql, null, SQL_QUERY_AUX);
        $stmt = $this->parse_query($sql);
        $result = oci_execute($stmt, $this->commit_status);
        $this->query_end($result, $stmt);
        $records = null;
        oci_fetch_all($stmt, $records, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
        oci_free_statement($stmt);

        if (!$records) {
            return array();
        }
        foreach ($records as $rawcolumn) {
            $rawcolumn = (object)$rawcolumn;

            $info = new stdClass();
            $info->name = strtolower($rawcolumn->CNAME);
            $info->auto_increment = ((int)$rawcolumn->HASTRIGGER) ? true : false;
            $matches = null;

            if ($rawcolumn->COLTYPE === 'VARCHAR2'
             or $rawcolumn->COLTYPE === 'VARCHAR'
             or $rawcolumn->COLTYPE === 'NVARCHAR2'
             or $rawcolumn->COLTYPE === 'NVARCHAR'
             or $rawcolumn->COLTYPE === 'CHAR'
             or $rawcolumn->COLTYPE === 'NCHAR') {
                $info->type          = $rawcolumn->COLTYPE;
                $info->meta_type     = 'C';
                $info->max_length    = $rawcolumn->WIDTH;
                $info->scale         = null;
                $info->not_null      = ($rawcolumn->NULLS === 'NOT NULL');
                $info->has_default   = !is_null($rawcolumn->DEFAULTVAL);
                if ($info->has_default) {

                    // this is hacky :-(
                    if ($rawcolumn->DEFAULTVAL === 'NULL') {
                        $info->default_value = null;
                    } else if ($rawcolumn->DEFAULTVAL === "' ' ") { // Sometimes it's stored with trailing space
                        $info->default_value = "";
                    } else if ($rawcolumn->DEFAULTVAL === "' '") { // Sometimes it's stored without trailing space
                        $info->default_value = "";
                    } else {
                        $info->default_value = trim($rawcolumn->DEFAULTVAL); // remove trailing space
                        $info->default_value = substr($info->default_value, 1, strlen($info->default_value)-2); //trim ''
                    }
                } else {
                    $info->default_value = null;
                }
                $info->primary_key   = false;
                $info->binary        = false;
                $info->unsigned      = null;
                $info->unique        = null;

            } else if ($rawcolumn->COLTYPE === 'NUMBER') {
                $info->type       = $rawcolumn->COLTYPE;
                $info->max_length = $rawcolumn->PRECISION;
                $info->binary     = false;
                if (!is_null($rawcolumn->SCALE) && $rawcolumn->SCALE == 0) { // null in oracle scale allows decimals => not integer
                    // integer
                    if ($info->name === 'id') {
                        $info->primary_key   = true;
                        $info->meta_type     = 'R';
                        $info->unique        = true;
                        $info->has_default   = false;
                    } else {
                        $info->primary_key   = false;
                        $info->meta_type     = 'I';
                        $info->unique        = null;
                    }
                    $info->scale = 0;

                } else {
                    //float
                    $info->meta_type     = 'N';
                    $info->primary_key   = false;
                    $info->unsigned      = null;
                    $info->unique        = null;
                    $info->scale         = $rawcolumn->SCALE;
                }
                $info->not_null      = ($rawcolumn->NULLS === 'NOT NULL');
                $info->has_default   = !is_null($rawcolumn->DEFAULTVAL);
                if ($info->has_default) {
                    $info->default_value = trim($rawcolumn->DEFAULTVAL); // remove trailing space
                } else {
                    $info->default_value = null;
                }

            } else if ($rawcolumn->COLTYPE === 'FLOAT') {
                $info->type       = $rawcolumn->COLTYPE;
                $info->max_length = (int)($rawcolumn->PRECISION * 3.32193);
                $info->primary_key   = false;
                $info->meta_type     = 'N';
                $info->unique        = null;
                $info->not_null      = ($rawcolumn->NULLS === 'NOT NULL');
                $info->has_default   = !is_null($rawcolumn->DEFAULTVAL);
                if ($info->has_default) {
                    $info->default_value = trim($rawcolumn->DEFAULTVAL); // remove trailing space
                } else {
                    $info->default_value = null;
                }

            } else if ($rawcolumn->COLTYPE === 'CLOB'
                    or $rawcolumn->COLTYPE === 'NCLOB') {
                $info->type          = $rawcolumn->COLTYPE;
                $info->meta_type     = 'X';
                $info->max_length    = -1;
                $info->scale         = null;
                $info->scale         = null;
                $info->not_null      = ($rawcolumn->NULLS === 'NOT NULL');
                $info->has_default   = !is_null($rawcolumn->DEFAULTVAL);
                if ($info->has_default) {
                    // this is hacky :-(
                    if ($rawcolumn->DEFAULTVAL === 'NULL') {
                        $info->default_value = null;
                    } else if ($rawcolumn->DEFAULTVAL === "' ' ") { // Sometimes it's stored with trailing space
                        $info->default_value = "";
                    } else if ($rawcolumn->DEFAULTVAL === "' '") { // Other times it's stored without trailing space
                        $info->default_value = "";
                    } else {
                        $info->default_value = trim($rawcolumn->DEFAULTVAL); // remove trailing space
                        $info->default_value = substr($info->default_value, 1, strlen($info->default_value)-2); //trim ''
                    }
                } else {
                    $info->default_value = null;
                }
                $info->primary_key   = false;
                $info->binary        = false;
                $info->unsigned      = null;
                $info->unique        = null;

            } else if ($rawcolumn->COLTYPE === 'BLOB') {
                $info->type          = $rawcolumn->COLTYPE;
                $info->meta_type     = 'B';
                $info->max_length    = -1;
                $info->scale         = null;
                $info->scale         = null;
                $info->not_null      = ($rawcolumn->NULLS === 'NOT NULL');
                $info->has_default   = !is_null($rawcolumn->DEFAULTVAL);
                if ($info->has_default) {
                    // this is hacky :-(
                    if ($rawcolumn->DEFAULTVAL === 'NULL') {
                        $info->default_value = null;
                    } else if ($rawcolumn->DEFAULTVAL === "' ' ") { // Sometimes it's stored with trailing space
                        $info->default_value = "";
                    } else if ($rawcolumn->DEFAULTVAL === "' '") { // Sometimes it's stored without trailing space
                        $info->default_value = "";
                    } else {
                        $info->default_value = trim($rawcolumn->DEFAULTVAL); // remove trailing space
                        $info->default_value = substr($info->default_value, 1, strlen($info->default_value)-2); //trim ''
                    }
                } else {
                    $info->default_value = null;
                }
                $info->primary_key   = false;
                $info->binary        = true;
                $info->unsigned      = null;
                $info->unique        = null;

            } else {
                // unknown type - sorry
                $info->type          = $rawcolumn->COLTYPE;
                $info->meta_type     = '?';
            }

            $structure[$info->name] = new database_column_info($info);
        }

        if ($usecache) {
            $cache->set($table, $structure);
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

        if (is_bool($value)) { // Always, convert boolean to int
            $value = (int)$value;

        } else if ($column->meta_type == 'B') { // CLOB detected, we return 'blob' array instead of raw value to allow
            if (!is_null($value)) {             // binding/executing code later to know about its nature
                $value = array('blob' => $value);
            }

        } else if ($column->meta_type == 'X' && strlen($value) > 4000) { // CLOB detected (>4000 optimisation), we return 'clob'
            if (!is_null($value)) {                                      // array instead of raw value to allow binding/
                $value = array('clob' => (string)$value);                // executing code later to know about its nature
            }

        } else if ($value === '') {
            if ($column->meta_type == 'I' or $column->meta_type == 'F' or $column->meta_type == 'N') {
                $value = 0; // prevent '' problems in numeric fields
            }
        }
        return $value;
    }

    /**
     * Transforms the sql and params in order to emulate the LIMIT clause available in other DBs
     *
     * @param string $sql the SQL select query to execute.
     * @param array $params array of sql parameters
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return array with the transformed sql and params updated
     */
    private function get_limit_sql($sql, array $params = null, $limitfrom=0, $limitnum=0) {

        list($limitfrom, $limitnum) = $this->normalise_limit_from_num($limitfrom, $limitnum);
        // TODO: Add the /*+ FIRST_ROWS */ hint if there isn't another hint

        if ($limitfrom and $limitnum) {
            $sql = "SELECT oracle_o.*
                      FROM (SELECT oracle_i.*, rownum AS oracle_rownum
                              FROM ($sql) oracle_i
                             WHERE rownum <= :oracle_num_rows
                            ) oracle_o
                     WHERE oracle_rownum > :oracle_skip_rows";
            $params['oracle_num_rows'] = $limitfrom + $limitnum;
            $params['oracle_skip_rows'] = $limitfrom;

        } else if ($limitfrom and !$limitnum) {
            $sql = "SELECT oracle_o.*
                      FROM (SELECT oracle_i.*, rownum AS oracle_rownum
                              FROM ($sql) oracle_i
                            ) oracle_o
                     WHERE oracle_rownum > :oracle_skip_rows";
            $params['oracle_skip_rows'] = $limitfrom;

        } else if (!$limitfrom and $limitnum) {
            $sql = "SELECT *
                      FROM ($sql)
                     WHERE rownum <= :oracle_num_rows";
            $params['oracle_num_rows'] = $limitnum;
        }

        return array($sql, $params);
    }

    /**
     * This function will handle all the column values before being inserted/updated to DB for Oracle
     * installations. This is because the "special feature" of Oracle where the empty string is
     * equal to NULL and this presents a problem with all our currently NOT NULL default '' fields.
     * (and with empties handling in general)
     *
     * Note that this function is 100% private and should be used, exclusively by DML functions
     * in this file. Also, this is considered a DIRTY HACK to be removed when possible.
     *
     * This function is private and must not be used outside this driver at all
     *
     * @param $table string the table where the record is going to be inserted/updated (without prefix)
     * @param $field string the field where the record is going to be inserted/updated
     * @param $value mixed the value to be inserted/updated
     */
    private function oracle_dirty_hack ($table, $field, $value) {

        // General bound parameter, just hack the spaces and pray it will work.
        if (!$table) {
            if ($value === '') {
                return ' ';
            } else if (is_bool($value)) {
                return (int)$value;
            } else {
                return $value;
            }
        }

        // Get metadata
        $columns = $this->get_columns($table);
        if (!isset($columns[$field])) {
            if ($value === '') {
                return ' ';
            } else if (is_bool($value)) {
                return (int)$value;
            } else {
                return $value;
            }
        }
        $column = $columns[$field];

        // !! This paragraph explains behaviour before Moodle 2.0:
        //
        // For Oracle DB, empty strings are converted to NULLs in DB
        // and this breaks a lot of NOT NULL columns currently Moodle. In the future it's
        // planned to move some of them to NULL, if they must accept empty values and this
        // piece of code will become less and less used. But, for now, we need it.
        // What we are going to do is to examine all the data being inserted and if it's
        // an empty string (NULL for Oracle) and the field is defined as NOT NULL, we'll modify
        // such data in the best form possible ("0" for booleans and numbers and " " for the
        // rest of strings. It isn't optimal, but the only way to do so.
        // In the opposite, when retrieving records from Oracle, we'll decode " " back to
        // empty strings to allow everything to work properly. DIRTY HACK.

        // !! These paragraphs explain the rationale about the change for Moodle 2.5:
        //
        // Before Moodle 2.0, we only used to apply this DIRTY HACK to NOT NULL columns, as
        // stated above, but it causes one problem in NULL columns where both empty strings
        // and real NULLs are stored as NULLs, being impossible to differentiate them when
        // being retrieved from DB.
        //
        // So, starting with Moodle 2.0, we are going to apply the DIRTY HACK to all the
        // CHAR/CLOB columns no matter of their nullability. That way, when retrieving
        // NULLABLE fields we'll get proper empties and NULLs differentiated, so we'll be able
        // to rely in NULL/empty/content contents without problems, until now that wasn't
        // possible at all.
        //
        // One space DIRTY HACK is now applied automatically for all query parameters
        // and results. The only problem is string concatenation where the glue must
        // be specified as "' '" sql fragment.
        //
        // !! Conclusions:
        //
        // From Moodle 2.5 onwards, ALL empty strings in Oracle DBs will be stored as
        // 1-whitespace char, ALL NULLs as NULLs and, obviously, content as content. And
        // those 1-whitespace chars will be converted back to empty strings by all the
        // get_field/record/set() functions transparently and any SQL needing direct handling
        // of empties will have to use placeholders or sql_isempty() helper function.

        // If the field isn't VARCHAR or CLOB, skip
        if ($column->meta_type != 'C' and $column->meta_type != 'X') {
            return $value;
        }

        // If the value isn't empty, skip
        if (!empty($value)) {
            return $value;
        }

        // Now, we have one empty value, going to be inserted to one VARCHAR2 or CLOB field
        // Try to get the best value to be inserted

        // The '0' string doesn't need any transformation, skip
        if ($value === '0') {
            return $value;
        }

        // Transformations start
        if (gettype($value) == 'boolean') {
            return '0'; // Transform false to '0' that evaluates the same for PHP

        } else if (gettype($value) == 'integer') {
            return '0'; // Transform 0 to '0' that evaluates the same for PHP

        } else if ($value === '') {
            return ' '; // Transform '' to ' ' that DON'T EVALUATE THE SAME
                        // (we'll transform back again on get_records_XXX functions and others)!!
        }

        // Fail safe to original value
        return $value;
    }

    /**
     * Helper function to order by string length desc
     *
     * @param $a string first element to compare
     * @param $b string second element to compare
     * @return int < 0 $a goes first (is less), 0 $b goes first, 0 doesn't matter
     */
    private function compare_by_length_desc($a, $b) {
        return strlen($b) - strlen($a);
    }

    /**
     * Is db in unicode mode?
     * @return bool
     */
    public function setup_is_unicodedb() {
        $sql = "SELECT VALUE
                  FROM NLS_DATABASE_PARAMETERS
                 WHERE PARAMETER = 'NLS_CHARACTERSET'";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $stmt = $this->parse_query($sql);
        $result = oci_execute($stmt, $this->commit_status);
        $this->query_end($result, $stmt);
        $records = null;
        oci_fetch_all($stmt, $records, 0, -1, OCI_FETCHSTATEMENT_BY_COLUMN);
        oci_free_statement($stmt);

        return (isset($records['VALUE'][0]) and $records['VALUE'][0] === 'AL32UTF8');
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
                $stmt = $this->parse_query($sql);
                $result = oci_execute($stmt, $this->commit_status);
                $this->query_end($result, $stmt);
                oci_free_statement($stmt);
            }
        } catch (ddl_change_structure_exception $e) {
            $this->reset_caches();
            throw $e;
        }

        $this->reset_caches();
        return true;
    }

    protected function bind_params($stmt, array $params=null, $tablename=null) {
        $descriptors = array();
        if ($params) {
            $columns = array();
            if ($tablename) {
                $columns = $this->get_columns($tablename);
            }
            foreach($params as $key => $value) {
                // Decouple column name and param name as far as sometimes they aren't the same
                if ($key == 'o_newfieldtoset') { // found case where column and key diverge, handle that
                    $columnname   = key($value);    // columnname is the key of the array
                    $params[$key] = $value[$columnname]; // set the proper value in the $params array and
                    $value        = $value[$columnname]; // set the proper value in the $value variable
                } else {
                    $columnname = preg_replace('/^o_/', '', $key); // Default columnname (for DB introspecting is key), but...
                }
                // Continue processing
                // Now, handle already detected LOBs
                if (is_array($value)) { // Let's go to bind special cases (lob descriptors)
                    if (isset($value['clob'])) {
                        $lob = oci_new_descriptor($this->oci, OCI_DTYPE_LOB);
                        oci_bind_by_name($stmt, $key, $lob, -1, SQLT_CLOB);
                        $lob->writeTemporary($this->oracle_dirty_hack($tablename, $columnname, $params[$key]['clob']), OCI_TEMP_CLOB);
                        $descriptors[] = $lob;
                        continue; // Column binding finished, go to next one
                    } else if (isset($value['blob'])) {
                        $lob = oci_new_descriptor($this->oci, OCI_DTYPE_LOB);
                        oci_bind_by_name($stmt, $key, $lob, -1, SQLT_BLOB);
                        $lob->writeTemporary($params[$key]['blob'], OCI_TEMP_BLOB);
                        $descriptors[] = $lob;
                        continue; // Column binding finished, go to next one
                    }
                }
                // TODO: Put proper types and length is possible (enormous speedup)
                // Arrived here, continue with standard processing, using metadata if possible
                if (isset($columns[$columnname])) {
                    $type = $columns[$columnname]->meta_type;
                    $maxlength = $columns[$columnname]->max_length;
                } else {
                    $type = '?';
                    $maxlength = -1;
                }
                switch ($type) {
                    case 'I':
                    case 'R':
                        // TODO: Optimise
                        oci_bind_by_name($stmt, $key, $params[$key]);
                        break;

                    case 'N':
                    case 'F':
                        // TODO: Optimise
                        oci_bind_by_name($stmt, $key, $params[$key]);
                        break;

                    case 'B':
                        // TODO: Only arrive here if BLOB is null: Bind if so, else exception!
                        // don't break here

                    case 'X':
                        // TODO: Only arrive here if CLOB is null or <= 4000 cc, else exception
                        // don't break here

                    default: // Bind as CHAR (applying dirty hack)
                        // TODO: Optimise
                        $params[$key] = $this->oracle_dirty_hack($tablename, $columnname, $params[$key]);
                        oci_bind_by_name($stmt, $key, $params[$key]);
                }
            }
        }
        return $descriptors;
    }

    protected function free_descriptors($descriptors) {
        foreach ($descriptors as $descriptor) {
            oci_free_descriptor($descriptor);
        }
    }

    /**
     * This function is used to convert all the Oracle 1-space defaults to the empty string
     * like a really DIRTY HACK to allow it to work better until all those NOT NULL DEFAULT ''
     * fields will be out from Moodle.
     * @param string the string to be converted to '' (empty string) if it's ' ' (one space)
     * @param mixed the key of the array in case we are using this function from array_walk,
     *              defaults to null for other (direct) uses
     * @return boolean always true (the converted variable is returned by reference)
     */
    public static function onespace2empty(&$item, $key=null) {
        $item = ($item === ' ') ? '' : $item;
        return true;
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

        if (strpos($sql, ';') !== false) {
            throw new coding_exception('moodle_database::execute() Multiple sql statements found or bound parameters not used properly in query!');
        }

        list($sql, $params) = $this->tweak_param_names($sql, $params);
        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $stmt = $this->parse_query($sql);
        $this->bind_params($stmt, $params);
        $result = oci_execute($stmt, $this->commit_status);
        $this->query_end($result, $stmt);
        oci_free_statement($stmt);

        return true;
    }

    /**
     * Get a single database record as an object using a SQL statement.
     *
     * The SQL statement should normally only return one record.
     * It is recommended to use get_records_sql() if more matches possible!
     *
     * @param string $sql The SQL string you wish to be executed, should normally only return one record.
     * @param array $params array of sql parameters
     * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
     *                        IGNORE_MULTIPLE means return first, ignore multiple records found(not recommended);
     *                        MUST_EXIST means throw exception if no record or multiple records found
     * @return mixed a fieldset object containing the first matching record, false or exception if error not found depending on mode
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_record_sql($sql, array $params=null, $strictness=IGNORE_MISSING) {
        $strictness = (int)$strictness;
        if ($strictness == IGNORE_MULTIPLE) {
            // do not limit here - ORA does not like that
            $rs = $this->get_recordset_sql($sql, $params);
            $result = false;
            foreach ($rs as $rec) {
                $result = $rec;
                break;
            }
            $rs->close();
            return $result;
        }
        return parent::get_record_sql($sql, $params, $strictness);
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

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        list($rawsql, $params) = $this->get_limit_sql($sql, $params, $limitfrom, $limitnum);

        list($rawsql, $params) = $this->tweak_param_names($rawsql, $params);
        $this->query_start($rawsql, $params, SQL_QUERY_SELECT);
        $stmt = $this->parse_query($rawsql);
        $this->bind_params($stmt, $params);
        $result = oci_execute($stmt, $this->commit_status);
        $this->query_end($result, $stmt);

        return $this->create_recordset($stmt);
    }

    protected function create_recordset($stmt) {
        return new oci_native_moodle_recordset($stmt);
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

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        list($rawsql, $params) = $this->get_limit_sql($sql, $params, $limitfrom, $limitnum);

        list($rawsql, $params) = $this->tweak_param_names($rawsql, $params);
        $this->query_start($rawsql, $params, SQL_QUERY_SELECT);
        $stmt = $this->parse_query($rawsql);
        $this->bind_params($stmt, $params);
        $result = oci_execute($stmt, $this->commit_status);
        $this->query_end($result, $stmt);

        $records = null;
        oci_fetch_all($stmt, $records, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
        oci_free_statement($stmt);

        $return = array();

        foreach ($records as $row) {
            $row = array_change_key_case($row, CASE_LOWER);
            unset($row['oracle_rownum']);
            array_walk($row, array('oci_native_moodle_database', 'onespace2empty'));
            $id = reset($row);
            if (isset($return[$id])) {
                $colname = key($row);
                debugging("Did you remember to make the first column something unique in your call to get_records? Duplicate value '$id' found in column '$colname'.", DEBUG_DEVELOPER);
            }
            $return[$id] = (object)$row;
        }

        return $return;
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
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        list($sql, $params) = $this->tweak_param_names($sql, $params);
        $this->query_start($sql, $params, SQL_QUERY_SELECT);
        $stmt = $this->parse_query($sql);
        $this->bind_params($stmt, $params);
        $result = oci_execute($stmt, $this->commit_status);
        $this->query_end($result, $stmt);

        $records = null;
        oci_fetch_all($stmt, $records, 0, -1, OCI_FETCHSTATEMENT_BY_COLUMN);
        oci_free_statement($stmt);

        $return = reset($records);
        array_walk($return, array('oci_native_moodle_database', 'onespace2empty'));

        return $return;
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

        if ($customsequence) {
            if (!isset($params['id'])) {
                throw new coding_exception('moodle_database::insert_record_raw() id field must be specified if custom sequences used.');
            }
            $returnid = false;
        } else {
            unset($params['id']);
            if ($returnid) {
                $returning = " RETURNING id INTO :oracle_id"; // crazy name nobody is ever going to use or parameter ;-)
            }
        }

        if (empty($params)) {
            throw new coding_exception('moodle_database::insert_record_raw() no fields found.');
        }

        $fields = implode(',', array_keys($params));
        $values = array();
        foreach ($params as $pname => $value) {
            $values[] = ":$pname";
        }
        $values = implode(',', $values);

        $sql = "INSERT INTO {" . $table . "} ($fields) VALUES ($values)";
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        $sql .= $returning;

        $id = null;

        // note we don't need tweak_param_names() here. Placeholders are safe column names. MDL-28080
        // list($sql, $params) = $this->tweak_param_names($sql, $params);
        $this->query_start($sql, $params, SQL_QUERY_INSERT);
        $stmt = $this->parse_query($sql);
        $descriptors = $this->bind_params($stmt, $params, $table);
        if ($returning) {
            oci_bind_by_name($stmt, ":oracle_id", $id, 10, SQLT_INT);
        }
        $result = oci_execute($stmt, $this->commit_status);
        $this->free_descriptors($descriptors);
        $this->query_end($result, $stmt);
        oci_free_statement($stmt);

        if (!$returnid) {
            return true;
        }

        if (!$returning) {
            die('TODO - implement oracle 9.2 insert support'); //TODO
        }

        return (int)$id;
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

        foreach ($dataobject as $field=>$value) {
            if ($field === 'id') {
                continue;
            }
            if (!isset($columns[$field])) { // Non-existing table field, skip it
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

        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) {
                continue;
            }
            $column = $columns[$field];
            $cleaned[$field] = $this->normalise_value($column, $value);
        }

        return $this->insert_record_raw($table, $cleaned, false, true, true);
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

        if (empty($params)) {
            throw new coding_exception('moodle_database::update_record_raw() no fields found.');
        }

        $sets = array();
        foreach ($params as $field=>$value) {
            if ($field == 'id') {
                continue;
            }
            $sets[] = "$field = :$field";
        }

        $sets = implode(',', $sets);
        $sql = "UPDATE {" . $table . "} SET $sets WHERE id=:id";
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        // note we don't need tweak_param_names() here. Placeholders are safe column names. MDL-28080
        // list($sql, $params) = $this->tweak_param_names($sql, $params);
        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $stmt = $this->parse_query($sql);
        $descriptors = $this->bind_params($stmt, $params, $table);
        $result = oci_execute($stmt, $this->commit_status);
        $this->free_descriptors($descriptors);
        $this->query_end($result, $stmt);
        oci_free_statement($stmt);

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

        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) {
                continue;
            }
            $column = $columns[$field];
            $cleaned[$field] = $this->normalise_value($column, $value);
        }

        $this->update_record_raw($table, $cleaned, $bulk);

        return true;
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

        // Get column metadata
        $columns = $this->get_columns($table);
        $column = $columns[$newfield];

        $newvalue = $this->normalise_value($column, $newvalue);

        list($select, $params, $type) = $this->fix_sql_params($select, $params);

        if (is_bool($newvalue)) {
            $newvalue = (int)$newvalue; // prevent "false" problems
        }
        if (is_null($newvalue)) {
            $newsql = "$newfield = NULL";
        } else {
            // Set the param to array ($newfield => $newvalue) and key to 'newfieldtoset'
            // name in the build sql. Later, bind_params() will detect the value array and
            // perform the needed modifications to allow the query to work. Note that
            // 'newfieldtoset' is one arbitrary name that hopefully won't be used ever
            // in order to avoid problems where the same field is used both in the set clause and in
            // the conditions. This was breaking badly in drivers using NAMED params like oci.
            $params['newfieldtoset'] = array($newfield => $newvalue);
            $newsql = "$newfield = :newfieldtoset";
        }
        $sql = "UPDATE {" . $table . "} SET $newsql $select";
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        list($sql, $params) = $this->tweak_param_names($sql, $params);
        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $stmt = $this->parse_query($sql);
        $descriptors = $this->bind_params($stmt, $params, $table);
        $result = oci_execute($stmt, $this->commit_status);
        $this->free_descriptors($descriptors);
        $this->query_end($result, $stmt);
        oci_free_statement($stmt);

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

        list($sql, $params) = $this->tweak_param_names($sql, $params);
        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $stmt = $this->parse_query($sql);
        $this->bind_params($stmt, $params);
        $result = oci_execute($stmt, $this->commit_status);
        $this->query_end($result, $stmt);
        oci_free_statement($stmt);

        return true;
    }

    function sql_null_from_clause() {
        return ' FROM dual';
    }

    public function sql_bitand($int1, $int2) {
        return 'bitand((' . $int1 . '), (' . $int2 . '))';
    }

    public function sql_bitnot($int1) {
        return '((0 - (' . $int1 . ')) - 1)';
    }

    public function sql_bitor($int1, $int2) {
        return 'MOODLELIB.BITOR(' . $int1 . ', ' . $int2 . ')';
    }

    public function sql_bitxor($int1, $int2) {
        return 'MOODLELIB.BITXOR(' . $int1 . ', ' . $int2 . ')';
    }

    /**
     * Returns the SQL text to be used in order to perform module '%'
     * operation - remainder after division
     *
     * @param integer int1 first integer in the operation
     * @param integer int2 second integer in the operation
     * @return string the piece of SQL code to be used in your statement.
     */
    public function sql_modulo($int1, $int2) {
        return 'MOD(' . $int1 . ', ' . $int2 . ')';
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
            return ' CAST(' . $fieldname . ' AS FLOAT) ';
        } else {
            return ' CAST(' . $this->sql_compare_text($fieldname) . ' AS FLOAT) ';
        }
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

        $LIKE = $notlike ? 'NOT LIKE' : 'LIKE';

        // no accent sensitiveness here for now, sorry

        if ($casesensitive) {
            return "$fieldname $LIKE $param ESCAPE '$escapechar'";
        } else {
            return "LOWER($fieldname) $LIKE LOWER($param) ESCAPE '$escapechar'";
        }
    }

    public function sql_concat() {
        $arr = func_get_args();
        if (empty($arr)) {
            return " ' ' ";
        }
        foreach ($arr as $k => $v) {
            if ($v === "' '") {
                $arr[$k] = "'*OCISP*'"; // New mega hack.
            }
        }
        $s = $this->recursive_concat($arr);
        return " MOODLELIB.UNDO_MEGA_HACK($s) ";
    }

    public function sql_concat_join($separator="' '", $elements = array()) {
        if ($separator === "' '") {
            $separator = "'*OCISP*'"; // New mega hack.
        }
        foreach ($elements as $k => $v) {
            if ($v === "' '") {
                $elements[$k] = "'*OCISP*'"; // New mega hack.
            }
        }
        for ($n = count($elements)-1; $n > 0 ; $n--) {
            array_splice($elements, $n, 0, $separator);
        }
        if (empty($elements)) {
            return " ' ' ";
        }
        $s = $this->recursive_concat($elements);
        return " MOODLELIB.UNDO_MEGA_HACK($s) ";
    }

    /**
     * Constructs 'IN()' or '=' sql fragment
     *
     * Method overriding {@link moodle_database::get_in_or_equal} to be able to get
     * more than 1000 elements working, to avoid ORA-01795. We use a pivoting technique
     * to be able to transform the params into virtual rows, so the original IN()
     * expression gets transformed into a subquery. Once more, be noted that we shouldn't
     * be using ever get_in_or_equal() with such number of parameters (proper subquery and/or
     * chunking should be used instead).
     *
     * @param mixed $items A single value or array of values for the expression.
     * @param int $type Parameter bounding type : SQL_PARAMS_QM or SQL_PARAMS_NAMED.
     * @param string $prefix Named parameter placeholder prefix (a unique counter value is appended to each parameter name).
     * @param bool $equal True means we want to equate to the constructed expression, false means we don't want to equate to it.
     * @param mixed $onemptyitems This defines the behavior when the array of items provided is empty. Defaults to false,
     *              meaning throw exceptions. Other values will become part of the returned SQL fragment.
     * @throws coding_exception | dml_exception
     * @return array A list containing the constructed sql fragment and an array of parameters.
     */
    public function get_in_or_equal($items, $type=SQL_PARAMS_QM, $prefix='param', $equal=true, $onemptyitems=false) {
        list($sql, $params) = parent::get_in_or_equal($items, $type, $prefix,  $equal, $onemptyitems);

        // Less than 1000 elements, nothing to do.
        if (count($params) < 1000) {
            return array($sql, $params); // Return unmodified.
        }

        // Extract the interesting parts of the sql to rewrite.
        if (preg_match('!(^.*IN \()([^\)]*)(.*)$!', $sql, $matches) === false) {
            return array($sql, $params); // Return unmodified.
        }

        $instart = $matches[1];
        $insql = $matches[2];
        $inend = $matches[3];
        $newsql = '';

        // Some basic verification about the matching going ok.
        $insqlarr = explode(',', $insql);
        if (count($insqlarr) !== count($params)) {
            return array($sql, $params); // Return unmodified.
        }

        // Arrived here, we need to chunk and pivot the params, building a new sql (params remain the same).
        $addunionclause = false;
        while ($chunk = array_splice($insqlarr, 0, 125)) { // Each chunk will handle up to 125 (+125 +1) elements (DECODE max is 255).
            $chunksize = count($chunk);
            if ($addunionclause) {
                $newsql .= "\n    UNION ALL";
            }
            $newsql .= "\n        SELECT DECODE(pivot";
            $counter = 1;
            foreach ($chunk as $element) {
                $newsql .= ",\n            {$counter}, " . trim($element);
                $counter++;
            }
            $newsql .= ")";
            $newsql .= "\n        FROM dual";
            $newsql .= "\n        CROSS JOIN (SELECT LEVEL AS pivot FROM dual CONNECT BY LEVEL <= {$chunksize})";
            $addunionclause = true;
        }

        // Rebuild the complete IN() clause and return it.
        return array($instart . $newsql . $inend, $params);
    }

    /**
     * Mega hacky magic to work around crazy Oracle NULL concats.
     * @param array $args
     * @return string
     */
    protected function recursive_concat(array $args) {
        $count = count($args);
        if ($count == 1) {
            $arg = reset($args);
            return $arg;
        }
        if ($count == 2) {
            $args[] = "' '";
            // No return here intentionally.
        }
        $first = array_shift($args);
        $second = array_shift($args);
        $third = $this->recursive_concat($args);
        return "MOODLELIB.TRICONCAT($first, $second, $third)";
    }

    /**
     * Returns the SQL for returning searching one string for the location of another.
     */
    public function sql_position($needle, $haystack) {
        return "INSTR(($haystack), ($needle))";
    }

    /**
     * Returns the SQL to know if one field is empty.
     *
     * @param string $tablename Name of the table (without prefix). Not used for now but can be
     *                          necessary in the future if we want to use some introspection using
     *                          meta information against the DB.
     * @param string $fieldname Name of the field we are going to check
     * @param bool $nullablefield For specifying if the field is nullable (true) or no (false) in the DB.
     * @param bool $textfield For specifying if it is a text (also called clob) field (true) or a varchar one (false)
     * @return string the sql code to be added to check for empty values
     */
    public function sql_isempty($tablename, $fieldname, $nullablefield, $textfield) {
        if ($textfield) {
            return " (".$this->sql_compare_text($fieldname)." = ' ') ";
        } else {
            return " ($fieldname = ' ') ";
        }
    }

    public function sql_order_by_text($fieldname, $numchars=32) {
        return 'dbms_lob.substr(' . $fieldname . ', ' . $numchars . ',1)';
    }

    /**
     * Is the required OCI server package installed?
     * @return bool
     */
    protected function oci_package_installed() {
        $sql = "SELECT 1
                FROM user_objects
                WHERE object_type = 'PACKAGE BODY'
                  AND object_name = 'MOODLELIB'
                  AND status = 'VALID'";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $stmt = $this->parse_query($sql);
        $result = oci_execute($stmt, $this->commit_status);
        $this->query_end($result, $stmt);
        $records = null;
        oci_fetch_all($stmt, $records, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
        oci_free_statement($stmt);
        return isset($records[0]) && reset($records[0]) ? true : false;
    }

    /**
     * Try to add required moodle package into oracle server.
     */
    protected function attempt_oci_package_install() {
        $sqls = file_get_contents(__DIR__.'/oci_native_moodle_package.sql');
        $sqls = preg_split('/^\/$/sm', $sqls);
        foreach ($sqls as $sql) {
            $sql = trim($sql);
            if ($sql === '' or $sql === 'SHOW ERRORS') {
                continue;
            }
            $this->change_database_structure($sql);
        }
    }

    /**
     * Does this driver support tool_replace?
     *
     * @since Moodle 2.8
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
        parent::get_session_lock($rowid, $timeout);

        $fullname = $this->dbname.'-'.$this->prefix.'-session-'.$rowid;
        $sql = 'SELECT MOODLELIB.GET_LOCK(:lockname, :locktimeout) FROM DUAL';
        $params = array('lockname' => $fullname , 'locktimeout' => $timeout);
        $this->query_start($sql, $params, SQL_QUERY_AUX);
        $stmt = $this->parse_query($sql);
        $this->bind_params($stmt, $params);
        $result = oci_execute($stmt, $this->commit_status);
        if ($result === false) { // Any failure in get_lock() raises error, causing return of bool false
            throw new dml_sessionwait_exception();
        }
        $this->query_end($result, $stmt);
        oci_free_statement($stmt);
    }

    public function release_session_lock($rowid) {
        if (!$this->used_for_db_sessions) {
            return;
        }

        parent::release_session_lock($rowid);

        $fullname = $this->dbname.'-'.$this->prefix.'-session-'.$rowid;
        $params = array('lockname' => $fullname);
        $sql = 'SELECT MOODLELIB.RELEASE_LOCK(:lockname) FROM DUAL';
        $this->query_start($sql, $params, SQL_QUERY_AUX);
        $stmt = $this->parse_query($sql);
        $this->bind_params($stmt, $params);
        $result = oci_execute($stmt, $this->commit_status);
        $this->query_end($result, $stmt);
        oci_free_statement($stmt);
    }

    /**
     * Driver specific start of real database transaction,
     * this can not be used directly in code.
     * @return void
     */
    protected function begin_transaction() {
        $this->commit_status = OCI_DEFAULT; //Done! ;-)
    }

    /**
     * Driver specific commit of real database transaction,
     * this can not be used directly in code.
     * @return void
     */
    protected function commit_transaction() {
        $this->query_start('--oracle_commit', NULL, SQL_QUERY_AUX);
        $result = oci_commit($this->oci);
        $this->commit_status = OCI_COMMIT_ON_SUCCESS;
        $this->query_end($result);
    }

    /**
     * Driver specific abort of real database transaction,
     * this can not be used directly in code.
     * @return void
     */
    protected function rollback_transaction() {
        $this->query_start('--oracle_rollback', NULL, SQL_QUERY_AUX);
        $result = oci_rollback($this->oci);
        $this->commit_status = OCI_COMMIT_ON_SUCCESS;
        $this->query_end($result);
    }
}
