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
require_once($CFG->libdir . '/completionlib.php');
use block_learnerscript\local\querylib;
use block_learnerscript\local\reportbase;
use block_learnerscript\report;
use block_learnerscript\local\ls as ls;

class report_quizzparticipation extends reportbase implements report {

    public function __construct($report, $reportproperties) {
        global $USER;
        parent::__construct($report);
        $this->columns = array('quizfield' => ['quizfield'], 'quizzparticipationcolumns' => array('username', 'state','finalgrade','submitteddate'));
        if(isset($this->role) && $this->role == 'student') {
            $this->parent = true;
        } else {
            $this->parent = false;
        }
        if (is_siteadmin() || $this->role != 'student') {
            $this->basicparams = [['name' => 'courses']];
        }
        $this->components = array('columns', 'filters', 'permissions', 'plot');
        $this->courselevel = false;
        $this->orderable = array('username', 'state','finalgrade','submitteddate');
        $this->defaultcolumn = "concat(q.id, '-', c.id, '-', ra.userid)";
    }

    public function init() {
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
        $this->sql  = "SELECT COUNT(DISTINCT (concat(q.id, '-', c.id, '-', ra.userid))) ";
    }

    function select() {
        $this->sql = "SELECT DISTINCT(concat(q.id, '-', c.id, '-', ra.userid)),q.id, q.name as name, m.name as modname, cm.id AS activityid , ra.userid AS userid, q.course AS courseid, u.username as username";
        
        parent::select();
    }

    function from() {
        if(empty($this->params['filter_status']) || $this->params['filter_status'] == 'all') {
            $this->sql    .= " FROM {modules} as m
                               JOIN {course_modules} as cm ON cm.module = m.id
                               JOIN {quiz} as q ON q.id = cm.instance
                               JOIN {course} as c ON c.id = cm.course
                               JOIN {context} AS ctx ON c.id = ctx.instanceid
                               JOIN {role_assignments} as ra ON ctx.id = ra.contextid 
                               JOIN {role} as r on r.id=ra.roleid AND r.shortname='student'
                               JOIN {user} as u ON u.id =ra.userid
                               LEFT JOIN {quiz_attempts} qa ON qa.quiz = q.id AND qa.userid = ra.userid";
        } else if($this->params['filter_status'] == 'inprogress') {
            $this->sql   .= "   FROM {quiz} q
                                JOIN {course_modules} as cm ON cm.instance = q.id
                                JOIN {modules} m ON m.id = cm.module
                                JOIN {course} as c ON c.id = cm.course
                                JOIN {enrol} AS e ON c.id = e.courseid AND e.status = 0
                                JOIN {user_enrolments} AS ue ON ue.enrolid = e.id AND ue.status = 0
                                JOIN {context} AS ctx ON c.id = ctx.instanceid
                                JOIN {role_assignments} as ra ON ctx.id = ra.contextid
                                JOIN {role} as r on r.id=ra.roleid AND r.shortname='student'
                                JOIN {user} as u ON u.id =ra.userid
                                LEFT JOIN {quiz_attempts} qa ON qa.quiz = q.id AND qa.userid = ra.userid";
        } else if($this->params['filter_status'] == 'completed') {
            $this->sql   .= "   FROM {quiz} q
                                JOIN {course_modules} as cm ON cm.instance = q.id
                                JOIN {modules} m ON m.id = cm.module
                                JOIN {course} as c ON c.id = cm.course
                                JOIN {context} AS ctx ON c.id = ctx.instanceid
                                JOIN {role_assignments} as ra ON ctx.id = ra.contextid
                                JOIN {role} as r on r.id=ra.roleid AND r.shortname='student'
                                JOIN {user} as u ON u.id =ra.userid
                                JOIN {course_modules_completion} as cmc ON cmc.coursemoduleid = cm.id";
        } else if($this->params['filter_status'] == 'notattempted') {
            $this->sql   .= " FROM {quiz} q
                              JOIN {course} c ON c.id = q.course
                              JOIN {course_modules} cm ON cm.instance = q.id
                              JOIN {modules} m ON m.id = cm.module
                              JOIN {context} AS ctx ON c.id = ctx.instanceid
                              JOIN {role_assignments} as ra ON ctx.id = ra.contextid
                              JOIN {role} as r on r.id=ra.roleid AND r.shortname='student'
                              JOIN {user} as u ON u.id =ra.userid";
        }
    }

    function joins() {
      parent::joins();
    }

    function where() {
        if(empty($this->params['filter_status']) || $this->params['filter_status'] == 'all') {
            $this->sql    .= " WHERE c.visible = 1 AND cm.visible = 1 AND cm.deletioninprogress = 0 
                                AND m.visible = 1 AND m.name = 'quiz'";
        } else if($this->params['filter_status'] == 'inprogress') {
            $this->sql   .= " WHERE qa.state = 'inprogress' AND qa.userid = ra.userid AND m.name='quiz'
                                AND cm.visible = 1 AND cm.deletioninprogress = 0 AND c.visible = 1";
        } else if($this->params['filter_status'] == 'completed') {
            $this->sql   .= "  WHERE cmc.userid = ra.userid AND m.name='quiz'
                                AND cm.visible = 1 AND cm.deletioninprogress = 0 AND cmc.completionstate > 0 AND c.visible = 1";
        } else if($this->params['filter_status'] == 'notattempted') {
            $this->sql   .= " WHERE cm.visible = 1
                               AND c.visible = 1 AND cm.deletioninprogress = 0 AND m.name = 'quiz'
                               AND ra.userid NOT IN ( SELECT qat.userid
                                                  FROM {quiz_attempts} qat
                                                  JOIN {quiz} q1 ON qat.quiz = q1.id 
                                                 WHERE q1.id = ".$this->params['filter_quiz']." AND q1.course = ".$this->params['filter_courses'].")
                              ";
        }
        if(isset($this->params['filter_quiz']) && $this->params['filter_quiz']){
            $this->sql .= " AND q.id=".$this->params['filter_quiz'];
        }
        $this->sql .= " AND u.id >2";
        parent::where();
    }

    function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $this->searchable = array('q.name','c.fullname','u.username');
            $statsql = array();
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);           
            $this->sql .= " AND ($fields) ";
        }
    }

    function filters() {        
        if ((!is_siteadmin() || $this->scheduling) && !(new ls)->is_manager()) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND c.id IN ($this->rolewisecourses) ";
            } 
        }
        if (!empty($this->params['filter_courses']) && $this->params['filter_courses'] <> SITEID  && !$this->scheduling) {
            $this->sql .= " AND cm.course = :filter_courses";
        }
        if ($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->sql .= " AND ra.timemodified BETWEEN :ls_fstartdate AND :ls_fenddate ";
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
        }
        if(isset($this->params['filter_quiz']) && $this->params['filter_quiz']){
            $this->sql .= " AND q.id=".$this->params['filter_quiz'];
        }
    }
    function groupby() {
        $this->sql .= " GROUP BY q.id, c.id, ra.userid,q.id, q.name, m.name, cm.id, ra.userid, q.course, u.username";
    }

    public function get_rows($quizs) {
        return $quizs;
    }
    public function column_queries($columnname, $quizid) {
        global $DB;
    }
}
