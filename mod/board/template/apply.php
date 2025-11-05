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
 * Apply template to existing board.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_board\board;

require('../../../config.php');

$id = required_param('id', PARAM_INT); // Course Module ID.
$templateid = optional_param('templateid', 0, PARAM_INT);

if (!$cm = get_coursemodule_from_id('board', $id)) {
    throw new \moodle_exception('invalidcoursemodule');
}
$board = board::get_board($cm->instance, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('moodle/course:manageactivities', $context);

$returnurl = new moodle_url('/mod/board/view.php', ['id' => $cm->id]);

if (board::board_has_notes($board->id)) {
    redirect($returnurl);
}

$templates = \mod_board\local\template::get_applicable_templates($context);
if (!$templates) {
    redirect($returnurl);
}
if (isset($templates[$templateid])) {
    $template = $DB->get_record('board_templates', ['id' => $templateid], '*', MUST_EXIST);
} else {
    $templateid = 0;
    $template = null;
}

$PAGE->set_context($context);
$PAGE->set_url('/mod/board/template/apply.php', ['id' => $cm->id]);
$title = get_string('template_apply', 'mod_board');
$PAGE->set_title($title);
$PAGE->set_heading($title);
// Match the appearance of settings page.
$PAGE->set_pagelayout('admin');
$PAGE->add_body_class('limitedwidth');
$PAGE->activityheader->disable();

if (!$template) {
    $form = new \mod_board\local\form\template_apply(null, ['board' => $board, 'templates' => $templates]);
    $form->set_data(['id' => $id]);
    if ($form->is_cancelled()) {
        redirect($returnurl);
    }
} else {
    $form = new \mod_board\local\form\template_apply_confirm(null, ['board' => $board, 'template' => $template]);
    $form->set_data(['id' => $id, 'templateid' => $template->id]);
    if ($form->is_cancelled()) {
        redirect($returnurl);
    }
    if ($data = $form->get_data()) {
        \mod_board\local\template::apply($board->id, $template->id);
        redirect($returnurl);
    }
}

echo $OUTPUT->header();

echo $OUTPUT->heading($title, 2);

$form->display();

echo $OUTPUT->footer();
