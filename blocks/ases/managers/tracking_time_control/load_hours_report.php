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
 * @author     Isabella Serna Ramírez
 * @package    block_ases
 * @copyright  2017 Isabella Serna Ramírez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('tracking_time_control_lib.php');
require_once('tracking_time_control_functions.php');
require_once dirname(__FILE__) .'/../periods_management/periods_lib.php';


    if (isset($_POST['initial_hour'])&&isset($_POST['final_hour'])){
        if($_POST['initial_hour']==0 && $_POST['final_hour']==0){
            $current_semester =get_current_semester();
            $semester_interval=get_semester_interval($current_semester->max);
            $initial_hour=$semester_interval->fecha_inicio;
            $final_hour=$semester_interval->fecha_fin;

        }else{
            $initial_hour=$_POST['initial_hour'];
            $final_hour=$_POST['final_hour'];

        }
    


    }


    $columns = array();
    array_push($columns, array("title"=>"Fecha", "name"=>"fecha", "data"=>"fecha"));
    array_push($columns, array("title"=>"Número horas", "name"=>"hours", "data"=>"hours"));
    array_push($columns, array("title"=>"Número minutos", "name"=>"minutes", "data"=>"total_minutes"));

        $data = array(
                "bsort" => false,
                "columns" => $columns,
                "data"=> get_hours_per_days($initial_hour,$final_hour,$_POST['monitorid']),
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
                 ),
                 "order"=> array(0, "desc" )
        );
    header('Content-Type: application/json');
echo json_encode($data); 


