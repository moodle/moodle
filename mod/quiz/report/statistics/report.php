<?php
/**
 * This script calculates various statistics about student attempts
 *
 * @version $Id$
 * @author Martin Dougiamas, Jamie Pratt, Tim Hunt and others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 *//** */

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

        $pageoptions = array();
        $pageoptions['id'] = $cm->id;
        $pageoptions['q'] = $quiz->id;
        $pageoptions['mode'] = 'statistics';

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
        }

        $questions = quiz_report_load_questions($quiz);
        // Load the question type specific information
        if (!get_question_options($questions)) {
            print_error('cannotloadquestion', 'question');
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
            print_table($quizinformationtable);
        }
        if (!$table->is_downloading()) {
            // Print display options
            $mform->set_data(array('useallattempts' => $useallattempts));
            $mform->display();
        }
        $fromqa = '{quiz_attempts} qa ';
        $whereqa = 'quiz = :quizid AND preview=0 AND timefinish !=0 ';
        $qaparams = array('quizid'=>$quiz->id);
        if (!empty($currentgroup) && $groupstudents) {
            list($grpsql, $grpparams) = $DB->get_in_or_equal(array_keys($groupstudents), SQL_PARAMS_NAMED, 'u0000');
            $whereqa .= ' AND qa.userid '.$grpsql.' ';
            $qaparams += $grpparams;
        }
        $sql = 'SELECT (attempt=1) AS isfirst, COUNT(1) AS countrecs, SUM(sumgrades) AS total ' .
                'FROM '.$fromqa.
                'WHERE ' .$whereqa.
                'GROUP BY (attempt=1)';

        if (!$nostudentsingroup){
            //Calculating_MEAN_of_grades_for_all_attempts_by_students
            //http://docs.moodle.org/en/Development:Quiz_item_analysis_calculations_in_practise#Calculating_MEAN_of_grades_for_all_attempts_by_students
            if (!$attempttotals = $DB->get_records_sql($sql, $qaparams)){
                print_heading(get_string('noattempts','quiz'));
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
                $usingattempts->heading = get_string('statsfor', 'quiz_statistics', $usingattempts->attempts);
                $s = $usingattempts->countrecs;
                $sumgradesavg = $usingattempts->total / $usingattempts->countrecs;
            }
        } else {
            $s = 0;
        }
       
        if ($s && !$table->is_downloading()) {
            print_heading(get_string('quizoverallstatistics', 'quiz_statistics'));
            $quizoverallstatistics = new object();
            $quizoverallstatistics->align = array('center', 'center');
            $quizoverallstatistics->width = '60%';
            $quizoverallstatistics->class = 'generaltable titlesleft';
            $quizoverallstatistics->data = array();
            $quizoverallstatistics->data[] = array(get_string('nooffirstattempts', 'quiz_statistics'), $firstattempt->countrecs);
            $quizoverallstatistics->data[] = array(get_string('noofallattempts', 'quiz_statistics'), $allattempts->countrecs);
            $quizoverallstatistics->data[] = array(get_string('firstattemptsavg', 'quiz_statistics'), quiz_report_scale_sumgrades_as_percentage($firstattempt->total / $firstattempt->countrecs, $quiz));
            $quizoverallstatistics->data[] = array(get_string('allattemptsavg', 'quiz_statistics'), quiz_report_scale_sumgrades_as_percentage($allattempts->total / $allattempts->countrecs, $quiz));
            print_table($quizoverallstatistics);
        }
        $quizattsstatistics = new object();
        $quizattsstatistics->align = array('center', 'center');
        $quizattsstatistics->width = '60%';
        $quizattsstatistics->class = 'generaltable titlesleft';
        $quizattsstatistics->data = array();
        //get the median
        if ($s && !$table->is_downloading()) {

            print_heading($usingattempts->heading);
            if (($s%2)==0){
                //even number of attempts
                $limitoffset = ($s/2) - 1;
                $limit = 2;
            } else {
                $limitoffset = (floor($s/2)) - 1;
                $limit = 1;
            }
            $sql = 'SELECT id, sumgrades ' .
                'FROM ' .$fromqa.
                'WHERE ' .$whereqa.
                $usingattempts->sql.
                'ORDER BY sumgrades';
            if (!$mediangrades = $DB->get_records_sql_menu($sql, $qaparams, $limitoffset, $limit)){
                print_error('errormedian', 'quiz_statistics');
            }
            if (count($mediangrades)==1){
                $median = array_shift($mediangrades);
            } else {
                $median = array_shift($mediangrades);
                $median += array_shift($mediangrades);
                $median = $median /2;
            }
            if ($s>1){
                $quizattsstatistics->data[] = array(get_string('median', 'quiz_statistics'), quiz_report_scale_sumgrades_as_percentage($median, $quiz));
                //fetch sum of squared, cubed and power 4d 
                //differences between grades and mean grade
                $mean = $usingattempts->total / $s;
                $sql = "SELECT " .
                    "SUM(POWER((qa.sumgrades - :mean1),2)) AS power2, " .
                    "SUM(POWER((qa.sumgrades - :mean2),3)) AS power3, ".
                    "SUM(POWER((qa.sumgrades - :mean3),4)) AS power4 ".
                    'FROM ' .$fromqa.
                    'WHERE ' .$whereqa.
                    $usingattempts->sql;
                $params = array('mean1' => $mean, 'mean2' => $mean, 'mean3' => $mean)+$qaparams;
                if (!$powers = $DB->get_record_sql($sql, $params)){
                    print_error('errorpowers', 'quiz_statistics');
                }
                
                //Standard_Deviation
                //see http://docs.moodle.org/en/Development:Quiz_item_analysis_calculations_in_practise#Standard_Deviation
                
                $sd = sqrt($powers->power2 / ($s -1));
                $quizattsstatistics->data[] = array(get_string('standarddeviation', 'quiz_statistics'), quiz_report_scale_sumgrades_as_percentage($sd, $quiz));

                
                //Skewness_and_Kurtosis
                if ($s>2){
                    //see http://docs.moodle.org/en/Development:Quiz_item_analysis_calculations_in_practise#Skewness_and_Kurtosis
                    $m2= $powers->power2 / $s;
                    $m3= $powers->power3 / $s;
                    $m4= $powers->power4 / $s;
                    
                    $k2= $s*$m2/($s-1);
                    $k3= $s*$s*$m3/(($s-1)*($s-2));
                    
                    $skewness = $k3 / (pow($k2, 2/3));
                    $quizattsstatistics->data[] = array(get_string('skewness', 'quiz_statistics'), $skewness);
                }
    
    
                if ($s>3){
                    $k4= (($s*$s*$s)/(($s-1)*($s-2)*($s-3)))*((($s+1)*$m4)-(3*($s-1)*$m2*$m2));
                    
                    $kurtosis = $k4 / ($k2*$k2);
                    
                    $quizattsstatistics->data[] = array(get_string('kurtosis', 'quiz_statistics'), $kurtosis);
                }
            }
        }
        if ($s){
            require_once("$CFG->dirroot/mod/quiz/report/statistics/qstats.php");
            $qstats = new qstats($questions, $s, $sumgradesavg);
            $qstats->get_records($fromqa, $whereqa, $usingattempts->sql, $qaparams);
            set_time_limit(0);
            $qstats->process_states();
        }
        if (!$table->is_downloading()){
            if ($s>1){
                $p = count($questions);//no of positions
                if ($p > 1){
                    $cic = (100 * $p / ($p -1)) * (1 - ($qstats->sum_of_grade_variance())/$k2);
                    $quizattsstatistics->data[] = array(get_string('cic', 'quiz_statistics'), number_format($cic, $quiz->decimalpoints).' %');
                    $errorratio = 100 * sqrt(1-($cic/100));
                    $quizattsstatistics->data[] = array(get_string('errorratio', 'quiz_statistics'), number_format($errorratio, $quiz->decimalpoints).' %');
                    $standarderror = ($errorratio * $sd / 100);
                    $quizattsstatistics->data[] = array(get_string('standarderror', 'quiz_statistics'), 
                        quiz_report_scale_sumgrades_as_percentage($standarderror, $quiz));
                }
            }
            if ($quizattsstatistics->data){
                print_table($quizattsstatistics);
            }
        }
        if (!$table->is_downloading()){
            print_heading(get_string('quizstructureanalysis', 'quiz_statistics'));
        }
        $table->setup($quiz, $cm->id, $reporturl, $s);
        if (isset($qstats)){
            foreach ($qstats->questions as $question){
                $table->add_data_keyed($table->format_row($question));
                foreach ($question->_stats->subitems as $itemid){
                    $table->add_data_keyed($table->format_row($qstats->subquestions[$itemid]));
                }
            }
        }
        $table->finish_output();
        return true;
    }

}


?>
