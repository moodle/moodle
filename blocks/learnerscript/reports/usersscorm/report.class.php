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
 * LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\querylib;
use block_learnerscript\local\reportbase;
use block_learnerscript\local\ls as ls;

class report_usersscorm extends reportbase {
    /**
     * @param object $report Report object
     * @param object $reportproperties Report properties object
     */
    public function __construct($report, $reportproperties) {
        global $USER;
        parent::__construct($report);
        $this->components = array('columns', 'conditions', 'ordering', 'permissions', 'filters', 'plot');
        $this->parent = false;
        $this->columns = array('userfield' => ['userfield'] , 'usersscormcolumns' => array('inprogress',
            'completed', 'notattempted', 'total', 'lastaccess', 'firstaccess', 'totaltimespent'));
        $this->basicparams = array(['name' => 'courses']);
        $this->courselevel = true;
        $this->filters = array('users');
        $this->orderable = array('fullname', 'inprogress', 'completed', 'notattempted', 'totaltimespent', 'firstaccess', 'lastaccess');
        $this->searchable = array("CONCAT(u.firstname, ' ', u.lastname)", "u.email");
        $this->defaultcolumn = 'u.id';
        $this->excludedroles = array("'student'");

    }
    public function init() {
        if(!isset($this->params['filter_courses'])){
            $this->initial_basicparams('courses');
            $fcourses = array_keys($this->filterdata);
            $this->params['filter_courses'] = array_shift($fcourses);
        }
        if (!$this->scheduling && isset($this->basicparams) && !empty($this->basicparams)) {
            $basicparams = array_column($this->basicparams, 'name');
            foreach ($basicparams as $basicparam) {
                if (empty($this->params['filter_' . $basicparam])) {
                    return false;
                }
            }
        }
        $this->categoriesid = isset($this->params['filter_coursecategories']) ? $this->params['filter_coursecategories'] : 0; 
    }
    public function count() {
       $this->sql = "SELECT COUNT(DISTINCT u.id)";
    }

    public function select() {
      $this->sql = "SELECT DISTINCT u.id , CONCAT(u.firstname,' ',u.lastname) AS fullname, c.id AS course ";
      parent::select();
    }

    public function from() {
      $this->sql .= " FROM {user} u
                        JOIN {user_enrolments} AS ue ON ue.userid = u.id AND ue.status = 0
                        JOIN {enrol} AS e ON ue.enrolid = e.id AND e.status = 0 
                        JOIN {course} c ON c.id = e.courseid
                        JOIN {context} con ON c.id = con.instanceid
                        JOIN {role_assignments} ra ON ra.userid = u.id
                        JOIN {role} AS rl ON rl.id = ra.roleid AND rl.shortname = 'student'
                        LEFT JOIN {scorm_scoes_track} sst ON sst.userid = u.id";

    }
    public function joins() {
      parent::joins();
    }

    public function where() {
        global $DB;
        $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $this->params['studentroleid'] = $studentroleid;
        $this->sql .= " WHERE ra.roleid = :studentroleid AND ra.contextid = con.id
                        AND u.confirmed = 1 AND u.deleted = 0 ";
        if ((!is_siteadmin() || $this->scheduling) && !(new ls)->is_manager()) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND c.id IN ($this->rolewisecourses) ";
            }
        }
        parent::where();
    }

    public function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $statsql = array();
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);           
            $this->sql .= " AND ($fields) ";
        }
    }

    public function filters() {
        global $DB, $CFG;
        $userid = isset($this->params['filter_users']) ? $this->params['filter_users'] : array();
        if (!empty($userid) && $userid != '_qf__force_multiselect_submission') {
            is_array($userid) ? $userid = implode(',', $userid) : $userid;
            $this->params['userid'] =$userid;
            $this->sql .= " AND u.id IN (:userid)";
        }
        if ($this->params['filter_courses'] <> SITEID) {
            $this->sql .= " AND c.id IN (:filter_courses)";
        }
        if ($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
            if ($CFG->dbtype == 'pgsql') {
                $this->sql .= " AND sst.element = 'x.start.time' AND sst.value::INTEGER BETWEEN :ls_fstartdate AND :ls_fenddate ";
            } else {
                $this->sql .= " AND sst.element = 'x.start.time' AND sst.value BETWEEN :ls_fstartdate AND :ls_fenddate ";
            }
        }
    }
    public function groupby() {

    }

    /**
     * @param  array $users users
     * @return array $data users courses information
     */
    public function get_rows($users) {
        return $users;
    }

    public function column_queries($columnname, $userid) {
        global $DB;
        $coursesql  = (new querylib)->get_learners($userid,'');
        $where = " AND %placeholder% = $userid";
        $filtercourseid = isset($this->params['filter_courses']) ? $this->params['filter_courses'] : SITEID;
        switch ($columnname) {
            case 'inprogress':
                $identy = 'st.userid';
                $query  = "SELECT COUNT(DISTINCT s.id) AS inprogress 
                                FROM {scorm} as s
                                JOIN {scorm_scoes_track} st ON st.scormid = s.id
                                JOIN {course_modules} AS cm ON cm.instance = s.id AND cm.visible =1 AND cm.deletioninprogress = 0
                                JOIN {role_assignments} AS ra ON st.userid = ra.userid
                                JOIN {role} as r on r.id = ra.roleid AND r.shortname='student'
                                JOIN {context} AS ctx ON ctx.id = ra.contextid AND ctx.instanceid =cm.course
                                JOIN {course} as c ON c.id = ctx.instanceid  AND c.visible =1
                                JOIN {modules} AS m ON m.id = cm.module AND m.name = 'scorm'
                                WHERE s.id NOT IN
                                    (SELECT s.id
                                       FROM {scorm} as s
                                       JOIN {course_modules} as cm ON cm.instance = s.id
                                       JOIN {course} as c ON c.id = cm.course
                                       JOIN {modules} as m ON m.id = cm.module
                                       JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id AND cmc.completionstate > 0
                                      WHERE cm.visible = 1 AND cm.deletioninprogress = 0 AND c.visible = 1 AND m.name = 'scorm' AND cmc.userid = st.userid AND cm.course IN ($coursesql))
                                AND s.course IN ($coursesql) AND s.course = $filtercourseid $where ";
                break;
            case 'notattempted':
                $identy = 'ue.userid';
                $query  = "SELECT COUNT(DISTINCT cm.instance) AS notattempted 
                            FROM {course} AS c
                            JOIN {enrol} AS e ON c.id = e.courseid
                            JOIN {user_enrolments} AS ue ON ue.enrolid = e.id
                            JOIN {role_assignments} AS ra ON ra.userid = ue.userid AND ra.roleid = 5
                            JOIN {context} AS con ON con.contextlevel = 50 AND c.id = con.instanceid
                            JOIN {course_modules} AS cm ON cm.course = c.id
                            JOIN {modules} AS m ON m.id = cm.module
                            WHERE con.id = ra.contextid AND cm.visible = 1 AND cm.deletioninprogress = 0 AND m.name = 'scorm' AND c.visible = 1
                            AND cm.instance NOT IN (SELECT st.scormid FROM {scorm_scoes_track}
                            st WHERE st.element = 'x.start.time' AND st.userid = ue.userid) AND cm.course IN ($coursesql)
                            AND cm.instance NOT IN (SELECT DISTINCT s.id
                                    FROM {scorm} as s
                                    JOIN {course_modules} as cm ON cm.instance = s.id
                                    JOIN {modules} as m ON m.id = cm.module
                                    JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id AND cmc.completionstate > 0
                                    JOIN {role_assignments} AS ra ON cmc.userid = ra.userid
                                    JOIN {role} as r on r.id=ra.roleid AND r.shortname='student'
                                    JOIN {context} AS ctx ON ctx.id = ra.contextid AND ctx.instanceid =cm.course
                                    JOIN {course} as c ON c.id = ctx.instanceid
                                    WHERE cm.visible = 1 AND cm.deletioninprogress = 0 AND c.visible = 1 AND m.name = 'scorm' AND cm.course IN ($coursesql) AND cmc.userid = ue.userid) AND cm.course =  $filtercourseid $where ";
            break;
            case 'completed':
                $identy = 'cmc.userid';
                $query  = "SELECT COUNT(DISTINCT s.id) AS completed 
                            FROM {scorm} as s
                            JOIN {course_modules} as cm ON cm.instance = s.id
                            JOIN {modules} as m ON m.id = cm.module
                            JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id AND cmc.completionstate > 0
                            JOIN {role_assignments} AS ra ON cmc.userid = ra.userid
                            JOIN {role} as r on r.id=ra.roleid AND r.shortname='student'
                            JOIN {context} AS ctx ON ctx.id = ra.contextid AND ctx.instanceid =cm.course
                            JOIN {course} as c ON c.id = ctx.instanceid
                            WHERE cm.visible = 1 AND cm.deletioninprogress = 0 AND c.visible = 1 AND m.name = 'scorm' AND cm.course IN ($coursesql) AND s.course = $filtercourseid $where ";
            break;
            case 'firstaccess':
                $identy = 'sst.userid';
                $query ="SELECT MIN(sst.value) AS firstaccess  FROM {scorm_scoes_track} sst 
                                JOIN {scorm} s ON s.id = sst.scormid 
                                WHERE sst.element = 'x.start.time' AND s.course = $filtercourseid $where ";
            break;
            case 'totalscorms':
                $identy = 'ue.userid';
                $query = "SELECT COUNT(DISTINCT cm.id) AS totalscorms 
                            FROM {course} AS c
                            JOIN {enrol} AS e ON c.id = e.courseid AND e.status = 0
                            JOIN {user_enrolments} AS ue ON ue.enrolid = e.id AND ue.status = 0
                            JOIN {role_assignments} AS ra ON ra.userid = ue.userid AND ra.roleid = 5
                            JOIN {context} AS con ON con.contextlevel = 50 AND c.id = con.instanceid
                            JOIN {course_modules} AS cm ON cm.course = c.id
                            JOIN {modules} AS m ON m.id = cm.module
                            JOIN {scorm} AS s ON s.course = c.id
                            LEFT JOIN {scorm_scoes_track} AS st ON st.scormid = s.id
                            JOIN {scorm_scoes} ss ON ss.scorm = s.id
                            WHERE con.id = ra.contextid AND cm.visible = 1 AND cm.deletioninprogress = 0 AND c.visible = 1 AND m.name = 'scorm' AND c.id = $filtercourseid $where ";
            break;
            case 'totaltimespent':
                $identy = 'mt.userid';
                $query = "SELECT SUM(mt.timespent) AS totaltimespent  FROM {block_ls_modtimestats} AS mt 
                         JOIN {course_modules} cm ON cm.id = mt.activityid 
                         JOIN {modules} m ON m.id = cm.module WHERE m.name='scorm' AND mt.courseid = $filtercourseid $where ";
            break;
            case 'numviews':
                $identy = 'lsl.userid';
                $query = "SELECT COUNT(DISTINCT lsl.id) AS numviews 
                                        FROM {logstore_standard_log} lsl 
                                        JOIN {course_modules} cm ON cm.id = lsl.contextinstanceid 
                                        JOIN {modules} m ON m.id = cm.module 
                                        WHERE m.name = 'scorm' AND lsl.crud = 'r' AND lsl.contextlevel = 70 AND lsl.anonymous = 0 AND cm.course IN ($filtercourseid) $where";
            break;

            default:
            return false;
                break;
        }
        $query = str_replace('%placeholder%', $identy, $query);
        return $query;
    }
}
