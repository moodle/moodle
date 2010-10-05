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


    if ($result && $oldversion < 2007072200) {
        require_once($CFG->dirroot.'/mod/data/lib.php');
        // too much debug output
        $db->debug = false;
        data_update_grades();
        $db->debug = true;
    }  

    if ($result && $oldversion < 2007081400) {

    /// Define field notification to be added to data
        $table = new XMLDBTable('data');
        $field = new XMLDBField('notification');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', null, null, null, null, null, null, 'editany');

    /// Launch add field notification
        $result = $result && add_field($table, $field);
    }

    if ($result && $oldversion < 2007081402) {

    /// Define index type-dataid (not unique) to be added to data_fields
        $table = new XMLDBTable('data_fields');
        $index = new XMLDBIndex('type-dataid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('type', 'dataid'));

    /// Launch add index type-dataid
        if (!index_exists($table, $index)) {
            $result = $result && add_index($table, $index);
        }

    /// Define index course (not unique) to be added to data
        $table = new XMLDBTable('data');
        $index = new XMLDBIndex('course');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('course'));

    /// Launch add index course
        if (!index_exists($table, $index)) {
            $result = $result && add_index($table, $index);
        }
    }

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2007101512) {
    /// Launch add field asearchtemplate again if does not exists yet - reported on several sites

        $table = new XMLDBTable('data');
        $field = new XMLDBField('asearchtemplate');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'jstemplate');

        if (!field_exists($table, $field)) {
            $result = $result && add_field($table, $field);
        }
    }

 ///Display a warning message about "Required Entries" fix from MDL-16999
    if ($result && $oldversion < 2007101514) {
        if (!get_config('data', 'requiredentriesfixflag')) {
            set_config('requiredentriesfixflag', true, 'data'); // remove old flag
        
            $databases = get_records_sql("SELECT d.*, c.fullname
                                              FROM {$CFG->prefix}data d, {$CFG->prefix}course c
                                              WHERE d.course = c.id
                                              AND (d.requiredentries > 0 OR d.requiredentriestoview > 0)
                                              ORDER BY c.fullname, d.name");
            if (!empty($databases)) {
                $a = new object();
                $a->text = '';
                foreach($databases as $database) {
                    $a->text .= "<p>".$database->fullname." - " .$database->name. " (course id: ".$database->course." - database id: ".$database->id.")</p>";
                }
                notify(get_string('requiredentrieschanged', 'data', $a));
            }
        }
    }

    if ($result && $oldversion <  2007101515) {
        // Upgrade all the data->notification currently being
        // NULL to 0
        $sql = "UPDATE {$CFG->prefix}data SET notification=0 WHERE notification IS NULL";
        $result = execute_sql($sql);
        $table = new XMLDBTable('data');
        $field = new XMLDBField('notification');
        // First step, Set NOT NULL
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'editany');
        $result = $result && change_field_notnull($table, $field);
        // Second step, Set default to 0
        $result = $result && change_field_default($table, $field);
    }

    return $result;
}

?>
