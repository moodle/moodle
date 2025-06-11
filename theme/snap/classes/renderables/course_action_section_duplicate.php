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

namespace theme_snap\renderables;
use context_course;
use section_info;

/**
 * Class course_action_section_duplicate
 *
 * @package    theme_snap
 * @copyright  2024 Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_action_section_duplicate extends course_action_section_base {

    /**
     * @var string
     */
    public $class = 'snap-duplicate';

    /**
     * course_action_section_duplicate constructor.
     * @param \stdClass $course
     * @param section_info $section
     * @param bool $onsectionpage 
     */
    public function __construct($course, section_info $section, $onsectionpage = false) {
        if ($onsectionpage) {
            $baseurl = course_get_url($course, $section->section);
        } else {
            $baseurl = course_get_url($course);
        }
        $baseurl->param('sesskey', sesskey());
        $coursecontext = context_course::instance($course->id);
        if (has_capability('moodle/course:update', $coursecontext)) {
            $duplicatesectionurl = clone($baseurl);
            $duplicatesectionurl->param('section', $section->section);
            $duplicatesectionurl->param('duplicatesection', $section->section);
            $this->title = get_string('duplicate');
            $this->arialabel = "aria-label='".get_string('duplicate')."'";
            $this->url = $duplicatesectionurl;
        }
    }
}