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
 * @author     Juan Pablo Moreno Muñoz
 * @package    block_ases
 * @copyright  2018 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/instance_management/instance_lib.php');
require_once('../managers/academic_reports/academic_reports_lib.php');
include('../lib.php');
include("../classes/output/report_grade_book_page.php");
include("../classes/output/renderer.php");

global $PAGE;

$title = "REPORTE DE NOTAS";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);
$id_course = optional_param('id_course',0,PARAM_INT);

require_login($courseid, false);

//se consulta si la instancia ya está registrada
if(!consult_instance($blockid)){
    header("Location: /blocks/ases/view/instance_configuration.php?courseid=$courseid&instanceid=$blockid");
}

$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/report_grade_book.php",array('courseid' => $courseid, 'instanceid' => $blockid,'id_course' => $id_course));

//Navigation setup
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create("Reportes Académicos", new moodle_url("/blocks/ases/view/academic_reports.php",array('courseid' => $courseid, 'instanceid' => $blockid)), null, 'block', $blockid);
$coursenode->add_node($blocknode);
$node = $blocknode->add($title,$url);
$blocknode->make_active();
$node->make_active();

$PAGE->set_url($url);
$PAGE->set_title($title);

$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert2.css', true);
$PAGE->requires->js_call_amd('block_ases/report_grade_book_main', 'init');


$output = $PAGE->get_renderer('block_ases');


//Carga de informacion a mostrar
$curso = get_info_course_for_reports($id_course, $USER->id);
$htmlTable = $curso->header_categories;
$students = "<div id = 'students-pilos' hidden> ";
foreach($curso->estudiantes as $student){
	$code = substr($student->username, 0,7);
	$id = "idmoodle_".$student->id;
    $students.="<div id = '$id' data-code = '$code'>  </div>";
}

$students .= "</div>";
// $number = strlen($header)-24;
// $htmlTable =  iconv_substr($header,$number);
// $htmlTable.=$students;
$record = new stdClass;

$record->nombre_curso = $curso->nombre_curso;
$record->profesor = $curso->profesor;
$record->table = $htmlTable;
$record->students = $students;
echo $output->header();
//echo $output->standard_head_html(); 
$report_grade_book_page = new \block_ases\output\report_grade_book_page($record);
echo $output->render($report_grade_book_page);
echo $output->footer();