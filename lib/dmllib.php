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

$empty_rs_cache = array();   // Keeps copies of the recordsets used in one invocation
$metadata_cache = array();   // Kereeps copies of the MetaColumns() for each table used in one invocations

$rcache = new StdClass;      // Cache simple get_record results
$rcache->data   = array();
$rcache->hits   = 0;
$rcache->misses = 0;

/// FUNCTIONS FOR DATABASE HANDLING  ////////////////////////////////

/**
 * Execute a given sql command string
 *
 * Completely general function - it just runs some SQL and reports success.
 *
 * @uses $db
 * @param string $command The sql string you wish to be executed.
 * @param bool $feedback Set this argument to true if the results generated should be printed. Default is true.
 * @return bool success
 */
function execute_sql($command, $feedback=true) {
/// Completely general function - it just runs some SQL and reports success.

    global $db, $CFG;

    $olddebug = $db->debug;

    if (!$feedback) {
        $db->debug = false;
    }

    if ($CFG->version >= 2006101007) { //Look for trailing ; from Moodle 1.7.0
        $command = trim($command);
    /// If the trailing ; is there, fix and warn!
        if (substr($command, strlen($command)-1, 1) == ';') {
        /// One noticeable exception, Oracle PL/SQL blocks require ending in ";"
            if ($CFG->dbfamily == 'oracle' && substr($command, -4) == 'END;') {
                /// Nothing to fix/warn. The command is one PL/SQL block, so it's ok.
            } else {
                $command = trim($command, ';');
                debugging('Warning. Avoid to end your SQL commands with a trailing ";".', DEBUG_DEVELOPER);
            }
        }
    }

    $empty_rs_cache = array();  // Clear out the cache, just in case changes were made to table structures

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    $rs = $db->Execute($command);

    $db->debug = $olddebug;

    if ($rs) {
        if ($feedback) {
            notify(get_string('success'), 'notifysuccess');
        }
        return true;
    } else {
        if ($feedback) {
            notify('<strong>' . get_string('error') . '</strong>');
        }
        // these two may go to difference places
        debugging($db->ErrorMsg() .'<br /><br />'. s($command));
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $command");
        }
        return false;
    }
}

/**
* on DBs that support it, switch to transaction mode and begin a transaction
* you'll need to ensure you call commit_sql() or your changes *will* be lost.
*
* Now using ADOdb standard transactions. Some day, we should switch to
* Smart Transactions (http://phplens.com/adodb/tutorial.smart.transactions.html)
* as they autodetect errors and are nestable and easier to write
*
* this is _very_ useful for massive updates
*/
function begin_sql() {

    global $db;

    $db->BeginTrans();

    return true;
}

/**
* on DBs that support it, commit the transaction
*
* Now using ADOdb standard transactions. Some day, we should switch to
* Smart Transactions (http://phplens.com/adodb/tutorial.smart.transactions.html)
* as they autodetect errors and are nestable and easier to write
*/
function commit_sql() {

    global $db;

    $db->CommitTrans();

    return true;
}

/**
* on DBs that support it, rollback the transaction
*
* Now using ADOdb standard transactions. Some day, we should switch to
* Smart Transactions (http://phplens.com/adodb/tutorial.smart.transactions.html)
* as they autodetect errors and are nestable and easier to write
*/
function rollback_sql() {

    global $db;

    $db->RollbackTrans();

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
 * Run an arbitrary sequence of semicolon-delimited SQL commands
 *
 * Assumes that the input text (file or string) consists of
 * a number of SQL statements ENDING WITH SEMICOLONS.  The
 * semicolons MUST be the last character in a line.
 * Lines that are blank or that start with "#" or "--" (postgres) are ignored.
 * Only tested with mysql dump files (mysqldump -p -d moodle)
 *
 * @uses $CFG
 *
 * @deprecated Moodle 1.7 use the new XMLDB stuff in lib/ddllib.php
 *
 * @param string $sqlfile The path where a file with sql commands can be found on the server.
 * @param string $sqlstring If no path is supplied then a string with semicolon delimited sql
 * commands can be supplied in this argument.
 * @return bool Returns true if databse was modified successfully.
 */
function modify_database($sqlfile='', $sqlstring='') {

    global $CFG;

    if ($CFG->version > 2006101007) {
        debugging('Function modify_database() is deprecated. Replace it with the new XMLDB stuff.', DEBUG_DEVELOPER);
    }

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

    if (!$rs = get_recordset_sql($sql, $limitfrom, $limitnum)) {
        return false;
    }

    if (rs_EOF($rs)) {
        $result = false;
    } else {
        $result = true;
    }

    rs_close($rs);
    return $result;
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

    if (is_object($rs) and is_array($rs->fields)) {
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

    // Check to see whether this record is eligible for caching (fields=*, only condition is id)
    $docache = false;
    if (!empty($CFG->rcache) && $CFG->rcache === true && $field1=='id' && !$field2 && !$field3 && $fields=='*') {
        $docache = true;
        // If it's in the cache, return it
        $cached = rcache_getforfill($table, $value1);
        if (!empty($cached)) {
            return $cached;
        }
    }

    $select = where_clause($field1, $value1, $field2, $value2, $field3, $value3);

    $record = get_record_sql('SELECT '.$fields.' FROM '. $CFG->prefix . $table .' '. $select);

    // If we're caching records, store this one
    // (supposing we got something - we don't cache failures)
    if ($docache) {
        if ($record !== false) {
            rcache_set($table, $value1, $record);
        } else {
            rcache_releaseforfill($table, $value1);
        }
    }
    return $record;
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
 * @param bool $expectmultiple If the SQL cannot be written to conveniently return just one record,
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
    } else if (debugging('', DEBUG_DEVELOPER)) {
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
    /// all those NOT NULL DEFAULT '' fields until we definitively delete them
        if ($CFG->dbfamily == 'oracle') {
            array_walk($rs->fields, 'onespace2empty');
        }
    /// End of DIRTY HACK
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
 * Since this method is a little less readable, use of it should be restricted to 
 * code where it's possible there might be large datasets being returned.  For known 
 * small datasets use get_records - it leads to simpler code.
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

    if ($sort) {
        $sort = ' ORDER BY '. $sort;
    }

    return get_recordset_sql('SELECT '. $fields .' FROM '. $CFG->prefix . $table . $select . $sort, $limitfrom, $limitnum);
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
 * Since this method is a little less readable, use of it should be restricted to 
 * code where it's possible there might be large datasets being returned.  For known 
 * small datasets use get_records_sql - it leads to simpler code.
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

/// Temporary hack as part of phasing out all access to obsolete user tables  XXX
    if (!empty($CFG->rolesactive)) {
        if (strpos($sql, ' '.$CFG->prefix.'user_students ') ||
            strpos($sql, ' '.$CFG->prefix.'user_teachers ') ||
            strpos($sql, ' '.$CFG->prefix.'user_coursecreators ') ||
            strpos($sql, ' '.$CFG->prefix.'user_admins ')) {
            if (debugging()) { var_dump(debug_backtrace()); }
            error('This SQL relies on obsolete tables!  Your code must be fixed by a developer.');
        }
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
        debugging($db->ErrorMsg() .'<br /><br />'. s($sql));
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $sql with limits ($limitfrom, $limitnum)");
        }
        return false;
    }

    return $rs;
}

/**
 * Utility function used by the following 4 methods. Note that for this to work, the first column
 * in the recordset must contain unique values, as it is used as the key to the associative array.
 *
 * @param object an ADODB RecordSet object.
 * @return mixed mixed an array of objects, or false if an error occured or the RecordSet was empty.
 */
function recordset_to_array($rs) {
    global $CFG;

    $debugging = debugging('', DEBUG_DEVELOPER);

    if ($rs && !rs_EOF($rs)) {
        $objects = array();
    /// First of all, we are going to get the name of the first column
    /// to introduce it back after transforming the recordset to assoc array
    /// See http://docs.moodle.org/en/XMLDB_Problems, fetch mode problem.
        $firstcolumn = $rs->FetchField(0);
    /// Get the whole associative array
        if ($records = $rs->GetAssoc(true)) {
            foreach ($records as $key => $record) {
            /// Really DIRTY HACK for Oracle, but it's the only way to make it work
            /// until we got all those NOT NULL DEFAULT '' out from Moodle
                if ($CFG->dbfamily == 'oracle') {
                    array_walk($record, 'onespace2empty');
                }
            /// End of DIRTY HACK
                $record[$firstcolumn->name] = $key;/// Re-add the assoc field
                if ($debugging && array_key_exists($key, $objects)) {
                    debugging("Did you remember to make the first column something unique in your call to get_records? Duplicate value '$key' found in column '".$firstcolumn->name."'.", DEBUG_DEVELOPER);
                }
                $objects[$key] = (object) $record; /// To object
            }
            return $objects;
    /// Fallback in case we only have 1 field in the recordset. MDL-5877
        } else if ($rs->_numOfFields == 1 && $records = $rs->GetRows()) {
            foreach ($records as $key => $record) {
            /// Really DIRTY HACK for Oracle, but it's the only way to make it work
            /// until we got all those NOT NULL DEFAULT '' out from Moodle
                if ($CFG->dbfamily == 'oracle') {
                    array_walk($record, 'onespace2empty');
                }
            /// End of DIRTY HACK
                if ($debugging && array_key_exists($record[$firstcolumn->name], $objects)) {
                    debugging("Did you remember to make the first column something unique in your call to get_records? Duplicate value '".$record[$firstcolumn->name]."' found in column '".$firstcolumn->name."'.", DEBUG_DEVELOPER);
                }
                $objects[$record[$firstcolumn->name]] = (object) $record; /// The key is the first column value (like Assoc)
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
 * This function is used to get the current record from the recordset. It
 * doesn't advance the recordset position. You'll need to do that by
 * using the rs_next_record($recordset) function.
 * @param ADORecordSet the recordset to fetch current record from
 * @return ADOFetchObj the object containing the fetched information
 */
function rs_fetch_record(&$rs) {
    global $CFG;

    if (!$rs) {
        debugging('Incorrect $rs used!', DEBUG_DEVELOPER);
        return false;
    }

    $rec = $rs->FetchObj(); //Retrieve record as object without advance the pointer

    if ($rs->EOF) { //FetchObj requires manual checking of EOF to detect if it's the last record
        $rec = false;
    } else {
    /// DIRTY HACK to retrieve all the ' ' (1 space) fields converted back
    /// to '' (empty string) for Oracle. It's the only way to work with
    /// all those NOT NULL DEFAULT '' fields until we definetively delete them
        if ($CFG->dbfamily == 'oracle') {
            $recarr = (array)$rec; /// Cast to array
            array_walk($recarr, 'onespace2empty');
            $rec = (object)$recarr;/// Cast back to object
        }
    /// End DIRTY HACK
    }

    return $rec;
}

/**
 * This function is used to advance the pointer of the recordset
 * to its next position/record.
 * @param ADORecordSet the recordset to be moved to the next record
 * @return boolean true if the movement was successful and false if not (end of recordset)
 */
function rs_next_record(&$rs) {
    if (!$rs) {
        debugging('Incorrect $rs used!', DEBUG_DEVELOPER);
        return false;
    }

    return $rs->MoveNext(); //Move the pointer to the next record
}

/**
 * This function is used to get the current record from the recordset. It
 * does advance the recordset position.
 * This is the prefered way to iterate over recordsets with code blocks like this:
 *
 * $rs = get_recordset('SELECT .....');
 * while ($rec = rs_fetch_next_record($rs)) {
 *     /// Perform actions with the $rec record here
 * }
 * rs_close($rs); /// Close the recordset if not used anymore. Saves memory (optional but recommended).
 *
 * @param ADORecordSet the recordset to fetch current record from
 * @return mixed ADOFetchObj the object containing the fetched information or boolean false if no record (end of recordset)
 */
function rs_fetch_next_record(&$rs) {

    global $CFG;

    if (!$rs) {
        debugging('Incorrect $rs used!', DEBUG_DEVELOPER);
        return false;
    }

    $rec = false;
    $recarr = $rs->FetchRow(); //Retrieve record as object without advance the pointer. It's quicker that FetchNextObj()

    if ($recarr) {
    /// DIRTY HACK to retrieve all the ' ' (1 space) fields converted back
    /// to '' (empty string) for Oracle. It's the only way to work with
    /// all those NOT NULL DEFAULT '' fields until we definetively delete them
        if ($CFG->dbfamily == 'oracle') {
            array_walk($recarr, 'onespace2empty');
        }
    /// End DIRTY HACK
    /// Cast array to object
        $rec = (object)$recarr;
    }

    return $rec;
}

/**
 * Returns true if no more records found
 * @param ADORecordSet the recordset
 * @return bool
 */
function rs_EOF($rs) {
    if (!$rs) {
        debugging('Incorrect $rs used!', DEBUG_DEVELOPER);
        return true;
    }
    return $rs->EOF;
}

/**
 * This function closes the recordset, freeing all the memory and associated resources.
 * Note that, once closed, the recordset must not be used anymore along the request.
 * Saves memory (optional but recommended).
 * @param ADORecordSet the recordset to be closed
 * @return void
 */
function rs_close(&$rs) {
    if (!$rs) {
        debugging('Incorrect $rs used!', DEBUG_DEVELOPER);
        return;
    }

    $rs->Close();
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
///End DIRTY HACK


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
 * @param string $fields a comma separated list of fields to return (optional, by default
 *   all fields are returned). The first field will be used as key for the
 *   array so must be a unique field such as 'id'.
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
 * @param string $fields a comma separated list of fields to return
 *   (optional, by default all fields are returned). The first field will be used as key for the
 *   array so must be a unique field such as 'id'.
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
 * @param string $fields A comma separated list of fields to be returned from the chosen table. If specified,
 *   the first field should be a unique one such as 'id' since it will be used as a key in the associative
 *   array.
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
 * @param string $sql the SQL select query to execute. The first column of this SELECT statement
 *   must be a unique value (usually the 'id' field), as it will be used as the key of the
 *   returned array.
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
    global $CFG;
    $menu = array();
    if ($rs && !rs_EOF($rs)) {
        $keys = array_keys($rs->fields);
        $key0=$keys[0];
        $key1=$keys[1];
        while (!$rs->EOF) {
            $menu[$rs->fields[$key0]] = $rs->fields[$key1];
            $rs->MoveNext();
        }
        /// Really DIRTY HACK for Oracle, but it's the only way to make it work
        /// until we got all those NOT NULL DEFAULT '' out from Moodle
        if ($CFG->dbfamily == 'oracle') {
            array_walk($menu, 'onespace2empty');
        }
        /// End of DIRTY HACK
        return $menu;
    } else {
        return false;
    }
}

/**
 * Utility function 
 * Similar to recordset_to_menu 
 *
 * field1, field2 is needed because the order from get_records_sql is not reliable
 * @param records - records from get_records_sql() or get_records()
 * @param field1 - field to be used as menu index
 * @param field2 - feild to be used as coresponding menu value
 * @return mixed an associative array, or false if an error occured or the RecordSet was empty.
 */
function records_to_menu($records, $field1, $field2) {

    $menu = array();
    foreach ($records as $record) {
        $menu[$record->$field1] = $record->$field2;
    }

    if (!empty($menu)) {
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
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an associative array, or false if no records were found or an error occured.
 */
function get_records_menu($table, $field='', $value='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    $rs = get_recordset($table, $field, $value, $sort, $fields, $limitfrom, $limitnum);
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
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an associative array, or false if no records were found or an error occured.
 */
function get_records_select_menu($table, $select='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    $rs = get_recordset_select($table, $select, $sort, $fields, $limitfrom, $limitnum);
    return recordset_to_menu($rs);
}

/**
 * Get the first two columns from a number of records as an associative array.
 *
 * Arguments as for @see function get_recordset_sql.
 * Return value as for @see function get_records_menu.
 *
 * @param string $sql The SQL string you wish to be executed.
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return mixed an associative array, or false if no records were found or an error occured.
 */
function get_records_sql_menu($sql, $limitfrom='', $limitnum='') {
    $rs = get_recordset_sql($sql, $limitfrom, $limitnum);
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
    global $CFG;

/// Strip potential LIMIT uses arriving here, debugging them (MDL-7173)
    $newsql = preg_replace('/ LIMIT [0-9, ]+$/is', '', $sql);
    if ($newsql != $sql) {
        debugging('Incorrect use of LIMIT clause (not cross-db) in call to get_field_sql(): ' . s($sql), DEBUG_DEVELOPER);
        $sql = $newsql;
    }

    $rs = get_recordset_sql($sql, 0, 1);

    if ($rs && $rs->RecordCount() == 1) {
        /// DIRTY HACK to retrieve all the ' ' (1 space) fields converted back
        /// to '' (empty string) for Oracle. It's the only way to work with
        /// all those NOT NULL DEFAULT '' fields until we definetively delete them
        if ($CFG->dbfamily == 'oracle') {
            $value = reset($rs->fields);
            onespace2empty($value);
            return $value;
        }
        /// End of DIRTY HACK
        return reset($rs->fields);
    } else {
        return false;
    }
}

/**
 * Get a single value from a table row where a particular select clause is true.
 *
 * @uses $CFG
 * @param string $table the table to query.
 * @param string $return the field to return the value of.
 * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
 * @return mixed|false Returns the value return from the SQL statment or false if an error occured.
 */
function get_fieldset_select($table, $return, $select) {
    global $CFG;
    if ($select) {
        $select = ' WHERE '. $select;
    }
    return get_fieldset_sql('SELECT ' . $return . ' FROM ' . $CFG->prefix . $table . $select);
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
        debugging($db->ErrorMsg() .'<br /><br />'. s($sql));
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $sql");
        }
        return false;
    }

    if ( !rs_EOF($rs) ) {
        $keys = array_keys($rs->fields);
        $key0 = $keys[0];
        $results = array();
        while (!$rs->EOF) {
            array_push($results, $rs->fields[$key0]);
            $rs->MoveNext();
        }
        /// DIRTY HACK to retrieve all the ' ' (1 space) fields converted back
        /// to '' (empty string) for Oracle. It's the only way to work with
        /// all those NOT NULL DEFAULT '' fields until we definetively delete them
        if ($CFG->dbfamily == 'oracle') {
            array_walk($results, 'onespace2empty');
        }
        /// End of DIRTY HACK
        rs_close($rs);
        return $results;
    } else {
        rs_close($rs);
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

    global $CFG;

    // Clear record_cache based on the parameters passed
    // (individual record or whole table)
    if ($CFG->rcache === true) {
        if ($field1 == 'id') {
            rcache_unset($table, $value1);
        } else if ($field2 == 'id') {
            rcache_unset($table, $value2);
        } else if ($field3 == 'id') {
            rcache_unset($table, $value3);
        } else {
            rcache_unset_table($table);
        }
    }

    $select = where_clause($field1, $value1, $field2, $value2, $field3, $value3);

    return set_field_select($table, $newfield, $newvalue, $select, true);
}

/**
 * Set a single field in every table row where the select statement evaluates to true.
 *
 * @uses $CFG
 * @uses $db
 * @param string $table The database table to be checked against.
 * @param string $newfield the field to set.
 * @param string $newvalue the value to set the field to.
 * @param string $select a fragment of SQL to be used in a where clause in the SQL call.
 * @param boolean $localcall Leave this set to false. (Should only be set to true by set_field.)
 * @return mixed An ADODB RecordSet object with the results from the SQL call or false.
 */
function set_field_select($table, $newfield, $newvalue, $select, $localcall = false) {

    global $db, $CFG;

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    if (!$localcall) {
        if ($select) {
            $select = 'WHERE ' . $select;
        }

        // Clear record_cache based on the parameters passed
        // (individual record or whole table)
        if ($CFG->rcache === true) {
            rcache_unset_table($table);
        }
    }

    $dataobject = new StdClass;
    $dataobject->{$newfield} = $newvalue;
    // Oracle DIRTY HACK -
    if ($CFG->dbfamily == 'oracle') {
        oracle_dirty_hack($table, $dataobject); // Convert object to the correct "empty" values for Oracle DB
        $newvalue = $dataobject->{$newfield};
    }
    // End DIRTY HACK

/// Under Oracle, MSSQL and PostgreSQL we have our own set field process
/// If the field being updated is clob/blob, we use our alternate update here
/// They will be updated later
    if (($CFG->dbfamily == 'oracle' || $CFG->dbfamily == 'mssql' || $CFG->dbfamily == 'postgres') && !empty($select)) {
    /// Detect lobs
        $foundclobs = array();
        $foundblobs = array();
        db_detect_lobs($table, $dataobject, $foundclobs, $foundblobs);
    }

/// Under Oracle, MSSQL and PostgreSQL, finally, update all the Clobs and Blobs present in the record
/// if we know we have some of them in the query
    if (($CFG->dbfamily == 'oracle' || $CFG->dbfamily == 'mssql' || $CFG->dbfamily == 'postgres') && !empty($select) &&
      (!empty($foundclobs) || !empty($foundblobs))) {
        if (!db_update_lobs($table, $select, $foundclobs, $foundblobs)) {
            return false; //Some error happened while updating LOBs
        } else {
            return true; //Everrything was ok
        }
    }

/// NULL inserts - introduced in 1.9
    if (is_null($newvalue)) {
        $update = "$newfield = NULL";
    } else {
        $update = "$newfield = '$newvalue'";
    }

/// Arriving here, standard update
    $sql = 'UPDATE '. $CFG->prefix . $table .' SET '.$update.' '.$select;
    $rs = $db->Execute($sql);
    if (!$rs) {
        debugging($db->ErrorMsg() .'<br /><br />'. s($sql));
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $sql");
        }
        return false;
    }
    return $rs;
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

    // Clear record_cache based on the parameters passed
    // (individual record or whole table)
    if ($CFG->rcache === true) {
        if ($field1 == 'id') {
            rcache_unset($table, $value1);
        } else if ($field2 == 'id') {
            rcache_unset($table, $value2);
        } else if ($field3 == 'id') {
            rcache_unset($table, $value3);
        } else {
            rcache_unset_table($table);
        }
    }

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    $select = where_clause($field1, $value1, $field2, $value2, $field3, $value3);

    $sql = 'DELETE FROM '. $CFG->prefix . $table .' '. $select;
    $rs = $db->Execute($sql);
    if (!$rs) {
        debugging($db->ErrorMsg() .'<br /><br />'. s($sql));
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $sql");
        }
        return false;
    }
    return $rs;
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

    // Clear record_cache (whole table)
    if ($CFG->rcache === true) {
        rcache_unset_table($table);
    }

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

    if ($select) {
        $select = 'WHERE '.$select;
    }

    $sql = 'DELETE FROM '. $CFG->prefix . $table .' '. $select;
    $rs = $db->Execute($sql);
    if (!$rs) {
        debugging($db->ErrorMsg() .'<br /><br />'. s($sql));
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $sql");
        }
        return false;
    }
    return $rs;
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
 * @param object $dataobject A data object with values for one or more fields in the record
 * @param bool $returnid Should the id of the newly created record entry be returned? If this option is not requested then true/false is returned.
 * @param string $primarykey (obsolete) This is now forced to be 'id'. 
 */
function insert_record($table, $dataobject, $returnid=true, $primarykey='id') {

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
            error('This SQL relies on obsolete tables ('.$table.')!  Your code must be fixed by a developer.');
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
            debugging('Sequence name for table ' . $xmldb_table->getName() . ' not found', DEBUG_DEVELOPER);
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
 * @param object $dataobject An object with contents equal to fieldname=>fieldvalue. Must have an entry for 'id' to map to the table specified.
 * @return bool
 */
function update_record($table, $dataobject) {

    global $db, $CFG;

    // integer value in id propery required
    if (empty($dataobject->id)) {
        return false;
    }
    $dataobject->id = (int)$dataobject->id;

/// Check we are handling a proper $dataobject
    if (is_array($dataobject)) {
        debugging('Warning. Wrong call to update_record(). $dataobject must be an object. array found instead', DEBUG_DEVELOPER);
        $dataobject = (object)$dataobject;
    }

    // Remove this record from record cache since it will change
    if (!empty($CFG->rcache)) { // no === here! breaks upgrade
        rcache_unset($table, $dataobject->id);
    }

/// Temporary hack as part of phasing out all access to obsolete user tables  XXX
    if (!empty($CFG->rolesactive)) {
        if (in_array($table, array('user_students', 'user_teachers', 'user_coursecreators', 'user_admins'))) {
            if (debugging()) { var_dump(debug_backtrace()); }
            error('This SQL relies on obsolete tables ('.$table.')!  Your code must be fixed by a developer.');
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

    debugging('Function sql_paging_limit() is deprecated. Replace it with the correct use of limitfrom, limitnum parameters', DEBUG_DEVELOPER);

    switch ($CFG->dbfamily) {
        case 'postgres':
             return 'LIMIT '. $recordsperpage .' OFFSET '. $page;
        default:
             return 'LIMIT '. $page .','. $recordsperpage;
    }
}

/**
 * Returns the proper SQL to do LIKE in a case-insensitive way
 *
 * Note the LIKE are case sensitive for Oracle. Oracle 10g is required to use
 * the caseinsensitive search using regexp_like() or NLS_COMP=LINGUISTIC :-(
 * See http://docs.moodle.org/en/XMLDB_Problems#Case-insensitive_searches
 *
 * @uses $CFG
 * @return string
 */
function sql_ilike() {
    global $CFG;

    switch ($CFG->dbfamily) {
        case 'postgres':
             return 'ILIKE';
        default:
             return 'LIKE';
    }
}


/**
 * Returns the proper SQL to do MAX
 *
 * @uses $CFG
 * @param string $field
 * @return string
 */
function sql_max($field) {
    global $CFG;

    switch ($CFG->dbfamily) {
        default:
             return "MAX($field)";
    }
}

/**
 * Returns the proper SQL (for the dbms in use) to concatenate $firstname and $lastname
 *
 * @uses $CFG
 * @param string $firstname User's first name
 * @param string $lastname User's last name
 * @return string
 */
function sql_fullname($firstname='firstname', $lastname='lastname') {
    return sql_concat($firstname, "' '", $lastname);
}

/**
 * Returns the proper SQL to do CONCAT between the elements passed
 * Can take many parameters - just a passthrough to $db->Concat()
 *
 * @uses $db
 * @param string $element
 * @return string
 */
function sql_concat() {
    global $db, $CFG;

    $args = func_get_args();
/// PostgreSQL requires at least one char element in the concat, let's add it
/// here (at the beginning of the array) until ADOdb fixes it
    if ($CFG->dbfamily == 'postgres' && is_array($args)) {
        array_unshift($args , "''");
    }
    return call_user_func_array(array($db, 'Concat'), $args);
}

/**
 * Returns the proper SQL to do CONCAT between the elements passed
 * with a given separator
 *
 * @uses $db
 * @param string $separator
 * @param array  $elements
 * @return string
 */
function sql_concat_join($separator="' '", $elements=array()) {
    global $db;

    // copy to ensure pass by value
    $elem = $elements;

    // Intersperse $elements in the array.
    // Add items to the array on the fly, walking it
    // _backwards_ splicing the elements in. The loop definition
    // should skip first and last positions.
    for ($n=count($elem)-1; $n > 0 ; $n--) {
        array_splice($elem, $n, 0, $separator);
    }
    return call_user_func_array(array($db, 'Concat'), $elem);
}

/**
 * Returns the proper SQL to know if one field is empty.
 *
 * Note that the function behavior strongly relies on the
 * parameters passed describing the field so, please,  be accurate
 * when speciffying them.
 *
 * Also, note that this function is not suitable to look for
 * fields having NULL contents at all. It's all for empty values!
 *
 * This function should be applied in all the places where conditins of
 * the type:
 *
 *     ... AND fieldname = '';
 *
 * are being used. Final result should be:
 *
 *     ... AND ' . sql_isempty('tablename', 'fieldname', true/false, true/false);
 *
 * (see parameters description below)
 *
 * @param string $tablename name of the table (without prefix). Not used for now but can be
 *                          necessary in the future if we want to use some introspection using
 *                          meta information against the DB. /// TODO ///
 * @param string $fieldname name of the field we are going to check
 * @param boolean $nullablefield to specify if the field us nullable (true) or no (false) in the DB
 * @param boolean $textfield to specify if it is a text (also called clob) field (true) or a varchar one (false)
 * @return string the sql code to be added to check for empty values
 */
function sql_isempty($tablename, $fieldname, $nullablefield, $textfield) {

    global $CFG;

    $sql = $fieldname . " = ''";

    switch ($CFG->dbfamily) {
        case 'mssql':
            if ($textfield) {
                $sql = sql_compare_text($fieldname) . " = ''";
            }
            break;
        case 'oracle':
            if ($nullablefield) {
                $sql = $fieldname . " IS NULL";                     /// empties in nullable fields are stored as
            } else {                                                /// NULLs
                if ($textfield) {
                    $sql = sql_compare_text($fieldname) . " = ' '"; /// oracle_dirty_hack inserts 1-whitespace
                } else {                                            /// in NOT NULL varchar and text columns so
                    $sql =  $fieldname . " = ' '";                  /// we need to look for that in any situation
                }
            }
            break;
    }

    // Add spaces to avoid wrong SQLs due to concatenation.
    // Add brackets to avoid operator precedence problems.
    return ' (' . $sql . ') ';
}

/**
 * Returns the proper SQL to know if one field is not empty.
 *
 * Note that the function behavior strongly relies on the
 * parameters passed describing the field so, please,  be accurate
 * when speciffying them.
 *
 * This function should be applied in all the places where conditions of
 * the type:
 *
 *     ... AND fieldname != '';
 *
 * are being used. Final result should be:
 *
 *     ... AND ' . sql_isnotempty('tablename', 'fieldname', true/false, true/false);
 *
 * (see parameters description below)
 *
 * @param string $tablename name of the table (without prefix). Not used for now but can be
 *                          necessary in the future if we want to use some introspection using
 *                          meta information against the DB. /// TODO ///
 * @param string $fieldname name of the field we are going to check
 * @param boolean $nullablefield to specify if the field us nullable (true) or no (false) in the DB
 * @param boolean $textfield to specify if it is a text (also called clob) field (true) or a varchar one (false)
 * @return string the sql code to be added to check for non empty values
 */
function sql_isnotempty($tablename, $fieldname, $nullablefield, $textfield) {

    return ' ( NOT ' . sql_isempty($tablename, $fieldname, $nullablefield, $textfield) . ') ';
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
 * @deprecated Moodle 1.7 because coding guidelines now enforce to use AS in column aliases
 */
function sql_as() {
    global $CFG, $db;

    switch ($CFG->dbfamily) {
        case 'postgres':
            return 'AS';
        default:
            return '';
    }
}

/**
 * Returns the empty string char used by every supported DB. To be used when
 * we are searching for that values in our queries. Only Oracle uses this
 * for now (will be out, once we migrate to proper NULLs if that days arrives)
 */
function sql_empty() {
    global $CFG;

    switch ($CFG->dbfamily) {
        case 'oracle':
            return ' '; //Only Oracle uses 1 white-space
        default:
            return '';
    }
}

/**
 * Returns the proper substr() function for each DB
 * Relies on ADOdb $db->substr property
 */
function sql_substr() {

    global $db;

    return $db->substr;
}

/**
 * Returns the SQL text to be used to compare one TEXT (clob) column with
 * one varchar column, because some RDBMS doesn't support such direct
 * comparisons.
 * @param string fieldname the name of the TEXT field we need to order by
 * @param string number of chars to use for the ordering (defaults to 32)
 * @return string the piece of SQL code to be used in your statement.
 */
function sql_compare_text($fieldname, $numchars=32) {
    return sql_order_by_text($fieldname, $numchars);
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

    switch ($CFG->dbfamily) {
        case 'mssql':
            return 'CONVERT(varchar, ' . $fieldname . ', ' . $numchars . ')';
            break;
        case 'oracle':
            return 'dbms_lob.substr(' . $fieldname . ', ' . $numchars . ',1)';
            break;
        default:
            return $fieldname;
    }
}

/**
 * Returns the SQL text to be used to calculate the length in characters of one expression.
 * @param string fieldname or expression to calculate its length in characters.
 * @return string the piece of SQL code to be used in the statement.
 */
function sql_length($fieldname) {

    global $CFG;

    switch ($CFG->dbfamily) {
        case 'mysql':
            return 'CHAR_LENGTH(' . $fieldname . ')';
            break;
        case 'mssql':
            return 'LEN(' . $fieldname . ')';
            break;
        default:
            return 'LENGTH(' . $fieldname . ')';
    }
}

    /**
     * Returns the SQL for returning searching one string for the location of another.
     * @param string $needle the SQL expression that will be searched for.
     * @param string $haystack the SQL expression that will be searched in.
     * @return string the required SQL
     */
function sql_position($needle, $haystack) {
    global $CFG;

    switch ($CFG->dbfamily) {
        case 'mssql':
            return "CHARINDEX(($needle), ($haystack))";
            break;
        case 'oracle':
            return "INSTR(($haystack), ($needle))";
            break;
        default:
            return "POSITION(($needle) IN ($haystack))";
    }
}

/**
 * Returns the SQL to be used in order to CAST one CHAR column to INTEGER.
 *
 * Be aware that the CHAR column you're trying to cast contains really
 * int values or the RDBMS will throw an error!
 *
 * @param string fieldname the name of the field to be casted
 * @param boolean text to specify if the original column is one TEXT (CLOB) column (true). Defaults to false.
 * @return string the piece of SQL code to be used in your statement.
 */
function sql_cast_char2int($fieldname, $text=false) {

    global $CFG;

    $sql = '';

    switch ($CFG->dbfamily) {
        case 'mysql':
            $sql = ' CAST(' . $fieldname . ' AS SIGNED) ';
            break;
        case 'postgres':
            $sql = ' CAST(' . $fieldname . ' AS INT) ';
            break;
        case 'mssql':
            if (!$text) {
                $sql = ' CAST(' . $fieldname . ' AS INT) ';
            } else {
                $sql = ' CAST(' . sql_compare_text($fieldname) . ' AS INT) ';
            }
            break;
        case 'oracle':
            if (!$text) {
                $sql = ' CAST(' . $fieldname . ' AS INT) ';
            } else {
                $sql = ' CAST(' . sql_compare_text($fieldname) . ' AS INT) ';
            }
            break;
        default:
            $sql = ' ' . $fieldname . ' ';
    }

    return $sql;
}

/**
 * Returns the SQL text to be used in order to perform one bitwise AND operation
 * between 2 integers.
 * @param integer int1 first integer in the operation
 * @param integer int2 second integer in the operation
 * @return string the piece of SQL code to be used in your statement.
 */
function sql_bitand($int1, $int2) {

    global $CFG;

    switch ($CFG->dbfamily) {
        case 'oracle':
            return 'bitand((' . $int1 . '), (' . $int2 . '))';
            break;
        default:
            return '((' . $int1 . ') & (' . $int2 . '))';
    }
}

/**
 * Returns the SQL text to be used in order to perform one bitwise OR operation
 * between 2 integers.
 * @param integer int1 first integer in the operation
 * @param integer int2 second integer in the operation
 * @return string the piece of SQL code to be used in your statement.
 */
function sql_bitor($int1, $int2) {

    global $CFG;

    switch ($CFG->dbfamily) {
        case 'oracle':
            return '((' . $int1 . ') + (' . $int2 . ') - ' . sql_bitand($int1, $int2) . ')';
            break;
        default:
            return '((' . $int1 . ') | (' . $int2 . '))';
    }
}

/**
 * Returns the SQL text to be used in order to perform one bitwise XOR operation
 * between 2 integers.
 * @param integer int1 first integer in the operation
 * @param integer int2 second integer in the operation
 * @return string the piece of SQL code to be used in your statement.
 */
function sql_bitxor($int1, $int2) {

    global $CFG;

    switch ($CFG->dbfamily) {
        case 'oracle':
            return '(' . sql_bitor($int1, $int2) . ' - ' . sql_bitand($int1, $int2) . ')';
            break;
        case 'postgres':
            return '((' . $int1 . ') # (' . $int2 . '))';
            break;
        default:
            return '((' . $int1 . ') ^ (' . $int2 . '))';
    }
}

/**
 * Returns the SQL text to be used in order to perform one bitwise NOT operation
 * with 1 integer.
 * @param integer int1 integer in the operation
 * @return string the piece of SQL code to be used in your statement.
 */
function sql_bitnot($int1) {

    global $CFG;

    switch ($CFG->dbfamily) {
        case 'oracle':
            return '((0 - (' . $int1 . ')) - 1)';
            break;
        default:
            return '(~(' . $int1 . '))';
    }
}

/**
 * Returns the FROM clause required by some DBs in all SELECT statements
 * To be used in queries not having FROM clause to provide cross_db
 */
function sql_null_from_clause() {
    global $CFG;

    switch ($CFG->dbfamily) {
        case 'oracle':
            return ' FROM dual';
            break;
        default:
            return '';
    }
}

/**
 * Returns the correct CEIL expression applied to fieldname
 * @param string fieldname the field (or expression) we are going to ceil
 * @return string the piece of SQL code to be used in your ceiling statement
 */
function sql_ceil($fieldname) {
    global $CFG;

    switch ($CFG->dbfamily) {
        case 'mssql':
            return ' CEILING(' . $fieldname . ')';
            break;
        default:
            return ' CEIL(' . $fieldname . ')';
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
        $select = is_null($value1) ? "WHERE $field1 IS NULL" : "WHERE $field1 = '$value1'";
        if ($field2) {
            $select .= is_null($value2) ? " AND $field2 IS NULL" : " AND $field2 = '$value2'";
            if ($field3) {
                $select .= is_null($value3) ? " AND $field3 IS NULL" : " AND $field3 = '$value3'";
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

    $sql = 'SELECT '.$column.' FROM '.$CFG->prefix.$table.' WHERE 1=2';
    if(!$rs = $db->Execute($sql)) {
        debugging($db->ErrorMsg() .'<br /><br />'. s($sql));
        if (!empty($CFG->dblogerror)) {
            $debug=array_shift(debug_backtrace());
            error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $sql");
        }
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
 * This internal function, called from setup.php, sets all the configuration
 * needed to work properly against any DB. It setups connection encoding
 * and some other variables.
 *
 * This function must contain the init code needed for each dbtype supported.
 */
function configure_dbconnection() {

    global $CFG, $db;

    switch ($CFG->dbtype) {
        case 'mysql':
        case 'mysqli':
            $db->Execute("SET NAMES 'utf8'");
            break;
        case 'postgres7':
            $db->Execute("SET NAMES 'utf8'");
            break;
        case 'mssql':
        case 'mssql_n':
        case 'odbc_mssql':
        /// No need to set charset. It must be specified in the driver conf
        /// Allow quoted identifiers
            $db->Execute('SET QUOTED_IDENTIFIER ON');
        /// Force ANSI nulls so the NULL check was done by IS NULL and NOT IS NULL
        /// instead of equal(=) and distinct(<>) simbols
            $db->Execute('SET ANSI_NULLS ON');
        /// Enable sybase quotes, so addslashes and stripslashes will use "'"
            ini_set('magic_quotes_sybase', '1');
        /// NOTE: Not 100% useful because GPC has been addslashed with the setting off
        ///       so IT'S MANDATORY TO CHANGE THIS UNDER php.ini or .htaccess for this DB
        ///       or to turn off magic_quotes to allow Moodle to do it properly
            break;
        case 'oci8po':
        /// No need to set charset. It must be specified by the NLS_LANG env. variable
        /// Enable sybase quotes, so addslashes and stripslashes will use "'"
            ini_set('magic_quotes_sybase', '1');
        /// NOTE: Not 100% useful because GPC has been addslashed with the setting off
        ///       so IT'S MANDATORY TO ENABLE THIS UNDER php.ini or .htaccess for this DB
        ///       or to turn off magic_quotes to allow Moodle to do it properly
        /// Now set the decimal separator to DOT, Moodle & PHP will always send floats to
        /// DB using DOTS. Manually introduced floats (if using other characters) must be
        /// converted back to DOTs (like gradebook does)
            $db->Execute("ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.,'");
            break;
    }
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
 * This function is private and must not be used outside dmllib at all
 *
 * @param $table string the table where the record is going to be inserted/updated (without prefix)
 * @param $dataobject object the object to be inserted/updated
 * @param $usecache boolean flag to determinate if we must use the per request cache of metadata
 *        true to use it, false to ignore and delete it
 */
function oracle_dirty_hack ($table, &$dataobject, $usecache = true) {

    global $CFG, $db, $metadata_cache;

/// Init and delete metadata cache
    if (!isset($metadata_cache) || !$usecache) {
        $metadata_cache = array();
    }

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

/// If the db isn't Oracle, return without modif
    if ( $CFG->dbfamily != 'oracle') {
        return;
    }

/// Get Meta info to know what to change, using the cached meta if exists
    if (!isset($metadata_cache[$table])) {
        $metadata_cache[$table] = array_change_key_case($db->MetaColumns($CFG->prefix . $table), CASE_LOWER);
    }
    $columns = $metadata_cache[$table];
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

    /// The '0' string doesn't need any transformation, skip
        if ($fieldvalue === '0') {
            continue;
        }

    /// Transformations start
        if (gettype($fieldvalue) == 'boolean') {
            $dataobject->$fieldname = '0'; /// Transform false to '0' that evaluates the same for PHP
        } else if (gettype($fieldvalue) == 'integer') {
            $dataobject->$fieldname = '0'; /// Transform 0 to '0' that evaluates the same for PHP
        } else if (gettype($fieldvalue) == 'NULL') {
            $dataobject->$fieldname = '0'; /// Transform NULL to '0' that evaluates the same for PHP
        } else if ($fieldvalue === '') {
            $dataobject->$fieldname = ' '; /// Transform '' to ' ' that DONT'T EVALUATE THE SAME
                                           /// (we'll transform back again on get_records_XXX functions and others)!!
        }
    }
}
/// End of DIRTY HACK

/**
 * This function will search for all the CLOBs and BLOBs fields passed in the dataobject, replacing
 * their contents by the fixed strings '@#CLOB#@' and '@#BLOB#@' and returning one array for all the
 * found CLOBS and another for all the found BLOBS
 * Used by Oracle drivers to perform the two-step insertion/update of LOBs and
 * by MSSQL to perform the same exclusively for BLOBs (IMAGE fields)
 *
 * This function is private and must not be used outside dmllib at all
 *
 * @param $table string the table where the record is going to be inserted/updated (without prefix)
 * @param $dataobject object the object to be inserted/updated
 * @param $clobs array of clobs detected
 * @param $dataobject array of blobs detected
 * @param $unset boolean to specify if we must unset found LOBs from the original object (true) or
 *        just return them modified to @#CLOB#@ and @#BLOB#@ (false)
 * @param $usecache boolean flag to determinate if we must use the per request cache of metadata
 *        true to use it, false to ignore and delete it
 */
function db_detect_lobs ($table, &$dataobject, &$clobs, &$blobs, $unset = false, $usecache = true) {

    global $CFG, $db, $metadata_cache;

    $dataarray = (array)$dataobject; //Convert to array. It's supposed that PHP 4.3 doesn't iterate over objects

/// Initial configuration, based on DB
    switch ($CFG->dbfamily) {
        case 'oracle':
            $clobdbtype = 'CLOB'; //Name of clobs for this DB
            $blobdbtype = 'BLOB'; //Name of blobs for this DB
            break;
        case 'mssql':
            $clobdbtype = 'NOTPROCESSES'; //Name of clobs for this DB (under mssql flavours we don't process CLOBS)
            $blobdbtype = 'IMAGE'; //Name of blobs for this DB
            break;
        case 'postgres':
            $clobdbtype = 'NOTPROCESSES'; //Name of clobs for this DB (under postgres flavours we don't process CLOBS)
            $blobdbtype = 'BYTEA'; //Name of blobs for this DB
            break;
        default:
            return; //Other DB doesn't need this two step to happen, prevent continue
    }

/// Init and delete metadata cache
    if (!isset($metadata_cache) || !$usecache) {
        $metadata_cache = array();
    }

/// Get Meta info to know what to change, using the cached meta if exists
    if (!isset($metadata_cache[$table])) {
        $metadata_cache[$table] = array_change_key_case($db->MetaColumns($CFG->prefix . $table), CASE_LOWER);
    }
    $columns = $metadata_cache[$table];

    foreach ($dataarray as $fieldname => $fieldvalue) {
    /// If the field doesn't exist in metadata, skip
        if (!isset($columns[strtolower($fieldname)])) {
            continue;
        }
    /// If the field is CLOB, update its value to '@#CLOB#@' and store it in the $clobs array
        if (strtoupper($columns[strtolower($fieldname)]->type) == $clobdbtype) {
        /// Oracle optimization. CLOBs under 4000cc can be directly inserted (no need to apply 2-phases to them)
            if ($CFG->dbfamily == 'oracle' && strlen($dataobject->$fieldname) < 4000) {
                continue;
            }
            $clobs[$fieldname] = $dataobject->$fieldname;
            if ($unset) {
                unset($dataobject->$fieldname);
            } else {
                $dataobject->$fieldname = '@#CLOB#@';
            }
            continue;
        }

    /// If the field is BLOB OR IMAGE OR BYTEA, update its value to '@#BLOB#@' and store it in the $blobs array
        if (strtoupper($columns[strtolower($fieldname)]->type) == $blobdbtype) {
            $blobs[$fieldname] = $dataobject->$fieldname;
            if ($unset) {
                unset($dataobject->$fieldname);
            } else {
                $dataobject->$fieldname = '@#BLOB#@';
            }
            continue;
        }
    }
}

/**
 * This function will iterate over $clobs and $blobs array, executing the needed
 * UpdateClob() and UpdateBlob() ADOdb function calls to store LOBs contents properly
 * Records to be updated are always searched by PK (id always!)
 *
 * Used by Orace CLOBS and BLOBS and MSSQL IMAGES
 *
 * This function is private and must not be used outside dmllib at all
 *
 * @param $table string the table where the record is going to be inserted/updated (without prefix)
 * @param $sqlcondition mixed value defining the records to be LOB-updated. It it's a number, must point
 *        to the PK og the table (id field), else it's processed as one harcoded SQL condition (WHERE clause)
 * @param $clobs array of clobs to be updated
 * @param $blobs array of blobs to be updated
 */
function db_update_lobs ($table, $sqlcondition, &$clobs, &$blobs) {

    global $CFG, $db;

    $status = true;

/// Initial configuration, based on DB
    switch ($CFG->dbfamily) {
        case 'oracle':
            $clobdbtype = 'CLOB'; //Name of clobs for this DB
            $blobdbtype = 'BLOB'; //Name of blobs for this DB
            break;
        case 'mssql':
            $clobdbtype = 'NOTPROCESSES'; //Name of clobs for this DB (under mssql flavours we don't process CLOBS)
            $blobdbtype = 'IMAGE'; //Name of blobs for this DB
            break;
        case 'postgres':
            $clobdbtype = 'NOTPROCESSES'; //Name of clobs for this DB (under postgres flavours we don't process CLOBS)
            $blobdbtype = 'BYTEA'; //Name of blobs for this DB
            break;
        default:
            return; //Other DB doesn't need this two step to happen, prevent continue
    }

/// Calculate the update sql condition
    if (is_numeric($sqlcondition)) { /// If passing a number, it's the PK of the table (id)
        $sqlcondition = 'id=' . $sqlcondition;
    } else { /// Else, it's a formal standard SQL condition, we try to delete the WHERE in case it exists
        $sqlcondition = trim(preg_replace('/^WHERE/is', '', trim($sqlcondition)));
    }

/// Update all the clobs
    if ($clobs) {
        foreach ($clobs as $key => $value) {

            if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; }; /// Count the extra updates in PERF

        /// Oracle CLOBs doesn't like quoted strings (are inserted via prepared statemets)
            if ($CFG->dbfamily == 'oracle') {
                $value = stripslashes_safe($value);
            }

            if (!$db->UpdateClob($CFG->prefix.$table, $key, $value, $sqlcondition)) {
                $status = false;
                $statement = "UpdateClob('$CFG->prefix$table', '$key', '" . substr($value, 0, 100) . "...', '$sqlcondition')";
                debugging($db->ErrorMsg() ."<br /><br />".s($statement));
                if (!empty($CFG->dblogerror)) {
                    $debug=array_shift(debug_backtrace());
                    error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $statement");
                }
            }
        }
    }
/// Update all the blobs
    if ($blobs) {
        foreach ($blobs as $key => $value) {

            if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; }; /// Count the extra updates in PERF

        /// Oracle, MSSQL and PostgreSQL BLOBs doesn't like quoted strings (are inserted via prepared statemets)
            if ($CFG->dbfamily == 'oracle' || $CFG->dbfamily == 'mssql' || $CFG->dbfamily == 'postgres') {
                $value = stripslashes_safe($value);
            }

            if(!$db->UpdateBlob($CFG->prefix.$table, $key, $value, $sqlcondition)) {
                $status = false;
                $statement = "UpdateBlob('$CFG->prefix$table', '$key', '" . substr($value, 0, 100) . "...', '$sqlcondition')";
                debugging($db->ErrorMsg() ."<br /><br />".s($statement));
                if (!empty($CFG->dblogerror)) {
                    $debug=array_shift(debug_backtrace());
                    error_log("SQL ".$db->ErrorMsg()." in {$debug['file']} on line {$debug['line']}. STATEMENT:  $statement");
                }
            }
        }
    }
    return $status;
}

/**
 * Set cached record.
 *
 * If you have called rcache_getforfill() before, it will also
 * release the lock.
 *
 * This function is private and must not be used outside dmllib at all
 *
 * @param $table string
 * @param $id integer
 * @param $rec obj
 * @return bool
 */
function rcache_set($table, $id, $rec) {
    global $CFG, $MCACHE, $rcache;

    if ($CFG->cachetype === 'internal') {
        if (!isset($rcache->data[$table])) {
            $rcache->data[$table] = array();
        }
        if (!isset($rcache->data[$table][$id]) and count($rcache->data[$table]) > $CFG->intcachemax) {
            // release oldes record 
            reset($rcache->data[$table]);
            $key = key($rcache->data[$table]);
            unset($rcache->data[$table][$key]);
        }
        $rcache->data[$table][$id] = clone($rec);
    } else {
        $key   = $table . '|' . $id;

        if (isset($MCACHE)) {
            // $table is a flag used to mark
            // a table as dirty & uncacheable
            // when an UPDATE or DELETE not bound by ID
            // is taking place
            if (!$MCACHE->get($table)) {
                // this will also release the _forfill lock
                $MCACHE->set($key, $rec, $CFG->rcachettl);
            }
        }
    }
    return true;

}

/**
 * Unset cached record if it exists.
 *
 * This function is private and must not be used outside dmllib at all
 *
 * @param $table string
 * @param $id integer
 * @return bool
 */
function rcache_unset($table, $id) {
    global $CFG, $MCACHE, $rcache;

    if ($CFG->cachetype === 'internal') {
        if (isset($rcache->data[$table][$id])) {
            unset($rcache->data[$table][$id]);
        }
    } else {
        $key   = $table . '|' . $id;
        if (isset($MCACHE)) {
            $MCACHE->delete($key);
        }
    }
    return true;
}

/**
 * Get cached record if available. ONLY use if you
 * are trying to get the cached record and will NOT
 * fetch it yourself if not cached.
 *
 * Use rcache_getforfill() if you are going to fetch
 * the record if not cached...
 *
 * This function is private and must not be used outside dmllib at all
 *
 * @param $table string
 * @param $id integer
 * @return mixed object-like record on cache hit, false otherwise
 */
function rcache_get($table, $id) {
    global $CFG, $MCACHE, $rcache;

    if ($CFG->cachetype === 'internal') {
        if (isset($rcache->data[$table][$id])) {
            $rcache->hits++;
            return clone($rcache->data[$table][$id]);
        } else {
            $rcache->misses++;
            return false;
        }
    }

    if (isset($MCACHE)) {
        $key   = $table . '|' . $id;
        // we set $table as a flag used to mark
        // a table as dirty & uncacheable
        // when an UPDATE or DELETE not bound by ID
        // is taking place
        if ($MCACHE->get($table)) {
            $rcache->misses++;
            return false;
        } else {
            $rec = $MCACHE->get($key);
            if (!empty($rec)) {
                $rcache->hits++;
                return $rec;
            } else {
                $rcache->misses++;
                return false;
            }
        }
    }
    return false;
}

/**
 * Get cached record if available. In most cases you want
 * to use this function -- namely if you are trying to get
 * the cached record and will fetch it yourself if not cached.
 * (and set the cache ;-)
 *
 * Uses the getforfill caching mechanism. See lib/eaccelerator.class.php
 * for a detailed description of the technique.
 *
 * Note: if you call rcache_getforfill() you are making an implicit promise
 * that if the cache is empty, you will later populate it, or cancel the promise
 * calling rcache_releaseforfill();
 *
 * This function is private and must not be used outside dmllib at all
 *
 * @param $table string
 * @param $id integer
 * @return mixed object-like record on cache hit, false otherwise
 */
function rcache_getforfill($table, $id) {
    global $CFG, $MCACHE, $rcache;

    if ($CFG->cachetype === 'internal') {
        return rcache_get($table, $id);
    }

    if (isset($MCACHE)) {
        $key   = $table . '|' . $id;
        // if $table is set - we won't take the
        // lock either
        if ($MCACHE->get($table)) {
            $rcache->misses++;
            return false;
        }
        $rec = $MCACHE->getforfill($key);
        if (!empty($rec)) {
            $rcache->hits++;
            return $rec;
        }
        $rcache->misses++;
        return false;
    }
    return false;
}

/**
 * Release the exclusive lock obtained by
 * rcache_getforfill(). See rcache_getforfill()
 * for more details.
 *
 * This function is private and must not be used outside dmllib at all
 *
 * @param $table string
 * @param $id integer
 * @return bool
 */
function rcache_releaseforfill($table, $id) {
    global $CFG, $MCACHE;

    if (isset($MCACHE)) {
        $key   = $table . '|' . $id;
        return $MCACHE->releaseforfill($key);
    }
    return true;
}

/**
 * Remove or invalidate all rcache entries related to
 * a table. Not all caching mechanisms cluster entries
 * by table so in those cases we use alternative strategies.
 *
 * This function is private and must not be used outside dmllib at all
 *
 * @param $table string the table to invalidate records for
 * @return bool
 */
function rcache_unset_table ($table) {
    global $CFG, $MCACHE, $rcache;

    if ($CFG->cachetype === 'internal') {
        if (isset($rcache->data[$table])) {
            unset($rcache->data[$table]);
        }
        return true;
    }

    if (isset($MCACHE)) {
        // at least as long as content keys to ensure they expire
        // before the dirty flag
        $MCACHE->set($table, true, $CFG->rcachettl);
    }
    return true;
}

?>
