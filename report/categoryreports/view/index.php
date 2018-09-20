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
 * Script for index of Category Reports
 *
 * @package   report_categoryreports
 * @copyright 2018 Iader E. García G.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once('../managers/attendance_manager.php');

global $PAGE;

$data = new stdClass();

// Setup program filters
$select_programs = "";
$counter_programs = 0;
$array_programs = get_programs();

foreach($array_programs as $program){
    if($counter_programs == 0){
        $array_courses = get_courses_category($program->id);
    }
    $select_programs .= "<option id='$program->id' value='$program->id'>";
    $select_programs .= "$program->name</option>";
    $counter_programs += 1;
}

$data->programs = $select_programs;

// Setup courses filters
$select_courses = "";

foreach($array_courses as $course){
    $select_courses .= "<option id='$course->id' value='$course->id'>";
    $select_courses .= "$course->fullname</option>";
}

$data->courses = $select_courses;

// Set up the page.
$pagetitle = "Index";
$url = new moodle_url("/report/categoryreports/view/index.php");

$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

$PAGE->requires->css('/report/categoryreports/styles/bootstrap.min.css', true);
$PAGE->requires->css('/report/categoryreports/styles/jquery.dataTables.css', true);

$PAGE->requires->js_call_amd('report_categoryreports/attendance','init');

$output = $PAGE->get_renderer('report_categoryreports');
$index_report_page = new \report_categoryreports\output\index_page($data);

echo $output->header();
echo $output->render($index_report_page);
echo $output->footer();
