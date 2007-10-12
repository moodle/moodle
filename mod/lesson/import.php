<?php // $Id$
/**
 * Imports lesson pages
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

    require_once("../../config.php");
    require_once("lib.php");
    require_once("locallib.php");
    require_once($CFG->libdir.'/questionlib.php');

    $id     = required_param('id', PARAM_INT);         // Course Module ID
    $pageid = optional_param('pageid', '', PARAM_INT); // Page ID

    if (! $cm = get_coursemodule_from_id('lesson', $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $lesson = get_record("lesson", "id", $cm->instance)) {
        error("Course module is incorrect");
    }


    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/lesson:edit', $context);

    $strimportquestions = get_string("importquestions", "lesson");
    $strlessons = get_string("modulenameplural", "lesson");

    $navigation = build_navigation($strimportquestions, $cm);
    print_header_simple("$strimportquestions", " $strimportquestions", $navigation);

    if ($form = data_submitted()) {   /// Filename

        $form->format = clean_param($form->format, PARAM_SAFEDIR); // For safety

        if (empty($_FILES['newfile'])) {      // file was just uploaded
            notify(get_string("uploadproblem") );
        }

        if ((!is_uploaded_file($_FILES['newfile']['tmp_name']) or $_FILES['newfile']['size'] == 0)) {
            notify(get_string("uploadnofilefound") );

        } else {  // Valid file is found

            if (! is_readable("$CFG->dirroot/question/format/$form->format/format.php")) {
                error("Format not known ($form->format)");
            }

            require("format.php");  // Parent class
            require("$CFG->dirroot/question/format/$form->format/format.php");

            $classname = "qformat_$form->format";
            $format = new $classname();

            if (! $format->importpreprocess()) {             // Do anything before that we need to
                error("Error occurred during pre-processing!");
            }

            if (! $format->importprocess($_FILES['newfile']['tmp_name'], $lesson, $pageid)) {    // Process the uploaded file
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

    $fileformatnames = get_import_export_formats('import');

    print_heading_with_help($strimportquestions, "import", "lesson");

    print_simple_box_start("center");
    echo "<form enctype=\"multipart/form-data\" method=\"post\" action=\"import.php\">";
    echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n";
    echo "<input type=\"hidden\" name=\"pageid\" value=\"$pageid\" />\n";
    echo "<table cellpadding=\"5\">";

    echo "<tr><td align=\"right\">";
    print_string("fileformat", "lesson");
    echo ":</td><td>";
    choose_from_menu($fileformatnames, "format", "gift", "");
    echo "</td></tr>";

    echo "<tr><td align=\"right\">";
    print_string("upload");
    echo ":</td><td>";
    echo "<input name=\"newfile\" type=\"file\" size=\"50\" />";
    echo "</td></tr><tr><td>&nbsp;</td><td>";
    echo "<input type=\"submit\" name=\"save\" value=\"".get_string("uploadthisfile")."\" />";
    echo "</td></tr>";

    echo "</table>";
    echo "</form>";
    print_simple_box_end();

    print_footer($course);

?>
