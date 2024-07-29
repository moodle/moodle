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
 * This script allows a teacher to create, edit and delete question categories.
 *
 * @package    qbank_managecategories
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @author     2021, Guillermo Gomez Arias <guillermogomez@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/question/editlib.php');

use core_question\output\qbank_actionbar;
use core_question\category_manager;
use qbank_managecategories\form\question_move_form;
use qbank_managecategories\helper;
use qbank_managecategories\output\categories;
use qbank_managecategories\output\categories_header;
use qbank_managecategories\question_categories;

require_login();
core_question\local\bank\helper::require_plugin_enabled(helper::PLUGINNAME);

list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) =
    question_edit_setup('categories', '/question/bank/managecategories/category.php');
$courseid = optional_param('courseid', 0, PARAM_INT);

if (!is_null($cmid)) {
    $thiscontext = context_module::instance($cmid)->id;
} else {
    $course = get_course($courseid);
    $thiscontext = context_course::instance($course->id)->id;
}

$todelete = optional_param('delete', 0, PARAM_INT); // The ID of a category to delete.

$PAGE->set_url($thispageurl);
$PAGE->add_body_class('limitedwidth');

$manager = new category_manager($thispageurl);

if ($todelete) {
    if (!$category = $DB->get_record("question_categories", ["id" => $todelete])) {
        throw new moodle_exception('nocate', 'question', $thispageurl->out(), $todelete);
    }

    helper::question_remove_stale_questions_from_category($todelete);

    $questionstomove = count($manager->get_real_question_ids_in_category($todelete));

    // Second pass, if we still have questions to move, setup the form.
    if ($questionstomove) {
        $categorycontext = context::instance_by_id($category->contextid);
        $moveform = new question_move_form($thispageurl,
            ['contexts' => [$categorycontext], 'currentcat' => $todelete]);
        if ($moveform->is_cancelled()) {
            $thispageurl->remove_all_params();
            if (!is_null($cmid)) {
                $thispageurl->param('cmid', $cmid);
            } else {
                $thispageurl->param('courseid', $courseid);
            }
            redirect($thispageurl);
        } else if ($formdata = $moveform->get_data()) {
            list($tocategoryid, $tocontextid) = explode(',', $formdata->category);
            $manager->move_questions_and_delete_category($formdata->delete, $tocategoryid);
            $thispageurl->remove_params('cat', 'category');
            redirect($thispageurl);
        }
    }
} else {
    $questionstomove = 0;
}

if ((!empty($todelete) && (!$questionstomove) && confirm_sesskey())) {
    $manager->delete_category($todelete);// Delete the category now no questions to move.
    $thispageurl->remove_params('cat', 'category');
    redirect($thispageurl);
}

$PAGE->set_title(get_string('editcategories', 'question'));
$PAGE->set_heading($COURSE->fullname);
$PAGE->activityheader->disable();

// Print horizontal nav if needed.
$renderer = $PAGE->get_renderer('core_question', 'bank');

$categoriesrenderer = $PAGE->get_renderer('qbank_managecategories');
echo $OUTPUT->header();
$qbankaction = new \core_question\output\qbank_action_menu($thispageurl);
echo $renderer->render($qbankaction);
if ($questionstomove) {
    $vars = new stdClass();
    $vars->name = $category->name;
    $vars->count = $questionstomove;
    echo $OUTPUT->box(get_string('categorymove', 'question', $vars), 'generalbox boxaligncenter');
    $moveform->display();
} else {
    // Display the user interface.
    $questioncategories = new question_categories(
        $thispageurl,
        $contexts->having_one_edit_tab_cap('categories'),
        $cmid,
        $courseid,
        $thiscontext,
    );
    $PAGE->requires->js_call_amd('qbank_managecategories/categorymanager', 'init'); // Load reactive module.
    echo $OUTPUT->render(new categories_header($questioncategories));
    echo $OUTPUT->render(new categories($questioncategories));
}

echo $OUTPUT->footer();
