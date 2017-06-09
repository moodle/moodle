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
 * One week before the course start.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\time_splitting;

defined('MOODLE_INTERNAL') || die();

class week_before_course_start extends base {

    public function get_name() {
        return get_string('timesplitting:weekbeforecoursestart', 'analytics');
    }

    public function is_valid_analysable(\core_analytics\analysable $analysable) {
        if ($analysable instanceof \core_analytics\site) {
            // Defer checking to is_valid_sample.
            return true;
        }
        if ($analysable instanceof \core_analytics\course && !$analysable->get_start()) {
            return get_string('nocoursestart', 'analytics');
        }

        // Default to true.
        return true;
    }

    protected function define_ranges() {
        return [
            [
                'start' => 0,
                'end' => \core_analytics\analysable::MAX_TIME,
                'time' => $this->analysable->get_start() - WEEKSECS
            ]
        ];
    }
}
