<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License AS published by
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

/** LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @subpackage learnerscript
 * @author: sreekanth
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\querylib;
use block_learnerscript\local\reportbase;
use block_learnerscript\local\ls as ls;
use block_learnerscript\report;

class report_userassignments extends reportbase implements report {
    /**
     * [__construct description]
     * @param [type] $report           [description]
     * @param [type] $reportproperties [description]
     */
    public function __construct($report, $reportproperties) {
        parent::__construct($report);
        $this->parent = false;
        $this->courselevel = true;
        $this->components = array('columns', 'filters', 'permissions', 'plot');
        $columns = ['total', 'inprogress', 'notyetstarted', 'completed', 'totaltimespent', 'numviews','submitted','highestgrade','lowestgrade'];
        $this->columns = ['userfield' => array('userfield'), 'userassignments' => $columns];
        $this->basicparams = array(['name' => 'courses']);
        $this->filters = array('users');
        $this->orderable = array('fullname', 'notyetstarted', 'inprogress', 'completed', 'totaltimespent', 'numviews', 'submitted', 'highestgrade', 'lowestgrade');
        $this->defaultcolumn = 'u.id';
        $this->excludedroles = array("'student'");
    }
    function init() {
        global $DB;
        if(!isset($this->params['filter_courses'])){
            $this->initial_basicparams('courses');
            $coursefilter = array_keys($this->filterdata);
            $this->params['filter_courses'] = array_shift($coursefilter);
        }
        if (!$this->scheduling && isset($this->basicparams) && !empty($this->basicparams)) {
            $basicparams = array_column($this->basicparams, 'name');
            foreach ($basicparams AS $basicparam) {
                if (empty($this->params['filter_' . $basicparam])) {
                    return false;
                }
            }
        }
        $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $this->params['roleid'] = $studentroleid;
    }
    function count() {
        $this->sql = "SELECT COUNT(DISTINCT u.id) ";
    }

    function select() {
        $courseid = $this->params['filter_courses'];
        $this->sql = "SELECT DISTINCT u.id, u.id AS userid, CONCAT(u.firstname,' ', u.lastname) AS fullname, c.id AS courseid ";
        if (!empty($this->selectedcolumns)) {
            if (in_array('total', $this->selectedcolumns)) {
                $this->sql .= ", 'total' ";
            }
        }
        
        parent::select();
    }

    function from() {
        $this->sql .= " FROM {course} AS c";
    }

    function joins() {
        $this->sql .= " JOIN {enrol} AS e ON c.id = e.courseid AND e.status = 0
                        JOIN {user_enrolments} AS ue ON ue.enrolid = e.id AND ue.status = 0
                        JOIN {role_assignments} AS ra ON ra.userid = ue.userid
                        JOIN {context} con ON c.id = con.instanceid
                        JOIN {role} AS rl ON rl.id = ra.roleid AND rl.shortname = 'student'
                        JOIN {user} AS u ON u.id = ue.userid";
        parent::joins();
    }

    function where() {
      
        $userid = isset($this->params['filter_users']) ? $this->params['filter_users'] : array();
        $this->sql .="  WHERE  c.visible = 1 AND ra.roleid = :roleid AND ra.contextid =con.id
                         AND u.confirmed = 1 AND u.deleted = 0";

        if ((!is_siteadmin() || $this->scheduling) && !(new ls)->is_manager()) {
            if ($this->rolewisecourses != '') {
                $this->params['rolewisecourses'] = $this->rolewisecourses;
                $this->sql .= " AND c.id IN ($this->rolewisecourses) ";
            }
        }
        parent::where();
    }

    function search() {
      global $DB;
        if (isset($this->search) && $this->search) {
            $this->searchable = array("CONCAT(u.firstname, ' ', u.lastname)", "u.email");
            $statsql = array();
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);          
            $this->sql .= " AND ($fields) ";
        }
    }

    function filters() {
        $userid = isset($this->params['filter_users']) ? $this->params['filter_users'] : array();
        if (!empty($userid) && $userid != '_qf__force_multiselect_submission') {
            is_array($userid) ? $userid = implode(',', $userid) : $userid;
            $this->params['userid'] = $userid;
            $this->sql .= " AND u.id IN (:userid)";
        }
        if ($this->params['filter_courses'] <> SITEID) {
            $courseid = $this->params['filter_courses'];
            $this->sql .= " AND c.id IN (:filter_courses)";
        }
        if ($this->ls_startdate > 0 && $this->ls_enddate) {
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
            $this->sql .= " AND ra.timemodified BETWEEN :ls_fstartdate AND :ls_fenddate ";
        }
    }

    function groupby() {

    }
    /**
     * [get_rows description]
     * @param  array  $users [description]
     * @return [type]        [description]
     */
    public function get_rows($users = array()) {
        return $users;
    }
    public function column_queries($columnname, $assignid, $courseid = null) {
        $where = " AND %placeholder% = $assignid";
        $filtercourseid = isset($this->params['filter_courses']) ? $this->params['filter_courses'] : SITEID;

        switch ($columnname) {
            case 'notyetstarted':
                $identy = 'ra.userid';
                $query = "SELECT COUNT(DISTINCT cm.instance) AS notyetstarted
                                  FROM {course_modules} AS cm
                                  JOIN {modules} AS m ON m.id = cm.module
                                 WHERE cm.visible = 1 AND cm.deletioninprogress = 0 AND m.name = 'qbassign'
                                   AND cm.course IN ( SELECT DISTINCT c.id FROM {course} c
                                                        JOIN {enrol} e ON e.courseid = c.id AND e.status = 0
                                                        JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.status = 0
                                                        JOIN {role_assignments} ra ON ra.userid = ue.userid
                                                        JOIN {role} r ON r.id =ra.roleid AND r.shortname = 'student' 
                                                       WHERE c.visible = 1 AND e.courseid IN ($filtercourseid) $where)
                                   AND cm.instance NOT IN ( SELECT qbassignment FROM {qbassign_submission} asub 
                                                                JOIN {role_assignments} ra ON ra.userid = asub.userid 
                                                             WHERE asub.status = 'submitted' $where)
                                   AND cm.instance NOT IN (SELECT cm.instance
                                                             FROM {course_modules} AS cm
                                                             JOIN {course} AS c ON c.id = cm.course
                                                             JOIN {modules} AS m ON m.id = cm.module
                                                             JOIN {course_modules_completion} AS cmc 
                                                               ON cmc.coursemoduleid = cm.id 
                                                             JOIN {role_assignments} ra ON ra.userid = cmc.userid
                                                            WHERE cm.visible = 1 AND cm.deletioninprogress = 0 AND c.visible = 1
                                                              AND m.name = 'qbassign' AND cmc.completionstate <> 0  AND cm.course IN ($filtercourseid) $where
                                                              )  AND cm.course IN ($filtercourseid) ";
                
                break;
            case 'inprogress':
                $identy = 'u1.id';
                $query = "SELECT COUNT(DISTINCT cm.id) AS inprogress
                               FROM {course_modules} AS cm
                               JOIN {course} AS c ON c.id = cm.course
                               JOIN {modules} AS m ON m.id = cm.module
                               JOIN {qbassign_submission} AS asub on asub.qbassignment = cm.instance 
                               JOIN {user} u1 ON u1.id = asub.userid
                                AND asub.status = 'submitted'
                              WHERE cm.visible = 1 AND c.visible = 1 AND m.name = 'qbassign'
                                AND cm.instance NOT IN
                                    (SELECT cm.instance
                                       FROM {course_modules} AS cm
                                       JOIN {course} AS c ON c.id = cm.course
                                       JOIN {modules} AS m ON m.id = cm.module
                                       JOIN {course_modules_completion} AS cmc ON cmc.coursemoduleid = cm.id 
                                       JOIN {user} u2 ON u2.id = cmc.userid
                                      WHERE cm.visible = 1 AND cm.deletioninprogress = 0 AND c.visible = 1 AND m.name = 'qbassign' AND cm.course IN ($filtercourseid) AND cmc.completionstate > 0 $where)  AND cm.course IN ($filtercourseid) $where ";
            break;
            case 'completed':
                $identy = 'cmc.userid';
                $query = " SELECT COUNT(cmc.id) AS completed
                               FROM {course_modules} AS cm
                               JOIN {course} AS c ON c.id = cm.course
                               JOIN {modules} AS m ON m.id = cm.module
                               JOIN {course_modules_completion} AS cmc ON cmc.coursemoduleid = cm.id
                              WHERE cm.visible = 1 AND cm.deletioninprogress = 0 AND m.name = 'qbassign'
                                AND c.visible = 1 AND cmc.completionstate > 0
                                 AND cm.course IN ($filtercourseid) $where ";
            break;
            case 'submitted':
                $identy = 'sub.userid';
                $query = " SELECT COUNT(sub.id) AS submitted FROM {qbassign_submission} AS sub
                                JOIN {qbassign} a ON a.id = sub.qbassignment
                              WHERE sub.status='submitted' AND a.course IN ($filtercourseid) $where ";
            break;
            case 'highestgrade':
                $identy = 'gg.userid';
                $query = "SELECT MAX(gg.finalgrade) AS highestgrade FROM {grade_grades} AS gg
                            JOIN {grade_items} AS gi ON gg.itemid = gi.id
                            JOIN {course_modules} AS cm ON gi.iteminstance = cm.instance
                            WHERE gi.itemmodule = 'qbassign' AND cm.course IN ($filtercourseid) $where ";
            break;
            case 'lowestgrade':
                $identy = 'gg.userid';
                $query = "SELECT MIN(gg.finalgrade) AS lowestgrade FROM {grade_grades} AS gg
                            JOIN {grade_items} AS gi ON gg.itemid = gi.id
                            JOIN {course_modules} AS cm ON gi.iteminstance = cm.instance
                            WHERE gi.itemmodule = 'qbassign' AND cm.course IN ($filtercourseid) $where ";
            break;
            case 'totaltimespent':
                $identy = 'mt.userid';
                $query = " SELECT SUM(mt.timespent) AS totaltimespent FROM {block_ls_modtimestats} AS mt JOIN {course_modules} cm ON cm.id = mt.activityid JOIN {modules} m ON m.id = cm.module WHERE m.name = 'qbassign' AND cm.course IN ($filtercourseid) $where ";
            break;
            case 'numviews':
                $identy = 'lsl.userid';
                $query = "SELECT COUNT(DISTINCT lsl.id) AS numviews 
                                        FROM {logstore_standard_log} lsl 
                                        JOIN {course_modules} cm ON cm.id = lsl.contextinstanceid 
                                        JOIN {modules} m ON m.id = cm.module 
                                        WHERE m.name = 'qbassign' AND lsl.crud = 'r' AND lsl.contextlevel = 70 AND lsl.anonymous = 0 AND cm.course IN ($filtercourseid) $where";
            break; 
                      
            default:
                return false;
                break;
        }
        $query = str_replace('%placeholder%', $identy, $query);
        return $query;
    }
}
