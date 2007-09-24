<?php  //$Id: upgrade.php,v 1.4 2007/09/24 19:15:39 stronk7 Exp $

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

function xmldb_book_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

/// And upgrade begins here. For each one, you'll need one 
/// block of code similar to the next one. Please, delete 
/// this comment lines once this file start handling proper
/// upgrade code.

    if ($result && $oldversion < 2007052001) {

    /// Changing type of field importsrc on table book_chapters to char
        $table = new XMLDBTable('book_chapters');
        $field = new XMLDBField('importsrc');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null, 'timemodified');

    /// Launch change of type for field importsrc
        $result = $result && change_field_type($table, $field);
    }

    return $result;
}

?>
