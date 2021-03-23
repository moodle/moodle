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

namespace core_course\output\cm_format;

use core_course\course_format;
use section_info;
use cm_info;
use renderable;
use stdClass;

/**
 * Contains the ajax update course module structure.
 *
 * @package   core_course
 * @copyright 2021 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class state implements renderable {

    /** @var course_format the course format class */
    protected $format;

    /** @var section_info the course section class */
    protected $section;

    /** @var bool if cmitem HTML content must be exported as well */
    protected $exportcontent;

    /** @var cm_info the course module to display */
    protected $cm;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section data
     * @param cm_info $cm the course module data
     * @param bool $exportcontent = false if pre-rendered cmitem must be exported.
     */
    public function __construct(course_format $format, section_info $section, cm_info $cm, bool $exportcontent = false) {
        $this->format = $format;
        $this->section = $section;
        $this->cm = $cm;
        $this->exportcontent = $exportcontent;
    }

    /**
     * Export this data so it can be used as state object in the course editor.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {

        $format = $this->format;
        $section = $this->section;
        $cm = $this->cm;

        $data = (object)[
            'id' => $cm->id,
            'name' => $cm->name,
            'visible' => !empty($cm->visible),
        ];

        if ($this->exportcontent) {
            $data->content = $output->course_section_updated_cm_item($format, $section, $cm);
        }

        return $data;
    }
}
