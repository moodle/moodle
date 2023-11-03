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

/** LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @subpackage learnerscript
 * @author: sreekanth
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
require_once $CFG->libdir . '/completionlib.php';
use block_learnerscript\local\reportbase;
use block_learnerscript\report;
use block_learnerscript\local\ls as ls;

class report_assignmentparticipation extends reportbase implements report {
    /**
     * @param object $report Report object
     * @param object $reportproperties Report properties object
     */
    public function __construct($report, $reportproperties) {
        parent::__construct($report, $reportproperties);
        $this->columns = array('assignmentfield' => ['assignmentfield'], 'assignmentparticipationcolumns' => array('username','finalgrade','status', 'noofdaysdelayed', 'duedate','submitteddate'));
        if (isset($this->role) && $this->role == 'student') {
            $this->parent = true;
        } else {
            $this->parent = false;
        }
        $this->basicparams = [['name' => 'courses']];
        $this->courselevel = false;
        $this->components = array('columns', 'filters', 'permissions', 'plot');
        $this->orderable = array('username','finalgrade','status', 'noofdaysdelayed', 'duedate','submitteddate');
        $this->searchable = array('a.name','u.username');

    $this->defaultcolumn = "concat(a.id, '-', c.id, '-', ra.userid)";
    }
    public function init() {
        $this->courseid = isset($this->params['filter_courses']) ? $this->params['filter_courses'] : array();
        $userid = isset($this->params['filter_users']) && $this->params['filter_users'] > 0
                    ? $this->params['filter_users'] : $this->userid;
        $this->params['userid'] = $userid;
    }
    function count() {
        $this->sql = "SELECT count(DISTINCT (concat(a.id, '-', c.id, '-', ra.userid))) ";
    }

    function select() {
        $this->sql = "SELECT DISTINCT concat(a.id, '-', c.id, '-', ra.userid), a.id, a.name as name, asb.timemodified AS overduedate, cm.course AS courseid, c.fullname AS course, ra.userid AS userid, a.duedate as due_date, asb.status as submissionstatus,
                      m.id AS module, m.name AS type, cm.id AS activityid, u.username as username ";
        if (!empty($this->selectedcolumns)) {
            if (in_array('noofdaysdelayed', $this->selectedcolumns)) {
                //$this->params['userid'] = $userid;
                   $this->sql .= ", (SELECT cmc.timemodified 
                                FROM {course_modules_completion} as cmc
                                WHERE cm.id = cmc.coursemoduleid  AND cmc.userid = ra.userid) AS noofdaysdelayed";
            }
        }
        parent::select();
    }

    function from() {
        $this->sql .= " FROM {modules} as m";
    }

    function joins() {
        $this->sql .= "   JOIN {course_modules} as cm ON cm.module = m.id
                          JOIN {qbassign} as a ON a.id = cm.instance
                          JOIN {course} as c ON c.id = cm.course
                          JOIN {context} AS ctx ON c.id = ctx.instanceid
                          JOIN {role_assignments} as ra ON ctx.id = ra.contextid
                          JOIN {role} as r on r.id=ra.roleid AND r.shortname='student'
                          JOIN {user} as u ON u.id = ra.userid ";

        if(empty($this->params['filter_status']) || $this->params['filter_status'] == 'all') {
          $this->sql .= " LEFT JOIN {qbassign_submission} as asb ON asb.qbassignment = a.id AND asb.userid = ra.userid";
        } else if($this->params['filter_status'] == 'inprogress') {
          $this->sql .= "JOIN {qbassign_submission} as asb ON asb.qbassignment = a.id AND asb.userid = ra.userid AND asb.status = 'submitted'";
        } else if($this->params['filter_status'] == 'completed') {
          $this->sql .= " LEFT JOIN {qbassign_submission} as asb ON asb.qbassignment = a.id AND asb.userid = ra.userid
                          JOIN {course_modules_completion} as cmc ON cmc.coursemoduleid = cm.id AND cmc.userid = ra.userid AND cmc.completionstate>0";
        }

        parent::joins();
    }

    function where() {

      $this->sql .=" WHERE c.visible = 1 AND cm.visible = 1 AND cm.deletioninprogress = 0 AND m.name = 'qbassign' AND m.visible = 1 ";
        if ((!is_siteadmin() || $this->scheduling) && !(new ls)->is_manager()) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND c.id IN ($this->rolewisecourses) ";
            }
        }
        if (!empty($this->courseid) && $this->courseid != '_qf__force_multiselect_submission') {
            $courseid = $this->courseid;
            $this->sql .= " AND cm.course = :courseid";
            $this->params['courseid'] = $courseid;
        }
        if ($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->sql .= " AND ra.timemodified BETWEEN :startdate AND :enddate ";
            $this->params['startdate'] = ROUND($this->ls_startdate);
            $this->params['enddate'] = ROUND($this->ls_enddate);
        }
        if(isset($this->params['filter_assignment']) && $this->params['filter_assignment']){
            $this->sql .=" AND a.id = :assignmentid";
            $this->params['assignmentid'] = $this->params['filter_assignment'];
        }
        parent::where();
    }

    function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $this->searchable = array('a.name','u.username');
            $statsql = array();
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);          
            $this->sql .= " AND ($fields) ";
        }
    }

    function filters() {

    }
    function groupby() {
        $this->sql .= " GROUP BY a.id, c.id, ra.userid, a.name, asb.timemodified, cm.course, c.fullname, a.duedate, asb.status, m.id, m.name, cm.id, u.username";
    }
    /**
     * [get_rows description]
     * @param  array  $assignments [description]
     * @param  string $sqlorder    [description]
     * @return [type]              [description]
     */
    public function get_rows($assignments = array(), $sqlorder = '') {
        return $assignments;
    }
    public function column_queries($columnname, $assignid, $courseid = null) {
        global $DB;
    }
}
