<?PHP // $Id$
      // Import quiz questions into the given category

    require_once("../../config.php");
	require_once("locallib.php");

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
            require_once($CFG->dirroot.'/lib/uploadlib.php');
            $um = new upload_manager('newfile',false,false,$course,false,0,false);
            if ($um->preprocess_files()) { // validate and virus check! 
                $newfile = $_FILES['newfile'];
            }
        }

        if (is_array($newfile)) { // either for file already on server or just uploaded file.

            if (! is_readable("../quiz/format/$form->format/format.php")) {
                error("Format not known ($form->format)");
            }

            require("format.php");  // Parent class
            require("../quiz/lib.php"); // for the constants used in quiz/format/<format>/format.php
            require("../quiz/format/$form->format/format.php");

            $format = new quiz_file_format();
			
			
			// jjg7:8/9/2004 remove double '\n' from a file if the format is aiken and reformat Brusca's to Aiken
			if ($form->format == 'aiken')
			{
				require("reformat.php"); // include functions to reformat styles
				if (removedoublecr($newfile['tmp_name']) === FALSE) {
					error("Error occurred while replacing double carriage returns");
				}
				if (importmodifiedaikenstyle($newfile['tmp_name']) === FALSE) {
					error("Error occurred while converting to Aiken");
				}
			}
			
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
    require_once($CFG->dirroot.'/lib/uploadlib.php');
    upload_print_form_fragment(1,array('newfile'),null,false,null,$course->maxbytes,0,false);
    echo "</tr><tr><td>&nbsp;</td><td>";
    echo " <input type=submit name=save value=\"".get_string("uploadthisfile")."\">";
    echo "</td></tr>";

    echo "</table>";
    echo "</form>";
    print_simple_box_end();

    print_footer($course);

?>
