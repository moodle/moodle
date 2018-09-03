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
 * @package   mod_chat
 * @copyright 2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_chat\analytics\indicator;

defined('MOODLE_INTERNAL') || die();

/**
 * Activity base class.
 *
 * @package   mod_chat
 * @copyright 2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class activity_base extends \core_analytics\local\indicator\community_of_inquiry_activity {

    /**
     * feedback_viewed_events
     *
     * @return string[]
     */
    protected function feedback_viewed_events() {
        return array('\mod_chat\event\course_module_viewed', '\mod_chat\event\message_sent',
            '\mod_chat\event\sessions_viewed');
    }

    /**
     * feedback_replied_events
     *
     * @return string[]
     */
    protected function feedback_replied_events() {
        return array('\mod_chat\event\message_sent');
    }

    /**
     * feedback_post_action
     *
     * @param \cm_info $cm
     * @param int $contextid
     * @param int $userid
     * @param string[] $eventnames
     * @param int $after
     * @return bool
     */
    protected function feedback_post_action(\cm_info $cm, $contextid, $userid, $eventnames, $after = false) {

        if (empty($this->activitylogs[$contextid][$userid])) {
            return false;
        }

        $logs = $this->activitylogs[$contextid][$userid];

        if (empty($logs['\mod_chat\event\message_sent'])) {
            // No feedback viewed if there is no submission.
            return false;
        }

        // First user message time.
        $firstmessage = $logs['\mod_chat\event\message_sent']->timecreated[0];

        // We consider feedback another user messages.
        foreach ($this->activitylogs[$contextid] as $anotheruserid => $logs) {
            if ($anotheruserid == $userid) {
                continue;
            }
            if (empty($logs['\mod_chat\event\message_sent'])) {
                continue;
            }
            $firstmessagesenttime = $logs['\mod_chat\event\message_sent']->timecreated[0];

            if (parent::feedback_post_action($cm, $contextid, $userid, $eventnames, $firstmessagesenttime)) {
                return true;
            }
            // Continue with the next user.
        }

        return false;
    }

    /**
     * feedback_check_grades
     *
     * @return bool
     */
    protected function feedback_check_grades() {
        // Chat's feedback is not contained in grades.
        return false;
    }
}
