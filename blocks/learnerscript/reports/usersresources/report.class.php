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

/** LearnerScript
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: sreekanth
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\reportbase;
use block_learnerscript\report;
use block_learnerscript\local\querylib;
use block_learnerscript\local\ls as ls;

class report_usersresources extends reportbase implements report {

	/**
	 * [__construct description]
	 * @param [type] $report           [description]
	 * @param [type] $reportproperties [description]
	 */
	public function __construct($report, $reportproperties = false) {
		parent::__construct($report);
		$this->parent = false;
		$this->components = array('columns', 'filters', 'permissions', 'plot');
		$resourcescolumns = array('totalresources','totaltimespent','numviews');
		$this->columns = ['userfield' => ['userfield'] ,'usersresources' => $resourcescolumns];
		$this->basicparams = array(['name' => 'courses']);
		$this->filters = ['users'];
		$this->courselevel = true;
		$this->orderable = array('fullname', 'totalresources', 'totaltimespent');
		$this->searchable = array("CONCAT(u.firstname, ' ', u.lastname)", "u.email");
        $this->defaultcolumn = 'u.id';
        $this->excludedroles = array("'student'");
	}
    public function init() {
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
    }
	public function count() {
       $this->sql = "SELECT COUNT(DISTINCT u.id)";
    }

    public function select() {
      $this->sql = "SELECT DISTINCT u.id, u.email, CONCAT(u.firstname,' ', u.lastname) AS fullname ";
      parent::select();
    }

    public function from() {
      $this->sql .= " FROM {user} u
                        JOIN {user_enrolments} AS ue ON ue.userid = u.id AND ue.status = 0
                        JOIN {enrol} AS e ON ue.enrolid = e.id AND e.status = 0 
                        JOIN {course} c ON c.id = e.courseid
                        JOIN {context} con ON c.id = con.instanceid
                        JOIN {role_assignments} ra ON ra.userid = u.id
                        JOIN {role} AS rl ON rl.id = ra.roleid AND rl.shortname = 'student'";
    }
    public function joins() {
      parent::joins();
    }

    public function where() {
    	global $DB;
       	$studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $this->params['studentroleid'] = $studentroleid;
        $this->sql .= " WHERE ra.roleid = :studentroleid AND ra.contextid = con.id
                        AND u.confirmed = 1 AND u.deleted = 0 ";
        if ((!is_siteadmin() || $this->scheduling) && !(new ls)->is_manager()) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND c.id IN ($this->rolewisecourses) ";
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
         
        $userid = isset($this->params['filter_users']) ? $this->params['filter_users'] : array();
        if (!empty($userid) && $userid != '_qf__force_multiselect_submission') {
            is_array($userid) ? $userid = implode(',', $userid) : $userid;
            $this->params['userid'] = $userid;
            $this->sql .= " AND u.id IN (:userid)";
        }
        if ($this->params['filter_courses'] > SITEID) {
            $this->sql .= " AND c.id IN (:filter_courses)";
        }
    }
    public function groupby() {
        
    }

	
	public function get_rows($elements) {
		return $elements;
	}

	public function column_queries($columnname, $userid) {
        global $DB;
        $coursesql  = (new querylib)->get_learners($userid,'');
        $where = " AND %placeholder% = $userid";
        if ($this->ls_startdate >= 0 && $this->ls_enddate) {            
            $where  .= " AND %placeholder2% BETWEEN $this->ls_startdate AND $this->ls_enddate ";
        }

        $modules = $DB->get_fieldset_select('modules',  'name', '');
        foreach ($modules as $modulename) {
        	$resourcearchetype = plugin_supports('mod', $modulename, FEATURE_MOD_ARCHETYPE);
        	if($resourcearchetype){
        		$resources[] = "'$modulename'";
        	}
        }
        $imploderesources = implode(', ', $resources);

        $filtercourseid = isset($this->params['filter_courses']) ? $this->params['filter_courses'] : SITEID;
        switch ($columnname) {
            case 'totalresources':
                $identy = 'ue.userid';
                $identy2 = 'ra.timemodified';
                $query = "SELECT COUNT(DISTINCT cm.id) AS totalscorms 
                            FROM {course} AS c
                            JOIN {enrol} AS e ON c.id = e.courseid AND e.status = 0
                            JOIN {user_enrolments} AS ue ON ue.enrolid = e.id AND ue.status = 0
                            JOIN {role_assignments} AS ra ON ra.userid = ue.userid AND ra.roleid = 5
                            JOIN {context} AS con ON con.contextlevel = 50 AND c.id = con.instanceid
                            JOIN {course_modules} AS cm ON cm.course = c.id
                            JOIN {modules} AS m ON m.id = cm.module
                            WHERE con.id = ra.contextid AND cm.visible = 1 AND cm.deletioninprogress = 0 AND c.visible = 1 AND m.name IN ($imploderesources) AND c.id = $filtercourseid $where ";
            break;
            case 'totaltimespent':
                $identy = 'mt.userid';
                $identy2 = 'mt.timemodified';
                $query = "SELECT SUM(mt.timespent) AS totaltimespent  
                            FROM {block_ls_modtimestats} AS mt 
                            JOIN {course_modules} cm ON cm.id = mt.activityid 
                            JOIN {modules} m ON m.id = cm.module WHERE m.name IN ($imploderesources) 
                             AND mt.courseid = $filtercourseid AND mt.activityid != $filtercourseid $where ";
            break;
            case 'numviews':
                $identy = 'lsl.userid';
                $identy2 = 'lsl.timecreated';
                $query = "SELECT COUNT(lsl.userid) AS distinctusers 
                    		FROM {logstore_standard_log} lsl 
                    		JOIN {course_modules} cm ON cm.id = lsl.contextinstanceid 
                    		JOIN {modules} m ON m.id = cm.module 
                    		WHERE m.name IN ($imploderesources) AND lsl.crud = 'r' 
                    		AND lsl.contextlevel = 70 AND lsl.anonymous = 0 
                    		AND lsl.courseid = $filtercourseid $where ";
            break;

            default:
            return false;
                break;
        }
        $query = str_replace('%placeholder%', $identy, $query);
        $query = str_replace('%placeholder2%', $identy2, $query);
        return $query;
    }
}
