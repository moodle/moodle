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

    public function connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, array $dboptions=null) {
        global $CFG;

        $driverstatus = $this->driver_installed();

        if ($driverstatus !== true) {
            throw new dml_exception('dbdriverproblem', $driverstatus);
        }

        ob_start();

        $this->store_settings($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions);

        $this->preconfigure_dbconnection();

        require_once($CFG->libdir.'/adodb/adodb.inc.php');

        $this->adodb = ADONewConnection($this->get_dbtype());

        // See MDL-6760 for why this is necessary. In Moodle 1.8, once we start using NULLs properly,
        // we probably want to change this value to ''.
        $this->adodb->null2null = 'A long random string that will never, ever match something we want to insert into the database, I hope. \'';


        if (!empty($this->dboptions['dbpersist'])) {    // Use persistent connection
            $connected = $this->adodb->PConnect($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
        } else {                                                     // Use single connection
            $connected = $this->adodb->Connect($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
        }

        $dberr = ob_get_contents();
        ob_end_clean();

        if (!$connected) {
            throw new dml_connection_exception($dberr);
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
        $this->query_start("--adodb-ServerInfo", null, SQL_QUERY_AUX);
        $info = $this->adodb->ServerInfo();
        $this->query_end(true);
        return $info;
    }

    /**
     * Return tables in database WITHOUT current prefix
     * @return array of table names in lowercase and without prefix
     */
    public function get_tables() {
        $this->query_start("--adodb-MetaTables", null, SQL_QUERY_AUX);
        $metatables = $this->adodb->MetaTables();
        $this->query_end(true);

        $tables = array();

        foreach ($metatables as $table) {
            $table = strtolower($table);
            if (empty($this->prefix) || strpos($table, $this->prefix) === 0) {
                $tablename = substr($table, strlen($this->prefix));
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
        $this->query_start("--adodb-MetaIndexes", null, SQL_QUERY_AUX);
        $indexes = $this->adodb->MetaIndexes($this->prefix.$table);
        $this->query_end(true);

        if (!$indexes) {
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

        $this->query_start("--adodb-MetaColumns", null, SQL_QUERY_AUX);
        $columns = $this->adodb->MetaColumns($this->prefix.$table);
        $this->query_end(true);

        if (!$columns) {
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
     * Do NOT use in code, to be used by database_manager only!
     * @param string $sql query
     * @return bool true
     * @throws dml_exception if error
     */
    public function change_database_structure($sql) {
        $this->reset_columns();

        $this->query_start($sql, null, SQL_QUERY_STRUCTURE);
        $rs = $this->adodb->Execute($sql);
        $this->query_end($rs);

        $rs->Close();

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
        $rs = $this->adodb->Execute($sql, $params);
        $this->query_end($rs);

        $rs->Close();

        return true;
    }

    /**
     * Insert new record into database, as fast as possible, no safety checks, lobs not supported.
     * @param string $table name
     * @param mixed $params data record as object or array
     * @param bool $returnit return it of inserted record
     * @param bool $bulk true means repeated inserts expected
     * @param bool $customsequence true if 'id' included in $params, disables $returnid
     * @return mixed true or new id
     * @throws dml_exception if error
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

        $sql = "INSERT INTO {$this->prefix}$table ($fields) VALUES($qms)";

        $this->query_start($sql, $params, SQL_QUERY_INSERT);
        $rs = $this->adodb->Execute($sql, $params);
        $this->query_end($rs);
        $rs->Close();

        if (!$returnid) {
            return true;
        }

        if (!$id = $this->adodb->Insert_ID()) {
            throw new dml_write_exception('unknown error fetching inserted id');
        }

        return (int)$id;
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
        $sql = "UPDATE {$this->prefix}$table SET $sets WHERE id=?";

        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $rs = $this->adodb->Execute($sql, $params);
        $this->query_end($rs);
        $rs->Close();

        return true;
    }

    /**
     * Delete one or more records from a table
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
        $rs = $this->adodb->Execute($sql, $params);
        $this->query_end($rs);
        $rs->Close();

        return true;
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
     * @return object moodle_recordset instance
     * @throws dml_exception if error
     */
    public function get_recordset_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        if ($limitfrom || $limitnum) {
            ///Special case, 0 must be -1 for ADOdb
            $limitfrom = empty($limitfrom) ? -1 : $limitfrom;
            $limitnum  = empty($limitnum) ? -1 : $limitnum;
            $this->query_start($sql." --LIMIT $limitfrom, $limitnum", $params, SQL_QUERY_SELECT);
            $rs = $this->adodb->SelectLimit($sql, $limitnum, $limitfrom, $params);
            $this->query_end($rs);
            return $this->create_recordset($rs);
        }

        $this->query_start($sql, $params, SQL_QUERY_SELECT);
        $rs = $this->adodb->Execute($sql, $params);
        $this->query_end($rs);
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
     * @return array of objects indexed by first column
     * @throws dml_exception if error
     */
    public function get_records_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        $rs = null;
        if ($limitfrom || $limitnum) {
            ///Special case, 0 must be -1 for ADOdb
            $limitfrom = empty($limitfrom) ? -1 : $limitfrom;
            $limitnum  = empty($limitnum) ? -1 : $limitnum;
            $this->query_start($sql." --LIMIT $limitfrom, $limitnum", $params, SQL_QUERY_SELECT);
            $rs = $this->adodb->SelectLimit($sql, $limitnum, $limitfrom, $params);
            $this->query_end($rs);
        } else {
            $this->query_start($sql, $params, SQL_QUERY_SELECT);
            $rs = $this->adodb->Execute($sql, $params);
            $this->query_end($rs);
        }
        $return = $this->adodb_recordset_to_array($rs);
        $rs->Close();

        return $return;
    }

    /**
     * Selects rows and return values of first column as array.
     *
     * @param string $sql The SQL query
     * @param array $params array of sql parameters
     * @return mixed array of values
     * @throws dml_exception if error
     */
    public function get_fieldset_sql($sql, array $params=null) {
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        $this->query_start($sql, $params, SQL_QUERY_SELECT);
        $rs = $this->adodb->Execute($sql, $params);
        $this->query_end($rs);

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
                $objects[$key] = (object) $record; /// To object
            }
            if ($debugging) {
                if (count($objects) != $rs->_numOfRows) {
                    debugging("Did you remember to make the first column something unique in your call to get_records? Duplicate values found in column '".$firstcolumn->name."'.", DEBUG_DEVELOPER);
                }
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
