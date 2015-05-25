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
 * Local stuff for category enrolment plugin.
 *
 * @package    core_badges
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function do_review_badges_for_user($userid)
{
    global $CFG;

    if (!empty($CFG->enablebadges)) {
        require_once($CFG->dirroot.'/lib/badgeslib.php');

        badges_review_user($userid);
    }
}

/**
 * Event observer for badges.
 */
class core_badges_observer {
    /**
     * Triggered when 'course_module_completion_updated' event is triggered.
     *
     * @param \core\event\course_module_completion_updated $event
     */
    public static function course_module_criteria_review(\core\event\course_module_completion_updated $event) {
        do_review_badges_for_user($event->other['relateduserid']);
    }

    /**
     * Triggered when 'course_completed' event is triggered.
     *
     * @param \core\event\course_completed $event
     */
    public static function course_criteria_review(\core\event\course_completed $event) {
        do_review_badges_for_user($event->other['relateduserid']);
    }

    /**
     * Triggered when 'user_updated' event happens.
     *
     * @param \core\event\user_updated $event event generated when user profile is updated.
     */
    public static function profile_criteria_review(\core\event\user_updated $event) {
        do_review_badges_for_user($event->objectid);
    }

    /**
     * Triggered when 'user_created' event happens.
     *
     * @param \core\event\user_created $event event generated when user is created.
     */
    public static function profile_criteria_review2(\core\event\user_created $event) {
        do_review_badges_for_user($event->objectid);
    }
}
