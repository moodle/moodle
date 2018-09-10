<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
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
 * 
 *
 * @package    mod_certificate
 * @copyright  Hernan Arango <hernan.arango@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once('lib.php');

require_login($course, false, $cm);

$id = required_param('id', PARAM_INT);    // Course Module ID


// Course Module ID
if (!$cm = get_coursemodule_from_id('certificateuv', $id)) {
    print_error('Course Module ID was incorrect'); // NOTE this is invalid use of print_error, must be a lang string id
}
if (!$course = $DB->get_record('course', array('id'=> $cm->course))) {
    print_error('course is misconfigured');  // NOTE As above
}
if (!$certificate = $DB->get_record('certificateuv', array('id'=> $cm->instance))) {
    print_error('course module is incorrect'); // NOTE As above
}


$PAGE->set_url('/mod/certificateuv/view.php', array('id' => $cm->id));

$PAGE->set_title(format_string("Asignar Permisos"));
$PAGE->set_heading(format_string("fullname"));
//$PAGE->navbar->add("Cursos Demo");

//$context = context_module::instance($cm->id);
//$PAGE->set_context($context);
//$PAGE->set_cm($cm);

echo $OUTPUT->header();



	//creamos los encabezados de la tabla	
    $table = new html_table();
    $table->classes = array('logtable','generaltable');
    $table->head = array(
        "Imagen",
        "Código",
        "Nombre",
        "Opción"

    );
    $table->data = array();			

	$result = certificateuv_get_user_course($course->id);
	
	//global $CFG;
	foreach ($result as $key => $obj) {
		$row=array();
		$row[]= $OUTPUT->user_picture($obj, array('size'=>50));
		$row[]= $obj->username;
		$row[]= $obj->firstname." ".$lastname;
		
		
		if(certificateuv_get_permission_user($obj->id,$certificate->id)){
			
			$row[]="<input type='checkbox' class='checkoption'  userid='$obj->id' certificateid='$certificate->id' checked>";
		}
		else{
			$row[]="<input type='checkbox' class='checkoption'  userid='$obj->id' certificateid='$certificate->id' >";
			
		}
		$table->data[] = $row;
	}
echo "<div class='box generalbox boxaligncenter boxwidthnormal'>";
echo html_writer::table($table);
echo "</div>";

$url = new moodle_url('/mod/certificateuv/view.php?id='.$id);
$button = new single_button($url, "Volver");
echo html_writer::tag('div', $OUTPUT->render($button), array('style' => 'text-align:center'));	

$PAGE->requires->js_call_amd('mod_certificateuv/checkbox','init');

echo $OUTPUT->footer();





