<?php // $Id$

require_once("../../config.php");
require_once("lib.php");
require_once("$CFG->dirroot/files/mimetypes.php");

optional_variable($id);        // question id
optional_variable($qtype);
optional_variable($category);

if ($id) {
    if (! $question = get_record("quiz_questions", "id", $id)) {
        error("This question doesn't exist");
    }
    if (!empty($category)) {
        $question->category = $category;
    }
    if (! $category = get_record("quiz_categories", "id", $question->category)) {
        error("This question doesn't belong to a valid category!");
    }
    if (! $course = get_record("course", "id", $category->course)) {
        error("This question category doesn't belong to a valid course!");
    }

    $qtype = $question->qtype;


} else if ($category) {
    if (! $category = get_record("quiz_categories", "id", $category)) {
        error("This wasn't a valid category!");
    }
    if (! $course = get_record("course", "id", $category->course)) {
        error("This category doesn't belong to a valid course!");
    }

    $question->category = $category->id;
    $question->qtype    = $qtype;

} else {
    error("Must specify question id or category");
}

if (empty($qtype)) {
    error("No question type was specified!");
} else if (!isset($QUIZ_QTYPES[$qtype])) {
    error("Could not find specified question type");
}

require_login($course->id);


if (!isteacheredit($course->id)) {
    error("You can't modify these questions!");
}


$strquizzes = get_string('modulenameplural', 'quiz');
$streditingquiz = get_string(isset($SESSION->modform->instance) ? "editingquiz" : "editquestions", "quiz");
$streditingquestion = get_string("editingquestion", "quiz");

print_header();
print_heading(get_string("previewquestion","quiz"));

if (empty($question->id)) {
    $question->id = "";
}
if (empty($question->name)) {
    $question->name = "";
}
if (empty($question->questiontext)) {
    $question->questiontext = "";
}
if (empty($question->image)) {
    $question->image = "";
}

if ($results && isset($results->details[$question->id])) {
    $details = $results->details[$question->id];
} else {
    $details = false;
}

if ($rawanswers = data_submitted()) {
    $rawanswers = (array)$rawanswers;

    $answers = array();
    $feedbacks = array();
    $qids = array();
    $fraction = 0;

    foreach ($rawanswers as $key => $value) { // Parse input for question->response
        $postedId = quiz_extract_posted_id($key);
        if ($id == $postedId) $question->response[$key] = trim($value);
    }

    print_simple_box_start("center", "90%");

    //simulate quiz only for quiestion previews
    class FakeQuiz {
        var $correctanswers;
        var $feedback;
        var $grade;
        function FakeQuiz() {
            $this->correctanswers = 1;
            $this->feedback = 1;
            $this->grade = 1;
        }
    }

    $question->maxgrade = 1; //this shouldn't be like that
    $resultdetails = $QUIZ_QTYPES[$question->qtype]->grade_response($question, quiz_qtype_nameprefix($question));
    $quiz = new FakeQuiz();

    $QUIZ_QTYPES[$question->qtype]->print_question(1, $quiz, $question, true, $resultdetails);

    print_simple_box_end();
    echo "<p align=\"center\"><input name=\"backbtn\" type=\"button\" 
             onclick=\"history.back()\" value=\"".get_string("back", "quiz")."\" />\n";
    echo "<input type=\"button\" onClick=\"window.close()\" value=\"" . get_string("close", "quiz") . "\" /></p>\n";

} else { //show question list
    echo "<form method=\"post\" action=\"preview.php\" $onsubmit>\n";
    echo "<input type=\"hidden\" name=\"id\" value=\"$id\" />\n";

    print_simple_box_start("center", "90%");
    $nextquestionnumber = $QUIZ_QTYPES[$question->qtype]->print_question($nextquestionnumber, $quiz,  
                                                                         $question, $readonly, $resultdetails);
    print_simple_box_end();

    echo "<br />";
    echo "<center>";
    echo "<input name=\"submit\" type=\"submit\" value=\"".get_string("checkanswer", "quiz")."\" />\n";
    echo "<input type=\"button\" onClick=\"window.close()\" value=\"" . get_string("close", "quiz") . "\" />";
    echo "</center>";
    echo "</form>";
}

?>
