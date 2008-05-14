<?php
define('QUIZ_REPORT_DEFAULT_PAGE_SIZE', 30);

define('QUIZ_REPORT_ATTEMPTS_ALL', 0);
define('QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH_NO', 1);
define('QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH', 2);
define('QUIZ_REPORT_ATTEMPTS_ALL_STUDENTS', 3);
/**
 * Get newest graded state or newest state for a number of attempts. Pass in the 
 * uniqueid field from quiz_attempt table not the id. Use question_state_is_graded
 * function to check that the question is actually graded.
 */
function quiz_get_newgraded_states($attemptids, $idxattemptq = true){
    global $CFG;
    if ($attemptids){
        $attemptidlist = join($attemptids, ',');
        $gradedstatesql = "SELECT qs.* FROM " .
                "{$CFG->prefix}question_sessions qns, " .
                "{$CFG->prefix}question_states qs " .
                "WHERE qns.attemptid IN ($attemptidlist) AND " .
                "qns.newgraded = qs.id";
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
function quiz_report_qm_filter_subselect($quiz){
    global $CFG;
    if ($quiz->attempts == 1) {//only one attempt allowed on this quiz
        return '';
    }
    $qmfilterattempts = true;
    switch ($quiz->grademethod) {
    case QUIZ_GRADEHIGHEST :
        $qmorderby = 'sumgrades DESC, timestart ASC';
        break;
    case QUIZ_GRADEAVERAGE :
        $qmfilterattempts = false;
        break;
    case QUIZ_ATTEMPTFIRST :
        $qmorderby = 'timestart ASC';
        break;
    case QUIZ_ATTEMPTLAST :
        $qmorderby = 'timestart DESC';
        break;
    }
    if ($qmfilterattempts){
        $qmsubselect = "(SELECT id FROM {$CFG->prefix}quiz_attempts " .
                "WHERE quiz = {$quiz->id} AND u.id = userid " .
                "ORDER BY $qmorderby LIMIT 1)=qa.id";
    } else {
        $qmsubselect = '';
    }
    return $qmsubselect;
}

function quiz_report_grade_bands($bands, $quizid, $useridlist){
    $sql = "SELECT
        FLOOR(qg.grade*$bands/q.grade) AS band,
        COUNT(1) AS num
    FROM
        mdl_quiz_grades qg, 
        mdl_quiz q
    WHERE qg.quiz = q.id AND qg.quiz = $quizid AND qg.userid IN ($useridlist)
    GROUP BY band
    ORDER BY band";
    $data = get_records_sql_menu($sql);
    //need to create array elements with values 0 at indexes where there is no element
    $data =  $data + array_fill(0, $bands, 0);
    ksort($data);
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
?>
