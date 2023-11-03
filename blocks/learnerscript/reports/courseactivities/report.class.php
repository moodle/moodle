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
 * LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\querylib;
use block_learnerscript\local\reportbase;
use block_learnerscript\report;
defined('MOODLE_INTERNAL') || die();
class report_courseactivities extends reportbase implements report {
    /**
     * [__construct description]
     * @param [type] $report           [description]
     * @param [type] $reportproperties [description]
     */
    public function __construct($report, $reportproperties) {
        parent::__construct($report, $reportproperties);
        $this->columns = array('activityfield' => ['activityfield'], 
                               'courseactivitiescolumns' => array('activityname', 'learnerscompleted', 'grademax', 'gradepass', 'averagegrade', 'highestgrade', 'lowestgrade', 'progress', 'totaltimespent', 'numviews', 'grades'));
        $this->parent = false;
        $this->basicparams = array(['name' => 'courses']);
        $this->components = array('columns', 'filters', 'permissions', 'calcs', 'plot');
        $this->courselevel = true;
        $this->orderable = array('activityname', 'learnerscompleted', 'grademax', 'gradepass', 'averagegrade', 'highestgrade', 'lowestgrade', 'totaltimespent');
        $this->defaultcolumn = 'main.id';
        $this->excludedroles = array("'student'");
    }

    function count() {
        global $DB;
        $this->sql = "SELECT COUNT(DISTINCT main.id) ";
    }

    function select() {
        global $DB;
        $modules = $DB->get_fieldset_select('modules', 'name', '', array('visible' => 1));
        foreach ($modules as $modulename) {
            $aliases[] = $modulename;
            $activities[] = "'$modulename'";
            $fields1[] = "COALESCE($modulename.name,'')";
        }
        $activitynames = implode(',', $fields1);
        $this->sql = " SELECT DISTINCT(main.id) , m.id AS moduleid, main.instance, 
                                main.course";
        $this->sql .= ", CONCAT($activitynames) AS activityname";
        if (!empty($this->selectedcolumns)) {
            if (in_array('grades', $this->selectedcolumns)) {
                $this->sql .= ", 'Grades'";
            }
        }
        parent::select();
    }

    function from() {
        $this->sql .= " FROM {course_modules} as main
                       JOIN {modules} m ON main.module = m.id";
    }

    function joins() {
        global $DB;
       parent::joins(); 
        $modules = $DB->get_fieldset_select('modules', 'name', '', array('visible' => 1));
        foreach ($modules as $modulename) {
            $aliases[] = $modulename;
            $activities[] = "'$modulename'";
            $fields1[] = "COALESCE($modulename.name,'')";
        }
        foreach ($aliases as $alias) {
            $this->sql .= " LEFT JOIN {".$alias."} AS $alias ON $alias.id = main.instance AND m.name = '$alias'";
        }
         $this->sql .= " LEFT JOIN {grade_items} gi ON gi.itemmodule = m.name
                       AND gi.courseid = main.course AND gi.iteminstance = main.instance ";
       
    }

    function where() {
        global $DB;
        $modules = $DB->get_fieldset_select('modules', 'name', '', array('visible' => 1));
        foreach ($modules as $modulename) {
            $activities[] = "'$modulename'";
        }
        $activitynames = implode(',', $activities);
        $this->sql .= " WHERE m.visible = 1 AND m.name IN ($activitynames) AND main.visible = 1 ";
        if ($this->ls_startdate > 0 && $this->ls_enddate) {
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
            $this->sql .= " AND main.added BETWEEN :ls_fstartdate AND :ls_fenddate ";
        }
        parent::where();
    }

    function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $modules = $DB->get_fieldset_select('modules',  'name','', array('visible'=> 1));
            foreach ($modules as $modulename) {
                $fields1[] = "COALESCE($modulename.name,'')";
            }
            $fields2 = array('m.name');
            $this->searchable = array_merge($fields1,$fields2);
            $statsql = array();
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);          
            $this->sql .= " AND ($fields) ";
        }
    }

    function filters() {
        if (!isset($this->params['filter_courses'])) {
            $this->initial_basicparams('courses');
            $filterdata = array_keys($this->filterdata);
            $this->params['filter_courses'] = array_shift($filterdata);
        }
        if ($this->params['filter_courses'] > SITEID) {
             $this->sql .= " AND main.course = :filter_courses";
        }
        if (isset($this->params['filter_modules']) && $this->params['filter_modules'] > 0) {
            $this->sql .= " AND main.module = :filter_modules";
        }
        if (isset($this->params['filter_activities']) && $this->params['filter_activities'] > 0) {
            $this->sql .= " AND main.id = :filter_activities";
        }
    } 

    function groupby() {
        // $this->sql .= " GROUP BY main.id ";
    }
    /**
     * [get_rows description]
     * @param  array  $activites [description]
     * @return [type]            [description]
     */
    public function get_rows($activites = array()) {
        return $activites;
    }

    public function column_queries($column, $activityid, $courses = null){
        global $CFG;
        if($courses){
            $learnersql  = (new querylib)->get_learners('', $courses);
        }else{
            $learnersql  = (new querylib)->get_learners('', '%courseid%');
        }
        $courseid = isset($this->params['filter_courses']) ? $this->params['filter_courses'] : SITEID;
        $where = " AND %placeholder% = $activityid";
        $limit = '';
        switch ($column) {
            case 'grademax':
                $identy = 'cm1.id';
                $query =  "SELECT $limit gi.grademax AS grademax
                            FROM {grade_grades} as gg  
                            JOIN {grade_items} as gi ON gg.itemid = gi.id AND gi.itemtype = 'mod'
                            JOIN {course_modules} cm1 ON cm1.instance = gi.iteminstance
                            JOIN {modules} m ON m.id = cm1.module
                            JOIN {course_sections} csc ON csc.id = cm1.section
                           WHERE cm1.course = $courseid AND m.name = gi.itemmodule
                            $where $limit";
                break;
            case 'gradepass':
                $identy = 'cm1.id';
                $query =  "SELECT $limit gi.gradepass AS gradepass
                            FROM {grade_grades} as gg  
                            JOIN {grade_items} as gi ON gg.itemid = gi.id AND gi.itemtype = 'mod'
                            JOIN {course_modules} cm1 ON cm1.instance = gi.iteminstance
                            JOIN {modules} m ON m.id = cm1.module
                            JOIN {course_sections} csc ON csc.id = cm1.section
                           WHERE cm1.course = $courseid AND m.name = gi.itemmodule
                            $where $limit";
                break;
            case 'learnerscompleted':
                $identy = 'cm.id';
                $courses = 'cm.course';
                $query = " SELECT COUNT(cmc.id) AS learnerscompleted
                            FROM {course_modules_completion} as cmc
                            JOIN {course_modules} cm ON cmc.coursemoduleid = cm.id
                           WHERE cmc.completionstate > 0 AND cmc.userid > 2 AND cmc.userid IN ($learnersql)
                                 $where ";
                break;
            case 'highestgrade':
                $identy = 'cm1.id';
                $query =  "SELECT MAX(finalgrade) AS highestgrade
                            FROM {grade_grades} as gg  
                            JOIN {grade_items} as gi ON gg.itemid = gi.id AND gi.itemtype = 'mod'
                            JOIN {course_modules} cm1 ON cm1.instance = gi.iteminstance
                            JOIN {modules} m ON m.id = cm1.module
                            JOIN {course_sections} csc ON csc.id = cm1.section
                           WHERE cm1.course = $courseid AND m.name = gi.itemmodule
                            $where ";
                break;
            case 'averagegrade':
                 $identy = 'cm1.id';
                 $query =  "SELECT AVG(finalgrade) AS averagegrade
                            FROM {grade_grades} as gg  
                            JOIN {grade_items} as gi ON gg.itemid = gi.id AND gi.itemtype = 'mod'
                            JOIN {course_modules} cm1 ON cm1.instance = gi.iteminstance
                            JOIN {modules} m ON m.id = cm1.module
                            JOIN {course_sections} csc ON csc.id = cm1.section
                            WHERE cm1.course = $courseid AND m.name = gi.itemmodule $where ";
                break;   
            case 'lowestgrade':
                 $identy = 'cm1.id';
                 $query =  "SELECT MIN(finalgrade) AS lowestgrade
                            FROM {grade_grades} as gg  
                            JOIN {grade_items} as gi ON gg.itemid = gi.id AND gi.itemtype = 'mod'
                            JOIN {course_modules} cm1 ON cm1.instance = gi.iteminstance
                            JOIN {modules} m ON m.id = cm1.module
                            JOIN {course_sections} csc ON csc.id = cm1.section
                           WHERE cm1.course = $courseid AND m.name = gi.itemmodule $where ";
                break;
            case 'progress':
                 $identy = 'cm.id';
                 $courses = 'cm.course';
                  $query =  "SELECT ((completed / total )* 100) AS progress 
                            FROM (SELECT COUNT(cmc.id) as completed
                            FROM {course_modules_completion} AS cmc
                            JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
                            WHERE cmc.completionstate > 0 AND cmc.userid IN ($learnersql) $where ) AS completed,
                            (SELECT count(DISTINCT u.id) as total FROM {user} u
                            JOIN {role_assignments} ra ON ra.userid = u.id
                            JOIN {context} ctx ON ctx.id = ra.contextid
                            JOIN {course} c ON c.id = ctx.instanceid
                            JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                            JOIN {course_modules} cm ON cm.course = c.id
                           WHERE ra.userid IN ($learnersql) $where ) as total";
                break;
            case 'totaltimespent':
                $identy = 'mt.activityid';
                $courses = 'mt.courseid';
                $query = "SELECT SUM(mt.timespent) AS totaltimespent 
                          FROM {block_ls_modtimestats} mt
                         WHERE mt.courseid = $courseid AND mt.userid IN ($learnersql) $where ";
            break;
            case 'numviews':
                $identy = 'lsl.contextinstanceid';
                $courses = 'lsl.courseid';
                if($this->reporttype == 'table'){
                    $query = "SELECT COUNT(DISTINCT lsl.userid)  AS distinctusers, COUNT('X') AS numviews 
                                                FROM {logstore_standard_log} lsl 
                                                JOIN {user} u ON u.id = lsl.userid 
                                               WHERE lsl.crud = 'r' AND lsl.contextlevel = 70 
                                                 AND lsl.courseid = $courseid AND lsl.anonymous = 0
                                                 AND u.confirmed = 1 AND u.deleted = 0 AND lsl.userid IN ($learnersql) $where ";
                }else{
                    $query = "SELECT COUNT('X') AS numviews 
                                FROM {logstore_standard_log} lsl JOIN {user} u ON u.id = lsl.userid
                               WHERE lsl.crud = 'r' AND lsl.contextlevel = 70 AND lsl.courseid = $courseid
                                 AND lsl.anonymous = 0 AND lsl.userid IN ($learnersql) AND u.confirmed = 1
                                 AND u.deleted = 0 $where";                    
                }
            break;
            default:
                return false;
                break;
        } 

        //TOP & LIMIT
        if ($CFG->dbtype == 'sqlsrv') {
            $limit = str_replace('%%TOP%%', 'TOP 1', $query);
        } else {
            $limit = str_replace('%%LIMIT%%', 'LIMIT 1', $query);
        }
        $query = str_replace('%placeholder%', $identy, $query);
        $query = str_replace('%courseid%', $courses, $query);
        return $query;
    }
}
