<?PHP // $Id$
      // Import quiz questions into the given category

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($category);
    optional_variable($format);

    if (! $category = get_record("quiz_categories", "id", $category)) {
        error("This wasn't a valid category!");
    }

    if (! $course = get_record("course", "id", $category->course)) {
        error("This category doesn't belong to a valid course!");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("Only the teacher can import quiz questions!");
    }

    $streditingquiz = get_string("editingquiz", "quiz");
    $strimportquestions = get_string("importquestions", "quiz");
    $strquestions = get_string("questions", "quiz");

    print_header("$course->shortname: $strimportquestions", "$course->shortname: $strimportquestions",
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> 
                  -> <A HREF=\"edit.php\">$streditingquiz</A> -> $strimportquestions");

    if ($form = data_submitted()) {   /// Filename

        if (!empty($_FILES['newfile'])) {
            $newfile = $_FILES['newfile'];
        }

        if (empty($newfile)) {
            notify(get_string("uploadproblem") );

        } else if (!is_uploaded_file($newfile['tmp_name']) or $newfile['size'] == 0) {
            notify(get_string("uploadnofilefound") );

        } else {

            if (! is_readable("format/$form->format".".php")) {
                error("Format not known ($form->format)");
            }

            require("format/$form->format".".php");

            $format = new quiz_file_format();

            if (! $format->preprocess($category)) {
                error("Error occurred during pre-processing.");
            }

            if (! $lines = $format->readdata($newfile['tmp_name'])) {
                error("File could not be read, or was empty");
            }

            if (! $questions = $format->readquestions($lines)) {
                error("There are no questions in this file!");
            }

            notify("Importing ".count($questions)." questions");

            $count = 0;
            $questionids = array();

            foreach ($questions as $question) {
                $count++;

                echo "<hr><p><b>$count</b>. ".stripslashes($question->questiontext)."</p>";

                $question->category = $category->id;

                if (!$question->id = insert_record("quiz_questions", $question)) {
                    error("Could not insert new question!");
                }

                $questionids[] = $question->id;

                // Now to save all the answers and type-specific options

                $result = quiz_save_question_options($question);

                if (!empty($result->error)) {
                    error($result->error);
                }

                if (!empty($result->notice)) {
                    notice($result->notice);
                }
            }

            if (! $format->postprocess($category, $questionids)) {
                error("Error occurred during post-processing.");
            }

            echo "<hr>";
            print_continue("edit.php");
            print_footer($course);
            exit;
        }
    } 

    /// Print upload form

    if (!$categories = quiz_get_category_menu($course->id, true)) {
        error("No categories!");
    }

    print_heading_with_help($strimportquestions, "import", "quiz");

    print_simple_box_start("center", "", "$THEME->cellheading");
    echo "<FORM ENCTYPE=\"multipart/form-data\" METHOD=\"POST\" ACTION=import.php>";
    echo "<TABLE cellpadding=5>";
    echo "<TR><TD align=right>";
    print_string("category", "quiz");
    echo ":</TD><TD>";
    choose_from_menu($categories, "category", "$category->id", "");
    echo "</TR>";

    echo "<TR><TD align=right>";
    print_string("fileformat", "quiz");
    echo ":</TD><TD>";
    choose_from_menu($QUIZ_FILE_FORMAT, "format", "custom", "");
    helpbutton("import", $strimportquestions, "quiz");
    echo "</TR><TR><TD align=right>";
    print_string("upload");
    echo ":</TD><TD>";
    echo " <INPUT NAME=\"newfile\" TYPE=\"file\" size=\"50\">";
    echo "</TR><TR><TD>&nbsp;</TD><TD>";
    echo " <INPUT TYPE=hidden NAME=category VALUE=\"$category->id\">";
    echo " <INPUT TYPE=submit NAME=save VALUE=\"".get_string("uploadthisfile")."\">";
    echo "</TD></TR>";
    echo "</TABLE>";
    echo "</FORM>";
    print_simple_box_end();

    print_footer($course);


?>
