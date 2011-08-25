<?php
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.


/**
 * This file contains submissions-specific code for the basiclti module
 *
 * @package blti
 * @copyright 2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Marc Alier
 * @author Jordi Piguillem
 * @author Nikolas Galanis
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("../../config.php");
require_once($CFG->dirroot.'/mod/blti/lib.php');
require_once($CFG->libdir.'/plagiarismlib.php');

$id   = optional_param('id', 0, PARAM_INT);          // Course module ID
$a    = optional_param('a', 0, PARAM_INT);           // Assignment ID
$mode = optional_param('mode', 'all', PARAM_ALPHA);  // What mode are we in?
$download = optional_param('download' , 'none', PARAM_ALPHA); //ZIP download asked for?

$url = new moodle_url('/mod/blti/submissions.php');
if ($id) {
    if (! $cm = get_coursemodule_from_id('blti', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $basiclti = $DB->get_record("blti", array("id"=>$cm->instance))) {
        print_error('invalidid', 'blti');
    }

    if (! $course = $DB->get_record("course", array("id"=>$basiclti->course))) {
        print_error('coursemisconf', 'blti');
    }
    $url->param('id', $id);
} else {
    if (!$basiclti = $DB->get_record("blti", array("id"=>$a))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id"=>$basiclti->course))) {
        print_error('coursemisconf', 'blti');
    }
    if (! $cm = get_coursemodule_from_instance("blti", $basiclti->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
    $url->param('a', $a);
}

if ($mode !== 'all') {
    $url->param('mode', $mode);
}
$PAGE->set_url($url);
require_login($course, false, $cm);

require_capability('mod/blti:grade', get_context_instance(CONTEXT_MODULE, $cm->id));

blti_submissions($cm, $course, $basiclti, $mode);   // Display or process the submissions
