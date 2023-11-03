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
 * LearnerScript
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: Arun Kumar M
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\reportbase;
use block_learnerscript\report;
use block_learnerscript\local\querylib;
use block_learnerscript\local\ls as ls;

defined('MOODLE_INTERNAL') || die();
class report_courses extends reportbase implements report {

    public function __construct($report, $reportproperties) {
        global $DB;
        parent::__construct($report, $reportproperties);
        $columns = ['enrolments', 'completed', 'activities', 'competencies', 'progress', 'avggrade',
                    'highgrade', 'lowgrade', 'badges', 'totaltimespent', 'numviews'];
        $this->columns = ['coursefield' => ['coursefield'] ,
                          'coursescolumns' => $columns];

        $coursecolumns = $DB->get_columns('course');
        $usercolumns = $DB->get_columns('user');
        $this->conditions = ['courses' => array_keys($coursecolumns),
                             'user' => array_keys($usercolumns)];

        $this->components = array('columns', 'conditions', 'ordering', 'filters','permissions', 'plot');
        $this->filters = array('coursecategories', 'courses');
        $this->parent = true;
        $this->orderable = array('enrolments', 'completed', 'activities', 'competencies', 'avggrade','progress', 'highgrade', 'lowgrade', 'badges', 'totaltimespent', 'fullname');

        $this->searchable = array('main.fullname', 'cat.name');
        $this->defaultcolumn = 'main.id';
        $this->excludedroles = array("'student'");
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
    public function count() {
       $this->sql = "SELECT COUNT(DISTINCT main.id)";

    }

    public function select() {
        $this->sql = "SELECT DISTINCT main.id, main.*, main.id AS course ";
        parent::select();
    }

    public function from() {
        $this->sql .= " FROM {course} AS main JOIN {course_categories} AS cat ON main.category = cat.id";
    }
    public function joins() {
        parent::joins();
    }

    public function where() {
        $this->sql .= " WHERE main.visible = :visible AND main.id <> :siteid ";
        $this->params['visible'] =1;
        if (!is_siteadmin($this->userid) && !(new ls)->is_manager($this->userid, $this->contextlevel, $this->role)) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND main.id IN ($this->rolewisecourses) ";
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
        if (!empty($this->params['filter_courses']) && $this->params['filter_courses'] <> SITEID  && !$this->scheduling) {           
            $this->sql .= " AND main.id IN (:filter_courses) ";
        }
        if (!empty($this->params['filter_coursecategories'])) {
            $this->sql .= " AND main.category IN (:filter_coursecategories) ";
        }
        if ($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
            $this->sql .= " AND main.timecreated BETWEEN :ls_fstartdate AND :ls_fenddate ";
        }
        if ($this->conditionsenabled) {
            $conditions = implode(',', $this->conditionfinalelements);
            if (empty($conditions)) {
                return array(array(), 0);
            }
            $this->params['lsconditions'] = $conditions;
            $this->sql .= " AND main.id IN ( :lsconditions )";
        } 
    }


    public function groupby() {

    }

    public function get_rows($courses) {
        return $courses;
    }

    public function column_queries($columnname, $courseid, $courses = null) { 
        global $DB;
        if($courses){
            $learnersql  = (new querylib)->get_learners('', $courses);
        }else{
            $learnersql  = (new querylib)->get_learners('', '%courseid%');
        }
        $where = " AND %placeholder% = $courseid";
        switch ($columnname) {
            case 'progress':
                $identy = 'ct.instanceid';
                $query = " SELECT CASE WHEN (SELECT COUNT(DISTINCT ue.userid) 
                             FROM {user_enrolments} ue
                             JOIN {enrol} e ON e.id = ue.enrolid AND e.status = 0 AND ue.status = 0
                             JOIN {role_assignments} ra ON ra.userid = ue.userid
                             JOIN {context} ct ON ct.id = ra.contextid
                             JOIN {role} rl ON rl.id = ra.roleid AND rl.shortname = 'student'
                             JOIN {user} u ON u.id = ue.userid AND u.confirmed = 1 AND u.deleted = 0
                        LEFT JOIN {course_completions} as cc ON cc.course = ct.instanceid AND cc.timecompleted > 0
                            WHERE 1 = 1 $where) = 0 THEN 0 ELSE (SELECT ROUND(((COUNT(DISTINCT cc.userid) * 100)/ COUNT(DISTINCT ue.userid)) , 2) AS progress 
                             FROM {user_enrolments} ue
                             JOIN {enrol} e ON e.id = ue.enrolid AND e.status = 0 AND ue.status = 0
                             JOIN {role_assignments} ra ON ra.userid = ue.userid
                             JOIN {context} ct ON ct.id = ra.contextid
                             JOIN {role} rl ON rl.id = ra.roleid AND rl.shortname = 'student'
                             JOIN {user} u ON u.id = ue.userid AND u.confirmed = 1 AND u.deleted = 0
                        LEFT JOIN {course_completions} as cc ON cc.course = ct.instanceid AND cc.timecompleted > 0
                            WHERE 1 = 1 $where) END ";
                break;
            case 'activities':
                $identy = 'course';
                $query  = "SELECT COUNT(id) AS activities FROM {course_modules} WHERE 1 = 1 AND visible = 1 $where ";
            break;
            case 'enrolments':
                $identy = 'ct.instanceid';
                $query  = "SELECT COUNT(DISTINCT ue.userid) AS enrolled 
                                     FROM {user_enrolments} ue
                                     JOIN {enrol} e ON e.id = ue.enrolid AND e.status = 0 AND ue.status = 0
                                     JOIN {role_assignments} ra ON ra.userid = ue.userid
                                     JOIN {context} ct ON ct.id = ra.contextid
                                     JOIN {role} rl ON rl.id = ra.roleid AND rl.shortname = 'student'
                                     JOIN {user} u ON u.id = ue.userid AND u.confirmed = 1 AND u.deleted = 0
                                    WHERE 1 = 1 $where ";
            break;
            case 'completed':
                $identy = 'ct.instanceid';
                $query ="SELECT COUNT(DISTINCT cc.userid) AS completed 
                                     FROM {user_enrolments} ue
                                     JOIN {enrol} e ON e.id = ue.enrolid AND e.status = 0 AND ue.status = 0
                                     JOIN {role_assignments} ra ON ra.userid = ue.userid
                                     JOIN {context} ct ON ct.id = ra.contextid
                                     JOIN {role} rl ON rl.id = ra.roleid AND rl.shortname = 'student'
                                     JOIN {user} u ON u.id = ue.userid AND u.confirmed = 1 AND u.deleted = 0
                                     JOIN {course_completions} cc ON cc.course = ct.instanceid AND cc.timecompleted > 0 AND cc.userid = ue.userid
                                    WHERE 1 = 1 AND cc.course = e.courseid $where "; 
            break;
            case 'highgrade':
                $identy = 'gi.courseid';
                $query = "SELECT MAX(finalgrade) AS highgrade 
                          FROM {grade_grades} g  
                          JOIN {grade_items} gi ON gi.itemtype = 'course' AND g.itemid = gi.id 
                         WHERE g.finalgrade IS NOT NULL AND g.userid IN ($learnersql) $where ";
            break;
            case 'lowgrade':
                $identy = 'gi.courseid';
                $query = "SELECT MIN(finalgrade) AS lowgrade 
                          FROM {grade_grades} g  
                          JOIN {grade_items} gi ON gi.itemtype = 'course' AND g.itemid = gi.id 
                         WHERE g.finalgrade IS NOT NULL AND g.userid IN ($learnersql) $where ";
            break;
            case 'avggrade':
                $identy = 'gi.courseid';
                $query = "SELECT AVG(finalgrade) AS avggrade 
                          FROM {grade_grades} g 
                          JOIN {grade_items} gi ON gi.itemtype = 'course' AND g.itemid = gi.id 
                         WHERE g.finalgrade IS NOT NULL AND g.userid IN ($learnersql) $where ";
            break;
            case 'badges':
                $identy = 'b.courseid';
                $query = "SELECT COUNT(b.id) AS badges  FROM {badge} b WHERE b.status != 0  AND b.status != 2 $where ";
            break;
            case 'totaltimespent':
                $identy = 'bt.courseid';
                $query = "SELECT SUM(bt.timespent) AS totaltimespent FROM {block_ls_coursetimestats} AS bt 
                           WHERE 1 = 1 AND bt.userid IN ($learnersql) $where ";
            break;
            case 'competencies':
                $identy = 'ccom.courseid';
                $query = " SELECT COUNT(ccom.id)
                            FROM {competency_coursecomp} ccom 
                            WHERE 1 = 1 $where ";
            break;
            default:
            return false;
                break;
        }
        $query = str_replace('%placeholder%', $identy, $query);
        $query = str_replace('%courseid%', $identy, $query);
        return $query;
    }
}
