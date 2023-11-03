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
use context_system;

class report_quizzes extends reportbase implements report {
    /**
     * @param object $report Report object
     * @param object $reportproperties Report properties object
     */
    public function __construct($report, $reportproperties) {
        parent::__construct($report, $reportproperties);
        $this->parent = true;
        $this->columns = array('quizfield' => ['quizfield'] , 'quizzes' => array(
            'avggrade', 'grademax', 'gradepass', 'notattemptedusers', 'inprogressusers',
            'completedusers', 'noofcompletegradedfirstattempts','totalattempts',
            'totalnoofcompletegradedattempts', 'avggradeoffirstattempts',
            'avggradeofallattempts', 'avggradeofhighestgradedattempts', 'totaltimespent', 'numviews'));
        $this->components = array('columns', 'filters', 'permissions', 'plot');
        $this->courselevel = true;
        $this->basicparams = array(['name' => 'courses']);
        $this->orderable = array('avggrade', 'grademax', 'gradepass', 'notattemptedusers', 'inprogressusers','completedusers', 'noofcompletegradedfirstattempts','totalattempts',
            'totalnoofcompletegradedattempts', 'avggradeoffirstattempts',
            'avggradeofallattempts', 'avggradeofhighestgradedattempts', 'totaltimespent', 'name', 'course');
        $this->searchable = array('main.name', 'c.fullname', 'c.shortname');
        $this->defaultcolumn = 'main.id';
        $this->excludedroles = array("'student'");
    }
    public function count() {
        $this->sql = "SELECT COUNT(DISTINCT main.id)";
    }
    public function select() {
        $this->sql = "SELECT DISTINCT main.id, main.name AS name, c.id AS course, cm.id AS activityid ";
        parent::select();
    }
    public function from() {
        $this->sql .= " FROM {quiz} as main
                        JOIN {course_modules} as cm ON cm.instance = main.id
                        JOIN {modules} m ON cm.module = m.id AND m.name = 'quiz'
                        JOIN {course} c ON c.id = cm.course ";
    }
    public function joins() {
        parent::joins();
    }
    public function where() {
        $this->sql .= " WHERE c.visible = 1 AND cm.visible = 1 AND c.id <> :siteid AND m.name = 'quiz' ";
        $this->params['name'] = 'quiz';
        if (!is_siteadmin($this->userid) && !(new ls)->is_manager($this->userid, $this->contextlevel, $this->role)) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND main.course IN ($this->rolewisecourses) ";
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
            $this->sql .= " AND c.id IN (:filter_courses) ";
        }
        if ($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->sql .= " AND cm.added BETWEEN :ls_fstartdate AND :ls_fenddate ";
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
        }
    }
    
    public function groupby() { 
        global $CFG; 
        if ($CFG->dbtype != 'sqlsrv') {
            $this->sql .= " GROUP BY main.id, c.id, cm.id "; 
        }
    }
    
    /**
     * [get_rows description]
     * @param  array  $users [description]
     * @return [type]        [description]
     */
    public function get_rows($quizs = array()) {
        return $quizs;
    }
    public function column_queries($columnname, $quizid, $courseid = null) {
        global $DB;
        $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));

        if($courseid){
            $learnersql  = (new querylib)->get_learners('', $courseid);
        }else{
            $learnersql  = (new querylib)->get_learners('', '%courseid%');
        }

        $where = " AND %placeholder% = $quizid";
        $filtercourseids = isset($this->params['filter_courses']) ? $this->params['filter_courses'] : SITEID;

        switch ($columnname) {
            case 'grademax':
                $identy = 'q.id';
                $query = "SELECT q.grade AS grademax 
                        FROM {quiz} q
                        WHERE 1 = 1 $where ";
            break;
            case 'gradepass':
                $identy = 'cm.instance';
                $query = "SELECT gi.gradepass AS gradepass 
                        FROM {quiz} q
                        JOIN {course_modules} as cm ON cm.instance = q.id
                        JOIN {modules} m ON cm.module = m.id
                        JOIN {course} c ON c.id = cm.course
                        JOIN {grade_items} gi ON gi.courseid = c.id AND gi.itemmodule = 'quiz' AND gi.iteminstance = q.id  
                        WHERE m.name = 'quiz' AND cm.visible = 1 AND cm.deletioninprogress = 0 AND c.visible = 1 $where ";
            break;
            case 'avggrade':
                $identy = 'gi.iteminstance';
                $query = "SELECT AVG(g.finalgrade) AS avggrade 
                        FROM {grade_grades} g 
                        JOIN {grade_items} gi ON gi.id = g.itemid
                        WHERE g.finalgrade IS NOT NULL  
                        AND gi.itemmodule = 'quiz' $where ";
            break;
            case 'noofcompletegradedfirstattempts':
                $identy = 'quiza.quiz';
                $courseid = 'q.course';
                $query = "SELECT COUNT(*) AS noofcompletegradedfirstattempts 
                        FROM {quiz_attempts} quiza
                        JOIN  {quiz} q ON quiza.quiz = q.id 
                        WHERE quiza.quiz = q.id AND quiza.preview = 0 
                        AND quiza.state = 'finished' AND (quiza.state = 'finished' AND NOT EXISTS ( SELECT 1 FROM {quiz_attempts} qa2 WHERE qa2.quiz = quiza.quiz AND qa2.userid = quiza.userid AND qa2.state = 'finished' AND qa2.attempt < quiza.attempt)) AND quiza.userid IN ($learnersql) AND quiza.sumgrades IS NOT NULL $where ";
            break;
            case 'totalnoofcompletegradedattempts':
                $identy = 'quiza.quiz';
                $courseid = 'q.course';
                $query = "SELECT COUNT(*) AS totalnoofcompletegradedattempts 
                              FROM {quiz_attempts} quiza 
                              JOIN  {quiz} q ON quiza.quiz = q.id 
                              WHERE quiza.quiz = q.id AND quiza.preview = 0 AND quiza.state = 'finished' AND quiza.userid IN ($learnersql) AND quiza.sumgrades IS NOT NULL $where ";
            break;
            case 'avggradeofhighestgradedattempts':
                $identy = 'quiza.quiz';
                $courseid = 'q.course';
                $query = "SELECT CASE WHEN q.sumgrades > 0 THEN 
                        (CASE WHEN AVG(quiza.sumgrades) > 0 THEN AVG(quiza.sumgrades) * 100 / q.sumgrades ELSE 0 END) ELSE 0 END AS avggradeofhighestgradedattempts   
                        FROM {quiz_attempts} quiza
                        JOIN  {quiz} q ON quiza.quiz = q.id 
                        WHERE quiza.quiz = q.id AND quiza.userid IN ($learnersql) AND quiza.preview = 0 AND quiza.state = 'finished' AND (quiza.state = 'finished' AND NOT EXISTS ( SELECT 1 FROM {quiz_attempts} qa2
                                WHERE qa2.quiz = quiza.quiz AND qa2.userid = quiza.userid AND qa2.state = 'finished'
                                AND ( COALESCE(qa2.sumgrades, 0) > COALESCE(quiza.sumgrades, 0) OR (COALESCE(qa2.sumgrades, 0) = COALESCE(quiza.sumgrades, 0) AND qa2.attempt < quiza.attempt) ))) AND quiza.sumgrades IS NOT NULL $where GROUP BY q.sumgrades ";
            break;
            case 'avggradeoffirstattempts':
                $identy = 'quiza.quiz';
                $courseid = 'q.course';
                $query = "SELECT CASE WHEN q.sumgrades > 0 THEN 
                        (CASE WHEN AVG(quiza.sumgrades) > 0 THEN AVG(quiza.sumgrades) * 100 / q.sumgrades ELSE 0 END) ELSE 0 END AS avggradeoffirstattempts 
                        FROM {quiz_attempts} quiza
                        JOIN  {quiz} q ON quiza.quiz = q.id 
                        WHERE quiza.quiz = q.id
                              AND quiza.preview = 0 AND quiza.userid IN ($learnersql) AND quiza.state = 'finished' AND (quiza.state = 'finished' AND NOT EXISTS ( SELECT 1 FROM {quiz_attempts} qa2 
                                  WHERE qa2.quiz = quiza.quiz AND qa2.userid = quiza.userid AND qa2.state = 'finished' AND qa2.attempt < quiza.attempt)) AND quiza.sumgrades IS NOT NULL $where GROUP BY q.sumgrades";
            break;
            case 'avggradeofallattempts':
                $identy = 'quiza.quiz';
                $courseid = 'q.course';
                $query = "SELECT CASE WHEN q.sumgrades > 0 THEN 
                        (CASE WHEN AVG(quiza.sumgrades) > 0 THEN AVG(quiza.sumgrades) * 100 / q.sumgrades ELSE 0 END) ELSE 0 END AS avggradeofallattempts  
                              FROM {quiz_attempts} quiza
                              JOIN  {quiz} q ON quiza.quiz = q.id 
                             WHERE quiza.quiz = q.id AND quiza.userid IN ($learnersql) AND quiza.preview = 0 AND quiza.state = 'finished' 
                               AND quiza.sumgrades IS NOT NULL $where GROUP BY q.sumgrades";
            break;
            case 'inprogressusers':
                $identy = 'qat.quiz';
                $courseid = 'q.course';
                $query = "SELECT COUNT(DISTINCT qat.userid) AS inprogressusers 
                              FROM {quiz_attempts} qat
                              JOIN {quiz} q ON qat.quiz = q.id 
                              JOIN {user} u ON qat.userid = u.id
                             WHERE qat.state = 'inprogress' AND qat.quiz = q.id AND u.deleted = 0 
                               AND u.confirmed = 1 AND qat.userid IN ($learnersql) AND u.suspended = 0 $where ";
            break;
            case 'completedusers':
                $identy = 'cmo.instance';
                $courseid = 'cmo.course';
                $query = "SELECT COUNT(DISTINCT cmc.userid) AS completedusers 
                            FROM {course_modules_completion} AS cmc
                            JOIN {course_modules} as cmo ON cmo.id = cmc.coursemoduleid
                            JOIN {modules} m ON m.id = cmo.module AND m.name= 'quiz'
                            JOIN {context} con ON con.instanceid = cmo.course
                            JOIN {role_assignments} ra ON ra.contextid = con.id
                            JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                           WHERE ra.userid = cmc.userid AND cmc.completionstate > 0
                             AND cmo.visible = 1 AND cmc.userid != 2 AND cmc.userid IN ($learnersql) $where ";
            break; 
            case 'totalattempts':
                $identy = 'qat.quiz';
                $courseid = 'q.course';
                $query = "SELECT COUNT(DISTINCT qat.userid) AS totalattempts 
                              FROM {quiz_attempts} qat
                              JOIN {quiz} q ON qat.quiz = q.id 
                              JOIN {user} u ON qat.userid = u.id
                             WHERE qat.state = 'finished' AND qat.quiz = q.id AND u.deleted = 0 
                               AND u.confirmed = 1 AND qat.userid IN ($learnersql) AND u.suspended = 0 $where ";
            break;
            case 'notattemptedusers':
                $identy = 'q.id';
                $courseid = 'cm.course';
                $query = "SELECT COUNT(DISTINCT u.id) AS notattemptedusers 
                            FROM {user} u
                            JOIN {user_enrolments} ue on ue.userid = u.id AND ue.status = 0
                            JOIN {enrol} e ON e.id = ue.enrolid AND e.status = 0
                            JOIN {role_assignments} ra ON ra.userid = ue.userid
                            JOIN {role} r ON r.id = ra.roleid AND r.shortname = 'student'
                            JOIN {context} con ON con.id = ra.contextid AND con.contextlevel = 50
                            JOIN {course} c ON c.id = con.instanceid 
                            JOIN {course_modules} as cm ON cm.course = c.id
                            JOIN {modules} m ON m.id = cm.module AND m.name= 'quiz'
                            JOIN {quiz} q ON cm.instance = q.id 
                           WHERE  u.id NOT IN ( SELECT qat.userid
                                                  FROM {quiz_attempts} qat
                                                  JOIN {quiz} q1 ON qat.quiz = q1.id 
                                                 WHERE q1.id = q.id $where)
                             AND u.id > 2 AND u.deleted = 0 AND u.confirmed = 1 AND u.suspended = 0 AND ra.userid IN ($learnersql) $where ";
            break;
            case 'totaltimespent':
                $identy = 'cm.instance';
                $courseid = 'mt.courseid';
                $query = "SELECT SUM(mt.timespent) AS totaltimespent 
                            FROM {block_ls_modtimestats} mt 
                            JOIN {course_modules} cm ON cm.id = mt.activityid 
                            JOIN {modules} m ON m.id = cm.module
                            WHERE m.name = 'quiz' AND mt.userid IN ($learnersql) $where ";
            break; 
            case 'numviews':
                $identy = 'cm.instance';
                $courseid = 'lsl.courseid';
                if($this->reporttype == 'table'){
                    $query = "  SELECT COUNT(DISTINCT lsl.userid) as distinctusers, COUNT('X') as numviews 
                                  FROM {logstore_standard_log} lsl 
                                  JOIN {user} u ON u.id = lsl.userid 
                                  JOIN {course_modules} cm ON lsl.contextinstanceid = cm.id
                                  JOIN {quiz} q ON q.id = cm.instance 
                                  JOIN {modules} m ON m.id = cm.module
                                 WHERE lsl.crud = 'r' AND lsl.contextlevel = 70  AND lsl.anonymous = 0 AND u.id IN ($learnersql)
                                   AND lsl.userid > 2  AND u.confirmed = 1 AND u.deleted = 0  AND lsl.anonymous = 0 AND m.name = 'quiz'
                                   $where ";
                }else{
                    $query = "  SELECT COUNT('X') as numviews 
                                  FROM {logstore_standard_log} lsl 
                                 JOIN {user} u ON u.id = lsl.userid
                                 JOIN {course_modules} cm ON lsl.contextinstanceid = cm.id
                                 JOIN {quiz} q ON q.id = cm.instance 
                                 JOIN {modules} m ON m.id = cm.module
                                 WHERE  lsl.crud = 'r' AND lsl.contextlevel = 70 AND lsl.userid > 2 AND u.id IN ($learnersql) AND lsl.anonymous = 0 AND u.confirmed = 1 AND u.deleted = 0 AND m.name = 'quiz' $where";
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
