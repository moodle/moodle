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
 * MSSQL database class using adodb backend
 *
 * TODO: delete before branching 2.0
 *
 * @package    moodlecore
 * @subpackage DML
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/dml/moodle_database.php');
require_once($CFG->libdir.'/dml/adodb_moodle_database.php');

/**
 * MSSQL database class using adodb backend
 */
class mssql_adodb_moodle_database extends adodb_moodle_database {
    /**
     * Ungly mssql hack needed for temp table names starting with '#'
     */
    public $temptables;

    public function connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, array $dboptions=null) {
        if ($prefix == '' and !$this->external) {
            //Enforce prefixes for everybody but mysql
            throw new dml_exception('prefixcannotbeempty', $this->get_dbfamily());
        }
        return parent::connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions);
    }

    /**
     * Detects if all needed PHP stuff installed.
     * Do not connect to connect to db if this test fails.
     * @return mixed true if ok, string if something
     */
    public function driver_installed() {
        if (!function_exists('mssql_connect')) {
            return get_string('mssqlextensionisnotpresentinphp', 'install');
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

        /// No need to set charset. It must be specified in the driver conf
        /// Allow quoted identifiers
        $sql = "SET QUOTED_IDENTIFIER ON";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $rs = $this->adodb->Execute($sql);
        $this->query_end($rs);
        /// Force ANSI nulls so the NULL check was done by IS NULL and NOT IS NULL
        /// instead of equal(=) and distinct(<>) simbols
        $sql = "SET ANSI_NULLS ON";
        $this->query_start($sql, null, SQL_QUERY_AUX);
        $rs = $this->adodb->Execute($sql);
        $this->query_end($rs);

        return true;
    }

    /**
     * Returns database family type
     * @return string db family name (mysql, postgres, mssql, oracle, etc.)
     */
    public function get_dbfamily() {
        return 'mssql';
    }

    /**
     * Returns database type
     * @return string db type mysql, mysqli, postgres7
     */
    protected function get_dbtype() {
        return 'mssql';
    }

    /**
     * Returns localised database description
     * Note: can be used before connect()
     * @return string
     */
    public function get_configuration_hints() {
        $str = get_string('databasesettingssub_mssql', 'install');
        $str .= "<p style='text-align:right'><a href=\"javascript:void(0)\" ";
        $str .= "onclick=\"return window.open('http://docs.moodle.org/en/Installing_MSSQL_for_PHP')\"";
        $str .= ">";
        $str .= '<img src="pix/docs.gif' . '" alt="Docs" class="iconhelp" />';
        $str .= get_string('moodledocslink', 'install') . '</a></p>';
        return $str;
    }

    /**
     * Converts short table name {tablename} to real table name
     * @param string sql
     * @return string sql
     */
    protected function fix_table_names($sql) {
        // look for temporary tables, they must start with #
        if (preg_match_all('/\{([a-z][a-z0-9_]*)\}/', $sql, $matches)) {
            foreach($matches[0] as $key=>$match) {
                $name = $matches[1][$key];
                if (empty($this->temptables[$name])) {
                    $sql = str_replace($match, $this->prefix.$name, $sql);
                } else {
                    $sql = str_replace($match, '#'.$this->prefix.$name, $sql);
                }
            }
        }
        return $sql;
    }

    /**
     * Returns supported query parameter types
     * @return bitmask
     */
    protected function allowed_param_types() {
        return SQL_PARAMS_QM;
    }

    public function sql_ceil($fieldname) {
        return ' CEILING(' . $fieldname . ')';
    }

    public function sql_cast_char2int($fieldname, $text=false) {
        if (!$text) {
            return ' CAST(' . $fieldname . ' AS INT) ';
        } else {
            return ' CAST(' . $this->sql_compare_text($fieldname) . ' AS INT) ';
        }
    }

    public function sql_order_by_text($fieldname, $numchars=32) {
        return ' CONVERT(varchar, ' . $fieldname . ', ' . $numchars . ')';
    }

    /**
     * Returns the SQL text to be used to calculate the length in characters of one expression.
     * @param string fieldname or expression to calculate its length in characters.
     * @return string the piece of SQL code to be used in the statement.
     */
    public function sql_length($fieldname) {
        return ' LEN(' . $fieldname . ')';
    }

    /**
     * Returns the SQL for returning searching one string for the location of another.
     */
    public function sql_position($needle, $haystack) {
        return "CHARINDEX(($needle), ($haystack))";
    }

    public function sql_isempty($tablename, $fieldname, $nullablefield, $textfield) {
        if ($textfield) {
            return $this->sql_compare_text($fieldname)." = '' ";
        } else {
            return " $fieldname = '' ";
        }
    }

    /**
     * Returns the proper substr() function for each DB.
     * NOTE: this was originally returning only function name
     *
     * @param string $expr some string field, no aggregates
     * @param mixed $start integer or expresion evaluating to int
     * @param mixed $length optional integer or expresion evaluating to int
     * @return string sql fragment
     */
    public function sql_substr($expr, $start, $length=false) {
        if ($length === false) {
            return "SUBSTRING($expr, $start, (LEN($expr) - $start + 1))";
        } else {
            return "SUBSTRING($expr, $start, $length)";
        }
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
        $blobs   = array();

        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) { /// Non-existing table field, skip it
                continue;
            }
            $column = $columns[$field];

            if (is_bool($value)) { /// Always, convert boolean to int
                $value = (int)$value;
            }

            if ($column->meta_type == 'B') { /// BLOBs (IMAGE) columns need to be updated apart
                if (!is_null($value)) {      /// If value not null, add it to the list of BLOBs to update later
                    $blobs[$field] = $value;
                    $value = null;           /// Set the default value to be inserted in first instance
                }

            } else if ($column->meta_type == 'X') { /// MSSQL doesn't cast from int to text, so if text column
                if (is_numeric($value)) {           /// and is numeric value
                    $value = (string)$value;        /// cast to string
                }

            } else if ($value === '') {
                if ($column->meta_type == 'I' or $column->meta_type == 'F' or $column->meta_type == 'N') {
                    $value = 0; // prevent '' problems in numeric fields
                }
            }
            $cleaned[$field] = $value;
        }

        if (empty($blobs)) { /// Without BLOBs, execute the raw update and return
            return $this->update_record_raw($table, $cleaned, $bulk);
        }

    /// We have BLOBs to postprocess, execute the raw update and then update blobs
        $this->update_record_raw($table, $cleaned, $bulk);

        foreach ($blobs as $key=>$value) {
            $this->query_start('--adodb-UpdateBlob', null, SQL_QUERY_UPDATE);
            $result = $this->adodb->UpdateBlob($this->prefix.$table, $key, $value, "id = {$dataobject->id}");
            $this->query_end($result);
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
     * @return bool true
     * @throws dml_exception if error
     */
    public function set_field_select($table, $newfield, $newvalue, $select, array $params=null) {

        if (is_null($params)) {
            $params = array();
        }
        list($select, $params, $type) = $this->fix_sql_params($select, $params);

        $columns = $this->get_columns($table);
        $column = $columns[$newfield];

        if ($column->meta_type == 'B') { /// If the column is a BLOB (IMAGE)
        /// Update BLOB column and return
            $select = $this->emulate_bound_params($select, $params); // adodb does not use bound parameters for blob updates :-(
            $this->query_start('--adodb-UpdateBlob', null, SQL_QUERY_UPDATE);
            $result = $this->adodb->UpdateBlob($this->prefix.$table, $newfield, $newvalue, $select);
            $this->query_end($result);
            return true;
        }

    /// Arrived here, normal update (without BLOBs)

        if (is_bool($newvalue)) { /// Always, convert boolean to int
            $newvalue = (int)$newvalue;
        }

        if (is_null($newvalue)) {
            $newfield = "$newfield = NULL";
        } else {
            if ($column->meta_type == 'X') {        /// MSSQL doesn't cast from int to text, so if text column
                if (is_numeric($newvalue)) {        /// and is numeric value
                    $newvalue = (string)$newvalue;  /// cast to string in PHP
                }

            } else if ($newvalue === '') {
                if ($column->meta_type == 'I' or $column->meta_type == 'F' or $column->meta_type == 'N') {
                    $newvalue = 0; // prevent '' problems in numeric fields
                }
            }

            $newfield = "$newfield = ?";
            array_unshift($params, $newvalue); // add as first param
        }
        $select = !empty($select) ? "WHERE $select" : '';
        $sql = "UPDATE {$this->prefix}$table SET $newfield $select";

        $this->query_start($sql, $params, SQL_QUERY_UPDATE);
        $rs = $this->adodb->Execute($sql, $params);
        $this->query_end($rs);
        $rs->Close();

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
     * @return mixed true or new id
     * @throws dml_exception if error
     */
    public function insert_record($table, $dataobject, $returnid=true, $bulk=false) {
        if (!is_object($dataobject)) {
            $dataobject = (object)$dataobject;
        }

        unset($dataobject->id);

        $columns = $this->get_columns($table);
        $cleaned = array();
        $blobs = array();

        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) { /// Non-existing table field, skip it
                continue;
            }
            $column = $columns[$field];

            if (is_bool($value)) { /// Always, convert boolean to int
                $value = (int)$value;
            }

            if ($column->meta_type == 'B') { /// BLOBs (IMAGE) columns need to be updated apart
                if (!is_null($value)) {      /// If value not null, add it to the list of BLOBs to update later
                    $blobs[$field] = $value;
                    $value = null;           /// Set the default value to be inserted in first instance
                }

            } else if ($column->meta_type == 'X') { /// MSSQL doesn't cast from int to text, so if text column
                if (is_numeric($value)) {           /// and is numeric value
                    $value = (string)$value;        /// cast to string
                }

            } else if ($value === '') {
                if ($column->meta_type == 'I' or $column->meta_type == 'F' or $column->meta_type == 'N') {
                    $value = 0; // prevent '' problems in numeric fields
                }
            }
            $cleaned[$field] = $value;
        }

        if (empty($blobs)) { /// Without BLOBs, execute the raw insert and return
            return $this->insert_record_raw($table, $cleaned, $returnid, $bulk);
        }

    /// We have BLOBs to postprocess, insert the raw record fetching the id to be used later
        $id = $this->insert_record_raw($table, $cleaned, true, $bulk);

        foreach ($blobs as $key=>$value) {
            $this->query_start('--adodb-UpdateBlob', null, SQL_QUERY_UPDATE);
            $result = $this->adodb->UpdateBlob($this->prefix.$table, $key, $value, "id = $id");
            $this->query_end($result);
        }

        return ($returnid ? $id : true);
    }

    /**
     * Import a record into a table, id field is required.
     * Basic safety checks only. Lobs are supported.
     * @param string $table name of database table to be inserted into
     * @param mixed $dataobject object or array with fields in the record
     * @return bool true
     * @throws dml_exception if error
     */
    public function import_record($table, $dataobject) {
        $dataobject = (object)$dataobject;

        $columns = $this->get_columns($table);
        $cleaned = array();
        $blobs = array();

        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) { // Non-existing table field, skip it
                continue;
            }
            $column = $columns[$field];
            if ($column->meta_type == 'B') { // BLOBs (IMAGE) columns need to be updated apart
                if (!is_null($value)) {      // If value not null, add it to the list of BLOBs to update later
                    $blobs[$field] = $value;
                    $value = null;           // Set the default value to be inserted in first instance
                }
            } else if ($column->meta_type == 'X') { // MSSQL doesn't cast from int to text, so if text column
                if (is_numeric($value)) {           // and is numeric value
                    $value = (string)$value;        // cast to string
                }
            }

            $cleaned[$field] = $value;
        }

        $this->insert_record_raw($table, $cleaned, false, true, true);

        if (empty($blobs)) {
            return true;
        }

    /// We have BLOBs to postprocess

        foreach ($blobs as $key=>$value) {
            $this->query_start('--adodb-UpdateBlob', null, SQL_QUERY_UPDATE);
            $result = $this->adodb->UpdateBlob($this->prefix.$table, $key, $value, "id = $id");
            $this->query_end($result);
        }

        return true;
    }

    public function get_columns($table, $usecache=true) {
        if ($usecache and isset($this->columns[$table])) {
            return $this->columns[$table];
        }

        $this->columns[$table] = array();

        $tablename = strtoupper($this->prefix.$table);

        $sql = "SELECT column_name AS name,
                       data_type   AS type,
                       numeric_precision AS max_length,
                       character_maximum_length AS char_max_length,
                       numeric_scale AS scale,
                       is_nullable AS is_nullable,
                       columnproperty(object_id(quotename(TABLE_SCHEMA) + '.' +
                           quotename(TABLE_NAME)), COLUMN_NAME, 'IsIdentity') AS auto_increment,
                       column_default AS default_value
                  FROM INFORMATION_SCHEMA.Columns
                 WHERE TABLE_NAME = '$tablename'
              ORDER BY ordinal_position";

        $this->query_start($sql, null, SQL_QUERY_AUX);
        $rs = $this->adodb->Execute($sql);
        $this->query_end($rs);

        $columns = $this->adodb_recordset_to_array($rs);
        $rs->Close();

        if (!$columns) {
            return array();
        }

        $this->columns[$table] = array();

        foreach ($columns as $column) {
            $dict = NewDataDictionary($this->adodb); // use dictionary because mssql driver lacks proper MetaType() function
            $column->meta_type = substr($dict->MetaType($column), 0 ,1); // only 1 character
            $column->meta_type = $column->meta_type == 'F' ? 'N' : $column->meta_type; // floats are numbers for us
            $column->meta_type = ($column->auto_increment && $column->meta_type == 'I') ? 'R' : $column->meta_type; // Proper 'R'
            $column->max_length = $column->meta_type == 'C' ? $column->char_max_length : $column->max_length; //Pick correct for Chars
            $column->max_length = ($column->meta_type == 'X' || $column->meta_type == 'B') ? -1 : $column->max_length; // -1 for xLOB
            $column->auto_increment = $column->auto_increment ? true : false;
            $column->not_null = $column->is_nullable == 'NO'  ? true : false; // Process not_null
            $column->has_default = !empty($column->default_value); // Calculate has_default
            $column->default_value = preg_replace("/^[\(N]+[']?(.*?)[']?[\)]+$/", '\\1', $column->default_value); // Clean default
            $this->columns[$table][$column->name] = new database_column_info($column);
        }

        return $this->columns[$table];
    }
}
