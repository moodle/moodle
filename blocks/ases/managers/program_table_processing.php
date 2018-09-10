<?php
//Esto es para la nueva vista.
require_once('query.php');

$columns = array();
$poblacion = array();
$campos_consulta = array();
$name_columns = new stdClass();

if(isset($_POST['cohorte']))
{
    array_push($poblacion, $_POST['cohorte']);
}

if(isset($_POST['grupo']))
{
    array_push($poblacion, $_POST['grupo']);
}

if(isset($_POST['estado']))
{
    array_push($poblacion, $_POST['estado']);
}

if(isset($_POST['enfasis']))
{
    array_push($poblacion, $_POST['enfasis']);
}

if(isset($_POST['chk'])){

    foreach($_POST['chk'] as $chk)
    {
        array_push($columns, array("title"=>$chk, "name"=>$chk, "data"=>$chk));
        array_push($campos_consulta, $chk);
    }
    if(isset($_POST['chk_risk'])){
        foreach($_POST['chk_risk'] as $chk_risk)
        {
            if($chk_risk == 'academic_risk'){
                $title = "R.A";
            }
            else if($chk_risk == 'social_risk'){
                $title = "R.S";
            }
            array_push($columns, array("title"=>$title, "name"=>$chk_risk, "data"=>$chk_risk));
        }
        $risk = true;
    }
    
    $result = get_usersByPopulation($campos_consulta, $poblacion, $risk);
    
    $data = array(
                "data"=> $result->data,
                "columns" => $columns,
                "select" => "false",
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
                 "autoFill"=>"true"
        );


header('Content-Type: application/json');
$prueba = new stdClass();
$prueba->data = $data;
$prueba->colums = $result->columns;
echo json_encode($prueba);

}
?>