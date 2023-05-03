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
require_once($CFG->dirroot."/question/editlib.php");

use qbank_managecategories\form\question_move_form;
use qbank_managecategories\helper;
use qbank_managecategories\question_category_object;

require_login();
core_question\local\bank\helper::require_plugin_enabled(helper::PLUGINNAME);

list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) =
        question_edit_setup('categories', '/question/bank/managecategories/category.php');

// Get values from form for actions on this page.
$param = new stdClass();
$param->moveup = optional_param('moveup', 0, PARAM_INT);
$param->movedown = optional_param('movedown', 0, PARAM_INT);
$param->moveupcontext = optional_param('moveupcontext', 0, PARAM_INT);
$param->movedowncontext = optional_param('movedowncontext', 0, PARAM_INT);
$param->tocontext = optional_param('tocontext', 0, PARAM_INT);
$param->left = optional_param('left', 0, PARAM_INT);
$param->right = optional_param('right', 0, PARAM_INT);
$param->delete = optional_param('delete', 0, PARAM_INT);
$param->confirm = optional_param('confirm', 0, PARAM_INT);
$param->cancel = optional_param('cancel', '', PARAM_ALPHA);
$param->move = optional_param('move', 0, PARAM_INT);
$param->moveto = optional_param('moveto', 0, PARAM_INT);
$param->edit = optional_param('edit', null, PARAM_INT);

$url = new moodle_url($thispageurl);
foreach ((array)$param as $key => $value) {
    if (($key !== 'cancel' && $key !== 'edit' && $value !== 0) ||
            ($key === 'cancel' && $value !== '') ||
            ($key === 'edit' && $value !== null)) {
        $url->param($key, $value);
    }
}
$PAGE->set_url($url);

$qcobject = new question_category_object($pagevars['cpage'], $thispageurl,
        $contexts->having_one_edit_tab_cap('categories'), $param->edit,
        $pagevars['cat'], $param->delete, $contexts->having_cap('moodle/question:add'));

if ($param->left || $param->right || $param->moveup || $param->movedown) {
    require_sesskey();

    foreach ($qcobject->editlists as $list) {
        // Processing of these actions is handled in the method where appropriate and page redirects.
        $list->process_actions($param->left, $param->right, $param->moveup, $param->movedown);
    }
}

if ($param->moveupcontext || $param->movedowncontext) {
    require_sesskey();

    if ($param->moveupcontext) {
        $catid = $param->moveupcontext;
    } else {
        $catid = $param->movedowncontext;
    }
    $newtopcat = question_get_top_category($param->tocontext);
    if (!$newtopcat) {
        throw new moodle_exception('invalidcontext');
    }
    $oldcat = $DB->get_record('question_categories', ['id' => $catid], '*', MUST_EXIST);
    // Log the move to another context.
    $category = new stdClass();
    $category->id = explode(',', $pagevars['cat'], -1)[0];
    $category->contextid = $param->tocontext;
    $event = \core\event\question_category_moved::create_from_question_category_instance($category);
    $event->trigger();
    // Update the set_reference records when moving a category to a different context.
    move_question_set_references($catid, $catid, $oldcat->contextid, $category->contextid);
    $qcobject->update_category($catid, "{$newtopcat->id},{$param->tocontext}", $oldcat->name, $oldcat->info);
    // The previous line does a redirect().
}

if ($param->delete) {
    if (!$category = $DB->get_record("question_categories", ["id" => $param->delete])) {
        throw new moodle_exception('nocate', 'question', $thispageurl->out(), $param->delete);
    }

    helper::question_remove_stale_questions_from_category($param->delete);

    $questionstomove = count($qcobject->get_real_question_ids_in_category($param->delete));

    // Second pass, if we still have questions to move, setup the form.
    if ($questionstomove) {
        $categorycontext = context::instance_by_id($category->contextid);
        $moveform = new question_move_form($thispageurl,
            ['contexts' => [$categorycontext], 'currentcat' => $param->delete]);
        if ($moveform->is_cancelled()) {
            redirect($thispageurl);
        } else if ($formdata = $moveform->get_data()) {
            list($tocategoryid, $tocontextid) = explode(',', $formdata->category);
            $qcobject->move_questions_and_delete_category($formdata->delete, $tocategoryid);
            $thispageurl->remove_params('cat', 'category');
            redirect($thispageurl);
        }
    }
} else {
    $questionstomove = 0;
}

if ($qcobject->catform->is_cancelled()) {
    redirect($thispageurl);
} else if ($catformdata = $qcobject->catform->get_data()) {
    $catformdata->infoformat = $catformdata->info['format'];
    $catformdata->info       = $catformdata->info['text'];
    if (!$catformdata->id) {// New category.
        $qcobject->add_category($catformdata->parent, $catformdata->name,
                $catformdata->info, false, $catformdata->infoformat, $catformdata->idnumber);
    } else {
        $qcobject->update_category($catformdata->id, $catformdata->parent,
                $catformdata->name, $catformdata->info, $catformdata->infoformat, $catformdata->idnumber);
    }
    redirect($thispageurl);
} else if ((!empty($param->delete) and (!$questionstomove) and confirm_sesskey())) {
    $qcobject->delete_category($param->delete);// Delete the category now no questions to move.
    $thispageurl->remove_params('cat', 'category');
    redirect($thispageurl);
}

if ($param->edit !== null || $qcobject->catform->is_submitted()) {
    // In the is_submitted case, we only get here if it was submitted,
    // but not valid, so we need to show the validation error.
    $PAGE->navbar->add(get_string('editingcategory', 'question'));
}

$PAGE->set_title(get_string('editcategories', 'question'));
$PAGE->set_heading($COURSE->fullname);
$PAGE->activityheader->disable();

echo $OUTPUT->header();

// Print horizontal nav if needed.
$renderer = $PAGE->get_renderer('core_question', 'bank');

$qbankaction = new \core_question\output\qbank_action_menu($url);
echo $renderer->render($qbankaction);

// Display the UI.
if ($param->edit !== null || $qcobject->catform->is_submitted()) {
    // In the is_submitted case, we only get here if it was submitted,
    // but not valid, so we need to show the validation error.
    // In this case, category id is in the 'id' hidden filed.
    $qcobject->edit_single_category($param->edit ?? required_param('id', PARAM_INT));
} else if ($questionstomove) {
    $vars = new stdClass();
    $vars->name = $category->name;
    $vars->count = $questionstomove;
    echo $OUTPUT->box(get_string('categorymove', 'question', $vars), 'generalbox boxaligncenter');
    $moveform->display();
} else {
    // Display the user interface.
    $qcobject->display_user_interface();
}
echo $OUTPUT->footer();
