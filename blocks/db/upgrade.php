<?php  //$Id$

// This file keeps track of upgrades to 
// the blocks system
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
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_blocks_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

/// And upgrade begins here. For each one, you'll need one 
/// block of code similar to the next one. Please, delete 
/// this comment lines once this file start handling proper
/// upgrade code.

/// if ($result && $oldversion < YYYYMMDD00) { //New version in version.php
///     $result = result of database_manager methods
/// }

    if ($result && $oldversion < 2007081300) {

    /// Changing nullability of field configdata on table block_instance to null
        $table = new xmldb_table('block_instance');
        $field = new xmldb_field('configdata');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'visible');

    /// Launch change of nullability for field configdata
        $result = $result && $dbman->change_field_notnull($table, $field);
    }


    return $result;
}



?>
