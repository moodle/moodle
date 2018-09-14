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
 * Ases block
 *
 * @author     Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  2018 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once dirname(__FILE__) . '/../../../../config.php';
require_once '../MyException.php';
require_once 'massmanagement_lib.php';

if (isset($_FILES['file']) || isset($_POST['idinstancia'])) {
    try {

        $archivo = $_FILES['file'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        date_default_timezone_set("America/Bogota");
        $nombre = $archivo['name'];

        $rootFolder = "../../view/archivos_subidos/mrm/status/files/";
        $zipFolder = "../../view/archivos_subidos/mrm/status/comprimidos/";
        
        if (!file_exists($rootFolder)) {
            //echo $rootFolder;
            mkdir($rootFolder, 0777, true);
        }
        if (!file_exists($zipFolder)) {
            mkdir($zipFolder, 0777, true);
        }

        //deletes everything from folders
        deleteFilesFromFolder($rootFolder);
        deleteFilesFromFolder($zipFolder);

        if ($extension !== 'csv') {
            throw new MyException("El archivo " . $archivo['name'] . " no corresponde al un archivo de tipo CSV. Por favor verifícalo");
        }

        if (!move_uploaded_file($archivo['tmp_name'], $rootFolder . 'Original_' . $nombre)) {
            throw new MyException("Error al cargar el archivo.");
        }

        ini_set('auto_detect_line_endings', true);
        if (!($handle = fopen($rootFolder . 'Original_' . $nombre, 'r'))) {
            throw new MyException("Error al cargar el archivo " . $archivo['name'] . ". Es posible que el archivo se encuentre dañado");
        }

        global $DB;

        $record = new stdClass();
        $count = 0;
        $wrong_rows = array();
        $success_rows = array();

        $detail_erros = array();
        array_push($detail_erros, ['No. linea - archivo original', 'No. linea - archivo registros erroneos', 'No. columna', 'Nombre Columna', 'detalle error']);
        $line_count = 2;
        $lc_wrongFile = 2;

        $titlesPos = fgetcsv($handle, 0, ",");
        array_push($wrong_rows, $titlesPos);
        array_push($success_rows, $titlesPos);

        $associativeTitles = getAssociativeTitles($titlesPos);

        while ($data = fgetcsv($handle, 0, ",")) {
            $isValidRow = true;
            $seguimientoid = 0;

            //*** begin validations on required fields

            $username = 0;
            $id_moodle = 0;
            $id_user_extended = 0;
            //validation id
            if ($associativeTitles['username'] !== null) {
                $sql_query = "SELECT id, username from {user} where username like '" . $data[$associativeTitles['username']] . "%';";
                $result = $DB->get_record_sql($sql_query);

                if (!$result) {
                    $isValidRow = false;
                    array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['username'] + 1), 'username', 'No existe un usuario moodle asociado al username' . $data[$associativeTitles['username']]]);
                } else {
                    $id_moodle = $result->id;

                    //validate user_extended
                    $sql_query = "SELECT * FROM {talentospilos_user_extended} WHERE id_moodle_user = $id_moodle";

                    $result = $DB->get_record_sql($sql_query);

                    if (!$result) {
                        $isValidRow = false;
                        array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['username'] + 1), 'username', 'No existe un usuario talentos asociado al usuario moodle con username' . $data[$associativeTitles['username']]]);
                    } else {
                        $id_talentos = $result->id_ases_user;
                        $id_user_extended = $result->id;
                    }
                }
            } else {
                throw new MyException('el campo "username"  es obligatorio');
            }

            //validate Estado Ases
            if ($associativeTitles['estado_ases'] !== null) {
                $estado_ases = $data[$associativeTitles['estado_ases']];
                $sql_query = "SELECT id FROM {talentospilos_estados_ases} WHERE nombre = '$estado_ases' ";
                $result = $DB->get_record_sql($sql_query);
                if (!$result) {
                    $isValidRow = false;
                    array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['estado_ases'] + 1), 'estado Ases', 'El estado ases solo puede ser *SEGUIMIENTO* o *SIN SEGUMIENTO*']);
                } else {
                    $id_estado_ases = $result->id;
                }

            } else {
                throw new MyException('el campo "estado_ases"  es obligatorio');
            }

            //validate Estado Icetex
            if ($associativeTitles['estado_icetex'] !== null) {
                $estado_icetex = $data[$associativeTitles['estado_icetex']];

                $sql_query = "SELECT id FROM {talentospilos_estados_icetex} WHERE nombre = '$estado_icetex' ";
                $result = $DB->get_record_sql($sql_query);
                if (!$result) {
                    $isValidRow = false;
                    array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['estado_icetex'] + 1), 'estado_icetex', 'No existe un estado icetex con nombre ' . $data[$associativeTitles['estado_icetex']]]);
                } else {
                    $id_estado_icetex = $result->id;
                }

            } else {
                throw new MyException('el campo "estado_icetex"  es obligatorio');
            }

            //validate Estado Programa
            if ($associativeTitles['estado_programa'] !== null) {
                $estado_programa = $data[$associativeTitles['estado_programa']];
                $sql_query = "SELECT id FROM {talentospilos_estad_programa} WHERE nombre = '$estado_programa' ";
                $result = $DB->get_record_sql($sql_query);
                if (!$result) {
                    $isValidRow = false;
                    array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['estado_programa'] + 1), 'estado_programa', 'No existe un estado de programa con nombre ' . $data[$associativeTitles['estado_programa']]]);
                }else{
                    $id_estado_programa = $result->id;
                }

            } else {
                throw new MyException('el campo "estado_programa"  es obligatorio');
            }

            //validate tracking_status
            if ($associativeTitles['tracking_status'] !== null) {
                $tracking_status = $data[$associativeTitles['tracking_status']];

                if ($tracking_status != 1 && $tracking_status != 0) {
                    $isValidRow = false;
                    array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['tracking_status'] + 1), 'tracking_status', 'Tracking_status solo puede ser 1 o 0']);
                }

            } else {
                throw new MyException('el campo "tracking_status"  es obligatorio');
            }

            //validate motivo_ases
            if ($associativeTitles['motivo_ases'] !== null) {
                $motivo_ases = $data[$associativeTitles['motivo_ases']];

                if ($motivo_ases != '') {
                    $sql_query = "SELECT id FROM {talentospilos_motivos} WHERE descripcion = '$motivo_ases' ";
                    $result = $DB->get_record_sql($sql_query);
                    if (!$result) {
                        $isValidRow = false;
                        array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['motivo_ases'] + 1), 'motivo_ases', 'No existe un motivo de retiro con descripcion ' . $data[$associativeTitles['motivo_ases']]]);
                    } else {
                        $id_motivo_ases = $result->id;
                    }

                } else {
                    $id_motivo_ases = false;
                }

            } else {
                throw new MyException('el campo "motivo_ases"  es obligatorio');
            }

            //validate motivo_icetex
            if ($associativeTitles['motivo_icetex'] !== null) {
                $motivo_icetex = $data[$associativeTitles['motivo_icetex']];

                if ($motivo_icetex != '') {
                    $sql_query = "SELECT id FROM {talentospilos_motivos} WHERE descripcion = '$motivo_icetex' ";
                    $result = $DB->get_record_sql($sql_query);
                    if (!$result) {
                        $isValidRow = false;
                        array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['motivo_icetex'] + 1), 'motivo_icetex', 'No existe un motivo de retiro con descripcion ' . $data[$associativeTitles['motivo_icetex']]]);
                    } else {
                        $id_motivo_icetex = $result->id;
                    }
                } else {
                    $id_motivo_icetex = false;
                }

            } else {
                throw new MyException('el campo "motivo_icetex"  es obligatorio');
            }

            //** End validations on required fields

            if (!$isValidRow) {
                $lc_wrongFile++;
                $line_count++;
                array_push($wrong_rows, $data);
                continue;
            } else {

                $result = update_status_student($id_user_extended, $id_talentos, $id_estado_ases, $id_estado_icetex, $id_estado_programa, $tracking_status, $id_motivo_ases, $id_motivo_icetex);

                if ($result) {
                    array_push($success_rows, $data);
                } else {
                    array_push($detail_erros, [$line_count, $lc_wrongFile, 'Error actualizando estado', 'Error asignando rol', 'Error del server asignando rol']);
                    array_push($wrong_rows, $data);
                    $lc_wrongFile++;
                }

                $line_count++;
            }

        }

        if (count($wrong_rows) > 1) {

            $filewrongname = $rootFolder . 'RegistrosErroneos_' . $nombre;

            $wrongfile = fopen($filewrongname, 'w');
            fprintf($wrongfile, chr(0xEF) . chr(0xBB) . chr(0xBF)); // feed utf-8 unicode format on
            foreach ($wrong_rows as $row) {
                fputcsv($wrongfile, $row);
            }
            fclose($wrongfile);

            //----
            $detailsFilename = $rootFolder . 'DetallesErrores_' . $nombre;

            $detailsFileHandler = fopen($detailsFilename, 'w');
            fprintf($detailsFileHandler, chr(0xEF) . chr(0xBB) . chr(0xBF)); // feed utf-8 unicode format on
            foreach ($detail_erros as $row) {
                fputcsv($detailsFileHandler, $row);
            }
            fclose($detailsFileHandler);

        }

        if (count($success_rows) > 1) { //First row are titles
            $arrayIdsFilename = $rootFolder . 'RegistrosExitosos_' . $nombre;

            $arrayIdsFileHandler = fopen($arrayIdsFilename, 'w');
            fprintf($arrayIdsFileHandler, chr(0xEF) . chr(0xBB) . chr(0xBF)); // feed utf-8 unicode format on
            foreach ($success_rows as $row) {
                fputcsv($arrayIdsFileHandler, $row);
            }
            fclose($arrayIdsFileHandler);

            $response = new stdClass();

            if (count($wrong_rows) > 1) {
                $response->warning = 'Archivo cargado con inconsistencias<br> Para mayor informacion descargar la carpeta con los detalles de inconsitencias.';
            } else {
                $response->success = 'Archivo cargado satisfactoriamente';
            }

            $zipname = $zipFolder . "detalle.zip";
            createZip($rootFolder, $zipname);

            $zipname = explode("..", $zipname)[2];

            $response->urlzip = "<a target='_blank' href='..$zipname'>Descargar detalles</a>";

            echo json_encode($response);

        } else {
            $response = new stdClass();
            $response->error = "No se cargo el archivo. Para mayor informacion descargar la carpeta con los detalles de inconsitencias.";

            $zipname = $zipFolder . "detalle.zip";
            createZip($rootFolder, $zipname);

            $zipname = explode("..", $zipname)[2];

            $response->urlzip = "<a target='_blank': href='..$zipname'>Descargar detalles</a>";

            echo json_encode($response);
        }

    } catch (MyException $e) {
        $msj = new stdClass();
        $msj->error = $e->getMessage() . pg_last_error();
        echo json_encode($msj);
    } catch (Exception $e) {
        $msj = new stdClass();
        $msj->error = $e->getMessage() . pg_last_error();
        echo json_encode($msj);
    }
} else {
    echo json_encode('no entro');
}

/**
 * Creates an associative array given a header from a CSV file
 *
 * @see getAssociativeTitles ($titlesPos)
 * @param $titlesPos --> header from CSV
 * @return array
 */
function getAssociativeTitles($array)
{

    $associativeArray = array();

    foreach ($array as $key => $value) {
        $associativeArray[$value] = $key;
    }

    return $associativeArray;
}

/**
 * Update all student's status
 *
 * @see update_status_student($id_moodle, $id_talentos, $id_estado_ases, $id_estado_icetex, $id_estado_programa, $tracking_status)
 * @param $id_moodle --> id of {user} table
 * @param $id_talentos --> id of {talentospilos_usuario} table
 * @param $id_estado_ases --> id of {talentospilos_estados_ases} table
 * @param $id_estado_icetex --> id of {talentospilos_estados_icetex} table
 * @param $id_estado_programa --> program_status of {talentospilos_user_extended}
 * @param $tracking_status --> tracking_status of {talentospilos_user_extended}
 * @return boolean
 */
function update_status_student($id_user_extended, $id_talentos, $id_estado_ases, $id_estado_icetex, $id_estado_programa, $tracking_status, $id_motivo_ases, $id_motivo_icetex)
{
    global $DB;

    //update tp_user_extended
    $object_user_extended_update = new stdClass;
    $object_user_extended_update->id = $id_user_extended;
    $object_user_extended_update->program_status = $id_estado_programa;
    $object_user_extended_update->tracking_status = $tracking_status;

    $update = $DB->update_record('talentospilos_user_extended', $object_user_extended_update);

    if (!$update) {
        return false;
    }

    $query_instance = "SELECT cohorte.id, inst_cohort.id_instancia, cohorte.idnumber
                       FROM {talentospilos_user_extended}  ext
                       INNER JOIN {cohort_members} memb ON ext.id_moodle_user = memb.userid
                       INNER JOIN {talentospilos_inst_cohorte} inst_cohort ON memb.cohortid = inst_cohort.id_cohorte
                       INNER JOIN {cohort} cohorte ON memb.cohortid = cohorte.id
                       WHERE ext.id = $id_user_extended
                       ORDER BY cohorte.id ASC
                       ";
    
    $instances = $DB->get_records_sql($query_instance);

    $id_instance = 0;

    foreach ($instances as $instance) {
        $instance_name = $instance->idnumber;
        if(substr($instance_name,0,2) == 'SP'){
            $id_instance = $instance->id_instancia;
            break;
        }else if(substr($instance_name,0,6) == 'PORRAS'){
            $id_instance = $instance->id_instancia;
            break;
        }else{
            $id_instance = $instance->id_instancia;
        }
    }
    //update estado ases

    $object_est_estado_ases = new stdClass;
    $object_est_estado_ases->id_estudiante = $id_talentos;
    $object_est_estado_ases->id_estado_ases = $id_estado_ases;    
    if ($id_motivo_ases != false) {
        $object_est_estado_ases->id_motivo_retiro = $id_motivo_ases;
    }
    $object_est_estado_ases->fecha = time();
    $object_est_estado_ases->id_instancia = $id_instance;

    $insert_ases = $DB->insert_record('talentospilos_est_estadoases', $object_est_estado_ases, false);
    
    if (!$insert_ases) {
        return false;
    }

    //update estado_icetex

    $object_est_estado_icetex = new stdClass;
    $object_est_estado_icetex->id_estudiante = $id_talentos;
    $object_est_estado_icetex->id_estado_icetex = $id_estado_icetex;    
    if ($id_motivo_icetex != false) {
        $object_est_estado_icetex->id_motivo_retiro = $id_motivo_icetex;
    }
    $object_est_estado_icetex->fecha = time();

    $insert_icetex = $DB->insert_record('talentospilos_est_est_icetex', $object_est_estado_icetex, false);
    
    if (!$insert_icetex) {
        return false;
    }

    return true;
}
