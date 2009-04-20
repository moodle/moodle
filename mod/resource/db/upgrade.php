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
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_resource_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2009042000) {

    /// Rename field summary on table resource to intro
        $table = new xmldb_table('resource');
        $field = new xmldb_field('summary', XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'reference');

    /// Launch rename field summary
        $dbman->rename_field($table, $field, 'intro');

    /// resource savepoint reached
        upgrade_mod_savepoint($result, 2009042000, 'resource');
    }

    if ($result && $oldversion < 2009042001) {

    /// Define field introformat to be added to resource
        $table = new xmldb_table('resource');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'intro');

    /// Launch add field introformat
        $dbman->add_field($table, $field);

    /// set format to current
        $DB->set_field('resource', 'introformat', FORMAT_MOODLE, array());

    /// resource savepoint reached
        upgrade_mod_savepoint($result, 2009042001, 'resource');
    }

    return $result;
}

?>
