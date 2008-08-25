<?php  //$Id$

require_once($CFG->libdir.'/dml/moodle_database.php');
require_once($CFG->libdir.'/dml/adodb_moodle_database.php');

/**
 * Recommended MySQL database class using adodb backend
 * @package dml
 */
class mysqli_adodb_moodle_database extends adodb_moodle_database {

    /**
     * Attempt to create the database
     * @param string $dbhost
     * @param string $dbuser
     * @param string $dbpass
     * @param string $dbname
     *
     * @return bool success
     */
    public function create_database($dbhost, $dbuser, $dbpass, $dbname) {
        $this->adodb->database = ''; // reset database name cached by ADODB. Trick from MDL-9609
        if ($this->adodb->Connect($dbhost, $dbuser, $dbpass)) { /// Try to connect without DB
            if ($this->adodb->Execute("CREATE DATABASE $dbname DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci")) {
                $this->adodb->Disconnect();
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Detects if all needed PHP stuff installed.
     * Do not connect to connect to db if this test fails.
     * @return mixed true if ok, string if something
     */
    public function driver_installed() {
        if (!extension_loaded('mysqli')) {
            return get_string('mysqliextensionisnotpresentinphp', 'install');
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
        return 'mysql';
    }

    /**
     * Returns database type
     * @return string db type mysql, mysqli, postgres7
     */
    protected function get_dbtype() {
        return 'mysqli';
    }

    /**
     * Returns localised database description
     * Note: can be used before connect()
     * @return string
     */
    public function get_configuration_hints() {
        return get_string('databasesettingssub_mysqli', 'install');
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
        $rs = $this->adodb->Execute("SHOW LOCAL VARIABLES LIKE 'character_set_database'");
        if ($rs && !$rs->EOF) {
            $records = $rs->GetAssoc(true);
            $encoding = $records['character_set_database']['Value'];
            if (strtoupper($encoding) == 'UTF8') {
                return  true;
            }
        }
        return false;
    }

    /**
    /* Tries to change default db encoding to utf8, if empty db
     * @return bool sucecss
     */
    public function change_db_encoding() {
        // try forcing utf8 collation, if mysql db and no tables present
        $this->reads++;
        if (!$this->adodb->Metatables()) {
            $SQL = 'ALTER DATABASE '.$this->dbname.' CHARACTER SET utf8';
            $this->writes++;
            $this->adodb->Execute($SQL);
            if ($this->setup_is_unicodedb()) {
                $this->configure_dbconnection();
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
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

        $columns = $this->get_columns($table);

        unset($dataobject->id);
        $cleaned = array();

        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) {
                continue;
            }
            $column = $columns[$field];
            if (is_bool($value)) {
                $value = (int)$value; // prevent "false" problems
            }
            if (!empty($column->enums)) {
                // workaround for problem with wrong enums in mysql
                if (is_null($value) and !$column->not_null) {
                    // ok - nulls allowed
                } else {
                    if (!in_array((string)$value, $column->enums)) {
                        debugging('Enum value '.s($value).' not allowed in field '.$field.' table '.$table.'.');
                        return false;
                    }
                }
            }
            $cleaned[$field] = $value;
        }

        if (empty($cleaned)) {
            return false;
        }

        return $this->insert_record_raw($table, $cleaned, $returnid, $bulk);
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

        if (!isset($dataobject->id) ) {
            return false;
        }

        $columns = $this->get_columns($table);
        $cleaned = array();

        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) {
                continue;
            }
            if (is_bool($value)) {
                $value = (int)$value; // prevent "false" problems
            }
            $cleaned[$field] = $value;
        }

        return $this->update_record_raw($table, $cleaned, $bulk);
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
        if ($select) {
            $select = "WHERE $select";
        }
        if (is_null($params)) {
            $params = array();
        }
        list($select, $params, $type) = $this->fix_sql_params($select, $params);

        if (is_bool($newvalue)) {
            $newvalue = (int)$newvalue; // prevent "false" problems
        }
        if (is_null($newvalue)) {
            $newfield = "$newfield = NULL";
        } else {
            $newfield = "$newfield = ?";
            array_unshift($params, $newvalue);
        }
        $sql = "UPDATE {$this->prefix}$table SET $newfield $select";

        $this->writes++;

        if (!$rs = $this->adodb->Execute($sql, $params)) {
            $this->report_error($sql, $params);
            return false;
        }
        return true;
    }


    public function sql_cast_char2int($fieldname, $text=false) {
        return ' CAST(' . $fieldname . ' AS SIGNED) ';
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
        return $positivematch ? 'REGEXP' : 'NOT REGEXP';
    }

    /**
     * Reset a sequence to the id field of a table.
     * @param string $table name of table
     * @return bool success
     */
    public function reset_sequence($table) {
        // From http://dev.mysql.com/doc/refman/5.0/en/alter-table.html
        if (!$this->get_manager()->table_exists($table)) {
            return false;
        }
        $value = (int)$this->get_field_sql('SELECT MAX(id) FROM {'.$table.'}');
        $value++;
        return $this->change_database_structure("ALTER TABLE $this->prefix$table AUTO_INCREMENT = $value");
    }
}