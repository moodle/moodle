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
 * Dynamic PHP Forms
 *
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs

    require_once(dirname(__FILE__). '/../../../../config.php');
    require_once('dphpforms_record_updater.php');
    require_once('dphpforms_response_recorder.php');
    require_once(dirname(__FILE__).'/../lib/lib.php');

    function dphpforms_render_recorder($id_form, $rol){
        return dphpforms_generate_html_recorder($id_form, $rol, '-1', '-1');
    };

    function dphpforms_render_updater($id_completed_form, $rol, $record_id){
        return dphpforms_generate_html_updater($id_completed_form, $rol, $record_id);
    };

    if( isset($_GET['form_id']) && isset($_GET['record_id']) ){

        global $USER;
        $rol = get_role_ases($USER->id);
        echo dphpforms_render_updater($_GET['form_id'], $rol, $_GET['record_id']);
    }

    
    /*if( isset($_GET['form_id']) && isset($_GET['rol']) && !(isset($_GET['record_id'])) ){
        echo dphpforms_generate_html_recorder($_GET['form_id'], $_GET['rol'], '-1', '-1');
    }
    die();*/

?>