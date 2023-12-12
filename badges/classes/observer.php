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

defined('MOODLE_INTERNAL') || die();

use \core_badges\badge;

/**
 * Event observer for badges.
 *
 * @package    core_badges
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_badges_observer {
    /**
     * Triggered when 'course_module_completion_updated' event is triggered.
     *
     * @param \core\event\course_module_completion_updated $event
     */
    public static function course_module_criteria_review(\core\event\course_module_completion_updated $event) {
        global $DB, $CFG;

        if (!empty($CFG->enablebadges)) {
            require_once($CFG->dirroot.'/lib/badgeslib.php');

            $eventdata = $event->get_record_snapshot('course_modules_completion', $event->objectid);
            $userid = $event->relateduserid;
            $mod = $event->contextinstanceid;

            if ($eventdata->completionstate == COMPLETION_COMPLETE
                || $eventdata->completionstate == COMPLETION_COMPLETE_PASS
                || $eventdata->completionstate == COMPLETION_COMPLETE_FAIL) {
                // Need to take into account that there can be more than one badge with the same activity in its criteria.
                if ($rs = $DB->get_records('badge_criteria_param', array('name' => 'module_' . $mod, 'value' => $mod))) {
                    foreach ($rs as $r) {
                        $bid = $DB->get_field('badge_criteria', 'badgeid', array('id' => $r->critid), MUST_EXIST);
                        $badge = new badge($bid);
                        if (!$badge->is_active() || $badge->is_issued($userid)) {
                            continue;
                        }

                        if ($badge->criteria[BADGE_CRITERIA_TYPE_ACTIVITY]->review($userid)) {
                            $badge->criteria[BADGE_CRITERIA_TYPE_ACTIVITY]->mark_complete($userid);

                            if ($badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->review($userid)) {
                                $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->mark_complete($userid);
                                $badge->issue($userid);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Triggered when '\core\event\competency_evidence_created' event is triggered.
     *
     * @param \core\event\competency_evidence_created $event
     */
    public static function competency_criteria_review(\core\event\competency_evidence_created $event) {
        global $DB, $CFG;

        if (!empty($CFG->enablebadges)) {
            require_once($CFG->dirroot.'/lib/badgeslib.php');

            if (!get_config('core_competency', 'enabled')) {
                return;
            }

            $ucid = $event->other['usercompetencyid'];
            $cid = $event->other['competencyid'];
            $userid = $event->relateduserid;

            if ($rs = $DB->get_records('badge_criteria_param', array('name' => 'competency_' . $cid, 'value' => $cid))) {
                foreach ($rs as $r) {
                    $crit = $DB->get_record('badge_criteria', array('id' => $r->critid), 'badgeid, criteriatype', MUST_EXIST);
                    $badge = new badge($crit->badgeid);
                    // Only site badges are updated from site competencies.
                    if (!$badge->is_active() || $badge->is_issued($userid)) {
                        continue;
                    }

                    if ($badge->criteria[$crit->criteriatype]->review($userid)) {
                        $badge->criteria[$crit->criteriatype]->mark_complete($userid);

                        if ($badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->review($userid)) {
                            $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->mark_complete($userid);
                            $badge->issue($userid);
                        }
                    }
                }
            }
        }
    }

    /**
     * Triggered when 'course_completed' event is triggered.
     *
     * @param \core\event\course_completed $event
     */
    public static function course_criteria_review(\core\event\course_completed $event) {
        global $DB, $CFG;

        if (!empty($CFG->enablebadges)) {
            require_once($CFG->dirroot.'/lib/badgeslib.php');

            $userid = $event->relateduserid;
            $courseid = $event->courseid;

            // Need to take into account that course can be a part of course_completion and courseset_completion criteria.
            if ($rs = $DB->get_records('badge_criteria_param', array('name' => 'course_' . $courseid, 'value' => $courseid))) {
                foreach ($rs as $r) {
                    $crit = $DB->get_record('badge_criteria', array('id' => $r->critid), 'badgeid, criteriatype', MUST_EXIST);
                    $badge = new badge($crit->badgeid);
                    if (!$badge->is_active() || $badge->is_issued($userid)) {
                        continue;
                    }

                    if ($badge->criteria[$crit->criteriatype]->review($userid)) {
                        $badge->criteria[$crit->criteriatype]->mark_complete($userid);

                        if ($badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->review($userid)) {
                            $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->mark_complete($userid);
                            $badge->issue($userid);
                        }
                    }
                }
            }
        }
    }

    /**
     * Triggered when 'badge_awarded' event happens.
     *
     * @param \core\event\badge_awarded $event event generated when a badge is awarded.
     */
    public static function badge_criteria_review(\core\event\badge_awarded $event) {
        global $DB, $CFG;

        if (!empty($CFG->enablebadges)) {
            require_once($CFG->dirroot.'/lib/badgeslib.php');
            $userid = $event->relateduserid;

            if ($rs = $DB->get_records('badge_criteria', array('criteriatype' => BADGE_CRITERIA_TYPE_BADGE))) {
                foreach ($rs as $r) {
                    $badge = new badge($r->badgeid);
                    if (!$badge->is_active() || $badge->is_issued($userid)) {
                        continue;
                    }

                    if ($badge->criteria[BADGE_CRITERIA_TYPE_BADGE]->review($userid)) {
                        $badge->criteria[BADGE_CRITERIA_TYPE_BADGE]->mark_complete($userid);

                        if ($badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->review($userid)) {
                            $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->mark_complete($userid);
                            $badge->issue($userid);
                        }
                    }
                }
            }
        }
    }
    /**
     * Triggered when 'user_updated' event happens.
     *
     * @param \core\event\user_updated $event event generated when user profile is updated.
     */
    public static function profile_criteria_review(\core\event\user_updated $event) {
        global $DB, $CFG;

        if (!empty($CFG->enablebadges)) {
            require_once($CFG->dirroot.'/lib/badgeslib.php');
            $userid = $event->objectid;

            if ($rs = $DB->get_records('badge_criteria', array('criteriatype' => BADGE_CRITERIA_TYPE_PROFILE))) {
                foreach ($rs as $r) {
                    $badge = new badge($r->badgeid);
                    if (!$badge->is_active() || $badge->is_issued($userid)) {
                        continue;
                    }

                    if ($badge->criteria[BADGE_CRITERIA_TYPE_PROFILE]->review($userid)) {
                        $badge->criteria[BADGE_CRITERIA_TYPE_PROFILE]->mark_complete($userid);

                        if ($badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->review($userid)) {
                            $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->mark_complete($userid);
                            $badge->issue($userid);
                        }
                    }
                }
            }
        }
    }

    /**
     * Triggered when the 'cohort_member_added' event happens.
     *
     * @param \core\event\cohort_member_added $event generated when a user is added to a cohort
     */
    public static function cohort_criteria_review(\core\event\cohort_member_added $event) {
        global $DB, $CFG;

        if (!empty($CFG->enablebadges)) {
            require_once($CFG->dirroot.'/lib/badgeslib.php');
            $cohortid = $event->objectid;
            $userid = $event->relateduserid;

            // Get relevant badges.
            $badgesql = "SELECT badgeid
                FROM {badge_criteria_param} cp
                JOIN {badge_criteria} c ON cp.critid = c.id
                WHERE c.criteriatype = ?
                AND cp.name = ?";
            $badges = $DB->get_records_sql($badgesql, array(BADGE_CRITERIA_TYPE_COHORT, "cohort_{$cohortid}"));
            if (empty($badges)) {
                return;
            }

            foreach ($badges as $b) {
                $badge = new badge($b->badgeid);
                if (!$badge->is_active()) {
                    continue;
                }
                if ($badge->is_issued($userid)) {
                    continue;
                }

                if ($badge->criteria[BADGE_CRITERIA_TYPE_COHORT]->review($userid)) {
                    $badge->criteria[BADGE_CRITERIA_TYPE_COHORT]->mark_complete($userid);

                    if ($badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->review($userid)) {
                        $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]->mark_complete($userid);
                        $badge->issue($userid);
                    }
                }
            }
        }
    }
}
