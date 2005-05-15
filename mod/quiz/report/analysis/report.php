<?php  // $Id$

/// Item analysis displays a table of quiz questions and their performance 

    require_once("locallib.php");
    require_once($CFG->libdir.'/tablelib.php');


function stats_sumx($sum, $data){
    $sum[0] += $data[0];
    $sum[1] += $data[1];
    return $sum;
}       

function stats_sumx2($sum, $data){
    $sum[0] += $data[0]*$data[0];
    $sum[1] += $data[1]*$data[1];
    return $sum;
}    

function stats_sumxy($sum, $data){
    $sum[0] += $data[0]*$data[1];
    return $sum;
}    


/// Item analysis displays a table of quiz questions and their performance 

class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {     /// This function just displays the report

        global $CFG, $SESSION, $db, $QUIZ_QTYPES;
        define(QUIZ_ALLATTEMPTS, 0);
        define(QUIZ_HIGHESTATTEMPT, 1);
        define(QUIZ_FIRSTATTEMPT, 2);
        define(QUIZ_LASTATTEMPT, 3);

        $strnoquiz = get_string('noquiz','quiz');
        $strnoattempts = get_string('noattempts','quiz');
        $strtimeformat = get_string('strftimedatetime');
        
        if (!$quiz->questions) {
            print_heading($strnoattempts);
            return true;
        }
        
    /// Check to see if groups are being used in this quiz
        if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
            $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id&amp;mode=overview");
        } else {
            $currentgroup = false;
        }

    /// Get all users: teachers and students
        if ($currentgroup) {
            $users = get_group_users($currentgroup);
        }
        else {
            $users = get_course_users($course->id);
        }
        
    /// Remove teachers 
        $teachers = get_course_teachers($course->id);
        if(!empty($teachers)) {
            $keys = array_keys($teachers);
        }
        foreach($keys as $key) {
            unset($users[$key]);
        }

        if(empty($users)) {
            print_heading($strnoattempts);
            return true;
        }
        
        // set tTable and Analysis stats options
        if(!isset($SESSION->quiz_analysis_table)) {
            $SESSION->quiz_analysis_table = array('attemptselection' => 0, 'lowmarklimit' => 0);
        }

        foreach($SESSION->quiz_analysis_table as $option => $value) {
            $urlparam = optional_param($option, NULL);
            if($urlparam === NULL) {
                $$option = $value;
            }
            else {
                $$option = $SESSION->quiz_analysis_table[$option] = $urlparam;
            }
        }
      
        $scorelimit = $quiz->grade * $lowmarklimit/ 100;
        
        // ULPGC ecastro DEBUG this is here to allow for differnt SQL to select attempts
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

        $select = 'SELECT  qa.* '.$limit;
        $sql = 'FROM '.$CFG->prefix.'user u '.
               'LEFT JOIN '.$CFG->prefix.'quiz_attempts qa ON u.id = qa.userid '.
               'LEFT JOIN '.$CFG->prefix.'quiz_states qr ON qr.attempt = qa.id '.   // es posible 
               'WHERE u.id IN ('.implode(',', array_keys($users)).') AND ( qa.quiz = '.$quiz->id.') '. // ULPGC ecastro
               ' AND ( qa.sumgrades >= '.$scorelimit.' ) ';
                                                                                   // ^^^^^^ es posible seleccionar aquí TODOS los quizzes, como quiere Jussi,
                                                                                   // pero habría que llevar la cuenta ed cada quiz para restaura las preguntas (quizquestions, states)
        //$options = ' AND qa.sumgrades > '.$SESSION->quiz_analysis_table->minscore.' ';       
        /// Fetch the attempts
        $attempts = get_records_sql($select.$sql.$group);
        
        if(empty($attempts)) {
            print_heading($strnoattempts);
            $this->print_options_form($quiz, $cm, $attemptselection, $lowmarklimit);
            return true;
        }

    /// Here we rewiew all attempts and record data to construct the table
        unset($questions);
        unset($statstable);
        foreach ($attempts as $attempt) {
            //print_object($attempt);  // ULPGC ecastro debug eliminar
            // print "<br/>Layout= ".$attempt->layout."  <br/>";
            $questionlist = quiz_questions_in_quiz($attempt->layout);
            $sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
                   "  FROM {$CFG->prefix}quiz_questions q,".
                   "       {$CFG->prefix}quiz_question_instances i".
                   " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
                   "   AND q.id IN ($questionlist)";
                   
            if (!$quizquestions = get_records_sql($sql)) {
                error('No questions found');
            }
        
            // Load the question type specific information
            if (!quiz_get_question_options($quizquestions)) {
                error('Could not load question options');
            }
        
            // Restore the question sessions to their most recent states
            // creating new sessions where required
            if (!$states = quiz_restore_question_sessions($quizquestions, $quiz, $attempt)) {
                error('Could not restore question sessions');
            }
            $numbers = explode(',', $questionlist);
            unset($statsrow);
            foreach ($numbers as $i) {
                $accepted = array(SHORTANSWER, TRUEFALSE, MULTICHOICE, RANDOM, MATCH, NUMERICAL, CALCULATED);
                if (!in_array ($quizquestions[$i]->qtype, $accepted)){
                    continue;
                }
                $q = quiz_get_question_responses($quizquestions[$i], $states[$i]);
                $qid = $q->id;
                if (!isset($questions[$qid])) {
                    $questions[$qid]->id = $qid;
                    foreach ($q->responses as $answer => $r) {
                        $r->count = 0;
                        $questions[$qid]->responses[$answer] = $r->answer;
                        $questions[$qid]->counts[$answer] = 0;
                        $questions[$qid]->credits[$answer] = $r->credit;
                        $statsrow[$qid] = 0;
                    }                    
                }
                $responses = quiz_get_question_actual_response($quizquestions[$i], $states[$i]);
                foreach ($responses as $resp){
                    //echo "resp= ".$resp." <br/>"; //debug
                    //print_object($questions[$qid]->responses);
                    if ($key = array_search($resp, $questions[$qid]->responses)) {                 
                        $questions[$qid]->counts[$key]++;
                    } else {
                        $questions[$qid]->responses[] = $resp;
                        $questions[$qid]->counts[] = 1;
                        $test->responses[''] = $resp;
                        //echo "Question type= ".
                        if ($QUIZ_QTYPES[$quizquestions[$i]->qtype]->compare_responses($quizquestions[$i], $states[$i], $test)) {
                            $questions[$qid]->credits[] = 1;
                        } else {
                            $questions[$qid]->credits[] = 0;
                        }
                    }
                }
                //echo "terminado el foreach <br/>"; //debug
                $fraction = quiz_get_question_fraction_grade($quizquestions[$i], $states[$i]);
                $statsrow[$qid] += $fraction; 
            }
            $attemptscores[$attempt->id] = $attempt->sumgrades;   
            $statstable[$attempt->id] = $statsrow;
        } // Statistics Data table built
       
        $tablecolumns = array('type', 'question', 'responses', 'counts', 'facility', 'sd','discrimination_index', 'discrimination_coeff');
        $tableheaders = array("Q#", 'Question', '  Answers ', ' Counts ', 'Facility<br/> % Correct ', 'SD',' Disc.<br/>Index', 'Disc.<br/>Coeff.');

        $table = new flexible_table('mod-quiz-report-itemanalysis-report');

        $table->define_columns($tablecolumns);
        $table->define_headers($tableheaders);
        $table->define_baseurl($CFG->wwwroot.'/mod/quiz/report.php?q='.$quiz->id.'&mode=analysis');

        $table->sortable(true);
        $table->collapsible(true);
        $table->initialbars(true);
        
        //$table->column_class('number', 'numcol');
        $table->column_class('type', 'numcol');
        $table->column_class('facility', 'numcol');
        $table->column_class('sd', 'numcol'); 
        $table->column_class('discrimination_index', 'numcol');
        $table->column_class('discrimination_coeff', 'numcol');
        
        $table->set_attribute('cellspacing', '0');
        //$table->set_attribute('border', '1');
        $table->set_attribute('id', 'itemanalysis');
        $table->set_attribute('class', 'generaltable generalbox');
        //$table->set_attribute('align', 'center');
        
        // Start working -- this is necessary as soon as the niceties are over
        $table->setup();
    
        $top = max($attemptscores);
        $bottom = min($attemptscores);
        $gap = ($top - $bottom)/3;
        $top -=$gap;
        $bottom +=$gap;
        
        foreach ($questions as $q){
            $question = get_record('quiz_questions', 'id', $q->id);
            
            $qnumber = " (".link_to_popup_window('/mod/quiz/question.php?id='.$q->id,'editquestion', $q->id, 450, 550, get_string('edit'), 'none', true ).") ";
            $qname = '<div class="qname">'.format_text($question->name." :  ", $question->questiontextformat, NULL, $quiz->course).'</div>';
            $qicon = quiz_print_question_icon($question, false, true);
            $qreview = quiz_get_question_review($quiz, $question);
            $qtext = format_text($question->questiontext, $question->questiontextformat, NULL, $quiz->course);
            
            $qquestion = $qname."<br/>\n".$qtext."\n";
            
            $qstats = $this->report_question_stats($attemptscores, $statstable, $q->id, $top, $bottom);
            
            $responses = "";
            $counts = "";
            foreach ($q->responses as $aid=>$resp){
                $credit = " (".format_float($q->credits[$aid],2).") ";
                $text = format_text("$resp"."$credit", FORMAT_MOODLE, NULL, $quiz->course);
                if ($q->credits[$aid] <= 0) {
                    $qclass = 'uncorrect';
                } elseif ($q->credits[$aid] == 1) {
                    $qclass = 'correct';
                } else {
                    $qclass = 'partialcorrect';
                }
                $responses .= str_replace("<p>", "\n".'<p class="'.$qclass.'">', $text); 
                $count = $q->counts[$aid].'/'.$qstats->count.' ('.format_float($q->counts[$aid]/$qstats->count*100,0).'%) ';
                $text = format_text("$count", FORMAT_MOODLE, NULL, $quiz->course);
                $counts .= str_replace("<p>", "\n".'<p class="'.$qclass.'">', $text); 
            }
            
            $facility = format_float($qstats->facility*100,0)." %";
            $qsd = format_float($qstats->qsd,3);
            $di = format_float($qstats->disc_index,2);
            $dc = format_float($qstats->disc_coeff,2);
            
            //$table->add_data(array($qnumber."<br/>\n".$qicon."<br>\n ".$qreview, $qquestion, $responses, $counts, $facility, $qsd, $di, $dc));
            $table->add_data(array($qnumber."\n<p>".$qicon."<p/>\n ".$qreview, $qquestion, $responses, $counts, $facility, $qsd, $di, $dc));
        }
        
        echo '<div id="titlecontainer" class="quiz-report-title">';
        echo get_string("analysistitle", "quiz");
        helpbutton("itemanalysis", get_string("reportanalysis","quiz"), "quiz");
        echo '</div>';

        echo '<div id="tablecontainer">';
        $table->print_html();

        $this->print_options_form($quiz, $cm, $attemptselection, $lowmarklimit);

        return true;
    }


    function print_options_form($quiz, $cm, $attempts, $lowlimit) {
        echo '<div class="controls">';
        echo '<form method="report.php">';
        echo '<p>'.get_string('analysisoptions', 'quiz').': ';
        echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
        echo '<input type="hidden" name="q" value="'.$quiz->id.'" />';
        echo '<input type="hidden" name="mode" value="analysis" />';
        echo '<label for="attemptselection">'.get_string('attemptselection', 'quiz').'</label>';
        $options = array ( QUIZ_ALLATTEMPTS     => get_string("allattempts", "quiz"),
                           QUIZ_HIGHESTATTEMPT => get_string("gradehighest", "quiz"),
                           QUIZ_FIRSTATTEMPT => get_string("attemptfirst", "quiz"),
                           QUIZ_LASTATTEMPT  => get_string("attemptlast", "quiz"));
        choose_from_menu($options, "attemptselection", "$attempts", "");
        
        echo '<label for="lowmarklimit">'.get_string('lowmarkslimit', 'quiz').'</label> <input type="text" id="lowmarklimit" name="lowmarklimit" size="1" value="'.$lowlimit.'" /> % ';
        echo '<input type="submit" value="'.get_string('go').'" />';
        helpbutton("analysisoptions", get_string("analysisoptions","quiz"), "quiz");
        echo '</p>';
        echo '</form>';
        echo '</div>';    
    }



    function report_question_stats(&$attemptscores, &$questionscores, $qid, $top, $bottom){
        unset($qstats);
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
        $sumx = array_reduce($qstats, "stats_sumx");
        $sumg = $sumx[0];
        $sumq = $sumx[1];
        $sumx2 = array_reduce($qstats, "stats_sumx2");
        $sumg2 = $sumx2[0];
        $sumq2 = $sumx2[1];
        $sumxy = array_reduce($qstats, "stats_sumxy");
        $sumgq = $sumxy[0];
        
        $result->count = $n;
        $result->facility = $sumq/$n;
        if ($n<2) {
            $result->qsd = sqrt(($sumq2 - $sumq*$sumq/$n)/($n));
        } else {
            $result->qsd = sqrt(($sumq2 - $sumq*$sumq/$n)/($n-1));
        }
        $result->disc_index = ($top_scores - $bottom_scores)/max($top_count, $bottom_count);
        $gsd = sqrt(($sumg2 - $sumg*$sumg/$n)/($n-1));
        $div = $n*$gsd*$result->qsd;
        if ($div!=0) {
            $result->disc_coeff = ($sumgq - $sumg*$sumq/$n)/($n*$gsd*$result->qsd);
        } else {
            $result->disc_coeff = -999;
        }
        return $result;
    }

    
}

?>
