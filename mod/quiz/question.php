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

    print_header_simple("$streditingquestion", "$streditingquestion",
                 "<a href=\"$CFG->wwwroot/mod/quiz/index.php?id=$course->id\">$strquizzes</a>".
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
            // determine if the question is being used in any quiz
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

            } else { // the question is not used in any of the existing quizzes

                // we also have to check if the question is being used in the quiz
                // which is currently being set up
                if (isset($SESSION->modform)) {
                    if ($qus = explode(",", $SESSION->modform->questions)) {
                        foreach ($qus as $key => $qu) {
                            if ($qu == $delete) {
                                unset($qus[$key]);
                                unset($SESSION->modform->grades[$qu]);
                            }
                        }
                    }
                    $SESSION->modform->questions = implode(",", $qus);
                }
         
                notice_yesno(get_string("deletequestioncheck", "quiz", $question->name), 
                            "question.php?id=$question->id&delete=$delete&confirm=".md5($delete), "edit.php");
            }
            print_footer($course);
            exit;
        }
    }

    if ($form = data_submitted()) {
        $question = $QUIZ_QTYPES[$qtype]->save_question($question, $form, $course);
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
    } else {
        $defaultformat = FORMAT_MOODLE;
    }

    require('questiontypes/'.$QUIZ_QTYPES[$qtype]->name().'/editquestion.php');

    if ($usehtmleditor) { 
        use_html_editor('questiontext');
    }

    print_footer($course);

?>
