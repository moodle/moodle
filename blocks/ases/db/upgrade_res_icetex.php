<?php

if ($oldversion < XXXXXXXXXX) {

// Define table talentospilos_res_icetex to be dropped.
$table = new xmldb_table('talentospilos_res_icetex');

// Conditionally launch drop table for talentospilos_res_icetex.
if ($dbman->table_exists($table)) {
    $dbman->drop_table($table);
}

// Define table talentospilos_res_icetex to be created.
$table = new xmldb_table('talentospilos_res_icetex');

// Adding fields to table talentospilos_res_icetex.
$table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);

// Adding keys to table talentospilos_res_icetex.
$table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

// Conditionally launch create table for talentospilos_res_icetex.
if (!$dbman->table_exists($table)) {
    $dbman->create_table($table);
}

// Define field codigo_resolucion to be added to talentospilos_res_icetex.
$table = new xmldb_table('talentospilos_res_icetex');
$field = new xmldb_field('codigo_resolucion', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'id');

// Conditionally launch add field codigo_resolucion.
if (!$dbman->field_exists($table, $field)) {
    $dbman->add_field($table, $field);
}

// Define field monto_total to be added to talentospilos_res_icetex.
$table = new xmldb_table('talentospilos_res_icetex');
$field = new xmldb_field('monto_total', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'codigo_resolucion');

// Conditionally launch add field monto_total.
if (!$dbman->field_exists($table, $field)) {
    $dbman->add_field($table, $field);
}

// Define field fecha_resolucion to be added to talentospilos_res_icetex.
$table = new xmldb_table('talentospilos_res_icetex');
$field = new xmldb_field('fecha_resolucion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'monto_total');

// Conditionally launch add field fecha_resolucion.
if (!$dbman->field_exists($table, $field)) {
    $dbman->add_field($table, $field);
}

// Define key unique_cod_res (unique) to be added to talentospilos_res_icetex.
$table = new xmldb_table('talentospilos_res_icetex');
$key = new xmldb_key('unique_cod_res', XMLDB_KEY_UNIQUE, array('codigo_resolucion'));

// Launch add key unique_cod_res.
$dbman->add_key($table, $key);


// Ases savepoint reached.
upgrade_block_savepoint(true, XXXXXXXXXX, 'ases');
}