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
 * Move questions page.
 *
 * @package    qbank_bulkmove
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../../editlib.php');

global $DB, $OUTPUT, $PAGE, $COURSE;

$moveselected = optional_param('move', false, PARAM_BOOL);
$returnurl = optional_param('returnurl', 0, PARAM_LOCALURL);
$cmid = optional_param('cmid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$category = optional_param('category', null, PARAM_SEQUENCE);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);
$movequestionselected = optional_param('movequestionsselected', null, PARAM_RAW);

if ($returnurl) {
    $returnurl = new moodle_url($returnurl);
}

\core_question\local\bank\helper::require_plugin_enabled('qbank_bulkmove');

if ($cmid) {
    list($module, $cm) = get_module_from_cmid($cmid);
    require_login($cm->course, false, $cm);
    $thiscontext = context_module::instance($cmid);
} else if ($courseid) {
    require_login($courseid, false);
    $thiscontext = context_course::instance($courseid);
} else {
    throw new moodle_exception('missingcourseorcmid', 'question');
}

$contexts = new core_question\local\bank\question_edit_contexts($thiscontext);
$url = new moodle_url('/question/bank/bulkmove/move.php');

$PAGE->set_url($url);
$streditingquestions = get_string('movequestions', 'qbank_bulkmove');
$PAGE->set_title($streditingquestions);
$PAGE->set_heading($COURSE->fullname);
$PAGE->activityheader->disable();
$PAGE->set_secondary_active_tab("questionbank");

if ($category) {
    list($tocategoryid, $contextid) = explode(',', $category);
    if (! $tocategory = $DB->get_record('question_categories',
        ['id' => $tocategoryid, 'contextid' => $contextid])) {
        throw new \moodle_exception('cannotfindcate', 'question');
    }
}

if ($movequestionselected && $confirm && confirm_sesskey()) {
    if ($confirm == md5($movequestionselected)) {
        \qbank_bulkmove\helper::bulk_move_questions($movequestionselected, $tocategory);
    }
    redirect(new moodle_url($returnurl, ['category' => "{$tocategoryid},{$contextid}"]));
}

echo $OUTPUT->header();

if ($moveselected) {
    $rawquestions = $_REQUEST;
    list($questionids, $questionlist) = \qbank_bulkmove\helper::process_question_ids($rawquestions);
    // No questions were selected.
    if (!$questionids) {
        redirect($returnurl);
    }
    // Create the urls.
    $moveparam = [
        'movequestionsselected' => $questionlist,
        'confirm' => md5($questionlist),
        'sesskey' => sesskey(),
        'returnurl' => $returnurl,
        'cmid' => $cmid,
        'courseid' => $courseid,
    ];
    $moveurl = new \moodle_url($url, $moveparam);

    $addcontexts = $contexts->having_cap('moodle/question:add');
    $displaydata = \qbank_bulkmove\helper::get_displaydata($addcontexts, $moveurl, $returnurl);
    echo $PAGE->get_renderer('qbank_bulkmove')->render_bulk_move_form($displaydata);
}

echo $OUTPUT->footer();
