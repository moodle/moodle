<?php
/**
 * This script lists student attempts
 *
 * @version $Id$
 * @author Martin Dougiamas, Tim Hunt and others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 *//** */

require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->dirroot.'/mod/quiz/report/overview/overviewsettings_form.php');

class quiz_report extends quiz_default_report {

    /**
     * Display the report.
     */
    function display($quiz, $cm, $course) {
        global $CFG, $db;
        $this->quiz = $quiz;
        // Define some strings
        $strreallydel  = addslashes(get_string('deleteattemptcheck','quiz'));
        $this->strtimeformat = get_string('strftimedatetime');

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        // Only print headers if not asked to download data
        if (!$download = optional_param('download', NULL)) {
            $this->print_header_and_tabs($cm, $course, $this->quiz, "overview");
        }

        if($attemptids = optional_param('attemptid', array(), PARAM_INT)) {
            //attempts need to be deleted
            require_capability('mod/quiz:deleteattempts', $context);
            $attemptids = optional_param('attemptid', array(), PARAM_INT);
            foreach($attemptids as $attemptid) {
                add_to_log($course->id, 'quiz', 'delete attempt', 'report.php?id=' . $cm->id,
                        $attemptid, $cm->id);
                quiz_delete_attempt($attemptid, $this->quiz);
            }
            //No need for a redirect, any attemptids that do not exist are ignored.
            //So no problem if the user refreshes and tries to delete the same attempts
            //twice.
        }

        // Work out some display options - whether there is feedback, and whether scores should be shown.
        $hasfeedback = quiz_has_feedback($this->quiz->id) && $this->quiz->grade > 1.e-7 && $this->quiz->sumgrades > 1.e-7;
        $fakeattempt = new stdClass();
        $fakeattempt->preview = false;
        $fakeattempt->timefinish = $this->quiz->timeopen;
        $reviewoptions = quiz_get_reviewoptions($this->quiz, $fakeattempt, $context);
        $showgrades = $this->quiz->grade && $this->quiz->sumgrades && $reviewoptions->scores;

        $pageoptions = array();
        $pageoptions['id'] = $cm->id;
        $pageoptions['q'] = $this->quiz->id;
        $pageoptions['mode'] = 'overview';

        $reporturl = new moodle_url($CFG->wwwroot.'/mod/quiz/report.php', $pageoptions);
        $this->qmsubselect = quiz_report_qm_filter_subselect($this->quiz);
        $mform = new mod_quiz_report_overview_settings($reporturl, array('qmsubselect'=> $this->qmsubselect, 'quiz'=>$this->quiz));
        if ($fromform = $mform->get_data()){
            $attemptsmode = $fromform->attemptsmode;
            if ($this->qmsubselect){
                //control is not on the form if
                //the grading method is not set 
                //to grade one attempt per user eg. for average attempt grade.
                $qmfilter = $fromform->qmfilter;
            } else {
                $qmfilter = 0;
            }
            set_user_preference('quiz_report_overview_detailedmarks', $fromform->detailedmarks);
            set_user_preference('quiz_report_pagesize', $fromform->pagesize);
            $detailedmarks = $fromform->detailedmarks;
            $pagesize = $fromform->pagesize;
        } else {
            $qmfilter = optional_param('qmfilter', 0, PARAM_INT);
            $attemptsmode = optional_param('attemptsmode', QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH, PARAM_INT);
            $detailedmarks = get_user_preferences('quiz_report_overview_detailedmarks', 1);
            $pagesize = get_user_preferences('quiz_report_pagesize', 0);
        }
        if (!$reviewoptions->scores) {
            $detailedmarks = 0;
        }
        if ($pagesize < 1) {
            $pagesize = QUIZ_REPORT_DEFAULT_PAGE_SIZE;
        }
        // We only want to show the checkbox to delete attempts
        // if the user has permissions and if the report mode is showing attempts.
        $candelete = has_capability('mod/quiz:deleteattempts', $context) 
                && ($attemptsmode!= QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH_NO);


        $displayoptions = array();
        $displayoptions['attemptsmode'] = $attemptsmode;
        $displayoptions['qmfilter'] = $qmfilter;
        $reporturlwithdisplayoptions = new moodle_url($CFG->wwwroot.'/mod/quiz/report.php', $pageoptions + $displayoptions);

        /// find out current groups mode
        $currentgroup = groups_get_activity_group($cm, true);

        if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used
            if (!$download) {
                groups_print_activity_menu($cm, $reporturlwithdisplayoptions->out());
            }
        }

        // Print information on the number of existing attempts
        if (!$download) { //do not print notices when downloading
            if ($strattemptnum = quiz_num_attempt_summary($this->quiz, $cm, false, $currentgroup)) {
                echo '<div class="quizattemptcounts">' . $strattemptnum . '</div>';
            }
        }

        // Print information on the grading method and whether we are displaying
        // 
        if (!$download) { //do not print notices when downloading
            if ($strattempthighlight = quiz_report_highlighting_grading_method($this->quiz, $this->qmsubselect, $qmfilter)) {
                echo '<div class="quizattemptcounts">' . $strattempthighlight . '</div>';
            }
        }

        // Now check if asked download of data
        if ($download) {
            $filename = clean_filename("$course->shortname ".format_string($this->quiz->name,true));
        }

        // Define table columns
        $columns = array();
        $headers = array();
        

        if (!$download && $candelete) {
            $columns[]= 'checkbox';
            $headers[]= NULL;
        }
        
        if (!$download && $CFG->grade_report_showuserimage) {
            $columns[]= 'picture';
            $headers[]= '';
        }

        $columns[]= 'fullname';
        $headers[]= get_string('name');

        if ($CFG->grade_report_showuseridnumber) {
            $columns[]= 'idnumber';
            $headers[]= get_string('idnumber');
        }
        
        $columns[]= 'timestart';
        $headers[]= get_string('startedon', 'quiz');

        $columns[]= 'timefinish';
        $headers[]= get_string('timecompleted','quiz');

        $columns[]= 'duration';
        $headers[]= get_string('attemptduration', 'quiz');

        if ($showgrades) {
            $columns[] = 'sumgrades';
            $headers[] = get_string('grade', 'quiz').'/'.$this->quiz->grade;
        }

        if ($hasfeedback) {
            $columns[] = 'feedbacktext';
            $headers[] = get_string('feedback', 'quiz');
        }

        if ($detailedmarks) {
            // we want to display marks for all questions
            $this->questions = quiz_report_load_questions($this->quiz);
            foreach ($this->questions as $id => $question) {
                // Ignore questions of zero length
                $columns[] = 'qsgrade'.$id;
                $headers[] = '#'.$question->number;
            }
        }

        if (!$download) {
            // Set up the table

            $this->table = new flexible_table('mod-quiz-report-overview-report');

            $this->table->define_columns($columns);
            $this->table->define_headers($headers);
            $this->table->define_baseurl($reporturlwithdisplayoptions->out());

            $this->table->sortable(true);
            $this->table->collapsible(true);

            $this->table->column_suppress('picture');
            $this->table->column_suppress('fullname');
            
            $this->table->no_sorting('feedbacktext');

            $this->table->column_class('picture', 'picture');
            $this->table->column_class('fullname', 'bold');
            $this->table->column_class('sumgrades', 'bold');

            $this->table->set_attribute('cellspacing', '0');
            $this->table->set_attribute('id', 'attempts');
            $this->table->set_attribute('class', 'generaltable generalbox');

            // Start working -- this is necessary as soon as the niceties are over
            $this->table->setup();
        } else if ($download =='ODS') {
            require_once("$CFG->libdir/odslib.class.php");

            $filename .= ".ods";
            // Creating a workbook
            $this->table->workbook = new MoodleODSWorkbook("-");
            // Sending HTTP headers
            $this->table->workbook->send($filename);
            // Creating the first worksheet
            $sheettitle = get_string('reportoverview','quiz');
            $this->table->myxls =& $this->table->workbook->add_worksheet($sheettitle);
            // format types
            $this->table->format =& $this->table->workbook->add_format();
            $this->table->format->set_bold(0);
            $this->table->formatbc =& $this->table->workbook->add_format();
            $this->table->formatbc->set_bold(1);
            $this->table->formatbc->set_align('center');
            $this->table->formatb =& $this->table->workbook->add_format();
            $this->table->formatb->set_bold(1);
            $this->table->formaty =& $this->table->workbook->add_format();
            $this->table->formaty->set_bg_color('yellow');
            $this->table->formatc =& $this->table->workbook->add_format();
            $this->table->formatc->set_align('center');
            $this->table->formatr =& $this->table->workbook->add_format();
            $this->table->formatr->set_bold(1);
            $this->table->formatr->set_color('red');
            $this->table->formatr->set_align('center');
            $this->table->formatg =& $this->table->workbook->add_format();
            $this->table->formatg->set_bold(1);
            $this->table->formatg->set_color('green');
            $this->table->formatg->set_align('center');
            // Here starts workshhet headers

            $colnum = 0;
            foreach ($headers as $item) {
                $this->table->myxls->write(0,$colnum,$item,$this->table->formatbc);
                $colnum++;
            }
            $this->rownum=1;
        } else if ($download =='Excel') {
            require_once("$CFG->libdir/excellib.class.php");

            $filename .= ".xls";
            // Creating a workbook
            $this->table->workbook = new MoodleExcelWorkbook("-");
            // Sending HTTP headers
            $this->table->workbook->send($filename);
            // Creating the first worksheet
            $sheettitle = get_string('reportoverview','quiz');
            $this->table->myxls =& $this->table->workbook->add_worksheet($sheettitle);
            // format types
            $this->table->format =& $this->table->workbook->add_format();
            $this->table->format->set_bold(0);
            $this->table->formatbc =& $this->table->workbook->add_format();
            $this->table->formatbc->set_bold(1);
            $this->table->formatbc->set_align('center');
            $this->table->formatb =& $this->table->workbook->add_format();
            $this->table->formatb->set_bold(1);
            $this->table->formaty =& $this->table->workbook->add_format();
            $this->table->formaty->set_bg_color('yellow');
            $this->table->formatc =& $this->table->workbook->add_format();
            $this->table->formatc->set_align('center');
            $this->table->formatr =& $this->table->workbook->add_format();
            $this->table->formatr->set_bold(1);
            $this->table->formatr->set_color('red');
            $this->table->formatr->set_align('center');
            $this->table->formatg =& $this->table->workbook->add_format();
            $this->table->formatg->set_bold(1);
            $this->table->formatg->set_color('green');
            $this->table->formatg->set_align('center');

            $colnum = 0;
            foreach ($headers as $item) {
                $this->table->myxls->write(0,$colnum,$item,$this->table->formatbc);
                $colnum++;
            }
            $rownum=1;
        } else if ($download=='CSV') {
            $filename .= ".txt";

            header("Content-Type: application/download\n");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Expires: 0");
            header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
            header("Pragma: public");

            echo implode("\t", $headers)." \n";
        }

        $students = join(',',array_keys(get_users_by_capability($context, 'mod/quiz:attempt','','','','','','',false)));
        if (empty($currentgroup)) {
            // all users who can attempt quizzes
            $groupstudents = '';
            $allowed = $students;
        } else {
            // all users who can attempt quizzes and who are in the currently selected group
            $groupstudents = join(',',array_keys(get_users_by_capability($context, 'mod/quiz:attempt','','','','',$currentgroup,'',false)));
            $allowed = $groupstudents;
        }

        // Construct the SQL
        $select = 'SELECT '.sql_concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).' AS uniqueid, '.
            ($this->qmsubselect?$this->qmsubselect.' AS gradedattempt, ':'').
            'qa.uniqueid AS attemptuniqueid, qa.id AS attempt, u.id AS userid, u.idnumber, u.firstname, u.lastname, u.picture, '.
            'qa.sumgrades, qa.timefinish, qa.timestart, qa.timefinish - qa.timestart AS duration ';

        // This part is the same for all cases - join users and quiz_attempts tables
        $from = 'FROM '.$CFG->prefix.'user u ';
        $from .= 'LEFT JOIN '.$CFG->prefix.'quiz_attempts qa ON qa.userid = u.id AND qa.quiz = '.$this->quiz->id;
        if ($this->qmsubselect && $qmfilter){
            $from .= ' AND '.$this->qmsubselect;
        }
        switch ($attemptsmode){
            case QUIZ_REPORT_ATTEMPTS_ALL:
                // Show all attempts, including students who are no longer in the course
                $where = ' WHERE qa.id IS NOT NULL AND qa.preview = 0';
                break;
            case QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH:
                // Show only students with attempts
                $where = ' WHERE u.id IN (' .$allowed. ') AND qa.preview = 0 AND qa.id IS NOT NULL';
                break;
            case QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH_NO:
                // Show only students without attempts
                $where = ' WHERE u.id IN (' .$allowed. ') AND qa.id IS NULL';
                break;
            case QUIZ_REPORT_ATTEMPTS_ALL_STUDENTS:
                // Show all students with or without attempts
                $where = ' WHERE u.id IN (' .$allowed. ') AND (qa.preview = 0 OR qa.preview IS NULL)';
                break;
        }
        
        // Add extra sql due to sorting by question grade
        if ($detailedmarks) {
            $from .= ' ';
            // we want to display marks for all questions
            foreach (array_keys($this->questions) as $qid) {
                $select .=  ", qs$qid.grade AS qsgrade$qid, qs$qid.event AS qsevent$qid, qs$qid.id AS qsid$qid";
                $from .= "LEFT JOIN {$CFG->prefix}question_sessions qns$qid ON qns$qid.attemptid = qa.uniqueid AND qns$qid.questionid = $qid ";
                $from .=  "LEFT JOIN  {$CFG->prefix}question_states qs$qid ON qs$qid.id = qns$qid.newgraded ";
            }
            $select .= ' ';
        }

        

        $countsql = 'SELECT COUNT(DISTINCT('.sql_concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).')) '.$from.$where;

        $sort = $this->table->get_sql_sort();
        // Fix some wired sorting
        if (empty($sort)) {
            $sort = ' ORDER BY uniqueid';
        } else {
            $sort = ' ORDER BY '.$sort;
        }
         if (!$download) {
            // Add extra limits due to initials bar
            if($this->table->get_sql_where()) {
                $where .= ' AND '.$this->table->get_sql_where();
            }

            // Count the records NOW, before funky question grade sorting messes up $from
            if (!empty($countsql)) {
                $totalinitials = count_records_sql($countsql);
                if ($this->table->get_sql_where()) {
                    $countsql .= ' AND '.$this->table->get_sql_where();
                }
                $total  = count_records_sql($countsql);

            }

            $this->table->pagesize($pagesize, $total);
        }

        // Fetch the attempts
        if (!$download) {
            $attempts = get_records_sql($select.$from.$where.$sort,
                                    $this->table->get_page_start(), $this->table->get_page_size());
        } else {
            $attempts = get_records_sql($select.$from.$where.$sort);
        }
        
        // Build table rows
        if (!$download) {
            $this->table->initialbars($totalinitials>20);
        }
        if ($attempts) {
            
            $this->build_table($attempts, $download);
            //end of adding data from attempts data to table / download
            //now add averages :
            if (!$download && $attempts){
    
                $averagesql = "SELECT AVG(qg.grade) AS grade " .
                        "FROM {$CFG->prefix}quiz_grades qg " .
                        "WHERE quiz=".$this->quiz->id;
                        
                $this->table->add_separator();
                if ($groupstudents){
                    $groupaveragesql = $averagesql." AND qg.userid IN ($groupstudents)";
                    $groupaverage = get_record_sql($groupaveragesql);
                    $groupaveragerow = array('fullname' => get_string('groupavg', 'grades'),
                            'sumgrades' => round($groupaverage->grade, $this->quiz->decimalpoints),
                            'feedbacktext'=> quiz_report_feedback_for_grade($groupaverage->grade, $this->quiz->id));
                    if($detailedmarks && $this->qmsubselect) {
                        $avggradebyq = quiz_get_average_grade_for_questions($this->quiz, $groupstudents);
                        $groupaveragerow += quiz_format_average_grade_for_questions($avggradebyq, $this->questions, $this->quiz, $download);
                    }
                    $this->table->add_data_keyed($groupaveragerow);
                }
                $overallaverage = get_record_sql($averagesql." AND qg.userid IN ($students)");
                $overallaveragerow = array('fullname' => get_string('overallaverage', 'grades'),
                            'sumgrades' => round($overallaverage->grade, $this->quiz->decimalpoints),
                            'feedbacktext'=> quiz_report_feedback_for_grade($overallaverage->grade, $this->quiz->id));
                if($detailedmarks && $this->qmsubselect) {
                    $avggradebyq = quiz_get_average_grade_for_questions($this->quiz, $students);
                    $overallaveragerow += quiz_format_average_grade_for_questions($avggradebyq, $this->questions, $this->quiz, $download);
                }
                $this->table->add_data_keyed($overallaveragerow);
            }    
            if (!$download) {
                // Start form
                echo '<div id="tablecontainer">';
                echo '<form id="attemptsform" method="post" action="' . $reporturlwithdisplayoptions->out(true) .
                        '" onsubmit="confirm(\''.$strreallydel.'\');">';
                echo $reporturlwithdisplayoptions->hidden_params_out();
                echo '<div>';

                // Print table
                $this->table->print_html();

                // Print "Select all" etc.
                if (!empty($attempts) && $candelete) {
                    echo '<table id="commands">';
                    echo '<tr><td>';
                    echo '<a href="javascript:select_all_in(\'DIV\',null,\'tablecontainer\');">'.
                            get_string('selectall', 'quiz').'</a> / ';
                    echo '<a href="javascript:deselect_all_in(\'DIV\',null,\'tablecontainer\');">'.
                            get_string('selectnone', 'quiz').'</a> ';
                    echo '&nbsp;&nbsp;';
                    echo '<input type="submit" value="'.get_string('deleteselected', 'quiz_overview').'"/>';
                    echo '</td></tr></table>';
                }
                // Close form
                echo '</div>';
                echo '</form></div>';

                if (!empty($attempts)) {
                    echo '<table class="boxaligncenter"><tr>';
                    echo '<td>';
                    print_single_button($reporturl->out(true), $pageoptions + $displayoptions + array('download' => 'ODS'),
                                         get_string('downloadods'));
                    echo "</td>\n";
                    echo '<td>';
                    print_single_button($reporturl->out(true), $pageoptions + $displayoptions + array('download' => 'Excel'),
                                         get_string('downloadexcel'));
                    echo "</td>\n";
                    echo '<td>';
                    print_single_button($reporturl->out(true), $pageoptions + $displayoptions + array('download' => 'CSV'),
                                         get_string('downloadtext'));
                    echo "</td>\n";
                    echo "<td>";
                    helpbutton('overviewdownload', get_string('overviewdownload', 'quiz_overview'), 'quiz');
                    echo "</td>\n";
                    echo '</tr></table>';
                }
            }
        } else {
            if (!$download) {
                $this->table->print_html();
            }
        }
        if ($download == 'Excel' or $download == 'ODS') {
            $this->table->workbook->close();
            exit;
        } else if ($download == 'CSV') {
            exit;
        }
        if (!$download) {
            // Print display options
            $mform->set_data($displayoptions +compact('detailedmarks', 'pagesize'));
            $mform->display();
            if ($attempts){
                $imageurl = $CFG->wwwroot.'/mod/quiz/report/overview/overviewgraph.php?id='.$this->quiz->id;
                print_heading(get_string('overviewreportgraph', 'quiz_overview'));
                echo '<div class="mdl-align"><img src="'.$imageurl.'" alt="'.get_string('overviewreportgraph', 'quiz_overview').'" /></div>';
            }
        }
        return true;
    }
    
    function col_checkbox($attempt, $download){
        if ($attempt->attempt){
            return '<input type="checkbox" name="attemptid[]" value="'.$attempt->attempt.'" />';
        } else {
            return '';
        }
    }
    
    function col_picture($attempt, $download){
        global $COURSE;
        return print_user_picture($attempt->userid, $COURSE->id, $attempt->picture, false, true);
    }

    function col_fullname($attempt, $download){
        global $COURSE, $CFG;
        if (!$download){
            return '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$attempt->userid.
                    '&amp;course='.$COURSE->id.'">'.fullname($attempt).'</a>';
        } else {
            return fullname($attempt);
        }
    }    
    function col_timestart($attempt, $download){
        if ($attempt->attempt) {
            $startdate = userdate($attempt->timestart, $this->strtimeformat);
            if (!$download) {
                return  '<a href="review.php?q='.$this->quiz->id.'&amp;attempt='.$attempt->attempt.'">'.$startdate.'</a>';
            } else {
                return  $startdate;
            }
        } else {
            return  '-';
        }
    }
    function col_timefinish($attempt, $download){
        if ($attempt->attempt) {
            if ($attempt->timefinish) {
                $timefinish = userdate($attempt->timefinish, $this->strtimeformat);
                if (!$download) {
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
    
    function col_duration($attempt, $download){
        if ($attempt->timefinish) {
            return format_time($attempt->duration);
        } elseif ($attempt->timestart) {
            return get_string('unfinished', 'quiz');
        } else {
            return '-';
        }
    }
    function col_sumgrades($attempt, $download){
        if ($attempt->timefinish) {
            $grade = quiz_rescale_grade($attempt->sumgrades, $this->quiz);
            if (!$download) {
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
    function other_cols($colname, $attempt, $download){
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
            if (!$download) {
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
    
    function col_feedbacktext($attempt, $download){
        if ($attempt->timefinish) {
            return quiz_report_feedback_for_grade(quiz_rescale_grade($attempt->sumgrades, $this->quiz), $this->quiz->id);
        } else {
            return '-';
        }
    
    }
    
    function format_row($row, $download){
        $formattedrow = array();
        foreach (array_keys($this->table->columns) as $column){
            $colmethodname = 'col_'.$column;
            if (method_exists($this, $colmethodname)){
                $formattedcolumn = $this->$colmethodname($row, $download);
            } else {
                $formattedcolumn = $this->other_cols($column, $row, $download);
                if ($formattedcolumn===NULL){
                    $formattedcolumn = $row->$column;
                }
            }
            $formattedrow[$column] = $formattedcolumn;
        }
        return $formattedrow;
    }
    
    function build_table($rows, $download){
        foreach($rows as $row){
            $formattedrow = $this->format_row($row, $download);
            $this->table->add_data_keyed($formattedrow, $download);
        }
    }

}

?>
