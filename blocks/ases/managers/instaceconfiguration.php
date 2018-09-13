<?php

require('query.php');

if(isset($_POST['function'])){
    
    switch($_POST['function']){
        case 'search': 
            searchUser();
            break;
        case 'load_programs':
            loadPrograms();
            break;
        case 'updateUser':
            updateUser();
            break;
        case 'loadSystemAdministrators':
            loadSystemAdminstrators();
            break;
        case 'deleteUser':
            deleteAdministrator();
    }
}

function searchUser(){
    
    if(isset($_POST['username'])){
        
        echo json_encode(getInfoSystemDirector($_POST['username']));
        
    }else{
        $msg =  new stdClass();
        $msg->Error = "Error al obtener vairbale de consulta de usuario";
        echo json_encode($msg);
    }
}

function loadPrograms(){
    echo json_encode(loadProgramsForSystemsAdmins());
}

function updateUser(){
    if(isset($_POST['username_input']) && isset($_POST['lista_programas']) && isset($_POST['idinstancia']) && isset($_POST['segAca']) &&  isset($_POST['segAsis']) &&  isset($_POST['segSoc'])){
        echo json_encode(updateSystemDirector($_POST['username_input'], $_POST['lista_programas'], $_POST['idinstancia'], $_POST['segAca'], $_POST['segAsis'], $_POST['segSoc']));
    }else{
        echo json_encode("Error al obtener vairbales para la actualizacion  del perfil administrador");
    }
}

function loadSystemAdminstrators(){
    $columns = array();
    array_push($columns, array("title"=>"Código", "name"=>"username", "data"=>"username"));
    array_push($columns, array("title"=>"Nombres", "name"=>"firstname", "data"=>"firstname"));
    array_push($columns, array("title"=>"Apellidos", "name"=>"lastname", "data"=>"lastname"));
    array_push($columns, array("title"=>"Programa", "name"=>"nombre_rol", "data"=>"programa"));
    array_push($columns, array("title"=>"Instancia", "name"=>"nombre_rol", "data"=>"id_instancia"));
    array_push($columns, array("title"=>"Eliminar", "name"=>"button", "data"=>"button"));
    
    $data = array(
                "bsort" => false,
                "columns" => $columns,
                "data"=> getSystemAdministrators(),
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

function deleteAdministrator(){
    if(isset($_POST['username'])){
        echo json_encode(deleteSystemAdministrator($_POST['username']));
    }else{
        echo "no entro";
    }
}

?>