<?php  //$Id$

require_once($CFG->libdir.'/dml/moodle_database.php');
require_once($CFG->libdir.'/dml/adodb_moodle_database.php');
require_once($CFG->libdir.'/dml/oci8po_adodb_moodle_recordset.php');

/**
 * Oracle database class using adodb backend
 * @package dmlib
 */
class oci8_adodb_moodle_database extends adodb_moodle_database {
    function __construct($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix) {
        if ($prefix=='') {
            print_error('prefixcannotbeempty', 'debug', '', array($prefix, $this->get_dbfamily()));
        }
        if (strlen($prefix) > 2) { //Max prefix length for Oracle is 2cc
            print_error('prefixlimit', 'debug', '', $prefix);
        }

        parent::__construct($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix);
    }

    protected function configure_dbconnection() {
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
        $this->db->SetFetchMode(ADODB_FETCH_ASSOC);

        /// No need to set charset. It must be specified by the NLS_LANG env. variable
        /// Enable sybase quotes, so addslashes and stripslashes will use "'"
            ini_set('magic_quotes_sybase', '1');
        /// NOTE: Not 100% useful because GPC has been addslashed with the setting off
        ///       so IT'S MANDATORY TO ENABLE THIS UNDER php.ini or .htaccess for this DB
        ///       or to turn off magic_quotes to allow Moodle to do it properly
        /// Now set the decimal separator to DOT, Moodle & PHP will always send floats to
        /// DB using DOTS. Manually introduced floats (if using other characters) must be
        /// converted back to DOTs (like gradebook does)
            $this->db->Execute("ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.,'");

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
     * Returns supported query parameter types
     * @return bitmask
     */
    protected function allowed_param_types() {
        return SQL_PARAMS_NAMED;
    }

    /**
     * This method will introspect inside DB to detect it it's a UTF-8 DB or no
     * Used from setup.php to set correctly "set names" when the installation
     * process is performed without the initial and beautiful installer
     * @return bool true if db in unicode mode
     */
    function setup_is_unicodedb() {
        $rs = $this->db->Execute("SELECT parameter, value FROM nls_database_parameters where parameter = 'NLS_CHARACTERSET'");
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
            array_walk($result, 'onespace2empty');
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
                array_walk($row, 'onespace2empty');
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
        return '((' . $int1 . ') + (' . $int2 . ') - ' . sql_bitand($int1, $int2) . ')';
    }

    public function sql_bitxor($int1, $int2) {
        return '((' . $int1 . ') # (' . $int2 . '))';
    }

    public function sql_cast_char2int($fieldname, $text=false) {
        if (!$text) {
            return ' CAST(' . $fieldname . ' AS INT) ';
        } else {
            return ' CAST(' . sql_compare_text($fieldname) . ' AS INT) ';
        }
    }

    public function sql_order_by_text($fieldname, $numchars=32) {
        return 'dbms_lob.substr(' . $fieldname . ', ' . $numchars . ',1)';
    }

    public function sql_isempty($tablename, $fieldname, $nullablefield, $textfield) {
        if ($nullablefield) {
            return " $fieldname IS NULL ";                    /// empties in nullable fields are stored as
        } else {                                              /// NULLs
            if ($textfield) {
                return " ".sql_compare_text($fieldname)." = ' ' "; /// oracle_dirty_hack inserts 1-whitespace
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

error('todo');

        global $db, $CFG;

        if (! isset($dataobject->id) ) {
            return false;
        }

    /// Check we are handling a proper $dataobject
        if (is_array($dataobject)) {
            debugging('Warning. Wrong call to update_record(). $dataobject must be an object. array found instead', DEBUG_DEVELOPER);
            $dataobject = (object)$dataobject;
        }

    /// Temporary hack as part of phasing out all access to obsolete user tables  XXX
        if (!empty($CFG->rolesactive)) {
            if (in_array($table, array('user_students', 'user_teachers', 'user_coursecreators', 'user_admins'))) {
                if (debugging()) { var_dump(debug_backtrace()); }
                print_error('This SQL relies on obsolete tables ('.$table.')!  Your code must be fixed by a developer.');
            }
        }

    /// Begin DIRTY HACK
        if ($CFG->dbfamily == 'oracle') {
            oracle_dirty_hack($table, $dataobject); // Convert object to the correct "empty" values for Oracle DB
        }
    /// End DIRTY HACK

    /// Under Oracle, MSSQL and PostgreSQL we have our own update record process
    /// detect all the clob/blob fields and delete them from the record being updated
    /// saving them into $foundclobs and $foundblobs [$fieldname]->contents
    /// They will be updated later
        if (($CFG->dbfamily == 'oracle' || $CFG->dbfamily == 'mssql' || $CFG->dbfamily == 'postgres')
          && !empty($dataobject->id)) {
        /// Detect lobs
            $foundclobs = array();
            $foundblobs = array();
            db_detect_lobs($table, $dataobject, $foundclobs, $foundblobs, true);
        }

        // Determine all the fields in the table
        if (!$columns = $db->MetaColumns($CFG->prefix . $table)) {
            return false;
        }
        $data = (array)$dataobject;

        if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

        // Pull out data matching these fields
        $update = array();
        foreach ($columns as $column) {
            if ($column->name == 'id') {
                continue;
            }
            if (array_key_exists($column->name, $data)) {
                $key   = $column->name;
                $value = $data[$key];
                if (is_null($value)) {
                    $update[] = "$key = NULL"; // previously NULLs were not updated
                } else if (is_bool($value)) {
                    $value = (int)$value;
                    $update[] = "$key = $value";   // lets keep pg happy, '' is not correct smallint MDL-13038
                } else {
                    $update[] = "$key = '$value'"; // All incoming data is already quoted
                }
            }
        }

    /// Only if we have fields to be updated (this will prevent both wrong updates +
    /// updates of only LOBs in Oracle
        if ($update) {
            $query = "UPDATE {$CFG->prefix}{$table} SET ".implode(',', $update)." WHERE id = {$dataobject->id}";
            if (!$rs = $db->Execute($query)) {
                debugging($db->ErrorMsg() .'<br /><br />'.s($query));
                if (!empty($CFG->dblogerror)) {
                    $debug=array_shift(debug_backtrace());
                    error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $query");
                }
                return false;
            }
        }

    /// Under Oracle, MSSQL and PostgreSQL, finally, update all the Clobs and Blobs present in the record
    /// if we know we have some of them in the query
        if (($CFG->dbfamily == 'oracle' || $CFG->dbfamily == 'mssql' || $CFG->dbfamily == 'postgres') &&
            !empty($dataobject->id) &&
            (!empty($foundclobs) || !empty($foundblobs))) {
            if (!db_update_lobs($table, $dataobject->id, $foundclobs, $foundblobs)) {
                return false; //Some error happened while updating LOBs
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
error('todo');

    ///////////////////////////////////////////////////////////////
    /// TODO: keeping this for now - only mysql implemented ;-) ///
    ///////////////////////////////////////////////////////////////

        global $db, $CFG, $empty_rs_cache;

        if (empty($db)) {
            return false;
        }

    /// Check we are handling a proper $dataobject
        if (is_array($dataobject)) {
            debugging('Warning. Wrong call to insert_record(). $dataobject must be an object. array found instead', DEBUG_DEVELOPER);
            $dataobject = (object)$dataobject;
        }

    /// Temporary hack as part of phasing out all access to obsolete user tables  XXX
        if (!empty($CFG->rolesactive)) {
            if (in_array($table, array('user_students', 'user_teachers', 'user_coursecreators', 'user_admins'))) {
                if (debugging()) { var_dump(debug_backtrace()); }
                print_error('This SQL relies on obsolete tables ('.$table.')!  Your code must be fixed by a developer.');
            }
        }

        if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    /// In Moodle we always use auto-numbering fields for the primary key
    /// so let's unset it now before it causes any trouble later
        unset($dataobject->{$primarykey});

    /// Get an empty recordset. Cache for multiple inserts.
        if (empty($empty_rs_cache[$table])) {
            /// Execute a dummy query to get an empty recordset
            if (!$empty_rs_cache[$table] = $db->Execute('SELECT * FROM '. $CFG->prefix . $table .' WHERE '. $primarykey  .' = \'-1\'')) {
                return false;
            }
        }

        $rs = $empty_rs_cache[$table];

    /// Postgres doesn't have the concept of primary key built in
    /// and will return the OID which isn't what we want.
    /// The efficient and transaction-safe strategy is to
    /// move the sequence forward first, and make the insert
    /// with an explicit id.
        if ( $CFG->dbfamily === 'postgres' && $returnid == true ) {
            if ($nextval = (int)get_field_sql("SELECT NEXTVAL('{$CFG->prefix}{$table}_{$primarykey}_seq')")) {
                $dataobject->{$primarykey} = $nextval;
            }
        }

    /// Begin DIRTY HACK
        if ($CFG->dbfamily == 'oracle') {
            oracle_dirty_hack($table, $dataobject); // Convert object to the correct "empty" values for Oracle DB
        }
    /// End DIRTY HACK

    /// Under Oracle, MSSQL and PostgreSQL we have our own insert record process
    /// detect all the clob/blob fields and change their contents to @#CLOB#@ and @#BLOB#@
    /// saving them into $foundclobs and $foundblobs [$fieldname]->contents
    /// Same for mssql (only processing blobs - image fields)
        if ($CFG->dbfamily == 'oracle' || $CFG->dbfamily == 'mssql' || $CFG->dbfamily == 'postgres') {
            $foundclobs = array();
            $foundblobs = array();
            db_detect_lobs($table, $dataobject, $foundclobs, $foundblobs);
        }

    /// Under Oracle, if the primary key inserted has been requested OR
    /// if there are LOBs to insert, we calculate the next value via
    /// explicit query to the sequence.
    /// Else, the pre-insert trigger will do the job, because the primary
    /// key isn't needed at all by the rest of PHP code
        if ($CFG->dbfamily === 'oracle' && ($returnid == true || !empty($foundclobs) || !empty($foundblobs))) {
        /// We need this here (move this function to dmlib?)
            include_once($CFG->libdir . '/ddllib.php');
            $xmldb_table = new XMLDBTable($table);
            $seqname = find_sequence_name($xmldb_table);
            if (!$seqname) {
            /// Fallback, seqname not found, something is wrong. Inform and use the alternative getNameForObject() method
                debugging('Sequence name for table ' . $table->getName() . ' not found', DEBUG_DEVELOPER);
                $generator = new XMLDBoci8po();
                $generator->setPrefix($CFG->prefix);
                $seqname = $generator->getNameForObject($table, $primarykey, 'seq');
            }
            if ($nextval = (int)$db->GenID($seqname)) {
                $dataobject->{$primarykey} = $nextval;
            } else {
                debugging('Not able to get value from sequence ' . $seqname, DEBUG_DEVELOPER);
            }
        }

    /// Get the correct SQL from adoDB
        if (!$insertSQL = $db->GetInsertSQL($rs, (array)$dataobject, true)) {
            return false;
        }

    /// Under Oracle, MSSQL and PostgreSQL, replace all the '@#CLOB#@' and '@#BLOB#@' ocurrences to proper default values
    /// if we know we have some of them in the query
        if (($CFG->dbfamily == 'oracle' || $CFG->dbfamily == 'mssql' || $CFG->dbfamily == 'postgres') &&
          (!empty($foundclobs) || !empty($foundblobs))) {
        /// Initial configuration, based on DB
            switch ($CFG->dbfamily) {
                case 'oracle':
                    $clobdefault = 'empty_clob()'; //Value of empty default clobs for this DB
                    $blobdefault = 'empty_blob()'; //Value of empty default blobs for this DB
                    break;
                case 'mssql':
                case 'postgres':
                    $clobdefault = 'null'; //Value of empty default clobs for this DB (under mssql this won't be executed
                    $blobdefault = 'null'; //Value of empty default blobs for this DB
                    break;
            }
            $insertSQL = str_replace("'@#CLOB#@'", $clobdefault, $insertSQL);
            $insertSQL = str_replace("'@#BLOB#@'", $blobdefault, $insertSQL);
        }

    /// Run the SQL statement
        if (!$rs = $db->Execute($insertSQL)) {
            debugging($db->ErrorMsg() .'<br /><br />'.s($insertSQL));
            if (!empty($CFG->dblogerror)) {
                $debug=array_shift(debug_backtrace());
                error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $insertSQL");
            }
            return false;
        }

    /// Under Oracle and PostgreSQL, finally, update all the Clobs and Blobs present in the record
    /// if we know we have some of them in the query
        if (($CFG->dbfamily == 'oracle' || $CFG->dbfamily == 'postgres') &&
          !empty($dataobject->{$primarykey}) &&
          (!empty($foundclobs) || !empty($foundblobs))) {
            if (!db_update_lobs($table, $dataobject->{$primarykey}, $foundclobs, $foundblobs)) {
                return false; //Some error happened while updating LOBs
            }
        }

    /// If a return ID is not needed then just return true now (but not in MSSQL DBs, where we may have some pending tasks)
        if (!$returnid && $CFG->dbfamily != 'mssql') {
            return true;
        }

    /// We already know the record PK if it's been passed explicitly,
    /// or if we've retrieved it from a sequence (Postgres and Oracle).
        if (!empty($dataobject->{$primarykey})) {
            return $dataobject->{$primarykey};
        }

    /// This only gets triggered with MySQL and MSQL databases
    /// however we have some postgres fallback in case we failed
    /// to find the sequence.
        $id = $db->Insert_ID();

    /// Under MSSQL all the Clobs and Blobs (IMAGE) present in the record
    /// if we know we have some of them in the query
        if (($CFG->dbfamily == 'mssql') &&
          !empty($id) &&
          (!empty($foundclobs) || !empty($foundblobs))) {
            if (!db_update_lobs($table, $id, $foundclobs, $foundblobs)) {
                return false; //Some error happened while updating LOBs
            }
        }

        if ($CFG->dbfamily === 'postgres') {
            // try to get the primary key based on id
            if ( ($rs = $db->Execute('SELECT '. $primarykey .' FROM '. $CFG->prefix . $table .' WHERE oid = '. $id))
                 && ($rs->RecordCount() == 1) ) {
                trigger_error("Retrieved $primarykey from oid on table $table because we could not find the sequence.");
                return (integer)reset($rs->fields);
            }
            trigger_error('Failed to retrieve primary key after insert: SELECT '. $primarykey .
                          ' FROM '. $CFG->prefix . $table .' WHERE oid = '. $id);
            return false;
        }

        return (integer)$id;

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
function onespace2empty(&$item, $key=null) {
    $item = $item == ' ' ? '' : $item;
    return true;
}
