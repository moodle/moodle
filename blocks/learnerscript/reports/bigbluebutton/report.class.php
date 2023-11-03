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
 * @author: <jahnavi@eabyas.in>
 * @date: 2020
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\querylib;
use block_learnerscript\local\reportbase;
use block_learnerscript\local\ls as ls;
use block_learnerscript\report;

class report_bigbluebutton extends reportbase implements report {
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
        $columns = ['session', 'course', 'timestart', 'duration', 'activestudents', 'inactivestudents'];
        $this->columns = ['bigbluebuttonfields' => $columns];
        $this->orderable = array('session', 'course', 'duration', 'activestudents'); 
        $this->basicparams = array(['name' => 'courses']);
        $this->defaultcolumn = 'bbb.id';
        $this->excludedroles = array("'student'");
    }
    function init() {
        global $DB;
        if (!isset($this->params['filter_courses'])) {
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
        $this->sql = "SELECT COUNT(DISTINCT bbb.id) ";
    }

    function select() {
        $this->sql = "SELECT bbb.id, bbb.name AS session, c.id AS courseid, c.fullname AS course, bbb.openingtime AS timestart, (bbb.closingtime - bbb.openingtime) AS duration, 
            (SELECT COUNT(DISTINCT bbbl.userid) 
            FROM {user} u 
            JOIN {bigbluebuttonbn_logs} bbbl ON bbbl.userid = u.id
            JOIN {user_enrolments} ue ON ue.userid = bbbl.userid AND bbbl.log = 'Join'
            JOIN {enrol} e ON e.id = ue.enrolid 
            JOIN {role_assignments} ra ON ra.userid = ue.userid
            JOIN {context} ct ON ct.id = ra.contextid
            JOIN {role} rl ON rl.id = ra.roleid AND rl.shortname = 'student'
            WHERE bbbl.bigbluebuttonbnid = bbb.id AND ct.instanceid = c.id 
            AND u.confirmed = 1 AND u.deleted = :deleted) AS activestudents,
            cm.id AS activityid ";
        $this->params['deleted'] = 0;
        parent::select();
    }

    function from() {
        $this->sql .= " FROM {bigbluebuttonbn} bbb ";
    }

    function joins() {
        $this->sql .= " JOIN {course} c ON c.id = bbb.course 
                        JOIN {course_modules} cm ON cm.instance = bbb.id 
                        JOIN {modules} m ON m.id = cm.module AND m.name = 'bigbluebuttonbn'";
        parent::joins();
    }

    function where() {
        $userid = isset($this->params['filter_users']) ? $this->params['filter_users'] : array();
        $this->sql .= " WHERE 1 = 1 ";

        if ((!is_siteadmin() || $this->scheduling) && !(new ls)->is_manager()) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND bbb.course IN ($this->rolewisecourses) ";
            }
        }
        parent::where();
    }

    function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $this->searchable =array("bbb.name", "c.fullname");
            $statsql = array();
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);          
            $this->sql .= " AND ($fields) ";
        }
    }

    function filters() { 
        if (isset($this->params['filter_courses']) && $this->params['filter_courses'] > SITEID) {
            $this->sql .= " AND bbb.course IN (:filter_courses) ";
        }
        if ($this->ls_startdate > 0 && $this->ls_enddate) {
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
            $this->sql .= " AND bbb.timecreated BETWEEN :ls_fstartdate AND :ls_fenddate ";
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
    public function column_queries($columnname, $activityid, $courseid = null) {
        $where = " AND %placeholder% = $activityid";
        $filtercourseid = $this->params['filter_courses'];

        switch ($columnname) {      
            default:
                return false;
                break;
        }
        $query = str_replace('%placeholder%', $identy, $query);
        return $query;
    }
}
