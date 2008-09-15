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
    require_once("editlib.php");
    require_once($CFG->libdir . '/uploadlib.php');
    require_once($CFG->libdir . '/questionlib.php');
    require_once("import_form.php");

    list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) = question_edit_setup('import', false, false);

   // get display strings
    $txt = new stdClass();
    $txt->importquestions = get_string("importquestions", "quiz");

    list($catid, $catcontext) = explode(',', $pagevars['cat']);
    if (!$category = get_record("question_categories", "id", $catid)) {
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

    $import_form = new question_import_form($thispageurl, array('contexts'=>$contexts->having_one_edit_tab_cap('import'),
                                                        'defaultcategory'=>$pagevars['cat']));

    if ($import_form->is_cancelled()){
        redirect($thispageurl);
    }
    //==========
    // PAGE HEADER
    //==========

    if ($cm!==null) {
        $strupdatemodule = has_capability('moodle/course:manageactivities', get_context_instance(CONTEXT_COURSE, $COURSE->id))
            ? update_module_button($cm->id, $COURSE->id, get_string('modulename', $cm->modname))
            : "";
        $navlinks = array();
        $navlinks[] = array('name' => get_string('modulenameplural', $cm->modname), 'link' => "$CFG->wwwroot/mod/{$cm->modname}/index.php?id=$COURSE->id", 'type' => 'activity');
        $navlinks[] = array('name' => format_string($module->name), 'link' => "$CFG->wwwroot/mod/{$cm->modname}/view.php?id={$cm->id}", 'type' => 'title');
        $navlinks[] = array('name' => $txt->importquestions, 'link' => '', 'type' => 'title');
        $navigation = build_navigation($navlinks);
        print_header_simple($txt->importquestions, '', $navigation, "", "", true, $strupdatemodule);

        $currenttab = 'edit';
        $mode = 'import';
        ${$cm->modname} = $module;
        include($CFG->dirroot."/mod/$cm->modname/tabs.php");
    } else {
        // Print basic page layout.
        $navlinks = array();
        $navlinks[] = array('name' => $txt->importquestions, 'link' => '', 'type' => 'title');
        $navigation = build_navigation($navlinks);

        print_header_simple($txt->importquestions, '', $navigation);
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
                print_error('importerror', 'quiz', $thispageurl->out());
            }

            // Process the uploaded file
            if (! $qformat->importprocess()) {
                print_error('importerror', 'quiz', $thispageurl->out());
            }

            // In case anything needs to be done after
            if (! $qformat->importpostprocess()) {
                print_error('importerror', 'quiz', $thispageurl->out());
            }

            echo "<hr />";
            print_continue("edit.php?".($thispageurl->get_query_string(array('category'=>"{$qformat->category->id},{$qformat->category->contextid}"))));
            print_footer($COURSE);
            exit;
        }
    }

    print_heading_with_help($txt->importquestions, "import", "quiz");

    /// Print upload form
    $import_form->display();
    print_footer($COURSE);

?>
