<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas     http://dougiamas.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// This library contains all the Data Manipulation Language (DML) functions
/// used to interact with the DB. All the dunctions in this library must be
/// generic and work against the major number of RDBMS possible. This is the
/// list of currently supported and tested DBs: mysql, postresql, mssql, oracle

/// This library is automatically included by Moodle core so you never need to
/// include it yourself.

/// For more info about the functions available in this library, please visit:
///     http://docs.moodle.org/en/DML_functions
/// (feel free to modify, improve and document such page, thanks!)

/// GLOBAL CONSTANTS /////////////////////////////////////////////////////////

require_once($CFG->libdir.'/dmllib_todo.php');

/**
 * Bitmask, indicates only :name type parameters are supported by db backend.
 */
define('SQL_PARAMS_NAMED', 1);

/**
 * Bitmask, indicates only ? type parameters are supported by db backend.
 */
define('SQL_PARAMS_QM', 2);

/**
 * Bitmask, indicates only $1, $2.. type parameters are supported by db backend.
 */
define('SQL_PARAMS_DOLAR', 4);


/**
 * Sets up global $DB moodle_database instance
 * @return void
 */
function setup_DB() {
    global $CFG, $DB;

    if (isset($DB)) {
        return;
    }

    if (!isset($CFG->dbuser)) {
        $CFG->dbuser = '';
    }

    if (!isset($CFG->dbpass)) {
        $CFG->dbpass = '';
    }

    if (!isset($CFG->dbname)) {
        $CFG->dbname = '';
    }

    if (!isset($CFG->dbpersist)) {
        $CFG->dbpersist = false;
    }

    if (!isset($CFG->dblibrary)) {
        $CFG->dblibrary = 'adodb';
    }

    if ($CFG->dblibrary == 'adodb') {
        $classname = $CFG->dbtype.'_adodb_moodle_database';
        require_once($CFG->libdir.'/dml/'.$classname.'.php');
        $DB = new $classname($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->dbpersist, $CFG->prefix);

    } else {
        error('Not implemented db library yet: '.$CFG->dblibrary);
    }

    $CFG->dbfamily = $DB->get_dbfamily(); // TODO: BC only for now

    $prevdebug = error_reporting(E_ALL);  // do not hide errors yet
    if (!$DB->connect()) {
        // In the name of protocol correctness, monitoring and performance
        // profiling, set the appropriate error headers for machine comsumption
        if (isset($_SERVER['SERVER_PROTOCOL'])) {
            // Avoid it with cron.php. Note that we assume it's HTTP/1.x
            header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
        }
        // and then for human consumption...
        echo '<html><body>';
        echo '<table align="center"><tr>';
        echo '<td style="color:#990000; text-align:center; font-size:large; border-width:1px; '.
             '    border-color:#000000; border-style:solid; border-radius: 20px; border-collapse: collapse; '.
             '    -moz-border-radius: 20px; padding: 15px">';
        echo '<p>Error: Database connection failed.</p>';
        echo '<p>It is possible that the database is overloaded or otherwise not running properly.</p>';
        echo '<p>The site administrator should also check that the database details have been correctly specified in config.php</p>';
        echo '</td></tr></table>';
        echo '</body></html>';

        if (empty($CFG->noemailever) and !empty($CFG->emailconnectionerrorsto)) {
            mail($CFG->emailconnectionerrorsto,
                 'WARNING: Database connection error: '.$CFG->wwwroot,
                 'Connection error: '.$CFG->wwwroot);
        }
        die;
    }
    error_reporting($prevdebug);

    return true;
}

/**
 * Interface definitions for resultsets returned from database functions.
 * This is a simple Iterator with needed recorset closing support.
 *
 * The differnece from old recorset is that the records are returned
 * as objects, not arrays. You should use "foreach ($recordset as $record) {}".
 *
 * Do not forget to close all recordsets when they are not needed anymore!
 */
interface moodle_recordset extends Iterator {
    /**
     * Free resources and connections, recordset can not be used anymore.
     */
    public function close();
}

/**
 * Detail database field information.
 * Based on ADOFieldObject.
 */
class database_column_info {
    public $name;
    public $type;         // raw db field type
    public $max_length;
    public $scale;
    public $enums;
    public $not_null;
    public $primary_key;
    public $auto_increment;
    public $binary;
    public $unsigned;
    public $zerofill;
    public $has_default;
    public $default_value;
    public $unique;

    public $meta_type; // type as one character

    /**
     * Contructor
     * @param $data mixed object or array with properties
     */
    public function database_column_info($data) {
        foreach ($data as $key=>$value) {
            if (array_key_exists($key, $this)) {
                $this->$key = $value;
            }
        }
    }
}












/////// DEPRECATED - works fine


function sql_ilike() {
    global $DB;
    return $DB->sql_ilike();
}

function sql_fullname($first='firstname', $last='lastname') {
    global $DB;
    return $DB->sql_fullname($first, $last);
}

function sql_concat() {
    global $DB;

    $args = func_get_args();
    return call_user_func_array(array($DB, 'sql_concat'), $args);
}

function sql_empty() {
    global $DB;
    return $DB->sql_empty();
}

function sql_substr() {
    global $DB;
    return $DB->sql_substr();
}

function sql_bitand($int1, $int2) {
    global $DB;
    return $DB->sql_bitand($int1, $int2);
}

function sql_bitnot($int1) {
    global $DB;
    return $DB->sql_bitnot($int1);
}

function sql_bitor($int1, $int2) {
    global $DB;
    return $DB->sql_bitor($int1);

}

function sql_bitxor($int1, $int2) {
    global $DB;
    return $DB->sql_bitxor($int1, $int2);

}

function sql_cast_char2int($fieldname, $text=false) {
    global $DB;
    return $DB->sql_cast_char2int($fieldname, $text);
}

function sql_compare_text($fieldname, $numchars=32) {
    return sql_order_by_text($fieldname, $numchars);
}

function sql_order_by_text($fieldname, $numchars=32) {
    global $DB;
    return $DB->sql_order_by_text($fieldname, $numchars);
}


function sql_concat_join($separator="' '", $elements=array()) {
    global $DB;
    return $DB->sql_concat_join($separator, $elements);
}

function sql_isempty($tablename, $fieldname, $nullablefield, $textfield) {
    global $DB;
    return $DB->sql_isempty($tablename, $fieldname, $nullablefield, $textfield);
}

function sql_isnotempty($tablename, $fieldname, $nullablefield, $textfield) {
    global $DB;
    return $DB->sql_isnotempty($tablename, $fieldname, $nullablefield, $textfield);
}


function begin_sql() {
    global $DB;
    return $DB->begin_sql();
}

function commit_sql() {
    global $DB;
    return $DB->commit_sql();
}

function rollback_sql() {
    global $DB;
    return $DB->rollback_sql();
}

function insert_record($table, $dataobject, $returnid=true, $primarykey='id') {
    global $DB;

    $dataobject = stripslashes_recursive($dataobject);
    return $DB->insert_record($table, $dataobject, $returnid);
}

function update_record($table, $dataobject) {
    global $DB;

    $dataobject = stripslashes_recursive($dataobject);
    return $DB->update_record($table, $dataobject, true);
}

function get_records($table, $field='', $value='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    global $DB;

    $conditions = array();
    if ($field) {
        $conditions[$field] = stripslashes_recursive($value);
    }

    return $DB->get_records($table, $conditions, $sort, $fields, $limitfrom, $limitnum);
}

function get_record($table, $field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields='*') {
    global $DB;

    $conditions = array();
    if ($field1) {
        $conditions[$field1] = stripslashes_recursive($value1);
    }
    if ($field2) {
        $conditions[$field2] = stripslashes_recursive($value2);
    }
    if ($field3) {
        $conditions[$field3] = stripslashes_recursive($value3);
    }

    return $DB->get_record($table, $conditions, $fields);
}

function set_field($table, $newfield, $newvalue, $field1, $value1, $field2='', $value2='', $field3='', $value3='') {
    global $DB;

    $conditions = array();
    if ($field1) {
        $conditions[$field1] = stripslashes_recursive($value1);
    }
    if ($field2) {
        $conditions[$field2] = stripslashes_recursive($value2);
    }
    if ($field3) {
        $conditions[$field3] = stripslashes_recursive($value3);
    }

    return $DB->set_field($table, $newfield, $newvalue, $conditions);
}

function count_records($table, $field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {
    global $DB;

    $conditions = array();
    if ($field1) {
        $conditions[$field1] = stripslashes_recursive($value1);
    }
    if ($field2) {
        $conditions[$field2] = stripslashes_recursive($value2);
    }
    if ($field3) {
        $conditions[$field3] = stripslashes_recursive($value3);
    }

    return $DB->count_records($table, $conditions);
}

function record_exists($table, $field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {
    global $DB;

    $conditions = array();
    if ($field1) {
        $conditions[$field1] = stripslashes_recursive($value1);
    }
    if ($field2) {
        $conditions[$field2] = stripslashes_recursive($value2);
    }
    if ($field3) {
        $conditions[$field3] = stripslashes_recursive($value3);
    }

    return $DB->record_exists($table, $conditions);
}

function delete_records($table, $field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {
    global $DB;

    $conditions = array();
    if ($field1) {
        $conditions[$field1] = stripslashes_recursive($value1);
    }
    if ($field2) {
        $conditions[$field2] = stripslashes_recursive($value2);
    }
    if ($field3) {
        $conditions[$field3] = stripslashes_recursive($value3);
    }

    return $DB->delete_records($table, $conditions);
}

function get_field($table, $return, $field1, $value1, $field2='', $value2='', $field3='', $value3='') {
    global $DB;

    $conditions = array();
    if ($field1) {
        $conditions[$field1] = stripslashes_recursive($value1);
    }
    if ($field2) {
        $conditions[$field2] = stripslashes_recursive($value2);
    }
    if ($field3) {
        $conditions[$field3] = stripslashes_recursive($value3);
    }

    return $DB->get_field($table, $return, $conditions);
}








///// DELETED - must not be used anymore

function configure_dbconnection() {
    error('configure_dbconnection() removed');
}

function sql_max($field) {
    error('sql_max() removed - use normal sql MAX() instead');
}

function sql_as() {
    error('sql_as() removed - do not use AS for tables at all');
}

function sql_paging_limit($page, $recordsperpage) {
    error('Function sql_paging_limit() is deprecated. Replace it with the correct use of limitfrom, limitnum parameters');
}

function db_uppercase() {
    error('upper() removed - use normal sql UPPER()');
}

function db_lowercase() {
    error('upper() removed - use normal sql LOWER()');
}

function modify_database($sqlfile='', $sqlstring='') {
    error('modify_database() removed - use new XMLDB functions');
}

function where_clause($field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {
    error('where_clause() removed - use new functions with $conditions parameter');
}

function execute_sql_arr($sqlarr, $continue=true, $feedback=true) {
    error('execute_sql_arr() removed');
}
