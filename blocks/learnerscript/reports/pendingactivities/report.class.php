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
 * @author: jahnavi<jahnavi@eabyas.com>
 * @date: 2022
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\querylib;
use block_learnerscript\local\reportbase;
use block_learnerscript\local\ls as ls;
use block_learnerscript\report;
use DateTime;
class report_pendingactivities extends reportbase implements report {
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
        $this->columns = array('activityfield' => ['activityfield'], 
                               'pendingactivities' => array('activityname','course','startdate', 'enddate','attempt'));
        $this->basicparams = array(['name' => 'users']);
        $this->filters = array('courses','activities');
        $this->orderable = array('activityname','course','startdate', 'enddate','attempt');
        $this->defaultcolumn = '';
    }
    function init() {
        global $DB;
        if(!isset($this->params['filter_activities'])){
            $this->initial_basicparams('activities');
            $coursefilter = array_keys($this->filterdata);
            $this->params['filter_activities'] = array_shift($coursefilter);
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
        
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $modules = $DB->get_fieldset_select('modules', 'name', '', array('visible' => 1));
        $coursemodules = $DB->get_records_sql_menu("SELECT md.id, md.name FROM {course_modules} AS cs JOIN {modules} AS md ON cs.module = md.id WHERE cs.visible = :visible", ['visible' => 1]); 
        $aliases =array();
        $activities =array();
        $fields1 =array();
        foreach ($modules as $modulename) {
            if(in_array($modulename, $coursemodules)){
                $aliases[] = $modulename;
                $activities[] = "'$modulename'";
                $fields1[] = "COALESCE($modulename.name,'')"; 
            }          
        }
        $activitynames = implode(',', $activities);
        if (!isset($this->params['filter_courses'])) {
            $this->initial_basicparams('courses');
            $filterdata = array_keys($this->filterdata);
            $this->params['filter_courses'] = array_shift($filterdata);
        }
        $userid = $this->params['filter_users'] ? $this->params['filter_users'] : $this->userid;
        if (isset($this->params['filter_courses']) && $this->params['filter_courses'] > 0) {
            $filters .= " AND main.course = " .$this->params['filter_courses'];
        }
        if (isset($this->params['filter_activities']) && $this->params['filter_activities'] > 0) {
            $filters .= " AND main.id = ".$this->params['filter_activities'];
        }
             $filters .= " AND u.id = $userid";
        $this->sql = "SELECT SUM(totalcount.activitycount) AS total FROM (";

        foreach ($aliases as $alias) {
            if($alias == 'qbassign'){
                $this->sql .= " SELECT COUNT(main.id) AS activitycount
                FROM {course_modules} as main
                JOIN {modules} m ON main.module = m.id                
                JOIN {course} c ON c.id = main.course
                JOIN {enrol} e ON e.courseid = c.id AND e.status = 0
                JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.status = 0
                JOIN {user} u ON u.id = ue.userid 
                JOIN {".$alias."} AS $alias ON $alias.id = main.instance AND m.name = '$alias' AND $alias.duedate < $timestamp
                JOIN {qbassign_submission} AS asb ON asb.qbassignment= $alias.id AND asb.userid =$userid 
                WHERE m.visible = 1 AND m.name IN ($activitynames) AND main.visible = 1 AND asb.status != 'submitted'".$filters;
            }
            if($alias == 'quiz'){
                $this->sql .= " UNION  ALL SELECT COUNT(main.id) AS activitycount
                FROM {course_modules} as main
                JOIN {modules} m ON main.module = m.id
                JOIN {course} c ON c.id = main.course
                JOIN {enrol} e ON e.courseid = c.id AND e.status = 0
                JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.status = 0
                JOIN {user} u ON u.id = ue.userid  
                JOIN {".$alias."} AS $alias ON $alias.id = main.instance AND m.name = '$alias' AND $alias.timeclose < ".$timestamp." 
                WHERE $alias.id 
                NOT IN (SELECT qa.quiz FROM  {quiz_attempts} qa WHERE qa.userid = ".$userid.") AND m.visible = 1 AND m.name IN ($activitynames) AND main.visible = 1 AND $alias.timeclose!=0 AND $alias.timeopen!=0".$filters;
            }
            if($alias == 'scorm'){
                $this->sql .= " UNION  ALL SELECT COUNT(main.id) AS activitycount
                FROM {course_modules} as main
                JOIN {modules} m ON main.module = m.id
                JOIN {course} c ON c.id = main.course
                JOIN {enrol} e ON e.courseid = c.id AND e.status = 0
                JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.status = 0
                JOIN {user} u ON u.id = ue.userid  
                JOIN {".$alias."} AS $alias ON $alias.id = main.instance AND m.name = '$alias' AND $alias.timeclose < ".$timestamp." 
                WHERE $alias.id NOT IN (SELECT scormid FROM {scorm_scoes_track} WHERE userid = ".$userid.") AND $alias.id NOT IN(SELECT cm.module FROM {course_modules_completion} cmc JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid WHERE cmc.userid = ".$userid." AND cmc.completionstate <> 0) AND m.visible = 1 AND m.name IN ($activitynames) AND main.visible = 1 AND $alias.timeclose!=0 AND $alias.timeopen!=0".$filters;
            }
        }
            $this->sql .= ") AS totalcount";
    }

    function select() {
        global $DB;
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $modules = $DB->get_fieldset_select('modules', 'name', '', array('visible' => 1));
        $coursemodules = $DB->get_records_sql_menu("SELECT md.id, md.name FROM {course_modules} AS cs JOIN {modules} AS md ON cs.module = md.id WHERE cs.visible = :visible", ['visible' => 1]); 
        $aliases =array();
        $activities =array();
        $fields1 =array();
        foreach ($modules as $modulename) {
            if(in_array($modulename, $coursemodules)){
                $aliases[] = $modulename;
                $activities[] = "'$modulename'";
                $fields1[] = "COALESCE($modulename.name,'')"; 
            }          
        }
        $activitynames = implode(',', $activities);
        if (!isset($this->params['filter_courses'])) {
            $this->initial_basicparams('courses');
            $filterdata = array_keys($this->filterdata);
            $this->params['filter_courses'] = array_shift($filterdata);
        }
        $userid = $this->params['filter_users'] ? $this->params['filter_users'] : $this->userid;
        if (isset($this->params['filter_courses']) && $this->params['filter_courses'] > 0) {
            $filters .= " AND main.course = " .$this->params['filter_courses'];
        }
        if (isset($this->params['filter_activities']) && $this->params['filter_activities'] > 0) {
            $filters .= " AND main.id = ".$this->params['filter_activities'];
        }
            $filters .= " AND u.id = $userid";
        foreach ($aliases as $alias) {
           
            if($alias == 'qbassign'){
                $this->sql = " SELECT DISTINCT(main.id), m.id AS moduleid, main.instance, 
                                main.course, $alias.name AS activityname, $alias.allowsubmissionsfromdate AS startdate, $alias.duedate as lastdate 
                FROM {course_modules} as main
                JOIN {modules} m ON main.module = m.id
                JOIN {course} c ON c.id = main.course
                JOIN {enrol} e ON e.courseid = c.id AND e.status = 0
                JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.status = 0
                JOIN {user} u ON u.id = ue.userid 
                JOIN {".$alias."} AS $alias ON $alias.id = main.instance AND m.name = '$alias' AND $alias.duedate < $timestamp
                JOIN {qbassign_submission} AS asb ON asb.qbassignment= $alias.id AND asb.userid =$userid WHERE m.visible = 1 AND m.name IN ($activitynames) AND main.visible = 1 AND asb.status != 'submitted'".$filters;
            }
            if($alias == 'quiz'){
                $this->sql .= " UNION SELECT DISTINCT(main.id), m.id AS moduleid, main.instance, 
                                main.course ,$alias.name AS activityname, $alias.timeopen AS startdate, $alias.timeclose AS lastdate 
                FROM {course_modules} as main
                JOIN {modules} m ON main.module = m.id
                JOIN {course} c ON c.id = main.course
                JOIN {enrol} e ON e.courseid = c.id AND e.status = 0
                JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.status = 0
                JOIN {user} u ON u.id = ue.userid  
                JOIN {".$alias."} AS $alias ON $alias.id = main.instance AND m.name = '$alias' AND $alias.timeclose < ".$timestamp." 
                WHERE $alias.id 
                NOT IN (SELECT qa.quiz FROM  {quiz_attempts} qa WHERE qa.userid = ".$userid.") AND m.visible = 1 AND m.name IN ($activitynames) AND main.visible = 1 AND $alias.timeclose!=0 AND $alias.timeopen!=0".$filters;
            }
            if($alias == 'scorm'){
                $this->sql .= " UNION SELECT DISTINCT(main.id), m.id AS moduleid, main.instance, 
                                main.course ,$alias.name AS activityname, $alias.timeopen AS startdate, $alias.timeclose AS lastdate 
                FROM {course_modules} as main
                JOIN {modules} m ON main.module = m.id
                JOIN {course} c ON c.id = main.course
                JOIN {enrol} e ON e.courseid = c.id AND e.status = 0
                JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.status = 0
                JOIN {user} u ON u.id = ue.userid  
                JOIN {".$alias."} AS $alias ON $alias.id = main.instance AND m.name = '$alias' AND $alias.timeclose < ".$timestamp." 
                 WHERE $alias.id NOT IN (SELECT scormid FROM {scorm_scoes_track} WHERE userid = ".$userid.") AND $alias.id NOT IN(SELECT cm.module FROM {course_modules_completion} cmc JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid WHERE cmc.userid = ".$userid." AND cmc.completionstate <> 0) AND m.visible = 1 AND m.name IN ($activitynames) AND main.visible = 1 AND $alias.timeclose!=0 AND $alias.timeopen!=0".$filters;
            }
            
        }
        parent::select();
    }

    function from() {
       
    }

    function joins() {
       
    }

    function where() {
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
