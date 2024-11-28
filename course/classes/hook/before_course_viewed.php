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
 * Contains class before_course_viewed responsible for external course routing
 *
 * @package    core_course
 * @copyright  2024 Jacob Viertel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\hook;

use stdClass;
use core\hook\described_hook;

/**
 * External course redirect hook before_course_viewed
 *
 */
final class before_course_viewed implements described_hook {
    /**
     * The course being viewed
     * @var stdClass
     */
    public $course;

    /**
     * Constructor for the hook.
     * @param stdClass $course The course instance.
     */
    public function __construct(stdClass $course) {
        $this->course = $course;
    }

    /**
     * Hooks description
     *
     * @return string
     */
    public static function get_hook_description(): string {
        return 'Hook dispatched just before viewing a course in course/view.php.';
    }

    /**
     * Hooks tags
     *
     * @return array
     */
    public static function get_hook_tags(): array {
        return ['course', 'view', 'routing', 'navigation'];
    }
}
