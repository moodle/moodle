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
 * Update board column.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_board\board;
use mod_board\local\column;
use mod_board\local\note;

define('AJAX_SCRIPT', true);

require('../../config.php');

$columnid = required_param('id', PARAM_INT);

$column = board::get_column($columnid, MUST_EXIST);
$board = board::get_board($column->boardid, MUST_EXIST);
$cm = board::coursemodule_for_board($board);
$context = context_module::instance($cm->id);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

require_login($course, false, $cm);
require_capability('mod/board:view', $context);
require_capability('mod/board:manageboard', $context);

$pageurl = new moodle_url(
    '/mod/board/column_update_ajax.php',
    ['id' => $column->id]
);
$returnurl = new moodle_url('/mod/board/view.php', ['id' => $cm->id]);

$PAGE->set_url($pageurl);
$PAGE->set_context($context);

$form = new \mod_board\local\form\column_update(null, ['column' => $column, 'board' => $board]);

if ($form->is_cancelled()) {
    $form::ajax_form_cancelled($returnurl);
}
if ($data = $form->get_data()) {
    $column = column::update($data->id, $data->name);
    // Full page reload is expected.
    $form::ajax_form_submitted(
        $returnurl,
        ['name' => note::format_plain_text($column->name)]
    );
}

$form->display();

$columnname = note::format_plain_text($column->name);

$dialogtitle = get_string('aria_updatecolumn', 'mod_board');
$dialogtitle = str_replace('{column}', $columnname, $dialogtitle);

$submitarialabel = get_string('aria_updatecolumn', 'mod_board');
$submitarialabel = str_replace('{column}', $columnname, $submitarialabel);

$form::ajax_form_render(
    dialogtitle: $dialogtitle,
    submittext: get_string('update'),
    submitarialabel: $submitarialabel
);
