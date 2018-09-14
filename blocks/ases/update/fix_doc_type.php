<?php

require_once(dirname(__FILE__). '/../../../config.php');

/* Inserta y actualiza los campos realcioandos con el tipo de documento
      
*/

global $DB;

$record = new stdClass();
$result_cc = 0;
$result_ti = 0;
$result_cr = 0;

$record->nombre = "TI";
$record->descripcion = "Tarjeta de identidad";
$result_ti = $DB->insert_record("talentospilos_tipo_documento", $record, true);

if($result_ti){
    print_r("TI ---> ok");
}else{
    print_r("TI ---> failed");
}

$record->nombre = "CC";
$record->descripcion = "Cedula de ciudadanía";
$result_cc = $DB->insert_record("talentospilos_tipo_documento", $record, true);

if($result_cc){
    print_r("CC ---> ok");
}else{
    print_r("CC ---> failed");
}

$record->nombre = "CR";
$record->descripcion = "Contraseña";
$result_cr = $DB->insert_record("talentospilos_tipo_documento",  $record, true);

if($result_cr){
    print_r("CR ---> ok");
}else{
    print_r("CR ---> failed");
}

$record->nombre = "NR";
$record->descripcion = "No registra";
$result_nr = $DB->insert_record("talentospilos_tipo_documento",  $record, true);

if($result_nr){
    print_r("NR ---> ok<br>");
}else{
    print_r("NR ---> failed<br>");
}

if($result_cr && $result_cc && $result_ti){

    try{
        $sql_query = "SELECT id, tipo_doc, tipo_doc_ini FROM {talentospilos_usuario}";
        $user_array = $DB->get_records_sql($sql_query);

        foreach($user_array as $user){

            $user->tipo_doc = strtoupper($user->tipo_doc);
            $user->tipo_doc_ini = strtoupper($user->tipo_doc_ini);

            pg_query("BEGIN") or die("Could not start transaction\n");
            
            $record = new stdClass();

            if($user->tipo_doc ==  "" OR $user->tipo_doc ==  "NIP"){

                $sql_query = "SELECT id FROM {talentospilos_tipo_documento} WHERE nombre = 'NR'";
                $id_doc_type = $DB->get_record_sql($sql_query);
                print_r($user);
                print_r("<br>");

                $record->id = $user->id;
                $record->tipo_doc = $id_doc_type->id;

                $DB->update_record('talentospilos_usuario', $record);

            }else{
                $replace_dot = str_replace(".", "", $user->tipo_doc);
                $replace_doc_type = str_replace(" ", "", $replace_dot);

                $sql_query = "SELECT id FROM {talentospilos_tipo_documento} WHERE nombre = '$replace_doc_type'";
                print_r($user);
                print_r("<br>");
                $id_doc_type = $DB->get_record_sql($sql_query);

                $record->id = $user->id;
                $record->tipo_doc = $id_doc_type->id;

                $DB->update_record('talentospilos_usuario', $record);
                
            }

            if($user->tipo_doc_ini ==  "" OR $user->tipo_doc ==  "NIP"){

                $sql_query = "SELECT id FROM {talentospilos_tipo_documento} WHERE nombre = 'NR'";
                $id_doc_type = $DB->get_record_sql($sql_query);
                print_r($user);
                print_r("<br>");

                $record->id = $user->id;
                $record->tipo_doc_ini = $id_doc_type->id;

                $DB->update_record('talentospilos_usuario', $record);

            }else{
                $replace_dot = str_replace(".", "", $user->tipo_doc_ini);
                $replace_doc_type = str_replace(" ", "", $replace_dot);

                $sql_query = "SELECT id FROM {talentospilos_tipo_documento} WHERE nombre = '$replace_doc_type'";
                $id_doc_type = $DB->get_record_sql($sql_query);
                print_r($user);
                print_r("<br>");

                $record->id = $user->id;
                $record->tipo_doc_ini = $id_doc_type->id;

                $DB->update_record('talentospilos_usuario', $record);
            }

            pg_query("COMMIT") or die("Transaction commit failed\n");
       }
    }catch(Exception $e){
        $errorSqlServer = pg_last_error();
        print_r($errorSqlServer);
        print_r("<br>");
        print_r("Consulta SQL ".$sql_query);
        print_r("Ejecución SQL ".$DB->get_record_sql($sql_query));
        pg_query("ROLLBACK");
    }
}else{
    print_r("Transacción abortada<br>Error al insertar registros en la tabla talentospilos_tipo_doc");
}

    





