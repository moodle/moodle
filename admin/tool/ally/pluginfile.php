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
 * Handles simplified download links for files in questions.
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_ally\local_file;

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/filelib.php');

$pathnamehash = required_param('pathnamehash', PARAM_ALPHANUM);

$fs = get_file_storage();
$file = $fs->get_file_by_hash($pathnamehash);
if (!$file) {
    throw new moodle_exception('filenotfound', 'error');
}
if ($file->get_component() !== 'question') {
    throw new moodle_exception('error:pluginfilequestiononly', 'tool_ally');
}
$coursecontext = local_file::course_context($file);
$cm = local_file::resolve_cm_from_file($file);
$cm = $cm ?: false;

require_login($coursecontext->instanceid, true, $cm);
require_capability('moodle/question:editall', $coursecontext);

send_stored_file($file, 0, 0, true);
