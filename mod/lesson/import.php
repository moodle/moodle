<?php

/**
 * Imports lesson pages
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

    require_once("../../config.php");
    require_once("lib.php");
    require_once("locallib.php");
    require_once($CFG->libdir.'/questionlib.php');

    $id     = required_param('id', PARAM_INT);         // Course Module ID
    $pageid = optional_param('pageid', '', PARAM_INT); // Page ID

    $url = new moodle_url($CFG->wwwroot.'/mod/lesson/import.php', array('id'=>$id));
    if ($pageid !== '') {
        $url->param('pageid', $pageid);
    }
    $PAGE->set_url($url);

    if (! $cm = get_coursemodule_from_id('lesson', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
        print_error('coursemisconf');
    }

    if (! $lesson = $DB->get_record("lesson", array("id" => $cm->instance))) {
        print_error('invalidcoursemodule');
    }


    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/lesson:edit', $context);

    $strimportquestions = get_string("importquestions", "lesson");
    $strlessons = get_string("modulenameplural", "lesson");

    $PAGE->navbar->add($strimportquestions);
    $PAGE->set_title($strimportquestions);
    $PAGE->set_heading($strimportquestions);
    echo $OUTPUT->header();

    if ($form = data_submitted()) {   /// Filename

        $form->format = clean_param($form->format, PARAM_SAFEDIR); // For safety

        if (empty($_FILES['newfile'])) {      // file was just uploaded
            echo $OUTPUT->notification(get_string("uploadproblem") );
        }

        if ((!is_uploaded_file($_FILES['newfile']['tmp_name']) or $_FILES['newfile']['size'] == 0)) {
            echo $OUTPUT->notification(get_string("uploadnofilefound") );

        } else {  // Valid file is found

            if (! is_readable("$CFG->dirroot/question/format/$form->format/format.php")) {
                print_error('unknowformat','', '', $form->format);
            }

            require("format.php");  // Parent class
            require("$CFG->dirroot/question/format/$form->format/format.php");

            $classname = "qformat_$form->format";
            $format = new $classname();

            if (! $format->importpreprocess()) {             // Do anything before that we need to
                print_error('preprocesserror', 'lesson');
            }

            if (! $format->importprocess($_FILES['newfile']['tmp_name'], $lesson, $pageid)) {    // Process the uploaded file
                print_error('processerror', 'lesson');
            }

            if (! $format->importpostprocess()) {                     // In case anything needs to be done after
                print_error('postprocesserror', 'lesson');
            }

            echo "<hr>";
            echo $OUTPUT->continue_button("view.php?id=$cm->id");
            echo $OUTPUT->footer();
            exit;
        }
    }

    /// Print upload form

    $fileformatnames = get_import_export_formats('import');

    $helpicon = new moodle_help_icon();
    $helpicon->text = $strimportquestions;
    $helpicon->page = "import";
    $helpicon->module = "lesson";

    echo $OUTPUT->heading_with_help($helpicon);

    echo $OUTPUT->box_start('generalbox boxaligncenter');
    echo "<form enctype=\"multipart/form-data\" method=\"post\" action=\"import.php\">";
    echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n";
    echo "<input type=\"hidden\" name=\"pageid\" value=\"$pageid\" />\n";
    echo "<table cellpadding=\"5\">";

    echo "<tr><td align=\"right\">";
    print_string("fileformat", "lesson");
    echo ":</td><td>";
    echo $OUTPUT->select(html_select::make($fileformatnames, "format", "gift", false));
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
    echo $OUTPUT->box_end();

    echo $OUTPUT->footer();


