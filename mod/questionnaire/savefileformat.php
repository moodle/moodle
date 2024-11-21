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
 * savefileformat.php - Replaces dataformatlib.php to capture output into files.
 *
 * @package    mod_questionnaire
 * @copyright  2019 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Sends a formated data file to the browser and optionally a file. This is needed until the main data format API provides a way
 * to ouput a file as well as stream to the browser. This file relies on capturing output buffers (ugly hack).
 *
 * @param string $filename The base filename without an extension
 * @param string $dataformat A dataformat name
 * @param array $columns An ordered map of column keys and labels
 * @param Iterator $iterator An iterator over the records, usually a RecordSet
 * @param array $users
 * @param array $emails
 * @param string $redirect
 */
function save_as_dataformat($filename, $dataformat, $columns, $iterator, $users = [], $emails = [], $redirect = '') {
    global $CFG, $OUTPUT;

    $classname = 'dataformat_' . $dataformat . '\writer';
    if (!class_exists($classname)) {
        throw new coding_exception("Unable to locate dataformat/$dataformat/classes/writer.php");
    }
    $format = new $classname;

    // The data format export could take a while to generate...
    set_time_limit(0);

    // Close the session so that the users other tabs in the same session are not blocked.
    \core\session\manager::write_close();

    $format->set_filename($filename);
    // File creation for any data format is initiated by "send_http_headers()". This is required. But, this also will cause the
    // browser to respond with a "save / open" dialogue. To get rid of the dialogue, immediately retract the headers with
    // "header_remove()".
    $format->send_http_headers();
    header_remove();

    // Start capturing output to write to a file.
    ob_start();
    // This exists to support all dataformats - see MDL-56046.
    if (method_exists($format, 'write_header')) {
        debugging('The function write_header() does not support multiple sheets. In order to support multiple sheets you ' .
            'must implement start_output() and start_sheet() and remove write_header() in your dataformat.', DEBUG_DEVELOPER);
        $format->write_header($columns);
    } else {
        $format->start_output();
        $format->start_sheet($columns);
    }
    $c = 0;
    foreach ($iterator as $row) {
        if ($row === null) {
            continue;
        }
        $format->write_record($row, $c++);
    }
    // This exists to support all dataformats - see MDL-56046.
    if (method_exists($format, 'write_footer')) {
        debugging('The function write_footer() does not support multiple sheets. In order to support multiple sheets you ' .
            'must implement close_sheet() and close_output() and remove write_footer() in your dataformat.', DEBUG_DEVELOPER);
        $format->write_footer($columns);
    } else {
        $format->close_sheet($columns);
        $format->close_output();
        $output = ob_get_contents();
        $ext = $format->get_extension();
        $filepath = make_temp_directory('mod_questionnaire') . '/' . $filename . $ext;
        $fp = fopen($filepath, 'wb');
        fwrite($fp, $output);
        fclose($fp);
        $subjecttext = get_string('summaryreportattached', 'questionnaire');
        foreach ($users as $user) {
            email_to_user($user, $CFG->noreplyaddress, $subjecttext, $subjecttext, '', $filepath, $filename.$ext);
        }
        foreach ($emails as $email) {
            $email = trim($email);
            $user = (object)['id' => -10, 'email' => $email, 'firstname' => $email, 'lastname' => $email, 'mailformat' => 1];
            email_to_user($user, $CFG->noreplyaddress, $subjecttext, $subjecttext, '', $filepath, $filename.$ext);
        }
        unlink($filepath);
    }
    ob_end_clean();
    echo $OUTPUT->redirect_message($redirect, get_string('emailssent', 'questionnaire'), 3, false);
}
