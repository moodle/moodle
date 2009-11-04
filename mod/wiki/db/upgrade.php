<?php

// This file keeps track of upgrades to
// the wiki module
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

function xmldb_wiki_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2009042000) {

    /// Rename field summary on table wiki to intro
        $table = new xmldb_table('wiki');
        $field = new xmldb_field('summary', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, 'name');

    /// Launch rename field summary
        $dbman->rename_field($table, $field, 'intro');

    /// wiki savepoint reached
        upgrade_mod_savepoint($result, 2009042000, 'wiki');
    }

    if ($result && $oldversion < 2009042001) {

    /// Define field introformat to be added to wiki
        $table = new xmldb_table('wiki');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

    /// Launch add field introformat
        $dbman->add_field($table, $field);

    /// wiki savepoint reached
        upgrade_mod_savepoint($result, 2009042001, 'wiki');
    }

/// Dropping all enums/check contraints from core. MDL-18577
    if ($result && $oldversion < 2009042700) {

    /// Changing list of values (enum) of field wtype on table wiki to none
        $table = new xmldb_table('wiki');
        $field = new xmldb_field('wtype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'group', 'pagename');

    /// Launch change of list of values for field wtype
        $dbman->drop_enum_from_field($table, $field);

    /// wiki savepoint reached
        upgrade_mod_savepoint($result, 2009042700, 'wiki');
    }

    return $result;
}


