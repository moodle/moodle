<?php // $Id$
/**
 * Import quiz questions into the given category
 *
 * @author Martin Dougiamas, Howard Miller, and many others.
 *         {@link http://moodle.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage importexport
 */

    require_once("../config.php");
    require_once("editlib.php" );
    require_once($CFG->libdir . '/uploadlib.php');
    require_once($CFG->libdir . '/questionlib.php');

    // get parameters
    $params = new stdClass;
    $params->choosefile = optional_param('choosefile','',PARAM_PATH);
    $categoryid = optional_param('category', 0, PARAM_INT);
    $catfromfile = optional_param('catfromfile', 0, PARAM_BOOL );
    $courseid = optional_param('course', 0, PARAM_INT);
    $format = optional_param('format','',PARAM_FILE);
    $params->matchgrades = optional_param('matchgrades','',PARAM_ALPHA);
    $params->stoponerror = optional_param('stoponerror', 0, PARAM_BOOL);

    // get display strings
    $txt = new stdClass();
    $txt->category = get_string('category','quiz');
    $txt->choosefile = get_string('choosefile','quiz');
    $txt->editingquiz = get_string(isset($SESSION->modform->instance) ? "editingquiz" : "editquestions", "quiz");
    $txt->file = get_string('file');
    $txt->fileformat = get_string('fileformat','quiz');
    $txt->fromfile = get_string('fromfile','quiz');
    $txt->importcategory = get_string('importcategory','quiz');
    $txt->importerror = get_string('importerror','quiz');
    $txt->importfilearea = get_string('importfilearea','quiz');
    $txt->importfileupload = get_string('importfileupload','quiz');
    $txt->importfromthisfile = get_string('importfromthisfile','quiz');
    $txt->importquestions = get_string("importquestions", "quiz");
    $txt->matchgrades = get_string('matchgrades','quiz');
    $txt->matchgradeserror = get_string('matchgradeserror','quiz');
    $txt->matchgradesnearest = get_string('matchgradesnearest','quiz');
    $txt->modulename = get_string('modulename','quiz');
    $txt->modulenameplural = get_string('modulenameplural','quiz');
    $txt->onlyteachersimport = get_string('onlyteachersimport','quiz');
    $txt->questions = get_string("questions", "quiz");
    $txt->quizzes = get_string('modulenameplural', 'quiz');
    $txt->stoponerror = get_string('stoponerror', 'quiz');
    $txt->upload = get_string('upload');
    $txt->uploadproblem = get_string('uploadproblem');
    $txt->uploadthisfile = get_string('uploadthisfile');

    // matching options
    $matchgrades = array();
    $matchgrades['error'] = $txt->matchgradeserror;
    $matchgrades['nearest'] = $txt->matchgradesnearest;

    if ($categoryid) { // update category in session variable
        $SESSION->questioncat = $categoryid;
    } else { // try to get category from modform
        if (isset($SESSION->questioncat)) {
            $categoryid = $SESSION->questioncat;
        }
    }

    if (!$category = get_record("question_categories", "id", $categoryid)) {
        // if no valid category was given, use the default category
        if ($courseid) {
            $category = get_default_question_category($courseid);
        } else {
            print_error('nocategory','quiz');
        }
    }

    if (!$courseid) { // need to get the course from the chosen category
        $courseid = $category->course;
    }

    if (!$course = get_record("course", "id", $courseid)) {
        error("Invalid course!");
    }

    require_login($course->id, false);

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/question:import', $context);

    // ensure the files area exists for this course
    make_upload_directory( "$course->id" );


    //==========
    // PAGE HEADER
    //==========

    if (isset($SESSION->modform->instance) and $quiz = get_record('quiz', 'id', $SESSION->modform->instance)) {
        $strupdatemodule = has_capability('moodle/course:manageactivities', $context)
            ? update_module_button($SESSION->modform->cmid, $course->id, $txt->modulename)
            : "";
        print_header_simple($txt->importquestions, '',
                 "<a href=\"$CFG->wwwroot/mod/quiz/index.php?id=$course->id\">".$txt->modulenameplural.'</a>'.
                 " -> <a href=\"$CFG->wwwroot/mod/quiz/view.php?q=$quiz->id\">".format_string($quiz->name).'</a>'.
                 ' -> '.$txt->importquestions,
                 "", "", true, $strupdatemodule);
        $currenttab = 'edit';
        $mode = 'import';
        include($CFG->dirroot.'/mod/quiz/tabs.php');
    } else {
        print_header_simple($txt->importquestions, '', $txt->importquestions);
        // print tabs
        $currenttab = 'import';
        include('tabs.php');
    }


    // file upload form sumitted
    if (!empty($format) and confirm_sesskey() ) { 

        // file checks out ok
        $fileisgood = false;

        // work out if this is an uploaded file 
        // or one from the filesarea.
        if (!empty($params->choosefile)) {
            $importfile = "{$CFG->dataroot}/{$course->id}/{$params->choosefile}";
            if (file_exists($importfile)) {
                $fileisgood = true;
            }
            else {
                notify($txt->uploadproblem);
            }
        }
        else {
            // must be upload file
            if (empty($_FILES['newfile'])) {
                notify( $txt->uploadproblem );
            }
            else if ((!is_uploaded_file($_FILES['newfile']['tmp_name']) or $_FILES['newfile']['size'] == 0)) {
                notify( $txt->uploadproblem );
            }
            else {
                $importfile = $_FILES['newfile']['tmp_name'];
                $fileisgood = true;
            }
        }

        // process if we are happy file is ok
        if ($fileisgood) { 

            if (! is_readable("format/$format/format.php")) {
                error( get_string('formatnotfound','quiz', $format) );
            }

            require("format.php");  // Parent class
            require("format/$format/format.php");

            $classname = "qformat_$format";
            $qformat = new $classname();

            // load data into class
            $qformat->setCategory( $category );
            $qformat->setCourse( $course );
            $qformat->setFilename( $importfile );
            $qformat->setMatchgrades( $params->matchgrades );
            $qformat->setCatfromfile( $catfromfile );
            $qformat->setStoponerror( $params->stoponerror );

            // Do anything before that we need to
            if (! $qformat->importpreprocess()) {             
                error( $txt->importerror ,
                      "$CFG->wwwroot/question/import.php?courseid={$course->id}&amp;category=$category->id");
            }

            // Process the uploaded file
            if (! $qformat->importprocess() ) {     
                error( $txt->importerror ,
                      "$CFG->wwwroot/question/import.php?courseid={$course->id}&amp;category=$category->id");
            }

            // In case anything needs to be done after
            if (! $qformat->importpostprocess()) {
                error( $txt->importerror ,
                      "$CFG->wwwroot/question/import.php?courseid={$course->id}&amp;category=$category->id");
            }

            echo "<hr />";
            print_continue("edit.php?courseid=$course->id");
            print_footer($course);
            exit;
        }
    }

    /// Print upload form

    // get list of available import formats
    $fileformatnames = get_import_export_formats( 'import' );

    print_heading_with_help($txt->importquestions, "import", "quiz");

    /// Get all the existing categories now
    $catmenu = question_category_options($course->id, false, true);
   
    //==========
    // DISPLAY
    //==========
 
    ?>

    <form id="form" enctype="multipart/form-data" method="post" action="import.php">
        <fieldset class="invisiblefieldset" style="display: block;">
            <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />
            <?php print_simple_box_start("center"); ?>
            <table cellpadding="5">
                <tr>
                    <td align="right"><?php echo $txt->category; ?>:</td>
                    <td><?php choose_from_menu($catmenu, "category", $category->id, ""); ?>
                        <?php echo $txt->fromfile; ?>
                        <input name="catfromfile" type="checkbox" />
                        <?php helpbutton('importcategory', $txt->importcategory, 'quiz'); ?></td>
                </tr>

                <tr>
                    <td align="right"><?php echo $txt->fileformat; ?>:</td>
                    <td><?php choose_from_menu($fileformatnames, 'format', 'gift', '');
                        helpbutton("import", $txt->importquestions, 'quiz'); ?></td>
                </tr>
                <tr>
                    <td align="right"><?php echo $txt->matchgrades; ?></td>
                    <td><?php choose_from_menu($matchgrades,'matchgrades',$txt->matchgradeserror,'' );
                        helpbutton('matchgrades', $txt->matchgrades, 'quiz'); ?></td>
                </tr>
                <tr>
                    <td align="right"><?php echo $txt->stoponerror; ?></td>
                    <td><input name="stoponerror" type="checkbox" checked="checked" />
                    <?php helpbutton('stoponerror', $txt->stoponerror, 'quiz'); ?></td>
                </tr>
            </table>
            <?php
            print_simple_box_end();

            print_simple_box_start('center'); ?>
            <?php echo $txt->importfileupload; ?>
            <table cellpadding="5">
                <tr>
                    <td align="right"><?php echo $txt->upload; ?>:</td>
                    <td><?php upload_print_form_fragment(1,array('newfile'),null,false,null,$course->maxbytes,0,false); ?></td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" name="save" value="<?php echo $txt->uploadthisfile; ?>" /></td>
                </tr>
            </table>
            <?php
            print_simple_box_end();

            print_simple_box_start('center'); ?>
            <?php echo $txt->importfilearea; ?>
            <table cellpadding="5">
                <tr>
                    <td align="right"><?php echo $txt->file; ?>:</td>
                    <td><input type="text" name="choosefile" size="50" /></td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td><?php  button_to_popup_window ("/files/index.php?id={$course->id}&amp;choose=form.choosefile", 
                        "coursefiles", $txt->choosefile, 500, 750, $txt->choosefile); ?>
                        <input type="submit" name="save" value="<?php echo $txt->importfromthisfile; ?>" /></td>
                </tr>
            </table>
            <?php 
            print_simple_box_end(); ?>
        </fieldset>
    </form>

    <?php
    print_footer($course);

?>
