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
 * Delete question page.
 *
 * This code is based on question/classes/bank/view.php
 *
 * @package    qbank_deletequestion
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../../editlib.php');
global $DB, $OUTPUT, $PAGE, $COURSE;

$deleteselected = optional_param('deleteselected', false, PARAM_BOOL);
$returnurl = optional_param('returnurl', 0, PARAM_LOCALURL);
$cmid = required_param('cmid', PARAM_INT);
$deleteall = optional_param('deleteall', false, PARAM_BOOL);

if ($returnurl) {
    $returnurl = new moodle_url($returnurl);
}

\core_question\local\bank\helper::require_plugin_enabled('qbank_deletequestion');

[$module, $cm] = get_module_from_cmid($cmid);
require_login($cm->course, false, $cm);
$thiscontext = context_module::instance($cmid);

$contexts = new core_question\local\bank\question_edit_contexts($thiscontext);
$url = new moodle_url('/question/bank/deletequestion/delete.php');

$PAGE->set_url($url);
$streditingquestions = get_string('deletequestion', 'qbank_deletequestion');
$PAGE->set_title($streditingquestions);
$PAGE->set_heading($COURSE->fullname);
$PAGE->activityheader->disable();
$PAGE->set_secondary_active_tab("questionbank");

// Unhide a question.
if (($unhide = optional_param('unhide', '', PARAM_INT)) and confirm_sesskey()) {
    question_require_capability_on($unhide, 'edit');
    $DB->set_field('question_versions', 'status',
        \core_question\local\bank\question_version_status::QUESTION_STATUS_READY, ['questionid' => $unhide]);

    // Purge these questions from the cache.
    \question_bank::notify_question_edited($unhide);

    redirect($returnurl);
}

// If user has already confirmed the action.
if ($deleteselected && ($confirm = optional_param('confirm', '', PARAM_ALPHANUM))
        && confirm_sesskey()) {
    $deleteselected = required_param('deleteselected', PARAM_RAW);
    if ($confirm == md5($deleteselected)) {
        if ($questionlist = explode(',', $deleteselected)) {
            \qbank_deletequestion\helper::delete_questions($questionlist, $deleteall);
        }
        redirect($returnurl);
    } else {
        throw new \moodle_exception('invalidconfirm', 'question');
    }
}

echo $OUTPUT->header();

if ($deleteselected) {
    // Make a list of all the questions that are selected.
    $rawquestions = $_REQUEST; // This code is called by both POST forms and GET links, so cannot use data_submitted.
    $questionlist = '';  // Comma separated list of ids of questions to be deleted.
    foreach ($rawquestions as $key => $value) {    // Parse input for question ids.
        if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
            $key = $matches[1];
            $questionlist .= $key.',';
            question_require_capability_on((int)$key, 'edit');
        }
    }
    if (!$questionlist) { // No questions were selected.
        redirect($returnurl);
    }
    $questionlist = rtrim($questionlist, ',');

    $deleteurl = new \moodle_url(
        '/question/bank/deletequestion/delete.php',
        [
            'deleteselected' => $questionlist,
            'deleteall' => $deleteall,
            'confirm' => md5($questionlist),
            'sesskey' => sesskey(),
            'returnurl' => $returnurl->out_as_local_url(false),
            'cmid' => $cmid,
        ],
    );
    $continue = new \single_button($deleteurl, get_string('delete'), 'post');

    $questionids = explode(',', $questionlist);
    [$displayoptions, $message] = qbank_deletequestion\helper::get_delete_confirmation_message($questionids,
        $deleteall);

    echo $OUTPUT->confirm($message, $continue, $returnurl, $displayoptions);
}

echo $OUTPUT->footer();
