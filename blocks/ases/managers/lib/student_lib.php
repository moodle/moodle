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
 * Talentos Pilos
 *
 * @author     Iader E. García Gómez
 * @author     Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  2017 Iader E. García <iadergg@gmail.com>
 * @copyright  2017 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once dirname(__FILE__) . '/../../../../config.php';

require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';

/**
 * Obtains an user object given user id from {talentospilos_usuario} table
 *
 * @see get_user_moodle($id)
 * @param $id --> moodle user id
 * @return object
 */
function get_user_moodle($id)
{

    global $DB;

    $sql_query = "SELECT * from {user} where id= (SELECT id_moodle_user
                                                  FROM {talentospilos_user_extended} extended
                                                  WHERE id_ases_user = $id and tracking_status=1)";
    $user = $DB->get_record_sql($sql_query);

    return $user;
}

/**
 * Función que recupera los campos de usuario de la tabla {talentospilos_usuario}
 * Gets all fields from user on {talentospilos_usuario} table
 *
 * @see get_ases_user($id)
 * @param $id_student --> student id on {talentospilos_usuario} table
 * @return array --> with every field
 */
function get_ases_user($id)
{

    global $DB;

    $sql_query = "SELECT num_doc, tipo_doc, (now() - fecha_nac)/365 AS age, estado, estado_ases, direccion_res, tel_ini, tel_res, celular, emailpilos, acudiente, tel_acudiente, estado_ases, observacion  FROM {talentospilos_usuario} WHERE id = $id";
    $user = $DB->get_record_sql($sql_query);

    return $user;
}
/**
 * //THIS GOTTA CHANGE TO THE NEW MODEL

 * Gets moodle user id (moodle table) given user id from {talentospilos_usuario}
 *
 * @see get_id_user_moodle($id_student)
 * @param $id_student --> user id from {talentospilos_usuario}
 * @return string with the moodle id
 */
function get_id_user_moodle($id_student)
{

    global $DB;

    $sql_query = "SELECT id_moodle_user FROM {talentospilos_user_extended} WHERE id_ases_user = $id_student AND tracking_status = 1";
    $id_user_moodle = $DB->get_record_sql($sql_query)->id_moodle_user;
    //USANDO MODELO ANTIGUO
    // $sql_query = "SELECT id FROM {user_info_field} WHERE shortname = 'idtalentos'";
    // $id_field = $DB->get_record_sql($sql_query)->id;

    // $sql_query = "SELECT MAX(userid) AS userid FROM {user_info_data} WHERE fieldid = $id_field AND data = '$id_student'";

    // $id_user_moodle = $DB->get_record_sql($sql_query)->userid;

    return $id_user_moodle;
}




/**
 * Gets ASES user given student id associated to moodle user name
 *
 * @see get_ases_user_by_code($code)
 * @param $username --> student id associated to moodle user
 * @return object containing the ASES student information
 */
function get_ases_user_by_code($code)
{

    global $DB;

    $sql_query = "SELECT MAX(id) as id_moodle FROM {user} WHERE username LIKE '" . $code . "%';";

    $id_moodle_result = $DB->get_record_sql($sql_query);

    if ($id_moodle_result) {
        $id_moodle = $id_moodle_result->id_moodle;
    } else {
        return false;
    }

    if( !$id_moodle ){
        return false;
    };

    $add_fields = get_adds_fields_mi($id_moodle);
    
    if($add_fields){
        $id_ases = $add_fields->idtalentos;
    }else{
        return false;
    }

    $sql_query = "SELECT *, (now() - fecha_nac)/365 AS age FROM {talentospilos_usuario} WHERE id =" . $id_ases;

    $ases_user = $DB->get_record_sql($sql_query);

    return $ases_user;
}

/**
 * Gets ASES student status
 *
 * @see get_student_ases_status($id)
 * @param $id --> user id on talentospilos_usuario table
 * @return object --> with ASES student information
 */

function get_student_ases_status($id_student)
{
    global $DB;

    $sql_query = "SELECT MAX(id) FROM {talentospilos_est_estadoases} WHERE id_estudiante = $id_student";
    $id_ases_status = $DB->get_record_sql($sql_query)->max;

    $sql_query = "SELECT * FROM {talentospilos_est_estadoases} WHERE id = $id_ases_status";
    $id_status = $DB->get_record_sql($sql_query)->id_estado_ases;

    if ($id_ases_status) {
        $sql_query = "SELECT * FROM {talentospilos_estados_ases} WHERE  id = $id_status";
        $status_ases = $DB->get_record_sql($sql_query);
    } else {
        $status_ases = "NO REGISTRA";
    }

    return $status_ases;
}

/**
 * Gets student ICETEX status
 *
 * @see get_student_icetex_status($id_student)
 * @param $id_student --> student id on talentospilos_usuario table
 * @return array --> with ICETEX student information
 */

function get_student_icetex_status($id_student)
{
    global $DB;

    $sql_query = "SELECT MAX(id) FROM {talentospilos_est_est_icetex} WHERE id_estudiante = $id_student";
    $id_icetex_status = $DB->get_record_sql($sql_query)->max;

    $sql_query = "SELECT * FROM {talentospilos_est_est_icetex} WHERE id = $id_icetex_status";
    $id_status = $DB->get_record_sql($sql_query)->id_estado_icetex;

    if ($id_icetex_status) {
        $sql_query = "SELECT * FROM {talentospilos_estados_icetex} WHERE  id = $id_status";
        $status_icetex = $DB->get_record_sql($sql_query);
    } else {
        $status_icetex = "NO REGISTRA";
    }

    return $status_icetex;
}

/**
 * Gets student information from {talentospilos_user_extended} table given his id
 *
 * @see get_adds_fields_mi($id_student)
 * @param $id_student --> student id
 * @return object --> object representing moodle user
 */

function get_adds_fields_mi($id_student)
{

    global $DB;

    $sql_query = "SELECT * FROM {talentospilos_user_extended} WHERE id_moodle_user = $id_student";

    $result = $DB->get_record_sql($sql_query);
    if ($result) {
        $array_result = new stdClass();
        $array_result->idtalentos = $result->id_ases_user;
        $array_result->idprograma = $result->id_academic_program;
        $array_result->estado = $result->program_status;
    } else {
        $array_result = false;
    }

    return $array_result;
}

/**
 * Obtains academic program data given the program id
 *
 * @see get_program($id_program)
 * @param $id --> program id
 * @return object representing all the academic program information
 */
function get_program($id)
{

    global $DB;

    $program = $DB->get_record_sql("SELECT * FROM  {talentospilos_programa} WHERE id=" . $id . ";");

    return $program;
}

/**
 * Obtains faculty information given its id
 *
 * @see get_faculty($id)
 * @param $$id --> faculty id
 * @return object representing all faculty information
 */
function get_faculty($id)
{

    global $DB;

    $sql_query = "SELECT * FROM {talentospilos_facultad} WHERE id=" . $id;
    $result = $DB->get_record_sql($sql_query);

    return $result;
}

/**
 * Gets student cohort
 *
 * @see get_cohort_by_student($id_student)
 * @param $id_student --> student id
 * @return object Representing the cohort
 */
function get_cohort_student($id_student)
{

    global $DB;

    $sql_query = "SELECT MAX(id) AS id FROM {cohort_members} WHERE userid = $id_student;";
    $id_cohort_member = $DB->get_record_sql($sql_query)->id;

    $sql_query = "SELECT cohortid FROM {cohort_members} WHERE id = $id_cohort_member";
    $id_cohort = $DB->get_record_sql($sql_query)->cohortid;

    $sql_query = "SELECT name, idnumber FROM {cohort} WHERE id = $id_cohort;";
    $cohort = $DB->get_record_sql($sql_query);

    return $cohort;
}

/**
 * Gets student cohorts
 *
 * @see get_cohorts_by_student($id_student)
 * @param $id_moodle_student --> ID moodle 
 * @return Array
 */
function get_cohorts_by_student($id_moodle_student){

    global $DB;

    $result_to_return = array();

    $sql_query = "SELECT cohorts.name
                  FROM {cohort_members} AS members 
                    INNER JOIN {cohort} AS cohorts ON members.cohortid = cohorts.id
                  WHERE userid = $id_moodle_student";
    
    $result_query = $DB->get_records_sql($sql_query);

    foreach($result_query as $result){
        array_push($result_to_return, $result);
    }

    return $result_to_return;
}

/**
 * Obtains name, lastname and email from a monitor assigned to a student, given the student id
 *
 * @see get_assigned_monitor($id_student)
 * @param $id_student --> student id on {talentospilos_usuario} table
 * @return array Containing the information
 */
function get_assigned_monitor($id_student)
{

    global $DB;

    $object_current_semester = get_current_semester();

    $sql_query = "SELECT id_monitor 
                  FROM {talentospilos_monitor_estud} 
                  WHERE id_estudiante = ".$id_student." AND id_semestre = ".$object_current_semester->max.";";

    $result = $DB->get_record_sql($sql_query);    
    
    $monitor = $DB->get_record_sql($sql_query);
    $id_monitor = -1;
    if($monitor){
        $id_monitor = $monitor->id_monitor;
    }else{
        return array();
    }

    if ($id_monitor) {

        $sql_query = "SELECT id, firstname, lastname, email 
                      FROM {user} 
                      WHERE id = ".$id_monitor;

        $monitor_object = $DB->get_record_sql($sql_query);

    } else {
        $monitor_object = array();
    }
    return $monitor_object;
}

/**
 * Obtains name, lastname and email from a practicant (practicante) assigned to a student, given the student id
 *
 * @see get_assigned_pract($id_student)
 * @param $id_student --> student id on {talentospilos_usuario} table
 * @return array Containing the information
 */
function get_assigned_pract($id_student)
{

    global $DB;

    $object_current_semester = get_current_semester();

    $sql_query = "SELECT id_monitor FROM {talentospilos_monitor_estud} WHERE id_estudiante =" . $id_student . " AND id_semestre = " . $object_current_semester->max . ";";
    $id_monitor = $DB->get_record_sql($sql_query)->id_monitor;

    if ($id_monitor) {
        $sql_query = "SELECT id_jefe FROM {talentospilos_user_rol} WHERE id_usuario = " . $id_monitor . " AND id_semestre = " . $object_current_semester->max . ";";
        $id_trainee = $DB->get_record_sql($sql_query)->id_jefe;

        if ($id_trainee) {
            $sql_query = "SELECT id, firstname, lastname, email FROM {user} WHERE id = " . $id_trainee;
            $trainee_object = $DB->get_record_sql($sql_query);
        } else {
            $trainee_object = array();
        }
    } else {
        $trainee_object = array();
    }

    return $trainee_object;
}

/**
 * Obtains name, lastname and email from a professional (profesional) assigned to a student, given the student id
 *
 * @see get_assigned_professional($id_student)
 * @param $id_student --> student id on {talentospilos_usuario} table
 * @return array Containing the information
 */
function get_assigned_professional($id_student)
{

    global $DB;

    $object_current_semester = get_current_semester();

    $sql_query = "SELECT id_monitor FROM {talentospilos_monitor_estud} WHERE id_estudiante =" . $id_student . " AND id_semestre = " . $object_current_semester->max . ";";
    $id_monitor = $DB->get_record_sql($sql_query);

    if ($id_monitor) {

        $sql_query = "SELECT id_jefe
                      FROM {talentospilos_user_rol}
                      WHERE id_usuario = $id_monitor->id_monitor AND id_semestre = $object_current_semester->max";

        $id_trainee = $DB->get_record_sql($sql_query)->id_jefe;

        if ($id_trainee) {

            $sql_query = "SELECT id_jefe
                          FROM {talentospilos_user_rol}
                          WHERE id_usuario = $id_trainee AND id_semestre = $object_current_semester->max;";

            $id_professional = $DB->get_record_sql($sql_query)->id_jefe;

            if ($id_professional) {
                $sql_query = "SELECT id, firstname, lastname, email
                              FROM {user} WHERE id = $id_professional ;";
                $professional_object = $DB->get_record_sql($sql_query);
                //$tmp = (array) $professional_object;
                //print_r($professional_object);
                if (!isset($professional_object->firstname)) {
                    $professional_object = array();
                }
            } else {
                $professional_object = array();
            }
        } else {
            $professional_object = array();
        }
    } else {
        $professional_object = array();
    }

    return $professional_object;
}

/**
 * Gets an array with all students risks given user id on {talentospilos_usuario} table
 *
 * @see get_risk_by_student($id_student)
 * @param $id_student --> student id on {talentospilos_usuario} table
 * @return array Containing the information
 */

function get_risk_by_student($id_student)
{

    global $DB;

    $sql_query = "SELECT riesgo.nombre, r_usuario.calificacion_riesgo
                  FROM {talentospilos_riesg_usuario} AS r_usuario INNER JOIN {talentospilos_riesgos_ases} AS riesgo ON r_usuario.id_riesgo = riesgo.id
                  WHERE r_usuario.id_usuario = $id_student AND riesgo.nombre <> 'geografico'";

    $array_risk = $DB->get_records_sql($sql_query);

    return $array_risk;
}

/**
 * Gets a moodle user object given his id
 *
 * @see get_full_user($id)
 * @param $id --> student id on {user} table
 * @return object representing the user
 */

function get_full_user($id)
{
    global $DB;

    $sql_query = "SELECT * FROM {user} WHERE id= " . $id;
    $user = $DB->get_record_sql($sql_query);

    return $user;
}

/**
 * Returns the academic programs associated with a student
 *
 * @see get_academic_program($id_ases_user)
 * @param $id_ases_user --> student id on {talentospilos_usuario} table 
 * @return array 
 */
function get_academic_programs_by_student($id_ases_user){

    global $DB;

    $result_to_return = new stdClass();
    $array_result = array();

    $sql_query = "SELECT user_extended.id_moodle_user, 
                         academic_program.id AS academic_program_id, 
                         academic_program.cod_univalle, 
                         academic_program.nombre AS nombre_programa, 
                         academic_program.jornada, 
                         faculty.nombre AS nombre_facultad,
                         user_extended.program_status, 
                         user_extended.tracking_status
                  FROM {talentospilos_user_extended} AS user_extended
                       INNER JOIN {talentospilos_programa} AS academic_program ON user_extended.id_academic_program = academic_program.id
                       INNER JOIN {talentospilos_facultad} AS faculty ON academic_program.id_facultad = faculty.id
                  WHERE id_ases_user = $id_ases_user";
    
    $array_result_query = $DB->get_records_sql($sql_query);

    foreach($array_result_query as $result){
        array_push($array_result, $result);
        
    }

    return $array_result;
}

/**
 * Update the academic program status of a student
 *
 * @see update_status_program($program_id, $status, $student_id)
 * @param $program_id --> Academic program id
 * @param $status --> New status for an academic program
 * @param $student_id --> Student id in Moodle table
 * @return array 
 */

function update_status_program($program_id, $status, $student_id){

    global $DB;

    $sql_query = "SELECT id 
                  FROM {talentospilos_user_extended} 
                  WHERE id_academic_program = $program_id AND id_moodle_user = $student_id";

    $id_register = $DB->get_record_sql($sql_query)->id;

    $object_updatable = new stdClass();
    $object_updatable->id = $id_register;
    $object_updatable->program_status = $status;

    $result = $DB->update_record('talentospilos_user_extended', $object_updatable);

    if($result){
        return 1;
    }else{
        return 0;
    }

}

/* Obtains name, lastname and email from a monitor assigned to a student, given the student id
 *
 * @see get_student_monitor($id_ases_user,$id_semester,$id_instance)
 * @param $id_student --> student id on {talentospilos_usuario} table
 * @return array Containing the information
 */
function get_student_monitor($id_ases_user, $id_semester, $id_instance)
{

    global $DB;

    $sql_query = "SELECT id_monitor FROM {talentospilos_monitor_estud} WHERE id_estudiante =$id_ases_user AND  id_instancia=$id_instance AND id_semestre =$id_semester";
    $id_monitor = $DB->get_record_sql($sql_query)->id_monitor;

    return $id_monitor;
}
