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
 * @author     Juan Pablo Moreno Muñoz
 * @package    block_ases
 * @copyright  2018 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

	require_once(dirname(__FILE__). '/../../../../config.php');
	require_once('icetex_reports_lib.php');

	if(isset($_POST['resR']) && $_POST['resR'] == 'resReport'){		
		$columns = array();
		$resolution_options = get_all_resolutions_codes();
		array_push($columns, array("title"=>"Código resolución".$resolution_options, "name"=>"codigo_resolucion", "data"=>"codigo_resolucion"));
		array_push($columns, array("title"=>"Semestre", "name"=>"nombre", "data"=>"nombre"));
		array_push($columns, array("title"=>"Nota crédito", "name"=>"nota_credito", "data"=>"nota_credito"));
		array_push($columns, array("title"=>"Total", "className"=>"dt-body-right", "name"=>"monto_total", "data"=>"monto_total"));
		array_push($columns, array("title"=>"Total monto estudiantes", "className"=>"dt-body-right", "name"=>"monto_sum_estudiantes", "data"=>"monto_sum_estudiantes"));
		array_push($columns, array("title"=>"Diferencia", "className"=>"dt-body-right","name"=>"monto_diferencia", "data"=>"monto_diferencia"));

		$data = array(
					"bsort" => false,
					"columns" => $columns,					
					"data" => get_resolutions_for_report(),
					"language" => 
                	 array(
                    	"search"=> "Buscar:",
                    	"oPaginate" => array(
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
					"order"=> array(0, "desc"),
					"dom"=>'lifrtpB',

					"buttons"=>array(
						array(
							"extend"=>'print',
							"text"=>'Imprimir'
						),
						array(
							"extend"=>'csvHtml5',
							"text"=>'CSV'
						),
						array(
							"extend" => "excel",
											"text" => 'Excel',
											"className" => 'buttons-excel',
											"filename" => 'Export excel',
											"extension" => '.xls'
						)
					)

				);
			header('Content-Type: application/json');
		echo json_encode($data);
	}