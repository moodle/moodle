<?PHP // $Id$
      /// For creating and editing quiz questions.

    require("../../config.php");
    require("lib.php");
    require("../../files/mimetypes.php");

    optional_variable($id);

    optional_variable($type);
    optional_variable($category);

    if ($id) {
        if (! $question = get_record("quiz_questions", "id", $id)) {
            error("This question doesn't exist");
        }

        if (! $category = get_record("quiz_categories", "id", $question->category)) {
            error("This question doesn't belong to a valid category!");
        }
        if (! $course = get_record("course", "id", $category->course)) {
            error("This question category doesn't belong to a valid course!");
        }

        $type = $question->type;


    } else if ($category) {
        if (! $category = get_record("quiz_categories", "id", $category)) {
            error("This wasn't a valid category!");
        }
        if (! $course = get_record("course", "id", $category->course)) {
            error("This category doesn't belong to a valid course!");
        }

        $question->category = $category->id;
        $question->type     = $type;

    } else {
        error("Must specify question id or category");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("You can't modify this course!");
    }

    $streditingquiz = get_string("editingquiz", "quiz");
    $streditingquestion = get_string("editingquestion", "quiz");

    print_header("$course->shortname: $streditingquestion", "$course->shortname: $streditingquestion",
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> 
                   -> <A HREF=\"$HTTP_REFERER\">$streditingquiz</A> -> $streditingquestion");

    if (match_referer() and isset($HTTP_POST_VARS)) {    // question submitted

        $form = (object)$HTTP_POST_VARS;
        
        // First, save the basic question itself
        $question->name         = $form->name;
        $question->questiontext = $form->questiontext;
        $question->image        = $form->image;
        $question->category     = $form->category;

        if ($question->id) { // Question already exists
            if (!update_record("quiz_questions", $question)) {
                error("Could not update question!");
            }
        } else {         // Question is a new one
            if (!$question->id = insert_record("quiz_questions", $question)) {
                error("Could not insert new question!");
            }
        }

        // Now to save all the answers and type-specific options

        switch ($question->type) {
            case SHORTANSWER:
                // Delete all the old answers
                delete_records("quiz_answers", "question", $question->id);
                delete_records("quiz_shortanswer", "question", $question->id);

                $answers = array();

                // Insert all the new answers
                foreach ($form->answer as $key => $formanswer) {
                    if ($formanswer) {
                        unset($answer);
                        $answer->answer   = $formanswer;
                        $answer->question = $question->id;
                        $answer->fraction = $fraction[$key];
                        $answer->feedback = $feedback[$key];
                        if (!$answer->id = insert_record("quiz_answers", $answer)) {
                            error("Could not insert quiz answer!");
                        }
                        $answers[] = $answer->id;
                    }
                }

                unset($options);
                $options->question = $question->id;
                $options->answers = implode(",",$answers);
                $options->usecase = $form->usecase;
                if (!insert_record("quiz_shortanswer", $options)) {
                    error("Could not insert quiz shortanswer options!");
                }
            break;
            case TRUEFALSE:
                delete_records("quiz_answers", "question", $question->id);
                delete_records("quiz_truefalse", "question", $question->id);

                $true->answer   = get_string("true", "quiz");
                $true->question = $question->id;
                $true->fraction = $form->answer;
                $true->feedback = $form->feedbacktrue;
                if (!$true->id = insert_record("quiz_answers", $true)) {
                    error("Could not insert quiz answer \"true\")!");
                }

                $false->answer   = get_string("false", "quiz");
                $false->question = $question->id;
                $false->fraction = 1 - (int)$form->answer;
                $false->feedback = $form->feedbackfalse;
                if (!$false->id = insert_record("quiz_answers", $false)) {
                    error("Could not insert quiz answer \"false\")!");
                }

                unset($options);
                $options->question = $question->id;
                $options->true     = $true->id;
                $options->false    = $false->id;
                if (!insert_record("quiz_truefalse", $options)) {
                    error("Could not insert quiz truefalse options!");
                }
            break;
            case MULTICHOICE:
                delete_records("quiz_answers", "question", $question->id);
                delete_records("quiz_multichoice", "question", $question->id);

                $answers = array();

                // Insert all the new answers
                foreach ($form->answer as $key => $formanswer) {
                    if ($formanswer) {
                        unset($answer);
                        $answer->answer   = $formanswer;
                        $answer->question = $question->id;
                        $answer->fraction = $fraction[$key];
                        $answer->feedback = $feedback[$key];
                        if (!$answer->id = insert_record("quiz_answers", $answer)) {
                            error("Could not insert quiz answer!");
                        }
                        $answers[] = $answer->id;
                    }
                }

                unset($options);
                $options->question = $question->id;
                $options->answers = implode(",",$answers);
                $options->single = $form->single;
                if (!insert_record("quiz_multichoice", $options)) {
                    error("Could not insert quiz multichoice options!");
                }
            break;
            case RANDOM:
                echo "<P>Not supported yet</P>";
            break;
            default:
                error("Non-existent question type!");
            break;
        }

        redirect("edit.php");

    } 

    $grades = array(1,0.9,0.8,0.75,0.70,0.6666,0.60,0.50,0.40,0.3333,0.30,0.25,0.20,0.10,0.05,0);
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


    $coursefiles = get_directory_list("$CFG->dataroot/$course->id", $CFG->moddata);
    foreach ($coursefiles as $filename) {
        if (mimeinfo("icon", $filename) == "image.gif") {
            $images["$filename"] = $filename;
        }
    }

    // Print the question editing form

    switch ($type) {
        case SHORTANSWER:
            $options = get_record("quiz_shortanswer", "question", "$question->id");// OK to fail
            $answersraw = get_records_list("quiz_answers", "id", "$options->answers");// OK to fail
            print_heading(get_string("editingshortanswer", "quiz"));
            if ($answersraw) {
                foreach ($answersraw as $answer) {
                    $answers[] = $answer;   // to renumber index 0,1,2...
                }
            }
            require("shortanswer.html");
        break;

        case TRUEFALSE:
            $options = get_record("quiz_truefalse", "question", "$question->id");  // OK to fail
            $true    = get_record("quiz_answers", "id", "$options->true");         // OK to fail
            $false   = get_record("quiz_answers", "id", "$options->false");        // OK to fail
            if ($true->fraction > $false->fraction) {
                $question->answer = 1;
            } else {
                $question->answer = 0;
            }
            print_heading(get_string("editingtruefalse", "quiz"));
            require("truefalse.html");
        break;

        case MULTICHOICE:
            $options = get_record("quiz_multichoice", "question", "$question->id");// OK to fail
            $answersraw = get_records_list("quiz_answers", "id", "$options->answers");// OK to fail
            if ($answersraw) {
                foreach ($answersraw as $answer) {
                    $answers[] = $answer;   // to renumber index 0,1,2...
                }
            }
            print_heading(get_string("editingmultichoice", "quiz"));
            require("multichoice.html");
        break;
            case RANDOM:
                print_heading("Sorry, random questions are not supported yet");
                print_continue("edit.php");
            break;

        default:
            error("Invalid question type");
        break;
    }

    print_footer($course);
?>
