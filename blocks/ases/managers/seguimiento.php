<?php

require_once('query.php');


if(isset($_POST['function'])){
    switch($_POST['function']){
        case "delete":
              deleteSeg();
              break;
        case "new":
            upgradePares(0);
            break;
        case "load":
            load();
            break;
        case "loadSegMonitor":
            loadbyMonitor();
            break;
        case "loadJustOne":
            loadJustOneSeg();
            break;
        case "update":
            upgradePares(1);
            break;
        case "getSeguimiento":
            //loadJustOneSeg();
            getSeguimientos();
            break;
        case "load_grupal":
            load_students();
            break;
        case "send_email":
            send_email($_POST["risk_array"], $_POST["observations_array"],'' ,$_POST["id_student_moodle"], $_POST["id_student_pilos"], $_POST["date"],'', '', $_POST["url"]);
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

function deleteSeg(){
         if(isset($_POST['id'])){
            $result= delete_seguimiento_grupal($_POST['id']);
             echo json_encode($result);
         }else{
            $msg =  new stdClass();
            $msg->error = "Error :(";
            $msg->msg = "Error al eliminar el registro. ";
            echo json_encode($msg);
         }
}




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
                    
                    //se almacena el riesgo siempre y cuando haya una descripcion del campo asosiado
                    
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
            
            //fin validaciones segun el tipo
            //comentados por esteban para evitar la modificacion del moonitor que creo el documento y la fecha creada
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
            //si $fun es cero es insercion de lo contrario es actualización
            if($fun == 0){
                
                if(!isset($_POST['idinstancia'])) throw new Exception('No se reconocio las variblaes necesarias: idinstancia.'); 
                
                //se almacena solo una vez la instancia de donde se generó el registro
                $insert_object->id_instancia = $_POST['idinstancia'];
                //se almacena solo una vez la fecha de creacion
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
                    
                    //se define e incializa el arreglo $idtalentos_old que va contener los id de los talentos del segumiento obenidos de la base de datos
                    $idtalentos_old =  array();
                    $result_get = getEstudiantesSegGrupal($insert_object->id);
                    
                    foreach($result_get as $r){
                        array_push($idtalentos_old,$r->id_estudiante);
                    }
                    
                     //se verifican los ids que ya no van ser parte del seguimiento- los que se borraran de la bd
                    foreach ($idtalentos_old as $id_old){
                        if (!in_array($id_old,$idtalentos_now)){
                            $msg="grupales-drop";
                            dropTalentosFromSeg($insert_object->id,$id_old);
                        }
                    }
                    
                    //se adicionan los nuevos en la lista
                    foreach ($idtalentos_now as $id_now){
                        if(!in_array($id_now, $idtalentos_old)){
                            $msg="grupales-add";
                            insertSegEst($insert_object->id,array($id_now));
                        }
                    }
                    
                    //se actualiza el segumiento
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

function load(){
    
    if((isset($_POST['idtalentos']) || isset($_POST['tipo'])) &&  isset($_POST['idinstancia'])){
        $result =  getSeguimientoOrderBySemester($_POST['idtalentos'], $_POST['tipo'],null, $_POST['idinstancia']);
        //print_r($result);
        echo json_encode($result);
    }else{
        $msg =  new stdClass();
        $msg->error = "Error :(";
        $msg->msg = "Error al cargar el registro. ";
        echo json_encode($msg);
    }
    
}

function getSeguimientos(){
      
        $result =  new stdClass();
        $result->content = get_estudiantes($_POST['id'],$_POST['tipo'],$_POST['idinstancia']);
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
        
        //Validar si es editable
        
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
        $r->createdate = date('d/m/Y \a \l\a\s h:i a',$result->seguimiento->created);
        $r->act_status = $result->seguimiento->status; //no es pendejada, la variable 'status'  hasta JQuery 3.1 es una variable reservada. Por esa razon  se renombra por 'act_status'
        
        $result->hour=$r;
        echo json_encode($result);
    
}

function loadJustOneSeg(){
    
    global $USER;
    $object_role = get_role_user($USER->id, $_POST['idinstancia']);

    if(isset($_POST['id']) && isset($_POST['tipo'])){

    $result =  getSeguimiento(null, $_POST['id'],$_POST['tipo']);
    
        foreach($result as $r){ 
            $r->fecha = date('Y-m-d', $r->fecha);
            
            $hora_ini = explode(":", $r->hora_ini);
            $r->h_ini = $hora_ini[0];
            $r->m_ini = $hora_ini[1];
            
            $hora_fin = explode(":", $r->hora_fin);
            $r->h_fin = $hora_fin[0];
            $r->m_fin = $hora_fin[1];
            
            $user = getUserMoodleByid($r->id_monitor);
            $r->infoMonitor = $user->firstname." ".$user->lastname;
            
            //Validar si es editable
            
            $editable = true;
            
            date_default_timezone_set("America/Bogota");
            $today = new DateTime(date('Y-m-d',time()));
            $created = new DateTime(date('Y-m-d',$r->created));
            $interval = $created->diff($today);
            $days = $interval->format('%a');
            
            // $hour_today = date('H',time());
            // $min_today = date('i',time());
                
            // $hour = date('H',$r->created);
            // $min = date('h',$r->created);
                
            //$r->days = "dias:".$days."how:".$hour_today.":".$min_today."  date:".$hour.":".$min;
            
            
            
            if (intval($days >= 8)){
                $editable =  false;
            }
            
            if($object_role->nombre_rol == 'sistemas' or $object_role->nombre_rol == 'profesional_ps' or $object_role->nombre_rol == 'practicante_ps'){
                $editable =  true;
            }
            
            $r->editable = $editable;
            //se formatea la fecha de creacíón
            $r->createdate = date('d/m/Y \a \l\a\s h:i a',$r->created);
            $r->act_status = $r->status; //no es pendejada, la variable 'status'  hasta JQuery 3.1 es una variable reservada. Por esa razon  se renombra por 'act_status'
            
            if($_POST['tipo'] == 'GRUPAL') $r->attendande_listid = getEstudiantesSegGrupal($_POST['id']);
            
        }
        
        $msg =  new stdClass();
        $msg->result = $result;
        $msg->rows = count($result);
        echo json_encode($msg);
    }else{
        $msg =  new stdClass();
        $msg->error = "Error :(";
        $msg->msg = "Error al cargar el registro";
        echo json_encode($msg);
    }
}

function load_students(){
    global $USER;
    $id_monitor;
    if(isset($_POST['user_management'])){
        $id_monitor = $_POST['user_management'];
    }else if(isset($_POST['user_ps_management'])){
        $id_monitor = $_POST['user_ps_management'];
    }else{
        $id_monitor = $USER->id;
    }
   
  if(!isset($_POST['idinstancia'])) throw new Exception('No se reconocio las variables necesarias: idinstancia.'); 
   
  $result =  new stdClass();
  $result->content = getStudentsGrupal($id_monitor,$_POST['idinstancia']);
  $result->rows = count($result->content);
  echo json_encode($result);
}

function loadbyMonitor(){
    global $USER;
    if(isset($_POST['tipo']) && isset($_POST['idinstancia']) ){
        
        $result =  getSegumientoByMonitor($USER->id,null, $_POST['tipo'], $_POST['idinstancia']);
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

function send_email($risk_array, $observations_array, $id_receiving_user, $id_student_moodle, $id_student_pilos, $date, $subject="", $messageText="", $track_url){

    global $USER;

    $emailToUser = new stdClass;
    $emailFromUser = new stdClass;
    
    $id_professional = get_id_assigned_professional($id_student_pilos);
    $id_practicante = get_id_assigned_pract($id_student_pilos);
    
    $sending_user = get_user_by_username('sistemas1008');
    $receiving_user = get_full_user($id_professional);
    
    $receiving_user_pract = get_full_user($id_practicante);
    
    // $receiving_user = get_full_user($id_receiving_user);
    $student_info = get_full_user($id_student_moodle);
    
    $risk_array = split(",",$risk_array);
    $observations_array = split(",",$observations_array);
    
    $emailToUser->email = $receiving_user->email;
    $emailToUser->firstname = $receiving_user->firstname;
    $emailToUser->lastname = $receiving_user->lastname;
    $emailToUser->maildisplay = true;
    $emailToUser->mailformat = 1;
    $emailToUser->id = $receiving_user->id; 
    $emailToUser->alternatename = '';
    $emailToUser->middlename = '';
    $emailToUser->firstnamephonetic = '';
    $emailToUser->lastnamephonetic = '';
    
    $emailToUserPract->email = $receiving_user_pract->email;
    $emailToUserPract->firstname = $receiving_user_pract->firstname;
    $emailToUserPract->lastname = $receiving_user_pract->lastname;
    $emailToUserPract->maildisplay = true;
    $emailToUserPract->mailformat = 1;
    $emailToUserPract->id = $receiving_user_pract->id; 
    $emailToUserPract->alternatename = '';
    $emailToUserPract->middlename = '';
    $emailToUserPract->firstnamephonetic = '';
    $emailToUserPract->lastnamephonetic = '';

    $emailFromUser->email = $sending_user->email;
    $emailFromUser->firstname = 'Seguimiento';
    $emailFromUser->lastname = 'Sistema de';
    $emailFromUser->maildisplay = false;
    $emailFromUser->mailformat = 1;
    $emailFromUser->id = $sending_user->id; 
    $emailFromUser->alternatename = '';
    $emailFromUser->middlename = '';
    $emailFromUser->firstnamephonetic = '';
    $emailFromUser->lastnamephonetic = '';
    
    $subject = "Registro riesgo alto estudiante";
    
    // Quien lo registro
    // Descripción
    // Enviar enlace ficha
    
    $messageHtml = "Se registra riesgo alto para el estudiante: <br><br>";
    $messageHtml .= "<b>Nombre completo</b>: $student_info->firstname $student_info->lastname <br>";
    $messageHtml .= "<b>Código:</b> $student_info->username <br>";
    $messageHtml .= "<b>Correo electrónico:</b> $student_info->email <br><br>";

    if(count($risk_array) > 1){
        $messageHtml .= "En los componentes: <br><br>";
        $messageHtml .= "<ul>";
        for($i = 0; $i < count($risk_array); $i++){
            
            $messageHtml .= "<li>";    
            $messageHtml .= "<b>".$risk_array[$i]."</b><br>";
            $messageHtml .= $observations_array[$i]."<br>";
            $messageHtml .= "</li>";    
        }
        $messageHtml .= "</ul>";
    }else{
        $messageHtml .= "En el componente: ";
        $messageHtml .= "<li>";
        $messageHtml .= $risk_array[0]."<br>";
        $messageHtml .= $observations_array[0]."<br>";
        $messageHtml .= "</li>";
        $messageHtml .= "</ul>";
    }
    
    $messageHtml .= "Fecha de seguimiento: $date <br>";
    $messageHtml .= "El registro fue realizado por: <b>$USER->firstname $USER->lastname</b><br><br>";
    $messageHtml .= "Puede revisar el registro de seguimiento haciendo clic <a href='$track_url'>aquí</a>.";
    
    $email_result = email_to_user($emailToUser, $emailFromUser, $subject, $messageText, $messageHtml, ", ", true);
    $email_result = email_to_user($emailToUserPract, $emailFromUser, $subject, $messageText, $messageHtml, ", ", true);
    
    return $email_result;
}

// send_email();
?>