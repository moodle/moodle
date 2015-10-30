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
 * Redirect the user based on their capabilities to either a scorm activity or to scorm reports
 *
 * @package   mod_scorm
 * @category  grade
 * @copyright 2010 onwards Dan Marsden
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");

$id   = required_param('id', PARAM_INT);          // Course module ID

if (! $cm = get_coursemodule_from_id('scorm', $id)) {
    print_error('invalidcoursemodule');
}

if (! $scorm = $DB->get_record('scorm', array('id'=>$cm->instance))) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record('course', array('id'=> $scorm->course))) {
    print_error('coursemisconf');
}

require_login($course, false, $cm);

if (has_capability('mod/scorm:viewreport', context_module::instance($cm->id))) {
    redirect('report.php?id='.$cm->id);
} else {
    redirect('view.php?id='.$cm->id);
}
