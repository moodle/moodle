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
 * This file is responsible for displaying the survey
 *
 * @package   mod_survey
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("lib.php");

$id = required_param('id', PARAM_INT);    // Course Module ID.

if (! $cm = get_coursemodule_from_id('survey', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
    print_error('coursemisconf');
}

$PAGE->set_url('/mod/survey/view.php', array('id' => $id));
require_login($course, false, $cm);
$context = context_module::instance($cm->id);

require_capability('mod/survey:participate', $context);

if (! $survey = $DB->get_record("survey", array("id" => $cm->instance))) {
    print_error('invalidsurveyid', 'survey');
}
$trimmedintro = trim($survey->intro);
if (empty($trimmedintro)) {
    $tempo = $DB->get_field("survey", "intro", array("id" => $survey->template));
    $survey->intro = get_string($tempo, "survey");
}

if (! $template = $DB->get_record("survey", array("id" => $survey->template))) {
    print_error('invalidtmptid', 'survey');
}

$showscales = ($template->name != 'ciqname');

// Check the survey hasn't already been filled out.
$surveyalreadydone = survey_already_done($survey->id, $USER->id);
if ($surveyalreadydone) {
    // Trigger course_module_viewed event and completion.
    survey_view($survey, $course, $cm, $context, 'graph');
} else {
    survey_view($survey, $course, $cm, $context, 'form');
}

$strsurvey = get_string("modulename", "survey");
$PAGE->set_title($survey->name);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading($survey->name);

// Check to see if groups are being used in this survey.
if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used.
    $currentgroup = groups_get_activity_group($cm);
} else {
    $currentgroup = 0;
}
$groupingid = $cm->groupingid;

if (has_capability('mod/survey:readresponses', $context) or ($groupmode == VISIBLEGROUPS)) {
    $currentgroup = 0;
}

if (has_capability('mod/survey:readresponses', $context)) {
    $numusers = survey_count_responses($survey->id, $currentgroup, $groupingid);
    echo "<div class=\"reportlink\"><a href=\"report.php?id=$cm->id\">".
          get_string("viewsurveyresponses", "survey", $numusers)."</a></div>";
} else if (!$cm->visible) {
    notice(get_string("activityiscurrentlyhidden"));
}

if (!is_enrolled($context)) {
    echo $OUTPUT->notification(get_string("guestsnotallowed", "survey"));
}

if ($surveyalreadydone) {

    $numusers = survey_count_responses($survey->id, $currentgroup, $groupingid);

    if ($showscales) {
        // Ensure that graph.php will allow the user to see the graph.
        if (has_capability('mod/survey:readresponses', $context) || !$groupmode || groups_is_member($currentgroup)) {

            echo $OUTPUT->box(get_string("surveycompleted", "survey"));
            echo $OUTPUT->box(get_string("peoplecompleted", "survey", $numusers));

            echo '<div class="resultgraph">';
            survey_print_graph("id=$cm->id&amp;sid=$USER->id&amp;group=$currentgroup&amp;type=student.png");
            echo '</div>';
        } else {
            echo $OUTPUT->box(get_string("surveycompletednograph", "survey"));
            echo $OUTPUT->box(get_string("peoplecompleted", "survey", $numusers));
        }

    } else {

        echo $OUTPUT->box(format_module_intro('survey', $survey, $cm->id), 'generalbox', 'intro');
        echo $OUTPUT->spacer(array('height' => 30, 'width' => 1), true);  // Should be done with CSS instead.

        $questions = survey_get_questions($survey);
        foreach ($questions as $question) {

            if ($question->type == 0 or $question->type == 1) {
                if ($answer = survey_get_user_answer($survey->id, $question->id, $USER->id)) {
                    $table = new html_table();
                    $table->head = array(get_string($question->text, "survey"));
                    $table->align = array ("left");
                    $table->data[] = array(s($answer->answer1));// No html here, just plain text.
                    echo html_writer::table($table);
                    echo $OUTPUT->spacer(array('height' => 30, 'width' => 1), true);
                }
            }
        }
    }

    echo $OUTPUT->footer();
    exit;
}

echo "<form method=\"post\" action=\"save.php\" id=\"surveyform\">";
echo '<div>';
echo "<input type=\"hidden\" name=\"id\" value=\"$id\" />";
echo "<input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />";

echo $OUTPUT->box(format_module_intro('survey', $survey, $cm->id), 'generalbox boxaligncenter bowidthnormal', 'intro');
echo '<div>'. get_string('allquestionrequireanswer', 'survey'). '</div>';

// Get all the major questions in order.
$questions = survey_get_questions($survey);

global $qnum;  // TODO: ugly globals hack for survey_print_*().
$qnum = 0;
foreach ($questions as $question) {

    if ($question->type >= 0) {

        $question = survey_translate_question($question);

        if ($question->multi) {
            survey_print_multi($question);
        } else {
            survey_print_single($question);
        }
    }
}

if (!is_enrolled($context)) {
    echo '</div>';
    echo "</form>";
    echo $OUTPUT->footer();
    exit;
}

$PAGE->requires->js_call_amd('mod_survey/validation', 'ensureRadiosChosen', array('surveyform'));

echo '<br />';
echo '<input type="submit" class="btn btn-primary" value="'.get_string("clicktocontinue", "survey").'" />';
echo '</div>';
echo "</form>";

echo $OUTPUT->footer();


