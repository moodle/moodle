<?php

require_once('query.php');
if(isset($_POST['idinstancia'])){
    $columns = array();
    array_push($columns, array("title"=>"Código", "name"=>"username", "data"=>"username"));
    array_push($columns, array("title"=>"Nombres", "name"=>"firstname", "data"=>"firstname"));
    array_push($columns, array("title"=>"Apellidos", "name"=>"lastname", "data"=>"lastname"));
    array_push($columns, array("title"=>"Rol", "name"=>"nombre_rol", "data"=>"nombre_rol"));
    array_push($columns, array("title"=>"Eliminar", "name"=>"button", "data"=>"button"));
    
    $data = array(
                "bsort" => false,
                "columns" => $columns,
                "data"=> get_users_role($_POST['idinstancia']),
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
}
