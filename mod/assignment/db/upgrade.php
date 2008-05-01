<?php  //$Id$

// This file keeps track of upgrades to
// the assignment module
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

function xmldb_assignment_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($result && $oldversion < 2007091900) { /// MDL-11268

    /// Changing nullability of field data1 on table assignment_submissions to null
        $table = new XMLDBTable('assignment_submissions');
        $field = new XMLDBField('data1');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'numfiles');

    /// Launch change of nullability for field data1
        $result = $result && change_field_notnull($table, $field);

    /// Changing nullability of field data2 on table assignment_submissions to null
        $field = new XMLDBField('data2');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'data1');

    /// Launch change of nullability for field data2
        $result = $result && change_field_notnull($table, $field);
    }

    if ($result && $oldversion < 2007091902) {
        // add draft tracking default to existing upload assignments
        $sql = "UPDATE {$CFG->prefix}assignment SET var4=1 WHERE assignmenttype='upload'";
        $result = execute_sql($sql);
    }

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2007101511) {
        notify('Processing assignment grades, this may take a while if there are many assignments...', 'notifysuccess');
        // change grade typo to text if no grades MDL-13920
        require_once $CFG->dirroot.'/mod/assignment/lib.php';
        // too much debug output
        $db->debug = false;
        assignment_update_grades();
        $db->debug = true;
    }

    return $result;
}

?>
