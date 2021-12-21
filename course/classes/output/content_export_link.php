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
 * Prepares content for buttons/links to course content export/download.
 *
 * @package   core_course
 * @copyright 2020 Michael Hawkins <michaelh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\output;

/**
 * Prepares content for buttons/links to course content export/download.
 *
 * @package   core_course
 * @copyright 2020 Michael Hawkins <michaelh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_export_link {

    /**
     * Prepare and return the various attributes required for a link/button to populate/trigger the download course content modal.
     *
     * @param \context $context The context of the content being exported.
     * @return stdClass
     */
    public static function get_attributes(\context $context): \stdClass {
        global $CFG;
        $downloadattr = new \stdClass();
        $downloadattr->url = new \moodle_url('/course/downloadcontent.php', ['contextid' => $context->id]);
        $downloadattr->displaystring = get_string('downloadcoursecontent', 'course');
        $maxfilesize = display_size($CFG->maxsizeperdownloadcoursefile);
        $downloadlink = new \moodle_url('/course/downloadcontent.php', ['contextid' => $context->id, 'download' => 1]);

        $downloadattr->elementattributes = [
            'data-downloadcourse' => 1,
            'data-download-body' => get_string('downloadcourseconfirmation', 'course', $maxfilesize),
            'data-download-button-text' => get_string('download'),
            'data-download-link' => $downloadlink->out(false),
            'data-download-title' => get_string('downloadcoursecontent', 'course'),
            'data-overrides-tree-activation-key-handler' => 1,
        ];

        return $downloadattr;
    }
}
