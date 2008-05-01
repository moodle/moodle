<?php  //$Id$

// This file keeps track of upgrades to 
// the chat module
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

function xmldb_chat_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($result && $oldversion < 2007012100) {

    /// Changing precision of field lang on table chat_users to (30)
        $table = new XMLDBTable('chat_users');
        $field = new XMLDBField('lang');
        $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, null, 'course');

    /// Launch change of precision for field lang
        $result = $result && change_field_precision($table, $field);
    }

//===== 1.9.0 upgrade line ======//

    return $result;
}

?>
