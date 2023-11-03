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
 * @author: jahnavi
 * @date: 2019
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\querylib;
use block_learnerscript\local\reportbase;
use block_learnerscript\local\ls as ls;
use block_learnerscript\report;

class report_attendanceoverview extends reportbase implements report {
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
        $columns = ['date', 'teachercount', 'studentcount'];
        $this->columns = ['attendanceoverview' => $columns];
        $this->basicparams = array(['name' => 'courses']);
        $this->orderable = array('date', 'teachercount', 'studentcount');
        $this->defaultcolumn = 't1.date';
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
    }
    function count() {
        global $DB;
        if ($this->ls_startdate == 0) { 
            $lastmonthdate = new \DateTime('1 month ago');
            $oneMonthAgo = $lastmonthdate->format('Y-m-d');
            $startdate = $oneMonthAgo; 
        } else {
            $firstdate = $DB->get_field_sql("SELECT $this->ls_startdate AS startdate");
            $startdate = userdate($firstdate, '%Y-%m-%d');
        } 
        $lastdate = $DB->get_field_sql("SELECT $this->ls_enddate AS enddate");
        $enddate = userdate($lastdate, '%Y-%m-%d');
        $sdate = strtotime($startdate); 
        $edate = strtotime($enddate);
        $i = 0;
        for ($currentDate = $sdate; $currentDate <= $edate; $currentDate += (86400)) {
            $i++;
            $date = date('d-m-Y', $currentDate);
            $dates[] = $date;
        }
        $total = COUNT($dates);
        $this->sql = " SELECT $total AS total ";
    }

    function select() {
        global $DB, $CFG;
        if ($this->ls_startdate == 0) { 
            $lastmonthdate = new \DateTime('1 month ago');
            $oneMonthAgo = $lastmonthdate->format('Y-m-d');
            $startdate = $oneMonthAgo; 
        } else {
            $firstdate = $DB->get_field_sql("SELECT $this->ls_startdate AS startdate");
            $startdate = userdate($firstdate, '%Y-%m-%d');
        } 
        $lastdate = $DB->get_field_sql("SELECT $this->ls_enddate AS enddate");
        $enddate = userdate($lastdate, '%Y-%m-%d');
        $sdate = strtotime($startdate); 
        $edate = strtotime($enddate);
        $i = 0;
        $query = " ";
        $concatsql = " ";
        if ($this->params['filter_courses'] > SITEID) {
            $concatsql .= " AND lsl.courseid IN (:filter_courses)";
        }

        for ($currentDate = $sdate; $currentDate <= $edate; $currentDate += (86400)) {
            $date = date('d-m-Y', $currentDate);
            $teachersql = "SELECT COUNT(DISTINCT lsl.userid) AS teachercount
                            FROM {logstore_standard_log} lsl 
                            JOIN {user} u ON u.id = lsl.userid 
                            JOIN {role_assignments} ra ON ra.userid = u.id 
                            JOIN {user_enrolments} ue ON ue.userid = ra.userid AND ue.status = 0 
                            JOIN {enrol} e ON e.id = ue.enrolid AND e.status = 0 
                            JOIN {context} ctx ON ctx.id = ra.contextid 
                            JOIN {course} c ON c.id = ctx.instanceid 
                            JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'editingteacher'
                            WHERE lsl.action = 'viewed' $concatsql AND c.id = lsl.courseid"; 
            $studentsql = "SELECT COUNT(DISTINCT lsl.userid) AS studentcount
                            FROM {logstore_standard_log} lsl 
                            JOIN {user} u ON u.id = lsl.userid 
                            JOIN {role_assignments} ra ON ra.userid = u.id 
                            JOIN {user_enrolments} ue ON ue.userid = ra.userid AND ue.status = 0 
                            JOIN {enrol} e ON e.id = ue.enrolid AND e.status = 0 
                            JOIN {context} ctx ON ctx.id = ra.contextid 
                            JOIN {course} c ON c.id = ctx.instanceid 
                            JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                            WHERE lsl.action = 'viewed' $concatsql AND c.id = lsl.courseid";
            if ($CFG->dbtype == 'sqlsrv') {
                $teachersql .= " AND CONVERT(VARCHAR, DATEADD(s, lsl.timecreated, '1970-01-01 00:00:00'), 105) = '" .$date. "'";
                $studentsql .= " AND CONVERT(VARCHAR, DATEADD(s, lsl.timecreated, '1970-01-01 00:00:00'), 105) = '" .$date. "'";
            } else if($CFG->dbtype == 'pgsql') {
                $teachersql .= " AND to_char(to_timestamp(lsl.timecreated), 'dd-mm-YYYY') = '" .$date. "'";
                $studentsql .= " AND to_char(to_timestamp(lsl.timecreated), 'dd-mm-YYYY') = '" .$date. "'";
            } else {
                $teachersql .= " AND FROM_UNIXTIME(lsl.timecreated, '%d-%m-%Y') = '" .$date. "' ";
                $studentsql .= " AND FROM_UNIXTIME(lsl.timecreated, '%d-%m-%Y') = '" .$date. "' ";
            }
               $teachercount = $DB->get_field_sql($teachersql, ['filter_courses' => $this->params['filter_courses']]);
               $studentcount = $DB->get_field_sql($studentsql, ['filter_courses' => $this->params['filter_courses']]);
            $attendancearray[] = "('".$date."',".$teachercount.", ".$studentcount.", " . $currentDate . ")";
            if ($i == 0) {
                $query .= "SELECT '".$date."' AS date, $teachercount AS teachercount, $studentcount AS studentcount, $currentDate AS currentdate";
            } else {
                $query .= " UNION SELECT '".$date."' AS date, $teachercount AS teachercount, $studentcount AS studentcount, $currentDate AS currentdate";
            }
            $i++;
        }
        $userattendance = implode(',', $attendancearray);
        $this->sql = " SELECT t1.date, t1.teachercount, t1.studentcount , t1.currentdate FROM ($query) AS t1 ORDER BY t1.currentdate DESC";
        parent::select();
    }

    function from() { 
        $this->sql .= " ";
    }

    function joins() {
        $this->sql .= " ";
        parent::joins();
    }

    function where() {
        $this->sql .= " ";
        parent::where();
    }

    function search() {
    }

    function filters() {
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
}
