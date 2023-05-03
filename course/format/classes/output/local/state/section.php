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

use core_availability\info_section;
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
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {
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
            'contentcollapsed' => $contentcollapsed,
            'hasrestrictions' => $this->get_has_restrictions(),
            'bulkeditable' => $this->is_bulk_editable(),
        ];

        if (empty($modinfo->sections[$section->section])) {
            return $data;
        }

        foreach ($modinfo->sections[$section->section] as $modnumber) {
            $mod = $modinfo->cms[$modnumber];
            if ($section->uservisible && $mod->is_visible_on_course_page()) {
                $data->cmlist[] = $mod->id;
            }
        }

        return $data;
    }

    /**
     * Return if the section can be selected for bulk editing.
     * @return bool if the section can be edited in bulk
     */
    protected function is_bulk_editable(): bool {
        $section = $this->section;
        return ($section->section != 0);
    }

    /**
     * Return if the section has a restrictions icon displayed or not.
     *
     * @return bool if the section has visible restrictions for the user.
     */
    protected function get_has_restrictions(): bool {
        global $CFG;

        $section = $this->section;
        $course = $this->format->get_course();
        $context = context_course::instance($course->id);

        // Hidden sections have no restriction indicator displayed.
        if (empty($section->visible) || empty($CFG->enableavailability)) {
            return false;
        }
        // The activity is not visible to the user but it may have some availability information.
        if (!$section->uservisible) {
            return !empty($section->availableinfo);
        }
        // Course editors can see all restrictions if the section is visible.
        if (has_capability('moodle/course:viewhiddensections', $context)) {
            $ci = new info_section($section);
            return !empty($ci->get_full_information());
        }
        // Regular users can only see restrictions if apply to them.
        return false;
    }
}
