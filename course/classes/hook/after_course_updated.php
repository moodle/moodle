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

namespace core_course\hook;

use stdClass;

/**
 * Hook after course updates.
 *
 * @package    core_course
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allows plugins or features to perform actions after a course is updated.')]
#[\core\attribute\tags('course')]
class after_course_updated {
    /**
     * Constructor for the hook.
     *
     * @param stdClass $course The course instance.
     * @param stdClass $oldcourse The old course instance.
     * @param bool $changeincoursecat Whether the course category has changed.
     */
    public function __construct(
        /** @var stdClass The course instance */
        public readonly stdClass $course,
        /** @var stdClass The old course instance */
        public readonly stdClass $oldcourse,
        /** @var bool Whether the course category has changed */
        public readonly bool $changeincoursecat = false,
    ) {
    }
}
