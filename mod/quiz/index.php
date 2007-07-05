<?php // $Id$
/**
* This page lists all the instances of quiz in a particular course
*
* @version $Id$
* @author Martin Dougiamas and many others.
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/
    require_once("../../config.php");
    require_once("locallib.php");

    $id = required_param('id', PARAM_INT);
    if (!$course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }
    $coursecontext = get_context_instance(CONTEXT_COURSE, $id);
    require_login($course->id);
    add_to_log($course->id, "quiz", "view all", "index.php?id=$course->id", "");

// Print the header
    $strquizzes = get_string("modulenameplural", "quiz");
    $streditquestions = '';
    if (has_capability('moodle/question:manage', $coursecontext)) {
        $streditquestions =
                "<form target=\"_parent\" method=\"get\" action=\"$CFG->wwwroot/question/edit.php\">
                   <div>
                   <input type=\"hidden\" name=\"courseid\" value=\"$course->id\" />
                   <input type=\"submit\" value=\"".get_string("editquestions", "quiz")."\" />
                   </div>
                 </form>";
    }
    
    $navlinks[] = array('name' => $strquizzes, 'link' => '', 'type' => 'activity');
    $navigation = build_navigation($navlinks);
    
    print_header_simple($strquizzes, '', $navigation,
                 '', '', true, $streditquestions, navmenu($course));

// Get all the appropriate data
    if (!$quizzes = get_all_instances_in_course("quiz", $course)) {
        notice("There are no quizzes", "../../course/view.php?id=$course->id");
        die;
    }

// Configure table for displaying the list of instances.
    $headings = array(get_string('name'), get_string('quizcloses', 'quiz'));
    $align = array('left', 'left');
    $colsize = array('', '');
    if ($course->format == "weeks") {
        array_unshift($headings, get_string('week'));
        array_unshift($align, 'center');
        array_unshift($colsize, 10);
    } else if ($course->format == "topics") {
        array_unshift($headings, get_string('topic'));
        array_unshift($align, 'center');
        array_unshift($colsize, 10);
    }

    if (has_capability('mod/quiz:viewreports', $coursecontext)) {
        array_push($headings, get_string('attempts', 'quiz'));
        array_push($align, 'left');
        array_push($colsize, '');
        $showing = 'stats';
    } else if (has_capability('mod/quiz:attempt', $coursecontext)) {
        array_push($headings, get_string('bestgrade', 'quiz'), get_string('feedback', 'quiz'));
        array_push($align, 'left', 'left');
        array_push($colsize, '', '');
        $showing = 'scores';
    }

    $table->head = $headings;
    $table->align = $align;
    $table->size = $colsize;

// Poplate the table with the list of instances.
    $currentsection = '';
    foreach ($quizzes as $quiz) {

        $cm = get_coursemodule_from_instance('quiz', $quiz->id);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        $data = array();

        // Section number if necessary.
        $strsection = '';
        if ($course->format == "weeks" or $course->format == "topics") {
            if ($quiz->section !== $currentsection) {
                if ($quiz->section) {
                    $strsection = $quiz->section;
                }
                if ($currentsection !== "") {
                    $table->data[] = 'hr';
                }
                $currentsection = $quiz->section;
            }
        }
        $data[] = $strsection;

        // Link to the instance.
        $class = '';
        if (!$quiz->visible) {
            $class = ' class="dimmed"';
        }
        $data[] = "<a$class href=\"view.php?id=$quiz->coursemodule\">" . format_string($quiz->name, true) . '</a>';

        // Close date.
        if ($quiz->timeclose) {
            $data[] = userdate($quiz->timeclose);
        } else {
            $data[] = '';
        }

        if ($showing == 'stats') {

            // Number of students who have attempted this quiz.
            if ($a->attemptnum = count_records('quiz_attempts', 'quiz', $quiz->id, 'preview', 0)) {
                $a->studentnum = count_records_select('quiz_attempts',
                        "quiz = '$quiz->id' AND preview = '0'", 'COUNT(DISTINCT userid)');
                $a->studentstring  = $course->students;
                $data[] = "<a href=\"report.php?mode=overview&amp;q=$quiz->id\">" .
                        get_string('numattempts', 'quiz', $a) . '</a>';
            }
        } else if ($showing = 'scores') {

            // Grade and feedback.
            $bestgrade = quiz_get_best_grade($quiz, $USER->id);
            $attempts = quiz_get_user_attempts($quiz->id, $USER->id, 'all');
            list($someoptions, $alloptions) = quiz_get_combined_reviewoptions($quiz, $attempts, $context);

            $grade = '';
            $feedback = '';
            if ($quiz->grade && !is_null($bestgrade)) {
                if ($alloptions->scores) {
                    $grade = "$bestgrade / $quiz->grade";
                }
                if ($alloptions->overallfeedback) {
                    $feedback = quiz_feedback_for_grade($bestgrade, $quiz->id);
                }
            }
            $data[] = $grade;
            $data[] = $feedback;
        }

        $table->data[] = $data;
    } // End of loop over quiz instances.

// Display the table.
    echo '<br />';
    print_table($table);

// Finish the page
    print_footer($course);
?>
