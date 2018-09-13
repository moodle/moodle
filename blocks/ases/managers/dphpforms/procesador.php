<?php 

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Dynamic PHP Forms
 *
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs

    require_once(dirname(__FILE__). '/../../../../config.php');
    require_once(dirname(__FILE__). '/dphpforms_get_record.php');
    require_once( $CFG->libdir . '/adminlib.php');

    global $DB;
    global $USER;

    $RECORD_ID = null;

    if(isset($_POST['id_registro'])){
        $RECORD_ID = $_POST['id_registro'];
    }
    
    $form = array(
        'id' => $_POST['id']
        //'id_monitor' => $_POST['id_monitor'],
        //'id_estudiante' => $_POST['id_estudiante']
    );

    $respuestas = array();

    foreach ($_POST as $key => $value) {
        if(is_numeric($key)){
            $elemento = $value;
            if(is_array($value)){
                $elemento = json_encode($elemento);
            }

            $respuesta = array(
                'id' => (string) $key,
                'valor' => (string) $elemento
            );
            array_push($respuestas, $respuesta);
        }
        next($_POST);
    }
    

    $full_form = array(
        'formulario' => $form,
        'respuestas' => $respuestas
    );

    $form_JSON = json_encode($full_form);
    

/*$formularioDiligenciado = '

{
    "formulario":{
        "id":43,
        "id_monitor":5245,
        "id_estudiante":6548
    },
    "respuestas":[
        {
            "id":139,
            "valor":"xyz"
        }
    ]
}

';*/


if($RECORD_ID){

    $previous_data = dphpforms_get_record($RECORD_ID, null);
    $current_data = $form_JSON;
    $retorno = dphpforms_update_respuesta($form_JSON, $RECORD_ID);
    $stored_data = dphpforms_get_record($RECORD_ID, null);

    $to_warehouse = new stdClass();
    $to_warehouse->id_usuario_moodle = $USER->id;
    $to_warehouse->accion = "UPDATE";
    $to_warehouse->id_registro_respuesta_form = $RECORD_ID;
    $to_warehouse->datos_previos = $previous_data;
    $to_warehouse->datos_enviados = $current_data;
    $to_warehouse->datos_almacenados = $stored_data;
    $to_warehouse->observaciones = "";
    $to_warehouse->cod_retorno = json_decode($retorno)->status;
    $to_warehouse->msg_retorno = json_decode($retorno)->message;
    $to_warehouse->dts_retorno = json_encode(json_decode($retorno)->data);
    $to_warehouse->navegador = $_SERVER['HTTP_USER_AGENT'];
    $to_warehouse->url_request = $_SERVER['HTTP_REFERER'];

    $DB->insert_record('talentospilos_df_dwarehouse', $to_warehouse, $returnid=false, $bulk=false);

}else{
    $previous_data = "";
    $current_data = $form_JSON;
    $retorno = dphpforms_new_store_respuesta($form_JSON);
    if( json_decode($retorno)->status == '0' ){

        $stored_data = dphpforms_get_record(json_decode($retorno)->data, null);
        $to_warehouse = new stdClass();
        $to_warehouse->id_usuario_moodle = $USER->id;
        $to_warehouse->accion = "INSERT";
        $to_warehouse->id_registro_respuesta_form = json_decode($retorno)->data;
        $to_warehouse->datos_previos = $previous_data;
        $to_warehouse->datos_enviados = $current_data;
        $to_warehouse->datos_almacenados = $stored_data;
        $to_warehouse->observaciones = "";
        $to_warehouse->cod_retorno = json_decode($retorno)->status;
        $to_warehouse->msg_retorno = json_decode($retorno)->message;
        $to_warehouse->dts_retorno = json_encode(json_decode($retorno)->data);
        $to_warehouse->navegador = $_SERVER['HTTP_USER_AGENT'];
        $to_warehouse->url_request = $_SERVER['HTTP_REFERER'];

        $DB->insert_record('talentospilos_df_dwarehouse', $to_warehouse, $returnid=false, $bulk=false);

    };
   
};

function dphpforms_update_respuesta($completed_form, $RECORD_ID){

    $processable = true;

    $obj_form_completed = json_decode($completed_form);
    $processable = dphpforms_form_exist($obj_form_completed->{'formulario'}->{'id'});
    
    foreach($obj_form_completed->{'respuestas'} as &$respuesta){
        if(dphpforms_pregunta_exist_into_form($respuesta->{'id'})){
            if($processable == false){
                break;
            }
        }else{
            $processable = false;
        };
    }

    $reglas = null;
    $reglas = dphpforms_get_form_reglas($obj_form_completed->{'formulario'}->{'id'});
    $registered_respuestas = null;
    $registered_respuestas = dphpforms_get_respuestas_form_completed($RECORD_ID);

    $updated_respuestas = array();
    //print_r($registered_respuestas);
    if($processable){
        //echo "\n¿Procesable?: Sí.\n";
        $different_flag = false;
        
        foreach ($registered_respuestas as &$respuesta) {
            /* 
                Asumimos que hay respuestas sin valores previos ni resgistros en 
                la base de datos
             */
            //$form_structure_was_changed = true;

            foreach ($obj_form_completed->{'respuestas'} as &$respuestaActualizada) {

                if( $respuesta['id'] == $respuestaActualizada->id ){
                    /* 
                        Si encontramos el registro previo a la respuesta, asumimos que no
                        existen cambios en la estructura del formulario.
                    */
                    //$form_structure_was_changed = false;
                    if( $respuesta['valor'] !== $respuestaActualizada->valor ){
                        //echo ' SE VA A ACTUALIZAR: ' . $respuesta['id'] ;
                        array_push( $updated_respuestas, array( 'id' => $respuesta['id'], 'valor' => $respuestaActualizada->valor ) );
                        $different_flag = true;
                        
                    }else{
                        array_push( $updated_respuestas, array('id' => $respuesta['id'], 'valor' => $respuesta['valor']) );
                    }

                    break;

                }/*else{

                    $exist_flag = false;
                    foreach ($updated_respuestas as &$ra) {
                        if($ra['id'] == $respuesta['id']){
                            $exist_flag = true;
                            break;
                        }
                    }

                    if(!$exist_flag){
                        array_push($updated_respuestas, array('id' => $respuesta['id'], 'valor' => $respuesta['valor']) );
                    }
                    
                }*/
            }

            /* Si no se encuentra una respuesta previa en las respuestas registradas */
            /*if( $form_structure_was_changed ){
                array_push( $updated_respuestas, array('id' => $respuesta['id'], 'valor' => $respuesta['valor']) );
                $different_flag = true;
            }*/
        }

        // Inicia el proceso de trabajo sobre las respuestas no registradas
        if( count( $registered_respuestas ) != count( $obj_form_completed->{'respuestas'} )  ){
            //$new_respuestas = array();
            foreach ($obj_form_completed->{'respuestas'} as &$respuestaActualizada) {
                $is_not_matched = true;
                foreach ($registered_respuestas as &$respuesta) {
                    if( $respuesta['id'] == $respuestaActualizada->id ){
                        $is_not_matched = false;
                        break;
                    }
                }
                if( $is_not_matched ){
                    
                    $new_respuesta = new stdClass();
                    $new_respuesta->id =  $respuestaActualizada->id;
                    $new_respuesta->valor = $respuestaActualizada->valor;

                    $id_respuesta_db = dphpforms_store_respuesta( $new_respuesta );
                    dphpforms_store_form_soluciones($RECORD_ID, $id_respuesta_db);

                    array_push( $updated_respuestas, array( 'id' => $respuestaActualizada->id, 'valor' => $new_respuesta->valor ) );
                    
                }
            }
            $different_flag = true;
        }

        //print_r( $updated_respuestas );

        $form_expected_respuestas =  dphpforms_get_expected_respuestas($obj_form_completed->{'formulario'}->{'id'});
    
        $all_respuestas_updated = array();
        for($z = 0; $z < count($form_expected_respuestas); $z++){
            //print_r( (array) $obj_form_completed->{'respuestas'} ) ;
            $flag = true;
            $array_form_respuestas = $updated_respuestas;

            //print_r( $array_form_respuestas );
            
            for($m = 0; $m < count($array_form_respuestas); $m++ ){
                if(!is_null($form_expected_respuestas[$z]) && !is_null($array_form_respuestas[$m])){
                    
                    if((int) $array_form_respuestas[$m]['id'] === (int) $form_expected_respuestas[$z]->mod_id_formulario_pregunta){
                        $flag = false;
                        array_push(
                            $all_respuestas_updated, 
                            $array_form_respuestas[$m]
                        );
                        $form_expected_respuestas[$z] = null;
                        break;
                    }
                }
            }
            if($flag){
                $new_respuesta = new stdClass();
                $new_respuesta->id = $form_expected_respuestas[$z]->mod_id_formulario_pregunta;
                if($form_expected_respuestas[$z]->campo == 'CHECKBOX'){
                    //Pendiente de comentar el por qué de estos valores.
                    if(count(json_decode($form_expected_respuestas[$z]->opciones_campo)) > 1){
                        $new_respuesta->valor = '';
                    }else{
                        $new_respuesta->valor = '-1';
                    }
                }elseif($form_expected_respuestas[$z]->campo == 'RADIOBUTTON'){
                    $new_respuesta->valor = '-#$%-';
                }else{
                    $new_respuesta->valor = '';
                }
                array_push(
                    $all_respuestas_updated, 
                    $new_respuesta
                );
            }
        }

        $updated_respuestas =  $all_respuestas_updated;


        /*if( count( $registered_respuestas ) != count( $obj_form_completed->{'respuestas'} )  ){
            $different_flag = true;
        }*/
        
        if($different_flag){
            
            //La última afectación es si las reglas son válidas
            $validator_response = dphpforms_reglas_validator(json_decode(json_encode($updated_respuestas)), $reglas);

            $processable = $validator_response['status'];
            $Unfulfilled_rules = $validator_response['unfulfilled_ruler'];

            /*print_r($updated_respuestas);

            if($processable){
                echo 'PROCESABLE';
            }else{
                echo 'NO PROCESABLE';
            }

            die();*/
           
            if($processable){

                //echo 'REGLAS OK, PENDIENTE';
                $updated_respuestas = json_decode(json_encode($updated_respuestas));
                //print_r($updated_respuestas);
                //die();
                foreach($updated_respuestas as &$r){

                    $updated = dphpforms_update_completed_form($RECORD_ID, $r->id, $r->valor);
                    if(!$updated){
                        $retorno = json_encode(
                            array(
                                'status' => '-1',
                                'message' => 'Error updating',
                                'data' => ''
                            )
                        );
                        echo $retorno;
                        return $retorno;
                    }
                }
                $retorno = json_encode(
                    array(
                        'status' => '0',
                        'message' => 'Updated',
                        'data' => ''
                    )
                );
                echo $retorno;
                return $retorno;
            }else{

                $rule = '';
                if( $Unfulfilled_rules != '' ){
                    $rule = dphpforms_get_regla( $Unfulfilled_rules );
                }

                $retorno = json_encode(
                    array(
                        'status' => '-2',
                        'message' => 'Unfulfilled rules',
                        'data' => $rule
                    )
                );
                echo $retorno;
                return $retorno;
            }

        }else{
            $retorno = json_encode(
                array(
                    'status' => '-2',
                    'message' => 'Without changes',
                    'data' => ''
                )
            );
            echo $retorno;
            return $retorno;
        }
    }
    
}

function dphpforms_new_store_respuesta($completed_form){

    $processable = true;

    $obj_form_completed = json_decode($completed_form);
    $processable = dphpforms_form_exist($obj_form_completed->{'formulario'}->{'id'});
    foreach($obj_form_completed->{'respuestas'} as &$respuesta){
        if(dphpforms_pregunta_exist_into_form($respuesta->{'id'})){
            if($processable == false){
                break;
            }
        }else{
            $processable = false;
        };
    }

    

    $reglas = dphpforms_get_form_reglas($obj_form_completed->{'formulario'}->{'id'});

    $form_expected_respuestas =  dphpforms_get_expected_respuestas($obj_form_completed->{'formulario'}->{'id'});
    
    $all_respuestas = array();
    for($z = 0; $z < count($form_expected_respuestas); $z++){
        //print_r( (array) $obj_form_completed->{'respuestas'} ) ;
        $flag = true;
        $array_form_respuestas = (array) $obj_form_completed->{'respuestas'};
        $array_form_respuestas = array_values($array_form_respuestas);
        
        for($m = 0; $m < count($array_form_respuestas); $m++ ){

            if(!is_null($form_expected_respuestas[$z]) && !is_null($array_form_respuestas[$m])){
                
                if((int) $array_form_respuestas[$m]->id === (int) $form_expected_respuestas[$z]->mod_id_formulario_pregunta){
                    //echo '==>'. $array_form_respuestas[$m]->id . ' ::: '.$form_expected_respuestas[$z]->mod_id_formulario_pregunta . "\n";
                    $flag = false;
                    array_push(
                        $all_respuestas, 
                        $array_form_respuestas[$m]
                    );
                    $form_expected_respuestas[$z] = null;
                    break;
                }
            }
        }
        if($flag){
            //echo $form_expected_respuestas[$z]->mod_id_formulario_pregunta . "<==\n";
            $new_respuesta = new stdClass();
            $new_respuesta->id = $form_expected_respuestas[$z]->mod_id_formulario_pregunta;
            if($form_expected_respuestas[$z]->campo == 'CHECKBOX'){
                //Pendiente de comentar el por qué de estos valores.
                if(count(json_decode($form_expected_respuestas[$z]->opciones_campo)) > 1){
                    $new_respuesta->valor = '';
                }else{
                    $new_respuesta->valor = '-1';
                }
            }elseif($form_expected_respuestas[$z]->campo == 'RADIOBUTTON'){
                $new_respuesta->valor = '-#$%-';
            }else{
                $new_respuesta->valor = '';
            }

            array_push(
                $all_respuestas, 
                $new_respuesta
            );
        }
    }

    //print_r($all_respuestas);
    //die();

    $validator_response = dphpforms_reglas_validator( json_decode( json_encode($all_respuestas) ), $reglas );
    $processable = $validator_response['status'];
    $Unfulfilled_rules = $validator_response['unfulfilled_ruler'];

    if($processable){
        //echo "\n¿Procesable?: Sí.\n";
        //echo "Inicio de registro en la base de datos\n";
        
        $resultadoRegistro = array();

        $ID_FORMULARIO_RESPUESTA = null;
        $ID_FORMULARIO_RESPUESTA = dphpforms_store_form_respuesta($obj_form_completed->{'formulario'});

        array_push($resultadoRegistro, array('ID_Formulario_respuesta' => $ID_FORMULARIO_RESPUESTA));

        // Registro de respuestas
        $respuestas_identifiers = array();
        $form_expected_respuestas =  dphpforms_get_expected_respuestas($obj_form_completed->{'formulario'}->{'id'});
        for($z = 0; $z < count($form_expected_respuestas); $z++){
            $flag = true;
            foreach ($obj_form_completed->{'respuestas'} as &$respuesta){
                if($respuesta->id == $form_expected_respuestas[$z]->mod_id_formulario_pregunta){
                    $flag = false;
                    break;
                }
            }
            if($flag){

                $new_respuesta = new stdClass();
                $new_respuesta->id = $form_expected_respuestas[$z]->mod_id_formulario_pregunta;
                if($form_expected_respuestas[$z]->campo == 'CHECKBOX'){
                    //Pendiente de comentar el por qué de estos valores.
                    if(count(json_decode($form_expected_respuestas[$z]->opciones_campo)) > 1){
                        $new_respuesta->valor = '';
                    }else{
                        $new_respuesta->valor = '-1';
                    }
                }elseif($form_expected_respuestas[$z]->campo == 'RADIOBUTTON'){
                    $new_respuesta->valor = '-#$%-';
                }else{
                    $new_respuesta->valor = "";
                }

                array_push(
                    $respuestas_identifiers, 
                    array( 
                        'idRespuestaDB' => dphpforms_store_respuesta($new_respuesta)
                    )
                );

            }

        }

        foreach ($obj_form_completed->{'respuestas'} as &$respuesta) {
            array_push(
                $respuestas_identifiers, 
                array( 
                    'idRespuestaDB' => dphpforms_store_respuesta($respuesta)
                )
            );
        }

        array_push($resultadoRegistro, array('ids_respuestas' => $respuestas_identifiers));

        $form_soluciones_identifiers = array();
        foreach ($respuestas_identifiers as &$idsRespuesta) {
            array_push($form_soluciones_identifiers,
                array(
                    'idFormularioSolucionDB' => dphpforms_store_form_soluciones($ID_FORMULARIO_RESPUESTA, $idsRespuesta['idRespuestaDB'])
                )
            );
        }

        array_push($resultadoRegistro, array('ids_respuestas' => $form_soluciones_identifiers));
        //echo "\nResultado del registro:\n";
        //print_r($resultadoRegistro);
        $retorno = json_encode(
            array(
                'status' => '0',
                'message' => 'Stored',
                'data' => $ID_FORMULARIO_RESPUESTA
            )
        );
        echo $retorno;
        return $retorno;
        

    }else{

        $rule = '';
        if( $Unfulfilled_rules != '' ){
            $rule = dphpforms_get_regla( $Unfulfilled_rules );
        }

        $retorno = json_encode(
            array(
                'status' => '-2',
                'message' => 'Unfulfilled rules',
                'data' => $rule
            )
        );
        echo $retorno;
        return $retorno;
    }
    
}

// $pregunta_identifier Es el identificador que relaciona una pregunta con un formulario
function dphpforms_update_completed_form( $form_identifier_respuesta, $pregunta_identifier, $new_value ){
    
    global $DB;
    
       $sql = "
       
       SELECT * 
       FROM {talentospilos_df_respuestas} AS R 
       INNER JOIN 
           (
               SELECT * 
               FROM {talentospilos_df_form_resp} AS FR 
               INNER JOIN {talentospilos_df_form_solu} AS FS 
               ON FR.id = FS.id_formulario_respuestas 
               WHERE FR.id = '".$form_identifier_respuesta."'
           ) AS FRS 
       ON FRS.id_respuesta = R.id WHERE R.id_pregunta = '".$pregunta_identifier."';
       
       ";

       $result = $DB->get_record_sql($sql);

       //Registrar aquí

       if( $result ){
        $respuesta_identifier = $result->id_respuesta;

        $obj_updated_respuesta = new stdClass();
        $obj_updated_respuesta->id = $respuesta_identifier;
        $obj_updated_respuesta->respuesta = $new_value;
        $obj_updated_respuesta->fecha_hora_registro = "now()";

        //echo json_encode( $obj_updated_respuesta );

        $DB->update_record('talentospilos_df_respuestas', $obj_updated_respuesta, $bulk=false);
       };

       /*}else{
        $obj_updated_respuesta = new stdClass();
        $obj_updated_respuesta->respuesta = $new_value;
        $obj_updated_respuesta->id_pregunta = $pregunta_identifier;
        $obj_updated_respuesta->fecha_hora_registro = "now()";

        echo json_encode( $obj_updated_respuesta );*/

        //$DB->insert_record('talentospilos_df_respuestas', $obj_updated_respuesta, $returnid=true, $bulk=false);

       //}

       return true;
}

function dphpforms_store_form_soluciones($form_id, $respuesta_identifier){

    global $DB;

       $obj_form_soluciones = new stdClass();
       $obj_form_soluciones->id_formulario_respuestas = $form_id;
       $obj_form_soluciones->id_respuesta = $respuesta_identifier;
   
       $form_solucines_identifier = $DB->insert_record('talentospilos_df_form_solu', $obj_form_soluciones, $returnid=false, $bulk=false);

       return $form_solucines_identifier;
}

function dphpforms_store_form_respuesta($form_detail){
    
    global $DB;

    $obj_form_respuesta = new stdClass();
    $obj_form_respuesta->id_formulario = $form_detail->{'id'};
    $obj_form_respuesta->id_monitor = '-1';
    $obj_form_respuesta->id_estudiante = '-1';

    $form_respuesta_identifier = $DB->insert_record('talentospilos_df_form_resp', $obj_form_respuesta, $returnid=true, $bulk=false);

    return $form_respuesta_identifier;
}

function dphpforms_store_respuesta($respuesta){
    
    global $DB;

    if(is_array($respuesta->{'valor'})){
        $respuesta->{'valor'} = json_encode($respuesta->{'valor'});
    }

    $obj_respuesta = new stdClass();
    $obj_respuesta->id_pregunta = $respuesta->{'id'};
    $obj_respuesta->respuesta = $respuesta->{'valor'};

    $respuesta_identifier = $DB->insert_record('talentospilos_df_respuestas', $obj_respuesta, $returnid=true, $bulk=false);
    return $respuesta_identifier;
}

function dphpforms_form_exist($id){

    global $DB;

    $sql = "
    
        SELECT * FROM {talentospilos_df_formularios} WHERE id = '" . $id . "'
    
    ";

    $result = $DB->get_record_sql($sql);

    $form_id = $result->id;
    if($form_id != null){
        return true;
    }else{
        return false;
    }
}

function dphpforms_pregunta_exist_into_form($pregunta_identifier){

    global $DB;
    
    $sql = "
    
        SELECT * FROM {talentospilos_df_form_preg} WHERE id = '" . $pregunta_identifier . "'
    
    ";
    $result = $DB->get_record_sql($sql);
    $pregunta_identifier = $result->id;
    if($pregunta_identifier != null){
        return true;
        
    }else{
        return false;
        
    }
}

function dphpforms_get_form_reglas($form_id){

    global $DB;

    $sql = "
    
        SELECT RFP.id, RFP.id_form_pregunta_a, RFP.id_form_pregunta_b, R.regla FROM {talentospilos_df_reg_form_pr} RFP INNER JOIN {talentospilos_df_reglas} R ON RFP.id_regla = R.id WHERE RFP.id_formulario = '" . $form_id . "'
    
    ";
    $result = $DB->get_records_sql($sql);
    $result = array_values($result);
    
    $reglas = array();
    for($i = 0; $i < count($result); $i++){
        $row = $result[$i];
        $regla = array(
            'id_regla' => $row->id,
            'respuesta_a' => $row->id_form_pregunta_a,
            'regla' => $row->regla,
            'respuesta_b' => $row->id_form_pregunta_b
        );
        
        array_push($reglas, $regla);
    }

    return $reglas;
}

function dphpforms_get_regla( $id ){

    global $DB;

    $sql = "
    
        SELECT RFP.id, RFP.id_form_pregunta_a, RFP.id_form_pregunta_b, R.regla FROM {talentospilos_df_reg_form_pr} RFP INNER JOIN {talentospilos_df_reglas} R ON RFP.id_regla = R.id WHERE RFP.id = '" . $id . "'
    
    ";

    return $DB->get_record_sql($sql);
}

function dphpforms_reglas_validator($respuestas, $reglas){
    
    $satisfied_reglas = false;
    if(count($reglas) == 0){
        return array(
            'status' => true,
            'unfulfilled_ruler' => ''
        );
    }

    for($i = 0; $i < count($reglas); $i++){
        $id_regla = $reglas[$i]['id_regla'];
        $regla = $reglas[$i]['regla'];
        $respuesta_a = null;
        $respuesta_b = null;
        foreach ($respuestas as &$respuesta) {
            if($reglas[$i]['respuesta_a'] == $respuesta->{'id'}){
                $respuesta_a = clone $respuesta;
                break;
            }
        }
        foreach ($respuestas as &$respuesta) {
            if($reglas[$i]['respuesta_b'] == $respuesta->{'id'}){
                $respuesta_b = clone $respuesta;
                break;
            }
        }
        
        /*if((  is_null($respuesta_a)  ) && (  is_null($respuesta_b)   )){
            return false;
        }*/

        if(is_null($respuesta_a) && $respuesta_b){
            if(!property_exists($respuesta_b, 'id')){
                $respuesta_b = new stdClass();
                $respuesta_a->id = $reglas[$i]['respuesta_a'];
                $respuesta_a->valor =  null;
            }
        }
        
        if(is_null($respuesta_b) && $respuesta_a){
            if(!property_exists($respuesta_a, 'id')){
                $respuesta_b = new stdClass();
                $respuesta_b->id = $reglas[$i]['respuesta_b'];
                $respuesta_b->valor =  null;
            }
        }

        if($regla == 'DIFFERENT'){

            if($respuesta_a->{'valor'} == $respuesta_b->{'valor'}){
                $satisfied_reglas = false;
                /*echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);*/
                return array(
                    'status' => false,
                    'unfulfilled_ruler' => $id_regla
                );
            }else{
                $satisfied_reglas = true;
                //echo "REGLA " . $regla . " CUMPLIDA\n";
            }
        }elseif($regla == 'EQUAL'){

            if($respuesta_a->{'valor'} != $respuesta_b->{'valor'}){
                $satisfied_reglas = false;
                /*echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);*/
                return array(
                    'status' => false,
                    'unfulfilled_ruler' => $id_regla
                );
            }else{
                $satisfied_reglas = true;
                //echo "REGLA " . $regla . " CUMPLIDA\n";
            }
        }elseif($regla == '>'){

            /* Validation for time XX:XX */
            
            if((count($respuesta_a->{'valor'}) == 5)&&(count($respuesta_b->{'valor'}) == 5)){
                    if(($respuesta_a->{'valor'}[2] == ':')&&($respuesta_b->{'valor'}[2] == ':')){
                        if(
                            (is_numeric(substr($respuesta_a->{'valor'}, 0, 1)))&&(is_numeric(substr($respuesta_a->{'valor'}, 3, 4)))&&
                            (is_numeric(substr($respuesta_b->{'valor'}, 0, 1)))&&(is_numeric(substr($respuesta_b->{'valor'}, 3, 4)))
                            ){
                                $time_a = strtotime($respuesta_a->{'valor'});
                                $time_b = strtotime($respuesta_b->{'valor'});
                                if($time_a > $time_b){
                                    $satisfied_reglas = true;
                                }else{
                                    $satisfied_reglas = false;
                                    return array(
                                        'status' => false,
                                        'unfulfilled_ruler' => $id_regla
                                    );
                                }
                        }
                    }
            }
            
            if($respuesta_a->{'valor'} < $respuesta_b->{'valor'}){
                $satisfied_reglas = false;
                /*echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);*/
                return array(
                    'status' => false,
                    'unfulfilled_ruler' => $id_regla
                );
            }else{
                $satisfied_reglas = true;
                //echo "REGLA " . $regla . " CUMPLIDA\n";
            }

        }elseif($regla == '<'){

            /* Validation for time XX:XX */
            
            if((count($respuesta_a->{'valor'}) == 5)&&(count($respuesta_b->{'valor'}) == 5)){
                    if(($respuesta_a->{'valor'}[2] == ':')&&($respuesta_b->{'valor'}[2] == ':')){
                        if(
                            (is_numeric(substr($respuesta_a->{'valor'}, 0, 1)))&&(is_numeric(substr($respuesta_a->{'valor'}, 3, 4)))&&
                            (is_numeric(substr($respuesta_b->{'valor'}, 0, 1)))&&(is_numeric(substr($respuesta_b->{'valor'}, 3, 4)))
                            ){
                                $time_a = strtotime($respuesta_a->{'valor'});
                                $time_b = strtotime($respuesta_b->{'valor'});
                                if($time_a < $time_b){
                                    $satisfied_reglas = true;
                                }else{
                                    $satisfied_reglas = false;
                                    return array(
                                        'status' => false,
                                        'unfulfilled_ruler' => $id_regla
                                    );
                                }
                        }
                    }
            }
            
            if($respuesta_a->{'valor'} > $respuesta_b->{'valor'}){
                $satisfied_reglas = false;
                /*echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);*/
                return array(
                    'status' => false,
                    'unfulfilled_ruler' => $id_regla
                );
            }else{
                $satisfied_reglas = true;
                //echo "REGLA " . $regla . " CUMPLIDA\n";
            }
        }elseif($regla == 'DEPENDS'){
            /*
                BOUND replaces DEPENDS
            */
        }elseif(($regla == 'BOUND')||($regla == 'DEPENDS')){
            /*
                    Se usa -#$%- para enviar cuando el RadioButton está vacío, esto con el fin
                    de asignarle un valor nulo diferente a 0, con el fin de no entrar en conflicto
                    con lo enviado por un CheckBox
                */

            

            /*if(!is_null($respuesta_a->{'valor'})){
                if(trim($respuesta_a->{'valor'}) == ''){
                    $respuesta_tmp = clone $respuesta_a;
                    $respuesta_a = new stdClass();
                    $respuesta_a->id = $respuesta_tmp->{'id'};
                    $respuesta_a->valor = null;
                }
            }

            if(!is_null($respuesta_b->{'valor'})){
                if(trim($respuesta_b->{'valor'}) == ''){
                    $respuesta_tmp = clone $respuesta_b;
                    $respuesta_b = new stdClass();
                    $respuesta_b->id = $respuesta_tmp->{'id'};
                    $respuesta_b->valor = null;
                }
            }*/
            
            if((( !is_null($respuesta_a->{'valor'}) ) && ($respuesta_a->{'valor'} !== "-#$%-") && ($respuesta_a->{'valor'} !== "") ) && (( is_null($respuesta_b->{'valor'}) )||($respuesta_b->{'valor'} === "-#$%-")||($respuesta_b->{'valor'} === "") )){
                $satisfied_reglas = false;
                /*echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);
                echo 'VALOR A' . $respuesta_a->{'valor'} . ' VALOR B' . $respuesta_a->{'valor'};*/
                break;
            }elseif(((  is_null($respuesta_a->{'valor'})  ) || ($respuesta_a->{'valor'} === "-#$%-" ) || ($respuesta_a->{'valor'} === "" )) && (( !is_null($respuesta_b->{'valor'}) ) && ($respuesta_b->{'valor'} !== "-#$%-") && ($respuesta_b->{'valor'} !== "") )){
                $satisfied_reglas = false;
                /*echo "REGLA " . $regla . " NO CUMPLIDA\n";
                print_r($respuesta_a);
                print_r($respuesta_b);
                echo 'VALOR A' . $respuesta_a->{'valor'} . ' VALOR B' . $respuesta_a->{'valor'};*/
                break;
            }else{
                $satisfied_reglas = true;
                //echo "REGLA " . $regla . " CUMPLIDA\n";
            }

            /*print_r($respuesta_a);
            if($satisfied_reglas){
                echo 'SATISFECHA';
            }else{
                echo 'NO SATISFECHA';
            }
            print_r($respuesta_b);
            echo '=====================' . "\n";*/
        }
    }
    return array(
        'status' => $satisfied_reglas,
        'unfulfilled_ruler' => $id_regla
    );
    //return $satisfied_reglas;
}

function dphpforms_get_respuestas_form_completed($idFormularioDiligenciado){
    
    global $DB;

    $sql_respuestas = '
    
        SELECT * 
        FROM {talentospilos_df_respuestas} AS R 
        INNER JOIN 
            (
                SELECT * 
                FROM {talentospilos_df_form_resp} AS FR 
                INNER JOIN {talentospilos_df_form_solu} AS FS 
                ON FR.id = FS.id_formulario_respuestas 
                WHERE FR.id = '.$idFormularioDiligenciado.'
            ) AS FRS 
        ON FRS.id_respuesta = R.id;
    
    ';

    $result = $DB->get_records_sql($sql_respuestas);
    $result = array_values($result);
    
    $respuestas = array();

    for($i = 0; $i < count($result); $i++){
        $row = $result[$i];
        $tmp = array(
            'id' => $row->id_pregunta,
            'valor' => $row->respuesta
        );
        array_push($respuestas, $tmp);
    }

    return $respuestas;

}

function dphpforms_get_expected_respuestas($form_id){

    global $DB;

    $sql = "SELECT * FROM {talentospilos_df_tipo_campo} AS TC 
    INNER JOIN (
        SELECT * FROM {talentospilos_df_preguntas} AS P 
        INNER JOIN (
            SELECT *, F.id AS mod_id_formulario, FP.id AS mod_id_formulario_pregunta FROM {talentospilos_df_formularios} AS F
            INNER JOIN {talentospilos_df_form_preg} AS FP
            ON F.id = FP.id_formulario WHERE F.id = '$form_id' AND F.estado = '1'
            ) AS AA ON P.id = AA.id_pregunta
        ) AS AAA
    ON TC.id = AAA.tipo_campo
    ORDER BY posicion";

    return array_values($DB->get_records_sql($sql));

}

?>