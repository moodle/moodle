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
 * Native mysqli class representing moodle database interface.
 *
 * @package    core_dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/moodle_database.php');
require_once(__DIR__.'/moodle_read_slave_trait.php');
require_once(__DIR__.'/mysqli_native_moodle_recordset.php');
require_once(__DIR__.'/mysqli_native_moodle_temptables.php');

/**
 * Native mysqli class representing moodle database interface.
 *
 * @package    core_dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mysqli_native_moodle_database extends moodle_database {
    use moodle_read_slave_trait {
        can_use_readonly as read_slave_can_use_readonly;
    }

    /** @var mysqli $mysqli */
    protected $mysqli = null;
    /** @var bool is compressed row format supported cache */
    protected $compressedrowformatsupported = null;

    private $transactions_supported = null;

    /**
     * Attempt to create the database
     * @param string $dbhost
     * @param string $dbuser
     * @param string $dbpass
     * @param string $dbname
     * @return bool success
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function create_database($dbhost, $dbuser, $dbpass, $dbname, array $dboptions=null) {
        $driverstatus = $this->driver_installed();

        if ($driverstatus !== true) {
            throw new dml_exception('dbdriverproblem', $driverstatus);
        }

        if (!empty($dboptions['dbsocket'])
                and (strpos($dboptions['dbsocket'], '/') !== false or strpos($dboptions['dbsocket'], '\\') !== false)) {
            $dbsocket = $dboptions['dbsocket'];
        } else {
            $dbsocket = ini_get('mysqli.default_socket');
        }
        if (empty($dboptions['dbport'])) {
            $dbport = (int)ini_get('mysqli.default_port');
        } else {
            $dbport = (int)$dboptions['dbport'];
        }
        // verify ini.get does not return nonsense
        if (empty($dbport)) {
            $dbport = 3306;
        }
        ob_start();
        $conn = new mysqli($dbhost, $dbuser, $dbpass, '', $dbport, $dbsocket); // Connect without db
        $dberr = ob_get_contents();
        ob_end_clean();
        $errorno = @$conn->connect_errno;

        if ($errorno !== 0) {
            throw new dml_connection_exception($dberr);
        }

        // Normally a check would be done before setting utf8mb4, but the database can be created
        // before the enviroment checks are done. We'll proceed with creating the database and then do checks next.
        $charset = 'utf8mb4';
        if (isset($dboptions['dbcollation']) and (strpos($dboptions['dbcollation'], 'utf8_') === 0
                || strpos($dboptions['dbcollation'], 'utf8mb4_') === 0)) {
            $collation = $dboptions['dbcollation'];
            $collationinfo = explode('_', $dboptions['dbcollation']);
            $charset = reset($collationinfo);
        } else {
            $collation = 'utf8mb4_unicode_ci';
        }

        $result = $conn->query("CREATE DATABASE $dbname DEFAULT CHARACTER SET $charset DEFAULT COLLATE ".$collation);

        $conn->close();

        if (!$result) {
            throw new dml_exception('cannotcreatedb');
        }

        return true;
    }

    /**
     * Detects if all needed PHP stuff installed.
     * Note: can be used before connect()
     * @return mixed true if ok, string if something
     */
    public function driver_installed() {
        if (!extension_loaded('mysqli')) {
            return get_string('mysqliextensionisnotpresentinphp', 'install');
        }
        return true;
    }

    /**
     * Returns database family type - describes SQL dialect
     * Note: can be used before connect()
     * @return string db family name (mysql, postgres, mssql, oracle, etc.)
     */
    public function get_dbfamily() {
        return 'mysql';
    }

    /**
     * Returns more specific database driver type
     * Note: can be used before connect()
     * @return string db type mysqli, pgsql, oci, mssql, sqlsrv
     */
    protected function get_dbtype() {
        return 'mysqli';
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
     * Returns the current MySQL db engine.
     *
     * This is an ugly workaround for MySQL default engine problems,
     * Moodle is designed to work best on ACID compliant databases
     * with full transaction support. Do not use MyISAM.
     *
     * @return string or null MySQL engine name
     */
    public function get_dbengine() {
        if (isset($this->dboptions['dbengine'])) {
            return $this->dboptions['dbengine'];
        }

        if ($this->external) {
            return null;
        }

        $engine = null;

        // Look for current engine of our config table (the first table that gets created),
        // so that we create all tables with the same engine.
        $sql = "SELECT engine
                  FROM INFORMATION_SCHEMA.TABLES
                 WHERE table_schema = DATABASE() AND table_name = '{$this->prefix}config'";
        $this->query_start($sql, NULL, SQL_QUERY_AUX);
        $result = $this->mysqli->query($sql);
        $this->query_end($result);
        if ($rec = $result->fetch_assoc()) {
            // MySQL 8 BC: information_schema.* returns the fields in upper case.
            $rec = array_change_key_case($rec, CASE_LOWER);
            $engine = $rec['engine'];
        }
        $result->close();

        if ($engine) {
            // Cache the result to improve performance.
            $this->dboptions['dbengine'] = $engine;
            return $engine;
        }

        // Get the default database engine.
        $sql = "SELECT @@default_storage_engine engine";
        $this->query_start($sql, NULL, SQL_QUERY_AUX);
        $result = $this->mysqli->query($sql);
        $this->query_end($result);
        if ($rec = $result->fetch_assoc()) {
            $engine = $rec['engine'];
        }
        $result->close();

        if ($engine === 'MyISAM') {
            // we really do not want MyISAM for Moodle, InnoDB or XtraDB is a reasonable defaults if supported
            $sql = "SHOW STORAGE ENGINES";
            $this->query_start($sql, NULL, SQL_QUERY_AUX);
            $result = $this->mysqli->query($sql);
            $this->query_end($result);
            $engines = array();
            while ($res = $result->fetch_assoc()) {
                if ($res['Support'] === 'YES' or $res['Support'] === 'DEFAULT') {
                    $engines[$res['Engine']] = true;
                }
            }
            $result->close();
            if (isset($engines['InnoDB'])) {
                $engine = 'InnoDB';
            }
            if (isset($engines['XtraDB'])) {
                $engine = 'XtraDB';
            }
        }

        // Cache the result to improve performance.
        $this->dboptions['dbengine'] = $engine;
        return $engine;
    }

    /**
     * Returns the current MySQL db collation.
     *
     * This is an ugly workaround for MySQL default collation problems.
     *
     * @return string or null MySQL collation name
     */
    public function get_dbcollation() {
        if (isset($this->dboptions['dbcollation'])) {
            return $this->dboptions['dbcollation'];
        }
    }

    /**
     * Set 'dbcollation' option
     *
     * @return string $dbcollation
     */
    private function detect_collation(): string {
        if ($this->external) {
            return null;
        }

        $collation = null;

        // Look for current collation of our config table (the first table that gets created),
        // so that we create all tables with the same collation.
        $sql = "SELECT collation_name
                  FROM INFORMATION_SCHEMA.COLUMNS
                 WHERE table_schema = DATABASE() AND table_name = '{$this->prefix}config' AND column_name = 'value'";
        $result = $this->mysqli->query($sql);
        if ($rec = $result->fetch_assoc()) {
            // MySQL 8 BC: information_schema.* returns the fields in upper case.
            $rec = array_change_key_case($rec, CASE_LOWER);
            $collation = $rec['collation_name'];
        }
        $result->close();


        if (!$collation) {
            // Get the default database collation, but only if using UTF-8.
            $sql = "SELECT @@collation_database";
            $result = $this->mysqli->query($sql);
            if ($rec = $result->fetch_assoc()) {
                if (strpos($rec['@@collation_database'], 'utf8_') === 0 || strpos($rec['@@collation_database'], 'utf8mb4_') === 0) {
                    $collation = $rec['@@collation_database'];
                }
            }
            $result->close();
        }

        if (!$collation) {
            // We want only utf8 compatible collations.
            $collation = null;
            $sql = "SHOW COLLATION WHERE Collation LIKE 'utf8mb4\_%' AND Charset = 'utf8mb4'";
            $result = $this->mysqli->query($sql);
            while ($res = $result->fetch_assoc()) {
                $collation = $res['Collation'];
                if (strtoupper($res['Default']) === 'YES') {
                    $collation = $res['Collation'];
                    break;
                }
            }
            $result->close();
        }

        // Cache the result to improve performance.
        $this->dboptions['dbcollation'] = $collation;
        return $collation;
    }

    /**
     * Tests if the Antelope file format is still supported or it has been removed.
     * When removed, only Barracuda file format is supported, given the XtraDB/InnoDB engine.
     *
     * @return bool True if the Antelope file format has been removed; otherwise, false.
     */
    protected function is_antelope_file_format_no_more_supported() {
        // Breaking change: Antelope file format support has been removed from both MySQL and MariaDB.
        // The following InnoDB file format configuration parameters were deprecated and then removed:
        // - innodb_file_format
        // - innodb_file_format_check
        // - innodb_file_format_max
        // - innodb_large_prefix
        // 1. MySQL: deprecated in 5.7.7 and removed 8.0.0+.
        $ismysqlge8d0d0 = ($this->get_dbtype() == 'mysqli') &&
                version_compare($this->get_server_info()['version'], '8.0.0', '>=');
        // 2. MariaDB: deprecated in 10.2.0 and removed 10.3.1+.
        $ismariadbge10d3d1 = ($this->get_dbtype() == 'mariadb') &&
                version_compare($this->get_server_info()['version'], '10.3.1', '>=');

        return $ismysqlge8d0d0 || $ismariadbge10d3d1;
    }

    /**
     * Get the row format from the database schema.
     *
     * @param string $table
     * @return string row_format name or null if not known or table does not exist.
     */
    public function get_row_format($table = null) {
        $rowformat = null;
        if (isset($table)) {
            $table = $this->mysqli->real_escape_string($table);
            $sql = "SELECT row_format
                      FROM INFORMATION_SCHEMA.TABLES
                     WHERE table_schema = DATABASE() AND table_name = '{$this->prefix}$table'";
        } else {
            if ($this->is_antelope_file_format_no_more_supported()) {
                // Breaking change: Antelope file format support has been removed, only Barracuda.
                $dbengine = $this->get_dbengine();
                $supporteddbengines = array('InnoDB', 'XtraDB');
                if (in_array($dbengine, $supporteddbengines)) {
                    $rowformat = 'Barracuda';
                }

                return $rowformat;
            }

            $sql = "SHOW VARIABLES LIKE 'innodb_file_format'";
        }
        $this->query_start($sql, NULL, SQL_QUERY_AUX);
        $result = $this->mysqli->query($sql);
        $this->query_end($result);
        if ($rec = $result->fetch_assoc()) {
            // MySQL 8 BC: information_schema.* returns the fields in upper case.
            $rec = array_change_key_case($rec, CASE_LOWER);
            if (isset($table)) {
                $rowformat = $rec['row_format'];
            } else {
                $rowformat = $rec['value'];
            }
        }
        $result->close();

        return $rowformat;
    }

    /**
     * Is this database compatible with compressed row format?
     * This feature is necessary for support of large number of text
     * columns in InnoDB/XtraDB database.
     *
     * @param bool $cached use cached result
     * @return bool true if table can be created or changed to compressed row format.
     */
    public function is_compressed_row_format_supported($cached = true) {
        if ($cached and isset($this->compressedrowformatsupported)) {
            return($this->compressedrowformatsupported);
        }

        $engine = strtolower($this->get_dbengine());
        $info = $this->get_server_info();

        if (version_compare($info['version'], '5.5.0') < 0) {
            // MySQL 5.1 is not supported here because we cannot read the file format.
            $this->compressedrowformatsupported = false;

        } else if ($engine !== 'innodb' and $engine !== 'xtradb') {
            // Other engines are not supported, most probably not compatible.
            $this->compressedrowformatsupported = false;

        } else if (!$this->is_file_per_table_enabled()) {
            $this->compressedrowformatsupported = false;

        } else if ($this->get_row_format() !== 'Barracuda') {
            $this->compressedrowformatsupported = false;

        } else {
            // All the tests passed, we can safely use ROW_FORMAT=Compressed in sql statements.
            $this->compressedrowformatsupported = true;
        }

        return $this->compressedrowformatsupported;
    }

    /**
     * Check the database to see if innodb_file_per_table is on.
     *
     * @return bool True if on otherwise false.
     */
    public function is_file_per_table_enabled() {
        if ($filepertable = $this->get_record_sql("SHOW VARIABLES LIKE 'innodb_file_per_table'")) {
            if ($filepertable->value == 'ON') {
                return true;
            }
        }
        return false;
    }

    /**
     * Check the database to see if innodb_large_prefix is on.
     *
     * @return bool True if on otherwise false.
     */
    public function is_large_prefix_enabled() {
        if ($this->is_antelope_file_format_no_more_supported()) {
            // Breaking change: Antelope file format support has been removed, only Barracuda.
            return true;
        }

        if ($largeprefix = $this->get_record_sql("SHOW VARIABLES LIKE 'innodb_large_prefix'")) {
            if ($largeprefix->value == 'ON') {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine if the row format should be set to compressed, dynamic, or default.
     *
     * Terrible kludge. If we're using utf8mb4 AND we're using InnoDB, we need to specify row format to
     * be either dynamic or compressed (default is compact) in order to allow for bigger indexes (MySQL
     * errors #1709 and #1071).
     *
     * @param  string $engine The database engine being used. Will be looked up if not supplied.
     * @param  string $collation The database collation to use. Will look up the current collation if not supplied.
     * @return string An sql fragment to add to sql statements.
     */
    public function get_row_format_sql($engine = null, $collation = null) {

        if (!isset($engine)) {
            $engine = $this->get_dbengine();
        }
        $engine = strtolower($engine);

        if (!isset($collation)) {
            $collation = $this->get_dbcollation();
        }

        $rowformat = '';
        if (($engine === 'innodb' || $engine === 'xtradb') && strpos($collation, 'utf8mb4_') === 0) {
            if ($this->is_compressed_row_format_supported()) {
                $rowformat = "ROW_FORMAT=Compressed";
            } else {
                $rowformat = "ROW_FORMAT=Dynamic";
            }
        }
        return $rowformat;
    }

    /**
     * Returns localised database type name
     * Note: can be used before connect()
     * @return string
     */
    public function get_name() {
        return get_string('nativemysqli', 'install');
    }

    /**
     * Returns localised database configuration help.
     * Note: can be used before connect()
     * @return string
     */
    public function get_configuration_help() {
        return get_string('nativemysqlihelp', 'install');
    }

    /**
     * Diagnose database and tables, this function is used
     * to verify database and driver settings, db engine types, etc.
     *
     * @return string null means everything ok, string means problem found.
     */
    public function diagnose() {
        $sloppymyisamfound = false;
        $prefix = str_replace('_', '\\_', $this->prefix);
        $sql = "SELECT COUNT('x')
                  FROM INFORMATION_SCHEMA.TABLES
                 WHERE table_schema = DATABASE()
                       AND table_name LIKE BINARY '$prefix%'
                       AND Engine = 'MyISAM'";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = $this->mysqli->query($sql);
        $this->query_end($result);
        if ($result) {
            if ($arr = $result->fetch_assoc()) {
                $count = reset($arr);
                if ($count) {
                    $sloppymyisamfound = true;
                }
            }
            $result->close();
        }

        if ($sloppymyisamfound) {
            return get_string('myisamproblem', 'error');
        } else {
            return null;
        }
    }

    /**
     * Connect to db
     * @param string $dbhost The database host.
     * @param string $dbuser The database username.
     * @param string $dbpass The database username's password.
     * @param string $dbname The name of the database being connected to.e
     * @param mixed $prefix string means moodle db prefix, false used for external databases where prefix not used
     * @param array $dboptions driver specific options
     * @return bool success
     */
    public function raw_connect(string $dbhost, string $dbuser, string $dbpass, string $dbname, $prefix, array $dboptions=null): bool {
        $driverstatus = $this->driver_installed();

        if ($driverstatus !== true) {
            throw new dml_exception('dbdriverproblem', $driverstatus);
        }

        $this->store_settings($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions);

        // dbsocket is used ONLY if host is NULL or 'localhost',
        // you can not disable it because it is always tried if dbhost is 'localhost'
        if (!empty($this->dboptions['dbsocket'])
                and (strpos($this->dboptions['dbsocket'], '/') !== false or strpos($this->dboptions['dbsocket'], '\\') !== false)) {
            $dbsocket = $this->dboptions['dbsocket'];
        } else {
            $dbsocket = ini_get('mysqli.default_socket');
        }
        if (empty($this->dboptions['dbport'])) {
            $dbport = (int)ini_get('mysqli.default_port');
        } else {
            $dbport = (int)$this->dboptions['dbport'];
        }
        // verify ini.get does not return nonsense
        if (empty($dbport)) {
            $dbport = 3306;
        }
        if ($dbhost and !empty($this->dboptions['dbpersist'])) {
            $dbhost = "p:$dbhost";
        }
        $this->mysqli = mysqli_init();
        if (!empty($this->dboptions['connecttimeout'])) {
            $this->mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, $this->dboptions['connecttimeout']);
        }

        $conn = null;
        $dberr = null;
        try {
            // real_connect() is doing things we don't expext.
            $conn = @$this->mysqli->real_connect($dbhost, $dbuser, $dbpass, $dbname, $dbport, $dbsocket);
        } catch (\Exception $e) {
            $dberr = "$e";
        }
        if (!$conn) {
            $dberr = $dberr ?: $this->mysqli->connect_error;
            $this->mysqli = null;
            throw new dml_connection_exception($dberr);
        }

        // Disable logging until we are fully setup.
        $this->query_log_prevent();

        if (isset($dboptions['dbcollation'])) {
            $collation = $this->dboptions['dbcollation'] = $dboptions['dbcollation'];
        } else {
            $collation = $this->detect_collation();
        }
        $collationinfo = explode('_', $collation);
        $charset = reset($collationinfo);

        $this->mysqli->set_charset($charset);

        // If available, enforce strict mode for the session. That guaranties
        // standard behaviour under some situations, avoiding some MySQL nasty
        // habits like truncating data or performing some transparent cast losses.
        // With strict mode enforced, Moodle DB layer will be consistently throwing
        // the corresponding exceptions as expected.
        $si = $this->get_server_info();
        if (version_compare($si['version'], '5.0.2', '>=')) {
            $sql = "SET SESSION sql_mode = 'STRICT_ALL_TABLES'";
            $result = $this->mysqli->query($sql);
        }

        // We can enable logging now.
        $this->query_log_allow();

        // Connection stabilised and configured, going to instantiate the temptables controller
        $this->temptables = new mysqli_native_moodle_temptables($this);

        return true;
    }

    /**
     * Close database connection and release all resources
     * and memory (especially circular memory references).
     * Do NOT use connect() again, create a new instance if needed.
     */
    public function dispose() {
        parent::dispose(); // Call parent dispose to write/close session and other common stuff before closing connection
        if ($this->mysqli) {
            $this->mysqli->close();
            $this->mysqli = null;
        }
    }

    /**
     * Gets db handle currently used with queries
     * @return resource
     */
    protected function get_db_handle() {
        return $this->mysqli;
    }

    /**
     * Sets db handle to be used with subsequent queries
     * @param resource $dbh
     * @return void
     */
    protected function set_db_handle($dbh): void {
        $this->mysqli = $dbh;
    }

    /**
     * Check if The query qualifies for readonly connection execution
     * Logging queries are exempt, those are write operations that circumvent
     * standard query_start/query_end paths.
     * @param int $type type of query
     * @param string $sql
     * @return bool
     */
    protected function can_use_readonly(int $type, string $sql): bool {
        // ... *_LOCK queries always go to master.
        if (preg_match('/\b(GET|RELEASE)_LOCK/i', $sql)) {
            return false;
        }

        return $this->read_slave_can_use_readonly($type, $sql);
    }

    /**
     * Returns database server info array
     * @return array Array containing 'description' and 'version' info
     */
    public function get_server_info() {
        return array('description'=>$this->mysqli->server_info, 'version'=>$this->mysqli->server_info);
    }

    /**
     * Returns supported query parameter types
     * @return int bitmask of accepted SQL_PARAMS_*
     */
    protected function allowed_param_types() {
        return SQL_PARAMS_QM;
    }

    /**
     * Returns last error reported by database engine.
     * @return string error message
     */
    public function get_last_error() {
        return $this->mysqli->error;
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
        $prefix = str_replace('_', '\\_', $this->prefix);
        $sql = "SHOW TABLES LIKE '$prefix%'";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = $this->mysqli->query($sql);
        $this->query_end($result);
        $len = strlen($this->prefix);
        if ($result) {
            while ($arr = $result->fetch_assoc()) {
                $tablename = reset($arr);
                $tablename = substr($tablename, $len);
                $this->tables[$tablename] = $tablename;
            }
            $result->close();
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
        $fixedtable = $this->fix_table_name($table);
        $sql = "SHOW INDEXES FROM $fixedtable";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = $this->mysqli->query($sql);
        try {
            $this->query_end($result);
        } catch (dml_read_exception $e) {
            return $indexes; // table does not exist - no indexes...
        }
        if ($result) {
            while ($res = $result->fetch_object()) {
                if ($res->Key_name === 'PRIMARY') {
                    continue;
                }
                if (!isset($indexes[$res->Key_name])) {
                    $indexes[$res->Key_name] = array('unique'=>empty($res->Non_unique), 'columns'=>array());
                }
                $indexes[$res->Key_name]['columns'][$res->Seq_in_index-1] = $res->Column_name;
            }
            $result->close();
        }
        return $indexes;
    }

    /**
     * Fetches detailed information about columns in table.
     *
     * @param string $table name
     * @return database_column_info[] array of database_column_info objects indexed with column names
     */
    protected function fetch_columns(string $table): array {
        $structure = array();

        $sql = "SELECT column_name, data_type, character_maximum_length, numeric_precision,
                       numeric_scale, is_nullable, column_type, column_default, column_key, extra
                  FROM information_schema.columns
                 WHERE table_name = '" . $this->prefix.$table . "'
                       AND table_schema = '" . $this->dbname . "'
              ORDER BY ordinal_position";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = $this->mysqli->query($sql);
        $this->query_end(true); // Don't want to throw anything here ever. MDL-30147

        if ($result === false) {
            return array();
        }

        if ($result->num_rows > 0) {
            // standard table exists
            while ($rawcolumn = $result->fetch_assoc()) {
                // MySQL 8 BC: information_schema.* returns the fields in upper case.
                $rawcolumn = array_change_key_case($rawcolumn, CASE_LOWER);
                $info = (object)$this->get_column_info((object)$rawcolumn);
                $structure[$info->name] = new database_column_info($info);
            }
            $result->close();

        } else {
            // temporary tables are not in information schema, let's try it the old way
            $result->close();
            $fixedtable = $this->fix_table_name($table);
            $sql = "SHOW COLUMNS FROM $fixedtable";
            $this->query_start($sql, null, SQL_QUERY_AUX);
            $result = $this->mysqli->query($sql);
            $this->query_end(true);
            if ($result === false) {
                return array();
            }
            while ($rawcolumn = $result->fetch_assoc()) {
                $rawcolumn = (object)array_change_key_case($rawcolumn, CASE_LOWER);
                $rawcolumn->column_name              = $rawcolumn->field; unset($rawcolumn->field);
                $rawcolumn->column_type              = $rawcolumn->type; unset($rawcolumn->type);
                $rawcolumn->character_maximum_length = null;
                $rawcolumn->numeric_precision        = null;
                $rawcolumn->numeric_scale            = null;
                $rawcolumn->is_nullable              = $rawcolumn->null; unset($rawcolumn->null);
                $rawcolumn->column_default           = $rawcolumn->default; unset($rawcolumn->default);
                $rawcolumn->column_key               = $rawcolumn->key; unset($rawcolumn->key);

                if (preg_match('/(enum|varchar)\((\d+)\)/i', $rawcolumn->column_type, $matches)) {
                    $rawcolumn->data_type = $matches[1];
                    $rawcolumn->character_maximum_length = $matches[2];

                } else if (preg_match('/([a-z]*int[a-z]*)\((\d+)\)/i', $rawcolumn->column_type, $matches)) {
                    $rawcolumn->data_type = $matches[1];
                    $rawcolumn->numeric_precision = $matches[2];
                    $rawcolumn->max_length = $rawcolumn->numeric_precision;

                    $type = strtoupper($matches[1]);
                    if ($type === 'BIGINT') {
                        $maxlength = 18;
                    } else if ($type === 'INT' or $type === 'INTEGER') {
                        $maxlength = 9;
                    } else if ($type === 'MEDIUMINT') {
                        $maxlength = 6;
                    } else if ($type === 'SMALLINT') {
                        $maxlength = 4;
                    } else if ($type === 'TINYINT') {
                        $maxlength = 2;
                    } else {
                        // This should not happen.
                        $maxlength = 0;
                    }
                    if ($maxlength < $rawcolumn->max_length) {
                        $rawcolumn->max_length = $maxlength;
                    }

                } else if (preg_match('/(decimal)\((\d+),(\d+)\)/i', $rawcolumn->column_type, $matches)) {
                    $rawcolumn->data_type = $matches[1];
                    $rawcolumn->numeric_precision = $matches[2];
                    $rawcolumn->numeric_scale = $matches[3];

                } else if (preg_match('/(double|float)(\((\d+),(\d+)\))?/i', $rawcolumn->column_type, $matches)) {
                    $rawcolumn->data_type = $matches[1];
                    $rawcolumn->numeric_precision = isset($matches[3]) ? $matches[3] : null;
                    $rawcolumn->numeric_scale = isset($matches[4]) ? $matches[4] : null;

                } else if (preg_match('/([a-z]*text)/i', $rawcolumn->column_type, $matches)) {
                    $rawcolumn->data_type = $matches[1];
                    $rawcolumn->character_maximum_length = -1; // unknown

                } else if (preg_match('/([a-z]*blob)/i', $rawcolumn->column_type, $matches)) {
                    $rawcolumn->data_type = $matches[1];

                } else {
                    $rawcolumn->data_type = $rawcolumn->column_type;
                }

                $info = $this->get_column_info($rawcolumn);
                $structure[$info->name] = new database_column_info($info);
            }
            $result->close();
        }

        return $structure;
    }

    /**
     * Indicates whether column information retrieved from `information_schema.columns` has default values quoted or not.
     * @return boolean True when default values are quoted (breaking change); otherwise, false.
     */
    protected function has_breaking_change_quoted_defaults() {
        return false;
    }

    /**
     * Indicates whether SQL_MODE default value has changed in a not backward compatible way.
     * @return boolean True when SQL_MODE breaks BC; otherwise, false.
     */
    public function has_breaking_change_sqlmode() {
        return false;
    }

    /**
     * Returns moodle column info for raw column from information schema.
     * @param stdClass $rawcolumn
     * @return stdClass standardised colum info
     */
    private function get_column_info(stdClass $rawcolumn) {
        $rawcolumn = (object)$rawcolumn;
        $info = new stdClass();
        $info->name           = $rawcolumn->column_name;
        $info->type           = $rawcolumn->data_type;
        $info->meta_type      = $this->mysqltype2moodletype($rawcolumn->data_type);
        if ($this->has_breaking_change_quoted_defaults()) {
            $info->default_value = is_null($rawcolumn->column_default) ? null : trim($rawcolumn->column_default, "'");
            if ($info->default_value === 'NULL') {
                $info->default_value = null;
            }
        } else {
            $info->default_value = $rawcolumn->column_default;
        }
        $info->has_default    = !is_null($info->default_value);
        $info->not_null       = ($rawcolumn->is_nullable === 'NO');
        $info->primary_key    = ($rawcolumn->column_key === 'PRI');
        $info->binary         = false;
        $info->unsigned       = null;
        $info->auto_increment = false;
        $info->unique         = null;
        $info->scale          = null;

        if ($info->meta_type === 'C') {
            $info->max_length = $rawcolumn->character_maximum_length;

        } else if ($info->meta_type === 'I') {
            if ($info->primary_key) {
                $info->meta_type = 'R';
                $info->unique    = true;
            }
            // Return number of decimals, not bytes here.
            $info->max_length    = $rawcolumn->numeric_precision;
            if (preg_match('/([a-z]*int[a-z]*)\((\d+)\)/i', $rawcolumn->column_type, $matches)) {
                $type = strtoupper($matches[1]);
                if ($type === 'BIGINT') {
                    $maxlength = 18;
                } else if ($type === 'INT' or $type === 'INTEGER') {
                    $maxlength = 9;
                } else if ($type === 'MEDIUMINT') {
                    $maxlength = 6;
                } else if ($type === 'SMALLINT') {
                    $maxlength = 4;
                } else if ($type === 'TINYINT') {
                    $maxlength = 2;
                } else {
                    // This should not happen.
                    $maxlength = 0;
                }
                // It is possible that display precision is different from storage type length,
                // always use the smaller value to make sure our data fits.
                if ($maxlength < $info->max_length) {
                    $info->max_length = $maxlength;
                }
            }
            $info->unsigned      = (stripos($rawcolumn->column_type, 'unsigned') !== false);
            $info->auto_increment= (strpos($rawcolumn->extra, 'auto_increment') !== false);

        } else if ($info->meta_type === 'N') {
            $info->max_length    = $rawcolumn->numeric_precision;
            $info->scale         = $rawcolumn->numeric_scale;
            $info->unsigned      = (stripos($rawcolumn->column_type, 'unsigned') !== false);

        } else if ($info->meta_type === 'X') {
            if ("$rawcolumn->character_maximum_length" === '4294967295') { // watch out for PHP max int limits!
                // means maximum moodle size for text column, in other drivers it may also mean unknown size
                $info->max_length = -1;
            } else {
                $info->max_length = $rawcolumn->character_maximum_length;
            }
            $info->primary_key   = false;

        } else if ($info->meta_type === 'B') {
            $info->max_length    = -1;
            $info->primary_key   = false;
            $info->binary        = true;
        }

        return $info;
    }

    /**
     * Normalise column type.
     * @param string $mysql_type
     * @return string one character
     * @throws dml_exception
     */
    private function mysqltype2moodletype($mysql_type) {
        $type = null;

        switch(strtoupper($mysql_type)) {
            case 'BIT':
                $type = 'L';
                break;

            case 'TINYINT':
            case 'SMALLINT':
            case 'MEDIUMINT':
            case 'INT':
            case 'INTEGER':
            case 'BIGINT':
                $type = 'I';
                break;

            case 'FLOAT':
            case 'DOUBLE':
            case 'DECIMAL':
                $type = 'N';
                break;

            case 'CHAR':
            case 'ENUM':
            case 'SET':
            case 'VARCHAR':
                $type = 'C';
                break;

            case 'TINYTEXT':
            case 'TEXT':
            case 'MEDIUMTEXT':
            case 'LONGTEXT':
                $type = 'X';
                break;

            case 'BINARY':
            case 'VARBINARY':
            case 'BLOB':
            case 'TINYBLOB':
            case 'MEDIUMBLOB':
            case 'LONGBLOB':
                $type = 'B';
                break;

            case 'DATE':
            case 'TIME':
            case 'DATETIME':
            case 'TIMESTAMP':
            case 'YEAR':
                $type = 'D';
                break;
        }

        if (!$type) {
            throw new dml_exception('invalidmysqlnativetype', $mysql_type);
        }
        return $type;
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

        } else if ($value === '') {
            if ($column->meta_type == 'I' or $column->meta_type == 'F' or $column->meta_type == 'N') {
                $value = 0; // prevent '' problems in numeric fields
            }
        // Any float value being stored in varchar or text field is converted to string to avoid
        // any implicit conversion by MySQL
        } else if (is_float($value) and ($column->meta_type == 'C' or $column->meta_type == 'X')) {
            $value = "$value";
        }
        return $value;
    }

    /**
     * Is this database compatible with utf8?
     * @return bool
     */
    public function setup_is_unicodedb() {
        // All new tables are created with this collation, we just have to make sure it is utf8 compatible,
        // if config table already exists it has this collation too.
        $collation = $this->get_dbcollation();

        $collationinfo = explode('_', $collation);
        $charset = reset($collationinfo);

        $sql = "SHOW COLLATION WHERE Collation ='$collation' AND Charset = '$charset'";
        $this->query_start($sql, NULL, SQL_QUERY_AUX);
        $result = $this->mysqli->query($sql);
        $this->query_end($result);
        if ($result->fetch_assoc()) {
            $return = true;
        } else {
            $return = false;
        }
        $result->close();

        return $return;
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
        if (is_array($sql)) {
            $sql = implode("\n;\n", $sql);
        }

        try {
            $this->query_start($sql, null, SQL_QUERY_STRUCTURE);
            $result = $this->mysqli->multi_query($sql);
            if ($result === false) {
                $this->query_end(false);
            }
            while ($this->mysqli->more_results()) {
                $result = $this->mysqli->next_result();
                if ($result === false) {
                    $this->query_end(false);
                }
            }
            $this->query_end(true);
        } catch (ddl_change_structure_exception $e) {
            while (@$this->mysqli->more_results()) {
                @$this->mysqli->next_result();
            }
            $this->reset_caches($tablenames);
            throw $e;
        }

        $this->reset_caches($tablenames);
        return true;
    }

    /**
     * Very ugly hack which emulates bound parameters in queries
     * because prepared statements do not use query cache.
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
            } else if (is_null($param)) {
                $return .= 'NULL';
            } else if (is_number($param)) {
                $return .= "'".$param."'"; // we have to always use strings because mysql is using weird automatic int casting
            } else if (is_float($param)) {
                $return .= $param;
            } else {
                $param = $this->mysqli->real_escape_string($param);
                $return .= "'$param'";
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

        if (strpos($sql, ';') !== false) {
            throw new coding_exception('moodle_database::execute() Multiple sql statements found or bound parameters not used properly in query!');
        }

        $rawsql = $this->emulate_bound_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $result = $this->mysqli->query($rawsql);
        $this->query_end($result);

        if ($result === true) {
            return true;

        } else {
            $result->close();
            return true;
        }
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
            if ($limitnum < 1) {
                $limitnum = "18446744073709551615";
            }
            $sql .= " LIMIT $limitfrom, $limitnum";
        }

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        $rawsql = $this->emulate_bound_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_SELECT);
        // no MYSQLI_USE_RESULT here, it would block write ops on affected tables
        $result = $this->mysqli->query($rawsql, MYSQLI_STORE_RESULT);
        $this->query_end($result);

        return $this->create_recordset($result);
    }

    /**
     * Get all records from a table.
     *
     * This method works around potential memory problems and may improve performance,
     * this method may block access to table until the recordset is closed.
     *
     * @param string $table Name of database table.
     * @return moodle_recordset A moodle_recordset instance {@link function get_recordset}.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function export_table_recordset($table) {
        $sql = $this->fix_table_names("SELECT * FROM {{$table}}");

        $this->query_start($sql, array(), SQL_QUERY_SELECT);
        // MYSQLI_STORE_RESULT may eat all memory for large tables, unfortunately MYSQLI_USE_RESULT blocks other queries.
        $result = $this->mysqli->query($sql, MYSQLI_USE_RESULT);
        $this->query_end($result);

        return $this->create_recordset($result);
    }

    protected function create_recordset($result) {
        return new mysqli_native_moodle_recordset($result);
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

        list($limitfrom, $limitnum) = $this->normalise_limit_from_num($limitfrom, $limitnum);

        if ($limitfrom or $limitnum) {
            if ($limitnum < 1) {
                $limitnum = "18446744073709551615";
            }
            $sql .= " LIMIT $limitfrom, $limitnum";
        }

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        $rawsql = $this->emulate_bound_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_SELECT);
        $result = $this->mysqli->query($rawsql, MYSQLI_STORE_RESULT);
        $this->query_end($result);

        $return = array();

        while($row = $result->fetch_assoc()) {
            $row = array_change_key_case($row, CASE_LOWER);
            $id  = reset($row);
            if (isset($return[$id])) {
                $colname = key($row);
                debugging("Did you remember to make the first column something unique in your call to get_records? Duplicate value '$id' found in column '$colname'.", DEBUG_DEVELOPER);
            }
            $return[$id] = (object)$row;
        }
        $result->close();

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
        $rawsql = $this->emulate_bound_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_SELECT);
        $result = $this->mysqli->query($rawsql, MYSQLI_STORE_RESULT);
        $this->query_end($result);

        $return = array();

        while($row = $result->fetch_assoc()) {
            $return[] = reset($row);
        }
        $result->close();

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
        $fixedtable = $this->fix_table_name($table);
        $sql = "INSERT INTO $fixedtable ($fields) VALUES($qms)";

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        $rawsql = $this->emulate_bound_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_INSERT);
        $result = $this->mysqli->query($rawsql);
        $id = @$this->mysqli->insert_id; // must be called before query_end() which may insert log into db
        $this->query_end($result);

        if (!$customsequence and !$id) {
            throw new dml_write_exception('unknown error fetching inserted id');
        }

        if (!$returnid) {
            return true;
        } else {
            return (int)$id;
        }
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
            if (!isset($columns[$field])) {
                continue;
            }
            $column = $columns[$field];
            $cleaned[$field] = $this->normalise_value($column, $value);
        }

        return $this->insert_record_raw($table, $cleaned, $returnid, $bulk);
    }

    /**
     * Insert multiple records into database as fast as possible.
     *
     * Order of inserts is maintained, but the operation is not atomic,
     * use transactions if necessary.
     *
     * This method is intended for inserting of large number of small objects,
     * do not use for huge objects with text or binary fields.
     *
     * @since Moodle 2.7
     *
     * @param string $table  The database table to be inserted into
     * @param array|Traversable $dataobjects list of objects to be inserted, must be compatible with foreach
     * @return void does not return new record ids
     *
     * @throws coding_exception if data objects have different structure
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function insert_records($table, $dataobjects) {
        if (!is_array($dataobjects) and !$dataobjects instanceof Traversable) {
            throw new coding_exception('insert_records() passed non-traversable object');
        }

        // MySQL has a relatively small query length limit by default,
        // make sure 'max_allowed_packet' in my.cnf is high enough
        // if you change the following default...
        static $chunksize = null;
        if ($chunksize === null) {
            if (!empty($this->dboptions['bulkinsertsize'])) {
                $chunksize = (int)$this->dboptions['bulkinsertsize'];

            } else {
                if (PHP_INT_SIZE === 4) {
                    // Bad luck for Windows, we cannot do any maths with large numbers.
                    $chunksize = 5;
                } else {
                    $sql = "SHOW VARIABLES LIKE 'max_allowed_packet'";
                    $this->query_start($sql, null, SQL_QUERY_AUX);
                    $result = $this->mysqli->query($sql);
                    $this->query_end($result);
                    $size = 0;
                    if ($rec = $result->fetch_assoc()) {
                        $size = $rec['Value'];
                    }
                    $result->close();
                    // Hopefully 200kb per object are enough.
                    $chunksize = (int)($size / 200000);
                    if ($chunksize > 50) {
                        $chunksize = 50;
                    }
                }
            }
        }

        $columns = $this->get_columns($table, true);
        $fields = null;
        $count = 0;
        $chunk = array();
        foreach ($dataobjects as $dataobject) {
            if (!is_array($dataobject) and !is_object($dataobject)) {
                throw new coding_exception('insert_records() passed invalid record object');
            }
            $dataobject = (array)$dataobject;
            if ($fields === null) {
                $fields = array_keys($dataobject);
                $columns = array_intersect_key($columns, $dataobject);
                unset($columns['id']);
            } else if ($fields !== array_keys($dataobject)) {
                throw new coding_exception('All dataobjects in insert_records() must have the same structure!');
            }

            $count++;
            $chunk[] = $dataobject;

            if ($count === $chunksize) {
                $this->insert_chunk($table, $chunk, $columns);
                $chunk = array();
                $count = 0;
            }
        }

        if ($count) {
            $this->insert_chunk($table, $chunk, $columns);
        }
    }

    /**
     * Insert records in chunks.
     *
     * Note: can be used only from insert_records().
     *
     * @param string $table
     * @param array $chunk
     * @param database_column_info[] $columns
     */
    protected function insert_chunk($table, array $chunk, array $columns) {
        $fieldssql = '('.implode(',', array_keys($columns)).')';

        $valuessql = '('.implode(',', array_fill(0, count($columns), '?')).')';
        $valuessql = implode(',', array_fill(0, count($chunk), $valuessql));

        $params = array();
        foreach ($chunk as $dataobject) {
            foreach ($columns as $field => $column) {
                $params[] = $this->normalise_value($column, $dataobject[$field]);
            }
        }

        $fixedtable = $this->fix_table_name($table);
        $sql = "INSERT INTO $fixedtable $fieldssql VALUES $valuessql";

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        $rawsql = $this->emulate_bound_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_INSERT);
        $result = $this->mysqli->query($rawsql);
        $this->query_end($result);
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
            $cleaned[$field] = $value;
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
        $fixedtable = $this->fix_table_name($table);
        $sql = "UPDATE $fixedtable SET $sets WHERE id=?";

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        $rawsql = $this->emulate_bound_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $result = $this->mysqli->query($rawsql);
        $this->query_end($result);

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
        list($select, $params, $type) = $this->fix_sql_params($select, $params);

        // Get column metadata
        $columns = $this->get_columns($table);
        $column = $columns[$newfield];

        $normalised_value = $this->normalise_value($column, $newvalue);

        if (is_null($normalised_value)) {
            $newfield = "$newfield = NULL";
        } else {
            $newfield = "$newfield = ?";
            array_unshift($params, $normalised_value);
        }
        $fixedtable = $this->fix_table_name($table);
        $sql = "UPDATE $fixedtable SET $newfield $select";
        $rawsql = $this->emulate_bound_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $result = $this->mysqli->query($rawsql);
        $this->query_end($result);

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
        $fixedtable = $this->fix_table_name($table);
        $sql = "DELETE FROM $fixedtable $select";

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        $rawsql = $this->emulate_bound_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $result = $this->mysqli->query($rawsql);
        $this->query_end($result);

        return true;
    }

    /**
     * Deletes records using a subquery, which is done with a strange DELETE...JOIN syntax in MySQL
     * because it performs very badly with normal subqueries.
     *
     * @param string $table Table to delete from
     * @param string $field Field in table to match
     * @param string $alias Name of single column in subquery e.g. 'id'
     * @param string $subquery Query that will return values of the field to delete
     * @param array $params Parameters for query
     * @throws dml_exception If there is any error
     */
    public function delete_records_subquery(string $table, string $field, string $alias, string $subquery, array $params = []): void {
        // Aliases mysql_deltable and mysql_subquery are chosen to be unlikely to conflict.
        $this->execute("DELETE mysql_deltable FROM {" . $table . "} mysql_deltable JOIN " .
                "($subquery) mysql_subquery ON mysql_subquery.$alias = mysql_deltable.$field", $params);
    }

    public function sql_cast_char2int($fieldname, $text=false) {
        return ' CAST(' . $fieldname . ' AS SIGNED) ';
    }

    public function sql_cast_char2real($fieldname, $text=false) {
        // Set to 65 (max mysql 5.5 precision) with 7 as scale
        // because we must ensure at least 6 decimal positions
        // per casting given that postgres is casting to that scale (::real::).
        // Can be raised easily but that must be done in all DBs and tests.
        return ' CAST(' . $fieldname . ' AS DECIMAL(65,7)) ';
    }

    public function sql_equal($fieldname, $param, $casesensitive = true, $accentsensitive = true, $notequal = false) {
        $equalop = $notequal ? '<>' : '=';

        $collationinfo = explode('_', $this->get_dbcollation());
        $bincollate = reset($collationinfo) . '_bin';

        if ($casesensitive) {
            // Current MySQL versions do not support case sensitive and accent insensitive.
            return "$fieldname COLLATE $bincollate $equalop $param";
        } else if ($accentsensitive) {
            // Case insensitive and accent sensitive, we can force a binary comparison once all texts are using the same case.
            return "LOWER($fieldname) COLLATE $bincollate $equalop LOWER($param)";
        } else {
            // Case insensitive and accent insensitive. All collations are that way, but utf8_bin.
            $collation = '';
            if ($this->get_dbcollation() == 'utf8_bin') {
                $collation = 'COLLATE utf8_unicode_ci';
            } else if ($this->get_dbcollation() == 'utf8mb4_bin') {
                $collation = 'COLLATE utf8mb4_unicode_ci';
            }
            return "$fieldname $collation $equalop $param";
        }
    }

    /**
     * Returns 'LIKE' part of a query.
     *
     * Note that mysql does not support $casesensitive = true and $accentsensitive = false.
     * More information in http://bugs.mysql.com/bug.php?id=19567.
     *
     * @param string $fieldname usually name of the table column
     * @param string $param usually bound query parameter (?, :named)
     * @param bool $casesensitive use case sensitive search
     * @param bool $accensensitive use accent sensitive search (ignored if $casesensitive is true)
     * @param bool $notlike true means "NOT LIKE"
     * @param string $escapechar escape char for '%' and '_'
     * @return string SQL code fragment
     */
    public function sql_like($fieldname, $param, $casesensitive = true, $accentsensitive = true, $notlike = false, $escapechar = '\\') {
        if (strpos($param, '%') !== false) {
            debugging('Potential SQL injection detected, sql_like() expects bound parameters (? or :named)');
        }
        $escapechar = $this->mysqli->real_escape_string($escapechar); // prevents problems with C-style escapes of enclosing '\'

        $collationinfo = explode('_', $this->get_dbcollation());
        $bincollate = reset($collationinfo) . '_bin';

        $LIKE = $notlike ? 'NOT LIKE' : 'LIKE';

        if ($casesensitive) {
            // Current MySQL versions do not support case sensitive and accent insensitive.
            return "$fieldname $LIKE $param COLLATE $bincollate ESCAPE '$escapechar'";

        } else if ($accentsensitive) {
            // Case insensitive and accent sensitive, we can force a binary comparison once all texts are using the same case.
            return "LOWER($fieldname) $LIKE LOWER($param) COLLATE $bincollate ESCAPE '$escapechar'";

        } else {
            // Case insensitive and accent insensitive.
            $collation = '';
            if ($this->get_dbcollation() == 'utf8_bin') {
                // Force a case insensitive comparison if using utf8_bin.
                $collation = 'COLLATE utf8_unicode_ci';
            } else if ($this->get_dbcollation() == 'utf8mb4_bin') {
                // Force a case insensitive comparison if using utf8mb4_bin.
                $collation = 'COLLATE utf8mb4_unicode_ci';
            }

            return "$fieldname $LIKE $param $collation ESCAPE '$escapechar'";
        }
    }

    /**
     * Returns the proper SQL to do CONCAT between the elements passed
     * Can take many parameters
     *
     * @param string $str,... 1 or more fields/strings to concat
     *
     * @return string The concat sql
     */
    public function sql_concat() {
        $arr = func_get_args();
        $s = implode(', ', $arr);
        if ($s === '') {
            return "''";
        }
        return "CONCAT($s)";
    }

    /**
     * Returns the proper SQL to do CONCAT between the elements passed
     * with a given separator
     *
     * @param string $separator The string to use as the separator
     * @param array $elements An array of items to concatenate
     * @return string The concat SQL
     */
    public function sql_concat_join($separator="' '", $elements=array()) {
        $s = implode(', ', $elements);

        if ($s === '') {
            return "''";
        }
        return "CONCAT_WS($separator, $s)";
    }

    /**
     * Returns the SQL text to be used to calculate the length in characters of one expression.
     * @param string fieldname or expression to calculate its length in characters.
     * @return string the piece of SQL code to be used in the statement.
     */
    public function sql_length($fieldname) {
        return ' CHAR_LENGTH(' . $fieldname . ')';
    }

    /**
     * Does this driver support regex syntax when searching
     */
    public function sql_regex_supported() {
        return true;
    }

    /**
     * Return regex positive or negative match sql
     * @param bool $positivematch
     * @param bool $casesensitive
     * @return string or empty if not supported
     */
    public function sql_regex($positivematch = true, $casesensitive = false) {
        $collation = '';
        if ($casesensitive) {
            if (substr($this->get_dbcollation(), -4) !== '_bin') {
                $collationinfo = explode('_', $this->get_dbcollation());
                $collation = 'COLLATE ' . $collationinfo[0] . '_bin ';
            }
        } else {
            if ($this->get_dbcollation() == 'utf8_bin') {
                $collation = 'COLLATE utf8_unicode_ci ';
            } else if ($this->get_dbcollation() == 'utf8mb4_bin') {
                $collation = 'COLLATE utf8mb4_unicode_ci ';
            }
        }

        return $collation . ($positivematch ? 'REGEXP' : 'NOT REGEXP');
    }

    /**
     * Returns the SQL to be used in order to an UNSIGNED INTEGER column to SIGNED.
     *
     * @deprecated since 2.3
     * @param string $fieldname The name of the field to be cast
     * @return string The piece of SQL code to be used in your statement.
     */
    public function sql_cast_2signed($fieldname) {
        return ' CAST(' . $fieldname . ' AS SIGNED) ';
    }

    /**
     * Returns the SQL that allows to find intersection of two or more queries
     *
     * @since Moodle 2.8
     *
     * @param array $selects array of SQL select queries, each of them only returns fields with the names from $fields
     * @param string $fields comma-separated list of fields
     * @return string SQL query that will return only values that are present in each of selects
     */
    public function sql_intersect($selects, $fields) {
        if (count($selects) <= 1) {
            return parent::sql_intersect($selects, $fields);
        }
        $fields = preg_replace('/\s/', '', $fields);
        static $aliascnt = 0;
        $falias = 'intsctal'.($aliascnt++);
        $rv = "SELECT $falias.".
            preg_replace('/,/', ','.$falias.'.', $fields).
            " FROM ($selects[0]) $falias";
        for ($i = 1; $i < count($selects); $i++) {
            $alias = 'intsctal'.($aliascnt++);
            $rv .= " JOIN (".$selects[$i].") $alias ON ".
                join(' AND ',
                    array_map(
                        function($a) use ($alias, $falias) {
                            return $falias . '.' . $a .' = ' . $alias . '.' . $a;
                        },
                        preg_split('/,/', $fields))
                );
        }
        return $rv;
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
        parent::get_session_lock($rowid, $timeout);

        $fullname = $this->dbname.'-'.$this->prefix.'-session-'.$rowid;
        $sql = "SELECT GET_LOCK('$fullname', $timeout)";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = $this->mysqli->query($sql);
        $this->query_end($result);

        if ($result) {
            $arr = $result->fetch_assoc();
            $result->close();

            if (reset($arr) == 1) {
                return;
            } else {
                throw new dml_sessionwait_exception();
            }
        }
    }

    public function release_session_lock($rowid) {
        if (!$this->used_for_db_sessions) {
            return;
        }

        parent::release_session_lock($rowid);
        $fullname = $this->dbname.'-'.$this->prefix.'-session-'.$rowid;
        $sql = "SELECT RELEASE_LOCK('$fullname')";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $result = $this->mysqli->query($sql);
        $this->query_end($result);

        if ($result) {
            $result->close();
        }
    }

    /**
     * Are transactions supported?
     * It is not responsible to run productions servers
     * on databases without transaction support ;-)
     *
     * MyISAM does not support support transactions.
     *
     * You can override this via the dbtransactions option.
     *
     * @return bool
     */
    protected function transactions_supported() {
        if (!is_null($this->transactions_supported)) {
            return $this->transactions_supported;
        }

        // this is all just guessing, might be better to just specify it in config.php
        if (isset($this->dboptions['dbtransactions'])) {
            $this->transactions_supported = $this->dboptions['dbtransactions'];
            return $this->transactions_supported;
        }

        $this->transactions_supported = false;

        $engine = $this->get_dbengine();

        // Only will accept transactions if using compatible storage engine (more engines can be added easily BDB, Falcon...)
        if (in_array($engine, array('InnoDB', 'INNOBASE', 'BDB', 'XtraDB', 'Aria', 'Falcon'))) {
            $this->transactions_supported = true;
        }

        return $this->transactions_supported;
    }

    /**
     * Driver specific start of real database transaction,
     * this can not be used directly in code.
     * @return void
     */
    protected function begin_transaction() {
        if (!$this->transactions_supported()) {
            return;
        }

        $sql = "SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED";
        $this->query_start($sql, NULL, SQL_QUERY_AUX);
        $result = $this->mysqli->query($sql);
        $this->query_end($result);

        $sql = "START TRANSACTION";
        $this->query_start($sql, NULL, SQL_QUERY_AUX);
        $result = $this->mysqli->query($sql);
        $this->query_end($result);
    }

    /**
     * Driver specific commit of real database transaction,
     * this can not be used directly in code.
     * @return void
     */
    protected function commit_transaction() {
        if (!$this->transactions_supported()) {
            return;
        }

        $sql = "COMMIT";
        $this->query_start($sql, NULL, SQL_QUERY_AUX);
        $result = $this->mysqli->query($sql);
        $this->query_end($result);
    }

    /**
     * Driver specific abort of real database transaction,
     * this can not be used directly in code.
     * @return void
     */
    protected function rollback_transaction() {
        if (!$this->transactions_supported()) {
            return;
        }

        $sql = "ROLLBACK";
        $this->query_start($sql, NULL, SQL_QUERY_AUX);
        $result = $this->mysqli->query($sql);
        $this->query_end($result);

        return true;
    }

    /**
     * Converts a table to either 'Compressed' or 'Dynamic' row format.
     *
     * @param string $tablename Name of the table to convert to the new row format.
     */
    public function convert_table_row_format($tablename) {
        $currentrowformat = $this->get_row_format($tablename);
        if ($currentrowformat == 'Compact' || $currentrowformat == 'Redundant') {
            $rowformat = ($this->is_compressed_row_format_supported(false)) ? "ROW_FORMAT=Compressed" : "ROW_FORMAT=Dynamic";
            $prefix = $this->get_prefix();
            $this->change_database_structure("ALTER TABLE {$prefix}$tablename $rowformat");
        }
    }

    /**
     * Does this mysql instance support fulltext indexes?
     *
     * @return bool
     */
    public function is_fulltext_search_supported() {
        $info = $this->get_server_info();

        if (version_compare($info['version'], '5.6.4', '>=')) {
            return true;
        }
        return false;
    }

    /**
     * Fixes any table names that clash with reserved words.
     *
     * @param string $tablename The table name
     * @return string The fixed table name
     */
    protected function fix_table_name($tablename) {
        $prefixedtablename = parent::fix_table_name($tablename);
        // This function quotes the table name if it matches one of the MySQL reserved
        // words, e.g. groups.
        return $this->get_manager()->generator->getEncQuoted($prefixedtablename);
    }
}
