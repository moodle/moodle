<?php  //$Id$

require_once($CFG->libdir.'/dml/moodle_database.php');
require_once($CFG->libdir.'/dml/adodb_moodle_database.php');

/**
 * Postgresql database class using adodb backend
 * @package dml
 */
class postgres7_adodb_moodle_database extends adodb_moodle_database {

    public function connect($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix, array $dboptions=null) {
        if ($prefix == '' and !$this->external) {
            //Enforce prefixes for everybody but mysql
            print_error('prefixcannotbeempty', 'error', '', $this->get_dbfamily());
        }
        return parent::connect($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix, $dboptions);
    }

    /**
     * Detects if all needed PHP stuff installed.
     * Do not connect to connect to db if this test fails.
     * @return mixed true if ok, string if something
     */
    public function driver_installed() {
        if (!extension_loaded('pgsql')) {
            return get_string('pgsqlextensionisnotpresentinphp', 'install');
        }
        return true;
    }

    protected function preconfigure_dbconnection() {
        if (!defined('ADODB_ASSOC_CASE')) {
            define ('ADODB_ASSOC_CASE', 2);
        }
    }

    protected function configure_dbconnection() {
        $this->adodb->SetFetchMode(ADODB_FETCH_ASSOC);
        $this->adodb->Execute("SET NAMES 'utf8'");

        return true;
    }

    /**
     * Returns database family type
     * @return string db family name (mysql, postgres, mssql, oracle, etc.)
     */
    public function get_dbfamily() {
        return 'postgres';
    }

    /**
     * Returns database type
     * @return string db type mysql, mysqli, postgres7
     */
    protected function get_dbtype() {
        return 'postgres7';
    }

    /**
     * Returns localised database description
     * Note: can be used before connect()
     * @return string
     */
    public function get_configuration_hints() {
        return get_string('databasesettingssub_postgres7', 'install');
    }

    /**
     * Returns db related part of config.php
     * Note: can be used before connect()
     * @return string
     */
    public function export_dbconfig($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix, array $dboptions=null) {
        $this->store_settings($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix, $dboptions);

        $cfg = new stdClass();
        $cfg->dbtype     = $this->get_dbtype();
        $cfg->dblibrary  = $this->get_dblibrary();
        if ($this->dbhost == 'localhost' or $this->dbhost == '127.0.0.1') {
            $cfg->dbhost = "user='{$this->dbuser}' password='{$this->dbpass}' dbname='{$this->dbname}'";
            $cfg->dbname = '';
            $cfg->dbuser = '';
            $cfg->dbpass = '';
        } else {
            $cfg->dbhost = $this->dbhost;
            $cfg->dbname = $this->dbname;
            $cfg->dbuser = $this->dbuser;
            $cfg->dbpass = $this->dbpass;
        }
        $cfg->prefix     = $this->prefix;
        if ($this->dboptions) {
            $cfg->dboptions = $this->dboptions;
        }

        return $cfg;
    }

    /**
     * Returns supported query parameter types
     * @return bitmask
     */
    protected function allowed_param_types() {
        return SQL_PARAMS_QM;
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
            if ($column->has_default) {
                if ($pos = strpos($column->default_value, '::')) {
                    if (strpos($column->default_value, "'") === 0) {
                        $column->default_value = substr($column->default_value, 1, $pos-2);
                    } else {
                        $column->default_value = substr($column->default_value, 0, $pos);
                    }
                }
            } else {
                $column->default_value = null;
            }
            $this->columns[$table][$column->name] = new database_column_info($column);
        }

        return $this->columns[$table];
    }

    /**
     * This method will introspect inside DB to detect it it's a UTF-8 DB or no
     * Used from setup.php to set correctly "set names" when the installation
     * process is performed without the initial and beautiful installer
     * @return bool true if db in unicode mode
     */
    function setup_is_unicodedb() {
    /// Get PostgreSQL server_encoding value
        $this->reads++;
        $rs = $this->adodb->Execute("SHOW server_encoding");
        if ($rs && !$rs->EOF) {
            $encoding = $rs->fields['server_encoding'];
            if (strtoupper($encoding) == 'UNICODE' || strtoupper($encoding) == 'UTF8') {
                return true;
            }
        }
        return false;
    }

    /**
     * Insert new record into database, as fast as possible, no safety checks, lobs not supported.
     * (overloaded from adodb_moodle_database because of sequence numbers
     * @param string $table name
     * @param mixed $params data record as object or array
     * @param bool $returnit return it of inserted record
     * @param bool $bulk true means repeated inserts expected
     * @return mixed success or new id
     */
    public function insert_record_raw($table, $params, $returnid=true, $bulk=false) {
    /// Postgres doesn't have the concept of primary key built in
    /// and will return the OID which isn't what we want.
    /// The efficient and transaction-safe strategy is to
    /// move the sequence forward first, and make the insert
    /// with an explicit id.

        if (!is_array($params)) {
            $params = (array)$params;
        }
        unset($params['id']);
        if ($returnid) {
            $this->reads++;
            $seqname = "{$this->prefix}{$table}_id_seq";
            if ($nextval = $this->adodb->GenID($seqname)) {
                $params['id'] = (int)$nextval;
            }
        }

        if (empty($params)) {
            return false;
        }

        $fields = implode(',', array_keys($params));
        $qms    = array_fill(0, count($params), '?');
        $qms    = implode(',', $qms);

        $sql = "INSERT INTO {$this->prefix}$table ($fields) VALUES($qms)";

        $this->writes++;

        if (!$rs = $this->adodb->Execute($sql, $params)) {
            $this->report_error($sql, $params);
            return false;
        }
        if (!$returnid) {
            return true;
        }
        if (!empty($params['id'])) {
            return (int)$params['id'];
        }

        $oid = $this->adodb->Insert_ID();

        $this->reads++;

        // try to get the primary key based on id
        $sql = "SELECT id FROM {$this->prefix}$table WHERE oid = $oid";
        if ( ($rs = $this->adodb->Execute($sql))
             && ($rs->RecordCount() == 1) ) {
            trigger_error("Retrieved id using oid on table $table because we could not find the sequence.");
            return (integer)reset($rs->fields);
        }
        trigger_error("Failed to retrieve primary key after insert: $sql");
        return false;
    }

    /**
     * Insert a record into a table and return the "id" field if required,
     * Some conversions and safety checks are carried out. Lobs are supported.
     * If the return ID isn't required, then this just reports success as true/false.
     * $data is an object containing needed data
     * @param string $table The database table to be inserted into
     * @param object $data A data object with values for one or more fields in the record
     * @param bool $returnid Should the id of the newly created record entry be returned? If this option is not requested then true/false is returned.
     * @param bool $bulk true means repeated inserts expected
     * @return mixed success or new ID
     */
    public function insert_record($table, $dataobject, $returnid=true, $bulk=false) {
        //TODO: add support for blobs BYTEA
        if (!is_object($dataobject)) {
            $dataobject = (object)$dataobject;
        }

        $columns = $this->get_columns($table);
        unset($dataobject->id);
        $cleaned = array();
        $blobs   = array();

        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) {
                continue;
            }
            $column = $columns[$field];
            if ($column->meta_type == 'B') {
                if (is_null($value)) {
                    $cleaned[$field] = null;
                } else {
                    $blobs[$field] = $value;
                    $cleaned[$field] = '@#BLOB#@';
                }
                continue;

            } else if (is_bool($value)) {
                $value = (int)$value; // prevent false '' problems

            } else if ($value === '') {
                if ($column->meta_type == 'I' or $column->meta_type == 'F' or $column->meta_type == 'N') {
                    $value = 0; // prevent '' problems in numeric fields
                }
            }

            $cleaned[$field] = $value;
        }

        if (empty($cleaned)) {
            return false;
        }

        if (empty($blobs)) {
            return $this->insert_record_raw($table, $cleaned, $returnid, $bulk);
        }

        if (!$id = $this->insert_record_raw($table, $cleaned, true, $bulk)) {
            return false;
        }

        foreach ($blobs as $key=>$value) {
            $this->writes++;
            if (!$this->adodb->UpdateBlob($this->prefix.$table, $key, $value, "id = $id", 'BLOB')) { // adodb does not use bound parameters for blob updates :-(
                return false;
            }
        }

        return ($returnid ? $id : true);
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
        //TODO: add support for blobs BYTEA
        if (!is_object($dataobject)) {
            $dataobject = (object)$dataobject;
        }

        if (!isset($dataobject->id) ) {
            return false;
        }
        $id = $dataobject->id;

        $columns = $this->get_columns($table);
        $cleaned = array();
        $blobs   = array();

        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) {
                continue;
            }
            $column = $columns[$field];
            if ($column->meta_type == 'B') {
                if (is_null($value)) {
                    $cleaned[$field] = null;
                } else {
                    $blobs[$field] = $value;
                    $cleaned[$field] = '@#BLOB#@';
                }
                continue;

            } else if (is_bool($value)) {
                $value = (int)$value; // prevent "false" problems

            } else if ($value === '') {
                if ($column->meta_type == 'I' or $column->meta_type == 'F' or $column->meta_type == 'N') {
                    $value = 0; // prevent '' problems in numeric fields
                }
            }
            $cleaned[$field] = $value;
        }

        if (!$this->update_record_raw($table, $cleaned, $bulk)) {
            return false;
        }

        if (empty($blobs)) {
            return true;
        }

        foreach ($blobs as $key=>$value) {
            $this->writes++;
            if (!$this->adodb->UpdateBlob($this->prefix.$table, $key, $value, "id = $id", 'BLOB')) { // adodb does not use bound parameters for blob updates :-(
                return false;
            }
        }

        return true;
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
        $params = (array)$params;
        list($select, $params, $type) = $this->fix_sql_params($select, $params);

        $columns = $this->get_columns($table);
        $column = $columns[$newfield];

        if ($column->meta_type == 'B') {
            /// update blobs and return
            $select = $this->emulate_bound_params($select, $params); // adodb does not use bound parameters for blob updates :-(
            $this->writes++;
            if (!$this->adodb->UpdateBlob($this->prefix.$table, $newfield, $newvalue, $select, 'BLOB')) {
                return false;
            }
            return true;
        }

        if ($select) {
            $select = "WHERE $select";
        }

        /// normal field update
        if (is_null($newvalue)) {
            $newfield = "$newfield = NULL";
        } else {
            if (is_bool($newvalue)) {
                $newvalue = (int)$newvalue; // prevent "false" problems
            } else if ($newvalue === '') {
                if ($column->meta_type == 'I' or $column->meta_type == 'F' or $column->meta_type == 'N') {
                    $newvalue = 0; // prevent '' problems in numeric fields
                }
            }

            $newfield = "$newfield = ?";
            array_unshift($params, $newvalue); // add as first param
        }
        $sql = "UPDATE {$this->prefix}$table SET $newfield $select";

        $this->writes++;

        if (!$rs = $this->adodb->Execute($sql, $params)) {
            $this->report_error($sql, $params);
            return false;
        }

        return true;
    }

    public function sql_ilike() {
        return 'ILIKE';
    }

    public function sql_concat() {
        $args = func_get_args();
    /// PostgreSQL requires at least one char element in the concat, let's add it
    /// here (at the beginning of the array) until ADOdb fixes it
        if (is_array($args)) {
            array_unshift($args , "''");
        }
        return call_user_func_array(array($this->adodb, 'Concat'), $args);
    }

    public function sql_bitxor($int1, $int2) {
        return '(' . $this->sql_bitor($int1, $int2) . ' - ' . $this->sql_bitand($int1, $int2) . ')';
    }

    public function sql_cast_char2int($fieldname, $text=false) {
        return ' CAST(' . $fieldname . ' AS INT) ';
    }

    public function sql_cast_char2real($fieldname, $text=false) {
        return " $fieldname::real ";
    }

    /**
     * Does this driver suppoer regex syntax when searching
     */
    public function sql_regex_supported() {
        return true;
    }

    /**
     * Return regex positive or negative match sql
     * @param bool $positivematch
     * @return string or empty if not supported
     */
    public function sql_regex($positivematch=true) {
        return $positivematch ? '~*' : '!~*';
    }

    /**
     * Reset a sequence to the id field of a table.
     * @param string $table name of table
     * @return bool success
     */
    public function reset_sequence($table) {
        // From http://www.postgresql.org/docs/7.4/static/sql-altersequence.html
        if (!$this->get_manager()->table_exists($table)) {
            return false;
        }
        $value = (int)$this->get_field_sql('SELECT MAX(id) FROM {'.$table.'}');
        $value++;
        return $this->change_database_structure("ALTER SEQUENCE $this->prefix{$table}_id_seq RESTART WITH $value");
    }
}