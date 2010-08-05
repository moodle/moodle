<?php
define ('QUIZ_REPORT_RESPONSES_MAX_LEN_TO_DISPLAY', 150);

class quiz_report_responses_table extends table_sql {

    var $useridfield = 'userid';

    var $reporturl;
    var $displayoptions;

    function quiz_report_responses_table($quiz , $qmsubselect, $groupstudents,
                $students, $questions, $candelete, $reporturl, $displayoptions){
        parent::table_sql('mod-quiz-report-responses-report');
        $this->quiz = $quiz;
        $this->qmsubselect = $qmsubselect;
        $this->groupstudents = $groupstudents;
        $this->students = $students;
        $this->questions = $questions;
        $this->candelete = $candelete;
        $this->reporturl = $reporturl;
        $this->displayoptions = $displayoptions;
    }
    function build_table(){
        if ($this->rawdata) {
            $this->strtimeformat = str_replace(',', ' ', get_string('strftimedatetime'));
            parent::build_table();
        }
    }

    function wrap_html_start(){
        if (!$this->is_downloading()) {
            if ($this->candelete) {
                // Start form
                $displayurl = new moodle_url($this->reporturl, $this->displayoptions);
                $strreallydel  = addslashes_js(get_string('deleteattemptcheck','quiz'));
                echo '<div id="tablecontainer">';
                echo '<form id="attemptsform" method="post" action="' . $displayurl->out_omit_querystring() .
                        '" onsubmit="confirm(\''.$strreallydel.'\');">';
                echo '<div style="display: none;">';
                echo html_writer::input_hidden_params($displayurl);
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
                echo '<div id="commands">';
                echo '<a href="javascript:select_all_in(\'DIV\',null,\'tablecontainer\');">'.
                        get_string('selectall', 'quiz').'</a> / ';
                echo '<a href="javascript:deselect_all_in(\'DIV\',null,\'tablecontainer\');">'.
                        get_string('selectnone', 'quiz').'</a> ';
                echo '&nbsp;&nbsp;';
                echo '<input type="submit" value="'.get_string('deleteselected', 'quiz_overview').'"/>';
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
    function other_cols($colname, $attempt){
        global $QTYPES, $OUTPUT;
        static $states =array();
        if (preg_match('/^qsanswer([0-9]+)$/', $colname, $matches)){
            if ($attempt->uniqueid == 0) {
                return '-';
            }
            $questionid = $matches[1];
            if (isset($this->gradedstatesbyattempt[$attempt->uniqueid][$questionid])){
                $stateforqinattempt = $this->gradedstatesbyattempt[$attempt->uniqueid][$questionid];
            } else {
                return '-';
            }

            $question = $this->questions[$questionid];
            restore_question_state($question, $stateforqinattempt);

            if (!$this->is_downloading() || $this->is_downloading() == 'xhtml'){
                $formathtml = true;
            } else {
                $formathtml = false;
            }

            $summary =  $QTYPES[$question->qtype]->response_summary($question, $stateforqinattempt,
                                                QUIZ_REPORT_RESPONSES_MAX_LEN_TO_DISPLAY, $formathtml);
            if (!$this->is_downloading()) {
                if ($summary){
                    $link = new moodle_url("/mod/quiz/reviewquestion.php?attempt=$attempt->attempt&question=$question->id");
                    $action = new popup_action('click', $link, 'reviewquestion', array('height' => 450, 'width' => 650));
                    $summary = $OUTPUT->action_link($link, $summary, $action, array('title'=>$question->formattedname));

                    if (question_state_is_graded($stateforqinattempt)
                                && ($question->maxgrade > 0)){
                        $grade = $stateforqinattempt->grade
                                        / $question->maxgrade;
                        $qclass = question_get_feedback_class($grade);
                        $feedbackimg = question_get_feedback_image($grade);
                        $questionclass = "que";
                        return "<span class=\"$questionclass\"><span class=\"$qclass\">".$summary."</span></span>$feedbackimg";
                    } else {
                        return $summary;
                    }
                } else {
                    return '';
                }

            } else {
                return $summary;
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

    function query_db($pagesize, $useinitialsbar=true){
        // Add table joins so we can sort by question answer
        // unfortunately can't join all tables necessary to fetch all answers
        // to get the state for one question per attempt row we must join two tables
        // and there is a limit to how many joins you can have in one query. In MySQL it
        // is 61. This means that when having more than 29 questions the query will fail.
        // So we join just the tables needed to sort the attempts.
        if($sort = $this->get_sql_sort()) {
                $this->sql->from .= ' ';
                $sortparts    = explode(',', $sort);
                $matches = array();
                foreach($sortparts as $sortpart) {
                    $sortpart = trim($sortpart);
                    if (preg_match('/^qsanswer([0-9]+)/', $sortpart, $matches)){
                        $qid = intval($matches[1]);
                        $this->sql->fields .=  ", qs$qid.grade AS qsgrade$qid, qs$qid.answer AS qsanswer$qid, qs$qid.event AS qsevent$qid, qs$qid.id AS qsid$qid";
                        $this->sql->from .= "LEFT JOIN {question_sessions} qns$qid ON qns$qid.attemptid = qa.uniqueid AND qns$qid.questionid = :qid$qid ";
                        $this->sql->from .=  "LEFT JOIN  {question_states} qs$qid ON qs$qid.id = qns$qid.newgraded ";
                        $this->sql->params['qid'.$qid] = $qid;
                    }
                }
        }
        parent::query_db($pagesize, $useinitialsbar);
        $qsfields = 'qs.id, qs.grade, qs.event, qs.question, qs.answer, qs.attempt';
        if (!$this->is_downloading()) {
            $attemptids = array();
            foreach ($this->rawdata as $attempt){
                if ($attempt->uniqueid > 0){
                    $attemptids[] = $attempt->uniqueid;
                }
            }
            $this->gradedstatesbyattempt = quiz_get_newgraded_states($attemptids, true, $qsfields);
        } else {
            $this->gradedstatesbyattempt = quiz_get_newgraded_states($this->sql, true, $qsfields);
        }
    }
}

