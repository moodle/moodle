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
 * Abstract database driver class.
 *
 * @package    core_dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/database_column_info.php');
require_once(__DIR__.'/moodle_recordset.php');
require_once(__DIR__.'/moodle_transaction.php');

/** SQL_PARAMS_NAMED - Bitmask, indicates :name type parameters are supported by db backend. */
define('SQL_PARAMS_NAMED', 1);

/** SQL_PARAMS_QM - Bitmask, indicates ? type parameters are supported by db backend. */
define('SQL_PARAMS_QM', 2);

/** SQL_PARAMS_DOLLAR - Bitmask, indicates $1, $2, ... type parameters are supported by db backend. */
define('SQL_PARAMS_DOLLAR', 4);

/** SQL_QUERY_SELECT - Normal select query, reading only. */
define('SQL_QUERY_SELECT', 1);

/** SQL_QUERY_INSERT - Insert select query, writing. */
define('SQL_QUERY_INSERT', 2);

/** SQL_QUERY_UPDATE - Update select query, writing. */
define('SQL_QUERY_UPDATE', 3);

/** SQL_QUERY_STRUCTURE - Query changing db structure, writing. */
define('SQL_QUERY_STRUCTURE', 4);

/** SQL_QUERY_AUX - Auxiliary query done by driver, setting connection config, getting table info, etc. */
define('SQL_QUERY_AUX', 5);

/**
 * Abstract class representing moodle database interface.
 * @link http://docs.moodle.org/dev/DML_functions
 *
 * @package    core_dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class moodle_database {

    /** @var database_manager db manager which allows db structure modifications. */
    protected $database_manager;
    /** @var moodle_temptables temptables manager to provide cross-db support for temp tables. */
    protected $temptables;
    /** @var array Cache of table info. */
    protected $tables  = null;

    // db connection options
    /** @var string db host name. */
    protected $dbhost;
    /** @var string db host user. */
    protected $dbuser;
    /** @var string db host password. */
    protected $dbpass;
    /** @var string db name. */
    protected $dbname;
    /** @var string Prefix added to table names. */
    protected $prefix;

    /** @var array Database or driver specific options, such as sockets or TCP/IP db connections. */
    protected $dboptions;

    /** @var bool True means non-moodle external database used.*/
    protected $external;

    /** @var int The database reads (performance counter).*/
    protected $reads = 0;
    /** @var int The database writes (performance counter).*/
    protected $writes = 0;
    /** @var float Time queries took to finish, seconds with microseconds.*/
    protected $queriestime = 0;

    /** @var int Debug level. */
    protected $debug  = 0;

    /** @var string Last used query sql. */
    protected $last_sql;
    /** @var array Last query parameters. */
    protected $last_params;
    /** @var int Last query type. */
    protected $last_type;
    /** @var string Last extra info. */
    protected $last_extrainfo;
    /** @var float Last time in seconds with millisecond precision. */
    protected $last_time;
    /** @var bool Flag indicating logging of query in progress. This helps prevent infinite loops. */
    protected $loggingquery = false;

    /** @var bool True if the db is used for db sessions. */
    protected $used_for_db_sessions = false;

    /** @var array Array containing open transactions. */
    protected $transactions = array();
    /** @var bool Flag used to force rollback of all current transactions. */
    private $force_rollback = false;

    /** @var string MD5 of settings used for connection. Used by MUC as an identifier. */
    private $settingshash;

    /** @var cache_application for column info */
    protected $metacache;

    /** @var cache_request for column info on temp tables */
    protected $metacachetemp;

    /** @var bool flag marking database instance as disposed */
    protected $disposed;

    /**
     * @var int internal temporary variable used to fix params. Its used by {@link _fix_sql_params_dollar_callback()}.
     */
    private $fix_sql_params_i;
    /**
     * @var int internal temporary variable used to guarantee unique parameters in each request. Its used by {@link get_in_or_equal()}.
     */
    private $inorequaluniqueindex = 1;

    /**
     * @var boolean variable use to temporarily disable logging.
     */
    protected $skiplogging = false;

    /**
     * Constructor - Instantiates the database, specifying if it's external (connect to other systems) or not (Moodle DB).
     *              Note that this affects the decision of whether prefix checks must be performed or not.
     * @param bool $external True means that an external database is used.
     */
    public function __construct($external=false) {
        $this->external  = $external;
    }

    /**
     * Destructor - cleans up and flushes everything needed.
     */
    public function __destruct() {
        $this->dispose();
    }

    /**
     * Detects if all needed PHP stuff are installed for DB connectivity.
     * Note: can be used before connect()
     * @return mixed True if requirements are met, otherwise a string if something isn't installed.
     */
    public abstract function driver_installed();

    /**
     * Returns database table prefix
     * Note: can be used before connect()
     * @return string The prefix used in the database.
     */
    public function get_prefix() {
        return $this->prefix;
    }

    /**
     * Loads and returns a database instance with the specified type and library.
     *
     * The loaded class is within lib/dml directory and of the form: $type.'_'.$library.'_moodle_database'
     *
     * @param string $type Database driver's type. (eg: mysqli, pgsql, mssql, sqldrv, oci, etc.)
     * @param string $library Database driver's library (native, pdo, etc.)
     * @param bool $external True if this is an external database.
     * @return moodle_database driver object or null if error, for example of driver object see {@link mysqli_native_moodle_database}
     */
    public static function get_driver_instance($type, $library, $external = false) {
        global $CFG;

        $classname = $type.'_'.$library.'_moodle_database';
        $libfile   = "$CFG->libdir/dml/$classname.php";

        if (!file_exists($libfile)) {
            return null;
        }

        require_once($libfile);
        return new $classname($external);
    }

    /**
     * Returns the database vendor.
     * Note: can be used before connect()
     * @return string The db vendor name, usually the same as db family name.
     */
    public function get_dbvendor() {
        return $this->get_dbfamily();
    }

    /**
     * Returns the database family type. (This sort of describes the SQL 'dialect')
     * Note: can be used before connect()
     * @return string The db family name (mysql, postgres, mssql, oracle, etc.)
     */
    public abstract function get_dbfamily();

    /**
     * Returns a more specific database driver type
     * Note: can be used before connect()
     * @return string The db type mysqli, pgsql, oci, mssql, sqlsrv
     */
    protected abstract function get_dbtype();

    /**
     * Returns the general database library name
     * Note: can be used before connect()
     * @return string The db library type -  pdo, native etc.
     */
    protected abstract function get_dblibrary();

    /**
     * Returns the localised database type name
     * Note: can be used before connect()
     * @return string
     */
    public abstract function get_name();

    /**
     * Returns the localised database configuration help.
     * Note: can be used before connect()
     * @return string
     */
    public abstract function get_configuration_help();

    /**
     * Returns the localised database description
     * Note: can be used before connect()
     * @deprecated since 2.6
     * @return string
     */
    public function get_configuration_hints() {
        debugging('$DB->get_configuration_hints() method is deprecated, use $DB->get_configuration_help() instead');
        return $this->get_configuration_help();
    }

    /**
     * Returns the db related part of config.php
     * @return stdClass
     */
    public function export_dbconfig() {
        $cfg = new stdClass();
        $cfg->dbtype    = $this->get_dbtype();
        $cfg->dblibrary = $this->get_dblibrary();
        $cfg->dbhost    = $this->dbhost;
        $cfg->dbname    = $this->dbname;
        $cfg->dbuser    = $this->dbuser;
        $cfg->dbpass    = $this->dbpass;
        $cfg->prefix    = $this->prefix;
        if ($this->dboptions) {
            $cfg->dboptions = $this->dboptions;
        }

        return $cfg;
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
     * Connects to the database.
     * Must be called before other methods.
     * @param string $dbhost The database host.
     * @param string $dbuser The database user to connect as.
     * @param string $dbpass The password to use when connecting to the database.
     * @param string $dbname The name of the database being connected to.
     * @param mixed $prefix string means moodle db prefix, false used for external databases where prefix not used
     * @param array $dboptions driver specific options
     * @return bool true
     * @throws dml_connection_exception if error
     */
    public abstract function connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, array $dboptions=null);

    /**
     * Store various database settings
     * @param string $dbhost The database host.
     * @param string $dbuser The database user to connect as.
     * @param string $dbpass The password to use when connecting to the database.
     * @param string $dbname The name of the database being connected to.
     * @param mixed $prefix string means moodle db prefix, false used for external databases where prefix not used
     * @param array $dboptions driver specific options
     * @return void
     */
    protected function store_settings($dbhost, $dbuser, $dbpass, $dbname, $prefix, array $dboptions=null) {
        $this->dbhost    = $dbhost;
        $this->dbuser    = $dbuser;
        $this->dbpass    = $dbpass;
        $this->dbname    = $dbname;
        $this->prefix    = $prefix;
        $this->dboptions = (array)$dboptions;
    }

    /**
     * Returns a hash for the settings used during connection.
     *
     * If not already requested it is generated and stored in a private property.
     *
     * @return string
     */
    protected function get_settings_hash() {
        if (empty($this->settingshash)) {
            $this->settingshash = md5($this->dbhost . $this->dbuser . $this->dbname . $this->prefix);
        }
        return $this->settingshash;
    }

    /**
     * Handle the creation and caching of the databasemeta information for all databases.
     *
     * @return cache_application The databasemeta cachestore to complete operations on.
     */
    protected function get_metacache() {
        if (!isset($this->metacache)) {
            $properties = array('dbfamily' => $this->get_dbfamily(), 'settings' => $this->get_settings_hash());
            $this->metacache = cache::make('core', 'databasemeta', $properties);
        }
        return $this->metacache;
    }

    /**
     * Handle the creation and caching of the temporary tables.
     *
     * @return cache_application The temp_tables cachestore to complete operations on.
     */
    protected function get_temp_tables_cache() {
        if (!isset($this->metacachetemp)) {
            // Using connection data to prevent collisions when using the same temp table name with different db connections.
            $properties = array('dbfamily' => $this->get_dbfamily(), 'settings' => $this->get_settings_hash());
            $this->metacachetemp = cache::make('core', 'temp_tables', $properties);
        }
        return $this->metacachetemp;
    }

    /**
     * Attempt to create the database
     * @param string $dbhost The database host.
     * @param string $dbuser The database user to connect as.
     * @param string $dbpass The password to use when connecting to the database.
     * @param string $dbname The name of the database being connected to.
     * @param array $dboptions An array of optional database options (eg: dbport)
     *
     * @return bool success True for successful connection. False otherwise.
     */
    public function create_database($dbhost, $dbuser, $dbpass, $dbname, array $dboptions=null) {
        return false;
    }

    /**
     * Returns transaction trace for debugging purposes.
     * @private to be used by core only
     * @return array or null if not in transaction.
     */
    public function get_transaction_start_backtrace() {
        if (!$this->transactions) {
            return null;
        }
        $lowesttransaction = end($this->transactions);
        return $lowesttransaction->get_backtrace();
    }

    /**
     * Closes the database connection and releases all resources
     * and memory (especially circular memory references).
     * Do NOT use connect() again, create a new instance if needed.
     * @return void
     */
    public function dispose() {
        if ($this->disposed) {
            return;
        }
        $this->disposed = true;
        if ($this->transactions) {
            $this->force_transaction_rollback();
        }

        if ($this->temptables) {
            $this->temptables->dispose();
            $this->temptables = null;
        }
        if ($this->database_manager) {
            $this->database_manager->dispose();
            $this->database_manager = null;
        }
        $this->tables  = null;
    }

    /**
     * This should be called before each db query.
     * @param string $sql The query string.
     * @param array $params An array of parameters.
     * @param int $type The type of query. ( SQL_QUERY_SELECT | SQL_QUERY_AUX | SQL_QUERY_INSERT | SQL_QUERY_UPDATE | SQL_QUERY_STRUCTURE )
     * @param mixed $extrainfo This is here for any driver specific extra information.
     * @return void
     */
    protected function query_start($sql, array $params=null, $type, $extrainfo=null) {
        if ($this->loggingquery) {
            return;
        }
        $this->last_sql       = $sql;
        $this->last_params    = $params;
        $this->last_type      = $type;
        $this->last_extrainfo = $extrainfo;
        $this->last_time      = microtime(true);

        switch ($type) {
            case SQL_QUERY_SELECT:
            case SQL_QUERY_AUX:
                $this->reads++;
                break;
            case SQL_QUERY_INSERT:
            case SQL_QUERY_UPDATE:
            case SQL_QUERY_STRUCTURE:
                $this->writes++;
            default:
                if ((PHPUNIT_TEST) || (defined('BEHAT_TEST') && BEHAT_TEST) ||
                    defined('BEHAT_SITE_RUNNING')) {

                    // Set list of tables that are updated.
                    require_once(__DIR__.'/../testing/classes/util.php');
                    testing_util::set_table_modified_by_sql($sql);
                }
        }

        $this->print_debug($sql, $params);
    }

    /**
     * This should be called immediately after each db query. It does a clean up of resources.
     * It also throws exceptions if the sql that ran produced errors.
     * @param mixed $result The db specific result obtained from running a query.
     * @throws dml_read_exception | dml_write_exception | ddl_change_structure_exception
     * @return void
     */
    protected function query_end($result) {
        if ($this->loggingquery) {
            return;
        }
        if ($result !== false) {
            $this->query_log();
            // free memory
            $this->last_sql    = null;
            $this->last_params = null;
            $this->print_debug_time();
            return;
        }

        // remember current info, log queries may alter it
        $type   = $this->last_type;
        $sql    = $this->last_sql;
        $params = $this->last_params;
        $error  = $this->get_last_error();

        $this->query_log($error);

        switch ($type) {
            case SQL_QUERY_SELECT:
            case SQL_QUERY_AUX:
                throw new dml_read_exception($error, $sql, $params);
            case SQL_QUERY_INSERT:
            case SQL_QUERY_UPDATE:
                throw new dml_write_exception($error, $sql, $params);
            case SQL_QUERY_STRUCTURE:
                $this->get_manager(); // includes ddl exceptions classes ;-)
                throw new ddl_change_structure_exception($error, $sql);
        }
    }

    /**
     * This logs the last query based on 'logall', 'logslow' and 'logerrors' options configured via $CFG->dboptions .
     * @param string|bool $error or false if not error
     * @return void
     */
    public function query_log($error=false) {
        // Logging disabled by the driver.
        if ($this->skiplogging) {
            return;
        }

        $logall    = !empty($this->dboptions['logall']);
        $logslow   = !empty($this->dboptions['logslow']) ? $this->dboptions['logslow'] : false;
        $logerrors = !empty($this->dboptions['logerrors']);
        $iserror   = ($error !== false);

        $time = $this->query_time();

        // Will be shown or not depending on MDL_PERF values rather than in dboptions['log*].
        $this->queriestime = $this->queriestime + $time;

        if ($logall or ($logslow and ($logslow < ($time+0.00001))) or ($iserror and $logerrors)) {
            $this->loggingquery = true;
            try {
                $backtrace = debug_backtrace();
                if ($backtrace) {
                    //remove query_log()
                    array_shift($backtrace);
                }
                if ($backtrace) {
                    //remove query_end()
                    array_shift($backtrace);
                }
                $log = new stdClass();
                $log->qtype      = $this->last_type;
                $log->sqltext    = $this->last_sql;
                $log->sqlparams  = var_export((array)$this->last_params, true);
                $log->error      = (int)$iserror;
                $log->info       = $iserror ? $error : null;
                $log->backtrace  = format_backtrace($backtrace, true);
                $log->exectime   = $time;
                $log->timelogged = time();
                $this->insert_record('log_queries', $log);
            } catch (Exception $ignored) {
            }
            $this->loggingquery = false;
        }
    }

    /**
     * Disable logging temporarily.
     */
    protected function query_log_prevent() {
        $this->skiplogging = true;
    }

    /**
     * Restore old logging behavior.
     */
    protected function query_log_allow() {
        $this->skiplogging = false;
    }

    /**
     * Returns the time elapsed since the query started.
     * @return float Seconds with microseconds
     */
    protected function query_time() {
        return microtime(true) - $this->last_time;
    }

    /**
     * Returns database server info array
     * @return array Array containing 'description' and 'version' at least.
     */
    public abstract function get_server_info();

    /**
     * Returns supported query parameter types
     * @return int bitmask of accepted SQL_PARAMS_*
     */
    protected abstract function allowed_param_types();

    /**
     * Returns the last error reported by the database engine.
     * @return string The error message.
     */
    public abstract function get_last_error();

    /**
     * Prints sql debug info
     * @param string $sql The query which is being debugged.
     * @param array $params The query parameters. (optional)
     * @param mixed $obj The library specific object. (optional)
     * @return void
     */
    protected function print_debug($sql, array $params=null, $obj=null) {
        if (!$this->get_debug()) {
            return;
        }
        if (CLI_SCRIPT) {
            $separator = "--------------------------------\n";
            echo $separator;
            echo "{$sql}\n";
            if (!is_null($params)) {
                echo "[" . var_export($params, true) . "]\n";
            }
            echo $separator;
        } else if (AJAX_SCRIPT) {
            $separator = "--------------------------------";
            error_log($separator);
            error_log($sql);
            if (!is_null($params)) {
                error_log("[" . var_export($params, true) . "]");
            }
            error_log($separator);
        } else {
            $separator = "<hr />\n";
            echo $separator;
            echo s($sql) . "\n";
            if (!is_null($params)) {
                echo "[" . s(var_export($params, true)) . "]\n";
            }
            echo $separator;
        }
    }

    /**
     * Prints the time a query took to run.
     * @return void
     */
    protected function print_debug_time() {
        if (!$this->get_debug()) {
            return;
        }
        $time = $this->query_time();
        $message = "Query took: {$time} seconds.\n";
        if (CLI_SCRIPT) {
            echo $message;
            echo "--------------------------------\n";
        } else if (AJAX_SCRIPT) {
            error_log($message);
            error_log("--------------------------------");
        } else {
            echo s($message);
            echo "<hr />\n";
        }
    }

    /**
     * Returns the SQL WHERE conditions.
     * @param string $table The table name that these conditions will be validated against.
     * @param array $conditions The conditions to build the where clause. (must not contain numeric indexes)
     * @throws dml_exception
     * @return array An array list containing sql 'where' part and 'params'.
     */
    protected function where_clause($table, array $conditions=null) {
        // We accept nulls in conditions
        $conditions = is_null($conditions) ? array() : $conditions;

        if (empty($conditions)) {
            return array('', array());
        }

        // Some checks performed under debugging only
        if (debugging()) {
            $columns = $this->get_columns($table);
            if (empty($columns)) {
                // no supported columns means most probably table does not exist
                throw new dml_exception('ddltablenotexist', $table);
            }
            foreach ($conditions as $key=>$value) {
                if (!isset($columns[$key])) {
                    $a = new stdClass();
                    $a->fieldname = $key;
                    $a->tablename = $table;
                    throw new dml_exception('ddlfieldnotexist', $a);
                }
                $column = $columns[$key];
                if ($column->meta_type == 'X') {
                    //ok so the column is a text column. sorry no text columns in the where clause conditions
                    throw new dml_exception('textconditionsnotallowed', $conditions);
                }
            }
        }

        $allowed_types = $this->allowed_param_types();
        $where = array();
        $params = array();

        foreach ($conditions as $key=>$value) {
            if (is_int($key)) {
                throw new dml_exception('invalidnumkey');
            }
            if (is_null($value)) {
                $where[] = "$key IS NULL";
            } else {
                if ($allowed_types & SQL_PARAMS_NAMED) {
                    // Need to verify key names because they can contain, originally,
                    // spaces and other forbidden chars when using sql_xxx() functions and friends.
                    $normkey = trim(preg_replace('/[^a-zA-Z0-9_-]/', '_', $key), '-_');
                    if ($normkey !== $key) {
                        debugging('Invalid key found in the conditions array.');
                    }
                    $where[] = "$key = :$normkey";
                    $params[$normkey] = $value;
                } else {
                    $where[] = "$key = ?";
                    $params[] = $value;
                }
            }
        }
        $where = implode(" AND ", $where);
        return array($where, $params);
    }

    /**
     * Returns SQL WHERE conditions for the ..._list group of methods.
     *
     * @param string $field the name of a field.
     * @param array $values the values field might take.
     * @return array An array containing sql 'where' part and 'params'
     */
    protected function where_clause_list($field, array $values) {
        if (empty($values)) {
            return array("1 = 2", array()); // Fake condition, won't return rows ever. MDL-17645
        }

        // Note: Do not use get_in_or_equal() because it can not deal with bools and nulls.

        $params = array();
        $select = "";
        $values = (array)$values;
        foreach ($values as $value) {
            if (is_bool($value)) {
                $value = (int)$value;
            }
            if (is_null($value)) {
                $select = "$field IS NULL";
            } else {
                $params[] = $value;
            }
        }
        if ($params) {
            if ($select !== "") {
                $select = "$select OR ";
            }
            $count = count($params);
            if ($count == 1) {
                $select = $select."$field = ?";
            } else {
                $qs = str_repeat(',?', $count);
                $qs = ltrim($qs, ',');
                $select = $select."$field IN ($qs)";
            }
        }
        return array($select, $params);
    }

    /**
     * Constructs 'IN()' or '=' sql fragment
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

        // default behavior, throw exception on empty array
        if (is_array($items) and empty($items) and $onemptyitems === false) {
            throw new coding_exception('moodle_database::get_in_or_equal() does not accept empty arrays');
        }
        // handle $onemptyitems on empty array of items
        if (is_array($items) and empty($items)) {
            if (is_null($onemptyitems)) {             // Special case, NULL value
                $sql = $equal ? ' IS NULL' : ' IS NOT NULL';
                return (array($sql, array()));
            } else {
                $items = array($onemptyitems);        // Rest of cases, prepare $items for std processing
            }
        }

        if ($type == SQL_PARAMS_QM) {
            if (!is_array($items) or count($items) == 1) {
                $sql = $equal ? '= ?' : '<> ?';
                $items = (array)$items;
                $params = array_values($items);
            } else {
                if ($equal) {
                    $sql = 'IN ('.implode(',', array_fill(0, count($items), '?')).')';
                } else {
                    $sql = 'NOT IN ('.implode(',', array_fill(0, count($items), '?')).')';
                }
                $params = array_values($items);
            }

        } else if ($type == SQL_PARAMS_NAMED) {
            if (empty($prefix)) {
                $prefix = 'param';
            }

            if (!is_array($items)){
                $param = $prefix.$this->inorequaluniqueindex++;
                $sql = $equal ? "= :$param" : "<> :$param";
                $params = array($param=>$items);
            } else if (count($items) == 1) {
                $param = $prefix.$this->inorequaluniqueindex++;
                $sql = $equal ? "= :$param" : "<> :$param";
                $item = reset($items);
                $params = array($param=>$item);
            } else {
                $params = array();
                $sql = array();
                foreach ($items as $item) {
                    $param = $prefix.$this->inorequaluniqueindex++;
                    $params[$param] = $item;
                    $sql[] = ':'.$param;
                }
                if ($equal) {
                    $sql = 'IN ('.implode(',', $sql).')';
                } else {
                    $sql = 'NOT IN ('.implode(',', $sql).')';
                }
            }

        } else {
            throw new dml_exception('typenotimplement');
        }
        return array($sql, $params);
    }

    /**
     * Converts short table name {tablename} to the real prefixed table name in given sql.
     * @param string $sql The sql to be operated on.
     * @return string The sql with tablenames being prefixed with $CFG->prefix
     */
    protected function fix_table_names($sql) {
        return preg_replace_callback(
            '/\{([a-z][a-z0-9_]*)\}/',
            function($matches) {
                return $this->fix_table_name($matches[1]);
            },
            $sql
        );
    }

    /**
     * Adds the prefix to the table name.
     *
     * @param string $tablename The table name
     * @return string The prefixed table name
     */
    protected function fix_table_name($tablename) {
        return $this->prefix . $tablename;
    }

    /**
     * Internal private utitlity function used to fix parameters.
     * Used with {@link preg_replace_callback()}
     * @param array $match Refer to preg_replace_callback usage for description.
     * @return string
     */
    private function _fix_sql_params_dollar_callback($match) {
        $this->fix_sql_params_i++;
        return "\$".$this->fix_sql_params_i;
    }

    /**
     * Detects object parameters and throws exception if found
     * @param mixed $value
     * @return void
     * @throws coding_exception if object detected
     */
    protected function detect_objects($value) {
        if (is_object($value)) {
            throw new coding_exception('Invalid database query parameter value', 'Objects are are not allowed: '.get_class($value));
        }
    }

    /**
     * Normalizes sql query parameters and verifies parameters.
     * @param string $sql The query or part of it.
     * @param array $params The query parameters.
     * @return array (sql, params, type of params)
     */
    public function fix_sql_params($sql, array $params=null) {
        $params = (array)$params; // mke null array if needed
        $allowed_types = $this->allowed_param_types();

        // convert table names
        $sql = $this->fix_table_names($sql);

        // cast booleans to 1/0 int and detect forbidden objects
        foreach ($params as $key => $value) {
            $this->detect_objects($value);
            $params[$key] = is_bool($value) ? (int)$value : $value;
        }

        // NICOLAS C: Fixed regexp for negative backwards look-ahead of double colons. Thanks for Sam Marshall's help
        $named_count = preg_match_all('/(?<!:):[a-z][a-z0-9_]*/', $sql, $named_matches); // :: used in pgsql casts
        $dollar_count = preg_match_all('/\$[1-9][0-9]*/', $sql, $dollar_matches);
        $q_count     = substr_count($sql, '?');

        $count = 0;

        if ($named_count) {
            $type = SQL_PARAMS_NAMED;
            $count = $named_count;

        }
        if ($dollar_count) {
            if ($count) {
                throw new dml_exception('mixedtypesqlparam');
            }
            $type = SQL_PARAMS_DOLLAR;
            $count = $dollar_count;

        }
        if ($q_count) {
            if ($count) {
                throw new dml_exception('mixedtypesqlparam');
            }
            $type = SQL_PARAMS_QM;
            $count = $q_count;

        }

        if (!$count) {
             // ignore params
            if ($allowed_types & SQL_PARAMS_NAMED) {
                return array($sql, array(), SQL_PARAMS_NAMED);
            } else if ($allowed_types & SQL_PARAMS_QM) {
                return array($sql, array(), SQL_PARAMS_QM);
            } else {
                return array($sql, array(), SQL_PARAMS_DOLLAR);
            }
        }

        if ($count > count($params)) {
            $a = new stdClass;
            $a->expected = $count;
            $a->actual = count($params);
            throw new dml_exception('invalidqueryparam', $a);
        }

        $target_type = $allowed_types;

        if ($type & $allowed_types) { // bitwise AND
            if ($count == count($params)) {
                if ($type == SQL_PARAMS_QM) {
                    return array($sql, array_values($params), SQL_PARAMS_QM); // 0-based array required
                } else {
                    //better do the validation of names below
                }
            }
            // needs some fixing or validation - there might be more params than needed
            $target_type = $type;
        }

        if ($type == SQL_PARAMS_NAMED) {
            $finalparams = array();
            foreach ($named_matches[0] as $key) {
                $key = trim($key, ':');
                if (!array_key_exists($key, $params)) {
                    throw new dml_exception('missingkeyinsql', $key, '');
                }
                if (strlen($key) > 30) {
                    throw new coding_exception(
                            "Placeholder names must be 30 characters or shorter. '" .
                            $key . "' is too long.", $sql);
                }
                $finalparams[$key] = $params[$key];
            }
            if ($count != count($finalparams)) {
                throw new dml_exception('duplicateparaminsql');
            }

            if ($target_type & SQL_PARAMS_QM) {
                $sql = preg_replace('/(?<!:):[a-z][a-z0-9_]*/', '?', $sql);
                return array($sql, array_values($finalparams), SQL_PARAMS_QM); // 0-based required
            } else if ($target_type & SQL_PARAMS_NAMED) {
                return array($sql, $finalparams, SQL_PARAMS_NAMED);
            } else {  // $type & SQL_PARAMS_DOLLAR
                //lambda-style functions eat memory - we use globals instead :-(
                $this->fix_sql_params_i = 0;
                $sql = preg_replace_callback('/(?<!:):[a-z][a-z0-9_]*/', array($this, '_fix_sql_params_dollar_callback'), $sql);
                return array($sql, array_values($finalparams), SQL_PARAMS_DOLLAR); // 0-based required
            }

        } else if ($type == SQL_PARAMS_DOLLAR) {
            if ($target_type & SQL_PARAMS_DOLLAR) {
                return array($sql, array_values($params), SQL_PARAMS_DOLLAR); // 0-based required
            } else if ($target_type & SQL_PARAMS_QM) {
                $sql = preg_replace('/\$[0-9]+/', '?', $sql);
                return array($sql, array_values($params), SQL_PARAMS_QM); // 0-based required
            } else { //$target_type & SQL_PARAMS_NAMED
                $sql = preg_replace('/\$([0-9]+)/', ':param\\1', $sql);
                $finalparams = array();
                foreach ($params as $key=>$param) {
                    $key++;
                    $finalparams['param'.$key] = $param;
                }
                return array($sql, $finalparams, SQL_PARAMS_NAMED);
            }

        } else { // $type == SQL_PARAMS_QM
            if (count($params) != $count) {
                $params = array_slice($params, 0, $count);
            }

            if ($target_type & SQL_PARAMS_QM) {
                return array($sql, array_values($params), SQL_PARAMS_QM); // 0-based required
            } else if ($target_type & SQL_PARAMS_NAMED) {
                $finalparams = array();
                $pname = 'param0';
                $parts = explode('?', $sql);
                $sql = array_shift($parts);
                foreach ($parts as $part) {
                    $param = array_shift($params);
                    $pname++;
                    $sql .= ':'.$pname.$part;
                    $finalparams[$pname] = $param;
                }
                return array($sql, $finalparams, SQL_PARAMS_NAMED);
            } else {  // $type & SQL_PARAMS_DOLLAR
                //lambda-style functions eat memory - we use globals instead :-(
                $this->fix_sql_params_i = 0;
                $sql = preg_replace_callback('/\?/', array($this, '_fix_sql_params_dollar_callback'), $sql);
                return array($sql, array_values($params), SQL_PARAMS_DOLLAR); // 0-based required
            }
        }
    }

    /**
     * Ensures that limit params are numeric and positive integers, to be passed to the database.
     * We explicitly treat null, '' and -1 as 0 in order to provide compatibility with how limit
     * values have been passed historically.
     *
     * @param int $limitfrom Where to start results from
     * @param int $limitnum How many results to return
     * @return array Normalised limit params in array($limitfrom, $limitnum)
     */
    protected function normalise_limit_from_num($limitfrom, $limitnum) {
        global $CFG;

        // We explicilty treat these cases as 0.
        if ($limitfrom === null || $limitfrom === '' || $limitfrom === -1) {
            $limitfrom = 0;
        }
        if ($limitnum === null || $limitnum === '' || $limitnum === -1) {
            $limitnum = 0;
        }

        if ($CFG->debugdeveloper) {
            if (!is_numeric($limitfrom)) {
                $strvalue = var_export($limitfrom, true);
                debugging("Non-numeric limitfrom parameter detected: $strvalue, did you pass the correct arguments?",
                    DEBUG_DEVELOPER);
            } else if ($limitfrom < 0) {
                debugging("Negative limitfrom parameter detected: $limitfrom, did you pass the correct arguments?",
                    DEBUG_DEVELOPER);
            }

            if (!is_numeric($limitnum)) {
                $strvalue = var_export($limitnum, true);
                debugging("Non-numeric limitnum parameter detected: $strvalue, did you pass the correct arguments?",
                    DEBUG_DEVELOPER);
            } else if ($limitnum < 0) {
                debugging("Negative limitnum parameter detected: $limitnum, did you pass the correct arguments?",
                    DEBUG_DEVELOPER);
            }
        }

        $limitfrom = (int)$limitfrom;
        $limitnum  = (int)$limitnum;
        $limitfrom = max(0, $limitfrom);
        $limitnum  = max(0, $limitnum);

        return array($limitfrom, $limitnum);
    }

    /**
     * Return tables in database WITHOUT current prefix.
     * @param bool $usecache if true, returns list of cached tables.
     * @return array of table names in lowercase and without prefix
     */
    public abstract function get_tables($usecache=true);

    /**
     * Return table indexes - everything lowercased.
     * @param string $table The table we want to get indexes from.
     * @return array An associative array of indexes containing 'unique' flag and 'columns' being indexed
     */
    public abstract function get_indexes($table);

    /**
     * Returns detailed information about columns in table. This information is cached internally.
     *
     * @param string $table The table's name.
     * @param bool $usecache Flag to use internal cacheing. The default is true.
     * @return database_column_info[] of database_column_info objects indexed with column names
     */
    public function get_columns($table, $usecache = true): array {
        if (!$table) { // Table not specified, return empty array directly.
            return [];
        }

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

        $structure = $this->fetch_columns($table);

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
     * Returns detailed information about columns in table. This information is cached internally.
     *
     * @param string $table The table's name.
     * @return database_column_info[] of database_column_info objects indexed with column names
     */
    protected abstract function fetch_columns(string $table): array;

    /**
     * Normalise values based on varying RDBMS's dependencies (booleans, LOBs...)
     *
     * @param database_column_info $column column metadata corresponding with the value we are going to normalise
     * @param mixed $value value we are going to normalise
     * @return mixed the normalised value
     */
    protected abstract function normalise_value($column, $value);

    /**
     * Resets the internal column details cache
     *
     * @param array|null $tablenames an array of xmldb table names affected by this request.
     * @return void
     */
    public function reset_caches($tablenames = null) {
        if (!empty($tablenames)) {
            $dbmetapurged = false;
            foreach ($tablenames as $tablename) {
                if ($this->temptables->is_temptable($tablename)) {
                    $this->get_temp_tables_cache()->delete($tablename);
                } else if ($dbmetapurged === false) {
                    $this->tables = null;
                    $this->get_metacache()->purge();
                    $this->metacache = null;
                    $dbmetapurged = true;
                }
            }
        } else {
            $this->get_temp_tables_cache()->purge();
            $this->tables = null;
            // Purge MUC as well.
            $this->get_metacache()->purge();
            $this->metacache = null;
        }
    }

    /**
     * Returns the sql generator used for db manipulation.
     * Used mostly in upgrade.php scripts.
     * @return database_manager The instance used to perform ddl operations.
     * @see lib/ddl/database_manager.php
     */
    public function get_manager() {
        global $CFG;

        if (!$this->database_manager) {
            require_once($CFG->libdir.'/ddllib.php');

            $classname = $this->get_dbfamily().'_sql_generator';
            require_once("$CFG->libdir/ddl/$classname.php");
            $generator = new $classname($this, $this->temptables);

            $this->database_manager = new database_manager($this, $generator);
        }
        return $this->database_manager;
    }

    /**
     * Attempts to change db encoding to UTF-8 encoding if possible.
     * @return bool True is successful.
     */
    public function change_db_encoding() {
        return false;
    }

    /**
     * Checks to see if the database is in unicode mode?
     * @return bool
     */
    public function setup_is_unicodedb() {
        return true;
    }

    /**
     * Enable/disable very detailed debugging.
     * @param bool $state
     * @return void
     */
    public function set_debug($state) {
        $this->debug = $state;
    }

    /**
     * Returns debug status
     * @return bool $state
     */
    public function get_debug() {
        return $this->debug;
    }

    /**
     * Enable/disable detailed sql logging
     *
     * @deprecated since Moodle 2.9
     */
    public function set_logging($state) {
        throw new coding_exception('set_logging() can not be used any more.');
    }

    /**
     * Do NOT use in code, this is for use by database_manager only!
     * @param string|array $sql query or array of queries
     * @param array|null $tablenames an array of xmldb table names affected by this request.
     * @return bool true
     * @throws ddl_change_structure_exception A DDL specific exception is thrown for any errors.
     */
    public abstract function change_database_structure($sql, $tablenames = null);

    /**
     * Executes a general sql query. Should be used only when no other method suitable.
     * Do NOT use this to make changes in db structure, use database_manager methods instead!
     * @param string $sql query
     * @param array $params query parameters
     * @return bool true
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public abstract function execute($sql, array $params=null);

    /**
     * Get a number of records as a moodle_recordset where all the given conditions met.
     *
     * Selects records from the table $table.
     *
     * If specified, only records meeting $conditions.
     *
     * If specified, the results will be sorted as specified by $sort. This
     * is added to the SQL as "ORDER BY $sort". Example values of $sort
     * might be "time ASC" or "time DESC".
     *
     * If $fields is specified, only those fields are returned.
     *
     * Since this method is a little less readable, use of it should be restricted to
     * code where it's possible there might be large datasets being returned.  For known
     * small datasets use get_records - it leads to simpler code.
     *
     * If you only want some of the records, specify $limitfrom and $limitnum.
     * The query will skip the first $limitfrom records (according to the sort
     * order) and then return the next $limitnum records. If either of $limitfrom
     * or $limitnum is specified, both must be present.
     *
     * The return value is a moodle_recordset
     * if the query succeeds. If an error occurs, false is returned.
     *
     * @param string $table the table to query.
     * @param array $conditions optional array $fieldname=>requestedvalue with AND in between
     * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
     * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return moodle_recordset A moodle_recordset instance
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_recordset($table, array $conditions=null, $sort='', $fields='*', $limitfrom=0, $limitnum=0) {
        list($select, $params) = $this->where_clause($table, $conditions);
        return $this->get_recordset_select($table, $select, $params, $sort, $fields, $limitfrom, $limitnum);
    }

    /**
     * Get a number of records as a moodle_recordset where one field match one list of values.
     *
     * Only records where $field takes one of the values $values are returned.
     * $values must be an array of values.
     *
     * Other arguments and the return type are like {@link function get_recordset}.
     *
     * @param string $table the table to query.
     * @param string $field a field to check (optional).
     * @param array $values array of values the field must have
     * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
     * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return moodle_recordset A moodle_recordset instance.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_recordset_list($table, $field, array $values, $sort='', $fields='*', $limitfrom=0, $limitnum=0) {
        list($select, $params) = $this->where_clause_list($field, $values);
        return $this->get_recordset_select($table, $select, $params, $sort, $fields, $limitfrom, $limitnum);
    }

    /**
     * Get a number of records as a moodle_recordset which match a particular WHERE clause.
     *
     * If given, $select is used as the SELECT parameter in the SQL query,
     * otherwise all records from the table are returned.
     *
     * Other arguments and the return type are like {@link function get_recordset}.
     *
     * @param string $table the table to query.
     * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
     * @param array $params array of sql parameters
     * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
     * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return moodle_recordset A moodle_recordset instance.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_recordset_select($table, $select, array $params=null, $sort='', $fields='*', $limitfrom=0, $limitnum=0) {
        $sql = "SELECT $fields FROM {".$table."}";
        if ($select) {
            $sql .= " WHERE $select";
        }
        if ($sort) {
            $sql .= " ORDER BY $sort";
        }
        return $this->get_recordset_sql($sql, $params, $limitfrom, $limitnum);
    }

    /**
     * Get a number of records as a moodle_recordset using a SQL statement.
     *
     * Since this method is a little less readable, use of it should be restricted to
     * code where it's possible there might be large datasets being returned.  For known
     * small datasets use get_records_sql - it leads to simpler code.
     *
     * The return type is like {@link function get_recordset}.
     *
     * @param string $sql the SQL select query to execute.
     * @param array $params array of sql parameters
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return moodle_recordset A moodle_recordset instance.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public abstract function get_recordset_sql($sql, array $params=null, $limitfrom=0, $limitnum=0);

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
        return $this->get_recordset($table, array());
    }

    /**
     * Get a number of records as an array of objects where all the given conditions met.
     *
     * If the query succeeds and returns at least one record, the
     * return value is an array of objects, one object for each
     * record found. The array key is the value from the first
     * column of the result set. The object associated with that key
     * has a member variable for each column of the results.
     *
     * @param string $table the table to query.
     * @param array $conditions optional array $fieldname=>requestedvalue with AND in between
     * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
     * @param string $fields a comma separated list of fields to return (optional, by default
     *   all fields are returned). The first field will be used as key for the
     *   array so must be a unique field such as 'id'.
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records in total (optional, required if $limitfrom is set).
     * @return array An array of Objects indexed by first column.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_records($table, array $conditions=null, $sort='', $fields='*', $limitfrom=0, $limitnum=0) {
        list($select, $params) = $this->where_clause($table, $conditions);
        return $this->get_records_select($table, $select, $params, $sort, $fields, $limitfrom, $limitnum);
    }

    /**
     * Get a number of records as an array of objects where one field match one list of values.
     *
     * Return value is like {@link function get_records}.
     *
     * @param string $table The database table to be checked against.
     * @param string $field The field to search
     * @param array $values An array of values
     * @param string $sort Sort order (as valid SQL sort parameter)
     * @param string $fields A comma separated list of fields to be returned from the chosen table. If specified,
     *   the first field should be a unique one such as 'id' since it will be used as a key in the associative
     *   array.
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records in total (optional).
     * @return array An array of objects indexed by first column
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_records_list($table, $field, array $values, $sort='', $fields='*', $limitfrom=0, $limitnum=0) {
        list($select, $params) = $this->where_clause_list($field, $values);
        return $this->get_records_select($table, $select, $params, $sort, $fields, $limitfrom, $limitnum);
    }

    /**
     * Get a number of records as an array of objects which match a particular WHERE clause.
     *
     * Return value is like {@link function get_records}.
     *
     * @param string $table The table to query.
     * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
     * @param array $params An array of sql parameters
     * @param string $sort An order to sort the results in (optional, a valid SQL ORDER BY parameter).
     * @param string $fields A comma separated list of fields to return
     *   (optional, by default all fields are returned). The first field will be used as key for the
     *   array so must be a unique field such as 'id'.
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records in total (optional, required if $limitfrom is set).
     * @return array of objects indexed by first column
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_records_select($table, $select, array $params=null, $sort='', $fields='*', $limitfrom=0, $limitnum=0) {
        if ($select) {
            $select = "WHERE $select";
        }
        if ($sort) {
            $sort = " ORDER BY $sort";
        }
        return $this->get_records_sql("SELECT $fields FROM {" . $table . "} $select $sort", $params, $limitfrom, $limitnum);
    }

    /**
     * Get a number of records as an array of objects using a SQL statement.
     *
     * Return value is like {@link function get_records}.
     *
     * @param string $sql the SQL select query to execute. The first column of this SELECT statement
     *   must be a unique value (usually the 'id' field), as it will be used as the key of the
     *   returned array.
     * @param array $params array of sql parameters
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records in total (optional, required if $limitfrom is set).
     * @return array of objects indexed by first column
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public abstract function get_records_sql($sql, array $params=null, $limitfrom=0, $limitnum=0);

    /**
     * Get the first two columns from a number of records as an associative array where all the given conditions met.
     *
     * Arguments are like {@link function get_recordset}.
     *
     * If no errors occur the return value
     * is an associative whose keys come from the first field of each record,
     * and whose values are the corresponding second fields.
     * False is returned if an error occurs.
     *
     * @param string $table the table to query.
     * @param array $conditions optional array $fieldname=>requestedvalue with AND in between
     * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
     * @param string $fields a comma separated list of fields to return - the number of fields should be 2!
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return array an associative array
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_records_menu($table, array $conditions=null, $sort='', $fields='*', $limitfrom=0, $limitnum=0) {
        $menu = array();
        if ($records = $this->get_records($table, $conditions, $sort, $fields, $limitfrom, $limitnum)) {
            foreach ($records as $record) {
                $record = (array)$record;
                $key   = array_shift($record);
                $value = array_shift($record);
                $menu[$key] = $value;
            }
        }
        return $menu;
    }

    /**
     * Get the first two columns from a number of records as an associative array which match a particular WHERE clause.
     *
     * Arguments are like {@link function get_recordset_select}.
     * Return value is like {@link function get_records_menu}.
     *
     * @param string $table The database table to be checked against.
     * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
     * @param array $params array of sql parameters
     * @param string $sort Sort order (optional) - a valid SQL order parameter
     * @param string $fields A comma separated list of fields to be returned from the chosen table - the number of fields should be 2!
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return array an associative array
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_records_select_menu($table, $select, array $params=null, $sort='', $fields='*', $limitfrom=0, $limitnum=0) {
        $menu = array();
        if ($records = $this->get_records_select($table, $select, $params, $sort, $fields, $limitfrom, $limitnum)) {
            foreach ($records as $record) {
                $record = (array)$record;
                $key   = array_shift($record);
                $value = array_shift($record);
                $menu[$key] = $value;
            }
        }
        return $menu;
    }

    /**
     * Get the first two columns from a number of records as an associative array using a SQL statement.
     *
     * Arguments are like {@link function get_recordset_sql}.
     * Return value is like {@link function get_records_menu}.
     *
     * @param string $sql The SQL string you wish to be executed.
     * @param array $params array of sql parameters
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return array an associative array
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_records_sql_menu($sql, array $params=null, $limitfrom=0, $limitnum=0) {
        $menu = array();
        if ($records = $this->get_records_sql($sql, $params, $limitfrom, $limitnum)) {
            foreach ($records as $record) {
                $record = (array)$record;
                $key   = array_shift($record);
                $value = array_shift($record);
                $menu[$key] = $value;
            }
        }
        return $menu;
    }

    /**
     * Get a single database record as an object where all the given conditions met.
     *
     * @param string $table The table to select from.
     * @param array $conditions optional array $fieldname=>requestedvalue with AND in between
     * @param string $fields A comma separated list of fields to be returned from the chosen table.
     * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
     *                        IGNORE_MULTIPLE means return first, ignore multiple records found(not recommended);
     *                        MUST_EXIST means we will throw an exception if no record or multiple records found.
     *
     * @todo MDL-30407 MUST_EXIST option should not throw a dml_exception, it should throw a different exception as it's a requested check.
     * @return mixed a fieldset object containing the first matching record, false or exception if error not found depending on mode
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_record($table, array $conditions, $fields='*', $strictness=IGNORE_MISSING) {
        list($select, $params) = $this->where_clause($table, $conditions);
        return $this->get_record_select($table, $select, $params, $fields, $strictness);
    }

    /**
     * Get a single database record as an object which match a particular WHERE clause.
     *
     * @param string $table The database table to be checked against.
     * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
     * @param array $params array of sql parameters
     * @param string $fields A comma separated list of fields to be returned from the chosen table.
     * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
     *                        IGNORE_MULTIPLE means return first, ignore multiple records found(not recommended);
     *                        MUST_EXIST means throw exception if no record or multiple records found
     * @return stdClass|false a fieldset object containing the first matching record, false or exception if error not found depending on mode
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_record_select($table, $select, array $params=null, $fields='*', $strictness=IGNORE_MISSING) {
        if ($select) {
            $select = "WHERE $select";
        }
        try {
            return $this->get_record_sql("SELECT $fields FROM {" . $table . "} $select", $params, $strictness);
        } catch (dml_missing_record_exception $e) {
            // create new exception which will contain correct table name
            throw new dml_missing_record_exception($table, $e->sql, $e->params);
        }
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
        $strictness = (int)$strictness; // we support true/false for BC reasons too
        if ($strictness == IGNORE_MULTIPLE) {
            $count = 1;
        } else {
            $count = 0;
        }
        if (!$records = $this->get_records_sql($sql, $params, 0, $count)) {
            // not found
            if ($strictness == MUST_EXIST) {
                throw new dml_missing_record_exception('', $sql, $params);
            }
            return false;
        }

        if (count($records) > 1) {
            if ($strictness == MUST_EXIST) {
                throw new dml_multiple_records_exception($sql, $params);
            }
            debugging('Error: mdb->get_record() found more than one record!');
        }

        $return = reset($records);
        return $return;
    }

    /**
     * Get a single field value from a table record where all the given conditions met.
     *
     * @param string $table the table to query.
     * @param string $return the field to return the value of.
     * @param array $conditions optional array $fieldname=>requestedvalue with AND in between
     * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
     *                        IGNORE_MULTIPLE means return first, ignore multiple records found(not recommended);
     *                        MUST_EXIST means throw exception if no record or multiple records found
     * @return mixed the specified value false if not found
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_field($table, $return, array $conditions, $strictness=IGNORE_MISSING) {
        list($select, $params) = $this->where_clause($table, $conditions);
        return $this->get_field_select($table, $return, $select, $params, $strictness);
    }

    /**
     * Get a single field value from a table record which match a particular WHERE clause.
     *
     * @param string $table the table to query.
     * @param string $return the field to return the value of.
     * @param string $select A fragment of SQL to be used in a where clause returning one row with one column
     * @param array $params array of sql parameters
     * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
     *                        IGNORE_MULTIPLE means return first, ignore multiple records found(not recommended);
     *                        MUST_EXIST means throw exception if no record or multiple records found
     * @return mixed the specified value false if not found
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_field_select($table, $return, $select, array $params=null, $strictness=IGNORE_MISSING) {
        if ($select) {
            $select = "WHERE $select";
        }
        try {
            return $this->get_field_sql("SELECT $return FROM {" . $table . "} $select", $params, $strictness);
        } catch (dml_missing_record_exception $e) {
            // create new exception which will contain correct table name
            throw new dml_missing_record_exception($table, $e->sql, $e->params);
        }
    }

    /**
     * Get a single field value (first field) using a SQL statement.
     *
     * @param string $sql The SQL query returning one row with one column
     * @param array $params array of sql parameters
     * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
     *                        IGNORE_MULTIPLE means return first, ignore multiple records found(not recommended);
     *                        MUST_EXIST means throw exception if no record or multiple records found
     * @return mixed the specified value false if not found
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_field_sql($sql, array $params=null, $strictness=IGNORE_MISSING) {
        if (!$record = $this->get_record_sql($sql, $params, $strictness)) {
            return false;
        }

        $record = (array)$record;
        return reset($record); // first column
    }

    /**
     * Selects records and return values of chosen field as an array which match a particular WHERE clause.
     *
     * @param string $table the table to query.
     * @param string $return the field we are intered in
     * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
     * @param array $params array of sql parameters
     * @return array of values
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_fieldset_select($table, $return, $select, array $params=null) {
        if ($select) {
            $select = "WHERE $select";
        }
        return $this->get_fieldset_sql("SELECT $return FROM {" . $table . "} $select", $params);
    }

    /**
     * Selects records and return values (first field) as an array using a SQL statement.
     *
     * @param string $sql The SQL query
     * @param array $params array of sql parameters
     * @return array of values
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public abstract function get_fieldset_sql($sql, array $params=null);

    /**
     * Insert new record into database, as fast as possible, no safety checks, lobs not supported.
     * @param string $table name
     * @param mixed $params data record as object or array
     * @param bool $returnid Returns id of inserted record.
     * @param bool $bulk true means repeated inserts expected
     * @param bool $customsequence true if 'id' included in $params, disables $returnid
     * @return bool|int true or new id
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public abstract function insert_record_raw($table, $params, $returnid=true, $bulk=false, $customsequence=false);

    /**
     * Insert a record into a table and return the "id" field if required.
     *
     * Some conversions and safety checks are carried out. Lobs are supported.
     * If the return ID isn't required, then this just reports success as true/false.
     * $data is an object containing needed data
     * @param string $table The database table to be inserted into
     * @param object|array $dataobject A data object with values for one or more fields in the record
     * @param bool $returnid Should the id of the newly created record entry be returned? If this option is not requested then true/false is returned.
     * @param bool $bulk Set to true is multiple inserts are expected
     * @return bool|int true or new id
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public abstract function insert_record($table, $dataobject, $returnid=true, $bulk=false);

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
        if (!is_array($dataobjects) and !($dataobjects instanceof Traversable)) {
            throw new coding_exception('insert_records() passed non-traversable object');
        }

        $fields = null;
        // Note: override in driver if there is a faster way.
        foreach ($dataobjects as $dataobject) {
            if (!is_array($dataobject) and !is_object($dataobject)) {
                throw new coding_exception('insert_records() passed invalid record object');
            }
            $dataobject = (array)$dataobject;
            if ($fields === null) {
                $fields = array_keys($dataobject);
            } else if ($fields !== array_keys($dataobject)) {
                throw new coding_exception('All dataobjects in insert_records() must have the same structure!');
            }
            $this->insert_record($table, $dataobject, false);
        }
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
    public abstract function import_record($table, $dataobject);

    /**
     * Update record in database, as fast as possible, no safety checks, lobs not supported.
     * @param string $table name
     * @param mixed $params data record as object or array
     * @param bool $bulk True means repeated updates expected.
     * @return bool true
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public abstract function update_record_raw($table, $params, $bulk=false);

    /**
     * Update a record in a table
     *
     * $dataobject is an object containing needed data
     * Relies on $dataobject having a variable "id" to
     * specify the record to update
     *
     * @param string $table The database table to be checked against.
     * @param object $dataobject An object with contents equal to fieldname=>fieldvalue. Must have an entry for 'id' to map to the table specified.
     * @param bool $bulk True means repeated updates expected.
     * @return bool true
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public abstract function update_record($table, $dataobject, $bulk=false);

    /**
     * Set a single field in every table record where all the given conditions met.
     *
     * @param string $table The database table to be checked against.
     * @param string $newfield the field to set.
     * @param string $newvalue the value to set the field to.
     * @param array $conditions optional array $fieldname=>requestedvalue with AND in between
     * @return bool true
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function set_field($table, $newfield, $newvalue, array $conditions=null) {
        list($select, $params) = $this->where_clause($table, $conditions);
        return $this->set_field_select($table, $newfield, $newvalue, $select, $params);
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
    public abstract function set_field_select($table, $newfield, $newvalue, $select, array $params=null);


    /**
     * Count the records in a table where all the given conditions met.
     *
     * @param string $table The table to query.
     * @param array $conditions optional array $fieldname=>requestedvalue with AND in between
     * @return int The count of records returned from the specified criteria.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function count_records($table, array $conditions=null) {
        list($select, $params) = $this->where_clause($table, $conditions);
        return $this->count_records_select($table, $select, $params);
    }

    /**
     * Count the records in a table which match a particular WHERE clause.
     *
     * @param string $table The database table to be checked against.
     * @param string $select A fragment of SQL to be used in a WHERE clause in the SQL call.
     * @param array $params array of sql parameters
     * @param string $countitem The count string to be used in the SQL call. Default is COUNT('x').
     * @return int The count of records returned from the specified criteria.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function count_records_select($table, $select, array $params=null, $countitem="COUNT('x')") {
        if ($select) {
            $select = "WHERE $select";
        }
        return $this->count_records_sql("SELECT $countitem FROM {" . $table . "} $select", $params);
    }

    /**
     * Get the result of a SQL SELECT COUNT(...) query.
     *
     * Given a query that counts rows, return that count. (In fact,
     * given any query, return the first field of the first record
     * returned. However, this method should only be used for the
     * intended purpose.) If an error occurs, 0 is returned.
     *
     * @param string $sql The SQL string you wish to be executed.
     * @param array $params array of sql parameters
     * @return int the count
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function count_records_sql($sql, array $params=null) {
        $count = $this->get_field_sql($sql, $params);
        if ($count === false or !is_number($count) or $count < 0) {
            throw new coding_exception("count_records_sql() expects the first field to contain non-negative number from COUNT(), '$count' found instead.");
        }
        return (int)$count;
    }

    /**
     * Test whether a record exists in a table where all the given conditions met.
     *
     * @param string $table The table to check.
     * @param array $conditions optional array $fieldname=>requestedvalue with AND in between
     * @return bool true if a matching record exists, else false.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function record_exists($table, array $conditions) {
        list($select, $params) = $this->where_clause($table, $conditions);
        return $this->record_exists_select($table, $select, $params);
    }

    /**
     * Test whether any records exists in a table which match a particular WHERE clause.
     *
     * @param string $table The database table to be checked against.
     * @param string $select A fragment of SQL to be used in a WHERE clause in the SQL call.
     * @param array $params array of sql parameters
     * @return bool true if a matching record exists, else false.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function record_exists_select($table, $select, array $params=null) {
        if ($select) {
            $select = "WHERE $select";
        }
        return $this->record_exists_sql("SELECT 'x' FROM {" . $table . "} $select", $params);
    }

    /**
     * Test whether a SQL SELECT statement returns any records.
     *
     * This function returns true if the SQL statement executes
     * without any errors and returns at least one record.
     *
     * @param string $sql The SQL statement to execute.
     * @param array $params array of sql parameters
     * @return bool true if the SQL executes without errors and returns at least one record.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function record_exists_sql($sql, array $params=null) {
        $mrs = $this->get_recordset_sql($sql, $params, 0, 1);
        $return = $mrs->valid();
        $mrs->close();
        return $return;
    }

    /**
     * Delete the records from a table where all the given conditions met.
     * If conditions not specified, table is truncated.
     *
     * @param string $table the table to delete from.
     * @param array $conditions optional array $fieldname=>requestedvalue with AND in between
     * @return bool true.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function delete_records($table, array $conditions=null) {
        // truncate is drop/create (DDL), not transactional safe,
        // so we don't use the shortcut within them. MDL-29198
        if (is_null($conditions) && empty($this->transactions)) {
            return $this->execute("TRUNCATE TABLE {".$table."}");
        }
        list($select, $params) = $this->where_clause($table, $conditions);
        return $this->delete_records_select($table, $select, $params);
    }

    /**
     * Delete the records from a table where one field match one list of values.
     *
     * @param string $table the table to delete from.
     * @param string $field The field to search
     * @param array $values array of values
     * @return bool true.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function delete_records_list($table, $field, array $values) {
        list($select, $params) = $this->where_clause_list($field, $values);
        return $this->delete_records_select($table, $select, $params);
    }

    /**
     * Deletes records from a table using a subquery. The subquery should return a list of values
     * in a single column, which match one field from the table being deleted.
     *
     * The $alias parameter must be set to the name of the single column in your subquery result
     * (e.g. if the subquery is 'SELECT id FROM whatever', then it should be 'id'). This is not
     * needed on most databases, but MySQL requires it.
     *
     * (On database where the subquery is inefficient, it is implemented differently.)
     *
     * @param string $table Table to delete from
     * @param string $field Field in table to match
     * @param string $alias Name of single column in subquery e.g. 'id'
     * @param string $subquery Subquery that will return values of the field to delete
     * @param array $params Parameters for subquery
     * @throws dml_exception If there is any error
     * @since Moodle 3.9.3
     */
    public function delete_records_subquery(string $table, string $field, string $alias,
            string $subquery, array $params = []): void {
        $this->delete_records_select($table, $field . ' IN (' . $subquery . ')', $params);
    }

    /**
     * Delete one or more records from a table which match a particular WHERE clause.
     *
     * @param string $table The database table to be checked against.
     * @param string $select A fragment of SQL to be used in a where clause in the SQL call (used to define the selection criteria).
     * @param array $params array of sql parameters
     * @return bool true.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public abstract function delete_records_select($table, $select, array $params=null);

    /**
     * Returns the FROM clause required by some DBs in all SELECT statements.
     *
     * To be used in queries not having FROM clause to provide cross_db
     * Most DBs don't need it, hence the default is ''
     * @return string
     */
    public function sql_null_from_clause() {
        return '';
    }

    /**
     * Returns the SQL text to be used in order to perform one bitwise AND operation
     * between 2 integers.
     *
     * NOTE: The SQL result is a number and can not be used directly in
     *       SQL condition, please compare it to some number to get a bool!!
     *
     * @param int $int1 First integer in the operation.
     * @param int $int2 Second integer in the operation.
     * @return string The piece of SQL code to be used in your statement.
     */
    public function sql_bitand($int1, $int2) {
        return '((' . $int1 . ') & (' . $int2 . '))';
    }

    /**
     * Returns the SQL text to be used in order to perform one bitwise NOT operation
     * with 1 integer.
     *
     * @param int $int1 The operand integer in the operation.
     * @return string The piece of SQL code to be used in your statement.
     */
    public function sql_bitnot($int1) {
        return '(~(' . $int1 . '))';
    }

    /**
     * Returns the SQL text to be used in order to perform one bitwise OR operation
     * between 2 integers.
     *
     * NOTE: The SQL result is a number and can not be used directly in
     *       SQL condition, please compare it to some number to get a bool!!
     *
     * @param int $int1 The first operand integer in the operation.
     * @param int $int2 The second operand integer in the operation.
     * @return string The piece of SQL code to be used in your statement.
     */
    public function sql_bitor($int1, $int2) {
        return '((' . $int1 . ') | (' . $int2 . '))';
    }

    /**
     * Returns the SQL text to be used in order to perform one bitwise XOR operation
     * between 2 integers.
     *
     * NOTE: The SQL result is a number and can not be used directly in
     *       SQL condition, please compare it to some number to get a bool!!
     *
     * @param int $int1 The first operand integer in the operation.
     * @param int $int2 The second operand integer in the operation.
     * @return string The piece of SQL code to be used in your statement.
     */
    public function sql_bitxor($int1, $int2) {
        return '((' . $int1 . ') ^ (' . $int2 . '))';
    }

    /**
     * Returns the SQL text to be used in order to perform module '%'
     * operation - remainder after division
     *
     * @param int $int1 The first operand integer in the operation.
     * @param int $int2 The second operand integer in the operation.
     * @return string The piece of SQL code to be used in your statement.
     */
    public function sql_modulo($int1, $int2) {
        return '((' . $int1 . ') % (' . $int2 . '))';
    }

    /**
     * Returns the cross db correct CEIL (ceiling) expression applied to fieldname.
     * note: Most DBs use CEIL(), hence it's the default here.
     *
     * @param string $fieldname The field (or expression) we are going to ceil.
     * @return string The piece of SQL code to be used in your ceiling statement.
     */
    public function sql_ceil($fieldname) {
        return ' CEIL(' . $fieldname . ')';
    }

    /**
     * Returns the SQL to be used in order to CAST one CHAR column to INTEGER.
     *
     * Be aware that the CHAR column you're trying to cast contains really
     * int values or the RDBMS will throw an error!
     *
     * @param string $fieldname The name of the field to be casted.
     * @param bool $text Specifies if the original column is one TEXT (CLOB) column (true). Defaults to false.
     * @return string The piece of SQL code to be used in your statement.
     */
    public function sql_cast_char2int($fieldname, $text=false) {
        return ' ' . $fieldname . ' ';
    }

    /**
     * Returns the SQL to be used in order to CAST one CHAR column to REAL number.
     *
     * Be aware that the CHAR column you're trying to cast contains really
     * numbers or the RDBMS will throw an error!
     *
     * @param string $fieldname The name of the field to be casted.
     * @param bool $text Specifies if the original column is one TEXT (CLOB) column (true). Defaults to false.
     * @return string The piece of SQL code to be used in your statement.
     */
    public function sql_cast_char2real($fieldname, $text=false) {
        return ' ' . $fieldname . ' ';
    }

    /**
     * Returns the SQL to be used in order to an UNSIGNED INTEGER column to SIGNED.
     *
     * (Only MySQL needs this. MySQL things that 1 * -1 = 18446744073709551615
     * if the 1 comes from an unsigned column).
     *
     * @deprecated since 2.3
     * @param string $fieldname The name of the field to be cast
     * @return string The piece of SQL code to be used in your statement.
     */
    public function sql_cast_2signed($fieldname) {
        return ' ' . $fieldname . ' ';
    }

    /**
     * Returns the SQL text to be used to compare one TEXT (clob) column with
     * one varchar column, because some RDBMS doesn't support such direct
     * comparisons.
     *
     * @param string $fieldname The name of the TEXT field we need to order by
     * @param int $numchars Number of chars to use for the ordering (defaults to 32).
     * @return string The piece of SQL code to be used in your statement.
     */
    public function sql_compare_text($fieldname, $numchars=32) {
        return $this->sql_order_by_text($fieldname, $numchars);
    }

    /**
     * Returns an equal (=) or not equal (<>) part of a query.
     *
     * Note the use of this method may lead to slower queries (full scans) so
     * use it only when needed and against already reduced data sets.
     *
     * @since Moodle 3.2
     *
     * @param string $fieldname Usually the name of the table column.
     * @param string $param Usually the bound query parameter (?, :named).
     * @param bool $casesensitive Use case sensitive search when set to true (default).
     * @param bool $accentsensitive Use accent sensitive search when set to true (default). (not all databases support accent insensitive)
     * @param bool $notequal True means not equal (<>)
     * @return string The SQL code fragment.
     */
    public function sql_equal($fieldname, $param, $casesensitive = true, $accentsensitive = true, $notequal = false) {
        // Note that, by default, it's assumed that the correct sql equal operations are
        // case sensitive. Only databases not observing this behavior must override the method.
        // Also, accent sensitiveness only will be handled by databases supporting it.
        $equalop = $notequal ? '<>' : '=';
        if ($casesensitive) {
            return "$fieldname $equalop $param";
        } else {
            return "LOWER($fieldname) $equalop LOWER($param)";
        }
    }

    /**
     * Returns 'LIKE' part of a query.
     *
     * @param string $fieldname Usually the name of the table column.
     * @param string $param Usually the bound query parameter (?, :named).
     * @param bool $casesensitive Use case sensitive search when set to true (default).
     * @param bool $accentsensitive Use accent sensitive search when set to true (default). (not all databases support accent insensitive)
     * @param bool $notlike True means "NOT LIKE".
     * @param string $escapechar The escape char for '%' and '_'.
     * @return string The SQL code fragment.
     */
    public function sql_like($fieldname, $param, $casesensitive = true, $accentsensitive = true, $notlike = false, $escapechar = '\\') {
        if (strpos($param, '%') !== false) {
            debugging('Potential SQL injection detected, sql_like() expects bound parameters (? or :named)');
        }
        $LIKE = $notlike ? 'NOT LIKE' : 'LIKE';
        // by default ignore any sensitiveness - each database does it in a different way
        return "$fieldname $LIKE $param ESCAPE '$escapechar'";
    }

    /**
     * Escape sql LIKE special characters like '_' or '%'.
     * @param string $text The string containing characters needing escaping.
     * @param string $escapechar The desired escape character, defaults to '\\'.
     * @return string The escaped sql LIKE string.
     */
    public function sql_like_escape($text, $escapechar = '\\') {
        $text = str_replace('_', $escapechar.'_', $text);
        $text = str_replace('%', $escapechar.'%', $text);
        return $text;
    }

    /**
     * Returns the proper SQL to do CONCAT between the elements(fieldnames) passed.
     *
     * This function accepts variable number of string parameters.
     * All strings/fieldnames will used in the SQL concatenate statement generated.
     *
     * @return string The SQL to concatenate strings passed in.
     * @uses func_get_args()  and thus parameters are unlimited OPTIONAL number of additional field names.
     */
    public abstract function sql_concat();

    /**
     * Returns the proper SQL to do CONCAT between the elements passed
     * with a given separator
     *
     * @param string $separator The separator desired for the SQL concatenating $elements.
     * @param array  $elements The array of strings to be concatenated.
     * @return string The SQL to concatenate the strings.
     */
    public abstract function sql_concat_join($separator="' '", $elements=array());

    /**
     * Returns the proper SQL (for the dbms in use) to concatenate $firstname and $lastname
     *
     * @todo MDL-31233 This may not be needed here.
     *
     * @param string $first User's first name (default:'firstname').
     * @param string $last User's last name (default:'lastname').
     * @return string The SQL to concatenate strings.
     */
    function sql_fullname($first='firstname', $last='lastname') {
        return $this->sql_concat($first, "' '", $last);
    }

    /**
     * Returns the SQL text to be used to order by one TEXT (clob) column, because
     * some RDBMS doesn't support direct ordering of such fields.
     *
     * Note that the use or queries being ordered by TEXT columns must be minimised,
     * because it's really slooooooow.
     *
     * @param string $fieldname The name of the TEXT field we need to order by.
     * @param int $numchars The number of chars to use for the ordering (defaults to 32).
     * @return string The piece of SQL code to be used in your statement.
     */
    public function sql_order_by_text($fieldname, $numchars=32) {
        return $fieldname;
    }

    /**
     * Returns the SQL text to be used to calculate the length in characters of one expression.
     * @param string $fieldname The fieldname/expression to calculate its length in characters.
     * @return string the piece of SQL code to be used in the statement.
     */
    public function sql_length($fieldname) {
        return ' LENGTH(' . $fieldname . ')';
    }

    /**
     * Returns the proper substr() SQL text used to extract substrings from DB
     * NOTE: this was originally returning only function name
     *
     * @param string $expr Some string field, no aggregates.
     * @param mixed $start Integer or expression evaluating to integer (1 based value; first char has index 1)
     * @param mixed $length Optional integer or expression evaluating to integer.
     * @return string The sql substring extraction fragment.
     */
    public function sql_substr($expr, $start, $length=false) {
        if (count(func_get_args()) < 2) {
            throw new coding_exception('moodle_database::sql_substr() requires at least two parameters', 'Originally this function was only returning name of SQL substring function, it now requires all parameters.');
        }
        if ($length === false) {
            return "SUBSTR($expr, $start)";
        } else {
            return "SUBSTR($expr, $start, $length)";
        }
    }

    /**
     * Returns the SQL for returning searching one string for the location of another.
     *
     * Note, there is no guarantee which order $needle, $haystack will be in
     * the resulting SQL so when using this method, and both arguments contain
     * placeholders, you should use named placeholders.
     *
     * @param string $needle the SQL expression that will be searched for.
     * @param string $haystack the SQL expression that will be searched in.
     * @return string The required searching SQL part.
     */
    public function sql_position($needle, $haystack) {
        // Implementation using standard SQL.
        return "POSITION(($needle) IN ($haystack))";
    }

    /**
     * This used to return empty string replacement character.
     *
     * @deprecated use bound parameter with empty string instead
     *
     * @return string An empty string.
     */
    function sql_empty() {
        debugging("sql_empty() is deprecated, please use empty string '' as sql parameter value instead", DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Returns the proper SQL to know if one field is empty.
     *
     * Note that the function behavior strongly relies on the
     * parameters passed describing the field so, please,  be accurate
     * when specifying them.
     *
     * Also, note that this function is not suitable to look for
     * fields having NULL contents at all. It's all for empty values!
     *
     * This function should be applied in all the places where conditions of
     * the type:
     *
     *     ... AND fieldname = '';
     *
     * are being used. Final result for text fields should be:
     *
     *     ... AND ' . sql_isempty('tablename', 'fieldname', true/false, true);
     *
     * and for varchar fields result should be:
     *
     *    ... AND fieldname = :empty; "; $params['empty'] = '';
     *
     * (see parameters description below)
     *
     * @param string $tablename Name of the table (without prefix). Not used for now but can be
     *                          necessary in the future if we want to use some introspection using
     *                          meta information against the DB. /// TODO ///
     * @param string $fieldname Name of the field we are going to check
     * @param bool $nullablefield For specifying if the field is nullable (true) or no (false) in the DB.
     * @param bool $textfield For specifying if it is a text (also called clob) field (true) or a varchar one (false)
     * @return string the sql code to be added to check for empty values
     */
    public function sql_isempty($tablename, $fieldname, $nullablefield, $textfield) {
        return " ($fieldname = '') ";
    }

    /**
     * Returns the proper SQL to know if one field is not empty.
     *
     * Note that the function behavior strongly relies on the
     * parameters passed describing the field so, please,  be accurate
     * when specifying them.
     *
     * This function should be applied in all the places where conditions of
     * the type:
     *
     *     ... AND fieldname != '';
     *
     * are being used. Final result for text fields should be:
     *
     *     ... AND ' . sql_isnotempty('tablename', 'fieldname', true/false, true/false);
     *
     * and for varchar fields result should be:
     *
     *    ... AND fieldname != :empty; "; $params['empty'] = '';
     *
     * (see parameters description below)
     *
     * @param string $tablename Name of the table (without prefix). This is not used for now but can be
     *                          necessary in the future if we want to use some introspection using
     *                          meta information against the DB.
     * @param string $fieldname The name of the field we are going to check.
     * @param bool $nullablefield Specifies if the field is nullable (true) or not (false) in the DB.
     * @param bool $textfield Specifies if it is a text (also called clob) field (true) or a varchar one (false).
     * @return string The sql code to be added to check for non empty values.
     */
    public function sql_isnotempty($tablename, $fieldname, $nullablefield, $textfield) {
        return ' ( NOT ' . $this->sql_isempty($tablename, $fieldname, $nullablefield, $textfield) . ') ';
    }

    /**
     * Returns true if this database driver supports regex syntax when searching.
     * @return bool True if supported.
     */
    public function sql_regex_supported() {
        return false;
    }

    /**
     * Returns the driver specific syntax (SQL part) for matching regex positively or negatively (inverted matching).
     * Eg: 'REGEXP':'NOT REGEXP' or '~*' : '!~*'
     *
     * @param bool $positivematch
     * @param bool $casesensitive
     * @return string or empty if not supported
     */
    public function sql_regex($positivematch = true, $casesensitive = false) {
        return '';
    }

    /**
     * Returns the SQL that allows to find intersection of two or more queries
     *
     * @since Moodle 2.8
     *
     * @param array $selects array of SQL select queries, each of them only returns fields with the names from $fields
     * @param string $fields comma-separated list of fields (used only by some DB engines)
     * @return string SQL query that will return only values that are present in each of selects
     */
    public function sql_intersect($selects, $fields) {
        if (!count($selects)) {
            throw new coding_exception('sql_intersect() requires at least one element in $selects');
        } else if (count($selects) == 1) {
            return $selects[0];
        }
        static $aliascnt = 0;
        $rv = '('.$selects[0].')';
        for ($i = 1; $i < count($selects); $i++) {
            $rv .= " INTERSECT (".$selects[$i].')';
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
        return false;
    }

    /**
     * Replace given text in all rows of column.
     *
     * @since Moodle 2.6.1
     * @param string $table name of the table
     * @param database_column_info $column
     * @param string $search
     * @param string $replace
     */
    public function replace_all_text($table, database_column_info $column, $search, $replace) {
        if (!$this->replace_all_text_supported()) {
            return;
        }

        // NOTE: override this methods if following standard compliant SQL
        //       does not work for your driver.

        // Enclose the column name by the proper quotes if it's a reserved word.
        $columnname = $this->get_manager()->generator->getEncQuoted($column->name);

        $searchsql = $this->sql_like($columnname, '?');
        $searchparam = '%'.$this->sql_like_escape($search).'%';

        $sql = "UPDATE {".$table."}
                       SET $columnname = REPLACE($columnname, ?, ?)
                     WHERE $searchsql";

        if ($column->meta_type === 'X') {
            $this->execute($sql, array($search, $replace, $searchparam));

        } else if ($column->meta_type === 'C') {
            if (core_text::strlen($search) < core_text::strlen($replace)) {
                $colsize = $column->max_length;
                $sql = "UPDATE {".$table."}
                       SET $columnname = " . $this->sql_substr("REPLACE(" . $columnname . ", ?, ?)", 1, $colsize) . "
                     WHERE $searchsql";
            }
            $this->execute($sql, array($search, $replace, $searchparam));
        }
    }

    /**
     * Analyze the data in temporary tables to force statistics collection after bulk data loads.
     *
     * @return void
     */
    public function update_temp_table_stats() {
        $this->temptables->update_stats();
    }

    /**
     * Checks and returns true if transactions are supported.
     *
     * It is not responsible to run productions servers
     * on databases without transaction support ;-)
     *
     * Override in driver if needed.
     *
     * @return bool
     */
    protected function transactions_supported() {
        // protected for now, this might be changed to public if really necessary
        return true;
    }

    /**
     * Returns true if a transaction is in progress.
     * @return bool
     */
    public function is_transaction_started() {
        return !empty($this->transactions);
    }

    /**
     * This is a test that throws an exception if transaction in progress.
     * This test does not force rollback of active transactions.
     * @return void
     * @throws dml_transaction_exception if stansaction active
     */
    public function transactions_forbidden() {
        if ($this->is_transaction_started()) {
            throw new dml_transaction_exception('This code can not be excecuted in transaction');
        }
    }

    /**
     * On DBs that support it, switch to transaction mode and begin a transaction
     * you'll need to ensure you call allow_commit() on the returned object
     * or your changes *will* be lost.
     *
     * this is _very_ useful for massive updates
     *
     * Delegated database transactions can be nested, but only one actual database
     * transaction is used for the outer-most delegated transaction. This method
     * returns a transaction object which you should keep until the end of the
     * delegated transaction. The actual database transaction will
     * only be committed if all the nested delegated transactions commit
     * successfully. If any part of the transaction rolls back then the whole
     * thing is rolled back.
     *
     * @return moodle_transaction
     */
    public function start_delegated_transaction() {
        $transaction = new moodle_transaction($this);
        $this->transactions[] = $transaction;
        if (count($this->transactions) == 1) {
            $this->begin_transaction();
        }
        return $transaction;
    }

    /**
     * Driver specific start of real database transaction,
     * this can not be used directly in code.
     * @return void
     */
    protected abstract function begin_transaction();

    /**
     * Indicates delegated transaction finished successfully.
     * The real database transaction is committed only if
     * all delegated transactions committed.
     * @param moodle_transaction $transaction The transaction to commit
     * @return void
     * @throws dml_transaction_exception Creates and throws transaction related exceptions.
     */
    public function commit_delegated_transaction(moodle_transaction $transaction) {
        if ($transaction->is_disposed()) {
            throw new dml_transaction_exception('Transactions already disposed', $transaction);
        }
        // mark as disposed so that it can not be used again
        $transaction->dispose();

        if (empty($this->transactions)) {
            throw new dml_transaction_exception('Transaction not started', $transaction);
        }

        if ($this->force_rollback) {
            throw new dml_transaction_exception('Tried to commit transaction after lower level rollback', $transaction);
        }

        if ($transaction !== $this->transactions[count($this->transactions) - 1]) {
            // one incorrect commit at any level rollbacks everything
            $this->force_rollback = true;
            throw new dml_transaction_exception('Invalid transaction commit attempt', $transaction);
        }

        if (count($this->transactions) == 1) {
            // only commit the top most level
            $this->commit_transaction();
        }
        array_pop($this->transactions);

        if (empty($this->transactions)) {
            \core\event\manager::database_transaction_commited();
            \core\message\manager::database_transaction_commited();
        }
    }

    /**
     * Driver specific commit of real database transaction,
     * this can not be used directly in code.
     * @return void
     */
    protected abstract function commit_transaction();

    /**
     * Call when delegated transaction failed, this rolls back
     * all delegated transactions up to the top most level.
     *
     * In many cases you do not need to call this method manually,
     * because all open delegated transactions are rolled back
     * automatically if exceptions not caught.
     *
     * @param moodle_transaction $transaction An instance of a moodle_transaction.
     * @param Exception|Throwable $e The related exception/throwable to this transaction rollback.
     * @return void This does not return, instead the exception passed in will be rethrown.
     */
    public function rollback_delegated_transaction(moodle_transaction $transaction, $e) {
        if (!($e instanceof Exception) && !($e instanceof Throwable)) {
            // PHP7 - we catch Throwables in phpunit but can't use that as the type hint in PHP5.
            $e = new \coding_exception("Must be given an Exception or Throwable object!");
        }
        if ($transaction->is_disposed()) {
            throw new dml_transaction_exception('Transactions already disposed', $transaction);
        }
        // mark as disposed so that it can not be used again
        $transaction->dispose();

        // one rollback at any level rollbacks everything
        $this->force_rollback = true;

        if (empty($this->transactions) or $transaction !== $this->transactions[count($this->transactions) - 1]) {
            // this may or may not be a coding problem, better just rethrow the exception,
            // because we do not want to loose the original $e
            throw $e;
        }

        if (count($this->transactions) == 1) {
            // only rollback the top most level
            $this->rollback_transaction();
        }
        array_pop($this->transactions);
        if (empty($this->transactions)) {
            // finally top most level rolled back
            $this->force_rollback = false;
            \core\event\manager::database_transaction_rolledback();
            \core\message\manager::database_transaction_rolledback();
        }
        throw $e;
    }

    /**
     * Driver specific abort of real database transaction,
     * this can not be used directly in code.
     * @return void
     */
    protected abstract function rollback_transaction();

    /**
     * Force rollback of all delegated transaction.
     * Does not throw any exceptions and does not log anything.
     *
     * This method should be used only from default exception handlers and other
     * core code.
     *
     * @return void
     */
    public function force_transaction_rollback() {
        if ($this->transactions) {
            try {
                $this->rollback_transaction();
            } catch (dml_exception $e) {
                // ignore any sql errors here, the connection might be broken
            }
        }

        // now enable transactions again
        $this->transactions = array();
        $this->force_rollback = false;

        \core\event\manager::database_transaction_rolledback();
        \core\message\manager::database_transaction_rolledback();
    }

    /**
     * Is session lock supported in this driver?
     * @return bool
     */
    public function session_lock_supported() {
        return false;
    }

    /**
     * Obtains the session lock.
     * @param int $rowid The id of the row with session record.
     * @param int $timeout The maximum allowed time to wait for the lock in seconds.
     * @return void
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_session_lock($rowid, $timeout) {
        $this->used_for_db_sessions = true;
    }

    /**
     * Releases the session lock.
     * @param int $rowid The id of the row with session record.
     * @return void
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function release_session_lock($rowid) {
    }

    /**
     * Returns the number of reads done by this database.
     * @return int Number of reads.
     */
    public function perf_get_reads() {
        return $this->reads;
    }

    /**
     * Returns whether we want to connect to slave database for read queries.
     * @return bool Want read only connection
     */
    public function want_read_slave(): bool {
        return false;
    }

    /**
     * Returns the number of reads before first write done by this database.
     * @return int Number of reads.
     */
    public function perf_get_reads_slave(): int {
        return 0;
    }

    /**
     * Returns the number of writes done by this database.
     * @return int Number of writes.
     */
    public function perf_get_writes() {
        return $this->writes;
    }

    /**
     * Returns the number of queries done by this database.
     * @return int Number of queries.
     */
    public function perf_get_queries() {
        return $this->writes + $this->reads;
    }

    /**
     * Time waiting for the database engine to finish running all queries.
     * @return float Number of seconds with microseconds
     */
    public function perf_get_queries_time() {
        return $this->queriestime;
    }

    /**
     * Whether the database is able to support full-text search or not.
     *
     * @return bool
     */
    public function is_fulltext_search_supported() {
        // No support unless specified.
        return false;
    }
}
