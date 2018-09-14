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
 * @copyright  2017 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*
 * Academic reports module queries (módulo académico)
 */
require_once(__DIR__ . '/../../../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/grade_categories/grader_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/periods_management/periods_lib.php'; 


/**
 * Gets all ASES students with items qualifications 'perdidos' 
 * 
 * @see studentsWithLoses($instance)
 * @param $instance --> id instancia
 * @return array --> Array filled with students information
 */
function studentsWithLoses($instance){
	global $DB;

	$semestre = get_current_semester();
    $sem = $semestre->nombre;

    $año = substr($sem,0,4);

    if(substr($sem,4,1) == 'A'){
        $semestre = $año.'02';
    }else if(substr($sem,4,1) == 'B'){
        $semestre = $año.'08';
	}
	

	$query = "  SELECT     estudiantes.*,
                Count(grades.id) AS cantidad
            FROM       (
                        SELECT     user_m.id,
                                    substring(user_m.username from 1 FOR 7) AS codigo,
                                    user_m.firstname,
                                    user_m.lastname,
                                    user_m.username
                        FROM       {user} user_m
                        INNER JOIN {talentospilos_user_extended} extended
                        ON         user_m.id = extended.id_moodle_user
                        INNER JOIN {talentospilos_usuario} user_t
                        ON         extended.id_ases_user = user_t.id
                        INNER JOIN {talentospilos_est_estadoases} estado_u
                        ON         user_t.id = estado_u.id_estudiante
                        INNER JOIN {talentospilos_estados_ases} estados
                        ON         estados.id = estado_u.id_estado_ases
                        WHERE      estados.nombre = 'seguimiento'
                        INTERSECT
                        SELECT     user_m.id,
                                    substring(user_m.username FROM 1 FOR 7) AS codigo,
                                    user_m.firstname,
                                    user_m.lastname,
                                    user_m.username
                        FROM       {user} user_m
                        INNER JOIN {cohort_members} memb
                        ON         user_m.id = memb.userid
                        WHERE      memb.cohortid IN
                                    (
                                            SELECT id_cohorte
                                            FROM   {talentospilos_inst_cohorte}
                                            WHERE  id_instancia = $instance) ) estudiantes
            INNER JOIN {grade_grades} grades
            ON         estudiantes.id = grades.userid
            INNER JOIN {grade_items} items
            ON         grades.itemid = items.id
            INNER JOIN {course} curso
            ON         curso.id = items.courseid
            WHERE      substring(curso.shortname FROM 15 FOR 6) = '$semestre'
            AND        grades.finalgrade < 3
            GROUP BY   estudiantes.id,
                estudiantes.codigo,
                estudiantes.firstname,
                estudiantes.lastname,
                estudiantes.username ";

	$result = $DB->get_records_sql($query);
	return $result;
}	

/**
 * Returns an HTML table containing all ASES students with items qualifications 'perdidos' 
 * 
 * @see getReportStudents($instance)
 * @param $instance --> id instancia
 * @return string -->  Html string with students information table 
 */
function getReportStudents($instance){

	$students = studentsWithLoses($instance);
	$string_html = "<table id = 'students'>
				<thead>
					<tr>
                        <th> Código</th>
						<th> Nombre </th>
						<th> Apellidos </th>
						<th> Número de items perdidos </th>
                    </tr>
				</thead>";

	foreach ($students as $student) {
		$string_html.= "<tr>
							<td>$student->codigo</td>
							<td>$student->firstname</td>
							<td>$student->lastname</td>
							<td class='lastC' id = '$student->username' >$student->cantidad</td>
						</tr>";
	}

	$string_html.= "</table>";
	return $string_html;
}



/**
 * Returns a String containing all lost students grades
 * 
 * @see get_loses_by_student($instance)
 * @param $username --> username instancia
 * @return string --> HTML students information table 
 */
function get_loses_by_student($username){
	global $DB;

	$query = "SELECT items.id, curso.fullname, items.itemname, grades.finalgrade
			  FROM {user} user_m INNER JOIN {grade_grades} grades ON user_m.id = grades.userid
		INNER JOIN {grade_items} items ON grades.itemid = items.id 
		INNER JOIN {course} curso ON curso.id = items.courseid
			  WHERE user_m.username = '$username' AND  grades.finalgrade < 3";

	$result = $DB->get_records_sql($query);
	$text = "";

	foreach($result as $nota){
		if(!$nota->itemname){
			$query_name = "SELECT cat.fullname as name
						   FROM {grade_items} item INNER JOIN {grade_categories} cat ON item.iteminstance = cat.id
						   WHERE item.id = $nota->id";
			$nota->itemname = $DB->get_record_sql($query_name)->name;
		}
		$note = round($nota->finalgrade,2);
		$text.="$nota->fullname $nota->itemname: $note\n";
	}
	return $text;
}

/**
 * Function that given a logged user id, returns an array of the courses with enrolled users in an instance.
 * @see get_courses_for_report($user_id)
 * @param $user_id -> ID of the logged user
 * @return array 
 */

function get_courses_for_report($user_id){
    global $DB;
    
    $semestre_object = get_current_semester();
    $sem = $semestre_object->nombre;
    $id_semestre = $semestre_object->max;
    $año = substr($sem,0,4);

    if(substr($sem,4,1) == 'A'){
        $semestre = $año.'02';
    }else if(substr($sem,4,1) == 'B'){
        $semestre = $año.'08';
    }
	//print_r($semestre);

	$intersect = "";
	
	$user_role = get_role_ases($user_id);

	if($user_role == "monitor_ps"){

		$intersect = " INTERSECT 
		                SELECT user_m.id
		                FROM {user} user_m
                        INNER JOIN {talentospilos_user_extended} extended ON user_m.id = extended.id_moodle_user
                        INNER JOIN {talentospilos_monitor_estud} mon_es ON extended.id_ases_user = mon_es.id_estudiante
                        WHERE mon_es.id_semestre = $id_semestre AND mon_es.id_monitor = $user_id
                        ";
    }
    elseif($user_role == "practicante_ps"){
        
        $intersect = " INTERSECT 
                        SELECT DISTINCT user_m.id
                        FROM {user} user_m
                        INNER JOIN {talentospilos_user_extended} extended ON user_m.id = extended.id_moodle_user
                        INNER JOIN {talentospilos_monitor_estud} mon_es ON extended.id_ases_user = mon_es.id_estudiante
                        INNER JOIN {talentospilos_user_rol} us_rol ON mon_es.id_monitor = us_rol.id_usuario 
                        WHERE mon_es.id_semestre = $id_semestre AND us_rol.id_semestre = $id_semestre AND us_rol.id_jefe = $user_id 
                        ";
    }
    elseif($user_role == "profesional_ps"){
        
        $intersect = " INTERSECT 
                        SELECT DISTINCT user_m.id
                        FROM {user} user_m
                        INNER JOIN {talentospilos_user_extended} extended ON user_m.id = extended.id_moodle_user
                        INNER JOIN {talentospilos_monitor_estud} mon_es ON extended.id_ases_user = mon_es.id_estudiante
                        INNER JOIN {talentospilos_user_rol} us_rol ON mon_es.id_monitor = us_rol.id_usuario
                        INNER JOIN {talentospilos_user_rol} us_rol_prof ON us_rol.id_jefe = us_rol_prof.id_usuario
                        WHERE mon_es.id_semestre = $id_semestre AND us_rol.id_semestre = $id_semestre AND us_rol_prof.id_semestre = $id_semestre AND us_rol_prof.id_jefe = $user_id 
                        ";
    }	

    $query_courses = "
	SELECT DISTINCT curso.id, curso.fullname, curso.shortname          
		FROM {course} curso
		INNER JOIN {enrol} ROLE ON curso.id = role.courseid
		INNER JOIN {user_enrolments} enrols ON enrols.enrolid = role.id
		WHERE SUBSTRING(curso.shortname FROM 15 FOR 6) = '$semestre' AND enrols.userid IN
			(SELECT user_m.id
				FROM  {user} user_m
                INNER JOIN {talentospilos_user_extended} extended ON user_m.id = extended.id_moodle_user
				INNER JOIN {talentospilos_usuario} user_t ON extended.id_ases_user = user_t.id
				INNER JOIN {talentospilos_est_estadoases} estado_u ON user_t.id = estado_u.id_estudiante
				INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
				WHERE estados.nombre = 'seguimiento' 
				$intersect			
				)";
    $result = $DB->get_records_sql($query_courses);
        
    return $result;
}

/**
 * Function that given a logged user id, returns an array of the courses with enrolled users in an instance.
 * @see get_courses_for_report($user_id)
 * @param $user_id -> ID of the logged user
 * @return array 
 */
function get_courses_report($user_id){
	$courses = get_courses_for_report($user_id);

	$string_html = "<table id = 'courses'>
						<thead>
							<tr>
								<th> Nombre </th>
								<th> Nombre Completo</th>
							</tr>
						</thead>"; 

	foreach ($courses as $course) {
		$string_html.= "<tr class='curso_reporte' id='$course->id'>
						<td>$course->fullname</td>
						<td>$course->shortname</td>
		        		</tr>";
	}

    $string_html.= "</table>";
	return $string_html;

}

/** 
 * Function that returns a course with all its information given the course id and the id of the logged user
 * @param $course_id
 * @param $user_id
 * @return object --> Representing the course
 */

function get_info_course_for_reports($course_id, $user_id){
    global $DB;

	$semestre_object = get_current_semester();
	$id_semestre = $semestre_object->max;


    $intersect = "";

    $user_role = get_role_ases($user_id);

    if($user_role == "monitor_ps"){

		$intersect = " INTERSECT 
		                SELECT user_m.id
		                FROM {user} user_m
                        INNER JOIN {talentospilos_user_extended} extended ON user_m.id = extended.id_moodle_user
                        INNER JOIN {talentospilos_monitor_estud} mon_es ON extended.id_ases_user = mon_es.id_estudiante
                        WHERE mon_es.id_semestre = $id_semestre AND mon_es.id_monitor = $user_id 
                        ";
    }
    elseif($user_role == "practicante_ps"){
        
        $intersect = " INTERSECT 
                        SELECT DISTINCT user_m.id
                        FROM {user} user_m
                        INNER JOIN {talentospilos_user_extended} extended ON user_m.id = extended.id_moodle_user
                        INNER JOIN {talentospilos_monitor_estud} mon_es ON extended.id_ases_user = mon_es.id_estudiante
                        INNER JOIN {talentospilos_user_rol} us_rol ON mon_es.id_monitor = us_rol.id_usuario 
                        WHERE mon_es.id_semestre = $id_semestre AND us_rol.id_semestre = $id_semestre AND us_rol.id_jefe = $user_id 
                        ";
    }
    elseif($user_role == "profesional_ps"){
        
        $intersect = " INTERSECT 
                        SELECT DISTINCT user_m.id
                        FROM {user} user_m
                        INNER JOIN {talentospilos_user_extended} extended ON user_m.id = extended.id_moodle_user
                        INNER JOIN {talentospilos_monitor_estud} mon_es ON extended.id_ases_user = mon_es.id_estudiante
                        INNER JOIN {talentospilos_user_rol} us_rol ON mon_es.id_monitor = us_rol.id_usuario
                        INNER JOIN {talentospilos_user_rol} us_rol_prof ON us_rol.id_jefe = us_rol_prof.id_usuario
                        WHERE mon_es.id_semestre = $id_semestre AND us_rol.id_semestre = $id_semestre AND us_rol_prof.id_semestre = $id_semestre AND us_rol_prof.id_jefe = $user_id 
                        ";
    }

    $course = $DB->get_record_sql("SELECT fullname FROM {course} WHERE id = $course_id");
    
    $query_teacher="SELECT concat_ws(' ',firstname,lastname) AS fullname
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
                AND cursoP.id = $course_id
              ORDER BY userenrol.timecreated ASC
              LIMIT 1) AS subc";
    $professor = $DB->get_record_sql($query_teacher);
    
    $query_students = "SELECT usuario.id, usuario.firstname, usuario.lastname, usuario.username
                    FROM {user} usuario INNER JOIN {user_enrolments} enrols ON usuario.id = enrols.userid 
                    INNER JOIN {enrol} enr ON enr.id = enrols.enrolid 
                    INNER JOIN {course} curso ON enr.courseid = curso.id  
                    WHERE curso.id= $course_id AND usuario.id IN (SELECT user_m.id
                                                                 FROM  {user} user_m
                                                                 INNER JOIN {talentospilos_user_extended} extended ON user_m.id = extended.id_moodle_user
                                                                 INNER JOIN {talentospilos_usuario} user_t ON extended.id_ases_user = user_t.id
                                                                 INNER JOIN {talentospilos_est_estadoases} estado_u ON user_t.id = estado_u.id_estudiante 
                                                                 INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
                                                                 WHERE estados.nombre = 'seguimiento'
                                                                 $intersect
                                                                 )";

    $students = $DB->get_records_sql($query_students);

    $header_categories = get_categories_global_grade_book($course_id);


    $curso = new stdClass;
    $curso->nombre_curso = $course->fullname;
    $curso->profesor = $professor->fullname;
    $curso->estudiantes = $students;
    $curso->header_categories = $header_categories;
    
    return $curso;
}

//print_r(get_info_course_for_reports(10, 324)->estudiantes);
