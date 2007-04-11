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

    // get parameters
    $categoryid = optional_param('category',0, PARAM_INT);
    $cattofile = optional_param('cattofile',0, PARAM_BOOL);
    $courseid = required_param('courseid',PARAM_INT);
    $exportfilename = optional_param('exportfilename','',PARAM_FILE );
    $format = optional_param('format','', PARAM_FILE );

    
    // get display strings
    $txt = new object;
    $txt->category = get_string('category','quiz');
    $txt->download = get_string('download','quiz');
    $txt->downloadextra = get_string('downloadextra','quiz');
    $txt->exporterror = get_string('exporterror','quiz');
    $txt->exportname = get_string('exportname','quiz');
    $txt->exportquestions = get_string('exportquestions', 'quiz');
    $txt->fileformat = get_string('fileformat','quiz');
    $txt->exportcategory = get_string('exportcategory','quiz');
    $txt->modulename = get_string('modulename','quiz');
    $txt->modulenameplural = get_string('modulenameplural','quiz');
    $txt->nocategory = get_string('nocategory','quiz');
    $txt->tofile = get_string('tofile','quiz');


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
        error( $txt->nocategory );
    }

    require_login($course->id, false);
    
    // check role capability
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/question:export', $context);

    // ensure the files area exists for this course
    make_upload_directory( "$course->id" );

    // check category is valid
    if (!empty($categoryid)) {
        $validcats = question_category_options( $course->id, true, false );
        if (!array_key_exists( $categoryid, $validcats)) {
            print_error( 'invalidcategory','quiz' );
        }
    }

    /// Header
    if (isset($SESSION->modform->instance) and $quiz = get_record('quiz', 'id', $SESSION->modform->instance)) {
        $strupdatemodule = has_capability('moodle/course:manageactivities', $context)
            ? update_module_button($SESSION->modform->cmid, $course->id, $txt->modulename )
            : "";
        print_header_simple($txt->exportquestions, '',
            "<a href=\"$CFG->wwwroot/mod/quiz/index.php?id=$course->id\">$txt->modulenameplural</a>".
            " -> <a href=\"$CFG->wwwroot/mod/quiz/view.php?q=$quiz->id\">".format_string($quiz->name).'</a>'.
            ' -> '.$txt->exportquestions,
            "", "", true, $strupdatemodule);
        $currenttab = 'edit';
        $mode = 'export';
        include($CFG->dirroot.'/mod/quiz/tabs.php');
    } else {
        print_header_simple($txt->exportquestions, '', $txt->exportquestions);
        // print tabs
        $currenttab = 'export';
        include('tabs.php');
    }

    if (!empty($format)) {   /// Filename

        if (!confirm_sesskey()) {
            print_error( 'sesskey' );
        }

        if (! is_readable("format/$format/format.php")) {
            error( "Format not known ($format)" );  }

        // load parent class for import/export
        require("format.php"); 
        
        // and then the class for the selected format
        require("format/$format/format.php");

        $classname = "qformat_$format";
        $qformat = new $classname();

        $qformat->setCategory( $category );
        $qformat->setCourse( $course );
        $qformat->setFilename( $exportfilename );
        $qformat->setCattofile( $cattofile );

        if (! $qformat->exportpreprocess()) {   // Do anything before that we need to
            error( $txt->exporterror, "$CFG->wwwroot/question/export.php?courseid={$course->id}&amp;category=$category->id");
        }

        if (! $qformat->exportprocess()) {         // Process the export data
            error( $txt->exporterror, "$CFG->wwwroot/question/export.php?courseid={$course->id}&amp;category=$category->id");
        }

        if (! $qformat->exportpostprocess()) {                    // In case anything needs to be done after
            error( $txt->exporterror, "$CFG->wwwroot/question/export.php?courseid={$course->id}&amp;category=$category->id");
        }
        echo "<hr />";

        // link to download the finished file
        $file_ext = $qformat->export_file_extension();
        if ($CFG->slasharguments) {
          $efile = "{$CFG->wwwroot}/file.php/".$qformat->question_get_export_dir()."/$exportfilename".$file_ext."?forcedownload=1";
        }
        else {
          $efile = "{$CFG->wwwroot}/file.php?file=/".$qformat->question_get_export_dir()."/$exportfilename".$file_ext."&forcedownload=1";
        }
        echo "</p><center><a href=\"$efile\">$txt->download</a></center></p>";
        echo "</p><center><font size=\"-1\">$txt->downloadextra</font></center></p>";

        print_continue("edit.php?courseid=$course->id");
        print_footer($course);
        exit;
    }

    /// Display upload form

    // get valid formats to generate dropdown list
    $fileformatnames = get_import_export_formats( 'export' );

    // get filename
    if (empty($exportfilename)) {
        $exportfilename = default_export_filename($course, $category);
    }

    // DISPLAY MAIN PAGE
    print_heading_with_help($txt->exportquestions, 'export', 'quiz');
    print_simple_box_start('center');
    
?>    

    <form enctype="multipart/form-data" method="post" action="export.php">
        <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />
        <input type="hidden" name="courseid" value="<?php echo $course->id; ?>" />
            
            <table cellpadding="5">
                <tr>
                    <td align="right"><?php echo $txt->category; ?>:</td>
                    <td>
                        <?php 
                        if (!$showcatmenu) { // category already specified
                            echo '<strong>'.question_category_coursename($category).'</strong>&nbsp;&nbsp;'; ?>
                            <input type="hidden" name="category" value="<?php echo $category->id ?>" />
                            <?php
                            } else { // no category specified, let user choose
                                question_category_select_menu($course->id, true, false, $category->id);
                            } 
                            echo $txt->tofile; ?>
                            <input name="cattofile" type="checkbox" />
                            <?php helpbutton('exportcategory', $txt->exportcategory, 'quiz'); ?>
                        </td>    
                    </tr>
                    <tr>
                        <td align="right"><?php echo $txt->fileformat; ?>:</td>
                        <td>
                            <?php choose_from_menu($fileformatnames, 'format', 'gift', '');
                            helpbutton('export', $txt->exportquestions, 'quiz'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right"><?php echo $txt->exportname; ?>:</td>
                        <td>
                            <input type="text" size="40" name="exportfilename" value="<?php echo $exportfilename; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2">
                            <input type="submit" name="save" value="<?php echo $txt->exportquestions; ?>" />
                        </td>
                    </tr>
                </table>
        </form>
    <?php
    print_simple_box_end();
    print_footer($course);
?>

