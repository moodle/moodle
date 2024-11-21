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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

defined('MOODLE_INTERNAL') || die();

function intelliboard_competency_access()
{
    global $USER;
    global $CFG;

    // Tutora and Moodle capabilities are different.
    if (isset($CFG->totara_version)) {
        if (!get_capability_info('totara/competency:assign_self')) {
            throw new moodle_exception('no_competency', 'local_intelliboard');
        }
    } else {
        if (!get_capability_info('moodle/competency:competencyview')) {
            throw new moodle_exception('no_competency', 'local_intelliboard');
        }
    }

    if (!get_config('local_intelliboard', 'competency_dashboard')) {
        throw new moodle_exception('invalidaccess', 'error');
    }
    if (is_siteadmin()) {
        return true;
    }
    $access = false;
    $instructor_roles = get_config('local_intelliboard', 'filter10');
    if (!empty($instructor_roles)) {
        $roles = explode(',', $instructor_roles);
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if ($role and user_has_role_assignment($USER->id, $role)){
                    $access = true;
                    break;
                }
            }
        }
    }
    if (!$access) {
        throw new moodle_exception('invalidaccess', 'error');
    }
    return true;
}

function intelliboard_competency_courses()
{
    global $DB, $USER, $CFG;

    $sql = ""; $params = array();
    if (!is_siteadmin()) {
        list($sql_roles, $params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter10')), SQL_PARAMS_NAMED, 'r');
        $params['userid'] = $USER->id;
        if (isset($CFG->totara_version)) {
            $courseid = "cc.iteminstance";
        } else {
            $courseid = "cc.courseid";
        }
        $sql = " AND $courseid IN (SELECT ctx.instanceid FROM {role_assignments} ra, {context} ctx WHERE ctx.id = ra.contextid AND ctx.contextlevel = 50 AND ra.roleid $sql_roles AND ra.userid = :userid GROUP BY ctx.instanceid)";
    }

    if (isset($CFG->totara_version)) {
        $query = "SELECT competencyid AS id, fullname AS shortname, COUNT(DISTINCT cc.iteminstance) AS courses
                	FROM {comp_criteria} cc
			        LEFT JOIN {comp} as c ON c.id = cc.competencyid
			        WHERE itemtype = 'coursecompletion' $sql
			        GROUP BY competencyid";
    } else {
        $query = "SELECT c.id, c.shortname, COUNT(DISTINCT cc.courseid) AS courses
        		    FROM {competency} c, {competency_coursecomp} cc
        		    WHERE c.id = cc.competencyid $sql 
        		    GROUP BY c.id";
    }
    return $DB->get_records_sql($query, $params);
}

function intelliboard_competencies_progress($cohortid = [])
{
    global $DB, $USER, $CFG;

    $sql1 = "";
    $sql2 = "";
    $sql3 = "";
    $sql4 = "";
    $params = array(
        'userid1' => $USER->id,
        'userid2' => $USER->id,
        'userid3' => $USER->id
    );

    if (isset($CFG->totara_version)) {
        $userid = "cu.user_id";
    } else {
        $userid = "cu.userid";
    }

    $cohortmembersjoin = 'LEFT ' . \local_intelliboard\helpers\SQLEntityHelper::cohortMembersJoin($USER->id, $userid,
            $cohortid);
    if (!is_siteadmin()) {
        $roles = explode(',', get_config('local_intelliboard', 'filter10'));

        list($sql_roles1, $sql_params) = $DB->get_in_or_equal($roles, SQL_PARAMS_NAMED, 'r1');
        $params = array_merge($params,$sql_params);

        $sql1 = " AND courseid IN (SELECT ctx.instanceid FROM {role_assignments} ra, {context} ctx WHERE ctx.id = ra.contextid AND ctx.contextlevel = 50 AND ra.roleid $sql_roles1 AND ra.userid = :userid1 GROUP BY ctx.instanceid)";

        list($sql_roles2, $sql_params) = $DB->get_in_or_equal($roles, SQL_PARAMS_NAMED, 'r2');
        $params = array_merge($params,$sql_params);

        $sql2 = " AND courseid IN (SELECT ctx.instanceid FROM {role_assignments} ra, {context} ctx WHERE ctx.id = ra.contextid AND ctx.contextlevel = 50 AND ra.roleid $sql_roles2 AND ra.userid = :userid2 GROUP BY ctx.instanceid)";

        list($sql_roles3, $sql_params) = $DB->get_in_or_equal($roles, SQL_PARAMS_NAMED, 'r3');
        $params = array_merge($params,$sql_params);

        $sql3 = " AND courseid IN (SELECT ctx.instanceid FROM {role_assignments} ra, 
                                        {context} ctx 
                                    WHERE ctx.id = ra.contextid AND ctx.contextlevel = 50 
                                    AND ra.roleid $sql_roles3 AND ra.userid = :userid3 GROUP BY ctx.instanceid)";

        $sql4 = " AND user_id = $USER->id ";
    }
    if (isset($CFG->totara_version)) {
        return $DB->get_records_sql(
            "SELECT c.id,
                c.fullname AS shortname,
                (SELECT count(id) FROM {totara_competency_achievement} cu {$cohortmembersjoin} 
                WHERE competency_id = c.id AND proficient = 1 {$sql4}) AS proficient,
                (SELECT count(id) FROM {totara_competency_achievement} cu {$cohortmembersjoin}
		        WHERE competency_id = c.id AND proficient = 0 {$sql4}) AS unrated
            FROM {comp} c", $params);
    } else {
        return $DB->get_records_sql(
            "SELECT c.id, c.shortname,
                (SELECT count(id) FROM {competency_usercompcourse} cu {$cohortmembersjoin}
                WHERE competencyid = c.id AND proficiency = 1 {$sql1}) AS proficient,
                (SELECT count(id) FROM {competency_usercompcourse} cu {$cohortmembersjoin}
                WHERE competencyid = c.id AND proficiency = 0 AND grade IS NOT NULL {$sql2}) AS unproficient,
                (SELECT count(id) FROM {competency_usercompcourse} cu {$cohortmembersjoin}
                WHERE competencyid = c.id AND grade IS NULL {$sql3}) AS unrated
            FROM {competency} c", $params);
    }
}

function intelliboard_competencies_total($cohortid = [])
{
    global $DB, $USER, $CFG;

    $sql1 = "";
    $sql2 = "";
    $sql3 = "";
    $sql4 = "";
    $sql5 = "";
    $params = array(
        'userid1' => $USER->id,
        'userid2' => $USER->id,
        'userid3' => $USER->id,
        'userid4' => $USER->id
    );

    if (isset($CFG->totara_version)) {
        $userid = "cu.user_id";
    } else {
        $userid = "cu.userid";
    }

    $cohortmembersjoin = ' LEFT ' . \local_intelliboard\helpers\SQLEntityHelper::cohortMembersJoin($USER->id, $userid, $cohortid);

    if (!is_siteadmin()) {
        $roles = explode(',', get_config('local_intelliboard', 'filter10'));

        list($sql_roles1, $sql_params) = $DB->get_in_or_equal($roles, SQL_PARAMS_NAMED, 'r1');
        $params = array_merge($params,$sql_params);

        $sql1 = " AND courseid IN (SELECT ctx.instanceid FROM {role_assignments} ra, {context} ctx WHERE ctx.id = ra.contextid AND ctx.contextlevel = 50 AND ra.roleid $sql_roles1 AND ra.userid = :userid1 GROUP BY ctx.instanceid)";

        list($sql_roles2, $sql_params) = $DB->get_in_or_equal($roles, SQL_PARAMS_NAMED, 'r2');
        $params = array_merge($params,$sql_params);

        $sql2 = " AND courseid IN (SELECT ctx.instanceid FROM {role_assignments} ra, {context} ctx WHERE ctx.id = ra.contextid AND ctx.contextlevel = 50 AND ra.roleid $sql_roles2 AND ra.userid = :userid2 GROUP BY ctx.instanceid)";

        list($sql_roles3, $sql_params) = $DB->get_in_or_equal($roles, SQL_PARAMS_NAMED, 'r3');
        $params = array_merge($params,$sql_params);

        $sql3 = " AND courseid IN (SELECT ctx.instanceid FROM {role_assignments} ra, {context} ctx WHERE ctx.id = ra.contextid AND ctx.contextlevel = 50 AND ra.roleid $sql_roles3 AND ra.userid = :userid3 GROUP BY ctx.instanceid)";

        list($sql_roles4, $sql_params) = $DB->get_in_or_equal($roles, SQL_PARAMS_NAMED, 'r3');
        $params = array_merge($params,$sql_params);

        $sql4 = " WHERE id IN (SELECT DISTINCT cc.competencyid FROM {role_assignments} ra, {context} ctx, {competency_coursecomp} cc WHERE cc.courseid = ctx.instanceid AND ctx.id = ra.contextid AND ctx.contextlevel = 50 AND ra.roleid $sql_roles4 AND ra.userid = :userid4)";

        $sql5 = " AND user_id = $USER->id ";
    }

    if (isset($CFG->totara_version)) {
        return $DB->get_record_sql(
            "SELECT
                (SELECT count(id) FROM {comp}) AS competencies,
                (SELECT count(id) FROM {comp_framework}) AS frameworks,
                (SELECT count(id) FROM {dp_plan_competency_assign}) AS plans,
                (SELECT count(id) FROM {totara_competency_achievement} cu {$cohortmembersjoin}  
                WHERE proficient = 1 $sql5)  AS proficient,
                (SELECT count(id) FROM {totara_competency_achievement} cu {$cohortmembersjoin} 
                WHERE proficient = 0 and scale_value_id IS NOT NULL $sql5) AS unproficient,
                (SELECT count(id) FROM {totara_competency_achievement} cu {$cohortmembersjoin} 
                WHERE proficient = 0 and scale_value_id IS NULL $sql5) AS unrated");
    } else {
        return $DB->get_record_sql(
            "SELECT
                (SELECT count(id) FROM {competency} $sql4) AS competencies,
                (SELECT count(id) FROM {competency_framework}) AS frameworks,
                (SELECT count(id) FROM {competency_plan}) AS plans,
                (SELECT count(id) FROM {competency_usercompcourse} cu {$cohortmembersjoin} 
                WHERE proficiency = 1 $sql1) AS proficient,
                (SELECT count(id) FROM {competency_usercompcourse} cu {$cohortmembersjoin} 
                WHERE proficiency = 0 AND grade IS NOT NULL $sql2) AS unproficient,
                (SELECT count(id) FROM {competency_usercompcourse} cu {$cohortmembersjoin} 
                WHERE grade IS NULL $sql3) AS unrated", $params);
    }
}

function intelliboard_competency_frameworks()
{
    global $DB, $CFG;

    if (isset($CFG->totara_version)) {
        $query = "SELECT cf.id, cf.fullname AS shortname, COUNT(c.id) AS competencies
			        FROM {comp_framework} cf
			        LEFT JOIN {comp} c ON c.frameworkid = cf.id
			        WHERE cf.id > 0
			        GROUP BY cf.id";
    } else {
        $query = "SELECT cf.id, cf.shortname, COUNT(c.id) AS competencies
        		    FROM {competency_framework} cf
            		LEFT JOIN {competency} c ON c.competencyframeworkid = cf.id
        		    WHERE cf.id > 0
			        GROUP BY cf.id";
    }
    return $DB->get_records_sql($query);
}

function intelliboard_course_total($courseid)
{
    global $DB, $CFG;

    $params = array('courseid' => $courseid, 'courseid2' => $courseid);
    $learner_roles = get_config('local_intelliboard', 'filter11');

    list($sql, $params) = intelliboard_filter_in_sql($learner_roles, "ra.roleid", $params);

    if (isset($CFG->totara_version)) {
	    return $DB->get_record_sql(
            "SELECT c.id,c.fullname, c.startdate, c.enablecompletion, ca.name AS category,
                (SELECT COUNT(cu.id) FROM {totara_competency_achievement} cu
                JOIN {comp_criteria} cc ON cc.iteminstance = c.id AND cc.competencyid = cu.competency_id
                JOIN {enrol} e ON e.courseid = c.id AND e.status = 0
                JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.userid = cu.user_id
                WHERE courseid = c.id AND cu.proficient = 1) AS proficiency,
                (SELECT COUNT(DISTINCT cc.competencyid) FROM {comp_criteria} cc
                WHERE cc.iteminstance = c.id) AS competencies,
                (SELECT COUNT(DISTINCT ra.userid) FROM {role_assignments} ra, {context} ctx
                WHERE ctx.id = ra.contextid AND ctx.contextlevel = 50 $sql AND ctx.instanceid = c.id) AS learners
            FROM {course} c
            LEFT JOIN {course_categories} ca ON ca.id = c.category
            WHERE c.id = :courseid LIMIT 1", $params);
    } else {
    return $DB->get_record_sql(
        "SELECT c.id,c.fullname, c.startdate, c.enablecompletion, ca.name AS category,
            (SELECT COUNT(DISTINCT cu.id) FROM {competency_usercompcourse} cu 
            WHERE cu.courseid = c.id AND cu.proficiency = 1) AS proficiency,
            (SELECT COUNT(DISTINCT cc.competencyid) FROM {competency_coursecomp} cc 
            WHERE cc.courseid = c.id) AS competencies,
            (SELECT COUNT(DISTINCT ra.userid) FROM {role_assignments} ra, {context} ctx 
            WHERE ctx.id = ra.contextid AND ctx.contextlevel = 50 $sql AND ctx.instanceid = c.id) AS learners
        FROM {course} c
        LEFT JOIN {course_categories} ca ON ca.id = c.category
	    WHERE c.id = :courseid LIMIT 1", $params);
    }
}
function intelliboard_learners_total($courseid, $competencyid)
{
    global $DB, $CFG;

    $params = array(
        'competencyid' => $competencyid,
        'courseid' => $courseid
    );
    if (isset($CFG->totara_version)) {
        return $DB->get_record_sql(
            "SELECT ca.competency_id as id, co.fullname as course, user_id, c.fullname as shortname, c.description, 
                c.idnumber, c.timecreated AS created, 
                SUM(ca.proficient) AS proficient, 
                COUNT(CASE WHEN ca.proficient = 0 THEN 1 END) AS rated 
            FROM {totara_competency_achievement} ca 
            JOIN {comp_criteria} cc ON cc.competencyid = ca.competency_id AND cc.iteminstance = :courseid
            JOIN {enrol} e ON e.courseid = cc.iteminstance AND e.status = 0
            JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.userid = ca.user_id
            JOIN {course} co ON co.id = cc.iteminstance
            LEFT JOIN {comp} c ON c.id = ca.competency_id
            WHERE ca.competency_id = :competencyid", $params);
    } else {
        return $DB->get_record_sql(
            "SELECT c.id, co.fullname AS course, cc.courseid, c.shortname, c.description, c.idnumber, 
                c.timecreated AS created, cc.timecreated AS asigned,
                (SELECT COUNT(DISTINCT cu.id) FROM {competency_usercompcourse} cu 
                WHERE cu.competencyid = c.id AND cu.courseid = cc.courseid AND cu.proficiency = 1) AS proficient,
                (SELECT COUNT(DISTINCT cu.id) FROM {competency_usercompcourse} cu 
                WHERE cu.competencyid = c.id AND cu.courseid = cc.courseid AND cu.grade IS NOT NULL) AS rated,
                (SELECT COUNT(DISTINCT m.cmid) FROM {course_modules} cm, {competency_modulecomp} m 
                WHERE cm.visible = 1 AND m.cmid = cm.id AND cm.course = cc.courseid AND  m.competencyid = cc.competencyid) AS activities
            FROM {competency_coursecomp} cc
            LEFT JOIN {competency} c ON c.id = cc.competencyid
            LEFT JOIN {course} co ON co.id = cc.courseid
            WHERE c.id = :competencyid AND cc.courseid = :courseid LIMIT 1", $params);
    }
}

function intelliboard_learner_total($userid, $courseid)
{
    global $DB, $CFG;

    $params = array(
        'userid' => $userid,
        'courseid' => $courseid
    );

    if (isset($CFG->totara_version)) {
	    return $DB->get_record_sql(
		    "SELECT cc.iteminstance AS courseid, c.fullname AS course,
		    COUNT(DISTINCT cc.id) AS competencycount,
		    COUNT(DISTINCT ca.competency_id) AS proficientcompetencycount    
	        FROM {comp_criteria} cc
	        LEFT JOIN {totara_competency_achievement}  ca ON ca.user_id = :userid
		        AND ca.competency_id = cc.competencyid AND ca.proficient = 1     
            LEFT JOIN {course} c ON c.id = cc.iteminstance 
            WHERE cc.iteminstance = :courseid     
            GROUP BY cc.iteminstance", $params);
    } else {

        return $DB->get_record_sql(
            "SELECT u.id, ctx.instanceid AS courseid, co.fullname AS course,
                (SELECT COUNT(DISTINCT comp.id) FROM {competency_coursecomp} coursecomp
                JOIN {competency} comp ON coursecomp.competencyid = comp.id
                WHERE coursecomp.courseid = ctx.instanceid) AS competencycount,
                (SELECT COUNT(DISTINCT cu.competencyid) FROM {competency_usercompcourse} cu 
                WHERE cu.courseid = ctx.instanceid AND cu.userid = u.id AND cu.proficiency = 1) AS proficientcompetencycount,
                (SELECT COUNT(DISTINCT cu.id) FROM {competency_usercompcourse} cu 
                WHERE cu.courseid = ctx.instanceid AND cu.userid = u.id AND cu.grade IS NOT NULL) AS users_rated
                FROM {role_assignments} ra
                LEFT JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = 50
                LEFT JOIN {course} co ON co.id = ctx.instanceid
                LEFT JOIN {user} u ON u.id = ra.userid
	        WHERE ctx.instanceid = :courseid AND ra.userid = :userid LIMIT 1", $params);
    }
}

