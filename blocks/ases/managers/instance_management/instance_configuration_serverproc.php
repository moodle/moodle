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
 * Talentos Pilos
 *
 * @author     Iader E. García 
 * @package    block_ases
 * @copyright  2018 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');
require_once('instance_lib.php');
require_once("../user_management/user_lib.php");
require_once("../periods_management/periods_lib.php");

if(isset($_POST['function'])){
    
    switch($_POST['function']){
        case 'insert_cohort':
            if(isset($_POST['cohort']) && isset($_POST['instance'])){
                insert_cohort($_POST['cohort'], $_POST['instance']);
            }             
            break;
        case 'load_cohorts':
            if(isset($_POST['instance'])){
                load_cohorts_assigned($_POST['instance']);
            }
            break;
        case 'load_cohorts_without_assignment':
            if(isset($_POST['instance'])){
                load_cohorts_without_assignment($_POST['instance']);
            }
            break;
        case 'unassign_cohort':
            if(isset($_POST['instance_id']) && isset($_POST['idnumber_cohort'])){
                unassign_cohort_server_proc($_POST['idnumber_cohort'], $_POST['instance_id']);
            }
            break;
        case 'update_info_instance':
            if(isset($_POST['instance_id']) && isset($_POST['idnumber']) && isset($_POST['description'])){
                update_info_instance_server_proc($_POST['instance_id'], $_POST['idnumber'], $_POST['description']);
            }
            break;
    }
}

 /**
 * Función que organiza los datos a retornar a la interfaz gráfica 
 * al solicitar la inserción de una nueva cohorte a una instancia determinada
 * 
 * @see insert_cohort
 * @param id_cohort  ---> ID cohorte
 * @param id_instance  ---> ID instancia
 * @return JSON
 */
function insert_cohort($id_cohort, $id_instance){

    global $DB;

    $msg_to_return = new stdClass();

    $validate_cohort = validate_cohort($id_cohort, $id_instance);

    if($validate_cohort->count >= 1){
        $msg_to_return->msg = 'Error. La cohorte ya está asignada a la instancia.';
        $msg_to_return->status = 0;
    }else{
        $object_to_record = new stdClass();
        $object_to_record->id_cohorte = $id_cohort;
        $object_to_record->id_instancia = $id_instance;
        $result_insertion = $DB->insert_record('talentospilos_inst_cohorte', $object_to_record);
        $msg_to_return->msg = 'La cohorte ha sido correctamente asignada.';
        $msg_to_return->status = 1;
    }
    
    echo json_encode($msg_to_return);
}

/**
 * Función que organiza los datos a retornar a la interfaz gráfica 
 * al solicitar extraer las cohortes asignadas a una instancia determinada
 * 
 * @see load_cohorts_assigned
 * @param id_instance  ---> ID instancia
 * @return JSON
 */
function load_cohorts_assigned($id_instance){

    $msg_to_return = new stdClass();

    $array_cohorts = load_cohorts_by_instance($id_instance);

    if(count($array_cohorts) == 0){
        $msg_to_return->status = 0;
        $msg_to_return->msg = 'No hay cohortes asigandas a la instancia actual.';
    }else{
        $msg_to_return->status = 1;

        $columns = array();
        array_push($columns, array("title"=>'Identificador', "name"=>'idnumber', "data"=>'idnumber'));
        array_push($columns, array("title"=>'Nombre cohorte', "name"=>'name', "data"=>'name'));
        array_push($columns, array("title"=>'', "name"=>'controls_column', "data"=>'controls_column'));

        $data = array(
            "bsort" => false,
            "data"=> $array_cohorts,
            "columns" => $columns,
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
        );
        $msg_to_return->msg = $data;
    };

    echo json_encode($msg_to_return);
}

/**
 * Función que organiza los datos a retornar a la interfaz gráfica 
 * al solicitar extraer las cohortes sin asignación a una instancia
 * 
 * @see load_cohorts_without_assignment
 * @param id_instance  ---> ID instancia
 * @return JSON
 */

function load_cohorts_without_assignment($id_instance){

    $msg_to_return = new stdClass();

    $array_cohorts_without_assignment = get_cohorts_without_assignment($id_instance);
    if(count($array_cohorts_without_assignment) >= 0){
        $msg_to_return->msg = $array_cohorts_without_assignment;
        $msg_to_return->status = 1;
    }else{
        $msg_to_return->status = 0;
        $msg_to_return->msg= "Error al cargar las cohortes sin asignación. Por favor recargue la página.";
    }

    echo json_encode($msg_to_return);
}

/**
 * Función que organiza los datos a retornar a la interfaz gráfica 
 * al solicitar desasignar una cohorte a una instancia
 * 
 * @see unassign_cohort
 * @param id_number_cohort   ---> ID cohorte
 * @param id_instance  ---> ID instancia
 * @return JSON
 */
function unassign_cohort_server_proc($id_number_cohort, $id_instance){

    $msg_to_return = new stdClass();
    $result_unassignment = unassign_cohort($id_number_cohort, $id_instance);

    if($result_unassignment){
        $msg_to_return->status = 1;
        $msg_to_return->msg = "Cohorte desasignada con éxito.";
    }else{
        $msg_to_return->status = 0;
        $msg_to_return->msg = "Error al desasignar la cohorte.";
    }

    echo json_encode($msg_to_return);
}

/**
 * Función que organiza los datos a retornar a la interfaz gráfica 
 * al solicitar actualizar una instancia determinada
 * 
 * @see unassign_cohort
 * @param instance_id   ---> ID de la instancia
 * @param idnumber  ---> Identificador de la instancia
 * * @param description  ---> Descripción de la instancia
 * @return JSON
 */
function update_info_instance_server_proc($instance_id, $idnumber, $description){

    $msg_to_return = new stdClass();
    $result_update = update_info_instance($instance_id, $idnumber, $description);

    if($result_update){
        $msg_to_return->status = 1;
        $msg_to_return->msg = "Instancia actualizada con éxito.";
    }else{
        $msg_to_return->status = 0;
        $msg_to_return->msg = "Error al actualizar la instancia";
    }

    echo json_encode($msg_to_return);

}