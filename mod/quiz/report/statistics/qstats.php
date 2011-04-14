<?php
class qstats{
    /**
     * @var mixed states from which to calculate stats - iteratable.
     */
    var $states;

    var $sumofgradevariance = 0;
    var $questions;
    var $subquestions = array();
    var $randomselectors = array();
    var $responses = array();

    function qstats($questions, $s, $sumgradesavg){
        $this->s = $s;
        $this->sumgradesavg = $sumgradesavg;

        foreach (array_keys($questions) as $qid){
            $questions[$qid]->_stats = $this->stats_init_object();
        }
        $this->questions = $questions;
    }
    function stats_init_object(){
        $statsinit = new stdClass();
        $statsinit->s = 0;
        $statsinit->totalgrades = 0;
        $statsinit->totalothergrades = 0;
        $statsinit->gradevariancesum = 0;
        $statsinit->othergradevariancesum = 0;
        $statsinit->covariancesum = 0;
        $statsinit->covariancemaxsum = 0;
        $statsinit->subquestion = false;
        $statsinit->subquestions = '';
        $statsinit->covariancewithoverallgradesum = 0;
        $statsinit->gradearray = array();
        $statsinit->othergradesarray = array();
        return $statsinit;
    }
    function get_records($quizid, $currentgroup, $groupstudents, $allattempts){
        global $DB;
        list($qsql, $qparams) = $DB->get_in_or_equal(array_keys($this->questions), SQL_PARAMS_NAMED, 'q');
        list($fromqa, $whereqa, $qaparams) = quiz_report_attempts_sql($quizid, $currentgroup, $groupstudents, $allattempts);
        $sql = 'SELECT qs.id, ' .
            'qs.question, ' .
            'qa.sumgrades, ' .
            'qs.grade, ' .
            'qs.answer ' .
            'FROM ' .
            '{question_sessions} qns, ' .
            '{question_states} qs, '.
            $fromqa.' '.
            'WHERE ' .$whereqa.
            'AND qs.question '.$qsql.' '.
            'AND qns.attemptid = qa.uniqueid '.
            'AND qns.newgraded = qs.id';
        $this->states = $DB->get_records_sql($sql, $qaparams + $qparams);
        if ($this->states === false){
            print_error('errorstatisticsquestions', 'quiz_statistics');
        }
    }

    function _initial_states_walker($state, &$stats, $positionstat = true){
        $stats->s++;
        $stats->totalgrades += $state->grade;
        if ($positionstat){
            $stats->totalothergrades += $state->sumgrades - $state->grade;
        } else {
            $stats->totalothergrades += $state->sumgrades;
        }
        //need to sort these to calculate max covariance :
        $stats->gradearray[] = $state->grade;
        if ($positionstat){
            $stats->othergradesarray[] = $state->sumgrades - $state->grade;
        } else {
            $stats->othergradesarray[] = $state->sumgrades;
        }

    }

    function _secondary_states_walker($state, &$stats){
        $gradedifference = ($state->grade - $stats->gradeaverage);
        if ($stats->subquestion){
            $othergradedifference = $state->sumgrades - $stats->othergradeaverage;
        } else {
            $othergradedifference = (($state->sumgrades - $state->grade) - $stats->othergradeaverage);
        }
        $overallgradedifference = $state->sumgrades - $this->sumgradesavg;
        $sortedgradedifference = (array_shift($stats->gradearray) - $stats->gradeaverage);
        $sortedothergradedifference = (array_shift($stats->othergradesarray) - $stats->othergradeaverage);
        $stats->gradevariancesum += pow($gradedifference,2);
        $stats->othergradevariancesum += pow($othergradedifference,2);
        $stats->covariancesum += $gradedifference * $othergradedifference;
        $stats->covariancemaxsum += $sortedgradedifference * $sortedothergradedifference;
        $stats->covariancewithoverallgradesum += $gradedifference * $overallgradedifference;

    }

    function add_response_detail_to_array($responsedetail){
        $responsedetail->rcount = 1;
        if (isset($this->responses[$responsedetail->subqid])){
            if (isset($this->responses[$responsedetail->subqid][$responsedetail->aid])){
                if (isset($this->responses[$responsedetail->subqid][$responsedetail->aid][$responsedetail->response])){
                    $this->responses[$responsedetail->subqid][$responsedetail->aid][$responsedetail->response]->rcount++;
                } else {
                    $this->responses[$responsedetail->subqid][$responsedetail->aid][$responsedetail->response] = $responsedetail;
                }
            } else {
                $this->responses[$responsedetail->subqid][$responsedetail->aid] = array($responsedetail->response => $responsedetail);
            }
        } else {
            $this->responses[$responsedetail->subqid] = array();
            $this->responses[$responsedetail->subqid][$responsedetail->aid] = array($responsedetail->response => $responsedetail);
        }
    }

    /**
     * Get the data for the individual question response analysis table.
     */
    function _process_actual_responses($question, $state){
        global $QTYPES;
        if ($question->qtype != 'random' &&
                $QTYPES[$question->qtype]->show_analysis_of_responses()){
            $restoredstate = clone($state);
            restore_question_state($question, $restoredstate);
            $responsedetails = $QTYPES[$question->qtype]->get_actual_response_details($question, $restoredstate);
            foreach ($responsedetails as $responsedetail){
                $responsedetail->questionid = $question->id;
                $this->add_response_detail_to_array($responsedetail);
            }
        }
    }

    function _initial_question_walker(&$stats){
        $stats->gradeaverage = $stats->totalgrades / $stats->s;
        if ($stats->maxgrade!=0){
            $stats->facility = $stats->gradeaverage / $stats->maxgrade;
        } else {
            $stats->facility = null;
        }
        $stats->othergradeaverage = $stats->totalothergrades / $stats->s;
        sort($stats->gradearray, SORT_NUMERIC);
        sort($stats->othergradesarray, SORT_NUMERIC);
    }
    function _secondary_question_walker(&$stats){
        if ($stats->s > 1){
            $stats->gradevariance = $stats->gradevariancesum / ($stats->s -1);
            $stats->othergradevariance = $stats->othergradevariancesum / ($stats->s -1);
            $stats->covariance = $stats->covariancesum / ($stats->s -1);
            $stats->covariancemax = $stats->covariancemaxsum / ($stats->s -1);
            $stats->covariancewithoverallgrade = $stats->covariancewithoverallgradesum / ($stats->s-1);
            $stats->sd = sqrt($stats->gradevariancesum / ($stats->s -1));
        } else {
            $stats->gradevariance = null;
            $stats->othergradevariance = null;
            $stats->covariance = null;
            $stats->covariancemax = null;
            $stats->covariancewithoverallgrade = null;
            $stats->sd = null;
        }
        //avoid divide by zero
        if ($stats->gradevariance * $stats->othergradevariance){
            $stats->discriminationindex = 100*$stats->covariance
                        / sqrt($stats->gradevariance * $stats->othergradevariance);
        } else {
            $stats->discriminationindex = null;
        }
        if ($stats->covariancemax){
            $stats->discriminativeefficiency = 100*$stats->covariance / $stats->covariancemax;
        } else {
            $stats->discriminativeefficiency = null;
        }
    }

    function process_states(){
        global $DB, $OUTPUT;
        set_time_limit(0);
        $subquestionstats = array();
        foreach ($this->states as $state){
            $this->_initial_states_walker($state, $this->questions[$state->question]->_stats);
            //if this is a random question what is the real item being used?
            if ($this->questions[$state->question]->qtype == 'random'){
                if ($realstate = question_get_real_state($state)){
                    if (!isset($subquestionstats[$realstate->question])){
                        $subquestionstats[$realstate->question] = $this->stats_init_object();
                        $subquestionstats[$realstate->question]->usedin = array();
                        $subquestionstats[$realstate->question]->subquestion = true;
                        $subquestionstats[$realstate->question]->differentweights = false;
                        $subquestionstats[$realstate->question]->maxgrade = $this->questions[$state->question]->maxgrade;
                    } else if ($subquestionstats[$realstate->question]->maxgrade != $this->questions[$state->question]->maxgrade){
                        $subquestionstats[$realstate->question]->differentweights = true;
                    }
                    $this->_initial_states_walker($realstate, $subquestionstats[$realstate->question], false);
                    $number = $this->questions[$state->question]->number;
                    $subquestionstats[$realstate->question]->usedin[$number] = $number;
                    $randomselectorstring = $this->questions[$state->question]->category.'/'.$this->questions[$state->question]->questiontext;
                    if (!isset($this->randomselectors[$randomselectorstring])){
                        $this->randomselectors[$randomselectorstring] = array();
                    }
                    $this->randomselectors[$randomselectorstring][$realstate->question] = $realstate->question;
                }
            }
        }
        foreach ($this->randomselectors as $key => $randomselector){
            ksort($this->randomselectors[$key]);
        }
        $this->subquestions = question_load_questions(array_keys($subquestionstats));
        foreach (array_keys($this->subquestions) as $qid){
            $this->subquestions[$qid]->_stats = $subquestionstats[$qid];
            $this->subquestions[$qid]->_stats->questionid = $qid;
            $this->subquestions[$qid]->maxgrade = $this->subquestions[$qid]->_stats->maxgrade;
            $this->_initial_question_walker($this->subquestions[$qid]->_stats);
            if ($subquestionstats[$qid]->differentweights){
                echo $OUTPUT->notification(get_string('erroritemappearsmorethanoncewithdifferentweight', 'quiz_statistics', $this->subquestions[$qid]->name));
            }
            if ($this->subquestions[$qid]->_stats->usedin){
                sort($this->subquestions[$qid]->_stats->usedin, SORT_NUMERIC);
                $this->subquestions[$qid]->_stats->positions = join($this->subquestions[$qid]->_stats->usedin, ',');
            } else {
                $this->subquestions[$qid]->_stats->positions = '';
            }
        }
        reset($this->questions);
        do{
            list($qid, $question) = each($this->questions);
            $nextquestion = current($this->questions);
            $this->questions[$qid]->_stats->questionid = $qid;
            $this->questions[$qid]->_stats->positions = $this->questions[$qid]->number;
            $this->questions[$qid]->_stats->maxgrade = $question->maxgrade;
            $this->_initial_question_walker($this->questions[$qid]->_stats);
            if ($question->qtype == 'random'){
                $randomselectorstring = $question->category.'/'.$question->questiontext;
                if ($nextquestion){
                    $nextrandomselectorstring = $nextquestion->category.'/'.$nextquestion->questiontext;
                    if ($nextquestion->qtype == 'random' && $randomselectorstring == $nextrandomselectorstring){
                        continue;//next loop iteration
                    }
                }
                if (isset($this->randomselectors[$randomselectorstring])){
                    $question->_stats->subquestions = join($this->randomselectors[$randomselectorstring], ',');
                }
            }
        } while ($nextquestion);
        //go through the records one more time
        foreach ($this->states as $state){
            $this->_secondary_states_walker($state, $this->questions[$state->question]->_stats);
            if ($this->questions[$state->question]->qtype == 'random'){
                if ($realstate = question_get_real_state($state)){
                    $this->_secondary_states_walker($realstate, $this->subquestions[$realstate->question]->_stats);
                }
            }
        }
        $sumofcovariancewithoverallgrade = 0;
        foreach (array_keys($this->questions) as $qid){
            $this->_secondary_question_walker($this->questions[$qid]->_stats);
            $this->sumofgradevariance += $this->questions[$qid]->_stats->gradevariance;
            if ($this->questions[$qid]->_stats->covariancewithoverallgrade >= 0){
                $sumofcovariancewithoverallgrade += sqrt($this->questions[$qid]->_stats->covariancewithoverallgrade);
                $this->questions[$qid]->_stats->negcovar = 0;
            } else {
                $this->questions[$qid]->_stats->negcovar = 1;
            }
        }
        foreach (array_keys($this->subquestions) as $qid){
            $this->_secondary_question_walker($this->subquestions[$qid]->_stats);
        }
        foreach (array_keys($this->questions) as $qid){
            if ($sumofcovariancewithoverallgrade){
                if ($this->questions[$qid]->_stats->negcovar){
                    $this->questions[$qid]->_stats->effectiveweight = null;
                } else {
                    $this->questions[$qid]->_stats->effectiveweight = 100 * sqrt($this->questions[$qid]->_stats->covariancewithoverallgrade)
                                /   $sumofcovariancewithoverallgrade;
                }
            } else {
                $this->questions[$qid]->_stats->effectiveweight = null;
            }
        }
    }

    function process_responses(){
        foreach ($this->states as $state){
            if ($this->questions[$state->question]->qtype == 'random'){
                if ($realstate = question_get_real_state($state)){
                    $this->_process_actual_responses($this->subquestions[$realstate->question], $realstate);
                }
            } else {
                $this->_process_actual_responses($this->questions[$state->question], $state);
            }
        }
        $this->responses = quiz_report_unindex($this->responses);
    }
    /**
     * Needed by quiz stats calculations.
     */
    function sum_of_grade_variance(){
        return $this->sumofgradevariance;
    }
}

