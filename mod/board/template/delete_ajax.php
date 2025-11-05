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
 * Delete template.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_board\local\template;
use mod_board\local\form\template_delete;

define('AJAX_SCRIPT', true);

require('../../../config.php');

$id = required_param('id', PARAM_INT);

$syscontext = context_system::instance();

require_login();
require_capability('mod/board:managetemplates', $syscontext);

$pageurl = new moodle_url('/mod/board/template/delete_ajax.php', ['id' => $id]);
$returnurl = new moodle_url('/mod/board/template/index.php');

$PAGE->set_url($pageurl);
$PAGE->set_context($syscontext);

$template = $DB->get_record('board_templates', ['id' => $id], '*', MUST_EXIST);

$form = new template_delete(null, ['template' => $template]);

if ($form->is_cancelled()) {
    $form::ajax_form_cancelled($returnurl);
}
if ($data = $form->get_data()) {
    template::delete($data->id);
    $form::ajax_form_submitted($returnurl);
}

$form->display();

$form::ajax_form_render(
    dialogtitle: get_string('template_delete', 'mod_board'),
    submittext: get_string('template_delete', 'mod_board')
);
