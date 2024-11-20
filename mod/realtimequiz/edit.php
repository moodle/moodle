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
 * This allows you to edit questions for a realtimequiz
 *
 * @copyright Davo Smith <moodle@davosmith.co.uk>
 * @package mod_realtimequiz
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

require_once('../../config.php');
global $CFG, $DB, $PAGE, $OUTPUT;
require_once($CFG->dirroot.'/mod/realtimequiz/lib.php');

$id = optional_param('id', false, PARAM_INT);
$quizid = optional_param('quizid', false, PARAM_INT);
$action = optional_param('action', 'listquestions', PARAM_ALPHA);
$questionid = optional_param('questionid', 0, PARAM_INT);

$addanswers = optional_param('addanswers', false, PARAM_BOOL);
$saveadd = optional_param('saveadd', false, PARAM_BOOL);
$canceledit = optional_param('cancel', false, PARAM_BOOL);

$removeimage = optional_param('removeimage', false, PARAM_BOOL);

if ($id) {
    $cm = get_coursemodule_from_id('realtimequiz', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $quiz = $DB->get_record('realtimequiz', ['id' => $cm->instance], '*', MUST_EXIST);
    $quizid = $quiz->id;
} else {
    $quiz = $DB->get_record('realtimequiz', ['id' => $quizid], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $quiz->course], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('realtimequiz', $quiz->id, $course->id, false, MUST_EXIST);
}

$PAGE->set_url(new moodle_url('/mod/realtimequiz/edit.php', ['id' => $cm->id]));

require_login($course->id, false, $cm);

$PAGE->set_pagelayout('incourse');
if ($CFG->version < 2011120100) {
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
} else {
    $context = context_module::instance($cm->id);
}
require_capability('mod/realtimequiz:editquestions', $context);

// Log this visit.
if ($CFG->version > 2014051200) { // Moodle 2.7+.
    $params = [
        'courseid' => $course->id,
        'context' => $context,
        'other' => [
            'quizid' => $quiz->id,
        ],
    ];
    $event = \mod_realtimequiz\event\edit_page_viewed::create($params);
    $event->trigger();
} else {
    add_to_log($course->id, "realtimequiz", "update: $action", "edit.php?quizid=$quizid");
}

// Some useful functions.
/**
 * List all questions
 * @param int $quizid
 * @param object $cm
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function realtimequiz_list_questions($quizid, $cm) {
    global $DB, $OUTPUT;

    echo '<h2>'.get_string('questionslist', 'realtimequiz').'</h2>';

    $questions = $DB->get_records('realtimequiz_question', ['quizid' => $quizid], 'questionnum');
    $questioncount = count($questions);
    $expectednumber = 1;
    echo '<ol>';
    foreach ($questions as $question) {
        // A good place to double-check the question numbers and fix any that are broken.
        if ($question->questionnum != $expectednumber) {
            $question->questionnum = $expectednumber;
            $DB->update_record('realtimequiz_question', $question);
        }

        $editurl = new moodle_url('/mod/realtimequiz/editquestion.php',
                                  ['quizid' => $quizid, 'questionid' => $question->id]);
        $qtext = format_string($question->questiontext);
        echo "<li><span class='realtimequiz_editquestion'>";
        echo html_writer::link($editurl, $qtext);
        echo " </span><span class='realtimequiz_editicons'>";
        if ($question->questionnum > 1) {
            $alt = get_string('questionmoveup', 'mod_realtimequiz', $question->questionnum);
            echo "<a href='edit.php?quizid=$quizid&amp;action=moveup&amp;questionid=$question->id'>";
            echo $OUTPUT->pix_icon('t/up', $alt);
            echo '</a>';
        } else {
            echo $OUTPUT->spacer(['width' => '16px']);
        }
        if ($question->questionnum < $questioncount) {
            $alt = get_string('questionmovedown', 'mod_realtimequiz', $question->questionnum);
            echo "<a href='edit.php?quizid=$quizid&amp;action=movedown&amp;questionid=$question->id'>";
            echo $OUTPUT->pix_icon('t/down', $alt);
            echo '</a>';
        } else {
            echo $OUTPUT->spacer(['width' => '15px']);
        }
        echo '&nbsp;';
        $alt = get_string('questiondelete', 'mod_realtimequiz', $question->questionnum);
        echo "<a href='edit.php?quizid=$quizid&amp;action=deletequestion&amp;questionid=$question->id'>";
        echo $OUTPUT->pix_icon('t/delete', $alt);
        echo '</a>';
        echo '</span></li>';
        $expectednumber++;
    }
    echo '</ol>';
    $url = new moodle_url('/mod/realtimequiz/editquestion.php', ['quizid' => $quizid]);
    echo $OUTPUT->single_button($url, get_string('addquestion', 'realtimequiz'), 'GET');
}

/**
 * Output the 'confirm delete question' HTML.
 * @param int $quizid
 * @param int $questionid
 * @param context $context
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function realtimequiz_confirm_deletequestion($quizid, $questionid, $context) {
    global $DB;

    $question = $DB->get_record('realtimequiz_question', ['id' => $questionid, 'quizid' => $quizid], '*', MUST_EXIST);

    echo '<center><h2>'.get_string('deletequestion', 'realtimequiz').'</h2>';
    echo '<p>'.get_string('checkdelete', 'realtimequiz').'</p><p>';
    $questiontext = format_text($question->questiontext, $question->questiontextformat);
    $questiontext = file_rewrite_pluginfile_urls($questiontext, 'pluginfile.php', $context->id, 'mod_realtimequiz',
                                                 'question', $questionid);
    echo $questiontext;
    echo '</p>';

    $url = new moodle_url('/mod/realtimequiz/edit.php', ['quizid' => $quizid]);
    echo '<form method="post" action="'.$url.'">';
    echo '<input type="hidden" name="action" value="dodeletequestion" />';
    echo '<input type="hidden" name="questionid" value="'.$questionid.'" />';
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
    echo '<input type="submit" name="yes" value="'.get_string('yes').'" /> ';
    echo '<input type="submit" name="no" value="'.get_string('no').'" />';
    echo '</form></center>';
}

// Back to the main code.
$strrealtimequizzes = get_string("modulenameplural", "realtimequiz");
$strrealtimequiz = get_string("modulename", "realtimequiz");

$PAGE->set_title(strip_tags($course->shortname.': '.$strrealtimequiz.': '.format_string($quiz->name, true)));
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

if ($CFG->branch < 400) {
    echo $OUTPUT->heading(format_string($quiz->name));

    if (class_exists('\core_completion\activity_custom_completion')) {
        // Render the activity information.
        $modinfo = get_fast_modinfo($course);
        $cminfo = $modinfo->get_cm($cm->id);
        $completiondetails = \core_completion\cm_completion_details::get_instance($cminfo, $USER->id);
        $activitydates = \core\activity_dates::get_dates_for_module($cminfo, $USER->id);
        echo $OUTPUT->activity_information($cminfo, $completiondetails, $activitydates);
    }

    realtimequiz_view_tabs('edit', $cm->id, $context);
}

echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter realtimequizbox');

if ($action == 'dodeletequestion') {

    require_sesskey();

    if (optional_param('yes', false, PARAM_BOOL)) {
        if ($question = $DB->get_record('realtimequiz_question', ['id' => $questionid, 'quizid' => $quiz->id])) {
            $answers = $DB->get_records('realtimequiz_answer', ['questionid' => $question->id]);
            if (!empty($answers)) {
                foreach ($answers as $answer) { // Get each answer for that question.
                    // Delete any submissions for that answer.
                    $DB->delete_records('realtimequiz_submitted', ['answerid' => $answer->id]);
                }
            }
            $DB->delete_records('realtimequiz_answer', ['questionid' => $question->id]); // Delete each answer.
            $DB->delete_records('realtimequiz_question', ['id' => $question->id]);

            // Delete files embedded in the heading.
            $fs = get_file_storage();
            $fs->delete_area_files($context->id, 'mod_realtimequiz', 'question', $questionid);
            // Questionnumbers sorted out when we display the list of questions.
        }
    }

    $action = 'listquestions';

} else if ($action == 'moveup') {

    $thisquestion = $DB->get_record('realtimequiz_question', ['id' => $questionid]);
    if ($thisquestion) {
        $questionnum = $thisquestion->questionnum;
        if ($questionnum > 1) {
            $swapquestion = $DB->get_record('realtimequiz_question', [
                'quizid' => $quizid, 'questionnum' => ($questionnum - 1),
            ]);
            if ($swapquestion) {
                $upd = new stdClass;
                $upd->id = $thisquestion->id;
                $upd->questionnum = $questionnum - 1;
                $DB->update_record('realtimequiz_question', $upd);

                $upd = new stdClass;
                $upd->id = $swapquestion->id;
                $upd->questionnum = $questionnum;
                $DB->update_record('realtimequiz_question', $upd);
            }
        }
    }

    $action = 'listquestions';

} else if ($action == 'movedown') {
    $thisquestion = $DB->get_record('realtimequiz_question', ['id' => $questionid]);
    if ($thisquestion) {
        $questionnum = $thisquestion->questionnum;
        $swapquestion = $DB->get_record('realtimequiz_question',
                                        ['quizid' => $quizid, 'questionnum' => ($questionnum + 1)]);
        if ($swapquestion) {
            $upd = new stdClass;
            $upd->id = $thisquestion->id;
            $upd->questionnum = $questionnum + 1;
            $DB->update_record('realtimequiz_question', $upd);

            $upd = new stdClass;
            $upd->id = $swapquestion->id;
            $upd->questionnum = $questionnum;
            $DB->update_record('realtimequiz_question', $upd);
        }
    }

    $action = 'listquestions';
}

switch ($action) {

    case 'listquestions': // Show all the currently available questions.
        realtimequiz_list_questions($quizid, $cm);
        break;

    case 'deletequestion': // Deleting a question - ask 'Are you sure?'.
        realtimequiz_confirm_deletequestion($quizid, $questionid, $context);
        break;

}

echo $OUTPUT->box_end();

// Finish the page.
echo $OUTPUT->footer();

