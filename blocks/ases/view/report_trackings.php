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
 * @author     Esteban Aguirre Martinez
 * @package    block_ases
 * @copyright  2017 Esteban Aguirre Martinez <estebanaguirre1997@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/pilos_tracking/tracking_functions.php');
require_once('../managers/instance_management/instance_lib.php');
require_once ('../managers/permissions_management/permissions_lib.php');
require_once ('../managers/role_management/role_management_lib.php');
require_once ('../managers/seguimiento_grupal/seguimientogrupal_lib.php');
require_once ('../managers/validate_profile_action.php');
require_once ('../managers/menu_options.php');
require_once ('../managers/lib/student_lib.php');
require_once '../managers/dphpforms/dphpforms_forms_core.php';
require_once '../managers/dphpforms/dphpforms_records_finder.php';
require_once '../managers/dphpforms/dphpforms_get_record.php';
require_once ('../managers/student_profile/studentprofile_lib.php');


include('../lib.php');
include("../classes/output/renderer.php");
include("../classes/output/report_trackings_page.php");

global $PAGE, $USER;

$title = "estudiantes";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

require_login($courseid, false);


// Instance is consulted for its registration
if(!consult_instance($blockid)){
    header("Location: instance_configuration.php?courseid=$courseid&instanceid=$blockid");
}


$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);


$url = new moodle_url("/blocks/ases/view/report_tackings.php",array('courseid' => $courseid, 'instanceid' => $blockid));


//Navigation setup
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create($title,$url, null, 'block', $blockid);
$coursenode->add_node($blocknode);
$blocknode->make_active();

//Menu items are created
$menu_option = create_menu_options($USER->id, $blockid, $courseid);

// Creates a class with information that'll be send to template
$data = 'data';
$data = new stdClass;

// Evaluates if user role has permissions assigned on this view
$actions = authenticate_user_view($USER->id, $blockid);
$data = $actions;
$data->menu = $menu_option;



//Getting role, username and email from current connected user

$userrole = get_id_rol($USER->id,$blockid);
if($userrole){
$usernamerole= get_name_rol($userrole);}
$username = $USER->username;
$email = $USER->email;

$seguimientotable ="";
$globalArregloPares = [];
$globalArregloGrupal =[];
$table="";
$table_periods="";

$periods = get_semesters();

// Getting last semester date range 
$intervalo_fechas[0] = reset($periods)->fecha_inicio;
$intervalo_fechas[1] =reset($periods)->fecha_fin;
$intervalo_fechas[2] =reset($periods)->id;

$choosen_date =strtotime($intervalo_fechas[0]);
$new_forms_date =strtotime('2018-01-01 00:00:00');

// Sort periods Select
$table_periods.=get_period_select($periods);

if($usernamerole=='monitor_ps'){

    $monitor_id =$USER->id;

    //Performs the total count of trackings per monitor, filtering by professional and practicant 

    $tracking_current_semestrer=get_tracking_current_semester('monitor',$monitor_id, $intervalo_fechas[2]);
    $counting_trackings=filter_trackings_by_review($tracking_current_semestrer);
    $counting=create_counting_advice('MONITOR',$counting_trackings);


    // Get peer trackings that a monitor has done and show it in a toggle.
    $students_by_monitor=get_students_of_monitor($monitor_id,$blockid);
    $table.=render_monitor_new_form($students_by_monitor);

    // Get grupal trackings that a monitor has done and show it in a toggle.
    $array_groupal_trackings_dphpforms =get_tracking_grupal_monitor_current_semester($monitor_id,$intervalo_fechas[2]);
    $table.=render_groupal_tracks_monitor_new_form($array_groupal_trackings_dphpforms,$monitor_id);


}elseif($usernamerole=='practicante_ps'){
   
    //Get trackings of students associated with a set of monitors assigned to a practicant.

    //Render new form of the role practicant
    $practicant_id =$USER->id;
    $monitors_of_pract = get_monitors_of_pract($practicant_id,$blockid);
    $table.=render_practicant_new_form($monitors_of_pract,$blockid);
  


}elseif($usernamerole=='profesional_ps'){

    //Get trackings of students associated with a set of monitors in turn assigned to a practitioner of a professional.

    //Render new form of the role professional
    $professional_id=$USER->id;
    $practicant_of_prof=get_pract_of_prof($professional_id,$blockid);
    $table.=render_professional_new_form($practicant_of_prof,$blockid);
   


}elseif($usernamerole=='sistemas'){

    //Gets all existent periods and roles containing "_ps"
    $roles = get_rol_ps();

    //Obtains the people who are in the last added semester in which their roles ended up with "_ps"
    $people = get_people_onsemester(reset($periods)->id,$roles,$blockid);


    //Sorts People 'select'
    $table_periods.=get_people_select($people);

}
$table_permissions=show_according_permissions($table,$actions);


$data->rol = $usernamerole;

$data->table_periods =$table_periods;
$data->table=$table_permissions;
$data->counting=$counting;

$PAGE->requires->css('/blocks/ases/style/jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert2.css', true);
$PAGE->requires->css('/blocks/ases/style/sugerenciaspilos.css', true);
$PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/c3.css', true);
$PAGE->requires->css('/blocks/ases/style/student_profile_risk_graph.css', true);
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
//Pendiente para cambiar el idioma del nombre del archivo junto con la estructura de
//su nombramiento.
$PAGE->requires->css('/blocks/ases/style/creadorFormulario.css', true);

$PAGE->requires->js_call_amd('block_ases/pilos_tracking_main','init');
$PAGE->requires->js_call_amd('block_ases/groupal_tracking','init');
$PAGE->requires->js_call_amd('block_ases/dphpforms_form_renderer', 'init');


$PAGE->set_url($url);
$PAGE->set_title($title);

$output = $PAGE->get_renderer('block_ases');

echo $output->header();
$report_trackings_page = new \block_ases\output\report_trackings_page($data);
echo $output->render($report_trackings_page);
echo $output->footer();