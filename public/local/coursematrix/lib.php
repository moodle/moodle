<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

function local_coursematrix_get_rules() {
    global $DB;
    return $DB->get_records('local_coursematrix');
}

function local_coursematrix_get_rule($id) {
    global $DB;
    return $DB->get_record('local_coursematrix', ['id' => $id]);
}

function local_coursematrix_save_rule($data) {
    global $DB;
    
    // Ensure courses is a CSV string
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

function local_coursematrix_delete_rule($id) {
    global $DB;
    return $DB->delete_records('local_coursematrix', ['id' => $id]);
}

/**
 * Process updates for a specific rule.
 * Finds all users matching the rule and ensures they are enrolled.
 */
function local_coursematrix_process_rule_updates($ruleid) {
    global $DB;
    $rule = $DB->get_record('local_coursematrix', ['id' => $ruleid]);
    if (!$rule) return;

    // Build SQL to find matching users
    $sql = "SELECT id FROM {user} WHERE deleted = 0 AND suspended = 0";
    $params = [];
    
    if (!empty($rule->department)) {
        $sql .= " AND department = :dept";
        $params['dept'] = $rule->department;
    }
    if (!empty($rule->jobtitle)) {
        $sql .= " AND institution = :job"; // Mapping jobtitle to institution
        $params['job'] = $rule->jobtitle;
    }
    
    $users = $DB->get_records_sql($sql, $params);
    foreach ($users as $user) {
        local_coursematrix_enrol_user($user->id);
    }
}

/**
 * Check a user against all matrix rules and enrol them in matching courses.
 */
function local_coursematrix_enrol_user($userid) {
    global $DB;
    $user = $DB->get_record('user', ['id' => $userid]);
    if (!$user) return;

    $rules = $DB->get_records('local_coursematrix');
    $courses_to_enrol = [];
    
    foreach ($rules as $rule) {
        // Case-insensitive comparison
        $match_dept = empty($rule->department) || strcasecmp($user->department ?? '', $rule->department) === 0;
        $match_job = empty($rule->jobtitle) || strcasecmp($user->institution ?? '', $rule->jobtitle) === 0;
        
        if ($match_dept && $match_job) {
            $course_ids = explode(',', $rule->courses);
            foreach ($course_ids as $cid) {
                if (is_numeric($cid)) {
                    $courses_to_enrol[] = $cid;
                }
            }
        }
    }
    
    $courses_to_enrol = array_unique($courses_to_enrol);
    
    if (empty($courses_to_enrol)) {
        return;
    }

    // Get manual enrolment plugin
    $enrol_manual = enrol_get_plugin('manual');
    if (!$enrol_manual) {
        return; // Manual enrolment not enabled
    }

    foreach ($courses_to_enrol as $courseid) {
        // Check if course exists
        if (!$DB->record_exists('course', ['id' => $courseid])) {
            continue;
        }

        // Get manual instance for the course
        $instance = $DB->get_record('enrol', ['courseid' => $courseid, 'enrol' => 'manual']);
        if (!$instance) {
            // If no manual instance, create one? Or skip? 
            // Better to skip or log. Auto-creating might be unexpected.
            // But for this feature to work, we need an instance.
            // Let's try to find ANY enrol instance we can use, or add manual if missing.
            // For now, assume manual exists or skip.
            continue;
        }

        if (!$DB->record_exists('user_enrolments', ['enrolid' => $instance->id, 'userid' => $user->id])) {
            $enrol_manual->enrol_user($instance, $user->id);
        }
    }
}
