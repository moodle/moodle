<?php

require_once(dirname(__FILE__). '/../../../config.php');

/* Modifica registros en la tabla {talentospilos_user_extended}
   cuando :
   El id_ases_user este repetido y en ambos tracking_status este en 1
*/

global $DB;
$continuar =true;
$msg="";

//Obtiene los registros duplicados cuando el id_ases_user este repetido.

$query = "With duplicados as (
    Select *, Count(*) OVER (PARTITION BY id_ases_user,tracking_status ) as total
    From {talentospilos_user_extended})
	Select * From duplicados where Total > 1 order by id_ases_user asc";

$registros_duplicados = $DB->get_records_sql($query);

try{
foreach ($registros_duplicados as $registro) {

	if($registro->total==2){
		$registro_1 = $registro;
		$registro_2 = current($registros_duplicados);
    		 if($registro_1->id_academic_program==1 && $registro_2->id_academic_program==1){
  		  		 continue ;
     		}else if($registro_1->id_academic_program==1 || $registro_2->id_academic_program==1){
     			  if($registro_1->id_academic_program==1){
     				$id_registro = $registro_1->id;
     				$record = new stdClass;
    				$record->id = $id_registro;
    				$record->tracking_status =0;
  					$DB->update_record('talentospilos_user_extended', $record);
     			 }else{
     				$id_registro = $registro_2->id;
     				$record = new stdClass;
    				$record->id = $id_registro;
    				$record->tracking_status =0;
  					$DB->update_record('talentospilos_user_extended', $record);
     			
	         }
         }
      }
    }
}catch(Exception $ex){
 $msg.="Se presentó un error : ".$ex;
 $continuar=false;
}

 if($continuar==true){
  $msg.="Éxito";
 }



echo $msg;





function get_username(){


$query = "With duplicados as (
    Select *, Count(*) OVER (PARTITION BY id_ases_user,tracking_status ) as total
    From {talentospilos_user_extended})
  Select duplicados.id,duplicados.id_moodle_user, duplicados.id_ases_user, duplicados.id_academic_program
  , duplicados.tracking_status,u.username From duplicados INNER JOIN {user} u ON duplicados.id_moodle_user = u.id
  where Total > 3 order by id_ases_user asc";

$registros_duplicados = $DB->get_records_sql($query);
  return $registros_duplicados;
}
