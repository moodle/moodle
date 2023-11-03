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

class report_myassignments extends reportbase implements report {
	/**
	 * @param object $report Report object
	 * @param object $reportproperties Report properties object
	 */
	public function __construct($report, $reportproperties) {
		parent::__construct($report, $reportproperties);
		$this->columns = array('assignmentfield' => ['assignmentfield'], 'myassignments' => array('gradepass', 'grademax', 'finalgrade', 'noofsubmissions','status', 'highestgrade', 'lowestgrade', 'noofdaysdelayed', 'overduedate'));
		if (isset($this->role) && $this->role == 'student') {
			$this->parent = true;
		} else {
			$this->parent = false;
		}
		if ($this->role != 'student' || is_siteadmin($this->userid)) {
			$this->basicparams = [['name' => 'users']];
		}
		$this->courselevel = false;
		$this->components = array('columns', 'filters', 'permissions', 'plot');
		$this->filters = array('courses');
		$this->orderable = array('name','gradepass', 'grademax', 'finalgrade', 'noofsubmissions', 'highestgrade', 'lowestgrade','course');
        $this->searchable = array('c.fullname', 'a.name');

    $this->defaultcolumn = 'a.id';
	}
    public function init() {
        if($this->role != 'student' && !isset($this->params['filter_users'])){
            $this->initial_basicparams('users');
            $fusers = array_keys($this->filterdata);
            $this->params['filter_users'] = array_shift($fusers);
        }
        $this->courseid = isset($this->params['filter_courses']) ? $this->params['filter_courses'] : array();
        $userid = isset($this->params['filter_users']) && $this->params['filter_users'] > 0
                    ? $this->params['filter_users'] : $this->userid;
        $this->params['userid'] = $userid;
        if (!$this->scheduling && isset($this->basicparams) && !empty($this->basicparams)) {
            $basicparams = array_column($this->basicparams, 'name');
            foreach ($basicparams as $basicparam) {
                if (empty($this->params['filter_' . $basicparam])) {
                    return false;
                }
            }
        }
    }
    function count() {
        $this->sql = "SELECT count(DISTINCT a.id) ";
    }

    function select() {
        $userid = isset($this->params['filter_users']) && $this->params['filter_users'] > 0
                    ? $this->params['filter_users'] : $this->userid;
        $this->sql = "SELECT DISTINCT a.id, a.name as name, asb.timemodified AS overduedate, cm.course AS courseid, c.fullname AS course, ra.userid AS userid, a.duedate, asb.status as submissionstatus,
                      m.id AS module, m.name AS type, cm.id AS activityid ";
        if (!empty($this->selectedcolumns)) {
            if (in_array('noofdaysdelayed', $this->selectedcolumns)) {
                $this->params['userid'] = $userid;
                   $this->sql .= ", (SELECT cmc.timemodified 
                                FROM {course_modules_completion} as cmc
                                WHERE cm.id = cmc.coursemoduleid  AND cmc.userid = :userid) AS noofdaysdelayed";
            }
        }
        parent::select();
    }

    function from() {
        $this->sql .= " FROM {modules} as m";
    }

    function joins() {
       $userid = isset($this->params['filter_users']) && $this->params['filter_users'] > 0
                    ? $this->params['filter_users'] : $this->userid;
        $this->sql .= "   JOIN {course_modules} as cm ON cm.module = m.id
                          JOIN {qbassign} as a ON a.id = cm.instance
                          JOIN {course} as c ON c.id = cm.course
                          JOIN {context} AS ctx ON c.id = ctx.instanceid
                          JOIN {role_assignments} as ra ON ctx.id = ra.contextid AND ra.userid = $userid ";

        if(empty($this->params['filter_status']) || $this->params['filter_status'] == 'all') {
          $this->sql .= " LEFT JOIN {qbassign_submission} as asb ON asb.qbassignment = a.id AND asb.userid = $userid";
        } else if($this->params['filter_status'] == 'inprogress') {
          $this->sql .= "JOIN {qbassign_submission} as asb ON asb.qbassignment = a.id AND asb.userid = $userid";
        } else if($this->params['filter_status'] == 'completed') {
          $this->sql .= " JOIN {qbassign_submission} as asb ON asb.qbassignment = a.id AND asb.userid = $userid
                          JOIN {course_modules_completion} as cmc ON cmc.coursemoduleid = cm.id AND cmc.userid = $userid";
        }
        parent::joins();
    }

    function where() {

      $this->sql .=" WHERE c.visible = 1 AND cm.visible = 1 AND cm.deletioninprogress = 0 AND m.name = 'qbassign' AND m.visible = 1";
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
        parent::where();
    }

    function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $this->searchable = array('a.name', 'c.fullname');
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
        global $USER;
        $where = " AND %placeholder% = $assignid";
        $userid = isset($this->params['filter_users']) ? $this->params['filter_users'] : $this->userid;
        switch ($columnname) {
            case 'gradepass':
                $identy = 'gi.iteminstance';
                $query = "SELECT gi.gradepass AS gradepass 
                            FROM {grade_items} gi 
                           WHERE gi.itemmodule = 'qbassign' $where ";
                break;
            case 'grademax':
                $identy = 'a1.id';
                $query = "SELECT a1.grade AS grademax 
                            FROM {qbassign} a1 
                           WHERE 1 = 1 $where ";
                break;
            case 'finalgrade':
                $identy = 'gi.iteminstance';
                $query = "SELECT gg.finalgrade AS finalgrade 
                            FROM {grade_grades} gg  
                            JOIN {grade_items} gi ON gg.itemid = gi.id  
                           WHERE 1 = 1 AND gi.itemmodule = 'qbassign' AND gg.userid = $userid $where  ";
            break;
            case 'highestgrade':
                $identy = 'gi.iteminstance';
                $query = "SELECT MAX(gg.finalgrade) AS highestgrade 
                            FROM {grade_grades} gg  
                            JOIN {grade_items} gi ON gg.itemid = gi.id  
                           WHERE 1 = 1 AND gi.itemmodule = 'qbassign' $where  ";
            break;
            case 'lowestgrade':
                $identy = 'gi.iteminstance';
                $query = "SELECT MIN(gg.finalgrade)  AS lowestgrade 
                            FROM {grade_grades} gg  
                            JOIN {grade_items} gi ON gg.itemid = gi.id  
                           WHERE 1 = 1 AND gi.itemmodule = 'qbassign' $where  ";
            break;
            case 'noofsubmissions':
                $identy = 'asb.qbassignment';
                $query = "SELECT count(asb.id) AS noofsubmissions FROM {qbassign_submission} asb
                             WHERE asb.status = 'submitted' AND asb.userid = $userid $where ";
            break;
            
            default:
                return false;
                break;
        }
        $query = str_replace('%placeholder%', $identy, $query);
        return $query;
    }
}
