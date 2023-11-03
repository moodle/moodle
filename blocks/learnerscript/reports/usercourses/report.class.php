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
use block_learnerscript\local\reportbase;
use block_learnerscript\report;
use context_course;

class report_usercourses extends reportbase implements report {

    private $relatedctxsql;
    private $datefiltersql;

    private $relatedctxparams;

    public function __construct($report, $reportproperties) {
        parent::__construct($report);
		$this->components = array('columns','ordering', 'filters', 'permissions', 'calcs', 'plot');
	 	$columns = ['timeenrolled', 'status','grade','totaltimespent', 'progressbar','completedassignments','completedquizzes', 'completedscorms', 'marks', 'badgesissued', 'completedactivities'];
        $this->columns = ['userfield'=>['userfield'],'usercoursescolumns' => $columns];
        $this->basicparams = array(['name' => 'courses']);
   		$this->parent = false;
   		$this->courselevel = true;
		$this->filters = array('users');
		$this->orderable = array('fullname', 'timeenrolled', 'completedassignments', 'completedquizzes', 'completedscorms', 'completedactivities', 'marks', 'grade', 'badgesissued', 'totaltimespent');
        $this->defaultcolumn = 'u.id';
        $this->excludedroles = array("'student'");
	}
    function init() {
        global $DB;
        if (!isset($this->params['filter_courses'])) {
            $this->initial_basicparams('courses');
            $fcourses = array_keys($this->filterdata);
            $this->params['filter_courses'] =array_shift($fcourses);
        }
        $this->courseid = !empty($this->params['filter_courses']) ? $this->params['filter_courses'] : SITEID; 
        $this->categoriesid = isset($this->params['filter_coursecategories']) ? $this->params['filter_coursecategories'] : 0; 
        $context = context_course::instance($this->courseid);
        list($this->relatedctxsql, $this->relatedctxparams) = $DB->get_in_or_equal($context->get_parent_context_ids(true), SQL_PARAMS_NAMED,'relatedctx');
        
        $this->params['contextlevel'] = CONTEXT_COURSE;
        $this->params['userid'] = $this->userid;
        $this->params['ej1_active'] = ENROL_USER_ACTIVE;
        $this->params['ej1_enabled'] = ENROL_INSTANCE_ENABLED;
        $this->params['ej1_now1'] = round(time(), -2); // improves db caching
        $this->params['ej1_now2'] = $this->params['ej1_now1'];
        $this->params['ej1_courseid'] = $this->courseid;
        $this->params['courseid'] = $this->courseid;
        $this->params['courseid1'] = $this->courseid;
        $this->params['roleid'] = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $this->params = array_merge($this->relatedctxparams, $this->params);

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
         $this->sql = "SELECT  count(u.id)";
    }

    function select() {
        $this->sql = "SELECT  u.id AS userid,CONCAT(u.firstname,' ',u.lastname) AS fullname, u.email,  cc.timestarted AS timestarted,
                              u.timezone,cc.timecompleted AS timecompleted, $this->courseid AS courseid ";
        if (!empty($this->selectedcolumns)) {
            if (in_array('timeenrolled', $this->selectedcolumns)) {
                $this->sql .= ", e.timecreated AS timeenrolled";
            }
        }
        parent::select();
    }

    function from() {
        $this->sql .= " FROM {user} u";
    }

    function joins() {
        $this->sql .=" JOIN (SELECT DISTINCT eu1_u.id, ej1_ue.timecreated
                               FROM {user} eu1_u
                               JOIN {user_enrolments} ej1_ue ON ej1_ue.userid = eu1_u.id
                               JOIN {enrol} ej1_e ON (ej1_e.id = ej1_ue.enrolid AND ej1_e.courseid = :ej1_courseid)
                               WHERE 1 = 1 AND ej1_ue.status = :ej1_active AND ej1_e.status = :ej1_enabled AND ej1_ue.timestart < :ej1_now1 AND (ej1_ue.timeend = 0 OR ej1_ue.timeend > :ej1_now2) AND eu1_u.deleted = 0) e ON e.id = u.id
                             LEFT JOIN {context} ctx ON (ctx.instanceid = u.id AND ctx.contextlevel = :contextlevel)
                 LEFT JOIN {course_completions} as cc ON cc.userid = u.id AND cc.course = $this->courseid 
                 LEFT JOIN {course} c ON c.id = cc.course";

        parent::joins();
    }

    function where() {
        $status = isset($this->params['filter_status']) ? $this->params['filter_status'] : '';
        $this->sql .= " WHERE u.id IN (SELECT ra.userid
                                         FROM {role_assignments} ra
                                        WHERE ra.roleid = :roleid AND ra.contextid $this->relatedctxsql $this->datefiltersql)
                                          AND u.confirmed = 1 AND u.deleted = 0";
        if ($status == 'completed') {
            $this->params['courseid'] = $this->courseid;
            $this->sql .= " AND u.id IN (SELECT userid FROM {course_completions}
                                    WHERE course= :courseid AND timecompleted IS NOT NULL)";
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
        if(isset($this->params['filter_users']) && $this->params['filter_users'] > 0) {
            $this->sql .= " AND u.id = :filter_users";
        }
        if ($this->ls_startdate > 0 && $this->ls_enddate) {
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
            $this->sql .= " AND u.timecreated BETWEEN :ls_fstartdate AND :ls_fenddate ";
        }
    }

    function groupby() {

    }
    
	public function get_rows($users) {
		return $users;
	}

    public function column_queries($columnname, $usercourseid, $courseid = null) {

        $where = " AND %placeholder% = $usercourseid";
        $filtercourseid = isset($this->params['filter_courses']) ? $this->params['filter_courses'] : SITEID;

        switch ($columnname) {
            case 'grade':
                $identy = 'gg.userid';
                $query = "SELECT ((gg.finalgrade/gi.grademax)*100) AS grade
                                        FROM {grade_items} gi
                                        JOIN {grade_grades} gg ON gg.itemid = gi.id AND gi.itemtype = 'course'
                                        WHERE gi.courseid = $filtercourseid $where ";
                
            break;
            case 'totaltimespent':
                $identy = 'bt.userid';
                $query = "SELECT SUM(bt.timespent) AS totaltimespent FROM {block_ls_coursetimestats} AS bt WHERE bt.courseid = $filtercourseid $where ";
            break;
            case 'completedassignments':
                $identy = 'cmc.userid';
                $query = "SELECT COUNT(cm.id) AS completedassignments
                                        FROM {course_modules} AS cm
                                        JOIN {modules} AS m ON m.id = cm.module
                                        JOIN {course_modules_completion} as cmc ON cmc.coursemoduleid = cm.id
                                       WHERE m.name = 'qbassign' AND cm.visible = 1 AND cm.deletioninprogress = 0
                                         AND cm.course = $filtercourseid AND cmc.completionstate != 0 $where";  
            break;
            case 'completedquizzes': 
                $identy = 'cmc.userid';     
                $query = "SELECT COUNT(cm.id) AS completedquizzes
                                        FROM {course_modules} AS cm
                                        JOIN {modules} AS m ON m.id = cm.module
                                        JOIN {course_modules_completion} as cmc ON cmc.coursemoduleid = cm.id
                                       WHERE m.name = 'quiz' AND cm.visible = 1 AND cm.deletioninprogress = 0
                                         AND cm.course = $filtercourseid AND cmc.completionstate != 0 $where";
            break;
            case 'completedscorms':
                $identy = 'cmc.userid';
                $query = "SELECT COUNT(cm.id) AS completedscorms
                                        FROM {course_modules} AS cm
                                        JOIN {modules} AS m ON m.id = cm.module
                                        JOIN {course_modules_completion} as cmc ON cmc.coursemoduleid = cm.id
                                       WHERE m.name = 'scorm' AND cm.visible = 1 AND cm.deletioninprogress = 0
                                        AND cm.course = $filtercourseid AND cmc.completionstate != 0 $where";
            break;
            case 'marks':
                $identy = 'gg.userid';
                $query = "SELECT gg.finalgrade AS marks FROM {grade_items} gi
                                         JOIN {grade_grades} gg ON gg.itemid = gi.id AND gi.itemtype = 'course'
                                        WHERE gi.courseid = $filtercourseid $where";
            break;
            case 'badgesissued':
                $identy = 'bi.userid';
                $query = "SELECT COUNT(bi.id) AS badgesissued FROM {badge_issued} as bi
                                        JOIN {badge} as b ON b.id = bi.badgeid
                                       WHERE  bi.visible = 1 AND b.status != 0
                                         AND b.status != 2 AND b.courseid = $filtercourseid $where";
            break;
            case 'completedactivities':
                $identy = 'cmc.userid';
                $query = "SELECT COUNT(cm.id) AS completedactivities
                                        FROM {course_modules} AS cm
                                        JOIN {modules} AS m ON m.id = cm.module
                                        JOIN {course_modules_completion} as cmc ON cmc.coursemoduleid = cm.id
                                       WHERE  cm.visible = 1 AND cm.deletioninprogress = 0 AND cm.course = $filtercourseid
                                         AND cmc.completionstate != 0 $where";
            break;
            default:
                return false;
                break;
        }
        $query = str_replace('%placeholder%', $identy, $query);
        return $query;
    }
}
