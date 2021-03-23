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

namespace core_course\output\course_format;

use core_course\course_format;
use renderable;
use stdClass;

/**
 * Contains the ajax update course structure.
 *
 * @package   core_course
 * @copyright 2021 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class state implements renderable {

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
        $format = $this->format;
        $course = $format->get_course();
        $modinfo = $this->format->get_modinfo();

        $data = (object)[
            'id' => $course->id,
            'numsections' => $format->get_last_section_number(),
            'sectionlist' => [],
            'editmode' => $format->show_editor(),
        ];

        $sections = $modinfo->get_section_info_all();
        foreach ($sections as $section) {
            if (!empty($section->uservisible)) {
                $data->sectionlist[] = $section->id;
            }
        }

        return $data;
    }
}
