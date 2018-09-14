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
 * @author     Isabella Serna Ramírez
 * @package    block_ases
 * @copyright  2017 Isabella Serna Ramírez <isabella.serna@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');



/**
 * Function that obtains the select organized from the users of the course
 * @see get_course_users_select($courseusers
 * @param $courseusers ---> array with users associated to the course
 * @return string
 **/
function get_course_users_select($courseusers){
	$table_courseuseres="";
	$table_courseuseres.='<option value=""> ---------------------------------------</option>';
	foreach ($courseusers as $courseuser) {
    	$table_courseuseres.='<option value="'.$courseuser->codigo.'">'.$courseuser->codigo.' - '.$courseuser->nombre.' '.$courseuser->apellido.'</option>';
	}
	return $table_courseuseres;


}

/**
 * Function that obtains the select organized from the users registered to the course 
 * @see get_period_select($periods)
 * @param $periods ---> existent periods
 * @return array
 **/
function get_students_select($students,$name){
	$table="";
    $table.='<div class="container"><form class="form-inline">';
    $table.='<div class="form-group"><select class="form-control" id="'.$name.'">';
    foreach($students as $student){
        $table.='<option value="'.$student->username.'">'.$student->firstname.' -'.''.$student->lastname.'</option>';
     }
    $table.='</select></div>';
    return $table;
}


/**
 * Function that obtaints options from the select organized from students registered to the course
 * @see get_students_option($students)
 * @param $students ---> array de estudiantes
 * @return string
 **/
function get_students_option($students){
    $table="";
    foreach($students as $student){
        $table.='<option value="'.$student->username.'">'.$student->firstname.' -'.''.$student->lastname.'</option>';
     }
    return $table;
}