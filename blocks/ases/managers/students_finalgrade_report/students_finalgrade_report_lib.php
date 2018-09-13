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
 * @author     Juan Pablo Moreno Muñoz
 * @package    block_ases
 * @copyright  2018 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');
require_once '../managers/periods_management/periods_lib.php';


function get_students_and_finalgrades($instance_id){
    global $DB;

    $students_finalgrades_array = array();

    $query = "SELECT DISTINCT row_number() over(), materias_criticas.materiacr_id, substring(courses.shortname from 0 for 14) AS course_code, courses.fullname, 
                    (SELECT concat_ws(' ',firstname,lastname) AS fullname
                        FROM
                        (SELECT usuario.firstname,
                                usuario.lastname,
                                userenrol.timecreated
                        FROM {course} cursoP
                        INNER JOIN {context} cont ON cont.instanceid = cursoP.id
                        INNER JOIN {role_assignments} rol ON cont.id = rol.contextid
                        INNER JOIN {user} usuario ON rol.userid = usuario.id
                        INNER JOIN {enrol} enrole ON cursoP.id = enrole.courseid
                        INNER JOIN {user_enrolments} userenrol ON (enrole.id = userenrol.enrolid
                                                                        AND usuario.id = userenrol.userid)
                        WHERE cont.contextlevel = 50
                            AND rol.roleid = 3
                            AND cursoP.id = courses.id
                        ORDER BY userenrol.timecreated ASC
                        LIMIT 1) AS subc) AS nombre_profe, 
                    student_id, student_name, student_lastname, student_code
                FROM
                {course} AS courses  

                INNER JOIN

                (SELECT DISTINCT enrols.courseid AS id_course, students.id AS student_id, students.firstname AS student_name, students.lastname AS student_lastname, 
                substring(students.username from 0 for 8) AS student_code
                FROM {cohort_members} AS members 
                INNER JOIN {cohort} AS cohorts ON cohorts.id = members.cohortid
                INNER JOIN {user_enrolments} AS enrolments ON  enrolments.userid = members.userid
                INNER JOIN {user} AS students ON enrolments.userid = students.id
                INNER JOIN {enrol} AS enrols ON enrols.id = enrolments.enrolid
                WHERE members.cohortid IN (SELECT id_cohorte
                                                FROM   {talentospilos_inst_cohorte}
                                                WHERE  id_instancia = $instance_id)) AS cursos_ases

                ON courses.id = cursos_ases.id_course  

                INNER JOIN  

                (SELECT id AS materiacr_id
                FROM {course} AS courses 
                INNER JOIN (SELECT codigo_materia 
                    FROM {talentospilos_materias_criti}) AS materias_criticas
                ON materias_criticas.codigo_materia = SUBSTR(courses.shortname, 4, 7)
                WHERE SUBSTR(courses.shortname, 15, 4) = '2018') AS materias_criticas

                ON cursos_ases.id_course = materias_criticas.materiacr_id";

    $records = $DB->get_records_sql($query);

    foreach ($records as $record) {
        $student_id = $record->student_id;
        $course_id = $record->materiacr_id;
        $record->finalgrade = get_finalgrade_by_student_and_course($student_id, $course_id);
        $record->grades = get_students_grades($student_id, $course_id);

        array_push($students_finalgrades_array, $record);
    }

    return $students_finalgrades_array;
}


function get_finalgrade_by_student_and_course($student_id, $course_id){
    global $DB;
    
    $query = "SELECT finalgrade 
                FROM {grade_grades} AS grades
                INNER JOIN {grade_items} items ON items.id = grades.itemid
                WHERE items.itemtype = 'course' AND grades.userid = $student_id 
                        AND items.courseid = $course_id";

    $finalgrade = $DB->get_record_sql($query)->finalgrade;

    return number_format($finalgrade, 1);
}

function get_students_grades($student_id, $course_id){
    global $DB;

    $grades = "";

    $query = "SELECT row_number() over(), substring(itemname from 0 for 5) AS it_name, finalgrade 
                FROM {grade_grades} AS grades
                INNER JOIN {grade_items} items ON items.id = grades.itemid
                WHERE items.itemtype = 'mod' AND grades.userid = $student_id 
                        AND items.courseid = $course_id AND grades.finalgrade IS NOT NULL";

    $records = $DB->get_records_sql($query);

    foreach ($records as $record){
        $formatted_grade = number_format($record->finalgrade, 1);
        $grades.= "$record->it_name : $formatted_grade ";
    }

    return $grades;    
}

function get_datatable_array_for_finalgrade_report($instance_id){
    $columns = array();
		array_push($columns, array("title"=>"Código del curso", "name"=>"course_code", "data"=>"course_code"));
		array_push($columns, array("title"=>"Nombre del curso", "name"=>"fullname", "data"=>"fullname"));
		array_push($columns, array("title"=>"Docente", "name"=>"nombre_profe", "data"=>"nombre_profe"));
		array_push($columns, array("title"=>"Nombre del estudiante", "name"=>"student_name", "data"=>"student_name"));
        array_push($columns, array("title"=>"Apellido del estudiante", "name"=>"student_lastname", "data"=>"student_lastname"));
        array_push($columns, array("title"=>"Notas", "name"=>"grades", "data"=>"grades"));
        array_push($columns, array("title"=>"Nota final parcial", "name"=>"finalgrade", "data"=>"finalgrade"));

		$data = array(
					"bsort" => false,
					"columns" => $columns,
					"data" => get_students_and_finalgrades($instance_id),
					"language" => 
                	 array(
                    	"search"=> "Buscar:",
                    	"oPaginate" => array(
                        	"sFirst"=>    "Primero",
                        	"sLast"=>     "Último",
                        	"sNext"=>     "Siguiente",
                        	"sPrevious"=> "Anterior"
                    		),
                		"sProcessing"=>     "Procesando...",
                		"sLengthMenu"=>     "Mostrar _MENU_ registros",
                    	"sZeroRecords"=>    "No se encontraron resultados",
                    	"sEmptyTable"=>     "Ningún dato disponible en esta tabla",
                    	"sInfo"=>           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    	"sInfoEmpty"=>      "Mostrando registros del 0 al 0 de un total de 0 registros",
                    	"sInfoFiltered"=>   "(filtrado de un total de _MAX_ registros)",
                    	"sInfoPostFix"=>    "",
                    	"sSearch"=>         "Buscar:",
                    	"sUrl"=>            "",
                    	"sInfoThousands"=>  ",",
                    	"sLoadingRecords"=> "Cargando...",
                 	),
					"order"=> array(0, "desc")

                );
    return $data;
}