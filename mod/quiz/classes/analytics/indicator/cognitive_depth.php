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
 * Cognitive depth indicator - quiz.
 *
 * @package   mod_quiz
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quiz\analytics\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * Cognitive depth indicator - quiz.
 *
 * @package   mod_quiz
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cognitive_depth extends activity_base {

    /**
     * get_name
     *
     * @return string
     */
    public static function get_name() {
        return get_string('indicator:cognitivedepthquiz', 'mod_quiz');
    }

    /**
     * get_indicator_type
     *
     * @return string
     */
    protected function get_indicator_type() {
        return self::INDICATOR_COGNITIVE;
    }

    /**
     * get_cognitive_depth_level
     *
     * @param \cm_info $cm
     * @return int
     */
    public function get_cognitive_depth_level(\cm_info $cm) {
        return 5;
    }

    /**
     * feedback_submitted_events
     *
     * @return string[]
     */
    protected function feedback_submitted_events() {
        return array('\mod_quiz\event\attempt_submitted');
    }

    /**
     * feedback_replied
     *
     * @param \cm_info $cm
     * @param int $contextid
     * @param int $userid
     * @param int $after
     * @return bool
     */
    protected function feedback_replied(\cm_info $cm, $contextid, $userid, $after = false) {
        // No level 4.
        return false;
    }
}
