<?php
define('QUIZ_REPORT_DEFAULT_PAGE_SIZE', 30);
/**
 * Get newest graded state or newest state for a number of attempts. Pass in the 
 * uniqueid field from quiz_attempt table not the id. Use question_state_is_graded
 * function to check that the question is actually graded.
 */
function quiz_get_newgraded_states($attemptids, $idxattemptq = true){
    global $CFG;
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
?>