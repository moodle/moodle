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
 * @author     Iader E. García Gómez
 * @package    block_ases
 * @copyright  2018 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__). '/../../../../config.php');
require_once('../lib/student_lib.php');
require_once('geographic_lib.php');

date_default_timezone_set('America/Bogota');

if(isset($_POST['func'])){
    if($_POST['func'] == 'load_geographic_info'){

        $id_ases = $_POST['id_ases'];
        load_geographic_info($id_ases);

    }else if($_POST['func'] == 'save_geographic_info'){

        $id_ases = $_POST['id_ases'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $neighborhood = $_POST['neighborhood'];
        $geographic_risk = $_POST['geographic_risk'];

        $msg = new stdClass();

        $result_save_info = save_geographic_info($id_ases, $latitude, $longitude, $neighborhood, $geographic_risk);
        
        if($result_save_info){
            $msg->title = 'Éxito';
            $msg->text = "La información geográfica ha sido guardada con éxito";
            $msg->type = "success";
        }else{
            $msg->title = 'Error';
            $msg->text = "La información geográfica no ha sido guardada. Intentalo nuevamente.";
            $msg->type = "error";
        }
        echo json_encode($msg);
    }
};
