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
 * Course action for showing section permalink.
 * @author    Bryan Cruz
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\renderables;
use moodle_url;
use section_info;
use context_course;

class course_action_section_permalink extends course_action_section_base {

    /**
     * @var string
     */
    public $class = 'snap-permalink';

    public function __construct($course, section_info $section, $onsectionpage = false) {
        $coursecontext = context_course::instance($course->id);
        if (
            has_any_capability([
                'moodle/course:movesections',
                'moodle/course:update',
                'moodle/course:sectionvisibility',
            ], $coursecontext)
        ) {
            $this->url = new moodle_url(
                '/course/view.php',
                ['id' => $course->id],
                "sectionid-{$section->id}-title"
            );
            $this->arialabel = "aria-label='".get_string('sectionlink', 'course')."'";
            $this->title = get_string('sectionlink', 'course');
        }
    }
}
