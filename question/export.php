<?php
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

    $PAGE->set_pagelayout('standard');

    list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) =
            question_edit_setup('export', '/question/export.php');

    // get display strings
    $strexportquestions = get_string('exportquestions', 'question');

    // make sure we are using the user's most recent category choice
    if (empty($categoryid)) {
        $categoryid = $pagevars['cat'];
    }

    list($catid, $catcontext) = explode(',', $pagevars['cat']);
    if (!$category = $DB->get_record("question_categories", array("id" => $catid, 'contextid' => $catcontext))) {
        print_error('nocategory','quiz');
    }

    /// Header
    $PAGE->set_url($thispageurl->out());
    $PAGE->set_title($strexportquestions);
    $PAGE->set_heading($COURSE->fullname);
    echo $OUTPUT->header();

    $export_form = new question_export_form($thispageurl, array('contexts'=>$contexts->having_one_edit_tab_cap('export'), 'defaultcategory'=>$pagevars['cat']));


    if ($from_form = $export_form->get_data()) {
        $thiscontext = $contexts->lowest();
        if (!is_readable("format/$from_form->format/format.php")) {
            print_error('unknowformat', '', '', $from_form->format);
        }
        $withcategories = 'nocategories';
        if (!empty($from_form->cattofile)) {
            $withcategories = 'withcategories';
        }
        $withcontexts = 'nocontexts';
        if (!empty($from_form->contexttofile)) {
            $withcontexts = 'withcontexts';
        }

        $classname = 'qformat_' . $from_form->format;
        $qformat = new $classname();
        $filename = question_default_export_filename($COURSE, $category) .
                $qformat->export_file_extension();
        $export_url = question_make_export_url($thiscontext->id, $category->id,
                $from_form->format, $withcategories, $withcontexts, $filename);

        echo $OUTPUT->box_start();
        echo get_string('yourfileshoulddownload', 'question', $export_url->out());
        echo $OUTPUT->box_end();

        $PAGE->requires->js_function_call('document.location.replace', array($export_url->out()), false, 1);

        echo $OUTPUT->continue_button(new moodle_url('edit.php', $thispageurl->params()));
        echo $OUTPUT->footer();
        exit;
    }

    /// Display export form
    echo $OUTPUT->heading_with_help($strexportquestions, 'exportquestions', 'question');

    $export_form->display();

    echo $OUTPUT->footer();
