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
require_once('user_functions.php');

require_once('user_lib.php');

$_JSON_POST_INPUT = json_decode(file_get_contents('php://input'));



if(isset($_POST['function'])){
    switch($_POST['function']){

        case "students_consult":
            $students = get_students($_POST["instancia"]);
            echo json_encode($students);
            break;


        case "load_grupal":
            load_students();
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
  $result->content = get_grupal_students($id_monitor,$_POST['idinstancia']);
  $result->rows = count($result->content);
  echo json_encode($result);
}
?>