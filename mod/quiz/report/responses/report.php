<?php  // $Id$

// This script lists student attempts and responses

    require_once($CFG->libdir.'/tablelib.php');

class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {     /// This function just displays the report
        global $CFG, $SESSION, $db, $QUIZ_QTYPES;

    /// Define some strings
        $strnoattempts = get_string('noattempts','quiz');
        $strtimeformat = get_string('strftimedatetime');
        $strreviewquestion = get_string('reviewresponse', 'quiz');

    /// Only print headers if not asked to download data
        if (!$download = optional_param('download', NULL)) {
            $this->print_header_and_tabs($cm, $course, $quiz, $reportmode="responses");
        }
    
    /// Check to see if groups are being used in this quiz
        if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
            if (!$download) {
                $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id&amp;mode=responses");
            } else {
                if (isset($_GET['group'])) {
                    $changegroup = $_GET['group'];  /// 0 or higher
                } else {
                    $changegroup = -1;              /// This means no group change was specified
                }

                $currentgroup = get_and_set_current_group($course, $groupmode, $changegroup);  
            }
        } else {
            $currentgroup = false;
        }
    
    /// Get all students
        if ($currentgroup) {
            $users = get_group_students($currentgroup);
        }
        else {
            $users = get_course_students($course->id);
        }
    
    if($users === false) {
        $users = array();
    }
    else {
        $users = array_keys($users);
    }

/// Now the tricky part: if there are people with attempts but they have been unenrolled
/// since making those attempts, count them in as well. DO NOT count course teachers.

    //$userswithattempts = get_records_sql('SELECT DISTINCT qa.userid AS id, qa.userid FROM '.$CFG->prefix.'quiz_attempts qa LEFT JOIN '.$CFG->prefix.'user_teachers ut ON qa.userid = ut.userid AND ut.course = '.$course->id.' WHERE ut.id IS NULL AND quiz = '.$quiz->id);
    //if(!empty($userswithattempts)) {
        //$unenrolledusers = array_diff(array_keys($userswithattempts), $users);
        //$users = array_merge($users, $unenrolledusers);
    //}

        if(empty($users)) {
            print_heading($strnoattempts);
            return true;
        }
    
        // set Table options
        if(!isset($SESSION->quiz_responses_table)) {
            $SESSION->quiz_responses_table = array('noattempts' => false, 'pagesize' => 10);
        }

        foreach($SESSION->quiz_responses_table as $option => $value) {
            $urlparam = optional_param($option, NULL);
            if($urlparam === NULL) {
                $$option = $value;
            }
            else {
                $$option = $SESSION->quiz_responses_table[$option] = $urlparam;
            }
        }
    /// Define table columns
        $tablecolumns = array('picture', 'fullname');
        $tableheaders = array('', get_string('fullname'));
    
        if ($quiz->grade) {
            $tablecolumns[] = 'sumgrades';
            $tableheaders[] = get_string('grade', 'quiz').'/'.$quiz->grade;
        }
    
        // we want to display marks for all questions
        // Start by getting all questions
        $questionlist = quiz_questions_in_quiz($quiz->questions);
        $questionids = explode(',', $questionlist);
        $sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
               "  FROM {$CFG->prefix}quiz_questions q,".
               "       {$CFG->prefix}quiz_question_instances i".
               " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
               "   AND q.id IN ($questionlist)";
        if (!$questions = get_records_sql($sql)) {
            error('No questions found');
        }
        $number = 1;
        foreach($questionids as $key => $id) {
            if ($questions[$id]->length) {
                // Only print questions of non-zero length
                $tablecolumns[] = '$'.$id;
                $tableheaders[] = '#'.$number;
                $questions[$id]->number = $number;
                $number += $questions[$id]->length;
            } else {
                // get rid of zero length questions
                unset($questions[$id]);
                unset($questionids[$key]);
            }
        }
        // Load the question type specific information
        if (!quiz_get_question_options($questions)) {
            error('Could not load question options');
        }

        /// Now check if asked download of data
        if ($download) {
            $filename = clean_filename("$course->shortname ".format_string($quiz->name,true));
            $pagelimit = '';
        }    
        // Construct the SQL
    
        $select = 'SELECT '.$db->Concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).' AS uniqueid, u.firstname, u.lastname, u.picture, qa.*';
        $from   = ' FROM '.$CFG->prefix.'user u LEFT JOIN '.$CFG->prefix.'quiz_attempts qa ON (u.id = qa.userid AND qa.quiz = '.$quiz->id.') ';
    $where  = 'WHERE u.id IN ('.implode(',', $users).') ';
    
        // Add extra limits if we 're not interested in students without attempts
        if(!$noattempts) {
            $where .= 'AND '.$db->IfNull('qa.attempt', '0').' != 0 ';
        }
        if (!$download) {
            // Set up the table
    
            $table = new flexible_table('mod-quiz-report-responses');
    
            $table->define_columns($tablecolumns);
            $table->define_headers($tableheaders);
            $table->define_baseurl($CFG->wwwroot.'/mod/quiz/report.php?mode=responses&amp;id='.$cm->id);
	    
            $table->sortable(true);
            $table->collapsible(true);
            $table->initialbars(count($users)>20);
	    
            $table->column_suppress('picture');
            $table->column_suppress('fullname');
	    
            $table->column_class('picture', 'picture');
	    
            $table->set_attribute('cellspacing', '0');
            $table->set_attribute('id', 'responses');
            $table->set_attribute('class', 'generaltable generalbox');
	    
            // Start working -- this is necessary as soon as the niceties are over
            $table->setup();
	
            // Add extra limits due to initials bar
            if($table->get_sql_where()) {
                $where .= 'AND '.$table->get_sql_where();
            }
            // Count the records NOW, before funky question grade sorting messes up $from
            $total  = count_records_sql('SELECT COUNT(DISTINCT('.$db->Concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).')) '.$from.$where);
	  
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
                            $select .= ', grade ';
                            $from        .= 'LEFT JOIN '.$CFG->prefix.'quiz_newest_states qns ON qns.attemptid = qa.id '.
	                                        'LEFT JOIN '.$CFG->prefix.'quiz_states qs ON qs.id = qns.newgraded ';
                            $where       .= ' AND ('.sql_isnull('qns.questionid').' OR qns.questionid = '.$qid.')';
                            $newsort[]    = 'answer '.(strpos($sortpart, 'ASC')? 'ASC' : 'DESC');
                            $questionsort = true;
                        }
                    } else {
                        $newsort[] = $sortpart;
                    }
                }
	    
                // Reconstruct the sort string
                $sort = ' ORDER BY '.implode(', ', $newsort);
            }
            // Now it is time to page the data, even if we ajust $total later 
            if (!isset($pagesize) || ((int)$pagesize < 1) ) {
                $pagesize = 10;
            }
            $table->pagesize($pagesize, $total);
            $start = $table->get_page_start();
            if($start !== '') {
                $pagelimit = ' '.sql_paging_limit($start, $pagesize);
            } else {
              $pagelimit = '';
            }
        } elseif ($download =='Excel') {
            require_once("$CFG->libdir/excel/Worksheet.php");
            require_once("$CFG->libdir/excel/Workbook.php");
        
            $filename .= ".xls";
            header("Content-Type: application/vnd.ms-excel");   
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Expires: 0");
            header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
            header("Pragma: public");
            header("Content-Transfer-Encoding: binary");

            $workbook = new Workbook("-");
            // Creating the first worksheet
            $sheettitle = get_string('reportresponses','quiz_responses');
            $myxls =& $workbook->add_worksheet($sheettitle);
            /// format types
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

            $headers = array(get_string('fullname')); 

            if ($quiz->grade) {
                $headers[] = get_string('grade', 'quiz').'/'.$quiz->grade;
            }
            foreach ($questions as $question) {
                $headers[] = '#'.$question->number;
            }
            $col = 0;
            foreach ($headers as $item) {
                $myxls->write(0,$col,$item,$formatbc);
                $col++;
            }
            $row=1;
        } elseif ($download=='CSV') {
            $filename .= ".txt";

            header("Content-Type: application/download\n");   
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Expires: 0");
            header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
            header("Pragma: public");

            $headers = get_string('fullname'); 

            if ($quiz->grade) {
                $headers .= "\t".get_string('grade', 'quiz')."/".$quiz->grade;
            }
            foreach ($questions as $question) {
                $headers .= "\t#".$question->number;
            }
            echo $headers." \n";

        }
    /// Fetch the attempts
        $attempts = get_records_sql($select.$from.$where.$sort.$pagelimit);

    /// Build table rows
    
        if(!empty($attempts)) {
    
            foreach ($attempts as $attempt) {
                if (!$download) {    
                    $picture = print_user_picture($attempt->userid, $course->id, $attempt->picture, false, true);
    
                   // if(in_array($attempt->userid, $unenrolledusers)) {
                   //     $userlink = '<a class="dimmed" href="'.$CFG->wwwroot.'/user/view.php?id='.$attempt->userid.'&amp;course='.$course->id.'">'.fullname($attempt).'</a>';
                   // } else {
                        $userlink = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$attempt->userid.'&amp;course='.$course->id.'">'.fullname($attempt).'</a>';
                   // }

                    $rowdata = array(
                                  $picture,
                                  $userlink);
    
                    if ($quiz->grade) {
                        $rowdata[] = $attempt->sumgrades === NULL ? '<a href="review.php?q='.$quiz->id.'&amp;attempt='.$attempt->id.'">-</a>' : '<a href="review.php?q='.$quiz->id.'&amp;attempt='.$attempt->id.'">'.round($attempt->sumgrades / $quiz->sumgrades * $quiz->grade,$quiz->decimalpoints).'</a>';
                    }
                } elseif ($download == 'Excel' || $download =='CSV') {
                    $rowdata = array(fullname($attempt));
                    if ($quiz->grade) {
                        $rowdata[] = $attempt->sumgrades === NULL ? '-' : round($attempt->sumgrades / $quiz->sumgrades * $quiz->grade,$quiz->decimalpoints); 
                    }
                }
                if(empty($attempt->id)) {
                    foreach($questionids as $questionid) {
                        $rowdata[] = '-';
                    }
                } else {
                    // Restore the question sessions to their most recent states
                    // creating new sessions where required

                    if (!$states = quiz_get_states($questions, $quiz, $attempt)) {
                        error('Could not restore question sessions');
                    }
                    foreach($questionids as $questionid) {
                        $gradedstateid = get_field('quiz_newest_states', 'newgraded', 'attemptid', $attempt->id, 'questionid', $questionid);
                        $grade = round(get_field('quiz_states', 'grade', 'id', $gradedstateid), $quiz->decimalpoints);
                        $responses =  quiz_get_question_actual_response($questions[$questionid], $states[$questionid]);
                        $response = implode(', ',$responses);
                        if (!$download) {
                            $format_options->para = false;
                            $format_options->newlines = false;
                            if ($grade<= 0) {
                                $qclass = 'uncorrect';
                            } elseif ($grade == 1) {
                                $qclass = 'correct';
                            } else {
                                $qclass = 'partialcorrect';
                            }          
                                $responsetext = '<span class="'.$qclass.'">'.$response.' </span>';
                                $rowdata[]=$responsetext;
                        } elseif ($download == 'Excel' || $download =='CSV') {
                            $rowdata[] = $response;
                        }
                    }
                }
                if (!$download) {    
                    $table->add_data($rowdata);
                } elseif ($download == 'Excel') {
                    $col = 0;
                    foreach($rowdata as $item){
                        $myxls->write($row,$col,$item,$format);
                        $col++;
                    }
                    $row++;
                } elseif ($download=='CSV') {
                    $text = implode("\t", $rowdata);
                    echo $text." \n";
                }
            }
    
    /// Start form
            if (!$download) {
                echo '<div id="titlecontainer" class="quiz-report-title">';
                echo get_string("responsestitle", "quiz_responses");
                helpbutton("responses", get_string("reportresponses","quiz_responses"), "quiz");
                echo '</div>';
    
                echo '<div id="tablecontainer">';
    
    /// Print table
                $table->print_html();

    /// Close form
                echo '</div>';
            }
        } else {
            if (!$download) {
                $table->print_html();
            }
        }
        if (!$download) {
            $this->print_options_form($quiz, $cm, $noattempts, $pagesize);
        } elseif ($download == 'Excel') {
            $workbook->close();
        } elseif ($download == 'CSV') {
            exit;
        }
        return true;
    }
    function print_options_form($quiz, $cm, $noattempts, $pagesize=10) {
        global $CFG, $USER;
        echo '<div class="controls">';
        echo '<form id="options" name="options" action="report.php" method="post">';
        echo '<p class="quiz-report-options">'.get_string('displayoptions', 'quiz').': </p>';
        echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
        echo '<input type="hidden" name="q" value="'.$quiz->id.'" />';
        echo '<input type="hidden" name="mode" value="responses" />';
        echo '<input type="hidden" name="noattempts" value="0" />';
        echo '<table id="responses-options" align="center">';
        echo '<tr align="left">';
        echo '<td><label for="pagesize">'.get_string('pagesize', 'quiz_responses').'</label></td>';
        echo '<td><input type="text" id="pagesize" name="pagesize" size="1" value="'.$pagesize.'" /></td>';
        echo '</tr>';
	echo '<tr align="left">';
        echo '<td colspan="2"><input type="checkbox" id="checknoattempts" name="noattempts" '.($noattempts?'checked="checked" ':'').'value="1" /> <label for="checknoattempts">'.get_string('shownoattempts', 'quiz').'</label> ';
	echo '</td></tr>';
        echo '<tr><td colspan="2" align="center">';
        echo '<input type="submit" value="'.get_string('go').'" />';
        helpbutton("responsesoptions", get_string("responsesoptions",'quiz_responses'), 'quiz');
        echo '</td></tr></table>';
        echo '</form>';
        echo '</div>';    
        echo "\n";
 
        echo '<table align="center"><tr>';
        unset($options);
        $options["id"] = "$cm->id";
        $options["q"] = "$quiz->id";
        $options["mode"] = "responses";
        $options['sesskey'] = $USER->sesskey;
        $options["noheader"] = "yes";
        echo '<td>';        
        $options["download"] = "Excel";
        print_single_button("report.php", $options, get_string("downloadexcel"));
        echo "</td>\n";
        echo '<td>';
        $options["download"] = "CSV";
        print_single_button('report.php', $options, get_string("downloadtext"));
        echo "</td>\n";
        echo "<td>";
        helpbutton("responsesdownload", get_string("responsesdownload","quiz"), "quiz");
        echo "</td>\n";
        echo '</tr></table>';
    }

}
?>
