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
 * Estrategia ASES
 *
 * @author     Isabella Serna Ramírez
 * @package    block_ases
 * @copyright  2017 Isabella Serna Ramírez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');
require_once(dirname(__FILE__).'/../seguimiento_grupal/seguimientogrupal_lib.php');
require_once(dirname(__FILE__).'/../student_profile/studentprofile_lib.php');
require_once(dirname(__FILE__).'/../lib/student_lib.php');
require_once(dirname(__FILE__).'/../user_management/user_functions.php');
require_once(dirname(__FILE__).'/../user_management/user_lib.php');
require_once(dirname(__FILE__).'/../dphpforms/dphpforms_forms_core.php');
require_once(dirname(__FILE__).'/../dphpforms/dphpforms_records_finder.php');
require_once(dirname(__FILE__).'/../dphpforms/dphpforms_get_record.php');
require_once(dirname(__FILE__).'/../periods_management/periods_lib.php');


$_JSON_POST_INPUT = json_decode(file_get_contents('php://input'));

//Verifies which functions will be executed to call the respective method or returns a json wheter with an email or  array of students, .
if(isset($_POST['function']) || !(is_null($_JSON_POST_INPUT)) ){
    
    $function = null;
    if(isset($_POST['function'])){
        $function = $_POST['function'];
    }elseif(!(is_null($_JSON_POST_INPUT))){
        $function = $_JSON_POST_INPUT->function;
    }

    switch($function){

        case "new":
            upgradePares(0);
            break;

        case "update":
            upgradePares(1);
            
            break;

        case "loadSegMonitor":
           //Falta agregar si son periodos anteriores mostrar con antiguo formulario
           loadbyMonitor();// --> antigua función para listar seguimientos grupales
            break;

        case "load_trackings_by_monitor";
            load_trackings_by_monitor();
            break;

        case "delete":
              deleteSeg();
              break;


        case "getSeguimiento":
              getSeguimientos();
              break;       

        default:
            $msg =  new stdClass();
            $msg->error = "Error";
            $msg->msg = "Error al comunicarse con el servidor. Verificar la función";
            echo json_encode($msg);
            break;
    }
    
}else{
        $msg =  new stdClass();
        $msg->error = "Error :(";
        $msg->msg = "Error al comunicarse con el servidor. No se reconoció la funcion a ejecutar".$_POST['id_seg'];
        echo json_encode($msg);
}


/**** The functions begin
****/


/**
 * Function that returns a student's tracking 
 * 
 * @see getSeguimientos()
 * @return object --> echo a json format (object)
 */
function getSeguimientos(){
      
        $result =  new stdClass();
        $result->content = get_students_assistance($_POST['id'],$_POST['tipo'],$_POST['idinstancia']);
        $result->rows = count($result->content);
        $result->seguimiento = get_seguimientos($_POST['id'],$_POST['tipo'],$_POST['idinstancia']);
            
        $r->seguimiento->fecha = date('Y-m-d', $result->seguimiento->fecha);
            
        $hora_ini = explode(":", $result->seguimiento->hora_ini);
        $r->h_ini = $hora_ini[0];
        $r->m_ini = $hora_ini[1];
        
        $hora_fin = explode(":", $result->seguimiento->hora_fin);
        $r->h_fin = $hora_fin[0];
        $r->m_fin = $hora_fin[1];
            
        $user = getUserMoodleByid($result->seguimiento->id_monitor);
        $r->infoMonitor = $user->firstname." ".$user->lastname;
        
        //Validate if it's editable
        
        $editable = true;
            
        date_default_timezone_set("America/Bogota");
        $today = new DateTime(date('Y-m-d',time()));
        $created = new DateTime(date('Y-m-d',$result->seguimiento->created));
        $interval = $created->diff($today);
        $days = $interval->format('%a');
           
            
        if (intval($days >= 8)){
            $editable =  false;
        }
            
        if($object_role->nombre_rol == 'sistemas' or $object_role->nombre_rol == 'profesional_ps' or $object_role->nombre_rol == 'practicante_ps'){
            $editable =  true;
        }
            
        $r->editable = $editable;
        //se formatea la fecha de creacíón
        //creation date is formatted
        $r->createdate = date('d/m/Y \a \l\a\s h:i a',$result->seguimiento->created);
        $r->act_status = $result->seguimiento->status; // variable 'status'  until JQuery 3.1 is a reserved variable. That's the reason of its rename to 'act_status'
        
        $result->hour=$r;
        echo json_encode($result);
    
}



/**
 * Function that deletes a tracking
 * 
 * @see deleteSeg()
 * @return object in a json format
 */
function deleteSeg(){
         if(isset($_POST['id'])){
            $result= delete_tracking_peer($_POST['id']);
             echo json_encode($result);
         }else{
            $msg =  new stdClass();
            $msg->error = "Error :(";
            $msg->msg = "Error al eliminar el registro. ";
            echo json_encode($msg);
         }
}

/** <new forms>
 * Function that obtains trackings given to monitor id( (with logic of new forms)
 * 
 * @see load_trackings_by_monitor()
 * @return object --> echo a json format (object)
 */
function load_trackings_by_monitor(){
    global $USER;

    if(isset($_POST['idinstancia']) ){

        $current_semester = get_current_semester();
        $result = get_tracking_grupal_monitor_current_semester($USER->id,$current_semester->max);
        $render_trackings= render_monitor_groupal_trackings($result);
  
        echo json_encode($render_trackings);
    }else{
        $msg =  new stdClass();
        $msg->error = "Error :(";
        $msg->msg = "Error al cargar seguimientos del monitor. ";
       echo json_encode($msg);
    }
}




/**  <old forms>
 * Function that obtains a track given a monitor id (instance), date and count of tracks.
 * 
 * @see load_students()
 * @return object --> echo a json format (object)
 */
function loadbyMonitor(){
    global $USER;

    if(isset($_POST['tipo']) && isset($_POST['idinstancia']) ){
        $result =  get_tracking_by_monitor($USER->id,null, $_POST['tipo'], $_POST['idinstancia']);
        $result_array=[];
        $array =[];

        foreach($result as $r){
            $r->fecha = date('d-m-Y', $r->fecha);
            $array = $r;
            array_push($result_array,$array);
        }

        $msg =  new stdClass();
        $msg->result = $result_array;
        $msg->rows = count($result);

        echo json_encode($msg);
    }else{
        $msg =  new stdClass();
        $msg->error = "Error :(";
        $msg->msg = "Error al almacenar el registro. ";
       echo json_encode($msg);
    }
}

/**
 * Function that updates a new groupal tracking
 * 
 * @see upgradePares($fun)
 * @param $fun ---> parameter that indicates wheter update or insertion
 * @return object in a json format
 */

function upgradePares($fun){
    try{
        
        if(isset($_POST['date']) && isset($_POST['place']) && isset($_POST['h_ini']) && isset($_POST['m_ini']) && isset($_POST['h_fin']) && isset($_POST['idtalentos']) && isset($_POST['m_fin']) && isset($_POST['tema']) && isset($_POST['objetivos']) && isset($_POST['tipo']) && isset($_POST['observaciones'])){
            global $USER;
            date_default_timezone_set("America/Bogota");
            $today = time();
            $insert_object = new stdClass();
        
             //inicio validaciones segun el tipo
            if($_POST['tipo'] == 'GRUPAL'){
                if(!isset($_POST['actividades'])){
                    throw new Exception('No se reconocio las variblaes necesarias para actualizar un nuevo seguimiento grupal');
                }
                $insert_object->actividades = $_POST['actividades'];
                $insert_object->id_monitor=$USER->id;
                $insert_object->status=1;
                
            }elseif($_POST['tipo'] == 'PARES'){
                
                    if(isset($_POST['individual']) && isset($_POST['familiar']) && isset($_POST['academico']) && isset($_POST['economico']) && isset($_POST['vida_uni'])){
                    if($_POST['individual'] != "" && !isset($_POST['riesgo_ind']))   throw new Exception('No se reconocio las variblaes necesarias para actualizar un nuevo seguimiento individual'); 
                    if($_POST['familiar'] != "" && !isset($_POST['riesgo_familiar']))   throw new Exception('No se reconocio las variblaes necesarias para actualizar un nuevo seguimiento familiar'); 
                    if($_POST['academico'] != "" && !isset($_POST['riesgo_aca']))   throw new Exception('No se reconocio las variblaes necesarias para actualizar un nuevo seguimiento academico'); 
                    if($_POST['economico'] != "" && !isset($_POST['riesgo_econom']))   throw new Exception('No se reconocio las variblaes necesarias para actualizar un nuevo seguimiento economico'); 
                    if($_POST['vida_uni'] != "" && !isset($_POST['riesgo_uni']))   throw new Exception('No se reconocio las variblaes necesarias para actualizar un nuevo seguimiento vida_uni'); 
                    
                    //the risk is storaged wether there is a description of the specific field
                    
                    if($_POST['vida_uni'] == "" && isset($_POST['riesgo_uni'])){
                        $insert_object->vida_uni_riesgo = null;
                    }else{
                        $insert_object->vida_uni_riesgo = $_POST['riesgo_uni'];
                    }
                    
                    if($_POST['economico'] == "" && isset($_POST['riesgo_econom'])){
                        $insert_object->economico_riesgo = null;
                    }else{
                        $insert_object->economico_riesgo = $_POST['riesgo_econom'];
                    }
                    
                    if($_POST['academico'] == "" && isset($_POST['riesgo_aca'])){
                        $insert_object->academico_riesgo = null;
                    }else{
                        $insert_object->academico_riesgo = $_POST['riesgo_aca'];
                    }
                    
                    if($_POST['familiar'] == "" && isset($_POST['riesgo_familiar'])){
                        $insert_object->familiar_riesgo = null;
                    }else{
                        $insert_object->familiar_riesgo = $_POST['riesgo_familiar'];
                    }
                    
                    if($_POST['individual'] == "" && isset($_POST['riesgo_ind'])){
                        $insert_object->individual_riesgo = null;
                    }else{
                        $insert_object->individual_riesgo = $_POST['riesgo_ind'];
                    }
                    
                    $insert_object->individual = $_POST['individual'];
                    $insert_object->familiar_desc = $_POST['familiar'];
                    $insert_object->academico = $_POST['academico'];
                    $insert_object->economico = $_POST['economico'];
                    $insert_object->vida_uni = $_POST['vida_uni'];

 
                }else{
                  throw new Exception('No se reconocio las variblaes necesarias para actualizar un nuevo seguimiento pares'); 
                }
            }
            
            //end on validations depending on type
            // $insert_object->id_monitor = $USER->id;
            // $insert_object->created = $today;
            $insert_object->fecha = strtotime($_POST['date']);
            $insert_object->hora_ini = $_POST['h_ini'].":".$_POST['m_ini'];
            $insert_object->hora_fin = $_POST['h_fin'].":".$_POST['m_fin'];
            $insert_object->lugar = $_POST['place'];
            $insert_object->tema = $_POST['tema'];
            $insert_object->objetivos = $_POST['objetivos'];
            $insert_object->observaciones = $_POST['observaciones'];
            $insert_object->tipo = $_POST['tipo'];

            $id = explode(",", $_POST['idtalentos']);

            $result = false;
            //if $fun = 0 then is an insert otherwise and update
            if($fun == 0){
                
                if(!isset($_POST['idinstancia'])) throw new Exception('No se reconocio las variblaes necesarias: idinstancia.'); 
                
                //the instance is storaged just once 
                $insert_object->id_instancia = $_POST['idinstancia'];
                //Creation date is storaged just once
                $insert_object->created = $today;
                
               
                insertSeguimiento($insert_object,$id);
                $msg =  new stdClass();
                $msg->exito = "exito";
                $msg->msg = "se ha almacenado la informacion con exito.";
                echo json_encode($msg);
                return 0;
               
            }else{
                $msg="";
                $insert_object->id = $_POST['id_seg'];
                $result = null;
                
                if ($insert_object->tipo == 'PARES'){
                    $msg = "pares";
                    $result = updateSeguimiento_pares($insert_object);
                }elseif($insert_object->tipo == 'GRUPAL'){
                    $msg="grupales";
                    $idtalentos_now = $id;
                    
                    //An array is defined and initialized that'll contain all the id's from 'talentos' on the database
                    $idtalentos_old =  array();
                    $result_get = getEstudiantesSegGrupal($insert_object->id);
                    
                    foreach($result_get as $r){
                        array_push($idtalentos_old,$r->id_estudiante);
                    }
                    
                     //All id's are verified of not beeing part of 'seguimiento'
                    foreach ($idtalentos_old as $id_old){
                        if (!in_array($id_old,$idtalentos_now)){
                            $msg="grupales-drop";
                            dropTalentosFromSeg($insert_object->id,$id_old);
                        }
                    }
                    
                    //The new nes will be added to the list
                    foreach ($idtalentos_now as $id_now){
                        if(!in_array($id_now, $idtalentos_old)){
                            $msg="grupales-add";
                            insertSegEst($insert_object->id,array($id_now));
                        }
                    }
                    
                    //Updates 'seguimiento'
                    $result = updateSeguimiento_pares($insert_object);
                }
                
                if ($result){
                    $msg =  new stdClass();
                    $msg->exito = "exito";
                    $msg->msg = "se ha almacenado la informacion con exito";
                    echo json_encode($msg);
                }else{
                    $msg =  new stdClass();
                    $msg->error = "Error :(";
                    $msg->msg = "error al actualizar";
                    echo json_encode($msg);
                }
            }
           
        }else{
            $msg =  new stdClass();
                $msg->error = "Error :(";
                $msg->msg = "Error al comuniscarse con el servidor. No se reconocio las variblaes necesarias para actualizar un nuevo seguimiento";
                echo json_encode($msg);
        }
    }
    catch(Exception $e){
        $msg =  new stdClass();
        $msg->error = "Error :(";
        $msg->msg = "Error al almacenar el registro. ".$e->getMessage()." ".pg_last_error();
        echo json_encode($msg);
    }
}

?>