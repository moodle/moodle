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

    $strimportquestions = get_string("importquestions", "quiz");
    $strquestions = get_string("questions", "quiz");

    $strquizzes = get_string('modulenameplural', 'quiz');
    $streditingquiz = get_string(isset($SESSION->modform->instance) ? "editingquiz" : "editquestions", "quiz");

    print_header("$course->shortname: $strimportquestions", "$course->shortname: $strimportquestions",
                 "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ".
                 "-> <a href=\"$CFG->wwwroot/mod/quiz/index.php?id=$course->id\">$strquizzes</a>".
                  " -> <a href=\"edit.php\">$streditingquiz</a> -> $strimportquestions");

    if ($form = data_submitted()) {   /// Filename

        if (isset($form->filename)) {                 // file already on server
            $newfile['tmp_name'] = $form->filename; 
            $newfile['size'] = filesize($form->filename);

        } else if (!empty($_FILES['newfile'])) {      // file was just uploaded
            $newfile = $_FILES['newfile'];
        }

        if (empty($newfile)) {
            notify(get_string("uploadproblem") );

        } else if (!isset($filename) and (!is_uploaded_file($newfile['tmp_name']) or $newfile['size'] == 0)) {
            notify(get_string("uploadnofilefound") );

        } else {  // Valid file is found

            if (! is_readable("format/$form->format/format.php")) {
                error("Format not known ($form->format)");
            }

            require("format.php");  // Parent class
            require("format/$form->format/format.php");

            $format = new quiz_file_format();

            if (! $format->importpreprocess($category)) {             // Do anything before that we need to
                error("Error occurred during pre-processing!", 
                      "$CFG->wwwroot/mod/quiz/import.php?category=$category->id");
            }

            if (! $format->importprocess($newfile['tmp_name'])) {     // Process the uploaded file
                error("Error occurred during processing!", 
                      "$CFG->wwwroot/mod/quiz/import.php?category=$category->id");
            }

            if (! $format->importpostprocess()) {                     // In case anything needs to be done after
                error("Error occurred during post-processing!", 
                      "$CFG->wwwroot/mod/quiz/import.php?category=$category->id");
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

    $fileformats = get_list_of_plugins("mod/quiz/format");
    $fileformatname = array();
    foreach ($fileformats as $key => $fileformat) {
        $formatname = get_string($fileformat, 'quiz');
        if ($formatname == "[[$fileformat]]") {
            $formatname = $fileformat;  // Just use the raw folder name
        }
        $fileformatnames[$fileformat] = $formatname;
    }
    natcasesort($fileformatnames);


    print_heading_with_help($strimportquestions, "import", "quiz");

    print_simple_box_start("center", "", "$THEME->cellheading");
    echo "<form enctype=\"multipart/form-data\" method=\"post\" action=import.php>";
    echo "<table cellpadding=5>";

    echo "<tr><td align=right>";
    print_string("category", "quiz");
    echo ":</td><td>";
    choose_from_menu($categories, "category", "$category->id", "");
    echo "</tr>";

    echo "<tr><td align=right>";
    print_string("fileformat", "quiz");
    echo ":</td><td>";
    choose_from_menu($fileformatnames, "format", "gift", "");
    helpbutton("import", $strimportquestions, "quiz");
    echo "</tr>";

    echo "<tr><td align=right>";
    print_string("upload");
    echo ":</td><td>";
    echo " <input name=\"newfile\" type=\"file\" size=\"50\">";
    echo "</tr><tr><td>&nbsp;</td><td>";
    echo " <input type=hidden name=category value=\"$category->id\">";
    echo " <input type=submit name=save value=\"".get_string("uploadthisfile")."\">";
    echo "</td></tr>";

    echo "</table>";
    echo "</form>";
    print_simple_box_end();

    print_footer($course);

?>
