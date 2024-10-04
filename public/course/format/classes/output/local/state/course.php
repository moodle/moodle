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

namespace core_courseformat\output\local\state;

use core_courseformat\base as course_format;
use course_modinfo;
use moodle_url;
use renderable;
use stdClass;

/**
 * Contains the ajax update course structure.
 *
 * @package   core_course
 * @copyright 2021 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course implements renderable {

    /** @var course_format the course format class */
    protected $format;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     */
    public function __construct(course_format $format) {
        $this->format = $format;
    }

    /**
     * Export this data so it can be used as state object in the course editor.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {
        global $CFG;

        $format = $this->format;
        $course = $format->get_course();
        $context = $format->get_context();
        // State must represent always the most updated version of the course.
        $modinfo = course_modinfo::instance($course);

        $url = new moodle_url('/course/view.php', ['id' => $course->id]);
        $maxbytes = get_user_max_upload_file_size($context, $CFG->maxbytes, $course->maxbytes);

        $data = (object)[
            'id' => $course->id,
            'numsections' => $format->get_last_section_number(),
            'sectionlist' => [],
            'editmode' => $format->show_editor(),
            'highlighted' => $format->get_section_highlighted_name(),
            'baseurl' => $url->out(),
            'statekey' => course_format::session_cache($course),
            'maxbytes' => $maxbytes,
            'maxbytestext' => display_size($maxbytes),
        ];


        $sections = $modinfo->get_section_info_all();
        foreach ($sections as $section) {
            if ($format->is_section_visible($section)) {
                $data->sectionlist[] = $section->id;
            }
        }

        return $data;
    }
}
