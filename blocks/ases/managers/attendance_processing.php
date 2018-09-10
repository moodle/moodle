<?php

require_once('query.php');

$columns = array();
array_push($columns, array("title"=>"Código", "name"=>"codigoestudiante", "data"=>"codigoestudiante"));
array_push($columns, array("title"=>"Apellidos", "name"=>"apellidos", "data"=>"apellidos"));
array_push($columns, array("title"=>"Nombres", "name"=>"nombres", "data"=>"nombres"));
array_push($columns, array("title"=>"Faltas injustificadas", "name"=>"faltasinjustificadas", "data"=>"faltasinjustificadas"));
array_push($columns, array("title"=>"Faltas justificadas", "name"=>"faltasjustificadas", "data"=>"faltasjustificadas"));
array_push($columns, array("title"=>"Total faltas", "name"=>"totalfaltas", "data"=>"totalfaltas"));

if(isset($_POST['dat']))
{
    $data = array(
                "bsort" => false,
                "columnDefs"=>array (
                    array(
                        "targets"=> 5,
                        "searchable"=>false),
                    array(
                        "targets"=> 4,
                        "searchable"=>false),
                    array(
                        "targets"=> 3,
                        "searchable"=>false,
                        )
                ),
                "columns" => $columns,
                "data"=> general_attendance('1008', $_POST['dat']),
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
                 "order"=> array(3, "desc" ),
                 "dom"=> "lfrtBip",
                 "buttons"=> array("csv", "pdf", "excel")
        );
}

header('Content-Type: application/json');
echo json_encode($data);