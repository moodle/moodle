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
 * @author     Juan Pablo Moreno Muñoz
 * @author     Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  2017 Iader E. García <iadergg@gmail.com>
 * @copyright  2017 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @copyright  2017 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once dirname(__FILE__) . '/../../../../config.php';
require_once $CFG->dirroot . '/blocks/ases/managers/periods_management/periods_lib.php';

/**
 * Gets all academic programs that are stored on talentospilos_programa table
 *
 * @see load_programs()
 * @return array -->Array with every academic program (id, codigo_snies, codigo_univalle, nombre, id_sede, jornada, id_facultad)
 */
function load_programs()
{

    global $DB;

    $sql_query = "SELECT * FROM {talentospilos_programa}";
    $array_programs = $DB->get_records_sql($sql_query);

    return $array_programs;
}

/**
 * Gets all academic programs that are stored on talentospilos_programa table corresponding to CALI city
 *
 * @see load_programs_cali()
 * @return array --> Array with every academic program  (id, codigo_snies, codigo_univalle, nombre, id_sede, jornada, id_facultad)
 */
function load_programs_cali()
{

    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_sede} WHERE nombre = 'CALI'";
    $id_cali = $DB->get_record_sql($sql_query)->id;

    $sql_query = "SELECT * FROM {talentospilos_programa} WHERE id_sede = $id_cali ORDER BY nombre";
    $array_programs = $DB->get_records_sql($sql_query);

    return $array_programs;
}

/**
 * Returns an user given his moodle username
 *
 * @see search_user($username)
 * @param $username
 * @return object --> Object representing the user
 */
function search_user($username)
{

    global $DB;

    $sql_query = "SELECT * FROM {user} WHERE username ='$username'";
    $array_user = $DB->get_record_sql($sql_query);

    return $array_user;
}

/**
 * Evaluates wheter a user is a practicant or monitor
 *
 * @see isMonOrPract($USER)
 * @param $USER --> Object user
 * @return bool --> True if it's a practicant or monitor, false otherwise
 */
function isMonOrPract($USER)
{
    global $DB;

    $id = $USER->id;
    $query_role = "SELECT rol.nombre_rol  FROM {talentospilos_rol} rol INNER JOIN {talentospilos_user_rol} uRol ON rol.id = uRol.id_rol WHERE uRol.id_usuario = $id AND uRol.id_semestre = (SELECT max(id_semestre) FROM {talentospilos_user_rol})";
    $rol = $DB->get_record_sql($query_role)->nombre_rol;

    if ($rol != "monitor_ps" && $rol != "practicante_ps") {
        return false;
    } else {
        return true;
    }
}

/**
 * Gets an ASES user role given his moodle id
 *
 * @see get_role_ases($id)
 * @param $id --> user moodle id
 * @return string --> containing user role
 */
function get_role_ases($id)
{
    global $DB;

    $semestre = get_current_semester();
    $id_semestre = $semestre->max;

    $query_role = "SELECT rol.nombre_rol  FROM {talentospilos_rol} rol INNER JOIN {talentospilos_user_rol} uRol ON rol.id = uRol.id_rol WHERE uRol.id_usuario = $id AND uRol.id_semestre = $id_semestre";
    $rol = $DB->get_record_sql($query_role)->nombre_rol;

    return $rol;
}

/**
 * Returns a select with every student that's been assigned to a 'profesional', 'practicante' or monitor and "ROL NO PERMITIDO" in case of different role
 *
 * @see make_select_ficha($id, $student_code)
 * @param $id --> student id
 * @param $rol
 * @param $student_code
 * @param $instance_id
 * @return string --> Containing the previous select
 */
function make_select_ficha($id, $rol, $student_code, $instance_id, $actions){

    global $DB;  

    $sel = "";

    $student_code = substr($student_code, 0, 7);

    if (is_null($student_code)) {
        $sel = "selected";
    }

    $asign = "<select name='asignados' id='asignados' style='width:100%'><option $sel>Seleccione un estudiante</option>";

    if(property_exists($actions, 'search_assigned_students_sp')){
        if ($rol == 'profesional_ps') {

            $asign .= process_info_assigned_students(get_asigned_by_profesional($id), $student_code);
    
        } elseif ($rol == 'practicante_ps') {
    
            $asign .= process_info_assigned_students(get_asigned_by_practicante($id), $student_code);
    
        } elseif ($rol == 'monitor_ps') {
    
            $asign .= process_info_assigned_students(get_asigned_by_monitor($id), $student_code);
    
        }elseif ($rol == 'director_prog') {
    
            $asign .= process_info_assigned_students(get_asigned_by_dir_prog($id), $student_code);   
            
        }
    }elseif(property_exists($actions, 'search_student_sp')) {
        $asign .= process_info_assigned_students(get_all_student($instance_id), $student_code);   
    }else{
        $asign .= "El usuario no tiene permisos para buscar estudiantes";
    }
    $asign .= "</select>";
    return $asign;
}

/**
 * Gets all students assigned to a monitor
 *
 * @see get_asigned_by_monitor($id)
 * @param $id --> monitor id
 * @return  Array --> with every stdClass student
 */

function get_asigned_by_monitor($id)
{
    global $DB;

    $semestre = get_current_semester();
    $id_semestre = $semestre->max;

    $query = "SELECT user_moodle.username, user_moodle.firstname, user_moodle.lastname
              FROM {user} AS user_moodle
              INNER JOIN {talentospilos_user_extended} AS user_extended ON user_moodle.id = user_extended.id_moodle_user
              INNER JOIN {talentospilos_monitor_estud} AS monitor_student ON user_extended.id_ases_user = monitor_student.id_estudiante
              WHERE monitor_student.id_monitor = $id AND monitor_student.id_semestre = $id_semestre";

    $result = $DB->get_records_sql($query);

    return $result;
}

/**
 * Gets all students assigned to a 'practicante'
 *
 * @see get_asigned_by_practicante($id)
 * @param $id --> practicant id
 * @return array --> with every student
 */

function get_asigned_by_practicante($id)
{
    global $DB;

    $semestre = get_current_semester();
    $id_semestre = $semestre->max;

    $query = "SELECT rol.id_usuario
              FROM {talentospilos_user_rol} AS rol
              WHERE rol.id_jefe = $id AND rol.id_semestre = $id_semestre AND rol.estado = 1";

    $students = array();

    $result = $DB->get_records_sql($query);

    foreach ($result as $id_mon) {
        $students = array_merge($students, get_asigned_by_monitor($id_mon->id_usuario));
    }
    return $students;
}

//print_r(get_asigned_by_practicante(121));

/**
 * Gets all students assigned to a 'profesional'
 *
 * @see get_asigned_by_profesional($id)
 * @param $id --> professional id
 * @return array --> with every student
 */

function get_asigned_by_profesional($id)
{
    global $DB;

    $semestre = get_current_semester();
    $id_semestre = $semestre->max;

    $query = "SELECT rol.id_usuario
              FROM {talentospilos_user_rol} AS rol
              WHERE rol.id_jefe = $id AND rol.id_semestre = $id_semestre AND rol.estado = 1";

    $students = array();

    $result = $DB->get_records_sql($query);

    foreach ($result as $id_prac) {
        $students = array_merge($students, get_asigned_by_practicante($id_prac->id_usuario));
    }
    return $students;
}

/**
 * Gets all students assigned to a 'director_prog'
 *
 * @see get_asigned_by_dir_prog($id)
 * @param $id --> dirrector id
 * @return array --> with every student
 */
function get_asigned_by_dir_prog($id)       
{
    global $DB;

    $semestre = get_current_semester();
    $id_semestre = $semestre->max;

    $query_program = "SELECT id_programa FROM {talentospilos_user_rol} WHERE id_usuario = $id AND id_semestre = $id_semestre";
    $id_programa = $DB->get_record_sql($query_program)->id_programa;

    $query = "SELECT user_moodle.username, user_moodle.firstname, user_moodle.lastname
              FROM {user} AS user_moodle
              INNER JOIN {talentospilos_user_extended} AS user_extended ON user_moodle.id = user_extended.id_moodle_user
              WHERE user_extended.id_academic_program = $id_programa";

    $result = $DB->get_records_sql($query);

    return $result;

}

/**
 * Gets all students from ASES
 *
 * @see get_asigned_by_dir_prog($id)
 * @param $id --> dirrector id
 * @return array --> with every student
 */
function get_all_student($instance_id)
{
    global $DB;

    $semestre = get_current_semester();
    $id_semestre = $semestre->max;

    $query = "SELECT user_moodle.username, user_moodle.firstname, user_moodle.lastname
              FROM {user} AS user_moodle
              INNER JOIN {talentospilos_user_extended} AS user_extended ON user_moodle.id = user_extended.id_moodle_user
              INNER JOIN {cohort_members} AS cohort_members ON cohort_members.userid = user_extended.id_moodle_user
              INNER JOIN {talentospilos_inst_cohorte} AS instance_cohort ON instance_cohort.id_cohorte = cohort_members.cohortid
              WHERE instance_cohort.id_instancia = $instance_id";

    $result = $DB->get_records_sql($query);

    return $result;

}

/**
 * Function that process the information contained in an array of students and returns a string with option html elements
 * @see process_info_assigne_students($array_students)
 * @param $array_students -> array which contains several student objects
 * @return string containing the students information
 */

function process_info_assigned_students($array_students, $student_code)
{

    $assign = "";

    if (is_null($student_code)) {
        foreach ($array_students as $student) {
            $assign .= "<option>$student->username $student->firstname $student->lastname </option>";
        }
    } else {
        foreach ($array_students as $student) {
            if ($student_code == substr($student->username, 0, 7)) {
                $assign .= "<option selected>$student->username $student->firstname $student->lastname </option>";
            } else {
                $assign .= "<option>$student->username $student->firstname $student->lastname </option>";
            }
        }
    }

    return $assign;

}

/**
 *
 * Returns current semester considering current date
 *
 * @see get_current_semester_today()
 * @return array -->  array object or zero if error
 */

function get_current_semester_today()
{

    global $DB;

    date_default_timezone_set('America/Bogota');
    $today = time();

    $sql_query = "SELECT * FROM {talentospilos_semestre}";
    $array_periods = $DB->get_records_sql($sql_query);

    foreach ($array_periods as $period) {
        if (strtotime($period->fecha_inicio) < $today && strtotime($period->fecha_fin) > $today) {
            return $period;
        }
    }

    return 0;
}

/**
 *
 * Returns current semester considering current date
 *
 * @see get_current_semester_today()
 * @return array -->  array object or zero if error
 */

function get_id_ases_user($id_moodle_user)
{

    global $DB;

    $sql_query = "SELECT * FROM {user} us
    INNER JOIN {talentospilos_user_extended} extended ON us.id = extended.id_moodle_user where id_moodle_user=$id_moodle_user and extended.tracking_status=1;";
    $id_ases = $DB->get_record_sql($sql_query);
    return $id_ases;

}
