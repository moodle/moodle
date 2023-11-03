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

class report_scorm extends reportbase implements report {
    /**
     * [__construct description]
     * @param object $report Report object
     * @param object $reportproperties Report properties object
     */
    public function __construct($report, $reportproperties) {
        global $USER;
        parent::__construct($report, $reportproperties);
        $this->components = array('columns', 'filters', 'permissions', 'calcs', 'plot');
        $columns = ['noofattempts', 'highestgrade','avggrade','lowestgrade','noofcompletions','totaltimespent', 'numviews'];
        $this->columns = ['scormfield'=> ['scormfield'], 'scorm' => $columns];
        $this->courselevel = true;
        $this->basicparams = array(['name' => 'courses']);
        $this->parent = true;
        $this->orderable = array('noofattempts', 'highestgrade','avggrade','lowestgrade','noofcompletions','totaltimespent','name', 'course');
        $this->searchable = array('main.name', 'c.fullname');
        $this->defaultcolumn = 'main.id';
        $this->excludedroles = array("'student'");

    }

    public function count() {
        $this->sql = "SELECT COUNT(DISTINCT main.id)";
    }

    public function select() {
        $this->sql = "SELECT DISTINCT main.id, main.name, main.course, cm.id AS activityid, cm.visible as status";
        parent::select();
    }

    public function from() {
        $this->sql .= " FROM {scorm} as main 
                        JOIN {course_modules} as cm ON cm.instance = main.id
                        JOIN {modules} m ON cm.module = m.id
                        JOIN {course} c ON c.id = cm.course ";
    }

    public function joins() {
        parent::joins();
    }

    public function where() {
        $this->sql .= " WHERE c.visible = 1 AND c.id <> :siteid AND m.name = 'scorm' AND cm.visible = 1 ";
        if (!is_siteadmin($this->userid) && !(new ls)->is_manager($this->userid, $this->contextlevel, $this->role)) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND cm.course IN ($this->rolewisecourses) ";
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
        if (!empty($this->params['filter_courses']) && $this->params['filter_courses'] <> SITEID && !$this->scheduling) {
            $this->sql .= " AND main.course IN (:filter_courses) ";
        }
        if ($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
            $this->sql .= " AND cm.added BETWEEN :ls_fstartdate AND :ls_fenddate ";
        }
    }

    public function groupby() {
        global $CFG; 
        if ($CFG->dbtype != 'sqlsrv') {
            $this->sql .= " GROUP BY main.id, cm.id";
        }
    }
    
    /**
     * @param  array $activites Activites
     * @return array $reportarray Activities information
     */
    public function get_rows($scormsdata = array()) {
        return $scormsdata;
    }

    public function column_queries($columnname, $scormid, $courseid = null) {
        global $DB;
        if($courseid){
            $learnersql  = (new querylib)->get_learners('', $courseid);
        }else{
            $learnersql  = (new querylib)->get_learners('', '%courseid%');
        }
        $where = " AND %placeholder% = $scormid";

        switch ($columnname) {
            case 'noofattempts' :
                $identy = 'sst.scormid';
                $courseid = 's.course';
                $query = "SELECT COUNT(sst.id) AS noofattempts 
                        FROM {scorm_scoes_track} sst 
                        JOIN {scorm} s ON s.id = sst.scormid 
                        WHERE sst.element = 'x.start.time' AND sst.userid > 2 
                        AND sst.userid IN ($learnersql) $where ";
                break;
            case 'noofcompletions' :
                $identy = 'cm.instance';
                $courseid = 'cm.course';
                $query = "SELECT COUNT(DISTINCT cmc.id) AS noofcompletions 
                            FROM {course_modules_completion} cmc 
                            JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
                            JOIN {modules} m ON m.id = cm.module
                            WHERE cmc.coursemoduleid = cm.id AND cmc.userid > 2 AND cmc.completionstate > 0 
                                AND cmc.userid IN ($learnersql) AND m.name = 'scorm' $where ";
                break;
            case 'highestgrade' :
                $identy = 'gi.iteminstance';
                $query = "SELECT MAX(gg.finalgrade) AS highestgrade 
                            FROM {grade_grades} gg 
                            JOIN {grade_items} gi ON gi.id = gg.itemid 
                            WHERE gi.itemmodule = 'scorm' $where ";
                break;  
            case 'avggrade' :
                $identy = 'gi.iteminstance';
                $query = "SELECT AVG(gg.finalgrade) AS avggrade 
                            FROM {grade_grades} gg 
                            JOIN {grade_items} gi ON gi.id = gg.itemid 
                            WHERE gi.itemmodule = 'scorm' $where ";
                break;

           case 'lowestgrade' :
                $identy = 'gi.iteminstance';
                $query = "SELECT MIN(gg.finalgrade) AS lowestgrade 
                            FROM {grade_grades} gg 
                            JOIN {grade_items} gi ON gi.id = gg.itemid 
                            WHERE gi.itemmodule = 'scorm' $where ";
                break;
            case 'totaltimespent' :
                $identy = 'cm.instance';
                $courseid = 'mt.courseid';
                $query = "SELECT SUM(mt.timespent) AS totaltimespent 
                            FROM {block_ls_modtimestats} as mt 
                            JOIN {course_modules} cm ON cm.id = mt.activityid
                            JOIN {modules} m ON m.id = cm.module
                            WHERE m.name = 'scorm' AND mt.userid IN ($learnersql) $where ";
                break;
            case 'numviews':
                $identy = 'cm.instance';
                $courseid = 'lsl.courseid';
                if($this->reporttype == 'table'){
                    $query = "  SELECT COUNT(DISTINCT lsl.userid) as distinctusers, COUNT('X') as numviews 
                                  FROM {logstore_standard_log} lsl 
                                  JOIN {user} u ON u.id = lsl.userid 
                                  JOIN {course_modules} cm ON lsl.contextinstanceid = cm.id
                                  JOIN {scorm} q ON q.id = cm.instance 
                                  JOIN {modules} m ON m.id = cm.module
                                 WHERE lsl.crud = 'r' AND lsl.contextlevel = 70  AND lsl.anonymous = 0 AND u.id IN ($learnersql)
                                   AND lsl.userid > 2  AND u.confirmed = 1 AND u.deleted = 0  AND lsl.anonymous = 0 AND m.name = 'scorm'
                                   $where ";
                }else{
                    $query = "  SELECT COUNT('X') as numviews 
                                  FROM {logstore_standard_log} lsl 
                                 JOIN {user} u ON u.id = lsl.userid
                                 JOIN {course_modules} cm ON lsl.contextinstanceid = cm.id
                                 JOIN {scorm} q ON q.id = cm.instance 
                                 JOIN {modules} m ON m.id = cm.module
                                 WHERE  lsl.crud = 'r' AND lsl.contextlevel = 70 AND lsl.userid > 2 AND u.id IN ($learnersql) AND lsl.anonymous = 0 AND u.confirmed = 1 AND u.deleted = 0 AND m.name = 'scorm' $where";
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
