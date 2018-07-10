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
 * This script lists all the instances of quiz in a particular course
 *
 * @package    mod_quiz
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once("../../config.php");
require_once("locallib.php");

$id = required_param('id', PARAM_INT);
$PAGE->set_url('/mod/quiz/index.php', array('id'=>$id));
if (!$course = $DB->get_record('course', array('id' => $id))) {
    print_error('invalidcourseid');
}
$coursecontext = context_course::instance($id);
require_login($course);
$PAGE->set_pagelayout('incourse');

$params = array(
    'context' => $coursecontext
);
$event = \mod_quiz\event\course_module_instance_list_viewed::create($params);
$event->trigger();

// Print the header.
$strquizzes = get_string("modulenameplural", "quiz");
$PAGE->navbar->add($strquizzes);
$PAGE->set_title($strquizzes);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading($strquizzes, 2);

// Get all the appropriate data.
if (!$quizzes = get_all_instances_in_course("quiz", $course)) {
    notice(get_string('thereareno', 'moodle', $strquizzes), "../../course/view.php?id=$course->id");
    die;
}

// Check if we need the feedback header.
$showfeedback = false;
foreach ($quizzes as $quiz) {
    if (quiz_has_feedback($quiz)) {
        $showfeedback=true;
    }
    if ($showfeedback) {
        break;
    }
}

// Configure table for displaying the list of instances.
$headings = array(get_string('name'));
$align = array('left');

array_push($headings, get_string('quizcloses', 'quiz'));
array_push($align, 'left');

if (course_format_uses_sections($course->format)) {
    array_unshift($headings, get_string('sectionname', 'format_'.$course->format));
} else {
    array_unshift($headings, '');
}
array_unshift($align, 'center');

$showing = '';

if (has_capability('mod/quiz:viewreports', $coursecontext)) {
    array_push($headings, get_string('attempts', 'quiz'));
    array_push($align, 'left');
    $showing = 'stats';

} else if (has_any_capability(array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'),
        $coursecontext)) {
    array_push($headings, get_string('grade', 'quiz'));
    array_push($align, 'left');
    if ($showfeedback) {
        array_push($headings, get_string('feedback', 'quiz'));
        array_push($align, 'left');
    }
    $showing = 'grades';

    $grades = $DB->get_records_sql_menu('
            SELECT qg.quiz, qg.grade
            FROM {quiz_grades} qg
            JOIN {quiz} q ON q.id = qg.quiz
            WHERE q.course = ? AND qg.userid = ?',
            array($course->id, $USER->id));
}

$table = new html_table();
$table->head = $headings;
$table->align = $align;

// Populate the table with the list of instances.
$currentsection = '';
// Get all closing dates.
$timeclosedates = quiz_get_user_timeclose($course->id);
foreach ($quizzes as $quiz) {
    $cm = get_coursemodule_from_instance('quiz', $quiz->id);
    $context = context_module::instance($cm->id);
    $data = array();

    // Section number if necessary.
    $strsection = '';
    if ($quiz->section != $currentsection) {
        if ($quiz->section) {
            $strsection = $quiz->section;
            $strsection = get_section_name($course, $quiz->section);
        }
        if ($currentsection) {
            $learningtable->data[] = 'hr';
        }
        $currentsection = $quiz->section;
    }
    $data[] = $strsection;

    // Link to the instance.
    $class = '';
    if (!$quiz->visible) {
        $class = ' class="dimmed"';
    }
    $data[] = "<a$class href=\"view.php?id=$quiz->coursemodule\">" .
            format_string($quiz->name, true) . '</a>';

    // Close date.
    if (($timeclosedates[$quiz->id]->usertimeclose != 0)) {
        $data[] = userdate($timeclosedates[$quiz->id]->usertimeclose);
    } else {
        $data[] = get_string('noclose', 'quiz');
    }

    if ($showing == 'stats') {
        // The $quiz objects returned by get_all_instances_in_course have the necessary $cm
        // fields set to make the following call work.
        $data[] = quiz_attempt_summary_link_to_reports($quiz, $cm, $context);

    } else if ($showing == 'grades') {
        // Grade and feedback.
        $attempts = quiz_get_user_attempts($quiz->id, $USER->id, 'all');
        list($someoptions, $alloptions) = quiz_get_combined_reviewoptions(
                $quiz, $attempts);

        $grade = '';
        $feedback = '';
        if ($quiz->grade && array_key_exists($quiz->id, $grades)) {
            if ($alloptions->marks >= question_display_options::MARK_AND_MAX) {
                $a = new stdClass();
                $a->grade = quiz_format_grade($quiz, $grades[$quiz->id]);
                $a->maxgrade = quiz_format_grade($quiz, $quiz->grade);
                $grade = get_string('outofshort', 'quiz', $a);
            }
            if ($alloptions->overallfeedback) {
                $feedback = quiz_feedback_for_grade($grades[$quiz->id], $quiz, $context);
            }
        }
        $data[] = $grade;
        if ($showfeedback) {
            $data[] = $feedback;
        }
    }

    $table->data[] = $data;
} // End of loop over quiz instances.

// Display the table.
echo html_writer::table($table);

// Finish the page.
echo $OUTPUT->footer();
