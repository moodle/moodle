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
 * Atto text editor import Microsoft Word file and convert to HTML
 *
 * @package   atto_wordimport
 * @copyright 2015 Eoin Campbell
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require(__DIR__ . '/../../../../../config.php');
require_once(__DIR__ . '/lib.php');

$itemid = required_param('itemid', PARAM_INT);
$contextid = required_param('ctx_id', PARAM_INT);
$filename = required_param('filename', PARAM_TEXT);
$sesskey = required_param('sesskey', PARAM_TEXT);

list($context, $course, $cm) = get_context_info_array($contextid);

// Check that this user is logged in before proceeding.
require_login($course, false, $cm);
require_sesskey();

$PAGE->set_context($context);

// Get the reference only of this users' uploaded file, to avoid rogue users' accessing other peoples files.
$fs = get_file_storage();
$usercontext = context_user::instance($USER->id);
if (!$file = $fs->get_file($usercontext->id, 'user', 'draft', $itemid, '/', basename($filename))) {
    // File is not readable.
    throw new \moodle_exception(get_string('errorreadingfile', 'error', basename($filename)));
}

// Save the uploaded file to a folder so we can process it using the PHP Zip library.
if (!$tmpfilename = $file->copy_content_to_temp()) {
    // Cannot save file.
    throw new \moodle_exception(get_string('errorcreatingfile', 'error', basename($filename)));
} else {
    // Delete it from the draft file area to avoid possible name-clash messages if it is re-uploaded in the same edit.
    $file->delete();
}

// Convert the Word file into XHTML, store any images, and delete the temporary HTML file once we're finished.
$htmltext = atto_wordimport_convert_to_xhtml($tmpfilename, $usercontext->id, $itemid);

if (!$htmltext) {
    // Error processing upload file.
    throw new \moodle_exception(get_string('cannotuploadfile', 'error'));
}

$htmltextjson = json_encode($htmltext);
if ($htmltextjson) {
    echo '{"html": ' . $htmltextjson . '}';
} else {
    // Invalid JSON string.
    throw new \moodle_exception(get_string('invalidjson', 'repository'));
}
