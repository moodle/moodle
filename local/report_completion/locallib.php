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

        // Create a temporary table to hold the userids.
        $temptablename = 'tmp_'.uniqid();
        $dbman = $DB->get_manager();

        // Define table user to be created.
        $table = new xmldb_table($temptablename);
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        $dbman->create_temp_table($table);

        // Deal with parent company managers
        if (!empty($parentcompanies)) {
            $userfilter = " AND userid NOT IN (
                             SELECT userid FROM {company_users}
                             WHERE companyid IN (" . implode(',', array_keys($parentcompanies)) . "))";
        } else {
            $userfilter = "";
        }

        // Populate it.
        $alldepartments = company::get_all_subdepartments($departmentid);
        if (count($alldepartments) > 0 ) {
            // Deal with suspended or not.
            if (empty($showsuspended)) {
                $suspendedsql = " AND userid IN (select id FROM {user} WHERE suspended = 0) ";
            } else {
                $suspendedsql = "";
            }
            $tempcreatesql = "INSERT INTO {".$temptablename."} (userid) SELECT userid from {company_users}
                              WHERE departmentid IN (".implode(',', array_keys($alldepartments)).") $userfilter $suspendedsql";
        } else {
            $tempcreatesql = "";
        }
        $DB->execute($tempcreatesql);

        // All or one course?
        $courses = array();
        if (!empty($courseid)) {
            $courses[$courseid] = new stdclass();
            $courses[$courseid]->id = $courseid;
        } else {
            $courses = company::get_recursive_department_courses($departmentid);
        }

        // We only want the student role.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        // Process them!
        $returnarr = array();
        foreach ($courses as $course) {
            $contextcourse = context_course::instance($course->courseid);
            $courseobj = new stdclass();
            $courseobj->id = $course->courseid;

            $courseobj->numenrolled = $DB->count_records_sql("SELECT COUNT(ue.id) FROM {user_enrolments} ue
                                                   JOIN {enrol} e ON (e.id = ue.enrolid AND e.status = 0)
                                                   JOIN {role_assignments} ra ON (ue.userid = ra.userid)
                                                   JOIN {".$temptablename."} tt ON (ue.userid = tt.userid)
                                                   WHERE e.courseid = :course
                                                   AND ra.roleid = :student
                                                   AND ra.contextid = :coursecontext",
                                                   array('course' => $course->courseid,
                                                         'student' => $studentrole->id,
                                                         'coursecontext' => $contextcourse->id));
            $courseobj->numnotstarted = $DB->count_records_sql("SELECT COUNT(cc.id) FROM {course_completions} cc
                                                   JOIN {role_assignments} ra ON (cc.userid = ra.userid)
                                                   JOIN {".$temptablename."} tt ON (cc.userid = tt.userid)
                                                   WHERE cc.course = :course
                                                   AND ra.roleid = :student
                                                   AND ra.contextid = :coursecontext
                                                   AND cc.timestarted = 0",
                                                   array('course' => $course->courseid,
                                                         'student' => $studentrole->id,
                                                         'coursecontext' => $contextcourse->id));
            $courseobj->numstarted = $DB->count_records_sql("SELECT COUNT(cc.id) FROM {course_completions} cc
                                                   JOIN {role_assignments} ra ON (cc.userid = ra.userid)
                                                   JOIN {".$temptablename."} tt ON (cc.userid = tt.userid)
                                                   WHERE
                                                   cc.course = :course
                                                   AND ra.roleid = :student
                                                   AND ra.contextid = :coursecontext
                                                   AND cc.timestarted != 0",
                                                   array('course' => $course->courseid,
                                                         'student' => $studentrole->id,
                                                         'coursecontext' => $contextcourse->id));
            $courseobj->numcompleted = $DB->count_records_sql("SELECT COUNT(cc.id) FROM {course_completions} cc
                                                   JOIN {role_assignments} ra ON (cc.userid = ra.userid)
                                                   JOIN {".$temptablename."} tt ON (cc.userid = tt.userid)
                                                   WHERE cc.course = :course
                                                   AND ra.roleid = :student
                                                   AND ra.contextid = :coursecontext
                                                   AND cc.timecompleted IS NOT NULL",
                                                   array('course' => $course->courseid,
                                                         'student' => $studentrole->id,
                                                         'coursecontext' => $contextcourse->id));
            $courseobj->historic = $DB->count_records_sql("SELECT COUNT(lct.id) FROM {local_iomad_track} lct
                                                   JOIN {".$temptablename."} tt ON (lct.userid = tt.userid)
                                                   WHERE
                                                   lct.courseid = :course", array('course' => $course->courseid));

            if (!$courseobj->coursename = $DB->get_field('course', 'fullname', array('id' => $course->courseid))) {
                continue;
            }
            $returnarr[$course->courseid] = $courseobj;
        }
        return $returnarr;
    }

    /** 
     * Get users into temporary table
     */
    private static function populate_temporary_users($temptablename, $searchinfo) {
        global $DB;


        // Create a temporary table to hold the userids.
        $dbman = $DB->get_manager();

        // Define table user to be created.
        $table = new xmldb_table($temptablename);
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        $dbman->create_temp_table($table);

        // Populate it.
        $alldepartments = company::get_all_subdepartments($searchinfo->departmentid);
        if (count($alldepartments) > 0 ) {
            $tempcreatesql = "INSERT INTO {".$temptablename."} (userid) SELECT userid from {company_users}
                              WHERE departmentid IN (".implode(',', array_keys($alldepartments)).")";
        } else {
            $tempcreatesql = "";
        }
        $DB->execute($tempcreatesql);

        return array($dbman, $table);
    }

    /** 
     * Get users into temporary table
     */
    private static function populate_temporary_completion($tempcomptablename, $tempusertablename, $courseid=0, $showhistoric=false) {
        global $DB, $USER;

        // Create a temporary table to hold the userids.
        $dbman = $DB->get_manager();

        // Define table user to be created.
        $table = new xmldb_table($tempcomptablename);
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timeenrolled', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timestarted', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timecompleted', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('finalscore', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('certsource', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        $dbman->create_temp_table($table);

        // We only want the student role.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        // Populate it.
        $tempcreatesql = "INSERT INTO {".$tempcomptablename."} (userid, courseid, timeenrolled, timestarted, timecompleted, finalscore, certsource)
                          SELECT ue.userid, e.courseid, ue.timestart, cc.timestarted, cc.timecompleted, gg.finalgrade, 0
                          FROM {".$tempusertablename."} tut
                          JOIN {user_enrolments} ue ON (tut.userid = ue.userid)
                          INNER JOIN {enrol} e ON (ue.enrolid = e.id AND e.status=0)
                          JOIN {course_completions} cc ON (ue.userid = cc.userid AND e.courseid = cc.course)
                          JOIN {role_assignments} ra ON (ue.userid = ra.userid)
                          JOIN {context} c ON (ra.contextid = c.id AND c.contextlevel = 50 AND c.instanceid = e.courseid)
                          LEFT JOIN {grade_items} gi
                          ON (cc.course = gi.courseid
                          AND gi.itemtype = 'course')
                          LEFT JOIN {grade_grades} gg ON (gg.userid = cc.userid AND gi.id = gg.itemid)
                          WHERE ra.roleid = " . $studentrole->id;

        if (!empty($courseid)) {
            $tempcreatesql .= " AND cc.course = ".$courseid;
        }
        $DB->execute($tempcreatesql);

        // Are we also adding in historic data?
        if ($showhistoric) {
        // Populate it.
            // get the current list of populated ids.
            $idlistsql = "SELECT lit2.id FROM {local_iomad_track} lit2 JOIN {".$tempcomptablename."} tt2
                          ON (lit2.courseid = tt2.courseid AND lit2.userid=tt2.userid AND lit2.timecompleted = tt2.timecompleted
                          AND lit2.timestarted = tt2.timestarted AND lit2.finalscore = tt2.finalscore)";
            if (!empty($courseid)) {
            $idlistsql .= " AND lit2.courseid = ".$courseid;
            } 
            $idlist = implode(',', array_keys($DB->get_records_sql($idlistsql)));
            
            $tempcreatesql = "INSERT INTO {".$tempcomptablename."} (userid, courseid, timeenrolled, timestarted, timecompleted, finalscore, certsource)
                              SELECT it.userid, it.courseid, it.timeenrolled, it.timestarted, it.timecompleted, it.finalscore, it.id
                              FROM {".$tempusertablename."} tut, {local_iomad_track} it
                              WHERE tut.userid = it.userid";
            if (!empty($idlist)) {
                $tempcreatesql .= " AND it.id NOT IN (".$idlist.")";
            }
            if (!empty($courseid)) {
                $tempcreatesql .= " AND it.courseid = ".$courseid;
            }
            $DB->execute($tempcreatesql);
        }

        return array($dbman, $table);
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
