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
 * Cognitive depth indicator - lesson.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\indicator\lesson;

defined('MOODLE_INTERNAL') || die();

/**
 * Cognitive depth indicator - lesson.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cognitive_depth extends activity_base {

    protected function get_indicator_type() {
        return self::INDICATOR_COGNITIVE;
    }

    protected function get_cognitive_depth_level(\cm_info $cm) {
        return 5;
    }

    protected function feedback_submitted(\cm_info $cm, $contextid, $userid, $after = false) {
        if (empty($this->activitylogs[$contextid][$userid]) ||
                empty($this->activitylogs[$contextid][$userid]['\mod_lesson\event\lesson_ended'])) {
            return false;
        }

        // Multiple lesson attempts completed counts as submitted after feedback.
        return (2 >= count($this->activitylogs[$contextid][$userid]['\mod_lesson\event\lesson_ended']));
    }

    protected function feedback_replied(\cm_info $cm, $contextid, $userid, $after = false) {
        // No level 4.
        return false;
    }

}
