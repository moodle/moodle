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
 * Cognitive depth indicator - choice.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\indicator\choice;

defined('MOODLE_INTERNAL') || die();

/**
 * Cognitive depth indicator - choice.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cognitive_depth extends activity_base {

    protected $choicedata = array();

    protected function get_indicator_type() {
        return self::INDICATOR_COGNITIVE;
    }

    protected function get_cognitive_depth_level(\cm_info $cm) {
        global $DB;

        if (!isset($this->choicedata[$cm->instance])) {
            $this->choicedata[$cm->instance] = $DB->get_record('choice', array('id' => $cm->instance), 'id, showresults, timeclose', MUST_EXIST);
        }

        if ($this->choicedata[$cm->instance]->showresults == 0 || $this->choicedata[$cm->instance]->showresults == 4) {
            // Results are not shown to students or are always shown.
            return 2;
        }

        return 3;
    }

    protected function any_feedback_view(\cm_info $cm, $contextid, $user) {

        // If results are shown after they answer a write action counts as feedback viewed.
        if ($this->choicedata[$cm->instance]->showresults == 1) {
            return $this->any_write_log($contextid, $user);
        }

        if (empty($this->activitylogs[$contextid])) {
            return false;
        }

        // Define the iteration, over all users if $user is set or a specific user.
        $it = $this->activitylogs[$contextid];
        if ($user) {
            if (empty($this->activitylogs[$contextid][$user->id])) {
                return false;
            }
            $it = array($user->id => $this->activitylogs[$contextid][$user->id]);
        }

        // Now we look for any log after the choice time close so we can confirm that the results were viewed.
        foreach ($it as $userid => $logs) {
            foreach ($logs as $log) {
                foreach ($log->timecreated as $timecreated) {
                    if ($timecreated >= $this->choicedata[$cm->instance]->timeclose) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
