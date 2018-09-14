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
 * Ases block
 *
 * @author     Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  2017 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once dirname(__FILE__) . '/../../../../config.php';

/**
 * Gets ASES id_user given student code associated to moodle username
 *
 * @see get_ases_id_by_code($code)
 * @param $code --> code that represent username of moodle user
 * @return $id or false
 */

function get_ases_id_by_code($code)
{
    global $DB;

    $sql_query = "SELECT MAX(id) as id_moodle FROM {user} WHERE username LIKE '" . $code . "%';";

    $id_moodle = $DB->get_record_sql($sql_query);
    //print_r($id_moodle);
    if($id_moodle->id_moodle){
        $id_moodle = $id_moodle->id_moodle;
    }else{
        return false;
    }

    $sql_query = "SELECT id_ases_user as id FROM {talentospilos_user_extended} WHERE id_moodle_user =" . $id_moodle;

    $ases_user = $DB->get_record_sql($sql_query);

    if (!$ases_user) {
        return false;
    } else {
        return $ases_user->id;
    }
}

/**
 * Gets id of program given program code
 *
 * @see get_id_program($code)
 * @param $username --> student id associated to moodle user
 * @return int|boolean
 */

function get_id_program($code)
{
    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_programa} WHERE cod_univalle = " . $code . " LIMIT 1;";

    $program = $DB->get_record_sql($sql_query);

    if (!$program) {
        return false;
    } else {
        return $program->id;
    }
}

/**
 * Gets id of semestre given semestre name
 *
 * @see get_id_semester($name)
 * @param $name --> name of semestre
 * @return int|boolean
 */

function get_id_semester($name)
{
    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_semestre} WHERE nombre = '" . $name . "';";

    $semestre = $DB->get_record_sql($sql_query);

    if (!$semestre) {
        return false;
    } else {
        return $semestre->id;
    }
}

/**
 * Creates an associative array given a header from a CSV file
 *
 * @see getAssociativeTitles ($titlesPos)
 * @param $titlesPos --> header from CSV
 * @return array
 */
function getAssociativeArray($array)
{

    $associativeArray = array();

    foreach ($array as $key => $value) {
        $associativeArray[$value] = $key;
    }

    return $associativeArray;
}

/**
 * validate a register in table talentospilos_historic_academ
 *
 * @see validate_historic_register($id_student, $id_program, $id_semester)
 * @param $id_student --> id from table talentospilos_usuario
 * @param $id_program --> id from table talentospilos_semestre
 * @param $id_semester --> id from
 * @return Object|boolean
 */
function validate_historic_register($id_student, $id_program, $id_semester)
{

    global $DB;

    $sql_query = "SELECT id FROM {talentospilos_history_academ} WHERE id_estudiante = $id_student AND id_programa = $id_program AND id_semestre = $id_semester";
    return $DB->get_record_sql($sql_query);

}

/**
 * Update or insert a register in table talentospilos_historic_academ
 *
 * @see update_historic_academic($id_student, $id_program, $id_semester, $average, $overall_average)
 * @param $id_student --> id from table talentospilos_usuario
 * @param $id_program --> id from table talentospilos_semestre
 * @param $id_semester --> id from
 * @param $average --> average
 * @param $overall_average --> header from CSV
 * @return array
 */

function update_historic_academic($id_student, $id_program, $id_semester, $average, $overall_average)
{

    global $DB;

    //validate existence
    $result = validate_historic_register($id_student, $id_program, $id_semester);
    $object_historic = new StdClass;

    if($average == ''){
        $average = null;
    }
    if($overall_average == ''){
        $overall_average = null;
    }
    if (!$result) {
        //INSERTION

        $object_historic->id_estudiante = $id_student;
        $object_historic->id_programa = $id_program;
        $object_historic->id_semestre = $id_semester;
        $object_historic->promedio_semestre = $average;
        $object_historic->promedio_acumulado = $overall_average;

        $insert = $DB->insert_record('talentospilos_history_academ', $object_historic, true);

        if ($insert) {
            return $insert;
        } else {
            return false;
        }

    } else {
        //UPDATE
        $id_historic = $result->id;

        $object_historic->id = $id_historic;
        $object_historic->id_estudiante = $id_student;
        $object_historic->id_programa = $id_program;
        $object_historic->id_semestre = $id_semester;
        $object_historic->promedio_semestre = $average;
        $object_historic->promedio_acumulado = $overall_average;

        $update = $DB->update_record('talentospilos_history_academ', $object_historic);

        if ($update) {
            return $id_historic;
        } else {
            return false;
        }

    }

}

/**
 * Update or insert a register in table talentospilos_historic_academ with this JSON_materias
 *
 * @see update_historic_materias($id_student, $id_program, $id_semester, $json_materias)
 * @param $id_student --> id from table talentospilos_usuario
 * @param $id_program --> id from table talentospilos_semestre
 * @param $id_semester --> id from
 * @param $json_materias --> JSON string
 * @return boolean
 */

function update_historic_materias($id_student, $id_program, $id_semester, $json_materias)
{
    global $DB;
    $object_historic = new StdClass;

    //validate existence
    $result = validate_historic_register($id_student, $id_program, $id_semester);
    
    if (!$result) {
        //INSERTION

        $object_historic->id_estudiante = $id_student;
        $object_historic->id_programa = $id_program;
        $object_historic->id_semestre = $id_semester;
        $object_historic->json_materias = $json_materias;

        $insert = $DB->insert_record('talentospilos_history_academ', $object_historic, true);

        if ($insert) {
            return true;
        } else {
            return false;
        }

    } else {
        //UPDATE
        $id_historic = $result->id;

        $object_historic->id = $id_historic;
        $object_historic->id_estudiante = $id_student;
        $object_historic->id_programa = $id_program;
        $object_historic->id_semestre = $id_semester;
        $object_historic->json_materias = $json_materias;

        $update = $DB->update_record('talentospilos_history_academ', $object_historic);

        if ($update) {
            return true;
        } else {
            return false;
        }

    }

}

/**
 * Update or insert a register in table talentospilos_history_cancel
 *
 * @see update_historic_cancel($id_historic, $cancel_date)
 * @param $id_historic --> id from table {talentospilos_history_academ}
 * @param $cancel_date --> date of cancelation
 * @return boolean
 */
function update_historic_cancel($id_historic, $cancel_date)
{
    global $DB;
    $object_cancel = new StdClass;

    //validate exitence
    $sql_query = "SELECT id FROM {talentospilos_history_cancel} WHERE id_history = $id_historic";
    $result = $DB->get_record_sql($sql_query);

    if (!$result) {
        //INSERTION
        $object_cancel->id_history = $id_historic;
        $object_cancel->fecha_cancelacion = strtotime($cancel_date);

        $insert = $DB->insert_record('talentospilos_history_cancel', $object_cancel, false);

        if ($insert) {
            return true;
        } else {
            return false;
        }

    } else {
        //UPDATE
        $id_register = $result->id;
        $object_cancel->id = $id_register;
        $object_cancel->id_history = $id_historic;
        $object_cancel->fecha_cancelacion = strtotime($cancel_date);
        //print_r($object_cancel);

        $update = $DB->update_record('talentospilos_history_cancel', $object_cancel);

        if ($update) {
            return true;
        } else {
            return false;
        }

    }

}

/**
 * Update or insert a register in table talentospilos_history_bajo
 *
 * @see update_historic_bajo($id_historic, $num_bajo)
 * @param $id_historic --> id from table {talentospilos_history_academ}
 * @param $num_bajo --> number of bajo rendimiento
 * @return boolean
 */
function update_historic_bajo($id_historic, $num_bajo)
{
    global $DB;
    $object_bajo = new StdClass;
    
    //validate exitence
    $sql_query = "SELECT id FROM {talentospilos_history_bajos} WHERE id_history = $id_historic";
    $result = $DB->get_record_sql($sql_query);

    if (!$result) {
        //INSERTION
        $object_bajo->id_history = $id_historic;
        $object_bajo->numero_bajo = $num_bajo;

        $insert = $DB->insert_record('talentospilos_history_bajos', $object_bajo, false);

        if ($insert) {
            return true;
        } else {
            return false;
        }

    } else {
        //UPDATE
        $id_register = $result->id;
        $object_bajo->id = $id_register;
        $object_bajo->id_history = $id_historic;
        $object_bajo->numero_bajo = $num_bajo;

        $update = $DB->update_record('talentospilos_history_bajos', $object_bajo);

        if ($update) {
            return true;
        } else {
            return false;
        }

    }

}

/**
 * Update or insert a register in table talentospilos_history_estim
 *
 * @see update_historic_estimulo($id_historic, $puesto)
 * @param $id_historic --> id from table {talentospilos_history_academ}
 * @param $puesto --> number of bajo rendimiento
 * @return boolean
 */
function update_historic_estimulo($id_historic, $puesto)
{
    global $DB;
    $object_estimulo = new StdClass;
    
    //validate exitence
    $sql_query = "SELECT id FROM {talentospilos_history_estim} WHERE id_history = $id_historic";
    $result = $DB->get_record_sql($sql_query);

    if (!$result) {
        //INSERTION
        $object_estimulo->id_history = $id_historic;
        $object_estimulo->puesto_ocupado = $puesto;

        $insert = $DB->insert_record('talentospilos_history_estim', $object_estimulo, false);

        if ($insert) {
            return true;
        } else {
            return false;
        }

    } else {
        //UPDATE
        $id_register = $result->id;
        $object_estimulo->id = $id_register;
        $object_estimulo->id_history = $id_historic;
        $object_estimulo->puesto_ocupado = $puesto;

        $update = $DB->update_record('talentospilos_history_estim', $object_estimulo);

        if ($update) {
            return true;
        } else {
            return false;
        }

    }

}
