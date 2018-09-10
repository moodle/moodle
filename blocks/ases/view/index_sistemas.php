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
 * @author     Juan Pablo Moreno Muñoz
 * @package    block_ases
 * @copyright  2017 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/instance_management/instance_lib.php');
require_once ('../managers/permissions_management/permissions_lib.php');
require_once ('../managers/validate_profile_action.php');
include("../classes/output/index_sistemas_page.php");
include("../classes/output/renderer.php");


global $PAGE, $USER;

$title = "sistemas";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

require_login($courseid, false);

//Instance is consulted for its registration
if(!consult_instance($blockid)){
    header("Location: /blocks/ases/view/instanceconfiguration.php?courseid=$courseid&instanceid=$blockid");
}

$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);
$url = new moodle_url("/blocks/ases/view/index_sistemas.php", array('courseid' => $courseid, 'instanceid' => $blockid));

// Creates a class with information that'll be send to template
$data = 'data';
$data = new stdClass;

// Evaluates if user role has permissions assigned on this view
$actions = authenticate_user_view($USER->id, $blockid);
$data = $actions;


$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$node = $coursenode->add('Índice de vistas',$url);
$node->make_active();

// Setup page
$PAGE->set_context($contextcourse);
$PAGE->set_context($contextblock);
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);

$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/styles_index.css', true);



$data->params = "?courseid=$courseid&instanceid=$blockid";
$output = $PAGE->get_renderer('block_ases');
$index_page = new \block_ases\output\index_sistemas_page($data);

echo $output->header();

echo $output->render($index_page);
echo $output->footer();




