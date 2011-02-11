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
 * This file defines the quiz manual grading report class.
 *
 * @package quiz_grading
 * @copyright 2006 Gustav Delius
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->dirroot . '/mod/quiz/report/grading/gradingsettings_form.php');


/**
 * Quiz report to help teachers manually grade questions that need it.
 *
 * This report basically provides two screens:
 * - List question that might need manual grading (or optionally all questions).
 * - Provide an efficient UI to grade all attempts at a particular question.
 *
 * @copyright 2006 Gustav Delius
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_grading_report extends quiz_default_report {
    const DEFAULT_PAGE_SIZE = 5;
    const DEFAULT_ORDER = 'random';

    protected $viewoptions = array();
    protected $questions;
    protected $currentgroup;
    protected $users;
    protected $cm;
    protected $quiz;
    protected $context;

    function display($quiz, $cm, $course) {
        global $CFG, $DB, $OUTPUT, $PAGE;

        $this->quiz = $quiz;
        $this->cm = $cm;
        $this->course = $course;

        // Get the URL options.
        $slot = optional_param('slot', null, PARAM_INT);
        $questionid = optional_param('qid', null, PARAM_INT);
        $grade = optional_param('grade', null, PARAM_ALPHA);

        $includeauto = optional_param('includeauto', false, PARAM_BOOL);
        if (!in_array($grade, array('all', 'needsgrading', 'autograded', 'manuallygraded'))) {
            $grade = null;
        }
        $pagesize = optional_param('pagesize', self::DEFAULT_PAGE_SIZE, PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT);
        $order = optional_param('order', self::DEFAULT_ORDER, PARAM_ALPHA);

        // Assemble the options requried to reload this page.
        $optparams = array('includeauto', 'page');
        foreach ($optparams as $param) {
            if ($$param) {
                $this->viewoptions[$param] = $$param;
            }
        }
        if ($pagesize != self::DEFAULT_PAGE_SIZE) {
            $this->viewoptions['pagesize'] = $pagesize;
        }
        if ($order != self::DEFAULT_ORDER) {
            $this->viewoptions['order'] = $order;
        }

        // Check permissions
        $this->context = get_context_instance(CONTEXT_MODULE, $cm->id);
        require_capability('mod/quiz:grade', $this->context);
        $shownames = has_capability('quizreport/grading:viewstudentnames', $this->context);
        $showidnumbers = has_capability('quizreport/grading:viewidnumber', $this->context);

        // Validate order.
        if (!in_array($order, array('random', 'date', 'student', 'idnumber'))) {
            $order = self::DEFAULT_ORDER;
        } else if (!$shownames && $order == 'student') {
            $order = self::DEFAULT_ORDER;
        } else if (!$showidnumbers && $order == 'idnumber') {
            $order = self::DEFAULT_ORDER;
        }
        if ($order == 'random') {
            $page = 0;
        }

        // Get the list of questions in this quiz.
        $this->questions = quiz_report_get_significant_questions($quiz);
        if ($slot && !array_key_exists($slot, $this->questions)) {
            throw new moodle_exception('unknownquestion', 'quiz_grading');
        }

        // Process any submitted data.
        if ($data = data_submitted() && confirm_sesskey() && $this->validate_submitted_marks()) {
            $this->process_submitted_data();

            redirect($this->grade_question_url($slot, $questionid, $grade, $page + 1));
        }

        // Get the group, and the list of significant users.
        $this->currentgroup = groups_get_activity_group($this->cm, true);
        $this->users = get_users_by_capability($this->context,
                array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'), '', '', '', '',
                $this->currentgroup, '', false);

        // Start output.
        $this->print_header_and_tabs($cm, $course, $quiz, 'grading');

        // What sort of page to display?
        if (!$slot) {
            $this->display_index($includeauto);

        } else {
            $this->display_grading_interface($slot, $questionid, $grade,
                    $pagesize, $page, $shownames, $showidnumbers, $order);
        }
        return true;
    }

    protected function get_attempts_query() {
        global $DB;

        $from = "FROM {$CFG->prefix}quiz_attempts quiza";
        $where = "quiza.quiz = {$this->cm->instance} AND quiza.preview = 0 AND quiza.timefinish <> 0";

        if ($this->currentgroup) {
            $where .= ' AND quiza.userid IN (' . implode(',', array_keys($this->users)) . ')';
        }

        $sql = new stdClass;
        $sql->from = $from;
        $sql->where = $where;
        $sql->usageidcolumn = 'quiza.uniqueid';

        return $sql;
    }

    function temp() { // TODO
        {
            {
                if ($allok) {
                    echo $OUTPUT->notification(get_string('changessaved', 'quiz'), 'notifysuccess');
                } else {
                    echo $OUTPUT->notification(get_string('changessavedwitherrors', 'quiz'), 'notifysuccess');
                }
            }
        }
        $this->viewurl = new moodle_url('/mod/quiz/report.php', $viewoptions);
        /// find out current groups mode

        if ($groupmode = groups_get_activity_groupmode($this->cm)) {   // Groups are being used
            groups_print_activity_menu($this->cm, $this->viewurl->out(true, array('userid'=>0, 'attemptid'=>0)));
        }

        if(empty($this->users)) {
            if ($currentgroup){
                echo $OUTPUT->notification(get_string('nostudentsingroup'));
            } else {
                echo $OUTPUT->notification(get_string('nostudentsyet'));
            }
            return true;
        }
        $qattempts = quiz_get_total_qas_graded_and_ungraded($quiz, array_keys($gradeableqs), array_keys($this->users));
        if(empty($qattempts)) {
            echo $OUTPUT->notification(get_string('noattemptstoshow', 'quiz'));
            return true;
        }
        $qmenu = array();
        foreach ($gradeableqs as $qid => $questionformenu){
            $a= new stdClass();
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
            $menu = $OUTPUT->single_select($qurl, 'questionid', $qmenu, $questionid, array(''=>'choosedots'), 'questionid');
            echo '<div class="mdl-align">'.$menu.'</div>';
        }
        if (!$questionid){
            return true;
        }
        $a= new stdClass();
        $a->number = $question->number;
        $a->name = $question->name;
        $a->gradedattempts =$qattempts[$question->id]->gradedattempts;
        $a->totalattempts =$qattempts[$question->id]->totalattempts;
        $a->openspan ='<span class="highlightgraded">';
        $a->closespan ='</span>';
        echo $OUTPUT->heading(get_string('questiontitle', 'quiz_grading', $a));

        // our 2 different views

        // the first view allows a user to select a question and
        // displays the users who have answered the essay question
        // and all of their attempts at answering the question

        // the second prints selected attempt answer(s) with a comment
        // and grade form underneath them

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
        global $CFG, $DB, $OUTPUT;

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

        list($select, $from, $where, $params) = $this->attempts_sql($quiz->id, true, $question->id);

        list($twhere, $tparams) = $table->get_sql_where();
        if ($twhere) {
            $where .= ' AND '.$twhere; //initial bar
            $params = array_merge($params, $tparams);
        }

        // sorting of the table
        if ($sort = $table->get_sql_sort()) {
            $sort = 'ORDER BY '.$sort;  // seems like I would need to have u. or qa. infront of the ORDER BY attribues... but seems to work..
        } else {
            // my default sort rule
            $sort = 'ORDER BY u.firstname, u.lastname, qa.timefinish ASC';
        }

        // set up the pagesize
        $table->pagesize(QUIZ_REPORT_DEFAULT_PAGE_SIZE, $totalattempts);

        // get the attempts and process them
        echo '<div id="tablecontainer">';
        if ($attempts = $DB->get_records_sql($select.$from.$where.$sort, $params, $table->get_page_start(), $table->get_page_size())) {
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

                $user = clone($attempt);
                $user->id = $user->userid;
                $picture = $OUTPUT->user_picture($user, array('courseid'=>$quiz->course));

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
        }
        // print everything here
        $table->print_html();
        echo '</div>';
    }


    /**
     * Prints questions with comment and grade form underneath each question
     *
     * @return void
     * @todo Finish documenting this function
     **/
    function print_questions_and_form($quiz, $question, $userid, $attemptid, $gradeungraded, $gradenextungraded, $ungraded) {
        global $CFG, $DB, $OUTPUT;

        $context = get_context_instance(CONTEXT_MODULE, $this->cm->id);

        $questions[$question->id] = &$question;
        $usehtmleditor = can_use_html_editor();

        list($select, $from, $where, $params) = $this->attempts_sql($quiz->id, false, $question->id, $userid, $attemptid, $gradeungraded, $gradenextungraded);

        $sort = 'ORDER BY u.firstname, u.lastname, qa.attempt ASC';

        if ($gradenextungraded){
            $attempts = $DB->get_records_sql($select.$from.$where.$sort, $params, 0, QUIZ_REPORT_DEFAULT_GRADING_PAGE_SIZE);
        } else {
            $attempts = $DB->get_records_sql($select.$from.$where.$sort, $params);
        }
        if ($attempts){
            $firstattempt = current($attempts);
            $fullname = fullname($firstattempt);
            if ($gradeungraded) { // getting all ungraded attempts
                echo $OUTPUT->heading(get_string('gradingungraded','quiz_grading', $ungraded), 3);
            } else if ($gradenextungraded) { // getting next ungraded attempts
                echo $OUTPUT->heading(get_string('gradingnextungraded','quiz_grading', QUIZ_REPORT_DEFAULT_GRADING_PAGE_SIZE), 3);
            } else if ($userid){
                echo $OUTPUT->heading(get_string('gradinguser','quiz_grading', $fullname), 3);
            } else if ($attemptid){
                $a = new stdClass();
                $a->fullname = $fullname;
                $a->attempt = $firstattempt->attempt;
                echo $OUTPUT->heading(get_string('gradingattempt','quiz_grading', $a), 3);
            } else {
                echo $OUTPUT->heading(get_string('gradingall','quiz_grading', count($attempts)), 3);
            }

            // Display the form with one part for each selected attempt

            echo '<form method="post" action="report.php" class="mform" id="manualgradingform">'.
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

                $options = quiz_get_review_options($quiz, $attempt, $context);
                unset($options->questioncommentlink);
                $options->readonly = 1;

                if (question_state_is_graded($state)) {
                    $gradedclass = 'main highlightgraded';
                    $gradedstring = ' ' . get_string('graded','quiz_grading');
                } else {
                    $gradedclass = 'main';
                    $gradedstring = '';
                }
                $a = new stdClass();
                $a->fullname = fullname($attempt, true);
                $a->attempt = $attempt->attempt;

                // print the user name, attempt count, the question, and some more hidden fields
                echo '<div class="boxaligncenter" width="80%" style="clear:left;padding:15px;">';
                echo $OUTPUT->heading(get_string('gradingattempt', 'quiz_grading', $a) . $gradedstring, 3, $gradedclass);

                // Print the question, without showing any previous comment.
                $copy = $state->manualcomment;
                $state->manualcomment = '';
                $options->noeditlink = true;
                print_question($question, $state, '', $quiz, $options);

                // The print the comment and grade fields, putting back the previous comment.
                $state->manualcomment = $copy;
                question_print_comment_fields($question, $state, 'manualgrades[' . $attempt->uniqueid . ']',
                        $quiz, get_string('manualgrading', 'quiz'));

                echo '</div>';
            }
            echo '<div class="boxaligncenter"><input type="submit" value="'.get_string('savechanges').'" /></div>'.
                '</form>';
        } else {
            echo $OUTPUT->notification(get_string('noattemptstoshow', 'quiz'));
        }
    }

    function attempts_sql($quizid, $wantstateevent=false, $questionid=0, $userid=0, $attemptid=0, $gradeungraded=0, $gradenextungraded=0){
        global $CFG, $DB;
        // this sql joins the attempts table and the user table
        $select = 'SELECT qa.id AS attemptid, qa.uniqueid, qa.attempt, qa.timefinish, qa.preview,
                    u.id AS userid, u.firstname, u.lastname, u.picture, u.imagealt, u.email ';
        if ($wantstateevent && $questionid){
            $select .= ', qs.event ';
        }
        $from   = 'FROM {user} u, ' .
                '{quiz_attempts} qa ';
        $params = array();

        $from .= "LEFT JOIN {question_sessions} qns " .
                "ON (qns.attemptid = qa.uniqueid AND qns.questionid = ?) ";
        $params[] = $questionid;
        $from .=  "LEFT JOIN  {question_states} qs " .
                "ON (qs.id = qns.newest AND qs.question = ?) ";
        $params[] = $questionid;

        list($usql, $u_params) = $DB->get_in_or_equal(array_keys($this->users));
        if ($gradenextungraded || $gradeungraded) { // get ungraded attempts
            $where = "WHERE u.id $usql AND qs.event NOT IN (".QUESTION_EVENTS_GRADED.")";
            $params = array_merge($params, $u_params);
        } else if ($userid) { // get all the attempts for a specific user
            $where = 'WHERE u.id=?';
            $params[] = $userid;
        } else if ($attemptid) { // get a specific attempt
            $where = 'WHERE qa.id=? ';
            $params[] = $attemptid;
        } else { // get all user attempts
            $where  = "WHERE u.id $usql ";
            $params = array_merge($params, $u_params);
        }

        $where .= ' AND qs.event IN ('.QUESTION_EVENTS_CLOSED_OR_GRADED.')';

        $where .= ' AND u.id = qa.userid AND qa.quiz = ?';
        $params[] = $quizid;
        // ignore previews
        $where .= ' AND preview = 0 ';

        $where .= ' AND qa.timefinish != 0 ';

        return array($select, $from, $where, $params);
    }

}


