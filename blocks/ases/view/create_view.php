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
 * @author     Edgar Mauricio Ceron Florez
 * @package    block_ases
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/permissions_management/permissions_functions.php');
require_once ('../managers/permissions_management/permissions_lib.php');
require_once ('../managers/validate_profile_action.php');
require_once ('../managers/menu_options.php'); 
include('../lib.php');
global $PAGE;

include("../classes/output/create_action_page.php");
require_once('../managers/user_management/user_lib.php');
include("../classes/output/renderer.php");


// Set up the page.
$title = "Crear accion";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

require_login($courseid, false);


//Gets all roles
$roles = get_roles();
$roles_table_user= get_roles_select($roles,"profiles_user");


//Gets all functionalities
$function = get_functions();
$functions_table = get_functions_select($function,"functions");
$functions = get_functions_select($function,"functions_table");

$general_table  = get_functions_actions();

//Menu items are created
$menu_option = create_menu_options($USER->id, $blockid, $courseid);

//Crea una clase con la información que se llevará al template.   
$data = new stdClass;

//Evaluates if user role has permissions assigned on this view
$actions = authenticate_user_view($USER->id, $blockid);
$data = $actions;
$data->menu = $menu_option;
$data->roles_table_user=$roles_table_user;
$data->functions_table =$functions_table;
$data->general_table=$general_table;
$data->functions =$functions;


$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);
$url = new moodle_url("/blocks/ases/view/create_action.php",array('courseid' => $courseid, 'instanceid' => $blockid));

// Navegation set up
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Crear accion',$url, null, 'block', $blockid);
$coursenode->add_node($blocknode);
$blocknode->make_active();


$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/sugerenciaspilos.css', true);
$PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables_themeroller.css', true);
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);



$PAGE->requires->js('/blocks/ases/js/npm.js', true);
$PAGE->requires->js_call_amd('block_ases/permissionsmanagement_main','init');

//$PAGE->requires->js('/blocks/ases/js/create_action.js', true);

$PAGE->set_url($url);
$PAGE->set_title($title);

$PAGE->set_heading($title);

$output = $PAGE->get_renderer('block_ases');

echo $output->header();

$permisos_rol_page = new \block_ases\output\create_action_page($data);
echo $output->render($permisos_rol_page);
echo $output->footer();