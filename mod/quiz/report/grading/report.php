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
        $gradeall  = optional_param('gradeall', 0, PARAM_INT);
        if ($userid    = optional_param('userid', 0, PARAM_INT)){
            $viewoptions += array('userid'=>$userid);
        }
        if ($attemptid = optional_param('attemptid', 0, PARAM_INT)){
            $viewoptions += array('attemptid'=>$attemptid);
        }
        if ($attemptid || $userid){
            $gradeall  = 0;
        }
        if ($gradeall){
            $viewoptions += array('gradeall'=> $gradeall);
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
        foreach ($gradeableqs as $qid => $questionformenu){
            if (!$QTYPES[$questionformenu->qtype]->is_manual_graded()){
                unset($gradeableqs[$qid]);
            }
        }
        
        if (empty($gradeableqs)) {
            print_heading(get_string('noessayquestionsfound', 'quiz'));
            return false;
        } else if (count($gradeableqs)==1){
            $questionid = array_shift(array_keys($gradeableqs));
        }

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
            confirm_sesskey();

            // now go through all of the responses and save them.
            $allok = true;
            foreach($data->manualgrades as $uniqueid => $response) {
                // get our attempt
                if (! $attempt = get_record('quiz_attempts', 'uniqueid', $uniqueid)) {
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
        $currentgroup = groups_get_activity_group($this->cm, true);

        if ($groupmode = groups_get_activity_groupmode($this->cm)) {   // Groups are being used
            groups_print_activity_menu($this->cm, $this->viewurl->out(false, array('userid'=>0, 'attemptid'=>0)));
        }

        $this->users     = get_users_by_capability($this->context, 'mod/quiz:attempt','','','','',$currentgroup,'',false);
        $this->userids   = implode(',', array_keys($this->users));
        if(empty($this->users)) {
            print_heading(get_string("noattempts", "quiz"));
            return true;
        }
        $gradeablequestionids = implode(',',array_keys($gradeableqs));
        $qattempts = quiz_get_total_qas_graded_and_ungraded($quiz, $gradeablequestionids, $this->userids);
        if(empty($qattempts)) {
            print_heading(get_string("noattempts", "quiz"));
            return true;
        }
        $qmenu = array();
        foreach ($gradeableqs as $qid => $questionformenu){
            $a= new object();
            $a->number = $gradeableqs[$qid]->number;
            $a->name = $gradeableqs[$qid]->name;
            $a->gradedattempts =$qattempts[$qid]->gradedattempts;
            $a->totalattempts =$qattempts[$qid]->totalattempts;

            $qmenu[$qid]= get_string('questiontitle', 'quiz_grading', $a);
        }
        if (count($gradeableqs)!=1){
            $qurl = clone($this->viewurl);
            $qurl->remove_params('questionid', 'attemptid');
            $menu = popup_form(($qurl->out()).'&amp;questionid=',$qmenu, 'questionid', $questionid, 'choose', '', '', true);
            echo '<div class="mdl-align" style="clear:left;">'.$menu.'</div>';
        }
        if ($questionid){
            print_heading($qmenu[$questionid]);
        }
        // our 3 different views
        // the first one displays all of the manually graded questions in the quiz
        // with the number of ungraded attempts for each question

        // the second view displays the users who have answered the essay question
        // and all of their attempts at answering the question

        // the third prints the question with a comment
        // and grade form underneath it

        if ($gradeall || $userid || $attemptid){
            $this->print_questions_and_form($quiz, $question, $userid, $attemptid);
        } else if ($questionid){
            $this->view_question($quiz, $question, $qattempts[$questionid]->totalattempts);
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
    function view_question($quiz, $question, $totalattempts) {
        global $CFG, $db;


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

        // this sql is a join of the attempts table and the user table.  I do this so I can sort by user name and attempt number (not id)
        $select = 'SELECT '.sql_concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).' AS userattemptid, qa.id AS attemptid, qa.uniqueid, qa.attempt, qa.timefinish, u.id AS userid, u.firstname, u.lastname, u.picture ';
        $from   = 'FROM '.$CFG->prefix.'user u LEFT JOIN '.$CFG->prefix.'quiz_attempts qa ON (u.id = qa.userid AND qa.quiz = '.$quiz->id.') ';
        $where  = 'WHERE u.id IN ('.$this->userids.') ';
        $where .= 'AND '.$db->IfNull('qa.attempt', '0').' != 0 ';
        $where .= 'AND '.$db->IfNull('qa.timefinish', '0').' != 0 ';
        $where .= 'AND preview = 0 '; // ignore previews

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
            // grade all and "back" links
            $link = "<strong><a href=\"report.php?mode=grading&amp;gradeall=1&amp;q=$quiz->id&amp;questionid=$question->id\">".get_string('gradeall', 'quiz_grading', $totalattempts).'</a></strong>';
            $table->add_data_keyed(array('grade'=> $link));
            $table->add_separator();
            foreach($attempts as $attempt) {

                $picture = print_user_picture($attempt->userid, $quiz->course, $attempt->picture, false, true);

                // link to student profile
                $userlink = "<a href=\"$CFG->wwwroot/user/view.php?id=$attempt->userid&amp;course=$quiz->course\">".
                            fullname($attempt, true).'</a>';

                if (!$this->is_graded($question, $attempt)) {
                    $style = 'class="manual-ungraded"';
                } else {
                    $style = 'class="manual-graded"';
                }

                // link for the attempt
                $attemptlink = "<a $style href=\"report.php?mode=grading&amp;q=$quiz->id&amp;questionid=$question->id&amp;attemptid=$attempt->attemptid\">".
                        userdate($attempt->timefinish, get_string('strftimedatetime')).'</a>';

                // grade all attempts for this user
                $gradelink = "<a href=\"report.php?mode=grading&amp;q=$quiz->id&amp;questionid=$question->id&amp;userid=$attempt->userid\">".
                        get_string('grade').'</a>';

                $table->add_data( array($picture, $userlink, $attemptlink, $gradelink) );
            }
            $table->add_separator();
            $table->add_data_keyed(array('grade'=> $link));
        }


        // print everything here
        echo '<div id="tablecontainer">';
        $table->print_html();
        echo '</div>';
    }

    /**
     * Checks to see if a question in a particular attempt is graded
     *
     * @return boolean
     * @todo Finnish documenting this function
     **/
    function is_graded($question, $attempt) {
        global $CFG;

        if (!$state = get_record_sql("SELECT state.id, state.event FROM
                                        {$CFG->prefix}question_states state, {$CFG->prefix}question_sessions sess
                                        WHERE sess.newest = state.id AND
                                        sess.attemptid = $attempt->uniqueid AND
                                        sess.questionid = $question->id")) {
            error('Could not find question state');
        }

        return question_state_is_graded($state);
    }

    /**
     * Prints questions with comment and grade form underneath each question
     *
     * @return void
     * @todo Finish documenting this function
     **/
    function print_questions_and_form($quiz, $question, $userid, $attemptid) {
        global $CFG, $db;


        // TODO get the context, and put in proper roles an permissions checks.
        $context = NULL;

        $questions[$question->id] = &$question;
        $usehtmleditor = can_use_richtext_editor();

        // this sql joins the attempts table and the user table
        $select = 'SELECT '.sql_concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).' AS userattemptid,
                    qa.id AS attemptid, qa.uniqueid, qa.attempt, qa.timefinish, qa.preview,
                    u.id AS userid, u.firstname, u.lastname, u.picture ';
        $from   = 'FROM '.$CFG->prefix.'user u LEFT JOIN '.$CFG->prefix.'quiz_attempts qa ON (u.id = qa.userid AND qa.quiz = '.$quiz->id.') ';

        if ($userid) { // get all the attempts for a specific user
            $where = 'WHERE u.id='.$userid.' ';
        } else if ($attemptid) { // get a specific attempt
            $where = 'WHERE qa.id='.$attemptid.' ';
        } else { // get all user attempts
            $where  = 'WHERE u.id IN ('.$this->userids.') ';
        }

        // ignore previews
        $where .= ' AND preview = 0 ';

        $where .= 'AND '.$db->IfNull('qa.attempt', '0').' != 0 ';
        $where .= 'AND '.$db->IfNull('qa.timefinish', '0').' != 0 ';
        $sort = 'ORDER BY u.firstname, u.lastname, qa.attempt ASC';
        $attempts = get_records_sql($select.$from.$where.$sort);

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
            $copy = $state->manualcomment;
            $state->manualcomment = '';

            $options->readonly = 1;

            // print the user name, attempt count, the question, and some more hidden fields
            echo '<div class="boxaligncenter" width="80%" style="clear:left;padding:15px;">'.
                fullname($attempt, true).': '.
                get_string('attempt', 'quiz').$attempt->attempt;

            print_question($question, $state, '', $quiz, $options);

            $prefix         = "manualgrades[$attempt->uniqueid]";
            $grade          = round($state->last_graded->grade, 3);
            $state->manualcomment = $copy;

            include($CFG->dirroot . '/question/comment.html');

            echo '</div>';
        }
        echo '<div class="boxaligncenter"><input type="submit" value="'.get_string('savechanges').'" /></div>'.
            '</form>';

        if ($usehtmleditor) {
            use_html_editor();
        }
    }

}

?>
