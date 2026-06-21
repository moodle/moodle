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
 * This file contains the moodle hooks for the comments feedback plugin
 *
 * @package   assignfeedback_comments
 * @copyright 2018 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Serves assignment comment feedback files.
 *
 * @param mixed $course course or id of the course
 * @param mixed $cm course module or id of the course module
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options - List of options affecting file serving.
 * @return bool false if file not found, does not return if found - just send the file
 */
function assignfeedback_comments_pluginfile(
        $course,
        $cm,
        context $context,
        $filearea,
        $args,
        $forcedownload,
        array $options = []) {
    global $CFG, $DB;

    require_once($CFG->dirroot . '/mod/assign/locallib.php');
    require_once($CFG->dirroot . '/mod/assign/feedback/comments/locallib.php');

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, false, $cm);
    $itemid = (int)array_shift($args);
    // If the filearea is for a marker comment, the itemid will refer to a mark, not a grade.
    if ($filearea === ASSIGNFEEDBACK_COMMENTS_FILEAREA_MARKER) {
        $record = $DB->get_record('assign_mark', ['id' => $itemid], 'gradeid, assignment', MUST_EXIST);
        $graderecord = $DB->get_record('assign_grades', ['id' => $record->gradeid], 'userid', MUST_EXIST);
        $userid = $graderecord->userid;
    } else {
        $record = $DB->get_record('assign_grades', ['id' => $itemid], 'userid, assignment', MUST_EXIST);
        $userid = $record->userid;
    }

    $assign = new assign($context, $cm, $course);
    $instance = $assign->get_instance();

    if ($instance->id != $record->assignment) {
        return false;
    }

    if (!$assign->can_view_submission($userid)) {
        return false;
    }

    $relativepath = implode('/', $args);

    $fullpath = "/{$context->id}/assignfeedback_comments/$filearea/$itemid/$relativepath";

    $fs = get_file_storage();

    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }

    // Download MUST be forced - security!
    send_stored_file($file, 0, 0, true, $options);
}
