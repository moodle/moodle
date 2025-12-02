<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_local_masterbuilder_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2025120201) {
        // Define table local_masterbuilder_state to be created.
        $table = new xmldb_table('local_masterbuilder_state');

        // Adding fields to table local_masterbuilder_state.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course_shortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('version', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_masterbuilder_state.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table local_masterbuilder_state.
        $table->add_index('course_shortname', XMLDB_INDEX_UNIQUE, ['course_shortname']);

        // Conditionally launch create table for local_masterbuilder_state.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Masterbuilder savepoint reached.
        upgrade_plugin_savepoint(true, 2025120201, 'local', 'masterbuilder');
    }

    return true;
}
