<?PHP // $Id$
      /// For creating and editing quiz questions.

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

    require_login($course->id);

    if (!isteacheredit($course->id)) {
        error("You can't modify these questions!");
    }

    $strquizzes = get_string('modulenameplural', 'quiz');
    $streditingquiz = get_string(isset($SESSION->modform->instance) ? "editingquiz" : "editquestions", "quiz");
    $streditingquestion = get_string("editingquestion", "quiz");

    print_header("$course->shortname: $streditingquestion", "$course->shortname: $streditingquestion",
                 "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ".
                 "-> <a href=\"$CFG->wwwroot/mod/quiz/index.php?id=$course->id\">$strquizzes</a>".
                  " -> <a href=\"edit.php\">$streditingquiz</a> -> $streditingquestion");

    if (isset($delete)) {
        if (isset($confirm)) {
            if ($confirm == md5($delete)) {
                if (!delete_records("quiz_questions", "id", $question->id)) {
                    error("An error occurred trying to delete question (id $question->id)");
                }
                redirect("edit.php");
            } else {
                error("Confirmation string was incorrect");
            }
            
        } else {
            if ($category->publish) {
                $quizzes = get_records("quiz");
            } else {
                $quizzes = get_records("quiz", "course", $course->id);
            }
            $beingused = array();
            if ($quizzes) {
                foreach ($quizzes as $quiz) {
                    $qqq = explode(",", $quiz->questions);
                    foreach ($qqq as $key => $value) {
                        if ($value == $delete) {
                            $beingused[] = $quiz->name;
                        }
                    }
                }
            }
            if ($beingused) {
                $beingused = implode(", ", $beingused);
                $beingused = get_string("questioninuse", "quiz", "<I>$question->name</I>")."<P>".$beingused;
                notice($beingused, "edit.php");
            } else {
                notice_yesno(get_string("deletequestioncheck", "quiz", $question->name), 
                            "question.php?id=$question->id&delete=$delete&confirm=".md5($delete), "edit.php");
            }
            print_footer($course);
            exit;
        }
    }

    if ($form = data_submitted()) { 

        // First, save the basic question itself

        $question->name               = $form->name;
        $question->questiontext       = $form->questiontext;
        $question->questiontextformat = $form->questiontextformat;

        if (empty($form->image)) {
            $question->image = "";
        } else {
            $question->image = $form->image;
        }

        if (isset($form->defaultgrade)) {
            $question->defaultgrade = $form->defaultgrade;
        }

        if ($err = formcheck($question)) {
            notify(get_string("someerrorswerefound"));

        } else {

            if (!empty($question->id)) { // Question already exists
                $question->version ++;    // Update version number of question
                if (!update_record("quiz_questions", $question)) {
                    error("Could not update question!");
                }
            } else {         // Question is a new one
                $question->stamp = make_unique_id_code();  // Set the unique code (not to be changed)
                $question->version = 1;
                if (!$question->id = insert_record("quiz_questions", $question)) {
                    error("Could not insert new question!");
                }
            }
    
            // Now to save all the answers and type-specific options

            $form->id       = $question->id;
            $form->qtype    = $question->qtype;
            $form->category = $question->category;

            $result = quiz_save_question_options($form);

            if (!empty($result->error)) {
                error($result->error);
            }

            if (!empty($result->notice)) {
                notice($result->notice, "question.php?id=$question->id");
            }

            if (!empty($result->noticeyesno)) {
                notice_yesno($result->noticeyesno, "question.php?id=$question->id", "edit.php");
                print_footer($course);
                exit;
            }
    
            redirect("edit.php");
        }
    } 

    $grades = array(1,0.9,0.8,0.75,0.70,0.66666,0.60,0.50,0.40,0.33333,0.30,0.25,0.20,0.16666,0.10,0.05,0);
    foreach ($grades as $grade) {
        $percentage = 100 * $grade;
        $neggrade = -$grade;
        $gradeoptions["$grade"] = "$percentage %";
        $gradeoptionsfull["$grade"] = "$percentage %";
        $gradeoptionsfull["$neggrade"] = -$percentage." %";
    }
    $gradeoptionsfull["0"] = $gradeoptions["0"] = get_string("none");

    arsort($gradeoptions, SORT_NUMERIC);
    arsort($gradeoptionsfull, SORT_NUMERIC);

    if (!$categories = quiz_get_category_menu($course->id, true)) {
        error("No categories!");
    }


    make_upload_directory("$course->id");    // Just in case
    $coursefiles = get_directory_list("$CFG->dataroot/$course->id", $CFG->moddata);
    foreach ($coursefiles as $filename) {
        if (mimeinfo("icon", $filename) == "image.gif") {
            $images["$filename"] = $filename;
        }
    }

    // Print the question editing form

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

    // Set up some Richtext editing if necessary
    if ($usehtmleditor = can_use_richtext_editor()) {
        $defaultformat = FORMAT_HTML;
        $onsubmit = "onsubmit=\"copyrichtext(theform.questiontext);\"";
    } else {
        $defaultformat = FORMAT_MOODLE;
        $onsubmit = "";
    }

    switch ($qtype) {
        case SHORTANSWER:
            if (!empty($question->id)) {
                $options = get_record("quiz_shortanswer", "question", $question->id);
            } else {
                $options->usecase = 0;
            }
            if (!empty($options->answers)) {
                $answersraw = get_records_list("quiz_answers", "id", $options->answers);
            }
            for ($i=0; $i<6; $i++) {
                $answers[] = "";   // Make answer slots, default as blank
            }
            if (!empty($answersraw)) {
                $i=0;
                foreach ($answersraw as $answer) {
                    $answers[$i] = $answer;   // insert answers into slots
                    $i++;
                }
            }
            print_heading_with_help(get_string("editingshortanswer", "quiz"), "shortanswer", "quiz");
            require("shortanswer.html");
        break;

        case TRUEFALSE:
            if (!empty($question->id)) {
                $options = get_record("quiz_truefalse", "question", "$question->id");
            }
            if (!empty($options->trueanswer)) {
                $true    = get_record("quiz_answers", "id", $options->trueanswer);
            } else {
                $true->fraction = 1;
                $true->feedback = "";
            }
            if (!empty($options->falseanswer)) {
                $false   = get_record("quiz_answers", "id", "$options->falseanswer");
            } else {
                $false->fraction = 0;
                $false->feedback = "";
            }

            if ($true->fraction > $false->fraction) {
                $question->answer = 1;
            } else {
                $question->answer = 0;
            }

            print_heading_with_help(get_string("editingtruefalse", "quiz"), "truefalse", "quiz");
            require("truefalse.html");
        break;

        case MULTICHOICE:
            if (!empty($question->id)) {
                $options = get_record("quiz_multichoice", "question", $question->id);
            } else {
                $options->single = 1;
            }
            if (!empty($options->answers)) {
                $answersraw = get_records_list("quiz_answers", "id", $options->answers);
            }
            for ($i=0; $i<QUIZ_MAX_NUMBER_ANSWERS; $i++) {
                $answers[] = "";   // Make answer slots, default as blank
            }
            if (!empty($answersraw)) {
                $i=0;
                foreach ($answersraw as $answer) {
                    $answers[$i] = $answer;   // insert answers into slots
                    $i++;
                }
            }
            print_heading_with_help(get_string("editingmultichoice", "quiz"), "multichoice", "quiz");
            require("multichoice.html");
        break;

        case MATCH:
            if (!empty($question->id)) {
                $options = get_record("quiz_match", "question", $question->id);
                if (!empty($options->subquestions)) {
                    $oldsubquestions = get_records_list("quiz_match_sub", "id", $options->subquestions);
                }
            }
            if (empty($subquestions) and empty($subanswers)) {
                for ($i=0; $i<QUIZ_MAX_NUMBER_ANSWERS; $i++) {
                    $subquestions[] = "";   // Make question slots, default as blank
                    $subanswers[] = "";     // Make answer slots, default as blank
                }
                if (!empty($oldsubquestions)) {
                    $i=0;
                    foreach ($oldsubquestions as $oldsubquestion) {
                        $subquestions[$i] = $oldsubquestion->questiontext;   // insert questions into slots
                        $subanswers[$i] = $oldsubquestion->answertext;       // insert answers into slots
                        $i++;
                    }
                }
            }
            print_heading_with_help(get_string("editingmatch", "quiz"), "match", "quiz");
            require("match.html");
        break;

        case RANDOMSAMATCH:
            if (!empty($question->id)) {
                $options = get_record("quiz_randomsamatch", "question", $question->id);
            } else {
                $options->choose = "";
            }
            $numberavailable = count_records("quiz_questions", "category", $category->id, "qtype", SHORTANSWER);
            print_heading_with_help(get_string("editingrandomsamatch", "quiz"), "randomsamatch", "quiz");
            require("randomsamatch.html");
        break;

        case RANDOM:
            print_heading_with_help(get_string("editingrandom", "quiz"), "random", "quiz");
            require("random.html");
        break;

        case DESCRIPTION:
            print_heading_with_help(get_string("editingdescription", "quiz"), "description", "quiz");
            require("description.html");
        break;

        case MULTIANSWER:
            print_heading_with_help(get_string("editingmultianswer", "quiz"), "multianswer", "quiz");
            require("editmultianswer.php");
        break;

        case NUMERICAL:
            // This will only support one answer of the type NUMERICAL
            // However, lib.php has support for multiple answers
            if (!empty($question->id)) {
                $answersraw= quiz_get_answers($question);
            }
            $answers= array();
            for ($i=0; $i<6; $i++) {
                $answers[$i]->answer   = ""; // Make answer slots, default as blank...
                $answers[$i]->min      = "";
                $answers[$i]->max      = "";
                $answers[$i]->feedback = "";
            }
            if (!empty($answersraw)) {
                $i=0;
                foreach ($answersraw as $answer) {
                    $answers[$i] = $answer;
                    $i++;
                }
            }
            print_heading_with_help(get_string("editingnumerical", "quiz"), "numerical", "quiz");
            require("numerical.html");
        break;


        default:
            error("Invalid question type");
        break;
    }

    print_footer($course);


function formcheck($question) {
   $err = array();

   if (empty($question->name)) {
       $err["name"] = get_string("missingname", "quiz");
   }
   if (empty($question->questiontext)) {
       $err["questiontext"] = get_string("missingquestiontext", "quiz");
   }
   return $err;
}

?>
