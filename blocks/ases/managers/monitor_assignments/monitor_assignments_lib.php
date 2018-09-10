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
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__). '/../../../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/user_management/user_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/ases_report/asesreport_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';

/**
 * Función que renombra para clasificar la función get_professionals_by_instance en otras partes del plugin, el objetivo
 * es dibujar un mapa de seguimiento para saber de donde provienen las funciones
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @see asesreport_lib.php
 * @param int $instance_id Instance id.
 * @return Array 
 */

function monitor_assignments_get_professionals_by_instance( $instance_id ){
    // This function is in asesreport_lib.php
    return get_professionals_by_instance( $instance_id );
}

/**
 * Función que renombra para clasificar la función get_practicing_by_instance en otras partes del plugin, el objetivo
 * es dibujar un mapa de seguimiento para saber de donde provienen las funciones
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @see asesreport_lib.php
 * @param int $instance_id Instance id.
 * @return Array 
 */

function monitor_assignments_get_practicing_by_instance( $instance_id ){
    // This function is in asesreport_lib.php
    return get_practicing_by_instance( $instance_id );
}

/**
 * Función que renombra para clasificar la función get_monitors_by_instance en otras partes del plugin, el objetivo
 * es dibujar un mapa de seguimiento para saber de donde provienen las funciones
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array 
 */

function monitor_assignments_get_monitors_by_instance( $instance_id ){

    global $DB;

    $sql = "SELECT DISTINCT user_programa_0.id, user_programa_0.fullname, user_programa_0.cod_programa, user_programa_0.nombre_programa, facultad_0.id AS id_facultad, facultad_0.nombre AS nombre_facultad
    FROM {talentospilos_facultad} AS facultad_0
    INNER JOIN (
            SELECT user_0.id, user_0.fullname, user_0.cod_programa, programa_0.nombre AS nombre_programa, programa_0.id_facultad
            FROM {talentospilos_programa} AS programa_0
            INNER JOIN (
                    SELECT CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS fullname, moodle_user.id, cast(nullif(split_part(moodle_user.username, '-', 2), '') AS INTEGER) AS cod_programa
                    FROM {talentospilos_user_rol} AS user_rol
                    INNER JOIN {user} AS moodle_user ON moodle_user.id = user_rol.id_usuario
                    WHERE id_rol = (
                                SELECT id
                                FROM {talentospilos_rol} 
                                WHERE nombre_rol = 'monitor_ps'
                            )
                    AND id_instancia = $instance_id
                    AND id_semestre = ". get_current_semester()->max ." 
                    AND estado = 1 
                    ORDER BY fullname
                   ) AS user_0
            ON user_0.cod_programa = programa_0.cod_univalle
           ) AS user_programa_0
    ON user_programa_0.id_facultad = facultad_0.id
   ORDER BY fullname ASC";

    return $DB->get_records_sql( $sql );
}

/**
 * Función que retorna todos los usuarios del sistema.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array (
 *      stdClass(
 *          ->fullname
 *          ->id_ases_user
 *          ->cod_programa
 *          ->nombre_programa
 *      )
 * )
 */

function monitor_assignments_get_students_by_instance( $instance_id ){

    global $DB;

    $sql = "SELECT moodle_ases_user_programa_facultad_0.id_ases_user AS id, CONCAT(moodle_user_0.firstname, CONCAT(' ', moodle_user_0.lastname)) AS fullname, moodle_ases_user_programa_facultad_0.cod_programa, moodle_ases_user_programa_facultad_0.nombre_programa, moodle_ases_user_programa_facultad_0.id_facultad, moodle_ases_user_programa_facultad_0.nombre_facultad
    FROM {user} AS moodle_user_0
    INNER JOIN
        (
            SELECT moodle_ases_user_programa_0.id_moodle_user, moodle_ases_user_programa_0.id_ases_user, moodle_ases_user_programa_0.cod_programa, moodle_ases_user_programa_0.nombre_programa, facultad_0.id AS id_facultad, facultad_0.nombre AS nombre_facultad
                FROM {talentospilos_facultad} AS facultad_0
                INNER JOIN 
                (
                    SELECT moodle_ases_user_0.id_moodle_user, moodle_ases_user_0.id_ases_user, programa_0.cod_univalle AS cod_programa, programa_0.nombre AS nombre_programa, id_facultad
                    FROM {talentospilos_programa} AS programa_0
                    INNER JOIN
                    (
                        SELECT *
                        FROM {talentospilos_user_extended} AS user_ext_0
                        INNER JOIN
                        (
                            SELECT DISTINCT cohort_members_0.userid as user_id
                            FROM {cohort_members} AS cohort_members_0 
                            INNER JOIN
                            (
                                SELECT id_cohorte 
                                FROM {talentospilos_inst_cohorte} AS inst_cohorte_0 
                                WHERE id_instancia = $instance_id
                            ) AS inst_cohorte1
                            ON inst_cohorte1.id_cohorte = cohort_members_0.cohortid
                        ) AS users_distinct_0
                        ON users_distinct_0.user_id = user_ext_0.id_moodle_user
                        WHERE user_ext_0.tracking_status = 1
                    ) AS moodle_ases_user_0
                    ON programa_0.id = moodle_ases_user_0.id_academic_program
                ) AS moodle_ases_user_programa_0
                ON facultad_0.id = moodle_ases_user_programa_0.id_facultad
        ) AS moodle_ases_user_programa_facultad_0
    ON moodle_ases_user_programa_facultad_0.id_moodle_user = moodle_user_0.id
    ORDER BY fullname ASC";

    return $DB->get_records_sql( $sql );

}

/**
 * Función retorna todos los programas asociados a los estudiantes de determinada instancia.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array (
 *      stdClass(
 *          ->cod_programa
 *          ->nombre_programa
 *      )
 * )
 */

function monitor_assignments_get_students_programs( $instance_id ){

    global $DB;

    $sql = "SELECT DISTINCT programa_0.cod_univalle AS cod_programa, programa_0.nombre AS nombre_programa
    FROM {talentospilos_programa} AS programa_0
    INNER JOIN
        (
            SELECT user_ext_0.id_moodle_user, user_ext_0.id_ases_user, user_ext_0.id_academic_program
            FROM {talentospilos_user_extended} AS user_ext_0
            INNER JOIN
                (
                    SELECT DISTINCT cohort_members_0.userid as user_id
                    FROM {cohort_members} AS cohort_members_0 
                    INNER JOIN
                        (
                            SELECT id_cohorte 
                            FROM {talentospilos_inst_cohorte} AS inst_cohorte_0 
                            WHERE id_instancia = $instance_id
                        ) AS inst_cohorte1
                    ON inst_cohorte1.id_cohorte = cohort_members_0.cohortid
                ) AS users_distinct_0
            ON users_distinct_0.user_id = user_ext_0.id_moodle_user
            WHERE user_ext_0.tracking_status = 1
        ) AS moodle_ases_user_0
    ON programa_0.id = moodle_ases_user_0.id_academic_program
    ORDER BY programa_0.nombre ASC";

    return $DB->get_records_sql( $sql );

}

/**
 * Función retorna todas las facultades asociadas a los programas académicos de los estudiantes en una determinada instancia.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array (
 *      stdClass(
 *          ->id_facultad
 *          ->nombre_facultad
 *      )
 * )
 */

function monitor_assignments_get_students_faculty( $instance_id ){

    global $DB;

    $sql = "SELECT DISTINCT facultad_0.id AS id_facultad, facultad_0.nombre AS nombre_facultad
    FROM {talentospilos_facultad} AS facultad_0
    INNER JOIN 
    (
        SELECT moodle_ases_user_0.id_moodle_user, moodle_ases_user_0.id_ases_user, programa_0.cod_univalle AS cod_programa, programa_0.nombre AS nombre_programa, id_facultad
        FROM {talentospilos_programa} AS programa_0
        INNER JOIN
        (
            SELECT *
            FROM {talentospilos_user_extended} AS user_ext_0
            INNER JOIN
            (
                SELECT DISTINCT cohort_members_0.userid as user_id
                FROM {cohort_members} AS cohort_members_0 
                INNER JOIN
                (
                    SELECT id_cohorte 
                    FROM {talentospilos_inst_cohorte} AS inst_cohorte_0 
                    WHERE id_instancia = $instance_id
                ) AS inst_cohorte1
                ON inst_cohorte1.id_cohorte = cohort_members_0.cohortid
            ) AS users_distinct_0
            ON users_distinct_0.user_id = user_ext_0.id_moodle_user
            WHERE user_ext_0.tracking_status = 1
        ) AS moodle_ases_user_0
        ON programa_0.id = moodle_ases_user_0.id_academic_program
    ) AS moodle_ases_user_programa_0
    ON facultad_0.id = moodle_ases_user_programa_0.id_facultad
    ORDER BY facultad_0.nombre ASC";

    return $DB->get_records_sql( $sql );

}

/**
 * Función retorna todas las facultades asociadas a los programas académicos de los monitores en una determinada instancia.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array (
 *      stdClass(
 *          ->id_facultad
 *          ->nombre_facultad
 *      )
 * )
 */

function monitor_assignments_get_monitors_faculty( $instance_id ){

    global $DB;

    $sql = "SELECT DISTINCT user_programa_0.id_facultad, facultad_0.nombre AS nombre_facultad
    FROM {talentospilos_facultad} AS facultad_0
    INNER JOIN (
            SELECT user_0.id, user_0.fullname, user_0.cod_programa, programa_0.nombre AS nombre_programa, programa_0.id_facultad
            FROM {talentospilos_programa} AS programa_0
            INNER JOIN (
                    SELECT CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS fullname, moodle_user.id, cast(nullif(split_part(moodle_user.username, '-', 2), '') AS INTEGER) AS cod_programa
                    FROM {talentospilos_user_rol} AS user_rol
                    INNER JOIN {user} AS moodle_user ON moodle_user.id = user_rol.id_usuario
                    WHERE id_rol = (
                                SELECT id
                                FROM {talentospilos_rol} 
                                WHERE nombre_rol = 'monitor_ps'
                            )
                    AND id_instancia = $instance_id
                    AND id_semestre = ". get_current_semester()->max ." 
                    AND estado = 1 
                    ORDER BY fullname
                   ) AS user_0
            ON user_0.cod_programa = programa_0.cod_univalle
           ) AS user_programa_0
    ON user_programa_0.id_facultad = facultad_0.id
    ORDER BY nombre_facultad ASC";

    return $DB->get_records_sql( $sql );

}

/**
 * Función retorna todas los programas de los monitores en una determinada instancia.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array (
 *      stdClass(
 *          ->id_facultad
 *          ->nombre_facultad
 *      )
 * )
 */

function monitor_assignments_get_monitors_programs( $instance_id ){

    global $DB;

    $sql = "SELECT DISTINCT user_0.cod_programa, programa_0.nombre AS nombre_programa
    FROM {talentospilos_programa} AS programa_0
    INNER JOIN (
            SELECT CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS fullname, moodle_user.id, cast(nullif(split_part(moodle_user.username, '-', 2), '') AS INTEGER) AS cod_programa
            FROM {talentospilos_user_rol} AS user_rol
            INNER JOIN {user} AS moodle_user ON moodle_user.id = user_rol.id_usuario
            WHERE id_rol = (
                        SELECT id
                        FROM {talentospilos_rol} 
                        WHERE nombre_rol = 'monitor_ps'
                    )
            AND id_instancia = $instance_id
            AND id_semestre = ". get_current_semester()->max ." 
            AND estado = 1 
            ORDER BY fullname
           ) AS user_0
    ON user_0.cod_programa = programa_0.cod_univalle
    ORDER BY nombre_programa ASC";

    return $DB->get_records_sql( $sql );

}

/**
 * Función retorna todas las relaciones monitor-estudiante del semestre actual en una instancia
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array (
 *      stdClass(
 *          ->id_monitor
 *          ->id_estudiante
 *      )
 * )
 */

function monitor_assignments_get_monitors_students_relationship_by_instance( $instance_id ){

    global $DB;

    $sql = "SELECT id, id_monitor, id_estudiante 
    FROM {talentospilos_monitor_estud} 
    WHERE id_semestre = ". get_current_semester()->max ." AND id_instancia = $instance_id";
  
    return $DB->get_records_sql( $sql );;

}

/**
 * Función que retorna todas las relaciones profesional-practicante del semestre actual en una instancia
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array(
 * 	stdClass(
 *	    ->id_profesional
 * 	    ->id_practicante
 *	)
 * )
 */

function monitor_assignments_get_profesional_practicant_relationship_by_instance( $instance_id ){

    global $DB;

    $sql="SELECT user_rol_1.id, user_rol_1.id_jefe AS id_profesional, user_rol_1.id_usuario AS id_practicante
	  FROM {talentospilos_user_rol} AS user_rol_1
	  INNER JOIN (
		SELECT id_usuario
        	FROM {talentospilos_user_rol} AS user_rol_0
	 	WHERE id_rol = ( 
			SELECT id 
			FROM {talentospilos_rol} 
			WHERE nombre_rol = 'profesional_ps'
		)
	  AND id_instancia = $instance_id
      AND id_semestre = ". get_current_semester()->max . "
      AND estado = 1
	) AS profesionales_0
	ON profesionales_0.id_usuario = id_jefe
	WHERE user_rol_1.id_semestre = ". get_current_semester()->max;

    return $DB->get_records_sql( $sql );

}

/**
 * Función que retorna todas las relaciones practicante-monitor del semestre actual en una instancia
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @return Array(
 * 	stdClass(
 *	    ->id_practicante
 * 	    ->id_monitor
 *	)
 * )
 */

function monitor_assignments_get_practicant_monitor_relationship_by_instance( $instance_id ){

    global $DB;

    $sql="SELECT user_rol_1.id, user_rol_1.id_jefe AS id_practicante, user_rol_1.id_usuario AS id_monitor
	  FROM {talentospilos_user_rol} AS user_rol_1
	  INNER JOIN (
		SELECT id_usuario
        	FROM {talentospilos_user_rol} AS user_rol_0
	 	WHERE id_rol = ( 
			SELECT id 
			FROM {talentospilos_rol} 
			WHERE nombre_rol = 'practicante_ps'
		)
	  AND id_instancia = $instance_id 
      AND id_semestre = ". get_current_semester()->max . "
      AND estado = 1
	) AS practicantes_0
	ON practicantes_0.id_usuario = id_jefe
	WHERE user_rol_1.id_semestre = ". get_current_semester()->max;

    return $DB->get_records_sql( $sql );

}

/**
 * Función que asigna un monitor a un estudiante en determinada instancia, en el semestre actual.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @param int $monitor_id Monitor id.
 * @param int $student_id Student Ases id.
 * @return int id
 */

 function monitor_assignments_create_monitor_student_relationship( $instance_id, $monitor_id, $student_id ){

    global $DB;

    $current_id_semester = get_current_semester()->max;

    $sql = "SELECT * 
            FROM {talentospilos_monitor_estud} 
            WHERE id_monitor = $monitor_id 
                AND id_estudiante = $student_id
                AND id_instancia = $instance_id
                AND id_semestre = $current_id_semester";
    
    $record = $DB->get_record_sql( $sql );

    if( !$record ){

        $new_relation = new stdClass();
        $new_relation->id_monitor = $monitor_id;
        $new_relation->id_estudiante = $student_id;
        $new_relation->id_instancia = $instance_id;
        $new_relation->id_semestre = $current_id_semester;

        return $DB->insert_record('talentospilos_monitor_estud', $new_relation, $returnid=true, $bulk=false);

    }else{

        return null;

    }

 }

 /**
 * Función que elimina la asignación de un monitor a un estudiante en determinada instancia, en el semestre actual.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @param int $monitor_id Monitor id.
 * @param int $student_id Student Ases id.
 * @return int
 */

function monitor_assignments_delete_monitor_student_relationship( $instance_id, $monitor_id, $student_id ){

    global $DB;

    $current_id_semester = get_current_semester()->max;

    $sql = "SELECT * 
            FROM {talentospilos_monitor_estud} 
            WHERE id_monitor = $monitor_id 
                AND id_estudiante = $student_id
                AND id_instancia = $instance_id
                AND id_semestre = $current_id_semester";
    
    $record = $DB->get_record_sql( $sql );

    if( $record ){

        $conditions = array(
            'id_monitor' => $monitor_id,
            'id_estudiante' => $student_id,
            'id_instancia' => $instance_id,
            'id_semestre' => $current_id_semester
        );

        return $DB->delete_records('talentospilos_monitor_estud', $conditions);

    }else{
        return null;
    }

 }

 /**
 * Función que asigna un monitor a un practicante en determinada instancia, en el semestre actual.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @param int $practicant_id Practicant id.
 * @param int $monitor_id Monitor id.
 * @return int id
 */

function monitor_assignments_create_practicant_monitor_relationship( $instance_id, $practicant_id, $monitor_id ){

    global $DB;

    $current_id_semester = get_current_semester()->max;

    $sql = "SELECT id FROM {talentospilos_rol} WHERE nombre_rol = 'monitor_ps'";
    $id_rol = $DB->get_record_sql( $sql )->id;

    // Indice triple sin validación de estado
    $sql = "SELECT * 
            FROM {talentospilos_user_rol} 
            WHERE id_rol = $id_rol
                AND id_usuario = $monitor_id
                AND estado = 1
                AND id_semestre = $current_id_semester
                AND id_jefe IS NULL
                AND id_instancia = $instance_id";
    
    $record = $DB->get_record_sql( $sql );

    if( $record ){

        $record->id_jefe = $practicant_id;

        return $DB->update_record('talentospilos_user_rol', $record, $bulk=false);

    }else{

        return null;

    }
    
 }


 /**
 * Función que elimina la asignación de un practicante a un monitor en determinada instancia, en el semestre actual.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $instance_id Instance id.
 * @param int $practicant_id Practicant id.
 * @param int $monitor_id Monitor id.
 * @return int
 */

function monitor_assignments_delete_practicant_monitor_relationship( $instance_id, $practicant_id, $monitor_id ){

    global $DB;

    $current_id_semester = get_current_semester()->max;

    $sql = "SELECT * 
            FROM {talentospilos_user_rol} 
            WHERE id_usuario = $monitor_id 
                AND estado = 1
                AND id_semestre = $current_id_semester
                AND id_jefe = $practicant_id
                AND id_instancia = $instance_id";
    
    $record = $DB->get_record_sql( $sql );

    if( $record ){

        $record->id_jefe = null;

        return $DB->update_record('talentospilos_user_rol', $record, $bulk=false);

    }else{
        return null;
    }

 }

 /**
  * Función que permite transferir las asignaciones de un monitor a otro monitor, en determinada instancia, en el 
  * semestre actual.
  *
  * @param int $instance_id
  * @param int $old_monitor_id 
  * @param int $new_monitor_id
  */

  function monitor_assignments_transfer( $instance_id, $old_monitor_id, $new_monitor_id ){

    global $DB;

    $current_id_semester = get_current_semester()->max;

    // Get old monitor asignations
    $sql = "SELECT * 
            FROM {talentospilos_monitor_estud} 
            WHERE id_semestre = ". get_current_semester()->max ." AND id_instancia = $instance_id AND id_monitor = $old_monitor_id";
  
    $asignations = $DB->get_records_sql( $sql );
    if( $asignations ){

        foreach($asignations as &$asignation){

            $asignation->id_monitor = $new_monitor_id;
            $DB->update_record('talentospilos_monitor_estud', $asignation, $bulk=false);

        }
        return 1;
    }else{
        return null;
    }

  }


?>
