<?php  // $Id$

    require_once($CFG->libdir.'/tablelib.php');

/// Item analysis displays a table of quiz questions and their performance
class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {     /// This function just displays the report
        global $CFG, $SESSION, $QTYPES;
        $strnoattempts = get_string('noattempts','quiz');
    /// Only print headers if not asked to download data
        $download = optional_param('download', NULL);
        if (!$download) {
            $this->print_header_and_tabs($cm, $course, $quiz, $reportmode="analysis");
        }
    /// Construct the table for this particular report

        if (!$quiz->questions) {
            print_heading($strnoattempts);
            return true;
        }

    /// Check to see if groups are being used in this quiz
        $currentgroup = groups_get_activity_group($cm, true);
        
        if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used
            if (!$download) {
                groups_print_activity_menu($cm, $CFG->wwwroot . "/mod/quiz/report.php?id=$cm->id&amp;mode=analysis");
            }
        }

        // set Table and Analysis stats options
        if(!isset($SESSION->quiz_analysis_table)) {
            $SESSION->quiz_analysis_table = array('attemptselection' => 0, 'lowmarklimit' => 0, 'pagesize' => QUIZ_REPORT_DEFAULT_PAGE_SIZE);
        }

        foreach($SESSION->quiz_analysis_table as $option => $value) {
            $urlparam = optional_param($option, NULL, PARAM_INT);
            if($urlparam === NULL) {
                $$option = $value;
            } else {
                $$option = $SESSION->quiz_analysis_table[$option] = $urlparam;
            }
        }
        if (!isset($pagesize) || ((int)$pagesize < 1) ){
            $pagesize = QUIZ_REPORT_DEFAULT_PAGE_SIZE;
        }


        $scorelimit = $quiz->sumgrades * $lowmarklimit/ 100;

        // ULPGC ecastro DEBUG this is here to allow for different SQL to select attempts
        switch ($attemptselection) {
        case QUIZ_ALLATTEMPTS :
            $limit = '';
            $group = '';
            break;
        case QUIZ_HIGHESTATTEMPT :
            $limit = ', max(qa.sumgrades) ';
            $group = ' GROUP BY qa.userid ';
            break;
        case QUIZ_FIRSTATTEMPT :
            $limit = ', min(qa.timemodified) ';
            $group = ' GROUP BY qa.userid ';
            break;
        case QUIZ_LASTATTEMPT :
            $limit = ', max(qa.timemodified) ';
            $group = ' GROUP BY qa.userid ';
            break;
        }

        if ($attemptselection != QUIZ_ALLATTEMPTS) {
            $sql = 'SELECT qa.userid '.$limit.
                    'FROM '.$CFG->prefix.'user u LEFT JOIN '.$CFG->prefix.'quiz_attempts qa ON u.id = qa.userid '.
                    'WHERE qa.quiz = '.$quiz->id.' AND qa.preview = 0 '.
                    $group;
            $usermax = get_records_sql_menu($sql);
        }

        $groupmembers = '';
        $groupwhere = '';

        //Add this to the SQL to show only group users
        if ($currentgroup) {
            $groupmembers = ", {$CFG->prefix}groups_members gm ";
            $groupwhere = "AND gm.groupid = '$currentgroup' AND u.id = gm.userid";
        }

        $sql = 'SELECT  qa.* FROM '.$CFG->prefix.'quiz_attempts qa, '.$CFG->prefix.'user u '.$groupmembers.
                 'WHERE u.id = qa.userid AND qa.quiz = '.$quiz->id.' AND qa.preview = 0 AND ( qa.sumgrades >= '.$scorelimit.' ) '.$groupwhere;

        // ^^^^^^ es posible seleccionar aqu TODOS los quizzes, como quiere Jussi,
        // pero haba que llevar la cuenta ed cada quiz para restaura las preguntas (quizquestions, states)

        /// Fetch the attempts
        $attempts = get_records_sql($sql);

        if(empty($attempts)) {
            print_heading(get_string('nothingtodisplay'));
            $this->print_options_form($quiz, $cm, $attemptselection, $lowmarklimit, $pagesize);
            return true;
        }

    /// Here we rewiew all attempts and record data to construct the table
        $questions = array();
        $statstable = array();
        $questionarray = array();
        foreach ($attempts as $attempt) {
            $questionarray[] = quiz_questions_in_quiz($attempt->layout);
        }
        $questionlist = quiz_questions_in_quiz(implode(",", $questionarray));
        $questionarray = array_unique(explode(",",$questionlist));
        $questionlist = implode(",", $questionarray);
        unset($questionarray);

        foreach ($attempts as $attempt) {
            switch ($attemptselection) {
            case QUIZ_ALLATTEMPTS :
                $userscore = 0;      // can be anything, not used
                break;
            case QUIZ_HIGHESTATTEMPT :
                $userscore = $attempt->sumgrades;
                break;
            case QUIZ_FIRSTATTEMPT :
                $userscore = $attempt->timemodified;
                break;
            case QUIZ_LASTATTEMPT :
                $userscore = $attempt->timemodified;
                break;
            }

            if ($attemptselection == QUIZ_ALLATTEMPTS || $userscore == $usermax[$attempt->userid]) {

            $sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
                   "  FROM {$CFG->prefix}question q,".
                   "       {$CFG->prefix}quiz_question_instances i".
                   " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
                   "   AND q.id IN ($questionlist)";

            if (!$quizquestions = get_records_sql($sql)) {
                error('No questions found');
            }

            // Load the question type specific information
            if (!get_question_options($quizquestions)) {
                error('Could not load question options');
            }
            // Restore the question sessions to their most recent states
            // creating new sessions where required
            if (!$states = get_question_states($quizquestions, $quiz, $attempt)) {
                error('Could not restore question sessions');
            }
            $numbers = explode(',', $questionlist);
            $statsrow = array();
            foreach ($numbers as $i) {
                if (!isset($quizquestions[$i]) or !isset($states[$i])) {
                    continue;
                }
                $qtype = ($quizquestions[$i]->qtype=='random') ? $states[$i]->options->question->qtype : $quizquestions[$i]->qtype;
                $q = get_question_responses($quizquestions[$i], $states[$i]);
                if (empty($q)){
                    continue;
                }
                $qid = $q->id;
                if (!isset($questions[$qid])) {
                    $questions[$qid]['id'] = $qid;
                    $questions[$qid]['qname'] = $quizquestions[$i]->name;
                    foreach ($q->responses as $answer => $r) {
                        $r->count = 0;
                        $questions[$qid]['responses'][$answer] = $r->answer;
                        $questions[$qid]['rcounts'][$answer] = 0;
                        $questions[$qid]['credits'][$answer] = $r->credit;
                        $statsrow[$qid] = 0;
                    }
                }
                $responses = get_question_actual_response($quizquestions[$i], $states[$i]);
                foreach ($responses as $resp){
                    if ($resp) {
                        if ($key = array_search($resp, $questions[$qid]['responses'])) {
                            $questions[$qid]['rcounts'][$key]++;
                        } else {
                            $test = new stdClass;
                            $test->responses = $QTYPES[$quizquestions[$i]->qtype]->get_correct_responses($quizquestions[$i], $states[$i]);
                            if ($key = $QTYPES[$quizquestions[$i]->qtype]->check_response($quizquestions[$i], $states[$i], $test)) {
                                $questions[$qid]['rcounts'][$key]++;
                            } else {
                                $questions[$qid]['responses'][] = $resp;
                                $questions[$qid]['rcounts'][] = 1;
                                $questions[$qid]['credits'][] = 0;
                            }
                        }
                    }
                }
                $statsrow[$qid] = get_question_fraction_grade($quizquestions[$i], $states[$i]);
            }
            $attemptscores[$attempt->id] = $attempt->sumgrades;
            $statstable[$attempt->id] = $statsrow;
            }
        } // Statistics Data table built

        unset($attempts);
        unset($quizquestions);
        unset($states);

        // now calculate statistics and set the values in the $questions array
        $top = max($attemptscores);
        $bottom = min($attemptscores);
        $gap = ($top - $bottom)/3;
        $top -=$gap;
        $bottom +=$gap;
        foreach ($questions as $qid=>$q) {
            $questions[$qid] = $this->report_question_stats($q, $attemptscores, $statstable, $top, $bottom);
        }
        unset($attemptscores);
        unset($statstable);

    /// Now check if asked download of data
        if ($download = optional_param('download', NULL)) {
            $filename = clean_filename("$course->shortname ".format_string($quiz->name,true));
            switch ($download) {
            case "Excel" :
                $this->Export_Excel($questions, $filename);
                break;
            case "ODS":
                $this->Export_ODS($questions, $filename);
                break;
            case "CSV":
                $this->Export_CSV($questions, $filename);
                break;
            }
        }

    /// Construct the table for this particular report

        $tablecolumns = array('id', 'qname',    'responses', 'credits', 'rcounts', 'rpercent', 'facility', 'qsd','disc_index', 'disc_coeff');
        $tableheaders = array(get_string('qidtitle','quiz_analysis'), get_string('qtexttitle','quiz_analysis'),
                        get_string('responsestitle','quiz_analysis'), get_string('rfractiontitle','quiz_analysis'),
                        get_string('rcounttitle','quiz_analysis'), get_string('rpercenttitle','quiz_analysis'),
                        get_string('facilitytitle','quiz_analysis'), get_string('stddevtitle','quiz_analysis'),
                        get_string('dicsindextitle','quiz_analysis'), get_string('disccoefftitle','quiz_analysis'));

        $table = new flexible_table('mod-quiz-report-itemanalysis');

        $table->define_columns($tablecolumns);
        $table->define_headers($tableheaders);
        $table->define_baseurl($CFG->wwwroot.'/mod/quiz/report.php?q='.$quiz->id.'&amp;mode=analysis');

        $table->sortable(true);
        $table->no_sorting('rpercent');
        $table->collapsible(true);
        $table->initialbars(false);

        $table->column_class('id', 'numcol');
        $table->column_class('credits', 'numcol');
        $table->column_class('rcounts', 'numcol');
        $table->column_class('rpercent', 'numcol');
        $table->column_class('facility', 'numcol');
        $table->column_class('qsd', 'numcol');
        $table->column_class('disc_index', 'numcol');
        $table->column_class('disc_coeff', 'numcol');

        $table->column_suppress('id');
        $table->column_suppress('qname');
        $table->column_suppress('facility');
        $table->column_suppress('qsd');
        $table->column_suppress('disc_index');
        $table->column_suppress('disc_coeff');

        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('id', 'itemanalysis');
        $table->set_attribute('class', 'generaltable generalbox');

        // Start working -- this is necessary as soon as the niceties are over
        $table->setup();

        $tablesort = $table->get_sql_sort();
        $sorts = explode(",",trim($tablesort));
        if ($tablesort and is_array($sorts)) {
            $sortindex = array();
            $sortorder = array ();
            foreach ($sorts as $sort) {
                $data = explode(" ",trim($sort));
                $sortindex[] = trim($data[0]);
                $s = trim($data[1]);
                if ($s=="ASC") {
                    $sortorder[] = SORT_ASC;
                } else {
                    $sortorder[] = SORT_DESC;
                }
            }
            if (count($sortindex)>0) {
                $sortindex[] = "id";
                $sortorder[] = SORT_ASC;
                foreach($questions as $qid => $row){
                    $index1[$qid] = $row[$sortindex[0]];
                    $index2[$qid] = $row[$sortindex[1]];
                }
                array_multisort($index1, $sortorder[0], $index2, $sortorder[1], $questions);
            }
        }

        $format_options = new stdClass;
        $format_options->para = false;
        $format_options->noclean = true;
        $format_options->newlines = false;

        // Now it is time to page the data, simply slice the keys in the array
        $table->pagesize($pagesize, count($questions));
        $start = $table->get_page_start();
        $pagequestions = array_slice(array_keys($questions), $start, $pagesize);

        foreach($pagequestions as $qnum) {
            $q = $questions[$qnum];
            $qid = $q['id'];
            $question = get_record('question', 'id', $qid);
            if (question_has_capability_on($question, 'edit') || question_has_capability_on($question, 'view')) {
                $qnumber = " (".link_to_popup_window('/question/question.php?id='.$qid.'&amp;cmid='.$cm->id, 'editquestion', $qid, 450, 550, get_string('edit'), 'none', true ).") ";
            } else {
                $qnumber = $qid;
            }
            $qname = '<div class="qname">'.format_text($question->name." :  ", $question->questiontextformat, $format_options, $quiz->course).'</div>';
            $qicon = print_question_icon($question, true);
            $qreview = quiz_question_preview_button($quiz, $question);
            $qtext = format_text($question->questiontext, $question->questiontextformat, $format_options, $quiz->course);
            $qquestion = $qname."\n".$qtext."\n";

            $responses = array();
            foreach ($q['responses'] as $aid=>$resp){
                $response = new stdClass;
                if ($q['credits'][$aid] <= 0) {
                    $qclass = 'uncorrect';
                } elseif ($q['credits'][$aid] == 1) {
                    $qclass = 'correct';
                } else {
                    $qclass = 'partialcorrect';
                }
                $response->credit = '<span class="'.$qclass.'">('.format_float($q['credits'][$aid],2).') </span>';
                $response->text = '<span class="'.$qclass.'">'.format_text($resp, FORMAT_MOODLE, $format_options, $quiz->course).' </span>';
                $count = $q['rcounts'][$aid].'/'.$q['count'];
                $response->rcount = $count;
                $response->rpercent =  '('.format_float($q['rcounts'][$aid]/$q['count']*100,0).'%)';
                $responses[] = $response;
            }

            $facility = format_float($q['facility']*100,0)."%";
            $qsd = format_float($q['qsd'],3);
            $di = format_float($q['disc_index'],2);
            $dc = format_float($q['disc_coeff'],2);

            $response = array_shift($responses);
            $table->add_data(array($qnumber."\n<br />".$qicon."\n ".$qreview, $qquestion, $response->text, $response->credit, $response->rcount, $response->rpercent, $facility, $qsd, $di, $dc));
            foreach($responses as $response) {
                $table->add_data(array('', '', $response->text, $response->credit, $response->rcount, $response->rpercent, '', '', '', ''));
            }
        }

        print_heading_with_help(get_string("analysistitle", "quiz_analysis"),"itemanalysis", "quiz");

        echo '<div id="tablecontainer">';
        $table->print_html();
        echo '</div>';

        $this->print_options_form($quiz, $cm, $attemptselection, $lowmarklimit, $pagesize);
        return true;
    }


    function print_options_form($quiz, $cm, $attempts, $lowlimit=0, $pagesize=QUIZ_REPORT_DEFAULT_PAGE_SIZE) {
        global $CFG, $USER;
        echo '<div class="controls">';
        echo '<form id="options" action="report.php" method="post">';
        echo '<fieldset class="invisiblefieldset">';
        echo '<p class="quiz-report-options">'.get_string('analysisoptions', 'quiz').': </p>';
        echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
        echo '<input type="hidden" name="q" value="'.$quiz->id.'" />';
        echo '<input type="hidden" name="mode" value="analysis" />';
        echo '<p><label for="menuattemptselection">'.get_string('attemptselection', 'quiz_analysis').'</label> ';
        $options = array ( QUIZ_ALLATTEMPTS     => get_string("attemptsall", 'quiz_analysis'),
                           QUIZ_HIGHESTATTEMPT => get_string("attemptshighest", 'quiz_analysis'),
                           QUIZ_FIRSTATTEMPT => get_string("attemptsfirst", 'quiz_analysis'),
                           QUIZ_LASTATTEMPT  => get_string("attemptslast", 'quiz_analysis'));
        choose_from_menu($options, "attemptselection", "$attempts", "");
        echo '</p>';
        echo '<p><label for="lowmarklimit">'.get_string('lowmarkslimit', 'quiz_analysis').'</label> ';
        echo '<input type="text" id="lowmarklimit" name="lowmarklimit" size="1" value="'.$lowlimit.'" /> % </p>';
        echo '<p><label for="pagesize">'.get_string('pagesize', 'quiz_analysis').'</label> ';
        echo '<input type="text" id="pagesize" name="pagesize" size="1" value="'.$pagesize.'" /></p>';
        echo '<p><input type="submit" value="'.get_string('go').'" />';
        helpbutton("analysisoptions", get_string("analysisoptions",'quiz_analysis'), 'quiz');
        echo '</p>';
        echo '</fieldset>';
        echo '</form>';
        echo '</div>';
        echo "\n";

        echo '<table class="boxaligncenter"><tr>';
        $options = array();
        $options["id"] = "$cm->id";
        $options["q"] = "$quiz->id";
        $options["mode"] = "analysis";
        $options['sesskey'] = $USER->sesskey;
        $options["noheader"] = "yes";
        echo '<td>';
        $options["download"] = "ODS";
        print_single_button("report.php", $options, get_string("downloadods"));
        echo "</td>\n";
        echo '<td>';
        $options["download"] = "Excel";
        print_single_button("report.php", $options, get_string("downloadexcel"));
        echo "</td>\n";

        if (file_exists("$CFG->libdir/phpdocwriter/lib/include.php")) {
            echo '<td>';
            $options["download"] = "OOo";
            print_single_button("report.php", $options, get_string("downloadooo", "quiz_analysis"));
            echo "</td>\n";
        }
        echo '<td>';
        $options["download"] = "CSV";
        print_single_button('report.php', $options, get_string("downloadtext"));
        echo "</td>\n";
        echo "<td>";
        helpbutton('analysisdownload', get_string('analysisdownload', 'quiz_analysis'), 'quiz');
        echo "</td>\n";
        echo '</tr></table>';
}

    function report_question_stats(&$q, &$attemptscores, &$questionscores, $top, $bottom) {
        $qstats = array();
        $qid = $q['id'];
        $top_scores = $top_count = 0;
        $bottom_scores = $bottom_count = 0;
        foreach ($questionscores as $aid => $qrow){
            if (isset($qrow[$qid])){
                $qstats[] =  array($attemptscores[$aid],$qrow[$qid]);
                if ($attemptscores[$aid]>=$top){
                    $top_scores +=$qrow[$qid];
                    $top_count++;
                }
                if ($attemptscores[$aid]<=$bottom){
                    $bottom_scores +=$qrow[$qid];
                    $bottom_count++;
                }
            }
        }
        $n = count($qstats);
        $sumx = stats_sumx($qstats, array(0,0));
        $sumg = $sumx[0];
        $sumq = $sumx[1];
        $sumx2 = stats_sumx2($qstats, array(0,0));
        $sumg2 = $sumx2[0];
        $sumq2 = $sumx2[1];
        $sumxy = stats_sumxy($qstats, array(0,0));
        $sumgq = $sumxy[0];

        $q['count'] = $n;
        $q['facility'] = $sumq/$n;
        if ($n<2) {
            $q['qsd'] = sqrt(($sumq2 - $sumq*$sumq/$n)/($n));
            $gsd = sqrt(($sumg2 - $sumg*$sumg/$n)/($n));
        } else {
            $q['qsd'] = sqrt(($sumq2 - $sumq*$sumq/$n)/($n-1));
            $gsd = sqrt(($sumg2 - $sumg*$sumg/$n)/($n-1));
        }
        $q['disc_index'] = ($top_scores - $bottom_scores)/max($top_count, $bottom_count, 1);
        $div = $n*$gsd*$q['qsd'];
        if ($div!=0) {
            $q['disc_coeff'] = ($sumgq - $sumg*$sumq/$n)/$div;
        } else {
            $q['disc_coeff'] = -999;
        }
        return $q;
    }

    function Export_Excel(&$questions, $filename) {
        global $CFG;
        require_once("$CFG->libdir/excellib.class.php");

    /// Calculate file name
        $filename .= ".xls";
    /// Creating a workbook
        $workbook = new MoodleExcelWorkbook("-");
    /// Sending HTTP headers
        $workbook->send($filename);
    /// Creating the first worksheet
        $sheettitle = get_string('reportanalysis','quiz_analysis');
        $myxls =& $workbook->add_worksheet($sheettitle);
    /// format types
        $format =& $workbook->add_format();
        $format->set_bold(0);
        $formatbc =& $workbook->add_format();
        $formatbc->set_bold(1);
        $formatb =& $workbook->add_format();
        $formatb->set_bold(1);
        $formaty =& $workbook->add_format();
        $formaty->set_bg_color('yellow');
        $formatyc =& $workbook->add_format();
        $formatyc->set_bg_color('yellow'); //bold text on yellow bg
        $formatyc->set_bold(1);
        $formatyc->set_align('center');
        $formatc =& $workbook->add_format();
        $formatc->set_align('center');
        $formatbc->set_align('center');
        $formatbpct =& $workbook->add_format();
        $formatbpct->set_bold(1);
        $formatbpct->set_num_format('0.0%');
        $formatbrt =& $workbook->add_format();
        $formatbrt->set_bold(1);
        $formatbrt->set_align('right');
        $formatred =& $workbook->add_format();
        $formatred->set_bold(1);
        $formatred->set_color('red');
        $formatred->set_align('center');
        $formatblue =& $workbook->add_format();
        $formatblue->set_bold(1);
        $formatblue->set_color('blue');
        $formatblue->set_align('center');
    /// Here starts workshhet headers
        $myxls->write_string(0,0,$sheettitle,$formatb);

        $headers = array(get_string('qidtitle','quiz_analysis'), get_string('qtypetitle','quiz_analysis'),
                        get_string('qnametitle','quiz_analysis'), get_string('qtexttitle','quiz_analysis'),
                        get_string('responsestitle','quiz_analysis'), get_string('rfractiontitle','quiz_analysis'),
                        get_string('rcounttitle','quiz_analysis'), get_string('rpercenttitle','quiz_analysis'),
                        get_string('qcounttitle','quiz_analysis'),
                        get_string('facilitytitle','quiz_analysis'), get_string('stddevtitle','quiz_analysis'),
                        get_string('dicsindextitle','quiz_analysis'), get_string('disccoefftitle','quiz_analysis'));

        foreach ($headers as $key => $header) {
            $headers[$key] = preg_replace('/<br[^>]*>/', ' ', $header);
        }

        $col = 0;
        foreach ($headers as $item) {
            $myxls->write(2,$col,$item,$formatbc);
            $col++;
        }

        $row = 3;
        foreach($questions as $q) {
            $rows = $this->print_row_stats_data($q);
            foreach($rows as $rowdata){
                $col = 0;
                foreach($rowdata as $item){
                    $myxls->write($row,$col,$item,$format);
                    $col++;
                }
                $row++;
            }
        }
    /// Close the workbook
        $workbook->close();

        exit;
    }


    function Export_ODS(&$questions, $filename) {
        global $CFG;
        require_once("$CFG->libdir/odslib.class.php");

    /// Calculate file name
        $filename .= ".ods";
    /// Creating a workbook
        $workbook = new MoodleODSWorkbook("-");
    /// Sending HTTP headers
        $workbook->send($filename);
    /// Creating the first worksheet
        $sheettitle = get_string('reportanalysis','quiz_analysis');
        $myxls =& $workbook->add_worksheet($sheettitle);
    /// format types
        $format =& $workbook->add_format();
        $format->set_bold(0);
        $formatbc =& $workbook->add_format();
        $formatbc->set_bold(1);
        $formatb =& $workbook->add_format();
        $formatb->set_bold(1);
        $formaty =& $workbook->add_format();
        $formaty->set_bg_color('yellow');
        $formatyc =& $workbook->add_format();
        $formatyc->set_bg_color('yellow'); //bold text on yellow bg
        $formatyc->set_bold(1);
        $formatyc->set_align('center');
        $formatc =& $workbook->add_format();
        $formatc->set_align('center');
        $formatbc->set_align('center');
        $formatbpct =& $workbook->add_format();
        $formatbpct->set_bold(1);
        $formatbpct->set_num_format('0.0%');
        $formatbrt =& $workbook->add_format();
        $formatbrt->set_bold(1);
        $formatbrt->set_align('right');
        $formatred =& $workbook->add_format();
        $formatred->set_bold(1);
        $formatred->set_color('red');
        $formatred->set_align('center');
        $formatblue =& $workbook->add_format();
        $formatblue->set_bold(1);
        $formatblue->set_color('blue');
        $formatblue->set_align('center');
    /// Here starts workshhet headers
        $myxls->write_string(0,0,$sheettitle,$formatb);

        $headers = array(get_string('qidtitle','quiz_analysis'), get_string('qtypetitle','quiz_analysis'),
                        get_string('qnametitle','quiz_analysis'), get_string('qtexttitle','quiz_analysis'),
                        get_string('responsestitle','quiz_analysis'), get_string('rfractiontitle','quiz_analysis'),
                        get_string('rcounttitle','quiz_analysis'), get_string('rpercenttitle','quiz_analysis'),
                        get_string('qcounttitle','quiz_analysis'),
                        get_string('facilitytitle','quiz_analysis'), get_string('stddevtitle','quiz_analysis'),
                        get_string('dicsindextitle','quiz_analysis'), get_string('disccoefftitle','quiz_analysis'));

        foreach ($headers as $key => $header) {
            $headers[$key] = preg_replace('/<br[^>]*>/', ' ', $header);
        }

        $col = 0;
        foreach ($headers as $item) {
            $myxls->write(2,$col,$item,$formatbc);
            $col++;
        }

        $row = 3;
        foreach($questions as $q) {
            $rows = $this->print_row_stats_data($q);
            foreach($rows as $rowdata){
                $col = 0;
                foreach($rowdata as $item){
                    $myxls->write($row,$col,$item,$format);
                    $col++;
                }
                $row++;
            }
        }
    /// Close the workbook
        $workbook->close();

        exit;
    }

    function Export_CSV(&$questions, $filename) {

        $headers = array(get_string('qidtitle','quiz_analysis'), get_string('qtypetitle','quiz_analysis'),
                        get_string('qnametitle','quiz_analysis'), get_string('qtexttitle','quiz_analysis'),
                        get_string('responsestitle','quiz_analysis'), get_string('rfractiontitle','quiz_analysis'),
                        get_string('rcounttitle','quiz_analysis'), get_string('rpercenttitle','quiz_analysis'),
                        get_string('qcounttitle','quiz_analysis'),
                        get_string('facilitytitle','quiz_analysis'), get_string('stddevtitle','quiz_analysis'),
                        get_string('dicsindextitle','quiz_analysis'), get_string('disccoefftitle','quiz_analysis'));

        foreach ($headers as $key => $header) {
            $headers[$key] = preg_replace('/<br[^>]*>/', ' ', $header);
        }

        $text = implode("\t", $headers)." \n";

        $filename .= ".txt";

        header("Content-Type: application/download\n");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
        header("Pragma: public");

        echo $text;

        foreach($questions as $q) {
            $rows = $this->print_row_stats_data($q);
            foreach($rows as $row){
                $text = implode("\t", $row);
                echo $text." \n";
            }
        }
        exit;
    }

    function print_row_stats_data(&$q) {
        $qid = $q['id'];
        $question = get_record('question', 'id', $qid);

        $options = new stdClass;
        $options->para = false;
        $options->noclean = true;
        $options->newlines = false;

        $qtype = $question->qtype;

        $qname = format_text($question->name, FORMAT_MOODLE, $options);
        $qtext = format_text($question->questiontext, FORMAT_MOODLE, $options);

        $responses = array();
        foreach ($q['responses'] as $aid=>$resp){
            $response = new stdClass;
            if ($q['credits'][$aid] <= 0) {
                $qclass = 'uncorrect';
            } elseif ($q['credits'][$aid] == 1) {
                $qclass = 'correct';
            } else {
                $qclass = 'partialcorrect';
            }
            $response->credit = " (".format_float($q['credits'][$aid],2).") ";
            $response->text = format_text("$resp", FORMAT_MOODLE, $options);
            $count = $q['rcounts'][$aid].'/'.$q['count'];
            $response->rcount = $count;
            $response->rpercent =  '('.format_float($q['rcounts'][$aid]/$q['count']*100,0).'%)';
            $responses[] = $response;
        }
        $count = format_float($q['count'],0);
        $facility = format_float($q['facility']*100,0);
        $qsd = format_float($q['qsd'],4);
        $di = format_float($q['disc_index'],3);
        $dc = format_float($q['disc_coeff'],3);

        $result = array();
        $response = array_shift($responses);
        $result[] = array($qid, $qtype, $qname, $qtext, $response->text, $response->credit, $response->rcount, $response->rpercent, $count, $facility, $qsd, $di, $dc);
        foreach($responses as $response){
            $result[] = array('', '', '', '', $response->text, $response->credit, $response->rcount, $response->rpercent, '', '', '', '', '');
        }
        return $result;
    }
}

define('QUIZ_ALLATTEMPTS', 0);
define('QUIZ_HIGHESTATTEMPT', 1);
define('QUIZ_FIRSTATTEMPT', 2);
define('QUIZ_LASTATTEMPT', 3);

function stats_sumx($data, $initsum){
    $accum = $initsum;
    foreach ($data as $v) {
        $accum[0] += $v[0];
        $accum[1] += $v[1];
    }
    return $accum;
}

function stats_sumx2($data, $initsum){
    $accum = $initsum;
    foreach ($data as $v) {
        $accum[0] += $v[0]*$v[0];
        $accum[1] += $v[1]*$v[1];
    }
    return $accum;
}

function stats_sumxy($data, $initsum){
    $accum = $initsum;
    foreach ($data as $v) {
        $accum[0] += $v[0]*$v[1];
        $accum[1] += $v[1]*$v[0];
    }
    return $accum;
}

?>
