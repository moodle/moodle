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
        // Define some strings
        $strreallydel  = addslashes(get_string('deleteattemptcheck','quiz'));
        $strtimeformat = get_string('strftimedatetime');
        $strreviewquestion = get_string('reviewresponse', 'quiz');

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        // Only print headers if not asked to download data
        if (!$download = optional_param('download', NULL)) {
            $this->print_header_and_tabs($cm, $course, $quiz, "overview");
        }

        if($attemptids = optional_param('attemptid', array(), PARAM_INT)) {
            //attempts need to be deleted
            require_capability('mod/quiz:deleteattempts', $context);
            $attemptids = optional_param('attemptid', array(), PARAM_INT);
            foreach($attemptids as $attemptid) {
                add_to_log($course->id, 'quiz', 'delete attempt', 'report.php?id=' . $cm->id,
                        $attemptid, $cm->id);
                quiz_delete_attempt($attemptid, $quiz);
            }
            //No need for a redirect, any attemptids that do not exist are ignored.
            //So no problem if the user refreshes and tries to delete the same attempts
            //twice.
        }

        // Set of format options for teacher-created content, for example overall feedback.
        $nocleanformatoptions = new stdClass;
        $nocleanformatoptions->noclean = true;

        // Work out some display options - whether there is feedback, and whether scores should be shown.
        $hasfeedback = quiz_has_feedback($quiz->id) && $quiz->grade > 1.e-7 && $quiz->sumgrades > 1.e-7;
        $fakeattempt = new stdClass();
        $fakeattempt->preview = false;
        $fakeattempt->timefinish = $quiz->timeopen;
        $reviewoptions = quiz_get_reviewoptions($quiz, $fakeattempt, $context);
        $showgrades = $quiz->grade && $quiz->sumgrades && $reviewoptions->scores;

        if (!$reviewoptions->scores) {
            $detailedmarks = 0;
        }
        $pageoptions = array();
        $pageoptions['id'] = $cm->id;
        $pageoptions['q'] = $quiz->id;
        $pageoptions['mode'] = 'overview';

        $reporturl = new moodle_url($CFG->wwwroot.'/mod/quiz/report.php', $pageoptions);
        $qmsubselect = quiz_report_qm_filter_subselect($quiz);
        $mform = new mod_quiz_report_overview_settings($reporturl, compact('qmsubselect', 'quiz'));
        if ($fromform = $mform->get_data()){
            $attemptsmode = $fromform->attemptsmode;
            if ($qmsubselect){
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
            if ($strattemptnum = quiz_num_attempt_summary($quiz, $cm, false, $currentgroup)) {
                echo '<div class="quizattemptcounts">' . $strattemptnum . '</div>';
            }
        }

        // Print information on the grading method and whether we are displaying
        // 
        if (!$download) { //do not print notices when downloading
            if ($strattempthighlight = quiz_report_highlighting_grading_method($quiz, $qmsubselect, $qmfilter)) {
                echo '<div class="quizattemptcounts">' . $strattempthighlight . '</div>';
            }
        }

        // Now check if asked download of data
        if ($download) {
            $filename = clean_filename("$course->shortname ".format_string($quiz->name,true));
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
            $headers[] = get_string('grade', 'quiz').'/'.$quiz->grade;
        }

        if ($detailedmarks) {
            // we want to display marks for all questions
            $questions = quiz_report_load_questions($quiz);
            foreach ($questions as $id => $question) {
                // Ignore questions of zero length
                $columns[] = '$'.$id;
                $headers[] = '#'.$question->number;
            }
        }
        if ($hasfeedback) {
            $columns[] = 'feedbacktext';
            $headers[] = get_string('feedback', 'quiz');
        }

        if (!$download) {
            // Set up the table

            $table = new flexible_table('mod-quiz-report-overview-report');

            $table->define_columns($columns);
            $table->define_headers($headers);
            $table->define_baseurl($reporturlwithdisplayoptions->out());

            $table->sortable(true);
            $table->collapsible(true);

            $table->column_suppress('picture');
            $table->column_suppress('fullname');

            $table->column_class('picture', 'picture');

            $table->set_attribute('cellspacing', '0');
            $table->set_attribute('id', 'attempts');
            $table->set_attribute('class', 'generaltable generalbox');

            // Start working -- this is necessary as soon as the niceties are over
            $table->setup();
        } else if ($download =='ODS') {
            require_once("$CFG->libdir/odslib.class.php");

            $filename .= ".ods";
            // Creating a workbook
            $workbook = new MoodleODSWorkbook("-");
            // Sending HTTP headers
            $workbook->send($filename);
            // Creating the first worksheet
            $sheettitle = get_string('reportoverview','quiz');
            $myxls =& $workbook->add_worksheet($sheettitle);
            // format types
            $format =& $workbook->add_format();
            $format->set_bold(0);
            $formatbc =& $workbook->add_format();
            $formatbc->set_bold(1);
            $formatbc->set_align('center');
            $formatb =& $workbook->add_format();
            $formatb->set_bold(1);
            $formaty =& $workbook->add_format();
            $formaty->set_bg_color('yellow');
            $formatc =& $workbook->add_format();
            $formatc->set_align('center');
            $formatr =& $workbook->add_format();
            $formatr->set_bold(1);
            $formatr->set_color('red');
            $formatr->set_align('center');
            $formatg =& $workbook->add_format();
            $formatg->set_bold(1);
            $formatg->set_color('green');
            $formatg->set_align('center');
            // Here starts workshhet headers

            $colnum = 0;
            foreach ($headers as $item) {
                $myxls->write(0,$colnum,$item,$formatbc);
                $colnum++;
            }
            $rownum=1;
        } else if ($download =='Excel') {
            require_once("$CFG->libdir/excellib.class.php");

            $filename .= ".xls";
            // Creating a workbook
            $workbook = new MoodleExcelWorkbook("-");
            // Sending HTTP headers
            $workbook->send($filename);
            // Creating the first worksheet
            $sheettitle = get_string('reportoverview','quiz');
            $myxls =& $workbook->add_worksheet($sheettitle);
            // format types
            $format =& $workbook->add_format();
            $format->set_bold(0);
            $formatbc =& $workbook->add_format();
            $formatbc->set_bold(1);
            $formatbc->set_align('center');
            $formatb =& $workbook->add_format();
            $formatb->set_bold(1);
            $formaty =& $workbook->add_format();
            $formaty->set_bg_color('yellow');
            $formatc =& $workbook->add_format();
            $formatc->set_align('center');
            $formatr =& $workbook->add_format();
            $formatr->set_bold(1);
            $formatr->set_color('red');
            $formatr->set_align('center');
            $formatg =& $workbook->add_format();
            $formatg->set_bold(1);
            $formatg->set_color('green');
            $formatg->set_align('center');

            $colnum = 0;
            foreach ($headers as $item) {
                $myxls->write(0,$colnum,$item,$formatbc);
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

        // Get users with quiz attempt capability 'students'.
        // don't need to do this expensive call if we are listing all attempts though.
        if ( $attemptsmode != QUIZ_REPORT_ATTEMPTS_ALL ) { 
            if (empty($currentgroup)) {
                // all users who can attempt quizzes
                $allowed = join(',',array_keys(get_users_by_capability($context, 'mod/quiz:attempt','','','','','','',false)));
            } else {
        
                // all users who can attempt quizzes and who are in the currently selected group
                $allowed = join(',',array_keys(get_users_by_capability($context, 'mod/quiz:attempt','','','','',$currentgroup,'',false)));
            }
        }

        // Construct the SQL
        $select = 'SELECT '.sql_concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).' AS uniqueid, '.
            ($qmsubselect?$qmsubselect.' AS gradedattempt, ':'').
            'qa.uniqueid AS attemptuniqueid, qa.id AS attempt, u.id AS userid, u.idnumber, u.firstname, u.lastname, u.picture, '.
            'qa.sumgrades, qa.timefinish, qa.timestart, qa.timefinish - qa.timestart AS duration ';

        // This part is the same for all cases - join users and quiz_attempts tables
        $from = 'FROM '.$CFG->prefix.'user u ';
        $from .= 'LEFT JOIN '.$CFG->prefix.'quiz_attempts qa ON qa.userid = u.id AND qa.quiz = '.$quiz->id;
        if ($qmsubselect && $qmfilter){
            $from .= ' AND '.$qmsubselect;
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

        $countsql = 'SELECT COUNT(DISTINCT('.sql_concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).')) '.$from.$where;

        if ($download) {
            $sort = '';
        } else {    
            // Add extra limits due to initials bar
            if($table->get_sql_where()) {
                $where .= ' AND '.$table->get_sql_where();
            }

            // Count the records NOW, before funky question grade sorting messes up $from
            if (!empty($countsql)) {
                $totalinitials = count_records_sql($countsql);
                if ($table->get_sql_where()) {
                    $countsql .= ' AND '.$table->get_sql_where();
                }
                $total  = count_records_sql($countsql);

            }

            // Add extra limits due to sorting by question grade
            if($sort = $table->get_sql_sort()) {
                $sortparts    = explode(',', $sort);
                $newsort      = array();
                $questionsort = false;
                foreach($sortparts as $sortpart) {
                    $sortpart = trim($sortpart);
                    if(substr($sortpart, 0, 1) == '$') {
                        if(!$questionsort) {
                            $qid          = intval(substr($sortpart, 1));
                            $select .= ', qs.grade AS qgrade ';
                            $from        .= ' LEFT JOIN '.$CFG->prefix.'question_sessions qns ON qns.attemptid = qa.uniqueid '.
                                                'LEFT JOIN '.$CFG->prefix.'question_states qs ON qs.id = qns.newgraded ';
                            $where       .= ' AND (qns.questionid IS NULL OR qns.questionid = '.$qid.')';
                            $newsort[]    = 'qgrade '.(strpos($sortpart, 'ASC')? 'ASC' : 'DESC');
                            $questionsort = true;
                        }
                    } else {
                        $newsort[] = $sortpart;
                    }
                }
                // Reconstruct the sort string
                $sort = ' ORDER BY '.implode(', ', $newsort);
            }

            // Fix some wired sorting
            if (empty($sort)) {
                $sort = ' ORDER BY uniqueid';
            }

            $table->pagesize($pagesize, $total);
        }

        // If there is feedback, include it in the query.
        if ($hasfeedback) {
            $factor = $quiz->grade/$quiz->sumgrades;
            $select .= ', qf.feedbacktext ';
            $from .= " LEFT JOIN {$CFG->prefix}quiz_feedback qf ON " .
                    "qf.quizid = $quiz->id AND qf.mingrade <= qa.sumgrades * $factor AND qa.sumgrades * $factor < qf.maxgrade";
        }

        // Fetch the attempts
        if (!$download) {
            $attempts = get_records_sql($select.$from.$where.$sort,
                                    $table->get_page_start(), $table->get_page_size());
        } else {
            $attempts = get_records_sql($select.$from.$where.$sort);
        }
        // Build table rows
        if (!$download) {
            $table->initialbars($totalinitials>20);
        }
        if ($attempts) {
            if($detailedmarks) {
                //get all the attempt ids we want to display on this page
                //or to export for download.
                $attemptids = array();
                foreach ($attempts as $attempt){
                    if ($attempt->attemptuniqueid > 0){
                        $attemptids[] = $attempt->attemptuniqueid;
                    }
                }
                $gradedstatesbyattempt = quiz_get_newgraded_states($attemptids);
            }
            foreach ($attempts as $attempt) {
                $picture = print_user_picture($attempt->userid, $course->id, $attempt->picture, false, true);

                $userlink = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$attempt->userid.
                        '&amp;course='.$course->id.'">'.fullname($attempt).'</a>';

                // Username columns.
                $row = array();
                if (in_array('checkbox', $columns)){
                    if ($attempt->attempt){
                        $row[] = '<input type="checkbox" name="attemptid[]" value="'.$attempt->attempt.'" />';
                    } else {
                        $row[] = '';
                    }
                }
                if (in_array('picture', $columns)){
                    $row[] = $picture;
                }
                if (!$download){
                    $row[] = $userlink;
                } else {
                    $row[] = fullname($attempt);
                }
                
                if (in_array('idnumber', $columns)){
                    $row[] = $attempt->idnumber;
                }

                // Timing columns.
                if ($attempt->attempt) {
                    $startdate = userdate($attempt->timestart, $strtimeformat);
                    if (!$download) {
                        $row[] = '<a href="review.php?q='.$quiz->id.'&amp;attempt='.$attempt->attempt.'">'.$startdate.'</a>';
                    } else {
                        $row[] = $startdate;
                    }
                    if ($attempt->timefinish) {
                        $timefinish = userdate($attempt->timefinish, $strtimeformat);
                        $duration = format_time($attempt->duration);
                        if (!$download) {
                            $row[] = '<a href="review.php?q='.$quiz->id.'&amp;attempt='.$attempt->attempt.'">'.$timefinish.'</a>';
                        } else {
                            $row[] = $timefinish;
                        }
                        $row[] = $duration;
                    } else {
                        $row[] = '-';
                        $row[] = get_string('unfinished', 'quiz');
                    }
                } else {
                    $row[] = '-';
                    $row[] = '-';
                    $row[] = '-';
                }

                // Grades columns.
                if ($showgrades) {
                    if ($attempt->timefinish) {
                        $grade = quiz_rescale_grade($attempt->sumgrades, $quiz);
                        if (!$download) {
                            $gradehtml = '<a href="review.php?q='.$quiz->id.'&amp;attempt='.$attempt->attempt.'">'.$grade.'</a>';
                            if ($qmsubselect && $attempt->gradedattempt){
                                $gradehtml = '<div class="highlight">'.$gradehtml.'</div>';
                            }
                            $row[] = $gradehtml;
                        } else {
                            $row[] = $grade;
                        }
                    } else {
                        $row[] = '-';
                    }
                }

                if($detailedmarks) {
                    //get the detailled grade data for this attempt
                    if(empty($attempt->attempt)) {
                        foreach($questions as $question) {
                            $row[] = '-';
                        }
                    } else {
                        foreach($questions as $questionid => $question) {
                            $stateforqinattempt = $gradedstatesbyattempt[$attempt->attemptuniqueid][$questionid];
                            if (question_state_is_graded($stateforqinattempt)) {
                                $grade = quiz_rescale_grade($stateforqinattempt->grade, $quiz);
                            } else {
                                $grade = '--';
                            }
                            if (!$download) {
                                $grade = $grade.'/'.quiz_rescale_grade($question->grade, $quiz);
                                $row[] = link_to_popup_window('/mod/quiz/reviewquestion.php?state='.
                                        $stateforqinattempt->id.'&amp;number='.$question->number,
                                        'reviewquestion', $grade, 450, 650, $strreviewquestion, 'none', true);
                            } else {
                                $row[] = $grade;
                            }
                        }
                    }
                }

                // Feedback column.
                if ($hasfeedback) {
                    if ($attempt->timefinish) {
                        $row[] = format_text($attempt->feedbacktext, FORMAT_MOODLE, $nocleanformatoptions);
                    } else {
                        $row[] = '-';
                    }
                }
                if (!$download) {
                    $table->add_data($row);
                } else if ($download == 'Excel' or $download == 'ODS') {
                    $colnum = 0;
                    foreach($row as $item){
                        $myxls->write($rownum,$colnum,$item,$format);
                        $colnum++;
                    }
                    $rownum++;
                } else if ($download=='CSV') {
                    $text = implode("\t", $row);
                    echo $text." \n";
                }
            }
            if (!$download) {
                // Start form
                echo '<div id="tablecontainer">';
                echo '<form id="attemptsform" method="post" action="' . $reporturlwithdisplayoptions->out(true) .
                        '" onsubmit="confirm(\''.$strreallydel.'\');">';
                echo $reporturlwithdisplayoptions->hidden_params_out();
                echo '<div>';

                // Print table
                $table->print_html();

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
                $table->print_html();
            }
        }
        if ($download == 'Excel' or $download == 'ODS') {
            $workbook->close();
            exit;
        } else if ($download == 'CSV') {
            exit;
        }
        if (!$download) {
            // Print display options
            $mform->set_data($displayoptions +compact('detailedmarks', 'pagesize'));
            $mform->display();
            $imageurl = $CFG->wwwroot.'/mod/quiz/report/overview/overviewgraph.php?id='.$quiz->id;
            print_heading(get_string('overviewreportgraph', 'quiz_overview'));
            echo '<div class="mdl-align"><img src="'.$imageurl.'" alt="'.get_string('overviewreportgraph', 'quiz_overview').'" /></div>';
        }
        return true;
    }
}

?>
