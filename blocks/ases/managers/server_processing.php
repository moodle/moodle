<?php

require_once('query.php');


$columns = array();
$poblacion = array();
$campos_consulta = array();
$riesgos_consulta = array();
$name_columns = new stdClass();

if(isset($_POST['cohorte'])){
    array_push($poblacion, $_POST['cohorte']);
}

if(isset($_POST['estado'])){
    array_push($poblacion, $_POST['estado']);
}

if(isset($_POST['chk']) && isset($_POST['idinstancia'])){

    $risk = -1; //Que hace esta variable??
    $counter = 0;
    
    foreach($_POST['chk'] as $chk)
    {
        array_push($columns, array("title"=>$chk, "name"=>$chk, "data"=>$chk));
        array_push($campos_consulta, $chk);
    }
    
    
    if(isset($_POST['chk_risk'])){
        
        foreach($_POST['chk_risk'] as $chk_risk)
        {
            
            // if($chk_risk == 'academic_risk'){
            //     $title = "R.A";
            //     $risk = 0;
            // }
            // else if($chk_risk == 'social_risk'){
            //     $title = "R.S";
            //     $risk = 1;
            // }
            
            $query_nombre = "SELECT * FROM {talentospilos_riesgos_ases} WHERE id =".$chk_risk;
            // print_r($query_nombre);
            // die();
            $nombre_riesgo = $DB->get_record_sql($query_nombre)->nombre;
            array_push($columns, array("title"=>'R.'.strtoupper(substr($nombre_riesgo, 0, 1)).substr($nombre_riesgo, 1, 2), "name"=>$nombre_riesgo, "data"=>$nombre_riesgo));
            array_push($riesgos_consulta, $chk_risk);
            $counter = $counter + 1;            
        }
        // if($counter >= 2){
        //   $risk = $counter;
        // }
    }
    // else{
    //     $risk = -1;
    // }
    
    //$result = get_usersByPopulation($campos_consulta, $poblacion, $risk, $_POST['idinstancia']);
    $result = get_usersByPopulation($campos_consulta, $poblacion, $riesgos_consulta, $_POST['idinstancia']);
    
    $data = array(
                "bsort" => false,
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
                "autoFill"=>"true",
                "dom"=> "lfrtBip",
                "buttons"=>array(
                                array("extend"=>"pdf", "message"=>"Generando PDF"),
                                "csv",
                                "excel"
                            )
        );


header('Content-Type: application/json');
$prueba = new stdClass();
$prueba->data = $data;
$prueba->colums = $result->columns;
echo json_encode($prueba);

}
// $columns = array();
// $poblacion = array();
// $campos_consulta = array();
// $name_columns = new stdClass();
// if(isset($_POST['cohorte'])){
//     array_push($poblacion, $_POST['cohorte']);
// }

// if(isset($_POST['estado'])){
//     array_push($poblacion, $_POST['estado']);
// }

// if(isset($_POST['chk']) && isset($_POST['idinstancia'])){

//     $risk = -1; //Que hace esta variable??
//     $counter = 0;
    
//     foreach($_POST['chk'] as $chk)
//     {
//         array_push($columns, array("title"=>$chk, "name"=>$chk, "data"=>$chk));
//         array_push($campos_consulta, $chk);
//     }
    
    
//     if(isset($_POST['chk_risk'])){
        
//         foreach($_POST['chk_risk'] as $chk_risk)
//         {
            
//             // if($chk_risk == 'academic_risk'){
//             //     $title = "R.A";
//             //     $risk = 0;
//             // }
//             // else if($chk_risk == 'social_risk'){
//             //     $title = "R.S";
//             //     $risk = 1;
//             // }
            
//             $query_nombre = "SELECT * FROM {talentospilos_riesgos_ases} WHERE id =".$chk_risk;
//             // print_r($query_nombre);
//             // die();
//             $nombre_riesgo = $DB->get_record_sql($query_nombre)->nombre;
//             array_push($columns, array("title"=>'R.'.strtoupper(substr($nombre_riesgo, 0, 1)).substr($nombre_riesgo, 1, 2), "name"=>$nombre_riesgo, "data"=>$nombre_riesgo));
//             array_push($riesgos_consulta, $chk_risk);
//             $counter = $counter + 1;            
//         }
//         // if($counter >= 2){
//         //   $risk = $counter;
//         // }
//     }
//     // else{
//     //     $risk = -1;
//     // }
    
//     //$result = get_usersByPopulation($campos_consulta, $poblacion, $risk, $_POST['idinstancia']);
//     $result = get_usersByPopulation($campos_consulta, $poblacion, $riesgos_consulta, $_POST['idinstancia']);
//     //print_r($result);
//     $data = array(
//                 "bsort" => false,
//                 "data"=> $result->data,
//                 "columns" => $columns,
//                 "select" => "false",
//                 "language" => 
//                  array(
//                     "search"=> "Buscar:",
//                     "oPaginate" => array (
//                         "sFirst"=>    "Primero",
//                         "sLast"=>     "Último",
//                         "sNext"=>     "Siguiente",
//                         "sPrevious"=> "Anterior"
//                     ),
//                     "sProcessing"=>     "Procesando...",
//                     "sLengthMenu"=>     "Mostrar _MENU_ registros",
//                     "sZeroRecords"=>    "No se encontraron resultados",
//                     "sEmptyTable"=>     "Ningún dato disponible en esta tabla",
//                     "sInfo"=>           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
//                     "sInfoEmpty"=>      "Mostrando registros del 0 al 0 de un total de 0 registros",
//                     "sInfoFiltered"=>   "(filtrado de un total de _MAX_ registros)",
//                     "sInfoPostFix"=>    "",
//                     "sSearch"=>         "Buscar:",
//                     "sUrl"=>            "",
//                     "sInfoThousands"=>  ",",
//                     "sLoadingRecords"=> "Cargando...",
//                     "oAria"=> array(
//                         "sSortAscending"=>  ": Activar para ordenar la columna de manera ascendente",
//                         "sSortDescending"=> ": Activar para ordenar la columna de manera descendente"
//                     )
//                  ),
//                 "autoFill"=>"true",
//                 "dom"=> "lfrtBip",
//                 "buttons"=>array(
//                                 array("extend"=>"pdf", "message"=>"Generando PDF"),
//                                 "csv",
//                                 "excel"
//                             )
//         );


// header('Content-Type: application/json');
// $prueba = new stdClass();
// $prueba->data = $data;
// $prueba->colums = $result->columns;
// echo json_encode($prueba);

// }
?>