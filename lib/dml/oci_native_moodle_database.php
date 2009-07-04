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
 * @package    moodlecore
 * @subpackage DML
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/dml/moodle_database.php');
require_once($CFG->libdir.'/dml/oci_native_moodle_recordset.php');

/**
 * Native oci class representing moodle database interface.
 */
class oci_native_moodle_database extends moodle_database {

    protected $oci     = null;
    protected $bytea_oid = null;

    protected $last_debug;

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
     * @return string db type mysql, oci, postgres7
     */
    protected function get_dbtype() {
        return 'oci';
    }

    /**
     * Returns general database library name
     * Note: can be used before connect()
     * @return string db type adodb, pdo, native
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
        return get_string('nativeoci', 'install'); // TODO: localise
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
     * Returns localised database description
     * Note: can be used before connect()
     * @return string
     */
    public function get_configuration_hints() {
        return get_string('databasesettingssub_oci', 'install'); // TODO: l
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

        $this->store_settings($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions);
        unset($this->dboptions['dbsocket']);

        $pass = addcslashes($this->dbpass, "'\\");

        if (empty($this->dbhost)) {
            // old style full address
        } else {
            if (empty($this->dboptions['dbport'])) {
                $this->dboptions['dbport'] = 1521;
            }
            $this->dbname = '//'.$this->dbhost.':'.$this->dboptions['dbport'].'/'.$this->dbname;
        }

        ob_start();
        if (empty($this->dboptions['dbpersit'])) {
            $this->oci = oci_connect($this->dbuser, $this->dbpass, $this->dbname, 'UTF-8');
        } else {
            $this->oci = oci_pconnect($this->dbuser, $this->dbpass, $this->dbname, 'UTF-8');
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

        //note: do not send "ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.,'" !
        //      instead fix our PHP code to convert "," to "." properly!

        return true;
    }

    /**
     * Close database connection and release all resources
     * and memory (especially circular memory references).
     * Do NOT use connect() again, create a new instance if needed.
     */
    public function dispose() {
        parent::dispose(); // Call parent dispose to write/close session and other common stuff before clossing conn
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
        //$this->last_debug = error_reporting(0);
    }

    /**
     * Called immediately after each db query.
     * @param mixed db specific result
     * @return void
     */
    protected function query_end($result, $stmt=null) {
        //reset original debug level
        //error_reporting($this->last_debug);
        if ($stmt and $result === false) {
            oci_free_statement($stmt);
        }
        parent::query_end($result);
    }

    /**
     * Returns database server info array
     * @return array
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

    protected function is_min_version($version) {
        $server = $this->get_server_info();
        $server = $server['version'];
        return version_compare($server, $version, '>=');
    }

    /**
     * Returns supported query parameter types
     * @return bitmask
     */
    protected function allowed_param_types() {
        return SQL_PARAMS_NAMED;
    }

    /**
     * Returns last error reported by database engine.
     */
    public function get_last_error() {
        $e = oci_error($this->oci);
        if (isset($e['message'])) {
            return $e['message'];
        }
        return false;
    }

    protected function parse_query($sql) {
        $stmt = oci_parse($this->oci, $sql);
        if ($stmt === false) {
            throw new dml_connection_exception('Can not parse sql query'); //TODO: maybe add better info
        }
        return $stmt;
    }

    /**
     * Return tables in database WITHOUT current prefix
     * @return array of table names in lowercase and without prefix
     */
    public function get_tables($usecache=true) {
        $tables = array();
        $prefix = str_replace('_', "\\_", strtoupper($this->prefix));
        $sql = "SELECT TABLE_NAME
                  FROM CAT
                 WHERE TABLE_TYPE='TABLE'
                       AND TABLE_NAME NOT LIKE 'BIN\$%'
                       AND TABLE_NAME LIKE '$prefix%' ESCAPE '\\'";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $stmt = $this->parse_query($sql);
        $result = oci_execute($stmt);
        $this->query_end($result, $stmt);
        $records = null;
        oci_fetch_all($stmt, $records, 0, -1, OCI_ASSOC);
        oci_free_statement($stmt);
        $records = array_map('strtolower', $records['TABLE_NAME']);
        foreach ($records as $tablename) {
            if (strpos($tablename, $this->prefix) !== 0) {
                continue;
            }
            $tablename = substr($tablename, strlen($this->prefix));
            $tables[$tablename] = $tablename;
        }

        return $tables;
    }

    /**
     * Return table indexes - everything lowercased
     * @return array of arrays
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
        $result = oci_execute($stmt);
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
     * Returns datailed information about columns in table. This information is cached internally.
     * @param string $table name
     * @param bool $usecache
     * @return array array of database_column_info objects indexed with column names
     */
    public function get_columns($table, $usecache=true) {
        if ($usecache and isset($this->columns[$table])) {
            return $this->columns[$table];
        }

        $this->columns[$table] = array();

        $tablename = strtoupper($this->prefix.$table);

        $sql = "SELECT CNAME, COLTYPE, WIDTH, SCALE, PRECISION, NULLS, DEFAULTVAL
                  FROM COL
                 WHERE TNAME='$tablename'
              ORDER BY COLNO";

        $this->query_start($sql, null, SQL_QUERY_AUX);
        $stmt = $this->parse_query($sql);
        $result = oci_execute($stmt);
        $this->query_end($result, $stmt);
        $records = null;
        oci_fetch_all($stmt, $records, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
        oci_free_statement($stmt);

        if (!$records) {
            return array();
        }
        foreach ($records as $rawcolumn) {
            $rawcolumn = (object)$rawcolumn;

            $info = new object();
            $info->name = strtolower($rawcolumn->CNAME);
            $matches = null;

            if ($rawcolumn->COLTYPE === 'VARCHAR2'
             or $rawcolumn->COLTYPE === 'VARCHAR'
             or $rawcolumn->COLTYPE === 'NVARCHAR2'
             or $rawcolumn->COLTYPE === 'NVARCHAR'
             or $rawcolumn->COLTYPE === 'CHAR'
             or $rawcolumn->COLTYPE === 'NCHAR') {
                //TODO add some basic enum support here
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
                    } else if ($rawcolumn->DEFAULTVAL === "' ' ") {
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
                $info->auto_increment= false;
                $info->unique        = null;

            } else if ($rawcolumn->COLTYPE === 'NUMBER') {
                $info->type       = $rawcolumn->COLTYPE;
                $info->max_length = $rawcolumn->PRECISION;
                $info->binary     = false;
                if ($rawcolumn->SCALE == 0) {
                    // integer
                    if ($info->name === 'id') {
                        $info->primary_key   = true;
                        $info->meta_type     = 'R';
                        $info->unique        = true;
                        $info->auto_increment= true;
                        $info->has_default   = false;
                    } else {
                        $info->primary_key   = false;
                        $info->meta_type     = 'I';
                        $info->unique        = null;
                        $info->auto_increment= false;
                    }
                    $info->scale = null;

                } else {
                    //float
                    $info->meta_type     = 'N';
                    $info->primary_key   = false;
                    $info->unsigned      = null;
                    $info->auto_increment= false;
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
                $info->auto_increment= false;
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
                $info->max_length    = $rawcolumn->WIDTH;
                $info->scale         = null;
                $info->scale         = null;
                $info->not_null      = ($rawcolumn->NULLS === 'NOT NULL');
                $info->has_default   = !is_null($rawcolumn->DEFAULTVAL);
                if ($info->has_default) {
                    // this is hacky :-(
                    if ($rawcolumn->DEFAULTVAL === 'NULL') {
                        $info->default_value = null;
                    } else if ($rawcolumn->DEFAULTVAL === "' ' ") {
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
                $info->auto_increment= false;
                $info->unique        = null;

            } else if ($rawcolumn->COLTYPE === 'BLOB') {
                $info->type          = $rawcolumn->COLTYPE;
                $info->meta_type     = 'B';
                $info->max_length    = $rawcolumn->WIDTH;
                $info->scale         = null;
                $info->scale         = null;
                $info->not_null      = ($rawcolumn->NULLS === 'NOT NULL');
                $info->has_default   = !is_null($rawcolumn->DEFAULTVAL);
                if ($info->has_default) {
                    // this is hacky :-(
                    if ($rawcolumn->DEFAULTVAL === 'NULL') {
                        $info->default_value = null;
                    } else if ($rawcolumn->DEFAULTVAL === "' ' ") {
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
                $info->auto_increment= false;
                $info->unique        = null;

            } else {
                // unknown type - sorry
                $info->type          = $rawcolumn->COLTYPE;
                $info->meta_type     = '?';
            }

            $this->columns[$table][$info->name] = new database_column_info($info);
        }

        return $this->columns[$table];
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
        $result = oci_execute($stmt);
        $this->query_end($result, $stmt);
        $records = null;
        oci_fetch_all($stmt, $records, 0, -1, OCI_FETCHSTATEMENT_BY_COLUMN);
        oci_free_statement($stmt);

        return (isset($records['VALUE'][0]) and $records['VALUE'][0] === 'AL32UTF8');
    }

    /**
     * Do NOT use in code, to be used by database_manager only!
     * @param string $sql query
     * @return bool true
     * @throws dml_exception if error
     */
    public function change_database_structure($sql) {
        $this->reset_caches();

        $this->query_start($sql, null, SQL_QUERY_STRUCTURE);
        $stmt = $this->parse_query($sql);
        $result = oci_execute($stmt);
        $this->query_end($result, $stmt);
        oci_free_statement($stmt);

        return true;
    }

    protected function bind_params($stmt, array $params=null, $tablename=null) {
        $descriptors = array();
        if ($params) {
            $columns = array();
            if ($tablename) {
                $columns = $this->get_columns($tablename);
            }
            foreach($params as $key=>$value) {
                if (isset($columns[$key])) {
                    $type = $columns[$key]->meta_type;
                    $maxlength = $columns[$key]->max_length;
                } else {
                    $type = '?';
                    $maxlength = -1;
                }
                switch ($type) {
                    case 'I':
                    case 'R':
                    case 'N':
                        $params[$key] = (int)$value;
                        oci_bind_by_name($stmt, ":$key", $params[$key]);
                        break;
                    case 'F':
                        $params[$key] = (float)$value;
                        oci_bind_by_name($stmt, ":$key", $params[$key]);
                        break;

                    case 'B':
                        //TODO
/*                        $lob = oci_new_descriptor($this->oci, OCI_D_LOB);
                        $lob->write($params[$key]);
                        oci_bind_by_name($stmt, ":$key", $lob, -1, SQLT_BLOB);
                        $descriptors[] = $lob;
                        break;*/

                    case 'X':
                    default:
                        if ($params[$key] === '') {
                            $params[$key] = ' ';
                        }
                        oci_bind_by_name($stmt, ":$key", $params[$key]);
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
     * Do NOT use this to make changes in db structure, use database_manager::execute_sql() instead!
     * @param string $sql query
     * @param array $params query parameters
     * @return bool true
     * @throws dml_exception if error
     */
    public function execute($sql, array $params=null) {
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        if (strpos($sql, ';') !== false) {
            throw new coding_exception('moodle_database::execute() Multiple sql statements found or bound parameters not used properly in query!');
        }

        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $stmt = $this->parse_query($sql);
        $this->bind_params($stmt, $params);
        $result = oci_execute($stmt);
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
     * @throws dml_exception if error
     */
    public function get_record_sql($sql, array $params=null, $mode=0) {
        $mode = (int)$mode;
        if ($mode == IGNORE_MULTIPLE) {
            // do not limit here - ORA does not like that
            if (!$rs = $this->get_recordset_sql($sql, $params)) {
                return false;
            }
            foreach ($rs as $result) {
                $rs->close();
                return $result;
            }
            $rs->close();
            return false;
        }
        return parent::get_record_sql($sql, $params, $mode);
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
     * @return mixed an moodle_recordset object
     * @throws dml_exception if error
     */
    public function get_recordset_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {
        $limitfrom = (int)$limitfrom;
        $limitnum  = (int)$limitnum;
        $limitfrom = ($limitfrom < 0) ? 0 : $limitfrom;
        $limitnum  = ($limitnum < 0)  ? 0 : $limitnum;

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        if ($limitfrom and $limitnum) {
            $sql = "SELECT oracle_o.*
                      FROM (SELECT oracle_i.*, rownum AS oracle_rownum
                              FROM ($sql) oracle_i
                            ) oracle_o
                     WHERE rownum <= :oracle_max AND oracle_rownum > :oracle_min";
            $params['oracle_max'] = $limitfrom + $limitnum;
            $params['oracle_min'] = $limitfrom;

        } else if ($limitfrom and !$limitnum) {
            $sql = "SELECT oracle_o.*
                      FROM (SELECT oracle_i.*, rownum AS oracle_rownum
                              FROM ($sql) oracle_i
                            ) oracle_o
                     WHERE oracle_rownum > :oracle_min";
            $params['oracle_min'] = $limitfrom;

        } else if (!$limitfrom and $limitnum) {
            $sql = "SELECT *
                      FROM ($sql)
                     WHERE rownum <= :oracle_max";
            $params['oracle_max'] = $limitnum;
        }

        $this->query_start($sql, $params, SQL_QUERY_SELECT);
        $stmt = $this->parse_query($sql);
        $this->bind_params($stmt, $params);
        $result = oci_execute($stmt);
        $this->query_end($result, $stmt);

        return $this->create_recordset($stmt);
    }

    protected function create_recordset($stmt) {
        return new oci_native_moodle_recordset($stmt);
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
     * @return mixed an array of objects, or empty array if no records were found
     * @throws dml_exception if error
     */
    public function get_records_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {
        $limitfrom = (int)$limitfrom;
        $limitnum  = (int)$limitnum;
        $limitfrom = ($limitfrom < 0) ? 0 : $limitfrom;
        $limitnum  = ($limitnum < 0)  ? 0 : $limitnum;

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        if ($limitfrom and $limitnum) {
            $sql = "SELECT oracle_o.*
                      FROM (SELECT oracle_i.*, rownum AS oracle_rownum
                              FROM ($sql) oracle_i
                            ) oracle_o
                     WHERE rownum <= :oracle_max AND oracle_rownum > :oracle_min";
            $params['oracle_max'] = $limitfrom + $limitnum;
            $params['oracle_min'] = $limitfrom;

        } else if ($limitfrom and !$limitnum) {
            $sql = "SELECT oracle_o.*
                      FROM (SELECT oracle_i.*, rownum AS oracle_rownum
                              FROM ($sql) oracle_i
                            ) oracle_o
                     WHERE oracle_rownum > :oracle_min";
            $params['oracle_min'] = $limitfrom;

        } else if (!$limitfrom and $limitnum) {
            $sql = "SELECT *
                      FROM ($sql)
                     WHERE rownum <= :oracle_max";
            $params['oracle_max'] = $limitnum;
        }

        $this->query_start($sql, $params, SQL_QUERY_SELECT);
        $stmt = $this->parse_query($sql);
        $this->bind_params($stmt, $params);
        $result = oci_execute($stmt);
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
     * @throws dml_exception if error
     */
    public function get_fieldset_sql($sql, array $params=null) {
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_SELECT);
        $stmt = $this->parse_query($sql);
        $this->bind_params($stmt, $params);
        $result = oci_execute($stmt);
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
     * @return true or new id
     * @throws dml_exception if error
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
            if ($returnid) {
                $returning = "RETURNING id INTO :oracle_id";// crazy name nobody is ever going to use or parameter ;-)
            }
            unset($params['id']);
        }

        if (empty($params)) {
            throw new coding_exception('moodle_database::insert_record_raw() no fields found.');
        }

        $fields = implode(',', array_keys($params));
        $values = array();
        foreach ($params as $pname=>$value) {
            $values[] = ":$pname";
        }
        $values = implode(',', $values);

        $sql = "INSERT INTO {$this->prefix}$table ($fields) VALUES($values) $returning";
        $id = null;

        $this->query_start($sql, $params, SQL_QUERY_INSERT);
        $stmt = $this->parse_query($sql);
        $descriptors = $this->bind_params($stmt, $params, $table);
        if ($returning) {
            oci_bind_by_name($stmt, ":oracle_id", $id, -1, SQLT_LNG);
        }
        $result = oci_execute($stmt);
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
     * @return true or new id
     * @throws dml_exception if error
     */
    public function insert_record($table, $dataobject, $returnid=true, $bulk=false) {
        if (!is_object($dataobject)) {
            $dataobject = (object)$dataobject;
        }

        $columns = $this->get_columns($table);

        unset($dataobject->id);
        $cleaned = array();

        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) {
                continue;
            }
            $cleaned[$field] = $value;
        }

        $id = $this->insert_record_raw($table, $cleaned, true, $bulk);

        return ($returnid ? $id : true);

    }

    /**
     * Import a record into a table, id field is required.
     * Safety checks are NOT carried out. Lobs are supported.
     *
     * @param string $table name of database table to be inserted into
     * @param object $dataobject A data object with values for one or more fields in the record
     * @return bool true
     * @throws dml_exception if error
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
     * Update record in database, as fast as possible, no safety checks, lobs not supported.
     * @param string $table name
     * @param mixed $params data record as object or array
     * @param bool true means repeated updates expected
     * @return bool true
     * @throws dml_exception if error
     */
    public function update_record_raw($table, $params, $bulk=false) {
        if (!is_array($params)) {
            $params = (array)$params;
        }
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
        $sql = "UPDATE {$this->prefix}$table SET $sets WHERE id=:id";

        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $stmt = $this->parse_query($sql);
        $descriptors = $this->bind_params($stmt, $params, $table);
        $result = oci_execute($stmt);
        $this->query_end($result, $stmt);
        $this->free_descriptors($descriptors);
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
     * @throws dml_exception if error
     */
    public function update_record($table, $dataobject, $bulk=false) {
        if (!is_object($dataobject)) {
            $dataobject = (object)$dataobject;
        }

        $columns = $this->get_columns($table);
        $cleaned = array();

        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) {
                continue;
            }
            $cleaned[$field] = $value;
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
     * @throws dml_exception if error
     */
    public function set_field_select($table, $newfield, $newvalue, $select, array $params=null) {
        if ($select) {
            $select = "WHERE $select";
        }
        $params = (array)$params;

        list($select, $params, $type) = $this->fix_sql_params($select, $params);

        if (is_bool($newvalue)) {
            $newvalue = (int)$newvalue; // prevent "false" problems
        }
        if (is_null($newvalue)) {
            $newsql = "$newfield = NULL";
        } else {
            $params[$newfield] = $newvalue;
            $newsql = "$newfield = :$newfield";
        }
        $sql = "UPDATE {$this->prefix}$table SET $newsql $select";

        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $stmt = $this->parse_query($sql);
        $descriptors = $this->bind_params($stmt, $params, $table);
        $result = oci_execute($stmt);
        $this->query_end($result, $stmt);
        $this->free_descriptors($descriptors);
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
     * @throws dml_exception if error
     */
    public function delete_records_select($table, $select, array $params=null) {
        if ($select) {
            $select = "WHERE $select";
        }
        $sql = "DELETE FROM {$this->prefix}$table $select";

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $stmt = $this->parse_query($sql);
        $this->bind_params($stmt, $params);
        $result = oci_execute($stmt);
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
        return '((' . $int1 . ') + (' . $int2 . ') - ' . $this->sql_bitand($int1, $int2) . ')';
    }

    public function sql_bitxor($int1, $int2) {
        return '(' . $this->sql_bitor($int1, $int2) . ' - ' . $this->sql_bitand($int1, $int2) . ')';
    }

    /**
     * Returns the SQL text to be used in order to perform module '%'
     * opration - remainder after division
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

    public function sql_ilike() {
        // TODO: add some ilike workaround
        return 'LIKE';
    }

    public function sql_concat() {
        $arr = func_get_args();
        $s = implode(' || ', $arr);
        if ($s === '') {
            return " '' ";
        }
        return " $s ";
    }

    public function sql_concat_join($separator="' '", $elements=array()) {
        for ($n=count($elements)-1; $n > 0 ; $n--) {
            array_splice($elements, $n, 0, $separator);
        }
        $s = implode(' || ', $elements);
        if ($s === '') {
            return " '' ";
        }
        return " $s ";
    }

    /**
     * Returns the SQL for returning searching one string for the location of another.
     */
    public function sql_position($needle, $haystack) {
        return "INSTR(($haystack), ($needle))";
    }

    public function sql_isempty($tablename, $fieldname, $nullablefield, $textfield) {
        if ($nullablefield) {
            return " $fieldname IS NULL ";                    /// empties in nullable fields are stored as
        } else {                                              /// NULLs
            if ($textfield) {
                return " ".$this->sql_compare_text($fieldname)." = ' ' "; /// oracle_dirty_hack inserts 1-whitespace
            } else {                                          /// in NOT NULL varchar and text columns so
                return " $fieldname = ' ' ";                  /// we need to look for that in any situation
            }
        }
    }

    function sql_empty() {
        return ' ';
    }

    public function sql_regex_supported() {
        return false;
    }

    public function sql_regex($positivematch=true) {
        return null;
    }

/// session locking
    // http://download.oracle.com/docs/cd/B10501_01/appdev.920/a96612/d_lock2.htm#999576

/// transactions
    /**
     * on DBs that support it, switch to transaction mode and begin a transaction
     * you'll need to ensure you call commit_sql() or your changes *will* be lost.
     *
     * this is _very_ useful for massive updates
     */
    public function begin_sql() {
        if (!parent::begin_sql()) {
            return false;
        }
        return true;

        $sql = "BEGIN";
        $this->query_start($sql, NULL, SQL_QUERY_AUX);
        $stmt = $this->parse_query($sql);
        $result = oci_execute($stmt);
        $this->query_end($result, $stmt);
        oci_free_statement($stmt);
        return true;
    }

    /**
     * on DBs that support it, commit the transaction
     */
    public function commit_sql() {
        if (!parent::commit_sql()) {
            return false;
        }
        return true;

        $sql = "COMMIT";
        $this->query_start($sql, NULL, SQL_QUERY_AUX);
        $stmt = $this->parse_query($sql);
        $result = oci_execute($stmt);
        $this->query_end($result, $stmt);
        oci_free_statement($stmt);
        return true;
    }

    /**
     * on DBs that support it, rollback the transaction
     */
    public function rollback_sql() {
        if (!parent::rollback_sql()) {
            return false;
        }
        return true;

        $sql = "ROLLBACK";
        $this->query_start($sql, NULL, SQL_QUERY_AUX);
        $stmt = $this->parse_query($sql);
        $result = oci_execute($stmt);
        $this->query_end($result, $stmt);
        oci_free_statement($stmt);
        return true;
    }
}
