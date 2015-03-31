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
 * Delete a file type with a confirmation box.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('tool_filetypes');

$extension = required_param('extension', PARAM_ALPHANUMEXT);
$redirecturl = new \moodle_url('/admin/tool/filetypes/index.php');

if (optional_param('delete', 0, PARAM_INT)) {
    require_sesskey();

    // Delete the file type from the config.
    core_filetypes::delete_type($extension);
    redirect($redirecturl);
}

// Page settings.
$title = get_string('deletefiletypes', 'tool_filetypes');

$context = context_system::instance();
$PAGE->set_url(new \moodle_url('/admin/tool/filetypes/delete.php', array('extension' => $extension)));
$PAGE->navbar->add($title);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($SITE->fullname. ': ' . $title);

// Display the page.
echo $OUTPUT->header();

$message = get_string('delete_confirmation', 'tool_filetypes', $extension);
$deleteurl = new \moodle_url('delete.php', array('extension' => $extension, 'delete' => 1));
$yesbutton = new single_button($deleteurl, get_string('yes'));
$nobutton = new single_button($redirecturl, get_string('no'), 'get');
echo $OUTPUT->confirm($message, $yesbutton, $nobutton);

echo $OUTPUT->footer();
