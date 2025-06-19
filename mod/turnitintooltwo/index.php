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
 * @package   turnitintooltwo
 * @copyright 2010 iParadigms LLC
 */

require_once(__DIR__."/../../config.php");
require_once(__DIR__."/lib.php");
require_once(__DIR__."/turnitintooltwo_view.class.php");

$turnitintooltwoview = new turnitintooltwo_view();

// Load Javascript and CSS.
$turnitintooltwoview->load_page_components();

$id = required_param('id', PARAM_INT); // Course id.

// Configure URL correctly.
$urlparams = array('id' => $id);
$url = new moodle_url('/mod/turnitintooltwo/index.php', $urlparams);

// Get course data.
if (!$course = $DB->get_record("course", array("id" => $id))) {
    turnitintooltwo_print_error('courseiderror', 'turnitintooltwo');
}

require_login($course->id);

// Print the header.
$turnitintooltwoview->output_header($url, get_string("modulenameplural", "turnitintooltwo"), $SITE->fullname);

echo $turnitintooltwoview->show_assignments($course);

echo $OUTPUT->footer();