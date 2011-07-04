<?php
/**
 * This script calculates various statistics about student attempts
 *
 * @author Martin Dougiamas, Jamie Pratt, Tim Hunt and others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 **/

define('QUIZ_REPORT_TIME_TO_CACHE_STATS', MINSECS * 15);
require_once($CFG->dirroot.'/mod/quiz/report/statistics/statistics_form.php');
require_once($CFG->dirroot.'/mod/quiz/report/statistics/statistics_table.php');

class quiz_statistics_report extends quiz_default_report {

    /**
     * @var object instance of table class used for main questions stats table.
     */
    var $table;

    /**
     * Display the report.
     */
    function display($quiz, $cm, $course) {
        global $CFG, $DB, $QTYPES, $OUTPUT, $PAGE;

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        $download = optional_param('download', '', PARAM_ALPHA);
        $everything = optional_param('everything', 0, PARAM_BOOL);
        $recalculate = optional_param('recalculate', 0, PARAM_BOOL);
        //pass the question id for detailed analysis question
        $qid = optional_param('qid', 0, PARAM_INT);
        $pageoptions = array();
        $pageoptions['id'] = $cm->id;
        $pageoptions['mode'] = 'statistics';
        if ($qid) {
            $pageoptions['qid'] = $qid;
        }

        $questions = quiz_report_load_questions($quiz);
        // Load the question type specific information
        if (!get_question_options($questions)) {
            print_error('cannotloadquestion', 'question');
        }

        $reporturl = new moodle_url('/mod/quiz/report.php', $pageoptions);

        $mform = new mod_quiz_report_statistics($reporturl);
        if ($fromform = $mform->get_data()){
            $useallattempts = $fromform->useallattempts;
            if ($fromform->useallattempts){
                set_user_preference('quiz_report_statistics_useallattempts', $fromform->useallattempts);
            } else {
                unset_user_preference('quiz_report_statistics_useallattempts');
            }
        } else {
            $useallattempts = get_user_preferences('quiz_report_statistics_useallattempts', 0);
        }

        /// find out current groups mode
        $currentgroup = groups_get_activity_group($cm, true);

        $nostudentsingroup = false;//true if a group is selected and their is noeone in it.
        if (!empty($currentgroup)) {
            // all users who can attempt quizzes and who are in the currently selected group
            $groupstudents = get_users_by_capability($context, array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'),'','','','',$currentgroup,'',false);
            if (!$groupstudents){
                $nostudentsingroup = true;
            }
        } else {
            $groupstudents = array();
        }

        if ($recalculate && confirm_sesskey()) {
            if ($todelete = $DB->get_records_menu('quiz_statistics', array('quizid' => $quiz->id, 'groupid'=> (int)$currentgroup, 'allattempts'=>$useallattempts))){
                list($todeletesql, $todeleteparams) = $DB->get_in_or_equal(array_keys($todelete));
                if (!$DB->delete_records_select('quiz_statistics', "id $todeletesql", $todeleteparams)){
                    print_error('errordeleting', 'quiz_statistics', '', 'quiz_statistics');
                }
                if (!$DB->delete_records_select('quiz_question_statistics', "quizstatisticsid $todeletesql", $todeleteparams)){
                    print_error('errordeleting', 'quiz_statistics', '', 'quiz_question_statistics');
                }
                if (!$DB->delete_records_select('quiz_question_response_stats', "quizstatisticsid $todeletesql", $todeleteparams)){
                    print_error('errordeleting', 'quiz_statistics', '', 'quiz_question_response_stats');
                }
            }
            redirect($reporturl);
        }

        $this->table = new quiz_report_statistics_table();
        $filename = "$course->shortname-".format_string($quiz->name,true);
        $this->table->is_downloading($download, $filename, get_string('quizstructureanalysis', 'quiz_statistics'));

        list($quizstats, $questions, $subquestions, $s, $usingattemptsstring)
            = $this->quiz_questions_stats($quiz, $currentgroup, $nostudentsingroup,
                                        $useallattempts, $groupstudents, $questions);

        if ($s) {
            $this->table->setup($quiz, $cm->id, $reporturl, $s);
        }

        if (!$qid) {//main page
            if (!$this->table->is_downloading()) {
                // Only print headers if not asked to download data
                $this->print_header_and_tabs($cm, $course, $quiz, 'statistics');
            }
            if (!$this->table->is_downloading()) {
                // Print display options
                $mform->set_data(array('useallattempts' => $useallattempts));
                $mform->display();
            }
            if (!$this->table->is_downloading() && $s == 0){
                echo $OUTPUT->heading(get_string('noattempts','quiz'));
            }
            if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used
                if (!$this->table->is_downloading()) {
                    groups_print_activity_menu($cm, $reporturl->out());
                    echo '<br />';
                    if ($currentgroup && !$groupstudents){
                        echo $OUTPUT->notification(get_string('nostudentsingroup', 'quiz_statistics'));
                    }
                }
            }

            $this->output_quiz_info_table($course, $cm, $quiz, $quizstats, $usingattemptsstring, $currentgroup, $groupstudents, $useallattempts, $download, $reporturl, $everything);
            $this->output_quiz_structure_analysis_table($s, $questions, $subquestions);
            if (!$this->table->is_downloading() || ($everything && $this->table->is_downloading() == 'xhtml')){
                if ($s > 1){
                    $imageurl = $CFG->wwwroot.'/mod/quiz/report/statistics/statistics_graph.php?id='.$quizstats->id;
                    echo $OUTPUT->heading(get_string('statisticsreportgraph', 'quiz_statistics'));
                    echo '<div class="graph flexible-wrap"><img src="'.$imageurl.'" alt="'.get_string('statisticsreportgraph', 'quiz_statistics').'" /></div>';
                }
            }
            if ($this->table->is_downloading()){
                if ($everything){
                    foreach ($questions as $question){
                        if ($question->qtype != 'random' && $QTYPES[$question->qtype]->show_analysis_of_responses()){
                            $this->output_individual_question_data($quiz, $question, $reporturl, $quizstats);
                        } elseif (!empty($question->_stats->subquestions)) {
                            $subitemstodisplay = explode(',', $question->_stats->subquestions);
                            foreach ($subitemstodisplay as $subitemid){
                                $this->output_individual_question_data($quiz, $subquestions[$subitemid], $reporturl, $quizstats);
                            }
                        }
                    }
                    $exportclassinstance =& $this->table->export_class_instance();
                } else {
                    $this->table->finish_output();
                }
            }
            if ($this->table->is_downloading() && $everything){
                $exportclassinstance->finish_document();
            }
        } else {//individual question page
            $thisquestion = false;
            if (isset($questions[$qid])){
                $thisquestion = $questions[$qid];
            } else if (isset($subquestions[$qid])){
                $thisquestion = $subquestions[$qid];
            } else {
                print_error('questiondoesnotexist', 'question');
            }
            if (!$this->table->is_downloading()) {
                // Only print headers if not asked to download data
                navigation_node::override_active_url(
                        new moodle_url('/mod/quiz/report.php', array('id' => $cm->id, 'mode' => 'statistics')));
                $PAGE->navbar->add($thisquestion->name);
                $this->print_header_and_tabs($cm, $course, $quiz, 'statistics');
            }
            $this->output_individual_question_data($quiz, $thisquestion, $reporturl, $quizstats);
        }
        return true;
    }

    function sort_response_details($detail1, $detail2){
        if ($detail1->credit == $detail2->credit){
            return strcmp($detail1->answer, $detail2->answer);
        }
        return ($detail1->credit > $detail2->credit) ? -1 : 1;
    }
    function sort_answers($answer1, $answer2){
        if ($answer1->rcount == $answer2->rcount){
            return strcmp($answer1->response, $answer2->response);
        } else {
            return ($answer1->rcount > $answer2->rcount)? -1 : 1;
        }
    }

    function output_individual_question_data($quiz, $question, $reporturl, $quizstats){
        global $CFG, $DB, $QTYPES, $OUTPUT;
        require_once($CFG->dirroot.'/mod/quiz/report/statistics/statistics_question_table.php');
        $this->qtable = new quiz_report_statistics_question_table($question->id);
        $downloadtype = $this->table->is_downloading();
        if (!$this->table->is_downloading()){
            $datumfromtable = $this->table->format_row($question);

            $questioninfotable = new html_table();
            $questioninfotable->align = array('center', 'center');
            $questioninfotable->width = '60%';
            $questioninfotable->attributes['class'] = 'generaltable titlesleft';

            $questioninfotable->data = array();
            $questioninfotable->data[] = array(get_string('modulename', 'quiz'), $quiz->name);
            $questioninfotable->data[] = array(get_string('questionname', 'quiz_statistics'), $question->name.'&nbsp;'.$datumfromtable['actions']);
            $questioninfotable->data[] = array(get_string('questiontype', 'quiz_statistics'), $datumfromtable['icon'].'&nbsp;'.get_string($question->qtype,'quiz').'&nbsp;'.$datumfromtable['icon']);
            $questioninfotable->data[] = array(get_string('positions', 'quiz_statistics'), $question->_stats->positions);

            $questionstatstable = new html_table();
            $questionstatstable->align = array('center', 'center');
            $questionstatstable->width = '60%';
            $questionstatstable->attributes['class'] = 'generaltable titlesleft';

            unset($datumfromtable['number']);
            unset($datumfromtable['icon']);
            $actions = $datumfromtable['actions'];
            unset($datumfromtable['actions']);
            unset($datumfromtable['name']);
            $labels = array('s' => get_string('attempts', 'quiz_statistics'),
                            'facility' => get_string('facility', 'quiz_statistics'),
                            'sd' => get_string('standarddeviationq', 'quiz_statistics'),
                            'random_guess_score' => get_string('random_guess_score', 'quiz_statistics'),
                            'intended_weight'=> get_string('intended_weight', 'quiz_statistics'),
                            'effective_weight'=> get_string('effective_weight', 'quiz_statistics'),
                            'discrimination_index'=> get_string('discrimination_index', 'quiz_statistics'),
                            'discriminative_efficiency'=> get_string('discriminative_efficiency', 'quiz_statistics'));
            foreach ($datumfromtable as $item => $value){
                $questionstatstable->data[] = array($labels[$item], $value);
            }
            echo $OUTPUT->heading(get_string('questioninformation', 'quiz_statistics'));
            echo html_writer::table($questioninfotable);

            echo $OUTPUT->box(format_text($question->questiontext, $question->questiontextformat, array('overflowdiv'=>true)).$actions, 'boxaligncenter generalbox boxwidthnormal mdl-align');

            echo $OUTPUT->heading(get_string('questionstatistics', 'quiz_statistics'));
            echo html_writer::table($questionstatstable);

        } else {
            $this->qtable->export_class_instance($this->table->export_class_instance());
            $questiontabletitle = !empty($question->number)?'('.$question->number.') ':'';
            $questiontabletitle .= "\"{$question->name}\"";
            $questiontabletitle = "<em>$questiontabletitle</em>";
            if ($downloadtype == 'xhtml'){
                $questiontabletitle = get_string('analysisofresponsesfor', 'quiz_statistics', $questiontabletitle);
            }
            $exportclass =& $this->table->export_class_instance();
            $exportclass->start_table($questiontabletitle);
        }
        if ($QTYPES[$question->qtype]->show_analysis_of_responses()){
            if (!$this->table->is_downloading()){
                echo $OUTPUT->heading(get_string('analysisofresponses', 'quiz_statistics'));
            }
            $teacherresponses = $QTYPES[$question->qtype]->get_possible_responses($question);
            $this->qtable->setup($reporturl, $question, count($teacherresponses)>1);
            if ($this->table->is_downloading()){
                $exportclass->output_headers($this->qtable->headers);
            }

            $responses = $DB->get_records('quiz_question_response_stats', array('quizstatisticsid' => $quizstats->id, 'questionid' => $question->id), 'credit DESC, subqid ASC, aid ASC, rcount DESC');
            $responses = quiz_report_index_by_keys($responses, array('subqid', 'aid'), false);
            foreach ($responses as $subqid => $response){
                foreach (array_keys($responses[$subqid]) as $aid){
                    uasort($responses[$subqid][$aid], array('quiz_statistics_report', 'sort_answers'));
                }
                if (isset($responses[$subqid]['0'])){
                    $wildcardresponse = new stdClass();
                    $wildcardresponse->answer = '*';
                    $wildcardresponse->credit = 0;
                    $teacherresponses[$subqid][0] = $wildcardresponse;
                }
            }
            $first = true;
            $subq = 0;
            foreach ($teacherresponses as $subqid => $tresponsesforsubq){
                $subq++;
                $qhaswildcards = $QTYPES[$question->qtype]->has_wildcards_in_responses($question, $subqid);
                if (!$first){
                    $this->qtable->add_separator();
                }
                uasort($tresponsesforsubq, array('quiz_statistics_report', 'sort_response_details'));
                foreach ($tresponsesforsubq as $aid => $teacherresponse){
                    $teacherresponserow = new stdClass();
                    $teacherresponserow->response = $teacherresponse->answer;
                    $teacherresponserow->indent = '';
                    $teacherresponserow->rcount = 0;
                    $teacherresponserow->subq = $subq;
                    $teacherresponserow->credit = $teacherresponse->credit;
                    if (isset($responses[$subqid][$aid])){
                        $singleanswer = count($responses[$subqid][$aid])==1 &&
                                        ($responses[$subqid][$aid][0]->response == $teacherresponserow->response);
                        if (!$singleanswer && $qhaswildcards){
                            $this->qtable->add_separator();
                        }
                        foreach ($responses[$subqid][$aid] as $response){
                            $teacherresponserow->rcount += $response->rcount;
                        }
                        if ($aid!=0 || $qhaswildcards){
                            $this->qtable->add_data_keyed($this->qtable->format_row($teacherresponserow));
                        }
                        if (!$singleanswer){
                            foreach ($responses[$subqid][$aid] as $response){
                                if (!$downloadtype || $downloadtype=='xhtml'){
                                    $indent = '&nbsp;&nbsp;&nbsp;&nbsp;';
                                } else {
                                    $indent = '    ';
                                }
                                $response->response = $response->response;
                                $response->indent = $qhaswildcards ? $indent : '';
                                $response->subq = $subq;
                                if ((count($responses[$subqid][$aid])<2) || ($response->rcount > ($teacherresponserow->rcount / 10))){
                                    $this->qtable->add_data_keyed($this->qtable->format_row($response));
                                }
                            }
                        }
                    } else {
                        $this->qtable->add_data_keyed($this->qtable->format_row($teacherresponserow));
                    }
                }
                $first = false;
            }
            $this->qtable->finish_output(!$this->table->is_downloading());
        }
        if (!$this->table->is_downloading()){
            $url = $reporturl->out();
            $text = get_string('backtoquizreport', 'quiz_statistics');
            echo $OUTPUT->box("<a href=\"$url\">$text</a>", 'boxaligncenter generalbox boxwidthnormal mdl-align');
        }
    }

    function output_quiz_structure_analysis_table($s, $questions, $subquestions){
        global $OUTPUT;
        if ($s){
            if (!$this->table->is_downloading()){
                echo $OUTPUT->heading(get_string('quizstructureanalysis', 'quiz_statistics'));
            }
            foreach ($questions as $question){
                $this->table->add_data_keyed($this->table->format_row($question));
                if (!empty($question->_stats->subquestions)){
                    $subitemstodisplay = explode(',', $question->_stats->subquestions);
                    foreach ($subitemstodisplay as $subitemid){
                        $subquestions[$subitemid]->maxgrade = $question->maxgrade;
                        $this->table->add_data_keyed($this->table->format_row($subquestions[$subitemid]));
                    }
                }
            }

            $this->table->finish_output(!$this->table->is_downloading());
        }
    }

    function output_quiz_info_table($course, $cm, $quiz, $quizstats, $usingattemptsstring,
                    $currentgroup, $groupstudents, $useallattempts, $download, $reporturl, $everything){
        global $DB, $OUTPUT;
        // Print information on the number of existing attempts
        $quizinformationtablehtml = $OUTPUT->heading(get_string('quizinformation', 'quiz_statistics'), 2, 'main');
        $quizinformationtable = new html_table();
        $quizinformationtable->align = array('center', 'center');
        $quizinformationtable->width = '60%';
        $quizinformationtable->attributes['class'] = 'generaltable titlesleft boxaligncenter';
        $quizinformationtable->data = array();
        $quizinformationtable->data[] = array(get_string('quizname', 'quiz_statistics'), $quiz->name);
        $quizinformationtable->data[] = array(get_string('coursename', 'quiz_statistics'), $course->fullname);
        if ($cm->idnumber){
            $quizinformationtable->data[] = array(get_string('idnumbermod'), $cm->idnumber);
        }
        if ($quiz->timeopen){
            $quizinformationtable->data[] = array(get_string('quizopen', 'quiz'), userdate($quiz->timeopen));
        }
        if ($quiz->timeclose){
            $quizinformationtable->data[] = array(get_string('quizclose', 'quiz'), userdate($quiz->timeclose));
        }
        if ($quiz->timeopen && $quiz->timeclose){
            $quizinformationtable->data[] = array(get_string('duration', 'quiz_statistics'), format_time($quiz->timeclose - $quiz->timeopen));
        }
        $format = array('firstattemptscount' => '',
                    'allattemptscount' => '',
                    'firstattemptsavg' => 'sumgrades_as_percentage',
                    'allattemptsavg' => 'sumgrades_as_percentage',
                    'median' => 'sumgrades_as_percentage',
                    'standarddeviation' => 'sumgrades_as_percentage',
                    'skewness' => '',
                    'kurtosis' => '',
                    'cic' => 'number_format',
                    'errorratio' => 'number_format',
                    'standarderror' => 'sumgrades_as_percentage');
        foreach ($quizstats as $property => $value){
            if (!isset($format[$property])){
                continue;
            }
            if (!is_null($value)){
                switch ($format[$property]){
                    case 'sumgrades_as_percentage' :
                        $formattedvalue = quiz_report_scale_sumgrades_as_percentage($value, $quiz);
                        break;
                    case 'number_format' :
                        $formattedvalue = quiz_format_grade($quiz, $value).'%';
                        break;
                    default :
                        $formattedvalue = $value;
                }
                $quizinformationtable->data[] = array(get_string($property, 'quiz_statistics', $usingattemptsstring), $formattedvalue);
            }
        }
        if (!$this->table->is_downloading()){
            if (isset($quizstats->timemodified)){
                list($fromqa, $whereqa, $qaparams) = quiz_report_attempts_sql($quiz->id, $currentgroup, $groupstudents, $useallattempts);
                $sql = 'SELECT COUNT(1) ' .
                    'FROM ' .$fromqa.' '.
                    'WHERE ' .$whereqa.' AND qa.timefinish > :time';
                $a = new stdClass();
                $a->lastcalculated = format_time(time() - $quizstats->timemodified);
                if (!$a->count = $DB->count_records_sql($sql, array('time'=>$quizstats->timemodified)+$qaparams)){
                    $a->count = 0;
                }
                $quizinformationtablehtml .= $OUTPUT->box_start('boxaligncenter generalbox boxwidthnormal mdl-align', 'cachingnotice');
                $quizinformationtablehtml .= get_string('lastcalculated', 'quiz_statistics', $a);
                $aurl = new moodle_url($reporturl->out_omit_querystring(), $reporturl->params() + array('recalculate' => 1, 'sesskey' => sesskey()));
                $quizinformationtablehtml .= $OUTPUT->single_button($aurl, get_string('recalculatenow', 'quiz_statistics'));
                $quizinformationtablehtml .= $OUTPUT->box_end();
            }
            $downloadoptions = $this->table->get_download_menu();
            $quizinformationtablehtml .= '<form action="'. $this->table->baseurl .'" method="post">';
            $quizinformationtablehtml .= '<div class="mdl-align">';
            $quizinformationtablehtml .= '<input type="hidden" name="everything" value="1"/>';
            $quizinformationtablehtml .= '<input type="submit" value="'.get_string('downloadeverything', 'quiz_statistics').'"/>';
            $quizinformationtablehtml .= html_writer::select($downloadoptions, 'download', $this->table->defaultdownloadformat, false);
            $quizinformationtablehtml .= '</div></form>';
        }
        $quizinformationtablehtml .= html_writer::table($quizinformationtable);
        if (!$this->table->is_downloading()){
            echo $quizinformationtablehtml;
        } elseif ($everything) {
            $exportclass =& $this->table->export_class_instance();
            if ($download == 'xhtml'){
                echo $quizinformationtablehtml;
            } else {
                $exportclass->start_table(get_string('quizinformation', 'quiz_statistics'));
                $headers = array();
                $row = array();
                foreach ($quizinformationtable->data as $data){
                    $headers[]= $data[0];
                    $row[] = $data[1];
                }
                $exportclass->output_headers($headers);
                $exportclass->add_data($row);
                $exportclass->finish_table();
            }
        }
    }

    function quiz_stats($nostudentsingroup, $quizid, $currentgroup, $groupstudents, $questions, $useallattempts){
        global $CFG, $DB;
        if (!$nostudentsingroup){
            //Calculating_MEAN_of_grades_for_all_attempts_by_students
            //http://docs.moodle.org/dev/Quiz_item_analysis_calculations_in_practise#Calculating_MEAN_of_grades_for_all_attempts_by_students

            list($fromqa, $whereqa, $qaparams) = quiz_report_attempts_sql($quizid, $currentgroup, $groupstudents);

            $sql = 'SELECT (CASE WHEN attempt=1 THEN 1 ELSE 0 END) AS isfirst, COUNT(1) AS countrecs, SUM(sumgrades) AS total ' .
                    'FROM '.$fromqa.
                    'WHERE ' .$whereqa.
                    'GROUP BY (attempt=1)';

            if (!$attempttotals = $DB->get_records_sql($sql, $qaparams)){
                $s = 0;
                $usingattemptsstring = '';
            } else {
                $firstattempt = $attempttotals[1];
                $allattempts = new stdClass();
                $allattempts->countrecs = $firstattempt->countrecs +
                                (isset($attempttotals[0])?$attempttotals[0]->countrecs:0);
                $allattempts->total = $firstattempt->total +
                                (isset($attempttotals[0])?$attempttotals[0]->total:0);
                if ($useallattempts){
                    $usingattempts = $allattempts;
                    $usingattempts->attempts = get_string('allattempts', 'quiz_statistics');
                    $usingattempts->sql = '';
                } else {
                    $usingattempts = $firstattempt;
                    $usingattempts->attempts = get_string('firstattempts', 'quiz_statistics');
                    $usingattempts->sql = 'AND qa.attempt=1 ';
                }
                $usingattemptsstring = $usingattempts->attempts;
                $s = $usingattempts->countrecs;
                $sumgradesavg = $usingattempts->total / $usingattempts->countrecs;
            }
        } else {
            $s = 0;
        }
        $quizstats = new stdClass();
        if ($s == 0){
            $quizstats->firstattemptscount = 0;
            $quizstats->allattemptscount = 0;
        } else {
            $quizstats->firstattemptscount = $firstattempt->countrecs;
            $quizstats->allattemptscount = $allattempts->countrecs;
            $quizstats->firstattemptsavg = $firstattempt->total / $firstattempt->countrecs;
            $quizstats->allattemptsavg = $allattempts->total / $allattempts->countrecs;
        }
        //recalculate sql again this time possibly including test for first attempt.
        list($fromqa, $whereqa, $qaparams) = quiz_report_attempts_sql($quizid, $currentgroup, $groupstudents, $useallattempts);

        //get the median
        if ($s) {

            if (($s%2)==0){
                //even number of attempts
                $limitoffset = ($s/2) - 1;
                $limit = 2;
            } else {
                $limitoffset = (floor($s/2));
                $limit = 1;
            }
            $sql = 'SELECT id, sumgrades ' .
                'FROM ' .$fromqa.
                'WHERE ' .$whereqa.
                'ORDER BY sumgrades';
            if (!$mediangrades = $DB->get_records_sql_menu($sql, $qaparams, $limitoffset, $limit)){
                print_error('errormedian', 'quiz_statistics');
            }
            $quizstats->median = array_sum($mediangrades) / count($mediangrades);
            if ($s>1){
                //fetch sum of squared, cubed and power 4d
                //differences between grades and mean grade
                $mean = $usingattempts->total / $s;
                $sql = "SELECT " .
                    "SUM(POWER((qa.sumgrades - :mean1),2)) AS power2, " .
                    "SUM(POWER((qa.sumgrades - :mean2),3)) AS power3, ".
                    "SUM(POWER((qa.sumgrades - :mean3),4)) AS power4 ".
                    'FROM ' .$fromqa.
                    'WHERE ' .$whereqa;
                $params = array('mean1' => $mean, 'mean2' => $mean, 'mean3' => $mean)+$qaparams;
                if (!$powers = $DB->get_record_sql($sql, $params)){
                    print_error('errorpowers', 'quiz_statistics');
                }

                //Standard_Deviation
                //see http://docs.moodle.org/dev/Quiz_item_analysis_calculations_in_practise#Standard_Deviation

                $quizstats->standarddeviation = sqrt($powers->power2 / ($s -1));



                //Skewness_and_Kurtosis
                if ($s>2){
                    //see http://docs.moodle.org/dev/Quiz_item_analysis_calculations_in_practise#Skewness_and_Kurtosis
                    $m2= $powers->power2 / $s;
                    $m3= $powers->power3 / $s;
                    $m4= $powers->power4 / $s;

                    $k2= $s*$m2/($s-1);
                    $k3= $s*$s*$m3/(($s-1)*($s-2));
                    if ($k2){
                        $quizstats->skewness = $k3 / (pow($k2, 3/2));
                    }
                }


                if ($s>3){
                    $k4= $s*$s*((($s+1)*$m4)-(3*($s-1)*$m2*$m2))/(($s-1)*($s-2)*($s-3));
                    if ($k2){
                        $quizstats->kurtosis = $k4 / ($k2*$k2);
                    }
                }
            }
        }
        if ($s){
            require_once("$CFG->dirroot/mod/quiz/report/statistics/qstats.php");
            $qstats = new qstats($questions, $s, $sumgradesavg);
            $qstats->get_records($quizid, $currentgroup, $groupstudents, $useallattempts);
            $qstats->process_states();
            $qstats->process_responses();
        } else {
            $qstats = false;
        }
        if ($s>1){
            $p = count($qstats->questions);//no of positions
            if ($p > 1){
                if (isset($k2)){
                    $quizstats->cic = (100 * $p / ($p -1)) * (1 - ($qstats->sum_of_grade_variance())/$k2);
                    $quizstats->errorratio = 100 * sqrt(1-($quizstats->cic/100));
                    $quizstats->standarderror = ($quizstats->errorratio * $quizstats->standarddeviation / 100);
                }
            }
        }
        return array($s, $usingattemptsstring, $quizstats, $qstats);
    }

    function quiz_questions_stats($quiz, $currentgroup, $nostudentsingroup, $useallattempts, $groupstudents, $questions){
        global $DB;
        $timemodified = time() - QUIZ_REPORT_TIME_TO_CACHE_STATS;
        $params = array('quizid'=>$quiz->id, 'groupid'=>(int)$currentgroup, 'allattempts'=>$useallattempts, 'timemodified'=>$timemodified);
        if (!$quizstats = $DB->get_record_select('quiz_statistics', 'quizid = :quizid  AND groupid = :groupid AND allattempts = :allattempts AND timemodified > :timemodified', $params, '*', true)){
            list($s, $usingattemptsstring, $quizstats, $qstats) = $this->quiz_stats($nostudentsingroup, $quiz->id, $currentgroup, $groupstudents, $questions, $useallattempts);
            if ($s){
                $toinsert = (object)((array)$quizstats + $params);
                if (isset($toinsert->errorratio) && is_nan($toinsert->errorratio)) {
                    $toinsert->errorratio = NULL;
                }
                if (isset($toinsert->standarderror) && is_nan($toinsert->standarderror)) {
                    $toinsert->standarderror = NULL;
                }
                $toinsert->timemodified = time();
                $quizstats->id = $DB->insert_record('quiz_statistics', $toinsert);
                foreach ($qstats->questions as $question){
                    $question->_stats->quizstatisticsid = $quizstats->id;
                    $DB->insert_record('quiz_question_statistics', $question->_stats, false, true);
                }
                foreach ($qstats->subquestions as $subquestion){
                    $subquestion->_stats->quizstatisticsid = $quizstats->id;
                    $DB->insert_record('quiz_question_statistics', $subquestion->_stats, false, true);
                }
                foreach ($qstats->responses as $response){
                    $response->quizstatisticsid = $quizstats->id;
                    $DB->insert_record('quiz_question_response_stats', $response, false);
                }
            }
            if ($qstats){
                $questions = $qstats->questions;
                $subquestions = $qstats->subquestions;
            } else {
                $questions = array();
                $subquestions = array();
            }
        } else {
            //use cached results
            if ($useallattempts){
                $usingattemptsstring = get_string('allattempts', 'quiz_statistics');
                $s = $quizstats->allattemptscount;
            } else {
                $usingattemptsstring = get_string('firstattempts', 'quiz_statistics');
                $s = $quizstats->firstattemptscount;
            }
            $subquestions = array();
            $questionstats = $DB->get_records('quiz_question_statistics', array('quizstatisticsid'=>$quizstats->id), 'subquestion ASC');
            $questionstats = quiz_report_index_by_keys($questionstats, array('subquestion', 'questionid'));
            if (1 < count($questionstats)){
                list($mainquestionstats, $subquestionstats) = $questionstats;
                $subqstofetch = array_keys($subquestionstats);
                $subquestions = question_load_questions($subqstofetch);
                foreach (array_keys($subquestions) as $subqid){
                    $subquestions[$subqid]->_stats = $subquestionstats[$subqid];
                }
            } elseif (count($questionstats)) {
                $mainquestionstats = $questionstats[0];
            }
            if (count($questionstats)) {
                foreach (array_keys($questions) as $qid){
                    $questions[$qid]->_stats = $mainquestionstats[$qid];
                }
            }
        }
        return array($quizstats, $questions, $subquestions, $s, $usingattemptsstring);
    }
}
function quiz_report_attempts_sql($quizid, $currentgroup, $groupstudents, $allattempts = true){
    global $DB;
    $fromqa = '{quiz_attempts} qa ';
    $whereqa = 'qa.quiz = :quizid AND qa.preview=0 AND qa.timefinish !=0 ';
    $qaparams = array('quizid'=>$quizid);
    if (!empty($currentgroup) && $groupstudents) {
        list($grpsql, $grpparams) = $DB->get_in_or_equal(array_keys($groupstudents), SQL_PARAMS_NAMED, 'u');
        $whereqa .= 'AND qa.userid '.$grpsql.' ';
        $qaparams += $grpparams;
    }
    if (!$allattempts){
        $whereqa .= 'AND qa.attempt=1 ';
    }
    return array($fromqa, $whereqa, $qaparams);
}


