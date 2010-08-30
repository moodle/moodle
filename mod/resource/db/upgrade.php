<?php  //$Id$

// This file keeps track of upgrades to 
// the resource module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

function xmldb_resource_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

/// And upgrade begins here. For each one, you'll need one 
/// block of code similar to the next one. Please, delete 
/// this comment lines once this file start handling proper
/// upgrade code.

/// if ($result && $oldversion < YYYYMMDD00) { //New version in version.php
///     $result = result of "/lib/ddllib.php" function calls
/// }

    if ($result && $oldversion < 2007011700) {
        //move format from options to reference field because it was colliding with course blocks setting
        execute_sql("UPDATE {$CFG->prefix}resource SET reference=options WHERE type='text' AND reference='' AND options!='showblocks'");
        //ignore result
    }

    if ($result && $oldversion < 2007012000) {

    /// Changing nullability of field summary on table resource to null
        $table = new XMLDBTable('resource');
        $field = new XMLDBField('summary');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'reference');

    /// Launch change of nullability for field summary
        $result = $result && change_field_notnull($table, $field);
    }

    if ($result && $oldversion < 2007012001) {

        if ($CFG->dbfamily == 'mysql') { // Only needed under mysql. The rest are long texts since ages

        /// Changing precision of field alltext on table resource to (medium)
            $table = new XMLDBTable('resource');
            $field = new XMLDBField('alltext');
            $field->setAttributes(XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null, null, null, 'summary');

        /// Launch change of precision for field alltext
            $result = $result && change_field_precision($table, $field);
        }
    }

    //Set 'Show navigation' setting to "Yes, without frame" for PDF file resources
    //Explanation: due to MDL-20320 bug, PDF can now be displayed as 'No', 'Yes, with frame' and 'Yes, without frame'.
    //The default being 'no', PDF resources on previous installations need to be set back to 'Yes, without frame'
    if ($result && $oldversion < 2007101510) {
       $sql = "UPDATE {$CFG->prefix}resource SET options = 'objectframe' WHERE (UPPER(reference) LIKE '%.PDF'
        OR UPPER(reference) LIKE '%.FDF'
        OR UPPER(reference) LIKE '%.XDP'
        OR UPPER(reference) LIKE '%.XFD'
        OR UPPER(reference) LIKE '%.XFDF')
        AND type='file' AND ".sql_isempty('resource', 'popup', false, true)."
        AND (".sql_isempty('resource', 'options', false, false)." OR options = 'frame')";
        $result = $result && execute_sql($sql);
    }

    // MDL-10906. Removing resource_allowlocalfiles setting.
    if ($result && $oldversion < 2007101511) {
        $result = $result && unset_config('resource_allowlocalfiles');
    }
    
//===== 1.9.0 upgrade line ======//
    return $result;
}

?>
