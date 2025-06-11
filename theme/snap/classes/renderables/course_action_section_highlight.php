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

class course_action_section_highlight extends course_action_section_base {

    /**
     * @var string
     */
    public $class = 'snap-highlight';

    /**
     * @var string
     */
    public $ariapressed = 'aria-pressed="false"';

    /**
     * course_action_section_highlight constructor.
     * @param stdClass $course
     * @param stdClass $section - Note this is the section we want to affect via the url param.
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

        if ($course->format === 'topics') {
            if (has_capability('moodle/course:setcurrentsection', $coursecontext)) {
                $url = clone($baseurl);
                $marker = optional_param('marker', '', PARAM_INT);
                $marker = $marker === '' ? $course->marker : $marker;
                $this->title = get_string('highlight');
                $this->arialabel = "aria-label='".get_string('highlight')."'";
                // Note if the new target section is 0 then it means the requested action is to have no current section
                // highlighted.
                if ($marker == $section->section || $section->section === 0) {
                    // Show the lightbulb.
                    $url->param('marker', 0);
                    $this->url = $url;
                    $this->ariapressed = 'aria-pressed="true"';
                    $this->title = get_string('highlightoff');
                    $this->arialabel = "aria-label='".get_string('highlightoff')."'";
                } else {
                    $url->param('marker', $section->section);
                    $this->url = $url;
                }
            }
        }
    }
}
