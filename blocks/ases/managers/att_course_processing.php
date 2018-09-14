<?php

require_once('query.php');

$columns = array();
array_push($columns, array("title"=>"Id", "name"=>"idcourse", "className"=>"id", "data"=>"idcourse"));
array_push($columns, array("title"=>"Curso", "name"=>"coursename", "data"=>"coursename"));
array_push($columns, array("title"=>"Faltas injustificadas", "name"=>"injustifiedabsence", "data"=>"injustifiedabsence"));
array_push($columns, array("title"=>"Faltas justificadas", "name"=>"justifiedabsence", "data"=>"justifiedabsence"));
array_push($columns, array("title"=>"Total faltas", "name"=>"total", "data"=>"total"));

if(isset($_POST['dat']))
{
    $data = array(
                "bsort" => false,
                "bPaginate"=> false,
                "searching"=>false,
                "columnDefs"=>array (
                    array(
                        "target"=> "0",
                        "visible"=>false,
                        "searchable"=>false,
                        
                        ),
                    array(
                        "targets"=> "1",
                        "searchable"=>false,
                         "className"=>"my_class"),
                    array(
                        "targets"=> "2",
                        "searchable"=>false),
                    array(
                        "targets"=> "3",
                        "searchable"=>false),
                    array(
                        "targets"=> "4",
                        "searchable"=>false),
                    array(
                        "targets"=> "5",
                        "searchable"=>false),
                ),
                "columns" => $columns,
                "data"=> attendance_by_course($_POST['dat']),
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
                 "order"=> array(2, "desc"),

        );
}
header('Content-Type: application/json');
echo json_encode($data);