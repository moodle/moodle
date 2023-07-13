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

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/question/editlib.php');

list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) =
        question_edit_setup('questions', '/question/edit.php');

if (($lastchanged = optional_param('lastchanged', 0, PARAM_INT)) !== 0) {
    $thispageurl->param('lastchanged', $lastchanged);
}
$PAGE->set_url($thispageurl);

if ($PAGE->course->id == $SITE->id) {
    $PAGE->set_primary_active_tab('home');
}

$thispageurl->param('deleteall', 1);
$questionbank = new core_question\local\bank\view($contexts, $thispageurl, $COURSE, $cm);

$context = $contexts->lowest();
$streditingquestions = get_string('editquestions', 'question');
$PAGE->set_title($streditingquestions);
$PAGE->set_heading($COURSE->fullname);
$PAGE->activityheader->disable();

echo $OUTPUT->header();

// Print horizontal nav if needed.
$renderer = $PAGE->get_renderer('core_question', 'bank');

// Render the selection action.
$qbankaction = new \core_question\output\qbank_action_menu($thispageurl);
echo $renderer->render($qbankaction);

// Print the question area.
$questionbank->display($pagevars, 'questions');

// Log the view of this category.
list($categoryid, $contextid) = explode(',', $pagevars['cat']);
$category = new stdClass();
$category->id = $categoryid;
$catcontext = \context::instance_by_id($contextid);
$event = \core\event\question_category_viewed::create_from_question_category_instance($category, $catcontext);
$event->trigger();

echo $OUTPUT->footer();
