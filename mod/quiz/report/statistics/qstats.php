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
    
    function qstats($questions, $s, $sumgradesavg){
        $this->s = $s;
        $this->sumgradesavg = $sumgradesavg;
        foreach (array_keys($questions) as $qid){
            $questions[$qid]->_stats = $this->stats_init_object();
        }
        $this->questions = $questions;
    }
    function stats_init_object(){
        $statsinit = new object();
        $statsinit->s = 0;
        $statsinit->totalgrades = 0;
        $statsinit->totalothergrades = 0;
        $statsinit->gradevariancesum = 0;
        $statsinit->othergradevariancesum = 0;
        $statsinit->covariancesum = 0;
        $statsinit->covariancemaxsum = 0;
        $statsinit->covariancewithoverallgradesum = 0;
        $statsinit->gradearray = array();
        $statsinit->othergradesarray = array();
        return $statsinit;
    }
    function get_records($fromqa, $whereqa, $usingattempts, $qaparams){
        global $DB;
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
            'AND qns.attemptid = qa.uniqueid '.
            $usingattempts.
            'AND qns.newgraded = qs.id';
        $this->states = $DB->get_records_sql($sql, $qaparams);
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
        $othergradedifference = (($state->sumgrades - $state->grade) - $stats->othergradeaverage);
        $overallgradedifference = $state->sumgrades - $this->sumgradesavg;
        $sortedgradedifference = (array_shift($stats->gradearray) - $stats->gradeaverage);
        $sortedothergradedifference = (array_shift($stats->othergradesarray) - $stats->othergradeaverage);
        $stats->gradevariancesum += pow($gradedifference,2);
        $stats->othergradevariancesum += pow($othergradedifference,2);
        $stats->covariancesum += $gradedifference * $othergradedifference;
        $stats->covariancemaxsum += $sortedgradedifference * $sortedothergradedifference;
        $stats->covariancewithoverallgradesum += $gradedifference * $overallgradedifference;
    }


    function _initial_question_walker(&$stats, $grade){
        $stats->gradeaverage = $stats->totalgrades / $stats->s;
        $stats->facility = $stats->gradeaverage / $grade;
        $stats->othergradeaverage = $stats->totalothergrades / $stats->s;
        sort($stats->gradearray, SORT_NUMERIC);
        sort($stats->othergradesarray, SORT_NUMERIC);
    }
    function _secondary_question_walker(&$stats){
        $stats->gradevariance = $stats->gradevariancesum / ($stats->s -1);
        $stats->othergradevariance = $stats->othergradevariancesum / ($stats->s -1);
        $stats->covariance = $stats->covariancesum / ($stats->s -1);
        $stats->covariancemax = $stats->covariancemaxsum / ($stats->s -1);
        $stats->covariancewithoverallgrade = $stats->covariancewithoverallgradesum / ($stats->s-1);
        $stats->sd = sqrt($stats->gradevariancesum / ($stats->s -1));
        //avoid divide by zero
        if ($stats->gradevariance * $stats->othergradevariance){
            $stats->discriminationindex = 100*$stats->covariance 
                        / sqrt($stats->gradevariance * $stats->othergradevariance);
        } else {
            $stats->discriminationindex = '';
        }
        if ($stats->covariancemax){
            $stats->discriminativeefficiency = 100*$stats->covariance / $stats->covariancemax;
        } else {
            $stats->discriminativeefficiency = '';
        }
    }
    
    function process_states(){
        foreach ($this->states as $state){
            $this->_initial_states_walker($state, $this->questions[$state->question]->_stats);
            //if this is a random question what is the real item being used?
            if ($this->questions[$state->question]->qtype == 'random'){
                if ($itemid = question_get_real_questionid($state)){
                    if (!isset($subquestionstats[$itemid])){
                        $subquestionstats[$itemid] = $this->stats_init_object();
                        $subquestionstats[$itemid]->usedin = array();
                        $subquestionstats[$itemid]->differentweights = false;
                        $subquestionstats[$itemid]->maxgrade = $this->questions[$state->question]->maxgrade;
                    } else if ($subquestionstats[$itemid]->maxgrade != $this->questions[$state->question]->maxgrade){
                        $subquestionstats[$itemid]->differentweights = true;
                    }
                    $this->_initial_states_walker($state, $subquestionstats[$itemid], false);
                    $subquestionstats[$itemid]->usedin[$state->question] = $state->question;
                    $randomselectorstring = $this->questions[$state->question]->category.'/'.$this->questions[$state->question]->questiontext;
                    if (!isset($this->randomselectors[$randomselectorstring])){
                        $this->randomselectors[$randomselectorstring] = array();
                    }
                    $this->randomselectors[$randomselectorstring][$itemid] = $itemid;
                }
            }
        }
        $this->subquestions = question_load_questions(array_keys($subquestionstats));
        foreach (array_keys($this->subquestions) as $qid){
            $this->subquestions[$qid]->_stats = $subquestionstats[$qid];
            $this->subquestions[$qid]->maxgrade = $this->subquestions[$qid]->_stats->maxgrade;
            $this->subquestions[$qid]->subquestion = true;
            $this->_initial_question_walker($this->subquestions[$qid]->_stats, $this->subquestions[$qid]->_stats->maxgrade);
            if ($subquestionstats[$qid]->differentweights){
                notify(get_string('erroritemappearsmorethanoncewithdifferentweight', 'quiz_statistics', $this->subquestions[$qid]->name));
            }
        }
        foreach (array_keys($this->questions) as $qid){
            $this->_initial_question_walker($this->questions[$qid]->_stats, $this->questions[$qid]->maxgrade);
            $this->questions[$qid]->subquestion = false;
        }
        //go through the records one more time
        foreach ($this->states as $state){
            $this->_secondary_states_walker($state, $this->questions[$state->question]->_stats);
            if ($this->questions[$state->question]->qtype == 'random'){
                if ($itemid = question_get_real_questionid($state)){
                    $this->_secondary_states_walker($state, $this->subquestions[$itemid]->_stats);
                }
            }
        }
        $sumofcovariancewithoverallgrade = 0;
        foreach (array_keys($this->questions) as $qid){
            $this->_secondary_question_walker($this->questions[$qid]->_stats);
            $this->sumofgradevariance += $this->questions[$qid]->_stats->gradevariance;
            $sumofcovariancewithoverallgrade += sqrt($this->questions[$qid]->_stats->covariancewithoverallgrade);
        }
        foreach (array_keys($this->subquestions) as $qid){
            $this->_secondary_question_walker($this->subquestions[$qid]->_stats);
        }
        foreach (array_keys($this->questions) as $qid){
            $this->questions[$qid]->_stats->effectiveweight = 100 * sqrt($this->questions[$qid]->_stats->covariancewithoverallgrade)
                        /   $sumofcovariancewithoverallgrade;
        }
    }
    /**
     * Needed by quiz stats calculations.
     */
    function sum_of_grade_variance(){
        return $this->sumofgradevariance;
    }
}
?>