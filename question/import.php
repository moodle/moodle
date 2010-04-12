<?php
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
    require_once("editlib.php");
    require_once($CFG->libdir . '/uploadlib.php');
    require_once($CFG->libdir . '/questionlib.php');
    require_once("import_form.php");

    list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) =
            question_edit_setup('import', '/question/import.php', false, false);

   // get display strings
    $txt = new stdClass();
    $txt->importerror = get_string('importerror','quiz');
    $txt->importquestions = get_string("importquestions", "quiz");

    list($catid, $catcontext) = explode(',', $pagevars['cat']);
    if (!$category = $DB->get_record("question_categories", array("id" => $catid))) {
        print_error('nocategory','quiz');
    }

    //this page can be called without courseid or cmid in which case
    //we get the context from the category object.
    if ($contexts === null) { // need to get the course from the chosen category
        $contexts = new question_edit_contexts(get_context_instance_by_id($category->contextid));
        $thiscontext = $contexts->lowest();
        if ($thiscontext->contextlevel == CONTEXT_COURSE){
            require_login($thiscontext->instanceid, false);
        } elseif ($thiscontext->contextlevel == CONTEXT_MODULE){
            list($module, $cm) = get_module_from_cmid($thiscontext->instanceid);
            require_login($cm->course, false, $cm);
        }
        $contexts->require_one_edit_tab_cap($edittab);
    }

    // ensure the files area exists for this course
    make_upload_directory("$COURSE->id");

    $PAGE->set_url($thispageurl->out());

    $import_form = new question_import_form($thispageurl, array('contexts'=>$contexts->having_one_edit_tab_cap('import'),
                                                        'defaultcategory'=>$pagevars['cat']));

    if ($import_form->is_cancelled()){
        redirect($thispageurl);
    }
    //==========
    // PAGE HEADER
    //==========

    if ($cm!==null) {
        $PAGE->navbar->add($txt->importquestions);
        $PAGE->set_title($txt->importquestions);
        echo $OUTPUT->header();

        $currenttab = 'edit';
        $mode = 'import';
        ${$cm->modname} = $module;
        include($CFG->dirroot."/mod/$cm->modname/tabs.php");
    } else {
        // Print basic page layout.
        $PAGE->navbar->add($txt->importquestions);
        $PAGE->set_title($txt->importquestions);
        echo $OUTPUT->header();
        // print tabs
        $currenttab = 'import';
        include('tabs.php');
    }


    // file upload form sumitted
    if ($form = $import_form->get_data()) {

        // file checks out ok
        $fileisgood = false;

        // work out if this is an uploaded file
        // or one from the filesarea.
        if (!empty($form->choosefile)) {
            $importfile = "{$CFG->dataroot}/{$COURSE->id}/{$form->choosefile}";
            $realfilename = $form->choosefile;
            if (file_exists($importfile)) {
                $fileisgood = true;
            } else {
                print_error('uploadproblem', 'moodle', $form->choosefile);
            }
        } else {
            // must be upload file
            $realfilename = $import_form->get_importfile_realname();
            if (!$importfile = $import_form->get_importfile_name()) {
                print_error('uploadproblem', 'moodle');
            }else {
                $fileisgood = true;
            }
        }

        // process if we are happy file is ok
        if ($fileisgood) {

            if (! is_readable("format/$form->format/format.php")) {
                print_error('formatnotfound','quiz', $form->format);
            }

            require_once("format.php");  // Parent class
            require_once("format/$form->format/format.php");

            $classname = "qformat_$form->format";
            $qformat = new $classname();

            // load data into class
            $qformat->setCategory($category);
            $qformat->setContexts($contexts->having_one_edit_tab_cap('import'));
            $qformat->setCourse($COURSE);
            $qformat->setFilename($importfile);
            $qformat->setRealfilename($realfilename);
            $qformat->setMatchgrades($form->matchgrades);
            $qformat->setCatfromfile(!empty($form->catfromfile));
            $qformat->setContextfromfile(!empty($form->contextfromfile));
            $qformat->setStoponerror($form->stoponerror);

            // Do anything before that we need to
            if (! $qformat->importpreprocess()) {
                //TODO: need more detailed error info
                print_error('cannotimport', '', $thispageurl->out());
            }

            // Process the uploaded file
            if (! $qformat->importprocess()) {
                //TODO: need more detailed error info
                print_error('cannotimport', '', $thispageurl->out());
            }

            // In case anything needs to be done after
            if (! $qformat->importpostprocess()) {
                //TODO: need more detailed error info
                print_error('cannotimport', '', $thispageurl->out());
            }

            echo "<hr />";
            $params = $thispageurl->params() + array('category'=>"{$qformat->category->id},{$qformat->category->contextid}");
            echo $OUTPUT->continue_button(new moodle_url('edit.php', $params));
            echo $OUTPUT->footer();
            exit;
        }
    }

    echo $OUTPUT->heading_with_help($txt->importquestions, 'import', 'quiz');

    /// Print upload form
    $import_form->display();
    echo $OUTPUT->footer();


