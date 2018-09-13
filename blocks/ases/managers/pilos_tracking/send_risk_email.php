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
        case "send_email":
            echo send_email($_POST["risk_array"], $_POST["observations_array"],'' ,$_POST["id_student_moodle"], $_POST["id_student_pilos"], $_POST["date"],'', '', $_POST["url"]);
            break;

        case "send_email_dphpforms":
            echo send_email_dphpforms($_JSON_POST_INPUT->risks, $_JSON_POST_INPUT->student_code, $_JSON_POST_INPUT->date, "", "", $_JSON_POST_INPUT->url );
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



function send_email($risk_array, $observations_array, $id_receiving_user, $id_student_moodle, $id_student_pilos, $date, $subject="", $messageText="", $track_url){

    global $USER, $DB;

    $emailToUser = new stdClass;
    $emailFromUser = new stdClass;
    $emailToUserPract = new stdClass();

    $sql_query = "select id_estudiante from {talentospilos_seg_estudiante}  where id_seguimiento=".$id_student_pilos;
    $id_student = $DB->get_record_sql($sql_query);

    $id_professional = get_id_assigned_professional($id_student->id_estudiante);
    $id_practicante = get_id_assigned_pract($id_student->id_estudiante);
    
    $sending_user = get_user_by_username('sistemas1008');
    $receiving_user = get_full_user($id_professional);
    
    $receiving_user_pract = get_full_user($id_practicante);
    
    // $receiving_user = get_full_user($id_receiving_user);

    $student_info = get_user_moodle($id_student->id_estudiante);

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

function send_email_dphpforms($json_risk_observation_, $student_code, $date, $subject="", $messageText="", $track_url){

    global $USER, $DB;

    $json_risk_observation = array_values( $json_risk_observation_ );

    $emailToUser = new stdClass;
    $emailFromUser = new stdClass;
    $emailToUserPract = new stdClass();
    
    //print_r(get_ases_user_by_code($student_code));
    //$id_estudiante = get_ases_user_by_code($student_code)->id;
    $id_estudiante = $student_code;

    $id_professional = get_assigned_professional($id_estudiante)->id;
    $id_practicante = get_assigned_pract($id_estudiante)->id;
    
    $sending_user = get_user_by_username('sistemas1008');
    $receiving_user = get_full_user($id_professional);
    
    $receiving_user_pract = get_full_user($id_practicante);
    $student_info = get_user_moodle($id_estudiante);

    //return json_encode(  $receiving_user_pract);

    $emailToUser->email = $receiving_user->email;
    $emailToUser->firstname = $receiving_user->firstname;
    $emailToUser->lastname = $receiving_user->lastname;
    $emailToUser->username = $receiving_user->username;
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
    $emailToUserPract->username = $receiving_user_pract->username;
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
    $emailFromUser->username = $sending_user->username;
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

    $risk_lvl3_counter = 0;
    $riskMessage = '';

    foreach($json_risk_observation as $key => $risk){

        if($risk->risk_lvl == 3){
            $riskMessage .= "<li>";    
            $riskMessage .= "<b>".$risk->name."</b><br>";
            $riskMessage .= $risk->observation."<br>";
            $riskMessage .= "</li>";
            $risk_lvl3_counter++;
        }

    }

    if($risk_lvl3_counter > 1){
        $messageHtml .= "En los componentes: <br><br>";
        $messageHtml .= "<ul>";
    }else{
        $messageHtml .= "En el componente: <br><br>";
        $messageHtml .= "<ul>";
    }

    $messageHtml .= $riskMessage;

    $messageHtml .= "</ul>";
    
    
    $messageHtml .= "Fecha de seguimiento: $date <br>";
    $messageHtml .= "El registro fue realizado por: <b>$USER->firstname $USER->lastname</b><br><br>";
    $messageHtml .= "Puede revisar el registro de seguimiento haciendo clic <a href='$track_url'>aquí</a>.";
    

    //return json_encode($emailToUser) ."  ". json_encode($emailFromUser) ."  ". json_encode($subject) ."  ". json_encode($messageText) ."  ". json_encode($messageHtml);

    $email_result = email_to_user($emailToUser, $emailFromUser, "Riesgo de alto nivel: " . $student_info->firstname . $student_info->lastname, "", $messageHtml, ", ", true);
    $email_result = email_to_user($emailToUserPract, $emailFromUser, "Riesgo de alto nivel: " . $student_info->firstname . $student_info->lastname, "", $messageHtml, ", ", true);
    
    //return 'Resultado: ' . $email_result . ' Usuarios: ' .  json_encode($emailToUser) ." |||| ". json_encode($emailFromUser);
    return $email_result;
}
?>