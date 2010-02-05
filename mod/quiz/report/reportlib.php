<?php
define('QUIZ_REPORT_DEFAULT_PAGE_SIZE', 30);
define('QUIZ_REPORT_DEFAULT_GRADING_PAGE_SIZE', 10);

define('QUIZ_REPORT_ATTEMPTS_ALL', 0);
define('QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH_NO', 1);
define('QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH', 2);
define('QUIZ_REPORT_ATTEMPTS_ALL_STUDENTS', 3);
/**
 * Get newest graded state or newest state for a number of attempts. Pass in the 
 * uniqueid field from quiz_attempt table not the id. Use question_state_is_graded
 * function to check that the question is actually graded.
 */
function quiz_get_newgraded_states($attemptids, $idxattemptq = true, $fields='qs.*'){
    global $CFG;
    if ($attemptids){
        $attemptidlist = join($attemptids, ',');
        $gradedstatesql = "SELECT $fields FROM " .
                "{$CFG->prefix}question_sessions qns, " .
                "{$CFG->prefix}question_states qs " .
                "WHERE qns.attemptid IN ($attemptidlist) AND " .
                "qns.newest = qs.id";
        $gradedstates = get_records_sql($gradedstatesql);
        if ($idxattemptq){
            $gradedstatesbyattempt = array();
            foreach ($gradedstates as $gradedstate){
                if (!isset($gradedstatesbyattempt[$gradedstate->attempt])){
                    $gradedstatesbyattempt[$gradedstate->attempt] = array();
                }
                $gradedstatesbyattempt[$gradedstate->attempt][$gradedstate->question] = $gradedstate;
            }
            return $gradedstatesbyattempt;
        } else {
            return $gradedstates;
        }
    } else {
        return array();
    }
}

function quiz_get_average_grade_for_questions($quiz, $userids){
    global $CFG;
    $qmfilter = quiz_report_qm_filter_select($quiz);
    $questionavgssql = "SELECT qs.question, AVG(qs.grade) FROM " .
            "{$CFG->prefix}question_sessions qns, " .
            "{$CFG->prefix}quiz_attempts qa, " .
            "{$CFG->prefix}question_states qs " .
            "WHERE qns.attemptid = qa.uniqueid AND " .
            "qa.quiz = {$quiz->id} AND " .
            ($qmfilter?$qmfilter.' AND ':'').
            "qa.userid IN ({$userids}) AND " .
            "qs.event IN (".QUESTION_EVENTS_GRADED.") AND ".
            "qns.newgraded = qs.id GROUP BY qs.question";
    return get_records_sql_menu($questionavgssql);
}

function quiz_get_total_qas_graded_and_ungraded($quiz, $questionids, $userids){
    global $CFG;
    $sql = "SELECT qs.question, COUNT(1) AS totalattempts, " .
            "SUM(CASE WHEN (qs.event IN (".QUESTION_EVENTS_GRADED.")) THEN 1 ELSE 0 END) AS gradedattempts " .
            "FROM " .
            "{$CFG->prefix}quiz_attempts qa, " .
            "{$CFG->prefix}question_sessions qns, " .
            "{$CFG->prefix}question_states qs " .
            "WHERE " .
            "qa.quiz = {$quiz->id} AND " .
            "qa.userid IN ({$userids}) AND " .
            "qns.attemptid = qa.uniqueid AND " .
            "qns.newgraded = qs.id AND " .
            "qs.question IN ({$questionids}) " .
            "GROUP BY qs.question";
    return get_records_sql($sql);
}

function quiz_format_average_grade_for_questions($avggradebyq, $questions, $quiz, $download){
    $row = array();
    if (!$avggradebyq){
        $avggradebyq = array();
    }
    foreach(array_keys($questions) as $questionid) {
        if (isset($avggradebyq[$questionid])){
            $grade = $avggradebyq[$questionid];
            $grade = quiz_rescale_grade($grade, $quiz);
        } else {
            $grade = '--';
        }
        if (!$download) {
            $grade = $grade.'/'.quiz_rescale_grade($questions[$questionid]->grade, $quiz);
        }
        $row['qsgrade'.$questionid]= $grade;
    }
    return $row;
}
/**
 * Load the question data necessary in the reports.
 * - Remove description questions.
 * - Order questions in order that they are in the quiz
 * - Add question numbers.
 * - Add grade from quiz_questions_instance
 */
function quiz_report_load_questions($quiz){
    global $CFG;
    $questionlist = quiz_questions_in_quiz($quiz->questions);
    //In fact in most cases the id IN $questionlist below is redundant 
    //since we are also doing a JOIN on the qqi table. But will leave it in
    //since this double check will probably do no harm.
    if (!$questions = get_records_sql("SELECT q.*, qqi.grade " .
            "FROM {$CFG->prefix}question q, " .
            "{$CFG->prefix}quiz_question_instances qqi " .
            "WHERE q.id IN ($questionlist) AND " .
            "qqi.question = q.id AND " .
            "qqi.quiz =".$quiz->id)) {
        print_error('No questions found');
    }
    //Now we have an array of questions from a quiz we work out there question nos and remove 
    //questions with zero length ie. description questions etc.
    //also put questions in order.
    $number = 1;
    $realquestions = array();
    $questionids = explode(',', $questionlist);
    foreach ($questionids as $id) {
        if ($questions[$id]->length) {
            // Ignore questions of zero length
            $realquestions[$id] = $questions[$id];
            $realquestions[$id]->number = $number;
            $number += $questions[$id]->length;
        }
    }
    return $realquestions;
}
/**
 * Given the quiz grading method return sub select sql to find the id of the
 * one attempt that will be graded for each user. Or return
 * empty string if all attempts contribute to final grade.
 */
function quiz_report_qm_filter_select($quiz){
    global $CFG;
    if ($quiz->attempts == 1) {//only one attempt allowed on this quiz
        return '';
    }
    $useridsql = 'qa.userid';
    $quizidsql = 'qa.quiz';
    $qmfilterattempts = true;
    switch ($quiz->grademethod) {
    case QUIZ_GRADEHIGHEST :
        $field1 = 'sumgrades';
        $field2 = 'timestart';
        $aggregator1 = 'MAX';
        $aggregator2 = 'MIN';
        $qmselectpossible = true;
        break;
    case QUIZ_GRADEAVERAGE :
        $qmselectpossible = false;
        break;
    case QUIZ_ATTEMPTFIRST :
        $field1 = 'timestart';
        $field2 = 'id';
        $aggregator1 = 'MIN';
        $aggregator2 = 'MIN';
        $qmselectpossible = true;
        break;
    case QUIZ_ATTEMPTLAST :
        $field1 = 'timestart';
        $field2 = 'id';
        $aggregator1 = 'MAX';
        $aggregator2 = 'MAX';
        $qmselectpossible = true;
        break;
    }
    if ($qmselectpossible){
        $qmselect = "qa.$field1 = (SELECT $aggregator1(qa2.$field1) FROM {$CFG->prefix}quiz_attempts qa2 WHERE qa2.quiz = $quizidsql AND qa2.userid = $useridsql) AND " .
                    "qa.$field2 = (SELECT $aggregator2(qa3.$field2) FROM {$CFG->prefix}quiz_attempts qa3 WHERE qa3.quiz = $quizidsql AND qa3.userid = $useridsql AND qa3.$field1 = qa.$field1)";
    } else {
        $qmselect = '';
    }

    return $qmselect;
}


function quiz_report_grade_bands($bandwidth, $bands, $quizid, $useridlist=''){
    global $CFG;
    $sql = "SELECT
        FLOOR(qg.grade/$bandwidth) AS band,
        COUNT(1) AS num
    FROM
        {$CFG->prefix}quiz_grades qg, 
        {$CFG->prefix}quiz q
    WHERE qg.quiz = q.id AND qg.quiz = $quizid 
            ".($useridlist?"AND qg.userid IN ($useridlist) ":'')."
    GROUP BY FLOOR(qg.grade/$bandwidth)
    ORDER BY band";
    if (!$data = get_records_sql_menu($sql)){
        $data= array();
    }
    //need to create array elements with values 0 at indexes where there is no element
    $data =  $data + array_fill(0, $bands+1, 0);
    ksort($data);
    //place the maximum (prefect grade) into the last band i.e. make last 
    //band for example 9 <= g <=10 (where 10 is the perfect grade) rather than
    //just 9 <= g <10.
    $data[$bands-1] += $data[$bands];
    unset($data[$bands]);
    return $data;

}
function quiz_report_highlighting_grading_method($quiz, $qmsubselect, $qmfilter){
    if ($quiz->attempts == 1) {//only one attempt allowed on this quiz
        return "<p>".get_string('onlyoneattemptallowed', "quiz_overview")."</p>";
    } else if (!$qmsubselect){
        return "<p>".get_string('allattemptscontributetograde', "quiz_overview")."</p>";
    } else if ($qmfilter){
        return "<p>".get_string('showinggraded', "quiz_overview")."</p>";
    }else {
        return "<p>".get_string('showinggradedandungraded', "quiz_overview",
                ('<span class="highlight">'.quiz_get_grading_option_name($quiz->grademethod).'</span>'))."</p>";
    }
}


/**
 * Get the feedback text for a grade on this quiz. The feedback is
 * processed ready for display.
 *
 * @param float $grade a grade on this quiz.
 * @param integer $quizid the id of the quiz object.
 * @return string the comment that corresponds to this grade (empty string if there is not one.
 */
function quiz_report_feedback_for_grade($grade, $quizid) {
    static $feedbackcache = array();
    if (!isset($feedbackcache[$quizid])){
        $feedbackcache[$quizid] = get_records('quiz_feedback', 'quizid', $quizid);
    }
    $feedbacks = $feedbackcache[$quizid];
    $feedbacktext = '';
    foreach ($feedbacks as $feedback) {
        if ($feedback->mingrade <= $grade && $grade < $feedback->maxgrade){
            $feedbacktext = $feedback->feedbacktext;
            break;
        }
    }

    // Clean the text, ready for display.
    $formatoptions = new stdClass;
    $formatoptions->noclean = true;
    $feedbacktext = format_text($feedbacktext, FORMAT_MOODLE, $formatoptions);

    return $feedbacktext;
}
?>
