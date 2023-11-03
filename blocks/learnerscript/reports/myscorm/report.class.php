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
 * @author: Arun Kumar <arun@eabyas.in>
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\reportbase;
use block_learnerscript\local\querylib;
use block_learnerscript\local\ls as ls;

class report_myscorm extends reportbase {

    public function __construct($report, $reportproperties) {
        parent::__construct($report);
        $this->columns = array('myscormcolumns' => array('course', 'scormname', 'attempt' , 'activitystate', 'finalgrade','firstaccess', 'lastaccess', 'totaltimespent', 'numviews'));
        $this->parent = true;
        if (isset($this->role) && $this->role == 'student') {
            $this->parent = true;
        } else {
            $this->parent = false;
        }
        if (is_siteadmin() || $this->role != 'student') {
            $this->basicparams = [['name' => 'users']];
        }
        $this->components = array('columns', 'filters', 'permissions', 'calcs', 'plot');
        $this->courselevel = false;
        $this->filters = array('courses');
        $this->orderable = array('course','scormname','activitystate','attempt','finalgrade','totaltimespent','numviews');
        $this->defaultcolumn = 's.id';
	}
  function init() {
        if($this->role != 'student' && !isset($this->params['filter_users'])){
            $this->initial_basicparams('users');
            $fusers = array_keys($this->filterdata);
            $this->params['filter_users'] = array_shift($fusers);
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
  function count() {
    $this->sql .= "SELECT COUNT(DISTINCT s.id) ";
  }

  function select() {
        $userid = isset($this->params['userid']) ? $this->params['userid'] : 0;
        $this->sql = "SELECT DISTINCT s.id, c.id AS courseid, ra.userid AS userid, cm.id AS cmid,
                                           m.id AS moduleid, st.scormid AS scormid, c.fullname AS course, s.name AS scormname"; 

        parent::select();

  }

  function from() {
    $this->sql .= " FROM {role_assignments} AS ra";
  }

  function joins() {
    $this->sql .= " JOIN {role} as r on r.id=ra.roleid AND r.shortname='student'
                   JOIN {context} AS ctx ON ctx.id = ra.contextid
                   JOIN {course} as c ON c.id = ctx.instanceid
                   JOIN {scorm} AS s ON s.course = c.id
                   JOIN {course_modules} AS cm ON cm.instance = s.id
                   JOIN {modules} AS m ON m.id = cm.module
              LEFT JOIN {scorm_scoes_track} AS st ON st.scormid = s.id
                   JOIN {scorm_scoes} ss ON ss.scorm = s.id";

    parent::joins();
  }

    function where() {
        $userid = isset($this->params['filter_users']) && $this->params['filter_users'] > 0
                    ? $this->params['filter_users'] : $this->userid;
        $this->params['userid'] = $userid;
        $this->sql .=" WHERE ra.userid = :userid  AND cm.visible = 1 AND
                                  cm.deletioninprogress = 0 AND c.visible = 1 AND m.name = 'scorm'";
        
        if ((!is_siteadmin() || $this->scheduling) && !(new ls)->is_manager()) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND c.id IN ($this->rolewisecourses) ";
            }
        }
        parent::where();
    }

    function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $this->searchable = array("c.fullname", "s.name");
            $statsql = array();
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);           
            $this->sql .= " AND ($fields) ";
        }
    }

    function filters() {
        
        $userid = isset($this->params['filter_users']) && $this->params['filter_users'] > 0
                    ? $this->params['filter_users'] : $this->userid;
        $this->courseid = isset($this->params['filter_courses']) ? $this->params['filter_courses'] : array();
        $this->params['userid'] = $userid;
        if($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->sql .= " AND ra.timemodified BETWEEN :ls_fstartdate AND :ls_fenddate ";
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
        }
        if (!empty($this->courseid) && $this->courseid != '_qf__force_multiselect_submission') {
          $courseid = $this->courseid;
          $this->sql .= " AND c.id = :courseid";
          $this->params['courseid'] = $courseid;
        }
    }

    function groupby() {

    }
	public function get_rows($elements) {
		return $elements;
	}
    public function column_queries($columnname, $scormid) {
        global $CFG;
        $where = " AND %placeholder% = $scormid";
        $userid = isset($this->params['userid']) ? $this->params['userid'] : 0;
        $limit = '';
        switch ($columnname) {
            case 'attempt':
                $identy = 'scormid';
                $query = "SELECT  $limit attempt AS attempt  
                            FROM {scorm_scoes_track} WHERE 1 = 1 $where 
                             AND userid = $userid ORDER BY id DESC  $limit  ";
                break;
            case 'activitystate':
                $identy = 'scormid';
                $query = "SELECT  $limit value AS activitystate FROM {scorm_scoes_track} 
                           WHERE element = 'cmi.core.lesson_status'
                             AND userid = $userid $where ORDER BY id DESC  $limit  ";
            break;
            case 'finalgrade':
                $identy = 'gi.iteminstance';
                $query = "SELECT gg.finalgrade AS finalgrade 
                            FROM {grade_grades} gg JOIN {grade_items} gi ON gi.id = gg.itemid 
                            WHERE gi.itemmodule = 'scorm' AND gg.userid = $userid $where";
            break;
            case 'firstaccess':
                $identy = 'scormid';
                $query = "SELECT  $limit value AS firstaccess FROM {scorm_scoes_track} 
                           WHERE element = 'x.start.time' 
                             AND userid = $userid $where ORDER BY attempt ASC  $limit ";
            break;
            case 'totaltimespent':
                $identy = 'cm.instance';
                $query = "SELECT SUM(mt.timespent) AS totaltimespent 
                            FROM {block_ls_modtimestats} as mt 
                            JOIN {course_modules} cm ON cm.id = mt.activityid 
                            JOIN {modules} m ON m.id = cm.module 
                            WHERE m.name = 'scorm' AND mt.userid = $userid $where";
            break;
            case 'numviews':
                $identy = 'cm.instance';
                $query = "SELECT COUNT(lsl.id) AS numviews 
                              FROM {logstore_standard_log} lsl 
                              JOIN {course_modules} cm ON cm.id = lsl.contextinstanceid  
                              JOIN {modules} m ON m.id = cm.module AND m.name = 'scorm'
                              JOIN {user} u ON u.id = lsl.userid AND u.confirmed = 1 AND u.deleted = 0
                             WHERE lsl.crud = 'r' AND lsl.contextlevel = 70 AND lsl.anonymous = 0 
                               AND lsl.userid = $userid $where ";
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
        return $query;
    }

}
