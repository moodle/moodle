<?php  //$Id$

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

    global $db, $CFG, $DB;

    $olddebug = $db->debug;

    if (!$feedback) {
        if ( !defined('CLI_UPGRADE') || !CLI_UPGRADE ) {
        $db->debug = false;
    }
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

    $DB->reset_columns();  // Clear out the cache, just in case changes were made to table structures

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
            if ( defined('CLI_UPGRADE') && CLI_UPGRADE ) {
                notify (get_string('error'));
            } else {
            notify('<strong>' . get_string('error') . '</strong>');
            }
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
            print_error('This SQL relies on obsolete tables!  Your code must be fixed by a developer.');
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

error('todo');
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

error('todo');

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

?>