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
 * @author     Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  2017 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once 'grader_lib.php';
require_once 'wizard_lib.php';

/***************************/
/***  GRADER PROCESSING  ***/
/***************************/
if (isset($_POST['user']) && isset($_POST['item']) && isset($_POST['finalgrade']) && isset($_POST['course'])) {

    $resp = update_grades_moodle($_POST['user'], $_POST['item'], $_POST['finalgrade'], $_POST['course']);
    echo json_encode($resp);

}

if(isset($_POST['type']) && isset($_POST['course']) && $_POST['type'] === 'update_grade_items'){

    $retorno = update_grade_items_by_course($_POST['course']);
    echo $retorno;

}

/***************************/
/***  WIZARD PROCESSING  ***/
/***************************/

if (isset($_POST['course']) && isset($_POST['parent']) && isset($_POST['fullname']) && isset($_POST['agregation']) && ($_POST['tipo'] == "CATEGORÍA") && isset($_POST['peso'])) {

    $retorno = insertCategory($_POST['course'], $_POST['parent'], $_POST['fullname'], $_POST['agregation'], $_POST['peso']);

    echo $retorno;

}

if (isset($_POST['course']) && isset($_POST['parent']) && isset($_POST['fullname']) && isset($_POST['agregation']) && ($_POST['tipo'] == "PARCIAL") && isset($_POST['peso'])) {

    $retorno = insertParcial($_POST['course'], $_POST['parent'], $_POST['fullname'], $_POST['agregation'], $_POST['peso']);

    echo $retorno;

}

if (isset($_POST['course']) && isset($_POST['parent']) && isset($_POST['fullname']) && ($_POST['tipo'] == "ÍTEM") && isset($_POST['peso'])) {

    $retorno = insertItem($_POST['course'], $_POST['parent'], $_POST['fullname'], $_POST['peso'], true);

    echo $retorno;

}

if (isset($_POST['course']) && isset($_POST['type']) && $_POST['type'] == "loadCat") {

    $cursos = getCategoriesandItems($_POST['course']);
    echo $cursos;
}

if (isset($_POST['type_ajax']) && isset($_POST['id']) && isset($_POST['type']) && isset($_POST['courseid']) && $_POST['type_ajax'] === "deleteElement") {
    $id = $_POST['id'];
    $courseid = $_POST['courseid'];
    $type = $_POST['type'];

    $response = delete_element($id, $courseid, $type);

    $resp = new stdClass;
    if ($response === true) {
        $resp->msg = "Elemento Borrado Correctamente";
    } else {
        $resp->error = "Error al borrar elemento";
    }

    echo json_encode($resp);

}

if (isset($_POST['course']) && isset($_POST['type']) && isset($_POST['type_e']) && isset($_POST['element']) && $_POST['type'] == "loadParentCat") {
    $categories = getParentCategories($_POST['course'], $_POST['element'], $_POST['type_e']);
    echo json_encode($categories);
}

if (isset($_POST['course']) && isset($_POST['element']) && isset($_POST['type_e']) && isset($_POST['newNombre']) && isset($_POST['newPeso']) && isset($_POST['newCalific']) && isset($_POST['type']) && $_POST['type'] == "editElement") {

    $response = editElement($_POST);

    $resp = new stdClass;
    if ($response == true) {
        $resp->msg = "Elemento Editado Correctamente";
    } else {
        $resp->error = "Error al editar elemento";
    }

    echo json_encode($resp);
}
