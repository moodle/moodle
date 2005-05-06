<?php  // $Id$
/**
* This page prints a particular instance of quiz
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

    $id = optional_param('id', 0, PARAM_INT);               // Course Module ID
    $q = optional_param('q', 0, PARAM_INT);                 // or quiz ID
    $page = optional_param('page', 0, PARAM_INT);
    $questionids = optional_param('questionids', '');
    $finishattempt = optional_param('finishattempt', 0, PARAM_BOOL);
    $timeup = optional_param('timeup', 0, PARAM_BOOL); // True if form was submitted by timer.
    $forcenew = optional_param('forcenew', false, PARAM_BOOL); // Teacher has requested new preview

    // We treat automatically closed attempts just like normally closed attempts
    if ($timeup) {
        $finishattempt = 1;
    }

    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }

        if (! $quiz = get_record("quiz", "id", $cm->instance)) {
            error("Course module is incorrect");
        }

    } else {
        if (! $quiz = get_record("quiz", "id", $q)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $quiz->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_login($course->id, false, $cm);
    $isteacher = isteacher($course->id);

// Get number for the next or unfinished attempt
    if(!$attemptnumber = (int)get_field_sql('SELECT MAX(attempt)+1 FROM ' .
     "{$CFG->prefix}quiz_attempts WHERE quiz = '{$quiz->id}' AND " .
     "userid = '{$USER->id}' AND timefinish > 0")) {
        $attemptnumber = 1;
    }

    $strattemptnum = get_string('attempt', 'quiz', $attemptnumber);
    $strquizzes = get_string("modulenameplural", "quiz");
    $popup = $isteacher ? 0 : $quiz->popup; // Controls whether this is shown in a javascript-protected window.

/// Print the page header
    if (!empty($popup)) {
        define('MESSAGE_WINDOW', true);  // This prevents the message window coming up
        print_header($course->shortname.': '.format_string($quiz->name), '', '', '', '', false, '', '', false, '');
        include('protect_js.php');
    } else {
        $strupdatemodule = isteacheredit($course->id)
                    ? update_module_button($cm->id, $course->id, get_string('modulename', 'quiz'))
                    : "";
        print_header_simple(format_string($quiz->name), "",
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a> ->
                  <a href=\"view.php?id=$cm->id\">".format_string($quiz->name)."</a> -> $strattemptnum",
                  "", "", true, $strupdatemodule);
    }

    echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

    /// Print the quiz name heading and tabs for teacher
    if ($isteacher) {
        $currenttab = 'preview';
        include('tabs.php');
    } else {
        print_heading(format_string($quiz->name));
    }

/// Check availability
    if (isguest()) {
        print_heading(get_string('guestsno', 'quiz'));
        if (empty($popup)) {
            print_footer($course);
        }
        exit;
    }

    if ($quiz->attempts and $attemptnumber > $quiz->attempts) {
        error(get_string('nomoreattempts', 'quiz'), "view.php?id={$cm->id}");
    }

    $timenow = time();
    if (($timenow < $quiz->timeopen || $timenow > $quiz->timeclose)) {
        if ($isteacher) {
            notify(get_string('notavailabletostudents', 'quiz'));
        } else {
            error(get_string('notavailable', 'quiz'), "view.php?id={$cm->id}");
        }
    }

/// Check subnet access
    if ($quiz->subnet and !address_in_subnet(getremoteaddr(), $quiz->subnet)) {
        if ($isteacher) {
            notify(get_string('subnetnotice', 'quiz'));
        } else {
            error(get_string("subneterror", "quiz"), "view.php?id=$cm->id");
        }
    }

/// Check password access
    if ($quiz->password and empty($_POST['q'])) {
        if (empty($_POST['quizpassword'])) {

            if (trim(strip_tags($quiz->intro))) {
                print_simple_box(format_text($quiz->intro), "center");
            }
            echo "<br />\n";

            echo "<form name=\"passwordform\" method=\"post\" action=\"attempt.php?id=$cm->id\">\n";
            print_simple_box_start("center");

            echo "<div align=\"center\">\n";
            print_string("requirepasswordmessage", "quiz");
            echo "<br /><br />\n";
            echo " <input name=\"quizpassword\" type=\"password\" value=\"\" alt=\"password\" />";
            echo " <input type=\"submit\" value=\"".get_string("ok")."\" />\n";
            echo "</div>\n";

            print_simple_box_end();
            echo "</form>\n";

            if (empty($popup)) {
                print_footer();
            }
            exit;

        } else {
            if (strcmp($quiz->password, $_POST['quizpassword']) !== 0) {
                error(get_string("passworderror", "quiz"), "view.php?id=$cm->id");
            }
        }
    }

/// Load attempt or create a new attempt if there is no unfinished one

    if ($isteacher and $forcenew) { // teacher wants a new preview
        // so we set a finish time on the current attempt (if any).
        // It will then automatically be deleted below
        set_field('quiz_attempts', 'timefinish', time(), 'quiz', $quiz->id, 'userid', $USER->id);
    }

    $attempt = get_record('quiz_attempts', 'quiz', $quiz->id,
     'userid', $USER->id, 'timefinish', 0);

    if (!$attempt) {
        // Check if this is a preview request from a teacher
        // in which case the previous previews should be deleted
        if ($isteacher) {
            if ($oldattempts = get_records_select('quiz_attempts', "quiz = '$quiz->id'
             AND userid = '$USER->id'")) {
                delete_records('quiz_attempts', 'quiz', $quiz->id, 'userid', $USER->id);
                delete_records('quiz_grades', 'quiz', $quiz->id, 'userid', $USER->id);
                foreach ($oldattempts as $oldattempt) {
                    // there should only be one but we loop just in case
                    delete_records('quiz_states', 'attempt', $oldattempt->id);
                    delete_records('quiz_newest_states', 'attemptid', $oldattempt->id);
                }
            }
        }
        // Start a new attempt and initialize the question sessions
        $attempt = quiz_create_attempt($quiz, $attemptnumber);
        // If this is an attempt by a teacher mark it as a preview
        if ($isteacher) {
            $attempt->preview = 1;
        }
        // Save the attempt
        if (!$attempt->id = insert_record('quiz_attempts', $attempt)) {
            error('Could not create new attempt');
        }
        // make log entries
        if ($isteacher) {
            add_to_log($course->id, 'quiz', 'preview',
                           "attempt.php?id=$cm->id",
                           "$quiz->id", $cm->id);
        } else {
            add_to_log($course->id, 'quiz', 'attempt',
                           "review.php?attempt=$attempt->id",
                           "$quiz->id", $cm->id);
        }
    } else {
        // log continuation of attempt only if some time has lapsed
        if ((time() - $attempt->timemodified) > 600) { // 10 minutes have elapsed
             add_to_log($course->id, 'quiz', 'continue attempt',
                           "review.php?attempt=$attempt->id",
                           "$quiz->id", $cm->id);
        }
    }


/// Load all the questions and states needed by this script

    // list of questions needed by page
    $pagelist = quiz_questions_on_page($attempt->layout, $page);

    // add all questions that are on the submitted form
    if ($questionids) {
        $questionlist = $pagelist.','.$questionids;
    } else {
        $questionlist = $pagelist;
    }

    if (!$questionlist) {
        error(get_string('noquestionsfound', 'quiz'), 'view.php?q='.$quiz->id);
    }

    $sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
           "  FROM {$CFG->prefix}quiz_questions q,".
           "       {$CFG->prefix}quiz_question_instances i".
           " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
           "   AND q.id IN ($questionlist)";

    // Load the questions
    if (!$questions = get_records_sql($sql)) {
        error(get_string('noquestionsfound', 'quiz'), 'view.php?q='.$quiz->id);
    }

    // Load the question type specific information
    if (!quiz_get_question_options($questions)) {
        error('Could not load question options');
    }

    // Restore the question sessions to their most recent states
    // creating new sessions where required
    if (!$states = quiz_restore_question_sessions($questions, $quiz, $attempt)) {
        error('Could not restore question sessions');
    }


/// Process form data /////////////////////////////////////////////////

    if ($responses = data_submitted() and empty($_POST['quizpassword'])) {

        // set the default event. This can be overruled by individual buttons.
        $event = (array_key_exists('markall', $responses)) ? QUIZ_EVENTGRADE :
         ($finishattempt ? QUIZ_EVENTCLOSE : QUIZ_EVENTSAVE);

        // Unset any variables we know are not responses
        unset($responses->id);
        unset($responses->q);
        unset($responses->oldpage);
        unset($responses->newpage);
        unset($responses->review);
        unset($responses->questionids);
        unset($responses->saveattempt); // responses get saved anway
        unset($responses->finishattempt); // same as $finishattempt
        unset($responses->markall);
        unset($responses->forcenewattempt);

        // extract responses
        // $actions is an array indexed by the questions ids
        $actions = quiz_extract_responses($questions, $responses, $event);

        // Process each question in turn

        $questionidarray = explode(',', $questionids);
        foreach($questionidarray as $i) {
            if (!isset($actions[$i])) {
                $actions[$i]->responses = array('' => '');
            }
            quiz_process_responses($questions[$i], $states[$i], $actions[$i], $quiz, $attempt);
            quiz_save_question_session($questions[$i], $states[$i]);
        }

        $attempt->timemodified = time();

    // We have now finished processing form data
    }


/// Finish attempt if requested
    if ($finishattempt) {

        // Set the attempt to be finished
        $attempt->timefinish = time();

        // Find all the questions for this attempt for which the newest
        // state is not also the newest graded state
        if ($closequestions = get_records_select('quiz_newest_states',
         "attemptid = $attempt->id AND new != newgraded", '', 'questionid, questionid')) {

            // load all the questions
            $closequestionlist = implode(',', array_keys($closequestions));
            $sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
                   "  FROM {$CFG->prefix}quiz_questions q,".
                   "       {$CFG->prefix}quiz_question_instances i".
                   " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
                   "   AND q.id IN ($closequestionlist)";
            if (!$closequestions = get_records_sql($sql)) {
                error('Questions missing');
            }

            // Load the question type specific information
            if (!quiz_get_question_options($closequestions)) {
                error('Could not load question options');
            }

            // Restore the question sessions
            if (!$closestates = quiz_restore_question_sessions($closequestions, $quiz, $attempt)) {
                error('Could not restore question sessions');
            }

            foreach($closequestions as $key => $question) {
                $action->event = QUIZ_EVENTCLOSE;
                $action->responses = $closestates[$key]->responses;
                quiz_process_responses($question, $closestates[$key], $action, $quiz, $attempt);
                            quiz_save_question_session($question, $closestates[$key]);
            }
        }
        add_to_log($course->id, 'quiz', 'close attempt',
                           "review.php?attempt=$attempt->id",
                           "$quiz->id", $cm->id);
    }


/// Update the quiz attempt and the overall grade for the quiz
    if ($responses || $finishattempt) {
        if (!update_record('quiz_attempts', $attempt)) {
            error('Failed to save the current quiz attempt!');
        }
        if (($attempt->attempt > 1 || $attempt->timefinish > 0) and !$attempt->preview) {
            quiz_save_best_grade($quiz);
        }
    }

    if ($finishattempt) {
        redirect('review.php?attempt='.$attempt->id);
    }

/// Get time limit if any.
    $timelimit = $quiz->timelimit * 60;

    if ($timelimit > 0) {
        $timestart = $attempt->timestart;
        if ($timestart) {
            $timesincestart = $timenow - $timestart;
            $timerstartvalue = $timelimit - $timesincestart;
        } else {
            $timerstartvalue = $timelimit;
        }
        if ($timerstartvalue <= 0) {
            $timerstartvalue = 1;
        }
        if(($timelimit + 60) <= $timesincestart) {
            // To pass it on to quiz_grade_responses
            $quiz->timesincestart = $timesincestart;
        }
    }


/// Print the quiz page ////////////////////////////////////////////////////////

/// Print the attempt number or preview heading
    if ($isteacher) {
        print_heading(get_string('previewquiz', 'quiz'));
        unset($buttonoptions);
        $buttonoptions['q'] = $quiz->id;
        $buttonoptions['forcenew'] = true;
        echo '<center>';
        print_single_button($CFG->wwwroot.'/mod/quiz/attempt.php', $buttonoptions, get_string('startagain', 'quiz'));
        echo '</center>';
    } else {
        print_heading($strattemptnum);
    }

/// Add the javascript timer in the title bar if the closing time appears close
    $secondsleft = $quiz->timeclose - time();
    if ($secondsleft > 0 and $secondsleft < 24*3600) {  // less than a day remaining
        include('jsclock.php');
    }

/// Start the form
    if($quiz->timelimit > 0) {
        // Make sure javascript is enabled for time limited quizzes
        ?>
        <script language="javascript" type="text/javascript">
        <!--
            document.write("<form name=\"responseform\" method=\"post\" action=\"attempt.php\">\n");
        // -->
        </script>
        <noscript>
        <center><p><strong><?php print_string('noscript', 'quiz'); ?></strong></p></center>
        </noscript>
        <?php
    } else {
        echo "<form name=\"responseform\" method=\"post\" action=\"attempt.php\">\n";
    }

    // Add a hidden field with the quiz id
    echo '<input type="hidden" name="q" value="' . s($quiz->id) . "\" />\n";

/// Print the navigation panel if required
    $numpages = quiz_number_of_pages($attempt->layout);
    if ($numpages > 1) {
        ?>
        <script language="javascript" type="text/javascript">
        function navigate(page) {
            document.responseform.page.value=page;
            document.responseform.submit();
        }
        </script>
        <?php
        echo '<input type="hidden" id="page" name="page" value="'.$page."\" />\n";
        quiz_print_navigation_panel($page, $numpages);
        echo "<br />\n";
    }

/// Print all the questions

    // Add a hidden field with questionids
    echo '<input type="hidden" name="questionids" value="'.$pagelist."\" />\n";

    $pagequestions = explode(',', $pagelist);
    $number = quiz_first_questionnumber($attempt->layout, $pagelist);
    foreach ($pagequestions as $i) {
        $options = quiz_get_renderoptions($quiz, $states[$i]);
        // Print the question
        if ($i > 0) {
            echo "<br />\n";
        }
        quiz_print_quiz_question($questions[$i], $states[$i], $number, $quiz, $options);
        quiz_save_question_session($questions[$i], $states[$i]);
        $number += $questions[$i]->length;
    }

/// Print the submit buttons

    $strconfirmattempt = addslashes(get_string("confirmclose", "quiz"));
    $onclick = "return confirm('$strconfirmattempt')";
    echo "<center>\n";

    echo "<input type=\"submit\" name=\"saveattempt\" value=\"".get_string("savenosubmit", "quiz")."\" />\n";
    if ($quiz->optionflags & QUIZ_ADAPTIVE) {
        echo "<input type=\"submit\" name=\"markall\" value=\"".get_string("markall", "quiz")."\" />\n";
    }
    echo "<input type=\"submit\" name=\"finishattempt\" value=\"".get_string("finishattempt", "quiz")."\" onclick=\"$onclick\" />\n";
    echo '<input type="hidden" name="timeup" value="0" />';

    echo "</center>";

    // Print the navigation panel if required
    if ($numpages > 1) {
        echo "<br />\n";
        quiz_print_navigation_panel($page, $numpages);
        echo '<br />';
    }

    // Finish the form
    echo "</form>\n";

    // If time limit is set include floating timer.
    if ($timelimit > 0) {
        require('jstimer.php');
    }

    if (!$isteacher) {
        include('attempt_close_js.php');
    }

    // Finish the page
    if (empty($popup)) {
        print_footer($course);
    }

?>
