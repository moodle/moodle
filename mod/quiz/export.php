<?php // $Id$
      // Import quiz questions into the given category

    require_once("../../config.php");
    require_once("locallib.php");

    $category = required_param('category',PARAM_INT);
    $format = optional_param('format','', PARAM_CLEANFILE );
    $exportfilename = optional_param('exportfilename','',PARAM_CLEANFILE );

    if (! $category = get_record("quiz_categories", "id", $category)) {
        error("This wasn't a valid category!");
    }

    if (! $course = get_record("course", "id", $category->course)) {
        error("This category doesn't belong to a valid course!");
    }

    require_login($course->id, false);

    if (!isteacher($course->id)) {
        error("Only the teacher can export quiz questions!");
    }

    // ensure the files area exists for this course
    make_upload_directory( "$course->id" );

    $strexportquestions = get_string("exportquestions", "quiz");
    $strquestions = get_string("questions", "quiz");

    $strquizzes = get_string('modulenameplural', 'quiz');
    $streditingquiz = get_string(isset($SESSION->modform->instance) ? "editingquiz" : "editquestions", "quiz");

    $dirname = get_string("exportfilename","quiz");
    print_header_simple("$strexportquestions", "$strexportquestions",
                 "<a href=\"$CFG->wwwroot/mod/$dirname/index.php?id=$course->id\">$strquizzes</a>".
                  " -> <a href=\"edit.php\">$streditingquiz</a> -> $strexportquestions");

    if (!empty($format)) {   /// Filename

        if (!confirm_sesskey()) {
            echo( 'Sesskey error' );
        }

        if (! is_readable("format/$format/format.php")) {
            error('Format not known ('.clean_text($form->format).')');
        }

        require("format.php");  // Parent class
        require("format/$format/format.php");

        $classname = "quiz_format_$format";
        $quiz_format = new $classname();

        if (! $quiz_format->exportpreprocess($category, $course)) {   // Do anything before that we need to
            error("Error occurred during pre-processing!",
                    "$CFG->wwwroot/mod/quiz/export.php?category=$category->id");
        }

        if (! $quiz_format->exportprocess($exportfilename)) {         // Process the export data
            error("Error occurred during processing!",
                    "$CFG->wwwroot/mod/quiz/export.php?category=$category->id");
        }

        if (! $quiz_format->exportpostprocess()) {                    // In case anything needs to be done after
            error("Error occurred during post-processing!",
                    "$CFG->wwwroot/mod/quiz/export.php?category=$category->id");
        }
        echo "<hr />";

        // link to download the finished file
        $file_ext = $quiz_format->export_file_extension();
        $download_str = get_string( 'download', 'quiz' );
        $downloadextra_str = get_string( 'downloadextra','quiz' );
        if ($CFG->slasharguments) {
          $efile = "{$CFG->wwwroot}/file.php/$course->id/quiz/$exportfilename".$file_ext;
        }
        else {
          $efile = "{$CFG->wwwroot}/file.php?file=/$course->id/quiz/$exportfilename".$file_ext;
        }
        echo "</p><center><a href=\"$efile\">$download_str</a></center></p>";
        echo "</p><center><font size=\"-1\">$downloadextra_str</font></center></p>";

        print_continue("edit.php");
        print_footer($course);
        exit;
    }

    /// Print upload form

    if (!$categories = quiz_get_category_menu($course->id, false)) {
        error("No categories!");
    }

    // get valid formats to generate dropdown list
    $fileformatnames = get_import_export_formats( "export" );

    // get filename
    if (!isset($exportfilename)) {
        $exportfilename = default_export_filename($course, $category);
    }

    print_heading_with_help($strexportquestions, "export", "quiz");

    print_simple_box_start("center");
    echo "<form enctype=\"multipart/form-data\" method=\"post\" action=\"export.php\">\n";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"" . sesskey() . "\" />\n";
    echo "<table cellpadding=\"5\">\n";

    echo "<tr><td align=\"right\">\n";
    print_string("category", "quiz");
    echo ":</td><td>";
    echo str_replace('&nbsp;', '', $categories[$category->id]) . " ($course->shortname)";
    echo "</td></tr>\n";

    echo "<tr><td align=\"right\">";
    print_string("fileformat", "quiz");
    echo ":</td><td>";
    choose_from_menu($fileformatnames, "format", "gift", "");
    helpbutton("export", $strexportquestions, "quiz");
    echo "</td></tr>\n";

    echo "<tr><td align=\"right\">";
    print_string("exportname", "quiz" );
    echo ":</td><td>";
    echo "<input type=\"text\" size=\"40\" name=\"exportfilename\" value=\"$exportfilename\" />";
    echo "</td></tr>\n";

    echo "<tr><td align=\"center\" colspan=\"2\">";
    echo " <input type=\"hidden\" name=\"category\" value=\"$category->id\" />";
    echo " <input type=\"submit\" name=\"save\" value=\"".get_string("exportquestions","quiz")."\" />";
    echo "</td></tr>\n";

    echo "</table>\n";
    echo "</form>\n";
    print_simple_box_end();

    print_footer($course);

?>
