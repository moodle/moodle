<?php  //$Id$

// This file keeps track of upgrades to 
// the quiz module
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

function xmldb_quiz_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

/// And upgrade begins here. For each one, you'll need one 
/// block of code similar to the next one. Please, delete 
/// this comment lines once this file start handling proper
/// upgrade code.

/// if ($result && $oldversion < YYYYMMDD00) { //New version in version.php
///     $result = result of "/lib/ddllib.php" function calls
/// }

    if ($result && $oldversion < 2007022800) {
    /// Ensure that there are not existing duplicate entries in the database.
        delete_records_select('question_numerical_units', "id > (SELECT MIN(iqnu.id)
                FROM {$CFG->prefix}question_numerical_units iqnu
                WHERE iqnu.question = {$CFG->prefix}question_numerical_units.question AND
                        iqnu.unit = {$CFG->prefix}question_numerical_units.unit)");

    /// Define index question-unit (unique) to be added to question_numerical_units
        $table = new XMLDBTable('question_numerical_units');
        $index = new XMLDBIndex('question-unit');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('question', 'unit'));

    /// Launch add index question-unit
        $result = $result && add_index($table, $index);
    }

    return $result;
}

?>
