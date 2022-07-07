<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines the import questions form.
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->dirroot . '/question/import_form.php');
require_once($CFG->dirroot . '/question/format.php');

list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) =
        question_edit_setup('import', '/question/import.php');

// get display strings
$txt = new stdClass();
$txt->importerror = get_string('importerror', 'question');
$txt->importquestions = get_string('importquestions', 'question');

list($catid, $catcontext) = explode(',', $pagevars['cat']);
if (!$category = $DB->get_record("question_categories", array('id' => $catid))) {
    print_error('nocategory', 'question');
}

$categorycontext = context::instance_by_id($category->contextid);
$category->context = $categorycontext;
//this page can be called without courseid or cmid in which case
//we get the context from the category object.
if ($contexts === null) { // need to get the course from the chosen category
    $contexts = new question_edit_contexts($categorycontext);
    $thiscontext = $contexts->lowest();
    if ($thiscontext->contextlevel == CONTEXT_COURSE){
        require_login($thiscontext->instanceid, false);
    } elseif ($thiscontext->contextlevel == CONTEXT_MODULE){
        list($module, $cm) = get_module_from_cmid($thiscontext->instanceid);
        require_login($cm->course, false, $cm);
    }
    $contexts->require_one_edit_tab_cap($edittab);
}

$PAGE->set_url($thispageurl);

$import_form = new question_import_form($thispageurl, array('contexts'=>$contexts->having_one_edit_tab_cap('import'),
                                                    'defaultcategory'=>$pagevars['cat']));

if ($import_form->is_cancelled()){
    redirect($thispageurl);
}
//==========
// PAGE HEADER
//==========
$PAGE->set_title($txt->importquestions);
$PAGE->set_heading($COURSE->fullname);
echo $OUTPUT->header();

// Print horizontal nav if needed.
$renderer = $PAGE->get_renderer('core_question', 'bank');
echo $renderer->extra_horizontal_navigation();

// file upload form sumitted
if ($form = $import_form->get_data()) {

    // file checks out ok
    $fileisgood = false;

    // work out if this is an uploaded file
    // or one from the filesarea.
    $realfilename = $import_form->get_new_filename('newfile');
    $importfile = make_request_directory() . "/{$realfilename}";
    if (!$result = $import_form->save_file('newfile', $importfile, true)) {
        throw new moodle_exception('uploadproblem');
    }

    $formatfile = 'format/' . $form->format . '/format.php';
    if (!is_readable($formatfile)) {
        throw new moodle_exception('formatnotfound', 'question', '', $form->format);
    }

    require_once($formatfile);

    $classname = 'qformat_' . $form->format;
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
    if (!$qformat->importpreprocess()) {
        print_error('cannotimport', '', $thispageurl->out());
    }

    // Process the uploaded file
    if (!$qformat->importprocess()) {
        print_error('cannotimport', '', $thispageurl->out());
    }

    // In case anything needs to be done after
    if (!$qformat->importpostprocess()) {
        print_error('cannotimport', '', $thispageurl->out());
    }

    // Log the import into this category.
    $eventparams = [
            'contextid' => $qformat->category->contextid,
            'other' => ['format' => $form->format, 'categoryid' => $qformat->category->id],
    ];
    $event = \core\event\questions_imported::create($eventparams);
    $event->trigger();

    $params = $thispageurl->params() + array(
        'category' => $qformat->category->id . ',' . $qformat->category->contextid);
    echo $OUTPUT->continue_button(new moodle_url('edit.php', $params));
    echo $OUTPUT->footer();
    exit;
}

echo $OUTPUT->heading_with_help($txt->importquestions, 'importquestions', 'question');

/// Print upload form
$import_form->display();
echo $OUTPUT->footer();
