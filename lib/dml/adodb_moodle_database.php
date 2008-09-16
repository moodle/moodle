<?php  //$Id$

require_once($CFG->libdir.'/dml/moodle_database.php');
require_once($CFG->libdir.'/dml/adodb_moodle_recordset.php');

/**
 * Abstract moodle database class
 * @package dml
 */
abstract class adodb_moodle_database extends moodle_database {

    protected $adodb;

    /**
     * Returns general database library name
     * Note: can be used before connect()
     * @return string db type adodb, pdo, native
     */
    protected function get_dblibrary() {
        return 'adodb';
    }

    /**
     * Returns localised database type name
     * Note: can be used before connect()
     * @return string
     */
    public function get_name() {
        $dbtype = $this->get_dbtype();
        return get_string($dbtype, 'install');
    }

    /**
     * Adodb preconnection routines, ususally sets up needed defines;
     */
    protected abstract function preconfigure_dbconnection();

    public function connect($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix, array $dboptions=null) {
        global $CFG;

        $this->store_settings($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix, $dboptions);

        $this->preconfigure_dbconnection();

        require_once($CFG->libdir.'/adodb/adodb.inc.php');

        $this->adodb = ADONewConnection($this->get_dbtype());

        // See MDL-6760 for why this is necessary. In Moodle 1.8, once we start using NULLs properly,
        // we probably want to change this value to ''.
        $this->adodb->null2null = 'A long random string that will never, ever match something we want to insert into the database, I hope. \'';

        if (!isset($this->dbpersist) or !empty($this->dbpersist)) {    // Use persistent connection (default)
            if (!$this->adodb->PConnect($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname)) {
                return false;
            }
        } else {                                                     // Use single connection
            if (!$this->adodb->Connect($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname)) {
                return false;
            }
        }
        $this->configure_dbconnection();
        return true;
    }

    /**
     * Adodb post connection routines, usually sets up encoding,e tc.
     */
    protected abstract function configure_dbconnection();

    /**
     * Close database connection and release all resources
     * and memory (especially circular memory references).
     * Do NOT use connect() again, create a new instance if needed.
     */
    public function dispose() {
        if ($this->adodb) {
            $this->adodb->Close();
        }
        parent::dispose();
    }

    /**
     * Returns database server info array
     * @return array
     */
    public function get_server_info() {
        //TODO: make all dblibraries return this info in a structured way (new server_info class or so, like database_column_info class)
        return $this->adodb->ServerInfo();
    }

    /**
     * Return tables in database WITHOUT current prefix
     * @return array of table names in lowercase and without prefix
     */
    public function get_tables($prefix=null) {
        $metatables = $this->adodb->MetaTables();
        $tables = array();

        if (is_null($prefix)) {
            $prefix = $this->prefix;
        }

        foreach ($metatables as $table) {
            $table = strtolower($table);
            if (empty($prefix) || strpos($table, $prefix) === 0) {
                $tablename = substr($table, strlen($prefix));
                $tables[$tablename] = $tablename;
            }
        }
        return $tables;
    }

    /**
     * Return table indexes - everything lowercased
     * @return array of arrays
     */
    public function get_indexes($table) {
        $this->reads++;
        if (!$indexes = $this->adodb->MetaIndexes($this->prefix.$table)) {
            return array();
        }
        $indexes = array_change_key_case($indexes, CASE_LOWER);
        foreach ($indexes as $indexname => $index) {
            $columns = $index['columns'];
            /// column names always lowercase
            $columns = array_map('strtolower', $columns);
            $indexes[$indexname]['columns'] = $columns;
        }

        return $indexes;
    }

    public function get_columns($table, $usecache=true) {
        if ($usecache and isset($this->columns[$table])) {
            return $this->columns[$table];
        }

        $this->reads++;
        if (!$columns = $this->adodb->MetaColumns($this->prefix.$table)) {
            return array();
        }

        $this->columns[$table] = array();

        foreach ($columns as $column) {
            // colum names must be lowercase
            $column->meta_type = substr($this->adodb->MetaType($column), 0 ,1); // only 1 character
            if (!empty($column->enums)) {
                // hack: fix the 'quotes' surrounding the values itroduced by adodb
                foreach ($column->enums as $key=>$value) {
                    if (strpos($value, "'") === 0 and strlen($value) > 2) {
                        $column->enums[$key] = substr($value, 1, strlen($value)-2);
                    }
                }
            }
            $this->columns[$table][$column->name] = new database_column_info($column);
        }

        return $this->columns[$table];
    }

    public function get_last_error() {
        return $this->adodb->ErrorMsg();
    }

    /**
     * Enable/disable very detailed debugging
     * @param bool $state
     */
    public function set_debug($state) {
        if ($this->adodb) {
            $this->adodb->debug = $state;
        }
    }

    /**
     * Returns debug status
     * @return bool $state
     */
    public function get_debug() {
        return $this->adodb->debug;
    }

    /**
     * Enable/disable detailed sql logging
     * @param bool $state
     */
    public function set_logging($state) {
        // TODO: adodb sql logging shares one table without prefix per db - this is no longer acceptable :-(
        // we must create one table shared by all drivers
    }

    /**
     * Do NOT use in code, to be used by database_manager only!
     * @param string $sql query
     * @return bool success
     */
    public function change_database_structure($sql) {
        $this->writes++;

        if ($rs = $this->adodb->Execute($sql)) {
            $result = true;
        } else {
            $result = false;
            $this->report_error($sql);
        }
        // structure changed, reset columns cache
        $this->reset_columns();
        return $result;
    }

    /**
     * Execute general sql query. Should be used only when no other method suitable.
     * Do NOT use this to make changes in db structure, use database_manager::execute_sql() instead!
     * @param string $sql query
     * @param array $params query parameters
     * @return bool success
     */
    public function execute($sql, array $params=null) {
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        if (strpos($sql, ';') !== false) {
            debugging('Error: Multiple sql statements found or bound parameters not used properly in query!');
            return false;
        }

        $this->writes++;

        if ($rs = $this->adodb->Execute($sql, $params)) {
            $result = true;
            $rs->Close();
        } else {
            $result = false;
            $this->report_error($sql, $params);
        }
        return $result;
    }

    /**
     * Insert new record into database, as fast as possible, no safety checks, lobs not supported.
     * @param string $table name
     * @param mixed $params data record as object or array
     * @param bool $returnit return it of inserted record
     * @param bool $bulk true means repeated inserts expected
     * @param bool $customsequence true if 'id' included in $params, disables $returnid
     * @return mixed success or new id
     */
    public function insert_record_raw($table, $params, $returnid=true, $bulk=false, $customsequence=false) {
        if (!is_array($params)) {
            $params = (array)$params;
        }

        if ($customsequence) {
            if (!isset($params['id'])) {
                return false;
            }
            $returnid = false;
        } else {
            unset($params['id']);
        }

        if (empty($params)) {
            return false;
        }

        $this->writes++;

        $fields = implode(',', array_keys($params));
        $qms    = array_fill(0, count($params), '?');
        $qms    = implode(',', $qms);

        $sql = "INSERT INTO {$this->prefix}$table ($fields) VALUES($qms)";

        if (!$rs = $this->adodb->Execute($sql, $params)) {
            $this->report_error($sql, $params);
            return false;
        }
        if (!$returnid) {
            return true;
        }
        if ($id = $this->adodb->Insert_ID()) {
            return (int)$id;
        }
        return false;
    }

    /**
     * Update record in database, as fast as possible, no safety checks, lobs not supported.
     * @param string $table name
     * @param mixed $params data record as object or array
     * @param bool true means repeated updates expected
     * @return bool success
     */
    public function update_record_raw($table, $params, $bulk=false) {
        if (!is_array($params)) {
            $params = (array)$params;
        }
        if (!isset($params['id'])) {
            return false;
        }
        $id = $params['id'];
        unset($params['id']);

        if (empty($params)) {
            return false;
        }

        $this->writes++;

        $sets = array();
        foreach ($params as $field=>$value) {
            $sets[] = "$field = ?";
        }

        $params[] = $id; // last ? in WHERE condition

        $sets = implode(',', $sets);
        $sql = "UPDATE {$this->prefix}$table SET $sets WHERE id=?";

        if (!$rs = $this->adodb->Execute($sql, $params)) {
            $this->report_error($sql, $params);
            return false;
        }
        return true;
    }

    /**
     * Delete one or more records from a table
     *
     * @param string $table The database table to be checked against.
     * @param string $select A fragment of SQL to be used in a where clause in the SQL call (used to define the selection criteria).
     * @param array $params array of sql parameters
     * @return returns success.
     */
    public function delete_records_select($table, $select, array $params=null) {
        if ($select) {
            $select = "WHERE $select";
        }
        $sql = "DELETE FROM {$this->prefix}$table $select";

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        $this->writes++;

        $result = false;
        if ($rs = $this->adodb->Execute($sql, $params)) {
            $result = true;
            $rs->Close();
        } else {
            $this->report_error($sql, $params);
        }
        return $result;
    }

    /**
     * Get a number of records as an moodle_recordset.  $sql must be a complete SQL query.
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
     * @return mixed an moodle_recorset object, or false if an error occured.
     */
    public function get_recordset_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        $this->reads++;

        if ($limitfrom || $limitnum) {
            ///Special case, 0 must be -1 for ADOdb
            $limitfrom = empty($limitfrom) ? -1 : $limitfrom;
            $limitnum  = empty($limitnum) ? -1 : $limitnum;
            $rs = $this->adodb->SelectLimit($sql, $limitnum, $limitfrom, $params);
        } else {
            $rs = $this->adodb->Execute($sql, $params);
        }
        if (!$rs) {
            $this->report_error($sql, $params);
            return false;
        }

        return $this->create_recordset($rs);
    }

    protected function create_recordset($rs) {
        return new adodb_moodle_recordset($rs);
    }

    /**
     * Get a number of records as an array of objects.
     *
     * Return value as for @see function get_records.
     *
     * @param string $sql the SQL select query to execute. The first column of this SELECT statement
     *   must be a unique value (usually the 'id' field), as it will be used as the key of the
     *   returned array.
     * @param array $params array of sql parameters
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return mixed an array of objects, or empty array if no records were found, or false if an error occured.
     */
    public function get_records_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        $this->reads++;

        if ($limitfrom || $limitnum) {
            ///Special case, 0 must be -1 for ADOdb
            $limitfrom = empty($limitfrom) ? -1 : $limitfrom;
            $limitnum  = empty($limitnum) ? -1 : $limitnum;
            $rs = $this->adodb->SelectLimit($sql, $limitnum, $limitfrom, $params);
        } else {
            $rs = $this->adodb->Execute($sql, $params);
        }
        if (!$rs) {
            $this->report_error($sql, $params);
            return false;
        }
        $return = $this->adodb_recordset_to_array($rs);
        $rs->close();
        return $return;
    }

    /**
     * Selects rows and return values of first column as array.
     *
     * @param string $sql The SQL query
     * @param array $params array of sql parameters
     * @return mixed array of values or false if an error occured
     */
    public function get_fieldset_sql($sql, array $params=null) {
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        $this->reads++;

        if (!$rs = $this->adodb->Execute($sql, $params)) {
            $this->report_error($sql, $params);
            return false;
        }
        $results = array();
        while (!$rs->EOF) {
            $res = reset($rs->fields);
            $results[] = $res;
            $rs->MoveNext();
        }
        $rs->Close();
        return $results;
    }

    protected function adodb_recordset_to_array($rs) {
        $debugging = debugging('', DEBUG_DEVELOPER);

        if ($rs->EOF) {
            // BIIIG change here - return empty array() if nothing found (2.0)
            return array();
        }

        $objects = array();
    /// First of all, we are going to get the name of the first column
    /// to introduce it back after transforming the recordset to assoc array
    /// See http://docs.moodle.org/en/XMLDB_Problems, fetch mode problem.
        $firstcolumn = $rs->FetchField(0);
    /// Get the whole associative array
        if ($records = $rs->GetAssoc(true)) {
            foreach ($records as $key => $record) {
                $record = array($firstcolumn->name=>$key) + $record; /// Re-add the assoc field  (as FIRST element since 2.0)
                if ($debugging && array_key_exists($key, $objects)) {
                    debugging("Did you remember to make the first column something unique in your call to get_records? Duplicate value '$key' found in column '".$firstcolumn->name."'.", DEBUG_DEVELOPER);
                }
                $objects[$key] = (object) $record; /// To object
            }
            return $objects;
    /// Fallback in case we only have 1 field in the recordset. MDL-5877
        } else if ($rs->_numOfFields == 1 and $records = $rs->GetRows()) {
            foreach ($records as $key => $record) {
                if ($debugging && array_key_exists($record[$firstcolumn->name], $objects)) {
                    debugging("Did you remember to make the first column something unique in your call to get_records? Duplicate value '".$record[$firstcolumn->name]."' found in column '".$firstcolumn->name."'.", DEBUG_DEVELOPER);
                }
                $objects[$record[$firstcolumn->name]] = (object) $record; /// The key is the first column value (like Assoc)
            }
            return $objects;
        } else {
            // weird error?
            return false;
        }
    }

    public function sql_substr() {
        return $this->adodb->substr;
    }

    public function sql_concat() {
        $args = func_get_args();
        return call_user_func_array(array($this->adodb, 'Concat'), $args);
    }

    public function sql_concat_join($separator="' '", $elements=array()) {
        // Intersperse $elements in the array.
        // Add items to the array on the fly, walking it
        // _backwards_ splicing the elements in. The loop definition
        // should skip first and last positions.
        for ($n=count($elements)-1; $n > 0 ; $n--) {
            array_splice($elements, $n, 0, $separator);
        }
        return call_user_func_array(array($this->adodb, 'Concat'), $elements);
    }



    public function begin_sql() {
        $this->adodb->BeginTrans();
        return true;
    }
    public function commit_sql() {
        $this->adodb->CommitTrans();
        return true;
    }
    public function rollback_sql() {
        $this->adodb->RollbackTrans();
        return true;
    }

    /**
     * Very ugly hack which emulates bound parameters in mssql queries
     * where params not supported (UpdateBlob) :-(
     */
    protected function emulate_bound_params($sql, array $params=null) {
        if (empty($params)) {
            return $sql;
        }
        /// ok, we have verified sql statement with ? and correct number of params
        $return = strtok($sql, '?');
        foreach ($params as $param) {
            if (is_bool($param)) {
                $return .= (int)$param;
            } else if (is_null($param)) {
                $return .= 'NULL';
            } else if (is_numeric($param)) {
                $return .= $param;
            } else {
                $param = $this->adodb->qstr($param);
                $return .= "$param";
            }
            $return .= strtok('?');
        }
        return $return;
    }
}
