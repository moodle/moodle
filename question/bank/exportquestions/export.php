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
 * Script for importing questions into the question bank.
 *
 * @package    qbank_exportquestions
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->dirroot . '/question/format.php');
require_once($CFG->dirroot . '/question/renderer.php');

use qbank_exportquestions\exportquestions_helper;
use qbank_exportquestions\form\export_form;

require_login();
core_question\local\bank\helper::require_plugin_enabled('qbank_exportquestions');

list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) =
        question_edit_setup('export', '/question/bank/exportquestions/export.php');

// Get display strings.
$strexportquestions = get_string('exportquestions', 'question');

list($catid, $catcontext) = explode(',', $pagevars['cat']);
$category = $DB->get_record('question_categories', ["id" => $catid, 'contextid' => $catcontext], '*', MUST_EXIST);

// Header.
$PAGE->set_url($thispageurl);
$PAGE->set_title($strexportquestions);
$PAGE->set_heading($COURSE->fullname);
$PAGE->activityheader->disable();

echo $OUTPUT->header();

// Print horizontal nav if needed.
$renderer = $PAGE->get_renderer('core_question', 'bank');

$qbankaction = new \core_question\output\qbank_action_menu($thispageurl);
echo $renderer->render($qbankaction);

$exportform = new export_form($thispageurl,
        ['contexts' => $contexts->having_one_edit_tab_cap('export'), 'defaultcategory' => $pagevars['cat']]);

if ($fromform = $exportform->get_data()) {
    $thiscontext = $contexts->lowest();
    if (!is_readable($CFG->dirroot . "/question/format/{$fromform->format}/format.php")) {
        throw new moodle_exception('unknowformat', '', '', $fromform->format);
    }
    $withcategories = 'nocategories';
    if (!empty($fromform->cattofile)) {
        $withcategories = 'withcategories';
    }
    $withcontexts = 'nocontexts';
    if (!empty($fromform->contexttofile)) {
        $withcontexts = 'withcontexts';
    }

    $classname = 'qformat_' . $fromform->format;
    $qformat = new $classname();
    $filename = question_default_export_filename($COURSE, $category) .
            $qformat->export_file_extension();
    $exporturl = exportquestions_helper::question_make_export_url($thiscontext->id, $category->id,
            $fromform->format, $withcategories, $withcontexts, $filename);

    echo $OUTPUT->box_start();
    echo get_string('yourfileshoulddownload', 'question', $exporturl->out());
    echo $OUTPUT->box_end();

    // Log the export of these questions.
    $eventparams = [
            'contextid' => $category->contextid,
            'other' => ['format' => $fromform->format, 'categoryid' => $category->id],
    ];
    $event = \core\event\questions_exported::create($eventparams);
    $event->trigger();

    // Don't allow force download for behat site, as pop-up can't be handled by selenium.
    if (!defined('BEHAT_SITE_RUNNING')) {
        $PAGE->requires->js_function_call('document.location.replace', [$exporturl->out(false)], false, 1);
    }

    echo $OUTPUT->continue_button(new moodle_url($PAGE->settingsnav->find(
                                            'questionbank',
                                            \navigation_node::TYPE_CONTAINER)->action, $thispageurl->params()));
    echo $OUTPUT->footer();
    exit;
}

// Display export form.
echo $OUTPUT->heading_with_help($strexportquestions, 'exportquestions', 'question');

$exportform->display();

echo $OUTPUT->footer();
