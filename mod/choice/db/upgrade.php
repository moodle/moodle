<?php

// This file keeps track of upgrades to
// the choice module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_choice_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();


    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014051201) {

        // Define field allowmultiple to be added to choice.
        $table = new xmldb_table('choice');
        $field = new xmldb_field('allowmultiple', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'allowupdate');

        // Conditionally launch add field allowmultiple.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Choice savepoint reached.
        upgrade_mod_savepoint(true, 2014051201, 'choice');
    }

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014111001) {

        // Define field showpreview to be added to choice.
        $table = new xmldb_table('choice');
        $field = new xmldb_field('showpreview', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'timeclose');

        // Conditionally launch add field showpreview.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Choice savepoint reached.
        upgrade_mod_savepoint(true, 2014111001, 'choice');
    }

    return true;
}


