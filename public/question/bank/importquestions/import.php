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
 * @package    qbank_importquestions
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->dirroot . '/question/format.php');
require_once($CFG->dirroot . '/question/renderer.php');

use qbank_importquestions\form\question_import_form;

require_login();
core_question\local\bank\helper::require_plugin_enabled('qbank_importquestions');
list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) =
        question_edit_setup('import', '/question/bank/importquestions/import.php');

// Get display strings.
$txt = new stdClass();
$txt->importerror = get_string('importerror', 'question');
$txt->importquestions = get_string('importquestions', 'question');

list($catid, $catcontext) = explode(',', $pagevars['cat']);
if (!$category = $DB->get_record("question_categories", ['id' => $catid])) {
    throw new moodle_exception('nocategory', 'question');
}

$categorycontext = context::instance_by_id($category->contextid);
$category->context = $categorycontext;
// This page can be called without cmid in which case.
// We get the context from the category object.
if ($contexts === null) { // Need to get the course from the chosen category.
    $contexts = new core_question\local\bank\question_edit_contexts($categorycontext);
    $thiscontext = $contexts->lowest();
    [$module, $cm] = get_module_from_cmid($thiscontext->instanceid);
    require_login($cm->course, false, $cm);
    $contexts->require_one_edit_tab_cap($edittab);
}

$PAGE->set_url($thispageurl);

$importform = new question_import_form($thispageurl, ['contexts' => $contexts->having_one_edit_tab_cap('import'),
    'defaultcategory' => $pagevars['cat']]);

if ($importform->is_cancelled()) {
    redirect($thispageurl);
}
// Page header.
$PAGE->set_title($txt->importquestions);
$PAGE->set_heading($COURSE->fullname);
$PAGE->activityheader->disable();

echo $OUTPUT->header();

// Print horizontal nav if needed.
$renderer = $PAGE->get_renderer('core_question', 'bank');

$qbankaction = new \core_question\output\qbank_action_menu($thispageurl);
echo $renderer->render($qbankaction);

// File upload form submitted.
if ($form = $importform->get_data()) {

    // File checks out ok.
    $fileisgood = false;

    // Work out if this is an uploaded file.
    // Or one from the filesarea.
    $realfilename = $importform->get_new_filename('newfile');
    $importfile = make_request_directory() . "/{$realfilename}";
    if (!$result = $importform->save_file('newfile', $importfile, true)) {
        throw new moodle_exception('uploadproblem');
    }

    $formatfile = $CFG->dirroot . '/question/format/' . $form->format . '/format.php';
    if (!is_readable($formatfile)) {
        throw new moodle_exception('formatnotfound', 'question', '', $form->format);
    }

    require_once($formatfile);

    $classname = 'qformat_' . $form->format;
    $qformat = new $classname();

    // Load data into class.
    $qformat->setCategory($category);
    $qformat->setContexts($contexts->having_one_edit_tab_cap('import'));
    $qformat->setCourse($COURSE);
    $qformat->setFilename($importfile);
    $qformat->setRealfilename($realfilename);
    $qformat->setMatchgrades($form->matchgrades);
    $qformat->setCatfromfile(!empty($form->catfromfile));
    $qformat->setContextfromfile(!empty($form->contextfromfile));
    $qformat->setStoponerror($form->stoponerror);

    // Do anything before that we need to.
    if (!$qformat->importpreprocess()) {
        throw new moodle_exception('cannotimport', '', $thispageurl->out());
    }

    // Process the uploaded file.
    if (!$qformat->importprocess()) {
        throw new moodle_exception('cannotimport', '', $thispageurl->out());
    }

    // In case anything needs to be done after.
    if (!$qformat->importpostprocess()) {
        throw new moodle_exception('cannotimport', '', $thispageurl->out());
    }

    // Log the import into this category.
    $eventparams = [
            'contextid' => $qformat->category->contextid,
            'other' => ['format' => $form->format, 'categoryid' => $qformat->category->id],
    ];
    $event = \core\event\questions_imported::create($eventparams);
    $event->trigger();

    $params = $thispageurl->params() + ['category' => $qformat->category->id . ',' . $qformat->category->contextid];
    echo $OUTPUT->continue_button(new moodle_url('/question/edit.php', $params));
    echo $OUTPUT->footer();
    exit;
}

echo $OUTPUT->heading_with_help($txt->importquestions, 'importquestions', 'question');

// Print upload form.
$importform->display();
echo $OUTPUT->footer();
