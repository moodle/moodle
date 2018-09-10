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

require_once dirname(__FILE__) . '/../../../../config.php';
require_once '../MyException.php';
require_once '../mass_management/massmanagement_lib.php';
require_once '../historic_management/historic_academic_lib.php';
require_once '../historic_management/historic_icetex_lib.php';

if (isset($_FILES['file'])) {

    try {
        global $DB;
        $record = new stdClass();

        $archivo = $_FILES['file'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        date_default_timezone_set("America/Bogota");
        $nombre = $archivo['name'];

        $rootFolder = "../../view/archivos_subidos/historic/resolucion/files/";
        $zipFolder = "../../view/archivos_subidos/historic/resolucion/comprimidos/";

        //validate and create folders
        if (!file_exists($rootFolder)) {
            mkdir($rootFolder, 0777, true);
        }
        if (!file_exists($zipFolder)) {
            mkdir($zipFolder, 0777, true);
        }

        //deletes everything from folders
        deleteFilesFromFolder($rootFolder);
        deleteFilesFromFolder($zipFolder);

        //validate extension
        if ($extension !== 'csv') {
            throw new MyException("El archivo " . $archivo['name'] . " no corresponde al un archivo de tipo CSV. Por favor verifícalo");
        }

        //validate and move file
        if (!move_uploaded_file($archivo['tmp_name'], $rootFolder . 'Original_' . $nombre)) {
            throw new MyException("Error al cargar el archivo.");
        }

        //validate and open file
        ini_set('auto_detect_line_endings', true);
        if (!($handle = fopen($rootFolder . 'Original_' . $nombre, 'r'))) {
            throw new MyException("Error al cargar el archivo " . $archivo['name'] . ". Es posible que el archivo se encuentre dañado");
        }

        //Control variables
        $wrong_rows = array();
        $success_rows = array();
        $detail_errors = array();

        //headers of error file
        array_push($detail_errors, ['No. linea - archivo original', 'No. linea - archivo registros erroneos', 'No. columna', 'Nombre Columna', 'detalle error']);

        $line_count = 2;
        $lc_wrongFile = 2;

        //headers of succes files
        $titlesPos = fgetcsv($handle, 0, ",");
        array_push($wrong_rows, $titlesPos);
        array_push($success_rows, $titlesPos);

        $associativeTitles = getAssociativeArray($titlesPos);

        while ($data = fgetcsv($handle, 0, ",")) {
            $isValidRow = true;
            /* VALIDATIONS OF FIELDS */

            //validate codigo_resolucion
            if (!is_null($associativeTitles['codigo_resolucion'])) {

                $codigo_resolucion = $data[$associativeTitles['codigo_resolucion']];
                if ($codigo_resolucion != '') {

                    $id_resolucion = get_resolution_id_by_number($codigo_resolucion);

                    if ($id_resolucion != false) {
                        $isValidRow = false;
                        array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['codigo_resolucion'] + 1), 'codigo_resolucion', 'Ya existe una resolución asociada al código: ' . $codigo_resolucion]);
                    }

                } else {
                    $isValidRow = false;
                    array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['codigo_resolucion'] + 1), 'codigo_resolucion', 'El campo codigo_resolucion es obligatorio y se encuentra vacio']);
                }

            } else {
                throw new MyException('La columna con el campo codigo_resolucion es obligatoria');
            }

            //validate nombre_semestre
           if ($associativeTitles['nombre_semestre'] != null) {
            $nombre_semestre = $data[$associativeTitles['nombre_semestre']];
            if ($nombre_semestre != '') {

                $id_semestre = get_semester_id_by_name($nombre_semestre);
                if (!$id_semestre) {
                    $isValidRow = false;
                    array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['nombre_semestre'] + 1), 'nombre_semestre', 'No existe ningun semestre registrado con el nombre: ' . $nombre_semestre]);
                }

                } else {
                    $isValidRow = false;
                    array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['nombre_semestre'] + 1), 'nombre_semestre', 'El campo nombre_semestre es obligatorio y se encuentra vacio']);
                }

            } else {
                throw new MyException('La columna con el campo nombre_semestre es obligatoria');
            }

            //validate fecha
            if ($associativeTitles['fecha'] != null) {
                $fecha = $data[$associativeTitles['fecha']];
                if ($fecha == '') {

                    $isValidRow = false;
                    array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['fecha'] + 1), 'fecha', 'El campo fecha es obligatorio y se encuentra vacio']);

                }
            } else {
                throw new MyException('La columna con el campo fecha es obligatoria');
            }

            //validate total_girado
            if ($associativeTitles['total_girado'] != null) {

                $total_girado = $data[$associativeTitles['total_girado']];
                if ($total_girado != '') {

                    if (!is_numeric($total_girado)) {
                        $isValidRow = false;
                        array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['total_girado'] + 1), 'total_girado', 'El campo total_girado debe ser de tipo numerico']);
                    }

                } else {
                    $isValidRow = false;
                    array_push($detail_errors, [$line_count, $lc_wrongFile, ($associativeTitles['total_girado'] + 1), 'total_girado', 'El campo total_girado es obligatorio y se encuentra vacio']);
                }

            } else {
                throw new MyException('La columna con el campo total_girado es obligatoria');
            }

            $has_credit_note = false;
            //validate nota_credito
            if (!is_null($associativeTitles['nota_credito'])) {
                $credit_note = $data[$associativeTitles['nota_credito']];
                if ($credit_note != "" and $credit_note != 'undefined') {
                    $has_credit_note = true;                    
                }
            }

            //FINALIZACION DE VALIDACIONES. CARGA O ACTUALIZACIÓN
            if (!$isValidRow) {
                $lc_wrongFile++;
                array_push($wrong_rows, $data);
                continue;
            } else {

                //Actualizar o crear un registro
                $result = create_resolution($codigo_resolucion, $id_semestre, $fecha, $total_girado);

                if (!$result) {
                    array_push($detail_errors, [$line_count, $lc_wrongFile, 'Error al registrar resolución', 'Error Servidor', 'Error del server registrando el historico']);
                    array_push($wrong_rows, $data);
                    $lc_wrongFile++;
                } else {
                    $id_resolution = $result;
                    array_push($success_rows, $data);
 
                    if ($has_credit_note) {
                     $insert_credit_note = update_resolution_credit_note($id_resolution, $credit_note);
                     
                         if (!$insert_credit_note) {
                             array_push($detail_erros, [$line_count, $lc_wrongFile, 'Error al nota crédito', 'Error Servidor', 'Error del server registrando la nota crédito']);
                             array_push($wrong_rows, $data);
                             $lc_wrongFile++;
                         }
                     }
                }
            }

            $line_count++;
        }

        //RECORRER LOS REGISTROS ERRONEOS Y CREAR ARCHIVO DE registros_erroneos
        if (count($wrong_rows) > 1) {
            $filewrongname = $rootFolder . 'RegistrosErroneos_' . $nombre;
            $wrongfile = fopen($filewrongname, 'w');
            fprintf($wrongfile, chr(0xEF) . chr(0xBB) . chr(0xBF)); // darle formato unicode utf-8
            foreach ($wrong_rows as $row) {
                fputcsv($wrongfile, $row);
            }
            fclose($wrongfile);
            //----
            $detailsFilename = $rootFolder . 'DetallesErrores_' . $nombre;
            $detailsFileHandler = fopen($detailsFilename, 'w');
            fprintf($detailsFileHandler, chr(0xEF) . chr(0xBB) . chr(0xBF)); // darle formato unicode utf-8
            foreach ($detail_errors as $row) {
                fputcsv($detailsFileHandler, $row);
            }
            fclose($detailsFileHandler);
        }

        //RECORRER LOS REGISTROS EXITOSOS Y CREAR ARCHIVO DE registros_exitosos

        if (count($success_rows) > 1) { //porque la primera fila corresponde a los titulos no datos
            $arrayIdsFilename = $rootFolder . 'RegistrosExitosos_' . $nombre;
            $arrayIdsFileHandler = fopen($arrayIdsFilename, 'w');
            fprintf($arrayIdsFileHandler, chr(0xEF) . chr(0xBB) . chr(0xBF)); // darle formato unicode utf-8
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
            $response->urlzip = "<a href='ases/$zipname'>Descargar detalles</a>";
            echo json_encode($response);
        } else {
            $response = new stdClass();
            $response->error = "No se cargó el archivo. Para mayor informacion descargar la carpeta con los detalles de inconsitencias.";
            $zipname = $zipFolder . "detalle.zip";
            createZip($rootFolder, $zipname);
            $response->urlzip = "<a href='ases/$zipname'>Descargar detalles</a>";
            echo json_encode($response);
        }

        //CREAR ZIP

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
    $msj = new stdClass();
    $msj->error = "No se recibio ningun archivo";
    echo json_encode($msj);
}
