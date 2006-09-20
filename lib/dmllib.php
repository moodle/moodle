<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-3001 Martin Dougiamas        http://dougiamas.com  //
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

$empty_rs_cache = array();   // Keeps copies of the recordsets used in one invocation

/// FUNCTIONS FOR DATABASE HANDLING  ////////////////////////////////

/**
 * Execute a given sql command string
 *
 * Completely general function - it just runs some SQL and reports success.
 *
 * @uses $db
 * @param string $command The sql string you wish to be executed.
 * @param bool $feedback Set this argument to true if the results generated should be printed. Default is true.
 * @return string
 */
function execute_sql($command, $feedback=true) {
/// Completely general function - it just runs some SQL and reports success.

    global $db, $CFG;

    $olddebug = $db->debug;

    if (!$feedback) {
        $db->debug = false;
    }

    $empty_rs_cache = array();  // Clear out the cache, just in case changes were made to table structures

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    $result = $db->Execute($command);

    $db->debug = $olddebug;

    if ($result) {
        if ($feedback) {
            notify(get_string('success'), 'notifysuccess');
        }
        return true;
    } else {
        if ($feedback) {
            notify('<strong>' . get_string('error') . '</strong>');
        }
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $command");
        }
        return false;
    }
}
/**
* on DBs that support it, switch to transaction mode and begin a transaction
* you'll need to ensure you call commit_sql() or your changes *will* be lost
* this is _very_ useful for massive updates
*/
function begin_sql() {
/// Completely general function - it just runs some SQL and reports success.

    global $CFG;
    if ($CFG->dbtype === 'postgres7') {
        return execute_sql('BEGIN', false);
    }
    return true;
}
/**
* on DBs that support it, commit the transaction 
*/
function rollback_sql() {
/// Completely general function - it just runs some SQL and reports success.

    global $CFG;
    if ($CFG->dbtype === 'postgres7') {
        return execute_sql('ROLLBACK', false);
    }
    return true;
}



/**
 * returns db specific uppercase function
 * @deprecated Moodle 1.7 because all the RDBMS use upper()
 */
function db_uppercase() {
    return "upper";
}

/**
 * returns db specific lowercase function
 * @deprecated Moodle 1.7 because all the RDBMS use lower()
 */
function db_lowercase() {
    return "lower";
}

/**
* on DBs that support it, commit the transaction 
*/
function commit_sql() {
/// Completely general function - it just runs some SQL and reports success.

    global $CFG;
    if ($CFG->dbtype === 'postgres7') {
        return execute_sql('COMMIT', false);
    }
    return true;
}

/**
 * Run an arbitrary sequence of semicolon-delimited SQL commands
 *
 * Assumes that the input text (file or string) consists of
 * a number of SQL statements ENDING WITH SEMICOLONS.  The
 * semicolons MUST be the last character in a line.
 * Lines that are blank or that start with "#" or "--" (postgres) are ignored.
 * Only tested with mysql dump files (mysqldump -p -d moodle)
 *
 * @uses $CFG
 * @param string $sqlfile The path where a file with sql commands can be found on the server.
 * @param string $sqlstring If no path is supplied then a string with semicolon delimited sql 
 * commands can be supplied in this argument.
 * @return bool Returns true if databse was modified successfully.
 */
function modify_database($sqlfile='', $sqlstring='') {

    global $CFG;

    $success = true;  // Let's be optimistic

    if (!empty($sqlfile)) {
        if (!is_readable($sqlfile)) {
            $success = false;
            echo '<p>Tried to modify database, but "'. $sqlfile .'" doesn\'t exist!</p>';
            return $success;
        } else {
            $lines = file($sqlfile);
        }
    } else {
        $sqlstring = trim($sqlstring);
        if ($sqlstring{strlen($sqlstring)-1} != ";") {
            $sqlstring .= ";"; // add it in if it's not there.
        }
        $lines[] = $sqlstring;
    }

    $command = '';

    foreach ($lines as $line) {
        $line = rtrim($line);
        $length = strlen($line);

        if ($length and $line[0] <> '#' and $line[0].$line[1] <> '--') {
            if (substr($line, $length-1, 1) == ';') {
                $line = substr($line, 0, $length-1);   // strip ;
                $command .= $line;
                $command = str_replace('prefix_', $CFG->prefix, $command); // Table prefixes
                if (! execute_sql($command)) {
                    $success = false;
                }
                $command = '';
            } else {
                $command .= $line;
            }
        }
    }

    return $success;

}

/// GENERIC FUNCTIONS TO CHECK AND COUNT RECORDS ////////////////////////////////////////

/**
 * Test whether a record exists in a table where all the given fields match the given values.
 *
 * The record to test is specified by giving up to three fields that must
 * equal the corresponding values.
 *
 * @uses $CFG
 * @param string $table The table to check.
 * @param string $field1 the first field to check (optional).
 * @param string $value1 the value field1 must have (requred if field1 is given, else optional).
 * @param string $field2 the second field to check (optional).
 * @param string $value2 the value field2 must have (requred if field2 is given, else optional).
 * @param string $field3 the third field to check (optional).
 * @param string $value3 the value field3 must have (requred if field3 is given, else optional).
 * @return bool true if a matching record exists, else false.
 */
function record_exists($table, $field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {

    global $CFG;

    $select = where_clause($field1, $value1, $field2, $value2, $field3, $value3);

    return record_exists_sql('SELECT * FROM '. $CFG->prefix . $table .' '. $select);
}

/**
 * Test whether any records exists in a table which match a particular WHERE clause.
 *
 * @uses $CFG
 * @param string $table The database table to be checked against.
 * @param string $select A fragment of SQL to be used in a WHERE clause in the SQL call.
 * @return bool true if a matching record exists, else false.
 */
function record_exists_select($table, $select='') {

    global $CFG;

    if ($select) {
        $select = 'WHERE '.$select;
    }

    return record_exists_sql('SELECT * FROM '. $CFG->prefix . $table . ' ' . $select);
}

/**
 * Test whether a SQL SELECT statement returns any records.
 *
 * This function returns true if the SQL statement executes
 * without any errors and returns at least one record.
 *
 * @param string $sql The SQL statement to execute.
 * @return bool true if the SQL executes without errors and returns at least one record.
 */
function record_exists_sql($sql) {

    $limitfrom = 0; /// Number of records to skip
    $limitnum  = 1; /// Number of records to retrieve

    $rs = get_recordset_sql($sql, $limitfrom, $limitnum);

    if ($rs && $rs->RecordCount() > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * Count the records in a table where all the given fields match the given values.
 *
 * @uses $CFG
 * @param string $table The table to query.
 * @param string $field1 the first field to check (optional).
 * @param string $value1 the value field1 must have (requred if field1 is given, else optional).
 * @param string $field2 the second field to check (optional).
 * @param string $value2 the value field2 must have (requred if field2 is given, else optional).
 * @param string $field3 the third field to check (optional).
 * @param string $value3 the value field3 must have (requred if field3 is given, else optional).
 * @return int The count of records returned from the specified criteria.
 */
function count_records($table, $field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {

    global $CFG;

    $select = where_clause($field1, $value1, $field2, $value2, $field3, $value3);

    return count_records_sql('SELECT COUNT(*) FROM '. $CFG->prefix . $table .' '. $select);
}

/**
 * Count the records in a table which match a particular WHERE clause.
 *
 * @uses $CFG
 * @param string $table The database table to be checked against.
 * @param string $select A fragment of SQL to be used in a WHERE clause in the SQL call.
 * @param string $countitem The count string to be used in the SQL call. Default is COUNT(*).
 * @return int The count of records returned from the specified criteria.
 */
function count_records_select($table, $select='', $countitem='COUNT(*)') {

    global $CFG;

    if ($select) {
        $select = 'WHERE '.$select;
    }

    return count_records_sql('SELECT '. $countitem .' FROM '. $CFG->prefix . $table .' '. $select);
}

/**
 * Get the result of a SQL SELECT COUNT(...) query.
 *
 * Given a query that counts rows, return that count. (In fact,
 * given any query, return the first field of the first record
 * returned. However, this method should only be used for the
 * intended purpose.) If an error occurrs, 0 is returned.
 *
 * @uses $CFG
 * @uses $db
 * @param string $sql The SQL string you wish to be executed.
 * @return int the count. If an error occurrs, 0 is returned.
 */
function count_records_sql($sql) {
    $rs = get_recordset_sql($sql);
    
    if ($rs) {
        return reset($rs->fields);
    } else {
        return 0;   
    }
}

/// GENERIC FUNCTIONS TO GET, INSERT, OR UPDATE DATA  ///////////////////////////////////

/**
 * Get a single record as an object
 *
 * @uses $CFG
 * @param string $table The table to select from.
 * @param string $field1 the first field to check (optional).
 * @param string $value1 the value field1 must have (requred if field1 is given, else optional).
 * @param string $field2 the second field to check (optional).
 * @param string $value2 the value field2 must have (requred if field2 is given, else optional).
 * @param string $field3 the third field to check (optional).
 * @param string $value3 the value field3 must have (requred if field3 is given, else optional).
 * @return mixed a fieldset object containing the first mathcing record, or false if none found.
 */
function get_record($table, $field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields='*') {

    global $CFG;

    $select = where_clause($field1, $value1, $field2, $value2, $field3, $value3);

    return get_record_sql('SELECT '.$fields.' FROM '. $CFG->prefix . $table .' '. $select);
}

/**
 * Get a single record as an object using an SQL statement
 *
 * The SQL statement should normally only return one record. In debug mode
 * you will get a warning if more record is returned (unless you
 * set $expectmultiple to true). In non-debug mode, it just returns
 * the first record. 
 *
 * @uses $CFG
 * @uses $db
 * @param string $sql The SQL string you wish to be executed, should normally only return one record.
 * @param bool $expectmultiple If the SQL cannot be written to conviniently return just one record, 
 *      set this to true to hide the debug message.
 * @param bool $nolimit sometimes appending ' LIMIT 1' to the SQL causes an error. Set this to true
 *      to stop your SQL being modified. This argument should probably be deprecated.
 * @return Found record as object. False if not found or error
 */
function get_record_sql($sql, $expectmultiple=false, $nolimit=false) {

    global $CFG;

/// Default situation
    $limitfrom = 0; /// Number of records to skip
    $limitnum  = 1; /// Number of records to retrieve

/// Only a few uses of the 2nd and 3rd parameter have been found
/// I think that we should avoid to use them completely, one
/// record is one record, and everything else should return error.
/// So the proposal is to change all the uses, (4-5 inside Moodle 
/// Core), drop them from the definition and delete the next two
/// "if" sentences. (eloy, 2006-08-19)

    if ($nolimit) {
        $limitfrom = 0;
        $limitnum  = 0;
    } else if ($expectmultiple) {
        $limitfrom = 0;
        $limitnum  = 1;
    } else if (debugging()) {
        // Debugging mode - don't use a limit of 1, but do change the SQL, because sometimes that
        // causes errors, and in non-debug mode you don't see the error message and it is 
        // impossible to know what's wrong.
        $limitfrom = 0;
        $limitnum  = 100;
    }

    if (!$rs = get_recordset_sql($sql, $limitfrom, $limitnum)) {
        return false;   
    }
    
    $recordcount = $rs->RecordCount();

    if ($recordcount == 0) {          // Found no records
        return false; 

    } else if ($recordcount == 1) {    // Found one record
    /// DIRTY HACK to retrieve all the ' ' (1 space) fields converted back
    /// to '' (empty string) for Oracle. It's the only way to work with
    /// all those NOT NULL DEFAULT '' fields until we definetively delete them
        if ($CFG->dbtype == 'oci8po') {
            array_walk($rs->fields, 'onespace2empty');
        }
    /// End od DIRTY HACK
        return (object)$rs->fields;

    } else {                          // Error: found more than one record
        notify('Error:  Turn off debugging to hide this error.');
        notify($sql . '(with limits ' . $limitfrom . ', ' . $limitnum . ')');
        if ($records = $rs->GetAssoc(true)) {
            notify('Found more than one record in get_record_sql !');
            print_object($records);
        } else {
            notify('Very strange error in get_record_sql !');
            print_object($rs);
        }
        print_continue("$CFG->wwwroot/$CFG->admin/config.php");
    }
}

/**
 * Gets one record from a table, as an object
 *
 * @uses $CFG
 * @param string $table The database table to be checked against.
 * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
 * @param string $fields A comma separated list of fields to be returned from the chosen table.
 * @return object|false Returns an array of found records (as objects) or false if no records or error occured.
 */
function get_record_select($table, $select='', $fields='*') {

    global $CFG;

    if ($select) {
        $select = 'WHERE '. $select;
    }

    return get_record_sql('SELECT '. $fields .' FROM '. $CFG->prefix . $table .' '. $select);
}

/**
 * Get a number of records as an ADODB RecordSet.
 *
 * Selects records from the table $table.
 * 
 * If specified, only records where the field $field has value $value are retured.
 * 
 * If specified, the results will be sorted as specified by $sort. This
 * is added to the SQL as "ORDER BY $sort". Example values of $sort
 * mightbe "time ASC" or "time DESC".
 * 
 * If $fields is specified, only those fields are returned.
 *
 * This function is internal to datalib, and should NEVER should be called directly 
 * from general Moodle scripts.  Use get_record, get_records etc.
 * 
 * If you only want some of the records, specify $limitfrom and $limitnum.
 * The query will skip the first $limitfrom records (according to the sort
 * order) and then return the next $limitnum records. If either of $limitfrom
 * or $limitnum is specified, both must be present.
 * 
 * The return value is an ADODB RecordSet object
 * @link http://phplens.com/adodb/reference.functions.adorecordset.html
 * if the query succeeds. If an error occurrs, false is returned.
 *
 * @param string $table the table to query.
 * @param string $field a field to check (optional).
 * @param string $value the value the field must have (requred if field1 is given, else optional).
 * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
 * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an ADODB RecordSet object, or false if an error occured.
 */
function get_recordset($table, $field='', $value='', $sort='', $fields='*', $limitfrom='', $limitnum='') {

    if ($field) {
        $select = "$field = '$value'";
    } else {
        $select = '';
    }
    
    return get_recordset_select($table, $select, $sort, $fields, $limitfrom, $limitnum);
}

/**
 * Get a number of records as an ADODB RecordSet.
 *
 * If given, $select is used as the SELECT parameter in the SQL query,
 * otherwise all records from the table are returned.
 * 
 * Other arguments and the return type as for @see function get_recordset. 
 *
 * @uses $CFG
 * @param string $table the table to query.
 * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
 * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
 * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an ADODB RecordSet object, or false if an error occured.
 */
function get_recordset_select($table, $select='', $sort='', $fields='*', $limitfrom='', $limitnum='') {

    global $CFG;

    if ($select) {
        $select = ' WHERE '. $select;
    }

    if ($limitfrom !== '') {
        $limit = sql_paging_limit($limitfrom, $limitnum);
    } else {
        $limit = '';
    }

    if ($sort) {
        $sort = ' ORDER BY '. $sort;
    }

    return get_recordset_sql('SELECT '. $fields .' FROM '. $CFG->prefix . $table . $select . $sort .' '. $limit);
}

/**
 * Get a number of records as an ADODB RecordSet.
 *
 * Only records where $field takes one of the values $values are returned.
 * $values should be a comma-separated list of values, for example "4,5,6,10"
 * or "'foo','bar','baz'".
 * 
 * Other arguments and the return type as for @see function get_recordset. 
 *
 * @param string $table the table to query.
 * @param string $field a field to check (optional).
 * @param string $values comma separated list of values the field must have (requred if field is given, else optional).
 * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
 * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an ADODB RecordSet object, or false if an error occured.
 */
function get_recordset_list($table, $field='', $values='', $sort='', $fields='*', $limitfrom='', $limitnum='') {

    if ($field) {
        $select = "$field IN ($values)";
    } else {
        $select = '';
    }

    return get_recordset_select($table, $select, $sort, $fields, $limitfrom, $limitnum);
}

/**
 * Get a number of records as an ADODB RecordSet.  $sql must be a complete SQL query.
 * This function is internal to datalib, and should NEVER should be called directly 
 * from general Moodle scripts.  Use get_record, get_records etc.
 *  
 * The return type is as for @see function get_recordset. 
 *
 * @uses $CFG
 * @uses $db
 * @param string $sql the SQL select query to execute.
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an ADODB RecordSet object, or false if an error occured.
 */
function get_recordset_sql($sql, $limitfrom=null, $limitnum=null) {
    global $CFG, $db;

    if (empty($db)) {
        return false;
    }

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    if ($limitfrom || $limitnum) {
        ///Special case, 0 must be -1 for ADOdb
        $limitfrom = empty($limitfrom) ? -1 : $limitfrom;
        $limitnum  = empty($limitnum) ? -1 : $limitnum;
        $rs = $db->SelectLimit($sql, $limitnum, $limitfrom);
    } else {
        $rs = $db->Execute($sql);
    }
    if (!$rs) {
        debugging($db->ErrorMsg() .'<br /><br />'. $sql);
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $sql with limits ($limitfrom, $limitnum)");
        }
        return false;
    }

    return $rs;
}

/**
 * Utility function used by the following 4 methods.
 * 
 * @param object an ADODB RecordSet object.
 * @return mixed mixed an array of objects, or false if an error occured or the RecordSet was empty.
 */
function recordset_to_array($rs) {

    global $CFG;

    if ($rs && $rs->RecordCount() > 0) {
    /// First of all, we are going to get the name of the first column
    /// to introduce it back after transforming the recordset to assoc array
    /// See http://docs.moodle.org/en/XMLDB_Problems, fetch mode problem.
        $firstcolumn = $rs->FetchField(0);
    /// Get the whole associative array
        if ($records = $rs->GetAssoc(true)) {
            foreach ($records as $key => $record) {
            /// Really DIRTY HACK for Oracle, but it's the only way to make it work
            /// until we got all those NOT NULL DEFAULT '' out from Moodle
                if ($CFG->dbtype == 'oci8po') {
                    array_walk($record, 'onespace2empty');
                }
            /// End of DIRTY HACK
                $record[$firstcolumn->name] = $key;/// Re-add the assoc field
                $objects[$key] = (object) $record; /// To object
            }
            return $objects;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/** 
 * This function is used to convert all the Oracle 1-space defaults to the empty string
 * like a really DIRTY HACK to allow it to work better until all those NOT NULL DEFAULT ''
 * fields will be out from Moodle.
 * @param string the string to be converted to '' (empty string) if it's ' ' (one space)
 */
function onespace2empty(&$item, $key) {
    $item = $item == ' ' ? '' : $item;
    return true;
}


/**
 * Get a number of records as an array of objects.
 *
 * If the query succeeds and returns at least one record, the
 * return value is an array of objects, one object for each
 * record found. The array key is the value from the first 
 * column of the result set. The object associated with that key
 * has a member variable for each column of the results.
 *
 * @param string $table the table to query.
 * @param string $field a field to check (optional).
 * @param string $value the value the field must have (requred if field1 is given, else optional).
 * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
 * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an array of objects, or false if no records were found or an error occured.
 */
function get_records($table, $field='', $value='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    $rs = get_recordset($table, $field, $value, $sort, $fields, $limitfrom, $limitnum);
    return recordset_to_array($rs);
}

/**
 * Get a number of records as an array of objects.
 *
 * Return value as for @see function get_records.
 *
 * @param string $table the table to query.
 * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
 * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
 * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an array of objects, or false if no records were found or an error occured.
 */
function get_records_select($table, $select='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    $rs = get_recordset_select($table, $select, $sort, $fields, $limitfrom, $limitnum);
    return recordset_to_array($rs);
}

/**
 * Get a number of records as an array of objects.
 *
 * Return value as for @see function get_records.
 *
 * @param string $table The database table to be checked against.
 * @param string $field The field to search
 * @param string $values Comma separated list of possible value
 * @param string $sort Sort order (as valid SQL sort parameter)
 * @param string $fields A comma separated list of fields to be returned from the chosen table.
 * @return mixed an array of objects, or false if no records were found or an error occured.
 */
function get_records_list($table, $field='', $values='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    $rs = get_recordset_list($table, $field, $values, $sort, $fields, $limitfrom, $limitnum);
    return recordset_to_array($rs);
}

/**
 * Get a number of records as an array of objects.
 *
 * Return value as for @see function get_records.
 * 
 * @param string $sql the SQL select query to execute.
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an array of objects, or false if no records were found or an error occured.
 */
function get_records_sql($sql, $limitfrom='', $limitnum='') {
    $rs = get_recordset_sql($sql, $limitfrom, $limitnum);
    return recordset_to_array($rs);
}

/**
 * Utility function used by the following 3 methods.
 * 
 * @param object an ADODB RecordSet object with two columns.
 * @return mixed an associative array, or false if an error occured or the RecordSet was empty.
 */
function recordset_to_menu($rs) {
    if ($rs && $rs->RecordCount() > 0) {
        $keys = array_keys($rs->fields);
        $key0=$keys[0];
        $key1=$keys[1];
        while (!$rs->EOF) {
            $menu[$rs->fields[$key0]] = $rs->fields[$key1];
            $rs->MoveNext();
        }
        return $menu;
    } else {
        return false;
    }
}

/**
 * Get the first two columns from a number of records as an associative array.
 *
 * Arguments as for @see function get_recordset.
 * 
 * If no errors occur, and at least one records is found, the return value
 * is an associative whose keys come from the first field of each record,
 * and whose values are the corresponding second fields. If no records are found,
 * or an error occurs, false is returned.
 *
 * @param string $table the table to query.
 * @param string $field a field to check (optional).
 * @param string $value the value the field must have (requred if field1 is given, else optional).
 * @param string $sort an order to sort the results in (optional, a valid SQL ORDER BY parameter).
 * @param string $fields a comma separated list of fields to return (optional, by default all fields are returned).
 * @return mixed an associative array, or false if no records were found or an error occured.
 */
function get_records_menu($table, $field='', $value='', $sort='', $fields='*') {
    $rs = get_recordset($table, $field, $value, $sort, $fields);
    return recordset_to_menu($rs);
}

/**
 * Get the first two columns from a number of records as an associative array.
 *
 * Arguments as for @see function get_recordset_select.
 * Return value as for @see function get_records_menu.
 *
 * @param string $table The database table to be checked against.
 * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
 * @param string $sort Sort order (optional) - a valid SQL order parameter
 * @param string $fields A comma separated list of fields to be returned from the chosen table.
 * @return mixed an associative array, or false if no records were found or an error occured.
 */
function get_records_select_menu($table, $select='', $sort='', $fields='*') {
    $rs = get_recordset_select($table, $select, $sort, $fields);
    return recordset_to_menu($rs);
}

/**
 * Get the first two columns from a number of records as an associative array.
 *
 * Arguments as for @see function get_recordset_sql.
 * Return value as for @see function get_records_menu.
 *
 * @param string $sql The SQL string you wish to be executed.
 * @return mixed an associative array, or false if no records were found or an error occured.
 */
function get_records_sql_menu($sql) {
    $rs = get_recordset_sql($sql);
    return recordset_to_menu($rs);
}

/**
 * Get a single value from a table row where all the given fields match the given values.
 *
 * @param string $table the table to query.
 * @param string $return the field to return the value of.
 * @param string $field1 the first field to check (optional).
 * @param string $value1 the value field1 must have (requred if field1 is given, else optional).
 * @param string $field2 the second field to check (optional).
 * @param string $value2 the value field2 must have (requred if field2 is given, else optional).
 * @param string $field3 the third field to check (optional).
 * @param string $value3 the value field3 must have (requred if field3 is given, else optional).
 * @return mixed the specified value, or false if an error occured.
 */
function get_field($table, $return, $field1, $value1, $field2='', $value2='', $field3='', $value3='') {
    global $CFG;
    $select = where_clause($field1, $value1, $field2, $value2, $field3, $value3);
    return get_field_sql('SELECT ' . $return . ' FROM ' . $CFG->prefix . $table . ' ' . $select);
}

/**
 * Get a single value from a table row where a particular select clause is true.
 *
 * @uses $CFG
 * @param string $table the table to query.
 * @param string $return the field to return the value of.
 * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
 * @return mixed the specified value, or false if an error occured.
 */
function get_field_select($table, $return, $select) {
    global $CFG;
    if ($select) {
        $select = 'WHERE '. $select;
    }
    return get_field_sql('SELECT ' . $return . ' FROM ' . $CFG->prefix . $table . ' ' . $select);
}

/**
 * Get a single value from a table.
 *
 * @param string $sql an SQL statement expected to return a single value.
 * @return mixed the specified value, or false if an error occured.
 */
function get_field_sql($sql) {

    $rs = get_recordset_sql($sql);

    if ($rs && $rs->RecordCount() == 1) {
        return reset($rs->fields);
    } else {
        return false;
    }
}

/**
 * Get an array of data from one or more fields from a database 
 * use to get a column, or a series of distinct values
 *
 * @uses $CFG
 * @uses $db
 * @param string $sql The SQL string you wish to be executed.
 * @return mixed|false Returns the value return from the SQL statment or false if an error occured.
 * @todo Finish documenting this function
 */
function get_fieldset_sql($sql) {

    global $db, $CFG;

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    $rs = $db->Execute($sql);
    if (!$rs) {
        debugging($db->ErrorMsg() .'<br /><br />'. $sql);
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $sql");
        }
        return false;
    }

    if ( $rs->RecordCount() > 0 ) {
        $keys = array_keys($rs->fields);
        $key0 = $keys[0];
        $results = array();
        while (!$rs->EOF) {
            array_push($results, $rs->fields[$key0]);
            $rs->MoveNext();
        }
        return $results;
    } else {
        return false;
    }
}

/**
 * Set a single field in every table row where all the given fields match the given values.
 *
 * @uses $CFG
 * @uses $db
 * @param string $table The database table to be checked against.
 * @param string $newfield the field to set.
 * @param string $newvalue the value to set the field to.
 * @param string $field1 the first field to check (optional).
 * @param string $value1 the value field1 must have (requred if field1 is given, else optional).
 * @param string $field2 the second field to check (optional).
 * @param string $value2 the value field2 must have (requred if field2 is given, else optional).
 * @param string $field3 the third field to check (optional).
 * @param string $value3 the value field3 must have (requred if field3 is given, else optional).
 * @return mixed An ADODB RecordSet object with the results from the SQL call or false.
 */
function set_field($table, $newfield, $newvalue, $field1, $value1, $field2='', $value2='', $field3='', $value3='') {

    global $db, $CFG;

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    $select = where_clause($field1, $value1, $field2, $value2, $field3, $value3);

    return $db->Execute('UPDATE '. $CFG->prefix . $table .' SET '. $newfield  .' = \''. $newvalue .'\' '. $select);
}

/**
 * Delete the records from a table where all the given fields match the given values.
 *
 * @uses $CFG
 * @uses $db
 * @param string $table the table to delete from.
 * @param string $field1 the first field to check (optional).
 * @param string $value1 the value field1 must have (requred if field1 is given, else optional).
 * @param string $field2 the second field to check (optional).
 * @param string $value2 the value field2 must have (requred if field2 is given, else optional).
 * @param string $field3 the third field to check (optional).
 * @param string $value3 the value field3 must have (requred if field3 is given, else optional).
 * @return mixed An ADODB RecordSet object with the results from the SQL call or false.
 */
function delete_records($table, $field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {

    global $db, $CFG;

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    $select = where_clause($field1, $value1, $field2, $value2, $field3, $value3);

    return $db->Execute('DELETE FROM '. $CFG->prefix . $table .' '. $select);
}

/**
 * Delete one or more records from a table
 *
 * @uses $CFG
 * @uses $db
 * @param string $table The database table to be checked against.
 * @param string $select A fragment of SQL to be used in a where clause in the SQL call (used to define the selection criteria).
 * @return object A PHP standard object with the results from the SQL call.
 * @todo Verify return type.
 */
function delete_records_select($table, $select='') {

    global $CFG, $db;

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    if ($select) {
        $select = 'WHERE '.$select;
    }

    return $db->Execute('DELETE FROM '. $CFG->prefix . $table .' '. $select);
}

/**
 * Insert a record into a table and return the "id" field if required
 *
 * If the return ID isn't required, then this just reports success as true/false.
 * $dataobject is an object containing needed data
 *
 * @uses $db
 * @uses $CFG
 * @param string $table The database table to be checked against.
 * @param array $dataobject A data object with values for one or more fields in the record
 * @param bool $returnid Should the id of the newly created record entry be returned? If this option is not requested then true/false is returned.
 * @param string $primarykey The primary key of the table we are inserting into (almost always "id")
 */
function insert_record($table, $dataobject, $returnid=true, $primarykey='id') {

    global $db, $CFG, $empty_rs_cache;

    if (empty($db)) {
        return false;
    }

/// DIRTY HACK: Implement one cache to store meta data (needed by Oracle inserts)
/// TODO: Possibly make it global to benefit other functions needing it (update_record...)
    if (!isset($metadatacache)) {
        $metadatacache = array();
    }
/// End DIRTY HACK

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
    if ( $CFG->dbtype === 'postgres7' && $returnid == true ) {
        if ($nextval = (int)get_field_sql("SELECT NEXTVAL('{$CFG->prefix}{$table}_{$primarykey}_seq')")) {
            $dataobject->{$primarykey} = $nextval;            
        } 
    }

/// First basic support of insert for Oracle. As it doesn't 
/// support autogenerated fields, we rely on the corresponding
/// sequence. It will work automatically, unless we need to
/// return the primary from the function, in this case we
/// get the next sequence value here and insert it manually.
    if ( $CFG->dbtype === 'oci8po' && $returnid == true) {
    /// We need this here (move this function to dmlib?)
        include_once($CFG->libdir . '/xmldb/classes/generators/XMLDBGenerator.class.php');
        include_once($CFG->libdir . '/xmldb/classes/generators/oci8po/oci8po.class.php');
        $generator = new XMLDBoci8po();
        $generator->setPrefix($CFG->prefix);
        $seqname = $generator->getNameForObject($table, $primarykey, 'seq');
        if ($nextval = (int)get_field_sql("SELECT $seqname.NEXTVAL from dual")) {
            $dataobject->{$primarykey} = $nextval;            
        }
    }
/// Also, for Oracle DB, empty strings are converted to NULLs in DB
/// and this breaks a lot of NOT NULL columns currenty Moodle. In the future it's
/// planned to move some of them to NULL, if they must accept empty values and this
/// piece of code will become less and less used. But, for now, we need it.
/// What we are going to do is to examine all the data being inserted and if it's
/// an empty string (NULL for Oracle) and the field is defined as NOT NULL, we'll modify
/// such data in the best form possible ("0" for booleans and numbers and " " for the
/// rest of strings. It isn't optimal, but the only way to do so. 
/// In the oppsite, when retrieving records from Oracle, we'll decode " " back to
/// empty strings to allow everything to work properly. DIRTY HACK.
    if ( $CFG->dbtype === 'oci8po') {
    /// Get Meta info to know what to change, using the cached meta if exists
        if (!isset($metadatacache[$table])) {
            $metadatacache[$table] = array_change_key_case($db->MetaColumns($CFG->prefix . $table), CASE_LOWER);
        }
        $columns = $metadatacache[$table];
    /// Iterate over all the fields in the insert, transforming values
    /// in the best possible form
        foreach ($dataobject as $fieldname => $fieldvalue) {
        /// If the field doesn't exist in metadata, skip
            if (!isset($columns[strtolower($fieldname)])) {
                continue;
            }
        /// If the field ins't VARCHAR or CLOB, skip
            if ($columns[strtolower($fieldname)]->type != 'VARCHAR2' && $columns[strtolower($fieldname)]->type != 'CLOB') {
                continue;
            }
        /// If the field isn't NOT NULL, skip (it's nullable, so accept empty values)
            if (!$columns[strtolower($fieldname)]->not_null) {
                continue;
            }
        /// If the value isn't empty, skip
            if (!empty($fieldvalue)) {
                continue;
            }
        /// Now, we have one empty value, going to be inserted to one NOT NULL, VARCHAR2 or CLOB field
        /// Try to get the best value to be inserted
            if (gettype($fieldvalue) == 'boolean') {
                $dataobject->$fieldname = '0'; /// Transform false to '0' that evaluates the same for PHP
            } else if (gettype($fieldvalue) == 'integer') {
                $dataobject->$fieldname = '0'; /// Transform 0 to '0' that evaluates the same for PHP
            } else if (gettype($fieldvalue) == 'NULL') {
                $dataobject->$fieldname = '0'; /// Transform NULL to '0' that evaluates the same for PHP
            } else {
                $dataobject->$fieldname = ' '; /// Transform '' to ' ' that DONT'T EVALUATE THE SAME
                                               /// (we'll transform back again on get_records_XXX functions)!!
            }
        }
    }
/// End of DIRTY HACK

/// Get the correct SQL from adoDB
    if (!$insertSQL = $db->GetInsertSQL($rs, (array)$dataobject, true)) {
        return false;
    }

/// Run the SQL statement
    if (!$rs = $db->Execute($insertSQL)) {
        debugging($db->ErrorMsg() .'<br /><br />'.$insertSQL);
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $insertSQL");
        }
        return false;
    }

/// If a return ID is not needed then just return true now
    if (!$returnid) {
        return true;
    }

/// We already know the record PK if it's been passed explicitly,
/// or if we've retrieved it from a sequence (Postgres).
    if (!empty($dataobject->{$primarykey})) {
        return $dataobject->{$primarykey};
    }

/// This only gets triggered with non-Postgres databases
/// however we have some postgres fallback in case we failed 
/// to find the sequence.
    $id = $db->Insert_ID();  

    if ($CFG->dbtype === 'postgres7') {
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

/**
 * Update a record in a table
 *
 * $dataobject is an object containing needed data
 * Relies on $dataobject having a variable "id" to
 * specify the record to update
 *
 * @uses $CFG
 * @uses $db
 * @param string $table The database table to be checked against.
 * @param array $dataobject An object with contents equal to fieldname=>fieldvalue. Must have an entry for 'id' to map to the table specified.
 * @return bool
 */
function update_record($table, $dataobject) {

    global $db, $CFG;

    if (! isset($dataobject->id) ) {
        return false;
    }

    // Determine all the fields in the table
    if (!$columns = $db->MetaColumns($CFG->prefix . $table)) {
        return false;
    }
    $data = (array)$dataobject;

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    // Pull out data matching these fields
    foreach ($columns as $column) {
        if ($column->name <> 'id' and isset($data[$column->name]) ) {
            $ddd[$column->name] = $data[$column->name];
            // PostgreSQL bytea support
            if ($CFG->dbtype == 'postgres7' && $column->type == 'bytea') {
                $ddd[$column->name] = $db->BlobEncode($ddd[$column->name]);
            }
        }
    }

    // Construct SQL queries
    $numddd = count($ddd);
    $count = 0;
    $update = '';

    foreach ($ddd as $key => $value) {
        $count++;
        $update .= $key .' = \''. $value .'\'';   // All incoming data is already quoted
        if ($count < $numddd) {
            $update .= ', ';
        }
    }

    if ($rs = $db->Execute('UPDATE '. $CFG->prefix . $table .' SET '. $update .' WHERE id = \''. $dataobject->id .'\'')) {
        return true;
    } else {
        debugging($db->ErrorMsg() .'<br /><br />UPDATE '. $CFG->prefix . $table .' SET '. $update .' WHERE id = \''. $dataobject->id .'\'');
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  UPDATE $CFG->prefix$table SET $update WHERE id = '$dataobject->id'");
        }
        return false;
    }
}



/**
 * Returns the proper SQL to do paging
 *
 * @uses $CFG
 * @param string $page Offset page number
 * @param string $recordsperpage Number of records per page
 * @deprecated Moodle 1.7 use the new $limitfrom, $limitnum available in all
 *             the get_recordXXX() funcions.
 * @return string
 */
function sql_paging_limit($page, $recordsperpage) {
    global $CFG;

    switch ($CFG->dbtype) {
        case 'postgres7':
             return 'LIMIT '. $recordsperpage .' OFFSET '. $page;
        default:
             return 'LIMIT '. $page .','. $recordsperpage;
    }
}

/**
 * Returns the proper SQL to do LIKE in a case-insensitive way
 *
 * @uses $CFG
 * @return string
 */
function sql_ilike() {
    global $CFG;

    switch ($CFG->dbtype) {
        case 'mysql':
             return 'LIKE';
        default:
             return 'ILIKE';
    }
}


/**
 * Returns the proper SQL to do LIKE in a case-insensitive way
 *
 * @uses $CFG
 * @param string $firstname User's first name
 * @param string $lastname User's last name
 * @return string
 */
function sql_fullname($firstname='firstname', $lastname='lastname') {
    global $CFG;

    switch ($CFG->dbtype) {
        case 'mysql':
             return ' CONCAT('. $firstname .'," ",'. $lastname .') ';
        case 'postgres7':
             return " ". $firstname ."||' '||". $lastname ." ";
        case 'mssql':
             return " ". $firstname ."+' '+". $lastname ." ";
        default:
             return ' '. $firstname .'||" "||'. $lastname .' ';
    }
}

/**
 * Returns the proper SQL to do IS NULL
 * @uses $CFG
 * @param string $fieldname The field to add IS NULL to
 * @return string
 */
function sql_isnull($fieldname) {
    global $CFG;

    switch ($CFG->dbtype) {
        case 'mysql':
             return $fieldname.' IS NULL';
        default:
             return $fieldname.' IS NULL';
    }
}

/** 
 * Returns the proper AS keyword to be used to aliase columns
 * SQL defines the keyword as optional and nobody but PG
 * seems to require it. This function should be used inside all
 * the statements using column aliases.
 * Note than the use of table aliases doesn't require the
 * AS keyword at all, only columns for postgres.
 * @uses $CFG
 * @ return string the keyword
 */
function sql_as() {
    global $CFG, $db;

    switch ($CFG->dbtype) {
        case 'postgres7':
            return 'AS';
        default:
            return '';
    }
}

/**
 * Returns the SQL text to be used to order by one TEXT (clob) column, because
 * some RDBMS doesn't support direct ordering of such fields. 
 * Note that the use or queries being ordered by TEXT columns must be minimised,
 * because it's really slooooooow.
 * @param string fieldname the name of the TEXT field we need to order by
 * @param string number of chars to use for the ordering (defaults to 32)
 * @return string the piece of SQL code to be used in your statement.
 */
function sql_order_by_text($fieldname, $numchars=32) {

    global $CFG;

    switch ($CFG->dbtype) {
        case 'mssql':
            return 'CONVERT(varchar, ' . $fieldname . ', ' . $numchars . ')';
            break;
        case 'oci8po':
            return 'dbms_lob.substr(' . $fieldname . ', ' . $numchars . ',1)';
            break;
        default:
            return $fieldname;
    }
}

/** 
 * Prepare a SQL WHERE clause to select records where the given fields match the given values.
 * 
 * Prepares a where clause of the form
 *     WHERE field1 = value1 AND field2 = value2 AND field3 = value3
 * except that you need only specify as many arguments (zero to three) as you need.
 * 
 * @param string $field1 the first field to check (optional).
 * @param string $value1 the value field1 must have (requred if field1 is given, else optional).
 * @param string $field2 the second field to check (optional).
 * @param string $value2 the value field2 must have (requred if field2 is given, else optional).
 * @param string $field3 the third field to check (optional).
 * @param string $value3 the value field3 must have (requred if field3 is given, else optional).
 */
function where_clause($field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {
    if ($field1) {
        $select = "WHERE $field1 = '$value1'";
        if ($field2) {
            $select .= " AND $field2 = '$value2'";
            if ($field3) {
                $select .= " AND $field3 = '$value3'";
            }
        }
    } else {
        $select = '';
    }
    return $select;
}

/**
 * Get the data type of a table column, using an ADOdb MetaType() call.
 *
 * @uses $CFG
 * @uses $db
 * @param string $table The name of the database table
 * @param string $column The name of the field in the table
 * @return string Field type or false if error
 */

function column_type($table, $column) {
    global $CFG, $db;

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    if(!$rs = $db->Execute('SELECT '.$column.' FROM '.$CFG->prefix.$table.' LIMIT 0')) {
        return false;
    }

    $field = $rs->FetchField(0);
    return $rs->MetaType($field->type);
}

/**
 * This function will execute an array of SQL commands, returning
 * true/false if any error is found and stopping/continue as desired.
 * It's widely used by all the ddllib.php functions
 *
 * @param array sqlarr array of sql statements to execute
 * @param boolean continue to specify if must continue on error (true) or stop (false)
 * @param boolean feedback to specify to show status info (true) or not (false)
 * @param boolean true if everything was ok, false if some error was found
 */
function execute_sql_arr($sqlarr, $continue=true, $feedback=true) {

    if (!is_array($sqlarr)) {
        return false;
    }

    $status = true;
    foreach($sqlarr as $sql) {
        if (!execute_sql($sql, $feedback)) {
            $status = false;
            if (!$continue) {
                break;
            }
        }
    }
    return $status;
}

/**
 * This function, called from setup.php includes all the configuration
 * needed to properly work agains any DB. It setups connection encoding
 * and some other variables.
 */
function configure_dbconnection() {

    global $CFG, $db;

    switch ($CFG->dbtype) {
        case 'mysql':
        /// Set names if needed
            if ($CFG->unicodedb) {
                $db->Execute("SET NAMES 'utf8'");
            }
            break;
        case 'postgres7':
        /// Set names if needed
            if ($CFG->unicodedb) {
                $db->Execute("SET NAMES 'utf8'");
            }
            break;
        case 'mssql':
        /// No need to set charset. It must be specified in the driver conf
        /// Allow quoted identifiers
            $db->Execute('SET QUOTED_IDENTIFIER ON');
        /// Enable sybase quotes, so addslashes and stripslashes will use "'"
            ini_set('magic_quotes_sybase', '1');
        /// NOTE: Not 100% useful because GPC has been addslashed with the setting off
        ///       so IT'S MANDATORY TO CHANGE THIS UNDER php.ini or .htaccess for this DB
            break;
        case 'oci8po':
        /// No need to set charset. It must be specified by the NLS_LANG env. variable
        /// Enable sybase quotes, so addslashes and stripslashes will use "'"
            ini_set('magic_quotes_sybase', '1');
        /// NOTE: Not 100% useful because GPC has been addslashed with the setting off
        ///       so IT'S MANDATORY TO ENABLE THIS UNDER php.ini or .htaccess for this DB
            break;
    }
}


?>
