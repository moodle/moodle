<?php

if ($oldversion < XXXXXXXXXX) {

// Define table talentospilos_res_estudiante to be dropped.
    $table = new xmldb_table('talentospilos_res_estudiante');

// Conditionally launch drop table for talentospilos_res_estudiante.
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

// Define table talentospilos_res_estudiante to be created.
    $table = new xmldb_table('talentospilos_res_estudiante');

    // Adding fields to table talentospilos_res_estudiante.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);

    // Adding keys to table talentospilos_res_estudiante.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    // Conditionally launch create table for talentospilos_res_estudiante.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

// Define field monto_estudiante to be added to talentospilos_res_estudiante.
    $table = new xmldb_table('talentospilos_res_estudiante');
    $field = new xmldb_field('monto_estudiante', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'id');

// Conditionally launch add field monto_estudiante.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

// Define field id_semestre to be added to talentospilos_res_estudiante.
    $table = new xmldb_table('talentospilos_res_estudiante');
    $field = new xmldb_field('id_semestre', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'monto_estudiante');

// Conditionally launch add field id_semestre.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

// Define field id_estudiante to be added to talentospilos_res_estudiante.
    $table = new xmldb_table('talentospilos_res_estudiante');
    $field = new xmldb_field('id_estudiante', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_semestre');

// Conditionally launch add field id_estudiante.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field id_resolucion to be added to talentospilos_res_estudiante.
    $table = new xmldb_table('talentospilos_res_estudiante');
    $field = new xmldb_field('id_resolucion', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_estudiante');

    // Conditionally launch add field id_resolucion.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

// Define key foreign_key_semestre (foreign) to be added to talentospilos_res_estudiante.
    $table = new xmldb_table('talentospilos_res_estudiante');
    $key = new xmldb_key('foreign_key_semestre', XMLDB_KEY_FOREIGN, array('id_semestre'), 'talentospilos_semestre', array('id'));

// Launch add key foreign_key_semestre.
    $dbman->add_key($table, $key);

// Define key foreign_key_estudiante (foreign) to be added to talentospilos_res_estudiante.
    $table = new xmldb_table('talentospilos_res_estudiante');
    $key = new xmldb_key('foreign_key_estudiante', XMLDB_KEY_FOREIGN, array('id_estudiante'), 'talentospilos_usuario', array('id'));

// Launch add key foreign_key_estudiante.
    $dbman->add_key($table, $key);

// Define key foreign_key_res_icetex (foreign) to be added to talentospilos_res_estudiante.
    $table = new xmldb_table('talentospilos_res_estudiante');
    $key = new xmldb_key('foreign_key_res_icetex', XMLDB_KEY_FOREIGN, array('id_resolucion'), 'talentospilos_res_icetex', array('id'));

// Launch add key foreign_key_res_icetex.
    $dbman->add_key($table, $key);

    $table = new xmldb_table('talentospilos_res_icetex');
    $key = new xmldb_key('unique_key', XMLDB_KEY_UNIQUE, array('codigo_resolucion'));

    // Launch add key unique_key.
    $dbman->add_key($table, $key);
// Ases savepoint reached.
    upgrade_block_savepoint(true, XXXXXXXXXX, 'ases');
}
