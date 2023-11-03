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
 * @author: sreekanth<sreekanth@eabyas.in>
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\querylib;
use block_learnerscript\local\reportbase;
use block_learnerscript\report;
use block_learnerscript\local\ls as ls;

defined('MOODLE_INTERNAL') || die();
class report_gradedactivity extends reportbase implements report {
    /**
     * [__construct description]
     * @param object $report Report object
     * @param object $reportproperties Report properties object
     */
    public function __construct($report, $reportproperties) {
        global $USER;
        parent::__construct($report, $reportproperties);
        $this->components = array('columns', 'filters', 'permissions', 'calcs', 'plot');
        $columns = ['modulename', 'highestgrade', 'averagegrade', 'lowestgrade', 'totaltimespent', 'numviews'];
        $this->columns = ['activityfield' => ['activityfield'], 'gradedactivity' => $columns];
        $this->courselevel = true;
        $this->basicparams = array(['name' => 'courses']);
        $this->filters = array( 'modules', 'activities');
        $this->parent = true;
        $this->orderable = array('modulename', 'highestgrade', 'averagegrade', 'lowestgrade', 'totaltimespent', 'course');
        $this->defaultcolumn = 'main.id';
        $this->excludedroles = array("'student'");
    }
    function init() {
        if (!isset($this->params['filter_courses'])) {
            $this->initial_basicparams('courses');
            $fcourses = array_keys($this->filterdata);
            $this->params['filter_courses'] =array_shift($fcourses);
        }
        if (!$this->scheduling && isset($this->basicparams) && !empty($this->basicparams)) {
            $basicparams = array_column($this->basicparams, 'name');
            foreach ($basicparams as $basicparam) {
                if (empty($this->params['filter_' . $basicparam])) {
                    return false;
                }
            }
        }
        $this->categoriesid = isset($this->params['filter_coursecategories']) ? $this->params['filter_coursecategories'] : 0; 
    }
    function count() {
        $this->sql   = "SELECT COUNT(DISTINCT main.id) ";
    }

    function select() {
        $this->sql  = "SELECT main.id, cm.id as activityid, m.id AS module, main.itemname as modulename, main.courseid";
        parent::select();
    }

    function from() {
        $this->sql .= " FROM {grade_items} as main";
    }

    function joins() {
        parent::joins();
        $this->sql .= "  JOIN {course_modules} cm ON cm.instance = main.iteminstance AND main.itemtype = 'mod' 
                         JOIN {modules} m ON m.id = cm.module
                         JOIN {course} co ON co.id = cm.course";
    }

    function where() {
        global $DB;
        $modules = $DB->get_fieldset_select('modules', 'name', '', array('visible' => 1));
        foreach ($modules as $modulename) {
            $activities[] = "'$modulename'";
        }
        $activitieslist = implode(',', $activities);

        $this->sql .= " WHERE co.visible = 1 AND cm.visible = 1 AND m.name IN ($activitieslist) AND m.visible = 1 AND m.name = main.itemmodule";

        if ($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
            $this->sql .= " AND cm.added BETWEEN :ls_fstartdate AND :ls_fenddate ";
        }

        if (!is_siteadmin($this->userid) && !(new ls)->is_manager($this->userid, $this->contextlevel, $this->role)) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND cm.course IN ($this->rolewisecourses) ";
            } 
        }
               
        parent::where();
    }

    function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $this->searchable = array('main.itemname', 'co.fullname');
            $statsql = array();
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);          
            $this->sql .= " AND ($fields) ";
        }
    }

    function filters() { 
        if (!empty($this->params['filter_courses']) && $this->params['filter_courses'] <> SITEID  && !$this->scheduling) { 
            $this->sql .= " AND cm.course IN (:filter_courses) ";
        }
        if (!empty($this->params['filter_modules'])) {
            $this->sql .= " AND m.id IN (:filter_modules) ";
        }
        if (!empty($this->params['filter_activities'])) {
            $this->sql .= " AND cm.id IN (:filter_activities) ";
        }
    } 

    function groupby() {
        
    }

    /**
     * @param  array $activites Activites
     * @return array $reportarray Activities information
     */
    public function get_rows($activites) {
        return $activites;
    }

    public function column_queries($column, $activityid, $courseid = null){
        if($courseid){
            $learnersql  = (new querylib)->get_learners('', $courseid);
        }else{
            $learnersql  = (new querylib)->get_learners('', '%courseid%');
        }
        $where = " AND %placeholder% = $activityid";
        $filtercourseids = isset($this->params['filter_courses']) ? $this->params['filter_courses'] : SITEID;
        switch ($column) {
            case 'highestgrade':
                $identy = 'gi.id';
                $query =  "SELECT MAX(finalgrade) AS highestgrade 
                            FROM {grade_grades} as gg  
                            JOIN {grade_items} as gi ON gg.itemid = gi.id AND gi.itemtype = 'mod'
                            JOIN {modules} as m ON gi.itemmodule = m.name
                            WHERE 1 = 1 AND gi.courseid IN ($filtercourseids) $where";
                break;
            case 'averagegrade':
                 $identy = 'gi.id';
                 $query =  "SELECT AVG(finalgrade) AS averagegrade 
                            FROM {grade_grades} as gg  
                            JOIN {grade_items} as gi ON gg.itemid = gi.id AND gi.itemtype = 'mod'
                            JOIN {modules} as m ON gi.itemmodule = m.name
                            WHERE 1 = 1 AND gi.courseid IN ($filtercourseids) $where";
                break;   
            case 'lowestgrade':
                 $identy = 'gi.id';
                 $query =  "SELECT MIN(finalgrade) AS lowestgrade 
                            FROM {grade_grades} as gg  
                            JOIN {grade_items} as gi ON gg.itemid = gi.id AND gi.itemtype = 'mod'
                            JOIN {modules} as m ON gi.itemmodule = m.name
                            WHERE 1 = 1 AND gi.courseid IN ($filtercourseids) $where";
                break;
            case 'totaltimespent':
                $identy = 'gi.id';
                $courseid = 'mt.courseid';
                $query =  "SELECT SUM(mt.timespent) AS totaltimespent
                             FROM {block_ls_modtimestats} mt 
                             JOIN {course_modules} cm ON cm.id = mt.activityid 
                             JOIN {modules} as m ON cm.module = m.id
                             JOIN {grade_items} as gi ON gi.iteminstance = cm.instance AND gi.itemtype = 'mod'
                              AND gi.itemmodule = m.name
                            WHERE 1 = 1 AND cm.course IN ($filtercourseids) AND mt.userid IN ($learnersql) $where";
                break;
            case 'numviews':
                $identy = 'gi.id';
                $courseid = 'lsl.courseid';
                if($this->reporttype == 'table'){
                    $query = "SELECT COUNT(DISTINCT lsl.userid) AS distinctusers, COUNT('X') AS numviews
                                           FROM {logstore_standard_log} lsl
                                           JOIN {course_modules} cm ON cm.id = lsl.contextinstanceid 
                                           JOIN {modules} as m ON cm.module = m.id
                                           JOIN {grade_items} as gi ON gi.iteminstance = cm.instance 
                                           AND gi.itemtype = 'mod' AND gi.itemmodule = m.name
                                           JOIN {user} u ON u.id = lsl.userid
                                          WHERE lsl.crud = 'r' AND lsl.contextlevel = 70
                                            AND lsl.userid > 2 AND  lsl.anonymous = 0
                                            AND u.confirmed = 1 AND lsl.courseid IN ($filtercourseids) AND lsl.userid IN ($learnersql) AND u.deleted = 0 $where";
                }else{
                    $query = "SELECT COUNT('X') AS numviews 
                                           FROM {logstore_standard_log} lsl 
                                           JOIN {course_modules} cm ON cm.id = lsl.contextinstanceid 
                                           JOIN {modules} as m ON cm.module = m.id
                                           JOIN {grade_items} as gi ON gi.iteminstance = cm.instance 
                                           AND gi.itemtype = 'mod' AND gi.itemmodule = m.name
                                           JOIN {user} u ON u.id = lsl.userid 
                                          WHERE lsl.crud = 'r' 
                                            AND lsl.contextlevel = 70 AND lsl.courseid IN ($filtercourseids) AND lsl.userid IN ($learnersql) AND lsl.userid > 2 
                                            AND lsl.anonymous = 0 AND u.confirmed = 1 AND u.deleted = 0 $where";
                  }
                break;        
            
            default:
                return false;
                break;
        }
        $query = str_replace('%placeholder%', $identy, $query);
        $query = str_replace('%courseid%', $courseid, $query);
        return $query; 
    }
}
