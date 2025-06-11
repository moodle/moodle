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
 * Course action for affecting section visibility.
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\renderables;
use context_course;
use section_info;

class course_action_section_visibility extends course_action_section_base {

    /**
     * @var string
     */
    public $class = 'snap-visibility';

    public function __construct($course, section_info $section, $onsectionpage = false) {

        if ($onsectionpage) {
            $baseurl = course_get_url($course, $section->section);
        } else {
            $baseurl = course_get_url($course);
        }
        $baseurl->param('sesskey', sesskey());

        $coursecontext = context_course::instance($course->id);

        $url = clone($baseurl);
        if (has_capability('moodle/course:sectionvisibility', $coursecontext)) {
            if ($section->visible) { // Show the hide/show eye.
                $this->title = get_string('hidefromothers', 'format_'.$course->format);
                $url->param('hide', $section->section);
                $this->url = $url;
                $this->class .= ' snap-hide';
                $this->arialabel = "aria-label='".get_string('hidefromothers', 'format_'.$course->format)."'";
            } else {
                $this->title = get_string('showfromothers', 'format_'.$course->format);
                $url->param('show',  $section->section);
                $this->url = $url;
                $this->class .= ' snap-show';
                $this->arialabel = "aria-label='".get_string('showfromothers', 'format_'.$course->format)."'";
            }
        }
    }
}
