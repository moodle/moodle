<?php  //$Id$

// This file keeps track of upgrades to 
// the exercise module
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

function xmldb_exercise_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($result && $oldversion < 2007110500) {
        $table = new XMLDBTable('exercise_submissions');
        $field = new XMLDBField('late');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 0, 'isexercise');
        $result = $result && add_field($table, $field);
    }

    return $result;
}

?>
