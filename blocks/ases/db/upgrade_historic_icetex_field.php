<?php

if ($oldversion < XXXXXXXXXX) {

// Define field id_programa to be added to talentospilos_res_estudiante.
$table = new xmldb_table('talentospilos_res_estudiante');
$field = new xmldb_field('id_programa', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'id_resolucion');

// Conditionally launch add field id_programa.
if (!$dbman->field_exists($table, $field)) {
    $dbman->add_field($table, $field);
}

// Ases savepoint reached.
upgrade_block_savepoint(true, XXXXXXXXXX, 'ases');
}
