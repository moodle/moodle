<?php
    require_once("../../config.php");
    require_once("lib.php");
    require_once("../../files/mimetypes.php");

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
        error("Could not find question type: '$qtype'");
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

    echo "<p align='center'><input type=\"button\" onClick=\"window.close()\" value=\"" . get_string("close", "quiz") . "\"></p>";

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

    if($rawanswers = data_submitted()) {
      $rawanswers = (array)$rawanswers;

      $answers = array();
      $feedbacks = array();
      $qids = array();

      foreach ($rawanswers as $key => $value) { // Parse input for question->response
        $postedId = quiz_extract_posted_id($key);
        if ($id == $postedId) {
          $question->response[$key] = trim($value);
          $questionType = $QUIZ_QTYPES[$question->qtype]->name();
          $rezult = get_exp_answers($id); //answers from the database

          if($rezult) {
            foreach ($rezult as $qid => $answer) {
              if($qid == $value || $answer->answer == $value) {
                $feedbacks[$qid] = "&nbsp;-&nbsp;<span class=\"feedbacktext\">".format_text($answer->feedback, true, false)."</span>";
                if($answer->fraction > 0)
                  $answers[$qid] = "<span class=\"highlight\">" . $answer->answer . "</span>";
                else
                  $answers[$qid] = $answer->answer;
              } else {
                if(!isset($answers[$qid])) {
                  if($answer->fraction > 0)
                    $answers[$qid] = "<span class=\"highlight\">" . $answer->answer . "</span>";
                  else
                    $answers[$qid] = $answer->answer;
                  $feedbacks[$qid] = '';
                }
              }
              $qids[] = $qid;
            }
          }//end if rezult
        }//end if pId==id
      }//end for each rowanswers

      print_simple_box_start("center", "90%");
      $QUIZ_QTYPES[$question->qtype]->print_question
            (1, 0, $question, true, $resultdetails);

      $qids = array_unique($qids); //erease duplicates
      for($i = 0; $i < count($qids); $i++) {
        global $THEME;
        echo "<p align=\"left\">" . $answers[$qids[$i]] . format_text($feedbacks[$qids[$i]], true, false)."</p>";
      }
      print_simple_box_end();

    } else {//show question list
      echo "<form method=\"post\" action=\"preview.php\" $onsubmit>\n";
      echo "<input type=\"hidden\" name=\"id\" value=\"$id\" />\n";

      print_simple_box_start("center", "90%");
      $nextquestionnumber = $QUIZ_QTYPES[$question->qtype]->print_question
              ($nextquestionnumber, $quiz, $question, $readonly, $resultdetails);
      print_simple_box_end();

      echo "<br />";
      echo "<center>";
      echo "<input name=\"submit\" type=\"submit\" value=\"".get_string("checkanswer", "quiz")."\"/>\n";
      echo "</center>";
      echo "</form>";
    }

?>
