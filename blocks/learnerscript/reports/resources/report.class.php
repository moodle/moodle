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

class report_resources extends reportbase implements report {

    private $resourcenames;

    private $resourceslist;

    private $aliases = [];
	/**
	 * [__construct description]
	 * @param [type] $report           [description]
	 * @param [type] $reportproperties [description]
	 */
	public function __construct($report, $reportproperties = false) {
		parent::__construct($report, $reportproperties);
		$this->parent = true;
		$this->components = array('columns', 'filters', 'permissions', 'plot');
		$resourcescolumns = array('activity','totaltimespent','numviews');
		$this->columns = ['activityfield' => ['activityfield'] ,'resourcescolumns' => $resourcescolumns];
		$this->basicparams = array(['name' => 'courses']); 
        $this->courselevel = true;
		$this->orderable = array('course','activity','totaltimespent');
        $this->defaultcolumn = 'main.id';
        $this->excludedroles = array("'student'");
	}
    function init() {
        global $DB;
        $modules = $DB->get_fieldset_select('modules',  'name', '');
        $this->aliases = [];
        foreach ($modules as $modulename) {
            $resourcearchetype = plugin_supports('mod', $modulename, FEATURE_MOD_ARCHETYPE);
            if($resourcearchetype){
                $this->aliases[] = $modulename;
                $resources[] = "'$modulename'";
                $fields1[] = "COALESCE($modulename.name,'')";
            }
        }
        $this->resourcenames = implode(',', $fields1);
        $this->resourceslist = implode(',', $resources);
        $this->params['siteid'] = SITEID;
        $this->params['target'] = 'course_module';
        $this->params['contextlevel'] = CONTEXT_MODULE;
        $this->params['action'] = 'viewed';
    }
    function count() {
        $this->sql = "SELECT COUNT(main.id) ";
    }

    function select() {
        $this->sql = "SELECT main.id, c.id AS course, 
                        c.fullname AS courseid, m.name AS moduletype, m.id AS module, 
                        CONCAT($this->resourcenames) AS activity, main.visible as status";
        parent::select();
    }

    function from() {
        $this->sql .= " FROM {course_modules} AS main";
    }

    function joins() {
        $this->sql .=" JOIN {modules} AS m ON m.id = main.module
                       JOIN {course} AS c ON c.id = main.course ";
        foreach ($this->aliases as $alias) {
            $this->sql .= " LEFT JOIN {".$alias."} AS $alias ON $alias.id = main.instance AND m.name = '$alias'";
        }
        parent::joins();
    }

    function where() {
        //$this->params['resourceslist'] = $this->resourceslist;
        $this->sql .= " WHERE c.visible = 1 AND c.id <> :siteid AND main.deletioninprogress = 0 AND m.name IN ($this->resourceslist) AND main.visible = 1 ";
        if (!is_siteadmin($this->userid) && !(new ls)->is_manager($this->userid, $this->contextlevel, $this->role)) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND main.course IN ($this->rolewisecourses) ";
            }
        }
        if ($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->sql .= " AND main.added BETWEEN :ls_fstartdate AND :ls_fenddate ";
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
        }
        parent::where();
    }

    function search() {
        global $DB;
        $modules = $DB->get_fieldset_select('modules',  'name', '');
        $this->aliases1 = [];
        foreach ($modules as $modulename) {
            $resourcearchetype = plugin_supports('mod', $modulename, FEATURE_MOD_ARCHETYPE);
            if($resourcearchetype){
                $this->aliases1[] = $modulename;
                $resources[] = "'$modulename'";
                $fields2[] = "COALESCE($modulename.name,'')";
            }
        }
        if (isset($this->search) && $this->search) {
            $this->searchable = array_push($fields2,"c.fullname");
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
            $this->sql .= " AND c.id IN (:filter_courses) ";
        }
    }
    function groupby() {

    }
	/**
	 * [get_rows description]
	 * @param  [type] $elements [description]
	 * @return [type]           [description]
	 */
	function get_rows($elements) {
		global $CFG, $OUTPUT, $DB;

		return $elements;
	}
	public function column_queries($columnname, $coursemoduleid, $courses = null) {
        if($courses){
            $learnersql  = (new querylib)->get_learners('', $courses);
        }else{
            $learnersql  = (new querylib)->get_learners('', '%courses%');
        }
        $where = " AND %placeholder% = $coursemoduleid";

        switch ($columnname) {
            case 'totaltimespent':
                $identy = 'cm.id';
                $courses = 'mt.courseid';
                $query =  "SELECT SUM(mt.timespent) 
                             FROM {block_ls_modtimestats} mt 
                             JOIN {course_modules} cm ON cm.id = mt.activityid 
                             WHERE 1 = 1 AND mt.userid IN ($learnersql) 
                            $where ";
            break;
            case 'numviews':
                $identy = 'lsl.contextinstanceid';
                $courses = 'lsl.courseid';
                if($this->reporttype == 'table'){
                    $query = "  SELECT COUNT(DISTINCT lsl.userid) as distinctusers, COUNT('X') as numviews 
                                  FROM {logstore_standard_log} lsl 
                                  JOIN {user} u ON u.id = lsl.userid 
                                  JOIN {course_modules} cm ON lsl.contextinstanceid = cm.id
                                 WHERE lsl.crud = 'r' AND lsl.contextlevel = 70  AND lsl.anonymous = 0 AND u.id IN ($learnersql)
                                   AND lsl.userid > 2  AND u.confirmed = 1 AND u.deleted = 0  AND lsl.anonymous = 0 
                                   $where ";
                }else{
                    $query = "  SELECT COUNT('X') as numviews 
                                  FROM {logstore_standard_log} lsl 
                                  JOIN {user} u ON u.id = lsl.userid
                                 JOIN {course_modules} cm ON lsl.contextinstanceid = cm.id
                                 WHERE  lsl.crud = 'r' AND lsl.contextlevel = 70 AND lsl.userid > 2 AND u.id IN ($learnersql) AND lsl.anonymous = 0 AND u.confirmed = 1 AND u.deleted = 0  $where";
                }

            break;
            default:
                return false;
            break;
        }
        $query = str_replace('%placeholder%', $identy, $query);
        $query = str_replace('%courses%', $courses, $query);
        return $query;
    }
}
