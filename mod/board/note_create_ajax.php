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
 * Create a board note.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_board\board;
use mod_board\local\note;
use mod_board\local\form\note_edit;

define('AJAX_SCRIPT', true);

require('../../config.php');

$columnid = required_param('columnid', PARAM_INT);
$ownerid = required_param('ownerid', PARAM_INT);
$groupid = required_param('groupid', PARAM_INT);

$column = board::get_column($columnid, MUST_EXIST);
$board = board::get_board($column->boardid, MUST_EXIST);
$cm = board::coursemodule_for_board($board);
$context = context_module::instance($cm->id);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

require_login($course, false, $cm);
require_capability('mod/board:view', $context);
require_capability('mod/board:post', $context);

$pageurl = new moodle_url(
    '/mod/board/note_create_ajax.php',
    ['columnid' => $column->id, 'ownerid' => $ownerid, 'groupid' => $groupid]
);
$returnurl = new moodle_url('/mod/board/view.php', ['id' => $cm->id]);

$PAGE->set_url($pageurl);
$PAGE->set_context($context);

if ($board->singleusermode != board::SINGLEUSER_DISABLED) {
    // Groups are not used in single-user-mode apart from user selection.
    $groupid = null;
} else {
    $groupmode = groups_get_activity_groupmode($cm);
    if ($groupmode == NOGROUPS) {
        $groupid = null;
    } else {
        if ($groupid) {
            board::require_access_for_group($board, $groupid);
        } else {
            // Only managers can post in "All groups".
            require_capability('mod/board:manageboard', $context);
        }
    }
}

if (board::board_readonly($board, $groupid)) {
    throw new \core\exception\invalid_parameter_exception('board is read only');
}

if ($board->singleusermode == board::SINGLEUSER_DISABLED) {
    if ($ownerid && $ownerid != $USER->id) {
        throw new \core\exception\invalid_parameter_exception('ownerid not available when single suer mode disabled');
    }
    $ownerid = $USER->id;
} else {
    if (!$ownerid) {
        debugging('ownerid is required in single-user modes', DEBUG_DEVELOPER);
        $ownerid = $USER->id;
    }
}

if (!board::can_post($board, $ownerid)) {
    throw new \core\exception\invalid_parameter_exception('Cannot post in board');
}

$formdata = (object)[
    'id' => '0',
    'columnid' => $column->id,
    'ownerid' => $ownerid,
    'groupid' => $groupid,
];

$form = new note_edit(null, ['data' => $formdata, 'formatted' => null, 'column' => $column, 'board' => $board]);

if ($form->is_cancelled()) {
    $form::ajax_form_cancelled($returnurl);
}
if ($data = $form->get_data()) {
    $note = note::create(
        $column->id,
        $ownerid,
        $groupid,
        $data->heading,
        $data->content,
        $form::get_attachment($data)
    );
    $formatted = note::format_for_display($note, $column, $board, $context);

    // Full page reload is expected.
    $form::ajax_form_submitted(
        $returnurl,
        ['note' => $formatted, 'historyid' => $note->historyid]
    );
}

$form->display();

$columnname = note::format_plain_text($column->name);
$dialogtitle = get_string('modal_title_new', 'mod_board');
$dialogtitle = str_replace('{column}', $columnname, $dialogtitle);

$submitarialabel = get_string('aria_postnew', 'mod_board');
$submitarialabel = str_replace('{column}', $columnname, $submitarialabel);

$form::ajax_form_render(
    dialogtitle: $dialogtitle,
    submittext: get_string('post_button_text', 'mod_board'),
    submitarialabel: $submitarialabel
);
