<?php  //$Id$

require_once($CFG->libdir.'/dml/moodle_database.php');
require_once($CFG->libdir.'/dml/adodb_moodle_database.php');
require_once($CFG->libdir.'/dml/oci8po_adodb_moodle_recordset.php');

/**
 * Oracle database class using adodb backend
 * @package dml
 */
class oci8po_adodb_moodle_database extends adodb_moodle_database {

    public function connect($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix, array $dboptions=null) {
        if ($prefix == '' and !$this->external) {
            //Enforce prefixes for everybody but mysql
            print_error('prefixcannotbeempty', 'error', '', $this->get_dbfamily());
        }
        if (!$this->external and strlen($prefix) > 2) {
            //Max prefix length for Oracle is 2cc
            $a = (object)array('dbfamily'=>'oracle', 'maxlength'=>2);
            print_error('prefixtoolong', 'error', '', $a);
        }
        return parent::connect($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix, $dboptions);
    }

    /**
     * Detects if all needed PHP stuff installed.
     * Do not connect to connect to db if this test fails.
     * @return mixed true if ok, string if something
     */
    public function driver_installed() {
        if (!extension_loaded('oci8')) {
            return get_string('ociextensionisnotpresentinphp', 'install');
        }
        return true;
    }

    protected function preconfigure_dbconnection() {
        if (!defined('ADODB_ASSOC_CASE')) {
            define ('ADODB_ASSOC_CASE', 0); /// Use lowercase fieldnames for ADODB_FETCH_ASSOC
                                            /// (only meaningful for oci8po, it's the default
                                            /// for other DB drivers so this won't affect them)
        }
        /// Row prefetching uses a bit of memory but saves a ton
        /// of network latency. With current AdoDB and PHP, only
        /// Oracle uses this setting.
        if (!defined('ADODB_PREFETCH_ROWS')) {
            define ('ADODB_PREFETCH_ROWS', 1000);
        }
    }

    protected function configure_dbconnection() {
        $this->adodb->SetFetchMode(ADODB_FETCH_ASSOC);

        /// No need to set charset. It must be specified by the NLS_LANG env. variable
        /// Now set the decimal separator to DOT, Moodle & PHP will always send floats to
        /// DB using DOTS. Manually introduced floats (if using other characters) must be
        /// converted back to DOTs (like gradebook does)
        $this->adodb->Execute("ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.,'");

        return true;
    }

    /**
     * Returns database family type
     * @return string db family name (mysql, postgres, mssql, oracle, etc.)
     */
    public function get_dbfamily() {
        return 'oracle';
    }

    /**
     * Returns database type
     * @return string db type mysql, mysqli, postgres7
     */
    protected function get_dbtype() {
        return 'oci8po';
    }

    /**
     * Returns localised database description
     * Note: can be used before connect()
     * @return string
     */
    public function get_configuration_hints() {
        $str = get_string('databasesettingssub_oci8po', 'install');
        $str .= "<p style='text-align:right'><a href=\"javascript:void(0)\" ";
        $str .= "onclick=\"return window.open('http://docs.moodle.org/en/Installing_Oracle_for_PHP')\"";
        $str .= ">";
        $str .= '<img src="pix/docs.gif' . '" alt="Docs" class="iconhelp" />';
        $str .= get_string('moodledocslink', 'install') . '</a></p>';
        return $str;
    }

    /**
     * Returns supported query parameter types
     * @return bitmask
     */
    protected function allowed_param_types() {
        return SQL_PARAMS_QM;
    }

    /**
     * This method will introspect inside DB to detect it it's a UTF-8 DB or no
     * Used from setup.php to set correctly "set names" when the installation
     * process is performed without the initial and beautiful installer
     * @return bool true if db in unicode mode
     */
    function setup_is_unicodedb() {
        $this->reads++;
        $rs = $this->adodb->Execute("SELECT parameter, value FROM nls_database_parameters where parameter = 'NLS_CHARACTERSET'");
        if ($rs && !$rs->EOF) {
            $encoding = $rs->fields['value'];
            if (strtoupper($encoding) == 'AL32UTF8') {
                return true;
            }
        }
        return false;
    }

    /**
     * Selects rows and return values of first column as array.
     *
     * @param string $sql The SQL query
     * @param array $params array of sql parameters
     * @return mixed array of values or false if an error occured
     */
    public function get_fieldset_sql($sql, array $params=null) {
        if ($result = parent::get_fieldset_sql($sql, $params)) {
            array_walk($result, array('oci8po_adodb_moodle_database', 'onespace2empty'));
        }
        return $result;
    }

    protected function create_recordset($rs) {
        return new oci8po_adodb_moodle_recordset($rs);
    }

    protected function adodb_recordset_to_array($rs) {
        /// Really DIRTY HACK for Oracle - needed because it can not see difference from NULL and ''
        /// this can not be removed even if we chane db defaults :-(
        if ($result = parent::adodb_recordset_to_array($rs)) {
            foreach ($result as $key=>$row) {
                $row = (array)$row;
                array_walk($row, array('oci8po_adodb_moodle_database', 'onespace2empty'));
                $result[$key] = (object)$row;
            }
        }

        return $result;
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
        return '((' . $int1 . ') # (' . $int2 . '))';
    }

    function sql_null_from_clause() {
        return ' FROM dual';
    }

    public function sql_cast_char2int($fieldname, $text=false) {
        if (!$text) {
            return ' CAST(' . $fieldname . ' AS INT) ';
        } else {
            return ' CAST(' . $this->sql_compare_text($fieldname) . ' AS INT) ';
        }
    }

    public function sql_order_by_text($fieldname, $numchars=32) {
        return 'dbms_lob.substr(' . $fieldname . ', ' . $numchars . ',1)';
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
        if (!is_object($dataobject)) {
            $dataobject = (object)$dataobject;
        }

        if (! isset($dataobject->id) ) {
            return false;
        }

        $columns = $this->get_columns($table);
        $cleaned = array();
        $clobs   = array();
        $blobs   = array();

        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) { /// Non-existing table field, skip it
                continue;
            }
        /// Apply Oracle dirty hack to value, to have "correct" empty values for Oracle
            $value = $this->oracle_dirty_hack($table, $field, $value);

        /// Get column metadata
            $column = $columns[$field];
            if ($column->meta_type == 'B') { /// BLOB columns need to be updated apart
                if (!is_null($value)) {      /// If value not null, add it to the list of BLOBs to update later
                    $blobs[$field] = $value;
                    continue;                /// We don't want this column to be processed by update_record_raw() at all.
                    /// $value = 'empty_blob()'; /// Set the default value to be inserted in first instance. Not needed to initialize lob storage in updates
                }

            } else if ($column->meta_type == 'X' && strlen($value) > 4000) { /// CLOB columns need to be updated apart (if lenght > 4000)
                if (!is_null($value)) {      /// If value not null, add it to the list of BLOBs to update later
                    $blobs[$field] = $value;
                    continue;                /// We don't want this column to be processed by update_record_raw() at all.
                    /// $value = 'empty_clob()'; /// Set the default value to be inserted in first instance. Not needed to initialize lob storage in updates
                }

            } else if (is_bool($value)) {
                $value = (int)$value; // prevent "false" problems

            } else if ($value === '' || $value === ' ') {
                if ($column->meta_type == 'I' or $column->meta_type == 'F' or $column->meta_type == 'N') {
                    $value = 0; // prevent '' problems in numeric fields
                }
            }
            $cleaned[$field] = $value;
        }

        if (empty($cleaned)) {
            return false;
        }

        if (empty($blobs) && empty($clobs)) { /// Without BLOBs and CLOBs, execute the raw update and return
            return $this->update_record_raw($table, $cleaned, $bulk);
        }

    /// We have BLOBs or CLOBs to postprocess, execute the raw update and then update blobs
        if (!$this->update_record_raw($table, $cleaned, $bulk)) {
            return false;
        }

        foreach ($blobs as $key=>$value) {
            $this->writes++;
            if (!$this->adodb->UpdateBlob($this->prefix.$table, $key, $value, "id = {$dataobject->id}")) {
                return false;
            }
        }

        foreach ($clobs as $key=>$value) {
            $this->writes++;
            if (!$this->adodb->UpdateClob($this->prefix.$table, $key, $value, "id = {$dataobject->id}")) {
                return false;
            }
        }

        return true;
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
        if (!is_object($dataobject)) {
            $dataobject = (object)$dataobject;
        }

        unset($dataobject->id);

        $columns = $this->get_columns($table);
        $cleaned = array();
        $blobs = array();
        $clobs = array();

        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) { /// Non-existing table field, skip it
                continue;
            }
        /// Apply Oracle dirty hack to value, to have "correct" empty values for Oracle
            $value = $this->oracle_dirty_hack($table, $field, $value);

        /// Get column metadata
            $column = $columns[$field];
            if ($column->meta_type == 'B') { /// BLOBs columns need to be updated apart
                if (!is_null($value)) {      /// If value not null, add it to the list of BLOBs to update later
                    $blobs[$field] = $value;
                    $value = 'empty_blob()'; /// Set the default value to be inserted (preparing lob storage for next update)
                }

            } else if ($column->meta_type == 'X' && strlen($value) > 4000) { /// CLOB columns need to be updated apart (if lenght > 4000)
                if (!is_null($value)) {      /// If value not null, add it to the list of BLOBs to update later
                    $clobs[$field] = $value;
                    $value = 'empty_clob()'; /// Set the default value to be inserted (preparing lob storage for next update)
                }

            } else if (is_bool($value)) {
                $value = (int)$value; // prevent "false" problems

            } else if ($value === '' || $value === ' ') {
                if ($column->meta_type == 'I' or $column->meta_type == 'F' or $column->meta_type == 'N') {
                    $value = 0; // prevent '' problems in numeric fields
                }
            }
            $cleaned[$field] = $value;
        }

        if (empty($cleaned)) {
            return false;
        }

        if (empty($blobs) && empty($clobs)) { /// Without BLOBs and CLOBs, execute the raw insert and return
            return $this->insert_record_raw($table, $cleaned, $returnid, $bulk);
        }

        /// We have BLOBs or CLOBs to postprocess, insert the raw record fetching the id to be used later
        if (!$id = $this->insert_record_raw($table, $cleaned, true, $bulk)) {
            return false;
        }

        foreach ($blobs as $key=>$value) {
            $this->writes++;
            if (!$this->adodb->UpdateBlob($this->prefix.$table, $key, $value, "id = $id")) {
                return false;
            }
        }

        foreach ($clobs as $key=>$value) {
            $this->writes++;
            if (!$this->adodb->UpdateClob($this->prefix.$table, $key, $value, "id = $id")) {
                return false;
            }
        }

        return ($returnid ? $id : true);
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

        if (is_null($params)) {
            $params = array();
        }
        list($select, $params, $type) = $this->fix_sql_params($select, $params);

    /// Apply Oracle dirty hack to value, to have "correct" empty values for Oracle
        $newvalue = $this->oracle_dirty_hack($table, $newfield, $newvalue);

    /// Get column metadata
        $columns = $this->get_columns($table);
        $column = $columns[$newfield];

        if ($column->meta_type == 'B') { /// If the column is a BLOB
        /// Update BLOB column and return
            $select = $this->emulate_bound_params($select, $params); // adodb does not use bound parameters for blob updates :-(
            $this->writes++;
            return $this->adodb->UpdateBlob($this->prefix.$table, $newfield, $newvalue, $select);
        }

        if ($column->meta_type == 'X' && strlen($newvalue) > 4000) { /// If the column is a CLOB with lenght > 4000
        /// Update BLOB column and return
            $select = $this->emulate_bound_params($select, $params); // adodb does not use bound parameters for blob updates :-(
            $this->writes++;
            return $this->adodb->UpdateClob($this->prefix.$table, $newfield, $newvalue, $select);
        }

    /// Arrived here, normal update (without BLOBs)
        if (is_null($newvalue)) {
            $newfield = "$newfield = NULL";
        } else {
            if (is_bool($newvalue)) {
                $newvalue = (int)$newvalue; // prevent "false" problems
            } else if ($newvalue === '' || $newvalue === ' ') {
                if ($column->meta_type == 'I' or $column->meta_type == 'F' or $column->meta_type == 'N') {
                    $newvalue = 0; // prevent '' problems in numeric fields
                }
            }

            $newfield = "$newfield = ?";
            array_unshift($params, $newvalue); // add as first param
        }
        $sql = "UPDATE {$this->prefix}$table SET $newfield WHERE $select";

        $this->writes++;
        if (!$rs = $this->adodb->Execute($sql, $params)) {
            $this->report_error($sql, $params);
            return false;
        }
        return true;
    }

    /**
     * Insert new record into database, as fast as possible, no safety checks, lobs not supported.
     * (overloaded from adodb_moodle_database because of sequence numbers and empty_blob()/empty_clob())
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

        if ($returnid) {
            $dbman = $this->get_manager();
            $xmldb_table = new xmldb_table($table);
            $this->reads++;
            $seqname = $dbman->find_sequence_name($xmldb_table);
            if (!$seqname) {
            /// Fallback, seqname not found, something is wrong. Inform and use the alternative getNameForObject() method
                $generator = $this->get_dbman()->generator;
                $generator->setPrefix($this->getPrefix());
                $seqname = $generator->getNameForObject($table, 'id', 'seq');
            }
            $this->reads++;
            if ($nextval = $this->adodb->GenID($seqname)) {
                $params['id'] = (int)$nextval;
            }
        }

        if (empty($params)) {
            return false;
        }

        $fields = implode(',', array_keys($params));
        $qms    = array();
    /// Look for 'empty_clob() and empty_blob() params to replace question marks properly
    /// Oracle requires those function calls on insert to prepare blob/clob storage, so we
    /// specify them as SQL, deleting them from parameters
        foreach ($params as $key=>$param) {
            if ($param === 'empty_blob()') {
                $qms[] = 'empty_blob()';
                unset($params[$key]);
            } else if ($param === 'empty_clob()') {
                $qms[] = 'empty_clob()';
                unset($params[$key]);
            } else {
                $qms[] = '?';
            }
        }
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
        return false;
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
    static function onespace2empty(&$item, $key=null) {
        $item = $item == ' ' ? '' : $item;
        return true;
    }

    /**
     * This function will handle all the records before being inserted/updated to DB for Oracle
     * installations. This is because the "special feature" of Oracle where the empty string is
     * equal to NULL and this presents a problem with all our currently NOT NULL default '' fields.
     *
     * Once Moodle DB will be free of this sort of false NOT NULLS, this hack could be removed safely
     *
     * Note that this function is 100% private and should be used, exclusively by DML functions
     * in this file. Also, this is considered a DIRTY HACK to be removed when possible. (stronk7)
     *
     * This function is private and must not be used outside this driver at all
     *
     * @param $table string the table where the record is going to be inserted/updated (without prefix)
     * @param $field string the field where the record is going to be inserted/updated
     * @param $value mixed the value to be inserted/updated
     */
    private function oracle_dirty_hack ($table, $field, $value) {

    /// Get metadata
        $columns = $this->get_columns($table);
        if (!isset($columns[$field])) {
            return $value;
        }
        $column = $columns[$field];

    /// For Oracle DB, empty strings are converted to NULLs in DB
    /// and this breaks a lot of NOT NULL columns currenty Moodle. In the future it's
    /// planned to move some of them to NULL, if they must accept empty values and this
    /// piece of code will become less and less used. But, for now, we need it.
    /// What we are going to do is to examine all the data being inserted and if it's
    /// an empty string (NULL for Oracle) and the field is defined as NOT NULL, we'll modify
    /// such data in the best form possible ("0" for booleans and numbers and " " for the
    /// rest of strings. It isn't optimal, but the only way to do so.
    /// In the oppsite, when retrieving records from Oracle, we'll decode " " back to
    /// empty strings to allow everything to work properly. DIRTY HACK.

    /// If the field ins't VARCHAR or CLOB, skip
        if ($column->meta_type != 'C' and $column->meta_type != 'X') {
            return $value;
        }

    /// If the field isn't NOT NULL, skip (it's nullable, so accept empty-null values)
        if (!$column->not_null) {
            return $value;
        }

    /// If the value isn't empty, skip
        if (!empty($value)) {
            return $value;
        }

    /// Now, we have one empty value, going to be inserted to one NOT NULL, VARCHAR2 or CLOB field
    /// Try to get the best value to be inserted

    /// The '0' string doesn't need any transformation, skip
        if ($value === '0') {
            return $value;
        }

    /// Transformations start
        if (gettype($value) == 'boolean') {
            return '0'; /// Transform false to '0' that evaluates the same for PHP

        } else if (gettype($value) == 'integer') {
            return '0'; /// Transform 0 to '0' that evaluates the same for PHP

        } else if (gettype($value) == 'NULL') {
            return '0'; /// Transform NULL to '0' that evaluates the same for PHP

        } else if ($value === '') {
            return ' '; /// Transform '' to ' ' that DONT'T EVALUATE THE SAME
                        /// (we'll transform back again on get_records_XXX functions and others)!!
        }

    /// Fail safe to original value
        return $value;
    }

    /**
     * Reset a sequence to the id field of a table.
     * @param string $table name of table
     * @return bool success
     */
    public function reset_sequence($table) {
        // From http://www.acs.ilstu.edu/docs/oracle/server.101/b10759/statements_2011.htm
        $dbman = $this->get_manager();
        if (!$dbman->table_exists($table)) {
            return false;
        }
        $value = (int)$this->get_field_sql('SELECT MAX(id) FROM {'.$table.'}');
        $value++;
        $xmldb_table = new xmldb_table($table);
        $this->reads++;
        $seqname = $dbman->find_sequence_name($xmldb_table);
        if (!$seqname) {
        /// Fallback, seqname not found, something is wrong. Inform and use the alternative getNameForObject() method
            $generator = $dbman->generator;
            $generator->setPrefix($this->getPrefix());
            $seqname = $generator->getNameForObject($table, 'id', 'seq');
        }

        $this->change_database_structure("DROP SEQUENCE $seqname");
        return $this->change_database_structure("CREATE SEQUENCE $seqname START WITH $value INCREMENT BY 1 NOMAXVALUE");
    }

    /**
     * Import a record into a table, id field is required.
     * Basic safety checks only. Lobs are supported.
     * @param string $table name of database table to be inserted into
     * @param mixed $dataobject object or array with fields in the record
     * @return bool success
     */
    public function import_record($table, $dataobject) {
        $dataobject = (object)$dataobject;

        $columns = $this->get_columns($table);
        $cleaned = array();
        $blobs = array();
        $clobs = array();

        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) { /// Non-existing table field, skip it
                continue;
            }
        /// Apply Oracle dirty hack to value, to have "correct" empty values for Oracle
            $value = $this->oracle_dirty_hack($table, $field, $value);

        /// Get column metadata
            $column = $columns[$field];
            if ($column->meta_type == 'B') { /// BLOBs columns need to be updated apart
                if (!is_null($value)) {      /// If value not null, add it to the list of BLOBs to update later
                    $blobs[$field] = $value;
                    $value = 'empty_blob()'; /// Set the default value to be inserted (preparing lob storage for next update)
                }

            } else if ($column->meta_type == 'X' && strlen($value) > 4000) { /// CLOB columns need to be updated apart (if lenght > 4000)
                if (!is_null($value)) {      /// If value not null, add it to the list of BLOBs to update later
                    $clobs[$field] = $value;
                    $value = 'empty_clob()'; /// Set the default value to be inserted (preparing lob storage for next update)
                }
            }

            $cleaned[$field] = $value;
        }

        if (!$this->insert_record_raw($table, $cleaned, false, true, true)) {
            return false;
        }

        if (empty($blobs) and empty($clobs)) {
            return true;
        }

    /// We have BLOBs or CLOBs to postprocess

        foreach ($blobs as $key=>$value) {
            $this->writes++;
            if (!$this->adodb->UpdateBlob($this->prefix.$table, $key, $value, "id = $id")) {
                return false;
            }
        }

        foreach ($clobs as $key=>$value) {
            $this->writes++;
            if (!$this->adodb->UpdateClob($this->prefix.$table, $key, $value, "id = $id")) {
                return false;
            }
        }

        return true;
    }
}
