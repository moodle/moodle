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
use section_info;
use renderable;
use stdClass;
use context_course;

/**
 * Contains the ajax update section structure.
 *
 * @package   core_course
 * @copyright 2021 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class section implements renderable {

    /** @var course_format the course format class */
    protected $format;

    /** @var section_info the course section class */
    protected $section;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     */
    public function __construct(course_format $format, section_info $section) {
        $this->format = $format;
        $this->section = $section;
    }

    /**
     * Export this data so it can be used as state object in the course editor.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {
        global $CFG;

        $format = $this->format;
        $course = $format->get_course();
        $section = $this->section;
        $modinfo = $format->get_modinfo();

        $indexcollapsed = false;
        $contentcollapsed = false;
        $preferences = $format->get_sections_preferences();
        if (isset($preferences[$section->id])) {
            $sectionpreferences = $preferences[$section->id];
            if (!empty($sectionpreferences->contentcollapsed)) {
                $contentcollapsed = true;
            }
            if (!empty($sectionpreferences->indexcollapsed)) {
                $indexcollapsed = true;
            }
        }

        $data = (object)[
            'id' => $section->id,
            'section' => $section->section,
            'number' => $section->section,
            'title' => $format->get_section_name($section),
            'hassummary' => !empty($section->summary),
            'rawtitle' => $section->name,
            'cmlist' => [],
            'visible' => !empty($section->visible),
            'sectionurl' => course_get_url($course, $section->section)->out(),
            'current' => $format->is_section_current($section),
            'indexcollapsed' => $indexcollapsed,
            'contentcollapsed' => $contentcollapsed
        ];

        // If the section availability restrictions must be displayed.
        $canviewhidden = has_capability(
            'moodle/course:viewhiddenactivities',
            context_course::instance($course->id)
        );
        if (!empty($CFG->enableavailability) && $canviewhidden) {
            $data->hasrestrictions = !empty($section->availableinfo);
        } else {
            $data->hasrestrictions = false;
        }

        if (empty($modinfo->sections[$section->section])) {
            return $data;
        }

        foreach ($modinfo->sections[$section->section] as $modnumber) {
            $mod = $modinfo->cms[$modnumber];
            if ($mod->is_visible_on_course_page()) {
                $data->cmlist[] = $mod->id;
            }
        }

        return $data;
    }
}
