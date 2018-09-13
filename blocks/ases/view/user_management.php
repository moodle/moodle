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

require_once (__DIR__ . '/../../../config.php');
require_once ($CFG->libdir . '/adminlib.php');
require_once ('../managers/instance_management/instance_lib.php');
require_once ('../managers/user_management/user_lib.php');
require_once ('../managers/user_management/user_functions.php');
require_once ('../managers/permissions_management/permissions_lib.php');
require_once ('../managers/validate_profile_action.php');
require_once ('../managers/menu_options.php');

global $PAGE;
include ("../classes/output/user_management_page.php");
include ("../classes/output/renderer.php");

// Variables for setup the page.

$title = "Gestionar Roles";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
require_login($courseid, false);
$contextcourse = context_course::instance($courseid);
$contextblock = context_block::instance($blockid);
$url = new moodle_url("/blocks/ases/view/user_management.php", array(
    'courseid' => $courseid,
    'instanceid' => $blockid
));

// Menu items are created
$menu_option = create_menu_options($USER->id, $blockid, $courseid);

// Obatins the people associated to the course and the students
$courseusers = get_course_usersby_id($courseid);
$table_courseuseres = get_course_users_select($courseusers);

// Creates a class with information that'll be send to template
$data = 'data';
$data = new stdClass;

// Evaluates if user role has permissions assigned on this view
$actions = authenticate_user_view($USER->id, $blockid);
$data = $actions;

// Load academic programs for program director role
$academic_programs = get_academic_programs();
$academic_programs_options = "";

foreach($academic_programs as $academic_program){
    $academic_programs_options .= "<option value='$academic_program->id'>$academic_program->cod_univalle - 
                                   $academic_program->academic_program_name - $academic_program->location_name - $academic_program->jornada</option>";
}

$data->academic_program_select = $academic_programs_options;

$data->table = $table_courseuseres;
$data->menu = $menu_option;
$PAGE->requires->js_call_amd('block_ases/usermanagement_main', 'init');

// Nav configuration
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$node = $coursenode->add('Gestion de roles del bloque', $url);
$node->make_active();

// Setup page

$PAGE->set_context($contextcourse);
$PAGE->set_context($contextblock);
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
$PAGE->requires->css('/blocks/ases/js/select2/css/select2.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$output = $PAGE->get_renderer('block_ases');
$index_page = new \block_ases\output\user_management_page($data);


echo $output->header();

echo $output->render($index_page);
echo $output->footer();