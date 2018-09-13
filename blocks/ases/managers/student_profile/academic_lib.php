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
 * @author     Iader E. García Gómez
 * @package    block_ases
 * @copyright  2018 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once dirname(__FILE__) . '/../../../../config.php';
require_once $CFG->dirroot . '/grade/querylib.php';
require_once $CFG->dirroot . '/grade/report/user/lib.php';
require_once $CFG->dirroot . '/grade/lib.php';

/**
 * Returns an html wiht courses and grades for a student in the last semester
 *
 * @see get_grades_courses_student_last_semester($id_student)
 * @param $id_student --> id from {user}
 * @return string html courses
 */

function get_grades_courses_student_last_semester($id_student)
{

    global $DB;

    $query_semestre = "SELECT nombre FROM {talentospilos_semestre} WHERE id = (SELECT MAX(id) FROM {talentospilos_semestre})";
    $sem = $DB->get_record_sql($query_semestre)->nombre;

    $año = substr($sem, 0, 4);

    if (substr($sem, 4, 1) == 'A') {
        $last_semester = $año . '02';
    } else if (substr($sem, 4, 1) == 'B') {
        $last_semester = $año . '08';
    }

    $courses = get_courses_by_student($id_student, $last_semester);

    if (!$courses) {
        $html_courses = "<b>EL ESTUDIANTE NO REGISTRA CURSOS EN EL SEMESTRE ACTUAL</b>";
    } else {
        $html_courses = make_html_courses($courses);
    }
    return $html_courses;
}
//print_r(get_grades_courses_student_last_semester(144));

/**
 * Process info of a courses array and make an html collapsable
 *
 * @see make_html_courses($courses)
 * @param $courses --> array() of stdClass object representing courses and grades for single student
 * @return string --> hmtl text with the info
 */

function make_html_courses($courses)
{
    $html = '';

    foreach ($courses as $course) {

        $html .= "<div class='panel panel-default'>
                    <div class='panel-heading' id = 'academic'>
                        <h4 class='panel-title'>
                        <a id = 'academic_link' data-toggle='collapse' data-parent='#accordion_academic' href='#course_$course->id_course' aria-expanded='false' aria-controls='$course->id_course'>
                            $course->fullname
                        </a>
                        </h4>
                    </div>
                    <div id = 'course_$course->id_course' class='panel-collapse collapse'>
                        <div class = 'panel-body'>
                            $course->descriptions
                        </div>
                    </div>
                  </div>";

    }

    return $html;
}

/**
 * Return courses and grades for a student in the last semester
 *
 * @see get_courses_by_student($id_student, $last_semester)
 * @param $id_student --> student id
 * @param $last_semester --> last semester string identifier
 * @return array --> filled with stdClass objects representing courses and grades for single student
 */

function get_courses_by_student($id_student, $last_semester)
{

    global $DB;

    $query = "SELECT DISTINCT curso.id as id_course,
			                curso.fullname,
			                curso.shortname,
			                to_timestamp(curso.timecreated)::DATE AS time_created
			FROM {course} curso
			INNER JOIN {enrol} role ON curso.id = role.courseid
			INNER JOIN {user_enrolments} enrols ON enrols.enrolid = role.id
			WHERE enrols.userid = $id_student AND SUBSTRING(curso.shortname FROM 15 FOR 6) = '$last_semester'
            ORDER BY time_created DESC";

    $result_query = $DB->get_records_sql($query);
    if (!$result_query) {
        return false;
    }
    $courses_array = array();
    foreach ($result_query as $result) {
        $result->grade = number_format(grade_get_course_grade($id_student, $result->id_course)->grade, 2);
        $result->descriptions = getCoursegradelib($result->id_course, $id_student);
        array_push($courses_array, $result);
    }

    return $courses_array;

}

//print_r(get_courses_by_student(144,false));

function getCoursegradelib($courseid, $userid)
{
    $context = context_course::instance($courseid);

    $gpr = new grade_plugin_return(array('type' => 'report', 'plugin' => 'user', 'courseid' => $courseid, 'userid' => $userid));
    $report = new grade_report_user($courseid, $gpr, $context, $userid);
    reduce_table($report);

    if ($report->fill_table()) {
        return $report->print_table(true);
    }
    return null;
}

/**
 * Reduces course information to display
 *
 * @param &$report --> report object containing information to reduce, such as percentage, range, feedback and contributiontocoursetotal.
 * @return null
 */
function reduce_table(&$report)
{
    $report->showpercentage = false;
    $report->showrange = false;
    $report->showfeedback = false;
    $report->showcontributiontocoursetotal = false;
    $report->setup_table();
}

/**
 * Return historical academic for a student
 *
 * @see get_historic_academic_by_student($id_student)
 * @param $id_student --> student id from {talentospilos_usuario}
 * @return string --> html
 */

function get_historic_academic_by_student($id_student)
{
    $semesters = get_historical_semesters_by_student($id_student);
    // print_r($semesters);
    if (!$semesters) {
        $html_courses = "<b>EL ESTUDIANTE NO REGISTRA HISTÓRICO ACADÉMICO</b>";
    } else {
        $html_courses = make_html_semesters($semesters);
    }

    return $html_courses;

}

// get_historic_academic_by_student(320);

/**
 * Return an array of academic semesters
 *
 * @see get_historical_semesters_by_student($id_student)
 * @param $id_student --> student id from {talentospilos_usuario}
 * @return array --> array with the semesters info
 */

function get_historical_semesters_by_student($id_student)
{
    global $DB;

    $sql_query = "SELECT *
                  FROM {talentospilos_history_academ}
                  WHERE id_estudiante = $id_student";

    $result = $DB->get_records_sql($sql_query);

    if (!$result) {
        return false;
    }

    $semester_info = array();

    foreach ($result as $register) {

        $semester = new stdClass;
        $id = $register->id;
        $id_semester = $register->id_semestre;
        $id_programa = $register->id_programa;
        $semester->json_materias = $register->json_materias;

        //search semester name
        $query_semestre = "SELECT nombre FROM {talentospilos_semestre} WHERE id = $id_semester";
        $semester_name = $DB->get_record_sql($query_semestre)->nombre;

        //search program name
        $query_program = "SELECT nombre FROM {talentospilos_programa} WHERE id = $id_programa";
        $program_name = $DB->get_record_sql($query_program)->nombre;
        $semester->program_name = $program_name;

        $semester->promedio_semestre = $register->promedio_semestre;
        $semester->promedio_acumulado = $register->promedio_acumulado;

        //validate bajo rendimiento
        $query_bajo = "SELECT numero_bajo as numero FROM {talentospilos_history_bajos} WHERE id_history = $id";
        $bajo = $DB->get_record_sql($query_bajo);

        if (!$bajo) {
            $semester->bajo = false;
        } else {
            $semester->bajo = $bajo->numero;
        }

        //validate estimulo
        $query_estimulo = "SELECT puesto_ocupado as puesto FROM {talentospilos_history_estim} WHERE id_history = $id";
        $estimulo = $DB->get_record_sql($query_estimulo);

        if (!$estimulo) {
            $semester->estimulo = false;
        } else {
            $semester->estimulo = $estimulo->puesto;
        }

        //validate cancelacion
        $query_cancelacion = "SELECT fecha_cancelacion FROM {talentospilos_history_cancel} WHERE id_history = $id";
        $cancelacion = $DB->get_record_sql($query_cancelacion);

        if (!$cancelacion) {
            $semester->cancelacion = false;
        } else {
            $semester->cancelacion = date("d / M / Y", $cancelacion->fecha_cancelacion);
        }

        //validate register
        if (!array_key_exists($semester_name, $semester_info)) {
            $semester_info[$semester_name] = array();
        }

        array_push($semester_info[$semester_name], $semester);

    }

    krsort($semester_info);
    return $semester_info;

}

/**
 * Return an array of academic semesters
 *
 * @see make_html_semesters($semesters)
 * @param $semesters --> array of Objects semester
 * @return String --> html
 */

function make_html_semesters($semesters)
{
    $html = "";

    foreach ($semesters as $semester_name => $semester) {
        foreach ($semester as $registro) {
            $descriptions = "";
            $promedio_semestre = $registro->promedio_semestre;
            $promedio_acumulado = $registro->promedio_acumulado;

            if($promedio_semestre == null){
                $promedio_semestre = "NO REGISTRA";
            }

            if($promedio_acumulado == null){
                $promedio_acumulado = "NO REGISTRA";
            }
            
            $descriptions .= "<div id = 'panel_academic' class = 'panel panel-default'><div id = 'info_course' class = 'row'>
                                <div class = 'col-md-4'>Programa: <b>$registro->program_name</b></div>
                                <div class = 'col-md-4'>Promedio Semestre: $promedio_semestre</div>
                                <div class = 'col-md-4'>Promedio Acumulado: $promedio_acumulado</div>
                             </div>";
            $div_bajo = "";
            $div_estimulo = "";
            $div_cancelacion = "";

            if ($registro->bajo != false) {
                $div_bajo .= "<div id = 'bajo' class = 'col-md-8 bajo'>Cae en bajo rendimiento número $registro->bajo.</div>";
            }

            if ($registro->estimulo != false) {
                $div_bajo .= "<div id = 'estimulo' class = 'col-md-8 estimulo'>Gana estimulo ocupando el puesto $registro->estimulo.</div>";
            }

            if ($registro->cancelacion != false) {
                $div_bajo .= "<div id = 'cancelacion' class = 'col-md-8 cancelacion'>Cancela semestre. Fecha de cancelación: $registro->cancelacion.</div>";
            }

            $descriptions .= "<div class = 'row'>
                                $div_bajo
                                $div_cancelacion
                                $div_estimulo
                              </div> <hr>";

            $materias = json_decode($registro->json_materias);

            if ($materias === null) {
                $descriptions .= "NO REGISTRA MATERIAS EN ESTE SEMESTRE";
            } else {
                $descriptions .= "<div class = 'row'> <b>
                <div class = 'col-md-4'>
                   MATERIA
                </div>
             <div class = 'col-md-2'>
                    CÓDIGO
             </div>
               <div class = 'col-md-2'>
                    NOTA
               </div>
               <div class = 'col-md-2'>
                    CREDITOS
               </div>
                </b>
            </div>";

                foreach ($materias as $materia) {
                    $perdida = "";
                    if(is_float($materia->nota + 0) and $materia->nota < 3){
                        $perdida = "perdida";
                    }
                    $descriptions .= "<div class = 'row $perdida'>
                    <div class = 'col-md-4'>
                        $materia->nombre_materia
                    </div>
                    <div class = 'col-md-2'>
                         $materia->codigo_materia
                    </div>
                    <div class = 'col-md-2 '>
                         $materia->nota
                    </div>
                    <div class = 'col-md-2'>
                         $materia->creditos
                    </div>
                    
                 </div>";
                }

            }

            $descriptions .= "</div>";

            $html .= "  <div class='panel panel-default'>
                      <div class='panel-heading' id = 'academic'>
                          <h4 class='panel-title'>
                          <a id = 'academic_link' data-toggle='collapse' data-parent='#accordion_academic_historic' href='#register_$semester_name' aria-expanded='false' aria-controls='$semester_name'>
                              Semestre $semester_name
                          </a>
                          </h4>
                      </div>
                      <div id = 'register_$semester_name' class='panel-collapse collapse'>
                          <div class = 'panel-body'>
                              $descriptions
                          </div>
                      </div>
                    </div>  ";
        }
    }

    return $html;
}

/**
 * Get the weigthed average from one student
 * @see get_promedio_ponderado($id_estudiante, $id_programa)
 * @param $id_estudiante --> id from {talentospilos_usuario}
 * @param $id_programa --> id from {talentospilos_programa}
 * @return float --> weigthed average
 */

function get_promedio_ponderado($id_estudiante, $id_programa)
{

    global $DB;

    $query = "SELECT promedio_acumulado as prom
	      FROM {talentospilos_history_academ}
              WHERE id_estudiante = $id_estudiante AND id_programa = $id_programa and 
	            id_semestre = (SELECT MAX(id_semestre) FROM {talentospilos_history_academ} WHERE id_estudiante = $id_estudiante AND id_programa = $id_programa) ";
    $result = $DB->get_record_sql($query);

    if (!$result) {
        $promedio = "NO REGISTRA";
    } else {
        $promedio = $result->prom;
    }

    return $promedio;

}

/**
 * Get the number of academic incentives that one student win in a program
 * @see get_estimulos($id_estudiante, $id_programa)
 * @param $id_estudiante, $id_programa --> id from {talentospilos_usuario}
 * @param $id_programa --> id from {talentospilos_programa}
 * @return int --> number of academic incentives
 */

function get_estimulos($id_estudiante, $id_programa)
{
    global $DB;

    $query = "SELECT COUNT(estim.id) as estimulos
              FROM {talentospilos_history_academ} academ INNER JOIN {talentospilos_history_estim} estim ON academ.id = estim.id_history
              WHERE academ.id_estudiante = $id_estudiante AND academ.id_programa = $id_programa";

    $result = $DB->get_record_sql($query);

    if (!$result) {
        $estimulos = 0;
    } else {
        $estimulos = $result->estimulos;
    }

    return $estimulos;
}

/**
 * Get the number of poor academic performance from a student in a program
 * @see get_bajos_rendimientos($id_estudiante, $id_programa)
 * @param $id_estudiante --> id from {talentospilos_usuario}
 * @param $id_programa --> id from {talentospilos_programa}
 * @return int --> number
 */

function get_bajos_rendimientos($id_estudiante, $id_programa)
{

    global $DB;

    $query = "SELECT COUNT(bajo.id) as bajos
    FROM {talentospilos_history_academ} academ INNER JOIN {talentospilos_history_bajos} bajo ON academ.id = bajo.id_history
    WHERE academ.id_estudiante = $id_estudiante AND academ.id_programa = $id_programa";

    $result = $DB->get_record_sql($query);

    if (!$result) {
        $bajos = 0;
    } else {
        $bajos = $result->bajos;
    }

    return $bajos;
}
