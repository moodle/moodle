<?php // $Id$
/**
* Export quiz questions into the given category
*
* @version $Id$
* @author Martin Dougiamas, Howard Miller, and many others.
*         {@link http://moodle.org}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/

    require_once("../config.php");
    require_once( "editlib.php" );

    $categoryid = optional_param('category',0, PARAM_INT);
    $courseid = required_param('courseid',PARAM_INT);
    $format = optional_param('format','', PARAM_FILE );
    $exportfilename = optional_param('exportfilename','',PARAM_FILE );

    if (! $course = get_record("course", "id", $courseid)) {
        error("Course does not exist!");
    }

    $showcatmenu = false;
    if ($categoryid) { // update category in session variable
        $SESSION->questioncat = $categoryid;
    } else { // try to get category from modform
        $showcatmenu = true; // will ensure that user can choose category
        if (isset($SESSION->questioncat)) {
            $categoryid = $SESSION->questioncat;
        }
    }

    if (! $category = get_record("question_categories", "id", $categoryid)) {
        $category = get_default_question_category($courseid);
    }

    if (! $categorycourse = get_record("course", "id", $category->course)) {
        error( get_string('nocategory','quiz') );
    }

    require_login($course->id, false);
    
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/question:export', $context);

    // ensure the files area exists for this course
    make_upload_directory( "$course->id" );

    $strexportquestions = get_string("exportquestions", "quiz");
    $strquestions = get_string("questions", "quiz");

    $strquizzes = get_string('modulenameplural', 'quiz');
    $streditingquiz = get_string(isset($SESSION->modform->instance) ? "editingquiz" : "editquestions", "quiz");

    $dirname = get_string("exportfilename","quiz");
    
    /// Header:

    if (isset($SESSION->modform->instance) and $quiz = get_record('quiz', 'id', $SESSION->modform->instance)) {
        $strupdatemodule = has_capability('moodle/course:manageactivities', $context)
            ? update_module_button($SESSION->modform->cmid, $course->id, get_string('modulename', 'quiz'))
            : "";
        print_header_simple($strexportquestions, '',
                 "<a href=\"$CFG->wwwroot/mod/quiz/index.php?id=$course->id\">".get_string('modulenameplural', 'quiz').'</a>'.
                 " -> <a href=\"$CFG->wwwroot/mod/quiz/view.php?q=$quiz->id\">".format_string($quiz->name).'</a>'.
                 ' -> '.$strexportquestions,
                 "", "", true, $strupdatemodule);
        $currenttab = 'edit';
        $mode = 'export';
        include($CFG->dirroot.'/mod/quiz/tabs.php');
    } else {
        print_header_simple($strexportquestions, '', $strexportquestions);
        // print tabs
        $currenttab = 'export';
        include('tabs.php');
    }

    if (!empty($format)) {   /// Filename

        if (!confirm_sesskey()) {
            echo( 'Sesskey error' );
        }

        if (! is_readable("format/$format/format.php")) {
            error('Format not known ('.clean_text($form->format).')');
        }

        require("format.php");  // Parent class
        require("format/$format/format.php");

        $classname = "qformat_$format";
        $qformat = new $classname();

        $qformat->setCategory( $category );
        $qformat->setCourse( $course );
        $qformat->setFilename( $exportfilename );

        if (! $qformat->exportpreprocess()) {   // Do anything before that we need to
            error( get_string('exporterror','quiz'),
                    "$CFG->wwwroot/question/export.php?courseid={$course->id}&amp;category=$category->id");
        }

        if (! $qformat->exportprocess()) {         // Process the export data
            error( get_string('exporterror','quiz'),
                    "$CFG->wwwroot/question/export.php?courseid={$course->id}&amp;category=$category->id");
        }

        if (! $qformat->exportpostprocess()) {                    // In case anything needs to be done after
            error( get_string('exporterror','quiz'),
                    "$CFG->wwwroot/question/export.php?courseid={$course->id}&amp;category=$category->id");
        }
        echo "<hr />";

        // link to download the finished file
        $file_ext = $qformat->export_file_extension();
        $download_str = get_string( 'download', 'quiz' );
        $downloadextra_str = get_string( 'downloadextra','quiz' );
        if ($CFG->slasharguments) {
          $efile = "{$CFG->wwwroot}/file.php/".$qformat->question_get_export_dir()."/$exportfilename".$file_ext."?forcedownload=1";
        }
        else {
          $efile = "{$CFG->wwwroot}/file.php?file=/".$qformat->question_get_export_dir()."/$exportfilename".$file_ext."&forcedownload=1";
        }
        echo "</p><center><a href=\"$efile\">$download_str</a></center></p>";
        echo "</p><center><font size=\"-1\">$downloadextra_str</font></center></p>";

        print_continue("edit.php?courseid=$course->id");
        print_footer($course);
        exit;
    }

    /// Print upload form

    // get valid formats to generate dropdown list
    $fileformatnames = get_import_export_formats( "export" );

    // get filename
    if (empty($exportfilename)) {
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
    if (!$showcatmenu) { // category already specified
        echo question_category_coursename($category);
        echo " <input type=\"hidden\" name=\"category\" value=\"$category->id\" />";
    } else { // no category specified, let user choose
        question_category_select_menu($course->id, true, false, $category->id);
    }
    //echo str_replace('&nbsp;', '', $category->name) . " ($categorycourse->shortname)";
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
    echo " <input type=\"hidden\" name=\"courseid\" value=\"$course->id\" />";
    echo " <input type=\"submit\" name=\"save\" value=\"".get_string("exportquestions","quiz")."\" />";
    echo "</td></tr>\n";

    echo "</table>\n";
    echo "</form>\n";
    print_simple_box_end();

    print_footer($course);

?>
