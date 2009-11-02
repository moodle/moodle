<?php  // $Id$
/**
 * Quiz report to help teachers manually grade quiz questions that need it.
 *
 * @package quiz
 * @subpackage reports
 */

// Flow of the file:
//     Get variables, run essential queries
//     Check for post data submitted.  If exists, then process data (the data is the grades and comments for essay questions)
//     Check for userid, attemptid, or gradeall and for questionid.  If found, print out the appropriate essay question attempts
//     Switch:
//         first case: print out all essay questions in quiz and the number of ungraded attempts
//         second case: print out all users and their attempts for a specific essay question

require_once($CFG->dirroot . "/mod/quiz/editlib.php");
require_once($CFG->libdir . '/tablelib.php');

/**
 * Quiz report to help teachers manually grade quiz questions that need it.
 *
 * @package quiz
 * @subpackage reports
 */
class quiz_report extends quiz_default_report {
    /**
     * Displays the report.
     */
    function display($quiz, $cm, $course) {
        global $CFG, $QTYPES;

        $viewoptions = array('mode'=>'grading', 'q'=>$quiz->id);

        if ($questionid = optional_param('questionid', 0, PARAM_INT)){
            $viewoptions += array('questionid'=>$questionid);
        }

        // grade question specific parameters
        $gradeungraded  = optional_param('gradeungraded', 0, PARAM_INT);

        if ($userid    = optional_param('userid', 0, PARAM_INT)){
            $viewoptions += array('userid'=>$userid);
        }
        if ($attemptid = optional_param('attemptid', 0, PARAM_INT)){
            $viewoptions += array('attemptid'=>$attemptid);
        }
        if ($gradeall  = optional_param('gradeall', 0, PARAM_INT)){
            $viewoptions += array('gradeall'=> $gradeall);
        }
        if ($gradeungraded  = optional_param('gradeungraded', 0, PARAM_INT)){
            $viewoptions += array('gradeungraded'=> $gradeungraded);
        }
        if ($gradenextungraded  = optional_param('gradenextungraded', 0, PARAM_INT)){
            $viewoptions += array('gradenextungraded'=> $gradenextungraded);
        }


        $this->cm = $cm;

        $this->print_header_and_tabs($cm, $course, $quiz, $reportmode="grading");

        // Check permissions
        $this->context = get_context_instance(CONTEXT_MODULE, $cm->id);
        if (!has_capability('mod/quiz:grade', $this->context)) {
            notify(get_string('gradingnotallowed', 'quiz_grading'));
            return true;
        }

        $gradeableqs = quiz_report_load_questions($quiz);
        $questionsinuse = implode(',', array_keys($gradeableqs));
        foreach ($gradeableqs as $qid => $question){
            if (!$QTYPES[$question->qtype]->is_question_manual_graded($question, $questionsinuse)){
                unset($gradeableqs[$qid]);
            }
        }

        if (empty($gradeableqs)) {
            print_heading(get_string('noessayquestionsfound', 'quiz'));
            return true;
        } else if (count($gradeableqs)==1){
            $questionid = array_shift(array_keys($gradeableqs));
        }

        $currentgroup = groups_get_activity_group($this->cm, true);
        $this->users = get_users_by_capability($this->context, array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'),'','','','',$currentgroup,'',false);
        $this->userids = implode(',', array_keys($this->users));


        if (!empty($questionid)) {
            if (!isset($gradeableqs[$questionid])){
                error("Gradeable question with id $questionid not found");
            } else {
                $question =& $gradeableqs[$questionid];
            }
            $question->maxgrade = get_field('quiz_question_instances', 'grade', 'quiz', $quiz->id, 'question', $question->id);

            // Some of the questions code is optimised to work with several questions
            // at once so it wants the question to be in an array. The array key
            // must be the question id.
            $key = $question->id;
            $questions[$key] = &$question;

            // We need to add additional questiontype specific information to
            // the question objects.
            if (!get_question_options($questions)) {
                error("Unable to load questiontype specific question information");
            }
            // This will have extended the question object so that it now holds
            // all the information about the questions that may be needed later.
        }

        add_to_log($course->id, "quiz", "manualgrading", "report.php?mode=grading&amp;q=$quiz->id", "$quiz->id", "$cm->id");

        echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

        if ($data = data_submitted()) {  // post data submitted, process it
            require_sesskey();

            // now go through all of the responses and save them.
            $allok = true;
            foreach($data->manualgrades as $uniqueid => $response) {
                // get our attempt
                $uniqueid = clean_param($uniqueid, PARAM_INT);
                if (!$attempt = get_record_sql("SELECT * FROM {$CFG->prefix}quiz_attempts " .
                                "WHERE uniqueid = $uniqueid AND " .
                                "userid IN ($this->userids) AND " .
                                "quiz=".$quiz->id)){
                    error('No such attempt ID exists');
                }

                // Load the state for this attempt (The questions array was created earlier)
                $states = get_question_states($questions, $quiz, $attempt);
                // The $states array is indexed by question id but because we are dealing
                // with only one question there is only one entry in this array
                $state = &$states[$question->id];

                // the following will update the state and attempt
                $error = question_process_comment($question, $state, $attempt, $response['comment'], $response['grade']);
                if (is_string($error)) {
                    notify($error);
                    $allok = false;
                } else if ($state->changed) {
                    // If the state has changed save it and update the quiz grade
                    save_question_session($question, $state);
                    quiz_save_best_grade($quiz, $attempt->userid);
                }
            }

            if ($allok) {
                notify(get_string('changessaved', 'quiz'), 'notifysuccess');
            } else {
                notify(get_string('changessavedwitherrors', 'quiz'), 'notifysuccess');
            }
        }
        $this->viewurl = new moodle_url($CFG->wwwroot.'/mod/quiz/report.php', $viewoptions); 
        /// find out current groups mode

        if ($groupmode = groups_get_activity_groupmode($this->cm)) {   // Groups are being used
            groups_print_activity_menu($this->cm, $this->viewurl->out(false, array('userid'=>0, 'attemptid'=>0)));
        }

        echo '<div class="quizattemptcounts">' . quiz_num_attempt_summary($quiz, $cm, true, $currentgroup) . '</div>';

        if(empty($this->users)) {
            if ($currentgroup){
                notify(get_string('nostudentsingroup'));
            } else {
                notify(get_string('nostudentsyet'));
            }
            return true;
        }
        $gradeablequestionids = implode(',',array_keys($gradeableqs));
        $qattempts = quiz_get_total_qas_graded_and_ungraded($quiz, $gradeablequestionids, $this->userids);
        if(empty($qattempts)) {
            notify(get_string('noattemptstoshow', 'quiz'));
            return true;
        }
        $qmenu = array();
        foreach ($gradeableqs as $qid => $questionformenu){
            $a= new object();
            $a->number = $gradeableqs[$qid]->number;
            $a->name = $gradeableqs[$qid]->name;
            $a->gradedattempts =$qattempts[$qid]->gradedattempts;
            $a->totalattempts =$qattempts[$qid]->totalattempts;
            $a->openspan ='';
            $a->closespan ='';
            $qmenu[$qid]= get_string('questiontitle', 'quiz_grading', $a);
        }
        if (count($gradeableqs)!=1){
            $qurl = fullclone($this->viewurl);
            $qurl->remove_params('questionid', 'attemptid', 'gradeall', 'gradeungraded', 'gradenextungraded');
            $menu = popup_form(($qurl->out()).'&amp;questionid=',$qmenu, 'questionid', $questionid, 'choose', '', '', true);
            echo '<div class="mdl-align">'.$menu.'</div>';
        }
        if (!$questionid){
            return true;
        }
        $a= new object();
        $a->number = $question->number;
        $a->name = $question->name;
        $a->gradedattempts =$qattempts[$question->id]->gradedattempts;
        $a->totalattempts =$qattempts[$question->id]->totalattempts;
        $a->openspan ='<span class="highlightgraded">';
        $a->closespan ='</span>';
        print_heading(get_string('questiontitle', 'quiz_grading', $a));

        // our 3 different views
        // the first one displays all of the manually graded questions in the quiz
        // with the number of ungraded attempts for each question

        // the second view displays the users who have answered the essay question
        // and all of their attempts at answering the question

        // the third prints the question with a comment
        // and grade form underneath it

        $ungraded = $qattempts[$questionid]->totalattempts- $qattempts[$questionid]->gradedattempts;
        if ($gradenextungraded ||$gradeungraded || $gradeall || $userid || $attemptid){
            $this->print_questions_and_form($quiz, $question, $userid, $attemptid, $gradeungraded, $gradenextungraded, $ungraded);
        } else {
            $this->view_question($quiz, $question, $qattempts[$questionid]->totalattempts, $ungraded);
        }
        return true;
    }

    /**
     * Prints a table with users and their attempts
     *
     * @return void
     * @todo Add current grade to the table
     *       Finnish documenting
     **/
    function view_question($quiz, $question, $totalattempts, $ungraded) {
        global $CFG;


        $usercount = count($this->users);

        // set up table
        $tablecolumns = array('picture', 'fullname', 'timefinish', 'grade');
        $tableheaders = array('', get_string('name'), get_string("completedon", "quiz"), '');

        $table = new flexible_table('mod-quiz-report-grading');

        $table->define_columns($tablecolumns);
        $table->define_headers($tableheaders);
        $table->define_baseurl($this->viewurl->out());

        $table->sortable(true);
        $table->initialbars($usercount>20);  // will show initialbars if there are more than 20 users
        $table->pageable(true);
        $table->collapsible(true);

        $table->column_suppress('fullname');
        $table->column_suppress('picture');
        $table->column_suppress('grade');

        $table->column_class('picture', 'picture');

        // attributes in the table tag
        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('id', 'attempts');
        $table->set_attribute('class', 'generaltable generalbox');
        $table->set_attribute('align', 'center');
        //$table->set_attribute('width', '50%');

        // get it ready!
        $table->setup();

        list($select, $from, $where) = $this->attempts_sql($quiz->id, true, $question->id);

        if($table->get_sql_where()) { // forgot what this does
            $where .= 'AND '.$table->get_sql_where();
        }

        // sorting of the table
        if($sort = $table->get_sql_sort()) {
            $sort = 'ORDER BY '.$sort;  // seems like I would need to have u. or qa. infront of the ORDER BY attribues... but seems to work..
        } else {
            // my default sort rule
            $sort = 'ORDER BY u.firstname, u.lastname, qa.timefinish ASC';
        }

        // set up the pagesize
        $table->pagesize(QUIZ_REPORT_DEFAULT_PAGE_SIZE, $totalattempts);

        // get the attempts and process them
        if ($attempts = get_records_sql($select.$from.$where.$sort,$table->get_page_start(), $table->get_page_size())) {
            // grade all link
            $links = "<strong><a href=\"report.php?mode=grading&amp;gradeall=1&amp;q=$quiz->id&amp;questionid=$question->id\">".get_string('gradeall', 'quiz_grading', $totalattempts).'</a></strong>';
            if ($ungraded>0){
                $links .="<br /><strong><a href=\"report.php?mode=grading&amp;gradeungraded=1&amp;q=$quiz->id&amp;questionid=$question->id\">".get_string('gradeungraded', 'quiz_grading', $ungraded).'</a></strong>';
                if ($ungraded>QUIZ_REPORT_DEFAULT_GRADING_PAGE_SIZE){
                    $links .="<br /><strong><a href=\"report.php?mode=grading&amp;gradenextungraded=1&amp;q=$quiz->id&amp;questionid=$question->id\">".get_string('gradenextungraded', 'quiz_grading', QUIZ_REPORT_DEFAULT_GRADING_PAGE_SIZE).'</a></strong>';
                }
            }
            $table->add_data_keyed(array('grade'=> $links));
            $table->add_separator();
            foreach($attempts as $attempt) {

                $picture = print_user_picture($attempt->userid, $quiz->course, $attempt->picture, false, true);

                // link to student profile
                $userlink = "<a href=\"$CFG->wwwroot/user/view.php?id=$attempt->userid&amp;course=$quiz->course\">".
                            fullname($attempt, true).'</a>';

                $gradedclass = question_state_is_graded($attempt)?' class="highlightgraded" ':'';
                $gradedstring = question_state_is_graded($attempt)?(' '.get_string('graded','quiz_grading')):'';

                // link for the attempt
                $attemptlink = "<a {$gradedclass}href=\"report.php?mode=grading&amp;q=$quiz->id&amp;questionid=$question->id&amp;attemptid=$attempt->attemptid\">".
                        userdate($attempt->timefinish, get_string('strftimedatetime')).
                        $gradedstring.'</a>';

                // grade all attempts for this user
                $gradelink = "<a href=\"report.php?mode=grading&amp;q=$quiz->id&amp;questionid=$question->id&amp;userid=$attempt->userid\">".
                        get_string('grade').'</a>';

                $table->add_data( array($picture, $userlink, $attemptlink, $gradelink) );
            }
            $table->add_separator();
            $table->add_data_keyed(array('grade'=> $links));
            // print everything here
            echo '<div id="tablecontainer">';
            $table->print_html();
            echo '</div>';
        } else {
            notify(get_string('noattemptstoshow', 'quiz'));
        }
    }


    /**
     * Prints questions with comment and grade form underneath each question
     *
     * @return void
     * @todo Finish documenting this function
     **/
    function print_questions_and_form($quiz, $question, $userid, $attemptid, $gradeungraded, $gradenextungraded, $ungraded) {
        global $CFG;

        // TODO get the context, and put in proper roles an permissions checks.
        $context = NULL;

        $questions[$question->id] = &$question;
        $usehtmleditor = can_use_richtext_editor();

        list($select, $from, $where) = $this->attempts_sql($quiz->id, false, $question->id, $userid, $attemptid, $gradeungraded, $gradenextungraded);

        $sort = 'ORDER BY u.firstname, u.lastname, qa.attempt ASC';

        if ($gradenextungraded){
            $attempts = get_records_sql($select.$from.$where.$sort, 0, QUIZ_REPORT_DEFAULT_GRADING_PAGE_SIZE);
        } else {
            $attempts = get_records_sql($select.$from.$where.$sort);
        }
        if ($attempts){
            $firstattempt = current($attempts);
            $fullname = fullname($firstattempt);
            if ($gradeungraded) { // getting all ungraded attempts
                print_heading(get_string('gradingungraded','quiz_grading', $ungraded), '', 3);
            } else if ($gradenextungraded) { // getting next ungraded attempts
                print_heading(get_string('gradingnextungraded','quiz_grading', QUIZ_REPORT_DEFAULT_GRADING_PAGE_SIZE), '', 3);
            } else if ($userid){
                print_heading(get_string('gradinguser','quiz_grading', $fullname), '', 3);
            } else if ($attemptid){
                $a = new object();
                $a->fullname = $fullname;
                $a->attempt = $firstattempt->attempt;
                print_heading(get_string('gradingattempt','quiz_grading', $a), '', 3);
            } else {
                print_heading(get_string('gradingall','quiz_grading', count($attempts)), '', 3);
            }

            // Display the form with one part for each selected attempt

            echo '<form method="post" action="report.php">'.
                '<input type="hidden" name="mode" value="grading" />'.
                '<input type="hidden" name="q" value="'.$quiz->id.'" />'.
                '<input type="hidden" name="sesskey" value="'.sesskey().'" />'.
                '<input type="hidden" name="questionid" value="'.$question->id.'" />';

            foreach ($attempts as $attempt) {

                // Load the state for this attempt (The questions array was created earlier)
                $states = get_question_states($questions, $quiz, $attempt);
                // The $states array is indexed by question id but because we are dealing
                // with only one question there is only one entry in this array
                $state = &$states[$question->id];

                $options = quiz_get_reviewoptions($quiz, $attempt, $context);
                unset($options->questioncommentlink);
                $options->noeditlink = true;
                $copy = $state->manualcomment;
                $state->manualcomment = '';

                $options->readonly = 1;

                $gradedclass = question_state_is_graded($state)?' class="highlightgraded" ':'';
                $gradedstring = question_state_is_graded($state)?(' '.get_string('graded','quiz_grading')):'';
                $a = new object();
                $a->fullname = fullname($attempt, true);
                $a->attempt = $attempt->attempt;

                // print the user name, attempt count, the question, and some more hidden fields
                echo '<div class="boxaligncenter" width="80%" style="clear:left;padding:15px;">';
                echo "<span$gradedclass>".get_string('gradingattempt','quiz_grading', $a);
                echo $gradedstring."</span>";

                print_question($question, $state, '', $quiz, $options);

                $prefix = "manualgrades[$attempt->uniqueid]";
                if (!question_state_is_graded($state)) {
                    $grade = '';
                } else {
                    $grade = round($state->last_graded->grade, 3);
                }
                $state->manualcomment = $copy;

                include($CFG->dirroot . '/question/comment.html');

                echo '</div>';
            }
            echo '<div class="boxaligncenter"><input type="submit" value="'.get_string('savechanges').'" /></div>'.
                '</form>';

            if ($usehtmleditor) {
                use_html_editor();
            }
        } else {
            notify(get_string('noattemptstoshow', 'quiz'));
        }
    }

    function attempts_sql($quizid, $wantstateevent=false, $questionid=0, $userid=0, $attemptid=0, $gradeungraded=0, $gradenextungraded=0){
        global $CFG;
        // this sql joins the attempts table and the user table
        $select = 'SELECT qa.id AS attemptid, qa.uniqueid, qa.attempt, qa.timefinish, qa.preview,
                    u.id AS userid, u.firstname, u.lastname, u.picture ';
        if ($wantstateevent && $questionid){
            $select .= ', qs.event ';
        }
        $from   = 'FROM '.$CFG->prefix.'user u, ' .
                $CFG->prefix.'quiz_attempts qa ';
        if (($wantstateevent|| $gradenextungraded || $gradeungraded) && $questionid){
            $from .= "LEFT JOIN {$CFG->prefix}question_sessions qns " .
                    "ON (qns.attemptid = qa.uniqueid AND qns.questionid = $questionid) ";
            $from .=  "LEFT JOIN  {$CFG->prefix}question_states qs " .
                    "ON (qs.id = qns.newgraded AND qs.question = $questionid) ";
        }
        if ($gradenextungraded || $gradeungraded) { // get ungraded attempts
            $where = 'WHERE u.id IN ('.$this->userids.') AND qs.event NOT IN ('.QUESTION_EVENTS_GRADED.') ';
        } else if ($userid) { // get all the attempts for a specific user
            $where = 'WHERE u.id='.$userid.' ';
        } else if ($attemptid) { // get a specific attempt
            $where = 'WHERE qa.id='.$attemptid.' ';
        } else { // get all user attempts
            $where  = 'WHERE u.id IN ('.$this->userids.') ';
        }

        $where .= ' AND u.id = qa.userid AND qa.quiz = '.$quizid;
        // ignore previews
        $where .= ' AND preview = 0 ';

        $where .= ' AND qa.timefinish != 0 ';

        return array($select, $from, $where);
    }

}

?>
