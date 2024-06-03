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
 * Download course content confirmation and execution.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');

use core\content;
use core\content\export\zipwriter;

$contextid = required_param('contextid', PARAM_INT);
$isdownload = optional_param('download', 0, PARAM_BOOL);
$coursecontext = context::instance_by_id($contextid);
$courseid = $coursecontext->instanceid;
$courselink = new moodle_url('/course/view.php', ['id' => $courseid]);

if (!\core\content::can_export_context($coursecontext, $USER)) {
    redirect($courselink);
}

$PAGE->set_url('/course/downloadcontent.php', ['contextid' => $contextid]);
require_login($courseid);

$courseinfo = get_fast_modinfo($courseid)->get_course();
$filename = str_replace('/', '', str_replace(' ', '_', $courseinfo->shortname)) . '_' . time() . '.zip';

// If download confirmed, prepare and start the zipstream of the course download content.
if ($isdownload) {
    require_sesskey();

    $exportoptions = null;

    if (!empty($CFG->maxsizeperdownloadcoursefile)) {
        $exportoptions = new stdClass();
        $exportoptions->maxfilesize = $CFG->maxsizeperdownloadcoursefile;
    }

    // Use file writer in debug developer mode, so any errors can be displayed instead of being streamed into the output file.
    if (debugging('', DEBUG_DEVELOPER)) {
        $writer = zipwriter::get_file_writer($filename, $exportoptions);

        ob_start();
        content::export_context($coursecontext, $USER, $writer);
        $content = ob_get_clean();

        // If no errors found, output the file.
        if (empty($content)) {
            send_file($writer->get_file_path(), $filename);
            redirect($courselink);
        } else {
            // If any errors occurred, display them instead of outputting the file.
            debugging("Errors found while producing the download course content output:\n {$content}", DEBUG_DEVELOPER);
        }
    } else {
        // If not developer debugging, stream the output file directly.
        $writer = zipwriter::get_stream_writer($filename, $exportoptions);
        content::export_context($coursecontext, $USER, $writer);

        redirect($courselink);
    }

} else {
    $PAGE->set_title(get_string('downloadcoursecontent', 'course'));
    $PAGE->set_heading(format_string($courseinfo->fullname));

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('downloadcoursecontent', 'course'));

    // Prepare download confirmation information and display it.
    $maxfilesize = display_size($CFG->maxsizeperdownloadcoursefile, 0);
    $downloadlink = new moodle_url('/course/downloadcontent.php', ['contextid' => $contextid, 'download' => 1]);

    echo $OUTPUT->confirm(get_string('downloadcourseconfirmation', 'course', $maxfilesize), $downloadlink, $courselink);
}
