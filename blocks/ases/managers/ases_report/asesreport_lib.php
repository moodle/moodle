<?php
require_once(dirname(__FILE__).'/../../../../config.php');
require_once(dirname(__FILE__).'/../instance_management/instance_lib.php');
require_once(dirname(__FILE__).'/../lib/lib.php');
require_once(dirname(__FILE__).'/../lib/student_lib.php');
require_once(dirname(__FILE__).'/../user_management/user_lib.php');

/**
 * Función que recupera riesgos 
 *
 * @see getRiesgo()
 * @return Array Riesgos
 */
function get_riesgos(){
    global $DB;
    $sql_query = "SELECT * FROM {talentospilos_riesgos_ases}";
    $result = $DB->get_records_sql($sql_query);
    return $result;
}

/**
 * Función que recupera cohortes dado su idnumber
 * @see get_cohorts_by_idnumber()
 * @param $idnumber --> Nombre de la cohorte
 * @return Array cohorts
 */
function get_cohorts_by_idnumber($id_number){

    global $DB;

    $sql_query = "SELECT id, name, idnumber 
                  FROM {cohort}
                  WHERE idnumber LIKE '$id_number%'";

    $result = $DB->get_records_sql($sql_query);

    return $result;
}

/**
 * Funcion recupera la informacion necesaria para la grafica de sexo de acuerdo a la cohorte seleccionado
 * 
 * @param $cohorte
 * @return Array 
 */
function getGraficSex($cohorte){
    global $DB;
    $query = "SELECT subconsulta.sexo, COUNT(subconsulta.userid)
        FROM (SELECT data.userid, usuarios_tal.sexo
              FROM {talentospilos_usuario} as usuarios_tal 
                         INNER JOIN {user_info_data} as data ON (cast(usuarios_tal.id AS varchar) = data.data) 
              WHERE data.fieldid = 2) as subconsulta 
              INNER JOIN {cohort_members} as cohorts ON subconsulta.userid = cohorts.userid
              INNER JOIN {cohort} as cohorte ON cohorts.cohortid = cohorte.id
        WHERE cohorte.name = '$cohorte'
        GROUP BY sexo";
    
    $sql_query = "SELECT  sexo, COUNT(id) FROM {talentospilos_usuario} GROUP BY sexo";
    
    //se verifica el cohorte ingresado, de acuerdo al caso se invoca el metodo de moodle con una de las dos
    //consultar armadas anteriormente
    if($cohorte == "TODOS"){
        $result = $DB->get_records_sql($sql_query);
    } else {
        $result = $DB->get_records_sql($query);
    }
    return $result;
}

/**
 * Funcion recupera la informacion necesaria para la grafica de edad de acuerdo al cohorte seleccionado
 * 
 * @param $cohorte
 * @return Array 
 */
function getGraficAge($cohorte){
    global $DB;
    $arrayRetornar=array();
    
    $query = "
        SELECT subconsulta.userid, (now() - subconsulta.fecha_nac)/365 AS age
            FROM (SELECT data.userid, usuarios_tal.fecha_nac
                  FROM {talentospilos_usuario} as usuarios_tal 
                             INNER JOIN {user_info_data} as data ON (cast(usuarios_tal.id AS varchar) = data.data) 
                  WHERE data.fieldid = 2) as subconsulta 
                  INNER JOIN {cohort_members} as cohorts ON subconsulta.userid = cohorts.userid
                  INNER JOIN {cohort} as cohorte ON cohorts.cohortid = cohorte.id
            WHERE cohorte.name = '$cohorte'";
    
    $sql_query = "SELECT id,(now() - fecha_nac)/365 AS age FROM {talentospilos_usuario}";
    
    //se verifica el cohorte ingresado, de acuerdo al caso se invoca el metodo de moodle con una de las dos
    //consultar armadas anteriormente
    if($cohorte == "TODOS"){
        $result = $DB->get_records_sql($sql_query);
    } else {
        $result = $DB->get_records_sql($query);
    }
    
    //ya que el dato recibido es un dato calculado se sabe que la edad son los dos primeros digitos de dicho campo
    //razon por la cual se accede a cada valor, se extraen los primeros valores y se retorna el arreglo
    foreach($result as $datoEdad){
         $años=substr($datoEdad->age,0,2);
         
         array_push($arrayRetornar,$años);
    }
    
    return array_count_values($arrayRetornar);
    
}

/**
 * Funcion recupera la informacion necesaria para la grafica de programas de acuerdo al cohorte seleccionado
 * 
 * @param $cohorte
 * @return Array 
 */
function getGraficPrograma($cohorte){
    global $DB;
    
    $sql_query = "SELECT programa.nombre,COUNT(programa.nombre)
                  FROM (SELECT DISTINCT data.userid AS userid,data.data AS codcarrera FROM {talentospilos_usuario} AS usuarios_talentos 
                        INNER JOIN {user_info_data} AS data ON (CAST (usuarios_talentos.id AS varchar) = data.data) WHERE data.fieldid=3)
                  AS sub INNER JOIN {talentospilos_programa} AS programa ON (cast(programa.id as text) = sub.codcarrera) 
                  GROUP BY programa.nombre";
    
    
    // consulta con la parte de los cohortes
    $query = "SELECT programa.nombre,COUNT(programa.nombre)
                  FROM (SELECT DISTINCT data.userid AS userid,data.data AS codcarrera,miembros.cohortid,cohort.name FROM {talentospilos_usuario} AS usuarios_talentos           
                  INNER JOIN {user_info_data} AS data ON (CAST (usuarios_talentos.id AS varchar) = data.data) INNER JOIN {cohort_members} AS miembros ON
                  (data.userid=miembros.userid) INNER JOIN {cohort} AS cohort ON (cohort.id=miembros.cohortid) WHERE data.fieldid = 3 AND cohort.name='$cohorte')
                  AS sub INNER JOIN {talentospilos_programa} AS programa ON (cast(programa.id as text) = sub.codcarrera) 
                  GROUP BY programa.nombre;";
    //se verifica el cohorte ingresado, de acuerdo al caso se invoca el metodo de moodle con una de las dos
    //consultar armadas anteriormente
    if($cohorte == "TODOS"){
        $result = $DB->get_records_sql($sql_query);
    } else {
        $result = $DB->get_records_sql($query);
    }
    return $result;
}

/**
 * Funcion recupera la informacion necesaria para la grafica de facultad de acuerdo al cohorte seleccionado
 * 
 * @param $cohorte
 * @return Array 
 */
function getGraficFacultad($cohorte){
    global $DB;
    
    $sql_query = "SELECT facultad.nombre,COUNT(facultad.nombre) FROM 
                    (SELECT nombre,programa.id_facultad FROM 
                        (SELECT DISTINCT  data.userid AS userid,data.data AS codcarrera FROM {talentospilos_usuario} AS usuarios_talentos 
                         INNER JOIN {user_info_data} AS data ON (cast (usuarios_talentos.id as varchar) = data.data) where data.fieldid=3) 
                     AS sub INNER JOIN {talentospilos_programa} AS programa ON (CAST(programa.id AS text) = sub.codcarrera)) 
                  AS subconsulta INNER JOIN {talentospilos_facultad} AS facultad ON (subconsulta.id_facultad=facultad.id) 
                  GROUP BY facultad.nombre";  
    // consulta con la parte de los cohortes
    $query = "SELECT facultad.nombre,COUNT(facultad.nombre) FROM 
                    (SELECT nombre,programa.id_facultad FROM 
                        (SELECT DISTINCT data.userid AS userid,data.data AS codcarrera,miembros.cohortid,cohort.name FROM {talentospilos_usuario} AS usuarios_talentos           
                  INNER JOIN {user_info_data} AS data ON (CAST (usuarios_talentos.id AS varchar) = data.data) INNER JOIN {cohort_members} AS miembros ON
                  (data.userid=miembros.userid) INNER JOIN {cohort} AS cohort ON (cohort.id=miembros.cohortid) WHERE data.fieldid = 3 AND cohort.name='$cohorte') 
                     AS sub INNER JOIN {talentospilos_programa} AS programa ON (CAST(programa.id AS text) = sub.codcarrera)) 
                  AS subconsulta INNER JOIN {talentospilos_facultad} AS facultad ON (subconsulta.id_facultad=facultad.id) 
                  GROUP BY facultad.nombre";
    //se verifica el cohorte ingresado, de acuerdo al caso se invoca el metodo de moodle con una de las dos
    //consultar armadas anteriormente
    if($cohorte == "TODOS"){
        $result = $DB->get_records_sql($sql_query);
    } else {
        $result = $DB->get_records_sql($query);
    }
    return $result;
    
}

/**
 * Funcion recupera la informacion necesaria para la grafica de estado de acuerdo al cohorte seleccionado
 * 
 * @param $cohorte
 * @return Array 
 */
function getGraficEstado($cohorte){
    global $DB;
    
    $sql_query = "SELECT data, COUNT(userid)
        FROM(SELECT subconsulta.userid, dato.data 
             FROM    (SELECT DISTINCT data.userid AS userid
                    FROM {talentospilos_usuario} AS usuarios_tal 
                         INNER JOIN {user_info_data} AS data ON (cast(usuarios_tal.id AS varchar) = data.data) 
                    WHERE data.fieldid = 2) AS subconsulta 
                    INNER JOIN {user_info_data} AS dato ON (subconsulta.userid = dato.userid)
             WHERE dato.fieldid = 4 ) AS cont
        GROUP BY data";
        
    //consulta con la parte de los cohortes
    $query = "
        SELECT data, COUNT(userid)
        FROM(SELECT subconsulta.userid, dato.data 
             FROM    (SELECT DISTINCT data.userid AS userid,data.data AS codcarrera,miembros.cohortid,cohort.name 
                      FROM {talentospilos_usuario} AS usuarios_tal INNER JOIN {user_info_data} AS data ON
                      (cast(usuarios_tal.id AS varchar) = data.data) INNER JOIN {cohort_members} AS miembros ON 
                      (data.userid=miembros.userid) INNER JOIN {cohort} AS cohort ON (cohort.id=miembros.cohortid) 
                        WHERE data.fieldid = 2 AND cohort.name='$cohorte') AS subconsulta 
                    INNER JOIN {user_info_data} AS dato ON (subconsulta.userid = dato.userid)
             WHERE dato.fieldid = 4 ) AS cont
        GROUP BY data";
    
    //se verifica el cohorte ingresado, de acuerdo al caso se invoca el metodo de moodle con una de las dos
    //consultar armadas anteriormente
    if($cohorte == "TODOS"){
        $result = $DB->get_records_sql($sql_query);
    } else {
        $result = $DB->get_records_sql($query);
    }
    return $result;
    
}

/**
 * Función que recupera datos para la tabla de ases_report, dado el estado, la cohorte y un conjunto de campos a extraer.
 *
 * @see get_not_assign_students()
 * @param $column       --> Campos a seleccionar
 * @param $population   --> Estado y cohorte
 * @param $academic_fields --> Campos relacionados con el programa académico y facultad
 * @param $idinstancia  --> Instancia del módulo
 * @return Array 
 */
function get_not_assign_students($general_fields=null, $conditions, $academic_fields=null, $instance_id){

    global $DB, $USER;

    $actions = $USER->actions;

    //$conditions[1] = 'TODOS';

    // ********* Se arman las clausulas de la consulta sql ***********

    // ***** Select clause *****
    $select_clause = "SELECT DISTINCT ";
    $from_clause = "";
    $where_clause = " WHERE ";
    $sub_query_cohort = "";
    $sub_query_status = "";
    $sub_query_academic = "";

    if($general_fields){
        foreach($general_fields as $field){
            $select_clause .= $field.', ';
        }
    }


    if($academic_fields){
        foreach($academic_fields as $field){
            $select_clause .= $field.', ';
        }

        $sub_query_academic .= " INNER JOIN {talentospilos_programa} AS acad_program ON user_extended.id_academic_program = acad_program.id
                                INNER JOIN {talentospilos_facultad} AS faculty ON faculty.id = acad_program.id_facultad";
    }

    

    // **** From clause ****
    $from_clause = " FROM {user} as user_moodle INNER JOIN {talentospilos_user_extended} AS user_extended ON user_moodle.id = user_extended.id_moodle_user
                                                INNER JOIN {talentospilos_usuario} AS tp_user ON user_extended.id_ases_user = tp_user.id ";

    // **** Where clause ****
    //$where_clause .= " tp_ases_status.id_instancia = $instance_id";

    // Condición cohorte
    if($conditions[0] != 'TODOS'){

        $sub_query_cohort .= "INNER JOIN (SELECT DISTINCT user_moodle.username, STRING_AGG(cohorts.idnumber, ', ') AS cohorts_student
                                         FROM {cohort_members} AS members_cohort INNER JOIN {user} AS user_moodle ON user_moodle.id = members_cohort.userid
                                                                                 INNER JOIN {cohort} AS cohorts ON cohorts.id = members_cohort.cohortid
                                         WHERE cohorts.idnumber = '$conditions[0]'
                                         GROUP BY user_moodle.username) AS cohort_query ON cohort_query.username = user_moodle.username ";
    }else if($conditions[0] == 'TODOS'){
        $sub_query_cohort .= " INNER JOIN (SELECT DISTINCT moodle_user.username, STRING_AGG(cohorts.idnumber, ', ') AS cohorts_student 
                                            FROM {cohort} AS cohort INNER JOIN {talentospilos_inst_cohorte} AS instance_cohort ON cohort.id = instance_cohort.id_cohorte
                                                                    INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                                                                    INNER JOIN {cohort} AS cohorts ON cohort_member.cohortid = cohorts.id
                                                                    INNER JOIN {user} AS moodle_user ON moodle_user.id = cohort_member.userid
                                            WHERE id_instancia = $instance_id
                                            GROUP BY moodle_user.username) AS all_students_cht ON all_students_cht.username = user_moodle.username";
    }

    if(property_exists($actions, 'search_all_students_ar')){
        
        $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_academic;
        $result_query = $DB->get_records_sql($sql_query);

    }

        $user_id = $USER->id;

        $instance= $instance_id;
        $id_current_semester = get_current_semester()->max;
        $sql_query = "SELECT roles.nombre_rol, user_role.id_programa 
                      FROM {talentospilos_user_rol} AS user_role 
                                                INNER JOIN {talentospilos_rol} AS roles ON user_role.id_rol = roles.id
                      WHERE user_role.id_semestre = $id_current_semester AND user_role.estado = 1 and user_role.id_usuario='$user_id' and id_instancia='$instance'";

        $user_role = $DB->get_record_sql($sql_query);

        switch($user_role->nombre_rol){
            case 'director_prog':

                $conditions_query_directors = " user_extended.id_academic_program = $user_role->id_programa";

                $where_clause .= $conditions_query_directors;

                $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_academic.$where_clause;
                $result_query = $DB->get_records_sql($sql_query);

                break;

            case 'profesional_ps':

                $where_clause .= " user_moodle.username NOT IN (SELECT user_moodle.username
                                                                FROM {talentospilos_usuario}  AS ases_user
                                                                INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_ases_user = ases_user.id
                                                                INNER JOIN {user} AS user_moodle ON user_moodle.id = user_extended.id_moodle_user
                                                                INNER JOIN {cohort_members}  AS cohorts_students ON cohorts_students.userid = user_extended.id_moodle_user
                                                                INNER JOIN {talentospilos_monitor_estud} AS students_monitor ON students_monitor.id_estudiante = ases_user.id
                                                                INNER JOIN {talentospilos_inst_cohorte}  AS cohorts_instance ON cohorts_instance.id_cohorte = cohorts_students.cohortid
                                                                WHERE cohorts_instance.id_instancia = $instance)";
                $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_academic.$where_clause;
                $result_query = $DB->get_records_sql($sql_query);

                break;

            case 'practicante_ps':
            
                $query_monitors = " INNER JOIN {talentospilos_monitor_estud} AS monitor_student ON monitor_student.id_estudiante = user_extended.id_ases_user";
                $query_pract = " INNER JOIN (SELECT id_jefe, id_usuario, id_semestre FROM {talentospilos_monitor_estud}) AS pract_monitor ON monitor_student.id_usuario = pract_monitor.id_usuario";
                $where_clause .= " pract_monitor.id_jefe = $user_id AND pract_monitor.id_semestre = $id_current_semester";

                $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_academic.$query_monitors;
                $result_query = $DB->get_records_sql($sql_query);

                break;

            case 'monitor_ps':

                $query_monitors = " INNER JOIN {talentospilos_monitor_estud} AS monitor_student ON monitor_student.id_estudiante = user_extended.id_ases_user";
                $where_clause .= " monitor_student.id_monitor = $user_id AND monitor_student.id_semestre = $id_current_semester";

                $sql_query = $select_clause.$from_clause.$sub_query_cohort.$sub_query_status.$sub_query_academic.$query_monitors.$where_clause;
                $result_query = $DB->get_records_sql($sql_query);

                break;

            default:
                break;
        }
            

    $result_to_return = array();

    foreach($result_query as $result){

        array_push($result_to_return, $result);
    }

    return $result_to_return;
}

/**
 * Función que recupera datos para la tabla de ases_report, dado el estado, la cohorte y un conjunto de campos a extraer.
 *
 * @see get_ases_report()
 * @param $general_fields       --> Campos a seleccionar
 * @param $conditions   --> Estado y cohorte
 * @param $risk         --> Nivel de riesgo a mostrar
 * @param $academic_fields --> Campos relacionados con el programa académico y facultad
 * @param $idinstancia  --> Instancia del módulo
 * @return Array 
 */
function get_ases_report($general_fields=null, 
                         $conditions, 
                         $risk_fields=null, 
                         $academic_fields=null, 
                         $statuses_fields=null, 
                         $assignment_fields=null, 
                         $instance_id){

    global $DB, $USER;

    $actions = $USER->actions;
    $id_current_semester = get_current_semester()->max;

    $conditions[1] = 'TODOS';

    // ********* Se arman las clausulas de la consulta sql ***********

    // ***** Select clause *****
    $select_clause = "SELECT DISTINCT ";
    $from_clause = " FROM ";
    $where_clause = " WHERE ";
    $subquery_cohort = "";

    $sub_query_status = "";
    $sub_query_academic = "";
     $sub_query_risks = "";
     $sub_query_assignment_fields = "";

    // Clausula select para los campos generales del reporte ASES
    if($general_fields){
        $counter_general_fields = 0;
        foreach($general_fields as $field){
            $counter_general_fields++;
            if($counter_general_fields == count($general_fields)){
                $select_clause .= $field.', ';
            }else{
                $select_clause .= $field.', ';
            }
        }
    }

    // Subconsultas para el riesgo de usuario en las diferentes dimensiones de riesgo
    if($risk_fields){
        foreach($risk_fields as $risk_field){
            $name_query = "SELECT * FROM {talentospilos_riesgos_ases} WHERE id =".$risk_field;
            $risk_name = $DB->get_record_sql($name_query)->nombre;
            
            // Riesgos asociado
            $select_clause = $select_clause." (SELECT 
                                                    CASE WHEN calificacion_riesgo = 1 THEN 'Bajo'
                                                         WHEN calificacion_riesgo = 2 THEN 'Medio'
                                                         WHEN calificacion_riesgo = 3 THEN 'Alto'
                                                         WHEN calificacion_riesgo = 0 THEN 'N.R.'
                                                         ELSE 'N.R.' 
                                                    END
                                                FROM {talentospilos_riesg_usuario} WHERE ";
            $select_clause = $select_clause."id_usuario = ases_students.student_id AND id_riesgo = ".$risk_field.") AS ".$risk_name.", ";
        }
    }

    // Subconsulta relacionados con los campos por defecto
    if($conditions[0] == 'TODOS'){
        $subquery_cohort = "(SELECT moodle_user.username, 
                                moodle_user.firstname,  
                                moodle_user.lastname,
                                ases_user.num_doc,
                                ases_user.id AS student_id,
                                moodle_user.email,
                                ases_user.celular,
                                ases_user.direccion_res,
                                STRING_AGG(cohort.idnumber, ', ') AS cohorts_student,
                                program_statuses.nombre AS program_status,
                                user_extended.id_academic_program	     
                            FROM {cohort} AS cohort 
                            INNER JOIN {talentospilos_inst_cohorte} AS instance_cohort ON cohort.id = instance_cohort.id_cohorte
                            INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                            INNER JOIN {user} AS moodle_user ON moodle_user.id = cohort_member.userid
                            INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = moodle_user.id
                            INNER JOIN {talentospilos_usuario} AS ases_user ON ases_user.id = user_extended.id_ases_user
                            INNER JOIN {talentospilos_estad_programa} AS program_statuses ON program_statuses.id = user_extended.program_status
                            WHERE instance_cohort.id_instancia = $instance_id AND user_extended.tracking_status = 1
                            GROUP BY moodle_user.username, 
                                     moodle_user.firstname, 
                                     moodle_user.lastname, 
                                     student_id,
                                     moodle_user.email,
                                     ases_user.celular,
                                     ases_user.direccion_res,
                                     ases_user.num_doc, 
                                     program_statuses.nombre, 
                                     user_extended.id_academic_program) AS ases_students";
    }else if (substr($conditions[0], 0, 5) == 'TODOS'){

        $idnumber_cohort = substr($conditions[0], 6);
        
        $subquery_cohort = "(SELECT moodle_user.username, 
                                moodle_user.firstname,  
                                moodle_user.lastname,
                                ases_user.num_doc,
                                ases_user.id AS student_id,
                                moodle_user.email,
                                ases_user.celular,
                                ases_user.direccion_res,
                                STRING_AGG(cohort.idnumber, ', ') AS cohorts_student,
                                program_statuses.nombre AS program_status,
                                user_extended.id_academic_program	     
                            FROM {cohort} AS cohort 
                            INNER JOIN {talentospilos_inst_cohorte} AS instance_cohort ON cohort.id = instance_cohort.id_cohorte
                            INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                            INNER JOIN {user} AS moodle_user ON moodle_user.id = cohort_member.userid
                            INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = moodle_user.id
                            INNER JOIN {talentospilos_usuario} AS ases_user ON ases_user.id = user_extended.id_ases_user
                            INNER JOIN {talentospilos_estad_programa} AS program_statuses ON program_statuses.id = user_extended.program_status
                            WHERE instance_cohort.id_instancia = $instance_id AND user_extended.tracking_status = 1
                                  AND cohort.idnumber LIKE '$idnumber_cohort%'
                            GROUP BY moodle_user.username, 
                                     moodle_user.firstname, 
                                     moodle_user.lastname, 
                                     student_id,
                                     moodle_user.email,
                                     ases_user.celular,
                                     ases_user.direccion_res,
                                     ases_user.num_doc, 
                                     program_statuses.nombre, 
                                     user_extended.id_academic_program) AS ases_students";
    }else{
        $subquery_cohort = "(SELECT moodle_user.username, 
                                moodle_user.firstname,  
                                moodle_user.lastname,
                                ases_user.num_doc,
                                ases_user.id AS student_id,
                                moodle_user.email,
                                ases_user.celular,
                                ases_user.direccion_res,
                                STRING_AGG(cohort.idnumber, ', ') AS cohorts_student,
                                program_statuses.nombre AS program_status,
                                user_extended.id_academic_program	     
                            FROM {cohort} AS cohort 
                            INNER JOIN {talentospilos_inst_cohorte} AS instance_cohort ON cohort.id = instance_cohort.id_cohorte
                            INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                            INNER JOIN {user} AS moodle_user ON moodle_user.id = cohort_member.userid
                            INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = moodle_user.id
                            INNER JOIN {talentospilos_usuario} AS ases_user ON ases_user.id = user_extended.id_ases_user
                            INNER JOIN {talentospilos_estad_programa} AS program_statuses ON program_statuses.id = user_extended.program_status
                            WHERE instance_cohort.id_instancia = $instance_id AND user_extended.tracking_status = 1
                                  AND cohort.idnumber = '$conditions[0]'
                            GROUP BY moodle_user.username, 
                                     moodle_user.firstname, 
                                     moodle_user.lastname, 
                                     student_id,
                                     moodle_user.email,
                                     ases_user.celular,
                                     ases_user.direccion_res,
                                     ases_user.num_doc, 
                                     program_statuses.nombre, 
                                     user_extended.id_academic_program) AS ases_students";
    }

    // Subconsutlas relacionadas con los campos de estado
    if($statuses_fields){
        foreach($statuses_fields as $status_field){
            switch(explode(".", $status_field)[1]){
                case 'ases_status_student':

                    $select_clause .= $status_field.", ";

                    $sub_query_status .= " LEFT JOIN (SELECT current_ases_status.id_ases_student AS id_ases_student,
                                                        CASE WHEN historic_ases_statuses.nombre = 'seguimiento' THEN '-SEGUIMIENTO'
                                                             WHEN historic_ases_statuses.nombre = 'sinseguimiento' THEN '-SIN SEGUIMIENTO'
                                                             ELSE 'N.R.' 
                                                        END AS ases_status_student
                                                      FROM
                                                        (SELECT student_ases_status.id_estudiante AS id_ases_student,
                                                                MAX(student_ases_status.fecha) AS fecha
                                                        FROM {talentospilos_est_estadoases} AS student_ases_status
                                                        WHERE id_instancia = $instance_id
                                                        GROUP BY student_ases_status.id_estudiante) AS current_ases_status
                                                      INNER JOIN
                                                      (SELECT student_ases_status.id_estudiante, 
                                                            student_ases_status.fecha, ases_statuses.nombre
                                                      FROM {talentospilos_est_estadoases} AS student_ases_status
                                                            INNER JOIN {talentospilos_estados_ases} AS ases_statuses ON ases_statuses.id = student_ases_status.id_estado_ases) AS historic_ases_statuses
                                                      ON (historic_ases_statuses.id_estudiante = current_ases_status.id_ases_student AND historic_ases_statuses.fecha = current_ases_status.fecha)
                                                      ) AS ases_status ON ases_status.id_ases_student = ases_students.student_id";
                    break;
                case 'icetex_status_student':

                    $select_clause .= $status_field.", ";

                    $sub_query_status .= " LEFT JOIN (SELECT current_icetex_status.id_ases_student AS id_ases_student, 
                                                        CASE WHEN historic_icetex_statuses.nombre IN ('APLAZADO', 'EGRESADO', 'RETIRADO') THEN 'INACTIVO'
                                                             ELSE historic_icetex_statuses.nombre
                                                        END AS icetex_status_student                                                      
                                                      FROM
                                                        (SELECT student_icetex_status.id_estudiante AS id_ases_student,
                                                                MAX(student_icetex_status.fecha) AS fecha
                                                        FROM {talentospilos_est_est_icetex} AS student_icetex_status
                                                        GROUP BY student_icetex_status.id_estudiante) AS current_icetex_status
                                                        INNER JOIN
                                                        (SELECT student_icetex_status.id_estudiante, student_icetex_status.fecha, icetex_statuses.nombre
                                                        FROM {talentospilos_est_est_icetex} AS student_icetex_status
                                                        INNER JOIN {talentospilos_estados_icetex} AS icetex_statuses ON icetex_statuses.id = student_icetex_status.id_estado_icetex) AS historic_icetex_statuses
                                                        ON (historic_icetex_statuses.id_estudiante = current_icetex_status.id_ases_student AND historic_icetex_statuses.fecha = current_icetex_status.fecha)) AS icetex_status ON icetex_status.id_ases_student = ases_students.student_id";
                    break;
                case 'program_status':

                    $select_clause .= $status_field.", ";

                    break;
            }
        }
    }

    // Subconsultas relacionadas con los campos académicos
    if($academic_fields){

        $sub_query_academic .= " INNER JOIN {talentospilos_programa} AS academic_program ON academic_program.id = ases_students.id_academic_program";
        
        foreach($academic_fields as $field){

            switch(explode(" ", $field)[2]){

                case 'cod_univalle';
                case 'nombre_programa';
                    $select_clause .= $field.', ';
                    break;

                case 'nombre_facultad':
                    $select_clause .= $field.', ';
                    $sub_query_academic .= " INNER JOIN {talentospilos_facultad} AS faculty ON faculty.id = academic_program.id_facultad";                    
                    break;
                
                case 'promedio_acumulado':

                    $select_clause .= $field.', ';

                    $sub_query_academic .= " LEFT JOIN (SELECT history_academic.promedio_acumulado, history_academic.id_estudiante
                                                        FROM {talentospilos_history_academ} AS history_academic
                                                        INNER JOIN (SELECT id_estudiante, MAX(id_semestre) AS id_semestre
                                                                        FROM {talentospilos_history_academ}
                                                                        GROUP BY id_estudiante) AS students_semesters
                                                                ON (history_academic.id_semestre = students_semesters.id_semestre AND history_academic.id_estudiante = students_semesters.id_estudiante 
                                                )
                                                                INNER JOIN {talentospilos_user_extended} AS user_extended ON (user_extended.id_ases_user = history_academic.id_estudiante AND history_academic.id_programa = user_extended.id_academic_program)
                                                        WHERE tracking_status = 1
                                                ) AS accum_average ON accum_average.id_estudiante = ases_students.student_id";
                    break;
                
                case 'bajos':

                    $select_clause .= $field.', ';

                    $sub_query_academic .= " LEFT JOIN (SELECT DISTINCT MAX(numero_bajo) AS numero_bajo, academic_history.id_estudiante
                                                       FROM {talentospilos_history_academ} AS academic_history
                                                           INNER JOIN {talentospilos_history_bajos} AS history_bajos ON history_bajos.id_history = academic_history.id
                                                       GROUP BY academic_history.id_estudiante
                                                       ) AS history_bajo ON history_bajo.id_estudiante = ases_students.student_id";
                    break;
                
                case 'estimulos':
                    $select_clause .= $field.', ';
                    $sub_query_academic .= " LEFT JOIN (SELECT DISTINCT COUNT(puesto_ocupado) AS numero_estimulos, academic_history.id_estudiante
                                            FROM {talentospilos_history_academ} AS academic_history
                                                INNER JOIN {talentospilos_history_estim} AS history_stim ON history_stim.id_history = academic_history.id
                                            GROUP BY academic_history.id_estudiante
                                            ) AS history_estim ON history_estim.id_estudiante = ases_students.student_id";
                    break;
            }
            
        }
    }

    $select_clause = substr($select_clause, 0, -2);

    // Campos asignaciones personal socioeducativo
    if($assignment_fields){

        foreach($assignment_fields as $assignment_field){
            $select_clause = $select_clause.", ".$assignment_field;
        }

        $sub_query_assignment_fields = " LEFT JOIN 
                                        (SELECT monitor_student.id_ases_user AS id_estudiante,
                                                monitor_student.username, 
                                            psico_staff.monitor AS monitor, 
                                            psico_staff.trainer AS trainer, 
                                            psico_staff.professional AS professional
                                        FROM
                                        (SELECT user_extended.id_ases_user,
                                                monitor_student.id_monitor, 
                                                moodle_user.username
                                            FROM {talentospilos_monitor_estud} AS monitor_student
                                                LEFT JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_ases_user = monitor_student.id_estudiante
                                                INNER JOIN {user} AS moodle_user ON moodle_user.id = user_extended.id_moodle_user
                                            WHERE monitor_student.id_semestre =  $id_current_semester
                                                AND monitor_student.id_instancia = $instance_id) AS monitor_student
                                    
                                            LEFT JOIN
                                    
                                        (SELECT query_monitor.id_monitor,
                                                query_monitor.monitor_name AS monitor,
                                                query_trainer.trainer_name AS trainer, 
                                                query_professional.professional_name AS professional
                                            FROM
                                            (SELECT user_role.id_usuario AS id_monitor,
                                                    CONCAT(moodle_user.firstname,
                                                    CONCAT(' ', moodle_user.lastname)) AS monitor_name,
                                                    user_role.id_jefe AS id_boss_monitor
                                            FROM {talentospilos_user_rol} AS user_role 
                                            INNER JOIN {user} AS moodle_user ON user_role.id_usuario = moodle_user.id
                                            WHERE id_rol = (SELECT id FROM {talentospilos_rol} AS role WHERE nombre_rol = 'monitor_ps') AND id_semestre = $id_current_semester AND id_instancia = $instance_id) AS query_monitor
                                    
                                            INNER JOIN
                                        
                                            (SELECT user_role.id_usuario AS id_trainer, CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS trainer_name, user_role.id_jefe AS id_boss_trainer
                                            FROM {talentospilos_user_rol} AS user_role 
                                            INNER JOIN {user} AS moodle_user ON user_role.id_usuario = moodle_user.id
                                            WHERE id_rol = (SELECT id FROM {talentospilos_rol} AS role WHERE nombre_rol = 'practicante_ps') AND id_semestre = $id_current_semester AND id_instancia = $instance_id) AS query_trainer
                                        
                                            ON query_monitor.id_boss_monitor = query_trainer.id_trainer
                                        
                                        INNER JOIN
                                        
                                        (SELECT user_role.id_usuario AS id_professional, CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS professional_name
                                        FROM {talentospilos_user_rol} AS user_role 
                                        INNER JOIN {user} AS moodle_user ON user_role.id_usuario = moodle_user.id
                                        WHERE id_rol = (SELECT id FROM {talentospilos_rol} AS role WHERE nombre_rol = 'profesional_ps') AND id_semestre = $id_current_semester AND id_instancia = $instance_id) AS query_professional
                                        
                                        ON query_professional.id_professional = query_trainer.id_boss_trainer) AS psico_staff
                                        
                                        ON monitor_student.id_monitor = psico_staff.id_monitor) AS assignments_query
                                    ON assignments_query.id_estudiante = ases_students.student_id
                                         ";
    }

    if(property_exists($actions, 'search_all_students_ar')){
        
        $sql_query = $select_clause.$from_clause.$subquery_cohort.$sub_query_status.$sub_query_academic.$sub_query_assignment_fields;
        $result_query = $DB->get_records_sql($sql_query);

    }else if(property_exists($actions, 'search_assigned_students_ar')){

        $user_id = $USER->id;
        
        $sql_query = "SELECT roles.nombre_rol, user_role.id_programa 
                      FROM {talentospilos_user_rol} AS user_role 
                                                INNER JOIN {talentospilos_rol} AS roles ON user_role.id_rol = roles.id
                      WHERE user_role.id_semestre = $id_current_semester AND user_role.estado = 1 AND user_role.id_usuario = $user_id";

        $user_role = $DB->get_record_sql($sql_query);

        switch($user_role->nombre_rol){
            case 'director_prog':

                $conditions_query_directors = " user_extended.id_academic_program = $user_role->id_programa";

                $where_clause .= $conditions_query_directors;

                $sql_query = $select_clause.$from_clause.$subquery_cohort.$sub_query_status.$sub_query_icetex_status.$sub_query_academic.$sub_query_assignment_fields.$where_clause;
                $result_query = $DB->get_records_sql($sql_query);

                break;

            case 'profesional_ps':

                $sub_query_ps_staff = " INNER JOIN {talentospilos_monitor_estud} AS monitor_student ON monitor_student.id_estudiante = ases_students.student_id
                                        INNER JOIN (SELECT t_monitor_practicante.id_monitor, t_monitor_practicante.id_practicante, t_practicante_profesional.id_profesional
                                                FROM
                                                (SELECT id_usuario AS id_monitor, id_jefe AS id_practicante 
                                                FROM {talentospilos_user_rol} AS user_rol
                                                    INNER JOIN {talentospilos_rol} AS rol ON user_rol.id_rol = rol.id
                                                WHERE rol.nombre_rol = 'monitor_ps' AND user_rol.id_semestre = $id_current_semester AND user_rol.id_instancia = $instance_id) AS t_monitor_practicante
                                                INNER JOIN
                                                (SELECT id_usuario AS id_practicante, id_jefe AS id_profesional
                                                FROM {talentospilos_user_rol} AS user_rol
                                                    INNER JOIN {talentospilos_rol} AS rol ON user_rol.id_rol = rol.id
                                                WHERE rol.nombre_rol = 'practicante_ps' AND user_rol.id_semestre = $id_current_semester AND user_rol.id_instancia = $instance_id) AS t_practicante_profesional
                                                ON t_monitor_practicante.id_practicante = t_practicante_profesional.id_practicante) AS t_monitor_practicante_profesional
                                    ON t_monitor_practicante_profesional.id_monitor = monitor_student.id_monitor";

                $where_clause .= " t_monitor_practicante_profesional.id_profesional = $user_id AND monitor_student.id_semestre = $id_current_semester";

                $sql_query = $select_clause.$from_clause.$subquery_cohort.$sub_query_status.$sub_query_academic.$sub_query_icetex_status.$sub_query_assignment_fields.$sub_query_ps_staff.$where_clause;

                $result_query = $DB->get_records_sql($sql_query);
                
                break;

            case 'practicante_ps':
            
                $sub_query_ps_staff = " INNER JOIN {talentospilos_monitor_estud} AS monitor_student ON monitor_student.id_estudiante = ases_students.student_id
                                        INNER JOIN (SELECT id_usuario AS id_monitor, id_jefe AS id_practicante 
                                                    FROM {talentospilos_user_rol} AS user_rol
                                                        INNER JOIN {talentospilos_rol} AS rol ON user_rol.id_rol = rol.id
                                                    WHERE rol.nombre_rol = 'monitor_ps' AND user_rol.id_semestre = $id_current_semester AND user_rol.id_instancia = $instance_id) AS t_monitor_practicante
                                        ON t_monitor_practicante.id_monitor = monitor_student.id_monitor";
                
                $where_clause .= " t_monitor_practicante.id_practicante = $user_id AND monitor_student.id_semestre = $id_current_semester";

                $sql_query = $select_clause.$from_clause.$subquery_cohort.$sub_query_status.$sub_query_academic.$sub_query_icetex_status.$sub_query_assignment_fields.$sub_query_ps_staff.$where_clause;

                $result_query = $DB->get_records_sql($sql_query);

                break;

            case 'monitor_ps':

                $query_monitors = " INNER JOIN {talentospilos_monitor_estud} AS monitor_student ON monitor_student.id_estudiante = ases_students.student_id";
                $where_clause .= " monitor_student.id_monitor = $user_id AND monitor_student.id_semestre = $id_current_semester";

                $sql_query = $select_clause.$from_clause.$subquery_cohort.$sub_query_status.$sub_query_academic.$sub_query_icetex_status.$sub_query_assignment_fields.$query_monitors.$where_clause;
                $result_query = $DB->get_records_sql($sql_query);

                break;

            default:
                break;
        }
            
    }else{
        return 'El usuario no tiene permisos para listar estudiantes en el reporte ASES';
    }

    $result_to_return = array();

    foreach($result_query as $result){
        array_push($result_to_return, $result);
    }

    return $result_to_return;
}

/**
 * Función que recupera datos para la tabla de ases_report, de los estudiantes asignados a una persona del rol socioeducativo o
 * asociados a un director de programa académico
 *
 * @see get_default_ases_report()
 * @param $column       --> Campos a seleccionar
 * @param $population   --> Estado y cohorte
 * @param $risk_fields         --> Nivel de riesgo a mostrar
 * @param $academic_fields --> Campos relacionados con el programa académico y facultad
 * @param $instance_id  --> Instancia del módulo
 * @return Array 
 */
function get_default_ases_report($id_instance){

    $query_fields = array();
    array_push($query_fields, "ases_students.username");
    array_push($query_fields, "ases_students.firstname");
    array_push($query_fields, "ases_students.lastname");
    array_push($query_fields, "ases_students.num_doc");
    array_push($query_fields, "ases_students.cohorts_student");

    $conditions = array();
    array_push($conditions, "TODOS");

    $columns = array();
    array_push($columns, array("title"=>"Código estudiante", "name"=>"username", "data"=>"username"));
    array_push($columns, array("title"=>"Nombre(s)", "name"=>"firstname", "data"=>"firstname"));
    array_push($columns, array("title"=>"Apellido(s)", "name"=>"lastname", "data"=>"lastname"));
    array_push($columns, array("title"=>"Número de documento", "name"=>"num_doc", "data"=>"num_doc"));
    array_push($columns, array("title"=>"Cohorte", "name"=>"cohorts_students", "data"=>"cohorts_student"));

    $default_students = get_ases_report($query_fields, $conditions, null, null, null, null, $id_instance);

    $data_to_table = array(
        "bsort" => false,
        "data"=> $default_students,
        "columns" => $columns,
        "select" => "false",
        "fixedHeader"=> array(
            "header"=> true,
            "footer"=> true
        ),
        "columnDefs" => array(
            array(
                "orderable" => false,
                "targets" => 'nosort'
            )
        ),
        "language" => 
            array(
                "search"=> "Buscar:",
                "oPaginate" => array (
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
                "oAria"=> array(
                    "sSortAscending"=>  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending"=> ": Activar para ordenar la columna de manera descendente"
                )
            ),
        "dom"=>'lifrtpB',
        "buttons"=>array(
            array(
                "extend"=>'print',
                "text"=>'Imprimir'
            ),
            array(
                "extend"=>'csvHtml5',
                "text"=>'CSV'
            ),
            array(
                "extend" => "excel",
                                "text" => 'Excel',
                                "className" => 'buttons-excel',
                                "filename" => 'Export excel',
                                "extension" => '.xls'
            )
        )
    );

    return $data_to_table;

}

/**
 * Función que extrae los profesionales socioeducativos asignados para el periodo actual en una 
 * instancia dada.
 *
 * @see get_professionals_by_instance
 * @param $instance_id       --> Identificador de instancia
 * @return Array 
 */

function get_professionals_by_instance($instance_id){
    
    global $DB;

    $result = array();

    $sql_query = "SELECT CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS fullname, moodle_user.id
                  FROM {talentospilos_user_rol} AS user_rol
                       INNER JOIN {user} AS moodle_user ON moodle_user.id = user_rol.id_usuario
                  WHERE id_rol = (SELECT id
                                  FROM {talentospilos_rol}
                                  WHERE nombre_rol = 'profesional_ps')
                        AND id_instancia = $instance_id
                        AND id_semestre =". get_current_semester()->max
                        ."ORDER BY fullname";

    $result = $DB->get_records_sql($sql_query);

    if(!$result){
        array_push($result, array('fullname'=>'N.R.'));
    }

    return $result;
}

/**
 * Función que extrae los practicantes socioeducativos asignados para el periodo actual en una 
 * instancia dada.
 *
 * @see get_practicing_by_instance
 * @param $instance_id       --> Identificador de instancia
 * @return Array 
 */

function get_practicing_by_instance($instance_id){
    
    global $DB;

    $result = array();

    $sql_query = "SELECT CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS fullname, moodle_user.id, user_rol.id_jefe
                  FROM {talentospilos_user_rol} AS user_rol
                       INNER JOIN {user} AS moodle_user ON moodle_user.id = user_rol.id_usuario
                  WHERE id_rol = (SELECT id
                                  FROM {talentospilos_rol}
                                  WHERE nombre_rol = 'practicante_ps')
                        AND id_instancia = $instance_id
                        AND id_semestre =". get_current_semester()->max
                        ."ORDER BY fullname";

    $result = $DB->get_records_sql($sql_query);

    if(!$result){
        array_push($result, array('fullname'=>'N.R.'));
    }

    return $result;
}

/**
 * Función que extrae los monitores socioeducativos asignados para el periodo actual en una 
 * instancia dada.
 *
 * @see get_practicing_by_instance
 * @param $instance_id       --> Identificador de instancia
 * @return Array 
 */
function get_monitors_by_instance($instance_id){
    
    global $DB;

    $result = array();

    $sql_query = "SELECT CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS fullname, moodle_user.id
                  FROM {talentospilos_user_rol} AS user_rol
                       INNER JOIN {user} AS moodle_user ON moodle_user.id = user_rol.id_usuario
                  WHERE id_rol = (SELECT id
                                  FROM {talentospilos_rol}
                                  WHERE nombre_rol = 'monitor_ps')
                        AND id_instancia = $instance_id
                        AND id_semestre =". get_current_semester()->max
                  ."ORDER BY fullname";

    $result = $DB->get_records_sql($sql_query);

    if(!$result){
        array_push($result, array('fullname'=>'N.R.'));
    }

    return $result;
}

/**
 * Función que devuelve el resumen de un grupo de cohortes
 * dado un identificador de cohorte o un fragmento de el
 *
 * @see get_summary_group_cohorts
 * @param $number_id  -->  Identificador de cohorte
 * @param $instance_id  -->  Identificador de la instancia
 * @return Array 
 */
function get_summary_group_cohorts($number_id, $instance_id){

    global $DB;

    $result_summary = array();
    $result_cohorts = array();

    $cohorts = get_cohorts_by_idnumber($number_id);

    foreach($cohorts as $ch){
        $cohort = new stdClass();
        $cohort->id = $ch->id;
        $cohort->name = $ch->name;
        $cohort->idnumber = $ch->idnumber;
        $cohort->summary_sra = count_student_by_cohort($ch->idnumber, 'sra', $instance_id);
        $cohort->summary_ases = count_student_by_cohort($ch->idnumber, 'ases', $instance_id);
        $cohort->total_ases = total_students_by_cohort($ch->idnumber, 'ases', $instance_id);
        $cohort->total_sra = total_students_by_cohort($ch->idnumber, 'sra', $instance_id);
        $cohort->total_gen = total_students_by_cohort($ch->idnumber, 'total', $instance_id);

        array_push($result_cohorts, $cohort);
    }

    $result_summary['cohorts'] = $result_cohorts;

    // Total de estudiantes en el grupo de cohortes
    $sql_query = "SELECT COUNT(cohort_member.userid) AS total_number
                  FROM {cohort} AS cohort 
                       INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                       INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = cohort_member.userid
                  WHERE user_extended.tracking_status = 1 
                        AND cohort.idnumber LIKE '$number_id%'";
    
    $result = $DB->get_record_sql($sql_query);

    $result_summary['total_students'] = $result->total_number;

    // Total de estudiantes activos en SRA
    $sql_query = "SELECT COUNT(cohort_member.userid) AS total_sra
                  FROM {cohort} AS cohort 
                    INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                    INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = cohort_member.userid
                  WHERE user_extended.tracking_status = 1 
                        AND (user_extended.program_status = (SELECT id FROM {talentospilos_estad_programa} WHERE nombre = 'ACTIVO')
                             OR user_extended.program_status = (SELECT id FROM {talentospilos_estad_programa} WHERE nombre = 'REGULARIZADO')
                             OR user_extended.program_status = (SELECT id FROM {talentospilos_estad_programa} WHERE nombre = 'ADMISION'))
                        AND cohort.idnumber LIKE '$number_id%'";

    $result = $DB->get_record_sql($sql_query);

    $result_summary['total_sra'] = $result->total_sra;

    // Total de estudiantes con seguimiento en la estrategia ASES
    $sql_query = "SELECT COUNT(cohort_member.userid) AS total_tracking
                  FROM {cohort} AS cohort 
                       INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                       INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = cohort_member.userid
                       INNER JOIN (SELECT MAX(status.fecha) AS fecha, id_estudiante 
                                   FROM {talentospilos_est_estadoases} AS status
                                   WHERE status.id_estado_ases = (SELECT id
                                                                  FROM {talentospilos_estados_ases} AS ases_status 
                                                                  WHERE ases_status.nombre = 'seguimiento')
                                         AND status.id_instancia = $instance_id
                                   GROUP BY  id_estudiante) AS student_ases_status ON student_ases_status.id_estudiante = user_extended.id_ases_user  
                  WHERE user_extended.tracking_status = 1
                        AND cohort.idnumber LIKE '$number_id%'";

    $result = $DB->get_record_sql($sql_query);

    $result_summary['total_tracking'] = $result->total_tracking;
    
    return $result_summary;
}

/**
 * Función que devuelve el conteo de estudiantes dado 
 * un estado y una cohorte
 *
 * @see count_student_by_cohort
 * @param $number_id  -->  Identificador de cohorte
 * @param $status_type -->  Estado por el cual se filtrará el conteo
 * @param $instance_id  -->  Identificador de la instancia
 * @return Array 
 */

 function count_student_by_cohort($number_id, $status_type, $instance_id){

    global $DB;

    $summary_cohorts = array();

    if($status_type == 'sra'){

        $sql_query = "SELECT *
                      FROM {talentospilos_estad_programa}";

        $program_statuses = $DB->get_records_sql($sql_query);

        

        foreach($program_statuses as $status){

            $sql_query = "SELECT COUNT(cohort_member.userid) AS total_sra
                      FROM {cohort} AS cohort 
                        INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                        INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = cohort_member.userid
                      WHERE user_extended.tracking_status = 1 
                        AND user_extended.program_status = (SELECT id FROM {talentospilos_estad_programa} WHERE nombre = '$status->nombre')
                        AND cohort.idnumber = '$number_id'";
            
            $result = $DB->get_record_sql($sql_query);;

            $summary_cohort['name'] = $status->nombre;
            $summary_cohort['total'] = $result->total_sra;

            array_push($summary_cohorts, $summary_cohort);
        }        
    }else{

        $names_mask = array();
        $names_mask['seguimiento'] = 'SEGUIMIENTO';
        $names_mask['sinseguimiento'] = 'SIN SEGUIMIENTO';

        $sql_query = "SELECT *
                      FROM {talentospilos_estados_ases}";

        $ases_statuses = $DB->get_records_sql($sql_query);

        // Totales por estado
        foreach($ases_statuses as $status){

            $sql_query = "SELECT COUNT(cohort_member.userid) AS total_tracking
                    FROM {cohort} AS cohort 
                        INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                        INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = cohort_member.userid
                        INNER JOIN (SELECT MAX(status.fecha) AS fecha, id_estudiante 
                                    FROM {talentospilos_est_estadoases} AS status
                                    WHERE status.id_estado_ases = (SELECT id
                                                                    FROM {talentospilos_estados_ases} AS ases_status 
                                                                    WHERE ases_status.nombre = '$status->nombre')
                                            AND status.id_instancia = $instance_id
                                    GROUP BY  id_estudiante) AS student_ases_status ON student_ases_status.id_estudiante = user_extended.id_ases_user  
                    WHERE user_extended.tracking_status = 1
                            AND cohort.idnumber LIKE '$number_id%'";

            $result = $DB->get_record_sql($sql_query);

            $summary_cohort['name'] = $names_mask[$status->nombre];
            $summary_cohort['total'] = $result->total_tracking;

            array_push($summary_cohorts, $summary_cohort);
        }
    }

    return $summary_cohorts;
}

function total_students_by_cohort($number_id, $status_type, $instance_id){

    global $DB;

    if($status_type == 'sra'){

        $sql_query = "SELECT COUNT(cohort_member.userid) AS total_sra
                  FROM {cohort} AS cohort 
                    INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                    INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = cohort_member.userid
                  WHERE user_extended.tracking_status = 1 
                        AND (user_extended.program_status = (SELECT id FROM {talentospilos_estad_programa} WHERE nombre = 'ACTIVO')
                             OR user_extended.program_status = (SELECT id FROM {talentospilos_estad_programa} WHERE nombre = 'REGULARIZADO')
                             OR user_extended.program_status = (SELECT id FROM {talentospilos_estad_programa} WHERE nombre = 'ADMISION'))
                        AND cohort.idnumber LIKE '$number_id%'";

        $result = $DB->get_record_sql($sql_query);

        return $result->total_sra;

    }elseif($status_type == 'ases'){

        $sql_query = "SELECT COUNT(cohort_member.userid) AS total_tracking
                  FROM {cohort} AS cohort 
                       INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
                       INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = cohort_member.userid
                       INNER JOIN (SELECT MAX(status.fecha) AS fecha, id_estudiante 
                                   FROM {talentospilos_est_estadoases} AS status
                                   WHERE status.id_estado_ases = (SELECT id
                                                                  FROM {talentospilos_estados_ases} AS ases_status 
                                                                  WHERE ases_status.nombre = 'seguimiento')
                                         AND status.id_instancia = $instance_id
                                   GROUP BY  id_estudiante) AS student_ases_status ON student_ases_status.id_estudiante = user_extended.id_ases_user  
                  WHERE user_extended.tracking_status = 1
                        AND cohort.idnumber LIKE '$number_id%'";

        $result = $DB->get_record_sql($sql_query);

        return $result->total_tracking;

    }else{
        $sql_query = "SELECT COUNT(cohort_member.userid) AS total_number
        FROM {cohort} AS cohort 
             INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
             INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = cohort_member.userid
        WHERE user_extended.tracking_status = 1 
              AND cohort.idnumber LIKE '$number_id%'";

        $result = $DB->get_record_sql($sql_query);

        return $result->total_number;
    }
}

/**
 * Función que retorna el conjunto de estados ASES
 *
 * @see get_ases_statuses
 * @return Array 
 */
function get_ases_statuses(){

    global $DB;

    $sql_query = "SELECT *
                  FROM {talentospilos_estados_ases}";

    $result = $DB->get_records_sql($sql_query);

    return $result;
}

/**
 * Función que retorna el conjunto de estados en SRA
 *
 * @see get_academic_program_statuses
 * @return Array 
 */
function get_academic_program_statuses(){

    global $DB;

    $sql_query = "SELECT *
                  FROM {talentospilos_estad_programa}";

    $result = $DB->get_records_sql($sql_query);

    return $result;
}

/**
 * Función que retorna el conjunto de estados en ICETEX
 *
 * @see get_icetex_states
 * @return Array 
 */

function get_icetex_states(){

    global $DB;

    $sql_query = "SELECT *
                  FROM {talentospilos_estados_icetex}";

    $result = $DB->get_records_sql($sql_query);

    return $result;
}

/**
 * Función que retorna el conjunto de estados en ICETEX
 *
 * @see get_icetex_statuses
 * @return Array 
 */
// function get_icetex_statuses(){

//     global $DB;

//     $sql_query = "SELECT *
//                   FROM {talentospilos_estados_icetex}";

//     $result = $DB->get_records_sql($sql_query);

//     return $result;
// }

