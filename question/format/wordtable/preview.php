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
 * Provide a simple mechanism to preview a question based on the course ID and a unique question name
 *
 * @package qformat_wordtable
 * @copyright 2010-2015 Eoin Campbell
 * @author Eoin Campbell
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */

require_once('../../../config.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->libdir.'/questionlib.php');
require_once($CFG->libdir.'/dmllib.php');

global $OUTPUT, $DB;
// Declare empty array to prevent each debug message from including a complete backtrace.
$backtrace = array();

require_login();
// Get the assigned temporary question name.
$qname = required_param('qname', PARAM_TEXT);
// Get the course id.
$courseid = required_param('courseid', PARAM_INT);


// Get the question ID by searching for the unique name, and redirect to the preview page.
if (($question = $DB->get_record('question', array('name' => $qname)))) {
    // Figure out the proper URL, allowing for an installation in a subfolder.
    $moodlerootfolderpath = parse_url($CFG->wwwroot, PHP_URL_PATH);
    $redirecturl = $moodlerootfolderpath . "/question/preview.php?id=" . $question->id . "&courseid=" . $courseid;
    debugging("Preview question: Redirecting to $redirecturl", DEBUG_DEVELOPER, $backtrace);
    redirect($redirecturl);
} else {   // No question found, report an error message so the reader isn't looking at a blank screen.
    debugging("Preview question: No question found", DEBUG_DEVELOPER, $backtrace);
    echo $OUTPUT->notification(get_string('preview_question_not_found', 'qformat_wordtable', $qname . " / " . $courseid));
}
