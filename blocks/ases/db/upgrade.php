<?php 
require_once(dirname(__FILE__).'/../../../config.php');
function xmldb_block_ases_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();
    $result = true;
    if ($oldversion < 2018083110300 ) {
    //     // ************************************************************************************************************
    //     // Actualización que crea la tabla para los campos extendidos de usuario (Tabla: {talentospilos_user_extended})
    //     // Versión: 2018010911179
    //     // ************************************************************************************************************
    //     // Define table talentospilos_user_extended to be created.
    //     $table = new xmldb_table('talentospilos_user_extended');
    //     // Adding fields to table talentospilos_user_extended.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_moodle_user', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_ases_user', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_academic_program', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('program_status', XMLDB_TYPE_BINARY, null, null, XMLDB_NOTNULL, null, null);
    //     // Adding keys to table talentospilos_user_extended.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk1_moodle_user', XMLDB_KEY_FOREIGN, array('id_moodle_user'), 'user', array('id'));
    //     $table->add_key('fk2_ases_user', XMLDB_KEY_FOREIGN, array('id_ases_user'), 'talentospilos_usuario', array('id'));
    //     $table->add_key('fk3_academic_program', XMLDB_KEY_FOREIGN, array('id_academic_program'), 'talentospilos_programa', array('id'));
    //     // Conditionally launch create table for talentospilos_user_extended.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     // ************************************************************************************************************
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Añade el campo id_funcionalidad en la tabla {talentospilos_est_estadoases}
    //     // Versión en la que se incluye: 2018011716029
    //     // ************************************************************************************************************
    //     // Define field id_instancia to be added to talentospilos_est_estadoases.
    //     $table = new xmldb_table('talentospilos_est_estadoases');
    //     $field = new xmldb_field('id_instancia', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'fecha');
    //     // Conditionally launch add field id_instancia.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //         // Define key id_instancia_fk (foreign) to be added to talentospilos_est_estadoases.
    //         $table = new xmldb_table('talentospilos_est_estadoases');
    //         $key = new xmldb_key('id_instancia_fk', XMLDB_KEY_FOREIGN, array('id_instancia'), 'talentospilos_instancia', array('id'));
    //         // Launch add key id_instancia_fk.
    //         $dbman->add_key($table, $key);
    //     }
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Añade el campo estado_seguimiento en la tabla {talentospilos_user_extended}
    //     // Versión en la que se incluye: 2018011716029
    //     // ************************************************************************************************************
    //     // Define field id_instancia to be added to talentospilos_est_estadoases.
    //     $table = new xmldb_table('talentospilos_user_extended');
    //     $field = new xmldb_field('tracking_status', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'program_status');
    //     // Conditionally launch add field id_instancia.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se modifica el nombre del campo estado_seguimiento en la tabla {talentospilos_user_extended}. Se pasa de estado_seguimiento a tracking_status
    //     // Versión en la que se incluye: 2018011911069
    //     // ************************************************************************************************************
    //     // Rename field id_estado_icetex on table talentospilos_est_est_icetex to tracking_status.
    //     //$table = new xmldb_table('talentospilos_user_extended');
    //     //$field = new xmldb_field('estado_seguimiento', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'program_status');
    //     // Launch rename field id_estado_icetex.
    //     //$dbman->rename_field($table, $field, 'tracking_status');
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_instancia_cohorte}. Con los campos
    //     //          id --> Autoincremental
    //     //          id_cohorte --> Identificador de la cohorte 
    //     //          id_instancia --> Identificador de la instancia relacionada a la cohorte
    //     // Versión en la que se incluye: 2018011911069
    //     // ************************************************************************************************************
        
    //     // Define table talentospilos_inst_cohorte to be created.
    //     $table = new xmldb_table('talentospilos_inst_cohorte');
    //     // Adding fields to table talentospilos_inst_cohorte.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_cohorte', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_instancia', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     // Adding keys to table talentospilos_inst_cohorte.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_inst_cohorte.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_history_academ}. Con los campos
    //     //          id --> Autoincremental
    //     //          id_estudiante --> Identificador del estudiante ASES
    //     //          id_semestre --> Identificador del semestre o periodo académico. Apunta a {talentospilos_semestre}
    //     //          id_programa --> Identificador del programa académico. Apunta a {talentospilos_programa}
    //     //          promedio_semestre --> Promedio semestral
    //     //          promedio_acumulado --> Promedio acumulado 
    //     //          json_materias --> Materias relacionadas al período académico
    //     // Versión en la que se incluye: 2018011911069
    //     // ************************************************************************************************************
        
    //     // Define table talentospilos_history_academ to be created.
    //     $table = new xmldb_table('talentospilos_history_academ');
        
    //     // Adding fields to table talentospilos_history_academ.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     // Adding keys to table talentospilos_history_academ.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_history_academ.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     // Define field id_estudiante to be added to talentospilos_history_academ.
    //     $table = new xmldb_table('talentospilos_history_academ');
    //     $field = new xmldb_field('id_estudiante', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
    //     // Conditionally launch add field id_estudiante.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define field id_semestre to be added to talentospilos_history_academ.
    //     $table = new xmldb_table('talentospilos_history_academ');
    //     $field = new xmldb_field('id_semestre', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_estudiante');
    //     // Conditionally launch add field id_semestre.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define field id_programa to be added to talentospilos_history_academ.
    //     $table = new xmldb_table('talentospilos_history_academ');
    //     $field = new xmldb_field('id_programa', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_semestre');
    //     // Conditionally launch add field id_programa.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    // // Define field promedio_semestre to be added to talentospilos_history_academ.
    // $table = new xmldb_table('talentospilos_history_academ');
    // $field = new xmldb_field('promedio_semestre', XMLDB_TYPE_FLOAT, '20', null, null, null, null, 'id_programa');
    // // Conditionally launch add field promedio_semestre.
    // if (!$dbman->field_exists($table, $field)) {
    //     $dbman->add_field($table, $field);
    // }
    // // Define field promedio_acumulado to be added to talentospilos_history_academ.
    // $table = new xmldb_table('talentospilos_history_academ');
    // $field = new xmldb_field('promedio_acumulado', XMLDB_TYPE_FLOAT, '20', null, null, null, null, 'promedio_semestre');
    // // Conditionally launch add field promedio_acumulado.
    // if (!$dbman->field_exists($table, $field)) {
    //     $dbman->add_field($table, $field);
    // }
        
    //     // Define field json_materias to be added to talentospilos_history_academ.
    //     $table = new xmldb_table('talentospilos_history_academ');
    //     $field = new xmldb_field('json_materias', XMLDB_TYPE_TEXT, null, null, null, null, null, 'promedio_acumulado');
    //     // Conditionally launch add field json_materias.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define key fk_estudiante (foreign) to be added to talentospilos_history_academ.
    //     $table = new xmldb_table('talentospilos_history_academ');
    //     $key = new xmldb_key('fk_estudiante', XMLDB_KEY_FOREIGN, array('id_estudiante'), 'talentospilos_usuario', array('id'));
    //     // Launch add key fk_estudiante.
    //     $dbman->add_key($table, $key);
    //     // Define key fk_semestre (foreign) to be added to talentospilos_history_academ.
    //     $table = new xmldb_table('talentospilos_history_academ');
    //     $key = new xmldb_key('fk_semestre', XMLDB_KEY_FOREIGN, array('id_semestre'), 'talentospilos_semestre', array('id'));
    //     // Launch add key fk_semestre.
    //     $dbman->add_key($table, $key);
    //     // Define key fk_programa (foreign) to be added to talentospilos_history_academ.
    //     $table = new xmldb_table('talentospilos_history_academ');
    //     $key = new xmldb_key('fk_programa', XMLDB_KEY_FOREIGN, array('id_programa'), 'talentospilos_programa', array('id'));
    //     // Launch add key fk_programa.
    //     $dbman->add_key($table, $key);
    //     // Define key unique_key (unique) to be added to talentospilos_history_academ.
    //     $table = new xmldb_table('talentospilos_history_academ');
    //     $key = new xmldb_key('unique_key', XMLDB_KEY_UNIQUE, array('id_estudiante', 'id_semestre', 'id_programa'));
    //     // Launch add key unique_key.
    //     $dbman->add_key($table, $key);
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_history_cancel}. Con los campos
    //     //          id --> Autoincremental
    //     //          id_history --> Identificador del histórico académico
    //     //          fecha_cancelacion --> Fecha en la que se realiza la cancelación del semestre
    //     // Versión en la que se incluye: 2018011911069
    //     // ************************************************************************************************************
    //     // Define table talentospilos_history_cancel to be created.
    //     $table = new xmldb_table('talentospilos_history_cancel');
    //     // Adding fields to table talentospilos_history_cancel.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_history', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('fecha_cancelacion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     // Adding keys to table talentospilos_history_cancel.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk_history', XMLDB_KEY_FOREIGN, array('id_history'), 'talentospilos_history_academ', array('id'));
    //     // Conditionally launch create table for talentospilos_history_cancel.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     // Define field id_history to be added to talentospilos_history_cancel.
    //     $table = new xmldb_table('talentospilos_history_cancel');
    //     $field = new xmldb_field('id_history', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'id');
    //     // Conditionally launch add field id_history.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define field fecha_cancelacion to be added to talentospilos_history_cancel.
    //     $table = new xmldb_table('talentospilos_history_cancel');
    //     $field = new xmldb_field('fecha_cancelacion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'id_history');
    //     // Conditionally launch add field fecha_cancelacion.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define key fk_history (foreign) to be added to talentospilos_history_cancel.
    //     $table = new xmldb_table('talentospilos_history_cancel');
    //     $key = new xmldb_key('fk_history', XMLDB_KEY_FOREIGN, array('id_history'), 'talentospilos_history_academ', array('id'));
    //     // Launch add key fk_history.
    //     $dbman->add_key($table, $key);
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_history_bajos}. Con los campos
    //     //          id --> Autoincremental
    //     //          id_history --> Identificador del histórico académico
    //     //          numero_bajo --> Cantidad de bajos registrados
    //     // Versión en la que se incluye: 2018011911069
    //     // ************************************************************************************************************
    //     // Define table talentospilos_history_bajos to be created.
    //     $table = new xmldb_table('talentospilos_history_bajos');
    //     // Adding fields to table talentospilos_history_bajos.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     // Adding keys to table talentospilos_history_bajos.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_history_bajos.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     // Define field id_history to be added to talentospilos_history_bajos.
    //     $table = new xmldb_table('talentospilos_history_bajos');
    //     $field = new xmldb_field('id_history', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'id');
    //     // Conditionally launch add field id_history.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define field numero_bajo to be added to talentospilos_history_bajos.
    //     $table = new xmldb_table('talentospilos_history_bajos');
    //     $field = new xmldb_field('numero_bajo', XMLDB_TYPE_INTEGER, '3', null, null, null, null, 'id_history');
    //     // Conditionally launch add field numero_bajo.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define key fk_history (foreign) to be added to talentospilos_history_bajos.
    //     $table = new xmldb_table('talentospilos_history_bajos');
    //     $key = new xmldb_key('fk_history', XMLDB_KEY_FOREIGN, array('id_history'), 'talentospilos_history_academ', array('id'));
    //     // Launch add key fk_history.
    //     $dbman->add_key($table, $key);
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_history_estim}. Con los campos
    //     //          id --> Autoincremental
    //     //          id_history --> Identificador del histórico académico
    //     //          puesto_ocupado --> Puesto ocupado por el estudiante en el semestre  
    //     // Versión en la que se incluye: 2018011911069
    //     // ************************************************************************************************************
    //     // Define table talentospilos_history_estim to be created.
    //     $table = new xmldb_table('talentospilos_history_estim');
    //     // Adding fields to table talentospilos_history_estim.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     // Adding keys to table talentospilos_history_estim.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_history_estim.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     // Define field id_history to be added to talentospilos_history_estim.
    //     $table = new xmldb_table('talentospilos_history_estim');
    //     $field = new xmldb_field('id_history', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'id');
    //     // Conditionally launch add field id_history.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define field puesto_ocupado to be added to talentospilos_history_estim.
    //     $table = new xmldb_table('talentospilos_history_estim');
    //     $field = new xmldb_field('puesto_ocupado', XMLDB_TYPE_INTEGER, '3', null, null, null, null, 'id_history');
    //     // Conditionally launch add field puesto_ocupado.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define key fk_history (foreign) to be added to talentospilos_history_estim.
    //     $table = new xmldb_table('talentospilos_history_estim');
    //     $key = new xmldb_key('fk_history', XMLDB_KEY_FOREIGN, array('id_history'), 'talentospilos_history_academ', array('id'));
    //     // Launch add key fk_history.
    //     $dbman->add_key($table, $key);
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_res_estudiante}. Con los campos
    //     //          id --> Autoincremental
    //     //          monto_estudiante --> Identificador del histórico académico
    //     //          id_semestre --> Identificador del semestre académico
    //     //          id_estudiante --> Identificador asociado al estudiante ASES
    //     //          id_resolucion --> Identificador de la resolución asociada al estudiante
    //     // Versión en la que se incluye: 2018013010459
    //     // ************************************************************************************************************
    //     // Define table talentospilos_res_estudiante to be dropped.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     // Conditionally launch drop table for talentospilos_res_estudiante.
        
    //     // Define table talentospilos_res_estudiante to be created.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     // Adding fields to table talentospilos_res_estudiante.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     // Adding keys to table talentospilos_res_estudiante.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_res_estudiante.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     // Define field monto_estudiante to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $field = new xmldb_field('monto_estudiante', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'id');
    //     // Conditionally launch add field monto_estudiante.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
        
    //     // Define field id_estudiante to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $field = new xmldb_field('id_estudiante', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_semestre');
    //     // Conditionally launch add field id_estudiante.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define field id_resolucion to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $field = new xmldb_field('id_resolucion', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_estudiante');
    //     // Conditionally launch add field id_resolucion.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }    
    //     // Define key foreign_key_estudiante (foreign) to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $key = new xmldb_key('foreign_key_estudiante', XMLDB_KEY_FOREIGN, array('id_estudiante'), 'talentospilos_usuario', array('id'));
    //     // Launch add key foreign_key_estudiante.
    //     $dbman->add_key($table, $key);
    //     // Define key foreign_key_res_icetex (foreign) to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $key = new xmldb_key('foreign_key_res_icetex', XMLDB_KEY_FOREIGN, array('id_resolucion'), 'talentospilos_res_icetex', array('id'));
    //     // Launch add key foreign_key_res_icetex.
    //     $dbman->add_key($table, $key);
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_res_icetex}. Con los campos
    //     //          id --> Autoincremental
    //     //          codigo_resolucion --> Identificador del histórico académico
    //     //          monto_total --> Identificador del semestre académico
    //     //          fecha_resolucion --> Identificador asociado al estudiante ASES
    //     // Versión en la que se incluye: 2018013010459
    //     // ************************************************************************************************************
    //     // Define table talentospilos_res_icetex to be dropped.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     // Conditionally launch drop table for talentospilos_res_icetex.
        
    //     // Define table talentospilos_res_icetex to be created.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     // Adding fields to table talentospilos_res_icetex.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     // Adding keys to table talentospilos_res_icetex.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_res_icetex.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     // Define field codigo_resolucion to be added to talentospilos_res_icetex.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     $field = new xmldb_field('codigo_resolucion', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'id');
    //     // Conditionally launch add field codigo_resolucion.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // Define field id_semestre to be added to talentospilos_res_icetex.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     $field = new xmldb_field('id_semestre', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'codigo_resolucion');
    //     // Conditionally launch add field id_semestre.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // Define field monto_total to be added to talentospilos_res_icetex.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     $field = new xmldb_field('monto_total', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'codigo_resolucion');
    //     // Conditionally launch add field monto_total.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define field fecha_resolucion to be added to talentospilos_res_icetex.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     $field = new xmldb_field('fecha_resolucion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'monto_total');
    //     // Conditionally launch add field fecha_resolucion.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // Define key unique_cod_res (unique) to be added to talentospilos_res_icetex.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     $key = new xmldb_key('unique_cod_res', XMLDB_KEY_UNIQUE, array('codigo_resolucion'));
    //     // Launch add key unique_cod_res.
    //     $dbman->add_key($table, $key);
    //     // Define key foreign_key_semestre (foreign) to be added to talentospilos_res_icetex.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     $key = new xmldb_key('foreign_key_semestre', XMLDB_KEY_FOREIGN, array('id_semestre'), 'talentospilos_semestre', array('id'));
    //     // Launch add key foreign_key_semestre.
    //     $dbman->add_key($table, $key);

    //     //*************************************************************************************************************
    //     // ************************************************************************************************************
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_formularios
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
        
    //     // Define table talentospilos_df_formularios to be created.
    //     $table = new xmldb_table('talentospilos_df_formularios');
    //     // Adding fields to table talentospilos_df_formularios.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('nombre', XMLDB_TYPE_CHAR, '140', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('descripcion', XMLDB_TYPE_TEXT, null, null, null, null, null);
    //     $table->add_field('method', XMLDB_TYPE_CHAR, '140', null, null, null, null);
    //     $table->add_field('action', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('enctype', XMLDB_TYPE_TEXT, null, null, null, null, null);
    //     $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, null, null, "now()");
    //     // Adding keys to table talentospilos_df_formularios.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_df_formularios.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_formularios
        
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_tipo_campo
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     // Define table talentospilos_df_tipo_campo to be created.
    //     $table = new xmldb_table('talentospilos_df_tipo_campo');
    //     // Adding fields to table talentospilos_df_tipo_campo.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('campo', XMLDB_TYPE_CHAR, '140', null, null, null, null);
    //     $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, null, null, "now()");
    //     // Adding keys to table talentospilos_df_tipo_campo.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_df_tipo_campo.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_tipo_campo
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_preguntas
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
        
    //     // Define table talentospilos_df_preguntas to be created.
    //     $table = new xmldb_table('talentospilos_df_preguntas');
    //     // Adding fields to table talentospilos_df_preguntas.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('tipo_campo', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('opciones_campo', XMLDB_TYPE_TEXT, null, null, null, null, null);
    //     $table->add_field('atributos_campo', XMLDB_TYPE_TEXT, null, null, null, null, null);
    //     $table->add_field('enunciado', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");
    //     // Adding keys to table talentospilos_df_preguntas.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk_preguntas_id_tipo_pregunta', XMLDB_KEY_FOREIGN, array('tipo_campo'), 'talentospilos_df_tipo_campo', array('id'));
    //     // Conditionally launch create table for talentospilos_df_preguntas.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_preguntas
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_form_preg
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     // Define table talentospilos_df_form_preg to be created.
    //     $table = new xmldb_table('talentospilos_df_form_preg');
    //     // Adding fields to table talentospilos_df_form_preg.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_formulario', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_pregunta', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('posicion', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");
    //     // Adding keys to table talentospilos_df_form_preg.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_df_form_preg.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_formulario_preguntas
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_reglas
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
        
    //     // Define table talentospilos_df_reglas to be created.
    //     $table = new xmldb_table('talentospilos_df_reglas');
    //     // Adding fields to table talentospilos_df_reglas.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('regla', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
    //     // Adding keys to table talentospilos_df_reglas.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     // Conditionally launch create table for talentospilos_df_reglas.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_reglas
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_respuestas
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     // Define table talentospilos_df_respuestas to be created.
    //     $table = new xmldb_table('talentospilos_df_respuestas');
    //     // Adding fields to table talentospilos_df_respuestas.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_pregunta', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('respuesta', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, null, null, "now()");
    //     // Adding keys to table talentospilos_df_respuestas.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk_respuestas_id_pregunta', XMLDB_KEY_FOREIGN, array('id_pregunta'), 'talentospilos_df_preguntas', array('id'));
    //     // Conditionally launch create table for talentospilos_df_respuestas.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_respuestas
        
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_form_resp
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
        
    //     // Define table talentospilos_df_form_resp to be created.
    //     $table = new xmldb_table('talentospilos_df_form_resp');
    //     // Adding fields to table talentospilos_df_form_resp.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_formulario', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_monitor', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_estudiante', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");
    //     // Adding keys to table talentospilos_df_form_resp.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk_form_resp_id_formulario', XMLDB_KEY_FOREIGN, array('id_formulario'), 'talentospilos_df_formularios', array('id'));
    //     // Conditionally launch create table for talentospilos_df_form_resp.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_formulario_respuestas
        
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_form_solu
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    
    //     // Define table talentospilos_df_form_solu to be created.
    //     $table = new xmldb_table('talentospilos_df_form_solu');
    //     // Adding fields to table talentospilos_df_form_solu.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_formulario_respuestas', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_respuesta', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()");
    //     // Adding keys to table talentospilos_df_form_solu.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk_form_solu_id_form_resp', XMLDB_KEY_FOREIGN, array('id_formulario_respuestas'), 'talentospilos_df_form_resp', array('id'));
    //     $table->add_key('fk_form_solu_id_resp', XMLDB_KEY_FOREIGN, array('id_respuesta'), 'talentospilos_df_respuestas', array('id'));
    //     // Conditionally launch create table for talentospilos_df_form_solu.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_soluciones
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_reg_form_pr
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     // Define table talentospilos_df_reg_form_pr to be created.
    //     $table = new xmldb_table('talentospilos_df_reg_form_pr');
    //     // Adding fields to table talentospilos_df_reg_form_pr.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_formulario', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_regla', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_form_pregunta_a', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_form_pregunta_b', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     // Adding keys to table talentospilos_df_reg_form_pr.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk_reg_form_pr_formularios', XMLDB_KEY_FOREIGN, array('id_formulario'), 'talentospilos_df_formularios', array('id'));
    //     $table->add_key('fk_reg_form_pr_id_reglas', XMLDB_KEY_FOREIGN, array('id_regla'), 'talentospilos_df_reglas', array('id'));
    //     $table->add_key('fk_reg_form_pr_id_form_pregunta_a', XMLDB_KEY_FOREIGN, array('id_form_pregunta_a'), 'talentospilos_df_form_preg', array('id'));
    //     $table->add_key('fk_reg_form_pr_id_form_pregunta_b', XMLDB_KEY_FOREIGN, array('id_form_pregunta_b'), 'talentospilos_df_form_preg', array('id'));
    //     // Conditionally launch create table for talentospilos_df_reg_form_pr.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_reglas_form_preguntas
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_per_form_pr
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     // Define table talentospilos_df_per_form_pr to be created.
    //     $table = new xmldb_table('talentospilos_df_per_form_pr');
    //     // Adding fields to table talentospilos_df_per_form_pr.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_formulario_pregunta', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('permisos', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    //     // Adding keys to table talentospilos_df_per_form_pr.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk_per_form_pr_id_form_preg', XMLDB_KEY_FOREIGN, array('id_formulario_pregunta'), 'talentospilos_df_form_preg', array('id'));
    //     // Conditionally launch create table for talentospilos_df_per_form_pr.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_permisos_formulario_preguntas
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la tabla talentospilos_df_disp_fordil
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     // Define table talentospilos_df_disp_fordil to be created.
    //     $table = new xmldb_table('talentospilos_df_disp_fordil');
    //     // Adding fields to table talentospilos_df_disp_fordil.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_formulario', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('disparadores', XMLDB_TYPE_TEXT, null, null, null, null, null);
    //     // Adding keys to table talentospilos_df_disp_fordil.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('fk_disparadores_id_formulario', XMLDB_KEY_FOREIGN_UNIQUE, array('id_formulario'), 'talentospilos_df_formularios', array('id'));
    //     // Conditionally launch create table for talentospilos_df_disp_fordil.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }
    //     //end tp_disparadores_formulario_diligenciado
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se inserta campo id_programa en la tabla talentospilos_res_estudiante
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     // Define field id_programa to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $field = new xmldb_field('id_programa', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id_resolucion');
    //     // Conditionally launch add field id_programa.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la llave foránea desde campo id_programa en la tabla talentospilos_res_estudiante hacia
    //     // la tabla talentospilos_programa
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     // Define key fk_programa (foreign) to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $key = new xmldb_key('fk_programa', XMLDB_KEY_FOREIGN, array('id_programa'), 'talentospilos_programa', array('id'));
    //     // Launch add key fk_programa.
    //     $dbman->add_key($table, $key);
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se insertan registros para los tipos de campo 
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
        
    //     //Registro de los tipos de campos  
    //     $verificador = $DB->get_record_sql("SELECT * FROM {talentospilos_df_tipo_campo} WHERE campo = 'TEXTFIELD'");
    //     if(!$verificador){
    //         $campo_textfield = new stdClass();
    //         $campo_textfield->campo                = 'TEXTFIELD';
    //         $campo_textfield->fecha_hora_registro  = 'now()';
    //         $campo_textarea = new stdClass();
    //         $campo_textarea->campo                 = 'TEXTAREA';
    //         $campo_textarea->fecha_hora_registro   = 'now()';
    //         $campo_date = new stdClass();
    //         $campo_date->campo                     = 'DATE';
    //         $campo_date->fecha_hora_registro       = 'now()';
    //         $campo_time = new stdClass();
    //         $campo_time->campo                     = 'TIME';
    //         $campo_time->fecha_hora_registro       = 'now()';
    //         $campo_radio = new stdClass();
    //         $campo_radio->campo                    = 'RADIOBUTTON';
    //         $campo_radio->fecha_hora_registro      = 'now()';
    //         $campo_check = new stdClass();
    //         $campo_check->campo                    = 'CHECKBOX';
    //         $campo_check->fecha_hora_registro      = 'now()';
    //         $records = array();
    //         array_push($records, $campo_textfield);
    //         array_push($records, $campo_textarea);
    //         array_push($records, $campo_date);
    //         array_push($records, $campo_time);
    //         array_push($records, $campo_radio);
    //         array_push($records, $campo_check);
    //         $DB->insert_records('talentospilos_df_tipo_campo', $records);
    //     }

    //     $sql_intel = "DELETE FROM {talentospilos_df_tipo_campo} WHERE id <> 1 and id <> 2 and id <> 3 and id <> 4 and id <> 5 and id <> 6";
    //     $DB->execute($sql_intel);
        
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se insertan registros para las reglas de los formularios
    //     // Versión en la que se incluye: 2018012217129
    //     // ************************************************************************************************************
    //     $verificador_reglas = $DB->get_record_sql("SELECT * FROM {talentospilos_df_reglas} WHERE regla = 'EQUAL'");
    //     if(!$verificador_reglas){
    //         $regla_mayor_que = new stdClass();
    //         $regla_mayor_que->regla    = '>';
    //         $regla_menor_que = new stdClass();
    //         $regla_menor_que->regla    = '<';
    //         $regla_igual = new stdClass();
    //         $regla_igual->regla        = 'EQUAL';
    //         $regla_diferente = new stdClass();
    //         $regla_diferente->regla    = 'DIFFERENT';
    //         $regla_depende = new stdClass();
    //         $regla_depende->regla      = 'DEPENDS';
    //         $regla_enlazado = new stdClass();
    //         $regla_enlazado->regla     = 'BOUND';
    //         $records = array();
    //         array_push($records, $regla_mayor_que);
    //         array_push($records, $regla_menor_que);
    //         array_push($records, $regla_igual);
    //         array_push($records, $regla_diferente);
    //         array_push($records, $regla_depende);
    //         array_push($records, $regla_enlazado);
    //         $DB->insert_records('talentospilos_df_reglas', $records);
    //     }
    //     $sql_intel = "DELETE FROM {talentospilos_df_reglas} WHERE id <> 1 and id <> 2 and id <> 3 and id <> 4 and id <> 5 and id <> 6";
    //     $DB->execute($sql_intel);
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se inserta campo id_programa en la tabla talentospilos_res_estudiante
    //     // Versión en la que se incluye: 2018012413229
    //     // ************************************************************************************************************
    //     // Define field id_programa to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $field = new xmldb_field('id_programa', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'id_resolucion');
    //     // Conditionally launch add field id_programa.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }
    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la llave foránea desde campo id_programa en la tabla talentospilos_res_estudiante hacia
    //     // la tabla talentospilos_programa
    //     // Versión en la que se incluye: 2018012413229
    //     // ************************************************************************************************************
    //     // Define key fk_programa (foreign) to be added to talentospilos_res_estudiante.
    //     $table = new xmldb_table('talentospilos_res_estudiante');
    //     $key = new xmldb_key('fk_programa', XMLDB_KEY_FOREIGN, array('id_programa'), 'talentospilos_programa', array('id'));
    //     // Launch add key fk_programa.
    //     $dbman->add_key($table, $key);

    //         // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade el campo id_programa a la tabla {talentospilos_user_rol}
    //     // Versión en la que se incluye: 2018012911099
    //     // ************************************************************************************************************

    //     // Define field id_programa to be added to talentospilos_user_rol.
    //     $table = new xmldb_table('talentospilos_user_rol');
    //     $field = new xmldb_field('id_programa', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'id_instancia');

    //     // Conditionally launch add field id_programa.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la llave foránea fk_program a la tabla {talentospilos_user_rol}
    //     // Versión en la que se incluye: 2018012911099
    //     // ************************************************************************************************************

    //     // Define key fk_program (foreign) to be added to talentospilos_user_rol.
    //     $table = new xmldb_table('talentospilos_user_rol');
    //     $key = new xmldb_key('fk_program', XMLDB_KEY_FOREIGN, array('id_programa'), 'talentospilos_programa', array('id'));

    //     // Launch add key fk_program.
    //     $dbman->add_key($table, $key);


    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_materias_criti}
    //     // Versión en la que se incluye: 2018012918099
    //     // ************************************************************************************************************

    //     // Define table talentospilos_materias_criti to be created.
    //         $table = new xmldb_table('talentospilos_materias_criti');

    //         // Adding fields to table talentospilos_materias_criti.
    //         $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //         $table->add_field('codigo_materia', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);

    //         // Adding keys to table talentospilos_materias_criti.
    //         $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //         $table->add_key('unique_key', XMLDB_KEY_UNIQUE, array('codigo_materia'));

    //         // Conditionally launch create table for talentospilos_materias_criti.
    //         if (!$dbman->table_exists($table)) {
    //             $dbman->create_table($table);
    //         }

    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla {talentospilos_df_alias}
    //     // Versión en la que se incluye: 2018013114389
    //     // ************************************************************************************************************

    //         // Define table talentospilos_df_alias to be created.
    //         $table = new xmldb_table('talentospilos_df_alias');

    //         // Adding fields to table talentospilos_df_alias.
    //         $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //         $table->add_field('id_pregunta', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
    //         $table->add_field('alias', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);

    //         // Adding keys to table talentospilos_df_alias.
    //         $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    //         // Adding indexes to table talentospilos_df_alias.
    //         $table->add_index('unique_id_pregunta', XMLDB_INDEX_UNIQUE, array('id_pregunta'));
    //         $table->add_index('unique_alias', XMLDB_INDEX_UNIQUE, array('alias'));

    //         // Conditionally launch create table for talentospilos_df_alias.
    //         if (!$dbman->table_exists($table)) {
    //             $dbman->create_table($table);
    //         }

    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade el campo nota_credito en la tabla {talentospilos_res_icetex}
    //     // Versión en la que se incluye: 2018020209479 
    //     // ************************************************************************************************************

    //     // Define field nota_credito to be added to talentospilos_res_icetex.
    //     $table = new xmldb_table('talentospilos_res_icetex');
    //     $field = new xmldb_field('nota_credito', XMLDB_TYPE_TEXT, null, null, null, null, null, 'fecha_resolucion');

    //     // Conditionally launch add field nota_credito.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade el campo nota_credito en la tabla {talentospilos_res_icetex}
    //     // Versión en la que se incluye: 2018020214529
    //     // ************************************************************************************************************

    //     // Define field alias to be added to talentospilos_df_formularios.
    //     $table = new xmldb_table('talentospilos_df_formularios');
    //     $field = new xmldb_field('alias', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'fecha_hora_registro');

    //     // Conditionally launch add field alias.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // Define field estado to be added to talentospilos_df_formularios.
    //     $table = new xmldb_table('talentospilos_df_formularios');
    //     $field = new xmldb_field('estado', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '1', 'alias');

    //     // Conditionally launch add field estado.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // Define field estado to be added to talentospilos_df_form_resp.
    //     $table = new xmldb_table('talentospilos_df_form_resp');
    //     $field = new xmldb_field('estado', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '1', 'fecha_hora_registro');

    //     // Conditionally launch add field estado.
    //     if (!$dbman->field_exists($table, $field)) {
    //         $dbman->add_field($table, $field);
    //     }

    //     // Define field estado to be added to talentospilos_df_form_preg.
    //     $table = new xmldb_table('talentospilos_df_form_preg');
    //     $field = new xmldb_field('estado', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '1', 'fecha_hora_registro');

    //     // Conditionally launch add field talentospilos_df_form_resp.
    //     if (!$dbman->field_exists($table, $field)){
    //         $dbman->add_field($table, $field);
    //     }

    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se elimina la tabla talentospilos_instancia
    //     // Versión en la que se incluye: 2018021417179
    //     // ************************************************************************************************************
    //     // Define table talentospilos_instancia to be dropped.
    //     // $table = new xmldb_table('talentospilos_instancia');

    //     // // Conditionally launch drop table for talentospilos_instancia.
    //     // if ($dbman->table_exists($table)) {
    //     //     $dbman->drop_table($table);
    //     // }

    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se añade la nueva tabla para la configuración de la instancia
    //     // Versión en la que se incluye: 2018021417179
    //     // ************************************************************************************************************
    //     // Define table talentospilos_instancia to be created.
    //     $table = new xmldb_table('talentospilos_instancia');

    //     // Adding fields to table talentospilos_instancia.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_instancia', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('descripcion', XMLDB_TYPE_CHAR, '200', null, null, null, null);

    //     // Adding keys to table talentospilos_instancia.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    //     // Conditionally launch create table for talentospilos_instancia.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }

    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se elimina la tabla talentospilos_monitor_estud
    //     // Versión en la que se incluye: 2018021417179
    //     // ************************************************************************************************************
    //     // Define table talentospilos_monitor_estud to be dropped.
    //     // $table = new xmldb_table('talentospilos_monitor_estud');

    //     // // Conditionally launch drop table for talentospilos_monitor_estud.
    //     // if ($dbman->table_exists($table)) {
    //     //     $dbman->drop_table($table);
    //     // }

    //     // ************************************************************************************************************
    //     // Actualización:
    //     // Se crea la tabla talentospilos_monitor_estud con llave única que incluye id_monitor, id_estudiante, id_instancia, id_semestre
    //     // Versión en la que se incluye: 2018021417179
    //     // ************************************************************************************************************
    //     // Define table talentospilos_monitor_estud to be created.
    //     $table = new xmldb_table('talentospilos_monitor_estud');

    //     // Adding fields to table talentospilos_monitor_estud.
    //     $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    //     $table->add_field('id_monitor', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_estudiante', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_instancia', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    //     $table->add_field('id_semestre', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

    //     // Adding keys to table talentospilos_monitor_estud.
    //     $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    //     $table->add_key('mon_est_pk1', XMLDB_KEY_FOREIGN, array('id_monitor'), 'user', array('id'));
    //     $table->add_key('mon_est_pk2', XMLDB_KEY_FOREIGN, array('id_estudiante'), 'talentospilos_usuario', array('id'));
    //     $table->add_key('mon_est_un', XMLDB_KEY_UNIQUE, array('id_monitor', 'id_estudiante', 'id_instancia', 'id_semestre'));

    //     // Conditionally launch create table for talentospilos_monitor_estud.
    //     if (!$dbman->table_exists($table)) {
    //         $dbman->create_table($table);
    //     }

        // ************************************************************************************************************
        // Actualización:
        // Se configuran los estados de la tabla talentospilos_estado_ases
        // Versión en la que se incluye: 2018021909439
        // ************************************************************************************************************

        // $register = $DB->get_record('talentospilos_estados_ases', array('nombre'=>'ACTIVO/SEGUIMIENTO'));
        // if($register){
        //     $data_object = new stdClass();
        //     $data_object->id = $register->id;
        //     $data_object->nombre = 'seguimiento';
        //     $data_object->descripcion = 'SEGUIMIENTO';

        //     $DB->update_record('talentospilos_estados_ases', $data_object);
        // }

        // $register = $DB->get_record('talentospilos_estados_ases', array('nombre'=>'ACTIVO/SINSEGUIMIENTO'));
        // if($register){
        //     $data_object = new stdClass();
        //     $data_object = new stdClass();
        //     $data_object->id = $register->id;
        //     $data_object->nombre = 'sinseguimiento';
        //     $data_object->descripcion = 'SIN SEGUIMIENTO';

        //     $DB->update_record('talentospilos_estados_ases', $data_object);
        // }

        // $register = $DB->get_record('talentospilos_estados_ases', array('nombre'=>'RETIRADO'));

        // if($register){
        //     $object_to_delete = array();
        //     $object_to_delete['id'] = $register->id;
        //     $DB->delete_records('talentospilos_estados_ases', $object_to_delete);
        // }

        // $register = $DB->get_record('talentospilos_estados_ases', array('nombre'=>'APLAZADO'));
        
        // if($register){
        //     $object_to_delete = array();
        //     $object_to_delete['id'] = $register->id;
        //     $DB->delete_records('talentospilos_estados_ases', $object_to_delete);
        // }

        // $register = $DB->get_record('talentospilos_estados_ases', array('nombre'=>'EGRESADO'));

        // if($register){
        //     $object_to_delete = array();
        //     $object_to_delete['id'] = $register->id;
        //     $DB->delete_records('talentospilos_estados_ases', $object_to_delete);
        // }





        // ************************************************************************************************************
        // Actualización:
        // Se crea tabla para almacenar los posibles estados del estudiante en un programa académico
        // Versión en la que se incluye: PENDIENTE
        // ************************************************************************************************************
        
        // // Define table talentospilos_estad_programa to be created.
        // $table = new xmldb_table('talentospilos_estad_programa');

        // // Adding fields to table talentospilos_estad_programa.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('nombre', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('descripcion', XMLDB_TYPE_CHAR, '500', null, null, null, null);

        // // Adding keys to table talentospilos_estad_programa.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // // Conditionally launch create table for talentospilos_estad_programa.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }

        // $array_status_program = array('ACTIVO', 'APLAZADO', 'EGRESADO', 'INACTIVO', 'NO REGISTRA', 'RETIRADO');
        // $array_description_status_program = array('El estudiante está activo en el programa académico',
        //                                     'El estudiante es egresado del programa académico',
        //                                     'El estudiante se encuentra aplazado en el programa académico',
        //                                     'El estudiante se encuentra inactivo en el programa académico',
        //                                     'No registra estado en el programa académico',
        //                                     'El estudiante se encuentra retirado del programa académico'
        //                                     );
        
        // $record = new stdClass();

        // for($i = 0; $i < count($array_status_program); $i++){
        //     $record->nombre = $array_status_program[$i];
        //     $record->descripcion = $array_description_status_program[$i];
        //     $result = $DB->insert_record('talentospilos_estad_programa', $record, true);
        // }

        // // Define field cantidad_estudiantes to be added to talentospilos_res_icetex.
        // $table = new xmldb_table('talentospilos_res_icetex');
        // $field = new xmldb_field('cantidad_estudiantes', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'fecha_resolucion');

        // // Conditionally launch add field cantidad_estudiantes.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // // Define table talentospilos_res_estudiante to be dropped.
        // $table = new xmldb_table('talentospilos_res_estudiante');

        // // Conditionally launch drop table for talentospilos_res_estudiante.
        // if ($dbman->table_exists($table)) {
        //     $dbman->drop_table($table);
        // }

        // // Define table talentospilos_res_estudiante to be created.
        // $table = new xmldb_table('talentospilos_res_estudiante');

        // // Adding fields to table talentospilos_res_estudiante.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('id_estudiante', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_resolucion', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_semestre', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('monto_estudiante', XMLDB_TYPE_NUMBER, '20, 2', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_estado_icetex', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // // Adding keys to table talentospilos_res_estudiante.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        // $table->add_key('uk_res_est', XMLDB_KEY_UNIQUE, array('id_estudiante', 'id_resolucion'));

        // // Conditionally launch create table for talentospilos_res_estudiante.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }
        
        // /*Eliminación de los campos id_semestre y id_estado_icetex en la tabla
        // talentospilos_res_estudiante */

        // // Define field id_semestre to be dropped from talentospilos_res_estudiante.
        // $table = new xmldb_table('talentospilos_res_estudiante');
        // $field = new xmldb_field('id_semestre');
 
        //  // Conditionally launch drop field id_semestre.
        //  if ($dbman->field_exists($table, $field)) {
        //      $dbman->drop_field($table, $field);
        //  }

        //  // Define field id_estado_icetex to be dropped from talentospilos_res_estudiante.
        // $table = new xmldb_table('talentospilos_res_estudiante');
        // $field = new xmldb_field('id_estado_icetex');

        // // Conditionally launch drop field id_estado_icetex.
        // if ($dbman->field_exists($table, $field)) {
        //     $dbman->drop_field($table, $field);
        // }

        // // Define field id_programa to be added to talentospilos_res_estudiante.
        // $table = new xmldb_table('talentospilos_res_estudiante');
        // $field = new xmldb_field('id_programa', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null);

        // // Conditionally launch add field id_programa.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // // ************************************************************************************************************
        // // Actualización:
        // // Se crea tabla para almacenar temporalmente los cambios de un formulario diligenciado en el tiempo
        // // Versión en la que se incluye: GIT 4.2, Moodle: 2018053015359
        // // ************************************************************************************************************
        // // Define table talentospilos_df_dwarehouse to be created.
        // $table = new xmldb_table('talentospilos_df_dwarehouse');

        // // Adding fields to table talentospilos_df_dwarehouse.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('id_usuario_moodle', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('accion', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        // $table->add_field('id_registro_respuesta_form', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('datos_previos', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('datos_enviados', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('datos_almacenados', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('observaciones', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('cod_retorno', XMLDB_TYPE_INTEGER, '5', null, null, null, null);
        // $table->add_field('msg_retorno', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // $table->add_field('dts_retorno', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // // Adding keys to table talentospilos_df_dwarehouse.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // // Adding indexes to table talentospilos_df_dwarehouse.
        // $table->add_index('indice_df_dw_id_usuario_moodle', XMLDB_INDEX_NOTUNIQUE, array('id_usuario_moodle'));
        // $table->add_index('df_dw_id_registro_respuesta_form', XMLDB_INDEX_NOTUNIQUE, array('id_registro_respuesta_form'));

        // // Conditionally launch create table for talentospilos_df_dwarehouse.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }

        // ************************************************************************************************************
        // Actualización:
        // Se crea tabla para almacenar temporalmente los cambios de un formulario diligenciado en el tiempo
        // Versión en la que se incluye: GIT 4.5, Moodle: 2018060109129
        // ************************************************************************************************************
        // Define field fecha_hora_registro to be added to talentospilos_df_dwarehouse.
        // $table = new xmldb_table('talentospilos_df_dwarehouse');
        // $field = new xmldb_field('fecha_hora_registro', XMLDB_TYPE_DATETIME, null, null, XMLDB_NOTNULL, null, "now()", 'dts_retorno');

        // // Conditionally launch add field fecha_hora_registro.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // ************************************************************************************************************
        // Actualización:
        // Se cambia el tipo de campo de program_status en la tabla talentospilos_user_extended
        // Versión en la que se incluye: GIT XXX, Moodle: 2018061810589
        // ************************************************************************************************************

        // Define field program_status to be dropped from talentospilos_user_extended.
        // $table = new xmldb_table('talentospilos_user_extended');
        // $field = new xmldb_field('program_status');

        // Conditionally launch drop field program_status.
        // if ($dbman->field_exists($table, $field)) {
        //     $dbman->drop_field($table, $field);
        // }

        // Define field program_status to be added to talentospilos_user_extended.
        // $table = new xmldb_table('talentospilos_user_extended');
        // $field = new xmldb_field('program_status', XMLDB_TYPE_INTEGER, '10', null, null, null, '1', 'estado_seguimiento');

        // Conditionally launch add field program_status.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // Define key fk_program_status (foreign) to be added to talentospilos_user_extended.
        // $table = new xmldb_table('talentospilos_user_extended');
        // $key = new xmldb_key('fk_program_status', XMLDB_KEY_FOREIGN, array('program_status'), 'talentospilos_estad_programa', array('id'));

        // Launch add key fk_program_status.
        // $dbman->add_key($table, $key);


        // ************************************************************************************************************
        // Actualización:
        // Se añade campo idnumber a la tabla talentospilos_instancia
        // Versión en la que se incluye: GIT XXX, Moodle: 2018062515379
        // ************************************************************************************************************

        // Define field id_number to be added to talentospilos_instancia.
        // $table = new xmldb_table('talentospilos_instancia');
        // $field = new xmldb_field('id_number', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'changeme', 'descripcion');

        // Conditionally launch add field id_number.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // $sql_query = "SELECT id FROM {talentospilos_instancia}";
        // $result_instances = $DB->get_records_sql($sql_query);
        // $counter = 0;

        // foreach($result_instances as $instance){
        //     $counter++;
        //     $record = new stdClass();
        //     $record->id = $instance->id;
        //     $record->id_number = 'changeme'+$counter;
        //     $update_query = $DB->update_record('talentospilos_instancia', $record);
        // }

        // Define key instance_uk_1 (unique) to be added to talentospilos_instancia.
        // $table = new xmldb_table('talentospilos_instancia');
        // $key = new xmldb_key('instance_uk_1', XMLDB_KEY_UNIQUE, array('id_number'));

        // Launch add key instance_uk_1.
        // $dbman->add_key($table, $key);


        // ************************************************************************************************************
        // Actualización:
        // Se añade campo navegador y usuario a la tabla talentospilos_df_dwarehouse
        // Versión en la que se incluye: GIT XXX, Moodle: 2018062515379
        // ************************************************************************************************************

        // Define field navegador to be added to talentospilos_df_dwarehouse.
        // $table = new xmldb_table('talentospilos_df_dwarehouse');
        // $field = new xmldb_field('navegador', XMLDB_TYPE_TEXT, null, null, null, null, null, 'dts_retorno');

        // // Conditionally launch add field navegador.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // // Define field url_request to be added to talentospilos_df_dwarehouse.
        // $table = new xmldb_table('talentospilos_df_dwarehouse');
        // $field = new xmldb_field('url_request', XMLDB_TYPE_TEXT, null, null, null, null, null, 'navegador');

        // // Conditionally launch add field url_request.
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }
        // ************************************************************************************************************
        


        // ************************************************************************************************************
        // Actualización:
        // Se crea tabla que almacena los tipos de documentos posibles 
        // Versión en la que se incluye: GIT XXX, Moodle: 2018080616479
        // ************************************************************************************************************
        // Define table talentospilos_tipo_documento to be created.
        // $table = new xmldb_table('talentospilos_tipo_documento');

        // // Adding fields to table talentospilos_tipo_documento.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('nombre', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        // $table->add_field('descripcion', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);

        // // Adding keys to table talentospilos_tipo_documento.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // // Conditionally launch create table for talentospilos_tipo_documento.
        // if (!$dbman->table_exists($table)) {
        //     $dbman->create_table($table);
        // }

        // // ************************************************************************************************************
        // // Actualización:
        // // Se cambia el tipo de dato para el campo tipo_doc_ini en la tabla talentospilos_usuario
        // // Versión en la que se incluye: GIT XXX, Moodle: 2018080815349
        // // ************************************************************************************************************        
        // // Changing type of field tipo_doc_ini on table talentospilos_usuario to int.
        // $table = new xmldb_table('talentospilos_usuario');
        // $field = new xmldb_field('tipo_doc_ini', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // // Launch change of type for field tipo_doc_ini.
        // $dbman->change_field_type($table, $field);

        // // ************************************************************************************************************
        // // Actualización:
        // // Se cambia el tipo de dato para el campo tipo_doc en la tabla talentospilos_usuario
        // // Versión en la que se incluye: GIT XXX, Moodle: 2018080815349
        // // ************************************************************************************************************        
        // // Changing type of field tipo_doc_ini on table talentospilos_usuario to int.
        // $table = new xmldb_table('talentospilos_usuario');
        // $field = new xmldb_field('tipo_doc', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // // Launch change of type for field tipo_doc_ini.
        // $dbman->change_field_type($table, $field);

        // // ************************************************************************************************************
        // // Actualización:
        // // Se crean las llaves foráneas de los campos tipo_doc y tipo_doc_ini en la tabla talentospilos_usuario,
        // // Las cuales apuntan a la tabla talentospilos_tipo_documento
        // // Versión en la que se incluye: GIT XXX, Moodle: 2018080815349
        // // ************************************************************************************************************    
        // // Define key doc_ini_type_fk (foreign) to be added to talentospilos_usuario.
        // $table = new xmldb_table('talentospilos_usuario');
        // $key = new xmldb_key('doc_ini_type_fk', XMLDB_KEY_FOREIGN, array('tipo_doc_ini'), 'talentospilos_tipo_documento', array('id'));

        // // Launch add key doc_ini_type_fk.
        // $dbman->add_key($table, $key);

        // // Define key tipo_doc_fk (foreign) to be added to talentospilos_usuario.
        // $table = new xmldb_table('talentospilos_usuario');
        // $key = new xmldb_key('tipo_doc_fk', XMLDB_KEY_FOREIGN, array('tipo_doc'), 'talentospilos_tipo_documento', array('id'));
 
        // // Launch add key tipo_doc_fk.
        // $dbman->add_key($table, $key);

        $table = new xmldb_table('talentospilos_alertas_academ');
        // Conditionally launch drop table for talentospilos_alertas_academ.
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }
    // Adding fields to table talentospilos_alertas_academ.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('id_estudiante', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
    $table->add_field('id_item', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
    $table->add_field('nota', XMLDB_TYPE_FLOAT, '20', null, null, null, null);
    $table->add_field('id_user_registra', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
    $table->add_field('fecha', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
     // Adding keys to table talentospilos_alertas_academ.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('unique_key', XMLDB_KEY_UNIQUE, array('id_item', 'id_estudiante'));
     // Conditionally launch create table for talentospilos_alertas_academ.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }



     // ************************************************************************************************************
    // Actualización:
    // Se crea tabla de el super super usuario de el bloque 
    // Versión en la que se incluye: GIT XXX, Moodle: 2018083109510


        // Define table talentospilos_superadmin to be created.
        $table = new xmldb_table('talentospilos_superadmin');

        // Adding fields to table talentospilos_superadmin.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('id_usuario', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('fecha_registro', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('fecha_ultima_modificacion', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('estado', XMLDB_TYPE_INTEGER, '2', null, null, null, '1');

        // Adding keys to table talentospilos_superadmin.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('fk_id_usuario', XMLDB_KEY_FOREIGN, array('id_usuario'), 'user', array('id'));

        // Conditionally launch create table for talentospilos_superadmin.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


     // ************************************************************************************************************
    // Actualización:
    // Se crea tabla de logs de alertas academicas
    // Versión en la que se incluye: GIT XXX, Moodle: 2018080609050

        upgrade_block_savepoint(true, 2018083110300 , 'ases');
    
        return $result;

    }
}
?>
