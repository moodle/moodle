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

class course_action_section_move extends course_action_section_base {

    /**
     * @var string
     */
    public $class = 'snap-move';

    public function __construct($course, section_info $section, $onsectionpage = false) {

        $coursecontext = context_course::instance($course->id);

        if ($onsectionpage && has_capability('moodle/course:movesections', $coursecontext)) {
            $this->url = '#section-'.$section->section;
            $sectionname = !empty($section->name) ? $section->name : get_section_name($course, $section);
            $this->title = s(get_string('move', 'theme_snap', $sectionname));
            $this->arialabel = "aria-label='".s(get_string('move', 'theme_snap', $sectionname))."'";
        }
    }
}
