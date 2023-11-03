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
use block_learnerscript\local\reportbase;
use block_learnerscript\local\querylib;
use block_learnerscript\local\ls as ls;

class report_users extends reportbase {
    /**
     * @param object $report Report object
     * @param object $reportproperties Report properties object
     */
    public function __construct($report, $reportproperties) {
        global $USER;
        parent::__construct($report, $reportproperties);
        $this->components = array('columns', 'conditions', 'ordering', 'permissions', 'filters', 'plot');
        $this->parent = true;
        $this->columns = array('userfield' => array('userfield'), 'usercolumns' => array('enrolled', 'inprogress',
            'completed', 'grade', 'badges', 'progress', 'status'));
        $this->orderable = array('fullname', 'email', 'enrolled', 'inprogress', 'completed','grade','progress',
                            'badges');
        $this->filters = array('users');
        $this->defaultcolumn = 'u.id';
        $this->excludedroles = array("'student'");

    }
    function count() {
      $this->sql  = " SELECT COUNT(DISTINCT u.id) ";
    }

    function select() {
      $this->sql = " SELECT DISTINCT u.id , CONCAT(u.firstname,' ',u.lastname) AS fullname, u.*";
      parent::select();
    }
    
    function from() {
      $this->sql .= " FROM {user} as u";
    }

    function joins() {
      $this->sql .= " JOIN {role_assignments} ra ON ra.userid = u.id
                      JOIN {context} AS ctx ON ctx.id = ra.contextid
                      JOIN {course} c ON c.id = ctx.instanceid
                      JOIN {enrol} e ON e.courseid = c.id AND e.status = 0
                      JOIN {user_enrolments} ue on ue.enrolid = e.id AND ue.userid = ra.userid AND ue.status = 0
                      JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                 LEFT JOIN {course_completions} cc ON cc.course = ctx.instanceid AND cc.userid = u.id AND cc.timecompleted > 0";

      parent::joins();
    }

    function where() {
        $this->sql .= " WHERE u.confirmed = 1 AND u.deleted = 0 AND u.id > 2";
        if ($this->conditionsenabled) {
            $conditions = implode(',', $this->conditionfinalelements);
            if (empty($conditions)) {
                return array(array(), 0);
            }
            $this->params['lsconditions'] = $conditions;
            $this->sql .= " AND u.id IN ( :lsconditions )";
        }

        if (!is_siteadmin($this->userid)  && !(new ls)->is_manager($this->userid)) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND c.id IN ($this->rolewisecourses) ";
            } else {
                return array(array(), 0);
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
        global $DB;
        if (isset($this->params['filter_users'])
            && $this->params['filter_users'] >0
            && $this->params['filter_users'] != '_qf__force_multiselect_submission') {
            $userid = $this->params['filter_users'];
          $this->params['userid'] = $userid;
            $this->sql .= " AND u.id IN (:userid) ";
        }
        if ($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
            $this->sql .= " AND u.timecreated BETWEEN :ls_fstartdate AND :ls_fenddate";
        }
    }

    function groupby() {
        global $CFG;
        if ($CFG->dbtype != 'sqlsrv') {
            $this->sql .= " GROUP BY u.id ";
        }
    }

    public function get_rows($users) {
        return $users;
    }
    public function column_queries($column, $userid){
        $where = " AND %placeholder% = $userid";
        if (!is_siteadmin($this->userid) && !(new ls)->is_manager($this->userid, $this->contextlevel, $this->role)) {
            if ($this->rolewisecourses != '') {
                $coursefilter = " AND c.id IN ($this->rolewisecourses) ";
            } 
        }else{
          $coursefilter = "";
        }
        switch ($column) {
            case 'enrolled':
                $identy = "ue.userid";
                $query = "SELECT COUNT(DISTINCT c.id) AS enrolled 
                          FROM {user_enrolments} ue   
                          JOIN {enrol} e ON ue.enrolid = e.id AND e.status = 0
                          JOIN {role_assignments} ra ON ra.userid = ue.userid
                          JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                          JOIN {context} AS ctx ON ctx.id = ra.contextid
                          JOIN {course} c ON c.id = ctx.instanceid AND  c.visible = 1 
                          WHERE ue.status = 0 $where $coursefilter ";
                break;
            case 'inprogress':
                $identy = "ue.userid";
                $query = "SELECT (COUNT(DISTINCT c.id) - COUNT(DISTINCT cc.id)) AS inprogress 
                          FROM {user_enrolments} ue   
                          JOIN {enrol} e ON ue.enrolid = e.id AND e.status = 0
                          JOIN {role_assignments} ra ON ra.userid = ue.userid
                          JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                          JOIN {context} AS ctx ON ctx.id = ra.contextid
                          JOIN {course} c ON c.id = ctx.instanceid AND  c.visible = 1 
                     LEFT JOIN {course_completions} cc ON cc.course = ctx.instanceid AND cc.userid = ue.userid AND cc.timecompleted > 0
                         WHERE 1 = 1 $where $coursefilter ";
                break;
            case 'completed':
                $identy = "cc.userid";
                $query = "SELECT COUNT(DISTINCT cc.course) AS completed 
                          FROM {user_enrolments} ue   
                          JOIN {enrol} e ON ue.enrolid = e.id AND e.status = 0
                          JOIN {role_assignments} ra ON ra.userid = ue.userid
                          JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                          JOIN {context} AS ctx ON ctx.id = ra.contextid
                          JOIN {course} c ON c.id = ctx.instanceid AND  c.visible = 1 
                          JOIN {course_completions} cc ON cc.course = ctx.instanceid AND cc.userid = ue.userid AND cc.timecompleted > 0
                          WHERE ue.status = 0 $where $coursefilter ";
                break;
            case 'progress':
                $identy = "ra.userid";
                $query = "SELECT ROUND((COUNT(distinct cc.course) / COUNT(DISTINCT c.id)) *100, 2) as progress 
                            FROM {user_enrolments} ue   
                            JOIN {enrol} e ON ue.enrolid = e.id AND e.status = 0
                            JOIN {role_assignments} ra ON ra.userid = ue.userid
                            JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                            JOIN {context} AS ctx ON ctx.id = ra.contextid
                            JOIN {course} c ON c.id = ctx.instanceid AND  c.visible = 1 
                       LEFT JOIN {course_completions} cc ON cc.course = ctx.instanceid AND cc.userid = ue.userid 
                             AND cc.timecompleted > 0 WHERE ue.status = 0 $where $coursefilter";
                break;
            case 'badges':
                $identy = "bi.userid";
                $query = "SELECT COUNT(bi.id) AS badges FROM {badge_issued} as bi 
                          JOIN {badge} as b ON b.id = bi.badgeid
                         WHERE  bi.visible = 1 AND b.status != 0
                          AND b.status != 2 AND b.status != 4 
                           $where ";
                break;
            case 'grade':
                 $identy = "gg.userid";
                 $query = "SELECT CONCAT(ROUND(SUM(gg.finalgrade), 2),' / ', ROUND(SUM(gi.grademax), 2)) AS grade 
                           FROM {grade_grades} AS gg
                           JOIN {grade_items} AS gi ON gi.id = gg.itemid
                           JOIN {course_completions} AS cc ON cc.course = gi.courseid
                           JOIN {course} AS c ON cc.course = c.id AND c.visible=1
                          WHERE gi.itemtype = 'course' AND cc.course = gi.courseid
                            AND cc.timecompleted IS NOT NULL 
                            AND gg.userid = cc.userid 
                             $where $coursefilter ";
                break;
            default:
                return false;
                break;
        }
        $query = str_replace('%placeholder%', $identy, $query);
        return $query;
    }

}
