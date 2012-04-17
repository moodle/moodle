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
 * @package    core
 * @subpackage dml
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/dml/database_column_info.php');
require_once($CFG->libdir.'/dml/moodle_recordset.php');
require_once($CFG->libdir.'/dml/moodle_transaction.php');

/// GLOBAL CONSTANTS /////////////////////////////////////////////////////////

/** Bitmask, indicates :name type parameters are supported by db backend. */
define('SQL_PARAMS_NAMED', 1);

/** Bitmask, indicates ? type parameters are supported by db backend. */
define('SQL_PARAMS_QM', 2);

/** Bitmask, indicates $1, $2, ... type parameters are supported by db backend. */
define('SQL_PARAMS_DOLLAR', 4);


/** Normal select query, reading only */
define('SQL_QUERY_SELECT', 1);

/** Insert select query, writing */
define('SQL_QUERY_INSERT', 2);

/** Update select query, writing */
define('SQL_QUERY_UPDATE', 3);

/** Query changing db structure, writing */
define('SQL_QUERY_STRUCTURE', 4);

/** Auxiliary query done by driver, setting connection config, getting table info, etc. */
define('SQL_QUERY_AUX', 5);

/**
 * Abstract class representing moodle database interface.
 */
abstract class moodle_database {

    /** @var database_manager db manager which allows db structure modifications */
    protected $database_manager;
    /** @var moodle_temptables temptables manager to provide cross-db support for temp tables */
    protected $temptables;
    /** @var array cache of column info */
    protected $columns = array(); // I wish we had a shared memory cache for this :-(
    /** @var array cache of table info */
    protected $tables  = null;

    // db connection options
    /** @var string db host name */
    protected $dbhost;
    /** @var string db host user */
    protected $dbuser;
    /** @var string db host password */
    protected $dbpass;
    /** @var string db name */
    protected $dbname;
    /** @var string table prefix */
    protected $prefix;

    /** @var array Database or driver specific options, such as sockets or TCPIP db connections */
    protected $dboptions;

    /** @var bool true means non-moodle external database used.*/
    protected $external;

    /** @var int The database reads (performance counter).*/
    protected $reads = 0;
    /** @var int The database writes (performance counter).*/
    protected $writes = 0;

    /** @var int Debug level */
    protected $debug  = 0;

    /** @var string last query sql */
    protected $last_sql;
    /** @var array last query parameters */
    protected $last_params;
    /** @var int last query type */
    protected $last_type;
    /** @var string last extra info */
    protected $last_extrainfo;
    /** @var float last time in seconds with millisecond precision */
    protected $last_time;
    /** @var bool flag indicating logging of query in progress, prevents infinite loops */
    private $loggingquery = false;

    /** @var bool true if db used for db sessions */
    protected $used_for_db_sessions = false;

    /** @var array open transactions */
    private $transactions = array();
    /** @var bool force rollback of all current transactions */
    private $force_rollback = false;

    /** @var int internal temporary variable */
    private $fix_sql_params_i;
    /** @var int internal temporary variable used by {@link get_in_or_equal()}. */
    private $inorequaluniqueindex = 1; // guarantees unique parameters in each request

    /**
     * Constructor - instantiates the database, specifying if it's external (connect to other systems) or no (Moodle DB)
     *              note this has effect to decide if prefix checks must be performed or no
     * @param bool true means external database used
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
     * Detects if all needed PHP stuff installed.
     * Note: can be used before connect()
     * @return mixed true if ok, string if something
     */
    public abstract function driver_installed();

    /**
     * Returns database table prefix
     * Note: can be used before connect()
     * @return string database table prefix
     */
    public function get_prefix() {
        return $this->prefix;
    }

    /**
     * Loads and returns a database instance with the specified type and library.
     * @param string $type database type of the driver (mysqli, pgsql, mssql, sqldrv, oci, etc.)
     * @param string $library database library of the driver (native, pdo, etc.)
     * @param boolean $external true if this is an external database
     * @return moodle_database driver object or null if error
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
     * Returns database family type - describes SQL dialect
     * Note: can be used before connect()
     * @return string db family name (mysql, postgres, mssql, oracle, etc.)
     */
    public abstract function get_dbfamily();

    /**
     * Returns more specific database driver type
     * Note: can be used before connect()
     * @return string db type mysqli, pgsql, oci, mssql, sqlsrv
     */
    protected abstract function get_dbtype();

    /**
     * Returns general database library name
     * Note: can be used before connect()
     * @return string db type pdo, native
     */
    protected abstract function get_dblibrary();

    /**
     * Returns localised database type name
     * Note: can be used before connect()
     * @return string
     */
    public abstract function get_name();

    /**
     * Returns localised database configuration help.
     * Note: can be used before connect()
     * @return string
     */
    public abstract function get_configuration_help();

    /**
     * Returns localised database description
     * Note: can be used before connect()
     * @return string
     */
    public abstract function get_configuration_hints();

    /**
     * Returns db related part of config.php
     * @return object
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
     * Connect to db
     * Must be called before other methods.
     * @param string $dbhost
     * @param string $dbuser
     * @param string $dbpass
     * @param string $dbname
     * @param mixed $prefix string means moodle db prefix, false used for external databases where prefix not used
     * @param array $dboptions driver specific options
     * @return bool true
     * @throws dml_connection_exception if error
     */
    public abstract function connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, array $dboptions=null);

    /**
     * Store various database settings
     * @param string $dbhost
     * @param string $dbuser
     * @param string $dbpass
     * @param string $dbname
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
     * Attempt to create the database
     * @param string $dbhost
     * @param string $dbuser
     * @param string $dbpass
     * @param string $dbname
     *
     * @return bool success
     */
    public function create_database($dbhost, $dbuser, $dbpass, $dbname, array $dboptions=null) {
        return false;
    }

    /**
     * Close database connection and release all resources
     * and memory (especially circular memory references).
     * Do NOT use connect() again, create a new instance if needed.
     * @return void
     */
    public function dispose() {
        if ($this->transactions) {
            // this should not happen, it usually indicates wrong catching of exceptions,
            // because all transactions should be finished manually or in default exception handler.
            // unfortunately we can not access global $CFG any more and can not print debug,
            // the diagnostic info should be printed in footer instead
            $lowesttransaction = end($this->transactions);
            $backtrace = $lowesttransaction->get_backtrace();

            error_log('Potential coding error - active database transaction detected when disposing database:'."\n".format_backtrace($backtrace, true));
            $this->force_transaction_rollback();
        }
        if ($this->used_for_db_sessions) {
            // this is needed because we need to save session to db before closing it
            session_get_instance()->write_close();
            $this->used_for_db_sessions = false;
        }
        if ($this->temptables) {
            $this->temptables->dispose();
            $this->temptables = null;
        }
        if ($this->database_manager) {
            $this->database_manager->dispose();
            $this->database_manager = null;
        }
        $this->columns = array();
        $this->tables  = null;
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
        }

        $this->print_debug($sql, $params);
    }

    /**
     * Called immediately after each db query.
     * @param mixed db specific result
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
            return;
        }

        // remember current info, log queries may alter it
        $type   = $this->last_type;
        $sql    = $this->last_sql;
        $params = $this->last_params;
        $time   = microtime(true) - $this->last_time;
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
     * Log last database query if requested
     * @param mixed string error or false if not error
     * @return void
     */
    public function query_log($error=false) {
        $logall    = !empty($this->dboptions['logall']);
        $logslow   = !empty($this->dboptions['logslow']) ? $this->dboptions['logslow'] : false;
        $logerrors = !empty($this->dboptions['logerrors']);
        $iserror   = ($error !== false);

        $time = microtime(true) - $this->last_time;

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
     * Returns database server info array
     * @return array
     */
    public abstract function get_server_info();

    /**
     * Returns supported query parameter types
     * @return int bitmask
     */
    protected abstract function allowed_param_types();

    /**
     * Returns last error reported by database engine.
     * @return string error message
     */
    public abstract function get_last_error();

    /**
     * Print sql debug info
     * @param string $sql query which caused problems
     * @param array $params optional query parameters
     * @param mixed $obj optional library specific object
     * @return void
     */
    protected function print_debug($sql, array $params=null, $obj=null) {
        if (!$this->get_debug()) {
            return;
        }
        if (CLI_SCRIPT) {
            echo "--------------------------------\n";
            echo $sql."\n";
            if (!is_null($params)) {
                echo "[".var_export($params, true)."]\n";
            }
            echo "--------------------------------\n";
        } else {
            echo "<hr />\n";
            echo s($sql)."\n";
            if (!is_null($params)) {
                echo "[".s(var_export($params, true))."]\n";
            }
            echo "<hr />\n";
        }
    }

    /**
     * Returns SQL WHERE conditions.
     * @param string $table - the table name that these conditions will be validated against.
     * @param array conditions - must not contain numeric indexes
     * @return array sql part and params
     */
    protected function where_clause($table, array $conditions=null) {
        $allowed_types = $this->allowed_param_types();
        if (empty($conditions)) {
            return array('', array());
        }
        $where = array();
        $params = array();

        if (debugging()) {
            $columns = $this->get_columns($table);
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
     * Returns SQL WHERE conditions for the ..._list methods.
     *
     * @param string $field the name of a field.
     * @param array $values the values field might take.
     * @return array sql part and params
     */
    protected function where_clause_list($field, array $values) {
        $params = array();
        $select = array();
        $values = (array)$values;
        foreach ($values as $value) {
            if (is_bool($value)) {
                $value = (int)$value;
            }
            if (is_null($value)) {
                $select[] = "$field IS NULL";
            } else {
                $select[] = "$field = ?";
                $params[] = $value;
            }
        }
        $select = implode(" OR ", $select);
        return array($select, $params);
    }

    /**
     * Constructs IN() or = sql fragment
     * @param mixed $items single or array of values
     * @param int $type bound param type SQL_PARAMS_QM or SQL_PARAMS_NAMED
     * @param string $prefix named parameter placeholder prefix (unique counter value is appended to each parameter name)
     * @param bool $equal true means equal, false not equal
     * @param mixed $onemptyitems defines the behavior when the array of items is empty. Defaults to false,
     *              meaning throw exceptions. Other values will become part of the returned SQL fragment.
     * @return array - $sql and $params
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
     * Converts short table name {tablename} to real table name
     * @param string sql
     * @return string sql
     */
    protected function fix_table_names($sql) {
        return preg_replace('/\{([a-z][a-z0-9_]*)\}/', $this->prefix.'$1', $sql);
    }

    /** Internal function */
    private function _fix_sql_params_dollar_callback($match) {
        $this->fix_sql_params_i++;
        return "\$".$this->fix_sql_params_i;
    }

    /**
     * Normalizes sql query parameters and verifies parameters.
     * @param string $sql query or part of it
     * @param array $params query parameters
     * @return array (sql, params, type of params)
     */
    public function fix_sql_params($sql, array $params=null) {
        $params = (array)$params; // mke null array if needed
        $allowed_types = $this->allowed_param_types();

        // convert table names
        $sql = $this->fix_table_names($sql);

        // cast booleans to 1/0 int
        foreach ($params as $key => $value) {
            $params[$key] = is_bool($value) ? (int)$value : $value;
        }

        // NICOLAS C: Fixed regexp for negative backwards lookahead of double colons. Thanks for Sam Marshall's help
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
     * Return tables in database WITHOUT current prefix
     * @return array of table names in lowercase and without prefix
     */
    public abstract function get_tables($usecache=true);

    /**
     * Return table indexes - everything lowercased
     * @return array of arrays
     */
    public abstract function get_indexes($table);

    /**
     * Returns detailed information about columns in table. This information is cached internally.
     * @param string $table name
     * @param bool $usecache
     * @return array of database_column_info objects indexed with column names
     */
    public abstract function get_columns($table, $usecache=true);

    /**
     * Normalise values based in RDBMS dependencies (booleans, LOBs...)
     *
     * @param database_column_info $column column metadata corresponding with the value we are going to normalise
     * @param mixed $value value we are going to normalise
     * @return mixed the normalised value
     */
    protected abstract function normalise_value($column, $value);

    /**
     * Reset internal column details cache
     * @param string $table - empty means all, or one if name of table given
     * @return void
     */
    public function reset_caches() {
        $this->columns = array();
        $this->tables  = null;
    }

    /**
     * Returns sql generator used for db manipulation.
     * Used mostly in upgrade.php scripts.
     * @return database_manager instance
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
     * Attempt to change db encoding toUTF-8 if possible
     * @return bool success
     */
    public function change_db_encoding() {
        return false;
    }

    /**
     * Is db in unicode mode?
     * @return bool
     */
    public function setup_is_unicodedb() {
        return true;
    }

    /**
     * Enable/disable very detailed debugging
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
     * @param bool $state
     */
    public function set_logging($state) {
        // adodb sql logging shares one table without prefix per db - this is no longer acceptable :-(
        // we must create one table shared by all drivers
    }

    /**
     * Do NOT use in code, to be used by database_manager only!
     * @param string $sql query
     * @return bool true
     * @throws dml_exception if error
     */
    public abstract function change_database_structure($sql);

    /**
     * Execute general sql query. Should be used only when no other method suitable.
     * Do NOT use this to make changes in db structure, use database_manager methods instead!
     * @param string $sql query
     * @param array $params query parameters
     * @return bool true
     * @throws dml_exception if error
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
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return moodle_recordset instance
     * @throws dml_exception if error
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
     * Other arguments and the return type as for @see function get_recordset.
     *
     * @param string $table the table to query.
     * @param string $field a field to check (optional).
     * @param array $values array of values the field must have
     * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
     * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return moodle_recordset instance
     * @throws dml_exception if error
     */
    public function get_recordset_list($table, $field, array $values, $sort='', $fields='*', $limitfrom=0, $limitnum=0) {
        list($select, $params) = $this->where_clause_list($field, $values);
        if (empty($select)) {
            $select = '1 = 2'; /// Fake condition, won't return rows ever. MDL-17645
            $params = array();
        }
        return $this->get_recordset_select($table, $select, $params, $sort, $fields, $limitfrom, $limitnum);
    }

    /**
     * Get a number of records as a moodle_recordset which match a particular WHERE clause.
     *
     * If given, $select is used as the SELECT parameter in the SQL query,
     * otherwise all records from the table are returned.
     *
     * Other arguments and the return type as for @see function get_recordset.
     *
     * @param string $table the table to query.
     * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
     * @param array $params array of sql parameters
     * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
     * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return moodle_recordset instance
     * @throws dml_exception if error
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
     * The return type is as for @see function get_recordset.
     *
     * @param string $sql the SQL select query to execute.
     * @param array $params array of sql parameters
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return moodle_recordset instance
     * @throws dml_exception if error
     */
    public abstract function get_recordset_sql($sql, array $params=null, $limitfrom=0, $limitnum=0);

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
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return array of objects indexed by first column
     * @throws dml_exception if error
     */
    public function get_records($table, array $conditions=null, $sort='', $fields='*', $limitfrom=0, $limitnum=0) {
        list($select, $params) = $this->where_clause($table, $conditions);
        return $this->get_records_select($table, $select, $params, $sort, $fields, $limitfrom, $limitnum);
    }

    /**
     * Get a number of records as an array of objects where one field match one list of values.
     *
     * Return value as for @see function get_records.
     *
     * @param string $table The database table to be checked against.
     * @param string $field The field to search
     * @param string $values array of values
     * @param string $sort Sort order (as valid SQL sort parameter)
     * @param string $fields A comma separated list of fields to be returned from the chosen table. If specified,
     *   the first field should be a unique one such as 'id' since it will be used as a key in the associative
     *   array.
     * @return array of objects indexed by first column
     * @throws dml_exception if error
     */
    public function get_records_list($table, $field, array $values, $sort='', $fields='*', $limitfrom=0, $limitnum=0) {
        list($select, $params) = $this->where_clause_list($field, $values);
        if (empty($select)) {
            // nothing to return
            return array();
        }
        return $this->get_records_select($table, $select, $params, $sort, $fields, $limitfrom, $limitnum);
    }

    /**
     * Get a number of records as an array of objects which match a particular WHERE clause.
     *
     * Return value as for @see function get_records.
     *
     * @param string $table the table to query.
     * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
     * @param array $params array of sql parameters
     * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
     * @param string $fields a comma separated list of fields to return
     *   (optional, by default all fields are returned). The first field will be used as key for the
     *   array so must be a unique field such as 'id'.
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return array of objects indexed by first column
     * @throws dml_exception if error
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
     * Return value as for @see function get_records.
     *
     * @param string $sql the SQL select query to execute. The first column of this SELECT statement
     *   must be a unique value (usually the 'id' field), as it will be used as the key of the
     *   returned array.
     * @param array $params array of sql parameters
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return array of objects indexed by first column
     * @throws dml_exception if error
     */
    public abstract function get_records_sql($sql, array $params=null, $limitfrom=0, $limitnum=0);

    /**
     * Get the first two columns from a number of records as an associative array where all the given conditions met.
     *
     * Arguments as for @see function get_recordset.
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
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return array an associative array
     * @throws dml_exception if error
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
     * Arguments as for @see function get_recordset_select.
     * Return value as for @see function get_records_menu.
     *
     * @param string $table The database table to be checked against.
     * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
     * @param array $params array of sql parameters
     * @param string $sort Sort order (optional) - a valid SQL order parameter
     * @param string $fields A comma separated list of fields to be returned from the chosen table - the number of fields should be 2!
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return array an associative array
     * @throws dml_exception if error
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
     * Arguments as for @see function get_recordset_sql.
     * Return value as for @see function get_records_menu.
     *
     * @param string $sql The SQL string you wish to be executed.
     * @param array $params array of sql parameters
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return array an associative array
     * @throws dml_exception if error
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
     *                        MUST_EXIST means throw exception if no record or multiple records found
     * @return mixed a fieldset object containing the first matching record, false or exception if error not found depending on mode
     * @throws dml_exception if error
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
     * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
     *                        IGNORE_MULTIPLE means return first, ignore multiple records found(not recommended);
     *                        MUST_EXIST means throw exception if no record or multiple records found
     * @return mixed a fieldset object containing the first matching record, false or exception if error not found depending on mode
     * @throws dml_exception if error
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
     * @throws dml_exception if error
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
     * @throws dml_exception if error
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
     * @throws dml_exception if error
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
     * @param string $table the table to query.
     * @param string $return the field to return the value of.
     * @param string $sql The SQL query returning one row with one column
     * @param array $params array of sql parameters
     * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
     *                        IGNORE_MULTIPLE means return first, ignore multiple records found(not recommended);
     *                        MUST_EXIST means throw exception if no record or multiple records found
     * @return mixed the specified value false if not found
     * @throws dml_exception if error
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
     * @throws dml_exception if error
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
     * @throws dml_exception if error
     */
    public abstract function get_fieldset_sql($sql, array $params=null);

    /**
     * Insert new record into database, as fast as possible, no safety checks, lobs not supported.
     * @param string $table name
     * @param mixed $params data record as object or array
     * @param bool $returnit return it of inserted record
     * @param bool $bulk true means repeated inserts expected
     * @param bool $customsequence true if 'id' included in $params, disables $returnid
     * @return bool|int true or new id
     * @throws dml_exception if error
     */
    public abstract function insert_record_raw($table, $params, $returnid=true, $bulk=false, $customsequence=false);

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
     * @throws dml_exception if error
     */
    public abstract function insert_record($table, $dataobject, $returnid=true, $bulk=false);

    /**
     * Import a record into a table, id field is required.
     * Safety checks are NOT carried out. Lobs are supported.
     *
     * @param string $table name of database table to be inserted into
     * @param object $dataobject A data object with values for one or more fields in the record
     * @return bool true
     * @throws dml_exception if error
     */
    public abstract function import_record($table, $dataobject);

    /**
     * Update record in database, as fast as possible, no safety checks, lobs not supported.
     * @param string $table name
     * @param mixed $params data record as object or array
     * @param bool true means repeated updates expected
     * @return bool true
     * @throws dml_exception if error
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
     * @param bool true means repeated updates expected
     * @return bool true
     * @throws dml_exception if error
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
     * @throws dml_exception if error
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
     * @throws dml_exception if error
     */
    public abstract function set_field_select($table, $newfield, $newvalue, $select, array $params=null);


    /**
     * Count the records in a table where all the given conditions met.
     *
     * @param string $table The table to query.
     * @param array $conditions optional array $fieldname=>requestedvalue with AND in between
     * @return int The count of records returned from the specified criteria.
     * @throws dml_exception if error
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
     * @throws dml_exception if error
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
     * @throws dml_exception if error
     */
    public function count_records_sql($sql, array $params=null) {
        if ($count = $this->get_field_sql($sql, $params)) {
            return $count;
        } else {
            return 0;
        }
    }

    /**
     * Test whether a record exists in a table where all the given conditions met.
     *
     * The record to test is specified by giving up to three fields that must
     * equal the corresponding values.
     *
     * @param string $table The table to check.
     * @param array $conditions optional array $fieldname=>requestedvalue with AND in between
     * @return bool true if a matching record exists, else false.
     * @throws dml_exception if error
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
     * @throws dml_exception if error
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
     * @throws dml_exception if error
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
     * @throws dml_exception if error
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
     * @throws dml_exception if error
     */
    public function delete_records_list($table, $field, array $values) {
        list($select, $params) = $this->where_clause_list($field, $values);
        if (empty($select)) {
            // nothing to delete
            return true;
        }
        return $this->delete_records_select($table, $select, $params);
    }

    /**
     * Delete one or more records from a table which match a particular WHERE clause.
     *
     * @param string $table The database table to be checked against.
     * @param string $select A fragment of SQL to be used in a where clause in the SQL call (used to define the selection criteria).
     * @param array $params array of sql parameters
     * @return bool true.
     * @throws dml_exception if error
     */
    public abstract function delete_records_select($table, $select, array $params=null);



/// sql constructs
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
     * @param int $int1 first integer in the operation
     * @param int $int2 second integer in the operation
     * @return string the piece of SQL code to be used in your statement
     */
    public function sql_bitand($int1, $int2) {
        return '((' . $int1 . ') & (' . $int2 . '))';
    }

    /**
     * Returns the SQL text to be used in order to perform one bitwise NOT operation
     * with 1 integer.
     *
     * @param int $int1 integer in the operation
     * @return string the piece of SQL code to be used in your statement.
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
     * @param int $int1 first integer in the operation
     * @param int $int2 second integer in the operation
     * @return string the piece of SQL code to be used in your statement.
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
     * @param int $int1 first integer in the operation
     * @param int $int2 second integer in the operation
     * @return string the piece of SQL code to be used in your statement.
     */
    public function sql_bitxor($int1, $int2) {
        return '((' . $int1 . ') ^ (' . $int2 . '))';
    }

    /**
     * Returns the SQL text to be used in order to perform module '%'
     * operation - remainder after division
     *
     * @param int $int1 first integer in the operation
     * @param int $int2 second integer in the operation
     * @return string the piece of SQL code to be used in your statement.
     */
    public function sql_modulo($int1, $int2) {
        return '((' . $int1 . ') % (' . $int2 . '))';
    }

    /**
     * Returns the correct CEIL expression applied to fieldname.
     *
     * @param string $fieldname the field (or expression) we are going to ceil
     * @return string the piece of SQL code to be used in your ceiling statement
     * Most DB use CEIL(), hence it's the default.
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
     * @param string $fieldname the name of the field to be casted
     * @param bool $text to specify if the original column is one TEXT (CLOB) column (true). Defaults to false.
     * @return string the piece of SQL code to be used in your statement.
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
     * @param string $fieldname the name of the field to be casted
     * @param bool $text to specify if the original column is one TEXT (CLOB) column (true). Defaults to false.
     * @return string the piece of SQL code to be used in your statement.
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
     * @param string $fieldname the name of the field to be cast
     * @return string the piece of SQL code to be used in your statement.
     */
    public function sql_cast_2signed($fieldname) {
        return ' ' . $fieldname . ' ';
    }

    /**
     * Returns the SQL text to be used to compare one TEXT (clob) column with
     * one varchar column, because some RDBMS doesn't support such direct
     * comparisons.
     *
     * @param string $fieldname the name of the TEXT field we need to order by
     * @param int $numchars of chars to use for the ordering (defaults to 32)
     * @return string the piece of SQL code to be used in your statement.
     */
    public function sql_compare_text($fieldname, $numchars=32) {
        return $this->sql_order_by_text($fieldname, $numchars);
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
        // by default ignore any sensitiveness - each database does it in a different way
        return "$fieldname $LIKE $param ESCAPE '$escapechar'";
    }

    /**
     * Escape sql LIKE special characters.
     * @param string $text
     * @param string $escapechar
     * @return string
     */
    public function sql_like_escape($text, $escapechar = '\\') {
        $text = str_replace('_', $escapechar.'_', $text);
        $text = str_replace('%', $escapechar.'%', $text);
        return $text;
    }

    /**
     * Returns the proper SQL to do LIKE in a case-insensitive way.
     *
     * Note the LIKE are case sensitive for Oracle. Oracle 10g is required to use
     * the case insensitive search using regexp_like() or NLS_COMP=LINGUISTIC :-(
     * See http://docs.moodle.org/en/XMLDB_Problems#Case-insensitive_searches
     *
     * @deprecated
     * @return string
     */
    public function sql_ilike() {
        debugging('sql_ilike() is deprecated, please use sql_like() instead');
        return 'LIKE';
    }

    /**
     * Returns the proper SQL to do CONCAT between the elements passed
     * Can take many parameters
     *
     * This function accepts variable number of string parameters.
     *
     * @return string
     */
    public abstract function sql_concat();

    /**
     * Returns the proper SQL to do CONCAT between the elements passed
     * with a given separator
     *
     * @param string $separator
     * @param array  $elements
     * @return string
     */
    public abstract function sql_concat_join($separator="' '", $elements=array());

    /**
     * Returns the proper SQL (for the dbms in use) to concatenate $firstname and $lastname
     * TODO: Does this really need to be here? Eloy 20070727.
     * TODO: we definitely do not! (skodak)
     *
     * @param string $first User's first name
     * @param string $last User's last name
     * @return string
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
     * @param string $fieldname the name of the TEXT field we need to order by
     * @param string $numchars of chars to use for the ordering (defaults to 32)
     * @return string the piece of SQL code to be used in your statement.
     */
    public function sql_order_by_text($fieldname, $numchars=32) {
        return $fieldname;
    }

    /**
     * Returns the SQL text to be used to calculate the length in characters of one expression.
     * @param string $fieldname or expression to calculate its length in characters.
     * @return string the piece of SQL code to be used in the statement.
     */
    public function sql_length($fieldname) {
        return ' LENGTH(' . $fieldname . ')';
    }

    /**
     * Returns the proper substr() SQL text used to extract substrings from DB
     * NOTE: this was originally returning only function name
     *
     * @param string $expr some string field, no aggregates
     * @param mixed $start integer or expression evaluating to int (1 based value; first char has index 1)
     * @param mixed $length optional integer or expression evaluating to int
     * @return string sql fragment
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
     * Note, there is no guarantee which order $needle, $haystack will be in
     * the resulting SQL, so when using this method, and both arguments contain
     * placeholders, you should use named placeholders.
     * @param string $needle the SQL expression that will be searched for.
     * @param string $haystack the SQL expression that will be searched in.
     * @return string the required SQL
     */
    public function sql_position($needle, $haystack) {
        // Implementation using standard SQL.
        return "POSITION(($needle) IN ($haystack))";
    }

    /**
     * Returns the empty string char used by every supported DB. To be used when
     * we are searching for that values in our queries. Only Oracle uses this
     * for now (will be out, once we migrate to proper NULLs if that days arrives)
     * @return string
     */
    function sql_empty() {
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
     * are being used. Final result should be:
     *
     *     ... AND ' . sql_isempty('tablename', 'fieldname', true/false, true/false);
     *
     * (see parameters description below)
     *
     * @param string $tablename name of the table (without prefix). Not used for now but can be
     *                          necessary in the future if we want to use some introspection using
     *                          meta information against the DB. /// TODO ///
     * @param string $fieldname name of the field we are going to check
     * @param boolean $nullablefield to specify if the field us nullable (true) or no (false) in the DB
     * @param boolean $textfield to specify if it is a text (also called clob) field (true) or a varchar one (false)
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
     * are being used. Final result should be:
     *
     *     ... AND ' . sql_isnotempty('tablename', 'fieldname', true/false, true/false);
     *
     * (see parameters description below)
     *
     * @param string $tablename name of the table (without prefix). Not used for now but can be
     *                          necessary in the future if we want to use some introspection using
     *                          meta information against the DB. /// TODO ///
     * @param string $fieldname name of the field we are going to check
     * @param boolean $nullablefield to specify if the field us nullable (true) or no (false) in the DB
     * @param boolean $textfield to specify if it is a text (also called clob) field (true) or a varchar one (false)
     * @return string the sql code to be added to check for non empty values
     */
    public function sql_isnotempty($tablename, $fieldname, $nullablefield, $textfield) {
        return ' ( NOT ' . $this->sql_isempty($tablename, $fieldname, $nullablefield, $textfield) . ') ';
    }

    /**
     * Does this driver suppoer regex syntax when searching
     * @return bool
     */
    public function sql_regex_supported() {
        return false;
    }

    /**
     * Return regex positive or negative match sql
     * @param bool $positivematch
     * @return string or empty if not supported
     */
    public function sql_regex($positivematch=true) {
        return '';
    }

/// transactions

    /**
     * Are transactions supported?
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
     * Returns true if transaction in progress
     * @return bool
     */
    public function is_transaction_started() {
        return !empty($this->transactions);
    }

    /**
     * Throws exception if transaction in progress.
     * This test does not force rollback of active transactions.
     * @return void
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
     * @return void
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
     * @param moodle_transaction $transaction
     * @param Exception $e exception that caused the problem
     * @return void - does not return, exception is rethrown
     */
    public function rollback_delegated_transaction(moodle_transaction $transaction, Exception $e) {
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
     * Does not trow any exceptions and does not log anything.
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
        $this->transactions = array(); // unfortunately all unfinished exceptions are kept in memory
        $this->force_rollback = false;
    }

/// session locking
    /**
     * Is session lock supported in this driver?
     * @return bool
     */
    public function session_lock_supported() {
        return false;
    }

    /**
     * Obtain session lock
     * @param int $rowid id of the row with session record
     * @param int $timeout max allowed time to wait for the lock in seconds
     * @return bool success
     */
    public function get_session_lock($rowid, $timeout) {
        $this->used_for_db_sessions = true;
    }

    /**
     * Release session lock
     * @param int $rowid id of the row with session record
     * @return bool success
     */
    public function release_session_lock($rowid) {
    }

/// performance and logging
    /**
     * Returns number of reads done by this database
     * @return int
     */
    public function perf_get_reads() {
        return $this->reads;
    }

    /**
     * Returns number of writes done by this database
     * @return int
     */
    public function perf_get_writes() {
        return $this->writes;
    }

    /**
     * Returns number of queries done by this database
     * @return int
     */
    public function perf_get_queries() {
        return $this->writes + $this->reads;
    }
}
