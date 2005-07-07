<?php  // $Id$
/**
* This page prints a review of a particular quiz attempt
*
* @version $Id$
* @author Martin Dougiamas and many others. This has recently been completely
*         rewritten by Alex Smith, Julian Sedding and Gustav Delius as part of
*         the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/

    require_once("../../config.php");
    require_once("locallib.php");

    $attempt = required_param('attempt', PARAM_INT);    // A particular attempt ID for review
    $page = optional_param('page', 0, PARAM_INT); // The required page
    $showall = optional_param('showall', 0, PARAM_BOOL);

    if (! $attempt = get_record("quiz_attempts", "id", $attempt)) {
        error("No such attempt ID exists");
    }
    if (! $quiz = get_record("quiz", "id", $attempt->quiz)) {
        error("The quiz with id $attempt->quiz belonging to attempt $attempt is missing");
    }
    if (! $course = get_record("course", "id", $quiz->course)) {
        error("The course with id $quiz->course that the quiz with id $quiz->id belongs to is missing");
    }
    if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
        error("The course module for the quiz with id $quiz->id is missing");
    }

    if (!count_records('quiz_newest_states', 'attemptid', $attempt->uniqueid)) {
        // this question has not yet been upgraded to the new model
        quiz_upgrade_states($attempt);
    }

    require_login($course->id, false, $cm);
    $isteacher = isteacher($course->id);
    $popup = $isteacher ? 0 : $quiz->popup; // Controls whether this is shown in a javascript-protected window.

    if (!$isteacher) {
        if (!$attempt->timefinish) {
            redirect('attempt.php?q='.$quiz->id);
        }
        // If not even responses are to be shown in review then we
        // don't allow any review
        if (!($quiz->review & QUIZ_REVIEW_RESPONSES)) {
            redirect('view.php?q='.$quiz->id);
        }
        if ((time() - $attempt->timefinish) > 120) { // always allow review right after attempt
            if ((!$quiz->timeclose or time() < $quiz->timeclose) and !($quiz->review & QUIZ_REVIEW_OPEN)) {
                redirect('view.php?q='.$quiz->id, get_string("noreviewuntil", "quiz", userdate($quiz->timeclose)));
            }
            if ($quiz->timeclose and time() >= $quiz->timeclose and !($quiz->review & QUIZ_REVIEW_CLOSED)) {
                redirect('view.php?q='.$quiz->id, get_string("noreview", "quiz"));
            }
        }
        if ($attempt->userid != $USER->id) {
            error("This is not your attempt!", 'view.php?q='.$quiz->id);
        }
    }

    add_to_log($course->id, "quiz", "review", "review.php?id=$cm->id&amp;attempt=$attempt->id", "$quiz->id", "$cm->id");

/// Print the page header

    $strquizzes = get_string("modulenameplural", "quiz");
    $strreview  = get_string("review", "quiz");
    $strscore  = get_string("score", "quiz");
    $strgrade  = get_string("grade");
    $strbestgrade  = get_string("bestgrade", "quiz");
    $strtimetaken     = get_string("timetaken", "quiz");
    $strtimecompleted = get_string("completedon", "quiz");
    $stroverdue = get_string("overdue", "quiz");

    if (!empty($popup)) {
        define('MESSAGE_WINDOW', true);  // This prevents the message window coming up
        print_header($course->shortname.': '.format_string($quiz->name), '', '', '', '', false, '', '', false, '');
        /// Include Javascript protection for this page
        include('protect_js.php');
    } else {
        $strupdatemodule = isteacheredit($course->id)
                    ? update_module_button($cm->id, $course->id, get_string('modulename', 'quiz'))
                    : "";
        print_header_simple(format_string($quiz->name), "",
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a>
                  -> <a href=\"view.php?id=$cm->id\">".format_string($quiz->name,true)."</a> -> $strreview",
                 "", "", true, $strupdatemodule);
    }
    echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

/// Print heading and tabs if this is part of a preview
    if ($isteacher) {
        $currenttab = ($attempt->userid == $USER->id) ? 'preview' : '';
        include('tabs.php');
    } else {
        print_heading(format_string($quiz->name));
    }

/// Load all the questions and states needed by this script

    // load the questions needed by page
    $pagelist = $showall ? quiz_questions_in_quiz($attempt->layout) : quiz_questions_on_page($attempt->layout, $page);
    $sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
           "  FROM {$CFG->prefix}quiz_questions q,".
           "       {$CFG->prefix}quiz_question_instances i".
           " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
           "   AND q.id IN ($pagelist)";
    if (!$questions = get_records_sql($sql)) {
        error('No questions found');
    }

    // Load the question type specific information
    if (!quiz_get_question_options($questions)) {
        error('Could not load question options');
    }

    // Restore the question sessions to their most recent states
    // creating new sessions where required
    if (!$states = quiz_get_states($questions, $quiz, $attempt)) {
        error('Could not restore question sessions');
    }

/// Print infobox

    $timelimit = (int)$quiz->timelimit * 60;
    $overtime = 0;

    if ($attempt->timefinish) {
        if ($timetaken = ($attempt->timefinish - $attempt->timestart)) {
            if($timelimit && $timetaken > ($timelimit + 60)) {
                $overtime = $timetaken - $timelimit;
                $overtime = format_time($overtime);
            }
            $timetaken = format_time($timetaken);
        } else {
            $timetaken = "-";
        }
    } else {
        $timetaken = get_string('unfinished', 'quiz');
    }

    $table->align  = array("right", "left");
    if ($attempt->userid <> $USER->id) {
       $student = get_record('user', 'id', $attempt->userid);
       $picture = print_user_picture($student->id, $course->id, $student->picture, false, true);
       $table->data[] = array($picture, fullname($student, true));
    }
    if ($isteacher and count($attempts = get_records_select('quiz_attempts', "quiz = '$quiz->id' AND userid = '$attempt->userid'", 'attempt ASC')) > 1) {
        // print list of attempts
        $attemptlist = '';
        foreach ($attempts as $at) {
            $attemptlist .= ($at->id == $attempt->id)
                ? '<b>'.$at->attempt.'</b>, '
                : '<a href="review.php?attempt='.$at->id.($showall?'&amp;showall=true':'').'">'.$at->attempt.'</a>, ';
        }
        $table->data[] = array(get_string('attempts', 'quiz').':', trim($attemptlist, ' ,'));
    }

    $table->data[] = array(get_string('startedon', 'quiz').':', userdate($attempt->timestart));
    if ($attempt->timefinish) {
        $table->data[] = array("$strtimecompleted:", userdate($attempt->timefinish));
        $table->data[] = array("$strtimetaken:", $timetaken);
    }
    if (!empty($overtime)) {
        $table->data[] = array("$stroverdue:", $overtime);
    }
    if ($quiz->grade) {
        if($overtime) {
            $result->sumgrades = "0";
            $result->grade = "0.0";
        }
        $percentage = round(($attempt->sumgrades/$quiz->sumgrades)*100, 0);
        $grade = round(($attempt->sumgrades/$quiz->sumgrades)*$quiz->grade, $CFG->quiz_decimalpoints);
        $table->data[] = array("$strscore:", "$attempt->sumgrades/$quiz->sumgrades ($percentage %)");
        $table->data[] = array("$strgrade:", $grade.get_string('outof', 'quiz').$quiz->grade);
    }
    if ($isteacher and $attempt->userid == $USER->id) {
        // the teacher is at the end of a preview. Print button to start new preview
        unset($buttonoptions);
        $buttonoptions['q'] = $quiz->id;
        $buttonoptions['forcenew'] = true;
        echo '<center>';
        print_single_button($CFG->wwwroot.'/mod/quiz/attempt.php', $buttonoptions, get_string('startagain', 'quiz'));
        echo '</center>';
    } else { // print number of the attempt
        print_heading(get_string('reviewofattempt', 'quiz', $attempt->attempt));
    }
    print_table($table);

    // print javascript button to close the window, if necessary
    if (!$isteacher) {
        include('attempt_close_js.php');
    }

/// Print the navigation panel if required
    $numpages = quiz_number_of_pages($attempt->layout);
    if ($numpages > 1 and !$showall) {
        print_paging_bar($numpages, $page, 1, 'review.php?attempt='.$attempt->id.'&amp;');
        echo '<center><a href="review.php?attempt='.$attempt->id.'&amp;showall=true">';
        print_string('showall', 'quiz');
        echo '</a></center>';
    }

/// Print all the questions

    $pagequestions = explode(',', $pagelist);
    $number = quiz_first_questionnumber($attempt->layout, $pagelist);
    foreach ($pagequestions as $i) {
        if (!isset($questions[$i])) {
            print_simple_box_start('center', '90%');
            echo '<b><font size="+1">' . $number . '</font></b><br />';
            notify(get_string('errormissingquestion', 'quiz', $i));
            print_simple_box_end();
            $number++; // Just guessing that the missing question would have lenght 1
            continue;
        }
        $options = quiz_get_reviewoptions($quiz, $attempt, $isteacher);
        $options->validation = QUIZ_EVENTVALIDATE === $states[$i]->event;
        $options->history = ($isteacher and !$attempt->preview) ? 'all' : 'graded';
        // Print the question
        if ($i > 0) {
            echo "<br />\n";
        }
        quiz_print_quiz_question($questions[$i], $states[$i], $number, $quiz, $options);
        $number += $questions[$i]->length;
    }

    // Print the navigation panel if required
    if ($numpages > 1 and !$showall) {
        print_paging_bar($numpages, $page, 1, 'review.php?attempt='.$attempt->id.'&amp;');
    }

    // print javascript button to close the window, if necessary
    if (!$isteacher) {
        include('attempt_close_js.php');
    }

    if (empty($popup)) {
        print_footer($course);
    }
?>
