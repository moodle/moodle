<?php  //$Id$

// This file keeps track of upgrades to 
// the lesson module
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

function xmldb_lesson_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($result && $oldversion < 2006091802) {

    /// Changing nullability of field response on table lesson_answers to null
        $table = new XMLDBTable('lesson_answers');
        $field = new XMLDBField('response');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'answer');

    /// Launch change of nullability for field response
        $result = $result && change_field_notnull($table, $field);
    }

    if ($result && $oldversion < 2006091803) {

    /// Changing nullability of field useranswer on table lesson_attempts to null
        $table = new XMLDBTable('lesson_attempts');
        $field = new XMLDBField('useranswer');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'correct');

    /// Launch change of nullability for field useranswer
        $result = $result && change_field_notnull($table, $field);
    }

    if ($result && $oldversion < 2006091804) {

    /// Changing nullability of field answer on table lesson_answers to null
        $table = new XMLDBTable('lesson_answers');
        $field = new XMLDBField('answer');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'timemodified');

    /// Launch change of nullability for field answer
        $result = $result && change_field_notnull($table, $field);
    }

    return $result;
}

?>
