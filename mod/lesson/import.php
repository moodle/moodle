<?PHP // $Id$
      // Import quiz questions into the given category

    require_once("../../config.php");
    require_once("lib.php");

    optional_variable($format);
    require_variable($id);    // Course Module ID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $lesson = get_record("lesson", "id", $cm->instance)) {
        error("Course module is incorrect");
    }


    require_login($course->id);

    if (!isteacher($course->id)) {
        error("Only the teacher can import questions!");
    }

    $strimportquestions = get_string("importquestions", "lesson");
    $strlessons = get_string("modulenameplural", "lesson");

    print_header("$course->shortname: $strimportquestions", "$course->shortname: $strimportquestions",
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> ". 
                 "<A HREF=index.php?id=$course->id>$strlessons</A> -> <a href=\"view.php?id=$cm->id\">$lesson->name</a>-> $strimportquestions");

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

            if (! is_readable("../quiz/format/$form->format/format.php")) {
                error("Format not known ($form->format)");
            }

            require("format.php");  // Parent class
            require("../quiz/lib.php"); // for the constants used in quiz/format/<format>/format.php
            require("../quiz/format/$form->format/format.php");

            $format = new quiz_file_format();

            if (! $format->importpreprocess()) {             // Do anything before that we need to
                error("Error occurred during pre-processing!");
            }

            if (! $format->importprocess($newfile['tmp_name'], $lesson, $_POST['pageid'])) {    // Process the uploaded file
                error("Error occurred during processing!");
            }

            if (! $format->importpostprocess()) {                     // In case anything needs to be done after
                error("Error occurred during post-processing!");
            }

            echo "<hr>";
            print_continue("view.php?id=$cm->id");
            print_footer($course);
            exit;
        }
    } 

    /// Print upload form

    $fileformats = get_list_of_plugins("mod/quiz/format");
    $fileformatname = array();
    foreach ($fileformats as $key => $fileformat) {
        $formatname = get_string($fileformat, 'lesson');
        if ($formatname == "[[$fileformat]]") {
            $formatname = $fileformat;  // Just use the raw folder name
        }
        $fileformatnames[$fileformat] = $formatname;
    }
    natcasesort($fileformatnames);


    print_heading_with_help($strimportquestions, "import", "lesson");

    print_simple_box_start("center", "", "$THEME->cellheading");
    echo "<form enctype=\"multipart/form-data\" method=\"post\" action=import.php>";
    echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";
    echo "<input type=\"hidden\" name=\"pageid\" value=\"".$_GET['pageid']."\">\n";
    echo "<table cellpadding=5>";

    echo "<tr><td align=right>";
    print_string("fileformat", "lesson");
    echo ":</td><td>";
    choose_from_menu($fileformatnames, "format", "gift", "");
    echo "</tr>";

    echo "<tr><td align=right>";
    print_string("upload");
    echo ":</td><td>";
    echo " <input name=\"newfile\" type=\"file\" size=\"50\">";
    echo "</tr><tr><td>&nbsp;</td><td>";
    echo " <input type=submit name=save value=\"".get_string("uploadthisfile")."\">";
    echo "</td></tr>";

    echo "</table>";
    echo "</form>";
    print_simple_box_end();

    print_footer($course);

?>
