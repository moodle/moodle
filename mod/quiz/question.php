<?PHP // $Id$
      /// For creating and editing quiz questions.

    require("../../config.php");
    require("lib.php");

    optional_variable($id);

    optional_variable($type);
    optional_variable($category);

    if ($id) {
        if (! $question = get_record("quiz_questions", "id", $id)) {
            error("This question doesn't exist");
        }

        if (! $category = get_record("quiz_categories", "id", $question->category)) {
            error("This question doesn't belong to a vald category!");
        }
        if (! $course = get_record("course", "id", $category->course)) {
            error("This question category doesn't belong to a valid course!");
        }

        $type = $question->type;

        switch ($type) {
            case SHORTANSWER:
                print_heading(get_string("editingshortanswer", "quiz"));
                require("shortanswer.html");
            break;
            case TRUEFALSE:
                print_heading(get_string("editingtruefalse", "quiz"));
                require("truefalse.html");
            break;
            case MULTICHOICE:
                print_heading(get_string("editingmultichoice", "quiz"));
                require("multichoice.html");
            break;
        }

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

        redirect("edit.php");

    } 

    $grades = array(100,90,80,75,70,66.66,60,50,40,33.33,30,25,20,10,5);
    foreach ($grades as $grade) {
        $gradeoptions[$grade] = "$grade %";
        $gradeoptionsfull[$grade] = "$grade %";
        $gradeoptionsfull[-$grade] = -$grade." %";
    }
    arsort($gradeoptions, SORT_NUMERIC);
    arsort($gradeoptionsfull, SORT_NUMERIC);

    if (!$categories = get_records_sql_menu("SELECT id,name FROM quiz_categories 
                                             WHERE course='$course->id' OR publish = '1'
                                             ORDER by name ASC")) {
        error("No categories!");
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

        default:
            error("Invalid question type");
        break;
    }

    print_footer($course);
?>
