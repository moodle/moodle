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
namespace tool_monitor\task;
use tool_monitor\subscription;
use tool_monitor\subscription_manager;

/**
 * Simple task class responsible for activating, deactivating and removing subscriptions.
 *
 * Activation/deactivation is managed by looking at the same access rules used to determine whether a user can
 * subscribe to the rule in the first place.
 *
 * Removal occurs when a subscription has been inactive for a period of time exceeding the lifespan, as set by
 * subscription_manager::get_inactive_subscription_lifespan().
 *
 * I.e.
 *  - Activation:   If a user can subscribe currently, then an existing subscription should be made active.
 *  - Deactivation: If a user cannot subscribe currently, then an existing subscription should be made inactive.
 *  - Removal:      If a user has a subscription that has been inactive for longer than the prescribed period, then
 *                  delete the subscription entirely.
 *
 * @since      3.0.5
 * @package    tool_monitor
 * @copyright  2016 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check_subscriptions extends \core\task\scheduled_task {

    /** @var array 1d static cache, indexed by userid, storing whether or not the user has been fully set up.*/
    protected $userssetupcache = array();

    /** @var array 2d static cache, indexed by courseid and userid, storing whether a user can access the course with
     *  the 'tool/monitor:subscribe' capability.
     */
    protected $courseaccesscache = array();

    /**
     * Get a descriptive name for this task.
     *
     * @since 3.0.5
     * @return string name of the task.
     */
    public function get_name() {
        return get_string('taskchecksubscriptions', 'tool_monitor');
    }

    /**
     * Checks all course-level rule subscriptions and activates/deactivates based on current course access.
     *
     * The ordering of checks within the task is important for optimisation purposes. The aim is to be able to make a decision
     * about whether to activate/deactivate each subscription without making unnecessary checks. The ordering roughly follows the
     * context model, starting with system and user checks and moving down to course and course-module only when necessary.
     *
     * For example, if the user is suspended, then any active subscription is made inactive right away. I.e. there is no need to
     * check site-level, course-level or course-module-level permissions. Likewise, if a subscriptions is site-level, there is no
     * need to check course-level and course-module-level permissions.
     *
     * The task performs the following checks, in this order:
     * 1. Check for a suspended user, breaking if suspended.
     * 2. Check for an incomplete (not set up) user, breaking if not fully set up.
     * 3. Check for the required capability in the relevant context, breaking if the capability is not found.
     * 4. Check whether the subscription is site-context, breaking if true.
     * 5. Check whether the user has course access, breaking only if the subscription is not also course-module-level.
     * 6. Check whether the user has course-module access.
     *
     * @since 3.0.5
     */
    public function execute() {
        global $DB;

        if (!get_config('tool_monitor', 'enablemonitor')) {
            return; // The tool is disabled. Nothing to do.
        }

        $toactivate   = array(); // Store the ids of subscriptions to be activated upon completion.
        $todeactivate = array(); // Store the ids of subscriptions to be deactivated upon completion.

        // Resultset rows are ordered by userid and courseid to work nicely with get_fast_modinfo() caching.
        $sql = "SELECT u.id AS userid, u.firstname AS userfirstname, u.lastname AS userlastname, u.suspended AS usersuspended,
                       u.email AS useremail, c.visible as coursevisible, c.cacherev as coursecacherev, s.courseid AS subcourseid,
                       s.userid AS subuserid, s.cmid AS subcmid, s.inactivedate AS subinactivedate, s.id AS subid
                  FROM {user} u
                  JOIN {tool_monitor_subscriptions} s ON (s.userid = u.id)
             LEFT JOIN {course} c ON (c.id = s.courseid)
                 WHERE u.id = s.userid
              ORDER BY s.userid, s.courseid";
        $rs = $DB->get_recordset_sql($sql);

        foreach ($rs as $row) {
            // Create skeleton records from the result. This should be enough to use in subsequent access calls and avoids DB hits.
            $sub = $this->get_subscription_from_rowdata($row);
            $sub = new subscription($sub);
            if (!isset($user) || $user->id != $sub->userid) {
                $user= $this->get_user_from_rowdata($row);
            }
            if ((!isset($course) || $course->id != $sub->courseid) && !empty($sub->courseid)) {
                $course = $this->get_course_from_rowdata($row);
            }

            // The user is suspended at site level, so deactivate any active subscriptions.
            if ($user->suspended) {
                if (subscription_manager::subscription_is_active($sub)) {
                    $todeactivate[] = $sub->id;
                }
                continue;
            }

            // Is the user fully set up? As per require_login on the subscriptions page.
            if (!$this->is_user_setup($user)) {
                if (subscription_manager::subscription_is_active($sub)) {
                    $todeactivate[] = $sub->id;
                }
                continue;
            }

            // Determine the context, based on the subscription course id.
            $sitelevelsubscription = false;
            if (empty($sub->courseid)) {
                $context = \context_system::instance();
                $sitelevelsubscription = true;
            } else {
                $context = \context_course::instance($sub->courseid);
            }

            // Check capability in the context.
            if (!has_capability('tool/monitor:subscribe', $context, $user)) {
                if (subscription_manager::subscription_is_active($sub)) {
                    $todeactivate[] = $sub->id;
                }
                continue;
            }

            // If the subscription is site-level, then we've run all the checks required to make an access decision.
            if ($sitelevelsubscription) {
                if (!subscription_manager::subscription_is_active($sub)) {
                    $toactivate[] = $sub->id;
                }
                continue;
            }

            // Check course access.
            if (!$this->user_can_access_course($user, $course, 'tool/monitor:subscribe')) {
                if (subscription_manager::subscription_is_active($sub)) {
                    $todeactivate[] = $sub->id;
                }
                continue;
            }

            // If the subscription has no course module relationship.
            if (empty($sub->cmid)) {
                if (!subscription_manager::subscription_is_active($sub)) {
                    $toactivate[] = $sub->id;
                }
                continue;
            }

            // Otherwise, check the course module info. We use the same checks as on the subscription page.
            $modinfo = get_fast_modinfo($course, $sub->userid);
            $cm = $modinfo->get_cm($sub->cmid);
            if (!$cm || !$cm->uservisible || !$cm->available) {
                if (subscription_manager::subscription_is_active($sub)) {
                    $todeactivate[] = $sub->id;
                }
                continue;
            }

            // The course module is available and visible, so make a decision.
            if (!subscription_manager::subscription_is_active($sub)) {
                $toactivate[] = $sub->id;
            }
        }
        $rs->close();

        // Activate/deactivate/delete relevant subscriptions.
        subscription_manager::activate_subscriptions($toactivate);
        subscription_manager::deactivate_subscriptions($todeactivate);
        subscription_manager::delete_stale_subscriptions();
    }

    /**
     * Determines whether a user is fully set up, using cached results where possible.
     *
     * @since 3.0.5
     * @param \stdClass $user the user record.
     * @return bool true if the user is fully set up, false otherwise.
     */
    protected function is_user_setup($user) {
        if (!isset($this->userssetupcache[$user->id])) {
            $this->userssetupcache[$user->id] = !user_not_fully_set_up($user, true);
        }
        return $this->userssetupcache[$user->id];
    }

    /**
     * Determines a user's access to a course with a given capability, using cached results where possible.
     *
     * @since 3.0.5
     * @param \stdClass $user the user record.
     * @param \stdClass $course the course record.
     * @param string $capability the capability to check.
     * @return bool true if the user can access the course with the specified capability, false otherwise.
     */
    protected function user_can_access_course($user, $course, $capability) {
        if (!isset($this->courseaccesscache[$course->id][$user->id][$capability])) {
            $this->courseaccesscache[$course->id][$user->id][$capability] = can_access_course($course, $user, $capability, true);
        }
        return $this->courseaccesscache[$course->id][$user->id][$capability];
    }

    /**
     * Returns a partial subscription record, created from properties of the supplied recordset row object.
     * Intended to return a minimal record for specific use within this class and in subsequent access control calls only.
     *
     * @since 3.0.5
     * @param \stdClass $rowdata the row object.
     * @return \stdClass a partial subscription record.
     */
    protected function get_subscription_from_rowdata($rowdata) {
        $sub = new \stdClass();
        $sub->id = $rowdata->subid;
        $sub->userid = $rowdata->subuserid;
        $sub->courseid = $rowdata->subcourseid;
        $sub->cmid = $rowdata->subcmid;
        $sub->inactivedate = $rowdata->subinactivedate;
        return $sub;
    }

    /**
     * Returns a partial course record, created from properties of the supplied recordset row object.
     * Intended to return a minimal record for specific use within this class and in subsequent access control calls only.
     *
     * @since 3.0.5
     * @param \stdClass $rowdata the row object.
     * @return \stdClass a partial course record.
     */
    protected function get_course_from_rowdata($rowdata) {
        $course = new \stdClass();
        $course->id = $rowdata->subcourseid;
        $course->visible = $rowdata->coursevisible;
        $course->cacherev = $rowdata->coursecacherev;
        return $course;
    }

    /**
     * Returns a partial user record, created from properties of the supplied recordset row object.
     * Intended to return a minimal record for specific use within this class and in subsequent access control calls only.
     *
     * @since 3.0.5
     * @param \stdClass $rowdata the row object.
     * @return \stdClass a partial user record.
     */
    protected function get_user_from_rowdata($rowdata) {
        $user = new \stdClass();
        $user->id = $rowdata->userid;
        $user->firstname = $rowdata->userfirstname;
        $user->lastname = $rowdata->userlastname;
        $user->email = $rowdata->useremail;
        $user->suspended = $rowdata->usersuspended;
        return $user;
    }
}
