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
 * The Custom Grader setup page.
 *
 * @package   custom_grader
 * @copyright  2018 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once '../../config.php';
require_once 'managers/grader_lib.php';
require_once($CFG->dirroot . '/grade/export/lib.php');
require_once $CFG->dirroot . '/grade/lib.php';
 $courseid        = required_param('id', PARAM_INT);

$url = new moodle_url('/local/customgrader/index.php', array('id' => $courseid));
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

/// Make sure they can even access this course
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}

require_login($course);
$context = context_course::instance($course->id);
require_capability('moodle/grade:manage', $context);
$PAGE->requires->css('/local/customgrader/style/styles_grader.css', true);
$PAGE->requires->css('/local/customgrader/style/styles_wizard.css', true);
$PAGE->requires->css('/local/customgrader/style/sweetalert.css', true);

$PAGE->requires->js_call_amd('local_customgrader/wizard_categories', 'init');
$PAGE->requires->js_call_amd('local_customgrader/grader', 'init');


print_grade_page_head($courseid, 'settings', null, '', false, false, false);
// print_grade_page_head($courseid, 'settings', 'setup', get_string('gradebooksetup', 'grades'));

echo $OUTPUT->box_start('gradetreebox generalbox');
$title = get_string('title', 'local_customgrader');

$info_course = get_categories_global_grade_book($courseid);
$info_wizard = getCategoriesandItems($courseid);
$docente = getTeacher($courseid);

////////////////////////////////////////////////////////////////////////////////////////////

// //SOLO RAMA UNIVALLE
$info_students = get_info_students($courseid);
$students = "<div id = 'students-ases' hidden> ";
foreach($info_students as $student){
	$code = substr($student->username, 0,7);
	$id = "idmoodle_".$student->id;
    $students.="<div id = '$id' data-code = '$code'>  </div>";
}

$students .= "</div>";
////////////////////////////////////////////////////////////////////////////////////////////


$course_name = $course->fullname;

$tpldata = new stdClass;
$tpldata->title = $title;
$tpldata->nombre_curso = $course_name;
$tpldata->info_wizard = $info_wizard;
$tpldata->table = $info_course;
$tpldata->students = $students;
$tpldata->docente = $docente;

echo $OUTPUT->render_from_template('local_customgrader/index', $tpldata);

echo $OUTPUT->box_end();

echo $OUTPUT->footer();
die;


