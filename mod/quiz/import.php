<?php // $Id$
      // Import quiz questions into the given category

    require_once("../../config.php");
    require_once("locallib.php");

    $category = required_param('category', PARAM_INT);

    if (! $category = get_record("quiz_categories", "id", $category)) {
        error("This wasn't a valid category!");
    }

    if (! $course = get_record("course", "id", $category->course)) {
        error("This category doesn't belong to a valid course!");
    }

    require_login($course->id, false);

    if (!isteacher($course->id)) {
        error("Only the teacher can import quiz questions!");
    }

    
    // ensure the files area exists for this course
    make_upload_directory( "$course->id" );

    $strimportquestions = get_string("importquestions", "quiz");
    $strquestions = get_string("questions", "quiz");

    $strquizzes = get_string('modulenameplural', 'quiz');
    $streditingquiz = get_string(isset($SESSION->modform->instance) ? "editingquiz" : "editquestions", "quiz");

    print_header_simple("$strimportquestions", "$strimportquestions",
                 "<a href=\"$CFG->wwwroot/mod/quiz/index.php?id=$course->id\">$strquizzes</a>".
                  " -> <a href=\"edit.php\">$streditingquiz</a> -> $strimportquestions");

    if ($form = data_submitted()) {   /// Filename

        $form->format = clean_filename($form->format); // For safety

        if (empty($_FILES['newfile'])) {      // file was just uploaded
            notify(get_string("uploadproblem") );
        }
        
        if ((!is_uploaded_file($_FILES['newfile']['tmp_name']) or $_FILES['newfile']['size'] == 0)) {
            notify(get_string("uploadnofilefound") );

        } else {  // Valid file is found

            if (! is_readable("format/$form->format/format.php")) {
                error("Format not known ($form->format)");
            }

            require("format.php");  // Parent class
            require("format/$form->format/format.php");

            $classname = "quiz_format_$form->format";
            $format = new $classname();

            if (! $format->importpreprocess($category,$course)) {             // Do anything before that we need to
                error("Error occurred during pre-processing!",
                      "$CFG->wwwroot/mod/quiz/import.php?category=$category->id");
            }

            if (! $format->importprocess($_FILES['newfile']['tmp_name'])) {     // Process the uploaded file
                error("Error occurred during processing!",
                      "$CFG->wwwroot/mod/quiz/import.php?category=$category->id");
            }

            if (! $format->importpostprocess()) {                     // In case anything needs to be done after
                error("Error occurred during post-processing!",
                      "$CFG->wwwroot/mod/quiz/import.php?category=$category->id");
            }

            echo "<hr />";
            print_continue("edit.php");
            print_footer($course);
            exit;
        }
    }

    /// Print upload form

    if (!$categories = quiz_get_category_menu($course->id, false)) {
        error("No categories!");
    }

    // get list of available import formats
    $fileformatnames = get_import_export_formats( 'import' );

    print_heading_with_help($strimportquestions, "import", "quiz");

    print_simple_box_start("center");
    echo "<form enctype=\"multipart/form-data\" method=\"post\" action=\"import.php\">";
    echo "<table cellpadding=\"5\">";

    echo "<tr><td align=\"right\">";
    print_string("category", "quiz");
    echo ":</td><td>";
    // choose_from_menu($categories, "category", "$category->id", "");
    echo quiz_get_category_coursename($category);
    echo "</tr>";

    echo "<tr><td align=\"right\">";
    print_string("fileformat", "quiz");
    echo ":</td><td>";
    choose_from_menu($fileformatnames, "format", "gift", "");
    helpbutton("import", $strimportquestions, "quiz");
    echo "</tr>";

    echo "<tr><td align=\"right\">";
    print_string("upload");
    echo ":</td><td>";
    require_once($CFG->dirroot.'/lib/uploadlib.php');
    upload_print_form_fragment(1,array('newfile'),null,false,null,$course->maxbytes,0,false);
    echo "</tr><tr><td>&nbsp;</td><td>";
    echo " <input type=\"hidden\" name=\"category\" value=\"$category->id\" />";
    echo " <input type=\"submit\" name=\"save\" value=\"".get_string("uploadthisfile")."\" />";
    echo "</td></tr>";

    echo "</table>";
    echo "</form>";
    print_simple_box_end();

    print_footer($course);

?>
