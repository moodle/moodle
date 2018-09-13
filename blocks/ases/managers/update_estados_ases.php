<?php
  require_once(dirname(__FILE__). '/../../../config.php');
    
  global $DB;
  $students = $DB->get_records_sql("SELECT id FROM {talentospilos_usuario} WHERE id NOT IN (SELECT id_estudiante FROM {talentospilos_est_estadoases})");
  $state = $DB->get_record_sql("SELECT id FROM {talentospilos_estados_ases} WHERE nombre = 'seguimiento'")->id;
  foreach($students as $id){
      $record = new stdClass;
      $record->id_estudiante = $id->id;
      $record->id_estado_ases = $state;
      $record->fecha = 1508795307; 
      echo $DB->insert_record('talentospilos_est_estadoases',$record,false);
  }

  $students = $DB->get_records_sql("SELECT id FROM {talentospilos_usuario} WHERE id NOT IN (SELECT id_estudiante FROM {talentospilos_est_est_icetex})");
  $state = $DB->get_record_sql("SELECT id FROM {talentospilos_estados_icetex} WHERE nombre = 'ACTIVO'")->id;
  
  foreach($students as $id){
    $record = new stdClass;
    $record->id_estudiante = $id->id;
    $record->id_estado_icetex = $state;
    $record->fecha = 1508795307; 
    echo $DB->insert_record('talentospilos_est_est_icetex',$record,false);
}

