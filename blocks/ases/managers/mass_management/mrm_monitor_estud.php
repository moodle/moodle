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
 * @author     John Lourido
 * @package    block_ases
 * @copyright  2017 John Lourido <jhonkrave@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once dirname(__FILE__) . '/../../../../config.php';
require_once '../MyException.php';
require_once 'massmanagement_lib.php';
require_once '../user_management/user_lib.php';
require_once '../periods_management/periods_lib.php';

if (isset($_FILES['file']) || isset($_POST['idinstancia'])) {
    try {

        $archivo = $_FILES['file'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);

        $nombre = $archivo['name'];

        $rootFolder = "../../view/archivos_subidos/mrm/monitor_estud/files/";
        $zipFolfer = "../../view/archivos_subidos/mrm/monitor_estud/comprimidos/";

        if (!file_exists($rootFolder)) {
            mkdir($rootFolder, 0777, true);
        }
        if (!file_exists($zipFolfer)) {
            mkdir($zipFolfer, 0777, true);
        }

        //deletes everything from folders
        deleteFilesFromFolder($rootFolder);
        deleteFilesFromFolder($zipFolfer);

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
        $wrong_rows = [];
        $success_rows = [];
        $detail_erros = [];

        array_push($detail_erros, ['No. linea - archivo original', 'No. linea - archivo registros erroneos', 'No. columna', 'Nombre Columna', 'detalle error']);
        $line_count = 2;
        $lc_wrongFile = 2;

        $titlesPos = fgetcsv($handle, 0, ",");
        array_push($wrong_rows, $titlesPos);
        array_push($success_rows, $titlesPos);

        $associativeTitles = getAssociativeTitles($titlesPos);

        while ($data = fgetcsv($handle, 0, ",")) {
            $isValidRow = true;

            //*** begin validations on required fields

            //validation id
            $id_estudiante = 0;
            if ($associativeTitles['username_estudiante'] !== null) {
                if ($data[$associativeTitles['username_estudiante']] == "" || $data[$associativeTitles['username_estudiante']] == "undefined") {
                    $isValidRow = false;
                    array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['username_estudiante'] + 1), 'username_estudiante', 'El campo No puede ser vacio']);
                } else {

                    // $result = get_userById(array('idtalentos'), $data[$associativeTitles['username_estudiante']]);
                    $result = get_ases_user_by_code($data[$associativeTitles['username_estudiante']]);                    

                    if (!$result) {
                        $isValidRow = false;
                        array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['username_estudiante'] + 1), 'username_estudiante', 'No existe un usuario Ases asociado al username ' . $data[$associativeTitles['username_estudiante']]]);
                    } else {
                        $id_estudiante = $result->id;
                    }

                }

            } else {
                throw new MyException('El campo "username_estudiante" es un campo obligatorio');
            }

            $id_monitor = 0;
            //validating user exists
            if ($associativeTitles['username_monitor'] !== null) {
                $sql_query = "SELECT id from {user} where username like '" . $data[$associativeTitles['username_monitor']] . "%';";
                $result = $DB->get_record_sql($sql_query);

                if (!$result) {
                    $isValidRow = false;
                    array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['username_monitor'] + 1), 'username_monitor', 'No existe un usuario asociado al username ' . $data[$associativeTitles['username_monitor']]]);
                } else {
                    $sql_query = "SELECT id_usuario from {talentospilos_user_rol} where id_usuario = '" . $result->id . "' AND id_rol = (SELECT id from {talentospilos_rol} where nombre_rol='monitor_ps');";

                    $consultMonitor = $DB->get_record_sql($sql_query);

                    if ($consultMonitor) {
                        $id_monitor = $result->id;
                    } else {
                        $isValidRow = false;
                        array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['username_monitor'] + 1), 'username_monitor', 'El usuario asociado al username ' . $data[$associativeTitles['username_monitor']] . ' no tiene asignado el rol monitor']);
                    }

                }
            } else {
                throw new MyException('El campo "username_monitor" es un campo obligatorio');
            }
            $last_semester = get_current_semester();

            //validating duplicity on instance
            $sql_query = "SELECT *  FROM {talentospilos_monitor_estud} where id_semestre='" . $last_semester->max . "' AND id_estudiante='" . $id_estudiante . "' AND id_instancia='" . $_POST['idinstancia'] . "'";
            $exists = $DB->get_record_sql($sql_query);

            if ($exists) {
                $isValidRow = false;
                array_push($detail_erros, [$line_count, $lc_wrongFile, ($associativeTitles['username_estudiante'] + 1), 'username_estudiante', 'El usuario asociado al username ' . $data[$associativeTitles['username_estudiante']] . ' Ya tiene asignado un monitor']);
            }

            //** End validations on required fields

            if (!$isValidRow) {
                $lc_wrongFile++;
                $line_count++;
                array_push($wrong_rows, $data);
                continue;
            } else {
                $record = new stdClass();
                $record->id_estudiante = $id_estudiante;
                $record->id_monitor = $id_monitor;
                $record->id_instancia = $_POST['idinstancia'];
                $record->id_semestre = $last_semester->max;

                $DB->insert_record('talentospilos_monitor_estud', $record);
                array_push($success_rows, $data);
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

            $zipname = $zipFolfer . "detalle.zip";
            createZip($rootFolder, $zipname);

            $zipname = explode("..", $zipname)[2];

            $response->urlzip = "<a href='..$zipname'>Descargar detalles</a>";

            echo json_encode($response);

        } else {
            $response = new stdClass();
            $response->error = "No se cargo el archivo. Para mayor informacion descargar la carpeta con los detalles de inconsitencias.";

            $zipname = $zipFolfer . "detalle.zip";
            createZip($rootFolder, $zipname);

            $zipname = explode("..", $zipname)[2];

            $response->urlzip = "<a href='..$zipname'>Descargar detalles</a>";

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
 * @return array --> associative array with 'username_monitor' and 'username_estudiante' fields on it
 */

function getAssociativeTitles($titlesPos)
{

    $associativeTitles = array();
    $count = 0;

    foreach ($titlesPos as $t) {

        switch ($t) {
            case 'username_monitor':
                $associativeTitles['username_monitor'] = $count;
                break;
            case 'username_estudiante':
                $associativeTitles['username_estudiante'] = $count;
                break;
            default:
                throw new MyException('Error al cargar el archivo. El titulo "' . $t . '" no corresponde a alguna columna valida');
        }

        $count++;
    }

    return $associativeTitles;
}
