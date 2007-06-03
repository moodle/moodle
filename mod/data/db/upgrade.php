<?php  //$Id$

// This file keeps track of upgrades to
// the data module
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

function xmldb_data_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

/// And upgrade begins here. For each one, you'll need one
/// block of code similar to the next one. Please, delete
/// this comment lines once this file start handling proper
/// upgrade code.

/// if ($result && $oldversion < YYYYMMDD00) { //New version in version.php
///     $result = result of "/lib/ddllib.php" function calls
/// }

    if ($result && $oldversion < 2006121300) {

    /// Define field format to be added to data_comments
        $table = new XMLDBTable('data_comments');
        $field = new XMLDBField('format');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'content');

    /// Launch add field format
        $result = $result && add_field($table, $field);

    }
    
    if ($result && $oldversion < 2007022600) {
    /// Define field asearchtemplate to be added to data
        $table = new XMLDBTable('data');
        $field = new XMLDBField('asearchtemplate');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'jstemplate');

    /// Launch add field asearchtemplate
        $result = $result && add_field($table, $field);
    }


    if ($result && $oldversion < 2007060300) {
        require_once($CFG->dirroot.'/mod/data/lib.php');
        // too much debug output
        $db->debug = false;
        data_update_grades();
        $db->debug = true;
    }  


    return $result;
}

?>
