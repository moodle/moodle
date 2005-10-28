<?php  // $Id$

// This script lists student attempts

    require_once($CFG->libdir.'/tablelib.php');

class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {     /// This function just displays the report
        global $CFG, $SESSION, $db, $QUIZ_QTYPES;

    /// Define some strings
        $strreallydel  = addslashes(get_string('deleteattemptcheck','quiz'));
        $strnoattempts = get_string('noattempts','quiz');
        $strtimeformat = get_string('strftimedatetime');
        $strreviewquestion = get_string('reviewresponse', 'quiz');

    /// Only print headers if not asked to download data
        if (!$download = optional_param('download', NULL)) {
            $this->print_header_and_tabs($cm, $course, $quiz, $reportmode="overview");
        }

    /// Deal with actions

        $action = optional_param('action', '');

        switch($action) {
            case 'delete':  /// Some attempts need to be deleted
                // the following needs to be improved to delete all associated data as well

                $attemptids = isset($_POST['attemptid']) ? $_POST['attemptid'] : array();
                if(!is_array($attemptids) || empty($attemptids)) {
                    break;
                }

                foreach($attemptids as $num => $attemptid) {
                    if(empty($attemptid)) {
                        unset($attemptids[$num]);
                    }
                }

                foreach($attemptids as $attemptid) {
                    if ($todelete = get_record('quiz_attempts', 'id', $attemptid)) {

                        delete_records('quiz_attempts', 'id', $attemptid);
                        delete_records('quiz_states', 'attempt', $todelete->uniqueid);
                        delete_records('quiz_newest_states', 'attemptid', $todelete->uniqueid);

                        // Search quiz_attempts for other instances by this user.
                        // If none, then delete record for this quiz, this user from quiz_grades
                        // else recalculate best grade

                        $userid = $todelete->userid;
                        if (!record_exists('quiz_attempts', 'userid', $userid, 'quiz', $quiz->id)) {
                            delete_records('quiz_grades', 'userid', $userid,'quiz', $quiz->id);
                        } else {
                            quiz_save_best_grade($quiz, $userid);
                        }
                    }
                }
            break;
        }

    /// Check to see if groups are being used in this quiz
        if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
            if (!$download) {
                $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id&amp;mode=overview");
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

        // Uncomment the following if desired: if there are people with attempts but they have been unenrolled
        // since making those attempts, count them in as well. DO NOT count course teachers.
        // Problem with this code: includes users from ALL groups, see bug 3995
        //$userswithattempts = get_records_sql('SELECT DISTINCT qa.userid AS id, qa.userid FROM '.$CFG->prefix.'quiz_attempts qa LEFT JOIN '.$CFG->prefix.'user_teachers ut ON qa.userid = ut.userid AND ut.course = '.$course->id.' WHERE ut.id IS NULL AND quiz = '.$quiz->id);
        //if(!empty($userswithattempts)) {
        //    $unenrolledusers = array_diff(array_keys($userswithattempts), $users);
        //    $users = array_merge($users, $unenrolledusers);
        //}

        if(empty($users)) {
            print_heading($strnoattempts);
            return true;
        }

    /// Set table options
        if(!isset($SESSION->quiz_overview_table)) {
            $SESSION->quiz_overview_table = array('noattempts' => false, 'detailedmarks' => false, 'pagesize' => 10);
        }

        foreach($SESSION->quiz_overview_table as $option => $value) {
            $urlparam = optional_param($option, NULL);
            if($urlparam === NULL) {
                $$option = $value;
            }
            else {
                $$option = $SESSION->quiz_overview_table[$option] = $urlparam;
            }
        }

        /// Now check if asked download of data
        if ($download) {
            $filename = clean_filename("$course->shortname ".format_string($quiz->name,true));
            $sort = '';
            $limit = '';
        }

    /// Define table columns
        $tablecolumns = array('checkbox', 'picture', 'fullname', 'timestart', 'duration');
        $tableheaders = array(NULL, '', get_string('fullname'), get_string('startedon', 'quiz'), get_string('attemptduration', 'quiz'));

        if ($quiz->grade) {
            $tablecolumns[] = 'sumgrades';
            $tableheaders[] = get_string('grade', 'quiz').'/'.$quiz->grade;
        }

        if($detailedmarks) {
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
        }

        if (!$download) {
            // Set up the table

            $table = new flexible_table('mod-quiz-report-overview-report');

            $table->define_columns($tablecolumns);
            $table->define_headers($tableheaders);
            $table->define_baseurl($CFG->wwwroot.'/mod/quiz/report.php?mode=overview&amp;id='.$cm->id);

            $table->sortable(true);
            $table->collapsible(true);
            $table->initialbars(count($users)>20);

            $table->column_suppress('picture');
            $table->column_suppress('fullname');

            $table->column_class('picture', 'picture');

            $table->set_attribute('cellspacing', '0');
            $table->set_attribute('id', 'attempts');
            $table->set_attribute('class', 'generaltable generalbox');

            // Start working -- this is necessary as soon as the niceties are over
            $table->setup();
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

            $headers = array(get_string('fullname'), get_string('startedon', 'quiz'), get_string('attemptduration', 'quiz'));

            if ($quiz->grade) {
                $headers[] = get_string('grade', 'quiz').'/'.$quiz->grade;
            }
            if($detailedmarks) {
                foreach ($questions as $question) {
                    $headers[] = '#'.$question->number;
                }
            }
            $colnum = 0;
            foreach ($headers as $item) {
                $myxls->write(0,$colnum,$item,$formatbc);
                $colnum++;
            }
            $rownum=1;
        } elseif ($download=='CSV') {
            $filename .= ".txt";

            header("Content-Type: application/download\n");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Expires: 0");
            header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
            header("Pragma: public");

            $headers = get_string('fullname')."\t".get_string('startedon', 'quiz')."\t".get_string('attemptduration', 'quiz');

            if ($quiz->grade) {
                $headers .= "\t".get_string('grade', 'quiz')."/".$quiz->grade;
            }
            if($detailedmarks) {
                foreach ($questions as $question) {
                    $headers .= "\t#".$question->number;
                }
            }
            echo $headers." \n";
        }



        // Construct the SQL

        $select = 'SELECT '.$db->Concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).' AS uniqueid, qa.id AS attempt, qa.uniqueid as attemptuniqueid, u.id AS userid, u.firstname, u.lastname, u.picture, '.
                  'qa.sumgrades, qa.timefinish, qa.timestart, qa.timefinish - qa.timestart AS duration ';
        $from   = 'FROM '.$CFG->prefix.'user u LEFT JOIN '.$CFG->prefix.'quiz_attempts qa ON (u.id = qa.userid AND qa.quiz = '.$quiz->id.') ';
        $where  = 'WHERE u.id IN ('.implode(',', $users).') ';

        // Add extra limits if we 're not interested in students without attempts
        if(!$noattempts) {
            $where .= 'AND '.$db->IfNull('qa.attempt', '0').' != 0 ';
        }
        if (!$download) {
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
                            $from        .= 'LEFT JOIN '.$CFG->prefix.'quiz_newest_states qns ON qns.attemptid = qa.attemptuniqueid '.
                                                'LEFT JOIN '.$CFG->prefix.'quiz_states qs ON qs.id = qns.newgraded ';
                            $where       .= ' AND ('.sql_isnull('qns.questionid').' OR qns.questionid = '.$qid.')';
                            $newsort[]    = 'grade '.(strpos($sortpart, 'ASC')? 'ASC' : 'DESC');
                            $questionsort = true;
                        }
                    }
                    else {
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

            // Now it is time to page the data
            if (!isset($pagesize)  || ((int)$pagesize < 1) ) {
                $pagesize = 10;
            }
            $table->pagesize($pagesize, $total);

            if($table->get_page_start() !== '' && $table->get_page_size() !== '') {
                $limit = ' '.sql_paging_limit($table->get_page_start(), $table->get_page_size());
            }
            else {
                $limit = '';
            }
        }

    /// Fetch the attempts
        $attempts = get_records_sql($select.$from.$where.$sort.$limit);

    /// Build table rows

        if(!empty($attempts)) {

            foreach ($attempts as $attempt) {

                $picture = print_user_picture($attempt->userid, $course->id, $attempt->picture, false, true);

                // uncomment the commented lines below if you are choosing to show unenrolled users and
                // have uncommented the corresponding lines earlier in this script
                //if (in_array($attempt->userid, $unenrolledusers)) {
                //    $userlink = '<a class="dimmed" href="'.$CFG->wwwroot.'/user/view.php?id='.$attempt->userid.'&amp;course='.$course->id.'">'.fullname($attempt).'</a>';
                //}
                //else {
                    $userlink = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$attempt->userid.'&amp;course='.$course->id.'">'.fullname($attempt).'</a>';
                //}
                if (!$download) {
                    $row = array(
                              '<input type="checkbox" name="attemptid[]" value="'.$attempt->attempt.'" />',
                              $picture,
                              $userlink,
                              empty($attempt->attempt) ? '-' : '<a href="review.php?q='.$quiz->id.'&amp;attempt='.$attempt->attempt.'">'.userdate($attempt->timestart, $strtimeformat).'</a>',
                              empty($attempt->attempt) ? '-' :
                               (empty($attempt->timefinish) ? get_string('unfinished', 'quiz') :
                                format_time($attempt->duration))
                           );
                } 
                else {
                    $row = array(fullname($attempt),
                               empty($attempt->attempt) ? '-' : userdate($attempt->timestart, $strtimeformat),
                               empty($attempt->attempt) ? '-' :
                               (empty($attempt->timefinish) ? get_string('unfinished', 'quiz') :
                               format_time($attempt->duration))
                           );
                }

                if ($quiz->grade) {
                    if (!$download) {
                        $row[] = $attempt->sumgrades === NULL ? '-' : '<a href="review.php?q='.$quiz->id.'&amp;attempt='.$attempt->attempt.'">'.round($attempt->sumgrades / $quiz->sumgrades * $quiz->grade,$quiz->decimalpoints).'</a>';
                    }
                    else {
                        $row[] = $attempt->sumgrades === NULL ? '-' : round($attempt->sumgrades / $quiz->sumgrades * $quiz->grade,$quiz->decimalpoints);
                    }
                }
                if($detailedmarks) {
                    if(empty($attempt->attempt)) {
                        foreach($questionids as $questionid) {
                            $row[] = '-';
                        }
                    }
                    else {
                        foreach($questionids as $questionid) {
                            if ($gradedstateid = get_field('quiz_newest_states', 'newgraded', 'attemptid', $attempt->attemptuniqueid, 'questionid', $questionid)) {
                                $grade = round(get_field('quiz_states', 'grade', 'id', $gradedstateid), $quiz->decimalpoints);
                            } else {
                                // This is an old-style attempt
                                $grade = round(get_field('quiz_states', 'grade', 'attempt', $attempt->attempt, 'question', $questionid), $quiz->decimalpoints);
                            }
                            if (!$download) {
                                $row[] = link_to_popup_window ('/mod/quiz/reviewquestion.php?state='.$gradedstateid.'&amp;number='.$questions[$questionid]->number, 'reviewquestion', $grade, 450, 650, $strreviewquestion, 'none', true);
                            }
                            else {
                            $row[] = $grade;
                            }
                        }
                    }
                }
                if (!$download) {
                    $table->add_data($row);
                }
                elseif ($download == 'Excel') {
                    $colnum = 0;
                    foreach($row as $item){
                        $myxls->write($rownum,$colnum,$item,$format);
                        $colnum++;
                    }
                    $rownum++;
                }
                elseif ($download=='CSV') {
                    $text = implode("\t", $row);
                    echo $text." \n";
                }
            }
            if (!$download) {
    /// Start form

                echo '<div id="tablecontainer">';
                echo '<form id="attemptsform" method="post" action="report.php" onsubmit="var menu = document.getElementById(\'menuaction\'); return (menu.options[menu.selectedIndex].value == \'delete\' ? \''.$strreallydel.'\' : true);">';
                echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
                echo '<input type="hidden" name="mode" value="overview" />';

    /// Print table

                $table->print_html();

    /// Print "Select all" etc.

                echo '<table id="commands">';
                echo '<tr><td>';
                echo '<a href="javascript:select_all_in(\'DIV\',null,\'tablecontainer\');">'.get_string('selectall', 'quiz').'</a> / ';
                echo '<a href="javascript:deselect_all_in(\'DIV\',null,\'tablecontainer\');">'.get_string('selectnone', 'quiz').'</a> ';
                echo '&nbsp;&nbsp;';
                $options = array('delete' => get_string('delete'));
                echo choose_from_menu($options, 'action', '', get_string('withselected', 'quiz'), 'if(this.selectedIndex > 0) submitFormById(\'attemptsform\');', '', true);
                echo '<noscript id="noscriptmenuaction" style="display: inline;">';
                echo '<input type="submit" value="'.get_string('go').'" /></noscript>';
                echo '<script type="text/javascript">'."\n<!--\n".'document.getElementById("noscriptmenuaction").style.display = "none";'."\n-->\n".'</script>';
                echo '</td></tr></table>';

    /// Close form
                echo '</form></div>';
    /// Print display options
                echo '<div class="controls">';
                echo '<form id="options" name="options" action="report.php" method="post">';
                echo '<p>'.get_string('displayoptions', 'quiz').': </p>';
                echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
                echo '<input type="hidden" name="q" value="'.$quiz->id.'" />';
                echo '<input type="hidden" name="mode" value="overview" />';
                echo '<input type="hidden" name="noattempts" value="0" />';
                echo '<input type="hidden" name="detailedmarks" value="0" />';
                echo '<table id="overview-options" align="center">';
                echo '<tr align="left">';
                echo '<td><label for="pagesize">'.get_string('pagesize', 'quiz').'</label></td>';
                echo '<td><input type="text" id="pagesize" name="pagesize" size="1" value="'.$pagesize.'" /></td>';
                echo '</tr>';
	            echo '<tr align="left">';
                echo '<td colspan="2"><input type="checkbox" id="checknoattempts" name="noattempts" '.($noattempts?'checked="checked" ':'').'value="1" /> <label for="checknoattempts">'.get_string('shownoattempts', 'quiz').'</label> ';
	            echo '</td></tr>';
	            echo '<tr align="left">';
                echo '<td colspan="2"><input type="checkbox" id="checkdetailedmarks" name="detailedmarks" '.($detailedmarks?'checked="checked" ':'').'value="1" /> <label for="checkdetailedmarks">'.get_string('showdetailedmarks', 'quiz').'</label> ';
	            echo '</td></tr>';
                echo '<tr><td colspan="2" align="center">';
                echo '<input type="submit" value="'.get_string('go').'" />';
                echo '</td></tr></table>';
                echo '</form>';
                echo '</div>';
                echo "\n";

                echo '<table align="center"><tr>';
                unset($options);
                $options["id"] = "$cm->id";
                $options["q"] = "$quiz->id";
                $options["mode"] = "overview";
                $options['sesskey'] = sesskey();
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
                helpbutton("download", get_string("download","quiz"), "quiz");
                echo "</td>\n";
                echo '</tr></table>';
            }
            elseif ($download == 'Excel') {
                $workbook->close();
            }
            elseif ($download == 'CSV') {
                exit;
            }

        }
        else {
            if (!$download) {
                $table->print_html();
            }
        }
        return true;
    }
}

?>
