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
 * Create board column.
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

$boardid = required_param('boardid', PARAM_INT);

$board = board::get_board($boardid, MUST_EXIST);
$cm = board::coursemodule_for_board($board);
$context = context_module::instance($cm->id);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

require_login($course, false, $cm);
require_capability('mod/board:view', $context);
require_capability('mod/board:manageboard', $context);

$pageurl = new moodle_url(
    '/mod/board/column_create_ajax.php',
    ['boardid' => $board->id]
);
$returnurl = new moodle_url('/mod/board/view.php', ['id' => $cm->id]);

$PAGE->set_url($pageurl);
$PAGE->set_context($context);

$form = new \mod_board\local\form\column_create(null, ['board' => $board]);

if ($form->is_cancelled()) {
    $form::ajax_form_cancelled($returnurl);
}
if ($data = $form->get_data()) {
    $column = column::create($data->boardid, $data->name);
    // Full page reload is expected.
    $form::ajax_form_submitted(
        $returnurl,
        ['name' => note::format_plain_text($column->name)]
    );
}

$form->display();

$form::ajax_form_render(
    dialogtitle: get_string('aria_newcolumn', 'mod_board'),
    submittext: get_string('submit'),
    submitarialabel: get_string('aria_newcolumn', 'mod_board')
);
