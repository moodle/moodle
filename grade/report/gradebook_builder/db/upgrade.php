<?php

function xmldb_gradereport_gradebook_builder_upgrade($oldversion) {
    global $DB;

    $result = true;

    $dbman = $DB->get_manager();

    if ($oldversion < 2012032613) {

        // Define table gradereport_builder_template to be created
        $table = new xmldb_table('gradereport_builder_template');

        // Adding fields to table gradereport_builder_template
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
            XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contextlevel', XMLDB_TYPE_INTEGER, '5',
            XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10',
            XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL,
            null, null);
        $table->add_field('data', XMLDB_TYPE_TEXT, 'medium', null,
            null, null, null);

        // Adding keys to table gradereport_builder_template
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table gradereport_builder_template
        $table->add_index('conins', XMLDB_INDEX_NOTUNIQUE, array(
            'contextlevel', 'instanceid'
        ));

        // Conditionally launch create table for gradereport_builder_template
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // gradebook_builder savepoint reached
        upgrade_plugin_savepoint($result, 2012032613, 'gradereport', 'gradebook_builder');
    }

    return $result;
}
