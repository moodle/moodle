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

class report_userquizzes extends reportbase implements report {

    /**
     * @param object $report Report object
     * @param object $reportproperties Report properties object
     */
    public function __construct($report, $reportproperties) {
        parent::__construct($report);
        $this->parent = false;
        $this->columns = array('userfield' => ['userfield'] ,'userquizzes' => array('totalquizs', 'notattemptedquizs', 'inprogressquizs', 'completedquizs', 'finishedquizs', 'totaltimespent', 'numviews'));
        $this->components = array('columns', 'filters', 'permissions', 'plot');
        $this->courselevel = true;
        $this->basicparams = array(['name' => 'courses']);
        $this->filters = array('users');
        $this->orderable = array('fullname', 'notattemptedquizs', 'inprogressquizs',
            'completedquizs', 'finishedquizs', 'totaltimespent', 'numviews');
        $this->defaultcolumn = 'u.id';
        $this->excludedroles = array("'student'");
    }
    function init() {
      global $DB;
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
        $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $this->params['studentroleid'] = $studentroleid;
        $this->params['roleid'] = $studentroleid;
    }
    function count() {
      $this->sql = "SELECT COUNT(DISTINCT u.id) ";
    }

    function select() {
        $courseid = $this->params['filter_courses'];  
        $this->sql = "SELECT DISTINCT u.id, u.id AS userid, CONCAT(u.firstname, ' ', u.lastname) AS fullname, c.id AS course ";
        if (!empty($this->selectedcolumns)) {
            if (in_array('totalquizs', $this->selectedcolumns)) {
                $this->sql .= ", 'totalquizs' ";
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
                      JOIN {context} con ON c.id = con.instanceid AND ra.contextid= con.id AND con.contextlevel=50
                      JOIN {role} AS rl ON rl.id = ra.roleid AND rl.shortname = 'student'
                      JOIN {user} AS u ON u.id = ue.userid";
      parent::joins();
    }

    function where() {
        global $DB;
        $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $this->params['studentroleid'] = $studentroleid;
        $this->params['roleid'] = $studentroleid;
        $this->sql .=" WHERE c.visible = 1 AND ra.roleid = :roleid AND ra.contextid = con.id
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
        global $DB;
        $userid = isset($this->params['filter_users']) ? $this->params['filter_users'] : array();
        if (!empty($userid) && $userid != '_qf__force_multiselect_submission') {
            is_array($userid) ? $userid = implode(',', $userid) : $userid;
            $this->params['userid'] = $userid;
            $this->sql .= " AND u.id IN (:userid)";
        }
        if ($this->params['filter_courses'] <> SITEID) {
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
    public function column_queries($columnname, $quizid, $courseid = null) {
        $where = " AND %placeholder% = $quizid";
        $filtercourseid = $this->params['filter_courses'] ? $this->params['filter_courses'] : SITEID; 
         
        switch ($columnname) {
            case 'inprogressquizs':
                $identy = 'u1.id';
                $query = " SELECT COUNT(t.coursemoduleid) as inprogressquizs FROM (SELECT cm.id As coursemoduleid 
                        FROM {quiz_attempts} qat
                        JOIN {course_modules} cm ON cm.instance = qat.quiz
                        AND cm.visible = 1 AND cm.deletioninprogress = 0
                        JOIN {course} c ON c.id = cm.course AND c.visible = 1
                        JOIN {modules} m on m.id=cm.module AND m.name = 'quiz'
                        JOIN {user} u1 ON u1.id = qat.userid 
                        WHERE c.visible = 1 AND qat.state = 'inprogress'
                        AND c.id IN ($filtercourseid) $where ) AS t";
                break;
            case 'finishedquizs':
                $identy = 'qat.userid';
                $query = " SELECT COUNT(DISTINCT qat.quiz) AS finishedquizs
                             FROM {quiz_attempts} qat
                             JOIN {course_modules} cm ON cm.instance = qat.quiz AND cm.visible=1 AND cm.deletioninprogress = 0
                             JOIN {course} c2 ON c2.id = cm.course
                             JOIN {modules} m on m.id = cm.module AND m.name = 'quiz'
                            WHERE c2.visible = 1 AND qat.state = 'finished' AND c2.visible = 1  AND c2.id IN ($filtercourseid) $where";
            break;
            case 'completedquizs':
                $identy = 'cmc.userid';
                $query = " SELECT COUNT(DISTINCT cmc.id) AS completedquizs
                             FROM {course_modules} cm
                             JOIN {course} c2 ON c2.id = cm.course
                             JOIN {modules} m on m.id = cm.module AND m.name = 'quiz' 
                             JOIN {course_modules_completion} AS cmc ON cmc.coursemoduleid = cm.id
                             WHERE c2.visible = 1 AND cmc.completionstate > 0 AND cm.visible = 1 AND cm.deletioninprogress = 0 AND c2.id IN ($filtercourseid) $where ";
            break;
            case 'notattemptedquizs':
                $identy = 'u1.id';
                $query = "  SELECT COUNT(DISTINCT cm.instance) AS notattemptedquizs
                             FROM {course_modules} cm
                             JOIN {modules} m ON m.id = cm.module AND m.name = 'quiz' AND cm.visible = 1 AND cm.deletioninprogress = 0
                            WHERE cm.course IN ($filtercourseid) 
                                AND cm.instance NOT IN (SELECT qat.quiz 
                                        FROM {quiz_attempts} qat
                                        JOIN {quiz} q ON qat.quiz = q.id
                                        JOIN {user} u1 ON u1.id = qat.userid
                                        WHERE 1 =1 AND q.course IN ($filtercourseid) $where) ";
            break;
            case 'totaltimespent':
                $identy = 'mt.userid';
                $query = " SELECT SUM(mt.timespent)  AS totaltimespent
                          FROM {block_ls_modtimestats} as mt JOIN {course_modules} cm ON cm.id = mt.activityid JOIN {modules} m ON m.id = cm.module WHERE m.name = 'quiz' AND mt.courseid IN ($filtercourseid) $where ";
            break;
            case 'numviews':
                $identy = 'lsl.userid';
                $query = "SELECT COUNT(DISTINCT lsl.id) AS numviews 
                                        FROM {logstore_standard_log} lsl 
                                        JOIN {course_modules} cm ON cm.id = lsl.contextinstanceid 
                                        JOIN {modules} m ON m.id = cm.module 
                                        WHERE m.name = 'quiz' AND lsl.crud = 'r' AND lsl.contextlevel = 70 AND lsl.anonymous = 0 AND cm.course IN ($filtercourseid) $where";
            break; 
                      
            default:
                return false;
                break;
        }
        $query = str_replace('%placeholder%', $identy, $query);
        return $query;
    }
}
