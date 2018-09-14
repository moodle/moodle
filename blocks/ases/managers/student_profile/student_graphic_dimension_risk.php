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
 * General Reports
 *
 * @author     Jeison Cardona Gomez
 * @copyright  2017 Jeison Cardona Gomez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');

 global $DB;

 /**
  * Gets current period (semester)
  *
  * @see getPeriodoActual()
  * @return array containing the period name, starting and ending date
  */
 function getPeriodoActual(){
     global $DB;
     $sql_query = 
        "SELECT *
        FROM {talentospilos_semestre}
        ORDER BY fecha_inicio DESC
        LIMIT 1
        ";
    $retornoConsulta = $DB->get_record_sql($sql_query);
    $periodo = get_object_vars($retornoConsulta);
    return array(
        'nombre_periodo' => $periodo['nombre'],
        'fecha_inicio' => $periodo['fecha_inicio'],
        'fecha_fin' => $periodo['fecha_fin']
    );
 }

/**
 * Gets all tracks given the student id and semester
 * 
 * Important Aspects: familiar_desc is renamed to familiar, vida_uni is renamed to vida_universitaria yand vida_uni_riesgo
 * is renamed to vida_universitaria_riesgo to preserve the nomination pattern.
 *
 * @see getSeguimientosEstudiante($idEstudiante, $periodo)
 * @param $idEstudiante --> ASES student id
 * @param $periodo --> semester array 
 * @return array with every track (seguimiento).
 */
 function getSeguimientosEstudiante($idEstudiante, $periodo){
    global $DB;
    $sql_query = 
        "SELECT 
            TPS.id AS id_seguimiento,
            TPS.hora_ini AS hora_ini,
            TPS.hora_fin AS hora_fin,
            TPS.familiar_desc AS familiar,
            TPS.familiar_riesgo AS familiar_riesgo,
            TPS.academico AS academico, 
            TPS.academico_riesgo AS academico_riesgo,
            TPS.economico AS economico,
            TPS.economico_riesgo AS economico_riesgo,
            TPS.vida_uni AS vida_universitaria,
            TPS.vida_uni_riesgo AS vida_universitaria_riesgo,
            TPS.individual AS individual,
            TPS.individual_riesgo AS individual_riesgo,
            TPS.tipo AS tipo,
            TPS.fecha AS fecha_seguimiento
        FROM {talentospilos_seg_estudiante} AS TPSE 
        INNER JOIN {talentospilos_seguimiento} AS TPS 
            ON TPSE.id_seguimiento = TPS.id 
        WHERE 
            (fecha BETWEEN " . strtotime($periodo['fecha_inicio']) . " AND " . strtotime($periodo['fecha_fin']) . ")
            AND (id_estudiante = $idEstudiante)
            AND (tipo = 'PARES')
        ORDER BY TPS.fecha ASC
        ";
    
    // stdClass objects array
    $seguimientos = $DB->get_records_sql($sql_query);

    $arraySeguimientos = array();
    foreach ($seguimientos as $key => $seguimiento) { 
        array_push($arraySeguimientos, get_object_vars($seguimiento));
    }
    return $arraySeguimientos;
 }

/**
 * Gets risk information on each track (seguimiento) on every next dimensions:
 *
 *  - Familiar
 *  - Academico
 *  - Económico
 *  - Vida Universitaria
 *  - Individual
 *
 * @param $seguimiento --> tracks array (seguimientos)
 * @return array with syntaxis: 
 *              ( 
 *                  'id_seguimiento' => '$id_seguimiento'
 *                   'datos' =>  
 *                          array(
 *                                  'dimension' => 'familiar', 
 *                                  'riesgo' => '$riesgo'
 *                              ),
 *                              (
 *                                  'dimension' => 'academico', 
 *                                  'riesgo' => '$riesgo'
 *                              ),
 *                              (
 *                                  'dimension' => 'economico', 
 *                                  'riesgo' => '$riesgo'
 *                              ),
 *                              (
 *                                  'dimension' => 'vida_universitaria', 
 *                                  'riesgo' => '$riesgo'
 *                              ),
 *                              (
 *                                  'dimension' => 'individual', 
 *                                  'riesgo' => '$riesgo'
 *                              ),
 *                     'fecha' => '$fecha'
 *              )
 */

 /**
  * Gets full data information of each dimension and push it into a track array (seguimiento)
  *
  * @see obtenerDatosDimensionSeguimiento($seguimiento, $dimension = 'todas')
  * @param $seguimiento --> array of tracks (seguimientos)
  * @param $dimension = 'todas' --> Needs information of all dimensions
  * @return array with date, dimension and track information
  */
 function obtenerDatosDimensionSeguimiento($seguimiento, $dimension = 'todas'){

    $fechaFormateada = new DateTime();
    $fechaFormateada->setTimestamp($seguimiento['fecha_seguimiento']);

    // Datos formateados de una o multiples dimensiones, según como se indique
    // en el parámetro de dimensión.
    $datos = array(); 
    if(($dimension == 'familiar')||($dimension == 'todas')){
        array_push(
            $datos, 
            array(
                'dimension' => 'familiar',
                'riesgo' => $seguimiento['familiar_riesgo']
            )
        );
    }

    if(($dimension == 'academico')||($dimension == 'todas')){
        array_push(
            $datos, 
            array(
                'dimension' => 'academico',
                'riesgo' => $seguimiento['academico_riesgo']
            )
        );
    }

    if(($dimension == 'economico')||($dimension == 'todas')){
        array_push(
            $datos, 
            array(
                'dimension' => 'economico',
                'riesgo' => $seguimiento['economico_riesgo']
            )
        );
    }

    if(($dimension == 'vida_universitaria')||($dimension == 'todas')){
        array_push(
            $datos, 
            array(
                'dimension' => 'vida_universitaria',
                'riesgo' => $seguimiento['vida_universitaria_riesgo']
            )
        );
    }

    if(($dimension == 'individual')||($dimension == 'todas')){
        array_push(
            $datos, 
            array(
                'dimension' => 'individual',
                'riesgo' => $seguimiento['individual_riesgo']
            )
        );
    }

    return array(
        'id_seguimiento' => $seguimiento['id_seguimiento'],
        'datos' => $datos,
        'fecha' =>  $fechaFormateada->format('Y-M-d')

    );
 }

 /**
 * Gets all track records of a student on every dimension
 *
 * @see obtenerDatosSeguimientoFormateados($idEstudiante, $dimension = 'todas', $periodo)
 * @param $idEstudiante --> student id
 * @param $dimension = 'todas' --> Gets a set of tracks on each dimension
 * @param $periodo --> array containing date range to get the information array(fecha_nicio=>'yyyy-mm-dd hh:mm:ss',fecha_fin=>'yyyy-mm-dd hh:mm:ss')
 * @return array --> Data processed to graph 
 */
 function obtenerDatosSeguimientoFormateados($idEstudiante, $dimension = 'todas', $periodo){
    $seguimientos = getSeguimientosEstudiante($idEstudiante, $periodo);
    $datosSeguimientoFormateados = array();
    foreach($seguimientos as $key => $seguimiento){
        $seguimientoFormateado = obtenerDatosDimensionSeguimiento($seguimiento, $dimension);
        $riesgoSeguimiento = $seguimientoFormateado['datos'][0]['riesgo'];
        if($riesgoSeguimiento > '0'){
            $color = null;
            if($riesgoSeguimiento == '1'){
                $color = 'green';
            }elseif ($riesgoSeguimiento == '2') {
                $color = 'orange';
            }elseif ($riesgoSeguimiento == '3') {
                $color = 'red';
            }
            $seguimientoFormateado['datos'][0] = array_merge($seguimientoFormateado['datos'][0], array('color' => $color));
            array_push($datosSeguimientoFormateados, $seguimientoFormateado);
        }
    }
    return $datosSeguimientoFormateados;
 }


?>