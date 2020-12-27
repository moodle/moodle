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
 * Resets a file type to the default Moodle values.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('tool_filetypes');

$extension = required_param('extension', PARAM_RAW);
$redirecturl = new \moodle_url('/admin/tool/filetypes/index.php');

if (optional_param('revert', 0, PARAM_INT)) {
    require_sesskey();

    // Reset the file type in config.
    core_filetypes::revert_type_to_default($extension);
    redirect($redirecturl);
}

// Page settings.
$title = get_string('revertfiletype', 'tool_filetypes');

$context = context_system::instance();
$PAGE->set_url(new \moodle_url('/admin/tool/filetypes/revert.php', array('extension' => $extension)));
$PAGE->navbar->add($title);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($SITE->fullname. ': ' . $title);

// Display the page.
echo $OUTPUT->header();

$message = get_string('revert_confirmation', 'tool_filetypes', $extension);
$reverturl = new \moodle_url('revert.php', array('extension' => $extension, 'revert' => 1));
$yesbutton = new single_button($reverturl, get_string('yes'));
$nobutton = new single_button($redirecturl, get_string('no'), 'get');
echo $OUTPUT->confirm($message, $yesbutton, $nobutton);

echo $OUTPUT->footer();
