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

class report_pageresourcetimespent extends reportbase implements report {

	/**
	 * [__construct description]
	 * @param [type] $report           [description]
	 * @param [type] $reportproperties [description]
	 */
	public function __construct($report, $reportproperties = false) {
		global $DB;
		parent::__construct($report, $reportproperties);
		$this->parent = true;
		$this->components = array('columns', 'filters', 'permissions', 'plot');
		$pageresourcetimespentcolumns = array('name', 'totaltimespent');
		$this->columns = ['activityfield' => ['activityfield'] ,
						  'pageresourcetimespentcolumns' => $pageresourcetimespentcolumns];
		$this->courselevel = false;
		$this->filters = array('courses');
		$this->orderable = array('name','course','totaltimespent');
		$this->defaultcolumn = 'p.id';
	}
	function init() {
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
		$this->sql = "SELECT COUNT(DISTINCT p.id) ";
	}

	function select() {
		$this->sql = "SELECT DISTINCT p.id AS pid, cm.id, c.id AS courseid, c.fullname AS course, m.name AS type, m.id AS module ";
		if (!empty($this->selectedcolumns)) {
			if (in_array('name', $this->selectedcolumns)) {
	            $this->sql .= ", p.name AS name";
	        }
	        if (in_array('totaltimespent', $this->selectedcolumns)) { 
	        	$learnersql  = (new querylib)->get_learners('', 'mt.courseid');
	            $this->sql .= ", (SELECT SUM(mt.timespent)  
			            		FROM {block_ls_modtimestats} mt WHERE mt.activityid = cm.id AND mt.userid IN ($learnersql)) AS totaltimespent";
	        }
	    }
	}

	function from() {
		$this->sql .= " FROM {page} p";
	}

	function joins() {
		$this->sql .= " JOIN {course_modules} cm ON p.id = cm.instance
					    JOIN {modules} m ON m.id = cm.module AND m.name = 'page'
					    JOIN {course} c ON c.id = cm.course
					   WHERE 1=1 AND c.visible = 1 AND cm.visible = 1 AND cm.deletioninprogress = 0";
	}

	function where() {	
		if (!is_siteadmin($this->userid) && !(new ls)->is_manager($this->userid, $this->contextlevel, $this->role)) {
            if ($this->rolewisecourses != '') {
	            $this->sql .= " AND cm.course IN ($this->rolewisecourses) ";            
	        } 
        }
        if (!is_siteadmin($this->userid) && !(new ls)->is_manager($this->userid, $this->contextlevel, $this->role)) {
			if($_SESSION['role'] == 'student') {
				$concatsql .= " AND l.userid = :userid ";
				$this->params['userid'] = $this->userid;
			}
		}
		if ($this->ls_startdate >= 0 && $this->ls_enddate) {
			$this->sql .= " AND cm.added BETWEEN :ls_fstartdate AND :ls_fenddate ";
			$this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
			$this->params['ls_fenddate'] = ROUND($this->ls_enddate);
		}
		parent::where();
	}

	function search() {
		global $DB;
        if (isset($this->search) && $this->search) {
			$this->searchable = array("p.name", "c.fullname");
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
			$this->sql .= " AND cm.course IN (:filter_courses)";
		}
		if (isset($this->params['filter_users']) && $this->params['filter_users'] > 0) {
			$this->sql .= " AND l.userid IN (:filter_users)";
		}
	}
	function groupby() {
		global $CFG;
		if ($CFG->dbtype != 'sqlsrv') {
			$this->sql .= " GROUP BY p.id, cm.id, c.id, m.id ";
		}
	}
	/**
	 * [get_rows description]
	 * @param  [type] $elements [description]
	 * @return [type]           [description]
	 */
	function get_rows($elements) {
		return $elements;
	}
}
