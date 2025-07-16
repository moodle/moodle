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
 * Page to edit the question bank
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\event\question_category_viewed;
use core_question\output\qbank_action_menu;
use core_question\local\bank\view;

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/question/editlib.php');

// Since Moodle 5.0 any request with the courseid parameter is deprecated and will redirect to the banks management page.
if ($courseid = optional_param('courseid', 0, PARAM_INT)) {
    redirect(new moodle_url('/question/banks.php', ['courseid' => $courseid]));
}

list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) =
        question_edit_setup('questions', '/question/edit.php');

$actionurl = new moodle_url($thispageurl);
if (($lastchanged = optional_param('lastchanged', 0, PARAM_INT)) !== 0) {
    $thispageurl->param('lastchanged', $lastchanged);
}
$PAGE->set_url($thispageurl);

if ($PAGE->course->id == $SITE->id) {
    $PAGE->set_primary_active_tab('home');
}

// Mods that publish questions need a modified navbar breadcrumb here as they are managed differently to other module types.
if (plugin_supports('mod', $cm->modname, FEATURE_PUBLISHES_QUESTIONS, false)) {
    $PAGE->navbar->ignore_active();
    $coursenode = $PAGE->navigation->find($PAGE->course->id, navigation_node::TYPE_COURSE);
    $modnode = $PAGE->navigation->find($cm->id, navigation_node::TYPE_ACTIVITY);
    $PAGE->navbar->add($coursenode->text, $coursenode->action, $coursenode->type, $coursenode->shorttext, icon: $coursenode->icon);
    $PAGE->navbar->add(get_string('questionbank_plural', 'core_question'),
        \core_question\local\bank\question_bank_helper::get_url_for_qbank_list($PAGE->course->id)
    );
    $PAGE->navbar->add($modnode->text, $modnode->action, $modnode->type, $modnode->shorttext, icon: $modnode->icon);
}

$questionbank = new view($contexts, $thispageurl, $COURSE, $cm, $pagevars);

$context = $contexts->lowest();
$streditingquestions = get_string('editquestions', 'question');
$PAGE->set_title($streditingquestions);
$PAGE->set_heading($COURSE->fullname);
$PAGE->activityheader->disable();

echo $OUTPUT->header();
if (!\core_question\local\bank\question_bank_helper::has_bank_migration_task_completed_successfully()) {
    $defaultactivityname = \core_question\local\bank\question_bank_helper::get_default_question_bank_activity_name();
    echo $OUTPUT->notification(
        get_string('transfernotfinished', 'mod_' . $defaultactivityname),
        \core\output\notification::NOTIFY_WARNING,
        false,
    );
    echo $OUTPUT->footer();
    exit();
}

// Print horizontal nav if needed.
$renderer = $PAGE->get_renderer('core_question', 'bank');

// Render the selection action.
$qbankaction = new qbank_action_menu($actionurl);
echo $renderer->render($qbankaction);

// Print the question area.
$questionbank->display();

[$categoryid, $contextid] = explode(',', $pagevars['cat']);
$questionbank->init_bulk_actions_js();

// Log the view of this category.
$category = new stdClass();
$category->id = $categoryid;
$catcontext = context::instance_by_id($contextid);
$event = question_category_viewed::create_from_question_category_instance($category, $catcontext);
$event->trigger();

echo $OUTPUT->footer();
