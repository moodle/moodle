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
 * Abstract analyser in course basis.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\analyser;

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract analyser in course basis.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class by_course extends base {

    /**
     * Return the list of courses to analyse.
     *
     * @return \core_analytics\course[]
     */
    public function get_analysables() {

        // Default to all system courses.
        if (!empty($this->options['filter'])) {
            $courses = array();
            foreach ($this->options['filter'] as $courseid) {
                $courses[$courseid] = new \stdClass();
                $courses[$courseid]->id = $courseid;
            }
        } else {
            // Iterate through all potentially valid courses.
            $courses = get_courses('all', 'c.sortorder ASC', 'c.id');
        }
        unset($courses[SITEID]);

        $analysables = array();
        foreach ($courses as $course) {
            // Skip the frontpage course.
            $analysable = \core_analytics\course::instance($course->id);
            $analysables[$analysable->get_id()] = $analysable;
        }

        if (empty($analysables)) {
            $this->log[] = get_string('nocourses', 'analytics');
        }

        return $analysables;
    }
}
