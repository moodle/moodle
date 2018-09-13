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
 * @author     Iader E. García Gómez
 * @package    block_ases
 * @copyright  2016 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

global $PAGE;

include("../classes/output/groupal_tracking_page.php");
include("../classes/output/renderer.php");
require_once('../managers/instance_management/instance_lib.php');
require_once ('../managers/permissions_management/permissions_lib.php');
require_once ('../managers/validate_profile_action.php');
require_once ('../managers/menu_options.php');
require_once ('../managers/dphpforms/dphpforms_forms_core.php');
require_once ('../managers/dphpforms/dphpforms_records_finder.php');
require_once ('../managers/dphpforms/dphpforms_get_record.php');
require_once ('../managers/seguimiento_grupal/seguimientogrupal_lib.php');



// Set up the page.
$title = "Seguimiento Grupal";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

require_login($courseid, false);

//Instance is consulted for its registration
if(!consult_instance($blockid)){
    header("Location: instance_configuration.php?courseid=$courseid&instanceid=$blockid");
}

$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);
$url = new moodle_url("/blocks/ases/view/groupal_tracking.php", array('courseid' => $courseid, 'instanceid' => $blockid));

$rol = get_role_ases($USER->id);

//Menu items are created
$menu_option = create_menu_options($USER->id, $blockid, $courseid);

// Creates a class with information that'll be send to template
$data = 'data';
$data = new stdClass;


$current_semester = get_current_semester();
$result = get_tracking_grupal_monitor_current_semester($USER->id,$current_semester->max);
$render_trackings= render_monitor_groupal_trackings($result);


// Evaluates if user role has permissions assigned on this view
$actions = authenticate_user_view($USER->id, $blockid);

$data = $actions;
$data->menu = $menu_option;
$data->seguimiento_grupal =$render_trackings;
$data->form_seguimientos_grupales = dphpforms_render_recorder('seguimiento_grupal', $rol);
  if ($record->form_seguimientos_grupales == '') {
       $record->form_seguimientos_grupales = "<strong><h3>Oops!: No se ha encontrado un formulario con el alias: <code>seguimientos_grupales</code>.</h3></strong>";
    }

$data->id_dphpforms_creado_por = $USER->id;


//Navegation set up
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$node = $coursenode->add('Seguimiento Grupal',$url);
$node->make_active();


//Falta añadir la validacion periodos antiguos.<--
$choosen_date =strtotime('2018-01-01 00:00:00');
$new_forms_date =strtotime('2018-01-01 00:00:00');


//set up page

$PAGE->set_url($url);
$PAGE->set_title($title);

$PAGE->set_heading($title);

$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/sugerenciaspilos.css', true);
$PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/style/creadorFormulario.css', true);

if($choosen_date>=$new_forms_date){
$PAGE->requires->js_call_amd('block_ases/new_logic_form_gt', 'init');
}else{
$PAGE->requires->js_call_amd('block_ases/groupal_tracking','init');
}
$PAGE->requires->js_call_amd('block_ases/dphpforms_form_renderer', 'init');


$output = $PAGE->get_renderer('block_ases');

echo $output->header();
//echo $output->standard_head_html(); 
$groupal_tracking_page = new \block_ases\output\groupal_tracking_page($data);
echo $output->render($groupal_tracking_page);
echo $output->footer();
