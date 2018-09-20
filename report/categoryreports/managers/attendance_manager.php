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
 * Script for management of attendance
 *
 * @package   report_categoryreports
 * @copyright 2018 Iader E. García G.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

function get_programs(){

    global $DB;

    $sql_query = "SELECT id FROM {course_categories} AS categories WHERE name = 'CENTRO DE LENGUAS Y CULTURAS'";
    $id_category_parent = $DB->get_record_sql($sql_query)->id;

    $sql_query = "SELECT id, name 
                  FROM {course_categories} AS categories 
                  WHERE parent = $id_category_parent
                  ORDER BY name ASC";

    $programs = $DB->get_records_sql($sql_query);

    return $programs;
}

function get_courses_category($id_program, $id_period=null){

    global $DB;

    $sql_query = "SELECT id, fullname 
                  FROM {course} AS courses 
                  WHERE category = $id_program
                  ORDER BY fullname";

    $courses = $DB->get_records_sql($sql_query);

    return $courses;
}

function get_attendance($id_course){

    global $DB;

    $sql_query = "SELECT id 
                  FROM {attendance}
                  WHERE course = $id_course";
    
    $id_attendance = $DB->get_record_sql($sql_query);

    $sql_query = "SELECT statuses.id, statuses.acronym, statuses.description
                  FROM {attendance_statuses} AS statuses
                  INNER JOIN {attendance} AS attendance ON attendance.id = statuses.attendanceid
                  WHERE attendance.course = $id_course"; 

    $statuses_array = $DB->get_records_sql($sql_query);

    $columns = array();
    $result_to_return = array();
    array_push($columns,  array("title"=>"Apellido(s)", "name"=>'lastname', "data"=>'lastname'));
    array_push($columns,  array("title"=>"Nombre(s)", "name"=>'firstname', "data"=>'firstname'));
    array_push($columns,  array("title"=>"Correo electrónico", "name"=>'email', "data"=>'email'));

    $select_clause = "SELECT moodle_users.lastname, moodle_users.firstname, moodle_users.email";
    
    foreach($statuses_array as $status){

        array_push($columns, array("title"=>$status->description, "name"=>strtolower($status->acronym), "data"=>strtolower($status->acronym)));
        
        $select_clause .= ", (SELECT COUNT(att_log.statusid)
                            FROM mdl_attendance_log AS att_log
                            INNER JOIN mdl_attendance_sessions AS att_sessions ON att_log.sessionid = att_sessions.id
                            INNER JOIN mdl_attendance AS att ON att.id = att_sessions.attendanceid
                            WHERE att_log.statusid = $status->id AND att_log.studentid = moodle_users.id
                            ) AS $status->acronym ";
    }

    $from_clause = " FROM mdl_enrol AS enrols
                        INNER JOIN mdl_user_enrolments AS user_enrolments  ON user_enrolments.enrolid = enrols.id
                        INNER JOIN mdl_user AS moodle_users ON moodle_users.id = user_enrolments.userid
                     WHERE enrols.courseid = $id_course AND enrols.enrol = 'manual' AND enrols.roleid = 5";

    $sql_query = $select_clause.$from_clause;

    $result_query = $DB->get_records_sql($sql_query);

    foreach($result_query as $result){
        array_push($result_to_return, $result);
    }

    $data = array(
        "bsort" => false,
        "data"=> $result_to_return,
        "columns" => $columns,
        "select" => "false",
        "fixedHeader"=> array(
            "header"=> true,
            "footer"=> true
        ),
        "scrollX" => true,
        "scrollCollapse" => true,
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
        "autoFill"=>"true",
        "dom"=> "lifrtpB",
        "tableTools"=>array(
            "sSwfPath"=>"../../style/swf/flashExport.swf"
        ),
        "buttons"=>array(
            array(
                "extend" => "print",
                "text" => 'Imprimir',

            ), 
            array(
                "extend" => "csv",
                "text" => 'CSV',
            ),
            array(
                "extend" => "excel",
                "text" => 'Excel',
                "className" => 'buttons-excel',
                "filename" => 'Export excel',
                "extension" => '.xls'
            ),
        )
    );

    return $data;

}