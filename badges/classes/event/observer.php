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

namespace core_badges\event;

use core\event\badge_awarded;
use core\event\cohort_member_added;
use core\event\competency_evidence_created;
use core\event\course_completed;
use core\event\course_module_completion_updated;
use core\event\user_updated;
use core_badges\badge;

/**
 * Event observer for badges.
 *
 * @package    core_badges
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @author     Rajesh Taneja <rajesh@moodle.com>
 * @author     Dai Nguyen Trong <ngtrdai@hotmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {
    /**
     * Determines if badge processing is enabled.
     *
     * @return bool True if badges are enabled, false otherwise.
     */
    private static function badges_enabled(): bool {
        global $CFG;
        return !empty($CFG->enablebadges);
    }

    /**
     * Common method to process badge issue after criteria is met.
     *
     * @param badge $badge The badge instance
     * @param int $criteriatype The type of criteria being checked
     * @param int $userid The user ID to check criteria against
     */
    private static function process_criteria_completion(badge $badge, int $criteriatype, int $userid): void {
        if (!$badge->is_active() || $badge->is_issued($userid)) {
            return;
        }

        if ($badge->criteria[$criteriatype]->review($userid)) {
            $badge->criteria[$criteriatype]->mark_complete($userid);

            if ($badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->review($userid)) {
                $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->mark_complete($userid);
                $badge->issue($userid);
            }
        }
    }

    /**
     * Triggered when 'course_module_completion_updated' event is triggered.
     *
     * @param course_module_completion_updated $event
     */
    public static function course_module_criteria_review(course_module_completion_updated $event): void {
        global $DB, $CFG;

        if (!self::badges_enabled()) {
            return;
        }

        require_once($CFG->dirroot.'/lib/badgeslib.php');

        $eventdata = $event->get_record_snapshot('course_modules_completion', $event->objectid);
        $userid = $event->relateduserid;
        $mod = $event->contextinstanceid;

        // Only process completion states that indicate the module has been completed.
        if (!in_array($eventdata->completionstate, [
            COMPLETION_COMPLETE,
            COMPLETION_COMPLETE_PASS,
            COMPLETION_COMPLETE_FAIL,
        ])) {
            return;
        }

        // Find all badges that have this module in their criteria.
        $params = [
            'name' => 'module_' . $mod,
            'value' => $mod,
        ];

        if ($rs = $DB->get_records('badge_criteria_param', $params)) {
            foreach ($rs as $r) {
                $bid = $DB->get_field('badge_criteria', 'badgeid', ['id' => $r->critid], MUST_EXIST);
                $badge = new badge($bid);

                self::process_criteria_completion($badge, BADGE_CRITERIA_TYPE_ACTIVITY, $userid);
            }
        }
    }

    /**
     * Triggered when '\core\event\competency_evidence_created' event is triggered.
     *
     * @param competency_evidence_created $event
     */
    public static function competency_criteria_review(competency_evidence_created $event): void {
        global $DB, $CFG;

        if (!self::badges_enabled()) {
            return;
        }

        require_once($CFG->dirroot.'/lib/badgeslib.php');

        if (!get_config('core_competency', 'enabled')) {
            return;
        }

        $cid = $event->other['competencyid'];
        $userid = $event->relateduserid;
        $params = [
            'name' => 'competency_' . $cid,
            'value' => $cid,
        ];

        if ($rs = $DB->get_records('badge_criteria_param', $params)) {
            foreach ($rs as $r) {
                $crit = $DB->get_record('badge_criteria', ['id' => $r->critid], 'badgeid, criteriatype', MUST_EXIST);
                $badge = new badge($crit->badgeid);

                self::process_criteria_completion($badge, $crit->criteriatype, $userid);
            }
        }
    }

    /**
     * Triggered when 'course_completed' event is triggered.
     *
     * @param course_completed $event
     */
    public static function course_criteria_review(course_completed $event): void {
        global $DB, $CFG;

        if (!self::badges_enabled()) {
            return;
        }

        require_once($CFG->dirroot.'/lib/badgeslib.php');

        $userid = $event->relateduserid;
        $courseid = $event->courseid;
        $params = [
            'name' => 'course_' . $courseid,
            'value' => $courseid,
        ];

        if ($rs = $DB->get_records('badge_criteria_param', $params)) {
            foreach ($rs as $r) {
                $crit = $DB->get_record(
                    'badge_criteria',
                    ['id' => $r->critid],
                    'badgeid, criteriatype',
                    MUST_EXIST
                );
                $badge = new badge($crit->badgeid);

                self::process_criteria_completion($badge, $crit->criteriatype, $userid);
            }
        }
    }

    /**
     * Triggered when 'badge_awarded' event happens.
     *
     * @param badge_awarded $event event generated when a badge is awarded.
     */
    public static function badge_criteria_review(badge_awarded $event): void {
        global $DB, $CFG;

        if (!self::badges_enabled()) {
            return;
        }

        require_once($CFG->dirroot.'/lib/badgeslib.php');

        $userid = $event->relateduserid;

        if ($rs = $DB->get_records('badge_criteria', ['criteriatype' => BADGE_CRITERIA_TYPE_BADGE])) {
            foreach ($rs as $r) {
                $badge = new badge($r->badgeid);
                self::process_criteria_completion($badge, BADGE_CRITERIA_TYPE_BADGE, $userid);
            }
        }
    }

    /**
     * Triggered when 'user_updated' event happens.
     *
     * @param user_updated $event event generated when user profile is updated.
     */
    public static function profile_criteria_review(user_updated $event): void {
        global $DB, $CFG;

        if (!self::badges_enabled()) {
            return;
        }

        require_once($CFG->dirroot . '/lib/badgeslib.php');
        $userid = $event->objectid;

        if ($rs = $DB->get_records('badge_criteria', ['criteriatype' => BADGE_CRITERIA_TYPE_PROFILE])) {
            foreach ($rs as $r) {
                $badge = new badge($r->badgeid);
                self::process_criteria_completion($badge, BADGE_CRITERIA_TYPE_PROFILE, $userid);
            }
        }
    }

    /**
     * Triggered when the 'cohort_member_added' event happens.
     *
     * @param cohort_member_added $event generated when a user is added to a cohort
     */
    public static function cohort_criteria_review(cohort_member_added $event): void {
        global $DB, $CFG;

        if (!self::badges_enabled()) {
            return;
        }

        require_once($CFG->dirroot . '/lib/badgeslib.php');

        $cohortid = $event->objectid;
        $userid = $event->relateduserid;

        // Get relevant badges.
        $badgesql = "SELECT badgeid
                       FROM {badge_criteria_param} cp
                       JOIN {badge_criteria} c ON cp.critid = c.id
                      WHERE c.criteriatype = ?
                            AND cp.name = ?";

        $badges = $DB->get_records_sql(
            $badgesql,
            [BADGE_CRITERIA_TYPE_COHORT, "cohort_{$cohortid}"],
        );

        if (empty($badges)) {
            return;
        }

        foreach ($badges as $b) {
            $badge = new badge($b->badgeid);
            self::process_criteria_completion($badge, BADGE_CRITERIA_TYPE_COHORT, $userid);
        }
    }
}
