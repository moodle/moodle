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
    $strexportquestions = get_string('exportquestions', 'quiz');

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
        $navlinks[] = array('name' => get_string('modulenameplural', $cm->modname), 'link' => "$CFG->wwwroot/mod/{$cm->modname}/index.php?id=$COURSE->id", 'type' => 'activity');
        $navlinks[] = array('name' => format_string($module->name), 'link' => "$CFG->wwwroot/mod/{$cm->modname}/view.php?id={$cm->id}", 'type' => 'title');
        $navlinks[] = array('name' => $strexportquestions, 'link' => '', 'type' => 'title');
        $navigation = build_navigation($navlinks);
        print_header_simple($strexportquestions, '', $navigation, "", "", true, $strupdatemodule);

        $currenttab = 'edit';
        $mode = 'export';
        ${$cm->modname} = $module;
        include($CFG->dirroot."/mod/$cm->modname/tabs.php");
    } else {
        // Print basic page layout.
        $navlinks = array();
        $navlinks[] = array('name' => $strexportquestions, 'link' => '', 'type' => 'title');
        $navigation = build_navigation($navlinks);

        print_header_simple($strexportquestions, '', $navigation);
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
        $canaccessbackupdata = has_capability('moodle/site:backup', $contexts->lowest());
        $qformat->set_can_access_backupdata($canaccessbackupdata);
        $qformat->setCattofile(!empty($from_form->cattofile));
        $qformat->setContexttofile(!empty($from_form->contexttofile));

        if (! $qformat->exportpreprocess()) {   // Do anything before that we need to
            print_error('exporterror', 'quiz', $thispageurl->out());
        }

        if (! $qformat->exportprocess()) {         // Process the export data
            print_error('exporterror', 'quiz', $thispageurl->out());
        }

        if (! $qformat->exportpostprocess()) {                    // In case anything needs to be done after
            print_error('exporterror', 'quiz', $thispageurl->out());
        }
        echo "<hr />";

        // link to download the finished file
        $file_ext = $qformat->export_file_extension();
        $filename = $from_form->exportfilename . $file_ext;
        if ($canaccessbackupdata) {
            $efile = get_file_url($qformat->question_get_export_dir() . '/' . $filename,
                    array('forcedownload' => 1));
            echo '<p><div class="boxaligncenter"><a href="' . $efile . '">' .
                    get_string('download', 'quiz') . '</a></div></p>';
            echo '<p><div class="boxaligncenter"><font size="-1">' .
                    get_string('downloadextra', 'quiz') . '</font></div></p>';
        } else {
            $efile = get_file_url($filename, null, 'questionfile');
            echo '<p><div class="boxaligncenter">' .
                    get_string('yourfileshoulddownload', 'question', $efile) . '</a></div></p>';
            echo '
<script type="text/javascript">
//<![CDATA[

  function redirect() {
      document.location.replace("' .  addslashes_js($efile) . '");
  }
  setTimeout("redirect()", 1000);
//]]>
</script>';
        }

        print_continue('edit.php?' . $thispageurl->get_query_string());
        print_footer($COURSE);
        exit;
    }

    /// Display export form
    print_heading_with_help($strexportquestions, 'export', 'quiz');

    $export_form->display();

    print_footer($COURSE);
?>
