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
 * @package   local_report_completion
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class report_completion {

    /**
     * Get completion summary info for a course
     *
     * Parameters - $departmentid = int;
     *              $courseid = int;
     *
     * Return array();
     **/
    public static function get_course_summary_info($departmentid, $courseid=0, $showsuspended) {
        global $DB;

        // Get the company details.
        $departmentrec = $DB->get_record('department', array('id' => $departmentid));
        $company = new company($departmentrec->company);

        // Get the full company tree as we may need it.
        $topcompanyid = $company->get_topcompanyid();
        $topcompany = new company($topcompanyid);
        $companytree = $topcompany->get_child_companies_recursive();
        $parentcompanies = $company->get_parent_companies_recursive();

        // Deal with parent company managers
        if (!empty($parentcompanies)) {
            $userfilter = " AND userid NOT IN (
                             SELECT userid FROM {company_users}
                             WHERE companyid IN (" . implode(',', array_keys($parentcompanies)) . "))";
        } else {
            $userfilter = "";
        }

        // Deal with department tree.
        $alldepartments = company::get_all_subdepartments($departmentid);
        $departmentsql = " AND cu.departmentid IN (" . join(",", array_keys($alldepartments)) . ") ";

        // Deal with suspended or not.
        if (empty($showsuspended)) {
            $suspendedsql = " AND u.suspended = 0 ";
        } else {
            $suspendedsql = "";
        }
        $courses = $DB->get_records_sql("SELECT courseid AS id, coursename
                                         FROM {local_iomad_track}
                                         WHERE companyid = :companyid
                                         GROUP BY courseid, coursename
                                         ORDER BY courseid",
                                         array('companyid' => $company->id));
        foreach ($courses as $id => $course) {
            $courses[$id]->licensesallocated = 0;
            if ($licensesallocated = $DB->count_records_sql("SELECT count(lit.id) FROM {local_iomad_track} lit
                                                             JOIN {company_users} cu ON (lit.userid = cu.userid AND lit.companyid = cu.companyid)
                                                             JOIN {user} u ON (lit.userid = u.id)
                                                             WHERE lit.courseid = :courseid
                                                             AND lit.companyid = :companyid
                                                             AND lit.licensename IS NOT NULL
                                                             $suspendedsql
                                                             $departmentsql",
                                                             array('courseid' => $course->id, 'companyid' => $company->id))) {
                $courses[$id]->licensed = true;
                $courses[$id]->licensesallocated = $licensesallocated;
            } else if ($DB->get_record('iomad_courses', array('courseid' => $course->id, 'licensed' => 1))) {
                $courses[$id]->licensed = true;
            }
            // Count the enrolled users
            $enrolled = $DB->count_records_sql("SELECT COUNT(lit.id)
                                                FROM {local_iomad_track} lit
                                                JOIN {company_users} cu ON (lit.userid = cu.userid AND lit.companyid = cu.companyid)
                                                JOIN {user} u ON (lit.userid = u.id)
                                                WHERE lit.companyid = :companyid
                                                AND lit.courseid = :courseid
                                                AND lit.timeenrolled IS NOT NULL
                                                AND lit.timecompleted IS NULL
                                                $suspendedsql
                                                $departmentsql",
                                                array('companyid' => $company->id, 'courseid' => $course->id));
            $courses[$id]->enrolled = $enrolled;

            // count the completed users
            $completed = $DB->count_records_sql("SELECT COUNT(lit.id)
                                                 FROM {local_iomad_track} lit
                                                 JOIN {company_users} cu ON (lit.userid = cu.userid AND lit.companyid = cu.companyid)
                                                 JOIN {user} u ON (lit.userid = u.id)
                                                 WHERE lit.companyid = :companyid
                                                 AND lit.courseid = :courseid
                                                 AND lit.timecompleted IS NOT NULL
                                                 $suspendedsql
                                                 $departmentsql",
                                                 array('companyid' => $company->id, 'courseid' => $course->id));
            $courses[$id]->completed = $completed;
        }

        return $courses;
    }

    /**
     * Get user completion info for a course
     *
     * Parameters - $departmentid = int;
     *              $courseid = int;
     *              $page = int;
     *              $perpade = int;
     *
     * Return array();
     **/
    public static function get_user_course_completion_data($searchinfo, $courseid, $page=0, $perpage=0, $completiontype=0, $showhistoric=false) {
        global $DB;

        $companyid = iomad::get_my_companyid(context_system::instance());
        $company = new company($companyid);

        // Get the full company tree as we may need it.
        $topcompanyid = $company->get_topcompanyid();
        $topcompany = new company($topcompanyid);
        $companytree = $topcompany->get_child_companies_recursive();
        $parentcompanies = $company->get_parent_companies_recursive();

        // Strip out people from companies above here.
        if (!empty($parentcompanies)) {
            $companyusql = " AND u.id NOT IN (
                            SELECT userid FROM {company_users}
                            WHERE companyid IN (" . implode(',', array_keys($parentcompanies)) ."))";
        } else {
            $companyusql = "";
        }

        $completiondata = new stdclass();

        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

        $temptablename = 'tmp_'.uniqid();
        list($dbman, $table) = self::populate_temporary_users($temptablename, $searchinfo);

        // Set up the temporary table for all the completion information to go into.
        $tempcomptablename = 'tmp_'.uniqid();

        // Deal with completion types.
        if (!empty($completiontype)) {
            if ($completiontype == 1) {
                $completionsql = " AND cc.timeenrolled > 0 AND cc.timestarted = 0 ";
            } else if ($completiontype == 2 ) {
                $completionsql = " AND cc.timestarted > 0 AND cc.timecompleted IS NULL ";
            } else if ($completiontype == 3 ) {
                $completionsql = " AND cc.timecompleted IS NOT NULL  ";
            }
        } else {
            $completionsql = "";
        }

        // Deal with parent company managers
        if (!empty($parentcompanies)) {
            $userfilter = " AND u.id NOT IN (
                             SELECT userid FROM {company_users}
                             WHERE companyid IN (" . implode(',', array_keys($parentcompanies)) . "))";
        } else {
            $userfilter = "";
        }

        // Populate the temporary completion table.
        list($compdbman, $comptable) = self::populate_temporary_completion($tempcomptablename, $temptablename, $courseid, $showhistoric);

        // Get the user details.
        $shortname = addslashes($course->shortname);
        $countsql = "SELECT cc.id,
                     cc.timeenrolled AS timeenrolled,
                     cc.timestarted,
                     cc.timecompleted,
                     cc.finalscore ";
        $selectsql = "SELECT DISTINCT u.id as uid,
                      u.firstname AS firstname,
                      u.lastname AS lastname,
                      u.email AS email,
                      '{$shortname}' AS coursename,
                      '$courseid' AS courseid,
                      cc.timeenrolled AS timeenrolled,
                      cc.timestarted AS timestarted,
                      cc.timecompleted AS timecompleted,
                      cc.certsource as certsource,
                      d.name as department,
                      cc.finalscore as result ";
        $fromsql = " FROM {user} u
                     JOIN {".$tempcomptablename."} cc ON (u.id = cc.userid)
                     JOIN {company_users} du ON (u.id = du.userid)
                     JOIN {department} d ON (du.departmentid = d.id)
                    WHERE $searchinfo->sqlsearch
                    AND cc.userid = u.id
                    AND u.id = cc.userid
                    AND du.userid = u.id
                    AND d.id = du.departmentid
                    AND du.companyid = :companyid
                    AND cc.courseid = $courseid
                    $companyusql
                    $completionsql $userfilter";

        $searchinfo->searchparams['courseid'] = $courseid;
        $searchinfo->searchparams['companyid'] = $companyid;
        $users = $DB->get_records_sql($selectsql.$fromsql.$searchinfo->sqlsort, $searchinfo->searchparams, $page * $perpage, $perpage);
        $countusers = $DB->get_records_sql($countsql.$fromsql.$searchinfo->sqlsort, $searchinfo->searchparams);
        $numusers = count($countusers);

        $returnobj = new stdclass();
        $returnobj->users = $users;
        $returnobj->totalcount = $numusers;

        $dbman->drop_table($comptable);
        $dbman->drop_table($table);

        return $returnobj;
    }

    /**
     * Get all users completion info regardless of course
     *
     * Parameters - $departmentid = int;
     *              $page = int;
     *              $perpade = int;
     *
     * Return array();
     **/
    public static function get_all_user_course_completion_data($searchinfo, $page=0, $perpage=0, $completiontype=0, $showhistoric=false) {
        global $DB, $USER;

        $companyid = iomad::get_my_companyid(context_system::instance());
        $company = new company($companyid);

        // Get the full company tree as we may need it.
        $topcompanyid = $company->get_topcompanyid();
        $topcompany = new company($topcompanyid);
        $companytree = $topcompany->get_child_companies_recursive();
        $parentcompanies = $company->get_parent_companies_recursive();

        // Strip out people from companies above here.
        if ($parentcompanies) {
            $companyusql = " AND u.id NOT IN (
                            SELECT userid FROM {company_users}
                            WHERE companyid IN (" . implode(',', array_keys($parentcompanies)) ."))";
        } else {
            $companyusql = "";
        }

        $completiondata = new stdclass();

        // Create a temporary table to hold the userids.
        $temptablename = 'tmp_'.uniqid();
        list($dbman, $table) = self::populate_temporary_users($temptablename, $searchinfo);

        // Set up the temporary table for all the completion information to go into.
        $tempcomptablename = 'tmp_'.uniqid();

        // Deal with completion types.
        if (!empty($completiontype)) {
            if ($completiontype == 1) {
                $completionsql = " AND cc.timeenrolled > 0 AND cc.timestarted = 0 ";
            } else if ($completiontype == 2 ) {
                $completionsql = " AND cc.timestarted > 0 AND cc.timecompleted IS NULL ";
            } else if ($completiontype == 3 ) {
                $completionsql = " AND cc.timecompleted IS NOT NULL  ";
            }
        } else {
            $completionsql = "";
        }

        // Deal with parent company managers
        if (!empty($parentcompanies)) {
            $userfilter = " AND u.id NOT IN (
                             SELECT userid FROM {company_users}
                             WHERE companyid IN (" . implode(',', array_keys($parentcompanies)) . "))";
        } else {
            $userfilter = "";
        }

        // Populate the temporary completion table.
        list($compdbman, $comptable) = self::populate_temporary_completion($tempcomptablename, $temptablename, 0, $showhistoric);

        // Get the user details.
        $countsql = "SELECT cc.id AS id ";
        $selectsql = "
                SELECT
                cc.id,
                u.id AS uid,
                u.firstname AS firstname,
                u.lastname AS lastname,
                u.email AS email,
                co.shortname AS coursename,
                co.id AS courseid,
                cc.timeenrolled AS timeenrolled,
                cc.timestarted AS timestarted,
                cc.timecompleted AS timecompleted,
                cc.finalscore AS result,
                d.name AS department";
        $fromsql = " FROM {user} u
                    JOIN {".$tempcomptablename."} cc ON (u.id = cc.userid)
                    JOIN {company_users} du ON (u.id = du.userid)
                    JOIN {department} d ON (du.departmentid = d.id)
                    JOIN {course} co ON (cc.courseid = co.id)

                WHERE $searchinfo->sqlsearch
                AND du.companyid = :companyid
                $companyusql
                $completionsql $userfilter
                $searchinfo->sqlsort";
        $searchinfo->searchparams['companyid'] = $companyid;

        $users = $DB->get_records_sql($selectsql.$fromsql, $searchinfo->searchparams, $page * $perpage, $perpage);
        $countusers = $DB->get_records_sql($countsql.$fromsql, $searchinfo->searchparams);
        $numusers = count($countusers);

        /*foreach ($users as $id => $user) {
            $gradeitem = $DB->get_record('grade_items', array('itemtype' => 'course', 'courseid' => $user->courseid));
            $grade = $DB->get_record('grade_grades', array('itemid' => $gradeitem->id, 'userid' => $user->uid));
            if ($grade) {
                $user->result = $grade->finalgrade;
            }
        }*/
        $returnobj = new stdclass();
        $returnobj->users = $users;
        $returnobj->totalcount = $numusers;

        $compdbman->drop_table($comptable);
        $dbman->drop_table($table);

        return $returnobj;
    }
}
