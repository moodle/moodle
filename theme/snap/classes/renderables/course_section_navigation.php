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
 * Renderable for course section navigation.
 * @package   theme_snap
 * @author    Guy Thomas
 * @copyright Copyright (c) 2016 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\renderables;
use context_course;

/**
 * Renderable class for course section navigation.
 * @package   theme_snap
 * @author    Guy Thomas
 * @copyright Copyright (c) 2016 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_section_navigation implements \renderable {

    /**
     * @var false|course_section_navigation_link previous section link
     */
    public $previous;

    /**
     * @var false|course_section_navigation_link next section link
     */
    public $next;

    /**
     * course_section_navigation constructor.
     * @param stdClass $course
     * @param section_info[] $sections
     * @param int $sectionno
     */
    public function __construct($course, $sections, $sectionno) {
        $course = course_get_format($course)->get_course();

        $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($course->id))
        || !$course->hiddensections;

        $this->previous = false;
        $target = $sectionno - 1;
        while ($target >= 0 && empty($this->previous)) {
            $extraclasses = '';
            if ($canviewhidden
                || $sections[$target]->uservisible
                || $sections[$target]->availableinfo) {
                if (!$sections[$target]->visible) {
                    $extraclasses = ' dimmed_text';
                }

                $sectiontitle = get_section_name($course, $sections[$target]);
                // Better first section title.
                if ($sectiontitle === get_string('general')) {
                    $sectiontitle = get_string('introduction', 'theme_snap');
                }

                $this->previous = new course_section_navigation_link($target, $extraclasses, $sectiontitle);
            }
            $target--;
        }

        $this->next = false;
        $target = $sectionno + 1;
        $lastsectionno = course_get_format($course)->get_last_section_number();
        while ($target <= $lastsectionno && empty($this->next)) {
            $extraclasses = '';
            if ($canviewhidden
                || $sections[$target]->uservisible
                || $sections[$target]->availableinfo) {
                if (!$sections[$target]->visible) {
                    $extraclasses = ' dimmed_text';
                }
                $sectiontitle = get_section_name($course, $sections[$target]);
                // Better first section title.
                if ($sectiontitle == get_string('general')) {
                    $sectiontitle = get_string('introduction', 'theme_snap');
                }

                $this->next = new course_section_navigation_link($target, $extraclasses, $sectiontitle);
            }
            $target++;
        }
    }
}
