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
 * @package dataformview
 * @subpackage aligned
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// PLUGIN FUNCTIONS WHICH ARE CALLED FROM OUTSIDE THE PLUGIN.

defined('MOODLE_INTERNAL') or die;

/**
 * Serves the dataformview_aligned template files.
 *
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool false if file not found, does not return if found - justsend the file
 */
function dataformview_aligned_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    if (!in_array($filearea, dataformview_aligned_aligned::get_file_areas())) {
        return false;
    }

    if ($context->contextlevel == CONTEXT_MODULE) {
        require_course_login($course, true, $cm);

        $viewid = (int) array_shift($args);
        $dataformid = $cm->instance;;

        // Confirm user access.
        $params = array('dataformid' => $dataformid, 'viewid' => $viewid);
        if (!mod_dataform\access\view_access::validate($params)) {
            return false;
        }

        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/dataformview_aligned/$filearea/$viewid/$relativepath";

        $fs = get_file_storage();
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }

        // Finally send the file.
        send_stored_file($file, 0, 0, true); // Download MUST be forced - security!
    }
    return false;
}
