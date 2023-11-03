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
use block_learnerscript\local\querylib;
use block_learnerscript\local\reportbase;
use block_learnerscript\report;
use block_learnerscript\local\ls as ls;
use DateTime;

class report_assignstatus extends reportbase implements report {
	/**
	 * @param object $report Report object
	 * @param object $reportproperties Report properties object
	 */
	public function __construct($report, $reportproperties) {
        global $DB;
		parent::__construct($report, $reportproperties);
		$this->columns = array('coursefield' => ['coursefield'],
                                'assignstatus' => array('total', 'completed', 'pending', 'overdue'));
		if (isset($this->role) && $this->role == 'student') {
			$this->parent = true;
		} else {
			$this->parent = false;
		}
		if ($this->role != 'student') {
			$this->basicparams = [['name' => 'users']];
		}
		$this->courselevel = false;
		$this->components = array('columns', 'filters', 'permissions', 'plot');
		$this->filters = array('courses');
		$this->orderable = array('fullname', 'total', 'completed', 'pending', 'overdue');
        $this->defaultcolumn = 'main.id';
	}

    public function init() {
       if($this->role != 'student' && !isset($this->params['filter_users'])){
            $this->initial_basicparams('users');
            $fusers = array_keys($this->filterdata);
            $this->params['filter_users'] = array_shift($fusers);
        }
        if (!$this->scheduling && isset($this->basicparams) && !empty($this->basicparams)) {
            $basicparams = array_column($this->basicparams, 'name');
            foreach ($basicparams as $basicparam) {
                if (empty($this->params['filter_' . $basicparam])) {
                    return false;
                }
            }
        }
        $this->courseid = isset($this->params['filter_courses']) ? $this->params['filter_courses'] : array();
        $userid = isset($this->params['filter_users']) && $this->params['filter_users'] > 0
                    ? $this->params['filter_users'] : $this->userid;
        $this->params['userid'] = $userid;
    }

    public function count() {
        $this->sql = "SELECT COUNT(DISTINCT main.id)";
    }

   public function select() {
        $this->sql = "SELECT DISTINCT main.id ";
         parent::select();
    }

    public function from() {
        $this->sql .= " FROM {course} as main";
    }

    public function joins() {
        $this->sql .= " JOIN {enrol} AS e ON e.courseid = main.id AND e.status = 0 
                        JOIN {user_enrolments} as ue ON ue.enrolid = e.id AND ue.status = 0
                        JOIN {course_modules} as cm ON cm.course = e.courseid 
                        JOIN {context} con ON main.id = con.instanceid
                        JOIN {role_assignments} ra ON ra.userid = ue.userid
                        JOIN {role} AS rl ON rl.id = ra.roleid AND rl.shortname = 'student'   ";
        parent::joins();
    }

    public function where() {
        global $DB;
        $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $this->sql .= " WHERE ra.roleid = :studentroleid AND ra.contextid = con.id AND cm.module = 1 AND cm.visible = :cmvisible AND main.visible = :mainvisible";
        $this->params['studentroleid'] = $studentroleid;
        $this->params['cmvisible'] =1;
        $this->params['mainvisible'] =1;
        if (!is_siteadmin($this->userid) && !(new ls)->is_manager($this->userid, $this->contextlevel, $this->role)) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND main.id IN ($this->rolewisecourses) ";
            }
        } 
        if ($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
            $this->sql .= " AND cm.added BETWEEN :ls_fstartdate AND :ls_fenddate ";
        }
        parent::where();
    }

    public function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
        $statsql = array();
        $this->searchable = array('main.fullname');
        foreach ($this->searchable as $key => $value) {
            $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'", $casesensitive = false, $accentsensitive = true, $notlike = false);
        }
        $fields = implode(" OR ", $statsql);
        $this->sql .= " AND ($fields) ";
        }
    }

    public function filters() {
       if (!empty($this->params['filter_courses']) && $this->params['filter_courses'] <> SITEID  && !$this->scheduling) {
           $this->sql .= " AND main.id IN (:filter_courses) ";
        }
        if ($this->params['filter_users'] > 0) {
            $this->sql .= " AND ue.userid IN (:filter_users) ";
        }
    }
    public function groupby() {

    }

	/**
	 * [get_rows description]
	 * @param  array  $assignments [description]
	 * @param  string $sqlorder    [description]
	 * @return [type]              [description]
	 */
	 public function get_rows($forums = array()) {
        return $forums;
    }
    public function column_queries($columnname, $courseid, $courses = null) { 
        $userid = isset($this->params['userid']) ? $this->params['userid'] : $this->userid;  
        $where = " AND %placeholder% = $courseid";
        switch ($columnname) {
            case 'total':
                $identy = 'c.id';
                $query = "SELECT COUNT(cm.module)  AS total 
                                FROM {course_modules} cm 
                                JOIN {course} c ON c.id = cm.course
                                WHERE cm.module = 1 AND c.visible = 1 AND cm.visible = 1 $where ";
                break;
            case 'completed':
                $identy = 'c.id';
                $query = "SELECT count(cmc.coursemoduleid) AS completed 
                            FROM {course_modules_completion} cmc
                            JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
                            JOIN {course} c ON c.id = cm.course
                            WHERE cmc.completionstate >= 1 AND cmc.coursemoduleid = cm.id AND cmc.userid = $userid AND cm.visible = 1  AND cm.module = 1  AND c.visible = 1 AND cm.visible = 1 $where ";
                break;
            case 'pending':
                $identy = 'c.id';
                $query = "SELECT COUNT(cm.id) AS pending 
                            FROM {enrol} AS e 
                            JOIN {course} c ON c.id = e.courseid
                            JOIN {user_enrolments} ue ON ue.enrolid = e.id 
                            JOIN {course_modules} cm ON cm.course = e.courseid
                            WHERE ue.userid = $userid  AND cm.visible = 1 AND cm.module = 1 
                              AND cm.id NOT IN (SELECT cm.id
                                                  FROM {course_modules_completion} cmc 
                                                  JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
                                                 WHERE cmc.completionstate >= 1 AND cmc.userid = $userid ) $where ";
                break;
            case 'overdue':
                $identy = 'c.id'; 
                $date = new DateTime();
                $timestamp = $date->getTimestamp();
                $query = "SELECT COUNT(a.id) AS overdue 
                            FROM {enrol} e 
                            JOIN {course} c ON c.id = e.courseid
                            JOIN {user_enrolments} ue ON ue.enrolid = e.id 
                            JOIN {course_modules} cm ON cm.course = e.courseid
                            JOIN {qbassign} a ON a.id = cm.instance
                            WHERE ue.userid = $userid AND cm.visible = 1 AND cm.module = 1 AND c.visible = 1 AND ($timestamp - a.duedate) > 0 AND cm.course = c.id  AND a.duedate > 0 $where ";
            break;
            default:
                return false;
                break;
        }
        $query = str_replace('%placeholder%', $identy, $query);
        return $query;
    }
}
