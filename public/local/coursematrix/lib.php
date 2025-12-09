<?php
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
    
    $coursestoenrol = array_unique($coursestoenrol);
    
    if (empty($coursestoenrol)) {
        return;
    }

    // Get manual enrolment plugin.
    $enrolmanual = enrol_get_plugin('manual');
    if (!$enrolmanual) {
        return; // Manual enrolment not enabled.
    }

    // Get student role.
    $studentrole = $DB->get_record('role', ['shortname' => 'student']);
    $studentroleid = $studentrole ? $studentrole->id : 5; // Default to 5 if not found.

    foreach ($coursestoenrol as $courseid) {
        if (!$DB->record_exists('course', ['id' => $courseid])) {
            continue;
        }
        local_coursematrix_enrol_user_in_course($user->id, $courseid, $studentroleid, $enrolmanual);
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

    if (!$DB->record_exists('user_enrolments', ['enrolid' => $instance->id, 'userid' => $userid])) {
        $enrolmanual->enrol_user($instance, $userid, $roleid);
    }
}
