<?php // $Id$
/**
 * Export quiz questions into the given category
 *
 * @author Martin Dougiamas, Howard Miller, and many others.
 *         {@link http://moodle.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage importexport
 */

    require_once("../config.php");
    require_once("editlib.php");
    require_once("export_form.php");

    list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) = question_edit_setup('export');


    // get display strings
    $txt = new object;
    $txt->category = get_string('category', 'quiz');
    $txt->download = get_string('download', 'quiz');
    $txt->downloadextra = get_string('downloadextra', 'quiz');
    $txt->exporterror = get_string('exporterror', 'quiz');
    $txt->exportname = get_string('exportname', 'quiz');
    $txt->exportquestions = get_string('exportquestions', 'quiz');
    $txt->fileformat = get_string('fileformat', 'quiz');
    $txt->exportcategory = get_string('exportcategory', 'quiz');
    $txt->modulename = get_string('modulename', 'quiz');
    $txt->modulenameplural = get_string('modulenameplural', 'quiz');
    $txt->tofile = get_string('tofile', 'quiz');



    // make sure we are using the user's most recent category choice
    if (empty($categoryid)) {
        $categoryid = $pagevars['cat'];
    }

    // ensure the files area exists for this course
    make_upload_directory("$COURSE->id");
    list($catid, $catcontext) = explode(',', $pagevars['cat']);
    if (!$category = get_record("question_categories", "id", $catid, 'contextid', $catcontext)) {
        print_error('nocategory','quiz');
    }

    /// Header
    if ($cm!==null) {
        $strupdatemodule = has_capability('moodle/course:manageactivities', $contexts->lowest())
            ? update_module_button($cm->id, $COURSE->id, get_string('modulename', $cm->modname))
            : "";
        $navlinks = array();
        $navlinks[] = array('name' => get_string('modulenameplural', $cm->modname), 'link' => "$CFG->wwwroot/mod/{$cm->modname}/index.php?id=$course->id", 'type' => 'activity');
        $navlinks[] = array('name' => format_string($module->name), 'link' => "$CFG->wwwroot/mod/{$cm->modname}/view.php?cmid={$cm->id}", 'type' => 'title');
        $navlinks[] = array('name' => $txt->exportquestions, 'link' => '', 'type' => 'title');
        $navigation = build_navigation($navlinks);
        print_header_simple($txt->exportquestions, '', $navigation, "", "", true, $strupdatemodule);

        $currenttab = 'edit';
        $mode = 'export';
        ${$cm->modname} = $module;
        include($CFG->dirroot."/mod/$cm->modname/tabs.php");
    } else {
        // Print basic page layout.
        $navlinks = array();
        $navlinks[] = array('name' => $txt->exportquestions, 'link' => '', 'type' => 'title');
        $navigation = build_navigation($navlinks);

        print_header_simple($txt->exportquestions, '', $navigation);
        // print tabs
        $currenttab = 'export';
        include('tabs.php');
    }

    $exportfilename = default_export_filename($COURSE, $category);
    $export_form = new question_export_form($thispageurl, array('contexts'=>$contexts->having_one_edit_tab_cap('export'), 'defaultcategory'=>$pagevars['cat'],
                                    'defaultfilename'=>$exportfilename));


    if ($from_form = $export_form->get_data()) {   /// Filename


        if (! is_readable("format/$from_form->format/format.php")) {
            error("Format not known ($from_form->format)");
        }

        // load parent class for import/export
        require_once("format.php");

        // and then the class for the selected format
        require_once("format/$from_form->format/format.php");

        $classname = "qformat_$from_form->format";
        $qformat = new $classname();
        $qformat->setContexts($contexts->having_one_edit_tab_cap('export'));
        $qformat->setCategory($category);
        $qformat->setCourse($COURSE);

        if (empty($from_form->exportfilename)) {
            $from_form->exportfilename = default_export_filename($COURSE, $category);
        }
        $qformat->setFilename($from_form->exportfilename);
        $qformat->setCattofile(!empty($from_form->cattofile));
        $qformat->setContexttofile(!empty($from_form->contexttofile));

        if (! $qformat->exportpreprocess()) {   // Do anything before that we need to
            error($txt->exporterror, $thispageurl->out());
        }

        if (! $qformat->exportprocess()) {         // Process the export data
            error($txt->exporterror, $thispageurl->out());
        }

        if (! $qformat->exportpostprocess()) {                    // In case anything needs to be done after
            error($txt->exporterror, $thispageurl->out());
        }
        echo "<hr />";

        // link to download the finished file
        $file_ext = $qformat->export_file_extension();
        if ($CFG->slasharguments) {
          $efile = "{$CFG->wwwroot}/file.php/".$qformat->question_get_export_dir()."/$from_form->exportfilename".$file_ext."?forcedownload=1";
        }
        else {
          $efile = "{$CFG->wwwroot}/file.php?file=/".$qformat->question_get_export_dir()."/$from_form->exportfilename".$file_ext."&forcedownload=1";
        }
        echo "<p><div class=\"boxaligncenter\"><a href=\"$efile\">$txt->download</a></div></p>";
        echo "<p><div class=\"boxaligncenter\"><font size=\"-1\">$txt->downloadextra</font></div></p>";

        print_continue("edit.php?".$thispageurl->get_query_string());
        print_footer($COURSE);
        exit;
    }

    /// Display export form


    print_heading_with_help($txt->exportquestions, 'export', 'quiz');

    $export_form->display();

    print_footer($COURSE);
?>

