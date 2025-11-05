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
require_once("$CFG->libdir/filelib.php");

$id = required_param('id', PARAM_INT);

$note = board::get_note($id, MUST_EXIST);
$column = board::get_column($note->columnid, MUST_EXIST);
$board = board::get_board($column->boardid, MUST_EXIST);
$cm = board::coursemodule_for_board($board);
$context = context_module::instance($cm->id);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

require_login($course, false, $cm);
require_capability('mod/board:view', $context);
require_capability('mod/board:post', $context);

$pageurl = new moodle_url('/mod/board/note_update_ajax.php', ['id' => $id]);
$returnurl = new moodle_url('/mod/board/view.php', ['id' => $cm->id]);

$PAGE->set_url($pageurl);
$PAGE->set_context($context);

if ($USER->id != $note->userid) {
    require_capability('mod/board:manageboard', $context);
}
if (!empty($note->groupid)) {
    board::require_access_for_group($board, $note->groupid);
}
if (board::board_readonly($board, $note->groupid)) {
    throw new \core\exception\invalid_parameter_exception('board is read only');
}

$formdata = (object)[
    'id' => $note->id,
    'heading' => $note->heading,
    'content' => $note->content,
    'mediatype' => $note->type,
];

switch ($note->type) {
    case board::MEDIATYPE_YOUTUBE:
        $formdata->youtubetitle = $note->info;
        $formdata->youtubeurl = $note->url;
        break;
    case board::MEDIATYPE_IMAGE:
        $formdata->imagetitle = $note->info;
        break;
    case board::MEDIATYPE_URL:
        $formdata->linktitle = $note->info;
        $formdata->linkurl = $note->url;
        break;
}

// Set up the images filearea.
$pickeroptions = note::get_image_picker_options();
$draftitemid = file_get_submitted_draft_itemid('imagefile');
file_prepare_draft_area($draftitemid, $context->id, 'mod_board', 'images', $note->id, $pickeroptions);
$formdata->imagefile = $draftitemid;

// Set up the files filearea.
$pickeroptions = note::get_general_picker_options();
if ($pickeroptions) {
    $draftitemid = file_get_submitted_draft_itemid('generalfile');
    file_prepare_draft_area($draftitemid, $context->id, 'mod_board', 'files', $note->id, $pickeroptions);
    $formdata->generalfile = $draftitemid;
}

$formatted = note::format_for_display($note, $column, $board, $context);

$form = new note_edit(null, ['data' => $formdata, 'formatted' => $formatted, 'column' => $column, 'board' => $board]);

if ($form->is_cancelled()) {
    $form::ajax_form_cancelled($returnurl);
}
if ($data = $form->get_data()) {
    $note = note::update(
        $note->id,
        $data->heading,
        $data->content,
        $form::get_attachment($data)
    );
    $formatted = note::format_for_display($note, $column, $board, $context);

    $form::ajax_form_submitted(
        $returnurl,
        ['note' => $formatted, 'historyid' => $note->historyid]
    );
}

$form->display();

$columnname = note::format_plain_text($column->name);
$dialogtitle = get_string('modal_title_edit', 'mod_board');
$dialogtitle = str_replace('{column}', $columnname, $dialogtitle);

$submitarialabel = get_string('aria_postedit', 'mod_board');
$submitarialabel = str_replace('{column}', $columnname, $submitarialabel);
$submitarialabel = str_replace('{post}', $formatted->identifier, $submitarialabel);

$form::ajax_form_render(
    dialogtitle: $dialogtitle,
    submittext: get_string('post_button_text', 'mod_board'),
    submitarialabel: $submitarialabel
);
