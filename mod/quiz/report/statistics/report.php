<?php
/**
 * This script calculates various statistics about student attempts
 *
 * @version $Id$
 * @author Martin Dougiamas, Jamie Pratt, Tim Hunt and others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 **/

define('QUIZ_REPORT_TIME_TO_CACHE_STATS', MINSECS * 15);
require_once($CFG->dirroot.'/mod/quiz/report/statistics/statistics_form.php');
require_once($CFG->dirroot.'/mod/quiz/report/statistics/statistics_table.php');

class quiz_statistics_report extends quiz_default_report {

    /**
     * Display the report.
     */
    function display($quiz, $cm, $course) {
        global $CFG, $DB;

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        $download = optional_param('download', '', PARAM_ALPHA);
        $recalculate = optional_param('recalculate', 0, PARAM_BOOL);
        $pageoptions = array();
        $pageoptions['id'] = $cm->id;
        $pageoptions['q'] = $quiz->id;
        $pageoptions['mode'] = 'statistics';
        
        $questions = quiz_report_load_questions($quiz);
        // Load the question type specific information
        if (!get_question_options($questions)) {
            print_error('cannotloadquestion', 'question');
        }

        
        $reporturl = new moodle_url($CFG->wwwroot.'/mod/quiz/report.php', $pageoptions);

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
            $groupstudents = get_users_by_capability($context, 'mod/quiz:attempt','','','','',$currentgroup,'',false);
            if (!$groupstudents){
                $nostudentsingroup = true;
            }
        } else {
            $groupstudents = array();
        }

        
        $table = new quiz_report_statistics_table();
        $table->is_downloading($download, get_string('reportstatistics','quiz_statistics'),
                    "$course->shortname ".format_string($quiz->name,true));
        if (!$table->is_downloading()) {
            // Only print headers if not asked to download data
            $this->print_header_and_tabs($cm, $course, $quiz, "statistics");
        }

        if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used
            if (!$table->is_downloading()) {
                groups_print_activity_menu($cm, $reporturl->out());
                if ($currentgroup && !$groupstudents){
                    notify(get_string('nostudentsingroup', 'quiz_statistics'));
                }
            }
        }

        if (!$table->is_downloading()) {
            // Print display options
            $mform->set_data(array('useallattempts' => $useallattempts));
            $mform->display();
        }
        // Print information on the number of existing attempts
        if (!$table->is_downloading()) { //do not print notices when downloading
            print_heading(get_string('quizinformation', 'quiz_statistics'));
            $quizinformationtable = new object();
            $quizinformationtable->align = array('center', 'center');
            $quizinformationtable->width = '60%';
            $quizinformationtable->class = 'generaltable titlesleft';
            $quizinformationtable->data = array();
            $quizinformationtable->data[] = array(get_string('quizname', 'quiz_statistics'), $quiz->name);
            $quizinformationtable->data[] = array(get_string('coursename', 'quiz_statistics'), $course->fullname);
            if ($cm->idnumber){
                $quizinformationtable->data[] = array(get_string('coursename', 'quiz_statistics'), $cm->idnumber);
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
        }

        $timemodified = time() - QUIZ_REPORT_TIME_TO_CACHE_STATS;
        $params = array('quizid'=>$quiz->id, 'groupid'=>$currentgroup, 'allattempts'=>$useallattempts, 'timemodified'=>$timemodified);
        if ($recalculate || !$quizstats = $DB->get_record_select('quiz_statistics', 'quizid = :quizid  AND groupid = :groupid AND allattempts = :allattempts AND timemodified > :timemodified', $params, '*', true)){
            list($s, $usingattemptsstring, $quizstats, $qstats) = $this->quiz_stats($nostudentsingroup, $quiz->id, $currentgroup, $groupstudents, $questions, $useallattempts);
            $toinsert = (object)((array)$quizstats + $params);
            $toinsert->timemodified = time();
            $quizstatisticsid = $DB->insert_record('quiz_statistics', $toinsert);
            foreach ($qstats->questions as $question){
                $question->_stats->quizstatisticsid = $quizstatisticsid;
                $DB->insert_record('quiz_question_statistics', $question->_stats, false, true);
            }
            foreach ($qstats->subquestions as $subquestion){
                $subquestion->_stats->quizstatisticsid = $quizstatisticsid;
                $DB->insert_record('quiz_question_statistics', $subquestion->_stats, false, true);
            }
            if (isset($qstats)){
                $questions = $qstats->questions;
                $subquestions = $qstats->subquestions;
            } else {
                $questions = array();
                $subquestions = array();
            }
        } else {
            if ($useallattempts){
                $usingattemptsstring = get_string('allattempts', 'quiz_statistics');
                $s = $quizstats->allattemptscount;
            } else {
                $usingattemptsstring = get_string('firstattempts', 'quiz_statistics');
                $s = $quizstats->firstattemptscount;
            }
            $questionstats = $DB->get_records('quiz_question_statistics', array('quizstatisticsid'=>$quizstats->id), 'subquestion ASC');
            $questionstats = quiz_report_index_by_keys($questionstats, array('subquestion', 'questionid'));
            if (1 < count($questionstats)){
                list($mainquestionstats, $subquestionstats) = $questionstats;
                $subqstofetch = array_keys($subquestionstats);
                $subquestions = question_load_questions($subqstofetch);
                foreach (array_keys($subquestions) as $subqid){
                    $subquestions[$subqid]->_stats = $subquestionstats[$subqid];
                }
            } else {
                $mainquestionstats = $questionstats[0];
                $subquestions = array();
            }
            foreach (array_keys($questions) as $qid){
                $questions[$qid]->_stats = $mainquestionstats[$qid];
            }
        }
        if (!$table->is_downloading()){
            if ($s==0){
                print_heading(get_string('noattempts','quiz'));
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
                switch ($format[$property]){
                    case 'sumgrades_as_percentage' :
                        $formattedvalue = quiz_report_scale_sumgrades_as_percentage($value, $quiz);
                        break;
                    case 'number_format' :
                        $formattedvalue = number_format($value, $quiz->decimalpoints).' %';
                        break;
                    default :
                        $formattedvalue = $value;
                }
                $quizinformationtable->data[] = array(get_string($property, 'quiz_statistics', $usingattemptsstring), $formattedvalue);
            }
            if (isset($quizstats->timemodified)){
                list($fromqa, $whereqa, $qaparams) = quiz_report_attempts_sql($quiz->id, $currentgroup, $groupstudents, $useallattempts);
                $sql = 'SELECT COUNT(1) ' .
                    'FROM ' .$fromqa.' '.
                    'WHERE ' .$whereqa.' AND qa.timefinish > :time';
                $a = new object();
                $a->lastcalculated = format_time(time() - $quizstats->timemodified);
                if (!$a->count = $DB->count_records_sql($sql, array('time'=>$quizstats->timemodified)+$qaparams)){
                    $a->count = 0;
                } 
                print_box_start('boxaligncenter generalbox boxwidthnormal mdl-align');
                echo get_string('lastcalculated', 'quiz_statistics', $a);
                print_single_button($reporturl->out(true), $reporturl->params()+array('recalculate'=>1),
                                    get_string('recalculatenow', 'quiz_statistics'), 'post');
                print_box_end();
            }
            print_table($quizinformationtable);
            
        }
        if (!$table->is_downloading()){
            print_heading(get_string('quizstructureanalysis', 'quiz_statistics'));
        }
        if ($s){
            $table->setup($quiz, $cm->id, $reporturl, $s);
            
            foreach ($questions as $question){
                $table->add_data_keyed($table->format_row($question));
                if (!empty($question->_stats->subquestions)){
                    $subitemstodisplay = explode(',', $question->_stats->subquestions);
                    foreach ($subitemstodisplay as $subitemid){
                        $subquestions[$subitemid]->maxgrade = $question->maxgrade;
                        $table->add_data_keyed($table->format_row($subquestions[$subitemid]));
                    }
                }
            }

            $table->finish_output();
        }
        return true;
    }
    
    
    function quiz_stats($nostudentsingroup, $quizid, $currentgroup, $groupstudents, $questions, $useallattempts){
        global $CFG, $DB;
        if (!$nostudentsingroup){
            //Calculating_MEAN_of_grades_for_all_attempts_by_students
            //http://docs.moodle.org/en/Development:Quiz_item_analysis_calculations_in_practise#Calculating_MEAN_of_grades_for_all_attempts_by_students
        
            list($fromqa, $whereqa, $qaparams) = quiz_report_attempts_sql($quizid, $currentgroup, $groupstudents);
    
            $sql = 'SELECT (CASE WHEN attempt=1 THEN 1 ELSE 0 END) AS isfirst, COUNT(1) AS countrecs, SUM(sumgrades) AS total ' .
                    'FROM '.$fromqa.
                    'WHERE ' .$whereqa.
                    'GROUP BY (attempt=1)';
            if (!$attempttotals = $DB->get_records_sql($sql, $qaparams)){
                $s = 0;
            } else {
                $firstattempt = $attempttotals[1];
                $allattempts = new object();
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
        $quizstats = new object();
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
                $limitoffset = (floor($s/2)) + 1;
                $limit = 1;
            }
            $sql = 'SELECT id, sumgrades ' .
                'FROM ' .$fromqa.
                'WHERE ' .$whereqa.
                'ORDER BY sumgrades';
            if (!$mediangrades = $DB->get_records_sql_menu($sql, $qaparams, $limitoffset, $limit)){
                print_error('errormedian', 'quiz_statistics');
            }
            if (count($mediangrades)==1){
                $quizstats->median = array_shift($mediangrades);
            } else {
                $median = array_shift($mediangrades);
                $median += array_shift($mediangrades);
                $quizstats->median = $median /2;
            }
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
                //see http://docs.moodle.org/en/Development:Quiz_item_analysis_calculations_in_practise#Standard_Deviation
                
                $quizstats->standarddeviation = sqrt($powers->power2 / ($s -1));
                

                
                //Skewness_and_Kurtosis
                if ($s>2){
                    //see http://docs.moodle.org/en/Development:Quiz_item_analysis_calculations_in_practise#Skewness_and_Kurtosis
                    $m2= $powers->power2 / $s;
                    $m3= $powers->power3 / $s;
                    $m4= $powers->power4 / $s;
                    
                    $k2= $s*$m2/($s-1);
                    $k3= $s*$s*$m3/(($s-1)*($s-2));
                    
                    $quizstats->skewness = $k3 / (pow($k2, 2/3));
                }
    
    
                if ($s>3){
                    $k4= (($s*$s*$s)/(($s-1)*($s-2)*($s-3)))*((($s+1)*$m4)-(3*($s-1)*$m2*$m2));
                    
                    $quizstats->kurtosis = $k4 / ($k2*$k2);
                }
            }
        }
        if ($s){
            require_once("$CFG->dirroot/mod/quiz/report/statistics/qstats.php");
            $qstats = new qstats($questions, $s, $sumgradesavg);
            $qstats->get_records($quizid, $currentgroup, $groupstudents, $useallattempts);
            set_time_limit(0);
            $qstats->process_states();
        } else {
            $qstats = false;
        }
        if ($s>1){
            $p = count($qstats->questions);//no of positions
            if ($p > 1){
                $quizstats->cic = (100 * $p / ($p -1)) * (1 - ($qstats->sum_of_grade_variance())/$k2);
                $quizstats->errorratio = 100 * sqrt(1-($quizstats->cic/100));
                $quizstats->standarderror = ($quizstats->errorratio * $quizstats->standarddeviation / 100);
                
            }
        }
        return array($s, $usingattemptsstring, $quizstats, $qstats);
    }

}
function quiz_report_attempts_sql($quizid, $currentgroup, $groupstudents, $allattempts = true){
    $fromqa = '{quiz_attempts} qa ';
    $whereqa = 'qa.quiz = :quizid AND qa.preview=0 AND qa.timefinish !=0 ';
    $qaparams = array('quizid'=>$quizid);
    if (!empty($currentgroup) && $groupstudents) {
        list($grpsql, $grpparams) = $DB->get_in_or_equal(array_keys($groupstudents), SQL_PARAMS_NAMED, 'u0000');
        $whereqa .= 'AND qa.userid '.$grpsql.' ';
        $qaparams += $grpparams;
    }
    if (!$allattempts){
        $whereqa .= 'AND qa.attempt=1 ';
    }
    return array($fromqa, $whereqa, $qaparams);
}
?>
