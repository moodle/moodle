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
 * Library functions for local_coursematrix
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Get all matrix rules.
 *
 * @return array List of rules
 */
function local_coursematrix_get_rules() {
    global $DB;
    return $DB->get_records('local_coursematrix');
}

/**
 * Get a single rule by ID.
 *
 * @param int $id Rule ID
 * @return stdClass|false Rule object or false
 */
function local_coursematrix_get_rule($id) {
    global $DB;
    return $DB->get_record('local_coursematrix', ['id' => $id]);
}

/**
 * Save a rule (create or update).
 *
 * @param stdClass $data Rule data
 * @return int Rule ID
 */
function local_coursematrix_save_rule($data) {
    global $DB;

    // Ensure courses is a CSV string.
    if (is_array($data->courses)) {
        $data->courses = implode(',', $data->courses);
    }

    if (empty($data->id)) {
        $id = $DB->insert_record('local_coursematrix', $data);
        local_coursematrix_process_rule_updates($id);
        return $id;
    } else {
        $DB->update_record('local_coursematrix', $data);
        local_coursematrix_process_rule_updates($data->id);
        return $data->id;
    }
}

/**
 * Delete a rule by ID.
 *
 * @param int $id Rule ID
 * @return bool True on success
 */
function local_coursematrix_delete_rule($id) {
    global $DB;
    return $DB->delete_records('local_coursematrix', ['id' => $id]);
}

/**
 * Process updates for a specific rule.
 * Finds all users matching the rule and ensures they are enrolled.
 *
 * @param int $ruleid Rule ID
 * @package local_coursematrix
 */
function local_coursematrix_process_rule_updates($ruleid) {
    global $DB;
    $rule = $DB->get_record('local_coursematrix', ['id' => $ruleid]);
    if (!$rule) {
        return;
    }

    // Build SQL to find matching users.
    $sql = "SELECT id FROM {user} WHERE deleted = 0 AND suspended = 0";
    $params = [];

    if (!empty($rule->department)) {
        $sql .= " AND department = :dept";
        $params['dept'] = $rule->department;
    }
    if (!empty($rule->jobtitle)) {
        $sql .= " AND institution = :job"; // Mapping jobtitle to institution.
        $params['job'] = $rule->jobtitle;
    }

    $users = $DB->get_records_sql($sql, $params);
    foreach ($users as $user) {
        local_coursematrix_enrol_user($user->id);
    }
}

/**
 * Check a user against all matrix rules and enrol them in matching courses.
 *
 * @param int $userid User ID
 * @package local_coursematrix
 */
function local_coursematrix_enrol_user($userid) {
    global $DB;
    $user = $DB->get_record('user', ['id' => $userid]);
    if (!$user) {
        return;
    }

    $rules = $DB->get_records('local_coursematrix');
    $coursestoenrol = local_coursematrix_get_courses_to_enrol($user, $rules);

    if (empty($coursestoenrol)) {
        return;
    }

    local_coursematrix_process_enrolments($user->id, $coursestoenrol);
}

/**
 * Get list of course IDs a user should be enrolled in based on rules.
 *
 * @param stdClass $user User object
 * @param array $rules List of rules
 * @return array Unique course IDs
 */
function local_coursematrix_get_courses_to_enrol($user, $rules) {
    $coursestoenrol = [];

    foreach ($rules as $rule) {
        if (local_coursematrix_user_matches_rule($user, $rule)) {
            $courseids = explode(',', $rule->courses);
            foreach ($courseids as $cid) {
                if (is_numeric($cid)) {
                    $coursestoenrol[] = $cid;
                }
            }
        }
    }

    return array_unique($coursestoenrol);
}

/**
 * Process the actual enrolments for a user into a list of courses.
 *
 * @param int $userid User ID
 * @param array $courseids List of course IDs
 */
function local_coursematrix_process_enrolments($userid, $courseids) {
    global $DB;

    // Get manual enrolment plugin.
    $enrolmanual = enrol_get_plugin('manual');
    if (!$enrolmanual) {
        return; // Manual enrolment not enabled.
    }

    // Get student role.
    $studentrole = $DB->get_record('role', ['shortname' => 'student']);
    $studentroleid = $studentrole ? $studentrole->id : 5; // Default to 5 if not found.

    foreach ($courseids as $courseid) {
        if (!$DB->record_exists('course', ['id' => $courseid])) {
            continue;
        }
        local_coursematrix_enrol_user_in_course($userid, $courseid, $studentroleid, $enrolmanual);
    }
}

/**
 * Check if a user matches a specific rule.
 *
 * @param stdClass $user User object
 * @param stdClass $rule Rule object
 * @return bool True if matches
 */
function local_coursematrix_user_matches_rule($user, $rule) {
    // Case-insensitive comparison.
    $matchdept = empty($rule->department) || strcasecmp($user->department ?? '', $rule->department) === 0;
    $matchjob = empty($rule->jobtitle) || strcasecmp($user->institution ?? '', $rule->jobtitle) === 0;

    return $matchdept && $matchjob;
}

/**
 * Enrol a user in a specific course using manual instance.
 *
 * @param int $userid User ID
 * @param int $courseid Course ID
 * @param int $roleid Role ID
 * @param object $enrolmanual Manual enrolment plugin instance
 */
function local_coursematrix_enrol_user_in_course($userid, $courseid, $roleid, $enrolmanual) {
    global $DB;

    // Get manual instance for the course.
    $instance = $DB->get_record('enrol', ['courseid' => $courseid, 'enrol' => 'manual']);
    if (!$instance) {
        return;
    }

    // Enrol user if not already enrolled.
    if (!$DB->record_exists('user_enrolments', ['enrolid' => $instance->id, 'userid' => $userid])) {
        $enrolmanual->enrol_user($instance, $userid, $roleid);
    }

    // ALWAYS check if the user has ALREADY completed this course (retroactive progress).
    // This must run even if user is already enrolled (e.g., from another plan).
    local_coursematrix_check_and_progress_if_complete($userid, $courseid);
}


// ============================================================================
// LEARNING PLAN FUNCTIONS
// ============================================================================

/**
 * Get all learning plans.
 *
 * @return array List of learning plans
 */
function local_coursematrix_get_plans() {
    global $DB;
    return $DB->get_records('local_coursematrix_plans', null, 'name ASC');
}

/**
 * Get a single learning plan with its courses.
 *
 * @param int $planid Plan ID
 * @return stdClass|false Plan object with courses array, or false
 */
function local_coursematrix_get_plan($planid) {
    global $DB;
    $plan = $DB->get_record('local_coursematrix_plans', ['id' => $planid]);
    if (!$plan) {
        return false;
    }

    // Get courses in order.
    $plan->courses = $DB->get_records('local_coursematrix_plan_courses',
        ['planid' => $planid], 'sortorder ASC');

    // Get reminders.
    $plan->reminders = $DB->get_records('local_coursematrix_reminders',
        ['planid' => $planid], 'daysbefore DESC');

    return $plan;
}

/**
 * Save a learning plan (create or update).
 *
 * @param stdClass $data Plan data including courses array
 * @return int Plan ID
 */
function local_coursematrix_save_plan($data) {
    global $DB;

    $now = time();

    if (empty($data->id)) {
        // Create new plan.
        $data->timecreated = $now;
        $data->timemodified = $now;
        $planid = $DB->insert_record('local_coursematrix_plans', $data);
    } else {
        // Update existing plan.
        $planid = $data->id;
        $data->timemodified = $now;
        $DB->update_record('local_coursematrix_plans', $data);

        // Delete old course associations.
        $DB->delete_records('local_coursematrix_plan_courses', ['planid' => $planid]);
        // Delete old reminders.
        $DB->delete_records('local_coursematrix_reminders', ['planid' => $planid]);
    }

    // Save courses with order and due days.
    // The form now provides $data->courses as array of course IDs and $data->duedays as matching array.
    if (!empty($data->courses) && is_array($data->courses)) {
        $sortorder = 1;
        foreach ($data->courses as $index => $courseid) {
            // Handle both old format (array of arrays) and new format (array of IDs).
            if (is_array($courseid)) {
                // Old format: array of ['courseid' => x, 'duedays' => y].
                $record = new stdClass();
                $record->planid = $planid;
                $record->courseid = $courseid['courseid'];
                $record->sortorder = $sortorder++;
                $record->duedays = $courseid['duedays'] ?? 14;
                $DB->insert_record('local_coursematrix_plan_courses', $record);
            } else {
                // New format: array of course IDs with separate duedays array.
                $record = new stdClass();
                $record->planid = $planid;
                $record->courseid = $courseid;
                $record->sortorder = $sortorder++;
                $record->duedays = $data->duedays[$index] ?? 14;
                $DB->insert_record('local_coursematrix_plan_courses', $record);
            }
        }
    }

    // Save per-course reminders if provided.
    if (!empty($data->course_reminders) && is_array($data->course_reminders)) {
        foreach ($data->course_reminders as $courseid => $reminderdays) {
            foreach ($reminderdays as $daysbefore) {
                if ($daysbefore > 0) {
                    $record = new stdClass();
                    $record->planid = $planid;
                    $record->courseid = $courseid;
                    $record->daysbefore = $daysbefore;
                    $record->enabled = 1;
                    $DB->insert_record('local_coursematrix_reminders', $record);
                }
            }
        }
    } else if (!empty($data->reminders) && is_array($data->reminders)) {
        // Fallback: save plan-level reminders (old format or backward compatibility).
        foreach ($data->reminders as $reminder) {
            $record = new stdClass();
            $record->planid = $planid;
            $record->courseid = null; // Plan-level reminder.
            if (is_array($reminder)) {
                $record->daysbefore = $reminder['daysbefore'];
                $record->enabled = $reminder['enabled'] ?? 1;
            } else {
                $record->daysbefore = $reminder;
                $record->enabled = 1;
            }
            $DB->insert_record('local_coursematrix_reminders', $record);
        }
    }

    return $planid;
}

/**
 * Delete a learning plan and all associated data.
 *
 * @param int $planid Plan ID
 * @return bool True on success
 */
function local_coursematrix_delete_plan($planid) {
    global $DB;

    // Delete in proper order (children first).
    $DB->delete_records('local_coursematrix_reminders', ['planid' => $planid]);
    $DB->delete_records('local_coursematrix_plan_courses', ['planid' => $planid]);
    $DB->delete_records('local_coursematrix_user_plans', ['planid' => $planid]);
    $DB->delete_records('local_coursematrix_plans', ['id' => $planid]);

    return true;
}

/**
 * Assign a user to a learning plan.
 *
 * @param int $userid User ID
 * @param int $planid Plan ID
 * @return int|bool User plan assignment ID, or false if failed
 */
function local_coursematrix_assign_user_to_plan($userid, $planid) {
    global $DB;

    // Check if already assigned.
    if ($DB->record_exists('local_coursematrix_user_plans', ['userid' => $userid, 'planid' => $planid])) {
        return false; // Already assigned.
    }

    // Get first course in the plan (lowest sortorder).
    $firstcourse = $DB->get_record_sql(
        'SELECT courseid, duedays FROM {local_coursematrix_plan_courses} WHERE planid = ? ORDER BY sortorder ASC LIMIT 1',
        [$planid]
    );

    if (!$firstcourse) {
        return false; // No courses in plan.
    }

    // Create user plan assignment.
    $userplan = new stdClass();
    $userplan->userid = $userid;
    $userplan->planid = $planid;
    $userplan->currentcourseid = $firstcourse->courseid;
    $userplan->startdate = time();
    $userplan->status = 'active';
    $userplan->timecreated = time();

    $userplanid = $DB->insert_record('local_coursematrix_user_plans', $userplan);

    // Enrol user in first course.
    $enrolmanual = enrol_get_plugin('manual');
    if ($enrolmanual) {
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $roleid = $studentrole ? $studentrole->id : 5;
        local_coursematrix_enrol_user_in_course($userid, $firstcourse->courseid, $roleid, $enrolmanual);
    }

    return $userplanid;
}

/**
 * Check if a user has already completed a course, and if so, trigger progression.
 * Useful for retroactive assignments or re-enrolments.
 *
 * @param int $userid
 * @param int $courseid
 */
function local_coursematrix_check_and_progress_if_complete($userid, $courseid) {
    global $CFG, $DB;
    require_once($CFG->libdir . '/completionlib.php');

    // Get course record.
    $course = $DB->get_record('course', ['id' => $courseid]);
    if (!$course) {
        return;
    }

    // Check completion.
    $completion = new completion_info($course);
    if (!$completion->is_enabled()) {
        return;
    }

    if ($completion->is_course_complete($userid)) {
        error_log("CM-TRACE: Retroactive completion DETECTED for User $userid Course $courseid - Triggering Progress.");
        local_coursematrix_progress_user_plan($userid, $courseid);
    }
}

/**
 * Progress a user to the next course in their learning plan.
 * Called when a user completes a course.
 *
 * @param int $userid User ID
 * @param int $completedcourseid The course that was completed
 * @return bool True if progressed, false if not applicable
 */
function local_coursematrix_progress_user_plan($userid, $completedcourseid) {
    global $DB;

    // debug
    error_log("CM-TRACE: Checking progress for User $userid completed Course $completedcourseid");

    // Find user plans where this is the current course.
    $userplans = $DB->get_records('local_coursematrix_user_plans', [
        'userid' => $userid,
        'currentcourseid' => $completedcourseid,
        'status' => 'active',
    ]);

    if (empty($userplans)) {
        error_log("CM-TRACE: No active plan found with currentcourseid = $completedcourseid for User $userid");
        return false;
    }

    foreach ($userplans as $userplan) {
        // Get current course's position in the plan.
        $currentpc = $DB->get_record('local_coursematrix_plan_courses', [
            'planid' => $userplan->planid,
            'courseid' => $completedcourseid,
        ]);

        if (!$currentpc) {
            error_log("CM-TRACE: Course $completedcourseid is not in Plan {$userplan->planid} config??");
            continue;
        }

        // Get next course (next highest sortorder).
        $nextcourse = $DB->get_record_sql(
            'SELECT courseid, duedays FROM {local_coursematrix_plan_courses} WHERE planid = ? AND sortorder > ? ORDER BY sortorder ASC LIMIT 1',
            [$userplan->planid, $currentpc->sortorder]
        );

        if ($nextcourse) {
            error_log("CM-TRACE: Progressing User $userid to Next Course {$nextcourse->courseid} in Plan {$userplan->planid}");
            // Progress to next course.
            $userplan->currentcourseid = $nextcourse->courseid;
            $userplan->startdate = time();
            $userplan->status = 'active';
            $DB->update_record('local_coursematrix_user_plans', $userplan);

            // Enrol in next course.
            $enrolmanual = enrol_get_plugin('manual');
            if ($enrolmanual) {
                $studentrole = $DB->get_record('role', ['shortname' => 'student']);
                $roleid = $studentrole ? $studentrole->id : 5;
                local_coursematrix_enrol_user_in_course($userid, $nextcourse->courseid, $roleid, $enrolmanual);
            }
        } else {
            error_log("CM-TRACE: Plan {$userplan->planid} COMPLETED for User $userid");
            // No more courses - plan completed!
            $userplan->status = 'completed';
            $userplan->currentcourseid = null;
            $DB->update_record('local_coursematrix_user_plans', $userplan);
        }
    }
    return true;
}

/**
 * Get the due date info for a user's course in a learning plan.
 *
 * @param int $userid User ID
 * @param int $courseid Course ID
 * @return stdClass|null Object with duedate, daysremaining, status, or null if not in plan
 */
function local_coursematrix_get_user_course_dueinfo($userid, $courseid) {
    global $DB;

    $userplan = $DB->get_record('local_coursematrix_user_plans', [
        'userid' => $userid,
        'currentcourseid' => $courseid,
    ]);

    if (!$userplan) {
        return null;
    }

    $plancourse = $DB->get_record('local_coursematrix_plan_courses', [
        'planid' => $userplan->planid,
        'courseid' => $courseid,
    ]);

    if (!$plancourse) {
        return null;
    }

    $dueinfo = new stdClass();
    $dueinfo->duedate = $userplan->startdate + ($plancourse->duedays * 86400);
    $dueinfo->daysremaining = ceil(($dueinfo->duedate - time()) / 86400);
    $dueinfo->planid = $userplan->planid;
    $dueinfo->status = $userplan->status;

    // Determine urgency level.
    if ($dueinfo->daysremaining < 0) {
        $dueinfo->urgency = 'overdue';
    } else if ($dueinfo->daysremaining <= 2) {
        $dueinfo->urgency = 'critical';
    } else if ($dueinfo->daysremaining <= 7) {
        $dueinfo->urgency = 'warning';
    } else {
        $dueinfo->urgency = 'normal';
    }

    return $dueinfo;
}

/**
 * Check all user plans and update overdue statuses.
 * Also returns list of users who need reminders.
 *
 * @return array Array of users needing reminders with their plan info
 */
function local_coursematrix_check_overdue_and_reminders() {
    global $DB;

    $now = time();
    $remindersneeded = [];

    // Get all active user plans.
    $userplans = $DB->get_records('local_coursematrix_user_plans', ['status' => 'active']);

    foreach ($userplans as $userplan) {
        if (!$userplan->currentcourseid) {
            continue;
        }

        $plancourse = $DB->get_record('local_coursematrix_plan_courses', [
            'planid' => $userplan->planid,
            'courseid' => $userplan->currentcourseid,
        ]);

        if (!$plancourse) {
            continue;
        }

        $duedate = $userplan->startdate + ($plancourse->duedays * 86400);
        $daysremaining = ceil(($duedate - $now) / 86400);

        // Check if overdue.
        if ($daysremaining < 0) {
            $userplan->status = 'overdue';
            $DB->update_record('local_coursematrix_user_plans', $userplan);
            continue;
        }

        // Check reminders for this plan.
        $reminders = $DB->get_records('local_coursematrix_reminders', [
            'planid' => $userplan->planid,
            'enabled' => 1,
        ]);

        foreach ($reminders as $reminder) {
            if ($daysremaining == $reminder->daysbefore) {
                $remindersneeded[] = (object)[
                    'userid' => $userplan->userid,
                    'planid' => $userplan->planid,
                    'courseid' => $userplan->currentcourseid,
                    'daysremaining' => $daysremaining,
                    'duedate' => $duedate,
                ];
            }
        }
    }

    return $remindersneeded;
}

/**
 * Process learning plan assignments from matrix rules.
 * Called when a user is created/updated.
 *
 * @param int $userid User ID
 */
function local_coursematrix_assign_user_plans($userid) {
    global $DB;

    $user = $DB->get_record('user', ['id' => $userid]);
    if (!$user) {
        return;
    }

    $rules = $DB->get_records('local_coursematrix');

    foreach ($rules as $rule) {
        if (!local_coursematrix_user_matches_rule($user, $rule)) {
            continue;
        }

        // Process learning plans from this rule.
        if (!empty($rule->learningplans)) {
            $planids = explode(',', $rule->learningplans);
            foreach ($planids as $planid) {
                if (is_numeric($planid)) {
                    local_coursematrix_assign_user_to_plan($userid, (int)$planid);
                }
            }
        }
    }
}

/**
 * Get dashboard statistics for learning plans.
 *
 * @return stdClass Dashboard data with plans and stats
 */
function local_coursematrix_get_dashboard_stats() {
    global $DB;

    $stats = new stdClass();
    $stats->totalplans = $DB->count_records('local_coursematrix_plans');
    $stats->totalusers = $DB->count_records_select('local_coursematrix_user_plans', 'status IN (?, ?, ?)',
        ['active', 'overdue', 'completed']);
    $stats->activeusers = $DB->count_records('local_coursematrix_user_plans', ['status' => 'active']);
    $stats->overdueusers = $DB->count_records('local_coursematrix_user_plans', ['status' => 'overdue']);
    $stats->completedusers = $DB->count_records('local_coursematrix_user_plans', ['status' => 'completed']);

    // Per-plan stats.
    $stats->plans = [];
    $plans = $DB->get_records('local_coursematrix_plans');

    foreach ($plans as $plan) {
        $planstat = new stdClass();
        $planstat->id = $plan->id;
        $planstat->name = $plan->name;
        $planstat->active = $DB->count_records('local_coursematrix_user_plans',
            ['planid' => $plan->id, 'status' => 'active']);
        $planstat->overdue = $DB->count_records('local_coursematrix_user_plans',
            ['planid' => $plan->id, 'status' => 'overdue']);
        $planstat->completed = $DB->count_records('local_coursematrix_user_plans',
            ['planid' => $plan->id, 'status' => 'completed']);
        $planstat->total = $planstat->active + $planstat->overdue + $planstat->completed;

        // Calculate percentages.
        if ($planstat->total > 0) {
            $planstat->activepct = round(($planstat->active / $planstat->total) * 100);
            $planstat->overduepct = round(($planstat->overdue / $planstat->total) * 100);
            $planstat->completedpct = round(($planstat->completed / $planstat->total) * 100);
        } else {
            $planstat->activepct = 0;
            $planstat->overduepct = 0;
            $planstat->completedpct = 0;
        }

        $stats->plans[] = $planstat;
    }

    return $stats;
}

/**
 * Get all user plan assignments with user and plan details.
 *
 * @param int|null $planid Optional plan ID to filter by
 * @param string|null $status Optional status to filter by
 * @return array List of user plan records with details
 */
function local_coursematrix_get_user_plan_list($planid = null, $status = null) {
    global $DB;

    $where = [];
    $params = [];

    if ($planid) {
        $where[] = 'up.planid = :planid';
        $params['planid'] = $planid;
    }

    if ($status) {
        $where[] = 'up.status = :status';
        $params['status'] = $status;
    }

    $whereclause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $sql = "SELECT up.id, up.userid, up.planid, up.currentcourseid, up.startdate, up.status, up.timecreated,
                   u.firstname, u.lastname, u.email,
                   p.name as planname,
                   c.fullname as coursename
            FROM {local_coursematrix_user_plans} up
            JOIN {user} u ON u.id = up.userid
            JOIN {local_coursematrix_plans} p ON p.id = up.planid
            LEFT JOIN {course} c ON c.id = up.currentcourseid
            $whereclause
            ORDER BY p.name, u.lastname, u.firstname";

    return $DB->get_records_sql($sql, $params);
}

// Note: Course page hooks are now implemented via the new hook callback system.
// See classes/hook_callbacks/output_callbacks.php and db/hooks.php
