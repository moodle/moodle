<?PHP // $Id$
      /// For creating and editing quiz questions.

    require_once("../../config.php");
    require_once("lib.php");
    require_once("../../files/mimetypes.php");

    optional_variable($id);

    optional_variable($qtype);
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

        $qtype = $question->qtype;


    } else if ($category) {
        if (! $category = get_record("quiz_categories", "id", $category)) {
            error("This wasn't a valid category!");
        }
        if (! $course = get_record("course", "id", $category->course)) {
            error("This category doesn't belong to a valid course!");
        }

        $question->category = $category->id;
        $question->qtype     = $qtype;

    } else {
        error("Must specify question id or category");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("You can't modify these questions!");
    }

    $streditingquiz = get_string("editingquiz", "quiz");
    $streditingquestion = get_string("editingquestion", "quiz");

    print_header("$course->shortname: $streditingquestion", "$course->shortname: $streditingquestion",
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> 
                  -> <A HREF=\"edit.php\">$streditingquiz</A> -> $streditingquestion");

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
        $question->name         = $form->name;
        $question->questiontext = $form->questiontext;
        if (empty($form->image)) {
            $question->image = "";
        } else {
            $question->image = $form->image;
        }
        $question->category     = $form->category;

        if (!$err = formcheck($question)) {

            if (!empty($question->id)) { // Question already exists
                if (!update_record("quiz_questions", $question)) {
                    error("Could not update question!");
                }
            } else {         // Question is a new one
                if (!$question->id = insert_record("quiz_questions", $question)) {
                    error("Could not insert new question!");
                }
            }
    
            // Now to save all the answers and type-specific options
    
            switch ($question->qtype) {
                case SHORTANSWER:
                    // Delete all the old answers
                    delete_records("quiz_answers", "question", $question->id);
                    delete_records("quiz_shortanswer", "question", $question->id);
    
                    $answers = array();
                    $maxfraction = -1;
    
                    // Insert all the new answers
                    foreach ($form->answer as $key => $formanswer) {
                        if ($formanswer != "") {
                            unset($answer);
                            $answer->answer   = $formanswer;
                            $answer->question = $question->id;
                            $answer->fraction = $fraction[$key];
                            $answer->feedback = $feedback[$key];
                            if (!$answer->id = insert_record("quiz_answers", $answer)) {
                                error("Could not insert quiz answer!");
                            }
                            $answers[] = $answer->id;
                            if ($fraction[$key] > $maxfraction) {
                                $maxfraction = $fraction[$key];
                            }
                        }
                    }
    
                    unset($options);
                    $options->question = $question->id;
                    $options->answers = implode(",",$answers);
                    $options->usecase = $form->usecase;
                    if (!insert_record("quiz_shortanswer", $options)) {
                        error("Could not insert quiz shortanswer options!");
                    }
    
                    /// Perform sanity checks on fractional grades
                    if ($maxfraction != 1) {
                        $maxfraction = $maxfraction * 100;
                        notice_yesno(get_string("fractionsnomax", "quiz", $maxfraction), "question.php?id=$question->id", "edit.php");
                        print_footer($course);
                        exit;
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
                    $options->question    = $question->id;
                    $options->trueanswer  = $true->id;
                    $options->falseanswer = $false->id;
                    if (!insert_record("quiz_truefalse", $options)) {
                        error("Could not insert quiz truefalse options!");
                    }
                break;
                case MULTICHOICE:
                    delete_records("quiz_answers", "question", $question->id);
                    delete_records("quiz_multichoice", "question", $question->id);
    
                    $totalfraction = 0;
                    $maxfraction = -1;
    
                    $answers = array();
    
                    // Insert all the new answers
                    foreach ($form->answer as $key => $formanswer) {
                        if ($formanswer != "") {
                            unset($answer);
                            $answer->answer   = $formanswer;
                            $answer->question = $question->id;
                            $answer->fraction = $fraction[$key];
                            $answer->feedback = $feedback[$key];
                            if (!$answer->id = insert_record("quiz_answers", $answer)) {
                                error("Could not insert quiz answer!");
                            }
                            $answers[] = $answer->id;
    
                            if ($fraction[$key] > 0) {                 // Sanity checks
                                $totalfraction += $fraction[$key];
                            }
                            if ($fraction[$key] > $maxfraction) {
                                $maxfraction = $fraction[$key];
                            }
                        }
                    }
    
                    unset($options);
                    $options->question = $question->id;
                    $options->answers = implode(",",$answers);
                    $options->single = $form->single;
                    if (!insert_record("quiz_multichoice", $options)) {
                        error("Could not insert quiz multichoice options!");
                    }
    
                    /// Perform sanity checks on fractional grades
                    if ($options->single) {
                        if ($maxfraction != 1) {
                            $maxfraction = $maxfraction * 100;
                            notice_yesno(get_string("fractionsnomax", "quiz", $maxfraction), "question.php?id=$question->id", "edit.php");
                            print_footer($course);
                            exit;
                        }
                    } else {
                        $totalfraction = round($totalfraction,2);
                        if ($totalfraction != 1) {
                            $totalfraction = $totalfraction * 100;
                            notice_yesno(get_string("fractionsaddwrong", "quiz", $totalfraction), "question.php?id=$question->id", "edit.php");
                            print_footer($course);
                            exit;
                        }
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
    } 

    $grades = array(1,0.9,0.8,0.75,0.70,0.66666,0.60,0.50,0.40,0.33333,0.30,0.25,0.20,0.10,0.05,0);
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
                $options->single = "";
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
            print_heading_with_help(get_string("editingmultichoice", "quiz"), "multichoice", "quiz");
            require("multichoice.html");
        break;

        case RANDOM:
            print_heading_with_help(get_string("editingrandom", "quiz"), "random", "quiz");
            print_continue("edit.php");
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
