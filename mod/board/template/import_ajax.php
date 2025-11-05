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
 * Create or update a template.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_board\local\template;

define('AJAX_SCRIPT', true);

require('../../../config.php');
require_once("$CFG->libdir/filelib.php");

$syscontext = context_system::instance();

require_login();
require_capability('mod/board:managetemplates', $syscontext);

$pageurl = new moodle_url('/mod/board/template/import.php');
$returnurl = new moodle_url('/mod/board/template/index.php');

$PAGE->set_url($pageurl);
$PAGE->set_context($syscontext);

$form1 = new \mod_board\local\form\template_import(null, []);
$form2 = new \mod_board\local\form\template_edit(null, ['id' => 0, 'contextid' => $syscontext->id]);

$template = [];

if ($form1->is_cancelled()) {
    $form1::ajax_form_cancelled($returnurl);
}
if ($form2->is_cancelled()) {
    $form2::ajax_form_cancelled($returnurl);
}

if ($form1->get_data()) {
    // Show the template creation form.
    $content = $form1->get_file_content('importfile');
    $template = \mod_board\local\template::decode_import_file($content);
    if ($template) {
        file_prepare_standard_editor($template, 'description', []);
        file_prepare_standard_editor($template, 'intro', []);
        $form2->set_data($template);
    }
    $form = $form2;
} else if ($form1->is_submitted()) {
    // JSON file validation error.
    $form = $form1;
} else if ($data = $form2->get_data()) {
    // Final step - create the template.
    template::create($data);
    $form2::ajax_form_submitted($returnurl);
} else if ($form2->is_submitted()) {
    // Template validation errors.
    $form = $form2;
} else {
    // Starting point.
    $form = $form1;
}

$form->display();

if ($form instanceof \mod_board\local\form\template_import) {
    $form::ajax_form_render(
        dialogtitle: get_string('template_import', 'mod_board'),
        submittext: get_string('continue')
    );
} else {
    $form::ajax_form_render(
        dialogtitle: get_string('template_import', 'mod_board'),
        submittext: get_string('template_create', 'mod_board'),
    );
}
