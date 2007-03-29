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

    // remember the current time as the time any responses were submitted
    // (so as to make sure students don't get penalized for slow processing on this page)
    $timestamp = time();

    // We treat automatically closed attempts just like normally closed attempts
    if ($timeup) {
        $finishattempt = 1;
    }

    if ($id) {
        if (! $cm = get_coursemodule_from_id('quiz', $id)) {
            error("There is no coursemodule with id $id");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }

        if (! $quiz = get_record("quiz", "id", $cm->instance)) {
            error("The quiz with id $cm->instance corresponding to this coursemodule $id is missing");
        }

    } else {
        if (! $quiz = get_record("quiz", "id", $q)) {
            error("There is no quiz with id $q");
        }
        if (! $course = get_record("course", "id", $quiz->course)) {
            error("The course with id $quiz->course that the quiz with id $q belongs to is missing");
        }
        if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
            error("The course module for the quiz with id $q is missing");
        }
    }

    require_login($course->id, false, $cm);
    $isteacher = has_capability('mod/quiz:grade', get_context_instance(CONTEXT_MODULE, $cm->id));
    
    $coursecontext = get_context_instance(CONTEXT_COURSE, $cm->course); // course context
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    
    // if no questions have been set up yet redirect to edit.php
    if (!$quiz->questions and has_capability('mod/quiz:manage', $context)) {
        redirect('edit.php?quizid=' . $quiz->id);
    }

// Get number for the next or unfinished attempt
    if(!$attemptnumber = (int)get_field_sql('SELECT MAX(attempt)+1 FROM ' .
     "{$CFG->prefix}quiz_attempts WHERE quiz = '{$quiz->id}' AND " .
     "userid = '{$USER->id}' AND timefinish > 0 AND preview != 1")) {
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
        $strupdatemodule = has_capability('moodle/course:manageactivities', $coursecontext)
                    ? update_module_button($cm->id, $course->id, get_string('modulename', 'quiz'))
                    : "";
        print_header_simple(format_string($quiz->name), "",
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a> ->
                  <a href=\"view.php?id=$cm->id\">".format_string($quiz->name)."</a> -> $strattemptnum",
                  "", "", true, $strupdatemodule);
    }

    echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

    /// Print the quiz name heading and tabs for teacher
    if (has_capability('mod/quiz:preview', $context)) {
        $currenttab = 'preview';
        include('tabs.php');
    } else {
        if ($quiz->attempts != 1) {
            print_heading(format_string($quiz->name).' - '.$strattemptnum);
        } else {
            print_heading(format_string($quiz->name));
        }
    }

/// Check availability
    if (isguestuser()) {
        print_heading(get_string('guestsno', 'quiz'));
        if (empty($popup)) {
            print_footer($course);
        }
        exit;
    }

    $numberofpreviousattempts = count_records_select('quiz_attempts', "quiz = '{$quiz->id}' AND " .
        "userid = '{$USER->id}' AND timefinish > 0 AND preview != 1");
    if ($quiz->attempts and $numberofpreviousattempts >= $quiz->attempts) {
        error(get_string('nomoreattempts', 'quiz'), "view.php?id={$cm->id}");
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

            echo "<form id=\"passwordform\" method=\"post\" action=\"attempt.php?id=$cm->id\" onclick=\"this.autocomplete='off'\">\n";
            echo '<fieldset class="invisiblefieldset">';
            print_simple_box_start("center");

            echo "<div class=\"boxaligncenter\">\n";
            print_string("requirepasswordmessage", "quiz");
            echo "<br /><br />\n";
            echo " <input name=\"quizpassword\" type=\"password\" value=\"\" alt=\"password\" />";
            echo " <input type=\"submit\" value=\"".get_string("ok")."\" />\n";
            echo "</div>\n";

            print_simple_box_end();
            echo '</fieldset>';
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

    if ($quiz->delay1 or $quiz->delay2) {
        //quiz enforced time delay
        if ($attempts = quiz_get_user_attempts($quiz->id, $USER->id)) {
            $numattempts = count($attempts);
        } else {
            $numattempts = 0;
        }
        $timenow = time();
        $lastattempt_obj = get_record_select('quiz_attempts', "quiz = $quiz->id AND attempt = $numattempts AND userid = $USER->id", 'timefinish');
        if ($lastattempt_obj) {
            $lastattempt = $lastattempt_obj->timefinish;
        }
        if ($numattempts == 1 && $quiz->delay1) {
            if ($timenow - $quiz->delay1 < $lastattempt) {
                error(get_string('timedelay', 'quiz'), 'view.php?q='.$quiz->id);
            }
        } else if($numattempts > 1 && $quiz->delay2) {
            if ($timenow - $quiz->delay2 < $lastattempt) {
                error(get_string('timedelay', 'quiz'), 'view.php?q='.$quiz->id);
            }
        }
    }

/// Load attempt or create a new attempt if there is no unfinished one

    if (has_capability('mod/quiz:preview', $context) and $forcenew) { // teacher wants a new preview
        // so we set a finish time on the current attempt (if any).
        // It will then automatically be deleted below
        set_field('quiz_attempts', 'timefinish', $timestamp, 'quiz', $quiz->id, 'userid', $USER->id);
    }

    $attempt = get_record('quiz_attempts', 'quiz', $quiz->id,
     'userid', $USER->id, 'timefinish', 0);

    $newattempt = false;
    if (!$attempt) {
        // Check if this is a preview request from a teacher
        // in which case the previous previews should be deleted
        if (has_capability('mod/quiz:preview', $context)) {
            if ($oldattempts = get_records_select('quiz_attempts', "quiz = '$quiz->id'
             AND userid = '$USER->id'")) {
                delete_records('quiz_attempts', 'quiz', $quiz->id, 'userid', $USER->id);
                delete_records('quiz_grades', 'quiz', $quiz->id, 'userid', $USER->id);
                foreach ($oldattempts as $oldattempt) {
                    // there should only be one but we loop just in case
                    delete_attempt($oldattempt->uniqueid);
                }
            }
        }
        $newattempt = true;
        // Start a new attempt and initialize the question sessions
        $attempt = quiz_create_attempt($quiz, $attemptnumber);
        // If this is an attempt by a teacher mark it as a preview
        if (has_capability('mod/quiz:preview', $context)) {
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
        if (($timestamp - $attempt->timemodified) > 600) { // 10 minutes have elapsed
             add_to_log($course->id, 'quiz', 'continue attemp', // this action used to be called 'continue attempt' but the database field has only 15 characters
                           "review.php?attempt=$attempt->id",
                           "$quiz->id", $cm->id);
        }
    }
    if (!$attempt->timestart) { // shouldn't really happen, just for robustness
        $attempt->timestart = time();
    }

/// Load all the questions and states needed by this script

    // list of questions needed by page
    $pagelist = quiz_questions_on_page($attempt->layout, $page);

    if ($newattempt) {
        $questionlist = quiz_questions_in_quiz($attempt->layout);
    } else {
        $questionlist = $pagelist;
    }

    // add all questions that are on the submitted form
    if ($questionids) {
        $questionlist .= ','.$questionids;
    }

    if (!$questionlist) {
        error(get_string('noquestionsfound', 'quiz'), 'view.php?q='.$quiz->id);
    }

    $sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
           "  FROM {$CFG->prefix}question q,".
           "       {$CFG->prefix}quiz_question_instances i".
           " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
           "   AND q.id IN ($questionlist)";

    // Load the questions
    if (!$questions = get_records_sql($sql)) {
        error(get_string('noquestionsfound', 'quiz'), 'view.php?q='.$quiz->id);
    }

    // Load the question type specific information
    if (!get_question_options($questions)) {
        error('Could not load question options');
    }

    // Restore the question sessions to their most recent states
    // creating new sessions where required
    if (!$states = get_question_states($questions, $quiz, $attempt)) {
        error('Could not restore question sessions');
    }

    // Save all the newly created states
    if ($newattempt) {
        foreach ($questions as $i => $question) {
            save_question_session($questions[$i], $states[$i]);
        }
    }

    // If the new attempt is to be based on a previous attempt copy responses over
    if ($newattempt and $attempt->attempt > 1 and $quiz->attemptonlast and !$attempt->preview) {
        // Find the previous attempt
        if (!$lastattemptid = get_field('quiz_attempts', 'uniqueid', 'quiz', $attempt->quiz, 'userid', $attempt->userid, 'attempt', $attempt->attempt-1)) {
            error('Could not find previous attempt to build on');
        }
        // For each question find the responses from the previous attempt and save them to the new session
        foreach ($questions as $i => $question) {
            // Load the last graded state for the question
            $statefields = 'n.questionid as question, s.*, n.sumpenalty';
            $sql = "SELECT $statefields".
                   "  FROM {$CFG->prefix}question_states s,".
                   "       {$CFG->prefix}question_sessions n".
                   " WHERE s.id = n.newgraded".
                   "   AND n.attemptid = '$lastattemptid'".
                   "   AND n.questionid = '$i'";
            if (!$laststate = get_record_sql($sql)) {
                // Only restore previous responses that have been graded
                continue;
            }
            // Restore the state so that the responses will be restored
            restore_question_state($questions[$i], $laststate);
            // prepare the previous responses for new processing
            $action = new stdClass;
            $action->responses = $laststate->responses;
            $action->timestamp = $laststate->timestamp;
            $action->event = QUESTION_EVENTSAVE; //emulate save of questions from all pages MDL-7631

            // Process these responses ...
            question_process_responses($questions[$i], $states[$i], $action, $quiz, $attempt);

            // Fix for Bug #5506: When each attempt is built on the last one,
            // preserve the options from any previous attempt. 
            if ( isset($laststate->options) ) {
                $states[$i]->options = $laststate->options;
            }

            // ... and save the new states
            save_question_session($questions[$i], $states[$i]);
        }
    }


/// Process form data /////////////////////////////////////////////////

    if ($responses = data_submitted() and empty($_POST['quizpassword'])) {

        // set the default event. This can be overruled by individual buttons.
        $event = (array_key_exists('markall', $responses)) ? QUESTION_EVENTSUBMIT :
         ($finishattempt ? QUESTION_EVENTCLOSE : QUESTION_EVENTSAVE);

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
        $actions = question_extract_responses($questions, $responses, $event);

        // Process each question in turn

        $questionidarray = explode(',', $questionids);
        foreach($questionidarray as $i) {
            if (!isset($actions[$i])) {
                $actions[$i]->responses = array('' => '');
                $actions[$i]->event = QUESTION_EVENTSAVE;
            }
            $actions[$i]->timestamp = $timestamp;
            question_process_responses($questions[$i], $states[$i], $actions[$i], $quiz, $attempt);
            save_question_session($questions[$i], $states[$i]);
        }

        $attempt->timemodified = $timestamp;

    // We have now finished processing form data
    }

/// Finish attempt if requested
    if ($finishattempt) {

        // Set the attempt to be finished
        $attempt->timefinish = $timestamp;

        // Find all the questions for this attempt for which the newest
        // state is not also the newest graded state
        if ($closequestions = get_records_select('question_sessions',
         "attemptid = $attempt->uniqueid AND newest != newgraded", '', 'questionid, questionid')) {

            // load all the questions
            $closequestionlist = implode(',', array_keys($closequestions));
            $sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
                   "  FROM {$CFG->prefix}question q,".
                   "       {$CFG->prefix}quiz_question_instances i".
                   " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
                   "   AND q.id IN ($closequestionlist)";
            if (!$closequestions = get_records_sql($sql)) {
                error('Questions missing');
            }

            // Load the question type specific information
            if (!get_question_options($closequestions)) {
                error('Could not load question options');
            }

            // Restore the question sessions
            if (!$closestates = get_question_states($closequestions, $quiz, $attempt)) {
                error('Could not restore question sessions');
            }

            foreach($closequestions as $key => $question) {
                $action->event = QUESTION_EVENTCLOSE;
                $action->responses = $closestates[$key]->responses;
                $action->timestamp = $closestates[$key]->timestamp;
                question_process_responses($question, $closestates[$key], $action, $quiz, $attempt);
                save_question_session($question, $closestates[$key]);
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

/// Check access to quiz page

    // check the quiz times
    if ($timestamp < $quiz->timeopen || ($quiz->timeclose and $timestamp > $quiz->timeclose)) {
        if ($isteacher) {
            notify(get_string('notavailabletostudents', 'quiz'));
        } else {
            notice(get_string('notavailable', 'quiz'), "view.php?id={$cm->id}");
        }
    }

    if ($finishattempt) {
        redirect('review.php?attempt='.$attempt->id);
    }

/// Print the quiz page ////////////////////////////////////////////////////////

/// Print the preview heading
    if (has_capability('mod/quiz:preview', $context)) {
        print_heading(get_string('previewquiz', 'quiz', format_string($quiz->name)));
        unset($buttonoptions);
        $buttonoptions['q'] = $quiz->id;
        $buttonoptions['forcenew'] = true;
        echo '<div class="boxaligncenter">';
        print_single_button($CFG->wwwroot.'/mod/quiz/attempt.php', $buttonoptions, get_string('startagain', 'quiz'));
        echo '</div>';
        if ($quiz->popup) {
            notify(get_string('popupnotice', 'quiz'));
        }
    }

/// Start the form
    if($quiz->timelimit > 0) {
        // Make sure javascript is enabled for time limited quizzes
        ?>
        <script type="text/javascript">
        //<![CDATA[
            document.write("<form id=\"responseform\" method=\"post\" action=\"attempt.php\" onclick=\"this.autocomplete='off'\">\n");
        //]]>
        </script>
        <noscript>
        <div>
        <?php print_heading(get_string('noscript', 'quiz')); ?>
        </div>
        </noscript>
        <?php
    } else {
        echo "<form id=\"responseform\" method=\"post\" action=\"attempt.php\" onclick=\"this.autocomplete='off'\">\n";
    }

    // Add a hidden field with the quiz id
    echo '<div>';
    echo '<input type="hidden" name="q" value="' . s($quiz->id) . "\" />\n";

/// Print the navigation panel if required
    $numpages = quiz_number_of_pages($attempt->layout);
    if ($numpages > 1) {
        ?>
        <script type="text/javascript">
        //<![CDATA[
        function navigate(page) {
            var ourForm = document.getElementById('responseform'); 
            ourForm.page.value=page;
            if (ourForm.onsubmit) {
                ourForm.onsubmit();
            }
            ourForm.submit();
        }
        //]]>
        </script>
        <?php
        echo '<input type="hidden" name="page" value="'.$page."\" />\n";
        quiz_print_navigation_panel($page, $numpages);
        echo "<br />\n";
    }

/// Print all the questions

    // Add a hidden field with questionids
    echo '<input type="hidden" name="questionids" value="'.$pagelist."\" />\n";

    $pagequestions = explode(',', $pagelist);
    $number = quiz_first_questionnumber($attempt->layout, $pagelist);
    foreach ($pagequestions as $i) {
        $options = quiz_get_renderoptions($quiz->review, $states[$i]);
        // Print the question
        if ($i > 0) {
            echo "<br />\n";
        }
        print_question($questions[$i], $states[$i], $number, $quiz, $options);
        save_question_session($questions[$i], $states[$i]);
        $number += $questions[$i]->length;
    }

/// Print the submit buttons

    $strconfirmattempt = addslashes(get_string("confirmclose", "quiz"));
    $onclick = "return confirm('$strconfirmattempt')";
    echo "<div class=\"submitbtns mdl-align\">\n";

    echo "<input type=\"submit\" name=\"saveattempt\" value=\"".get_string("savenosubmit", "quiz")."\" />\n";
    if ($quiz->optionflags & QUESTION_ADAPTIVE) {
        echo "<input type=\"submit\" name=\"markall\" value=\"".get_string("markall", "quiz")."\" />\n";
    }
    echo "<input type=\"submit\" name=\"finishattempt\" value=\"".get_string("finishattempt", "quiz")."\" onclick=\"$onclick\" />\n";
    echo '<input type="hidden" name="timeup" value="0" />';

    echo "</div>";

    // Print the navigation panel if required
    if ($numpages > 1) {
        echo "<br />\n";
        quiz_print_navigation_panel($page, $numpages);
        echo '<br />';
    }

    // Finish the form
    echo '</div>';
    echo "</form>\n";


    $secondsleft = ($quiz->timeclose ? $quiz->timeclose : 999999999999) - time();
    if ($isteacher) {
        // For teachers ignore the quiz closing time
        $secondsleft = 999999999999;
    }
    // If time limit is set include floating timer.
    // MDL-7495, no timer for users with disability
    
    if ($quiz->timelimit > 0 && !has_capability('mod/quiz:ignoretimelimits', $context)) {

        $timesincestart = time() - $attempt->timestart;
        $timerstartvalue = min($quiz->timelimit*60 - $timesincestart, $secondsleft);
        if ($timerstartvalue <= 0) {
            $timerstartvalue = 1;
        }

        require('jstimer.php');
    } else {
        // Add the javascript timer in the title bar if the closing time appears close
        if ($secondsleft > 0 and $secondsleft < 24*3600) {  // less than a day remaining
            include('jsclock.php');
        }
    }

    // Finish the page
    if (empty($popup)) {
        print_footer($course);
    }

?>
