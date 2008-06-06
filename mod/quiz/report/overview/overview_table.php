<?php  // $Id$

class quiz_report_overview_table extends table_sql {
    
    var $useridfield = 'userid';
    
    var $candelete;
    var $reporturl;
    var $displayoptions;
    
    function quiz_report_overview_table($quiz , $qmsubselect, $groupstudents,
                $students, $detailedmarks, $questions, $candelete, $reporturl, $displayoptions){
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
    }
    function build_table(){
        global $CFG;
        if ($this->rawdata) {
            // Define some things we need later to process raw data from db.
            $this->strtimeformat = get_string('strftimedatetime');
            parent::build_table();
            //end of adding data from attempts data to table / download
            //now add averages at bottom of table :
            $averagesql = "SELECT AVG(qg.grade) AS grade " .
                    "FROM {$CFG->prefix}quiz_grades qg " .
                    "WHERE quiz=".$this->quiz->id;
                    
            $this->add_separator();
            if ($this->is_downloading()){
                $namekey = 'lastname';
            } else {
                $namekey = 'fullname';
            }
            if ($this->groupstudents){
                $groupaveragesql = $averagesql." AND qg.userid IN ($this->groupstudents)";
                $groupaverage = get_record_sql($groupaveragesql);
                $groupaveragerow = array($namekey => get_string('groupavg', 'grades'),
                        'sumgrades' => round($groupaverage->grade, $this->quiz->decimalpoints),
                        'feedbacktext'=> strip_tags(quiz_report_feedback_for_grade($groupaverage->grade, $this->quiz->id)));
                if($this->detailedmarks && $this->qmsubselect) {
                    $avggradebyq = quiz_get_average_grade_for_questions($this->quiz, $this->groupstudents);
                    $groupaveragerow += quiz_format_average_grade_for_questions($avggradebyq, $this->questions, $this->quiz, $this->is_downloading());
                }
                $this->add_data_keyed($groupaveragerow);
            }
            $overallaverage = get_record_sql($averagesql." AND qg.userid IN ($this->students)");
            $overallaveragerow = array($namekey => get_string('overallaverage', 'grades'),
                        'sumgrades' => round($overallaverage->grade, $this->quiz->decimalpoints),
                        'feedbacktext'=> strip_tags(quiz_report_feedback_for_grade($overallaverage->grade, $this->quiz->id)));
            if($this->detailedmarks && $this->qmsubselect) {
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
                $strreallydel  = addslashes_js(get_string('deleteattemptcheck','quiz'));
                echo '<div id="tablecontainer">';
                echo '<form id="attemptsform" method="post" action="' . $this->reporturl->out(true) .
                        '" onsubmit="confirm(\''.$strreallydel.'\');">';
                echo $this->reporturl->hidden_params_out(array(), 0, $this->displayoptions);
                echo '<div>';
            }
        }
    }
    function wrap_html_finish(){
        if (!$this->is_downloading()) {
            // Print "Select all" etc.
            if ($this->candelete) {
                echo '<table id="commands">';
                echo '<tr><td>';
                echo '<a href="javascript:select_all_in(\'DIV\',null,\'tablecontainer\');">'.
                        get_string('selectall', 'quiz').'</a> / ';
                echo '<a href="javascript:deselect_all_in(\'DIV\',null,\'tablecontainer\');">'.
                        get_string('selectnone', 'quiz').'</a> ';
                echo '&nbsp;&nbsp;';
                echo '<input type="submit" value="'.get_string('deleteselected', 'quiz_overview').'"/>';
                echo '</td></tr></table>';
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
        global $COURSE;
        return print_user_picture($attempt->userid, $COURSE->id, $attempt->picture, false, true);
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
            return format_time($attempt->duration);
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
        if (preg_match('/^qsgrade([0-9]+)$/', $colname, $matches)){
            $questionid = $matches[1];
            $question = $this->questions[$questionid];
            $state = new object();
            $state->event = $attempt->{'qsevent'.$questionid};
            if (question_state_is_graded($state)) {
                $grade = quiz_rescale_grade($attempt->{'qsgrade'.$questionid}, $this->quiz);
            } else {
                $grade = '--';
            }
            if (!$this->is_downloading()) {
                $grade = $grade.'/'.quiz_rescale_grade($question->grade, $this->quiz);
                return link_to_popup_window('/mod/quiz/reviewquestion.php?state='.
                        $attempt->{'qsid'.$questionid}.'&amp;number='.$question->number,
                        'reviewquestion', $grade, 450, 650, get_string('reviewresponse', 'quiz'),
                        'none', true);
            } else {
                return $grade;
            }     
        } else {
            return NULL;
        }
    }
    
    function col_feedbacktext($attempt){
        if ($attempt->timefinish) {
            if (!$this->is_downloading()) {
                return quiz_report_feedback_for_grade(quiz_rescale_grade($attempt->sumgrades, $this->quiz), $this->quiz->id);
            } else {
                return strip_tags(quiz_report_feedback_for_grade(quiz_rescale_grade($attempt->sumgrades, $this->quiz), $this->quiz->id));
            }
        } else {
            return '-';
        }
    
    }
}
?>
