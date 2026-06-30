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
 * Script to download the export of several question.
 *
 * @package    qbank_exporttoxml
 * @copyright  2026 MoodleMoot DACH
 * @author     Andreas Steiger, Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');

// Get the parameters from the URL.
$returnurl = optional_param('returnurl', 0, PARAM_LOCALURL);
$cmid = required_param('cmid', PARAM_INT);
$urlparams = ['sesskey' => sesskey()];

\core_question\local\bank\helper::require_plugin_enabled('qbank_exporttoxml');

$cm = get_coursemodule_from_id(null, $cmid);
require_login($cm->course, false, $cm);
$thiscontext = context_module::instance($cmid);
$urlparams['cmid'] = $cmid;

if ($returnurl) {
    $returnurl = new moodle_url($returnurl);
}
// Load the necessary data.
$contexts = new core_question\local\bank\question_edit_contexts($thiscontext);

require_sesskey();

// Initialise $PAGE. Nothing is output, so this does not really matter. Just avoids notices.
$PAGE->set_url('/question/bank/exporttoxml/exportmany.php', $urlparams);
$PAGE->set_heading($COURSE->fullname);
$PAGE->set_pagelayout('admin');

// Make a list of all the questions that are selected.
$rawquestions = $_REQUEST; // This code is called by both POST forms and GET links, so cannot use data_submitted.
$questionlist = [];  // Array of ids of questions to be exported.
foreach ($rawquestions as $key => $value) {    // Parse input for question ids.
    if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
        $key = $matches[1];
        $questionlist[] = question_bank::load_question_data((int)$key);
        question_require_capability_on((int)$key, 'edit');
    }
}
if (!$questionlist) { // No questions were selected.
    redirect($returnurl);
}

// Set up the export format.
$qformat = new qformat_xml();
$filename = question_default_export_filename(
    $COURSE,
    (object) ['name' => get_string('selectedquestions', 'qbank_exporttoxml')],
) . $qformat->export_file_extension();
$qformat->setContexts($contexts->having_one_edit_tab_cap('export'));
$qformat->setCourse($COURSE);
$qformat->setQuestions($questionlist);
$qformat->setCattofile(false);
$qformat->setContexttofile(false);

// Do the export.
if (!$qformat->exportpreprocess()) {
    send_file_not_found();
}
if (!$content = $qformat->exportprocess(true)) {
    send_file_not_found();
}
send_file($content, $filename, 0, 0, true, true, $qformat->mime_type());
