<?PHP // $Id$
      // A quick way to add lots of questions to a category (and a quiz)

	require_once("../../config.php");
	require_once("lib.php");

    require_variable($category);

    // This script can only be called while editing a quiz

    if (!isset($SESSION->modform)) {
        error("You have used this page incorrectly!");
    } else {
        $modform = $SESSION->modform;
    }

    if (! $category = get_record("quiz_categories", "id", $category)) {
        error("Course ID is incorrect");
    }

    if (! $course = get_record("course", "id", $category->course)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("Only teachers can use this page!");
    }



/// If data submitted, then process and store.

    if ($form = data_submitted()) {
        if ($form->randomcreate > 0) {
            $existing = count_records("quiz_questions", "qtype", RANDOM, "category", $category->id);
            $randomcreate = $form->randomcreate - $existing;

            if ($randomcreate > 0) {
                $newquestionids = array();

                $question->qtype = RANDOM;
                $question->category = $category->id;
                $question->name = get_string("random", "quiz")." (".$category->name.")";
                $question->questiontext = "---";
                $question->image = "";
                $question->defaultgrade = $form->randomgrade;
                for ($i=0; $i<$randomcreate; $i++) {
                    $question->stamp = make_unique_id_code();  // Set the unique code (not to be changed)
                    if (!$newquestionids[] = insert_record("quiz_questions", $question)) {
                        error("Could not insert new random question!");
                    }
                }

                // Add them to the quiz if necessary
                if (!empty($form->addquestionstoquiz)) {
                    if (!empty($modform->questions)) {
                        $questionids = explode(",", $modform->questions);
                        foreach ($questionids as $questionid) {
                            foreach ($newquestionids as $key => $newquestionid) {
                                if ($newquestionid == $questionid) {
                                    unset($newquestionids[$key]);
                                    break;
                                }
                            }
                        }
                    } else {
                        $questionids = array();
                    }

                    foreach ($newquestionids as $newquestionid) {
                        $modform->grades[$newquestionid] = $form->randomgrade;
                        $modform->sumgrades += $form->randomgrade;
                    }

                    $newquestionids = array_merge($questionids, $newquestionids);
                    $modform->questions = implode(",", $newquestionids);
                    $SESSION->modform = $modform;
                }
            }
        }
        redirect("edit.php");
    }


/// Otherwise print the form

/// Print headings

    $strquestions = get_string("questions", "quiz");
    $strpublish = get_string("publish", "quiz");
    $strdelete = get_string("delete");
    $straction = get_string("action");
    $stradd = get_string("add");
    $strcancel = get_string("cancel");
    $strsavechanges = get_string("savechanges");
    $strbacktoquiz = get_string("backtoquiz", "quiz");
    $strquizzes = get_string('modulenameplural', 'quiz');
    $streditingquiz = get_string(isset($SESSION->modform->instance) ? "editingquiz" : "editquestions", "quiz");
    $strcreatemultiple = get_string("createmultiple", "quiz");

    print_header("$course->shortname: $strcreatemultiple", "$course->shortname: $strcreatemultiple",
                 "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ".
                 " -> <a href=\"$CFG->wwwroot/mod/quiz/index.php?id=$course->id\">$strquizzes</a>".
                 " -> <a href=\"edit.php\">$streditingquiz</a> -> $strcreatemultiple");


    print_heading_with_help($strcreatemultiple, "createmultiple", "quiz");

    if (!$categories = quiz_get_category_menu($course->id, true)) {
        error("No categories!");
    }

    for ($i=1;$i<=100; $i++) {
        $randomcount[$i] = $i;
    }
    for ($i=1;$i<=10; $i++) {
        $gradecount[$i] = $i;
    }
    $options = array();
    $options[0] = get_string("no");
    $options[1] = get_string("yes");

    print_simple_box_start("center", "", "$THEME->cellheading");
    echo "<FORM METHOD=\"POST\" ACTION=multiple.php>";
    echo "<TABLE cellpadding=5>";
    echo "<TR><TD align=right>";
    print_string("category", "quiz");
    echo ":</TD><TD>";
    choose_from_menu($categories, "category", "$category->id", "");
    echo "</TR>";

    echo "<TR><TD align=right>";
    print_string("randomcreate", "quiz");
    echo ":</TD><TD>";
    choose_from_menu($randomcount, "randomcreate", "10", "");
    echo "</TR>";

    echo "<TR><TD align=right>";
    print_string("defaultgrade", "quiz");
    echo ":</TD><TD>";
    choose_from_menu($gradecount, "randomgrade", "1", "");
    echo "</TR>";

    echo "<TR><TD align=right>";
    print_string("addquestionstoquiz", "quiz");
    echo ":</TD><TD>";
    choose_from_menu($options, "addquestionstoquiz", "1", "");
    echo "</TR>";

    echo "<TR><TD>&nbsp;</TD><TD>";
    echo " <INPUT TYPE=hidden NAME=category VALUE=\"$category->id\">";
    echo " <INPUT TYPE=submit NAME=save VALUE=\"$strcreatemultiple\">";
    echo "</TD></TR>";
    echo "</TABLE>";
    echo "</FORM>";
    print_simple_box_end();

    print_footer();

?>
