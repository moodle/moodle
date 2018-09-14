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
 * @author     Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  2018 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once __DIR__ . '/../../../../config.php';
require_once $CFG->dirroot . '/blocks/ases/managers/historic_icetex_reports/icetex_reports_lib.php';
require_once $CFG->dirroot . '/blocks/ases/managers/student_profile/academic_lib.php';

/**
 * Función que recupera datos para la tabla de reporte historico academico por estudiantes,
 * de los estudiantes asignados a una cohorte de las relacionadas con la instancia
 *
 * @see get_datatable_array_Students($instance_id)
 * @param $instance_id  --> Instancia del módulo
 * @return Array
 */
function get_datatable_array_Students($instance_id)
{
    $cohort_options = get_cohort_names($instance_id);
    $semester_options = get_all_semesters_names();
    $default_students = $columns = array();
    $estimulo_options = "<select>
                        <option value=''></option>
                        <option value='NO'>NO</option>
                        <option value='SI'>SI</option>
                        </select>";
    $bajos_options = "<select>
                        <option value=''></option>
                        <option value='NO'>NO</option>
                        <option value='1'>1</option>
                        <option value='2'>2</option>
                        <option value='3'>3</option>
                        </select>";
    array_push($columns, array("title" => "Cohorte" . $cohort_options, "name" => "cohorte", "data" => "cohorte"));
    array_push($columns, array("title" => "Número de documento", "name" => "num_doc", "data" => "num_doc"));
    array_push($columns, array("title" => "Código estudiante", "name" => "username", "data" => "username"));
    array_push($columns, array("title" => "Nombre(s)", "name" => "firstname", "data" => "firstname"));
    array_push($columns, array("title" => "Apellido(s)", "name" => "lastname", "data" => "lastname"));
    array_push($columns, array("title" => "Semestre" . $semester_options, "name" => "semestre", "data" => "semestre"));
    array_push($columns, array("title" => "Programa", "name" => "programa", "data" => "programa"));
    array_push($columns, array("title" => "Materias Perdidas", "name" => "perdidas", "data" => "perdidas"));
    array_push($columns, array("title" => "Cancela", "name" => "cancel", "data" => "cancel"));
    array_push($columns, array("title" => "Promedio Semestre", "name" => "promsem", "data" => "promsem"));
    array_push($columns, array("title" => "Gano Estimulo" . $estimulo_options, "name" => "estim", "data" => "estim"));
    array_push($columns, array("title" => "Cae en Bajo" . $bajos_options, "name" => "bajo", "data" => "bajo"));
    array_push($columns, array("title" => "Promedio Acumulado", "name" => "promacum", "data" => "promacum"));
    array_push($columns, array("title" => "Estimulos Acumulados", "name" => "Numestim", "data" => "estim"));
    array_push($columns, array("title" => "Bajos Acumulados", "name" => "bajos", "data" => "bajos"));

    $default_students = get_historic_report($instance_id);

    $data_to_table = array(
        "bsort" => false,
        "data" => $default_students,
        "columns" => $columns,
        "select" => "false",
        "fixedHeader" => array(
            "header" => true,
            "footer" => true,
        ),
        "language" => array(
            "search" => "Buscar:",
            "oPaginate" => array(
                "sFirst" => "Primero",
                "sLast" => "Último",
                "sNext" => "Siguiente",
                "sPrevious" => "Anterior",
            ),
            "sProcessing" => "Procesando...",
            "sLengthMenu" => "Mostrar _MENU_ registros",
            "sZeroRecords" => "No se encontraron resultados",
            "sEmptyTable" => "Ningún dato disponible en esta tabla",
            "sInfo" => "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty" => "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered" => "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix" => "",
            "sSearch" => "Buscar:",
            "sUrl" => "",
            "sInfoThousands" => ",",
            "sLoadingRecords" => "Cargando...",
            "oAria" => array(
                "sSortAscending" => ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending" => ": Activar para ordenar la columna de manera descendente",
            ),
        ),
        "autoFill" => "true",
        "dom" => "lfrtBip",
    );

    return $data_to_table;

}

/**
 * Retorna un arreglo con la informacion de la tabla de historico academico
 * @see get_historic_report($id_instance)
 * @param $id_instance --> id del modulo
 * @return Array --> info historic_academic_report
 */

function get_historic_report($id_instance)
{

    global $DB;
    $array_historic = array();

    $query = "  SELECT     historic.id as id,
                           programa.id as program_id,
                           usuario.id as student_id,
                           num_doc,
                           substring(username from 1 FOR 7) AS username,
                           firstname,
                           lastname,
                           semestre.nombre    AS semestre,
                           promedio_semestre  AS promsem,
                           promedio_acumulado AS promacum,
                           programa.nombre    AS programa,
                           cohorte.name       AS cohorte,
                           json_materias
                FROM       {talentospilos_history_academ} historic
                INNER JOIN {talentospilos_usuario} usuario
                ON         historic.id_estudiante = usuario.id
                INNER JOIN {talentospilos_semestre} semestre
                ON         historic.id_semestre = semestre.id
                INNER JOIN {talentospilos_programa} programa
                ON         historic.id_programa = programa.id
                INNER JOIN {talentospilos_user_extended} user_ext
                ON         historic.id_estudiante = user_ext.id_ases_user
                INNER JOIN {user} user_moodle
                ON         user_ext.id_moodle_user = user_moodle.id
                INNER JOIN {cohort_members} memb
                ON         memb.userid = user_moodle.id
                INNER JOIN {cohort} cohorte
                ON         memb.cohortid = cohorte.id
                WHERE memb.cohortid IN (SELECT id_cohorte
                                    FROM   {talentospilos_inst_cohorte}
                                    WHERE  id_instancia = $id_instance) AND 
                                    username LIKE (SELECT CONCAT('%-', prg.cod_univalle)
                                                   FROM {talentospilos_programa} prg 
                                                   WHERE prg.id = programa.id) ";

    $historics = $DB->get_records_sql($query);

    foreach ($historics as $historic) {
        //validate cancel
        $query_cancel = "SELECT * FROM {talentospilos_history_cancel} WHERE id_history = $historic->id ";

        $cancel = $DB->get_record_sql($query_cancel);

        if ($cancel) {
            $fecha = date('m/d/Y', $cancel->fecha_cancelacion);

            $historic->cancel = $fecha;
        } else {
            $historic->cancel = "NO";
        }

        //validate estimulo
        $query_estimulo = "SELECT * FROM {talentospilos_history_estim} WHERE id_history = $historic->id ";

        $estimulo = $DB->get_record_sql($query_estimulo);

        if ($estimulo) {
            $historic->estim = "SI";
        } else {
            $historic->estim = "NO";
        }

        //validate bajo
        $query_bajo = "SELECT * FROM {talentospilos_history_bajos} WHERE id_history = $historic->id ";

        $bajo = $DB->get_record_sql($query_bajo);

        if ($bajo) {
            $historic->bajo = $bajo->numero_bajo;
        } else {
            $historic->bajo = "NO";
        }

        //validate estimulos
        $estimulos = get_estimulos($historic->student_id, $historic->program_id);
        $historic->Numestim = $estimulos;

        //validate bajos
        $bajos = get_bajos_rendimientos($historic->student_id, $historic->program_id);
        $historic->bajos = $bajos;

        //validate materias perdidas
        $materias = json_decode($historic->json_materias);
        $perdidas = 0;

        if ($materias) {
            foreach ($materias as $materia) {
                if (is_numeric($materia->nota) and $materia->nota < 3) {
                    $perdidas++;
                }
            }

        }

        $historic->perdidas = $perdidas;
        array_push($array_historic, $historic);
    }

    return $array_historic;
}

/**
 * Función que recupera datos para la tabla de reporte historico academico por totales,
 * de las cohortes relacionadas con la instancia
 *
 * @see get_datatable_array_totals($instance_id)
 * @param $instance_id  --> Instancia del módulo
 * @return Array
 */
function get_datatable_array_totals($instance_id)
{
    $default_students = $columns = array();
    $cohort_options = get_cohort_names($instance_id);
    $semester_options = get_all_semesters_names();

    array_push($columns, array("title" => "Cohorte".$cohort_options, "name" => "cohorte", "data" => "cohorte"));
    array_push($columns, array("title" => "Semestre".$semester_options, "name" => "semestre", "data" => "semestre"));
    array_push($columns, array("title" => "Total Activos", "name" => "act", "data" => "act"));
    array_push($columns, array("title" => "Total Inactivos", "name" => "inact", "data" => "inact"));
    array_push($columns, array("title" => "Total Cohorte", "n" => "total", "data" => "total"));

    $default_students = get_Totals_report($instance_id);

    $data_to_table = array(
        "bsort" => false,
        "data" => $default_students,
        "columns" => $columns,
        "select" => "false",
        "fixedHeader" => array(
            "header" => true,
            "footer" => true,
        ),
        "language" => array(
            "search" => "Buscar:",
            "oPaginate" => array(
                "sFirst" => "Primero",
                "sLast" => "Último",
                "sNext" => "Siguiente",
                "sPrevious" => "Anterior",
            ),
            "sProcessing" => "Procesando...",
            "sLengthMenu" => "Mostrar _MENU_ registros",
            "sZeroRecords" => "No se encontraron resultados",
            "sEmptyTable" => "Ningún dato disponible en esta tabla",
            "sInfo" => "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty" => "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered" => "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix" => "",
            "sSearch" => "Buscar:",
            "sUrl" => "",
            "sInfoThousands" => ",",
            "sLoadingRecords" => "Cargando...",
            "oAria" => array(
                "sSortAscending" => ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending" => ": Activar para ordenar la columna de manera descendente",
            ),
        ),
        "autoFill" => "true",
        "dom" => "lfrtBip",
    );

    return $data_to_table;

}

/**
 * Retorna un arreglo con la informacion de la tabla de historico academico
 * @see get_Totals_report($id_instance)
 * @param $id_instance --> id del modulo
 * @return Array --> info totals_historic_academic_report
 */

function get_Totals_report($id_instance)
{

    global $DB;
    $array_historic = array();

    $query = "SELECT DISTINCT row_number() over(),semestre.nombre as semestre,
                     cohorte.name as cohorte,
                     COUNT(academ.id) as total
              FROM {talentospilos_history_academ} academ
              INNER JOIN {talentospilos_semestre} semestre
              ON         academ.id_semestre = semestre.id
              INNER JOIN {talentospilos_user_extended} extend
              ON         academ.id_estudiante = extend.id_ases_user
              INNER JOIN {user} user_moodle 
              ON         user_moodle.id = extend.id_moodle_user
              INNER JOIN {talentospilos_programa} programa
              ON         academ.id_programa = programa.id
              INNER JOIN {cohort_members} memb
              ON         memb.userid = extend.id_moodle_user
              INNER JOIN {cohort} cohorte
              ON         memb.cohortid = cohorte.id
              WHERE memb.cohortid IN (SELECT id_cohorte
                                    FROM   {talentospilos_inst_cohorte}
                                    WHERE  id_instancia = $id_instance) AND extend.tracking_status = 1 AND 
                                    username LIKE (SELECT CONCAT('%-', prg.cod_univalle)
                                                   FROM {talentospilos_programa} prg 
                                                   WHERE prg.id = programa.id)
              GROUP BY semestre, cohorte";

    $historics = $DB->get_records_sql($query);

    foreach ($historics as $historic) {

        $query_cancel = "SELECT row_number() over(), COUNT(cancel.id) as inact,
                                semestre.nombre as semestre,
                                cohorte.name as cohorte
                                FROM {talentospilos_history_academ} academ
                        INNER JOIN {talentospilos_semestre} semestre
                        ON         academ.id_semestre = semestre.id
                        INNER JOIN {talentospilos_history_cancel} cancel
                        ON         academ.id = cancel.id_history
                        INNER JOIN {talentospilos_user_extended} extend
                        ON         academ.id_estudiante = extend.id_ases_user
                        INNER JOIN {cohort_members} memb
                        ON         memb.userid = extend.id_moodle_user
                        INNER JOIN {cohort} cohorte
                        ON         memb.cohortid = cohorte.id
                        WHERE semestre.nombre = '$historic->semestre' AND cohorte.name = '$historic->cohorte'
                        GROUP BY semestre, cohorte";

        $inact = $DB->get_record_sql($query_cancel);

        if (!$inact) {
            $historic->inact = 0;
            $historic->act = $historic->total;
        } else {
            $historic->inact = $inact->inact;
            $historic->act = $historic->total - $inact->inact;
        }

        array_push($array_historic, $historic);

        //

    }

    return $array_historic;

}

/**
 * Function that returns a string with the names of all cohorts
 *
 * @see get_cohort_names($id_instance)
 * @return string
 */
function get_cohort_names($id_instance)
{
    global $DB;

    $cohorts_options = "<select style='width:150px !important'><option value=''></option>";

    $sql_query = "SELECT idnumber AS cohort_name, name as nombre FROM {cohort}
                    WHERE  id IN (SELECT id_cohorte
                                    FROM   {talentospilos_inst_cohorte}
                                    WHERE  id_instancia = $id_instance)";

    $cohorts = $DB->get_records_sql($sql_query);

    foreach ($cohorts as $cohort) {
        $cohorts_options .= "<option value='$cohort->nombre'>$cohort->cohort_name</option>";
    }

    $cohorts_options .= "</select>";

    return $cohorts_options;
}

/**
 * Function that returns a string with the posicion_estimulo
 *
 * @see get_posicion_estimulo($codigo, $programa, $semestre)
 * @return string
 */
function get_posicion_estimulo($codigo, $programa, $semestre)
{
    global $DB;

    $programa_obj = $DB->get_record_sql("SELECT * FROM {talentospilos_programa} WHERE nombre = '$programa' LIMIT 1");
    $cod_programa = $programa_obj->cod_univalle;

    $query = "SELECT usex.id_ases_user as id, usmood.firstname, usmood.lastname
              FROM {user} usmood INNER JOIN {talentospilos_user_extended} usex ON usmood.id = usex.id_moodle_user
              WHERE usmood.username = '$codigo-$cod_programa' LIMIT 1";

    $estudiante = $DB->get_record_sql($query);

    $query_semestre = "SELECT * FROM {talentospilos_semestre} WHERE nombre = '$semestre'";
    $semestre_obj = $DB->get_record_sql($query_semestre);

    $query_puesto = "SELECT estim.puesto_ocupado as puesto
                     FROM {talentospilos_history_academ} acad INNER JOIN {talentospilos_history_estim} estim ON acad.id = estim.id_history
                     WHERE id_semestre = $semestre_obj->id AND id_programa = $programa_obj->id AND id_estudiante = $estudiante->id";

    $puesto = $DB->get_record_sql($query_puesto)->puesto;

    return "El estudiante $estudiante->firstname $estudiante->lastname <br>obtuvo el <b> Puesto: $puesto </b> en los estimulos del programa $programa el semestre $semestre";
}

/**
 * Function that returns a string with the posicion_estimulo
 *
 * @see get_loses($codigo, $programa, $semestre)
 * @return string
 */
function get_loses($codigo, $programa, $semestre)
{
    global $DB;

    $programa_obj = $DB->get_record_sql("SELECT * FROM {talentospilos_programa} WHERE nombre = '$programa' LIMIT 1");
    $cod_programa = $programa_obj->cod_univalle;

    $query = "SELECT usex.id_ases_user as id, usmood.firstname, usmood.lastname
              FROM {user} usmood INNER JOIN {talentospilos_user_extended} usex ON usmood.id = usex.id_moodle_user
              WHERE usmood.username = '$codigo-$cod_programa' LIMIT 1";

    $estudiante = $DB->get_record_sql($query);

    $query_semestre = "SELECT * FROM {talentospilos_semestre} WHERE nombre = '$semestre'";
    $semestre_obj = $DB->get_record_sql($query_semestre);

    $query_materias = "SELECT json_materias as materias
                     FROM {talentospilos_history_academ} acad
                     WHERE id_semestre = $semestre_obj->id AND id_programa = $programa_obj->id AND id_estudiante = $estudiante->id";

    $materias = $DB->get_record_sql($query_materias)->materias;

    $json_materias = json_decode($materias);

    $materias_perdidas = "Las materias perdidas por el estudiante $estudiante->firstname $estudiante->lastname el semestre $semestre en el programa $programa son: <br>";

    foreach ($json_materias as $materia) {
        if (is_numeric($materia->nota) and $materia->nota < 3) {
            $materias_perdidas .= "Cod: $materia->codigo_materia Nombre: $materia->nombre_materia Nota: $materia->nota <br>";
        }
    }

    return $materias_perdidas;
}
