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
 * Activity base class.
 *
 * @package   mod_feedback
 * @copyright 2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_feedback\analytics\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * Activity base class.
 *
 * @package   mod_feedback
 * @copyright 2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class activity_base extends \core_analytics\local\indicator\community_of_inquiry_activity {

    /**
     * @var int[] Tiny cache to hold feedback instance - publish_stats field relation.
     */
    protected $publishstats = array();

    /**
     * fill_publishstats
     *
     * @param \cm_info $cm
     * @return void
     */
    protected function fill_publishstats(\cm_info $cm) {
        global $DB;

        if (!isset($this->publishstats[$cm->instance])) {
            $this->publishstats[$cm->instance] = $DB->get_field('feedback', 'publish_stats', array('id' => $cm->instance));
        }
    }

    /**
     * Overwritten to mark as viewed if stats are published.
     *
     * @param \cm_info $cm
     * @param int $contextid
     * @param int $userid
     * @param int $after
     * @return bool
     */
    protected function feedback_viewed(\cm_info $cm, $contextid, $userid, $after = null) {
        // If stats are published any write action counts as viewed feedback.
        if (!empty($this->publishstats[$cm->instance])) {
            $user = (object)['id' => $userid];
            return $this->any_write_log($contextid, $user);
        }

        return parent::feedback_viewed($cm, $contextid, $userid, $after);
    }
}
