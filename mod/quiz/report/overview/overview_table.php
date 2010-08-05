<?php

class quiz_report_overview_table extends table_sql {

    var $useridfield = 'userid';

    var $candelete;
    var $reporturl;
    var $displayoptions;
    var $regradedqs = array();

    function quiz_report_overview_table($quiz , $qmsubselect, $groupstudents,
                $students, $detailedmarks, $questions, $candelete, $reporturl, $displayoptions, $context){
        parent::table_sql('mod-quiz-report-overview-report');
        $this->quiz = $quiz;
        $this->qmsubselect = $qmsubselect;
        $this->groupstudents = $groupstudents;
        $this->students = $students;
        $this->detailedmarks = $detailedmarks;
        $this->questions = $questions;
        $this->candelete = $candelete;
        $this->reporturl = $reporturl;
        $this->displayoptions = $displayoptions;
        $this->context = $context;
    }
    function build_table(){
        global $CFG, $DB;
        if ($this->rawdata) {
            // Define some things we need later to process raw data from db.
            $this->strtimeformat = str_replace(',', '', get_string('strftimedatetime'));
            parent::build_table();
            //end of adding data from attempts data to table / download
            //now add averages at bottom of table :
            $params = array($this->quiz->id);
            $averagesql = "SELECT AVG(qg.grade) AS grade " .
                    "FROM {quiz_grades} qg " .
                    "WHERE quiz=?";

            $this->add_separator();
            if ($this->is_downloading()){
                $namekey = 'lastname';
            } else {
                $namekey = 'fullname';
            }
            if ($this->groupstudents){
                list($g_usql, $g_params) = $DB->get_in_or_equal($this->groupstudents);

                $groupaveragesql = $averagesql." AND qg.userid $g_usql";
                $groupaverage = $DB->get_record_sql($groupaveragesql, array_merge($params, $g_params));
                $groupaveragerow = array($namekey => get_string('groupavg', 'grades'),
                        'sumgrades' => quiz_format_grade($this->quiz, $groupaverage->grade),
                        'feedbacktext'=> strip_tags(quiz_report_feedback_for_grade($groupaverage->grade, $this->quiz->id)));
                if($this->detailedmarks && ($this->qmsubselect || $this->quiz->attempts == 1)) {
                    $avggradebyq = quiz_get_average_grade_for_questions($this->quiz, $this->groupstudents);
                    $groupaveragerow += quiz_format_average_grade_for_questions($avggradebyq, $this->questions, $this->quiz, $this->is_downloading());
                }
                $this->add_data_keyed($groupaveragerow);
            }

            list($s_usql, $s_params) = $DB->get_in_or_equal($this->students);
            $overallaverage = $DB->get_record_sql($averagesql." AND qg.userid $s_usql", array_merge($params, $s_params));
            $overallaveragerow = array($namekey => get_string('overallaverage', 'grades'),
                        'sumgrades' => quiz_format_grade($this->quiz, $overallaverage->grade),
                        'feedbacktext'=> strip_tags(quiz_report_feedback_for_grade($overallaverage->grade, $this->quiz->id)));
            if($this->detailedmarks && ($this->qmsubselect || $this->quiz->attempts == 1)) {
                $avggradebyq = quiz_get_average_grade_for_questions($this->quiz, $this->students);
                $overallaveragerow += quiz_format_average_grade_for_questions($avggradebyq, $this->questions, $this->quiz, $this->is_downloading());
            }
            $this->add_data_keyed($overallaveragerow);
        }
    }

    function wrap_html_start(){
        if (!$this->is_downloading()) {
            if ($this->candelete) {
                // Start form
                $url = new moodle_url($this->reporturl, $this->displayoptions);
                echo '<div id="tablecontainer">';
                echo '<form id="attemptsform" method="post" action="' . $this->reporturl->out_omit_querystring() .'">';
                echo '<div style="display: none;">';
                echo html_writer::input_hidden_params($url);
                echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey())) . "\n";
                echo '</div>';
                echo '<div>';
            }
        }
    }
    function wrap_html_finish(){
        if (!$this->is_downloading()) {
            // Print "Select all" etc.
            if ($this->candelete) {
                $strreallydel  = addslashes_js(get_string('deleteattemptcheck','quiz'));
                echo '<div id="commands">';
                echo '<a href="javascript:select_all_in(\'DIV\',null,\'tablecontainer\');">'.
                        get_string('selectall', 'quiz').'</a> / ';
                echo '<a href="javascript:deselect_all_in(\'DIV\',null,\'tablecontainer\');">'.
                        get_string('selectnone', 'quiz').'</a> ';
                echo '&nbsp;&nbsp;';
                if (has_capability('mod/quiz:regrade', $this->context)){
                    echo '<input type="submit" name="regrade" value="'.get_string('regradeselected', 'quiz_overview').'"/>';
                }
                echo '<input type="submit" onclick="return confirm(\''.$strreallydel.'\');" name="delete" value="'.get_string('deleteselected', 'quiz_overview').'"/>';
                echo '</div>';
                // Close form
                echo '</div>';
                echo '</form></div>';
            }
        }
    }


    function col_checkbox($attempt){
        if ($attempt->attempt){
            return '<input type="checkbox" name="attemptid[]" value="'.$attempt->attempt.'" />';
        } else {
            return '';
        }
    }

    function col_picture($attempt){
        global $COURSE, $OUTPUT;
        $user = new object();
        $user->id = $attempt->userid;
        $user->lastname = $attempt->lastname;
        $user->firstname = $attempt->firstname;
        $user->imagealt = $attempt->imagealt;
        $user->picture = $attempt->picture;
        $user->email = $attempt->email;
        return $OUTPUT->user_picture($user);
    }


    function col_timestart($attempt){
        if ($attempt->attempt) {
            $startdate = userdate($attempt->timestart, $this->strtimeformat);
            if (!$this->is_downloading()) {
                return  '<a href="review.php?q='.$this->quiz->id.'&amp;attempt='.$attempt->attempt.'">'.$startdate.'</a>';
            } else {
                return  $startdate;
            }
        } else {
            return  '-';
        }
    }
    function col_timefinish($attempt){
        if ($attempt->attempt) {
            if ($attempt->timefinish) {
                $timefinish = userdate($attempt->timefinish, $this->strtimeformat);
                if (!$this->is_downloading()) {
                    return '<a href="review.php?q='.$this->quiz->id.'&amp;attempt='.$attempt->attempt.'">'.$timefinish.'</a>';
                } else {
                    return $timefinish;
                }
            } else {
                return  '-';
            }
        } else {
            return  '-';
        }
    }

    function col_duration($attempt){
        if ($attempt->timefinish) {
            return format_time($attempt->timefinish - $attempt->timestart);
        } elseif ($attempt->timestart) {
            return get_string('unfinished', 'quiz');
        } else {
            return '-';
        }
    }
    function col_sumgrades($attempt){
        if ($attempt->timefinish) {
            $grade = quiz_rescale_grade($attempt->sumgrades, $this->quiz);
            if (!$this->is_downloading()) {
                if (isset($this->regradedqs[$attempt->attemptuniqueid])){
                    $newsumgrade = 0;
                    $oldsumgrade = 0;
                    foreach ($this->questions as $question){
                        if (isset($this->regradedqs[$attempt->attemptuniqueid][$question->id])){
                            $newsumgrade += $this->regradedqs[$attempt->attemptuniqueid][$question->id]->newgrade;
                            $oldsumgrade += $this->regradedqs[$attempt->attemptuniqueid][$question->id]->oldgrade;
                        } else {
                            $newsumgrade += $this->gradedstatesbyattempt[$attempt->attemptuniqueid][$question->id]->grade;
                            $oldsumgrade += $this->gradedstatesbyattempt[$attempt->attemptuniqueid][$question->id]->grade;
                        }
                    }
                    $newsumgrade = quiz_rescale_grade($newsumgrade, $this->quiz);
                    $oldsumgrade = quiz_rescale_grade($oldsumgrade, $this->quiz);
                    $grade = "<del>$oldsumgrade</del><br />$newsumgrade";
                }
                $gradehtml = '<a href="review.php?q='.$this->quiz->id.'&amp;attempt='.$attempt->attempt.'">'.$grade.'</a>';
                if ($this->qmsubselect && $attempt->gradedattempt){
                    $gradehtml = '<div class="highlight">'.$gradehtml.'</div>';
                }
                return $gradehtml;
            } else {
                return $grade;
            }
        } else {
            return '-';
        }
    }

    /**
     * @param string $colname the name of the column.
     * @param object $attempt the row of data - see the SQL in display() in
     * mod/quiz/report/overview/report.php to see what fields are present,
     * and what they are called.
     * @return string the contents of the cell.
     */
    function other_cols($colname, $attempt){
        global $OUTPUT;

        if (preg_match('/^qsgrade([0-9]+)$/', $colname, $matches)){
            $questionid = $matches[1];
            $question = $this->questions[$questionid];
            if (isset($this->gradedstatesbyattempt[$attempt->attemptuniqueid][$questionid])){
                $stateforqinattempt = $this->gradedstatesbyattempt[$attempt->attemptuniqueid][$questionid];
            } else {
                $stateforqinattempt = false;
            }
            if ($stateforqinattempt && question_state_is_graded($stateforqinattempt)) {
                $grade = quiz_rescale_grade($stateforqinattempt->grade, $this->quiz, 'question');
                if (!$this->is_downloading()) {
                    if (isset($this->regradedqs[$attempt->attemptuniqueid][$questionid])){
                        $gradefromdb = $grade;
                        $newgrade = quiz_rescale_grade($this->regradedqs[$attempt->attemptuniqueid][$questionid]->newgrade, $this->quiz, 'question');
                        $oldgrade = quiz_rescale_grade($this->regradedqs[$attempt->attemptuniqueid][$questionid]->oldgrade, $this->quiz, 'question');

                        $grade = '<del>'.$oldgrade.'</del><br />'.
                                $newgrade;
                    }

                    $link = new moodle_url("/mod/quiz/reviewquestion.php?attempt=$attempt->attempt&question=$question->id");
                    $action = new popup_action('click', $link, 'reviewquestion', array('height' => 450, 'width' => 650));
                    $linktopopup = $OUTPUT->action_link($link, $grade, $action, array('title'=>get_string('reviewresponsetoq', 'quiz', $question->formattedname)));

                    if (($this->questions[$questionid]->maxgrade != 0)){
                        $fractionofgrade = $stateforqinattempt->grade
                                        / $this->questions[$questionid]->maxgrade;
                        $qclass = question_get_feedback_class($fractionofgrade);
                        $feedbackimg = question_get_feedback_image($fractionofgrade);
                        $questionclass = "que";
                        return "<span class=\"$questionclass\"><span class=\"$qclass\">".$linktopopup."</span></span>$feedbackimg";
                    } else {
                        return $linktopopup;
                    }

                } else {
                    return $grade;
                }
            } else {
                return '--';
            }
        } else {
            return NULL;
        }
    }

    function col_feedbacktext($attempt){
        if ($attempt->timefinish) {
            if (!$this->is_downloading()) {
                return quiz_report_feedback_for_grade(quiz_rescale_grade($attempt->sumgrades, $this->quiz, false), $this->quiz->id);
            } else {
                return strip_tags(quiz_report_feedback_for_grade(quiz_rescale_grade($attempt->sumgrades, $this->quiz, false), $this->quiz->id));
            }
        } else {
            return '-';
        }

    }
    function col_regraded($attempt){
        if ($attempt->regraded == '') {
            return '';
        } else if ($attempt->regraded == 0) {
            return get_string('needed', 'quiz_overview');
        } else if ($attempt->regraded == 1) {
            return get_string('done', 'quiz_overview');
        }
    }
    function query_db($pagesize, $useinitialsbar=true){
        // Add table joins so we can sort by question grade
        // unfortunately can't join all tables necessary to fetch all grades
        // to get the state for one question per attempt row we must join two tables
        // and there is a limit to how many joins you can have in one query. In MySQL it
        // is 61. This means that when having more than 29 questions the query will fail.
        // So we join just the tables needed to sort the attempts.
        if($sort = $this->get_sql_sort()) {
            if ($this->detailedmarks) {
                $this->sql->from .= ' ';
                $sortparts    = explode(',', $sort);
                $matches = array();
                foreach($sortparts as $sortpart) {
                    $sortpart = trim($sortpart);
                    if (preg_match('/^qsgrade([0-9]+)/', $sortpart, $matches)){
                        $qid = intval($matches[1]);
                        $this->sql->fields .=  ", qs$qid.grade AS qsgrade$qid, qs$qid.event AS qsevent$qid, qs$qid.id AS qsid$qid";
                        $this->sql->from .= "LEFT JOIN {question_sessions} qns$qid ON qns$qid.attemptid = qa.uniqueid AND qns$qid.questionid = :qid$qid ";
                        $this->sql->from .=  "LEFT JOIN  {question_states} qs$qid ON qs$qid.id = qns$qid.newgraded ";
                        $this->sql->params['qid'.$qid] = $qid;
                    }
                }
            } else {
                //unset any sort columns that sort on question grade as the
                //grades are not being fetched as fields
                $sess = &$this->sess;
                foreach($sess->sortby as $column => $order) {
                    if (preg_match('/^qsgrade([0-9]+)/', trim($column))){
                        unset($sess->sortby[$column]);
                    }
                }
            }
        }
        parent::query_db($pagesize, $useinitialsbar);
        //get all the attempt ids we want to display on this page
        //or to export for download.
        if (!$this->is_downloading()) {
            $attemptids = array();
            foreach ($this->rawdata as $attempt){
                if ($attempt->attemptuniqueid > 0){
                    $attemptids[] = $attempt->attemptuniqueid;
                }
            }
            $this->gradedstatesbyattempt = quiz_get_newgraded_states($attemptids, true, 'qs.id, qs.grade, qs.event, qs.question, qs.attempt');
            if (has_capability('mod/quiz:regrade', $this->context)){
                $this->regradedqs = quiz_get_regraded_qs($attemptids);
            }
        } else {
            $this->gradedstatesbyattempt = quiz_get_newgraded_states($this->sql, true, 'qs.id, qs.grade, qs.event, qs.question, qs.attempt');
            if (has_capability('mod/quiz:regrade', $this->context)){
                $this->regradedqs = quiz_get_regraded_qs($this->sql);
            }
        }
    }
}

