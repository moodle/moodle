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

require_once(dirname(__FILE__).'/../../../../config.php');
require_once(dirname(__FILE__).'/../periods_management/periods_lib.php');
require_once(dirname(__FILE__).'/../role_management/role_management_lib.php');
require_once ('asesreport_functions.php');
global $USER;

if(isset($_POST['type'])&&$_POST['type']=="assign_student") 
 {
    global $DB;


     $record = new stdClass;

    //Assign a professional as head of a practitioner in current period 

    $role =get_role_id('practicante_ps')->id;
    $practicant = $_POST['practicant'];
    $monitor = $_POST['monitor'];
    $semester =get_current_semester()->max;
    $student_username= $_POST['student'];
    $instance = $_POST['instance'];


    $sql_query = "SELECT id FROM {talentospilos_user_rol} WHERE id_rol = '$role' and id_usuario='$practicant' and estado=1 and id_semestre='$semester' and id_jefe='$USER->id' and id_instancia='$instance'";

    $result = $DB->get_record_sql($sql_query);
    
    if(! $result){
    $record->id_rol     = $role;
    $record->id_usuario = $practicant;
    $record->estado =1;
    $record->id_semestre =$semester;
    $record->id_jefe= $USER->id;
    $record->id_instancia= $instance;
    $record->id_programa=null;
    $insert = $DB->insert_record('talentospilos_user_rol', $record, true);
    }

    $role =get_role_id('monitor_ps')->id;

    $sql_query = "SELECT id FROM {talentospilos_user_rol} WHERE id_rol = '$role' and id_usuario='$monitor' and estado=1 and id_semestre='$semester' and id_jefe='$practicant' and id_instancia='$instance'";
    $result = $DB->get_record_sql($sql_query);

    if(! $result){

    //Assign a practitioner as head of a monitor in current period
    $record= new stdClass;
    $record->id_rol     = $role;
    $record->id_usuario = $monitor;
    $record->estado =1;
    $record->id_semestre =$semester;
    $record->id_jefe= $practicant;
    $record->id_instancia= $instance;
    $insert_record = $DB->insert_record('talentospilos_user_rol', $record, false);
   }
    //Assigns the student in the current period to the monitor
    $sql_query = "SELECT * FROM {user} WHERE username='$student_username'";
    $student = $DB->get_record_sql($sql_query);
    if($student){

    $sql_query = "SELECT * FROM  mdl_talentospilos_user_extended ext INNER JOIN mdl_user users ON ext.id_moodle_user = users.id where id_moodle_user='$student->id'";

      $id_ases_user  = $DB->get_record_sql($sql_query)->id_ases_user; 

      $sql_query = "SELECT * FROM {talentospilos_monitor_estud} WHERE id_monitor='$monitor' and id_estudiante='$id_ases_user' and id_semestre='$semester' and id_instancia='$instance'";
      $assign  = $DB->get_record_sql($sql_query);  

      if (! $assign){
 
      $record= new stdClass;
      $record->id_monitor = $monitor;
      $record->id_estudiante = $id_ases_user;
      $record->id_semestre =$semester;
      $record->id_instancia= $instance;
      $insert_record = $DB->insert_record('talentospilos_monitor_estud', $record, false);
      echo "Asignó el estudiante al monitor";
     }else{
      echo "El estudiante ya se encuentra asignado a otro monitor";
    }
    }


}else if(isset($_POST['user'])&&isset($_POST['source'])&&$_POST['source']=="list_monitors"&&isset($_POST['instance'])){
 $monitors = get_monitors_of_pract($_POST['user'],$_POST['instance']);
 $select_options =create_option_of_select($monitors);
  echo json_encode($select_options);
}
?>