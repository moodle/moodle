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
 * Cognitive depth indicator - feedback.
 *
 * @package   mod_feedback
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_feedback\analytics\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * Cognitive depth indicator - feedback.
 *
 * @package   mod_feedback
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cognitive_depth extends activity_base {

    /**
     * @var int[] Tiny cache to hold feedback instance - publish_stats field relation.
     */
    protected $publishstats = array();

    public static function get_name() {
        return get_string('indicator:cognitivedepthfeedback', 'mod_feedback');
    }

    protected function get_indicator_type() {
        return self::INDICATOR_COGNITIVE;
    }

    protected function get_cognitive_depth_level(\cm_info $cm) {
        global $DB;

        if (!isset($this->publishstats[$cm->instance])) {
            $this->publishstats[$cm->instance] = $DB->get_field('feedback', 'publish_stats', array('id' => $cm->instance));
        }

        if (!empty($this->publishstats[$cm->instance])) {
            // If stats are published we count that the user viewed feedback.
            return 3;
        }
        return 2;
    }

    protected function any_feedback_view(\cm_info $cm, $contextid, $user) {
        // If stats are published any write action counts as viewed feedback.
        return $this->any_write_log($contextid, $user);
    }
}
