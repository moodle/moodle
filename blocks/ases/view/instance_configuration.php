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
 * Ases Block
 *
 * @author     Iader E. García Gómez
 * @package    block_ases
 * @copyright  2016 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once $CFG->libdir.'/adminlib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/permissions_management/permissions_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/validate_profile_action.php';
require_once $CFG->dirroot.'/blocks/ases/managers/menu_options.php';
require_once $CFG->dirroot.'/blocks/ases/managers/periods_management/periods_lib.php'; 
require_once $CFG->dirroot.'/blocks/ases/managers/instance_management/instance_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/role_management/role_management_lib.php';
global $PAGE;
global $COURSE;

include("../classes/output/instance_configuration_page.php");
include("../classes/output/renderer.php");

// Set up the page.
$title = get_string('pluginname', 'block_ases');
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

require_login($courseid, false);

$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/instance_configuration.php",array('courseid' => $courseid, 'instanceid' => $blockid));

//Navegation set up
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create($title,$url, null, 'block', $blockid);
$coursenode->add_node($blocknode);
$blocknode->make_active();

// Creates a class with information that'll be send to template
$object_to_render = new stdClass();

if(!consult_instance($blockid)){
    $category_context = context_coursecat::instance($COURSE->category);
    if(has_capability('moodle/category:manage', $category_context)) {
        
        // Systems role assignment for the current instance
        $result_assign_role = update_role_user($USER->username, 'sistemas', $blockid, 1, get_current_semester(), null, null);
        insert_instance($blockid, $USER->id);
        
        if($result_assign_role == 4 || $result_assign_role == 2){
            $object_to_render->status = 0;
            $object_to_render->status_message .= ' Error al asignar el rol sistemas al administrador.';
        }

    } else {
        $object_to_render->status = 0;
        $object_to_render->status_message = 'El usuario actual no tiene permisos para configurar nuevas instancias.';
    }
}

if(!isset($object_to_render->status)){
    //Menu items are created
    $menu_option = create_menu_options($USER->id, $blockid, $courseid);

    $actions = authenticate_user_view($USER->id, $blockid);

    $object_to_render = $actions;
    $object_to_render->menu = $menu_option;

    $info_instance = get_info_instance($blockid);

    $object_to_render->idnumber = $info_instance->id_number;
    $object_to_render->description = $info_instance->descripcion;

    $cohorts = get_cohorts_without_assignment($blockid);
    $cohorts_options = "";

    if($cohorts){
        foreach($cohorts as $cohort){
            $cohorts_options .= "<option value='$cohort->id'>$cohort->idnumber - $cohort->name</option>";
        }
    }else{
        $cohorts_options .= "<option value=''>No hay cohortes disponibles para asignar</option>";
    }

    $object_to_render->select_cohorts = $cohorts_options;
}

$PAGE->set_url($url);
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
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);

$PAGE->requires->js_call_amd('block_ases/instanceconfiguration_main','init');

$output = $PAGE->get_renderer('block_ases');

echo $output->header();
$instance_configuration_page = new \block_ases\output\instance_configuration_page($object_to_render);
echo $output->render($instance_configuration_page);
echo $output->footer();