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

namespace core_courseformat\local;

use core_courseformat\base as course_format;
use section_info;
use cm_info;
use stdClass;

/**
 * Format base actions.
 *
 * This class defined the format actions base class extended by the course, section and cm actions.
 *
 * It also provides helpers to get the most recent modinfo and format information. Those
 * convenience methods are meant to improve the actions readability and prevent excessive
 * message chains.
 *
 * @package    core_courseformat
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class baseactions {
    /**
     * @var stdClass the course object.
     */
    protected stdClass $course;

    /**
     * Constructor.
     * @param stdClass $course the course object.
     */
    public function __construct(stdClass $course) {
        $this->course = $course;
    }

    /**
     * Get the course.
     * @return stdClass the course object.
     */
    protected function get_course(): stdClass {
        return $this->course;
    }

    /**
     * Get the course format.
     * @return course_format the course format.
     */
    protected function get_format(): course_format {
        return course_format::instance($this->course);
    }

    /**
     * Get the section info.
     * @param int $sectionid the section id.
     * @param int $strictness Use MUST_EXIST to throw exception if it doesn't
     * @return section_info|null Information for numbered section or null if not found
     */
    protected function get_section_info($sectionid, int $strictness = IGNORE_MISSING): ?section_info {
        // Course actions must always get the most recent version of the section info.
        return get_fast_modinfo($this->course->id)->get_section_info_by_id($sectionid, $strictness);
    }

    /**
     * Get the cm info.
     * @param int $cmid the cm id.
     * @return cm_info|null Information for numbered cm or null if not found
     */
    protected function get_cm_info($cmid): ?cm_info {
        // Course actions must always get the most recent version of the cm info.
        return get_fast_modinfo($this->course->id)->get_cm($cmid);
    }
}
