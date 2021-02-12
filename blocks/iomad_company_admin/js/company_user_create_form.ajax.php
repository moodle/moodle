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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @basedon   mod_feedback
 * @writtenby Andreas Grabs
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once('../lib.php');

$licenseid = required_param('licenseid', PARAM_INT);

$context = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:user_create', $context);

$return = '';

if ($license = $DB->get_record('companylicense', array('id' => $licenseid))) {
    if ($license->program) {
        $type = ' type="hidden"';
        $liccourses = $DB->get_records('companylicense_courses', array('licenseid' => $licenseid));
        $selected = "selected disabled";
        $license->used = $license->used / count($liccourses);
        $license->allocation = $license->allocation / count($liccourses);
    } else {
        $selected="";
    }
    if ($courses = $DB->get_records_sql_menu("SELECT c.id, c.fullname FROM {companylicense_courses} clc
                                              JOIN {course} c ON (clc.courseid = c.id
                                              AND clc.licenseid = :licenseid)
                                              ORDER BY c.fullname",
                                              array('licenseid' => $licenseid))) {
        $return .= '<select id="licensecourseselector" name="licensecourses[]" multiple="multiple">';
        $return .= '<optgroup label="'.$license->name.' ('.$license->used.'/'.$license->allocation.')">';
        foreach ($courses as $id => $course) {
            $return .= '<option value="'. $id . '" ' . $selected . '>' . $course .'</option>';
        }
        $return .= '</optgroup></select>';
    }
    if ($license->program) {
        $return .= "</div>PROGRAM";
    }

}
echo $return;
die;
