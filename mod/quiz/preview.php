<?php // $Id$

require_once("../../config.php");
require_once("locallib.php");

require_variable($id);        // question id
optional_variable($quizid, 0);

if (! $question = get_record("quiz_questions", "id", $id)) {
    error("This question doesn't exist");
}
if (! $category = get_record("quiz_categories", "id", $question->category)) {
    error("This question doesn't belong to a valid category!");
}
if ($quizid) {
    if (! $quiz = get_record('quiz', 'id', $quizid)) {
        error('Incorrect quiz id');
    }
} else {
    // make fake quiz
    $quiz->id = 0;
    $quiz->course = $category->course;
    $quiz->name = "";
    $quiz->intro = "";
    $quiz->timeopen = 0;
    $quiz->timeclose = 0;
    $quiz->attempts = $CFG->quiz_attempts;
    $quiz->attemptonlast = $CFG->quiz_attemptonlast;
    $quiz->feedback = $CFG->quiz_showfeedback;
    $quiz->correctanswers = $CFG->quiz_showanswer;
    $quiz->grademethod = $CFG->quiz_grademethod;
    $quiz->review = $CFG->quiz_allowreview;
    $quiz->shufflequestions = $CFG->quiz_shufflequestions;
    $quiz->shuffleanswers = $CFG->quiz_shuffleanswers;
    $quiz->questions = "$question->id";
    $quiz->sumgrades = $question->defaultgrade;
    $quiz->grade = $CFG->quiz_maximumgrade;
    $quiz->timecreated = 0;
    $quiz->timemodified = 0;
    $quiz->timelimit = $CFG->quiz_timelimit;
    $quiz->password = $CFG->quiz_password;
    $quiz->subnet = $CFG->quiz_subnet;
    $quiz->popup = $CFG->quiz_popup;
}

$qtype = $question->qtype;

require_login();

if (!isteacher()) {
    error('This page is for teachers only');
}

if (!isteacher($category->course) and !$category->publish) {
    error("You can't preview these questions!");
}

print_header();
print_heading(get_string("previewquestion","quiz"));

if ($rawanswers = data_submitted()) {
    $rawanswers = (array)$rawanswers;

    foreach ($rawanswers as $key => $value) { // Parse input for question->response
        $postedId = quiz_extract_posted_id($key);
        if ($id == $postedId) $question->response[$key] = trim($value);
    }

    print_simple_box_start("center", "90%");
    
    $resultdetails = $QUIZ_QTYPES[$question->qtype]->grade_response($question, quiz_qtype_nameprefix($question));
    
    $question->maxgrade = 1;
    $quiz->correctanswers=1;
    $quiz->feedback=1;
    $quiz->grade=1;
    $QUIZ_QTYPES[$question->qtype]->print_question(1, $quiz, $question, true, $resultdetails);

    print_simple_box_end();
    echo "<p align=\"center\"><input name=\"backbtn\" type=\"button\" 
             onclick=\"history.back()\" value=\"".get_string("back", "quiz")."\" />\n";
    echo "<input type=\"button\" onClick=\"window.close()\" value=\"" . get_string("close", "quiz") . "\" /></p>\n";

} else { //show question list
    echo "<form method=\"post\" action=\"preview.php\">\n";
    echo "<input type=\"hidden\" name=\"id\" value=\"$id\" />\n";

    print_simple_box_start("center", "90%");
    $readonly = false;
    $resultdetails = NULL;
    $QUIZ_QTYPES[$question->qtype]->print_question(1, $quiz, $question, $readonly, $resultdetails);
    print_simple_box_end();

    echo "<br />";
    echo "<center>";
    echo "<input name=\"submit\" type=\"submit\" value=\"".get_string("checkanswer", "quiz")."\" />\n";
    echo "<input type=\"button\" onClick=\"window.close()\" value=\"" . get_string("close", "quiz") . "\" />";
    echo "</center>";
    echo "</form>";
}

print_footer();

?>
