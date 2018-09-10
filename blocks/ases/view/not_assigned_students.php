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
 * Not assigned students
 *
 * @author     Isabella Serna Ramirez
 * @package    block_ases
 * @copyright  2018 Isabella Serna Ramirez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once('../managers/ases_report/asesreport_lib.php');
require_once('../managers/instance_management/instance_lib.php');
require_once('../managers/student_profile/studentprofile_lib.php');
require_once('../managers/permissions_management/permissions_lib.php');
require_once('../managers/validate_profile_action.php');
require_once('../managers/menu_options.php');

include('../lib.php');

global $PAGE;
include("../classes/output/not_assigned_students_page.php");
include("../classes/output/renderer.php");


// Set up the page.
$title           = "Reporte no asignados";
$pagetitle       = $title;
$courseid        = required_param('courseid', PARAM_INT);
$blockid         = required_param('instanceid', PARAM_INT);
$id_current_user = $USER->id;

// Instance is consulted for its registration
if (!consult_instance($blockid)) {
    header("Location: instance_configuration.php?courseid=$courseid&instanceid=$blockid");
    die();
}
require_login($courseid, false);

$cohorts = load_cohorts_by_instance($blockid);

// Menu items are created
$menu_option = create_menu_options($USER->id, $blockid, $courseid);


$cohorts_table = '';


// Carga de cohortes
$cohorts_table .= '<option value="TODOS">TODOS</option>';

foreach ($cohorts as $cohort) {
    $cohorts_table .= '<option value="' . $cohort->idnumber . '">' . $cohort->name . '</option>';
}


// Creates a class with information that'll be send to template
$data = new stdClass;

// Evaluates if user role has permissions assigned on this view
$actions = authenticate_user_view($USER->id, $blockid);
$data    = $actions;

foreach ($actions as $act) {
    $data->$act = $act;
}

$data->menu           = $menu_option;
$data->cohorts_checks = $cohorts_table;


if(!$data->message){
	$PAGE->requires->js_call_amd('block_ases/ases_report_main', 'init');
	$PAGE->requires->js_call_amd('block_ases/not_assigned_students_main', 'init');
}

// Nav configuration
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$node       = $coursenode->add('Gestion de roles del bloque', $url);
$node->make_active();


// Setup page
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);
$PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/add_fields.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables_themeroller.css', true);
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);

$output     = $PAGE->get_renderer('block_ases');
$index_page = new \block_ases\output\not_assigned_students_page($data);


echo $output->header();
echo $output->render($index_page);
echo $output->footer();
