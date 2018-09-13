<?php
if ($oldversion < XXXXXXXXXX) {

    //********************************************************//
    //**  CREACION DE LA TABLA talentospilos_history_academ **//
    //********************************************************//

    // Define table talentospilos_history_academ to be created.
    $table = new xmldb_table('talentospilos_history_academ');

    // Conditionally launch drop table for talentospilos_history_academ.
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

    // Adding fields to table talentospilos_history_academ.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);

    // Adding keys to table talentospilos_history_academ.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    // Conditionally launch create table for talentospilos_history_academ.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    // Define field id_estudiante to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $field = new xmldb_field('id_estudiante', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

    // Conditionally launch add field id_estudiante.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field id_semestre to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $field = new xmldb_field('id_semestre', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_estudiante');

    // Conditionally launch add field id_semestre.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field id_programa to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $field = new xmldb_field('id_programa', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_semestre');

    // Conditionally launch add field id_programa.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field promedio_semestre to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $field = new xmldb_field('promedio_semestre', XMLDB_TYPE_FLOAT, '20', null, null, null, null, 'id_programa');

    // Conditionally launch add field promedio_semestre.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field promedio_acumulado to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $field = new xmldb_field('promedio_acumulado', XMLDB_TYPE_FLOAT, '20', null, null, null, null, 'promedio_semestre');

    // Conditionally launch add field promedio_acumulado.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field json_materias to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $field = new xmldb_field('json_materias', XMLDB_TYPE_TEXT, null, null, null, null, null, 'promedio_acumulado');

    // Conditionally launch add field json_materias.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define key fk_estudiante (foreign) to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $key = new xmldb_key('fk_estudiante', XMLDB_KEY_FOREIGN, array('id_estudiante'), 'talentospilos_usuario', array('id'));

    // Launch add key fk_estudiante.
    $dbman->add_key($table, $key);

    // Define key fk_semestre (foreign) to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $key = new xmldb_key('fk_semestre', XMLDB_KEY_FOREIGN, array('id_semestre'), 'talentospilos_semestre', array('id'));

    // Launch add key fk_semestre.
    $dbman->add_key($table, $key);

    // Define key fk_programa (foreign) to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $key = new xmldb_key('fk_programa', XMLDB_KEY_FOREIGN, array('id_programa'), 'talentospilos_programa', array('id'));

    // Launch add key fk_programa.
    $dbman->add_key($table, $key);

    // Define key unique_key (unique) to be added to talentospilos_history_academ.
    $table = new xmldb_table('talentospilos_history_academ');
    $key = new xmldb_key('unique_key', XMLDB_KEY_UNIQUE, array('id_estudiante', 'id_semestre', 'id_programa'));

    // Launch add key unique_key.
    $dbman->add_key($table, $key);

    //********************************************************//
    //**  CREACION DE LA TABLA talentospilos_history_cancel **//
    //********************************************************//
    // Define table talentospilos_history_cancel to be created.
    $table = new xmldb_table('talentospilos_history_cancel');

    // Conditionally launch drop table for talentospilos_history_cancel.
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

    // Adding fields to table talentospilos_history_cancel.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('id_history', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    $table->add_field('fecha_cancelacion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);

    // Adding keys to table talentospilos_history_cancel.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('fk_history', XMLDB_KEY_FOREIGN, array('id_history'), 'talentospilos_history_academ', array('id'));

    // Conditionally launch create table for talentospilos_history_cancel.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    // Define field id_history to be added to talentospilos_history_cancel.
    $table = new xmldb_table('talentospilos_history_cancel');
    $field = new xmldb_field('id_history', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'id');

    // Conditionally launch add field id_history.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field fecha_cancelacion to be added to talentospilos_history_cancel.
    $table = new xmldb_table('talentospilos_history_cancel');
    $field = new xmldb_field('fecha_cancelacion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'id_history');

    // Conditionally launch add field fecha_cancelacion.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define key fk_history (foreign) to be added to talentospilos_history_cancel.
    $table = new xmldb_table('talentospilos_history_cancel');
    $key = new xmldb_key('fk_history', XMLDB_KEY_FOREIGN, array('id_history'), 'talentospilos_history_academ', array('id'));

    // Launch add key fk_history.
    $dbman->add_key($table, $key);

    //*******************************************************//
    //**  CREACION DE LA TABLA talentospilos_history_bajos **//
    //*******************************************************//

    // Define table talentospilos_history_bajos to be created.
    $table = new xmldb_table('talentospilos_history_bajos');

    // Conditionally launch drop table for talentospilos_history_cancel.
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

    // Adding fields to table talentospilos_history_bajos.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);

    // Adding keys to table talentospilos_history_bajos.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    // Conditionally launch create table for talentospilos_history_bajos.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    // Define field id_history to be added to talentospilos_history_bajos.
    $table = new xmldb_table('talentospilos_history_bajos');
    $field = new xmldb_field('id_history', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'id');

    // Conditionally launch add field id_history.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field numero_bajo to be added to talentospilos_history_bajos.
    $table = new xmldb_table('talentospilos_history_bajos');
    $field = new xmldb_field('numero_bajo', XMLDB_TYPE_INTEGER, '3', null, null, null, null, 'id_history');

    // Conditionally launch add field numero_bajo.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define key fk_history (foreign) to be added to talentospilos_history_bajos.
    $table = new xmldb_table('talentospilos_history_bajos');
    $key = new xmldb_key('fk_history', XMLDB_KEY_FOREIGN, array('id_history'), 'talentospilos_history_academ', array('id'));

    // Launch add key fk_history.
    $dbman->add_key($table, $key);

    //**********************************************************//
    //**  CREACION DE LA TABLA talentospilos_history_estimulo **//
    //**********************************************************//

    // Define table talentospilos_history_estim to be created.
    $table = new xmldb_table('talentospilos_history_estim');

    // Conditionally launch drop table for talentospilos_history_cancel.
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

    // Adding fields to table talentospilos_history_estim.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);

    // Adding keys to table talentospilos_history_estim.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    // Conditionally launch create table for talentospilos_history_estim.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    // Define field id_history to be added to talentospilos_history_estim.
    $table = new xmldb_table('talentospilos_history_estim');
    $field = new xmldb_field('id_history', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'id');

    // Conditionally launch add field id_history.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field puesto_ocupado to be added to talentospilos_history_estim.
    $table = new xmldb_table('talentospilos_history_estim');
    $field = new xmldb_field('puesto_ocupado', XMLDB_TYPE_INTEGER, '3', null, null, null, null, 'id_history');

    // Conditionally launch add field puesto_ocupado.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define key fk_history (foreign) to be added to talentospilos_history_estim.
    $table = new xmldb_table('talentospilos_history_estim');
    $key = new xmldb_key('fk_history', XMLDB_KEY_FOREIGN, array('id_history'), 'talentospilos_history_academ', array('id'));

    // Launch add key fk_history.
    $dbman->add_key($table, $key);

    // Ases savepoint reached.
    upgrade_block_savepoint(true, XXXXXXXXXX, 'ases');
}
