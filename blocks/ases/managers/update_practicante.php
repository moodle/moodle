<?php
require_once(dirname(__FILE__). '/../../../config.php');
require('query.php');
    global $DB;
    
    $sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol = 'practicante_ps'";
    $id_rol_practicante = $DB->get_record_sql($sql_query)->id;
    
    $sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol = 'geografo'";
    $id_geografo = $DB->get_record_sql($sql_query)->id;
    
    $sql_query = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol = 'sistemas'";
    $id_sistemas = $DB->get_record_sql($sql_query)->id;
    
    $sql_query = "SELECT id FROM {talentospilos_funcionalidad} WHERE nombre_func = 'f_general'";
    $id_ficha_general = $DB->get_record_sql($sql_query)->id;
    
    $sql_query = "SELECT id FROM {talentospilos_funcionalidad} WHERE nombre_func = 'f_geografia'";
    $id_ficha_geografia = $DB->get_record_sql($sql_query)->id;
    
    $sql_query = "SELECT id FROM {talentospilos_funcionalidad} WHERE nombre_func = 'f_socioeducativa_pro'";
    $id_ficha_socioeducativa_pro = $DB->get_record_sql($sql_query)->id;
    
    $record = new stdClass;
    
    $record->id_rol = $id_rol_practicante;
    $record->id_permiso = 2;
    $record->id_funcionalidad = $id_ficha_general;
    $DB->insert_record('talentospilos_permisos_rol', $record, false);

    $record->id_rol = $id_rol_practicante;
    $record->id_permiso = 2;
    $record->id_funcionalidad = $id_ficha_socioeducativa_pro;
    $DB->insert_record('talentospilos_permisos_rol', $record, false);
    
    $record->id_rol = $id_rol_practicante;
    $record->id_permiso = 1;
    $record->id_funcionalidad = $id_ficha_socioeducativa_pro;
    $DB->insert_record('talentospilos_permisos_rol', $record, false);