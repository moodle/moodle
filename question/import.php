<?php // $Id$
/**
* Import quiz questions into the given category
*
* @version $Id$
* @author Martin Dougiamas, Howard Miller, and many others.
*         {@link http://moodle.org}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/

    require_once("../config.php");
    require_once($CFG->libdir.'/questionlib.php');

    $categoryid = optional_param('category', 0, PARAM_INT);
    $courseid = optional_param('course', 0, PARAM_INT);
    $format = optional_param('format','',PARAM_CLEANFILE);

    if (!$categoryid) { // try to get category from modform
        $showcatmenu = true; // will ensure that user can choose category
        if (isset($SESSION->modform)) {
            $categoryid = $SESSION->modform->category;
        }
    }

    if (! $category = get_record("question_categories", "id", $categoryid)) {
        // if no valid category was given, use the default category
        if ($courseid) {
            $category = get_default_question_category($courseid);
        } else {
            error("No category specified");
        }
    }

    if (!$courseid) { // need to get the course from the chosen category
        $courseid = $category->course;
    }

    if (! $course = get_record("course", "id", $courseid)) {
        error("Invalid course!");
    }

    require_login($course->id, false);

    if (!isteacheredit($course->id)) {
        error("Only editing teachers can import quiz questions!");
    }

    // ensure the files area exists for this course
    make_upload_directory( "$course->id" );

    $strimportquestions = get_string("importquestions", "quiz");
    $strquestions = get_string("questions", "quiz");

    $strquizzes = get_string('modulenameplural', 'quiz');
    $streditingquiz = get_string(isset($SESSION->modform->instance) ? "editingquiz" : "editquestions", "quiz");

    
    /// Header:

    if (isset($SESSION->modform->instance) and $quiz = get_record('quiz', 'id', $SESSION->modform->instance)) {
        $strupdatemodule = isteacheredit($course->id)
            ? update_module_button($SESSION->modform->cmid, $course->id, get_string('modulename', 'quiz'))
            : "";
        print_header_simple($strimportquestions, '',
                 "<a href=\"index.php?id=$course->id\">".get_string('modulenameplural', 'quiz').'</a>'.
                 " -> <a href=\"view.php?q=$quiz->id\">".format_string($quiz->name).'</a>'.
                 ' -> '.$strimportquestions,
                 "", "", true, $strupdatemodule);
        $currenttab = 'edit';
        $mode = 'import';
        include('tabs.php');
    } else {
        print_header_simple($strimportquestions, '',
                 '<a href="edit.php">'.get_string('editquestions', 'quiz').'</a>'.
                 ' -> '.$strimportquestions);
        // print tabs
        $currenttab = 'import';
        include('tabs.php');
    }


    if (!empty($format)) {   /// Filename

        if (!confirm_sesskey()) {
            error( 'sesskey error' );
        }

        if (empty($_FILES['newfile'])) {      // file was just uploaded
            notify(get_string("uploadproblem") );
        }
        
        if ((!is_uploaded_file($_FILES['newfile']['tmp_name']) or $_FILES['newfile']['size'] == 0)) {
            notify(get_string("uploadnofilefound") );

        } else {  // Valid file is found

            if (! is_readable("format/$format/format.php")) {
                error("Format not known ($format)");
            }

            require("format.php");  // Parent class
            require("format/$format/format.php");

            $classname = "qformat_$format";
            $qformat = new $classname();

            if (! $qformat->importpreprocess($category,$course)) {             // Do anything before that we need to
                error("Error occurred during pre-processing!",
                      "$CFG->wwwroot/mod/quiz/import.php?category=$category->id");
            }

            if (! $qformat->importprocess($_FILES['newfile']['tmp_name'])) {     // Process the uploaded file
                error("Error occurred during processing!",
                      "$CFG->wwwroot/mod/quiz/import.php?category=$category->id");
            }

            if (! $qformat->importpostprocess()) {                     // In case anything needs to be done after
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

    // get list of available import formats
    $fileformatnames = get_import_export_formats( 'import' );

    print_heading_with_help($strimportquestions, "import", "quiz");

    /// Get all the existing categories now
    if (isadmin()) { // the admin can import into all categories
        if (!$categories = get_records_select("question_categories", "course = '{$course->id}' OR publish = '1'", "parent, sortorder, name ASC")) {
            error("Could not find any question categories!"); // Something is really wrong
        }
    } else { // select only the categories to which the teacher has write access
        $sql = "SELECT c.*
              FROM {$CFG->prefix}question_categories AS c,
                   {$CFG->prefix}user_teachers AS t
             WHERE t.userid = '$USER->id'
               AND t.course = c.course
               AND (c.course = '$course->id' 
                   OR (c.publish = '1' AND t.editall = '1'))
          ORDER BY c.parent ASC, c.sortorder ASC, c.name ASC";
        if (!$categories = get_records_sql($sql)) {
            error("Could not find any question categories!");
        }
    }
    $categories = add_indented_names($categories);
    foreach ($categories as $key => $cat) {
       if ($catcourse = get_record("course", "id", $cat->course)) {
           if ($cat->publish && $cat->course != $course->id) {
               $cat->indentedname .= " ($catcourse->shortname)";
           }
           $catmenu[$cat->id] = $cat->indentedname;
       }
    }
    
    print_simple_box_start("center");
    echo "<form enctype=\"multipart/form-data\" method=\"post\" action=\"import.php\">\n";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"" . sesskey() . "\" />\n";
    echo "<table cellpadding=\"5\">\n";

    echo "<tr><td align=\"right\">";
    print_string("category", "quiz");
    echo ":</td><td>";
    if (!showcatmenu) { // category already specified
        echo question_category_coursename($category);
        echo " <input type=\"hidden\" name=\"category\" value=\"$category->id\" />";
    } else { // no category specified, let user choose
        choose_from_menu($catmenu, "category", $category->id, "");
    }
    echo "</tr>\n";

    echo "<tr><td align=\"right\">";
    print_string("fileformat", "quiz");
    echo ":</td><td>";
    choose_from_menu($fileformatnames, "format", "gift", "");
    helpbutton("import", $strimportquestions, "quiz");
    echo "</tr>\n";

    echo "<tr><td align=\"right\">";
    print_string("upload");
    echo ":</td><td>";
    require_once($CFG->dirroot.'/lib/uploadlib.php');
    upload_print_form_fragment(1,array('newfile'),null,false,null,$course->maxbytes,0,false);
    echo "</tr><tr><td>&nbsp;</td><td>";
    echo " <input type=\"submit\" name=\"save\" value=\"".get_string("uploadthisfile")."\" />";
    echo "</td></tr>\n";

    echo "</table>\n";
    echo "</form>\n";
    print_simple_box_end();

    print_footer($course);

?>
